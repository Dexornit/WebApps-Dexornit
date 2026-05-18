@extends('layouts.app')

@section('title', 'Tools — Dexornit Store')

@section('content')
<style>
.tools-hero {
    background: var(--color-cream);
    padding: 120px 0 48px; /* top padding untuk fixed navbar */
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
    font-family: var(--font-heading); font-size: clamp(2rem,5vw,3.2rem);
    font-weight: 800; line-height: 1.15; margin-bottom: 16px;
}
.tools-hero__title span { color: var(--color-coral); }
.tools-hero__desc { font-size: 1rem; color: #555; max-width: 540px; line-height: 1.7; }

.tools-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px,1fr));
    gap: 24px;
    padding: 56px 0;
}
.tool-card {
    background: var(--color-white);
    border: 3px solid var(--color-black);
    border-radius: 16px; padding: 28px;
    box-shadow: 6px 6px 0 var(--color-black);
    transition: transform .2s, box-shadow .2s;
    text-decoration: none; color: var(--color-black);
    display: flex; flex-direction: column; position: relative; overflow: hidden;
}
.tool-card:not(.tool-card--disabled):hover {
    transform: translate(-3px,-3px); box-shadow: 9px 9px 0 var(--color-black);
}
.tool-card--disabled { opacity: .5; cursor: not-allowed; }

.tool-card__badge {
    position: absolute; top: 16px; right: 16px;
    padding: 3px 10px; border-radius: 50px; font-size: 0.7rem;
    font-weight: 700; text-transform: uppercase; letter-spacing: .05em;
    border: 2px solid var(--color-black);
}
.badge--new   { background: var(--color-pastel-green); }
.badge--soon  { background: #eee; color: #999; }
.badge--ext   { background: var(--color-pastel-blue); }

.tool-card__icon {
    width: 56px; height: 56px; border-radius: 14px;
    border: 2px solid var(--color-black);
    display: flex; align-items: center; justify-content: center;
    margin-bottom: 20px; flex-shrink: 0;
}
.tool-card__title { font-family: var(--font-heading); font-size: 1.2rem; font-weight: 700; margin-bottom: 8px; }
.tool-card__desc  { font-size: 0.9rem; color: #555; line-height: 1.6; margin-bottom: 20px; flex: 1; }
.tool-card__cta   { display: inline-flex; align-items: center; gap: 6px; font-family: var(--font-heading); font-weight: 700; font-size: 0.9rem; color: var(--color-coral); }
.tool-card__cta svg { transition: transform .2s; }
.tool-card:not(.tool-card--disabled):hover .tool-card__cta svg { transform: translateX(4px); }
.tool-card--disabled .tool-card__cta { color: #aaa; }
</style>

{{-- Hero --}}
<div class="tools-hero">
    <div class="container">
        <div class="tools-hero__badge">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/></svg>
            Tools by Dexornit
        </div>
        <h1 class="tools-hero__title">
            Toolkit Digital<br><span>Gratis untuk Semua</span>
        </h1>
        <p class="tools-hero__desc">
            Kumpulan tools berguna untuk kebutuhan digital sehari-hari. Ringan, cepat, dan berjalan langsung di browser kamu.
        </p>
    </div>
</div>

{{-- Tools Grid --}}
<div style="background: var(--color-cream);">
<div class="container">
<div class="tools-grid">

    {{-- A2F Authenticator --}}
    <a href="{{ route('tools.a2f') }}" class="tool-card">
        <span class="tool-card__badge badge--new">Baru</span>
        <div class="tool-card__icon" style="background:#e8f5e9;">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color:#2e7d32;">
                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
            </svg>
        </div>
        <div class="tool-card__title">A2F Authenticator</div>
        <p class="tool-card__desc">Generator kode TOTP (2FA) langsung di browser. Tidak perlu login, tidak ada data yang disimpan.</p>
        <span class="tool-card__cta">
            Buka Tool
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
        </span>
    </a>

    {{-- TMail --}}
    <a href="https://mail.wanseven.com" target="_blank" rel="noopener" class="tool-card">
        <span class="tool-card__badge badge--ext">↗ Subdomain</span>
        <div class="tool-card__icon" style="background:#e3f2fd;">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color:#1565c0;">
                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                <polyline points="22,6 12,13 2,6"/>
            </svg>
        </div>
        <div class="tool-card__title">TMail</div>
        <p class="tool-card__desc">Email temporer sekali pakai. Berguna untuk registrasi tanpa menggunakan email utamamu.</p>
        <span class="tool-card__cta">
            Buka TMail
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
        </span>
    </a>

    {{-- Password Generator (coming soon) --}}
    <div class="tool-card tool-card--disabled">
        <span class="tool-card__badge badge--soon">Segera</span>
        <div class="tool-card__icon" style="background:#f3e5f5;">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color:#6a1b9a;">
                <path d="M21 2l-2 2m-7.61 7.61a5.5 5.5 0 1 1-7.778 7.778 5.5 5.5 0 0 1 7.777-7.777zm0 0L15.5 7.5m0 0l3 3L22 7l-3-3m-3.5 3.5L19 4"/>
            </svg>
        </div>
        <div class="tool-card__title">Password Generator</div>
        <p class="tool-card__desc">Buat password kuat dan acak sesuai kebutuhan. Atur panjang, karakter, dan kompleksitas.</p>
        <span class="tool-card__cta">Segera Hadir</span>
    </div>

    {{-- URL Shortener (coming soon) --}}
    <div class="tool-card tool-card--disabled">
        <span class="tool-card__badge badge--soon">Segera</span>
        <div class="tool-card__icon" style="background:#fff3e0;">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color:#e65100;">
                <path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/>
                <path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/>
            </svg>
        </div>
        <div class="tool-card__title">URL Shortener</div>
        <p class="tool-card__desc">Persingkat URL panjang menjadi link pendek yang mudah dibagikan.</p>
        <span class="tool-card__cta">Segera Hadir</span>
    </div>

</div>
</div>
</div>

@endsection
