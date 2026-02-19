<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payou.id - Satu Link untuk Semua Kebutuhan Bisnis UMKM</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/landing.css') }}">
    <script src="{{ asset('js/landing.js') }}"></script>
    <img src="{{ asset('images/logo.png') }}">

</head>
<body>
    
    <nav>
        <div class="nav-container">
            <a href="#" class="logo"><img src="../img/icon.png" alt="payou.id" srcset=""></a>
            <div class="nav-buttons" id="navButtons">
                <a href="{{ route('service') }}" class="btn btn-secondary">Service</a>
                <a href="{{ route('faq') }}" class="btn btn-secondary">FAQ</a>
                <a href="/login" class="btn btn-secondary">Masuk</a>
                <a href="/register" class="btn btn-primary">Daftar Gratis</a>
            </div>
            <button class="hamburger" id="hamburger" aria-label="Menu">
                <span></span>
                <span></span>
                <span></span>
            </button>
        </div>
    </nav>

    
    <section class="hero">
        <div class="hero-container">
            <div class="hero-content">
                <span class="hero-badge">✨ Untuk UMKM Indonesia</span>
                <h1>Satu Link untuk <span class="highlight">Semua Kebutuhan</span> Bisnis Online UMKM</h1>
                <p>Kelola link bio, terima pembayaran, tampilkan katalog produk, dan hubungkan semua sosial media dalam satu halaman profesional</p>
                <div class="hero-cta">
                    <a href="#" class="btn btn-primary btn-large">Daftar Gratis</a>
                    <a href="#" class="btn btn-secondary btn-large">Lihat Contoh</a>
                </div>
            </div>
            <div class="hero-visual">
                <div class="img1"><img src="../img/Your paragraph text (2).png" alt="" srcset=""></div>
                <div class="img11"><img src="../img/Your paragraph text (2).png" alt="" srcset=""></div>
                <div class="img2"><img src="../img/Gemini_Generated_Image_i7n9e1i7n9e1i7n9.png" alt="" srcset=""></div>
                <div class="img3">
                    <img src="../img/Your paragraph text (4).png" alt="" srcset="">
                    <img src="../img/Your paragraph text (5).png" alt="" srcset="">
                    <img src="../img/Your paragraph text (6).png" alt="" srcset="">
                
                </div>
            </div>
        </div>
    </section>

    
<section class="trust">
    <div class="trust-container">
        <div class="trust-header">
            <h2>Dipercaya UMKM Lokal Indonesia</h2>
            <p>Mudah, aman, dan cepat untuk bisnis Anda</p>
        </div>
        <div class="trust-stats">
            <div class="stat-card">
                <div class="stat-number" id="counter-1">0</div>
                <div class="stat-label">UMKM Aktif</div>
            </div>
            <div class="stat-card">
                <div class="stat-number" id="counter-2">0</div>
                <div class="stat-label">Transaksi Berhasil</div>
            </div>
            <div class="stat-card">
                <div class="stat-number" id="counter-3">0%</div>
                <div class="stat-label">Uptime</div>
            </div>
        </div>
        <div class="trust-logos">
            <div class="trust-logos-header">
                <p class="trust-tagline">Digunakan oleh berbagai jenis UMKM</p>
                <p class="trust-subtitle">Berbagai sektor bisnis telah mempercayai layanan kami</p>
            </div>
            <div class="logos-grid">
                <div class="umkm-card fashion">
                    <div class="umkm-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M20.59 13.41L13.42 20.58C12.05 21.95 10.05 21.95 8.68 20.58L3.42 15.32C2.05 13.95 2.05 11.95 3.42 10.58L10.59 3.41C11.96 2.04 13.96 2.04 15.33 3.41L20.59 8.67C21.96 10.04 21.96 12.04 20.59 13.41Z"/>
                            <path d="M12 8L8 12M16 12L12 16"/>
                        </svg>
                    </div>
                    <span class="umkm-name">Toko Fashion</span>
                    <span class="umkm-count">2.5K+ UMKM</span>
                </div>
                
                <div class="umkm-card kuliner">
                    <div class="umkm-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M18 8h1a4 4 0 0 1 0 8h-1M2 8h16v9a4 4 0 0 1-4 4H6a4 4 0 0 1-4-4V8ZM6 1v3M10 1v3M14 1v3"/>
                        </svg>
                    </div>
                    <span class="umkm-name">Kuliner</span>
                    <span class="umkm-count">3.8K+ UMKM</span>
                </div>
                
                <div class="umkm-card kerajinan">
                    <div class="umkm-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M14 7a2 2 0 1 0-4 0v7a2 2 0 1 0 4 0V7Z"/>
                            <path d="M7 14a2 2 0 1 0 0-4 2 2 0 0 0 0 4Z"/>
                            <path d="M17 14a2 2 0 1 0 0-4 2 2 0 0 0 0 4Z"/>
                            <path d="M7 7h10v10H7z"/>
                        </svg>
                    </div>
                    <span class="umkm-name">Kerajinan</span>
                    <span class="umkm-count">1.9K+ UMKM</span>
                </div>
                
                <div class="umkm-card jasa">
                    <div class="umkm-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M20 12a8 8 0 1 1-16 0 8 8 0 0 1 16 0Z"/>
                            <path d="M12 8v8M8 12h8"/>
                        </svg>
                    </div>
                    <span class="umkm-name">Jasa</span>
                    <span class="umkm-count">2.3K+ UMKM</span>
                </div>
            </div>
        </div>
    </div>
