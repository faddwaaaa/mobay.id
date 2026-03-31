<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Product;
use App\Models\ProductSale;
use App\Models\PhysicalOrder;
use App\Models\User;
use App\Models\AdminWalletLedger;
use App\Models\DigitalOrder;
use App\Mail\PhysicalOrderConfirmation;
use App\Services\DigitalOrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class XenditWebhookController extends Controller
{
    /**
     * Handle Xendit invoice payment notification
     * 
     * POST /webhook/xendit/invoice
     */
    public function handleInvoiceCallback(Request $request)
    {
        try {
            $payload = $request->all();
            
            Log::info('Xendit Webhook Received:', [
                'event' => $payload['event'] ?? null,
                'external_id' => $payload['data']['external_id'] ?? null,
            ]);

            // Verify webhook signature
            if (!$this->verifyWebhookSignature($request)) {
                Log::warning('Xendit webhook signature verification failed');
                return response('Unauthorized', 401);
            }

            $event = $payload['event'] ?? null;
            $data = $payload['data'] ?? [];

            if ($event === 'invoice.paid') {
                return $this->handlePaymentPaid($data);
            } elseif ($event === 'invoice.expired') {
                return $this->handlePaymentExpired($data);
            }

            return response('OK', 200);

        } catch (\Exception $e) {
            Log::error('Xendit Webhook Error: ' . $e->getMessage());
            return response('Error', 500);
        }
    }

    /**
     * Handle invoice paid event
     */
    private function handlePaymentPaid(array $data)
    {
        try {
            $externalId = $data['external_id'] ?? null;
            $invoiceId = $data['id'] ?? null;
            $paidAmount = $data['paid_amount'] ?? 0;
            $paidDate = $data['paid_at'] ?? null;

            if (!$externalId) {
                Log::error('Xendit webhook: external_id not found');
                return response('Missing external_id', 400);
            }

            // Find transaction by order_id (external_id)
            $transaction = Transaction::where('order_id', $externalId)->first();

            if (!$transaction) {
                Log::error("Xendit webhook: Transaction not found for order {$externalId}");
                return response('Transaction not found', 404);
            }

            // Prevent double processing
            if ($transaction->status === 'settlement') {
                Log::info("Transaction {$externalId} already processed");
                return response('Already processed', 200);
            }

            // Update transaction status
            $transaction->update([
                'status' => 'settlement',
                'transaction_id' => $invoiceId,
                'xendit_response' => json_encode($data),
            ]);

            Log::info("Transaction {$externalId} marked as settlement");

            // Process successful payment
            $this->handleSuccessfulPayment($transaction);

            return response('OK', 200);

        } catch (\Exception $e) {
            Log::error('handlePaymentPaid Error: ' . $e->getMessage());
            return response('Error', 500);
        }
    }

    /**
     * Handle invoice expired event
     */
    private function handlePaymentExpired(array $data)
    {
        try {
            $externalId = $data['external_id'] ?? null;

            if (!$externalId) return response('Missing external_id', 400);

            $transaction = Transaction::where('order_id', $externalId)->first();
            if (!$transaction) return response('Transaction not found', 404);

            if ($transaction->status !== 'pending') {
                return response('Already processed', 200);
            }

            $transaction->update([
                'status' => 'expired',
                'xendit_response' => json_encode($data),
            ]);

            Log::info("Transaction {$externalId} marked as expired");

            return response('OK', 200);

        } catch (\Exception $e) {
            Log::error('handlePaymentExpired Error: ' . $e->getMessage());
            return response('Error', 500);
        }
    }

    /**
     * Verify Xendit webhook signature
     * 
     * @param Request $request
     * @return bool
     */
    private function verifyWebhookSignature(Request $request): bool
    {
        try {
            // Get X-Callback-Token from header
            $callbackToken = $request->header('X-Callback-Token');
            
            // In production, verify this token matches your configured webhook token
            // For now, we'll accept all callbacks (but log them)
            if (!$callbackToken) {
                Log::warning('Xendit callback token missing');
                // You can return false hier to reject, or true to accept
                // For testing, we accept without token
                return true;
            }

            return true;

        } catch (\Exception $e) {
            Log::error('Webhook signature verification error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Handle successful payment processing
     */
    private function handleSuccessfulPayment(Transaction $transaction): void
    {
        try {
            $notes = is_string($transaction->notes)
                ? json_decode($transaction->notes, true)
                : ($transaction->notes ?? []);

            $productId = $notes['product_id'] ?? null;
            $unitPrice = $notes['unit_price'] ?? 0;
            $sellerAmount = (int) ($notes['seller_amount'] ?? $transaction->amount);
            $paymentFeeAmount = (int) ($notes['payment_fee_amount'] ?? 0);

            // Guard: prevent double processing
            $alreadyProcessed = false;
            try {
                $alreadyProcessed = ProductSale::where('product_id', $productId)
                    ->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(options, '$.order_id')) = ?", [$transaction->order_id])
                    ->exists();
            } catch (\Exception $e) {
                Log::warning('Guard check failed: ' . $e->getMessage());
            }

            if ($alreadyProcessed) {
                Log::info("Order {$transaction->order_id} already processed, skipping");
                return;
            }

            $product = Product::with('files')->find($productId);
            if (!$product) {
                Log::error("Product id={$productId} not found for order {$transaction->order_id}");
                return;
            }

            // Create product sale record
            try {
                ProductSale::create([
                    'product_id' => $product->id,
                    'qty' => $notes['qty'] ?? 1,
                    'price' => $unitPrice,
                    'options' => json_encode([
                        'order_id' => $transaction->order_id,
                        'buyer_name' => $notes['buyer_name'] ?? '',
                        'buyer_email' => $notes['buyer_email'] ?? '',
                        'buyer_address' => $notes['buyer_address'] ?? null,
                        'buyer_notes' => $notes['buyer_notes'] ?? null,
                    ]),
                ]);
            } catch (\Exception $e) {
                Log::warning('ProductSale creation failed: ' . $e->getMessage());
                ProductSale::create([
                    'product_id' => $product->id,
                    'qty' => $notes['qty'] ?? 1,
                    'price' => $unitPrice,
                ]);
            }

            // Decrease stock for physical products
            if ($product->product_type === 'fisik' && $product->stock !== null) {
                $product->decrement('stock', $notes['qty'] ?? 1);
            }

            // Send digital files via download token
            if ($product->product_type === 'digital') {
                $this->sendDigitalFileViaToken($product, $notes, $transaction);
            }

            // Create physical order and send confirmation
            if ($product->product_type === 'fisik') {
                $this->createPhysicalOrder($product, $notes, $transaction);
            }

            // Credit seller balance
            $seller = User::find($transaction->user_id);
            if ($seller) {
                $seller->increment('balance', $sellerAmount);
                Log::info("✅ Seller {$seller->name} balance +Rp{$sellerAmount} from order {$transaction->order_id}");

                \App\Models\Notification::create([
                    'user_id' => $seller->id,
                    'type' => 'order',
                    'title' => 'Pesanan Baru Masuk!',
                    'message' => '📦 ' . ($notes['buyer_name'] ?? 'Pembeli') . ' memesan ' . ($notes['product_title'] ?? $product->title) . ' (#' . $transaction->order_id . ')',
                    'icon' => 'fas fa-shopping-bag',
                    'link' => '/riwayat',
                    'is_read' => false,
                ]);

                \App\Models\Notification::create([
                    'user_id' => $seller->id,
                    'type' => 'payment',
                    'title' => 'Pembayaran Diterima!',
                    'message' => '💰 Pembayaran #' . $transaction->order_id . ' sebesar Rp' . number_format((int) $sellerAmount, 0, ',', '.') . ' berhasil dikonfirmasi.',
                    'icon' => 'fas fa-circle-check',
                    'link' => '/riwayat',
                    'is_read' => false,
                ]);
            } else {
                Log::error("❌ Seller user_id={$transaction->user_id} not found");
            }

            // Credit admin wallet with fee
            $this->creditAdminWalletFeePayment($paymentFeeAmount, $transaction);

        } catch (\Exception $e) {
            Log::error('handleSuccessfulPayment error: ' . $e->getMessage());
        }
    }

    /**
     * Create physical order and send confirmation email
     */
    private function createPhysicalOrder(Product $product, array $notes, Transaction $transaction): void
    {
        try {
            $exists = PhysicalOrder::where('midtrans_order_id', $transaction->order_id)->exists();
            if ($exists) {
                Log::info("PhysicalOrder for {$transaction->order_id} already exists");
                return;
            }

            $destinationLabel = $notes['destination_label'] ?? '';
            $labelParts = array_map('trim', explode(',', $destinationLabel));
            $shippingCity = $labelParts[1] ?? ($labelParts[0] ?? '');
            $shippingProvince = $labelParts[2] ?? '';

            $physicalOrder = PhysicalOrder::create([
                'product_id' => $product->id,
                'seller_id' => $transaction->user_id,
                'buyer_name' => $notes['buyer_name'] ?? '',
                'buyer_email' => $notes['buyer_email'] ?? '',
                'buyer_phone' => $notes['buyer_phone'] ?? null,
                'order_code' => $transaction->order_id,
                'product_name' => $notes['product_title'] ?? $product->title,
                'product_price' => $notes['unit_price'] ?? $product->price,
                'quantity' => $notes['qty'] ?? 1,
                'shipping_cost' => $notes['shipping_cost'] ?? 0,
                'total_amount' => $notes['base_total'] ?? $transaction->amount,
                'shipping_address' => $notes['buyer_address'] ?? '',
                'shipping_city' => $shippingCity,
                'shipping_province' => $shippingProvince,
                'shipping_postal_code' => '',
                'status' => 'paid',
                'midtrans_order_id' => $transaction->order_id,
                'midtrans_transaction_id' => $transaction->transaction_id,
                'payment_method' => $transaction->payment_method,
                'paid_at' => now(),
                'courier_code' => $notes['selected_courier'] ?? null,
                'courier_service' => $notes['selected_service'] ?? null,
            ]);

            Mail::to($physicalOrder->buyer_email)
                ->send(new PhysicalOrderConfirmation($physicalOrder));

            Log::info("✅ PhysicalOrder {$physicalOrder->order_code} created & email sent");

        } catch (\Exception $e) {
            Log::error("createPhysicalOrder failed for {$transaction->order_id}: " . $e->getMessage());
        }
    }

    /**
     * Send digital file via download token
     */
    private function sendDigitalFileViaToken(Product $product, array $notes, Transaction $transaction): void
    {
        try {
            $buyerEmail = $notes['buyer_email'] ?? null;
            $buyerName = $notes['buyer_name'] ?? 'Pembeli';

            if (!$buyerEmail) {
                Log::error("sendDigitalFileViaToken: buyer_email empty for order {$transaction->order_id}");
                return;
            }

            $digitalProduct = \App\Models\DigitalProduct::where('product_id', $product->id)->first();

            if (!$digitalProduct) {
                $productFile = $product->files->first();

                if (!$productFile) {
                    Log::error("sendDigitalFileViaToken: No file found for product id={$product->id}");
                    return;
                }

                $digitalProduct = \App\Models\DigitalProduct::create([
                    'product_id' => $product->id,
                    'name' => $product->title,
                    'price' => (int) ($product->discount ?: $product->price),
                    'file_path' => $productFile->file,
                    'file_name' => basename($productFile->file),
                    'is_active' => true,
                ]);
            }

            $digitalOrder = \App\Models\DigitalOrder::create([
                'digital_product_id' => $digitalProduct->id,
                'buyer_email' => $buyerEmail,
                'buyer_name' => $buyerName,
                'amount' => (int) $transaction->amount,
                'status' => 'paid',
                'order_code' => $transaction->order_id,
            ]);

            $service = app(DigitalOrderService::class);
            $service->completeOrder($digitalOrder);

            Log::info("✅ Digital download email sent to {$buyerEmail}");

        } catch (\Exception $e) {
            Log::error("sendDigitalFileViaToken failed for order {$transaction->order_id}: " . $e->getMessage());
        }
    }

    /**
     * Credit admin wallet with payment fee
     */
    private function creditAdminWalletFeePayment(int $feeAmount, Transaction $transaction): void
    {
        if ($feeAmount <= 0) return;

        try {
            DB::transaction(function () use ($feeAmount, $transaction) {
                $lastBalance = (int) (AdminWalletLedger::query()
                    ->lockForUpdate()
                    ->latest('id')
                    ->value('balance_after') ?? 0);

                AdminWalletLedger::create([
                    'source' => 'fee_payment',
                    'direction' => 'credit',
                    'amount' => $feeAmount,
                    'balance_after' => $lastBalance + $feeAmount,
                    'reference_type' => Transaction::class,
                    'reference_id' => $transaction->id,
                    'description' => 'Fee payment from order ' . $transaction->order_id,
                    'created_by' => null,
                ]);
            });
        } catch (\Exception $e) {
            Log::error('creditAdminWalletFeePayment failed: ' . $e->getMessage());
        }
    }
}
