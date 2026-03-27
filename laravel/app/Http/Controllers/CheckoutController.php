<?php

namespace App\Http\Controllers;

use App\Models\AdminWalletLedger;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\ProductSale;
use App\Models\User;
use App\Models\DigitalOrder;
use App\Models\PhysicalOrder;
use App\Mail\PhysicalOrderConfirmation;
use App\Services\DigitalOrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Midtrans\Config;
use Midtrans\Snap;
use Midtrans\Notification;

class CheckoutController extends Controller
{
    public function __construct()
    {
        Config::$serverKey        = config('midtrans.server_key');
        Config::$clientKey        = config('midtrans.client_key');
        Config::$isProduction     = config('midtrans.is_production');
        Config::$isSanitized      = config('midtrans.is_sanitized', true);
        Config::$is3ds            = config('midtrans.is_3ds', true);
        Config::$overrideNotifUrl = config('app.url') . '/midtrans/webhook';
    }

    // =========================================================
    // SHOW
    // =========================================================
    public function show($productId)
    {
        $product = Product::with(['images', 'files'])->findOrFail($productId);
        $seller  = User::find($product->user_id);
        return view('checkout', compact('product', 'seller'));
    }

    public function checkpointStore(Request $request)
    {
        $request->validate([
            'product_id'     => 'required|exists:products,id',
            'buyer_name'     => 'required|string|max:255',
            'buyer_email'    => 'required|email|max:255',
            'buyer_phone'    => 'required|string|max:20',
            'qty'            => 'nullable|integer|min:1',
            'payment_method' => 'required|string',
        ]);

        $product = Product::findOrFail($request->product_id);
        $shippingEnabled = $product->product_type === 'fisik'
            ? (bool) ($product->shipping_enabled ?? true)
            : false;

        $qty = $product->product_type === 'digital' ? 1 : max((int) ($request->qty ?? 1), 1);

        if ($product->product_type === 'fisik') {
            if ($product->stock !== null && $product->stock < $qty) {
                return back()->withErrors(['qty' => 'Stok tidak mencukupi. Tersedia: ' . $product->stock])->withInput();
            }
            if ($product->purchase_limit && $qty > $product->purchase_limit) {
                return back()->withErrors(['qty' => 'Batas pembelian maks. ' . $product->purchase_limit . ' per transaksi.'])->withInput();
            }
            if (empty($request->buyer_address)) {
                return back()->withErrors(['buyer_address' => 'Alamat pengiriman wajib diisi untuk produk fisik.'])->withInput();
            }
            if ($shippingEnabled) {
                if (empty($request->destination_village_code)) {
                    return back()->withErrors(['destination_village_code' => 'Pilih area tujuan pengiriman terlebih dahulu.'])->withInput();
                }
                if (!isset($request->selected_ongkir_cost) || (int) $request->selected_ongkir_cost <= 0) {
                    return back()->withErrors(['selected_ongkir_cost' => 'Pilih layanan pengiriman terlebih dahulu.'])->withInput();
                }
            }
        }

        $payload = [
            'product_id'                => (int) $product->id,
            'buyer_name'                => (string) $request->buyer_name,
            'buyer_email'               => (string) $request->buyer_email,
            'buyer_phone'               => (string) $request->buyer_phone,
            'buyer_address'             => $request->buyer_address,
            'buyer_notes'               => $request->buyer_notes,
            'qty'                       => $qty,
            'payment_method'            => (string) $request->payment_method,
            'destination_village_code'  => $shippingEnabled ? $request->destination_village_code : null,
            'destination_label'         => $shippingEnabled ? $request->destination_label : null,
            'selected_courier'          => $shippingEnabled ? ($request->selected_courier ?: 'OTHER') : 'FREE',
            'selected_service'          => $shippingEnabled ? ($request->selected_service ?: 'Standard') : 'Tanpa Ongkir',
            'selected_ongkir_cost'      => $shippingEnabled ? (int) ($request->selected_ongkir_cost ?? 0) : 0,
        ];

        session(['checkout_checkpoint' => $payload]);

        \App\Models\Notification::create([
            'user_id' => $product->user_id,
            'type'    => 'checkout',
            'title'   => 'Pembeli Siap Checkout',
            'message' => '🛒 ' . ($payload['buyer_name'] ?? 'Pembeli') . ' sedang mereview pembayaran untuk ' . $product->title . '.',
            'icon'    => 'fas fa-cash-register',
            'link'    => '/riwayat',
            'is_read' => false,
        ]);

        return redirect()->route('checkout.checkpoint.show');
    }

