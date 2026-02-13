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
        // Konfigurasi Midtrans dari config yang sudah ada
        Config::$serverKey    = config('midtrans.server_key');
        Config::$clientKey    = config('midtrans.client_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized  = config('midtrans.is_sanitized', true);
        Config::$is3ds        = config('midtrans.is_3ds', true);
    }

    // =========================================================
    // SHOW — halaman checkout
    // =========================================================
    public function show($productId)
    {
        $product = Product::with(['images', 'files'])->findOrFail($productId);
        $seller  = User::find($product->user_id);

        return view('checkout', compact('product', 'seller'));
    }

    // =========================================================
    // PROCESS — buat transaksi & ambil snap_token
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
            // Alamat wajib untuk produk fisik, divalidasi di bawah
        ]);

        $product = Product::with('files')->findOrFail($request->product_id);

        // === Validasi stok (produk fisik) ===
        if ($product->product_type === 'umkm') {
            if ($product->stock !== null && $product->stock < $request->qty) {
                return response()->json([
                    'error'   => true,
                    'message' => 'Stok tidak mencukupi. Tersedia: ' . $product->stock,
                ], 422);
            }

            if ($product->purchase_limit && $request->qty > $product->purchase_limit) {
                return response()->json([
                    'error'   => true,
                    'message' => 'Batas pembelian maks. ' . $product->purchase_limit . ' per transaksi.',
                ], 422);
            }

            if (empty($request->buyer_address)) {
                return response()->json([
                    'error'   => true,
                    'message' => 'Alamat pengiriman wajib diisi untuk produk fisik.',
                ], 422);
            }
        }

        $qty        = $product->product_type === 'digital' ? 1 : (int) $request->qty;
        $unitPrice  = (int) ($product->discount ?? $product->price);
        $amount     = $unitPrice * $qty;
        $orderId    = 'PAYOU-' . strtoupper(Str::random(8)) . '-' . time();

        // === Simpan transaksi awal (pending) ===
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
            ]),
            'ip_address' => $request->ip(),
        ]);

        // === Buat payload Midtrans ===
        $itemDetails = [
            [
                'id'       => (string) $product->id,
                'price'    => $unitPrice,
                'quantity' => $qty,
                'name'     => substr($product->title, 0, 50),
            ]
        ];

        $customerDetails = [
            'first_name' => $request->buyer_name,
            'email'      => $request->buyer_email,
            'phone'      => $request->buyer_phone,
        ];

        if ($product->product_type === 'umkm' && $request->buyer_address) {
            $customerDetails['shipping_address'] = [
                'first_name' => $request->buyer_name,
                'phone'      => $request->buyer_phone,
                'address'    => $request->buyer_address,
            ];
        }

        $snapParams = [
            'transaction_details' => [
                'order_id'      => $orderId,
                'gross_amount'  => $amount,
            ],
            'item_details'       => $itemDetails,
            'customer_details'   => $customerDetails,
            'enabled_payments'   => $this->getEnabledPayments($request->payment_method),
        ];

        try {
            $snapToken = Snap::getSnapToken($snapParams);
        } catch (\Exception $e) {
            Log::error('Midtrans Snap Error: ' . $e->getMessage());
            return response()->json([
                'error'   => true,
                'message' => 'Gagal menghubungi payment gateway. Coba lagi.',
            ], 500);
        }

        return response()->json([
            'snap_token' => $snapToken,
            'order_id'   => $orderId,
        ]);
    }

    // =========================================================
    // WEBHOOK — notifikasi dari Midtrans
    // =========================================================
    public function webhook(Request $request)
    {
        try {
            $notif = new Notification();
        } catch (\Exception $e) {
            Log::error('Midtrans webhook error: ' . $e->getMessage());
            return response('Error', 500);
        }

        $orderId           = $notif->order_id;
        $transactionStatus = $notif->transaction_status;
        $fraudStatus       = $notif->fraud_status;
        $paymentType       = $notif->payment_type;

        $transaction = Transaction::where('order_id', $orderId)->first();
        if (!$transaction) {
            return response('Not found', 404);
        }

        // Tentukan status akhir
        $finalStatus = match (true) {
            $transactionStatus === 'capture' && $fraudStatus === 'accept' => 'settlement',
            $transactionStatus === 'settlement'                            => 'settlement',
            $transactionStatus === 'pending'                               => 'pending',
            in_array($transactionStatus, ['deny', 'cancel', 'expire'])     => $transactionStatus,
            $transactionStatus === 'failure'                               => 'failed',
            default                                                        => $transaction->status,
        };

        $transaction->update([
            'status'             => $finalStatus,
            'payment_method'     => $paymentType ?? $transaction->payment_method,
            'transaction_id'     => $notif->transaction_id ?? null,
            'midtrans_response'  => json_encode($notif->getResponse()),
        ]);

        // === Jika pembayaran berhasil ===
        if ($finalStatus === 'settlement') {
            $this->handleSuccessfulPayment($transaction);
        }

        return response('OK', 200);
    }

    // =========================================================
    // SUCCESS PAGE
    // =========================================================
    public function success(Request $request)
    {
        $transaction = Transaction::where('order_id', $request->order_id)->first();
        $notes       = $transaction ? json_decode($transaction->notes, true) : [];

        return view('checkout-success', compact('transaction', 'notes'));
    }

    // =========================================================
    // PENDING PAGE
    // =========================================================
    public function pending(Request $request)
    {
        $transaction = Transaction::where('order_id', $request->order_id)->first();
        $notes       = $transaction ? json_decode($transaction->notes, true) : [];

        return view('checkout-pending', compact('transaction', 'notes'));
    }

    // =========================================================
    // PRIVATE — Handle setelah pembayaran sukses
    // =========================================================
    private function handleSuccessfulPayment(Transaction $transaction): void
    {
        $notes   = json_decode($transaction->notes, true);
        $product = Product::with('files')->find($notes['product_id'] ?? null);

        if (!$product) return;

        // Catat product_sales
        ProductSale::create([
            'product_id' => $product->id,
            'qty'        => $notes['qty'] ?? 1,
            'options'    => json_encode([
                'order_id'      => $transaction->order_id,
                'buyer_name'    => $notes['buyer_name'] ?? '',
                'buyer_email'   => $notes['buyer_email'] ?? '',
                'buyer_address' => $notes['buyer_address'] ?? null,
                'buyer_notes'   => $notes['buyer_notes'] ?? null,
            ]),
        ]);

        // Kurangi stok untuk produk fisik
        if ($product->product_type === 'umkm' && $product->stock !== null) {
            $product->decrement('stock', $notes['qty'] ?? 1);
        }

        // Kirim file digital via email
        if ($product->product_type === 'digital') {
            $this->sendDigitalFile($product, $notes, $transaction);
        }

        // Tambah saldo seller (sama seperti logika payout yang sudah ada)
        $seller = User::find($transaction->user_id);
        if ($seller) {
            // Potong biaya platform jika ada, sisanya masuk balance seller
            // Sesuaikan dengan logika komisi yang sudah ada di app
            $sellerAmount = $transaction->amount; // full amount, sesuaikan jika ada komisi
            $seller->increment('balance', $sellerAmount);
        }
    }

    // =========================================================
    // PRIVATE — Kirim file digital via email
    // =========================================================
    private function sendDigitalFile(Product $product, array $notes, Transaction $transaction): void
    {
        $buyerEmail = $notes['buyer_email'] ?? null;
        if (!$buyerEmail) return;

        $files = $product->files;
        if (!$files || $files->isEmpty()) return;

        try {
            Mail::send('emails.digital-product', [
                'buyerName'   => $notes['buyer_name'] ?? 'Pembeli',
                'productTitle'=> $product->title,
                'orderId'     => $transaction->order_id,
                'files'       => $files,
            ], function ($mail) use ($buyerEmail, $notes, $product, $files) {
                $mail->to($buyerEmail, $notes['buyer_name'] ?? 'Pembeli')
                     ->subject('📦 Produk Digital Anda: ' . $product->title);

                // Lampirkan file
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

    // =========================================================
    // PRIVATE — Daftar metode pembayaran yang dienable di Snap
    // =========================================================
    private function getEnabledPayments(string $preferred): array
    {
        // Tampilkan semua, biarkan user pilih di Snap jika mau ganti
        return [
            'gopay', 'qris', 'shopeepay',
            'bca_va', 'bni_va', 'bri_va', 'permata_va', 'other_va',
            'echannel', 'credit_card',
        ];
    }
}