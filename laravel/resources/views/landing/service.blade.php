<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Layanan Kami - Payou.id</title>

    <!-- Font Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- CSS dari Landing (untuk navbar & footer) -->
    <link rel="stylesheet" href="{{ asset('css/landing.css') }}">

    <!-- Tailwind CSS (untuk isi service) -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50">

<!-- ================= NAVBAR ================= -->
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


<!-- Main Content -->
<div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-purple-50 py-12 px-4 mt-20">

    <div class="max-w-6xl mx-auto">

        <!-- Header -->
        <div class="text-center mb-12">
            <div class="inline-block p-3 bg-blue-100 rounded-full mb-4">
                <svg class="w-12 h-12 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
            </div>
            <h1 class="text-4xl font-bold text-gray-900 mb-4">
                Layanan Kami
            </h1>
            <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                Dapatkan bantuan profesional untuk mengembangkan bisnis digital Anda. 
                Tim kami siap membantu 24/7!
            </p>
        </div>

        <!-- Services Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-12">
            
            <!-- Service 1 -->
            <div class="service-card group">
                <div class="service-icon-wrapper bg-gradient-to-br from-blue-500 to-blue-600">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                    </svg>
                </div>
                <h3 class="service-title">Custom Link Bio</h3>
                <p class="service-description">
                    Desain link bio yang unik dan profesional sesuai brand Anda. Maksimalkan konversi dengan tampilan yang menarik.
                </p>
                <ul class="service-features">
                    <li>✓ Design Custom</li>
                    <li>✓ Unlimited Links</li>
                    <li>✓ Analytics Dashboard</li>
                    <li>✓ Free Revisi</li>
                </ul>
            </div>

            <!-- Service 2 -->
            <div class="service-card group">
                <div class="service-icon-wrapper bg-gradient-to-br from-green-500 to-green-600">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                    </svg>
                </div>
                <h3 class="service-title">Setup Toko Digital</h3>
                <p class="service-description">
                    Kami bantu setup toko digital Anda dari nol sampai siap jualan. Upload produk, setting harga, dan integrasi pembayaran.
                </p>
                <ul class="service-features">
                    <li>✓ Upload 50+ Produk</li>
                    <li>✓ Design Banner</li>
                    <li>✓ Setup Payment Gateway</li>
                    <li>✓ Training 1 Jam</li>
                </ul>
            </div>

            <!-- Service 3 -->
            <div class="service-card group">
                <div class="service-icon-wrapper bg-gradient-to-br from-purple-500 to-purple-600">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                    </svg>
                </div>
                <h3 class="service-title">Content Creation</h3>
                <p class="service-description">
                    Butuh konten berkualitas? Kami siap buat copywriting, design grafis, dan video promosi untuk bisnis Anda.
                </p>
                <ul class="service-features">
                    <li>✓ Copywriting Produk</li>
                    <li>✓ Design Feed Instagram</li>
                    <li>✓ Video Promosi</li>
                    <li>✓ Caption & Hashtag</li>
                </ul>
            </div>

            <!-- Service 4 -->
            <div class="service-card group">
                <div class="service-icon-wrapper bg-gradient-to-br from-yellow-500 to-orange-600">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                    </svg>
                </div>
                <h3 class="service-title">SEO & Digital Marketing</h3>
                <p class="service-description">
                    Tingkatkan visibility online Anda dengan strategi SEO dan digital marketing yang terbukti efektif.
                </p>
                <ul class="service-features">
                    <li>✓ Keyword Research</li>
                    <li>✓ On-Page SEO</li>
                    <li>✓ Social Media Ads</li>
                    <li>✓ Monthly Report</li>
                </ul>
            </div>

            <!-- Service 5 -->
            <div class="service-card group">
                <div class="service-icon-wrapper bg-gradient-to-br from-red-500 to-pink-600">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"/>
                    </svg>
                </div>
                <h3 class="service-title">Business Consultation</h3>
                <p class="service-description">
                    Konsultasi langsung dengan expert kami untuk strategi bisnis digital yang tepat sasaran.
                </p>
                <ul class="service-features">
                    <li>✓ 1-on-1 Consultation</li>
                    <li>✓ Business Strategy</li>
                    <li>✓ Market Analysis</li>
                    <li>✓ Action Plan</li>
                </ul>
            </div>

            <!-- Service 6 -->
            <div class="service-card group">
                <div class="service-icon-wrapper bg-gradient-to-br from-indigo-500 to-indigo-600">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <h3 class="service-title">Technical Support</h3>
                <p class="service-description">
                    Ada masalah teknis? Tim support kami siap membantu Anda 24/7 untuk menyelesaikan semua kendala.
                </p>
                <ul class="service-features">
                    <li>✓ 24/7 Support</li>
                    <li>✓ Bug Fixing</li>
                    <li>✓ Custom Feature</li>
                    <li>✓ Priority Response</li>
                </ul>
            </div>

        </div>

        <!-- CTA Section -->
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-blue-600 via-purple-600 to-pink-600 p-1 shadow-2xl">
            <div class="bg-white rounded-xl p-8 md:p-12">
                <div class="flex flex-col md:flex-row items-center justify-between gap-8">
                    <div class="flex-1 text-center md:text-left">
                        <h2 class="text-3xl font-bold text-gray-900 mb-3">
                            Butuh Bantuan atau Konsultasi?
                        </h2>
                        <p class="text-lg text-gray-600 mb-2">
                            Tim Customer Service kami siap membantu Anda!
                        </p>
                        <p class="text-sm text-gray-500">
                            Response time: <span class="font-semibold text-green-600">< 5 menit</span>
                        </p>
                    </div>
                    
                    <div class="flex flex-col gap-3">
                        <a href="https://wa.me/6285600489815?text=Halo%20Admin%20Payou.id!%20Saya%20tertarik%20dengan%20layanan%20Anda" 
                           target="_blank"
                           class="group flex items-center gap-3 bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white px-8 py-4 rounded-xl font-semibold shadow-lg hover:shadow-xl transform hover:-translate-y-1 transition-all duration-200">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M20.52 3.48A11.8 11.8 0 0012.01 0C5.37 0 .02 5.35.02 11.99c0 2.11.55 4.18 1.6 6.01L0 24l6.17-1.61a11.93 11.93 0 005.84 1.49h.01c6.63 0 11.99-5.35 11.99-11.99 0-3.2-1.25-6.21-3.49-8.41zM12.02 21.6a9.57 9.57 0 01-4.87-1.33l-.35-.21-3.66.96.98-3.56-.23-.37a9.54 9.54 0 01-1.47-5.09c0-5.28 4.3-9.58 9.6-9.58 2.56 0 4.97 1 6.78 2.8a9.5 9.5 0 012.8 6.78c0 5.29-4.3 9.6-9.58 9.6z"/>
                            </svg>
                            <div class="text-left">
                                <div class="text-sm opacity-90">Chat via WhatsApp</div>
                                <div class="text-xs opacity-75">+62 856-0048-9815</div>
                            </div>
                            <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </a>

                        <div class="flex gap-3 justify-center">
                            <a href="mailto:smeganemolab@gmail.com?subject=Pertanyaan%20Payou.id&body=Halo%20Admin,%20saya%20ingin%20bertanya%20tentang%20layanan%20Payou.id" 
                               class="flex items-center gap-2 text-gray-600 hover:text-blue-600 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                                <span class="text-sm font-medium">smeganemolab@gmail.com</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Features Section -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-12">
            <div class="feature-card flex items-start gap-4 p-6 bg-white rounded-xl">
                <div class="flex-shrink-0 w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="font-semibold text-gray-900 mb-1">Fast Response</h3>
                    <p class="text-sm text-gray-600">Tim kami merespon dalam hitungan menit, bukan jam</p>
                </div>
            </div>

            <div class="feature-card flex items-start gap-4 p-6 bg-white rounded-xl">
                <div class="flex-shrink-0 w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="font-semibold text-gray-900 mb-1">Professional Team</h3>
                    <p class="text-sm text-gray-600">Ditangani oleh expert berpengalaman di bidangnya</p>
                </div>
            </div>

            <div class="feature-card flex items-start gap-4 p-6 bg-white rounded-xl">
                <div class="flex-shrink-0 w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="font-semibold text-gray-900 mb-1">Affordable Price</h3>
                    <p class="text-sm text-gray-600">Harga terjangkau dengan kualitas terjamin</p>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- ================= FOOTER ================= -->
