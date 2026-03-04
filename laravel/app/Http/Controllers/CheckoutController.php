<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Transaction;
use App\Models\ProductSale;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
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
            if (empty($request->destination_village_code)) {
                return response()->json(['error' => true, 'message' => 'Pilih area tujuan pengiriman terlebih dahulu.'], 422);
            }
            if (!isset($request->selected_ongkir_cost) || (int) $request->selected_ongkir_cost <= 0) {
                return response()->json(['error' => true, 'message' => 'Pilih layanan pengiriman terlebih dahulu.'], 422);
            }
        }

        $qty       = $product->product_type === 'digital' ? 1 : (int) $request->qty;
        // FIX: Gunakan ?: bukan ?? supaya discount bernilai 0 tetap fallback ke price
        $unitPrice = (int) ($product->discount ?: $product->price);
        $subtotal  = $unitPrice * $qty;
        $shippingCost = $product->product_type === 'fisik'
            ? max((int) ($request->selected_ongkir_cost ?? 0), 0)
            : 0;
        $amount    = $subtotal + $shippingCost;
        $orderId   = 'PAYOU-' . strtoupper(Str::random(8)) . '-' . time();

        $transaction = Transaction::create([
            'user_id'        => $product->user_id,
            'order_id'       => $orderId,
            'amount'         => $amount,
            'status'         => 'pending',
            'payment_method' => $request->payment_method,
            'notes'          => json_encode([
                'buyer_name'    => $request->buyer_name,
                'buyer_email'   => $request->buyer_email,
                'buyer_phone'   => $request->buyer_phone,
                'buyer_address' => $request->buyer_address ?? null,
                'buyer_notes'   => $request->buyer_notes ?? null,
                'product_id'    => $product->id,
                'product_title' => $product->title,
                'product_type'  => $product->product_type,
                'qty'           => $qty,
                'unit_price'    => $unitPrice,
                'shipping_cost' => $shippingCost,
                'subtotal'      => $subtotal,
                'destination_village_code' => $request->destination_village_code ?? null,
                'destination_label' => $request->destination_label ?? null,
                'selected_courier' => $request->selected_courier ?: 'OTHER',
                'selected_service' => $request->selected_service ?: 'Standard',
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
            $courierLabel = strtoupper((string) ($request->selected_courier ?: 'OTHER'));
            $snapParams['item_details'][] = [
                'id'       => 'shipping',
                'price'    => $shippingCost,
                'quantity' => 1,
                'name'     => 'Ongkir ' . $courierLabel,
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

        // Jangan proses ulang yang sudah settlement
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
    // SUCCESS — fallback cek status Midtrans langsung
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

        // Jika masih pending, cek langsung ke Midtrans
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

        $productId = $notes['product_id'] ?? null;
        $unitPrice = $notes['unit_price'] ?? 0;

        // ── DEBUG LOG ──
        Log::info('DEBUG handleSuccessfulPayment', [
            'order_id'   => $transaction->order_id,
            'notes'      => $notes,
            'unit_price' => $unitPrice,
            'productId'  => $productId,
        ]);

        // ── Guard double-processing ──
        $alreadyProcessed = false;

        try {
            $alreadyProcessed = ProductSale::where('product_id', $productId)
                ->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(options, '$.order_id')) = ?", [$transaction->order_id])
                ->exists();
        } catch (\Exception $e) {
            Log::warning('Guard check gagal (kolom options belum ada?): ' . $e->getMessage());
            $alreadyProcessed = false;
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

        // 1. Catat product_sales — selalu sertakan price di semua kondisi
        try {
            Log::info('DEBUG sebelum ProductSale::create', ['price' => $unitPrice]);
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
            Log::info('DEBUG ProductSale berhasil dibuat', ['price' => $unitPrice]);
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

        // 3. Kirim file digital
        if ($product->product_type === 'digital') {
            $this->sendDigitalFile($product, $notes, $transaction);
        }

        // 4. Tambah saldo seller
        $seller = User::find($transaction->user_id);
        if ($seller) {
            $seller->increment('balance', (int) $transaction->amount);
            Log::info("✅ Saldo {$seller->name} +Rp{$transaction->amount} dari order {$transaction->order_id}");

            // ── Notifikasi pesanan masuk ──
            \App\Models\Notification::create([
                'user_id' => $seller->id,
                'type'    => 'order',
                'title'   => 'Pesanan Baru Masuk!',
                'message' => '📦 ' . ($notes['buyer_name'] ?? 'Pembeli') . ' memesan ' . ($notes['product_title'] ?? $product->title) . ' (#' . $transaction->order_id . ')',
                'icon'    => 'fas fa-shopping-bag',
                'link'    => '/riwayat',
                'is_read' => false,
            ]);

            // ── Notifikasi pembayaran diterima ──
            \App\Models\Notification::create([
                'user_id' => $seller->id,
                'type'    => 'payment',
                'title'   => 'Pembayaran Diterima!',
                'message' => '💰 Pembayaran #' . $transaction->order_id . ' sebesar Rp' . number_format((int) $transaction->amount, 0, ',', '.') . ' berhasil dikonfirmasi.',
                'icon'    => 'fas fa-circle-check',
                'link'    => '/riwayat',
                'is_read' => false,
            ]);

        } else {
            Log::error("❌ Seller user_id={$transaction->user_id} tidak ditemukan.");
        }
    }

    // =========================================================
    // KIRIM FILE DIGITAL
    // =========================================================
    private function sendDigitalFile(Product $product, array $notes, Transaction $transaction): void
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
        } catch (\Exception $e) {
            Log::error('Gagal kirim email digital: ' . $e->getMessage());
        }
    }
}
