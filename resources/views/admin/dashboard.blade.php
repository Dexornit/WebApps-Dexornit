@extends('admin.layouts.admin')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<div style="margin-bottom: 32px;">
    <h2 style="font-family: var(--font-heading); font-size: 1.8rem; font-weight: 700; margin-bottom: 8px;">
        Welcome back, {{ Auth::user()->name }}! 👋
    </h2>
    <p style="color: #666; font-size: 0.95rem;">Here's what's happening with your store today.</p>
</div>

<!-- Stats Cards -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 20px; margin-bottom: 40px;">
    <!-- Total Products -->
    <div style="background: var(--color-white); border: var(--border-width) solid var(--border-color); border-radius: 12px; padding: 24px; box-shadow: var(--shadow-brutal); position: relative; overflow: hidden;">
        <div style="position: absolute; top: 0; left: 0; width: 100%; height: 4px; background: var(--color-pastel-blue);"></div>
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px;">
            <div style="width: 48px; height: 48px; background: var(--color-pastel-blue); border: 2px solid var(--border-color); border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">
                📦
            </div>
            <span style="font-size: 0.75rem; font-weight: 600; text-transform: uppercase; color: #888; letter-spacing: 0.5px;">Total</span>
        </div>
        <div style="font-family: var(--font-heading); font-size: 2.5rem; font-weight: 700; margin-bottom: 4px;">
            {{ $stats['total_products'] }}
        </div>
        <div style="font-size: 0.9rem; color: #666;">Total Products</div>
    </div>

    <!-- Active Products -->
    <div style="background: var(--color-white); border: var(--border-width) solid var(--border-color); border-radius: 12px; padding: 24px; box-shadow: var(--shadow-brutal); position: relative; overflow: hidden;">
        <div style="position: absolute; top: 0; left: 0; width: 100%; height: 4px; background: var(--color-pastel-green);"></div>
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px;">
            <div style="width: 48px; height: 48px; background: var(--color-pastel-green); border: 2px solid var(--border-color); border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">
                ✓
            </div>
            <span style="font-size: 0.75rem; font-weight: 600; text-transform: uppercase; color: #888; letter-spacing: 0.5px;">Active</span>
        </div>
        <div style="font-family: var(--font-heading); font-size: 2.5rem; font-weight: 700; margin-bottom: 4px;">
            {{ $stats['active_products'] }}
        </div>
        <div style="font-size: 0.9rem; color: #666;">Active Products</div>
    </div>

    <!-- Inactive Products -->
    <div style="background: var(--color-white); border: var(--border-width) solid var(--border-color); border-radius: 12px; padding: 24px; box-shadow: var(--shadow-brutal); position: relative; overflow: hidden;">
        <div style="position: absolute; top: 0; left: 0; width: 100%; height: 4px; background: var(--color-pastel-yellow);"></div>
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px;">
            <div style="width: 48px; height: 48px; background: var(--color-pastel-yellow); border: 2px solid var(--border-color); border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">
                ⏸
            </div>
            <span style="font-size: 0.75rem; font-weight: 600; text-transform: uppercase; color: #888; letter-spacing: 0.5px;">Inactive</span>
        </div>
        <div style="font-family: var(--font-heading); font-size: 2.5rem; font-weight: 700; margin-bottom: 4px;">
            {{ $stats['inactive_products'] }}
        </div>
        <div style="font-size: 0.9rem; color: #666;">Inactive Products</div>
    </div>

    <!-- Deleted Products -->
    <div style="background: var(--color-white); border: var(--border-width) solid var(--border-color); border-radius: 12px; padding: 24px; box-shadow: var(--shadow-brutal); position: relative; overflow: hidden;">
        <div style="position: absolute; top: 0; left: 0; width: 100%; height: 4px; background: #FFB5B5;"></div>
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px;">
            <div style="width: 48px; height: 48px; background: #FFB5B5; border: 2px solid var(--border-color); border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">
                🗑️
            </div>
            <span style="font-size: 0.75rem; font-weight: 600; text-transform: uppercase; color: #888; letter-spacing: 0.5px;">Deleted</span>
        </div>
        <div style="font-family: var(--font-heading); font-size: 2.5rem; font-weight: 700; margin-bottom: 4px;">
            {{ $stats['deleted_products'] }}
        </div>
        <div style="font-size: 0.9rem; color: #666;">Deleted Products</div>
    </div>
</div>

<!-- Quick Actions -->
<div style="background: var(--color-white); border: var(--border-width) solid var(--border-color); border-radius: 12px; padding: 28px; box-shadow: var(--shadow-brutal); margin-bottom: 32px;">
    <h3 style="font-family: var(--font-heading); font-size: 1.3rem; font-weight: 700; margin-bottom: 20px;">Quick Actions</h3>
    <div style="display: flex; gap: 12px; flex-wrap: wrap;">
        <a href="{{ route('admin.products.create') }}" style="display: inline-flex; align-items: center; gap: 8px; padding: 12px 24px; background: var(--color-coral); color: white; border: var(--border-width) solid var(--border-color); border-radius: 10px; font-family: var(--font-heading); font-weight: 600; text-decoration: none; box-shadow: 3px 3px 0px var(--color-black); transition: all 0.2s ease;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <line x1="12" y1="5" x2="12" y2="19"/>
                <line x1="5" y1="12" x2="19" y2="12"/>
            </svg>
            Add New Product
        </a>
        <a href="{{ route('admin.products.index') }}" style="display: inline-flex; align-items: center; gap: 8px; padding: 12px 24px; background: var(--color-white); color: var(--color-black); border: var(--border-width) solid var(--border-color); border-radius: 10px; font-family: var(--font-heading); font-weight: 600; text-decoration: none; box-shadow: 3px 3px 0px var(--color-black); transition: all 0.2s ease;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/>
                <line x1="3" y1="6" x2="21" y2="6"/>
                <path d="M16 10a4 4 0 01-8 0"/>
            </svg>
            View All Products
        </a>
        <a href="{{ route('home') }}" target="_blank" style="display: inline-flex; align-items: center; gap: 8px; padding: 12px 24px; background: var(--color-pastel-blue); color: var(--color-black); border: var(--border-width) solid var(--border-color); border-radius: 10px; font-family: var(--font-heading); font-weight: 600; text-decoration: none; box-shadow: 3px 3px 0px var(--color-black); transition: all 0.2s ease;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/>
                <polyline points="15 3 21 3 21 9"/>
                <line x1="10" y1="14" x2="21" y2="3"/>
            </svg>
            View Landing Page
        </a>
    </div>
