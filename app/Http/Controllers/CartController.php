<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CartController extends Controller
{
    // ─────────────────────────────────────────────────────────────
    // HELPERS
    // ─────────────────────────────────────────────────────────────

    private function getCartSessionId(Request $request): string
    {
        // Prioritas: cookie cart_sid → session Laravel → buat UUID baru
        return $request->cookie('cart_sid')
            ?? session()->getId()
            ?: ('cart_' . Str::uuid()->toString());
    }

    /**
     * Query builder cart milik user saat ini.
     * - Login  → filter by user_id
     * - Guest  → filter by session_id (cookie cart_sid)
     */
    private function cartQuery(Request $request)
    {
        if (auth()->check()) {
            return Cart::where('user_id', auth()->id());
        }

        $sid = $this->getCartSessionId($request);
        return Cart::where('session_id', $sid)->whereNull('user_id');
    }

    /**
     * Ambil URL gambar pertama dari tabel product_images.
     * Kolom 'image' sesuai ProductImage model.
     */
    private function getProductImageUrl(Product $product): ?string
    {
        $firstImage = $product->images->first();

        if (!$firstImage || !$firstImage->image) return null;

        if (str_starts_with($firstImage->image, 'http')) {
            return $firstImage->image;
        }

        return asset('storage/' . $firstImage->image);
    }

    /**
     * Hitung harga final setelah diskon.
     * Kolom 'discount' berisi harga setelah diskon (bukan persentase).
     * Jika discount > 0 dan discount < price → pakai discount sebagai harga final.
     */
    private function getFinalPrice(Product $product): float
    {
        $price    = (float) ($product->price    ?? 0);
        $discount = (float) ($product->discount ?? 0);

        if ($discount > 0 && $discount < $price) {
            return $discount;
        }

        return $price;
    }

    // ─────────────────────────────────────────────────────────────
    // GET /api/cart
    // ─────────────────────────────────────────────────────────────
    public function index(Request $request)
    {
        $cartRows = $this->cartQuery($request)
            ->with(['product.images']) // eager load, cegah N+1
            ->get();

        $items = $cartRows->map(function (Cart $cart) {
            $product = $cart->product;
            if (!$product) return null;

            $price      = (float) ($product->price ?? 0);
            $finalPrice = $this->getFinalPrice($product);
            $hasDis     = $finalPrice < $price;

            return [
                'id'             => $cart->id,
                'product_id'     => $cart->product_id,
                'title'          => $product->title,
                'image_url'      => $this->getProductImageUrl($product),
                'quantity'       => (int) $cart->quantity,
                'original_price' => $price,
                'final_price'    => $finalPrice,
                'has_discount'   => $hasDis,
                'stock'          => $product->stock ?? 999,
                'product_type'   => $product->product_type ?? 'physical',
            ];
        })->filter()->values();

        return response()->json([
            'items'       => $items,
            'total_items' => (int) $items->sum('quantity'),
        ]);
    }

    // ─────────────────────────────────────────────────────────────
    // POST /api/cart/add
    // ─────────────────────────────────────────────────────────────
    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|integer|exists:products,id',
            'quantity'   => 'sometimes|integer|min:1|max:100',
        ]);

        $productId = (int) $request->product_id;
        $qty       = (int) ($request->quantity ?? 1);
        $product   = Product::findOrFail($productId);

        // Tolak jika stok habis
        if ($product->stock !== null && $product->stock < 1) {
            return response()->json(['message' => 'Stok produk habis.'], 422);
        }

        $sid = $this->getCartSessionId($request);

        // Cari baris cart yang sudah ada
        $existingQuery = auth()->check()
            ? Cart::where('user_id', auth()->id())->where('product_id', $productId)
            : Cart::where('session_id', $sid)->whereNull('user_id')->where('product_id', $productId);

        $cart = $existingQuery->first();

        if ($cart) {
            // Sudah ada → tambah quantity, batasi dengan stok
            $newQty = $cart->quantity + $qty;
            if ($product->stock !== null) {
                $newQty = min($newQty, $product->stock);
            }
            $cart->quantity = $newQty;
            $cart->save();
        } else {
            // Baru → insert ke DB
            $cart = Cart::create([
                'session_id' => $sid,
                'user_id'    => auth()->check() ? auth()->id() : null,
                'product_id' => $productId,
                'quantity'   => $product->stock !== null
                    ? min($qty, $product->stock)
                    : $qty,
            ]);
        }

        $totalItems = (int) $this->cartQuery($request)->sum('quantity');

        $response = response()->json([
            'message'     => 'Produk berhasil ditambahkan ke keranjang.',
            'cart_id'     => $cart->id,
            'total_items' => $totalItems,
        ]);

        // Simpan session_id ke cookie agar persist 1 tahun (guest)
        if (!auth()->check()) {
            $response->withCookie(
                cookie('cart_sid', $sid, 60 * 24 * 365, '/', null, false, false)
            );
        }

        return $response;
    }

    // ─────────────────────────────────────────────────────────────
    // PATCH /api/cart/{id}
    // ─────────────────────────────────────────────────────────────
    public function update(Request $request, int $id)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1|max:100',
        ]);

        $cart    = $this->cartQuery($request)->where('id', $id)->firstOrFail();
        $product = $cart->product;
        $newQty  = (int) $request->quantity;

        if ($product && $product->stock !== null && $newQty > $product->stock) {
            return response()->json(['message' => 'Melebihi stok tersedia.'], 422);
        }

        $cart->quantity = $newQty;
        $cart->save();

        return response()->json([
            'message'     => 'Keranjang diperbarui.',
            'total_items' => (int) $this->cartQuery($request)->sum('quantity'),
        ]);
    }

    // ─────────────────────────────────────────────────────────────
    // DELETE /api/cart/{id}   ← nama method 'remove' sesuai routes/web.php
    // ─────────────────────────────────────────────────────────────
    public function remove(Request $request, int $id)
    {
        $cart = $this->cartQuery($request)->where('id', $id)->firstOrFail();
        $cart->delete();

        return response()->json([
            'message'     => 'Produk dihapus dari keranjang.',
            'total_items' => (int) $this->cartQuery($request)->sum('quantity'),
        ]);
    }

    // ─────────────────────────────────────────────────────────────
    // DELETE /api/cart/clear  — kosongkan semua
    // ─────────────────────────────────────────────────────────────
    public function clear(Request $request)
    {
        $this->cartQuery($request)->delete();

        return response()->json([
            'message'     => 'Keranjang dikosongkan.',
            'total_items' => 0,
        ]);
    }

    // ─────────────────────────────────────────────────────────────
    // Merge cart guest → user setelah login
    // Panggil di AuthController setelah Auth::attempt() berhasil:
    //   CartController::mergeGuestCart($request, auth()->id());
    // ─────────────────────────────────────────────────────────────
    public static function mergeGuestCart(Request $request, int $userId): void
    {
        $sid = $request->cookie('cart_sid') ?? session()->getId();
        if (!$sid) return;

        $guestItems = Cart::where('session_id', $sid)
            ->whereNull('user_id')
            ->get();

        foreach ($guestItems as $guestCart) {
            $existing = Cart::where('user_id', $userId)
                ->where('product_id', $guestCart->product_id)
                ->first();

            if ($existing) {
                $maxStock           = $guestCart->product?->stock ?? 999;
                $existing->quantity = min($existing->quantity + $guestCart->quantity, $maxStock);
                $existing->save();
                $guestCart->delete();
            } else {
                $guestCart->user_id    = $userId;
                $guestCart->session_id = session()->getId();
                $guestCart->save();
            }
        }
    }
}