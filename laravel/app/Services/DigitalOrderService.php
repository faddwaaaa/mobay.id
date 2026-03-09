<?php

namespace App\Services;

use App\Models\DigitalOrder;
use App\Models\DigitalProduct;
use App\Models\DownloadToken;
use App\Mail\DigitalProductDelivery;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class DigitalOrderService
{
    /**
     * Buat order baru untuk produk digital
     */
    public function createOrder(DigitalProduct $product, array $buyerData): DigitalOrder
    {
        $order = DigitalOrder::create([
            'digital_product_id' => $product->id,
            'buyer_email'        => $buyerData['email'],
            'buyer_name'         => $buyerData['name'],
            'amount'             => $product->price,
            'status'             => 'pending',
        ]);

        return $order;
    }

    /**
     * Tandai order sebagai paid dan kirim email dengan link download
     */
    public function completeOrder(DigitalOrder $order): DownloadToken
    {
        // Update status order
        $order->update(['status' => 'paid']);

        // ✅ Load relasi product agar tidak null saat dipakai di email
        $order->load('product');

        // Buat token download yang unik
        $token = DownloadToken::create([
            'digital_order_id' => $order->id,
            'buyer_email'      => $order->buyer_email,
            'max_downloads'    => 1,
            'expires_at'       => Carbon::now()->addDays(7),
        ]);

        // ✅ Load relasi expires_at agar siap dipakai di email
        $token->refresh();

        // Kirim email ke pembeli
        Mail::to($order->buyer_email)
            ->send(new DigitalProductDelivery($order, $token));

        return $token;
    }

    /**
     * Proses download file - validasi token lalu stream file
     */
    public function processDownload(string $tokenString, string $email): array
    {
        $token = DownloadToken::where('token', $tokenString)
            ->where('buyer_email', $email)
            ->with('order.product')
            ->first();

        if (!$token) {
            return ['success' => false, 'message' => 'Token tidak valid atau email tidak sesuai.'];
        }

        if ($token->isExpired()) {
            return ['success' => false, 'message' => 'Link download sudah kadaluarsa.'];
        }

        if ($token->isMaxDownloads()) {
            return ['success' => false, 'message' => 'Batas download sudah tercapai (maksimal ' . $token->max_downloads . 'x).'];
        }

        $product = $token->order->product;

        if (!Storage::disk('public')->exists($product->file_path)) {
            return ['success' => false, 'message' => 'File tidak ditemukan. Hubungi seller.'];
        }

        // Tambah hitungan download
        $token->increment('download_count');

        return [
            'success'   => true,
            'file_path' => $product->file_path,
            'file_name' => $product->file_name,
            'remaining' => $token->max_downloads - $token->download_count,
        ];
    }

    /**
     * Validasi token tanpa proses download (untuk halaman konfirmasi)
     */
    public function validateToken(string $tokenString): array
    {
        $token = DownloadToken::where('token', $tokenString)
            ->with('order.product')
            ->first();

        if (!$token) {
            return ['valid' => false, 'message' => 'Token tidak ditemukan.'];
        }

        if ($token->isExpired()) {
            return ['valid' => false, 'message' => 'Link sudah kadaluarsa.'];
        }

        if ($token->isMaxDownloads()) {
            return ['valid' => false, 'message' => 'Batas download tercapai.'];
        }

        return [
            'valid'      => true,
            'product'    => $token->order->product->name,
            'remaining'  => $token->max_downloads - $token->download_count,
            'expires_at' => $token->expires_at->format('d M Y H:i'),
            'token'      => $token,
        ];
    }
}