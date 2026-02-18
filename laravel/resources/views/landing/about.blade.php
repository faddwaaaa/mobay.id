<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - Payou.id</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/landing.css') }}">
</head>
<body>

<nav>
    <div class="nav-container">
        <a href="/" class="logo">
            <img src="../img/icon.png" alt="payou.id">
        </a>
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
            <span class="hero-badge">💙 Tentang Kami</span>
            <h1>Misi Kami Membantu <span class="highlight">UMKM Indonesia</span> Go Digital</h1>
            <p>
                Payou.id hadir sebagai solusi all-in-one untuk membantu pelaku usaha
                mengelola link bio, pembayaran, dan katalog produk dalam satu halaman profesional.
            </p>
        </div>
    </div>
</section>

<section class="vision-section">
    <div class="vision-container">
        <div class="vision-label">VISI PAYOU.ID</div>
        <h2>Membangun Masa Depan Digital UMKM Indonesia</h2>
        <p>
            Menjadi platform digital terpercaya yang membantu UMKM Indonesia
            berkembang, terlihat profesional, dan siap bersaing di era modern.
        </p>
    </div>
</section>

<div class="testimonial-columns">

    <div class="testimonial-card">
        <div class="mission-icon">
            <i class="fa-solid fa-rocket"></i>
        </div>
        <h4>Akses Teknologi Mudah</h4>
        <p>
            Memberikan akses teknologi yang sederhana dan terjangkau untuk semua pelaku usaha.
        </p>
    </div>

    <div class="testimonial-card">
        <div class="mission-icon">
            <i class="fa-solid fa-briefcase"></i>
        </div>
        <h4>Tampilan Profesional</h4>
        <p>
            Membantu meningkatkan kepercayaan pelanggan melalui tampilan bisnis yang modern.
        </p>
    </div>

    <div class="testimonial-card">
        <div class="mission-icon">
            <i class="fa-solid fa-chart-line"></i>
        </div>
        <h4>Mendorong Pertumbuhan</h4>
        <p>
            Mendukung pertumbuhan UMKM agar lebih siap bersaing di era digital.
        </p>
    </div>
</div>


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

<style>
    /* ================= ABOUT PAGE ================= */

.hero {
    padding: 100px 0 80px;
    background: linear-gradient(135deg, #eff6ff, #ffffff);
    text-align: center
}

.hero-badge {
    display: inline-block;
    background: #dbeafe;
    color: #2563eb;
    padding: 6px 14px;
    border-radius: 30px;
    font-size: 14px;
    font-weight: 600;
    margin-bottom: 20px;
}

.hero h1 {
    font-size: 42px;
    font-weight: 800;
    line-height: 1.3;
    color: #0f172a;
}

/* ================= ABOUT HERO CENTER FIX ================= */

.hero-container {
    display: flex;
    justify-content: center;
    align-items: center;
    text-align: center;
}

.hero-content {
    max-width: 800px;
    margin: 0 auto;
}

.hero-content h1 {
    text-align: center;
}

.hero-content p {
    text-align: center;
    margin-left: auto;
    margin-right: auto;
}


.highlight {
    color: #2563eb;
}

.hero p {
    max-width: 650px;
    margin: 20px auto 0;
    font-size: 17px;
    color: #64748b;
}


/* Section spacing biar konsisten */
.features,
.testimonials,
.value-strip {
    padding: 100px 0;
}

/* ================= VISION SECTION ================= */

.vision-section {
    padding: 140px 20px;
    background: linear-gradient(135deg, #1e3a8a, #2563eb);
    text-align: center;
    color: white;
}

.vision-container {
    max-width: 850px;
    margin: 0 auto;
}

.vision-label {
    font-size: 13px;
    letter-spacing: 2px;
    text-transform: uppercase;
    opacity: 0.8;
    margin-bottom: 15px;
}

.vision-section h2 {
    font-size: 36px;
    font-weight: 800;
    margin-bottom: 20px;
}

.vision-section p {
    font-size: 18px;
    line-height: 1.7;
    opacity: 0.95;
}

/* ================= MISI SECTION ================= */

.testimonials {
    background: #f8fafc;
    padding: 120px 20px;
}

.testimonials-header h2 {
    font-size: 36px;
    font-weight: 800;
    margin-bottom: 10px;
}

.testimonial-columns {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 35px;
    max-width: 1100px;
    margin: 70px auto 0;
}

.testimonial-card {
    background: white;
    padding: 40px;
    border-radius: 20px;
    text-align: center;
    box-shadow: 0 15px 35px rgba(37, 99, 235, 0.08);
    transition: all 0.4s ease;
    border: 1px solid #e2e8f0;
}

.testimonial-card:hover {
    transform: translateY(-12px);
    box-shadow: 0 25px 50px rgba(37, 99, 235, 0.18);
    border: 1px solid #2563eb;
}

.mission-icon {
    font-size: 38px;
    margin-bottom: 20px;
}

.testimonial-card h4 {
    font-size: 20px;
    font-weight: 700;
    margin-bottom: 12px;
    color: #0f172a;
}

.testimonial-card p {
    color: #64748b;
    font-size: 16px;
    line-height: 1.6;
}

/* Card sedikit lebih clean */
.feature-card,
.testimonial-card {
    border-radius: 16px;
    transition: 0.3s ease;
}

.feature-card:hover,
.testimonial-card:hover {
    transform: translateY(-6px);
}


/* Responsive */
@media (max-width: 768px) {

    .hero h1 {
        font-size: 30px;
    }

    .features,
    .testimonials,
    .value-strip {
        padding: 70px 0;
    }
}

</style>
</body>
</html>