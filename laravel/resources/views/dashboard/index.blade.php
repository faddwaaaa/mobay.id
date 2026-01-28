@php
    use Illuminate\Support\Str;

    $user = Auth::user();
    $userSlug = $user->username ?? Str::slug($user->name);
@endphp

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard | Payou.id</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
</head>
<body>
    <div class="dashboard-wrapper">
        <!-- TOP NAVBAR -->
        <nav class="top-navbar">
            <div class="navbar-left">
                <div class="menu-toggle" id="menuToggle">
                    <i class="fas fa-bars"></i>
                </div>
                <div class="logo">
                    <a href="#" class="logo-icon"><img src="../img/icon.png" alt="payou.id"></a>
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
                
                {{-- <div class="wallet-balance">
                    <div class="wallet-icon">
                        <i class="fas fa-wallet"></i>
                    </div>
                    <div class="wallet-info">
                        <span class="balance-label">Saldo</span>
                        <span class="balance-amount">Rp 2.450.000</span>
                    </div>
                    <button class="btn-topup">
                        <i class="fas fa-plus"></i>
                    </button>
                </div> --}}
                
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
                        <a href="#"><i class="fas fa-user"></i> Profil Saya</a>
                        <a href="#"><i class="fas fa-cog"></i> Pengaturan</a>
                        <a href="#"><i class="fas fa-question-circle"></i> Bantuan</a>
                        <div class="divider"></div>
                        <a href="{{ route('logout') }}"
                        class="logout"
                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <i class="fas fa-sign-out-alt"></i> Keluar
                        </a>

                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                    </div>
                </div>
            </div>
        </nav>

        <!-- MAIN CONTENT -->
        <div class="main-container">
            <!-- SIDEBAR -->
            <aside class="sidebar" id="sidebar">
                <nav class="sidebar-nav">
                    <a href="#" class="nav-item active">
                        <i class="fas fa-home"></i>
                        <span>Dashboard</span>
                    </a>
                    <a href="#" class="nav-item">
                        <i class="fas fa-link"></i>
                        <span>Link Saya</span>
                        <span class="nav-badge">12</span>
                    </a>
                    <a href="#" class="nav-item">
                        <i class="fas fa-chart-bar"></i>
                        <span>Analitik</span>
                    </a>
                    <a href="#" class="nav-item">
                        <i class="fas fa-qrcode"></i>
                        <span>QR Code</span>
                    </a>
                    <a href="#" class="nav-item">
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

            <!-- CONTENT AREA -->
            <main class="content-area">
                <!-- PAGE HEADER -->
                <div class="page-header">
                    <div>
                        <h1>Dashboard</h1>
                        <p class="subtitle">
                            Selamat datang kembali, <strong>{{ Auth::user()->name }}</strong> 👋
                        </p>
                    </div>
                    <button class="btn-create-link">
                        <i class="fas fa-plus-circle"></i>
                        <span>Buat Link Baru</span>
                    </button>
                </div>

                <!-- STATS CARDS -->
                <div class="stats-cards">

                    <!-- TOTAL KLIK -->
                    <div class="stat-card">
                        <div class="stat-header">
                            <h3>Total Klik</h3>
                        </div>
                        <div class="stat-value">
                            {{ number_format($totalClicks) }}
                        </div>
                        <div class="stat-footer">
                            <i class="fas fa-calendar"></i>
                            <span>Semua waktu</span>
                        </div>
                    </div>

                    <!-- TOTAL LINK -->
                    <div class="stat-card">
                        <div class="stat-header">
                            <h3>Total Link</h3>
                        </div>
                        <div class="stat-value">
                            {{ $totalLinks }}
                        </div>
                        <div class="stat-footer">
                            <i class="fas fa-check-circle"></i>
                            <span>{{ $activeLinks }} aktif</span>
                        </div>
                    </div>

                    <!-- SALDO (DUMMY TAPI AMAN) -->
                    <div class="stat-card">
                        <div class="stat-header">
                            <h3>Saldo Tersedia</h3>
                        </div>
                        <div class="stat-value">
                            Rp 0
                        </div>
                        <div class="stat-footer">
                            <i class="fas fa-wallet"></i>
                            <span>Belum tersedia</span>
                        </div>
                    </div>

                    <!-- KONVERSI (DUMMY REALISTIS) -->
                    <div class="stat-card">
                        <div class="stat-header">
                            <h3>Konversi</h3>
                        </div>
                        <div class="stat-value">
                            0%
                        </div>
                        <div class="stat-footer">
                            <i class="fas fa-shopping-cart"></i>
                            <span>0 transaksi</span>
                        </div>
                    </div>

                </div>

                <!-- RECENT LINKS -->
                <div class="content-section">
                    <div class="section-header">
                        <h2><i class="fas fa-link"></i> Link Terbaru</h2>
                        <a href="#" class="view-all">Lihat Semua <i class="fas fa-arrow-right"></i></a>
                    </div>
                    
                    <div class="links-grid">
                    @forelse($links as $link)
                        <div class="link-card">
                            <div class="link-card-header">
                                <div class="link-icon">
                                    <i class="fas fa-link"></i>
                                </div>
                                <div class="link-status {{ $link->is_active ? 'active' : 'inactive' }}"></div>
                            </div>

                            <h3>{{ $link->title }}</h3>
                            <p class="link-description">{{ $link->url }}</p>

                            <div class="link-url">
                                <span>payou.id/{{ $userSlug }}/{{ $link->slug }}</span>
                                <button class="copy-btn"
                                    data-url="{{ url($userSlug.'/'.$link->slug) }}">
                                    <i class="far fa-copy"></i>
                                </button>
                            </div>

                            <div class="link-stats">
                                <div class="stat">
                                    <i class="fas fa-mouse-pointer"></i>
                                    <span>{{ $link->clicks_count }} klik</span>
                                </div>
                            </div>

                            <div class="link-actions">
                                <button class="btn-action edit"><i class="fas fa-edit"></i></button>
                                <button class="btn-action qr"><i class="fas fa-qrcode"></i></button>
                                <button class="btn-action analytics"><i class="fas fa-chart-bar"></i></button>
                            </div>
                        </div>
                    @empty
                        <p>Belum ada link.</p>
                    @endforelse
                    </div>
                </div>

                <!-- QUICK ACTIONS & ANALYTICS -->
                <div class="two-column-section">
                    <div class="column">
                        <div class="content-section">
                            <div class="section-header">
                                <h2><i class="fas fa-bolt"></i> Aksi Cepat</h2>
                            </div>
                            <div class="quick-actions">
                                <button class="quick-action-btn">
                                    <div class="action-icon">
                                        <i class="fas fa-qrcode"></i>
                                    </div>
                                    <span>Buat QR Code</span>
                                </button>
                                <button class="quick-action-btn">
                                    <div class="action-icon">
                                        <i class="fas fa-paint-brush"></i>
                                    </div>
                                    <span>Ubah Tema</span>
                                </button>
                                <button class="quick-action-btn">
                                    <div class="action-icon">
                                        <i class="fas fa-file-export"></i>
                                    </div>
                                    <span>Export Data</span>
                                </button>
                                <button class="quick-action-btn">
                                    <div class="action-icon">
                                        <i class="fas fa-share-alt"></i>
                                    </div>
                                    <span>Bagikan Halaman</span>
                                </button>
                            </div>
                        </div>
                        
                        <div class="content-section">
                            <div class="section-header">
                                <h2><i class="fas fa-money-bill-wave"></i> Saldo & Transaksi</h2>
                            </div>
                            <div class="balance-overview">
                                <div class="total-balance">
                                    <h3>Total Saldo</h3>
                                    <div class="balance-amount">Rp 2.450.000</div>
                                </div>
                                <div class="balance-actions">
                                    <button class="btn-balance topup">
                                        <i class="fas fa-plus-circle"></i>
                                        <span>Top Up</span>
                                    </button>
                                    <button class="btn-balance withdraw">
                                        <i class="fas fa-arrow-up"></i>
                                        <span>Tarik</span>
                                    </button>
                                </div>
                            </div>
                            <div class="recent-transactions">
                                <h4>Transaksi Terakhir</h4>
                                <div class="transaction-list">
                                    <div class="transaction-item">
                                        <div class="transaction-info">
                                            <div class="transaction-icon success">
                                                <i class="fas fa-arrow-down"></i>
                                            </div>
                                            <div>
                                                <p class="transaction-title">Top Up Saldo</p>
                                                <p class="transaction-date">26 Jan 2026</p>
                                            </div>
                                        </div>
                                        <div class="transaction-amount plus">+Rp 500.000</div>
                                    </div>
                                    <div class="transaction-item">
                                        <div class="transaction-info">
                                            <div class="transaction-icon">
                                                <i class="fas fa-arrow-up"></i>
                                            </div>
                                            <div>
                                                <p class="transaction-title">Tarik Dana</p>
                                                <p class="transaction-date">25 Jan 2026</p>
                                            </div>
                                        </div>
                                        <div class="transaction-amount minus">-Rp 250.000</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="column">
                        <div class="content-section">
                            <div class="section-header">
                                <h2><i class="fas fa-chart-line"></i> Performa 7 Hari Terakhir</h2>
                            </div>
                            <div class="chart-container">
                                <div class="chart-placeholder">
                                    <!-- Chart akan ditempatkan di sini -->
                                    <div class="chart-bars">
                                        @foreach ($data as $index => $value)
                                            @php
                                                $height = $maxClick > 0 ? ($value / $maxClick) * 100 : 0;
                                            @endphp
                                            <div class="chart-bar"
                                                style="height: {{ $height }}%;"
                                                data-day="{{ $labels[$index] }}"
                                                data-value="{{ $value }}">
                                            </div>
                                        @endforeach
                                    </div>
                                    <div class="chart-labels">
                                        @foreach ($labels as $label)
                                            <span>{{ $label }}</span>
                                        @endforeach
                                    </div>
                                </div>
                                <div class="chart-stats">
                                    <div class="chart-stat">
                                        <h4>Klik Tertinggi</h4>
                                        <p>{{ $maxClick }} klik</p>
                                    </div>

                                    <div class="chart-stat">
                                        <h4>Total Klik</h4>
                                        <p>{{ $totalClicks }} klik</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="content-section">
                            <div class="section-header">
                                <h2><i class="fas fa-users"></i> Demografi Pengunjung</h2>
                            </div>
                            <div class="demographics">
                                <div class="demo-item">
                                    <div class="demo-label">
                                        <i class="fas fa-male"></i>
                                        <span>Pria</span>
                                    </div>
                                    <div class="demo-bar">
                                        <div class="demo-fill" style="width: 65%; background-color: #8B5CF6;"></div>
                                    </div>
                                    <span class="demo-percent">65%</span>
                                </div>
                                <div class="demo-item">
                                    <div class="demo-label">
                                        <i class="fas fa-female"></i>
                                        <span>Wanita</span>
                                    </div>
                                    <div class="demo-bar">
                                        <div class="demo-fill" style="width: 35%; background-color: #F59E0B;"></div>
                                    </div>
                                    <span class="demo-percent">35%</span>
                                </div>
                                <div class="demo-item">
                                    <div class="demo-label">
                                        <i class="fas fa-map-marker-alt"></i>
                                        <span>Jakarta</span>
                                    </div>
                                    <div class="demo-bar">
                                        <div class="demo-fill" style="width: 45%; background-color: #10B981;"></div>
                                    </div>
                                    <span class="demo-percent">45%</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="{{ asset('js/dashboard.js') }}"></script>
</body>
</html>