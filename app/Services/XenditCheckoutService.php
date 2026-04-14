<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Str;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class XenditCheckoutService
{
    private $client;
    private $baseUrl = 'https://api.xendit.co';
    private $apiKey;

    public function __construct()
    {
        $this->apiKey = config('xendit.secret_key');
        $this->client = new Client([
            'base_uri' => $this->baseUrl,
            'auth' => [$this->apiKey, ''],
        ]);
    }

    /**
     * Buat invoice untuk checkout produk (Digital/Fisik)
     * Xendit akan handle payment method selection & fee kalkulasi
     */
    public function createCheckoutInvoice($checkoutData): array
    {
        try {
            $product = Product::findOrFail($checkoutData['product_id']);
            $seller = User::findOrFail($product->user_id);

            $qty = $checkoutData['qty'] ?? 1;
            $unitPrice = (int)($product->discount ?: $product->price);
            $subtotal = $unitPrice * $qty;
            $shippingCost = (int)($checkoutData['selected_ongkir_cost'] ?? 0);
            $baseTotal = $subtotal + $shippingCost;

            // Platform fee (calculated)
            $paymentFeePercent = (float)config('payment.payment_fee_percent', 5);
            $paymentFeeAmount = (int)ceil($baseTotal * ($paymentFeePercent / 100));

            // Total TANPA biaya payment method (Xendit akan hitung)
            $totalAmount = $baseTotal + $paymentFeeAmount;

            // Generate order ID
            $orderId = 'PAYOU-' . strtoupper(Str::random(8)) . '-' . time();
            $externalId = $orderId;

            // Prepare item details
            $items = [
                [
                    'name' => substr($product->title, 0, 100),
                    'quantity' => $qty,
                    'price' => $unitPrice,
                ]
            ];

            if ($shippingCost > 0) {
                $items[] = [
                    'name' => 'Ongkir ' . ($checkoutData['selected_service'] ?? 'Standard'),
                    'quantity' => 1,
                    'price' => $shippingCost,
                ];
            }

            if ($paymentFeeAmount > 0) {
                $items[] = [
                    'name' => 'Biaya Layanan Platform',
                    'quantity' => 1,
                    'price' => $paymentFeeAmount,
                ];
            }

            // Create Xendit invoice
            $response = $this->client->post('/v2/invoices', [
                'json' => [
                    'external_id' => $externalId,
                    'amount' => $totalAmount,
                    'payer_email' => $checkoutData['buyer_email'],
                    'description' => 'Pembelian: ' . $product->title,
                    'invoice_duration' => 86400, // 24 jam
                    'customer' => [
                        'given_names' => $checkoutData['buyer_name'],
                        'email' => $checkoutData['buyer_email'],
                        'mobile_number' => $checkoutData['buyer_phone'],
                    ],
                    'items' => $items,
                    'fees' => [
                        [
                            'type' => 'XENDIT_ADMIN_FEE',
                            'value' => 0,
                        ],
                    ],
                    'success_redirect_url' => route('checkout.success'),
                    'failure_redirect_url' => route('checkout.pending'),
                ],
            ]);

            $invoiceData = json_decode($response->getBody(), true);

            // Create transaction record
            $transaction = Transaction::create([
                'user_id' => $seller->id,
                'order_id' => $orderId,
                'amount' => $totalAmount,
                'status' => 'pending',
                'payment_method' => 'xendit_all_methods', // Xendit handle semua method
                'notes' => json_encode([
                    'buyer_name' => $checkoutData['buyer_name'],
                    'buyer_email' => $checkoutData['buyer_email'],
                    'buyer_phone' => $checkoutData['buyer_phone'],
                    'buyer_address' => $checkoutData['buyer_address'] ?? null,
                    'buyer_notes' => $checkoutData['buyer_notes'] ?? null,
                    'product_id' => $product->id,
                    'product_title' => $product->title,
                    'product_type' => $product->product_type,
                    'qty' => $qty,
                    'unit_price' => $unitPrice,
                    'subtotal' => $subtotal,
                    'shipping_cost' => $shippingCost,
                    'base_total' => $baseTotal,
                    'payment_fee_amount' => $paymentFeeAmount,
                    'seller_amount' => $baseTotal,
                    'destination_village_code' => $checkoutData['destination_village_code'] ?? null,
                    'destination_label' => $checkoutData['destination_label'] ?? null,
                    'selected_courier' => $checkoutData['selected_courier'] ?? 'FREE',
                    'selected_service' => $checkoutData['selected_service'] ?? 'Tanpa Ongkir',
                ]),
                'ip_address' => $checkoutData['ip_address'] ?? null,
            ]);

            // Store invoice ID untuk tracking
            $transaction->update([
                'transaction_id' => $invoiceData['id'],
                'xendit_response' => json_encode($invoiceData),
            ]);

            Log::info('Xendit checkout invoice created', [
                'order_id' => $orderId,
                'invoice_id' => $invoiceData['id'],
                'amount' => $totalAmount,
            ]);

            return [
                'success' => true,
                'invoice_id' => $invoiceData['id'],
                'external_id' => $externalId,
                'invoice_url' => $invoiceData['invoice_url'] ?? null,
                'amount' => $totalAmount,
                'order_id' => $orderId,
            ];

        } catch (\Exception $e) {
            Log::error('Xendit checkout invoice error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Handle pembayaran sukses dari Xendit webhook
     */
    public function handlePaymentSuccess(array $data): bool
    {
        try {
            $externalId = $data['external_id'] ?? null;
            $status = $data['status'] ?? null;

            if (!$externalId || $status !== 'PAID') {
                return false;
            }

            // Find transaction
            $transaction = Transaction::where('order_id', $externalId)->first();
            if (!$transaction) {
                Log::warning('Transaction not found for order: ' . $externalId);
                return false;
            }

            // Prevent double processing
            if ($transaction->status === 'settlement') {
                Log::info('Transaction already processed: ' . $externalId);
                return false;
            }

            // Update transaction to settlement
            $transaction->update([
                'status' => 'settlement',
                'xendit_response' => json_encode($data),
            ]);

            // Process the order (same logic as Midtrans)
            $this->processCheckoutOrder($transaction);

            Log::info('Xendit checkout order processed: ' . $externalId);
            return true;

        } catch (\Exception $e) {
            Log::error('Error handling Xendit checkout payment: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Process order setelah pembayaran sukses
     */
    private function processCheckoutOrder(Transaction $transaction): void
    {
        $notes = is_string($transaction->notes)
            ? json_decode($transaction->notes, true)
            : ($transaction->notes ?? []);

        $productId = $notes['product_id'] ?? null;
        $product = Product::with('files')->find($productId);

        if (!$product) {
            Log::error('Product not found for order: ' . $transaction->order_id);
            return;
        }

        // Create product sale record
        \App\Models\ProductSale::create([
            'product_id' => $product->id,
            'quantity' => $notes['qty'] ?? 1,
            'unit_price' => $notes['unit_price'] ?? 0,
            'total_price' => $notes['subtotal'] ?? 0,
            'options' => json_encode([
                'order_id' => $transaction->order_id,
                'buyer_name' => $notes['buyer_name'] ?? 'Guest',
                'buyer_email' => $notes['buyer_email'] ?? '',
                'buyer_phone' => $notes['buyer_phone'] ?? '',
                'buyer_address' => $notes['buyer_address'] ?? '',
                'shipping_cost' => $notes['shipping_cost'] ?? 0,
                'courier' => $notes['selected_courier'] ?? 'FREE',
            ]),
        ]);

        // Create order based on product type
        if ($product->product_type === 'digital') {
            \App\Models\DigitalOrder::create([
                'product_id' => $product->id,
                'buyer_name' => $notes['buyer_name'] ?? 'Guest',
                'buyer_email' => $notes['buyer_email'] ?? '',
                'order_id' => $transaction->order_id,
                'status' => 'completed',
                'download_count' => 0,
                'download_limit' => 10,
            ]);
        } elseif ($product->product_type === 'fisik') {
            \App\Models\PhysicalOrder::create([
                'product_id' => $product->id,
                'buyer_name' => $notes['buyer_name'] ?? 'Guest',
                'buyer_email' => $notes['buyer_email'] ?? '',
                'buyer_phone' => $notes['buyer_phone'] ?? '',
                'buyer_address' => $notes['buyer_address'] ?? '',
                'order_id' => $transaction->order_id,
                'status' => 'pending',
            ]);
        }

        // Update product stock if fisik
        if ($product->product_type === 'fisik' && $product->stock !== null) {
            $product->decrement('stock', $notes['qty'] ?? 1);
        }

        // Credit seller (jika tidak ada fee/settle langsung)
        $seller = User::find($product->user_id);
        if ($seller && isset($notes['seller_amount'])) {
            $seller->increment('balance', $notes['seller_amount']);

            // Log ledger
            \App\Models\Ledger::create([
                'user_id' => $seller->id,
                'type' => 'credit',
                'amount' => $notes['seller_amount'],
                'description' => 'Penjualan: ' . $product->title,
                'reference_id' => $transaction->order_id,
            ]);
        }

        Log::info('Checkout order fully processed: ' . $transaction->order_id);
    }
}
