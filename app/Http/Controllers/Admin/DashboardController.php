<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display the admin dashboard.
     */
    public function index()
    {
        // Get statistics
        $stats = [
            'total_products' => Product::withTrashed()->count(),
            'active_products' => Product::where('status', true)->count(),
            'inactive_products' => Product::where('status', false)->count(),
            'deleted_products' => Product::onlyTrashed()->count(),
        ];

        // Get recent products
        $recentProducts = Product::with('variants')
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact('stats', 'recentProducts'));
    }
}
