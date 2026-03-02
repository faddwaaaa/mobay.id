<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show orders page (physical products only)
     * GET /pesanan
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Ambil ID produk fisik milik user
        $myPhysicalProductIds = \App\Models\Product::where('user_id', $user->id)
            ->where('product_type', 'umkm')
            ->pluck('id')
            ->toArray();
        
        // Ambil semua transaksi settlement
        $allOrders = Transaction::where('status', 'settlement')
            ->latest()
            ->get();
        
        // Filter hanya yang produknya milik user
        $orders = $allOrders->filter(function($order) use ($myPhysicalProductIds) {
            $notes = is_string($order->notes) ? json_decode($order->notes, true) : ($order->notes ?? []);
            $productId = $notes['product_id'] ?? null;
            
            // Hanya tampilkan jika product_id ada di list produk fisik user
            return $productId && in_array($productId, $myPhysicalProductIds);
        });
        
        // Manual pagination
        $perPage = 10;
        $currentPage = request()->get('page', 1);
        $orders = new \Illuminate\Pagination\LengthAwarePaginator(
            $orders->forPage($currentPage, $perPage),
            $orders->count(),
            $perPage,
            $currentPage,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return view('orders', compact('orders'));
    }

    /**
     * Update order status
     * POST /pesanan/{id}/update-status
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,processing,shipped,completed'
        ]);

        $order = Transaction::findOrFail($id);
        
        // Verify ownership via notes
        $notes = is_string($order->notes) ? json_decode($order->notes, true) : ($order->notes ?? []);
        $productId = $notes['product_id'] ?? null;
        
        if ($productId) {
            $product = \App\Models\Product::find($productId);
            if (!$product || $product->user_id !== Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 403);
            }
        }

        $order->update(['status' => $request->status]);

        return response()->json([
            'success' => true,
            'message' => 'Status pesanan berhasil diupdate',
            'new_status' => $request->status
        ]);
    }
}