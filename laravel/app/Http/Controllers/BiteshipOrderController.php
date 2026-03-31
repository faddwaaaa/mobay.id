<?php

namespace App\Http\Controllers;

use App\Mail\PhysicalOrderShipped;
use App\Models\PhysicalOrder;
use App\Models\Product;
use App\Models\Transaction;
use App\Services\BiteshipService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class BiteshipOrderController extends Controller
{
    public function __construct(private BiteshipService $biteship) {}

    public function createOrder(PhysicalOrder $physicalOrder)
    {
        // 1. Guard
        if ($physicalOrder->status !== 'paid') {
            return back()->with('error', 'Pesanan ini tidak bisa diproses.');
        }

        $seller  = Auth::user();
        $product = Product::find($physicalOrder->product_id);

        // 2. Validasi seller punya origin
        if (!$seller->origin_village_code) {
            return back()->with('error', 'Lengkapi alamat asal pengiriman di Settings → Shipping terlebih dahulu.');
        }

        // 3. Build payload Biteship
        $courierCode    = strtolower($physicalOrder->courier_code ?? 'jne');
        $courierService = $this->normalizeCourierService(
            strtolower($physicalOrder->courier_service ?? 'reg'),
            $courierCode
        );
        $weight = max(1, (int) (($product->weight ?? 100) * ($physicalOrder->quantity ?? 1)));

        // Resolve postal codes
        $originPostalCode      = $this->extractPostalCode($seller->origin_village_code);
        $destinationPostalCode = $physicalOrder->shipping_postal_code
            ?: $this->extractPostalCodeFromAddress($physicalOrder->shipping_address ?? '');

        $payload = [
            'shipper_contact_name'      => $seller->name,
            'shipper_contact_phone'     => $seller->phone ?? '08000000000',
            'shipper_contact_email'     => $seller->email,
            'shipper_organization'      => $seller->name,
            'origin_contact_name'       => $seller->name,
            'origin_contact_phone'      => $seller->phone ?? '08000000000',
            'origin_address'            => $seller->origin_city_name,
            'origin_note'               => '',
            'origin_postal_code'        => $originPostalCode,
            'destination_contact_name'  => $physicalOrder->buyer_name,
            'destination_contact_phone' => $physicalOrder->buyer_phone,
            'destination_contact_email' => $physicalOrder->buyer_email,
            'destination_address'       => $physicalOrder->shipping_address,
            'destination_postal_code'   => $destinationPostalCode,
            'destination_note'          => '',
            'courier_company'           => $courierCode,
            'courier_type'              => $courierService,
            'courier_insurance'         => 0,
            'delivery_type'             => 'now',
            'order_note'                => 'Order #' . $physicalOrder->order_code,
            'metadata'                  => [],
            'items'                     => [
                [
                    'name'        => $physicalOrder->product_name,
                    'description' => $physicalOrder->product_name,
                    'value'       => (int) $physicalOrder->product_price,
                    'length'      => 10,
                    'width'       => 10,
                    'height'      => 10,
                    'weight'      => $weight,
                    'quantity'    => $physicalOrder->quantity ?? 1,
                ],
            ],
        ];

        Log::info('[Biteship] Resolved postal codes', [
            'origin'      => $originPostalCode,
            'destination' => $destinationPostalCode,
            'courier'     => $courierCode,
            'service'     => $courierService,
        ]);

        // 4. Hit Biteship API
        $result = $this->biteship->createOrder($payload);

        if (!$result['success']) {
            $errorMsg = $result['data']['error'] ?? 'Gagal membuat order di Biteship.';
            Log::error('[Biteship] createOrder gagal', $result);
            return back()->with('error', "Biteship: {$errorMsg}");
        }

        $data           = $result['data'];
        $trackingNumber = $data['courier']['tracking_id'] ?? null;
        $biteshipId     = $data['id'] ?? null;
        $waybill        = $data['courier']['waybill_id'] ?? $trackingNumber;

        if (!$trackingNumber && !$waybill) {
            return back()->with('error', 'Biteship tidak mengembalikan nomor resi. Coba input manual.');
        }

        $resi = $waybill ?? $trackingNumber;

        // 5. Simpan ke database
        $physicalOrder->update([
            'biteship_order_id' => $biteshipId,
            'tracking_number'   => $resi,
            'tracking_url'      => 'https://biteship.com/id/track/' . $resi,
            'courier_code'      => $courierCode,
            'courier_service'   => $courierService,
            'status'            => 'shipped',
            'shipped_at'        => now(),
        ]);

        // 6. Kirim email ke pembeli
        try {
            Mail::to($physicalOrder->buyer_email)
                ->send(new PhysicalOrderShipped($physicalOrder->fresh()));
        } catch (\Exception $e) {
            Log::error('[Biteship] Email gagal', ['error' => $e->getMessage()]);
        }

        // 7. Redirect ke detail pesanan
        $transaction = Transaction::where('order_id', $physicalOrder->midtrans_order_id)->first();

        if ($transaction) {
            return redirect()
                ->route('orders.show', ['id' => (int) $transaction->id])
                ->with('success', "✅ Kurir berhasil dipesan! Resi: {$resi}");
        }

        return back()->with('success', "✅ Kurir berhasil dipesan! Resi: {$resi}");
    }

    /**
     * Normalisasi nama layanan kurir ke format yang diterima Biteship.
     * Contoh: "Reguler" → "reg", "Next Day" → "next_day"
     */
    private function normalizeCourierService(string $service, string $courier): string
    {
        $map = [
            'reguler'    => 'reg',
            'regular'    => 'reg',
            'regularr'   => 'reg',
            'next day'   => 'next_day',
            'nextday'    => 'next_day',
            'same day'   => 'same_day',
            'sameday'    => 'same_day',
            'yes'        => 'yes',
            'oke'        => 'oke',
            'jtr'        => 'jtr',
            'best'       => 'best',
            'sds'        => 'sds',
            'ons'        => 'ons',
            'eko'        => 'eko',
            'ez'         => 'ez',
        ];

        $normalized = str_replace(' ', '_', strtolower(trim($service)));

        return $map[$normalized] ?? $map[strtolower(trim($service))] ?? $normalized;
    }

    /**
     * Ambil kode pos dari village code Biteship.
     * Format: IDNP10IDNC371IDND4379IDZ53331 → 53331
     */
    private function extractPostalCode(string $villageCode): string
    {
        if (preg_match('/IDZ(\d+)/', $villageCode, $m)) {
            return $m[1];
        }
        return '00000';
    }

    /**
     * Ambil kode pos 5 digit dari string alamat.
     * Contoh: "Kaligondang, Purbalingga, Jawa Tengah. 53331" → "53331"
     */
    private function extractPostalCodeFromAddress(string $address): string
    {
        if (preg_match('/\b(\d{5})\b/', $address, $m)) {
            return $m[1];
        }
        return '00000';
    }
}