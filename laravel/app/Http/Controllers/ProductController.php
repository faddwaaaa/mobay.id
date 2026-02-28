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

        $showForm        = false;
        $productTypeForm = null;
        $product         = null;

        if (in_array($request->tambah, ['digital', 'fisik'])) {
            $showForm        = true;
            $productTypeForm = $request->tambah;
        }

        if ($request->edit) {
            $product = Product::where('user_id', Auth::id())
                ->with('images')
                ->with('files')
                ->findOrFail($request->edit);
            $showForm        = true;
            $productTypeForm = $product->product_type;
        }

        return view('products.manage', compact('products', 'showForm', 'product', 'productTypeForm'));
    }

    /**
     * API: Ambil data produk TANPA mencatat view
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
     * API: Catat view produk
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
            'price'    => str_replace('.', '', $request->price),
            'discount' => $request->discount
                ? str_replace('.', '', $request->discount)
                : null,
        ]);

        $platform = $request->input('file_platform', 'upload');

        $rules = [
            'product_type'   => 'required|in:fisik,digital',
            'title'          => 'required|string|max:255',
            'description'    => 'nullable|string',
            'price'          => 'required|numeric|min:0',
            'discount'       => 'nullable|numeric|min:0|lt:price',
            'stock'          => 'nullable|integer|min:1',
            'purchase_limit' => 'nullable|integer|min:1',
            'images.*'       => 'nullable|image|max:5120',
        ];

        if ($request->product_type === 'digital') {
            if ($platform === 'upload') {
                // wajib ada minimal 1 file kalau platform upload
                $rules['files']   = 'required|array|min:1';
                $rules['files.*'] = 'required|file|max:10240';
            } else {
                // wajib ada URL kalau platform eksternal
                $rules['file_url'] = 'required|url';
            }
        } else {
            $rules['files.*'] = 'nullable|file|max:10240';
        }

        $request->validate($rules);

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

        // Simpan gambar tampilan
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $img) {
                $path = $img->store('products/images', 'public');
                $product->images()->create(['image' => $path]);
            }
        }

        // Simpan file/link digital
        if ($request->product_type === 'digital') {
            $this->saveDigitalFiles($request, $product, $platform);
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
            // Hanya hapus file fisik kalau memang di-upload (bukan URL eksternal)
            if (($file->platform ?? 'upload') === 'upload' && $file->file) {
                Storage::delete('public/' . $file->file);
            }
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
            'price'    => str_replace('.', '', $request->price),
            'discount' => $request->discount
                ? str_replace('.', '', $request->discount)
                : null,
        ]);

        $platform = $request->input('file_platform', 'upload');

        $rules = [
            'product_type'   => 'required|in:fisik,digital',
            'title'          => 'required|string|max:255',
            'description'    => 'nullable|string',
            'price'          => 'required|numeric|min:0',
            'discount'       => 'nullable|numeric|min:0|lt:price',
            'stock'          => 'nullable|integer|min:1',
            'purchase_limit' => 'nullable|integer|min:1',
            'images.*'       => 'nullable|image|max:5120',
            'delete_images.*'=> 'nullable|integer',
            'delete_files.*' => 'nullable|integer',
        ];

        if ($request->product_type === 'digital') {
            if ($platform === 'upload') {
                $rules['files.*'] = 'nullable|file|max:10240';
            } else {
                $rules['file_url'] = 'nullable|url';
            }
        } else {
            $rules['files.*'] = 'nullable|file|max:10240';
        }

        $request->validate($rules);

        $product->update([
            'product_type'   => $request->product_type,
            'title'          => $request->title,
            'description'    => $request->description,
            'price'          => $request->price,
            'discount'       => $request->discount,
            'stock'          => $request->has('stock_toggle') ? $request->stock : null,
            'purchase_limit' => $request->has('limit_toggle') ? $request->purchase_limit : null,
        ]);

        // Hapus gambar yang dicentang (by ID)
        if ($request->has('delete_images') && is_array($request->delete_images)) {
            $images = ProductImage::whereIn('id', $request->delete_images)
                ->where('product_id', $product->id)
                ->get();
            foreach ($images as $img) {
                Storage::delete('public/' . $img->image);
                $img->delete();
            }
        }

        // Hapus file yang dicentang (by ID)
        // ⚠️  edit modal sekarang kirim ID (integer), bukan file path
        if ($request->has('delete_files') && is_array($request->delete_files)) {
            $files = ProductFile::whereIn('id', $request->delete_files)
                ->where('product_id', $product->id)
                ->get();
            foreach ($files as $file) {
                if (($file->platform ?? 'upload') === 'upload' && $file->file) {
                    Storage::delete('public/' . $file->file);
                }
                $file->delete();
            }
        }

        // Tambah gambar baru
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $img) {
                $path = $img->store('products/images', 'public');
                $product->images()->create(['image' => $path]);
            }
        }

        // Tambah file/link digital baru
        if ($request->product_type === 'digital') {
            $this->saveDigitalFiles($request, $product, $platform);
        }

        return redirect()
            ->route('products.manage')
            ->with('success', 'Produk berhasil diupdate');
    }

    /**
     * EDIT — redirect ke halaman manage dengan query edit
     */
    public function edit($id)
    {
        return redirect()->route('products.manage', ['edit' => $id]);
    }

    /**
     * SHOW (web page) — catat views
     */
    public function show($id)
    {
        $product = Product::findOrFail($id);
        ProductViews::create(['product_id' => $product->id]);
        return view('products.show', compact('product'));
    }

    // =========================================================
    // PRIVATE HELPERS
    // =========================================================

    /**
     * Simpan file/link digital untuk pembeli.
     *
     * - platform 'upload' → upload file ke storage (bisa multiple)
     * - platform lainnya  → simpan satu URL eksternal (dropbox/gdrive/other)
     */
    private function saveDigitalFiles(Request $request, Product $product, string $platform): void
    {
        if ($platform === 'upload') {
            if (!$request->hasFile('files')) return;

            foreach ($request->file('files') as $file) {
                if (!$file->isValid()) continue;

                $path = $file->store('products/files', 'public');

                $product->files()->create([
                    'file'     => $path,
                    'platform' => 'upload',
                    'file_url' => null,
                ]);
            }
        } else {
            // Dropbox / G-Drive / Other — hanya satu URL per simpan
            $url = $request->input('file_url');
            if (!$url) return;

            $product->files()->create([
                'file'     => null,
                'platform' => $platform,
                'file_url' => $url,
            ]);
        }
    }
}