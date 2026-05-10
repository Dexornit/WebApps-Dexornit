@extends('admin.layouts.admin')

@section('title', 'Products')
@section('page-title', 'Products')

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
            All Products
        </h2>
        <p style="color: #666; font-size: 0.9rem;">Manage your product catalog</p>
    </div>
    <a href="{{ route('admin.products.create') }}" style="display: inline-flex; align-items: center; gap: 8px; padding: 12px 24px; background: var(--color-coral); color: white; border: var(--border-width) solid var(--border-color); border-radius: 10px; font-family: var(--font-heading); font-weight: 600; text-decoration: none; box-shadow: var(--shadow-brutal); transition: all 0.2s ease;">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <line x1="12" y1="5" x2="12" y2="19"/>
            <line x1="5" y1="12" x2="19" y2="12"/>
        </svg>
        Add New Product
    </a>
</div>

<!-- Search & Filter Bar -->
<div style="background: var(--color-white); border: var(--border-width) solid var(--border-color); border-radius: 12px; padding: 20px; box-shadow: var(--shadow-brutal); margin-bottom: 24px;">
    <form method="GET" action="{{ route('admin.products.index') }}">
        <div style="display: grid; grid-template-columns: 2fr 1fr 1fr auto; gap: 12px; align-items: center;">
            <!-- Search Input -->
            <div style="position: relative;">
                <input 
                    type="text" 
                    name="search" 
                    value="{{ request('search') }}" 
                    placeholder="Search products by name..." 
                    style="width: 100%; padding: 12px 16px 12px 44px; background: var(--color-cream); border: 2px solid var(--border-color); border-radius: 10px; font-size: 0.95rem; font-family: var(--font-body); outline: none; transition: all 0.2s ease;"
                    onfocus="this.style.borderColor='var(--color-coral)'; this.style.boxShadow='0 0 0 3px rgba(249, 104, 84, 0.1)'"
                    onblur="this.style.borderColor='var(--border-color)'; this.style.boxShadow='none'"
                >
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: #888; pointer-events: none;">
                    <circle cx="11" cy="11" r="8"></circle>
                    <path d="m21 21-4.35-4.35"></path>
                </svg>
            </div>

            <!-- Category Filter -->
            <select name="category" style="width: 100%; padding: 12px 16px; background: var(--color-cream); border: 2px solid var(--border-color); border-radius: 10px; font-size: 0.95rem; outline: none; cursor: pointer;">
                <option value="">All Categories</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                        {{ $category->icon }} {{ $category->name }}
                    </option>
                @endforeach
            </select>

            <!-- Status Filter -->
            <select name="status" style="width: 100%; padding: 12px 16px; background: var(--color-cream); border: 2px solid var(--border-color); border-radius: 10px; font-size: 0.95rem; outline: none; cursor: pointer;">
                <option value=""    {{ request('status') === ''        ? 'selected' : '' }}>All Status</option>
                <option value="active"   {{ request('status') === 'active'   ? 'selected' : '' }}>✅ Active</option>
                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>⏸ Inactive</option>
                <option value="deleted"  {{ request('status') === 'deleted'  ? 'selected' : '' }}>🗑 Deleted</option>
            </select>

            <!-- Action Buttons -->
            <div style="display: flex; gap: 8px;">
                <button type="submit" style="padding: 12px 24px; background: var(--color-coral); color: white; border: var(--border-width) solid var(--border-color); border-radius: 10px; font-family: var(--font-heading); font-weight: 600; cursor: pointer; box-shadow: 3px 3px 0px var(--color-black); transition: all 0.2s ease; white-space: nowrap;">
                    Filter
                </button>
                @if(request('search') || request('category') || request('status'))
                    <a href="{{ route('admin.products.index') }}" style="padding: 12px 24px; background: var(--color-white); color: var(--color-black); border: var(--border-width) solid var(--border-color); border-radius: 10px; font-family: var(--font-heading); font-weight: 600; text-decoration: none; box-shadow: 3px 3px 0px var(--color-black); transition: all 0.2s ease; white-space: nowrap; display: inline-flex; align-items: center;">
                        Clear
                    </a>
                @endif
            </div>
        </div>
    </form>
