<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    /**
     * =========================
     * MANAJEMEN PRODUK
     * GET /produk
     * =========================
     * - Tampilkan daftar produk
     * - Form tambah produk hanya muncul jika ada ?tambah=1
     */
    public function index(Request $request)
{
    $products = Product::where('user_id', Auth::id())
        ->latest()
        ->get();

    $showForm = $request->query('tambah') == 1;

    return view('products.manage', compact('products', 'showForm'));
}



    /**
     * =========================
     * SIMPAN PRODUK
     * POST /produk
     * =========================
     */
    public function store(Request $request)
    {
        $request->validate([
            'title'           => 'required|string|max:255',
            'description'     => 'nullable|string',

            'price'           => 'required|numeric|min:0',
            'discount'        => 'nullable|numeric|min:0|lt:price',

            'stock'           => 'nullable|integer|min:1',
            'purchase_limit'  => 'nullable|integer|min:1',

            'images.*'        => 'image|max:5120',
            'files.*'         => 'file|max:10240',
        ]);

        $product = Product::create([
            'user_id'        => Auth::id(),
            'title'          => $request->title,
            'description'    => $request->description,
            'price'          => (int) str_replace('.', '', $request->price),
            'discount'       => $request->discount
                                ? (int) str_replace('.', '', $request->discount)
                                : null,
            'stock'          => $request->has('stock_toggle') ? $request->stock : null,
            'purchase_limit' => $request->has('limit_toggle') ? $request->purchase_limit : null,
        ]);

        // simpan gambar
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $img) {
                $path = $img->store('products/images', 'public');
                $product->images()->create([
                    'image' => $path
                ]);
            }
        }

        // simpan file digital
        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                $path = $file->store('products/files', 'public');
                $product->files()->create([
                    'file' => $path
                ]);
            }
        }

        // balik ke manage + form ditutup
        return redirect()
            ->route('products.manage')
            ->with('success', 'Produk berhasil ditambahkan');
    }


    /**
     * =========================
     * HAPUS PRODUK
     * DELETE /produk/{produk}
     * =========================
     */
    public function destroy(Product $produk)
    {
        abort_if($produk->user_id !== Auth::id(), 403);

        $produk->delete();

        return back()->with('success', 'Produk berhasil dihapus');
    }
}
