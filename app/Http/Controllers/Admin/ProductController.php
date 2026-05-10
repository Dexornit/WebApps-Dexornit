<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Product::withTrashed()->with(['variants', 'category']);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%");
        }

        // Category filter
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        // Status filter
        $status = $request->get('status', '');
        if ($status === 'active') {
            $query->where('status', true)->whereNull('deleted_at');
        } elseif ($status === 'inactive') {
            $query->where('status', false)->whereNull('deleted_at');
        } elseif ($status === 'deleted') {
            $query->whereNotNull('deleted_at');
        }
        // '' = all (default, withTrashed already applied)

        // Pagination
        $products = $query->orderBy('created_at', 'desc')->paginate(15);

        // Get categories for filter dropdown
        $categories = \App\Models\Category::active()->ordered()->get();

        return view('admin.products.index', compact('products', 'categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = \App\Models\Category::active()->ordered()->get();
        return view('admin.products.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate request
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'logo' => 'required|image|mimes:jpeg,png,webp,svg|max:1024', // Max 1MB
            'category_id' => 'required|exists:categories,id',
            'short_description' => 'required|string|max:500',
            'full_description' => 'required|string',
            'warranty' => 'nullable|string',
            'terms_conditions' => 'nullable|string',
            'status' => 'nullable|boolean',
            
            // Variant validation
            'variants' => 'nullable|array',
            'variants.*.variant_name' => 'required_with:variants|string|max:255',
            'variants.*.price' => 'required_with:variants|numeric|min:0',
            'variants.*.wholesale_price' => 'nullable|numeric|min:0',
            'variants.*.description' => 'nullable|string',
            'variants.*.stock' => 'nullable|integer|min:0',
            
            // Image validation
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,webp|max:2048', // max 2MB
        ]);

        // Set status (checkbox returns null if unchecked)
        $validated['status'] = $request->has('status') ? true : false;

        try {
            // Use database transaction
            \DB::beginTransaction();

            // Create product first to get ID
            $product = Product::create([
                'name' => $validated['name'],
                'emoji' => '📦', // Default emoji for backward compatibility
                'category_id' => $validated['category_id'],
                'short_description' => $validated['short_description'],
                'full_description' => $validated['full_description'],
                'warranty' => $validated['warranty'] ?? null,
                'terms_conditions' => $validated['terms_conditions'] ?? null,
                'status' => $validated['status'],
            ]);

            // Handle logo upload
            if ($request->hasFile('logo')) {
                $logo = $request->file('logo');
                $directory = 'logos';
                $filename = $product->id . '_logo_' . time() . '.' . $logo->getClientOriginalExtension();
                $logoPath = $logo->storeAs($directory, $filename, 'public');
                
                // Update product with logo path
                $product->update(['logo_path' => $logoPath]);
            }

            // Create variants if provided
            if ($request->has('variants') && is_array($request->variants)) {
                foreach ($request->variants as $variantData) {
                    // Skip empty variant entries
                    if (empty($variantData['variant_name']) || empty($variantData['price'])) {
                        continue;
                    }

                    $product->variants()->create([
                        'variant_name' => $variantData['variant_name'],
                        'price' => $variantData['price'],
                        'wholesale_price' => $variantData['wholesale_price'] ?? null,
                        'description' => $variantData['description'] ?? null,
                        'stock' => $variantData['stock'] ?? null,
                    ]);
                }
            }

            // Handle image uploads
            if ($request->hasFile('images')) {
                $images = $request->file('images');
                $order = 1;

                foreach ($images as $image) {
                    // Create directory for product images
                    $directory = 'products/' . $product->id;
                    
                    // Store image with original name (sanitized)
                    $filename = time() . '_' . $order . '.' . $image->getClientOriginalExtension();
                    $path = $image->storeAs($directory, $filename, 'public');

                    // Save image record
                    $product->images()->create([
                        'image_path' => $path,
                        'order' => $order,
                    ]);

                    $order++;
                }
            }

            // Commit transaction
            \DB::commit();

            return redirect()->route('admin.products.index')
                ->with('success', 'Product created successfully!');

        } catch (\Exception $e) {
            // Rollback transaction on error
            \DB::rollBack();

            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create product: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // Not needed for admin panel
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $product = Product::with(['variants', 'images', 'category'])->findOrFail($id);
        $categories = \App\Models\Category::active()->ordered()->get();
        return view('admin.products.edit', compact('product', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $product = Product::findOrFail($id);

        // Validate request
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,webp,svg|max:1024', // Max 1MB
            'category_id' => 'required|exists:categories,id',
            'short_description' => 'required|string|max:500',
            'full_description' => 'required|string',
            'warranty' => 'nullable|string',
            'terms_conditions' => 'nullable|string',
            'status' => 'nullable|boolean',
            
            // Existing variant validation
            'existing_variants' => 'nullable|array',
            'existing_variants.*.id' => 'required|exists:product_variants,id',
            'existing_variants.*.variant_name' => 'required|string|max:255',
            'existing_variants.*.price' => 'required|numeric|min:0',
            'existing_variants.*.wholesale_price' => 'nullable|numeric|min:0',
            'existing_variants.*.description' => 'nullable|string',
            'existing_variants.*.stock' => 'nullable|integer|min:0',
            
            // New variant validation
            'variants' => 'nullable|array',
            'variants.*.variant_name' => 'required_with:variants|string|max:255',
            'variants.*.price' => 'required_with:variants|numeric|min:0',
            'variants.*.wholesale_price' => 'nullable|numeric|min:0',
            'variants.*.description' => 'nullable|string',
            'variants.*.stock' => 'nullable|integer|min:0',
            
            // Delete arrays
            'delete_variants' => 'nullable|array',
            'delete_variants.*' => 'exists:product_variants,id',
            'delete_images' => 'nullable|array',
            'delete_images.*' => 'exists:product_images,id',
            
            // Image validation
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,webp|max:2048', // max 2MB
        ]);

        // Set status (checkbox returns null if unchecked)
        $validated['status'] = $request->has('status') ? true : false;

        try {
            // Use database transaction
            \DB::beginTransaction();

            // Update product basic info
            $product->update([
                'name' => $validated['name'],
                'category_id' => $validated['category_id'],
                'short_description' => $validated['short_description'],
                'full_description' => $validated['full_description'],
                'warranty' => $validated['warranty'] ?? null,
                'terms_conditions' => $validated['terms_conditions'] ?? null,
                'status' => $validated['status'],
            ]);

            // Handle logo upload
            if ($request->hasFile('logo')) {
                // Delete old logo if exists
                if ($product->logo_path) {
                    \Storage::disk('public')->delete($product->logo_path);
                }

                $logo = $request->file('logo');
                $directory = 'logos';
                $filename = $product->id . '_logo_' . time() . '.' . $logo->getClientOriginalExtension();
                $logoPath = $logo->storeAs($directory, $filename, 'public');
                
                // Update product with logo path
                $product->update(['logo_path' => $logoPath]);
            }

            // Handle variant deletions
            if ($request->has('delete_variants') && is_array($request->delete_variants)) {
                foreach ($request->delete_variants as $variantId) {
                    $variant = $product->variants()->find($variantId);
                    if ($variant) {
                        $variant->delete();
                    }
                }
            }

            // Update existing variants
            if ($request->has('existing_variants') && is_array($request->existing_variants)) {
                foreach ($request->existing_variants as $variantId => $variantData) {
                    $variant = $product->variants()->find($variantId);
                    if ($variant) {
                        $variant->update([
                            'variant_name' => $variantData['variant_name'],
                            'price' => $variantData['price'],
                            'wholesale_price' => $variantData['wholesale_price'] ?? null,
                            'description' => $variantData['description'] ?? null,
                            'stock' => $variantData['stock'] ?? null,
                        ]);
                    }
                }
            }

            // Create new variants
            if ($request->has('variants') && is_array($request->variants)) {
                foreach ($request->variants as $variantData) {
                    // Skip empty variant entries
                    if (empty($variantData['variant_name']) || empty($variantData['price'])) {
                        continue;
                    }

                    $product->variants()->create([
                        'variant_name' => $variantData['variant_name'],
                        'price' => $variantData['price'],
                        'wholesale_price' => $variantData['wholesale_price'] ?? null,
                        'description' => $variantData['description'] ?? null,
                        'stock' => $variantData['stock'] ?? null,
                    ]);
                }
            }

            // Handle image deletions
            if ($request->has('delete_images') && is_array($request->delete_images)) {
                foreach ($request->delete_images as $imageId) {
                    if (empty($imageId)) continue;
                    
                    $image = $product->images()->find($imageId);
                    if ($image) {
                        // Delete file from storage
                        \Storage::disk('public')->delete($image->image_path);
                        // Delete record
                        $image->delete();
                    }
                }
            }

            // Handle new image uploads
            if ($request->hasFile('images')) {
                $images = $request->file('images');
                
                // Get the highest order number from existing images
                $maxOrder = $product->images()->max('order') ?? 0;
                $order = $maxOrder + 1;

                foreach ($images as $image) {
                    // Create directory for product images
                    $directory = 'products/' . $product->id;
                    
                    // Store image with timestamp
                    $filename = time() . '_' . $order . '.' . $image->getClientOriginalExtension();
                    $path = $image->storeAs($directory, $filename, 'public');

                    // Save image record
                    $product->images()->create([
                        'image_path' => $path,
                        'order' => $order,
                    ]);

                    $order++;
                }
            }

            // Commit transaction
            \DB::commit();

            return redirect()->route('admin.products.index')
                ->with('success', 'Product updated successfully!');

        } catch (\Exception $e) {
            // Rollback transaction on error
            \DB::rollBack();

            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update product: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage (soft delete).
     */
    public function destroy(string $id)
    {
        try {
            $product = Product::findOrFail($id);
            
            // Soft delete the product
            $product->delete();

            return redirect()->route('admin.products.index')
                ->with('success', 'Product deleted successfully! You can restore it anytime.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to delete product: ' . $e->getMessage());
        }
    }

    /**
     * Restore a soft deleted product.
     */
    public function restore(string $id)
    {
        try {
            $product = Product::withTrashed()->findOrFail($id);
            
            // Check if product is actually deleted
            if (!$product->trashed()) {
                return redirect()->route('admin.products.index')
                    ->with('error', 'Product is not deleted.');
            }

            // Restore the product
            $product->restore();

            return redirect()->route('admin.products.index')
                ->with('success', 'Product restored successfully!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to restore product: ' . $e->getMessage());
        }
    }

    /**
     * Toggle product status (active/inactive).
     */
    public function toggleStatus(string $id)
    {
        try {
            $product = Product::findOrFail($id);
            
            // Toggle status
            $product->status = !$product->status;
            $product->save();

            $statusText = $product->status ? 'activated' : 'deactivated';

            return redirect()->route('admin.products.index')
                ->with('success', "Product {$statusText} successfully!");

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to toggle product status: ' . $e->getMessage());
        }
    }
}