</section>

   
    <section class="testimonials">
        <div class="testimonials-header">
            <h2>Lihat Apa Kata UMKM</h2>
            <p>Tidak perlu banyak aplikasi, Payou.id menyatukan semuanya.</p>
        </div>

        <div class="testimonial-columns">
            
            <div class="testimonial-card">
                <div class="testimonial-user">
                    <img src="../img/profil default instagram.jpeg" alt="user">
                    <div>
                        <p class="name">Toko Berkah</p>
                        <p class="username">@tokoberkah</p>
                    </div>
                </div>
                <p class="testimonial-text">
                     Fitur-fitur di Payou.id sangat membantu bisnis saya. 
                    Sekarang pelanggan bisa langsung bayar tanpa ribet chat bolak-balik.
                </p>
                </p>
            </div>

            <div class="testimonial-card">
                <div class="testimonial-user">
                    <img src="../img/profil default instagram.jpeg" alt="user">
                    <div>
                        <p class="name">Dapur Umma</p>
                        <p class="username">@dapurumma</p>
                    </div>
                </div>
                <p class="testimonial-text">
                    Sejak pakai Payou.id, jualan online jadi lebih rapi dan profesional.
                    Link bio saya sekarang kelihatan jauh lebih meyakinkan.
                </p>
            </div>

            <div class="testimonial-card">
                <div class="testimonial-user">
                    <img src="../img/profil default instagram.jpeg" alt="user">
                    <div>
                        <p class="name">Hijab Cantika</p>
                        <p class="username">@hijabcantika</p>
                    </div>
                </div>
                <p class="testimonial-text">
                    Payou.id bikin usaha saya kelihatan lebih profesional.
                </p>
            </div>

            <div class="testimonial-card">
                <div class="testimonial-user">
                    <img src="../img/profil default instagram.jpeg" alt="user">
                    <div>
                        <p class="name">Kreasi Kayu</p>
                        <p class="username">@kreasikayu</p>
                    </div>
                </div>
                <p class="testimonial-text">
                    Semua link penting ada di satu halaman. 
                    Pelanggan gampang akses katalog dan langsung order.
                </p>
            </div>

            <div class="testimonial-card">
                <div class="testimonial-user">
                    <img src="../img/profil default instagram.jpeg" alt="user">
                    <div>
                        <p class="name">Warung Kopi Lokal</p>
                        <p class="username">@kopilokal</p>
                    </div>
                </div>
                <p class="testimonial-text">
                    Praktis banget! Cukup satu link untuk promo, katalog, dan pembayaran.
                    Sangat cocok buat UMKM.
                </p>
            </div>

            <div class="testimonial-card">
                <div class="testimonial-user">
                    <img src="../img/profil default instagram.jpeg" alt="user">
                    <div>
                        <p class="name">Jasa Digital Nusantara</p>
                        <p class="username">@jasadigital</p>
                    </div>
                </div>
                <p class="testimonial-text">
                    Sejauh ini Payou.id benar-benar membantu workflow bisnis saya.
                    Simple, cepat, dan mudah digunakan.
                </p>
            </div>
        </div>
    </section>

   
    <section class="features">
        <div class="features-container">
            <div class="features-header">
                <h2>Semua yang Bisnis Anda Butuhkan</h2>
                <p>Kelola bisnis online dengan lebih efisien</p>
            </div>
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">🔗</div>
                    <h3>Satu Link untuk Semua</h3>
                    <p>Ganti banyak link di bio Instagram, TikTok, atau WhatsApp dengan satu link payou.id yang mencakup semuanya</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">💳</div>
                    <h3>Terima Pembayaran</h3>
                    <p>Pelanggan bisa langsung bayar dari halaman Anda tanpa perlu chat bolak-balik untuk nomor rekening</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">📦</div>
                    <h3>Katalog Produk Simpel</h3>
                    <p>Tampilkan produk jualan dengan foto dan harga dalam tampilan yang rapi dan profesional</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">💬</div>
                    <h3>Terhubung ke Semua Channel</h3>
                    <p>Hubungkan WhatsApp, Instagram, TikTok, dan platform lainnya dalam satu tempat untuk memudahkan pelanggan</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">📊</div>
                    <h3>Laporan & Statistik</h3>
                    <p>Pantau performa bisnis Anda lewat data transaksi dan aktivitas pelanggan secara ringkas dan jelas</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">🔒</div>
                    <h3>Aman & Terpercaya</h3>
                    <p>Sistem pembayaran dan data pelanggan dilindungi dengan keamanan berlapis untuk kenyamanan bisnis Anda</p>
                </div>
            </div>
        </div>
    </section>

    <section class="value-strip">
        <div class="value-container">
            <h3>Dipercaya oleh Ribuan UMKM di Indonesia</h3>
            <p>Satu halaman untuk link, produk, dan pembayaran. Tanpa ribet.</p>
        </div>
    </section>

    
    <footer class="footer">
        <div class="footer-wrapper">
            <a href="/" class="logo">
                <img src="../img/icon.png" alt="payou.id">
            </a>
            <div class="footer-links">
                <a href="{{ route('about') }}">About Us</a>
                <a href="{{ route('contact') }}">Contact Us</a>
                <a href="#">Terms & Conditions</a>
                <a href="#">Privacy</a>
            </div>
        </div>
        <div class="footer-bottom">
            <p>© 2025 payou.id - Satu Link untuk Semua Kebutuhan Bisnis UMKM</p>
        </div>
    </footer>

    <script src="script.js"></script>
</body>
</html>