</div>

<!-- Products Table -->
<div style="background: var(--color-white); border: var(--border-width) solid var(--border-color); border-radius: 12px; box-shadow: var(--shadow-brutal); overflow: hidden;">
    @if($products->count() > 0)
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background: var(--color-cream); border-bottom: var(--border-width) solid var(--border-color);">
                        <th style="text-align: left; padding: 16px; font-family: var(--font-heading); font-weight: 700; font-size: 0.85rem; text-transform: uppercase; color: #666;">Product</th>
                        <th style="text-align: left; padding: 16px; font-family: var(--font-heading); font-weight: 700; font-size: 0.85rem; text-transform: uppercase; color: #666;">Category</th>
                        <th style="text-align: left; padding: 16px; font-family: var(--font-heading); font-weight: 700; font-size: 0.85rem; text-transform: uppercase; color: #666;">Price</th>
                        <th style="text-align: left; padding: 16px; font-family: var(--font-heading); font-weight: 700; font-size: 0.85rem; text-transform: uppercase; color: #666;">Status</th>
                        <th style="text-align: right; padding: 16px; font-family: var(--font-heading); font-weight: 700; font-size: 0.85rem; text-transform: uppercase; color: #666;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($products as $product)
                        <tr style="border-bottom: 1px solid #eee; {{ $product->trashed() ? 'opacity: 0.6; background: #f9f9f9;' : '' }}">
                            <td style="padding: 16px;">
                                <div style="display: flex; align-items: center; gap: 12px;">
                                    <span style="font-size: 2.5rem;">{{ $product->emoji }}</span>
                                    <div>
                                        <div style="font-weight: 600; font-size: 0.95rem; margin-bottom: 2px;">
                                            {{ $product->name }}
                                            @if($product->trashed())
                                                <span style="display: inline-block; padding: 2px 8px; background: #FFB5B5; border: 2px solid var(--border-color); border-radius: 4px; font-size: 0.7rem; font-weight: 700; margin-left: 8px;">DELETED</span>
                                            @endif
                                        </div>
                                        <div style="font-size: 0.8rem; color: #888;">{{ Str::limit($product->short_description, 50) }}</div>
                                    </div>
                                </div>
                            </td>
                            <td style="padding: 16px;">
                                @if($product->category)
                                    <span style="display: inline-block; padding: 6px 14px; background: {{ $product->category->color }}; border: 2px solid var(--border-color); border-radius: 50px; font-size: 0.75rem; font-weight: 600; text-transform: uppercase;">
                                        {{ $product->category->icon }} {{ $product->category->name }}
                                    </span>
                                @else
                                    <span style="display: inline-block; padding: 6px 14px; background: #E0E0E0; border: 2px solid var(--border-color); border-radius: 50px; font-size: 0.75rem; font-weight: 600;">
                                        No Category
                                    </span>
                                @endif
                            </td>
                            <td style="padding: 16px;">
                                @if($product->variants->count() > 0)
                                    <div style="font-weight: 600; color: var(--color-coral); font-size: 1rem;">
                                        Rp {{ number_format($product->variants->min('price'), 0, ',', '.') }}
                                    </div>
                                    <div style="font-size: 0.75rem; color: #888;">
                                        {{ $product->variants->count() }} variant(s)
                                    </div>
                                @else
                                    <span style="color: #888; font-size: 0.85rem;">No variants</span>
                                @endif
                            </td>
                            <td style="padding: 16px;">
                                @if($product->trashed())
                                    <span style="display: inline-flex; align-items: center; gap: 6px; padding: 6px 14px; background: #FFB5B5; border: 2px solid var(--border-color); border-radius: 50px; font-size: 0.75rem; font-weight: 600;">
                                        <span style="width: 6px; height: 6px; background: #F44336; border-radius: 50%;"></span>
                                        Deleted
                                    </span>
                                @elseif($product->status)
                                    <span style="display: inline-flex; align-items: center; gap: 6px; padding: 6px 14px; background: var(--color-pastel-green); border: 2px solid var(--border-color); border-radius: 50px; font-size: 0.75rem; font-weight: 600;">
                                        <span style="width: 6px; height: 6px; background: #4CAF50; border-radius: 50%;"></span>
                                        Active
                                    </span>
                                @else
                                    <span style="display: inline-flex; align-items: center; gap: 6px; padding: 6px 14px; background: var(--color-pastel-yellow); border: 2px solid var(--border-color); border-radius: 50px; font-size: 0.75rem; font-weight: 600;">
                                        <span style="width: 6px; height: 6px; background: #FFC107; border-radius: 50%;"></span>
                                        Inactive
                                    </span>
                                @endif
                            </td>
                            <td style="padding: 16px;">
                                <div style="display: flex; gap: 8px; justify-content: flex-end;">
                                    @if($product->trashed())
                                        <!-- Restore Button -->
                                        <form method="POST" action="{{ route('admin.products.restore', $product->id) }}" style="display: inline;">
                                            @csrf
                                            <button type="submit" onclick="return confirm('Are you sure you want to restore this product?')" style="display: inline-flex; align-items: center; gap: 6px; padding: 8px 14px; background: var(--color-pastel-green); border: 2px solid var(--border-color); border-radius: 8px; font-size: 0.85rem; font-weight: 600; cursor: pointer; box-shadow: 2px 2px 0px var(--color-black); transition: all 0.2s ease;">
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <polyline points="23 4 23 10 17 10"/>
                                                    <path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"/>
                                                </svg>
                                                Restore
                                            </button>
                                        </form>
                                    @else
                                        <!-- Edit Button -->
                                        <a href="{{ route('admin.products.edit', $product->id) }}" style="display: inline-flex; align-items: center; gap: 6px; padding: 8px 14px; background: var(--color-pastel-blue); border: 2px solid var(--border-color); border-radius: 8px; font-size: 0.85rem; font-weight: 600; text-decoration: none; color: var(--color-black); box-shadow: 2px 2px 0px var(--color-black); transition: all 0.2s ease;">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                            </svg>
                                            Edit
                                        </a>

                                        <!-- Toggle Status Button -->
                                        <form method="POST" action="{{ route('admin.products.toggleStatus', $product->id) }}" style="display: inline;">
                                            @csrf
                                            <button type="submit" style="display: inline-flex; align-items: center; gap: 6px; padding: 8px 14px; background: {{ $product->status ? 'var(--color-pastel-yellow)' : 'var(--color-pastel-green)' }}; border: 2px solid var(--border-color); border-radius: 8px; font-size: 0.85rem; font-weight: 600; cursor: pointer; box-shadow: 2px 2px 0px var(--color-black); transition: all 0.2s ease;">
                                                @if($product->status)
                                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                        <circle cx="12" cy="12" r="10"/>
                                                        <line x1="15" y1="9" x2="9" y2="15"/>
                                                        <line x1="9" y1="9" x2="15" y2="15"/>
                                                    </svg>
                                                    Deactivate
                                                @else
                                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                        <polyline points="20 6 9 17 4 12"/>
                                                    </svg>
                                                    Activate
                                                @endif
                                            </button>
                                        </form>

                                        <!-- Delete Button -->
                                        <form method="POST" action="{{ route('admin.products.destroy', $product->id) }}" style="display: inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" onclick="return confirm('Are you sure you want to delete this product? It can be restored later.')" style="display: inline-flex; align-items: center; gap: 6px; padding: 8px 14px; background: #FFB5B5; border: 2px solid var(--border-color); border-radius: 8px; font-size: 0.85rem; font-weight: 600; cursor: pointer; box-shadow: 2px 2px 0px var(--color-black); transition: all 0.2s ease;">
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <polyline points="3 6 5 6 21 6"/>
                                                    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                                                </svg>
                                                Delete
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($products->hasPages())
            <div style="padding: 20px; border-top: 2px solid var(--border-color); background: var(--color-cream);">
                <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
                    <div style="font-size: 0.9rem; color: #666;">
                        Showing {{ $products->firstItem() }} to {{ $products->lastItem() }} of {{ $products->total() }} products
                    </div>
                    <div style="display: flex; gap: 8px;">
                        @if($products->onFirstPage())
                            <span style="padding: 8px 16px; background: #eee; border: 2px solid var(--border-color); border-radius: 8px; font-weight: 600; color: #999;">Previous</span>
                        @else
                            <a href="{{ $products->previousPageUrl() }}" style="padding: 8px 16px; background: var(--color-white); border: 2px solid var(--border-color); border-radius: 8px; font-weight: 600; text-decoration: none; color: var(--color-black); box-shadow: 2px 2px 0px var(--color-black); transition: all 0.2s ease;">Previous</a>
                        @endif

                        @if($products->hasMorePages())
                            <a href="{{ $products->nextPageUrl() }}" style="padding: 8px 16px; background: var(--color-coral); color: white; border: 2px solid var(--border-color); border-radius: 8px; font-weight: 600; text-decoration: none; box-shadow: 2px 2px 0px var(--color-black); transition: all 0.2s ease;">Next</a>
                        @else
                            <span style="padding: 8px 16px; background: #eee; border: 2px solid var(--border-color); border-radius: 8px; font-weight: 600; color: #999;">Next</span>
                        @endif
                    </div>
                </div>
            </div>
        @endif
    @else
        <!-- Empty State -->
        <div style="text-align: center; padding: 60px 20px;">
            <div style="font-size: 4rem; margin-bottom: 16px;">📦</div>
            <h3 style="font-family: var(--font-heading); font-size: 1.5rem; font-weight: 700; margin-bottom: 8px;">
                @if(request('search'))
                    No products found
                @else
                    No products yet
                @endif
            </h3>
            <p style="color: #666; margin-bottom: 24px; font-size: 0.95rem;">
                @if(request('search'))
                    Try adjusting your search terms
                @else
                    Start by adding your first product to the catalog
                @endif
            </p>
            @if(request('search'))
                <a href="{{ route('admin.products.index') }}" style="display: inline-flex; align-items: center; gap: 8px; padding: 12px 24px; background: var(--color-white); border: var(--border-width) solid var(--border-color); border-radius: 10px; font-family: var(--font-heading); font-weight: 600; text-decoration: none; color: var(--color-black); box-shadow: var(--shadow-brutal);">
                    Clear Search
                </a>
            @else
                <a href="{{ route('admin.products.create') }}" style="display: inline-flex; align-items: center; gap: 8px; padding: 12px 24px; background: var(--color-coral); color: white; border: var(--border-width) solid var(--border-color); border-radius: 10px; font-family: var(--font-heading); font-weight: 600; text-decoration: none; box-shadow: var(--shadow-brutal);">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="12" y1="5" x2="12" y2="19"/>
                        <line x1="5" y1="12" x2="19" y2="12"/>
                    </svg>
                    Add Your First Product
                </a>
            @endif
        </div>
    @endif
</div>

@push('styles')
<style>
    @media (max-width: 1024px) {
        table {
            font-size: 0.85rem;
        }
        
        table th,
        table td {
            padding: 12px 8px !important;
        }

        table td > div {
            flex-direction: column !important;
            align-items: flex-start !important;
            gap: 8px !important;
        }

        /* Responsive filter bar - stack on mobile */
        form > div[style*="grid-template-columns"] {
            grid-template-columns: 1fr !important;
        }

        form > div > div:last-child {
            justify-content: stretch !important;
        }

        form > div > div:last-child > * {
            flex: 1 !important;
        }
    }
</style>
@endpush
@endsection
