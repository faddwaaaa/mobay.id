@php
    use Illuminate\Support\Str;

    $user = Auth::user();
    $userSlug = $user->username ?? Str::slug($user->name);
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
</head>
<body>

<div class="dashboard-wrapper">

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
                    @if(Auth::user()->avatar)
                        <img src="{{ Auth::user()->avatar }}" alt="{{ Auth::user()->name }}">
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

        <!-- ================= SIDEBAR ================= -->
        <aside class="sidebar" id="sidebar">
            <nav class="sidebar-nav">
                <a href="{{ route('dashboard') }}"
                   class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="fas fa-home"></i>
                    <span>Dashboard</span>
                </a>
                <a href="{{ route('links.index') }}"
                    class="nav-item {{ request()->routeIs('links.*') ? 'active' : '' }}">
                    <i class="fas fa-link"></i>
                    <span>Link Saya</span>
                </a>


                <a href="{{ route('analitik.show') }}" 
                    class="nav-item {{ request()->routeIs('analitik.show') ? 'active' : '' }}">
                    <i class="fas fa-chart-bar"></i>
                    <span>Analitik</span>
                </a>

                <a href="{{ route('qrcode.show') }}"
                class="nav-item {{ request()->routeIs('qrcode.show') ? 'active' : '' }}">
                    <i class="fas fa-qrcode"></i>
                    <span>QR Code</span>
                </a>


                <a href="{{ route('products.create') }}"
                class="nav-item {{ request()->routeIs('products.*') ? 'active' : '' }}">
                    <i class="fas fa-shopping-cart"></i>
                    <span>Produk</span>
                </a>


                <a href="#" class="nav-item">
                    <i class="fas fa-credit-card"></i>
                    <span>Pembayaran</span>
                </a>

                <a href="#" class="nav-item">
                    <i class="fas fa-paint-brush"></i>
                    <span>Tema</span>
                </a>

                <a href="#" class="nav-item">
                    <i class="fas fa-cog"></i>
                    <span>Pengaturan</span>
                </a>
            </nav>

            <div class="sidebar-promo">
                <div class="promo-icon">
                    <i class="fas fa-rocket"></i>
                </div>
                <h4>Tingkatkan Bisnis Anda</h4>
                <p>Upgrade ke Premium untuk fitur lebih lengkap</p>
                <button class="btn-promo">Upgrade Sekarang</button>
            </div>
        </aside>

        <!-- ================= CONTENT ================= -->
        <main class="content-area">
            @yield('content')
        </main>

    </div>
</div>

<script src="{{ asset('js/dashboard.js') }}"></script>
</body>
</html>
