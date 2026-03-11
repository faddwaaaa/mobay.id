<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Contact - Payou.id</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/landing.css') }}">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>
<body class="bg-gray-50">
<nav>
    <div class="nav-container">
        <a href="/" class="logo">
            <img src="../img/logo.png" alt="payou.id">
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

    <div class="max-w-6xl mx-auto px-6 pt-32 pb-20">

        <!-- Heading -->
        <div class="text-center mb-16">
            <h1 class="text-4xl font-extrabold text-gray-900 mb-4">
                Hubungi Kami
            </h1>
            <p class="text-gray-600">
                Punya pertanyaan atau ingin menggunakan layanan kami? Tim kami siap membantu.
            </p>
        </div>

        <!-- Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 max-w-3xl mx-auto">

            <!-- WhatsApp -->
            <a href="https://wa.me/6285600489815?text=Halo%20Admin%20Payou.id!%20Saya%20ingin%20bertanya%20tentang%20layanan%20Anda"
                target="_blank"
                class="group bg-white p-8 rounded-2xl shadow-md hover:shadow-xl transition-all border border-gray-200 hover:-translate-y-2">

                <div class="w-14 h-14 bg-green-100 text-green-600 rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 transition">
                    <i class="fa-brands fa-whatsapp text-2xl"></i>
                </div>

                <h3 class="text-lg font-semibold text-gray-900 mb-2">
                    WhatsApp
                </h3>

                <p class="text-gray-500 text-sm">
                    Chat langsung dengan tim kami untuk respon cepat.
                </p>

                <div class="mt-4 text-green-600 font-medium text-sm">
                    +62 856-0048-9815 →
                </div>
            </a>

            <!-- Email -->
            <a href="mailto:smeganemolab@gmail.com"
            class="group bg-white p-8 rounded-2xl shadow-md hover:shadow-xl transition-all border border-gray-200 hover:-translate-y-2">

                <div class="w-14 h-14 bg-red-100 text-red-500 rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 transition">
                    <i class="fa-solid fa-envelope text-2xl"></i>
                </div>

                <h3 class="text-lg font-semibold text-gray-900 mb-2">
                    Email
                </h3>

                <p class="text-gray-500 text-sm">
                    Kirim pertanyaan atau permintaan kerja sama melalui email.
                </p>

                <div class="mt-4 text-red-500 font-medium text-sm">
                    smeganemolab@gmail.com →
                </div>
            </a>

        </div>

    </div>

<footer class="footer">
    <div class="footer-wrapper">
        <a href="/" class="logo">
            <img src="../img/logo.png" alt="payou.id">
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
<!-- JS Hamburger dari Landing -->
<script src="{{ asset('js/landing.js') }}"></script>
</body>
</html>
