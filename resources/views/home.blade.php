@extends('layouts.app')

@section('title', 'Dexornit Store — Solusi Digital Terpercaya')

@section('content')

<!-- Pass products data to JavaScript -->
<script>
    window.productsData = @json($productsData);
</script>

<!-- ==================== HERO SECTION ==================== -->
<section class="hero" id="home">
    <div class="container hero__inner">
        <div class="hero__content">
            <div class="hero__badge">
                <span class="hero__badge-dot"></span>
                Trusted Digital Store 🚀
            </div>
            <h1 class="hero__title">
                Solusi <span class="hero__title--highlight">Digital</span> Terpercaya untuk Semua Kebutuhanmu
            </h1>
            <p class="hero__description">
                Dexornit Store menyediakan berbagai layanan dan produk digital berkualitas tinggi dengan harga terjangkau. Dapatkan akun premium, tools, dan layanan digital lainnya dengan proses cepat dan aman.
            </p>
            <div class="hero__actions">
                <a href="#products" class="btn btn--primary" id="hero-cta">
                    Lihat Produk
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                </a>
                <a href="#about" class="btn btn--secondary" id="hero-learn-more">Pelajari Lebih</a>
            </div>
            <div class="hero__stats">
                <div class="hero__stat">
                    <span class="hero__stat-number" data-target="2500">0</span>+
                    <span class="hero__stat-label">Pelanggan</span>
                </div>
                <div class="hero__stat">
                    <span class="hero__stat-number" data-target="150">0</span>+
                    <span class="hero__stat-label">Produk</span>
                </div>
                <div class="hero__stat">
                    <span class="hero__stat-number" data-target="99">0</span>%
                    <span class="hero__stat-label">Kepuasan</span>
                </div>
            </div>
        </div>
        <div class="hero__visual">
            <div class="hero__image-wrapper">
                <img src="{{ asset('assets/images/hero-illustration.png') }}" alt="Dexornit Store Digital Products" class="hero__image" id="hero-image">
            </div>
            <div class="hero__floating hero__floating--1">⚡</div>
            <div class="hero__floating hero__floating--2">🛒</div>
            <div class="hero__floating hero__floating--3">💎</div>
        </div>
    </div>
    <div class="hero__marquee">
        <div class="hero__marquee-track">
            <span>DEXORNIT STORE</span>
            <span>★</span>
            <span>TERPERCAYA</span>
            <span>★</span>
            <span>CEPAT & AMAN</span>
            <span>★</span>
            <span>HARGA TERBAIK</span>
            <span>★</span>
            <span>DEXORNIT STORE</span>
            <span>★</span>
            <span>TERPERCAYA</span>
            <span>★</span>
            <span>CEPAT & AMAN</span>
            <span>★</span>
            <span>HARGA TERBAIK</span>
            <span>★</span>
        </div>
    </div>
</section>

<!-- ==================== ABOUT SECTION ==================== -->
<section class="about" id="about">
    <div class="container">
        <div class="section-header">
            <span class="section-tag">Tentang Kami</span>
            <h2 class="section-title">Kenapa Memilih <span class="highlight">Dexornit</span>?</h2>
            <p class="section-subtitle">Kami hadir untuk memberikan pengalaman belanja digital yang mudah, cepat, dan tentunya aman untuk setiap pelanggan.</p>
        </div>
        <div class="about__grid">
            <div class="about__card" id="about-card-1">
                <div class="about__card-icon">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/></svg>
                </div>
                <h3 class="about__card-title">Proses Instan</h3>
                <p class="about__card-desc">Pengiriman produk digital dilakukan secara otomatis dan instan setelah pembayaran dikonfirmasi.</p>
            </div>
            <div class="about__card about__card--accent" id="about-card-2">
                <div class="about__card-icon">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                </div>
                <h3 class="about__card-title">100% Aman</h3>
                <p class="about__card-desc">Semua transaksi dilindungi dan data pelanggan dijaga kerahasiaannya dengan sistem keamanan terbaik.</p>
            </div>
            <div class="about__card" id="about-card-3">
                <div class="about__card-icon">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                </div>
                <h3 class="about__card-title">Support 24/7</h3>
                <p class="about__card-desc">Tim support kami siap membantu Anda kapan saja melalui berbagai channel komunikasi.</p>
            </div>
            <div class="about__card" id="about-card-4">
                <div class="about__card-icon">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                </div>
                <h3 class="about__card-title">Harga Bersaing</h3>
                <p class="about__card-desc">Dapatkan produk digital premium dengan harga yang bersahabat dan berbagai promo menarik.</p>
            </div>
        </div>
    </div>