<footer class="footer">
    <div class="footer-wrapper">
        <a href="/" class="logo">
            <img src="../img/icon.png" alt="payou.id">
        </a>
        <div class="footer-links">
            <a href="#">About Us</a>
            <a href="#">Contact Us</a>
            <a href="#">Terms & Conditions</a>
            <a href="#">Privacy</a>
        </div>
    </div>
    <div class="footer-bottom">
        <p>© 2025 payou.id - Satu Link untuk Semua Kebutuhan Bisnis UMKM</p>
    </div>
</footer>

<style>
.service-card {
    background: white;
    border-radius: 16px;
    padding: 24px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
    transition: all 0.3s ease;
    border: 1px solid rgba(229, 231, 235, 0.5);
}

.service-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
    border-color: rgba(59, 130, 246, 0.3);
}

.service-icon-wrapper {
    width: 56px;
    height: 56px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 16px;
    transition: transform 0.3s ease;
}

.service-card:hover .service-icon-wrapper {
    transform: scale(1.1) rotate(5deg);
}

.service-title {
    font-size: 20px;
    font-weight: 700;
    color: #111827;
    margin-bottom: 12px;
}

.service-description {
    font-size: 14px;
    color: #6b7280;
    line-height: 1.6;
    margin-bottom: 16px;
}

.service-features {
    list-style: none;
    padding: 0;
    margin: 0;
}

.service-features li {
    font-size: 13px;
    color: #4b5563;
    padding: 6px 0;
    border-bottom: 1px solid #f3f4f6;
}

.service-features li:last-child {
    border-bottom: none;
}

/* ===== FEATURE CARD ANIMATION ===== */

.feature-card {
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
    transition: all 0.3s ease;
    border: 1px solid rgba(229, 231, 235, 0.5);
}

.feature-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
    border-color: rgba(59, 130, 246, 0.3);
}

.feature-card svg {
    transition: transform 0.3s ease;
}

.feature-card:hover svg {
    transform: scale(1.15) rotate(5deg);
}

</style>

<!-- JS Hamburger dari Landing -->
<script src="{{ asset('js/landing.js') }}"></script>
</body>
</html>