    public function checkpointShow(Request $request)
    {
        $payload = session('checkout_checkpoint');
        if (!$payload || empty($payload['product_id'])) {
            return redirect()->route('home');
        }

        $product = Product::with(['images'])->find($payload['product_id']);
        if (!$product) {
            return redirect()->route('home');
        }

        $seller = User::find($product->user_id);
        $shippingEnabled = $product->product_type === 'fisik'
            ? (bool) ($product->shipping_enabled ?? true)
            : false;
        $qty = $product->product_type === 'digital' ? 1 : max((int) ($payload['qty'] ?? 1), 1);
        $unitPrice = (int) ($product->discount ?: $product->price);
        $subtotal = $unitPrice * $qty;
        $shippingCost = $shippingEnabled ? max((int) ($payload['selected_ongkir_cost'] ?? 0), 0) : 0;
        $baseTotal = $subtotal + $shippingCost;
        $paymentFeePercent = (float) config('payment.payment_fee_percent', 5);
        $paymentFeeAmount = (int) ceil($baseTotal * ($paymentFeePercent / 100));
        $total = $baseTotal + $paymentFeeAmount;

        return view('checkout-checkpoint', compact(
            'product', 'seller', 'payload', 'qty', 'unitPrice',
            'subtotal', 'shippingCost', 'baseTotal',
            'paymentFeePercent', 'paymentFeeAmount', 'total', 'shippingEnabled'
        ));
    }

    // =========================================================
    // PROCESS
    // =========================================================
    public function process(Request $request)
    {
        $request->validate([
            'product_id'     => 'required|exists:products,id',
            'buyer_name'     => 'required|string|max:255',
            'buyer_email'    => 'required|email|max:255',
            'buyer_phone'    => 'required|string|max:20',
            'qty'            => 'required|integer|min:1',
            'payment_method' => 'required|string',
        ]);

        $product = Product::with('files')->findOrFail($request->product_id);
        $shippingEnabled = $product->product_type === 'fisik'
            ? (bool) ($product->shipping_enabled ?? true)
            : false;

        if ($product->product_type === 'fisik') {
            if ($product->stock !== null && $product->stock < $request->qty) {
                return response()->json(['error' => true, 'message' => 'Stok tidak mencukupi. Tersedia: ' . $product->stock], 422);
            }
            if ($product->purchase_limit && $request->qty > $product->purchase_limit) {
                return response()->json(['error' => true, 'message' => 'Batas pembelian maks. ' . $product->purchase_limit . ' per transaksi.'], 422);
            }
            if (empty($request->buyer_address)) {
                return response()->json(['error' => true, 'message' => 'Alamat pengiriman wajib diisi untuk produk fisik.'], 422);
            }
            if ($shippingEnabled) {
                if (empty($request->destination_village_code)) {
                    return response()->json(['error' => true, 'message' => 'Pilih area tujuan pengiriman terlebih dahulu.'], 422);
                }
                if (!isset($request->selected_ongkir_cost) || (int) $request->selected_ongkir_cost <= 0) {
                    return response()->json(['error' => true, 'message' => 'Pilih layanan pengiriman terlebih dahulu.'], 422);
                }
            }
        }

        $qty          = $product->product_type === 'digital' ? 1 : (int) $request->qty;
        $unitPrice    = (int) ($product->discount ?: $product->price);
        $subtotal     = $unitPrice * $qty;
        $shippingCost = ($product->product_type === 'fisik' && $shippingEnabled)
            ? max((int) ($request->selected_ongkir_cost ?? 0), 0)
            : 0;
        $baseTotal          = $subtotal + $shippingCost;
        $paymentFeePercent  = (float) config('payment.payment_fee_percent', 5);
        $paymentFeeAmount   = (int) ceil($baseTotal * ($paymentFeePercent / 100));
        $amount             = $baseTotal + $paymentFeeAmount;
        $orderId            = 'PAYOU-' . strtoupper(Str::random(8)) . '-' . time();

        $transaction = Transaction::create([
            'user_id'        => $product->user_id,
            'order_id'       => $orderId,
            'amount'         => $amount,
            'status'         => 'pending',
            'payment_method' => $request->payment_method,
            'notes'          => json_encode([
                'buyer_name'           => $request->buyer_name,
                'buyer_email'          => $request->buyer_email,
                'buyer_phone'          => $request->buyer_phone,
                'buyer_address'        => $request->buyer_address ?? null,
                'buyer_notes'          => $request->buyer_notes ?? null,
                'product_id'           => $product->id,
                'product_title'        => $product->title,
                'product_type'         => $product->product_type,
                'qty'                  => $qty,
                'unit_price'           => $unitPrice,
                'shipping_enabled'     => $shippingEnabled,
                'shipping_cost'        => $shippingCost,
                'subtotal'             => $subtotal,
                'base_total'           => $baseTotal,
                'payment_fee_percent'  => $paymentFeePercent,
                'payment_fee_amount'   => $paymentFeeAmount,
                'seller_amount'        => $baseTotal,
                'destination_village_code' => $request->destination_village_code ?? null,
                'destination_label'    => $request->destination_label ?? null,
                'selected_courier'     => $shippingEnabled ? ($request->selected_courier ?: 'OTHER') : 'FREE',
                'selected_service'     => $shippingEnabled ? ($request->selected_service ?: 'Standard') : 'Tanpa Ongkir',
            ]),
            'ip_address' => $request->ip(),
        ]);

        $snapParams = [
            'transaction_details' => [
                'order_id'     => $orderId,
                'gross_amount' => $amount,
            ],
            'item_details' => [[
                'id'       => (string) $product->id,
                'price'    => $unitPrice,
                'quantity' => $qty,
                'name'     => substr($product->title, 0, 50),
            ]],
            'customer_details' => [
                'first_name' => $request->buyer_name,
                'email'      => $request->buyer_email,
                'phone'      => $request->buyer_phone,
            ],
        ];

        if ($shippingCost > 0) {
            $snapParams['item_details'][] = [
                'id'       => 'shipping',
                'price'    => $shippingCost,
                'quantity' => 1,
                'name'     => 'Ongkir ' . strtoupper((string) ($request->selected_courier ?: 'OTHER')),
            ];
        }

        if ($paymentFeeAmount > 0) {
            $snapParams['item_details'][] = [
                'id'       => 'platform-fee',
                'price'    => $paymentFeeAmount,
                'quantity' => 1,
                'name'     => 'Biaya Layanan',
            ];
        }

        try {
            $snapToken = Snap::getSnapToken($snapParams);
        } catch (\Exception $e) {
            Log::error('Midtrans Snap Error: ' . $e->getMessage());
            return response()->json(['error' => true, 'message' => 'Gagal menghubungi payment gateway. Coba lagi.'], 500);
        }

        return response()->json(['snap_token' => $snapToken, 'order_id' => $orderId]);
    }

