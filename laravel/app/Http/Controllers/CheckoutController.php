<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    /**
     * Show checkout page for a product
     */
    public function show($productId)
    {
        // Find product
        $product = Product::with('images')->find($productId);
        
        // If product not found, redirect back
        if (!$product) {
            return redirect()->back()->with('error', 'Produk tidak ditemukan');
        }
        
        // Return checkout view
        return view('checkout', compact('product'));
    }
    
    /**
     * Process checkout (placeholder for future development)
     */
    public function process(Request $request, $productId)
    {
        // TODO: Implement payment processing
        // This will be developed later
        
        return redirect()->back()->with('info', 'Fitur pembayaran sedang dalam pengembangan');
    }
}