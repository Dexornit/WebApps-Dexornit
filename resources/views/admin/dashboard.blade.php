@extends('admin.layouts.admin')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')

{{-- ─── Welcome ─────────────────────────────────────────────────── --}}
<div class="dash-welcome">
    <div>
        <h2 class="dash-welcome__title">Welcome back, {{ Auth::user()->name }}! 👋</h2>
        <p class="dash-welcome__sub">Here's what's happening with your store today.</p>
    </div>
</div>

{{-- ─── Stats Cards ──────────────────────────────────────────────── --}}
<div class="dash-stats">
    @php
        $cards = [
            [
                'label' => 'Total Products',
                'value' => $stats['total_products'],
                'color' => 'var(--color-pastel-blue)',
                'icon' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 01-8 0"/></svg>'
            ],
            [
                'label' => 'Active Products',
                'value' => $stats['active_products'],
                'color' => 'var(--color-pastel-green)',
                'icon' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>'
            ],
            [
                'label' => 'Inactive Products',
                'value' => $stats['inactive_products'],
                'color' => 'var(--color-pastel-yellow)',
                'icon' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="8" y1="12" x2="16" y2="12"/></svg>'
            ],
            [
                'label' => 'Deleted Products',
                'value' => $stats['deleted_products'],
                'color' => '#FFB5B5',
                'icon' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>'
            ],
        ];
    @endphp
    @foreach($cards as $card)
    <div class="stat-card">
        <div class="stat-card__bar" style="background: {{ $card['color'] }};"></div>
        <div class="stat-card__head">
            <div class="stat-card__icon" style="background: {{ $card['color'] }};">{!! $card['icon'] !!}</div>
        </div>
        <div class="stat-card__value">{{ $card['value'] }}</div>
        <div class="stat-card__label">{{ $card['label'] }}</div>
    </div>
    @endforeach
</div>

{{-- ─── Quick Actions ────────────────────────────────────────────── --}}
<div class="dash-card" style="margin-bottom: 24px;">
    <h3 class="dash-card__title">Quick Actions</h3>
    <div class="dash-actions">
        <a href="{{ route('admin.products.create') }}" class="dash-btn dash-btn--coral">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Add New Product
        </a>
        <a href="{{ route('admin.products.index') }}" class="dash-btn dash-btn--white">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 01-8 0"/></svg>
            View All Products
        </a>
        <a href="{{ route('admin.categories.index') }}" class="dash-btn dash-btn--blue">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/></svg>
            Manage Categories
        </a>
        <a href="{{ route('home') }}" target="_blank" class="dash-btn dash-btn--purple">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
            View Landing Page
        </a>
    </div>
</div>