    // =========================================================
    // WEBHOOK
    // =========================================================
    public function webhook(Request $request)
    {
        try {
            $notif = new Notification();
        } catch (\Exception $e) {
            Log::error('Midtrans webhook error: ' . $e->getMessage());
            return response('Error', 500);
        }

        $transaction = Transaction::where('order_id', $notif->order_id)->first();
        if (!$transaction) return response('Not found', 404);

        if ($transaction->status === 'settlement') return response('Already processed', 200);

        $finalStatus = match (true) {
            $notif->transaction_status === 'capture' && $notif->fraud_status === 'accept' => 'settlement',
            $notif->transaction_status === 'settlement'                                    => 'settlement',
            $notif->transaction_status === 'pending'                                       => 'pending',
            in_array($notif->transaction_status, ['deny', 'cancel', 'expire'])             => $notif->transaction_status,
            $notif->transaction_status === 'failure'                                       => 'failed',
            default                                                                        => $transaction->status,
        };

        $transaction->update([
            'status'            => $finalStatus,
            'payment_method'    => $notif->payment_type ?? $transaction->payment_method,
            'transaction_id'    => $notif->transaction_id ?? null,
            'midtrans_response' => json_encode($notif->getResponse()),
        ]);

        if ($finalStatus === 'settlement') {
            $this->handleSuccessfulPayment($transaction->fresh());
        }

        return response('OK', 200);
    }

