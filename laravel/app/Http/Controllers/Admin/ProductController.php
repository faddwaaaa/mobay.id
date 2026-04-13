<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\DigitalOrder;
use App\Models\PhysicalOrder;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with('owner')->withCount('sales as sales_count');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhereHas('owner', fn($u) => $u
                      ->where('name', 'like', "%{$search}%")
                      ->orWhere('username', 'like', "%{$search}%")
                  );
            });
        }

        if ($request->filled('type')) {
            $query->where('product_type', $request->type);
        }

        $products = $query->latest()->paginate(10)->withQueryString();

        $total    = Product::count();
        $digital  = Product::where('product_type', 'digital')->count();
        $physical = Product::where('product_type', 'fisik')->count();

        $stats = [
            'total_products'     => $total,
            'new_products_today' => Product::whereDate('created_at', today())->count(),
            'digital_products'   => $digital,
            'digital_percent'    => $total > 0 ? round($digital / $total * 100) : 0,
            'physical_products'  => $physical,
            'physical_percent'   => $total > 0 ? round($physical / $total * 100) : 0,
        ];

        return view('admin.products.index', compact('products', 'stats'));
    }

    public function show(int $id)
    {
        $product = Product::with(['owner', 'views'])->findOrFail($id);

        $digitalOrders = DigitalOrder::where('digital_product_id', $product->id)
            ->latest()
            ->get();

        $physicalOrders = PhysicalOrder::where('product_id', $product->id)
            ->with('seller')
            ->latest()
            ->get();

        // Hitung persen diskon: kolom discount = harga setelah diskon
        $discountPercent = null;
        if ($product->discount && $product->price > 0) {
            $discountPercent = round((1 - $product->discount / $product->price) * 100);
        }

        $stats = [
            'total_sales'   => $digitalOrders->where('status', 'paid')->count()
                             + $physicalOrders->where('status', 'paid')->count(),
            'total_revenue' => $digitalOrders->where('status', 'paid')->sum('amount')
                             + $physicalOrders->where('status', 'paid')->sum('total_amount'),
            'total_views'   => $product->views->count(),
        ];

        return view('admin.products.show', compact('product', 'digitalOrders', 'physicalOrders', 'stats', 'discountPercent'));
    }

    public function approve(int $id)
    {
        $product = Product::findOrFail($id);
        return back()->with('success', "\"{$product->title}\" berhasil di-approve.");
    }

    public function reject(int $id)
    {
        $product = Product::findOrFail($id);
        return back()->with('success', "\"{$product->title}\" telah ditolak.");
    }

    public function suspend(int $id)
    {
        $product = Product::findOrFail($id);
        return back()->with('success', "\"{$product->title}\" berhasil disuspend.");
    }

    public function unsuspend(int $id)
    {
        $product = Product::findOrFail($id);
        return back()->with('success', "\"{$product->title}\" berhasil diaktifkan.");
    }
}