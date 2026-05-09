/* ============================================================
   DEXORNIT STORE — JavaScript (DOM Manipulation)
   Features: Navbar, Scroll Animations, Product Filter,
             Testimonial Slider, Counter Animation, Form, etc.
   ============================================================ */

document.addEventListener('DOMContentLoaded', () => {
    // ==================== DATA ====================
    // Use data from Blade if available, otherwise use default data
    const products = window.productsData || [
        { emoji: '🎬', name: 'Netflix Premium', desc: 'Akun Netflix Premium UHD 4K, sharing atau private.', price: 'Rp 35.000', period: '/bulan', category: 'streaming' },
        { emoji: '🎵', name: 'Spotify Premium', desc: 'Spotify Premium Individual & Family plan, garansi full.', price: 'Rp 15.000', period: '/bulan', category: 'streaming' },
        { emoji: '▶️', name: 'YouTube Premium', desc: 'YouTube Premium tanpa iklan + YouTube Music.', price: 'Rp 20.000', period: '/bulan', category: 'streaming' },
        { emoji: '🎨', name: 'Canva Pro', desc: 'Canva Pro team invite, akses semua fitur premium.', price: 'Rp 25.000', period: '/bulan', category: 'tools' },
        { emoji: '🤖', name: 'ChatGPT Plus', desc: 'Akun ChatGPT Plus dengan akses GPT-4 & plugins.', price: 'Rp 85.000', period: '/bulan', category: 'tools' },
        { emoji: '🎮', name: 'Top-Up ML', desc: 'Top-up diamond Mobile Legends, proses cepat & aman.', price: 'Rp 10.000', period: '', category: 'gaming' },
        { emoji: '🏰', name: 'Valorant VP', desc: 'Top-up Valorant Points dengan harga termurah.', price: 'Rp 15.000', period: '', category: 'gaming' },
        { emoji: '☁️', name: 'Adobe CC', desc: 'Adobe Creative Cloud All Apps, lisensi full 1 tahun.', price: 'Rp 150.000', period: '/tahun', category: 'tools' },
    ];

    const testimonials = [
        { name: 'Rizky Pratama', role: 'Pelanggan Setia', avatar: '👨‍💻', text: 'Dexornit Store bener-bener recommended! Proses cepat, harga murah, dan yang paling penting garansi nya jelas. Udah langganan dari tahun lalu.' },
        { name: 'Sarah Amelia', role: 'Content Creator', avatar: '👩‍🎨', text: 'Beli Canva Pro di sini harganya jauh lebih murah. Support-nya juga fast response banget. Pasti bakal repeat order terus!' },
        { name: 'Budi Setiawan', role: 'Mahasiswa', avatar: '🧑‍🎓', text: 'Sebagai mahasiswa, harga terjangkau itu penting banget. Di Dexornit Store semua produk digital harganya ramah di kantong. Top!' },
        { name: 'Anisa Putri', role: 'Freelancer', avatar: '👩‍💼', text: 'Adobe Creative Cloud setahun full dengan harga segini? Gila sih, murah banget. Kualitas produk juga oke, ga pernah ada masalah.' },
        { name: 'Dimas Arya', role: 'Gamer', avatar: '🎮', text: 'Top-up game di sini prosesnya instan banget. Baru bayar langsung masuk diamond-nya. Mantap Dexornit!' },
        { name: 'Lina Marlina', role: 'Pelajar', avatar: '👧', text: 'Spotify Premium murah dan garansi sebulan penuh. Kalau ada masalah langsung diganti. Pelayanan terbaik!' },
    ];

    // ==================== NAVBAR ====================
    const navbar = document.getElementById('navbar');
    const navToggle = document.getElementById('nav-toggle');
    const navMenu = document.getElementById('nav-menu');
    const navLinks = document.querySelectorAll('.navbar__link');

    // Create overlay for mobile menu
    const overlay = document.createElement('div');
    overlay.classList.add('navbar__overlay');
    document.body.appendChild(overlay);

    // Toggle mobile menu
    function toggleMobileMenu() {
        const isOpen = navMenu.classList.toggle('navbar__menu--open');
        navToggle.classList.toggle('navbar__toggle--active');
        overlay.classList.toggle('navbar__overlay--visible');
        document.body.style.overflow = isOpen ? 'hidden' : '';
    }

    navToggle.addEventListener('click', toggleMobileMenu);
    overlay.addEventListener('click', toggleMobileMenu);

    // Close menu on link click
    navLinks.forEach(link => {
        link.addEventListener('click', () => {
            if (navMenu.classList.contains('navbar__menu--open')) {
                toggleMobileMenu();
            }
        });
    });

    // Navbar scroll effect
    window.addEventListener('scroll', () => {
        if (window.scrollY > 50) {
            navbar.classList.add('navbar--scrolled');
        } else {
            navbar.classList.remove('navbar--scrolled');
        }
    });

    // Active link on scroll
    const sections = document.querySelectorAll('section[id]');

    function updateActiveNav() {
        const scrollY = window.scrollY + 100;
        sections.forEach(section => {
            const sectionTop = section.offsetTop;
            const sectionHeight = section.offsetHeight;
            const sectionId = section.getAttribute('id');

            if (scrollY >= sectionTop && scrollY < sectionTop + sectionHeight) {
                navLinks.forEach(link => {
                    link.classList.remove('active');
                    if (link.getAttribute('data-section') === sectionId) {
                        link.classList.add('active');
                    }
                });
            }
        });
    }

    window.addEventListener('scroll', updateActiveNav);

    // ==================== COUNTER ANIMATION ====================
    function animateCounters() {
        const counters = document.querySelectorAll('.hero__stat-number');
        counters.forEach(counter => {
            const target = parseInt(counter.getAttribute('data-target'));
            const duration = 2000;
            const step = target / (duration / 16);
            let current = 0;

            function updateCounter() {
                current += step;
                if (current < target) {
                    counter.textContent = Math.floor(current);
                    requestAnimationFrame(updateCounter);
                } else {
                    counter.textContent = target;
                }
            }

            updateCounter();
        });
    }

    // Trigger counters when hero is visible
    const heroObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                animateCounters();
                heroObserver.unobserve(entry.target);
            }
        });
    }, { threshold: 0.3 });

    const heroStats = document.querySelector('.hero__stats');
    if (heroStats) heroObserver.observe(heroStats);

    // ==================== PRODUCT CARDS (DOM) ====================
    const productGrid = document.getElementById('product-grid');
    const filterBtns = document.querySelectorAll('.products__filter-btn');
    const searchInput = document.getElementById('product-search');
    
    let currentFilter = 'all';
    let currentSearch = '';

    function createProductCard(product) {
        const card = document.createElement('div');
        card.classList.add('product-card', 'product-card--animate');
        card.setAttribute('data-category', product.category);

        const categoryClass = `product-card__category--${product.category}`;

        // Add click handler to navigate to product detail
        const productUrl = product.id ? `/product/${product.id}` : '#';

        // Display logo if available, otherwise use emoji
        const logoOrEmoji = product.logo 
            ? `<img src="${product.logo}" alt="${product.name}" class="product-card__logo" style="width: 64px; height: 64px; object-fit: contain;">`
            : `<span class="product-card__emoji">${product.emoji}</span>`;

        // Format price display
        const priceDisplay = product.period 
            ? `<span class="product-card__price">${product.price} <small>${product.period}</small></span>`
            : `<span class="product-card__price">${product.price}</span>`;

        card.innerHTML = `
            ${logoOrEmoji}
            <span class="product-card__category ${categoryClass}">${product.categoryName}</span>
            <h3 class="product-card__name">${product.name}</h3>
            <p class="product-card__desc">${product.desc}</p>
            <div class="product-card__footer">
                ${priceDisplay}
                <a href="${productUrl}" class="product-card__btn" aria-label="Order ${product.name}">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 01-8 0"/></svg>
                </a>
            </div>
        `;

        // Make entire card clickable
        card.style.cursor = 'pointer';
        card.addEventListener('click', (e) => {
            // Don't navigate if clicking the button
            if (!e.target.closest('.product-card__btn')) {
                window.location.href = productUrl;
            }
        });

        return card;
    }

    function renderProducts(filter = 'all', search = '') {
        productGrid.innerHTML = '';

        let filtered = filter === 'all'
            ? products
            : products.filter(p => p.category === filter);
        
        // Apply search filter
        if (search.trim() !== '') {
            const searchLower = search.toLowerCase();
            filtered = filtered.filter(p => 
                p.name.toLowerCase().includes(searchLower) || 
                p.desc.toLowerCase().includes(searchLower) ||
                p.category.toLowerCase().includes(searchLower)
            );
        }

        if (filtered.length === 0) {
            productGrid.innerHTML = '<div style="grid-column: 1/-1; text-align: center; padding: 60px 20px; color: #888;"><p style="font-size: 1.1rem; margin-bottom: 8px;">😔 Produk tidak ditemukan</p><p style="font-size: 0.9rem;">Coba kata kunci lain atau pilih kategori berbeda</p></div>';
            return;
        }

        filtered.forEach((product, index) => {
            const card = createProductCard(product);
            card.style.animationDelay = `${index * 0.08}s`;
            productGrid.appendChild(card);
        });
    }

    // Initial render
    renderProducts();

    // Filter click handler
    filterBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            filterBtns.forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            currentFilter = btn.getAttribute('data-filter');
            renderProducts(currentFilter, currentSearch);
        });
    });

    // Search input handler
    if (searchInput) {
        searchInput.addEventListener('input', (e) => {
            currentSearch = e.target.value;
            renderProducts(currentFilter, currentSearch);
        });
    }

    // ==================== TESTIMONIAL SLIDER ====================
    const testimonialSlider = document.getElementById('testimonial-slider');
    const testimonialTrack = document.getElementById('testimonial-track');
    const testimonialDots = document.getElementById('testimonial-dots');
    const prevBtn = document.getElementById('testimonial-prev');
    const nextBtn = document.getElementById('testimonial-next');

    let currentSlide = 0;
    let slidesPerView = 3;

    function getResponsiveSlidesPerView() {
        if (window.innerWidth <= 768) return 1;
        if (window.innerWidth <= 1024) return 2;
        return 3;
    }

    // Calculate and set explicit pixel widths on cards based on slider width
    function setCardWidths() {
        const cards = testimonialTrack.querySelectorAll('.testimonial-card');
        if (cards.length === 0) return;

        const sliderW = testimonialSlider.clientWidth;
        const gap = 24;
        let cardW;

        if (window.innerWidth <= 768) {
            // 1 card visible + peek of next (~32px)
            cardW = sliderW - gap - 16;
        } else if (window.innerWidth <= 1024) {
            // 2 cards
            cardW = (sliderW - gap) / 2;
        } else {
            // 3 cards
            cardW = (sliderW - gap * 2) / 3;
        }

        cards.forEach(card => {
            card.style.flex = `0 0 ${cardW}px`;
            card.style.width = `${cardW}px`;
        });
    }

    function createTestimonialCard(testimonial) {
        const card = document.createElement('div');
        card.classList.add('testimonial-card');

        card.innerHTML = `
            <div class="testimonial-card__stars">
                ${'<span class="testimonial-card__star">★</span>'.repeat(5)}
            </div>
            <p class="testimonial-card__text">"${testimonial.text}"</p>
            <div class="testimonial-card__author">
                <div class="testimonial-card__avatar">${testimonial.avatar}</div>
                <div>
                    <div class="testimonial-card__name">${testimonial.name}</div>
                    <div class="testimonial-card__role">${testimonial.role}</div>
                </div>
            </div>
        `;

        return card;
    }

    function renderTestimonials() {
        testimonialTrack.innerHTML = '';
        testimonials.forEach(t => {
            testimonialTrack.appendChild(createTestimonialCard(t));
        });
    }

    function getTotalSlides() {
        return Math.max(0, testimonials.length - slidesPerView);
    }

    function renderDots() {
        testimonialDots.innerHTML = '';
        const total = getTotalSlides() + 1;
        for (let i = 0; i < total; i++) {
            const dot = document.createElement('button');
            dot.classList.add('testimonials__dot');
            if (i === currentSlide) dot.classList.add('active');
            dot.addEventListener('click', () => goToSlide(i));
            dot.setAttribute('aria-label', `Go to testimonial ${i + 1}`);
            testimonialDots.appendChild(dot);
        }
    }

    function goToSlide(index) {
        currentSlide = Math.max(0, Math.min(index, getTotalSlides()));
        updateSlider();
    }

    function updateSlider() {
        const cards = testimonialTrack.querySelectorAll('.testimonial-card');
        if (cards.length === 0) return;

        const cardW = cards[0].offsetWidth;
        const gap = 24;
        testimonialSlider.scrollTo({ left: currentSlide * (cardW + gap), behavior: 'smooth' });

        const dots = testimonialDots.querySelectorAll('.testimonials__dot');
        dots.forEach((dot, i) => dot.classList.toggle('active', i === currentSlide));
    }

    prevBtn.addEventListener('click', () => goToSlide(currentSlide - 1));
    nextBtn.addEventListener('click', () => goToSlide(currentSlide + 1));

    // Auto-slide
    let autoSlideInterval = setInterval(() => {
        goToSlide(currentSlide >= getTotalSlides() ? 0 : currentSlide + 1);
    }, 5000);

    // Pause on hover
    testimonialSlider.addEventListener('mouseenter', () => clearInterval(autoSlideInterval));
    testimonialSlider.addEventListener('mouseleave', () => {
        autoSlideInterval = setInterval(() => {
            goToSlide(currentSlide >= getTotalSlides() ? 0 : currentSlide + 1);
        }, 5000);
    });

    // Sync dots on native scroll/swipe
    testimonialSlider.addEventListener('scroll', () => {
        const cards = testimonialTrack.querySelectorAll('.testimonial-card');
        if (cards.length === 0) return;
        const cardW = cards[0].offsetWidth;
        currentSlide = Math.min(Math.round(testimonialSlider.scrollLeft / (cardW + 24)), getTotalSlides());
        const dots = testimonialDots.querySelectorAll('.testimonials__dot');
        dots.forEach((dot, i) => dot.classList.toggle('active', i === currentSlide));
    }, { passive: true });

    // Responsive resize
    function handleSliderResize() {
        slidesPerView = getResponsiveSlidesPerView();
        currentSlide = Math.min(currentSlide, getTotalSlides());
        setCardWidths();
        renderDots();
        updateSlider();
    }

    window.addEventListener('resize', handleSliderResize);

    // Init
    renderTestimonials();
    setCardWidths();
    handleSliderResize();

    // ==================== SCROLL REVEAL ANIMATION ====================
    const revealElements = document.querySelectorAll(
        '.about__card, .services__card, .contact__info-card, .contact__form, .cta__card'
    );

    revealElements.forEach(el => el.classList.add('reveal'));

    const revealObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('reveal--visible');
                revealObserver.unobserve(entry.target);
            }
        });
    }, { threshold: 0.15, rootMargin: '0px 0px -40px 0px' });

    revealElements.forEach(el => revealObserver.observe(el));

    // ==================== CONTACT FORM ====================
    const contactForm = document.getElementById('contact-form');

    contactForm.addEventListener('submit', (e) => {
        e.preventDefault();

        const name = document.getElementById('contact-name').value.trim();
        const email = document.getElementById('contact-email-input').value.trim();
        const message = document.getElementById('contact-message').value.trim();

        if (!name || !email || !message) {
            alert('Mohon lengkapi semua field!');
            return;
        }

        // Simulate success
        contactForm.innerHTML = `
            <div class="contact__form--success">
                <div class="success-icon">✅</div>
                <h3 class="success-title">Pesan Terkirim!</h3>
                <p class="success-desc">Terima kasih ${name}, pesan Anda sudah kami terima. Kami akan segera menghubungi Anda melalui email.</p>
            </div>
        `;
    });

    // ==================== BACK TO TOP ====================
    const backToTop = document.getElementById('back-to-top');

    window.addEventListener('scroll', () => {
        if (window.scrollY > 500) {
            backToTop.classList.add('back-to-top--visible');
        } else {
            backToTop.classList.remove('back-to-top--visible');
        }
    });

    backToTop.addEventListener('click', () => {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });

    // ==================== SMOOTH SCROLL FOR ANCHOR LINKS ====================
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({ behavior: 'smooth' });
            }
        });
    });

    // ==================== PARALLAX FLOATING ELEMENTS ====================
    window.addEventListener('scroll', () => {
        const scrolled = window.scrollY;
        const floatingElements = document.querySelectorAll('.hero__floating');
        floatingElements.forEach((el, i) => {
            const speed = (i + 1) * 0.3;
            el.style.transform = `translateY(${scrolled * speed * -0.2}px)`;
        });
    });

    console.log('%c🚀 Dexornit Store Landing Page Loaded!', 'color: #F96854; font-size: 16px; font-weight: bold;');
});
