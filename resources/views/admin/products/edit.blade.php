@extends('admin.layouts.admin')

@section('title', 'Edit Product')
@section('page-title', 'Edit Product')

@section('content')
<!-- Success/Error Messages -->
@if(session('success'))
<div style="background: #D4EDDA; border: 2px solid #28A745; border-radius: 10px; padding: 16px 20px; margin-bottom: 24px; display: flex; align-items: center; gap: 12px; box-shadow: 3px 3px 0px var(--color-black);">
    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#28A745" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
        <polyline points="22 4 12 14.01 9 11.01"/>
    </svg>
    <span style="color: #155724; font-weight: 600;">{{ session('success') }}</span>
</div>
@endif

@if(session('error'))
<div style="background: #F8D7DA; border: 2px solid #DC3545; border-radius: 10px; padding: 16px 20px; margin-bottom: 24px; display: flex; align-items: center; gap: 12px; box-shadow: 3px 3px 0px var(--color-black);">
    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#DC3545" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <circle cx="12" cy="12" r="10"/>
        <line x1="15" y1="9" x2="9" y2="15"/>
        <line x1="9" y1="9" x2="15" y2="15"/>
    </svg>
    <span style="color: #721C24; font-weight: 600;">{{ session('error') }}</span>
</div>
@endif

