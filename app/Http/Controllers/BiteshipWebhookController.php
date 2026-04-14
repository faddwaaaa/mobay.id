<?php

namespace App\Http\Controllers;

use App\Mail\PhysicalOrderStatusUpdated;
use App\Models\PhysicalOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class BiteshipWebhookController extends Controller
{
    private const SKIP_EMAIL_STATUSES = [
        'allocated',
        'picking_up',
        'courier_not_found',
    ];

    public function handle(Request $request)
    {
        // ✅ 1. HANDLE VERIFICATION (body kosong dari Biteship)
        if (empty($request->all())) {
            Log::info('[Biteship] Verification ping received');
            return response('ok', 200);
        }

        // ✅ 2. VALIDASI SECRET (hanya kalau ada payload)
        $secret = $request->header('x-api-key');

        if ($secret !== config('services.biteship.webhook_secret')) {
            Log::warning('[Biteship] Unauthorized webhook attempt', [
                'ip' => $request->ip(),
                'headers' => $request->headers->all(),
            ]);

            // ⚠️ tetap balikin OK biar tidak retry terus
            return response('ok', 200);
        }

        try {
            $payload = $request->all();

            Log::info('[Biteship] Webhook received', $payload);

            // ✅ 3. Ambil data
            $trackingNumber = $payload['order']['courier']['tracking_id'] ?? null;
            $biteshipStatus = $payload['order']['courier']['status'] ?? null;
            $biteshipOrderId = $payload['order']['id'] ?? null;

            if (! $trackingNumber || ! $biteshipStatus) {
                Log::warning('[Biteship] Payload tidak lengkap', $payload);
                return response('ok', 200);
            }

            // ✅ 4. Cari order
            $order = PhysicalOrder::where('tracking_number', $trackingNumber)
                ->orWhere('biteship_order_id', $biteshipOrderId)
                ->first();

            if (! $order) {
                Log::warning('[Biteship] Order tidak ditemukan', [
                    'tracking_number' => $trackingNumber,
                    'biteship_order_id' => $biteshipOrderId,
                ]);
                return response('ok', 200);
            }

            // ✅ 5. Map status
            $internalStatus = $this->mapStatus($biteshipStatus);

            // ✅ 6. Update
            $order->update([
                'status'          => $internalStatus,
                'biteship_status' => $biteshipStatus,
                'last_updated_at' => now(),
            ]);

            // ✅ 7. Email (optional)
            if (! in_array($biteshipStatus, self::SKIP_EMAIL_STATUSES)) {
                try {
                    Mail::to($order->buyer_email)
                        ->send(new PhysicalOrderStatusUpdated($order, $biteshipStatus));
                } catch (\Exception $e) {
                    Log::error('[Biteship] Email gagal', [
                        'order_id' => $order->id,
                        'error' => $e->getMessage(),
                    ]);
                }
            }

        } catch (\Throwable $e) {
            Log::error('[Biteship] Fatal error', [
                'error' => $e->getMessage(),
            ]);
        }

        // ✅ WAJIB: selalu return OK
        return response('ok', 200);
    }

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
            default             => 'shipped',
        };
    }
}