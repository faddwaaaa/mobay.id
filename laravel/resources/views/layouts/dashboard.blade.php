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
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="{{ asset('css/profile.css') }}">
    <link rel="stylesheet" href="{{ asset('css/darkmode.css') }}">
    
    <style>
        /* ========== MOBILE FIXES ========== */
        @media (max-width: 768px) {
            /* Reset semua margin/padding yang membuat konten tertutup */
            body, html {
                overflow-x: hidden;
            }
            
            /* Navbar mobile fix - lebih kompak */
            .top-navbar {
                height: 56px !important;
                padding: 0 12px !important;
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                z-index: 1000;
                background: white;
                box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            }
            
            /* Menu toggle - pastikan muncul */
            .menu-toggle {
                display: flex !important;
                width: 40px;
                height: 40px;
                align-items: center;
                justify-content: center;
                cursor: pointer;
                margin-right: 8px;
                color: #475569;
            }
            
            /* Logo lebih kecil dan Sembunyikan logo di mobile */
            .logo {
                display: none !important;
            }
            
            /* Atau jika hanya ingin image yang hilang, teks tetap ada */
            .logo-icon img {
                display: none !important;
            }
            
            /* Sembunyikan search di mobile */
            .navbar-center {
                display: none !important;
            }
            
            /* Perbaiki navbar kanan untuk mobile */
            .navbar-right {
                gap: 8px !important;
            }
            
            .btn-upgrade span {
                display: none !important;
            }
            
            .btn-upgrade {
                padding: 6px 8px !important;
                min-width: auto !important;
            }
            
            .user-name {
                display: none !important;
            }
            
            /* ========== SIDEBAR FIX ========== */
            /* Sembunyikan sidebar default */
            .sidebar {
                display: none !important;
            }
            
            /* Buat sidebar mobile khusus */
            .sidebar.mobile-sidebar {
                display: block !important;
                position: fixed !important;
                top: 56px !important;
                left: -280px !important;
                width: 280px !important;
                height: calc(100vh - 56px) !important;
                z-index: 999 !important;
                background: white;
                box-shadow: 2px 0 10px rgba(0,0,0,0.1);
                transition: transform 0.3s ease;
                transform: translateX(-100%);
                overflow-y: auto;
            }
            
            .sidebar.mobile-sidebar.active {
                transform: translateX(0);
                left: 0 !important;
            }
            
            /* Main container reset */
            .main-container {
                margin-left: 0 !important;
                width: 100% !important;
                margin-top: 56px !important;
            }
            
            /* Content area fill full width */
            .content-area {
                width: 100% !important;
                padding: 16px !important;
                margin: 0 !important;
                min-height: calc(100vh - 56px);
                background: #f8fafc;
            }
            
            /* Overlay untuk sidebar */
            .sidebar-overlay {
                display: none;
                position: fixed;
                top: 56px;
                left: 0;
                right: 0;
                bottom: 0;
                background: rgba(0,0,0,0.5);
                z-index: 998;
                backdrop-filter: blur(2px);
            }
            
            .sidebar-overlay.active {
                display: block;
            }
            
            /* Atau jika Anda ingin navbar-left lebih kompak */
            .navbar-left {
                min-width: auto;
                justify-content: flex-start;
            }
            
            /* Style untuk sidebar mobile header */
            .mobile-sidebar-header {
                padding: 20px 16px;
                border-bottom: 1px solid #e2e8f0;
                background: #f8fafc;
            }
            
            .mobile-user-info {
                display: flex;
                align-items: center;
                gap: 12px;
            }
            
            .mobile-user-avatar {
                width: 48px;
                height: 48px;
                border-radius: 50%;
                overflow: hidden;
            }
            
            .mobile-user-avatar img {
                width: 100%;
                height: 100%;
                object-fit: cover;
            }
            
            .mobile-user-name {
                margin: 0;
                font-size: 16px;
                font-weight: 600;
                color: #0f172a;
            }
            
            .mobile-user-email {
                margin: 4px 0 0 0;
                font-size: 12px;
                color: #64748b;
            }
            
            /* Style untuk sidebar mobile footer */
            .mobile-sidebar-footer {
                margin-top: auto;
                padding: 16px;
                border-top: 1px solid #e2e8f0;
            }
            
            .logout-mobile {
                color: #ef4444 !important;
            }
            
            .logout-mobile:hover {
                background: #fef2f2 !important;
            }
        }
        
        /* Tablet - Sembunyikan logo jika perlu */
        @media (max-width: 1024px) and (min-width: 769px) {
            /* Jika ingin logo lebih kecil di tablet */
            .logo-icon img {
                height: 28px !important;
            }
            
            .menu-toggle {
                display: none !important;
            }
        }
        
        /* ========== DESKTOP ========== */
        @media (min-width: 769px) {
            .menu-toggle {
                display: none !important;
            }
            
            .sidebar-overlay {
                display: none !important;
            }
            
            .sidebar.mobile-sidebar {
                display: none !important;
            }
            
            /* Tampilkan sidebar desktop */
            .sidebar:not(.mobile-sidebar) {
                display: block !important;
            }
            
            /* Tampilkan logo di desktop */
            .logo {
                display: block !important;
            }
            
            .logo-icon img {
                display: block !important;
            }

            .tem{
                margin-left: 8px;
            }
        }
    </style>