</section>

<!-- ==================== SERVICES SECTION ==================== -->
<section class="services" id="services">
    <div class="container">
        <div class="section-header">
            <span class="section-tag">Layanan Kami</span>
            <h2 class="section-title">Layanan <span class="highlight">Unggulan</span> Kami</h2>
            <p class="section-subtitle">Berbagai layanan digital yang siap memenuhi kebutuhan Anda dengan kualitas terbaik.</p>
        </div>
        <div class="services__grid">
            <div class="services__card" id="service-card-1">
                <div class="services__card-img">
                    <img src="{{ asset('assets/images/service-speed.png') }}" alt="Fast Delivery Service" loading="lazy">
                </div>
                <div class="services__card-content">
                    <span class="services__card-tag">Populer</span>
                    <h3 class="services__card-title">Akun Premium</h3>
                    <p class="services__card-desc">Netflix, Spotify, YouTube Premium, Disney+, dan berbagai akun streaming premium lainnya dengan garansi.</p>
                    <a href="#" class="services__card-link">
                        Selengkapnya
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                    </a>
                </div>
            </div>
            <div class="services__card" id="service-card-2">
                <div class="services__card-img">
                    <img src="{{ asset('assets/images/service-secure.png') }}" alt="Secure Digital Service" loading="lazy">
                </div>
                <div class="services__card-content">
                    <span class="services__card-tag services__card-tag--pink">Terbaru</span>
                    <h3 class="services__card-title">Tools & Software</h3>
                    <p class="services__card-desc">Canva Pro, ChatGPT Plus, Adobe Creative Cloud, dan berbagai tools produktivitas untuk kebutuhan Anda.</p>
                    <a href="#" class="services__card-link">
                        Selengkapnya
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                    </a>
                </div>
            </div>
            <div class="services__card" id="service-card-3">
                <div class="services__card-img">
                    <img src="{{ asset('assets/images/service-support.png') }}" alt="24/7 Support Service" loading="lazy">
                </div>
                <div class="services__card-content">
                    <span class="services__card-tag services__card-tag--green">24/7</span>
                    <h3 class="services__card-title">Jasa Digital</h3>
                    <p class="services__card-desc">Top-up game, voucher digital, desain grafis, jasa pembuatan website, dan layanan digital custom lainnya.</p>
                    <a href="#" class="services__card-link">
                        Selengkapnya
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ==================== PRODUCTS SECTION ==================== -->
<section class="products" id="products">
    <div class="container">
        <div class="section-header">
            <span class="section-tag">Produk Kami</span>
            <h2 class="section-title">Produk <span class="highlight">Terlaris</span></h2>
            <p class="section-subtitle">Produk digital terpopuler pilihan pelanggan kami dengan harga terbaik.</p>
        </div>
        <div class="products__filter" id="product-filter">
            <button class="products__filter-btn active" data-filter="all">Semua</button>
            @foreach($categories as $category)
                <button class="products__filter-btn" data-filter="{{ $category->slug }}">
                    {{ $category->icon }} {{ $category->name }}
                </button>
            @endforeach
        </div>
        
        <!-- Search Bar -->
        <div class="products__search" style="margin-bottom: 32px;">
            <div style="position: relative; max-width: 500px; margin: 0 auto;">
                <input 
                    type="text" 
                    id="product-search" 
                    placeholder="Cari produk..." 
                    style="width: 100%; padding: 14px 48px 14px 18px; background: var(--color-white); border: var(--border-width) solid var(--border-color); border-radius: 50px; font-size: 0.95rem; outline: none; transition: all var(--transition-fast);"
                    onfocus="this.style.boxShadow='4px 4px 0px var(--color-coral)'; this.style.borderColor='var(--color-coral)';"
                    onblur="this.style.boxShadow=''; this.style.borderColor='var(--border-color)';"
                >
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="position: absolute; right: 18px; top: 50%; transform: translateY(-50%); color: #888; pointer-events: none;">
                    <circle cx="11" cy="11" r="8"></circle>
                    <path d="m21 21-4.35-4.35"></path>
                </svg>
            </div>
        </div>
        
        <div class="products__grid" id="product-grid">
            <!-- Product cards will be injected by JavaScript from database -->
        </div>
    </div>
