<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\PhysicalOrderShipped;
use App\Models\PhysicalOrder;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class PhysicalOrderShipmentController extends Controller
{
    /**
     * Form input resi untuk order tertentu.
     */
    public function edit(PhysicalOrder $physicalOrder)
    {
        abort_if($physicalOrder->status !== 'paid', 404);

        return view('admin.physical-orders.shipment', [
            'order' => $physicalOrder,
        ]);
    }

    /**
     * Simpan resi, update status, kirim email ke pembeli.
     */
    public function update(Request $request, PhysicalOrder $physicalOrder)
    {
        abort_if(in_array($physicalOrder->status, ['shipped', 'delivered', 'cancelled']), 422);

        $validated = $request->validate([
            'tracking_number'   => ['required', 'string', 'max:100'],
            'courier_code'      => ['required', 'string', 'max:50'],
            'courier_service'   => ['nullable', 'string', 'max:100'],
            'estimated_arrival' => ['nullable', 'string', 'max:100'],
        ]);

        $physicalOrder->update([
            'tracking_number'   => $validated['tracking_number'],
            'courier_code'      => $validated['courier_code'],
            'courier_service'   => $validated['courier_service'] ?? null,
            'estimated_arrival' => $validated['estimated_arrival'] ?? null,
            'tracking_url'      => 'https://biteship.com/id/track/' . $validated['tracking_number'],
            'status'            => 'shipped',
            'shipped_at'        => now(),
        ]);

        Mail::to($physicalOrder->buyer_email)
            ->send(new PhysicalOrderShipped($physicalOrder->fresh()));

        // ✅ Redirect ke halaman detail pesanan penjual
        $transaction = Transaction::where('order_id', $physicalOrder->midtrans_order_id)->first();

        if ($transaction) {
            return redirect()
                ->route('orders.show', ['id' => $transaction->id])
                ->with('success', "Resi disimpan & email dikirim ke {$physicalOrder->buyer_email}");
        }

        return back()->with('success', "Resi disimpan & email dikirim ke {$physicalOrder->buyer_email}");
    }
}