    // =========================================================
    // SUCCESS
    // =========================================================
    public function success(Request $request)
    {
        $orderId     = $request->order_id;
        $transaction = Transaction::where('order_id', $orderId)->first();

        if (!$transaction) {
            return view('checkout-success', ['transaction' => null, 'notes' => []]);
        }

        $notes = is_string($transaction->notes)
            ? json_decode($transaction->notes, true)
            : ($transaction->notes ?? []);

        if ($transaction->status === 'pending') {
            try {
                $status    = \Midtrans\Transaction::status($orderId);
                $txStatus  = $status->transaction_status ?? '';
                $fraud     = $status->fraud_status ?? 'accept';
                $isSuccess = ($txStatus === 'settlement') ||
                             ($txStatus === 'capture' && $fraud === 'accept');

                if ($isSuccess) {
                    $transaction->update([
                        'status'            => 'settlement',
                        'payment_method'    => $status->payment_type ?? $transaction->payment_method,
                        'transaction_id'    => $status->transaction_id ?? null,
                        'midtrans_response' => json_encode($status),
                    ]);

                    $this->handleSuccessfulPayment($transaction->fresh());
                    $transaction = $transaction->fresh();
                    $notes = is_string($transaction->notes)
                        ? json_decode($transaction->notes, true)
                        : ($transaction->notes ?? []);
                }
            } catch (\Exception $e) {
                Log::error('Midtrans status check error: ' . $e->getMessage());
            }
        }

        return view('checkout-success', compact('transaction', 'notes'));
    }

    // =========================================================
    // PENDING
    // =========================================================
    public function pending(Request $request)
    {
        $transaction = Transaction::where('order_id', $request->order_id)->first();
        $notes = $transaction
            ? (is_string($transaction->notes) ? json_decode($transaction->notes, true) : ($transaction->notes ?? []))
            : [];

        return view('checkout-pending', compact('transaction', 'notes'));
    }

    // =========================================================
    // HANDLE SUCCESSFUL PAYMENT
    // =========================================================
    private function handleSuccessfulPayment(Transaction $transaction): void
    {
        if ($transaction->status !== 'settlement') return;

        $notes = is_string($transaction->notes)
            ? json_decode($transaction->notes, true)
            : ($transaction->notes ?? []);

        $productId        = $notes['product_id'] ?? null;
        $unitPrice        = $notes['unit_price'] ?? 0;
        $sellerAmount     = (int) ($notes['seller_amount'] ?? $transaction->amount);
        $paymentFeeAmount = (int) ($notes['payment_fee_amount'] ?? 0);

        Log::info('DEBUG handleSuccessfulPayment', [
            'order_id'            => $transaction->order_id,
            'notes'               => $notes,
            'unit_price'          => $unitPrice,
            'productId'           => $productId,
            'seller_amount'       => $sellerAmount,
            'payment_fee_amount'  => $paymentFeeAmount,
        ]);

        // Guard double-processing
        $alreadyProcessed = false;
        try {
            $alreadyProcessed = ProductSale::where('product_id', $productId)
                ->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(options, '$.order_id')) = ?", [$transaction->order_id])
                ->exists();
        } catch (\Exception $e) {
            Log::warning('Guard check gagal: ' . $e->getMessage());
        }

        if ($alreadyProcessed) {
            Log::info("Order {$transaction->order_id} sudah diproses sebelumnya, skip.");
            return;
        }

        $product = Product::with('files')->find($productId);
        if (!$product) {
            Log::error("Product id={$productId} tidak ditemukan untuk order {$transaction->order_id}");
            return;
        }

        // 1. Catat product_sales
        try {
            ProductSale::create([
                'product_id' => $product->id,
                'qty'        => $notes['qty'] ?? 1,
                'price'      => $unitPrice,
                'options'    => json_encode([
                    'order_id'      => $transaction->order_id,
                    'buyer_name'    => $notes['buyer_name'] ?? '',
                    'buyer_email'   => $notes['buyer_email'] ?? '',
                    'buyer_address' => $notes['buyer_address'] ?? null,
                    'buyer_notes'   => $notes['buyer_notes'] ?? null,
                ]),
            ]);
        } catch (\Exception $e) {
            Log::warning('ProductSale create tanpa options: ' . $e->getMessage());
            ProductSale::create([
                'product_id' => $product->id,
                'qty'        => $notes['qty'] ?? 1,
                'price'      => $unitPrice,
            ]);
        }

        // 2. Kurangi stok produk fisik
        if ($product->product_type === 'fisik' && $product->stock !== null) {
            $product->decrement('stock', $notes['qty'] ?? 1);
        }

