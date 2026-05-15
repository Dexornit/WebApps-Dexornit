<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Dexornit Store — Solusi digital terpercaya untuk kebutuhan akun premium, tools, dan layanan digital Anda. Cepat, aman, dan terjangkau.">
    <meta name="keywords" content="dexornit, dexornit store, digital store, akun premium, tools digital, layanan digital">
    <meta name="author" content="Dexornit Store">
    <title>@yield('title', 'Dexornit Store — Solusi Digital Terpercaya')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="icon" type="image/png" href="{{ asset('assets/images/logo-square.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>

    <!-- ==================== NAVBAR ==================== -->
    <nav class="navbar" id="navbar">
        <div class="container navbar__inner">
            <a href="{{ route('home') }}" class="navbar__logo" id="nav-logo">
                <img src="{{ asset('assets/images/logo-horizontal.png') }}" alt="Dexornit Store" class="navbar__logo-img">
            </a>

            <ul class="navbar__menu" id="nav-menu">
                <li><a href="{{ route('home') }}#home" class="navbar__link active" data-section="home">Beranda</a></li>
                <li><a href="{{ route('home') }}#about" class="navbar__link" data-section="about">Tentang</a></li>
                <li><a href="{{ route('home') }}#services" class="navbar__link" data-section="services">Layanan</a></li>
                <li><a href="{{ route('home') }}#products" class="navbar__link" data-section="products">Produk</a></li>
                <li><a href="{{ route('home') }}#testimonials" class="navbar__link" data-section="testimonials">Testimoni</a></li>
                <li><a href="{{ route('home') }}#contact" class="navbar__link" data-section="contact">Kontak</a></li>
                @auth
                    <li><a href="{{ route('admin.dashboard') }}" class="navbar__link" style="background: var(--color-coral); color: var(--color-white); border-color: var(--color-black); box-shadow: 2px 2px 0px var(--color-black);">Dashboard</a></li>
                @else
                    <li><a href="{{ route('login') }}" class="navbar__link" style="background: var(--color-pastel-purple); border-color: var(--color-black); box-shadow: 2px 2px 0px var(--color-black);">Login</a></li>
                @endauth
            </ul>

            <button class="navbar__toggle" id="nav-toggle" aria-label="Toggle navigation menu">
                <span class="navbar__toggle-bar"></span>
                <span class="navbar__toggle-bar"></span>
                <span class="navbar__toggle-bar"></span>
            </button>
        </div>
    </nav>

    <!-- ==================== MAIN CONTENT ==================== -->
    @yield('content')

    <!-- ==================== FOOTER ==================== -->
    <footer class="footer" id="footer">
        <div class="container">
            <div class="footer__grid">
                <div class="footer__brand">
                    <a href="{{ route('home') }}" class="navbar__logo">
                        <img src="{{ asset('assets/images/logo-horizontal.png') }}" alt="Dexornit Store" class="navbar__logo-img footer__logo-img">
                    </a>
                    <p class="footer__brand-desc">Solusi digital terpercaya untuk kebutuhan akun premium, tools, dan layanan digital Anda.</p>
                    <div class="footer__socials">
                        @if(isset($socialMedia) && $socialMedia->count() > 0)
                            @foreach($socialMedia as $social)
                                <a href="{{ $social->link }}" class="footer__social" aria-label="Social Media" target="_blank" rel="noopener noreferrer">
                                    {!! $social->icon !!}
                                </a>
                            @endforeach
                        @else
                            <a href="#" class="footer__social" aria-label="Instagram">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/></svg>
                            </a>
                            <a href="#" class="footer__social" aria-label="Twitter">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M23 3a10.9 10.9 0 0 1-3.14 1.53 4.48 4.48 0 0 0-7.86 3v1A10.66 10.66 0 0 1 3 4s-4 9 5 13a11.64 11.64 0 0 1-7 2c9 5 20 0 20-11.5a4.5 4.5 0 0 0-.08-.83A7.72 7.72 0 0 0 23 3z"/></svg>
                            </a>
                            <a href="#" class="footer__social" aria-label="Telegram">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
                            </a>
                            <a href="#" class="footer__social" aria-label="TikTok">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 12a4 4 0 1 0 4 4V4a5 5 0 0 0 5 5"/></svg>
                            </a>
                        @endif
                    </div>
                </div>
                <div class="footer__links">
                    <h4 class="footer__links-title">Menu</h4>
                    <ul>
                        <li><a href="{{ route('home') }}#home">Beranda</a></li>
                        <li><a href="{{ route('home') }}#about">Tentang</a></li>
                        <li><a href="{{ route('home') }}#services">Layanan</a></li>
                        <li><a href="{{ route('home') }}#products">Produk</a></li>
                    </ul>
                </div>
                <div class="footer__links">
                    <h4 class="footer__links-title">Layanan</h4>
                    <ul>
                        <li><a href="#">Akun Premium</a></li>
                        <li><a href="#">Tools & Software</a></li>
                        <li><a href="#">Top-up Game</a></li>
                        <li><a href="#">Voucher Digital</a></li>
                    </ul>
                </div>
                <div class="footer__links">
                    <h4 class="footer__links-title">Bantuan</h4>
                    <ul>
                        <li><a href="#">FAQ</a></li>
                        <li><a href="#">Kebijakan Privasi</a></li>
                        <li><a href="#">Syarat & Ketentuan</a></li>
                        <li><a href="{{ route('home') }}#contact">Hubungi Kami</a></li>
                    </ul>
                </div>
            </div>
            <div class="footer__bottom">
                <p>&copy; 2026 Dexornit Store. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Back to Top Button -->
    <button class="back-to-top" id="back-to-top" aria-label="Kembali ke atas">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M18 15l-6-6-6 6"/></svg>
    </button>

</body>
</html>