@if($errors->any())
<div style="background: #FFF3CD; border: 2px solid #FFC107; border-radius: 10px; padding: 16px 20px; margin-bottom: 24px; box-shadow: 3px 3px 0px var(--color-black);">
    <div style="display: flex; align-items: start; gap: 12px;">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#856404" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink: 0; margin-top: 2px;">
            <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
            <line x1="12" y1="9" x2="12" y2="13"/>
            <line x1="12" y1="17" x2="12.01" y2="17"/>
        </svg>
        <div style="flex: 1;">
            <p style="color: #856404; font-weight: 600; margin-bottom: 8px;">Please fix the following errors:</p>
            <ul style="color: #856404; margin: 0; padding-left: 20px;">
                @foreach($errors->all() as $error)
                    <li style="margin-bottom: 4px;">{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
</div>
@endif

<!-- Back Button -->
<div style="margin-bottom: 24px;">
    <a href="{{ route('admin.products.index') }}" style="display: inline-flex; align-items: center; gap: 8px; padding: 10px 20px; background: var(--color-white); border: 2px solid var(--border-color); border-radius: 8px; font-weight: 600; text-decoration: none; color: var(--color-black); box-shadow: 2px 2px 0px var(--color-black); transition: all 0.2s ease;">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M19 12H5M12 19l-7-7 7-7"/>
        </svg>
        Back to Products
    </a>
</div>

<form method="POST" action="{{ route('admin.products.update', $product->id) }}" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 24px; align-items: start;">
        <!-- Main Form -->
        <div>
            <!-- Product Information -->
            <div style="background: var(--color-white); border: var(--border-width) solid var(--border-color); border-radius: 12px; padding: 28px; box-shadow: var(--shadow-brutal); margin-bottom: 24px;">
                <h3 style="font-family: var(--font-heading); font-size: 1.3rem; font-weight: 700; margin-bottom: 24px;">Product Information</h3>

                <!-- Name -->
                <div style="margin-bottom: 20px;">
                    <label style="display: block; font-weight: 600; margin-bottom: 8px; font-size: 0.9rem;">Product Name <span style="color: var(--color-coral);">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $product->name) }}" required style="width: 100%; padding: 12px 16px; background: var(--color-cream); border: 2px solid var(--border-color); border-radius: 10px; font-size: 0.95rem; outline: none; transition: all 0.2s ease;" placeholder="e.g., Netflix Premium">
                    @error('name')
                        <span style="color: var(--color-coral); font-size: 0.85rem; margin-top: 4px; display: block;">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Logo Upload -->
                <div style="margin-bottom: 20px;">
                    <label style="display: block; font-weight: 600; margin-bottom: 8px; font-size: 0.9rem;">Product Logo</label>
                    
                    <!-- Current Logo -->
                    @if($product->logo_path)
                    <div style="margin-bottom: 12px;">
                        <p style="font-size: 0.85rem; color: #666; margin-bottom: 8px;">Current Logo:</p>
                        <div style="width: 120px; height: 120px; border: 2px solid var(--border-color); border-radius: 10px; overflow: hidden; background: var(--color-white); display: flex; align-items: center; justify-content: center;">
                            <img src="{{ asset('storage/' . $product->logo_path) }}" alt="Current Logo" style="width: 100%; height: 100%; object-fit: contain;">
                        </div>
                    </div>
                    @endif
                    
                    <input type="file" name="logo" accept="image/jpeg,image/png,image/webp,image/svg+xml" onchange="previewLogo(event)" style="width: 100%; padding: 12px 16px; background: var(--color-cream); border: 2px solid var(--border-color); border-radius: 10px; font-size: 0.95rem; cursor: pointer;">
                    <p style="font-size: 0.8rem; color: #888; margin-top: 6px;">Max 1MB. Formats: JPEG, PNG, WebP, SVG. Leave empty to keep current logo.</p>
                    @error('logo')
                        <span style="color: var(--color-coral); font-size: 0.85rem; margin-top: 4px; display: block;">{{ $message }}</span>
                    @enderror
                    
                    <!-- Logo Preview -->
                    <div id="logo-preview" style="margin-top: 12px; display: none;">
                        <p style="font-size: 0.85rem; color: #666; margin-bottom: 8px;">New Logo Preview:</p>
                        <div style="width: 120px; height: 120px; border: 2px solid var(--border-color); border-radius: 10px; overflow: hidden; background: var(--color-white); display: flex; align-items: center; justify-content: center;">
                            <img id="logo-preview-img" src="" alt="Logo Preview" style="width: 100%; height: 100%; object-fit: contain;">
                        </div>
                    </div>
                </div>

                <!-- Category -->
                <div style="margin-bottom: 20px;">
                    <label style="display: block; font-weight: 600; margin-bottom: 8px; font-size: 0.9rem;">Category <span style="color: var(--color-coral);">*</span></label>
                    <select name="category_id" required style="width: 100%; padding: 12px 16px; background: var(--color-cream); border: 2px solid var(--border-color); border-radius: 10px; font-size: 0.95rem; outline: none; cursor: pointer;">
                        <option value="">Select Category</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                {{ $category->icon }} {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id')
                        <span style="color: var(--color-coral); font-size: 0.85rem; margin-top: 4px; display: block;">{{ $message }}</span>
                    @enderror
                    <p style="font-size: 0.8rem; color: #888; margin-top: 6px;">
                        Don't see your category? 
                        <a href="{{ route('admin.categories.index') }}" target="_blank" style="color: var(--color-coral); font-weight: 600; text-decoration: underline;">Manage Categories</a>
                    </p>
                </div>

                <!-- Short Description -->
                <div style="margin-bottom: 20px;">
                    <label style="display: block; font-weight: 600; margin-bottom: 8px; font-size: 0.9rem;">Short Description <span style="color: var(--color-coral);">*</span></label>
                    <input type="text" name="short_description" value="{{ old('short_description', $product->short_description) }}" required style="width: 100%; padding: 12px 16px; background: var(--color-cream); border: 2px solid var(--border-color); border-radius: 10px; font-size: 0.95rem; outline: none;" placeholder="Brief description for product card">
                    @error('short_description')
                        <span style="color: var(--color-coral); font-size: 0.85rem; margin-top: 4px; display: block;">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Full Description -->
                <div style="margin-bottom: 20px;">
                    <label style="display: block; font-weight: 600; margin-bottom: 8px; font-size: 0.9rem;">Full Description <span style="color: var(--color-coral);">*</span></label>
                    <textarea name="full_description" required rows="4" style="width: 100%; padding: 12px 16px; background: var(--color-cream); border: 2px solid var(--border-color); border-radius: 10px; font-size: 0.95rem; outline: none; resize: vertical;" placeholder="Detailed description for product detail page">{{ old('full_description', $product->full_description) }}</textarea>
                    @error('full_description')
                        <span style="color: var(--color-coral); font-size: 0.85rem; margin-top: 4px; display: block;">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Warranty -->
                <div style="margin-bottom: 20px;">
                    <label style="display: block; font-weight: 600; margin-bottom: 8px; font-size: 0.9rem;">Warranty (Optional)</label>
                    <textarea name="warranty" rows="2" style="width: 100%; padding: 12px 16px; background: var(--color-cream); border: 2px solid var(--border-color); border-radius: 10px; font-size: 0.95rem; outline: none; resize: vertical;" placeholder="e.g., Garansi 30 hari ganti baru">{{ old('warranty', $product->warranty) }}</textarea>
                    @error('warranty')
                        <span style="color: var(--color-coral); font-size: 0.85rem; margin-top: 4px; display: block;">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Terms & Conditions -->
                <div style="margin-bottom: 0;">
                    <label style="display: block; font-weight: 600; margin-bottom: 8px; font-size: 0.9rem;">Terms & Conditions (Optional)</label>
                    <textarea name="terms_conditions" rows="2" style="width: 100%; padding: 12px 16px; background: var(--color-cream); border: 2px solid var(--border-color); border-radius: 10px; font-size: 0.95rem; outline: none; resize: vertical;" placeholder="e.g., Tidak dapat di-refund setelah produk diterima">{{ old('terms_conditions', $product->terms_conditions) }}</textarea>
                    @error('terms_conditions')
                        <span style="color: var(--color-coral); font-size: 0.85rem; margin-top: 4px; display: block;">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <!-- Product Variants -->
            <div style="background: var(--color-white); border: var(--border-width) solid var(--border-color); border-radius: 12px; padding: 28px; box-shadow: var(--shadow-brutal); margin-bottom: 24px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <h3 style="font-family: var(--font-heading); font-size: 1.3rem; font-weight: 700;">Product Variants (Optional)</h3>
                    <button type="button" onclick="addVariant()" style="display: inline-flex; align-items: center; gap: 6px; padding: 8px 16px; background: var(--color-pastel-green); border: 2px solid var(--border-color); border-radius: 8px; font-weight: 600; cursor: pointer; box-shadow: 2px 2px 0px var(--color-black);">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="12" y1="5" x2="12" y2="19"/>
                            <line x1="5" y1="12" x2="19" y2="12"/>
                        </svg>
                        Add Variant
                    </button>
                </div>

                <div id="variants-container">
                    <!-- Existing Variants -->
                    @foreach($product->variants as $index => $variant)
                    <div class="variant-item" id="existing-variant-{{ $variant->id }}" style="background: var(--color-cream); border: 2px solid var(--border-color); border-radius: 10px; padding: 20px; margin-bottom: 16px; position: relative;">
                        <input type="hidden" name="existing_variants[{{ $variant->id }}][id]" value="{{ $variant->id }}">
                        
                        <button type="button" onclick="removeExistingVariant({{ $variant->id }})" style="position: absolute; top: 12px; right: 12px; width: 32px; height: 32px; background: #FFB5B5; border: 2px solid var(--border-color); border-radius: 6px; cursor: pointer; display: flex; align-items: center; justify-content: center;">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="18" y1="6" x2="6" y2="18"/>
                                <line x1="6" y1="6" x2="18" y2="18"/>
                            </svg>
                        </button>

                        <h4 style="font-family: var(--font-heading); font-weight: 700; margin-bottom: 16px; padding-right: 40px;">Variant #{{ $index + 1 }}</h4>

                        <div style="margin-bottom: 12px;">
                            <label style="display: block; font-weight: 600; margin-bottom: 6px; font-size: 0.85rem;">Variant Name <span style="color: var(--color-coral);">*</span></label>
                            <input type="text" name="existing_variants[{{ $variant->id }}][variant_name]" value="{{ old('existing_variants.'.$variant->id.'.variant_name', $variant->variant_name) }}" required style="width: 100%; padding: 10px 14px; background: var(--color-white); border: 2px solid var(--border-color); border-radius: 8px; font-size: 0.9rem; outline: none;" placeholder="e.g., 1 Bulan Sharing">
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 12px;">
                            <div>
                                <label style="display: block; font-weight: 600; margin-bottom: 6px; font-size: 0.85rem;">Price <span style="color: var(--color-coral);">*</span></label>
                                <input type="number" name="existing_variants[{{ $variant->id }}][price]" value="{{ old('existing_variants.'.$variant->id.'.price', $variant->price) }}" required min="0" step="0.01" style="width: 100%; padding: 10px 14px; background: var(--color-white); border: 2px solid var(--border-color); border-radius: 8px; font-size: 0.9rem; outline: none;" placeholder="35000">
                            </div>
                            <div>
                                <label style="display: block; font-weight: 600; margin-bottom: 6px; font-size: 0.85rem;">Wholesale Price</label>
                                <input type="number" name="existing_variants[{{ $variant->id }}][wholesale_price]" value="{{ old('existing_variants.'.$variant->id.'.wholesale_price', $variant->wholesale_price) }}" min="0" step="0.01" style="width: 100%; padding: 10px 14px; background: var(--color-white); border: 2px solid var(--border-color); border-radius: 8px; font-size: 0.9rem; outline: none;" placeholder="30000">
                            </div>
                        </div>

                        <div style="margin-bottom: 12px;">
                            <label style="display: block; font-weight: 600; margin-bottom: 6px; font-size: 0.85rem;">Description</label>
                            <textarea name="existing_variants[{{ $variant->id }}][description]" rows="2" style="width: 100%; padding: 10px 14px; background: var(--color-white); border: 2px solid var(--border-color); border-radius: 8px; font-size: 0.9rem; outline: none; resize: vertical;" placeholder="Variant description">{{ old('existing_variants.'.$variant->id.'.description', $variant->description) }}</textarea>
                        </div>

                        <div>
                            <label style="display: block; font-weight: 600; margin-bottom: 6px; font-size: 0.85rem;">Stock</label>
                            <input type="number" name="existing_variants[{{ $variant->id }}][stock]" value="{{ old('existing_variants.'.$variant->id.'.stock', $variant->stock) }}" min="0" style="width: 100%; padding: 10px 14px; background: var(--color-white); border: 2px solid var(--border-color); border-radius: 8px; font-size: 0.9rem; outline: none;" placeholder="Leave empty for unlimited">
                        </div>
                    </div>
                    @endforeach
                    
                    <!-- New variants will be added here dynamically -->
                </div>

                @if($product->variants->isEmpty())
                <div id="no-variants-message" style="text-align: center; padding: 40px 20px; color: #888;">
                    <div style="font-size: 2.5rem; margin-bottom: 12px;">📦</div>
                    <p style="font-size: 0.95rem;">No variants added yet. Click "Add Variant" to create pricing options.</p>
                </div>
                @else
                <div id="no-variants-message" style="text-align: center; padding: 40px 20px; color: #888; display: none;">
                    <div style="font-size: 2.5rem; margin-bottom: 12px;">📦</div>
                    <p style="font-size: 0.95rem;">No variants added yet. Click "Add Variant" to create pricing options.</p>
                </div>
                @endif
            </div>

            <!-- Product Images -->
            <div style="background: var(--color-white); border: var(--border-width) solid var(--border-color); border-radius: 12px; padding: 28px; box-shadow: var(--shadow-brutal);">
                <h3 style="font-family: var(--font-heading); font-size: 1.3rem; font-weight: 700; margin-bottom: 20px;">Product Images</h3>
                
                <!-- Existing Images -->
                @if($product->images->isNotEmpty())
                <div style="margin-bottom: 20px;">
                    <label style="display: block; font-weight: 600; margin-bottom: 12px; font-size: 0.9rem;">Current Images</label>
                    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(120px, 1fr)); gap: 12px;">
                        @foreach($product->images as $image)
                        <div id="existing-image-{{ $image->id }}" style="position: relative; aspect-ratio: 1; border: 2px solid var(--border-color); border-radius: 8px; overflow: hidden; background: var(--color-cream);">
                            <img src="{{ asset('storage/' . $image->image_path) }}" style="width: 100%; height: 100%; object-fit: cover;">
                            <button type="button" onclick="deleteExistingImage({{ $image->id }})" style="position: absolute; top: 6px; right: 6px; width: 28px; height: 28px; background: #FFB5B5; border: 2px solid var(--border-color); border-radius: 6px; cursor: pointer; display: flex; align-items: center; justify-content: center;">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <line x1="18" y1="6" x2="6" y2="18"/>
                                    <line x1="6" y1="6" x2="18" y2="18"/>
                                </svg>
                            </button>
                            <input type="hidden" name="delete_images[]" id="delete-image-{{ $image->id }}" value="" disabled>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
                
                <div style="margin-bottom: 16px;">
                    <label style="display: block; font-weight: 600; margin-bottom: 8px; font-size: 0.9rem;">Upload New Images</label>
                    <input type="file" name="images[]" multiple accept="image/jpeg,image/png,image/webp" onchange="previewImages(event)" style="width: 100%; padding: 12px 16px; background: var(--color-cream); border: 2px solid var(--border-color); border-radius: 10px; font-size: 0.95rem; cursor: pointer;">
                    <p style="font-size: 0.8rem; color: #888; margin-top: 6px;">Max 2MB per image. Formats: JPEG, PNG, WebP</p>
                    @error('images.*')
                        <span style="color: var(--color-coral); font-size: 0.85rem; margin-top: 4px; display: block;">{{ $message }}</span>
                    @enderror
                </div>

                <div id="image-preview" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(120px, 1fr)); gap: 12px;">
                    <!-- Image previews will appear here -->
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div>
            <!-- Status & Actions -->
            <div style="background: var(--color-white); border: var(--border-width) solid var(--border-color); border-radius: 12px; padding: 24px; box-shadow: var(--shadow-brutal); position: sticky; top: 100px;">
                <h3 style="font-family: var(--font-heading); font-size: 1.1rem; font-weight: 700; margin-bottom: 20px;">Publish</h3>

                <!-- Status -->
                <div style="margin-bottom: 24px;">
                    <label style="display: flex; align-items: center; gap: 12px; cursor: pointer;">
                        <input type="checkbox" name="status" value="1" {{ old('status', $product->status) ? 'checked' : '' }} style="width: 20px; height: 20px; cursor: pointer;">
                        <span style="font-weight: 600; font-size: 0.95rem;">Active (visible on landing page)</span>
                    </label>
                </div>

                <!-- Submit Button -->
                <button type="submit" style="width: 100%; padding: 14px 24px; background: var(--color-coral); color: white; border: var(--border-width) solid var(--border-color); border-radius: 10px; font-family: var(--font-heading); font-weight: 700; font-size: 1rem; cursor: pointer; box-shadow: var(--shadow-brutal); transition: all 0.2s ease; margin-bottom: 12px;">
                    Update Product
                </button>

                <a href="{{ route('admin.products.index') }}" style="display: block; width: 100%; padding: 14px 24px; background: var(--color-white); color: var(--color-black); border: 2px solid var(--border-color); border-radius: 10px; font-family: var(--font-heading); font-weight: 600; font-size: 0.95rem; text-align: center; text-decoration: none; box-shadow: 3px 3px 0px var(--color-black); transition: all 0.2s ease;">
                    Cancel
                </a>
            </div>
        </div>
    </div>
