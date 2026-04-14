<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DigitalOrder;
use App\Models\PhysicalOrder;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $digitalOrders  = collect($this->getDigitalQuery($request)->get()->map(fn($o) => $this->normalizeOrder($o, 'digital'))->all());
        $physicalOrders = collect($this->getPhysicalQuery($request)->get()->map(fn($o) => $this->normalizeOrder($o, 'physical'))->all());

        // Gabungkan dan sort by created_at
        $merged = $digitalOrders->concat($physicalOrders)->sortByDesc('created_at')->values();

        // Manual paginate
        $page    = $request->get('page', 1);
        $perPage = 15;
        $total   = $merged->count();
        $items   = $merged->slice(($page - 1) * $perPage, $perPage)->values();
        $orders  = new \Illuminate\Pagination\LengthAwarePaginator($items, $total, $perPage, $page, [
            'path'  => $request->url(),
            'query' => $request->query(),
        ]);

        $dTotal   = DigitalOrder::count();
        $pTotal   = PhysicalOrder::count();
        $allTotal = $dTotal + $pTotal;
        $success  = DigitalOrder::where('status', 'paid')->count()
                  + PhysicalOrder::where('status', 'paid')->count();

        $stats = [
            'total_orders'     => $allTotal,
            'new_orders_today' => DigitalOrder::whereDate('created_at', today())->count()
                                + PhysicalOrder::whereDate('created_at', today())->count(),
            'success_orders'   => $success,
            'success_percent'  => $allTotal > 0 ? round($success / $allTotal * 100) : 0,
            'pending_orders'   => DigitalOrder::where('status', 'pending')->count()
                                + PhysicalOrder::where('status', 'pending')->count(),
            'dispute_orders'   => DigitalOrder::where('status', 'dispute')->count()
                                + PhysicalOrder::where('status', 'dispute')->count(),
        ];

        return view('admin.orders.index', compact('orders', 'stats'));
    }

    public function show(int $id)
    {
        $digitalOrder  = DigitalOrder::with(['product.product.owner', 'downloadToken'])->find($id);
        $physicalOrder = $digitalOrder ? null : PhysicalOrder::with(['product', 'seller'])->find($id);

        if (!$digitalOrder && !$physicalOrder) {
            abort(404);
        }

        if ($digitalOrder) {
            $order = $this->normalizeOrder($digitalOrder, 'digital');
        } else {
            $order = $this->normalizeOrder($physicalOrder, 'physical');
        }

        return view('admin.orders.show', compact('order'));
    }

    public function export(Request $request): StreamedResponse
    {
        $digitalOrders  = collect($this->getDigitalQuery($request)->get()->map(fn($o) => $this->normalizeOrder($o, 'digital'))->all());
        $physicalOrders = collect($this->getPhysicalQuery($request)->get()->map(fn($o) => $this->normalizeOrder($o, 'physical'))->all());
        $orders         = $digitalOrders->concat($physicalOrders)->sortByDesc('created_at');

        return response()->stream(function () use ($orders) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['ID', 'Tipe', 'Produk', 'Pembeli', 'Email Pembeli', 'Seller', 'Total', 'Status', 'Tanggal']);
            foreach ($orders as $o) {
                fputcsv($handle, [
                    str_pad($o['id'], 5, '0', STR_PAD_LEFT),
                    $o['product_type'],
                    $o['product_name'],
                    $o['buyer_name'],
                    $o['buyer_email'],
                    $o['seller_name'],
                    $o['total'],
                    $o['status'],
                    $o['created_at'],
                ]);
            }
            fclose($handle);
        }, 200, [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="orders-' . now()->format('Y-m-d') . '.csv"',
        ]);
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────

    private function getDigitalQuery(Request $request)
    {
        $query = DigitalOrder::with('product.product.owner');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                  ->orWhere('buyer_name', 'like', "%{$search}%")
                  ->orWhere('buyer_email', 'like', "%{$search}%")
                  ->orWhere('order_code', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('type') && $request->type !== 'digital') {
            return $query->whereRaw('0=1');
        }

        return $query;
    }

    private function getPhysicalQuery(Request $request)
    {
        $query = PhysicalOrder::with('seller');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                  ->orWhere('buyer_name', 'like', "%{$search}%")
                  ->orWhere('buyer_email', 'like', "%{$search}%")
                  ->orWhere('order_code', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('type') && $request->type !== 'physical') {
            return $query->whereRaw('0=1');
        }

        return $query;
    }

    /**
     * Normalisasi DigitalOrder & PhysicalOrder ke array seragam untuk view.
     */
    private function normalizeOrder($order, string $type): array
    {
        if ($type === 'digital') {
            // Seller: DigitalOrder -> DigitalProduct -> Product -> User (owner)
            $seller = $order->product->product->owner ?? null;

            return [
                'id'              => $order->id,
                'order_code'      => $order->order_code,
                'product_type'    => 'digital',
                'product_name'    => $order->product->name ?? 'Produk dihapus',
                'product_id'      => $order->digital_product_id,
                'buyer_name'      => $order->buyer_name,
                'buyer_email'     => $order->buyer_email,
                'buyer_initials'  => strtoupper(substr($order->buyer_name ?? 'UN', 0, 2)),
                'seller_name'     => $seller->name ?? '-',
                'seller_email'    => $seller->email ?? '-',
                'seller_initials' => strtoupper(substr($seller->name ?? 'UN', 0, 2)),
                'seller_username' => $seller->username ?? '-',
                'seller_id'       => $seller->id ?? null,
                'total'           => $order->amount,
                'subtotal'        => $order->amount,
                'platform_fee'    => 0,
                'payment_method'  => $order->payment_method ?? '-',
                'status'          => $order->status,
                'created_at'      => $order->created_at,
                'paid_at'         => $order->paid_at ?? null,
                'dispute_reason'  => $order->dispute_reason ?? null,
                'dispute_at'      => $order->dispute_at ?? null,
                'refunded_at'     => $order->refunded_at ?? null,
                '_model'          => 'digital',
            ];
        }

        // physical
        return [
            'id'              => $order->id,
            'order_code'      => $order->order_code,
            'product_type'    => 'physical',
            'product_name'    => $order->product_name,
            'product_id'      => $order->product_id,
            'buyer_name'      => $order->buyer_name,
            'buyer_email'     => $order->buyer_email,
            'buyer_phone'     => $order->buyer_phone ?? '-',
            'buyer_initials'  => strtoupper(substr($order->buyer_name ?? 'UN', 0, 2)),
            'seller_name'     => optional($order->seller)->name ?? '-',
            'seller_email'    => optional($order->seller)->email ?? '-',
            'seller_initials' => strtoupper(substr(optional($order->seller)->name ?? 'UN', 0, 2)),
            'seller_username' => optional($order->seller)->username ?? '-',
            'seller_id'       => $order->seller_id ?? null,
            'total'           => $order->total_amount,
            'subtotal'        => $order->product_price ?? $order->total_amount,
            'platform_fee'    => 0,
            'payment_method'  => $order->payment_method ?? '-',
            'status'          => $order->status,
            'created_at'      => $order->created_at,
            'paid_at'         => $order->paid_at ?? null,
            'dispute_reason'  => $order->dispute_reason ?? null,
            'dispute_at'      => $order->dispute_at ?? null,
            'refunded_at'     => $order->refunded_at ?? null,
            '_model'          => 'physical',
        ];
    }
}