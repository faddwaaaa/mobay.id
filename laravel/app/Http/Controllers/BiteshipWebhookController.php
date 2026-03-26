<?php

namespace App\Http\Controllers;

use App\Mail\PhysicalOrderStatusUpdatedOrderStatusUpdated;
use App\Models\PhysicalOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class BiteshipWebhookController extends Controller
{
    /**
     * Status yang tidak perlu kirim email ke pembeli (terlalu teknis / spam).
     * Sesuaikan dengan kebutuhanmu untuk hemat kuota Brevo.
     */
    private const SKIP_EMAIL_STATUSES = [
        'allocated',
        'picking_up',
        'courier_not_found',
    ];

    /**
     * Handle incoming Biteship webhook.
     * Biteship POST ke endpoint ini setiap kali status pengiriman berubah.
     *
     * Docs: https://biteship.com/id/docs/api/webhook
     */
    public function handle(Request $request)
    {
        // 1. Verifikasi webhook secret dari Biteship
        // $secret = $request->header('x-api-key');
        // if ($secret !== config('services.biteship.webhook_secret')) {
        //     Log::warning('[Biteship] Unauthorized webhook attempt', [
        //         'ip' => $request->ip(),
        //     ]);
        //     return response()->json(['message' => 'Unauthorized'], 401);
        // }

        $payload = $request->all();

        Log::info('[Biteship] Webhook received', $payload);

        // 2. Ambil data dari payload Biteship
        // Sesuaikan field ini dengan struktur payload Biteship kamu
        $trackingNumber = $payload['order']['courier']['tracking_id']   ?? null;
        $biteshipStatus = $payload['order']['courier']['status']        ?? null;
        $biteshipOrderId= $payload['order']['id']                       ?? null;

        if (! $trackingNumber || ! $biteshipStatus) {
            return response()->json(['message' => 'Payload tidak lengkap'], 400);
        }

        // 3. Cari order berdasarkan tracking number
        $order = PhysicalOrder::where('tracking_number', $trackingNumber)
                      ->orWhere('biteship_order_id', $biteshipOrderId)
                      ->first();

        if (! $order) {
            Log::warning('[Biteship] Order tidak ditemukan', [
                'tracking_number' => $trackingNumber,
                'biteship_order_id' => $biteshipOrderId,
            ]);
            // Tetap return 200 biar Biteship tidak retry terus
            return response()->json(['message' => 'Order tidak ditemukan'], 200);
        }

        // 4. Map status Biteship ke status internal kamu
        $internalStatus = $this->mapStatus($biteshipStatus);

        // 5. Update status order
        $order->update([
            'status'           => $internalStatus,
            'biteship_status'  => $biteshipStatus,
            'last_updated_at'  => now(),
        ]);

        // 6. Kirim email notifikasi (skip status yang tidak penting)
        if (! in_array($biteshipStatus, self::SKIP_EMAIL_STATUSES)) {
            try {
                Mail::to($order->buyer_email)
                    ->send(new PhysicalOrderStatusUpdated($order, $biteshipStatus));
            } catch (\Exception $e) {
                Log::error('[Biteship] Gagal kirim email status update', [
                    'order_id' => $order->id,
                    'error'    => $e->getMessage(),
                ]);
                // Jangan lempar exception ke sini, biar Biteship dapat response 200
            }
        }

        return response()->json(['message' => 'OK'], 200);
    }

    /**
     * Map status dari Biteship ke status internal order kamu.
     * Sesuaikan sesuai status yang ada di tabel orders kamu.
     *
     * Referensi status Biteship:
     * https://biteship.com/id/docs/api/order-status
     */
    private function mapStatus(string $biteshipStatus): string
    {
        return match($biteshipStatus) {
            'allocated',
            'picking_up',
            'picked_up'         => 'processing',
            'dropping_off',
            'in_transit'        => 'shipped',
            'delivered'         => 'delivered',
            'return_in_transit',
            'returned'          => 'returned',
            'cancelled'         => 'cancelled',
            default             => 'shipped', // fallback aman
        };
    }
}