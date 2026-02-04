<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    public function create()
    {
        return view('products.create');
    }

    public function store(Request $request)
{
    $request->validate([
        'title'           => 'required|string|max:255',
        'description'     => 'nullable|string',

        // harga & diskon (rupiah)
        'price'           => 'required|numeric|min:0',
        'discount'        => 'nullable|numeric|min:0|lt:price',

        // toggle dependent
        'stock'           => 'nullable|integer|min:1',
        'purchase_limit'  => 'nullable|integer|min:1',

        // upload
        'images.*'        => 'image|max:5120',
        'files.*'         => 'file|max:10240',
    ]);

    $product = Product::create([
        'user_id'        => Auth::id(),
        'title'          => $request->title,
        'description'    => $request->description,
        'price' => (int) str_replace('.', '', $request->price),
        'discount' => $request->discount
        ? (int) str_replace('.', '', $request->discount) : null,
        'stock'          => $request->has('stock_toggle') ? $request->stock : null,
        'purchase_limit' => $request->has('limit_toggle') ? $request->purchase_limit : null,
    ]);

    if ($request->hasFile('images')) {
        foreach ($request->file('images') as $img) {
            $path = $img->store('products/images', 'public');
            $product->images()->create(['image' => $path]);
        }
    }

    if ($request->hasFile('files')) {
        foreach ($request->file('files') as $file) {
            $path = $file->store('products/files', 'public');
            $product->files()->create(['file' => $path]);
        }
    }

    return back()->with('success', 'Produk berhasil ditambahkan');
}

}