{{-- ─── Recent Products ──────────────────────────────────────────── --}}
<div class="dash-card">
    <div class="dash-card__header">
        <h3 class="dash-card__title" style="margin-bottom:0;">Recent Products</h3>
        <a href="{{ route('admin.products.index') }}" class="dash-link">View all →</a>
    </div>

    @if($recentProducts->count() > 0)

        {{-- TABLE — visible on md+ --}}
        <div class="dash-table-wrap">
            <table class="dash-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Product</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Status</th>
                        <th style="text-align:right;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentProducts as $product)
                    <tr>
                        <td class="text-muted" style="font-size:.8rem;">{{ $product->id }}</td>
                        <td>
                            <div style="display:flex;align-items:center;gap:10px;">
                                <span style="font-size:1.8rem;line-height:1;">{{ $product->emoji }}</span>
                                <div>
                                    <div style="font-weight:600;font-size:.9rem;">{{ $product->name }}</div>
                                    <div class="text-muted" style="font-size:.75rem;">{{ Str::limit($product->short_description, 35) }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            @if($product->category)
                                <span class="badge" style="background:{{ $product->category->color ?? 'var(--color-pastel-blue)' }};">
                                    {{ $product->category->icon }} {{ $product->category->name }}
                                </span>
                            @else
                                <span class="badge" style="background:#eee;">No Category</span>
                            @endif
                        </td>
                        <td>
                            @if($product->variants->count() > 0)
                                <span style="font-weight:700;color:var(--color-coral);">Rp {{ number_format($product->variants->min('price'), 0, ',', '.') }}</span>
                                <span class="text-muted" style="font-size:.75rem;display:block;">/ mulai</span>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td>
                            @if($product->status)
                                <span class="badge badge--green"><span class="dot dot--green"></span>Active</span>
                            @else
                                <span class="badge badge--red"><span class="dot dot--red"></span>Inactive</span>
                            @endif
                        </td>
                        <td style="text-align:right;">
                            <a href="{{ route('admin.products.edit', $product->id) }}" class="dash-edit-btn">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                Edit
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- CARDS — visible on mobile only --}}
        <div class="dash-product-cards">
            @foreach($recentProducts as $product)
            <div class="product-card-m">
                <div class="product-card-m__top">
                    <div style="display:flex;align-items:center;gap:10px;">
                        <span style="font-size:2rem;">{{ $product->emoji }}</span>
                        <div>
                            <div style="font-weight:700;font-size:.9rem;">{{ $product->name }}</div>
                            <div class="text-muted" style="font-size:.75rem;">#{{ $product->id }}</div>
                        </div>
                    </div>
                    @if($product->status)
                        <span class="badge badge--green"><span class="dot dot--green"></span>Active</span>
                    @else
                        <span class="badge badge--red"><span class="dot dot--red"></span>Inactive</span>
                    @endif
                </div>
                <div class="product-card-m__meta">
                    <div>
                        <span class="text-muted" style="font-size:.75rem;">Kategori</span>
                        @if($product->category)
                            <span class="badge" style="background:{{ $product->category->color ?? 'var(--color-pastel-blue)' }};display:block;margin-top:2px;">
                                {{ $product->category->icon }} {{ $product->category->name }}
                            </span>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </div>
                    <div>
                        <span class="text-muted" style="font-size:.75rem;">Harga mulai</span>
                        @if($product->variants->count() > 0)
                            <div style="font-weight:700;color:var(--color-coral);font-size:.9rem;">Rp {{ number_format($product->variants->min('price'), 0, ',', '.') }}</div>
                        @else
                            <div class="text-muted">No variants</div>
                        @endif
                    </div>
                    <div style="display:flex;align-items:flex-end;">
                        <a href="{{ route('admin.products.edit', $product->id) }}" class="dash-edit-btn">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                            Edit
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

    @else
        <div style="text-align:center;padding:40px 20px;color:#888;">
            <div style="font-size:3rem;margin-bottom:16px;">📦</div>
            <p style="font-size:1.1rem;margin-bottom:8px;">No products yet</p>
            <p style="font-size:.9rem;">Start by adding your first product!</p>
        </div>
    @endif
</div>

