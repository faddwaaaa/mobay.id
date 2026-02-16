<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FAQ - Payou.id</title>

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
        <div class="mt-16">
            <div class="p-8 bg-gradient-to-r from-blue-600 to-indigo-600 rounded-2xl text-white text-center shadow-lg">
                <h3 class="text-2xl font-bold mb-3">Masih Punya Pertanyaan?</h3>
                <p class="mb-6 opacity-90">Tim Customer Service kami siap membantu Anda 24/7</p>
                <a href="https://wa.me/6285600489815?text=Halo%20Admin%20Payou.id!%20Saya%20punya%20pertanyaan" 
                target="_blank"
                class="inline-flex items-center gap-3 bg-white text-blue-600 px-8 py-4 rounded-xl font-semibold shadow-lg hover:shadow-xl transform hover:-translate-y-1 transition-all duration-200">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M20.52 3.48A11.8 11.8 0 0012.01 0C5.37 0 .02 5.35.02 11.99c0 2.11.55 4.18 1.6 6.01L0 24l6.17-1.61a11.93 11.93 0 005.84 1.49h.01c6.63 0 11.99-5.35 11.99-11.99 0-3.2-1.25-6.21-3.49-8.41zM12.02 21.6a9.57 9.57 0 01-4.87-1.33l-.35-.21-3.66.96.98-3.56-.23-.37a9.54 9.54 0 01-1.47-5.09c0-5.28 4.3-9.58 9.6-9.58 2.56 0 4.97 1 6.78 2.8a9.5 9.5 0 012.8 6.78c0 5.29-4.3 9.6-9.58 9.6z"/>
                    </svg>
                    <span>Chat via WhatsApp</span>
                </a>
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

/* ================= CATEGORY BUTTON ================= */
.category-btn {
    padding: 10px 20px;
    border-radius: 12px;
    font-size: 14px;
    font-weight: 600;
    color: #6b7280;
    background: #fff;
    border: 2px solid #e5e7eb;
    cursor: pointer;
    transition: all 0.25s ease;
}

.category-btn:hover {
    border-color: #1940ff;
    color: #1940ff;
    transform: translateY(-2px);
}

.category-btn.active {
    background: linear-gradient(135deg, #1940ff, #6366f1);
    color: #fff;
    border-color: #1940ff;
    box-shadow: 0 6px 16px rgba(25, 64, 255, 0.25);
}

/* ================= FAQ CARD ================= */
.faq-item {
    background: #fff;
    border-radius: 14px;
    overflow: hidden;
    border: 1px solid #e5e7eb;
    box-shadow: 0 4px 14px rgba(0, 0, 0, 0.04);
    transition: all 0.3s ease;
}

.faq-item:hover {
    border-color: #1940ff;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
}

/* ================= FAQ QUESTION ================= */
.faq-question {
    width: 100%;
    display: flex;
    align-items: center;
    gap: 14px;
    padding: 22px;
    background: transparent;
    border: none;
    cursor: pointer;
    font-size: 16px;
    font-weight: 600;
    color: #111827;
    transition: background 0.2s ease;
}

.faq-question:hover {
    background: #f9fafb;
}

.faq-icon {
    font-size: 22px;
}

.faq-chevron {
    width: 20px;
    height: 20px;
    transition: transform 0.3s ease;
}

.faq-item.active .faq-chevron {
    transform: rotate(180deg);
}

/* ================= FAQ ANSWER ================= */
.faq-answer {
    max-height: 0;
    overflow: hidden;
    transition: max-height 0.35s ease;
    padding: 0 22px;
}

.faq-item.active .faq-answer {
    max-height: 500px;
    padding: 0 22px 22px;
}

.faq-answer p {
    font-size: 14px;
    line-height: 1.7;
    color: #4b5563;
}

/* ================= HIDDEN ================= */
.faq-item.hidden {
    display: none;
}

/* ================= FOOTER FIX ================= */
footer {
    margin-top: 80px;
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
<!-- JS Hamburger dari Landing -->
<script src="{{ asset('js/landing.js') }}"></script>
</body>
</html>