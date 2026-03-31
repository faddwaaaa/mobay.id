<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Transaction;
use App\Models\PhysicalOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Pagination\LengthAwarePaginator;

class OrderController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $seller = Auth::user();
        $type = $request->get('type', 'fisik');
        if (!in_array($type, ['fisik', 'digital'], true)) {
            $type = 'fisik';
        }

        $sellerProducts = Product::where('user_id', $seller->id)
            ->get(['id', 'title', 'product_type'])
            ->keyBy('id');

        $ordersCollection = Transaction::where('status', 'settlement')
            ->latest()
            ->get();

        $orders = $ordersCollection->filter(function ($order) use ($sellerProducts, $type) {
            $notes = is_string($order->notes) ? json_decode($order->notes, true) : ($order->notes ?? []);
            $productId = (int) ($notes['product_id'] ?? 0);
            if (!$productId || !$sellerProducts->has($productId)) {
                return false;
            }

            $productType = (string) ($notes['product_type'] ?? $sellerProducts[$productId]->product_type ?? '');
            return $productType === $type;
        })->map(function ($order) use ($sellerProducts) {
            $notes = is_string($order->notes) ? json_decode($order->notes, true) : ($order->notes ?? []);
            $productId = (int) ($notes['product_id'] ?? 0);
            $order->order_notes = $notes;
            $order->order_product = $sellerProducts->get($productId);
            return $order;
        })->values();

        $perPage = 10;
        $currentPage = (int) $request->get('page', 1);
        $paginatedOrders = new LengthAwarePaginator(
            $orders->forPage($currentPage, $perPage),
            $orders->count(),
            $perPage,
            $currentPage,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return view('orders.index', [
            'orders' => $paginatedOrders,
            'type' => $type,
        ]);
    }

    public function show(int $id)
    {
        $order = Transaction::findOrFail($id);
        $notes = is_string($order->notes) ? json_decode($order->notes, true) : ($order->notes ?? []);
        $productId = (int) ($notes['product_id'] ?? 0);
        $product = $productId ? Product::find($productId) : null;

        if (!$product || $product->user_id !== Auth::id()) {
            abort(403);
        }

        $buyerPhone = preg_replace('/\D+/', '', (string) ($notes['buyer_phone'] ?? ''));
        if (str_starts_with($buyerPhone, '0')) {
            $buyerPhone = '62' . substr($buyerPhone, 1);
        } elseif (!str_starts_with($buyerPhone, '62')) {
            $buyerPhone = '62' . $buyerPhone;
        }

        $waMessage = rawurlencode(
            'Halo ' . ($notes['buyer_name'] ?? 'Kak') .
            ', terkait pesanan #' . $order->order_id .
            ' untuk produk "' . ($notes['product_title'] ?? $product->title) . '"'
        );

        // ✅ Ambil PhysicalOrder yang matching — untuk form input resi
        $physicalOrder = null;
        if (($notes['product_type'] ?? '') === 'fisik') {
            $physicalOrder = PhysicalOrder::where('midtrans_order_id', $order->order_id)->first();
        }

        return view('orders.show', [
            'order'         => $order,
            'notes'         => $notes,
            'product'       => $product,
            'waLink'        => $buyerPhone ? "https://wa.me/{$buyerPhone}?text={$waMessage}" : null,
            'physicalOrder' => $physicalOrder,  // ✅ null kalau digital / belum ada
        ]);
    }
}