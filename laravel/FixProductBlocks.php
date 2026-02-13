<?php

namespace App\Console\Commands;

use App\Models\Block;
use App\Models\Product;
use Illuminate\Console\Command;

class FixProductBlocks extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'fix:product-blocks';

    /**
     * The console command description.
     */
    protected $description = 'Fix product blocks yang product_id-nya NULL';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔧 Memperbaiki product blocks...');
        
        // Get all product blocks with NULL product_id
        $brokenBlocks = Block::where('type', 'product')
            ->whereNull('product_id')
            ->get();
        
        if ($brokenBlocks->count() === 0) {
            $this->info('✅ Tidak ada product block yang rusak!');
            return 0;
        }
        
        $this->info("📦 Ditemukan {$brokenBlocks->count()} product blocks yang rusak");
        
        $fixed = 0;
        $failed = 0;
        
        foreach ($brokenBlocks as $block) {
            $this->line("Processing block #{$block->id}...");
            
            // Try to fix by asking user to select product
            $products = Product::where('user_id', $block->page->user_id)
                ->orderBy('created_at', 'desc')
                ->get();
            
            if ($products->count() === 0) {
                $this->error("  ❌ User tidak punya product, skip block #{$block->id}");
                $failed++;
                continue;
            }
            
            // Show products
            $this->table(
                ['ID', 'Title', 'Price'],
                $products->map(function($p) {
                    return [$p->id, $p->title, 'Rp ' . number_format($p->price, 0, ',', '.')];
                })
            );
            
            $productId = $this->ask("Pilih Product ID untuk block #{$block->id} (atau 'skip')");
            
            if ($productId === 'skip') {
                $this->warn("  ⏭️  Skip block #{$block->id}");
                continue;
            }
            
            $product = $products->find($productId);
            
            if (!$product) {
                $this->error("  ❌ Product ID tidak valid, skip block #{$block->id}");
                $failed++;
                continue;
            }
            
            // Update block
            $product->load('images');
            
            $block->update([
                'product_id' => $product->id,
                'content' => [
                    'product' => [
                        'title' => $product->title,
                        'price' => $product->price,
                        'discount' => $product->discount,
                        'image' => $product->images->first()->image ?? null,
                    ]
                ]
            ]);
            
            $this->info("  ✅ Block #{$block->id} diperbaiki dengan product: {$product->title}");
            $fixed++;
        }
        
        $this->newLine();
        $this->info("🎉 Selesai!");
        $this->info("✅ Diperbaiki: {$fixed}");
        $this->info("❌ Gagal/Skip: {$failed}");
        
        return 0;
    }
}
