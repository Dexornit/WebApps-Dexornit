<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Panel') - Dexornit Store</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        :root {
            --admin-sidebar-width: 260px;
            --admin-header-height: 70px;
            --color-cream: #FFF5E6;
            --color-white: #FFFFFF;
            --color-black: #1A1A2E;
            --color-coral: #F96854;
            --color-coral-dark: #E85A48;
            --color-pastel-blue: #A8D8EA;
            --color-pastel-purple: #D4A5FF;
            --color-pastel-green: #A8E6CF;
            --color-pastel-yellow: #FFD93D;
            --border-width: 3px;
            --border-color: var(--color-black);
            --shadow-brutal: 5px 5px 0px var(--color-black);
            --shadow-brutal-lg: 8px 8px 0px var(--color-black);
            --font-heading: 'Space Grotesk', sans-serif;
            --font-body: 'Inter', sans-serif;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: var(--font-body);
            background: var(--color-cream);
            color: var(--color-black);
            line-height: 1.6;
            font-size: clamp(13px, 2vw, 16px);
        }

        /* Admin Layout */
        .admin-layout {
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar */
        .admin-sidebar {
            width: var(--admin-sidebar-width);
            background: var(--color-white);
            border-right: var(--border-width) solid var(--border-color);
            position: fixed;
            left: 0;
            top: 0;
            height: 100vh;
            overflow-y: auto;
            z-index: 1000;
            transition: transform 0.3s ease;
        }

        .admin-sidebar__header {
            padding: 24px 20px;
            border-bottom: var(--border-width) solid var(--border-color);
        }

        .admin-sidebar__logo {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
        }

        .admin-sidebar__logo-text {
            font-family: var(--font-heading);
            font-weight: 700;
            font-size: 1.3rem;
            color: var(--color-coral);
        }

        .admin-sidebar__nav {
            padding: 20px 0;
        }

        .admin-sidebar__menu {
            list-style: none;
        }

        .admin-sidebar__menu-item {
            margin-bottom: 4px;
        }

        .admin-sidebar__link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 20px;
            color: var(--color-black);
            text-decoration: none;
            font-weight: 500;
            font-size: 0.95rem;
            transition: all 0.2s ease;
            border-left: 3px solid transparent;
        }

        .admin-sidebar__link:hover {
            background: var(--color-cream);
            border-left-color: var(--color-coral);
        }

        .admin-sidebar__link.active {
            background: var(--color-pastel-yellow);
            border-left-color: var(--color-black);
            font-weight: 700;
        }

        .admin-sidebar__link svg {
            width: 20px;
            height: 20px;
            flex-shrink: 0;
        }

        .admin-sidebar__footer {
            padding: 20px;
            border-top: var(--border-width) solid var(--border-color);
            margin-top: auto;
        }

        .admin-sidebar__user {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px;
            background: var(--color-cream);
            border: 2px solid var(--border-color);
            border-radius: 10px;
        }

        .admin-sidebar__user-avatar {
            width: 40px;
            height: 40px;
            background: var(--color-coral);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            font-size: 1.1rem;
        }

        .admin-sidebar__user-info {
            flex: 1;
        }

        .admin-sidebar__user-name {
            font-weight: 600;
            font-size: 0.9rem;
            display: block;
        }

        .admin-sidebar__user-role {
            font-size: 0.75rem;
            color: #666;
        }

        /* Main Content */
        .admin-main {
            flex: 1;
            margin-left: var(--admin-sidebar-width);
            min-height: 100vh;
        }

        .admin-header {
            height: var(--admin-header-height);
            background: var(--color-white);
            border-bottom: var(--border-width) solid var(--border-color);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 32px;
            position: sticky;
            top: 0;
            z-index: 999;
        }

        .admin-header__title {
            font-family: var(--font-heading);
            font-size: 1.5rem;
            font-weight: 700;
        }

        .admin-header__actions {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .admin-header__btn {
            padding: 10px 20px;
            background: var(--color-coral);
            color: white;
            border: var(--border-width) solid var(--border-color);
            border-radius: 8px;
            font-family: var(--font-heading);
            font-weight: 600;
            font-size: 0.9rem;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            box-shadow: 3px 3px 0px var(--color-black);
            transition: all 0.2s ease;
        }

        .admin-header__btn:hover {
            transform: translate(1px, 1px);
            box-shadow: 2px 2px 0px var(--color-black);
        }

        .admin-header__btn--secondary {
            background: var(--color-white);
            color: var(--color-black);
        }

        /* Hide button text on very small screens */
        @media (max-width: 480px) {
            .admin-header__btn-text { display: none; }
            .admin-header__btn { padding: 10px 12px; }
            .admin-header__title { font-size: 1rem; }
        }

        /* ─── Global Responsive Helpers ───────────────────── */
        /* All tables scroll horizontally on mobile */
        .admin-content table { min-width: 500px; }
        .admin-content .table-wrap,
        .admin-content [style*="overflow-x"] { overflow-x: auto !important; }

        /* Form inputs full width */
        .admin-content input,
        .admin-content select,
        .admin-content textarea { max-width: 100%; }

        /* Fluid headings */
        .admin-content h2 { font-size: clamp(1.1rem, 4vw, 1.8rem) !important; }
        .admin-content h3 { font-size: clamp(1rem, 3vw, 1.3rem) !important; }

        /* 2-column form grid → 1 column on mobile */
        @media (max-width: 768px) {
            /* Products create/edit sidebar fix */
            .admin-form-grid {
                grid-template-columns: 1fr !important;
            }
            .admin-form-sidebar {
                position: static !important;
            }
            /* Filter bar full width */
            .admin-filter-grid {
                grid-template-columns: 1fr !important;
            }
        }

        .admin-header__mobile-toggle {
            display: none;
            flex-direction: column;
            gap: 5px;
            padding: 8px;
            background: none;
            border: none;
            cursor: pointer;
        }

        .admin-header__mobile-toggle span {
            width: 24px;
            height: 3px;
            background: var(--color-black);
            border-radius: 2px;
            transition: all 0.3s ease;
        }

        .admin-content {
            padding: clamp(16px, 3vw, 32px);
        }

        /* Mobile Responsive */
        @media (max-width: 1024px) {
            .admin-sidebar {
                transform: translateX(-100%);
            }

            .admin-sidebar.active {
                transform: translateX(0);
            }

            .admin-main {
                margin-left: 0;
            }

            .admin-header__mobile-toggle {
                display: flex;
            }

            .admin-content {
                padding: 20px;
            }
        }

        @media (max-width: 768px) {
            .admin-header {
                padding: 0 16px;
            }

            .admin-header__title {
                font-size: 1.2rem;
            }

            .admin-content {
                padding: 16px;
            }
        }

        /* Overlay for mobile */
        .admin-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 999;
        }

        .admin-overlay.active {
            display: block;
        }

        /* ─── Toast Notification ─── */
        .toast-container {
            position: fixed;
            top: 24px;
            right: 24px;
            z-index: 9999;
            display: flex;
            flex-direction: column;
            gap: 12px;
            pointer-events: none;
        }
        .toast {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            padding: 16px 20px;
            background: #fff;
            border: 2.5px solid var(--color-black);
            border-radius: 14px;
            box-shadow: 5px 5px 0px var(--color-black);
            font-family: var(--font-body);
            font-size: 0.9rem;
            font-weight: 500;
            min-width: 300px;
            max-width: 420px;
            pointer-events: all;
            transform: translateX(120%);
            transition: transform 0.35s cubic-bezier(.175,.885,.32,1.275);
            position: relative;
            overflow: hidden;
        }
        .toast.show { transform: translateX(0); }
        .toast.hide { transform: translateX(120%); }
        .toast__icon {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            font-size: 1.1rem;
        }
        .toast__body { flex: 1; }
        .toast__title { font-weight: 700; font-size: 0.92rem; margin-bottom: 2px; }
        .toast__msg   { color: #555; font-size: 0.85rem; line-height: 1.4; }
        .toast__close {
            background: none;
            border: none;
            cursor: pointer;
            color: #999;
            padding: 2px;
            display: flex;
            align-items: center;
            flex-shrink: 0;
            transition: color .15s;
        }
        .toast__close:hover { color: #333; }
        .toast__bar {
            position: absolute;
            bottom: 0;
            left: 0;
            height: 3px;
            width: 100%;
            transform-origin: left;
            animation: toastBar 4s linear forwards;
        }
        @keyframes toastBar { from { transform: scaleX(1); } to { transform: scaleX(0); } }
        .toast--success .toast__icon { background: #d1fae5; color: #059669; }
        .toast--success .toast__bar  { background: #059669; }
        .toast--error   .toast__icon { background: #fee2e2; color: #dc2626; }
        .toast--error   .toast__bar  { background: #dc2626; }
        .toast--info    .toast__icon { background: #dbeafe; color: #2563eb; }
        .toast--info    .toast__bar  { background: #2563eb; }
    </style>

    @stack('styles')
</head>
<body>
    <div class="admin-layout">
        <!-- Sidebar -->
        <aside class="admin-sidebar" id="adminSidebar">
            <div class="admin-sidebar__header">
                <a href="{{ route('admin.dashboard') }}" class="admin-sidebar__logo">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                        <polyline points="9 22 9 12 15 12 15 22"/>
                    </svg>
                    <span class="admin-sidebar__logo-text">Dexornit Admin</span>
                </a>
            </div>

            <nav class="admin-sidebar__nav">
                <ul class="admin-sidebar__menu">
                    <li class="admin-sidebar__menu-item">
                        <a href="{{ route('admin.dashboard') }}" class="admin-sidebar__link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="3" width="7" height="7"/>
                                <rect x="14" y="3" width="7" height="7"/>
                                <rect x="14" y="14" width="7" height="7"/>
                                <rect x="3" y="14" width="7" height="7"/>
                            </svg>
                            Dashboard
                        </a>
                    </li>
                    <li class="admin-sidebar__menu-item">
                        <a href="{{ route('admin.categories.index') }}" class="admin-sidebar__link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/>
                            </svg>
                            Categories
                        </a>
                    </li>
                    <li class="admin-sidebar__menu-item">
                        <a href="{{ route('admin.products.index') }}" class="admin-sidebar__link {{ request()->routeIs('admin.products.*') ? 'active' : '' }}">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/>
                                <line x1="3" y1="6" x2="21" y2="6"/>
                                <path d="M16 10a4 4 0 01-8 0"/>
                            </svg>
                            Products
                        </a>
                    </li>
                </ul>
            </nav>

            <div class="admin-sidebar__footer">
                <div class="admin-sidebar__user">
                    <div class="admin-sidebar__user-avatar">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </div>
                    <div class="admin-sidebar__user-info">
                        <span class="admin-sidebar__user-name">{{ Auth::user()->name }}</span>
                        <span class="admin-sidebar__user-role">Administrator</span>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="admin-main">
            <!-- Header -->
            <header class="admin-header">
                <button class="admin-header__mobile-toggle" id="mobileToggle">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>

                <h1 class="admin-header__title">@yield('page-title', 'Dashboard')</h1>

                <div class="admin-header__actions">
                    <a href="{{ route('home') }}" class="admin-header__btn admin-header__btn--secondary" target="_blank">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/>
                            <polyline points="15 3 21 3 21 9"/>
                            <line x1="10" y1="14" x2="21" y2="3"/>
                        </svg>
                        <span class="admin-header__btn-text">View Site</span>
                    </a>
                    <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                        @csrf
                        <button type="submit" class="admin-header__btn">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                                <polyline points="16 17 21 12 16 7"/>
                                <line x1="21" y1="12" x2="9" y2="12"/>
                            </svg>
                            <span class="admin-header__btn-text">Logout</span>
                        </button>
                    </form>
                </div>
            </header>

            <!-- Content -->
            <div class="admin-content">
                @if(session('success'))
                    <div style="padding: 16px 20px; background: var(--color-pastel-green); border: var(--border-width) solid var(--border-color); border-radius: 10px; margin-bottom: 24px; box-shadow: 3px 3px 0px var(--color-black);">
                        <strong>✓ Success!</strong> {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div style="padding: 16px 20px; background: #FFB5B5; border: var(--border-width) solid var(--border-color); border-radius: 10px; margin-bottom: 24px; box-shadow: 3px 3px 0px var(--color-black);">
                        <strong>✗ Error!</strong> {{ session('error') }}
                    </div>
                @endif

                @yield('content')
            </div>
        </main>
    </div>

    <!-- Toast Container -->
    <div class="toast-container" id="toastContainer"></div>

    <!-- Mobile Overlay -->
    <div class="admin-overlay" id="adminOverlay"></div>

    <script>
        // ─── Toast System ───────────────────────────────────────────────
        function showToast(type, title, message) {
            const container = document.getElementById('toastContainer');
            const icons = {
                success: '✓',
                error:   '✕',
                info:    'ℹ'
            };
            const titles = { success: 'Berhasil!', error: 'Error!', info: 'Info' };

            const toast = document.createElement('div');
            toast.className = `toast toast--${type}`;
            toast.innerHTML = `
                <div class="toast__icon">${icons[type] || 'ℹ'}</div>
                <div class="toast__body">
                    <div class="toast__title">${title || titles[type]}</div>
                    <div class="toast__msg">${message}</div>
                </div>
                <button class="toast__close" onclick="dismissToast(this.parentElement)">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                </button>
                <div class="toast__bar"></div>
            `;
            container.appendChild(toast);

            // Trigger animation
            requestAnimationFrame(() => {
                requestAnimationFrame(() => toast.classList.add('show'));
            });

            // Auto dismiss
            setTimeout(() => dismissToast(toast), 4000);
        }

        function dismissToast(toast) {
            if (!toast) return;
            toast.classList.remove('show');
            toast.classList.add('hide');
            setTimeout(() => toast.remove(), 400);
        }

        // ─── Show toasts from session ────────────────────────────────────
        @if(session('success'))
            showToast('success', 'Berhasil!', @json(session('success')));
        @endif
        @if(session('error'))
            showToast('error', 'Error!', @json(session('error')));
        @endif
        @if(session('info'))
            showToast('info', 'Info', @json(session('info')));
        @endif

        // ─── Mobile menu toggle ──────────────────────────────────────────
        const mobileToggle = document.getElementById('mobileToggle');
        const adminSidebar = document.getElementById('adminSidebar');
        const adminOverlay = document.getElementById('adminOverlay');

        if (mobileToggle) {
            mobileToggle.addEventListener('click', () => {
                adminSidebar.classList.toggle('active');
                adminOverlay.classList.toggle('active');
            });

            adminOverlay.addEventListener('click', () => {
                adminSidebar.classList.remove('active');
                adminOverlay.classList.remove('active');
            });
        }
    </script>

    @stack('scripts')
</body>
</html>
