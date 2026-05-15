<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Display the landing page with products.
     */
    public function index()
    {
        // Query active products with variants, images, and category
        $products = Product::with(['variants', 'images', 'category'])
            ->where('status', true)
            ->whereNull('deleted_at')
            ->get();

        // Get active categories for filter buttons
        $categories = \App\Models\Category::active()->ordered()->get();

        // Get active social media links
        $socialMedia = \App\Models\SocialMedia::where('is_active', true)
            ->orderBy('order')
            ->get();

        // Prepare products data for JavaScript
        $productsData = $products->map(function ($product) {
            $minPrice = null;
            if ($product->variants->isNotEmpty()) {
                $minPrice = $product->variants->min('price');
            }

            return [
                'id' => $product->id,
                'emoji' => $product->emoji,
                'logo' => $product->logo_path ? asset('storage/' . $product->logo_path) : null,
                'name' => $product->name,
                'desc' => $product->short_description,
                'price' => $minPrice ? 'Rp ' . number_format($minPrice, 0, ',', '.') : 'Hubungi Kami',
                'period' => $minPrice && $product->variants->isNotEmpty() ? '/mulai' : '',
                'category' => $product->category ? $product->category->slug : 'uncategorized',
                'categoryName' => $product->category ? $product->category->name : 'Uncategorized',
            ];
        });

        return view('home', compact('productsData', 'categories', 'socialMedia'));
    }

    /**
     * Display product detail.
     */
    public function show($id)
    {
        // Query product with variants and images
        $product = Product::with(['variants', 'images'])
            ->findOrFail($id);

        return view('product-detail', compact('product'));
    }
}
