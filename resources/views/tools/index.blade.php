@extends('layouts.app')

@section('title', 'Tools — Dexornit Store')

@section('content')
<style>
    .tools-hero {
        background: var(--color-cream);
        padding: 80px 0 48px;
        border-bottom: 3px solid var(--color-black);
    }
    .tools-hero__badge {
        display: inline-flex; align-items: center; gap: 8px;
        padding: 6px 16px; background: var(--color-pastel-yellow);
        border: 2px solid var(--color-black); border-radius: 50px;
        font-size: 0.8rem; font-weight: 700; text-transform: uppercase;
        letter-spacing: .06em; margin-bottom: 20px;
    }
    .tools-hero__title {
        font-family: var(--font-heading); font-size: clamp(2rem, 5vw, 3.2rem);
        font-weight: 800; line-height: 1.15; margin-bottom: 16px;
        color: var(--color-black);
    }
    .tools-hero__title span { color: var(--color-coral); }
    .tools-hero__desc { font-size: 1.05rem; color: #555; max-width: 540px; line-height: 1.7; }

    .tools-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 24px;
        padding: 56px 0;
    }
    .tool-card {
        background: var(--color-white);
        border: 3px solid var(--color-black);
        border-radius: 16px;
        padding: 28px;
        box-shadow: 6px 6px 0 var(--color-black);
        transition: transform .2s, box-shadow .2s;
        text-decoration: none;
        color: var(--color-black);
        display: flex; flex-direction: column;
        position: relative; overflow: hidden;
    }
    .tool-card:hover {
        transform: translate(-3px, -3px);
        box-shadow: 9px 9px 0 var(--color-black);
    }
    .tool-card--disabled { opacity: .55; cursor: not-allowed; }
    .tool-card--disabled:hover { transform: none; box-shadow: 6px 6px 0 var(--color-black); }

    .tool-card__badge {
        position: absolute; top: 16px; right: 16px;
        padding: 3px 10px; border-radius: 50px;
        font-size: 0.7rem; font-weight: 700; text-transform: uppercase;
        letter-spacing: .05em; border: 2px solid var(--color-black);
    }
    .tool-card__badge--new { background: var(--color-pastel-green); }
    .tool-card__badge--soon { background: #eee; color: #888; }
    .tool-card__badge--external { background: var(--color-pastel-blue); }

    .tool-card__icon {
        width: 56px; height: 56px; border-radius: 14px;
        border: 2px solid var(--color-black);
        display: flex; align-items: center; justify-content: center;
        font-size: 1.8rem; margin-bottom: 20px;
    }
    .tool-card__title {
        font-family: var(--font-heading); font-size: 1.25rem;
        font-weight: 700; margin-bottom: 8px;
    }
    .tool-card__desc { font-size: 0.9rem; color: #555; line-height: 1.6; margin-bottom: 20px; flex: 1; }
    .tool-card__cta {
        display: inline-flex; align-items: center; gap: 6px;
        font-family: var(--font-heading); font-weight: 700;
        font-size: 0.9rem; color: var(--color-coral);
    }
    .tool-card__cta svg { transition: transform .2s; }
    .tool-card:hover .tool-card__cta svg { transform: translateX(4px); }
</style>

{{-- Hero --}}
<div class="tools-hero">
    <div class="container">
        <div class="tools-hero__badge">⚡ Tools by Dexornit</div>
        <h1 class="tools-hero__title">
            Toolkit Digital<br><span>Gratis untuk Semua</span>
        </h1>
        <p class="tools-hero__desc">
            Kumpulan tools berguna untuk kebutuhan digital sehari-hari. Ringan, cepat, dan berjalan langsung di browser kamu.
        </p>
    </div>
</div>

{{-- Tools Grid --}}
<div class="container">
    <div class="tools-grid">

        {{-- A2F Authenticator --}}
        <a href="{{ route('tools.a2f') }}" class="tool-card">
            <span class="tool-card__badge tool-card__badge--new">Baru</span>
            <div class="tool-card__icon" style="background: #e8f5e9;">🔐</div>
            <div class="tool-card__title">A2F Authenticator</div>
            <p class="tool-card__desc">
                Generator kode TOTP (2FA) langsung di browser. Tidak perlu install app, data tersimpan di perangkatmu.
            </p>
            <span class="tool-card__cta">
                Buka Tool
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
            </span>
        </a>

        {{-- TMail --}}
        <a href="https://mail.wanseven.com" target="_blank" rel="noopener" class="tool-card">
            <span class="tool-card__badge tool-card__badge--external">↗ External</span>
            <div class="tool-card__icon" style="background: #e3f2fd;">📧</div>
            <div class="tool-card__title">TMail</div>
            <p class="tool-card__desc">
                Email temporer sekali pakai. Berguna untuk registrasi tanpa menggunakan email utamamu.
            </p>
            <span class="tool-card__cta">
                Buka TMail
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
            </span>
        </a>

        {{-- Coming Soon --}}
        <div class="tool-card tool-card--disabled">
            <span class="tool-card__badge tool-card__badge--soon">Segera</span>
            <div class="tool-card__icon" style="background: #f3e5f5;">🔑</div>
            <div class="tool-card__title">Password Generator</div>
            <p class="tool-card__desc">
                Buat password kuat dan acak sesuai kebutuhan. Atur panjang, karakter, dan kompleksitasnya.
            </p>
            <span class="tool-card__cta" style="color:#aaa;">
                Segera Hadir
            </span>
        </div>

        <div class="tool-card tool-card--disabled">
            <span class="tool-card__badge tool-card__badge--soon">Segera</span>
            <div class="tool-card__icon" style="background: #fff3e0;">🔗</div>
            <div class="tool-card__title">URL Shortener</div>
            <p class="tool-card__desc">
                Persingkat URL panjang menjadi link pendek yang mudah dibagikan.
            </p>
            <span class="tool-card__cta" style="color:#aaa;">
                Segera Hadir
            </span>
        </div>

    </div>
</div>

@endsection
