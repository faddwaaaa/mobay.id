<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FAQ - Payou.id</title>
    
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
<div class="min-h-screen bg-gradient-to-br from-purple-50 via-white to-blue-50 py-12 px-4">
    <div class="max-w-4xl mx-auto">
        
        <!-- Back to Home -->
        <div class="mb-8">
            <a href="/" class="inline-flex items-center gap-2 text-gray-600 hover:text-purple-600 transition-colors">
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
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <h1 class="text-4xl font-bold text-gray-900 mb-4">
                Frequently Asked Questions
            </h1>
            <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                Temukan jawaban untuk pertanyaan yang sering ditanyakan. 
                Belum menemukan jawaban? Hubungi CS kami!
            </p>
        </div>

        <!-- Search Box -->
        <div class="mb-8">
            <div class="relative">
                <input type="text" 
                       id="faqSearch"
                       placeholder="Cari pertanyaan..." 
                       class="w-full px-5 py-4 pl-12 border-2 border-gray-200 rounded-xl focus:border-purple-500 focus:outline-none transition-colors text-gray-700">
                <svg class="w-5 h-5 text-gray-400 absolute left-4 top-1/2 transform -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>
        </div>

        <!-- FAQ Categories -->
        <div class="flex flex-wrap gap-3 mb-8 justify-center">
            <button class="category-btn active" data-category="all">
                Semua
            </button>
            <button class="category-btn" data-category="account">
                Akun & Keamanan
            </button>
            <button class="category-btn" data-category="product">
                Produk & Penjualan
            </button>
            <button class="category-btn" data-category="payment">
                Pembayaran
            </button>
            <button class="category-btn" data-category="technical">
                Teknis
            </button>
        </div>

        <!-- FAQ Accordion -->
        <div class="space-y-4" id="faqContainer">
            
            <!-- Account Questions -->
            <div class="faq-item" data-category="account">
                <button class="faq-question">
                    <span class="faq-icon">🔐</span>
                    <span class="flex-1 text-left">Bagaimana cara membuat akun di Payou.id?</span>
                    <svg class="faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div class="faq-answer">
                    <p>Membuat akun di Payou.id sangat mudah. Klik tombol "Daftar", masukkan email dan password, lalu verifikasi email Anda.</p>
                </div>
            </div>

            <div class="faq-item" data-category="account">
                <button class="faq-question">
                    <span class="faq-icon">🔑</span>
                    <span class="flex-1 text-left">Lupa password, bagaimana cara resetnya?</span>
                    <svg class="faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div class="faq-answer">
                    <p>Klik "Lupa Password" di halaman login, masukkan email terdaftar, lalu ikuti instruksi di email untuk reset password.</p>
                </div>
            </div>

            <!-- Product Questions -->
            <div class="faq-item" data-category="product">
                <button class="faq-question">
                    <span class="faq-icon">📦</span>
                    <span class="flex-1 text-left">Berapa banyak produk yang bisa saya upload?</span>
                    <svg class="faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div class="faq-answer">
                    <p>Free Plan: 10 produk, Pro Plan: 100 produk, Enterprise: Unlimited produk.</p>
                </div>
            </div>

            <div class="faq-item" data-category="product">
                <button class="faq-question">
                    <span class="faq-icon">🎨</span>
                    <span class="flex-1 text-left">Bisakah saya custom tampilan link bio saya?</span>
                    <svg class="faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div class="faq-answer">
                    <p>Ya! Anda bisa custom warna, layout, font, logo, dan bahkan custom CSS untuk paket Pro & Enterprise.</p>
                </div>
            </div>

            <!-- Payment Questions -->
            <div class="faq-item" data-category="payment">
                <button class="faq-question">
                    <span class="faq-icon">💳</span>
                    <span class="flex-1 text-left">Metode pembayaran apa saja yang tersedia?</span>
                    <svg class="faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div class="faq-answer">
                    <p>Kami menerima Transfer Bank, E-Wallet, Kartu Kredit, QRIS, Minimarket, dan Crypto.</p>
                </div>
            </div>

            <div class="faq-item" data-category="payment">
                <button class="faq-question">
                    <span class="faq-icon">💸</span>
                    <span class="flex-1 text-left">Berapa biaya transaksi yang dikenakan?</span>
                    <svg class="faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div class="faq-answer">
                    <p>Platform fee 2.5%, payment gateway sesuai provider (±1-3%), dan biaya withdraw Rp 2.500.</p>
                </div>
            </div>

            <!-- Technical Questions -->
            <div class="faq-item" data-category="technical">
                <button class="faq-question">
                    <span class="faq-icon">🔧</span>
                    <span class="flex-1 text-left">Bagaimana cara menghubungkan domain custom?</span>
                    <svg class="faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div class="faq-answer">
                    <p>Beli domain, masukkan di Settings → Custom Domain, tambahkan DNS records yang kami berikan, tunggu propagasi 1-24 jam.</p>
                </div>
            </div>

            <div class="faq-item" data-category="technical">
                <button class="faq-question">
                    <span class="faq-icon">📱</span>
                    <span class="flex-1 text-left">Apakah ada aplikasi mobile?</span>
                    <svg class="faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div class="faq-answer">
                    <p>Website kami 100% responsive dan bisa di-install sebagai PWA. Aplikasi iOS & Android sedang dalam development.</p>
                </div>
            </div>

        </div>

        <!-- CTA -->
        <div class="mt-12 p-8 bg-gradient-to-r from-blue-600 to-blue-600 rounded-2xl text-white text-center">
            <h3 class="text-2xl font-bold mb-3">Masih Punya Pertanyaan?</h3>
            <p class="mb-6 opacity-90">Tim Customer Service kami siap membantu Anda 24/7</p>
            <a href="https://wa.me/6281234567890?text=Halo%20Payou.id!%20Saya%20punya%20pertanyaan" 
               target="_blank"
               class="inline-flex items-center gap-3 bg-white text-blue-600 px-8 py-4 rounded-xl font-semibold shadow-lg hover:shadow-xl transform hover:-translate-y-1 transition-all duration-200">
                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5 -.669 -.5 . - . - . - . - . - . - . - . - . - . - . - . - . - . - . - . - . - . - . - . - . - .
                </svg>
                <span>Chat via WhatsApp</span>
            </a>
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
.category-btn {
    padding: 10px 20px;
    border-radius: 10px;
    font-size: 14px;
    font-weight: 600;
    color: #6b7280;
    background: white;
    border: 2px solid #e5e7eb;
    cursor: pointer;
    transition: all 0.2s ease;
}