</head>
<body class="preload">

<div class="dashboard-wrapper">
    
    <!-- Overlay untuk sidebar di mobile -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- ================= TOP NAVBAR ================= -->
    <nav class="top-navbar">
        <div class="navbar-left">
            <div class="menu-toggle" id="menuToggle">
                <i class="fas fa-bars"></i>
            </div>
            <div class="logo">
                <a href="{{ route('dashboard') }}" class="logo-icon">
                    <img src="{{ asset('img/icon.png') }}" alt="payou.id">
                </a>
            </div>
        </div>

        <div class="navbar-center">
            <div class="search-container">
                <i class="fas fa-search"></i>
                <input type="text" placeholder="Cari link, analitik, atau pengaturan...">
            </div>
        </div>

        <div class="navbar-right">
            <button class="btn-upgrade">
                <i class="fas fa-crown"></i>
                <span>Upgrade Premium</span>
            </button>

            <div class="notification-bell">
                <i class="fas fa-bell"></i>
                <span class="notification-dot"></span>
            </div>

            <div class="user-profile-dropdown">
                <div class="user-avatar">
                    @if ($avatar)
                        <img
                            src="{{ Str::startsWith($avatar, ['http://', 'https://'])
                                    ? $avatar
                                    : asset('storage/'.$avatar) }}"
                            alt="{{ Auth::user()->name }}"
                        >
                    @else
                        <img src="{{ asset('img/default-avatar.jpg') }}" alt="Default Avatar">
                    @endif
                </div>

                <div class="user-name">
                    {{ Auth::user()->name }}
                </div>

                <i class="fas fa-chevron-down"></i>

                <div class="dropdown-menu">
                    <a href="{{ route('dashboard.profile') }}">
                        <i class="fas fa-user"></i> Profil Saya
                    </a>
                    <a href="#"><i class="fas fa-cog"></i> Pengaturan</a>
                    <a href="#"><i class="fas fa-question-circle"></i> Bantuan</a>
                    <div class="divider"></div>
                    <a href="{{ route('logout') }}"
                       class="logout"
                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="fas fa-sign-out-alt"></i> Keluar
                    </a>

                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display:none;">
                        @csrf
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <!-- ================= MAIN CONTAINER ================= -->
    <div class="main-container">

        <!-- ================= SIDEBAR DESKTOP ================= -->
        <aside class="sidebar" id="sidebarDesktop">
            <nav class="sidebar-nav">
                <a href="{{ route('dashboard') }}"
                   class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="fas fa-home"></i>
                    <span>Dashboard</span>
                </a>
                <a href="{{ route('links.index') }}"
                    class="nav-item {{ request()->routeIs('links.*') ? 'active ' : '' }}">
                    <i class="fas fa-link"></i>
                    <span>Link Saya</span>
                </a>

                <a href="{{ route('analitik.index') }}" 
                    class="nav-item {{ request()->routeIs('analitik.show') ? 'active' : '' }}">
                    <i class="fas fa-chart-bar"></i>
                    <span>Analitik</span>
                </a>

                <a href="{{ route('qrcode.show') }}"
                class="nav-item {{ request()->routeIs('qrcode.show') ? 'active' : '' }}">
                    <i class="fas fa-qrcode"></i>
                    <span>QR Code</span>
                </a>

                <a href="{{ route('products.manage') }}"
                class="nav-item {{ request()->routeIs('products.manage') ? 'active' : '' }}">
                    <i class="fas fa-shopping-cart"></i>
                    <span>Produk</span>
                </a>

                <a href="#" class="nav-item">
                    <i class="fas fa-credit-card"></i>
                    <span>Pembayaran</span>
                </a>


                <!-- ================================================================================ -->
                <!-- ================================================================================ -->
                <!-- ================================================================================ -->
                <!-- ================================================================================ -->
                <!-- Theme Dropdown -->
                <!-- <div class="theme-dropdown">
                    <div class="nav-item theme-dropdown-toggle">
                        <div class="nav-left">
                            <i class="fas fa-paint-brush"></i>
                            <span class="tem"> Tema</span>
                        </div>
                        <i class="fas fa-chevron-down arrow"></i>
                    </div> -->
                <!-- ================================================================================ -->
                <!-- ================================================================================ -->
                <!-- ================================================================================ -->
                <!-- ================================================================================ -->


                    <div class="theme-dropdown-menu">
                        <button class="theme-option" data-theme="light">
                            <i class="fas fa-sun"></i>
                            <div class="theme-option-text">
                                <span class="theme-label">Terang</span>
                                <!-- <span class="theme-desc">Tampilan cerah untuk siang hari</span> -->
                            </div>
                            <i class="fas fa-check check-icon"></i>
                        </button>

                        <button class="theme-option" data-theme="dark">
                            <i class="fas fa-moon"></i>
                            <div class="theme-option-text">
                                <span class="theme-label">Gelap</span>
                                <!-- <span class="theme-desc">Tampilan nyaman untuk malam hari</span> -->
                            </div>
                            <i class="fas fa-check check-icon"></i>
                        </button>
                    </div>
                </div>

                <a href="#" class="nav-item">
                    <i class="fas fa-cog"></i>
                    <span>Pengaturan</span>
                </a>
            </nav>
        </aside>

        <!-- ================= SIDEBAR MOBILE ================= -->
        <aside class="sidebar mobile-sidebar" id="sidebarMobile">
            <nav class="sidebar-nav">
                <div class="mobile-sidebar-header">
                    <div class="mobile-user-info">
                        <div class="mobile-user-avatar">
                            @if ($avatar)
                                <img
                                    src="{{ Str::startsWith($avatar, ['http://', 'https://'])
                                            ? $avatar
                                            : asset('storage/'.$avatar) }}"
                                    alt="{{ Auth::user()->name }}"
                                >
                            @else
                                <img src="{{ asset('img/default-avatar.jpg') }}" alt="Default Avatar">
                            @endif
                        </div>
                        <div>
                            <p class="mobile-user-name">{{ Auth::user()->name }}</p>
                            <p class="mobile-user-email">{{ Auth::user()->email }}</p>
                        </div>
                    </div>
                </div>
                
                <a href="{{ route('dashboard') }}"
                   class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="fas fa-home"></i>
                    <span>Dashboard</span>
                </a>
                <a href="{{ route('links.index') }}"
                    class="nav-item {{ request()->routeIs('links.*') ? 'active ' : '' }}">
                    <i class="fas fa-link"></i>
                    <span>Link Saya</span>
                </a>

                <a href="{{ route('analitik.index') }}" 
                    class="nav-item {{ request()->routeIs('analitik.show') ? 'active' : '' }}">
                    <i class="fas fa-chart-bar"></i>
                    <span>Analitik</span>
                </a>

                <a href="{{ route('qrcode.show') }}"
                class="nav-item {{ request()->routeIs('qrcode.show') ? 'active' : '' }}">
                    <i class="fas fa-qrcode"></i>
                    <span>QR Code</span>
                </a>

                <a href="{{ route('products.manage') }}"
                class="nav-item {{ request()->routeIs('products.manage') ? 'active' : '' }}">
                    <i class="fas fa-box"></i>
                    <span>Produk</span>
                </a>

                <a href="#" class="nav-item">
                    <i class="fas fa-credit-card"></i>
                    <span>Pembayaran</span>
                </a>

                <!-- Theme Dropdown Mobile -->
                <div class="theme-dropdown">
                    <div class="nav-item theme-dropdown-toggle">
                        <div class="nav-left">
                            <i class="fas fa-paint-brush"></i>
                            <span>Tema</span>
                        </div>
                        <i class="fas fa-chevron-down arrow"></i>
                    </div>

                    <div class="theme-dropdown-menu">
                        <button class="theme-option" data-theme="light">
                            <i class="fas fa-sun"></i>
                            <div class="theme-option-text">
                                <span class="theme-label">Terang</span>
                                <span class="theme-desc">Tampilan cerah untuk siang hari</span>
                            </div>
                            <i class="fas fa-check check-icon"></i>
                        </button>

                        <button class="theme-option" data-theme="dark">
                            <i class="fas fa-moon"></i>
                            <div class="theme-option-text">
                                <span class="theme-label">Gelap</span>
                                <span class="theme-desc">Tampilan nyaman untuk malam hari</span>
                            </div>
                            <i class="fas fa-check check-icon"></i>
                        </button>
                    </div>
                </div>

                <a href="#" class="nav-item">
                    <i class="fas fa-cog"></i>
                    <span>Pengaturan</span>
                </a>
                
                <div class="mobile-sidebar-footer">
                    <a href="{{ route('logout') }}" 
                       class="nav-item logout-mobile"
                       onclick="event.preventDefault(); document.getElementById('logout-form-mobile').submit();">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Keluar</span>
                    </a>
                    <form id="logout-form-mobile" action="{{ route('logout') }}" method="POST" style="display:none;">
                        @csrf
                    </form>
                </div>
            </nav>
        </aside>

        <!-- ================= CONTENT ================= -->
        <main class="content-area">
            @yield('content')
        </main>

    </div>
