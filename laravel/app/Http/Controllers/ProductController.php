<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductFile;
use App\Models\ProductViews;
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
            ->with('images')
            ->withCount('views')
            ->withCount('sales as sold')
            ->withSum('sales as total_qty', 'qty')
            ->latest()
            ->get();

        $showForm = false;
        $product = null;

        if ($request->tambah) {
            $showForm = true;
        }

        if ($request->edit) {
            $product = Product::where('user_id', Auth::id())
                ->with('images')
                ->with('files')
                ->findOrFail($request->edit);
            $showForm = true;
        }

        return view('products.manage', compact('products', 'showForm', 'product'));
    }

    /**
     * API: Ambil data produk TANPA mencatat view
     * Dipakai untuk render kartu produk di halaman publik
     */
    public function apiShow($id)
    {
        $product = Product::with('images')->findOrFail($id);

        $imageUrl = null;
        if ($product->images->isNotEmpty()) {
            $imageUrl = asset('storage/' . $product->images->first()->image);
        }

        return response()->json([
            'id'          => $product->id,
            'title'       => $product->title,
            'description' => $product->description,
            'price'       => $product->price,
            'discount'    => $product->discount,
            'stock'       => $product->stock,
            'image_url'   => $imageUrl,
        ]);
    }

    /**
     * API: Catat view produk (dipanggil saat user KLIK produk, bukan saat render)
     */
    public function trackView($id)
    {
        $product = Product::findOrFail($id);
        ProductViews::create(['product_id' => $product->id]);

        return response()->json(['success' => true]);
    }

    /**
     * STORE PRODUK
     */
    public function store(Request $request)
    {
        $request->merge([
            'price' => str_replace('.', '', $request->price),
            'discount' => $request->discount
                ? str_replace('.', '', $request->discount)
                : null
        ]);

        $request->validate([
            'product_type'   => 'required|in:umkm,digital',
            'title'          => 'required|string|max:255',
            'description'    => 'nullable|string',
            'price'          => 'required|numeric|min:0',
            'discount'       => 'nullable|numeric|min:0|lt:price',
            'stock'          => 'nullable|integer|min:1',
            'purchase_limit' => 'nullable|integer|min:1',
            'images.*'       => 'nullable|image|max:5120',
            'files.*'        => 'nullable|file|max:10240',
        ]);

        $product = Product::create([
            'user_id'        => Auth::id(),
            'product_type'   => $request->product_type,
            'title'          => $request->title,
            'description'    => $request->description,
            'price'          => $request->price,
            'discount'       => $request->discount,
            'stock'          => $request->has('stock_toggle') ? $request->stock : null,
            'purchase_limit' => $request->has('limit_toggle') ? $request->purchase_limit : null,
        ]);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $img) {
                $path = $img->store('products/images', 'public');
                $product->images()->create(['image' => $path]);
            }
        }

        if ($request->product_type === 'digital' && $request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                $path = $file->store('products/files', 'public');
                $product->files()->create(['file' => $path]);
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
     * UPDATE PRODUK
     */
    public function update(Request $request, Product $product)
    {
        abort_if($product->user_id !== Auth::id(), 403);

        $request->merge([
            'price' => str_replace('.', '', $request->price),
            'discount' => $request->discount
                ? str_replace('.', '', $request->discount)
                : null
        ]);

        $request->validate([
            'product_type'   => 'required|in:umkm,digital',
            'title'          => 'required|string|max:255',
            'description'    => 'nullable|string',
            'price'          => 'required|numeric|min:0',
            'discount'       => 'nullable|numeric|min:0|lt:price',
            'stock'          => 'nullable|integer|min:1',
            'purchase_limit' => 'nullable|integer|min:1',
            'images.*'       => 'nullable|image|max:5120',
            'files.*'        => 'nullable|file|max:10240',
        ]);

        $product->update([
            'product_type'   => $request->product_type,
            'title'          => $request->title,
            'description'    => $request->description,
            'price'          => $request->price,
            'discount'       => $request->discount,
            'stock'          => $request->has('stock_toggle') ? $request->stock : null,
            'purchase_limit' => $request->has('limit_toggle') ? $request->purchase_limit : null,
        ]);

        if ($request->has('delete_images') && is_array($request->delete_images)) {
            $images = ProductImage::whereIn('id', $request->delete_images)
                ->where('product_id', $product->id)
                ->get();
            foreach ($images as $img) {
                Storage::delete('public/' . $img->image);
                $img->delete();
            }
        }

        if ($request->has('delete_files') && is_array($request->delete_files)) {
            foreach ($request->delete_files as $filePath) {
                $file = ProductFile::where('product_id', $product->id)
                    ->where('file', $filePath)
                    ->first();
                if ($file) {
                    Storage::delete('public/' . $file->file);
                    $file->delete();
                }
            }
        }

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $img) {
                $path = $img->store('products/images', 'public');
                $product->images()->create(['image' => $path]);
            }
        }

        if ($request->hasFile('files') && $request->product_type === 'digital') {
            foreach ($request->file('files') as $file) {
                $path = $file->store('products/files', 'public');
                $product->files()->create(['file' => $path]);
            }
        }

        return redirect()
            ->route('products.manage')
            ->with('success', 'Produk berhasil diupdate');
    }

    /**
     * EDIT
     */
    public function edit($id)
    {
        return redirect()->route('products.manage', ['edit' => $id]);
    }

    /**
     * SHOW (web page) - catat views
     */
    public function show($id)
    {
        $product = Product::findOrFail($id);
        ProductViews::create(['product_id' => $product->id]);
        return view('products.show', compact('product'));
    }
}