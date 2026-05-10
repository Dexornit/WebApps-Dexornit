@extends('admin.layouts.admin')

@section('title', 'Categories')
@section('page-title', 'Categories')

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

<!-- Header Actions -->
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 32px; flex-wrap: wrap; gap: 16px;">
    <div>
        <h2 style="font-family: var(--font-heading); font-size: 1.5rem; font-weight: 700; margin-bottom: 4px;">
            Product Categories
        </h2>
        <p style="color: #666; font-size: 0.9rem;">Manage product categories for your store</p>
    </div>
    <a href="{{ route('admin.categories.create') }}" style="display: inline-flex; align-items: center; gap: 8px; padding: 12px 24px; background: var(--color-coral); color: white; border: var(--border-width) solid var(--border-color); border-radius: 10px; font-family: var(--font-heading); font-weight: 600; text-decoration: none; box-shadow: var(--shadow-brutal); transition: all 0.2s ease;">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <line x1="12" y1="5" x2="12" y2="19"/>
            <line x1="5" y1="12" x2="19" y2="12"/>
        </svg>
        Add New Category
    </a>
</div>

<!-- Categories Grid -->
<div class="cat-grid">
    @forelse($categories as $category)
        <div style="background: var(--color-white); border: var(--border-width) solid var(--border-color); border-radius: 12px; padding: 24px; box-shadow: var(--shadow-brutal); position: relative;">
            <!-- Status Badge -->
            <div style="position: absolute; top: 16px; right: 16px;">
                @if($category->status)
                    <span style="display: inline-flex; align-items: center; gap: 6px; padding: 4px 12px; background: var(--color-pastel-green); border: 2px solid var(--border-color); border-radius: 50px; font-size: 0.7rem; font-weight: 600;">
                        <span style="width: 6px; height: 6px; background: #4CAF50; border-radius: 50%;"></span>
                        Active
                    </span>
                @else
                    <span style="display: inline-flex; align-items: center; gap: 6px; padding: 4px 12px; background: #E0E0E0; border: 2px solid var(--border-color); border-radius: 50px; font-size: 0.7rem; font-weight: 600;">
                        <span style="width: 6px; height: 6px; background: #999; border-radius: 50%;"></span>
                        Inactive
                    </span>
                @endif
            </div>

            <!-- Category Icon & Color -->
            <div style="width: 80px; height: 80px; background: {{ $category->color }}; border: var(--border-width) solid var(--border-color); border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 2.5rem; margin-bottom: 16px; box-shadow: 3px 3px 0px var(--color-black);">
                {{ $category->icon ?? '📁' }}
            </div>

            <!-- Category Info -->
            <h3 style="font-family: var(--font-heading); font-size: 1.3rem; font-weight: 700; margin-bottom: 8px;">
                {{ $category->name }}
            </h3>
            <p style="color: #666; font-size: 0.85rem; margin-bottom: 4px;">
                <strong>Slug:</strong> {{ $category->slug }}
            </p>
            <p style="color: #666; font-size: 0.85rem; margin-bottom: 4px;">
                <strong>Order:</strong> {{ $category->order }}
            </p>
            <p style="color: #666; font-size: 0.85rem; margin-bottom: 16px;">
                <strong>Products:</strong> {{ $category->products()->count() }}
            </p>

            <!-- Actions -->
            <div style="display: flex; gap: 8px;">
                <a href="{{ route('admin.categories.edit', $category->id) }}" style="flex: 1; display: inline-flex; align-items: center; justify-content: center; gap: 6px; padding: 10px 16px; background: var(--color-pastel-blue); border: 2px solid var(--border-color); border-radius: 8px; font-size: 0.85rem; font-weight: 600; text-decoration: none; color: var(--color-black); box-shadow: 2px 2px 0px var(--color-black); transition: all 0.2s ease;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                    </svg>
                    Edit
                </a>

                <form method="POST" action="{{ route('admin.categories.destroy', $category->id) }}" style="flex: 1;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" onclick="return confirm('Are you sure you want to delete this category? This action cannot be undone if there are no products.')" style="width: 100%; display: inline-flex; align-items: center; justify-content: center; gap: 6px; padding: 10px 16px; background: #FFB5B5; border: 2px solid var(--border-color); border-radius: 8px; font-size: 0.85rem; font-weight: 600; cursor: pointer; box-shadow: 2px 2px 0px var(--color-black); transition: all 0.2s ease;">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="3 6 5 6 21 6"/>
                            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                        </svg>
                        Delete
                    </button>
                </form>
            </div>
        </div>
    @empty
        <!-- Empty State -->
        <div style="grid-column: 1 / -1; text-align: center; padding: 60px 20px; background: var(--color-white); border: var(--border-width) solid var(--border-color); border-radius: 12px; box-shadow: var(--shadow-brutal);">
            <div style="font-size: 4rem; margin-bottom: 16px;">📁</div>
            <h3 style="font-family: var(--font-heading); font-size: 1.5rem; font-weight: 700; margin-bottom: 8px;">
                No categories yet
            </h3>
            <p style="color: #666; margin-bottom: 24px; font-size: 0.95rem;">
                Start by adding your first product category
            </p>
            <a href="{{ route('admin.categories.create') }}" style="display: inline-flex; align-items: center; gap: 8px; padding: 12px 24px; background: var(--color-coral); color: white; border: var(--border-width) solid var(--border-color); border-radius: 10px; font-family: var(--font-heading); font-weight: 600; text-decoration: none; box-shadow: var(--shadow-brutal);">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="12" y1="5" x2="12" y2="19"/>
                    <line x1="5" y1="12" x2="19" y2="12"/>
                </svg>
                Add Your First Category
            </a>
        </div>
    @endforelse
</div>

@push('styles')
<style>
    .cat-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
    }
    @media (max-width: 600px) {
        .cat-grid { grid-template-columns: 1fr; }
    }
    .cat-card { font-size: clamp(0.8rem, 2.5vw, 0.95rem); }
    .cat-card h3 { font-size: clamp(1rem, 3vw, 1.3rem) !important; }
</style>
@endpush
@endsection
