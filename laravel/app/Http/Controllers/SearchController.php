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

        $pages = $user->pages()->with('blocks')->get();

        foreach ($pages as $page) {
            foreach ($page->blocks as $block) {
                // PENTING: selalu gunakan key 'type' yang sama persis
                $blockType = $block->type;
                $content   = is_array($block->content)
                    ? $block->content
                    : (json_decode($block->content, true) ?? []);

                // ── Blok Produk ─────────────────────────────────
                if ($blockType === 'product') {
                    $productId = $block->product_id ?? $content['product_id'] ?? null;
                    if (!$productId) continue;

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
                            'type'        => 'product',   // ← key 'type' konsisten
                            'id'          => $product->id,
                            'title'       => $product->title ?? '(tanpa judul)',
                            'subtitle'    => $product->description ?? '',
                            'price'       => $price,
                            'final_price' => $finalPrice,
                            'image_url'   => $product->image_url ?? null,
                            'block_id'    => $block->id,
                            'url'         => null,
                        ];
                    }
                }

                // ── Blok Link ────────────────────────────────────
                if ($blockType === 'link') {
                    $title = $content['title'] ?? '';
                    $url   = $content['url']   ?? '';

                    // Cari di title DAN url
                    if (
                        stripos($title, $q) !== false ||
                        stripos($url, $q) !== false
                    ) {
                        $results[] = [
                            'type'        => 'link',      // ← key 'type' konsisten
                            'id'          => $block->id,
                            // FIX: jika title kosong, gunakan url sebagai judul
                            'title'       => ($title !== '') ? $title : ($url ?: '(tanpa judul)'),
                            'subtitle'    => $url,
                            'url'         => $url,
                            'image_url'   => null,
                            'block_id'    => $block->id,
                            'price'       => 0,
                            'final_price' => 0,
                        ];
                    }
                }

                // ── Blok Teks ────────────────────────────────────
                if ($blockType === 'text') {
                    $text = $content['text'] ?? '';
                    if ($text !== '' && stripos($text, $q) !== false) {
                        $results[] = [
                            'type'        => 'text',      // ← key 'type' konsisten
                            'id'          => $block->id,
                            'title'       => mb_strlen($text) > 60
                                ? mb_substr($text, 0, 60) . '...'
                                : $text,
                            'subtitle'    => 'Teks konten',
                            'url'         => null,
                            'image_url'   => null,
                            'block_id'    => $block->id,
                            'price'       => 0,
                            'final_price' => 0,
                        ];
                    }
                }
            }
        }

        return response()->json(['results' => $results]);
    }
}