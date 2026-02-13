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
            // Log incoming request
            Log::info('Block Store Request:', $request->all());

            $validated = $request->validate([
                'page_id' => 'required|exists:pages,id',
                'type' => 'required|in:text,image,link,video,product',
            ]);

            $type = $validated['type'];
            $content = [];
            $productId = null;

            // Handle different block types
            switch ($type) {
                case 'text':
                    $content['text'] = $request->input('content.text', '');
                    break;

                case 'link':
                    $content['title'] = $request->input('content.title', '');
                    $content['url'] = $request->input('content.url', '');
                    break;

                case 'image':
                    if ($request->hasFile('image')) {
                        $path = $request->file('image')->store('blocks/images', 'public');
                        $content['image'] = $path;
                    }
                    break;

                case 'video':
                    $content['youtube_url'] = $request->input('content.youtube_url', '');
                    $content['youtube_id'] = $request->input('content.youtube_id', '');
                    break;

                case 'product':
                    // 🔥 FIX: Ambil product_id dari berbagai kemungkinan
                    $productId = $request->input('product_id') 
                              ?? $request->input('content.product_id')
                              ?? $request->get('product_id');
                    
                    Log::info('Product ID received:', ['product_id' => $productId]);
                    
                    if (!$productId) {
                        Log::error('Product ID is missing from request');
                        return response()->json([
                            'success' => false,
                            'message' => 'Product ID tidak ditemukan dalam request'
                        ], 400);
                    }

                    // Get product with images
                    $product = Product::with('images')->find($productId);
                    
                    if (!$product) {
                        Log::error('Product not found:', ['product_id' => $productId]);
                        return response()->json([
                            'success' => false,
                            'message' => 'Product tidak ditemukan'
                        ], 404);
                    }

                    // Store product data in content
                    $content['product'] = [
                        'title' => $product->title,
                        'price' => $product->price,
                        'discount' => $product->discount,
                        'image' => $product->images->first()->image ?? null,
                    ];
                    
                    Log::info('Product data prepared:', ['content' => $content]);
                    break;
            }

            // Get max position for this page
            $maxPosition = Block::where('page_id', $validated['page_id'])->max('position') ?? 0;

            // Create block
            $block = Block::create([
                'page_id' => $validated['page_id'],
                'type' => $type,
                'content' => $content,
                'product_id' => $productId, // 🔥 PENTING: Simpan product_id
                'position' => $maxPosition + 1,
            ]);

            Log::info('Block created:', [
                'id' => $block->id,
                'type' => $block->type,
                'product_id' => $block->product_id,
                'content' => $block->content
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Block berhasil ditambahkan',
                'block' => $block
            ]);

        } catch (\Exception $e) {
            Log::error('Block creation error:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan block: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update existing block
     */
    public function update(Request $request, Block $block)
    {
        try {
            Log::info('Block Update Request:', [
                'block_id' => $block->id,
                'data' => $request->all()
            ]);

            $type = $block->type;
            $content = $block->content ?? [];

            // Handle different block types
            switch ($type) {
                case 'text':
                    $content['text'] = $request->input('content.text', '');
                    break;

                case 'link':
                    $content['title'] = $request->input('content.title', '');
                    $content['url'] = $request->input('content.url', '');
                    break;

                case 'image':
                    if ($request->hasFile('image')) {
                        // Delete old image
                        if (isset($content['image'])) {
                            Storage::disk('public')->delete($content['image']);
                        }
                        
                        // Store new image
                        $path = $request->file('image')->store('blocks/images', 'public');
                        $content['image'] = $path;
                    }
                    break;

                case 'video':
                    $content['youtube_url'] = $request->input('content.youtube_url', '');
                    $content['youtube_id'] = $request->input('content.youtube_id', '');
                    break;

                case 'product':
                    // 🔥 FIX: Update product data
                    $productId = $request->input('product_id') ?? $block->product_id;
                    
                    if ($productId) {
                        $product = Product::with('images')->find($productId);
                        
                        if ($product) {
                            $content['product'] = [
                                'title' => $product->title,
                                'price' => $product->price,
                                'discount' => $product->discount,
                                'image' => $product->images->first()->image ?? null,
                            ];
                            
                            $block->product_id = $productId;
                        }
                    }
                    break;
            }

            // Update block
            $block->update([
                'content' => $content,
            ]);

            Log::info('Block updated:', [
                'id' => $block->id,
                'content' => $block->content
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Block berhasil diupdate',
                'block' => $block
            ]);

        } catch (\Exception $e) {
            Log::error('Block update error:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal update block: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete block
     */
    public function destroy(Block $block)
    {
        try {
            // Delete associated files
            if ($block->type === 'image' && isset($block->content['image'])) {
                Storage::disk('public')->delete($block->content['image']);
            }

            $block->delete();

            return response()->json([
                'success' => true,
                'message' => 'Block berhasil dihapus'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus block'
            ], 500);
        }
    }

    /**
     * Reorder blocks
     */
    public function reorder(Request $request)
    {
        try {
            $order = $request->input('order', []);

            foreach ($order as $item) {
                Block::where('id', $item['id'])->update([
                    'position' => $item['position']
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Urutan block berhasil diupdate'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal update urutan block'
            ], 500);
        }
    }
}
