<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CartController extends Controller
{
    // ─────────────────────────────────────────────────────────────
    //  Helper: ambil atau buat session ID untuk guest
    // ─────────────────────────────────────────────────────────────
    private function getSessionId(Request $request): string
    {
        if (! $request->session()->has('cart_session_id')) {
            $request->session()->put('cart_session_id', uniqid('cart_', true));
        }
        return $request->session()->get('cart_session_id');
    }

    // ─────────────────────────────────────────────────────────────
    //  Helper: base query — filter berdasarkan session
    // ─────────────────────────────────────────────────────────────
    private function cartQuery(Request $request)
    {
        return Cart::forSession($this->getSessionId($request))
                   ->with('product');
    }

    // ─────────────────────────────────────────────────────────────
    //  GET /api/cart
    //  Ambil semua item + total harga + jumlah item
    // ─────────────────────────────────────────────────────────────
    public function index(Request $request): JsonResponse
    {
        $items = $this->cartQuery($request)->get();

        $data = $items->map(fn($item) => $this->formatItem($item));

        return response()->json([
            'items'       => $data,
            'total_items' => $items->sum('quantity'),
            'total_price' => $items->sum('subtotal'),
        ]);
    }

    // ─────────────────────────────────────────────────────────────
    //  POST /api/cart/add
    //  Tambah produk ke keranjang (atau tambah qty jika sudah ada)
    // ─────────────────────────────────────────────────────────────
    public function add(Request $request): JsonResponse
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity'   => 'sometimes|integer|min:1|max:100',
        ]);

        $product = Product::findOrFail($request->product_id);

        // Cek stok
        if ($product->stock <= 0) {
            return response()->json(['message' => 'Stok produk habis.'], 422);
        }

        $sessionId = $this->getSessionId($request);
        $qty       = $request->input('quantity', 1);

        $cartItem = Cart::firstOrNew([
            'session_id' => $sessionId,
            'product_id' => $product->id,
        ]);

        $newQty = $cartItem->exists ? $cartItem->quantity + $qty : $qty;

        // Jangan melebihi stok
        if ($newQty > $product->stock) {
            return response()->json([
                'message' => "Stok tersedia hanya {$product->stock}.",
            ], 422);
        }

        $cartItem->quantity = $newQty;
        $cartItem->user_id  = auth()->id();
        $cartItem->save();

        $totalItems = $this->cartQuery($request)->sum('quantity');

        return response()->json([
            'message'     => 'Produk berhasil ditambahkan ke keranjang.',
            'total_items' => $totalItems,
            'item'        => $this->formatItem($cartItem->load('product')),
        ], 201);
    }

    // ─────────────────────────────────────────────────────────────
    //  PATCH /api/cart/{id}
    //  Update qty satu item
    // ─────────────────────────────────────────────────────────────
    public function update(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'quantity' => 'required|integer|min:1|max:100',
        ]);

        $item = $this->cartQuery($request)->findOrFail($id);

        if ($request->quantity > $item->product->stock) {
            return response()->json([
                'message' => "Stok tersedia hanya {$item->product->stock}.",
            ], 422);
        }

        $item->update(['quantity' => $request->quantity]);

        $cartItems  = $this->cartQuery($request)->get();
        $totalPrice = $cartItems->sum('subtotal');
        $totalItems = $cartItems->sum('quantity');

        return response()->json([
            'message'     => 'Jumlah produk diperbarui.',
            'item'        => $this->formatItem($item->fresh('product')),
            'total_price' => $totalPrice,
            'total_items' => $totalItems,
        ]);
    }

    // ─────────────────────────────────────────────────────────────
    //  DELETE /api/cart/{id}
    //  Hapus satu item dari keranjang
    // ─────────────────────────────────────────────────────────────
    public function remove(Request $request, int $id): JsonResponse
    {
        $item = $this->cartQuery($request)->findOrFail($id);
        $item->delete();

        $cartItems  = $this->cartQuery($request)->get();
        $totalPrice = $cartItems->sum('subtotal');
        $totalItems = $cartItems->sum('quantity');

        return response()->json([
            'message'     => 'Produk dihapus dari keranjang.',
            'total_price' => $totalPrice,
            'total_items' => $totalItems,
        ]);
    }

    // ─────────────────────────────────────────────────────────────
    //  DELETE /api/cart
    //  Kosongkan semua keranjang
    // ─────────────────────────────────────────────────────────────
    public function clear(Request $request): JsonResponse
    {
        $this->cartQuery($request)->delete();

        return response()->json(['message' => 'Keranjang dikosongkan.']);
    }

    // ─────────────────────────────────────────────────────────────
    //  Helper: format satu item untuk response JSON
    // ─────────────────────────────────────────────────────────────
    private function formatItem(Cart $item): array
{
    $product = $item->product;

    // Hitung final price: discount adalah harga diskon langsung
    $finalPrice = ($product->discount && $product->discount > 0 && $product->discount < $product->price)
        ? $product->discount
        : $product->price;

    return [
        'id'             => $item->id,
        'product_id'     => $product->id,
        'title'          => $product->title,
        'image_url'      => $product->images->first()
                                ? asset('storage/' . $product->images->first()->image)
                                : null,
        'original_price' => $product->price,
        'final_price'    => $finalPrice,
        'has_discount'   => $finalPrice < $product->price,
        'quantity'       => $item->quantity,
        'subtotal'       => $finalPrice * $item->quantity,
        'stock'          => $product->stock,
    ];
}
}