</div>

<script src="{{ asset('js/dashboard.js') }}"></script>
<script src="{{ asset('js/darkmode.js') }}"></script>
<script>
// Remove preload class after page load to enable transitions
window.addEventListener('load', function() {
    document.body.classList.remove('preload');
});

// Mobile sidebar toggle
document.addEventListener('DOMContentLoaded', function() {
    const menuToggle = document.getElementById('menuToggle');
    const sidebarMobile = document.getElementById('sidebarMobile');
    const sidebarOverlay = document.getElementById('sidebarOverlay');
    
    if (menuToggle && sidebarMobile) {
        // Toggle sidebar mobile
        menuToggle.addEventListener('click', function(e) {
            e.stopPropagation();
            sidebarMobile.classList.toggle('active');
            if (sidebarOverlay) {
                sidebarOverlay.classList.toggle('active');
            }
            
            // Toggle body overflow
            if (sidebarMobile.classList.contains('active')) {
                document.body.style.overflow = 'hidden';
            } else {
                document.body.style.overflow = '';
            }
        });
        
        // Close sidebar when overlay is clicked
        if (sidebarOverlay) {
            sidebarOverlay.addEventListener('click', function() {
                sidebarMobile.classList.remove('active');
                sidebarOverlay.classList.remove('active');
                document.body.style.overflow = '';
            });
        }
        
        // Close sidebar when menu item is clicked
        const navItems = sidebarMobile.querySelectorAll('.nav-item:not(.theme-dropdown-toggle), .nav-sub-item');
        navItems.forEach(item => {
            item.addEventListener('click', function() {
                sidebarMobile.classList.remove('active');
                if (sidebarOverlay) {
                    sidebarOverlay.classList.remove('active');
                }
                document.body.style.overflow = '';
            });
        });
        
        // Close sidebar on window resize (if going to desktop)
        window.addEventListener('resize', function() {
            if (window.innerWidth > 768) {
                sidebarMobile.classList.remove('active');
                if (sidebarOverlay) {
                    sidebarOverlay.classList.remove('active');
                }
                document.body.style.overflow = '';
            }
        });
        
        // Close sidebar with ESC key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && sidebarMobile.classList.contains('active')) {
                sidebarMobile.classList.remove('active');
                if (sidebarOverlay) {
                    sidebarOverlay.classList.remove('active');
                }
                document.body.style.overflow = '';
            }
        });
    }
});
</script>
</body>
</html>