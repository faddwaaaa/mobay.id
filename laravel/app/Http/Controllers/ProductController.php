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
    public function index(Request $request)
    {
        $baseQuery = Product::where('user_id', Auth::id())
            ->with('images')
            ->withCount('views')
            ->withCount('sales as sold')
            ->withSum('sales as total_qty', 'qty');

        $showForm        = false;
        $productTypeForm = null;
        $product         = null;

        if (in_array($request->tambah, ['digital', 'fisik'])) {
            $showForm        = true;
            $productTypeForm = $request->tambah;
        }

        if ($request->edit) {
            $product = Product::where('user_id', Auth::id())
                ->with('images')->with('files')
                ->findOrFail($request->edit);
            $showForm        = true;
            $productTypeForm = $product->product_type;
        }

        // Paginasi 8 per halaman untuk tab Produk
        // withQueryString() agar ?tab=produk ikut terbawa di link pagination
        $products = (clone $baseQuery)
            ->latest()
            ->paginate(8)
            ->withQueryString();

        // Semua produk (tanpa paginasi) untuk tab Statistik
        $allProducts = (clone $baseQuery)
            ->latest()
            ->get();

        return view('products.manage', compact(
            'products',
            'allProducts',
            'showForm',
            'product',
            'productTypeForm'
        ));
    }

    public function apiShow($id)
    {
        $product  = Product::with('images')->findOrFail($id);
        $imageUrl = $product->images->isNotEmpty()
            ? asset('storage/' . $product->images->first()->image)
            : null;

        return response()->json([
            'id'           => $product->id,
            'title'        => $product->title,
            'description'  => $product->description,
            'price'        => $product->price,
            'discount'     => $product->discount,
            'final_price'  => ($product->discount && $product->discount > 0) ? $product->discount : $product->price,
            'stock'        => $product->stock,
            'weight'       => $product->weight,
            'product_type' => $product->product_type,
            'image_url'    => $imageUrl,
        ]);
    }

    public function trackView($id)
    {
        $product = Product::findOrFail($id);
        ProductViews::create(['product_id' => $product->id]);
        return response()->json(['success' => true]);
    }

    public function store(Request $request)
    {
        $price    = (int) str_replace('.', '', $request->price    ?? '');
        $discount = $request->discount ? (int) str_replace('.', '', $request->discount) : null;

        $request->merge(['price' => $price, 'discount' => $discount]);

        $platform = $request->input('file_platform', 'upload');

        $rules = [
            'product_type'   => 'required|in:fisik,digital',
            'title'          => 'required|string|max:255',
            'description'    => 'nullable|string',
            'price'          => 'required|numeric|min:0',
            'discount'       => 'nullable|numeric|min:0|lt:price',
            'stock'          => 'nullable|integer|min:1',
            'purchase_limit' => 'nullable|integer|min:1',
            'weight'         => 'nullable|integer|min:1',
            'images.*'       => 'nullable|image|max:5120',
        ];

        if ($request->product_type === 'digital') {
            if ($platform === 'upload') {
                $rules['files']   = 'required|array|min:1';
                $rules['files.*'] = 'required|file|max:10240';
            } else {
                $rules['file_url'] = 'required|url';
            }
        }

        $request->validate($rules);

        $product = Product::create([
            'user_id'          => Auth::id(),
            'product_type'     => $request->product_type,
            'title'            => $request->title,
            'description'      => $request->description,
            'price'            => $request->price,
            'discount'         => $request->discount,
            'weight'           => $request->product_type === 'fisik' ? ($request->weight ?? 1000) : 0,
            'shipping_enabled' => $request->product_type === 'fisik' ? $request->has('shipping_toggle') : false,
            'stock'            => $request->has('stock_toggle') ? $request->stock : null,
            'purchase_limit'   => $request->has('limit_toggle') ? $request->purchase_limit : null,
        ]);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $img) {
                $path = $img->store('products/images', 'public');
                $product->images()->create(['image' => $path]);
            }
        }

        if ($request->product_type === 'digital') {
            $this->saveDigitalFiles($request, $product, $platform);
        }

        if ($request->redirect === 'builder') {
            return redirect()->route('links.index')
                ->with('openProductModal', true)
                ->with('success', 'Produk berhasil ditambahkan');
        }

        return redirect()->route('products.manage')
            ->with('success', 'Produk berhasil ditambahkan');
    }

    public function destroy(Product $produk)
    {
        abort_if($produk->user_id !== Auth::id(), 403);

        foreach ($produk->images as $img) {
            Storage::delete('public/' . $img->image);
        }
        foreach ($produk->files as $file) {
            if (($file->platform ?? 'upload') === 'upload' && $file->file) {
                Storage::delete('public/' . $file->file);
            }
        }

        $produk->images()->delete();
        $produk->files()->delete();
        $produk->delete();

        return back()->with('success', 'Produk berhasil dihapus');
    }

    public function update(Request $request, Product $product)
    {
        abort_if($product->user_id !== Auth::id(), 403);

        $price    = (int) str_replace('.', '', $request->price    ?? '');
        $discount = $request->discount ? (int) str_replace('.', '', $request->discount) : null;

        $request->merge(['price' => $price, 'discount' => $discount]);

        $platform = $request->input('file_platform', 'upload');

        $rules = [
            'product_type'    => 'required|in:fisik,digital',
            'title'           => 'required|string|max:255',
            'description'     => 'nullable|string',
            'price'           => 'required|numeric|min:0',
            'discount'        => 'nullable|numeric|min:0|lt:price',
            'stock'           => 'nullable|integer|min:1',
            'purchase_limit'  => 'nullable|integer|min:1',
            'weight'          => 'nullable|integer|min:1',
            'images.*'        => 'nullable|image|max:5120',
            'delete_images.*' => 'nullable|integer',
            'delete_files.*'  => 'nullable|integer',
        ];

        if ($request->product_type === 'digital') {
            $rules['files.*'] = 'nullable|file|max:10240';
            if ($platform !== 'upload') {
                $rules['file_url'] = 'nullable|url';
            }
        }

        $request->validate($rules);

        $product->update([
            'product_type'     => $request->product_type,
            'title'            => $request->title,
            'description'      => $request->description,
            'price'            => $request->price,
            'discount'         => $request->discount,
            'weight'           => $request->product_type === 'fisik' ? ($request->weight ?? $product->weight ?? 1000) : 0,
            'shipping_enabled' => $request->product_type === 'fisik' ? $request->has('shipping_toggle') : false,
            'stock'            => $request->has('stock_toggle') ? $request->stock : null,
            'purchase_limit'   => $request->has('limit_toggle') ? $request->purchase_limit : null,
        ]);

        if ($request->has('delete_images') && is_array($request->delete_images)) {
            $imgs = ProductImage::whereIn('id', $request->delete_images)
                ->where('product_id', $product->id)
                ->get();
            foreach ($imgs as $img) {
                Storage::delete('public/' . $img->image);
                $img->delete();
            }
        }

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

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $img) {
                $path = $img->store('products/images', 'public');
                $product->images()->create(['image' => $path]);
            }
        }

        if ($request->product_type === 'digital') {
            $this->saveDigitalFiles($request, $product, $platform);
        }

        return redirect()->route('products.manage')
            ->with('success', 'Produk berhasil diupdate');
    }

    public function edit($id)
    {
        return redirect()->route('products.manage', ['edit' => $id]);
    }

    public function show($id)
    {
        $product = Product::findOrFail($id);
        ProductViews::create(['product_id' => $product->id]);
        return view('products.show', compact('product'));
    }

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