</div>

<!-- Recent Products -->
<div style="background: var(--color-white); border: var(--border-width) solid var(--border-color); border-radius: 12px; padding: 28px; box-shadow: var(--shadow-brutal);">
    <h3 style="font-family: var(--font-heading); font-size: 1.3rem; font-weight: 700; margin-bottom: 20px;">Recent Products</h3>
    
    @if($recentProducts->count() > 0)
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="border-bottom: 2px solid var(--border-color);">
                        <th style="text-align: left; padding: 12px 8px; font-family: var(--font-heading); font-weight: 700; font-size: 0.85rem; text-transform: uppercase; color: #666;">Product</th>
                        <th style="text-align: left; padding: 12px 8px; font-family: var(--font-heading); font-weight: 700; font-size: 0.85rem; text-transform: uppercase; color: #666;">Category</th>
                        <th style="text-align: left; padding: 12px 8px; font-family: var(--font-heading); font-weight: 700; font-size: 0.85rem; text-transform: uppercase; color: #666;">Price</th>
                        <th style="text-align: left; padding: 12px 8px; font-family: var(--font-heading); font-weight: 700; font-size: 0.85rem; text-transform: uppercase; color: #666;">Status</th>
                        <th style="text-align: right; padding: 12px 8px; font-family: var(--font-heading); font-weight: 700; font-size: 0.85rem; text-transform: uppercase; color: #666;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentProducts as $product)
                        <tr style="border-bottom: 1px solid #eee;">
                            <td style="padding: 16px 8px;">
                                <div style="display: flex; align-items: center; gap: 12px;">
                                    <span style="font-size: 2rem;">{{ $product->emoji }}</span>
                                    <div>
                                        <div style="font-weight: 600; font-size: 0.95rem;">{{ $product->name }}</div>
                                        <div style="font-size: 0.8rem; color: #888;">{{ Str::limit($product->short_description, 40) }}</div>
                                    </div>
                                </div>
                            </td>
                            <td style="padding: 16px 8px;">
                                <span style="display: inline-block; padding: 4px 12px; background: var(--color-pastel-{{ $product->category === 'streaming' ? 'blue' : ($product->category === 'tools' ? 'purple' : 'green') }}); border: 2px solid var(--border-color); border-radius: 50px; font-size: 0.75rem; font-weight: 600; text-transform: uppercase;">
                                    {{ $product->category }}
                                </span>
                            </td>
                            <td style="padding: 16px 8px;">
                                @if($product->variants->count() > 0)
                                    <span style="font-weight: 600; color: var(--color-coral);">
                                        Rp {{ number_format($product->variants->min('price'), 0, ',', '.') }}
                                    </span>
                                    <span style="font-size: 0.8rem; color: #888;">/ mulai</span>
                                @else
                                    <span style="color: #888; font-size: 0.9rem;">No variants</span>
                                @endif
                            </td>
                            <td style="padding: 16px 8px;">
                                @if($product->status)
                                    <span style="display: inline-flex; align-items: center; gap: 6px; padding: 4px 12px; background: var(--color-pastel-green); border: 2px solid var(--border-color); border-radius: 50px; font-size: 0.75rem; font-weight: 600;">
                                        <span style="width: 6px; height: 6px; background: #4CAF50; border-radius: 50%;"></span>
                                        Active
                                    </span>
                                @else
                                    <span style="display: inline-flex; align-items: center; gap: 6px; padding: 4px 12px; background: #FFB5B5; border: 2px solid var(--border-color); border-radius: 50px; font-size: 0.75rem; font-weight: 600;">
                                        <span style="width: 6px; height: 6px; background: #F44336; border-radius: 50%;"></span>
                                        Inactive
                                    </span>
                                @endif
                            </td>
                            <td style="padding: 16px 8px; text-align: right;">
                                <a href="{{ route('admin.products.edit', $product->id) }}" style="display: inline-flex; align-items: center; gap: 6px; padding: 6px 14px; background: var(--color-white); border: 2px solid var(--border-color); border-radius: 8px; font-size: 0.85rem; font-weight: 600; text-decoration: none; color: var(--color-black); box-shadow: 2px 2px 0px var(--color-black); transition: all 0.2s ease;">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                    </svg>
                                    Edit
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div style="text-align: center; padding: 40px 20px; color: #888;">
            <div style="font-size: 3rem; margin-bottom: 16px;">📦</div>
            <p style="font-size: 1.1rem; margin-bottom: 8px;">No products yet</p>
            <p style="font-size: 0.9rem;">Start by adding your first product!</p>
        </div>
    @endif
</div>

@push('styles')
<style>
    @media (max-width: 768px) {
        table {
            font-size: 0.85rem;
        }
        
        table th,
        table td {
            padding: 10px 6px !important;
        }
    }
</style>
@endpush
@endsection