        // 3. Kirim file digital via link download (token-based)
        if ($product->product_type === 'digital') {
            $this->sendDigitalFileViaToken($product, $notes, $transaction);
        }

        // 3b. BUAT PHYSICAL ORDER + KIRIM EMAIL KONFIRMASI
        if ($product->product_type === 'fisik') {
            $this->createPhysicalOrder($product, $notes, $transaction);
        }

        // 4. Tambah saldo seller
        $seller = User::find($transaction->user_id);
        if ($seller) {
            $seller->increment('balance', $sellerAmount);
            Log::info("✅ Saldo {$seller->name} +Rp{$sellerAmount} dari order {$transaction->order_id}");

            \App\Models\Notification::create([
                'user_id' => $seller->id,
                'type'    => 'order',
                'title'   => 'Pesanan Baru Masuk!',
                'message' => '📦 ' . ($notes['buyer_name'] ?? 'Pembeli') . ' memesan ' . ($notes['product_title'] ?? $product->title) . ' (#' . $transaction->order_id . ')',
                'icon'    => 'fas fa-shopping-bag',
                'link'    => '/riwayat',
                'is_read' => false,
            ]);

            \App\Models\Notification::create([
                'user_id' => $seller->id,
                'type'    => 'payment',
                'title'   => 'Pembayaran Diterima!',
                'message' => '💰 Pembayaran #' . $transaction->order_id . ' sebesar Rp' . number_format((int) $sellerAmount, 0, ',', '.') . ' berhasil dikonfirmasi.',
                'icon'    => 'fas fa-circle-check',
                'link'    => '/riwayat',
                'is_read' => false,
            ]);
        } else {
            Log::error("❌ Seller user_id={$transaction->user_id} tidak ditemukan.");
        }