</section>

<!-- ==================== TESTIMONIALS SECTION ==================== -->
<section class="testimonials" id="testimonials">
    <div class="container">
        <div class="section-header">
            <span class="section-tag">Testimoni</span>
            <h2 class="section-title">Apa Kata <span class="highlight">Mereka</span>?</h2>
            <p class="section-subtitle">Dengarkan pengalaman pelanggan kami yang sudah merasakan layanan Dexornit Store.</p>
        </div>
        <div class="testimonials__slider" id="testimonial-slider">
            <div class="testimonials__track" id="testimonial-track">
                <!-- Testimonial cards injected by JS -->
            </div>
        </div>
        <div class="testimonials__controls">
            <button class="testimonials__btn testimonials__btn--prev" id="testimonial-prev" aria-label="Previous testimonial">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
            </button>
            <div class="testimonials__dots" id="testimonial-dots"></div>
            <button class="testimonials__btn testimonials__btn--next" id="testimonial-next" aria-label="Next testimonial">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
            </button>
        </div>
    </div>
</section>

<!-- ==================== CTA SECTION ==================== -->
<section class="cta" id="cta">
    <div class="container">
        <div class="cta__card">
            <div class="cta__content">
                <h2 class="cta__title">Siap Memulai? 🎉</h2>
                <p class="cta__desc">Bergabung dengan ribuan pelanggan yang sudah mempercayakan kebutuhan digital mereka kepada Dexornit Store.</p>
                <div class="cta__actions">
                    <a href="https://wa.me/6281234567890" target="_blank" class="btn btn--primary btn--lg" id="cta-whatsapp">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                        Chat WhatsApp
                    </a>
                    <a href="#products" class="btn btn--outline btn--lg" id="cta-shop">Belanja Sekarang</a>
                </div>
            </div>
            <div class="cta__decoration">
                <div class="cta__shape cta__shape--1"></div>
                <div class="cta__shape cta__shape--2"></div>
                <div class="cta__shape cta__shape--3"></div>
            </div>
        </div>
    </div>
</section>

<!-- ==================== CONTACT SECTION ==================== -->
<section class="contact" id="contact">
    <div class="container">
        <div class="section-header">
            <span class="section-tag">Kontak</span>
            <h2 class="section-title">Hubungi <span class="highlight">Kami</span></h2>
            <p class="section-subtitle">Ada pertanyaan? Jangan ragu untuk menghubungi kami melalui channel di bawah ini.</p>
        </div>
        <div class="contact__grid">
            <div class="contact__info">
                @forelse($socialMedia as $social)
                    @php $meta = $social->platform_meta; @endphp
                    <a href="{{ $social->url }}" target="_blank" rel="noopener"
                       class="contact__info-card" id="contact-{{ $social->platform }}"
                       style="text-decoration:none; color:inherit;">
                        <div class="contact__info-icon" style="background:{{ $meta['color'] }}20; color:{{ $meta['color'] }}; flex-shrink:0;">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                {!! preg_replace('/<svg[^>]*>|<\/svg>/', '', $meta['svg']) !!}
                            </svg>
                        </div>
                        <div style="min-width:0;">
                            <h4 style="margin:0 0 4px; font-size:1rem;">{{ $meta['label'] }}</h4>
                            <p style="margin:0; font-size:0.82rem; color:#666; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">{{ $social->url }}</p>
                        </div>
                    </a>

                @empty
                    <div class="contact__info-card" id="contact-empty" style="color:#888; text-align:center;">
                        <p>Belum ada kontak yang dikonfigurasi.</p>
                    </div>
                @endforelse
            </div>
            <form class="contact__form" id="contact-form">
                <div class="contact__form-group">
                    <label for="contact-name">Nama Lengkap</label>
                    <input type="text" id="contact-name" name="name" placeholder="Masukkan nama Anda" required>
                </div>
                <div class="contact__form-group">
                    <label for="contact-email-input">Email</label>
                    <input type="email" id="contact-email-input" name="email" placeholder="contoh@email.com" required>
                </div>
                <div class="contact__form-group">
                    <label for="contact-message">Pesan</label>
                    <textarea id="contact-message" name="message" rows="5" placeholder="Tulis pesan Anda di sini..." required></textarea>
                </div>
                <button type="submit" class="btn btn--primary btn--full" id="contact-submit">
                    Kirim Pesan
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
                </button>
            </form>
        </div>
    </div>
</section>

@endsection

