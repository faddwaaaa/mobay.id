<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /**
     * INDEX
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
    // CLEAN FORMAT RUPIAH DULU
    $request->merge([
        'price' => str_replace('.', '', $request->price),
        'discount' => $request->discount
            ? str_replace('.', '', $request->discount)
            : null
    ]);

    $request->validate([
        'product_type' => 'required|in:umkm,digital',

        'title' => 'required|string|max:255',
        'description' => 'nullable|string',

        'price' => 'required|numeric|min:0',
        'discount' => 'nullable|numeric|min:0|lt:price',

        'stock' => 'nullable|integer|min:1',
        'purchase_limit' => 'nullable|integer|min:1',

        'images.*' => 'nullable|image|max:5120',
    ]);

    $product = Product::create([
        'user_id' => Auth::id(),
        'product_type' => $request->product_type,

        'title' => $request->title,
        'description' => $request->description,

        'price' => $request->price,
        'discount' => $request->discount,

            'stock'          => $request->has('stock_toggle')
                                ? $request->stock
                                : null,

            'purchase_limit' => $request->has('limit_toggle')
                                ? $request->purchase_limit
                                : null,
    ]);

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
        | SIMPAN FILE DIGITAL
        */
        if ($request->product_type === 'digital' && $request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                $path = $file->store('products/files', 'public');

                $product->files()->create([
                    'file' => $path
                ]);
            }
        }

        if ($request->redirect === 'builder') {
            return redirect()
                ->route('links.index')
                ->with('openProductModal', true)
                ->with('success', 'Produk berhasil ditambahkan');
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

        // Hapus file storage gambar
        foreach ($produk->images as $img) {
            Storage::delete('public/' . $img->image);
        }

        foreach ($produk->files as $file) {
            Storage::delete('public/' . $file->file);
        }

        $produk->images()->delete();
        $produk->files()->delete();
        $produk->delete();

        return back()->with('success', 'Produk berhasil dihapus');
    }


    /**
     * UPDATE PRODUK (🔥 MULTI IMAGE + DISCOUNT + DELETE IMAGE)
     */
    public function update(Request $request, Product $product)
{
    abort_if($product->user_id !== Auth::id(), 403);

    // CLEAN FORMAT RUPIAH
    $request->merge([
        'price' => str_replace('.', '', $request->price),
        'discount' => $request->discount
            ? str_replace('.', '', $request->discount)
            : null
    ]);

    $request->validate([
        'title' => 'required|string|max:255',
        'description' => 'nullable|string',

        'price' => 'required|numeric|min:0',
        'discount' => 'nullable|numeric|min:0|lt:price',

        'new_images.*' => 'nullable|image|max:5120',
    ]);

    $product->update([
        'title' => $request->title,
        'description' => $request->description,
        'price' => $request->price,
        'discount' => $request->discount,
        'stock' => $request->has('stock_toggle')
                    ? $request->stock
                    : null,
        ]);

        /*
        | =========================
        | HAPUS GAMBAR TERTENTU
        | =========================
        */
        if ($request->delete_images) {

            $images = ProductImage::whereIn('id', $request->delete_images)->get();

            foreach ($images as $img) {
                Storage::delete('public/' . $img->image);
                $img->delete();
            }
        }


        /*
        | =========================
        | TAMBAH GAMBAR BARU MULTI
        | =========================
        */
        if ($request->hasFile('new_images')) {

            foreach ($request->file('new_images') as $file) {

                $path = $file->store('products/images', 'public');

                $product->images()->create([
                    'image' => $path
                ]);
            }
        }

        return back()->with('success','Produk berhasil diupdate');
    }

    public function edit(Product $product)
{
    $product->load('images'); // penting biar gambar muncul
}
}
