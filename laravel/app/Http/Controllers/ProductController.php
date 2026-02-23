<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductFile;
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

    $showForm = false;
    $product = null;

    // tambah
    if ($request->tambah) {
        $showForm = true;
    }

    // edit
    if ($request->edit) {
        $product = Product::where('user_id', Auth::id())
            ->with('images')
            ->with('files') // Tambahkan ini untuk memuat relasi files
            ->findOrFail($request->edit);

        $showForm = true;
    }

    return view('products.manage', compact('products','showForm','product'));
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
        'files.*' => 'nullable|file|max:10240', // 10MB max untuk file
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

        // Hapus file storage digital
        foreach ($produk->files as $file) {
            Storage::delete('public/' . $file->file);
        }

        $produk->images()->delete();
        $produk->files()->delete();
        $produk->delete();

        return back()->with('success', 'Produk berhasil dihapus');
    }


    /**
     * UPDATE PRODUK (🔥 MULTI IMAGE + DISCOUNT + DELETE IMAGE + DELETE FILE)
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
        'product_type' => 'required|in:umkm,digital',
        'title' => 'required|string|max:255',
        'description' => 'nullable|string',

        'price' => 'required|numeric|min:0',
        'discount' => 'nullable|numeric|min:0|lt:price',

        'stock' => 'nullable|integer|min:1',
        'purchase_limit' => 'nullable|integer|min:1',

        'images.*' => 'nullable|image|max:5120',
        'files.*' => 'nullable|file|max:10240', // 10MB max untuk file
    ]);

    // UPDATE DATA PRODUK
    $product->update([
        'product_type' => $request->product_type,
        'title' => $request->title,
        'description' => $request->description,
        'price' => $request->price,
        'discount' => $request->discount,
        'stock' => $request->has('stock_toggle') ? $request->stock : null,
        'purchase_limit' => $request->has('limit_toggle') ? $request->purchase_limit : null,
    ]);

    /*
    | =========================
    | HAPUS GAMBAR TERTENTU (delete_images[])
    | =========================
    */
    if ($request->has('delete_images') && is_array($request->delete_images)) {
        $images = ProductImage::whereIn('id', $request->delete_images)
            ->where('product_id', $product->id)
            ->get();

        foreach ($images as $img) {
            // Hapus file fisik
            Storage::delete('public/' . $img->image);
            // Hapus record dari database
            $img->delete();
        }
    }

    /*
    | =========================
    | HAPUS FILE TERTENTU (delete_files[])
    | =========================
    */
    if ($request->has('delete_files') && is_array($request->delete_files)) {
        foreach ($request->delete_files as $filePath) {
            // Cari file di database berdasarkan path
            $file = ProductFile::where('product_id', $product->id)
                ->where('file', $filePath)
                ->first();
                
            if ($file) {
                // Hapus file fisik
                Storage::delete('public/' . $file->file);
                // Hapus record dari database
                $file->delete();
            }
        }
    }

    /*
    | =========================
    | TAMBAH GAMBAR BARU (images[])
    | =========================
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
    | =========================
    | TAMBAH FILE BARU (files[])
    | =========================
    */
    if ($request->hasFile('files') && $request->product_type === 'digital') {
        foreach ($request->file('files') as $file) {
            $path = $file->store('products/files', 'public');

            $product->files()->create([
                'file' => $path
            ]);
        }
    }

    return redirect()
        ->route('products.manage')
        ->with('success', 'Produk berhasil diupdate');
}

    /**
     * EDIT - Redirect ke halaman manage dengan parameter edit
     */
    public function edit($id)
    {
        return redirect()->route('products.manage', [
            'edit' => $id
        ]);
    }

}