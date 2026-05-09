@extends('layouts.app')

@section('title', $product->name . ' - Dexornit Store')

@section('content')

<section class="product-detail" style="padding: 120px 0 80px; background: var(--color-cream);">
    <div class="container">
        <div class="product-detail__grid">
            <!-- Product Images -->
            <div class="product-detail__image-wrapper">
                <div class="product-detail__image-card">
                    @if($product->images->isNotEmpty())
                        <img src="{{ asset('storage/' . $product->images->first()->image_path) }}" alt="{{ $product->name }}" class="product-detail__image">
                    @else
                        <div class="product-detail__emoji-placeholder">
                            {{ $product->emoji }}
                        </div>
                    @endif
                </div>
            </div>

            <!-- Product Info -->
            <div class="product-detail__info-wrapper">
                <div class="product-detail__info-card">
                    @if($product->category)
                        <span class="product-detail__category" style="background: {{ $product->category->color }};">
                            {{ $product->category->icon }} {{ $product->category->name }}
                        </span>
                    @endif
                    
                    <h1 class="product-detail__title">
                        @if($product->logo_path)
                            <img src="{{ asset('storage/' . $product->logo_path) }}" alt="{{ $product->name }}" style="width: 48px; height: 48px; object-fit: contain; display: inline-block; vertical-align: middle; margin-right: 12px;">
                        @else
                            {{ $product->emoji }}
                        @endif
                        {{ $product->name }}
                    </h1>
                    
                    <p class="product-detail__description">
                        {{ $product->full_description }}
                    </p>

                    @if($product->variants->isNotEmpty())
                        <h3 class="product-detail__variants-title">Pilihan Paket:</h3>
                        <div class="product-detail__variants">
                            @foreach($product->variants as $variant)
                                <div class="variant-card">
                                    <div class="variant-card__header">
                                        <h4 class="variant-card__name">{{ $variant->variant_name }}</h4>
                                        <div class="variant-card__price-wrapper">
                                            <div class="variant-card__price">
                                                Rp {{ number_format($variant->price, 0, ',', '.') }}
                                            </div>
                                            @if($variant->wholesale_price)
                                                <div class="variant-card__wholesale">
                                                    Grosir: Rp {{ number_format($variant->wholesale_price, 0, ',', '.') }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    <p class="variant-card__description">{{ $variant->description }}</p>
                                    @if($variant->stock !== null)
                                        <span class="variant-card__stock">Stok: {{ $variant->stock }}</span>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif

                    @if($product->warranty)
                        <div class="product-detail__warranty">
                            <h4>🛡️ Garansi:</h4>
                            <p>{{ $product->warranty }}</p>
                        </div>
                    @endif

                    @if($product->terms_conditions)
                        <div class="product-detail__terms">
                            <h4>📋 Syarat & Ketentuan:</h4>
                            <p>{{ $product->terms_conditions }}</p>
                        </div>
                    @endif

                    <a href="https://wa.me/6281234567890?text=Halo, saya tertarik dengan {{ $product->name }}" target="_blank" class="btn btn--primary btn--lg product-detail__cta">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                        Pesan via WhatsApp
                    </a>
                </div>
            </div>
        </div>

        <div class="product-detail__back">
            <a href="{{ route('home') }}#products" class="btn btn--secondary">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
                Kembali ke Produk
            </a>
        </div>
    </div>
</section>

@endsection