.category-btn:hover {
    border-color: #1940ff;
    color: #1940ff;
    transform: translateY(-2px);
}

.category-btn.active {
    background: linear-gradient(135deg, #1940ff 0%, #6366f1 100%);
    color: white;
    border-color: #1940ff;
    box-shadow: 0 4px 12px rgba(85, 90, 247, 0.3);
}

.faq-item {
    background: white;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    border: 1px solid #e5e7eb;
    transition: all 0.3s ease;
}

.faq-item:hover {
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
    border-color: #1940ff;
}

.faq-question {
    width: 100%;
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 20px;
    background: transparent;
    border: none;
    cursor: pointer;
    font-size: 16px;
    font-weight: 600;
    color: #111827;
    transition: all 0.2s ease;
}

.faq-question:hover {
    background: #f9fafb;
}

.faq-icon {
    font-size: 24px;
    flex-shrink: 0;
}

.faq-chevron {
    width: 20px;
    height: 20px;
    flex-shrink: 0;
    transition: transform 0.3s ease;
}

.faq-item.active .faq-chevron {
    transform: rotate(180deg);
}

.faq-answer {
    max-height: 0;
    overflow: hidden;
    transition: max-height 0.3s ease;
    padding: 0 20px;
}

.faq-item.active .faq-answer {
    max-height: 1000px;
    padding: 0 20px 20px 20px;
}

.faq-answer p {
    color: #4b5563;
    font-size: 14px;
    line-height: 1.6;
}

.faq-item.hidden {
    display: none;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const faqItems = document.querySelectorAll('.faq-item');
    
    // Accordion
    faqItems.forEach(item => {
        const question = item.querySelector('.faq-question');
        question.addEventListener('click', () => {
            const isActive = item.classList.contains('active');
            faqItems.forEach(i => i.classList.remove('active'));
            if (!isActive) item.classList.add('active');
        });
    });
    
    // Category filter
    const categoryBtns = document.querySelectorAll('.category-btn');
    categoryBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            const category = btn.dataset.category;
            categoryBtns.forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            
            faqItems.forEach(item => {
                if (category === 'all' || item.dataset.category === category) {
                    item.classList.remove('hidden');
                } else {
                    item.classList.add('hidden');
                }
            });
        });
    });
    
    // Search
    const searchInput = document.getElementById('faqSearch');
    searchInput.addEventListener('input', (e) => {
        const term = e.target.value.toLowerCase();
        faqItems.forEach(item => {
            const text = item.textContent.toLowerCase();
            if (text.includes(term)) {
                item.classList.remove('hidden');
            } else {
                item.classList.add('hidden');
            }
        });
    });
});
</script>

</body>
</html>