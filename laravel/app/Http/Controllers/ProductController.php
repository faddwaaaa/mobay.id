<?php

namespace App\Http\Controllers;

use App\Models\Product;
//use App\Models\Block;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    /**
     * INDEX
     * - Tampilkan daftar produk
     * - Form tambah produk muncul jika ?tambah=1
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
     * STORE PRODUK
     */
    public function store(Request $request)
    {
        // Bersihkan format harga (hapus titik ribuan)
        $price = (int) str_replace('.', '', $request->price);
        $discount = $request->discount
            ? (int) str_replace('.', '', $request->discount)
            : null;

        // VALIDASI
        $request->validate([
            'product_type'    => 'required|in:umkm,digital',

            'title'           => 'required|string|max:255',
            'description'     => 'nullable|string',

            'price'           => 'required',
            'discount'        => 'nullable|numeric|min:0|lt:price',

            'stock'           => 'nullable|integer|min:1',
            'purchase_limit'  => 'nullable|integer|min:1',

            'images.*'        => 'nullable|image|max:5120',

            // File wajib kalau digital
            'files.*'         => $request->product_type === 'digital'
                                ? 'required|file|max:10240'
                                : 'nullable|file|max:10240',
        ]);

        // BUAT PRODUK
        $product = Product::create([
            'user_id'        => Auth::id(),
            'product_type'   => $request->product_type,

            'title'          => $request->title,
            'description'    => $request->description,

            'price'          => $price,
            'discount'       => $discount,

            'stock'          => $request->has('stock_toggle')
                                ? $request->stock
                                : null,

            'purchase_limit' => $request->has('limit_toggle')
                                ? $request->purchase_limit
                                : null,
        ]);

        /*
        | AUTO BUAT BLOCK PRODUCT
        */
        // Block::create([
        //     'user_id'    => Auth::id(),
        //     'type'       => 'product',
        //     'product_id' => $product->id,
        //     'order'      => Block::where('user_id', Auth::id())->max('order') + 1,
        // ]);


        /*
        | SIMPAN GAMBAR
        */
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $img) {
                $path = $img->store('products/images', 'public');

                $product->images()->create([
                    'image' => $path
                ]);
            }
        }


        /*
        | SIMPAN FILE DIGITAL (HANYA JIKA ADA)
        */
        if ($request->product_type === 'digital' && $request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                $path = $file->store('products/files', 'public');

                $product->files()->create([
                    'file' => $path
                ]);
            }
        }

        return redirect()
            ->route('products.manage')
            ->with('success', 'Produk berhasil ditambahkan');
    }


    /**
     * HAPUS PRODUK
     */
    public function destroy(Product $produk)
    {
        abort_if($produk->user_id !== Auth::id(), 403);

        $produk->images()->delete();
        $produk->files()->delete();
        $produk->delete();

        return back()->with('success', 'Produk berhasil dihapus');
    }
}