</form>

@push('scripts')
<script>
let variantCount = {{ $product->variants->count() }};
let deletedVariants = [];

// Logo preview function
function previewLogo(event) {
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.getElementById('logo-preview');
            const img = document.getElementById('logo-preview-img');
            img.src = e.target.result;
            preview.style.display = 'block';
        };
        reader.readAsDataURL(file);
    }
}

function addVariant() {
    variantCount++;
    const container = document.getElementById('variants-container');
    const noMessage = document.getElementById('no-variants-message');
    
    if (noMessage) {
        noMessage.style.display = 'none';
    }

    const variantHtml = `
        <div class="variant-item" id="variant-${variantCount}" style="background: var(--color-cream); border: 2px solid var(--border-color); border-radius: 10px; padding: 20px; margin-bottom: 16px; position: relative;">
            <button type="button" onclick="removeVariant(${variantCount})" style="position: absolute; top: 12px; right: 12px; width: 32px; height: 32px; background: #FFB5B5; border: 2px solid var(--border-color); border-radius: 6px; cursor: pointer; display: flex; align-items: center; justify-content: center;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18"/>
                    <line x1="6" y1="6" x2="18" y2="18"/>
                </svg>
            </button>

            <h4 style="font-family: var(--font-heading); font-weight: 700; margin-bottom: 16px; padding-right: 40px;">New Variant #${variantCount}</h4>

            <div style="margin-bottom: 12px;">
                <label style="display: block; font-weight: 600; margin-bottom: 6px; font-size: 0.85rem;">Variant Name <span style="color: var(--color-coral);">*</span></label>
                <input type="text" name="variants[${variantCount}][variant_name]" required style="width: 100%; padding: 10px 14px; background: var(--color-white); border: 2px solid var(--border-color); border-radius: 8px; font-size: 0.9rem; outline: none;" placeholder="e.g., 1 Bulan Sharing">
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 12px;">
                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 6px; font-size: 0.85rem;">Price <span style="color: var(--color-coral);">*</span></label>
                    <input type="number" name="variants[${variantCount}][price]" required min="0" step="0.01" style="width: 100%; padding: 10px 14px; background: var(--color-white); border: 2px solid var(--border-color); border-radius: 8px; font-size: 0.9rem; outline: none;" placeholder="35000">
                </div>
                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 6px; font-size: 0.85rem;">Wholesale Price</label>
                    <input type="number" name="variants[${variantCount}][wholesale_price]" min="0" step="0.01" style="width: 100%; padding: 10px 14px; background: var(--color-white); border: 2px solid var(--border-color); border-radius: 8px; font-size: 0.9rem; outline: none;" placeholder="30000">
                </div>
            </div>

            <div style="margin-bottom: 12px;">
                <label style="display: block; font-weight: 600; margin-bottom: 6px; font-size: 0.85rem;">Description</label>
                <textarea name="variants[${variantCount}][description]" rows="2" style="width: 100%; padding: 10px 14px; background: var(--color-white); border: 2px solid var(--border-color); border-radius: 8px; font-size: 0.9rem; outline: none; resize: vertical;" placeholder="Variant description"></textarea>
            </div>

            <div>
                <label style="display: block; font-weight: 600; margin-bottom: 6px; font-size: 0.85rem;">Stock</label>
                <input type="number" name="variants[${variantCount}][stock]" min="0" style="width: 100%; padding: 10px 14px; background: var(--color-white); border: 2px solid var(--border-color); border-radius: 8px; font-size: 0.9rem; outline: none;" placeholder="Leave empty for unlimited">
            </div>
        </div>
    `;

    container.insertAdjacentHTML('beforeend', variantHtml);
}

