<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Product;

class SearchController extends Controller
{
    public function search(Request $request)
    {
        $q        = trim($request->get('q', ''));
        $username = trim($request->get('username', ''));

        if (!$q || !$username) {
            return response()->json(['results' => []]);
        }

        $user = User::where('username', $username)->first();

        if (!$user) {
            return response()->json(['results' => []]);
        }

        $results = [];

        // Load semua pages + blocks. Product di-load manual per block
        // supaya tidak bergantung pada nama relasi yang mungkin beda
        $pages = $user->pages()->with('blocks')->get();

        foreach ($pages as $page) {
            foreach ($page->blocks as $block) {
                $type    = $block->type;
                $content = is_array($block->content)
                    ? $block->content
                    : json_decode($block->content, true) ?? [];

                // ── Blok Produk ─────────────────────────────────
                if ($type === 'product') {
                    $productId = $block->product_id ?? $content['product_id'] ?? null;
                    if (!$productId) continue;

                    // Coba load product langsung dari DB agar aman
                    $product = Product::find($productId);
                    if (!$product) continue;

                    if (
                        stripos($product->title ?? '', $q) !== false ||
                        stripos($product->description ?? '', $q) !== false
                    ) {
                        $price      = $product->price ?? 0;
                        $discount   = $product->discount ?? null;
                        $finalPrice = ($discount && $discount > 0 && $discount < $price)
                            ? $discount : $price;

                        $results[] = [
                            'type'        => 'product',
                            'id'          => $product->id,
                            'title'       => $product->title,
                            'subtitle'    => $product->description,
                            'price'       => $price,
                            'final_price' => $finalPrice,
                            'image_url'   => $product->image_url ?? null,
                            'block_id'    => $block->id,
                        ];
                    }
                }

                // ── Blok Link ────────────────────────────────────
                if ($type === 'link') {
                    $title = $content['title'] ?? '';
                    $url   = $content['url']   ?? '';
                    if (
                        stripos($title, $q) !== false ||
                        stripos($url, $q) !== false
                    ) {
                        $results[] = [
                            'type'      => 'link',
                            'id'        => $block->id,
                            'title'     => $title ?: $url,
                            'subtitle'  => $url,
                            'url'       => $url,
                            'image_url' => null,
                            'block_id'  => $block->id,
                        ];
                    }
                }

                // ── Blok Teks ────────────────────────────────────
                if ($type === 'text') {
                    $text = $content['text'] ?? '';
                    if (stripos($text, $q) !== false) {
                        $results[] = [
                            'type'      => 'text',
                            'id'        => $block->id,
                            'title'     => mb_strlen($text) > 60
                                ? mb_substr($text, 0, 60) . '...'
                                : $text,
                            'subtitle'  => 'Teks konten',
                            'image_url' => null,
                            'block_id'  => $block->id,
                        ];
                    }
                }
            }
        }

        return response()->json(['results' => $results]);
    }
}