        $this->creditAdminWalletFeePayment($paymentFeeAmount, $transaction);
    }

    // =========================================================
    // BUAT PHYSICAL ORDER & KIRIM EMAIL KONFIRMASI
    // =========================================================
    private function createPhysicalOrder(Product $product, array $notes, Transaction $transaction): void
    {
        // Cek sudah ada belum — hindari double create
        $exists = PhysicalOrder::where('midtrans_order_id', $transaction->order_id)->exists();
        if ($exists) {
            Log::info("PhysicalOrder untuk {$transaction->order_id} sudah ada, skip.");
            return;
        }

        $destinationLabel = $notes['destination_label'] ?? '';
        $labelParts       = array_map('trim', explode(',', $destinationLabel));
        $shippingCity     = $labelParts[1] ?? ($labelParts[0] ?? '');
        $shippingProvince = $labelParts[2] ?? '';

        try {
            $physicalOrder = PhysicalOrder::create([
                'product_id'              => $product->id,
                'seller_id'               => $transaction->user_id,
                'buyer_name'              => $notes['buyer_name'] ?? '',
                'buyer_email'             => $notes['buyer_email'] ?? '',
                'buyer_phone'             => $notes['buyer_phone'] ?? null,
                'order_code'              => $transaction->order_id, // ✅ Pakai PAYOU-xxx biar sama dengan admin
                'product_name'            => $notes['product_title'] ?? $product->title,
                'product_price'           => $notes['unit_price'] ?? $product->price,
                'quantity'                => $notes['qty'] ?? 1,
                'shipping_cost'           => $notes['shipping_cost'] ?? 0,
                'total_amount'            => $notes['base_total'] ?? $transaction->amount,
                'shipping_address'        => $notes['buyer_address'] ?? '',
                'shipping_city'           => $shippingCity,
                'shipping_province'       => $shippingProvince,
                'shipping_postal_code'    => '',
                'status'                  => 'paid',
                'midtrans_order_id'       => $transaction->order_id,
                'midtrans_transaction_id' => $transaction->transaction_id,
                'payment_method'          => $transaction->payment_method,
                'paid_at'                 => now(),
                'courier_code'            => $notes['selected_courier'] ?? null,
                'courier_service'         => $notes['selected_service'] ?? null,
            ]);

            // Kirim email konfirmasi ke pembeli
            Mail::to($physicalOrder->buyer_email)
                ->send(new PhysicalOrderConfirmation($physicalOrder));

            Log::info("✅ PhysicalOrder {$physicalOrder->order_code} dibuat & email dikirim ke {$physicalOrder->buyer_email}");

        } catch (\Exception $e) {
            Log::error("createPhysicalOrder gagal untuk {$transaction->order_id}: " . $e->getMessage());
        }
    }

    // =========================================================
    // KIRIM FILE DIGITAL VIA TOKEN
    // =========================================================
    private function sendDigitalFileViaToken(Product $product, array $notes, Transaction $transaction): void
    {
        $buyerEmail = $notes['buyer_email'] ?? null;
        $buyerName  = $notes['buyer_name'] ?? 'Pembeli';

        if (!$buyerEmail) {
            Log::error("sendDigitalFileViaToken: buyer_email kosong untuk order {$transaction->order_id}");
            return;
        }

        try {
            $digitalProduct = \App\Models\DigitalProduct::where('product_id', $product->id)->first();

            if (!$digitalProduct) {
                $productFile = $product->files->first();

                if (!$productFile) {
                    Log::error("sendDigitalFileViaToken: Tidak ada file untuk product id={$product->id}");
                    $this->sendDigitalFileFallback($product, $notes, $transaction);
                    return;
                }

                $digitalProduct = \App\Models\DigitalProduct::create([
                    'product_id' => $product->id,
                    'name'       => $product->title,
                    'price'      => (int) ($product->discount ?: $product->price),
                    'file_path'  => $productFile->file,
                    'file_name'  => basename($productFile->file),
                    'is_active'  => true,
                ]);
            }

            $digitalOrder = \App\Models\DigitalOrder::create([
                'digital_product_id' => $digitalProduct->id,
                'buyer_email'        => $buyerEmail,
                'buyer_name'         => $buyerName,
                'amount'             => (int) $transaction->amount,
                'status'             => 'paid',
                'order_code'         => $transaction->order_id,
            ]);

            $service = app(DigitalOrderService::class);
            $service->completeOrder($digitalOrder);

            Log::info("✅ Email download digital dikirim ke {$buyerEmail} untuk order {$transaction->order_id}");

        } catch (\Exception $e) {
            Log::error("sendDigitalFileViaToken gagal untuk order {$transaction->order_id}: " . $e->getMessage());
            $this->sendDigitalFileFallback($product, $notes, $transaction);
        }
    }

    // =========================================================
    // FALLBACK: kirim file langsung sebagai attachment
    // =========================================================
    private function sendDigitalFileFallback(Product $product, array $notes, Transaction $transaction): void
    {
        $buyerEmail = $notes['buyer_email'] ?? null;
        if (!$buyerEmail) return;

        $files = $product->files;
        if (!$files || $files->isEmpty()) return;

        try {
            Mail::send('emails.digital-product', [
                'buyerName'    => $notes['buyer_name'] ?? 'Pembeli',
                'productTitle' => $product->title,
                'orderId'      => $transaction->order_id,
                'files'        => $files,
            ], function ($mail) use ($buyerEmail, $notes, $product, $files) {
                $mail->to($buyerEmail, $notes['buyer_name'] ?? 'Pembeli')
                     ->subject('📦 Produk Digital Anda: ' . $product->title);

                foreach ($files as $file) {
                    $filePath = storage_path('app/public/' . $file->file);
                    if (file_exists($filePath)) {
                        $mail->attach($filePath, [
                            'as'   => basename($file->file),
                            'mime' => mime_content_type($filePath),
                        ]);
                    }
                }
            });

            Log::info("✅ Fallback email attachment dikirim ke {$buyerEmail}");
        } catch (\Exception $e) {
            Log::error('Fallback kirim email digital gagal: ' . $e->getMessage());
        }
    }

    private function creditAdminWalletFeePayment(int $feeAmount, Transaction $transaction): void
    {
        if ($feeAmount <= 0) return;

        DB::transaction(function () use ($feeAmount, $transaction) {
            $lastBalance = (int) (AdminWalletLedger::query()
                ->lockForUpdate()
                ->latest('id')
                ->value('balance_after') ?? 0);

            AdminWalletLedger::create([
                'source'         => 'fee_payment',
                'direction'      => 'credit',
                'amount'         => $feeAmount,
                'balance_after'  => $lastBalance + $feeAmount,
                'reference_type' => Transaction::class,
                'reference_id'   => $transaction->id,
                'description'    => 'Fee payment dari order ' . $transaction->order_id,
                'created_by'     => null,
            ]);
        });
    }
}