@push('styles')
<style>
/* ─── Dashboard Layout ───────────────────────────────────────── */
.dash-welcome         { margin-bottom: 28px; }
.dash-welcome__title  { font-family: var(--font-heading); font-size: 1.7rem; font-weight: 700; margin-bottom: 6px; }
.dash-welcome__sub    { color: #666; font-size: .95rem; }

/* Stats */
.dash-stats {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 16px;
    margin-bottom: 28px;
}
.stat-card {
    background: var(--color-white);
    border: var(--border-width) solid var(--border-color);
    border-radius: 12px;
    padding: 20px;
    box-shadow: var(--shadow-brutal);
    position: relative;
    overflow: hidden;
}
.stat-card__bar   { position: absolute; top: 0; left: 0; width: 100%; height: 4px; }
.stat-card__head  { margin-bottom: 10px; }
.stat-card__icon  { width: 44px; height: 44px; border: 2px solid var(--border-color); border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1.4rem; }
.stat-card__value { font-family: var(--font-heading); font-size: 2.2rem; font-weight: 700; margin-bottom: 2px; line-height: 1; }
.stat-card__label { font-size: .85rem; color: #666; }

/* Card container */
.dash-card         { background: var(--color-white); border: var(--border-width) solid var(--border-color); border-radius: 12px; padding: 24px; box-shadow: var(--shadow-brutal); }
.dash-card__header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
.dash-card__title  { font-family: var(--font-heading); font-size: 1.2rem; font-weight: 700; }

/* Actions */
.dash-actions { display: flex; gap: 10px; flex-wrap: wrap; }
.dash-btn     { display: inline-flex; align-items: center; gap: 8px; padding: 10px 20px; border: var(--border-width) solid var(--border-color); border-radius: 10px; font-family: var(--font-heading); font-weight: 600; text-decoration: none; box-shadow: 3px 3px 0 var(--color-black); transition: all .2s; font-size: .9rem; white-space: nowrap; }
.dash-btn:hover { transform: translate(1px,1px); box-shadow: 2px 2px 0 var(--color-black); }
.dash-btn--coral  { background: var(--color-coral);          color: #fff; }
.dash-btn--white  { background: var(--color-white);          color: var(--color-black); }
.dash-btn--blue   { background: var(--color-pastel-blue);    color: var(--color-black); }
.dash-btn--purple { background: var(--color-pastel-purple);  color: var(--color-black); }

/* Table */
.dash-table-wrap   { overflow-x: auto; }
.dash-table        { width: 100%; border-collapse: collapse; font-size: .88rem; }
.dash-table th     { text-align: left; padding: 10px 10px; font-family: var(--font-heading); font-weight: 700; font-size: .78rem; text-transform: uppercase; color: #777; border-bottom: 2px solid var(--border-color); }
.dash-table td     { padding: 14px 10px; border-bottom: 1px solid #eee; vertical-align: middle; }
.dash-table tr:last-child td { border-bottom: none; }

/* Badges */
.badge         { display: inline-flex; align-items: center; gap: 5px; padding: 4px 10px; border: 2px solid var(--border-color); border-radius: 50px; font-size: .72rem; font-weight: 700; text-transform: uppercase; }
.badge--green  { background: var(--color-pastel-green); }
.badge--red    { background: #FFB5B5; }
.dot           { width: 6px; height: 6px; border-radius: 50%; flex-shrink: 0; }
.dot--green    { background: #4caf50; }
.dot--red      { background: #f44336; }

/* Edit button */
.dash-edit-btn { display: inline-flex; align-items: center; gap: 5px; padding: 6px 12px; background: var(--color-white); border: 2px solid var(--border-color); border-radius: 7px; font-size: .82rem; font-weight: 600; text-decoration: none; color: var(--color-black); box-shadow: 2px 2px 0 var(--color-black); transition: all .2s; white-space: nowrap; }
.dash-edit-btn:hover { transform: translate(1px,1px); box-shadow: 1px 1px 0 var(--color-black); }

/* Misc */
.dash-link    { font-size: .85rem; font-weight: 600; color: var(--color-coral); text-decoration: none; }
.text-muted   { color: #888; }

/* Mobile product cards (hidden on desktop) */
.dash-product-cards { display: none; }
.product-card-m { border: 2px solid var(--border-color); border-radius: 10px; padding: 14px; margin-bottom: 12px; }
.product-card-m:last-child { margin-bottom: 0; }
.product-card-m__top  { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 12px; }
.product-card-m__meta { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 10px; }

/* ─── Responsive Breakpoints ─────────────────────────────────── */
@media (max-width: 1100px) {
    .dash-stats { grid-template-columns: repeat(2, 1fr); }
}

@media (max-width: 768px) {
    .dash-welcome__title { font-size: 1.3rem; }
    .dash-stats  { grid-template-columns: repeat(2, 1fr); gap: 12px; }
    .stat-card   { padding: 16px; }
    .stat-card__value { font-size: 1.8rem; }

    /* Quick actions: 2 per row */
    .dash-btn    { flex: 1 1 calc(50% - 5px); justify-content: center; font-size: .82rem; padding: 10px 12px; }

    /* Hide table, show cards */
    .dash-table-wrap    { display: none; }
    .dash-product-cards { display: block; }
    .product-card-m__meta { grid-template-columns: 1fr 1fr; }
    .product-card-m__meta > div:last-child { grid-column: 1 / -1; justify-self: start; }
}

@media (max-width: 480px) {
    .dash-stats  { grid-template-columns: 1fr 1fr; gap: 10px; }
    .stat-card__value { font-size: 1.6rem; }
    .stat-card__icon  { width: 36px; height: 36px; font-size: 1.1rem; }
    .dash-btn    { flex: 1 1 100%; }
    .product-card-m__meta { grid-template-columns: 1fr 1fr; }
}
</style>
@endpush

@endsection
