<?php

namespace App\Http\Controllers;

use App\Models\Block;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class BlockController extends Controller
{
    /**
     * Store a new block
     */
    public function store(Request $request)
    {
        try {
            Log::info('Block Store Request:', $request->all());

            $validated = $request->validate([
                'page_id' => 'required|exists:pages,id',
                'type'    => 'required|in:text,image,link,video,product',
            ]);

            $type      = $validated['type'];
            $content   = [];
            $productId = null;

            switch ($type) {
                case 'text':
                    $content['text'] = $request->input('content.text', '');
                    break;

                case 'link':
                    $content['title'] = $request->input('content.title', '');
                    $content['url']   = $request->input('content.url', '');
                    break;

                case 'image':
                    if ($request->hasFile('image')) {
                        $path             = $request->file('image')->store('blocks/images', 'public');
                        $content['image'] = $path;
                    }
                    break;

                case 'video':
                    $content['youtube_url'] = $request->input('content.youtube_url', '');
                    $content['youtube_id']  = $request->input('content.youtube_id', '');
                    break;

                case 'product':
                    $productId = $request->input('product_id')
                            ?? $request->input('content.product_id');

                    Log::info('Product ID received:', ['product_id' => $productId]);

                    if (!$productId) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Product ID tidak ditemukan dalam request',
                        ], 400);
                    }

                    $product = Product::with('images')->find($productId);

                    if (!$product) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Product tidak ditemukan',
                        ], 404);
                    }

                    $content['product'] = [
                        'title'    => $product->title,
                        'price'    => $product->price,
                        'discount' => $product->discount,
                        'image'    => $product->images->first()->image ?? null,
                    ];
                    break;
            }

            $maxPosition = Block::where('page_id', $validated['page_id'])->max('position') ?? 0;

            $block = Block::create([
                'page_id'    => $validated['page_id'],
                'type'       => $type,
                'content'    => $content,
                'product_id' => $productId,
                'position'   => $maxPosition + 1,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Block berhasil ditambahkan',
                'block'   => $block,
            ]);

        } catch (\Exception $e) {
            Log::error('Block creation error:', ['message' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan block: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update existing block
     */
    public function update(Request $request, Block $block)
    {
        try {
            Log::info('Block Update Request:', ['block_id' => $block->id, 'data' => $request->all()]);

            $type    = $block->type;
            $content = $block->content ?? [];

            switch ($type) {
                case 'text':
                    $content['text'] = $request->input('content.text', '');
                    break;

                case 'link':
                    $content['title'] = $request->input('content.title', '');
                    $content['url']   = $request->input('content.url', '');
                    break;

                case 'image':
                    if ($request->hasFile('image')) {
                        if (isset($content['image'])) {
                            Storage::disk('public')->delete($content['image']);
                        }
                        $path             = $request->file('image')->store('blocks/images', 'public');
                        $content['image'] = $path;
                    }
                    break;

                case 'video':
                    $content['youtube_url'] = $request->input('content.youtube_url', '');
                    $content['youtube_id']  = $request->input('content.youtube_id', '');
                    break;

                case 'product':
                    // Ambil product_id baru dari request, fallback ke yang lama
                    $newProductId = $request->input('product_id') ?? $block->product_id;

                    if ($newProductId) {
                        $product = Product::with('images')->find($newProductId);

                        if ($product) {
                            $content['product'] = [
                                'title'    => $product->title,
                                'price'    => $product->price,
                                'discount' => $product->discount,
                                'image'    => $product->images->first()->image ?? null,
                            ];
                        }
                    }
                    break;
            }

            // Update content + product_id sekaligus dalam satu query
            $block->update([
                'content'    => $content,
                'product_id' => $type === 'product'
                    ? ($request->input('product_id') ?? $block->product_id)
                    : $block->product_id,
            ]);

            Log::info('Block updated:', ['id' => $block->id]);

            return response()->json([
                'success' => true,
                'message' => 'Block berhasil diupdate',
                'block'   => $block,
            ]);

        } catch (\Exception $e) {
            Log::error('Block update error:', ['message' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal update block: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete block
     */
    public function destroy(Block $block)
    {
        try {
            if ($block->type === 'image' && isset($block->content['image'])) {
                Storage::disk('public')->delete($block->content['image']);
            }

            $block->delete();

            return response()->json([
                'success' => true,
                'message' => 'Block berhasil dihapus',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus block',
            ], 500);
        }
    }

    public function getByPage(Request $request)
{
    $pageId = $request->query('page_id');
    
    if (!$pageId) {
        return response()->json(['success' => false, 'message' => 'Page ID required'], 400);
    }

    $page = Page::with('blocks')->find($pageId);
    
    if (!$page || $page->user_id !== auth()->id()) {
        return response()->json(['success' => false, 'message' => 'Not found'], 404);
    }

    $blocks = $page->blocks->sortBy('position')->map(function($block) {
        return [
            'id' => $block->id,
            'type' => $block->type,
            'content' => $block->content,
            'product_id' => $block->product_id ?? null,
            'position' => $block->position
        ];
    })->values();

    return response()->json([
        'success' => true,
        'blocks' => $blocks,
        'pageTitle' => $page->title
    ]);
}

    /**
     * Reorder blocks
     */
    public function reorder(Request $request)
    {
        try {
            foreach ($request->input('order', []) as $item) {
                Block::where('id', $item['id'])->update(['position' => $item['position']]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Urutan block berhasil diupdate',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal update urutan block',
            ], 500);
        }
    }

    
}