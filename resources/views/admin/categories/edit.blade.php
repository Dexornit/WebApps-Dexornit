@extends('admin.layouts.admin')

@section('title', 'Edit Category')
@section('page-title', 'Edit Category')

@section('content')
<!-- Back Button -->
<div style="margin-bottom: 24px;">
    <a href="{{ route('admin.categories.index') }}" style="display: inline-flex; align-items: center; gap: 8px; padding: 10px 20px; background: var(--color-white); border: 2px solid var(--border-color); border-radius: 8px; font-weight: 600; text-decoration: none; color: var(--color-black); box-shadow: 2px 2px 0px var(--color-black); transition: all 0.2s ease;">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M19 12H5M12 19l-7-7 7-7"/>
        </svg>
        Back to Categories
    </a>
</div>

<form method="POST" action="{{ route('admin.categories.update', $category->id) }}">
    @csrf
    @method('PUT')

    <div class="admin-form-grid" style="display: grid; grid-template-columns: 2fr 1fr; gap: 24px; align-items: start;">
        <!-- Main Form -->
        <div>
            <div style="background: var(--color-white); border: var(--border-width) solid var(--border-color); border-radius: 12px; padding: 28px; box-shadow: var(--shadow-brutal);">
                <h3 style="font-family: var(--font-heading); font-size: 1.3rem; font-weight: 700; margin-bottom: 24px;">Category Information</h3>

                <!-- Name -->
                <div style="margin-bottom: 20px;">
                    <label style="display: block; font-weight: 600; margin-bottom: 8px; font-size: 0.9rem;">Category Name <span style="color: var(--color-coral);">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $category->name) }}" required
                        style="width: 100%; padding: 12px 16px; background: var(--color-cream); border: 2px solid var(--border-color); border-radius: 10px; font-size: 0.95rem; outline: none;"
                        placeholder="e.g., Streaming">
                    @error('name')
                        <span style="color: var(--color-coral); font-size: 0.85rem; margin-top: 4px; display: block;">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Icon & Color -->
                <div class="cat-icon-row" style="display: grid; grid-template-columns: 1fr 2fr; gap: 16px; margin-bottom: 20px;">
                    <div>
                        <label style="display: block; font-weight: 600; margin-bottom: 8px; font-size: 0.9rem;">Icon (Emoji)</label>
                        <input type="text" name="icon" value="{{ old('icon', $category->icon) }}" maxlength="10"
                            style="width: 100%; padding: 12px 16px; background: var(--color-cream); border: 2px solid var(--border-color); border-radius: 10px; font-size: 1.5rem; text-align: center; outline: none;"
                            placeholder="🎬">
                        @error('icon')
                            <span style="color: var(--color-coral); font-size: 0.85rem; margin-top: 4px; display: block;">{{ $message }}</span>
                        @enderror
                    </div>
                    <div>
                        <label style="display: block; font-weight: 600; margin-bottom: 8px; font-size: 0.9rem;">Color (Hex) <span style="color: var(--color-coral);">*</span></label>
                        <div style="display: flex; gap: 12px; align-items: center;">
                            <input type="color" name="color" value="{{ old('color', $category->color) }}" required
                                style="width: 56px; height: 48px; border: 2px solid var(--border-color); border-radius: 8px; cursor: pointer; padding: 2px; background: var(--color-cream); flex-shrink: 0;">
                            <input type="text" id="color-text" value="{{ old('color', $category->color) }}" readonly
                                style="flex: 1; min-width: 0; padding: 12px 16px; background: var(--color-cream); border: 2px solid var(--border-color); border-radius: 10px; font-size: 0.95rem; outline: none;">
                        </div>
                        @error('color')
                            <span style="color: var(--color-coral); font-size: 0.85rem; margin-top: 4px; display: block;">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <!-- Order -->
                <div style="margin-bottom: 20px;">
                    <label style="display: block; font-weight: 600; margin-bottom: 8px; font-size: 0.9rem;">Display Order</label>
                    <input type="number" name="order" value="{{ old('order', $category->order) }}" min="0"
                        style="width: 100%; padding: 12px 16px; background: var(--color-cream); border: 2px solid var(--border-color); border-radius: 10px; font-size: 0.95rem; outline: none;"
                        placeholder="0">
                    <p style="font-size: 0.8rem; color: #888; margin-top: 6px;">Lower numbers appear first.</p>
                    @error('order')
                        <span style="color: var(--color-coral); font-size: 0.85rem; margin-top: 4px; display: block;">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Info -->
                <div style="background: var(--color-cream); border: 2px solid var(--border-color); border-radius: 10px; padding: 16px;">
                    <p style="font-size: 0.85rem; color: #666; margin-bottom: 8px;">
                        <strong>Slug:</strong> {{ $category->slug }}
                    </p>
                    <p style="font-size: 0.85rem; color: #666; margin-bottom: 0;">
                        <strong>Products using this category:</strong> {{ $category->products()->count() }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="admin-form-sidebar">
            <div style="background: var(--color-white); border: var(--border-width) solid var(--border-color); border-radius: 12px; padding: 24px; box-shadow: var(--shadow-brutal); position: sticky; top: 100px;">
                <h3 style="font-family: var(--font-heading); font-size: 1.1rem; font-weight: 700; margin-bottom: 20px;">Publish</h3>

                <!-- Status -->
                <div style="margin-bottom: 24px;">
                    <label style="display: flex; align-items: center; gap: 12px; cursor: pointer;">
                        <input type="checkbox" name="status" value="1" {{ old('status', $category->status) ? 'checked' : '' }}
                            style="width: 20px; height: 20px; cursor: pointer; flex-shrink: 0;">
                        <span style="font-weight: 600; font-size: 0.95rem;">Active (visible in product forms)</span>
                    </label>
                </div>

                <!-- Submit Button -->
                <button type="submit" style="width: 100%; padding: 14px 24px; background: var(--color-coral); color: white; border: var(--border-width) solid var(--border-color); border-radius: 10px; font-family: var(--font-heading); font-weight: 700; font-size: 1rem; cursor: pointer; box-shadow: var(--shadow-brutal); transition: all 0.2s ease; margin-bottom: 12px;">
                    Update Category
                </button>

                <a href="{{ route('admin.categories.index') }}" style="display: block; width: 100%; padding: 14px 24px; background: var(--color-white); color: var(--color-black); border: 2px solid var(--border-color); border-radius: 10px; font-family: var(--font-heading); font-weight: 600; font-size: 0.95rem; text-align: center; text-decoration: none; box-shadow: 3px 3px 0px var(--color-black); transition: all 0.2s ease;">
                    Cancel
                </a>
            </div>
        </div>
    </div>
</form>

@push('scripts')
<script>
document.querySelector('input[name="color"]').addEventListener('input', function(e) {
    document.getElementById('color-text').value = e.target.value.toUpperCase();
});
</script>
@endpush

@push('styles')
<style>
    @media (max-width: 768px) {
        .admin-form-sidebar > div { position: static !important; }
    }
    @media (max-width: 480px) {
        .cat-icon-row { grid-template-columns: 1fr !important; }
    }
</style>
@endpush
@endsection
