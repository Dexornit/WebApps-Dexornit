<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProductController;
use Illuminate\Support\Facades\Route;

// Redirect /install ke setup.php (standalone EZ installer)
Route::get('/install', function () {
    return redirect('/setup.php');
})->name('installer.index');

// Landing page routes
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/product/{id}', [HomeController::class, 'show'])->name('product.show');

// Tools routes
Route::get('/tools', [\App\Http\Controllers\ToolsController::class, 'index'])->name('tools.index');
Route::get('/tools/a2f', [\App\Http\Controllers\ToolsController::class, 'a2f'])->name('tools.a2f');

// Admin routes - protected by auth middleware
Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Categories
    Route::resource('categories', \App\Http\Controllers\Admin\CategoryController::class);
    
    // Products
    Route::resource('products', ProductController::class);
    Route::post('/products/{id}/restore', [ProductController::class, 'restore'])->name('products.restore');
    Route::post('/products/{id}/toggle-status', [ProductController::class, 'toggleStatus'])->name('products.toggleStatus');
    
    // Social Media — simple preset management
    Route::get('/social-media', [\App\Http\Controllers\Admin\SocialMediaController::class, 'index'])->name('social-media.index');
    Route::post('/social-media', [\App\Http\Controllers\Admin\SocialMediaController::class, 'updateAll'])->name('social-media.updateAll');
    Route::post('/social-media/{platform}/toggle', [\App\Http\Controllers\Admin\SocialMediaController::class, 'toggle'])->name('social-media.toggle');

    
    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
