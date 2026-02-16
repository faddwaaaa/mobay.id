<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Layanan Kami - Payou.id</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50">

<!-- Navbar -->
<nav class="bg-white shadow-sm sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 py-4">
        <div class="flex justify-between items-center">
            <a href="/" class="text-2xl font-bold text-blue-600">
                Payou.id
            </a>
            
            <div class="flex items-center gap-6">
                <a href="/service" class="text-gray-600 hover:text-blue-600 transition-colors font-medium">
                    Layanan
                </a>
                <a href="/faq" class="text-gray-600 hover:text-blue-600 transition-colors font-medium">
                    FAQ
                </a>
                
                <div class="flex gap-3">
                    <!-- <a href="/login" class="text-gray-600 hover:text-blue-600 transition-colors font-medium">
                        Login
                    </a> -->
                    <a href="/register" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition-colors font-medium">
                        Daftar
                    </a>
                </div>
            </div>
        </div>
    </div>
</nav>

<!-- Main Content -->
<div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-purple-50 py-12 px-4">
    <div class="max-w-6xl mx-auto">
        
        <!-- Back to Home -->
        <div class="mb-8">
            <a href="/" class="inline-flex items-center gap-2 text-gray-600 hover:text-blue-600 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                <span class="font-medium">Kembali ke Beranda</span>
            </a>
        </div>

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
                        <a href="https://wa.me/6281234567890?text=Halo%20Payou.id!%20Saya%20tertarik%20dengan%20layanan%20Anda" 
                           target="_blank"
                           class="group flex items-center gap-3 bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white px-8 py-4 rounded-xl font-semibold shadow-lg hover:shadow-xl transform hover:-translate-y-1 transition-all duration-200">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                            </svg>
                            <div class="text-left">
                                <div class="text-sm opacity-90">Chat via WhatsApp</div>
                                <div class="text-xs opacity-75">+62 812-3456-7890</div>
                            </div>
                            <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </a>

                        <div class="flex gap-3 justify-center">
                            <a href="mailto:support@payou.id" 
                               class="flex items-center gap-2 text-gray-600 hover:text-blue-600 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                                <span class="text-sm font-medium">support@payou.id</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Features Section -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-12">
            <div class="flex items-start gap-4 p-6 bg-white rounded-xl shadow-sm hover:shadow-md transition-shadow">
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

            <div class="flex items-start gap-4 p-6 bg-white rounded-xl shadow-sm hover:shadow-md transition-shadow">
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

            <div class="flex items-start gap-4 p-6 bg-white rounded-xl shadow-sm hover:shadow-md transition-shadow">
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

<!-- Footer -->
<footer class="bg-gray-900 text-white py-12 mt-12">
    <div class="max-w-7xl mx-auto px-4">
        <div class="text-center text-sm text-gray-400">
            © 2024 Payou.id. All rights reserved.
        </div>
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
</style>

</body>
</html>