function removeVariant(id) {
    const variant = document.getElementById(`variant-${id}`);
    if (variant) {
        variant.remove();
    }

    // Show no variants message if all removed
    const container = document.getElementById('variants-container');
    const noMessage = document.getElementById('no-variants-message');
    if (container.children.length === 0 && noMessage) {
        noMessage.style.display = 'block';
    }
}

function removeExistingVariant(id) {
    if (confirm('Are you sure you want to delete this variant?')) {
        const variant = document.getElementById(`existing-variant-${id}`);
        if (variant) {
            // Add hidden input to mark for deletion
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'delete_variants[]';
            input.value = id;
            variant.appendChild(input);
            
            // Hide the variant
            variant.style.display = 'none';
        }

        // Show no variants message if all removed
        const container = document.getElementById('variants-container');
        const visibleVariants = container.querySelectorAll('.variant-item:not([style*="display: none"])');
        const noMessage = document.getElementById('no-variants-message');
        if (visibleVariants.length === 0 && noMessage) {
            noMessage.style.display = 'block';
        }
    }
}

function deleteExistingImage(id) {
    if (confirm('Are you sure you want to delete this image?')) {
        const imageDiv = document.getElementById(`existing-image-${id}`);
        const deleteInput = document.getElementById(`delete-image-${id}`);
        
        if (imageDiv && deleteInput) {
            // Mark for deletion
            deleteInput.value = id;
            deleteInput.disabled = false;
            
            // Hide the image
            imageDiv.style.opacity = '0.3';
            imageDiv.style.pointerEvents = 'none';
            
            // Add "DELETED" overlay
            const overlay = document.createElement('div');
            overlay.style.cssText = 'position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: rgba(255, 0, 0, 0.7); display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 0.8rem;';
            overlay.textContent = 'DELETED';
            imageDiv.appendChild(overlay);
        }
    }
}

function previewImages(event) {
    const preview = document.getElementById('image-preview');
    preview.innerHTML = '';

    const files = event.target.files;
    for (let i = 0; i < files.length; i++) {
        const file = files[i];
        const reader = new FileReader();

        reader.onload = function(e) {
            const div = document.createElement('div');
            div.style.cssText = 'position: relative; aspect-ratio: 1; border: 2px solid var(--border-color); border-radius: 8px; overflow: hidden; background: var(--color-cream);';
            div.innerHTML = `<img src="${e.target.result}" style="width: 100%; height: 100%; object-fit: cover;">`;
            preview.appendChild(div);
        };

        reader.readAsDataURL(file);
    }
}
</script>
@endpush

@push('styles')
<style>
    @media (max-width: 1024px) {
        form > div {
            grid-template-columns: 1fr !important;
        }

        .admin-content > form > div > div:last-child > div {
            position: static !important;
        }
    }
</style>
@endpush
@endsection
