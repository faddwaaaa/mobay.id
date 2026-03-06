@php
    use Illuminate\Support\Str;

    $user = Auth::user();
    $userSlug = $user->username;
    $avatar = Auth::user()->avatar ?? null;
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Dashboard | Payou.id')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="{{ asset('css/profile.css') }}">
    <link rel="stylesheet" href="{{ asset('css/darkmode.css') }}">

    <style>
        /* ── TOKENS ──────────────────────────────────────── */
        :root {
            --sidebar-w:        220px;
            --nav-font:         'Plus Jakarta Sans', sans-serif;
            --sidebar-border:   #eff2f7;
            --nav-hover-bg:     #f3f7ff;
            --nav-active-bg:    #e8f0fe;
            --nav-active-color: #2356e8;
            --nav-color:        #556070;
            --nav-icon-color:   #9aaabb;
            --accent:           #2356e8;
            --accent-light:     #e8f0fe;
            --danger:           #e53e3e;
            --danger-light:     #fff5f5;
            --page-bg:          linear-gradient(145deg, #ffffff 0%, #f4f8ff 52%, #deeaff 100%);
            --text-primary:     #111827;
            --text-muted:       #6b7c93;
            --r-nav:            8px;
            --r-card:           10px;
            --ease:             0.17s ease;

            /* Sidebar dark blue */
            --sb-bg:            #1e3a8a;
            --sb-text:          rgba(255,255,255,0.72);
            --sb-text-muted:    rgba(255,255,255,0.35);
            --sb-icon:          rgba(255,255,255,0.42);
            --sb-hover:         rgba(255,255,255,0.09);
            --sb-active:        rgba(255,255,255,0.15);
            --sb-border:        rgba(255,255,255,0.10);
            --sb-shape1:        rgba(255,255,255,0.06);
            --sb-shape2:        rgba(255,255,255,0.04);
        }

        /* ── RESET ───────────────────────────────────────── */
        *, *::before, *::after { box-sizing: border-box; }
        html, body { margin: 0; padding: 0; min-height: 100vh; }

        /* ── PAGE BACKGROUND ─────────────────────────────── */
        .s-page-bg {
            position: fixed;
            inset: 0;
            background: var(--page-bg);
            background-attachment: fixed;
            z-index: -1;
        }

        /* ── SIDEBAR ─────────────────────────────────────── */
        .s-sidebar {
            position: fixed;
            top: 0; left: 0;
            width: var(--sidebar-w);
            height: 100vh;
            background: var(--sb-bg);
            border-right: none;
            display: flex;
            flex-direction: column;
            z-index: 40;
            overflow: hidden;
            box-shadow: 4px 0 24px rgba(15, 30, 80, 0.20);
            font-family: var(--nav-font);
            -webkit-font-smoothing: antialiased;
        }

        /* Shape dekoratif */
        .s-sidebar::before {
            content: '';
            position: absolute;
            top: -40px; right: -50px;
            width: 200px; height: 200px;
            border-radius: 50%;
            background: var(--sb-shape1);
            pointer-events: none;
            z-index: 0;
        }

        .s-sidebar::after {
            content: '';
            position: absolute;
            bottom: 80px; left: -30px;
            width: 130px; height: 130px;
            border-radius: 50%;
            background: var(--sb-shape2);
            pointer-events: none;
            z-index: 0;
        }

        /* Semua konten di atas shape */
        .s-logo,
        .s-top-account,
        .s-nav,
        .s-footer { position: relative; z-index: 1; }

        /* ── LOGO ────────────────────────────────────────── */
        .s-logo {
            padding: 18px 17px 14px;
            border-bottom: 1px solid var(--sb-border);
            flex-shrink: 0;
            text-align: center;
        }

        .s-logo a { display: inline-flex; align-items: center; }

        .s-logo img {
            height: 23px;
            width: auto;
            object-fit: contain;
            display: block;
            margin: 0 auto;
        }

        /* ── TOP ACCOUNT ─────────────────────────────────── */
        .s-top-account {
            flex-shrink: 0;
            padding: 7px 7px 8px;
            border-bottom: 1px solid var(--sb-border);
            overflow: visible;
        }

        .s-footer-top {
            display: flex;
            align-items: center;
            gap: 2px;
        }

        /* ── PROFILE LINK ────────────────────────────────── */
        .s-profile-link {
            display: flex;
            align-items: center;
            gap: 9px;
            padding: 8px 9px;
            border-radius: var(--r-card);
            text-decoration: none;
            transition: background var(--ease);
            flex: 1;
            min-width: 0;
        }

        .s-profile-link:hover { background: var(--sb-hover); }

        .s-avatar {
            width: 31px; height: 31px;
            border-radius: 50%;
            overflow: hidden;
            flex-shrink: 0;
            border: 1.5px solid rgba(255,255,255,0.18);
        }

        .s-avatar img { width: 100%; height: 100%; object-fit: cover; }

        .s-profile-info { flex: 1; min-width: 0; }

        .s-profile-name {
            font-size: 12.5px;
            font-weight: 600;
            font-family: var(--nav-font);
            color: #ffffff;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            line-height: 1.35;
        }

        .s-profile-uname {
            font-size: 11px;
            font-family: var(--nav-font);
            color: rgba(255,255,255,0.48);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .s-profile-arrow { font-size: 9px; color: rgba(255,255,255,0.22); flex-shrink: 0; }

        /* ── NAV ─────────────────────────────────────────── */
        .s-nav {
            flex: 1;
            padding: 4px 7px;
            display: flex;
            flex-direction: column;
            gap: 1px;
            overflow-y: auto;
            overflow-x: hidden;
        }

        .s-nav::-webkit-scrollbar { width: 0; }

        /* ── SECTION LABEL ───────────────────────────────── */
        .s-label {
            padding: 14px 17px 4px;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: .09em;
            text-transform: uppercase;
            color: var(--sb-text-muted);
            font-family: var(--nav-font);
        }

        /* ── NAV ITEM ────────────────────────────────────── */
        .s-nav-item {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 9px;
            border-radius: var(--r-nav);
            color: var(--sb-text);
            font-size: 13px;
            font-weight: 500;
            font-family: var(--nav-font);
            text-decoration: none;
            transition: background var(--ease), color var(--ease);
            white-space: nowrap;
            cursor: pointer;
            border: none;
            background: transparent;
            width: 100%;
            text-align: left;
            line-height: 1;
        }

        .s-nav-item:hover {
            background: var(--sb-hover);
            color: #ffffff;
        }

        .s-nav-item:hover .s-icon {
            background: rgba(255,255,255,0.13);
            color: #ffffff;
        }

        .s-nav-item.active {
            background: var(--sb-active);
            color: #ffffff;
            font-weight: 600;
        }

        .s-nav-item.active .s-icon {
            background: #ffffff;
            color: var(--sb-bg);
        }

        /* ── ICON BUBBLE ─────────────────────────────────── */
        .s-icon {
            width: 29px; height: 29px;
            border-radius: 7px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            color: var(--sb-icon);
            background: transparent;
            flex-shrink: 0;
            transition: background var(--ease), color var(--ease);
        }

        /* ── DIVIDER ─────────────────────────────────────── */
        .s-divider {
            height: 1px;
            background: var(--sb-border);
            margin: 5px 7px;
        }

        /* ── TEMA DROPDOWN ───────────────────────────────── */
        .s-theme-toggle {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 8px 9px;
            border-radius: var(--r-nav);
            color: var(--sb-text);
            font-size: 13px;
            font-weight: 500;
            font-family: var(--nav-font);
            cursor: pointer;
            transition: background var(--ease), color var(--ease);
            user-select: none;
        }

        .s-theme-toggle:hover {
            background: var(--sb-hover);
            color: #ffffff;
        }

        .s-theme-toggle:hover .s-icon {
            background: rgba(255,255,255,0.13);
            color: #ffffff;
        }

        .s-theme-toggle .s-left { display: flex; align-items: center; gap: 8px; }

        .s-arrow {
            font-size: 9px;
            color: rgba(255,255,255,0.28);
            transition: transform var(--ease);
        }

        .s-theme-toggle.open .s-arrow { transform: rotate(180deg); }

        .s-theme-menu {
            display: none;
            flex-direction: column;
            margin: 2px 0 2px 38px;
            gap: 1px;
        }

        .s-theme-menu.open { display: flex; }

        .s-theme-opt {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 7px 10px;
            border-radius: 7px;
            border: none;
            background: transparent;
            color: var(--sb-text);
            font-size: 12.5px;
            font-family: var(--nav-font);
            cursor: pointer;
            width: 100%;
            text-align: left;
            transition: background var(--ease);
        }

        .s-theme-opt:hover {
            background: var(--sb-hover);
            color: #ffffff;
        }

        .s-theme-opt .check-icon { margin-left: auto; opacity: 0; font-size: 10px; }

        /* ── FOOTER ──────────────────────────────────────── */
        .s-footer {
            flex-shrink: 0;
            padding: 7px 7px 12px;
            border-top: 1px solid var(--sb-border);
            overflow: visible;
        }

        /* ── LOGOUT ──────────────────────────────────────── */
        .s-logout {
            display: flex;
            align-items: center;
            gap: 8px;
            width: 100%;
            padding: 8px 9px;
            border-radius: var(--r-nav);
            border: none;
            background: transparent;
            color: rgba(255,190,190,0.88);
            font-size: 13px;
            font-weight: 500;
            font-family: var(--nav-font);
            cursor: pointer;
            text-decoration: none;
            transition: background var(--ease);
        }

        .s-logout:hover { background: rgba(255,255,255,0.08); }

        .s-logout .s-icon { color: rgba(255,190,190,0.88); font-size: 12px; }

        /* ── MAIN CONTENT ────────────────────────────────── */
        .main-wrap { margin-left: var(--sidebar-w); min-height: 100vh; }

        .content-pad {
            padding: 28px 30px;
            min-height: 100vh;
            background: transparent;
        }

        /* ── MOBILE TOPBAR ───────────────────────────────── */
        .m-topbar {
            display: none;
            position: fixed;
            top: 0; left: 0; right: 0;
            height: 50px;
            background: rgba(255,255,255,0.92);
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
            border-bottom: 1px solid var(--sidebar-border);
            z-index: 300;
            align-items: center;
            padding: 0 13px;
            gap: 10px;
            font-family: var(--nav-font);
        }

        .m-topbar-logo { margin-right: auto; }
        .m-topbar-logo img { height: 21px; }

        .m-hamburger {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 32px; height: 32px;
            border-radius: 7px;
            border: none;
            background: transparent;
            color: #5a6a85;
            font-size: 15px;
            cursor: pointer;
            flex-shrink: 0;
            transition: background var(--ease);
            font-family: inherit;
        }

        .m-hamburger:hover { background: var(--nav-hover-bg); }

        /* ── OVERLAY MOBILE ──────────────────────────────── */
        .s-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(8,18,48,.35);
            z-index: 39;
            backdrop-filter: blur(3px);
        }

        .s-overlay.active { display: block; }

        /* ── MODAL FIX ───────────────────────────────────── */
        #qr-modal,
        #withdraw-modal {
            left: 0 !important;
            top: 0 !important;
            width: 100vw !important;
            height: 100vh !important;
            margin: 0 !important;
        }

        /* ── RESPONSIVE ──────────────────────────────────── */
        @media (max-width: 768px) {
            .m-topbar { display: flex; }
            .s-sidebar {
                transform: translateX(-100%);
                transition: transform 0.24s cubic-bezier(.4,0,.2,1);
                z-index: 41;
            }
            .s-sidebar.active { transform: translateX(0); }
            .s-overlay { z-index: 40; }
            .main-wrap { margin-left: 0; margin-top: 50px; }
            .content-pad { padding: 16px 15px; }
        }

        @media (max-width: 480px) {
            .content-pad { padding: 13px 11px; }
        }

        /* ── FADE IN ─────────────────────────────────────── */
        body:not(.preload) .content-pad {
            animation: pgFadeUp .3s ease both;
        }

        @keyframes pgFadeUp {
            from { opacity: 0; transform: translateY(7px); }
            to   { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body class="preload">

<div class="s-page-bg"></div>
<div class="s-overlay" id="sOverlay"></div>

<!-- ── MOBILE TOPBAR ───────────────────────────────────────── -->
<div class="m-topbar">
    <button class="m-hamburger" id="mHamburger" aria-label="Buka menu">
        <i class="fas fa-bars"></i>
    </button>
    <div class="m-topbar-logo">
        <a href="{{ route('dashboard') }}">
            <img src="{{ asset('img/icon.png') }}" alt="Payou.id">
        </a>
    </div>
    <button class="notif-bell-btn notif-bell-trigger" id="notifBellBtnMobile" aria-label="Notifikasi">
        <i class="fas fa-bell"></i>
        <span class="notif-badge notif-badge-trigger" id="notifBadgeMobile"></span>
    </button>
</div>

<!-- ── SIDEBAR ──────────────────────────────────────────────── -->
<aside class="s-sidebar" id="sSidebar">

    <!-- Logo -->
    <div class="s-logo">
        <a href="{{ route('dashboard') }}">
            <img src="{{ asset('img/icon.png') }}" alt="Payou.id">
        </a>
    </div>

    <!-- Profile -->
    <div class="s-top-account">
        <div class="s-footer-top">
            <a href="{{ route('dashboard.profile') }}" class="s-profile-link">
                <div class="s-avatar">
                    @if ($avatar)
                        <img src="{{ Str::startsWith($avatar, ['http://','https://']) ? $avatar : asset('storage/'.$avatar) }}"
                             alt="{{ Auth::user()->name }}">
                    @else
                        <img src="{{ asset('img/default-avatar.jpg') }}" alt="Avatar">
                    @endif
                </div>
                <div class="s-profile-info">
                    <div class="s-profile-name">{{ Auth::user()->name }}</div>
                    <div class="s-profile-uname">&#64;{{ $user->username }}</div>
                </div>
                <i class="fas fa-chevron-right s-profile-arrow"></i>
            </a>
            <button class="notif-bell-btn notif-bell-trigger" id="notifBellBtnSidebar" aria-label="Notifikasi">
                <i class="fas fa-bell"></i>
                <span class="notif-badge notif-badge-trigger" id="notifBadgeSidebar"></span>
            </button>
        </div>
    </div>

    <!-- Nav -->
    <nav class="s-nav">
        <div class="s-label">Menu</div>

        <a href="{{ route('dashboard') }}"
           class="s-nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <span class="s-icon"><i class="fas fa-house"></i></span>
            Dashboard
        </a>

        <a href="{{ route('links.index') }}"
           class="s-nav-item {{ request()->routeIs('links.*') ? 'active' : '' }}">
            <span class="s-icon"><i class="fas fa-link"></i></span>
            Link Saya
        </a>

        <a href="{{ route('analitik.index') }}"
           class="s-nav-item {{ request()->routeIs('analitik.*') ? 'active' : '' }}">
            <span class="s-icon"><i class="fas fa-chart-bar"></i></span>
            Analitik
        </a>

        <a href="{{ route('qrcode.show') }}"
           class="s-nav-item {{ request()->routeIs('qrcode.show') ? 'active' : '' }}">
            <span class="s-icon"><i class="fas fa-qrcode"></i></span>
            QR Code
        </a>

        <a href="{{ route('products.manage') }}"
           class="s-nav-item {{ request()->routeIs('products.manage') ? 'active' : '' }}">
            <span class="s-icon"><i class="fas fa-shopping-bag"></i></span>
            Produk
        </a>

        <a href="{{ route('orders.index') }}"
           class="s-nav-item {{ request()->routeIs('orders.*') ? 'active' : '' }}">
            <span class="s-icon"><i class="fas fa-box-open"></i></span>
            Pesanan
        </a>

        <a href="{{ route('transactions.history') }}"
           class="s-nav-item {{ request()->routeIs('transactions.*') ? 'active' : '' }}">
            <span class="s-icon"><i class="fas fa-file-lines"></i></span>
            Riwayat Transaksi
        </a>

        <a href="{{ route('payment.accounts.index') }}"
           class="s-nav-item {{ request()->routeIs('payment.accounts.*') ? 'active' : '' }}">
            <span class="s-icon"><i class="fas fa-building-columns"></i></span>
            Rekening Bank
        </a>

        <a href="{{ route('settings.shipping') }}"
           class="s-nav-item {{ request()->routeIs('settings.shipping') ? 'active' : '' }}">
            <span class="s-icon"><i class="fas fa-truck"></i></span>
            Pengaturan Pengiriman
        </a>

        <div class="s-divider"></div>
        <div class="s-label">Preferensi</div>

        <div>
            <div class="s-theme-toggle" id="sThemeToggle">
                <div class="s-left">
                    <span class="s-icon"><i class="fas fa-swatchbook"></i></span>
                    Tema
                </div>
                <i class="fas fa-chevron-down s-arrow"></i>
            </div>
            <div class="s-theme-menu" id="sThemeMenu">
                <button class="s-theme-opt" data-theme="light">
                    <i class="fas fa-sun"></i> Terang
                    <i class="fas fa-check check-icon"></i>
                </button>
                <button class="s-theme-opt" data-theme="dark">
                    <i class="fas fa-moon"></i> Gelap
                    <i class="fas fa-check check-icon"></i>
                </button>
            </div>
        </div>
    </nav>

    <!-- Logout -->
    <div class="s-footer">
        <a href="{{ route('logout') }}"
           class="s-logout"
           onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
            <span class="s-icon"><i class="fas fa-right-from-bracket"></i></span>
            Keluar
        </a>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display:none;">
            @csrf
        </form>
    </div>

</aside>

<!-- ── CONTENT ───────────────────────────────────────────────── -->
<div class="main-wrap">
    <main class="content-pad">
        @yield('content')
    </main>
</div>

<script src="{{ asset('js/dashboard.js') }}"></script>
<script src="{{ asset('js/darkmode.js') }}"></script>

@include('components.notification-bell')
@stack('modals')

<script>
window.addEventListener('load', function () {
    document.body.classList.remove('preload');
});

document.addEventListener('DOMContentLoaded', function () {
    var hamburger = document.getElementById('mHamburger');
    var sidebar   = document.getElementById('sSidebar');
    var overlay   = document.getElementById('sOverlay');

    function openSidebar()  {
        sidebar.classList.add('active');
        overlay.classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function closeSidebar() {
        sidebar.classList.remove('active');
        overlay.classList.remove('active');
        document.body.style.overflow = '';
    }

    hamburger && hamburger.addEventListener('click', function (e) {
        e.stopPropagation();
        sidebar.classList.contains('active') ? closeSidebar() : openSidebar();
    });

    overlay && overlay.addEventListener('click', closeSidebar);

    sidebar.querySelectorAll('a.s-nav-item, a.s-profile-link, a.s-logout').forEach(function (el) {
        el.addEventListener('click', function () {
            if (window.innerWidth <= 768) closeSidebar();
        });
    });

    window.addEventListener('resize', function () {
        if (window.innerWidth > 768) closeSidebar();
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') closeSidebar();
    });

    var themeToggle = document.getElementById('sThemeToggle');
    var themeMenu   = document.getElementById('sThemeMenu');

    themeToggle && themeToggle.addEventListener('click', function () {
        themeMenu.classList.toggle('open');
        themeToggle.classList.toggle('open');
    });
});
</script>
</body>
</html>