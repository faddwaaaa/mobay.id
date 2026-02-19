
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>{{ $user->name }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: system-ui, -apple-system, sans-serif;
            background: #f9fafb;
        }

        /* =========================================================
           NAVIGATION BAR
        ========================================================= */
        .navbar {
            background: #fff;
            border-bottom: 1px solid #e5e7eb;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }

        .navbar-container {
            max-width: 420px;
            margin: 0 auto;
            padding: 12px 16px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .navbar-left {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .hamburger {
            width: 32px;
            height: 32px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            gap: 4px;
            cursor: pointer;
            padding: 4px;
            border-radius: 6px;
            transition: background 0.2s;
        }

        .hamburger:hover {
            background: #f3f4f6;
        }

        .hamburger span {
            width: 100%;
            height: 2px;
            background: #374151;
            border-radius: 2px;
            transition: all 0.3s ease;
        }

        .hamburger.active span:nth-child(1) {
            transform: rotate(45deg) translate(5px, 5px);
        }

        .hamburger.active span:nth-child(2) {
            opacity: 0;
        }

        .hamburger.active span:nth-child(3) {
            transform: rotate(-45deg) translate(5px, -5px);
        }

        .navbar-title {
            font-size: 16px;
            font-weight: 600;
            color: #111827;
        }

        .navbar-right {
            display: flex;
            gap: 8px;
        }

        .nav-icon {
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            cursor: pointer;
            transition: background 0.2s;
            position: relative;
        }

        .nav-icon:hover {
            background: #f3f4f6;
        }

        .cart-badge {
            position: absolute;
            top: 2px;
            right: 2px;
            background: #ef4444;
            color: white;
            font-size: 10px;
            font-weight: 600;
            width: 16px;
            height: 16px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* =========================================================
           SIDEBAR MENU
        ========================================================= */
        .sidebar-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 200;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }

        .sidebar-overlay.active {
            opacity: 1;
            visibility: visible;
        }

        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: 280px;
            height: 100%;
            background: #fff;
            z-index: 201;
            transform: translateX(-100%);
            transition: transform 0.3s ease;
            overflow-y: auto;
        }

        .sidebar.active {
            transform: translateX(0);
        }

        .sidebar-header {
            padding: 20px 16px;
            border-bottom: 1px solid #e5e7eb;
        }

        .sidebar-header h3 {
            font-size: 18px;
            font-weight: 600;
            color: #111827;
        }

        .sidebar-menu {
            padding: 8px 0;
        }

        .menu-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 16px;
            color: #374151;
            text-decoration: none;
            transition: background 0.2s;
            cursor: pointer;
        }

        .menu-item:hover {
            background: #f3f4f6;
        }

        .menu-item.active {
            background: #eff6ff;
            color: #2563eb;
            border-left: 3px solid #2563eb;
        }

        .menu-icon {
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .menu-text {
            font-size: 14px;
            font-weight: 500;
            flex: 1;
        }

        /* =========================================================
           MAIN CONTENT
        ========================================================= */
        .container {
            max-width: 420px;
            margin: 0 auto;
            padding: 24px 16px;
        }

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
        }

        /* =========================================================
           USER PROFILE — tampil di semua halaman
        ========================================================= */
        .user-profile {
            text-align: center;
            margin-bottom: 24px;
            padding-bottom: 20px;
            border-bottom: 1px solid #e5e7eb;
        }

        .avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: #e5e7eb;
            margin: 0 auto 10px;
            display: block;
            object-fit: cover;
            border: 3px solid #fff;
            box-shadow: 0 0 0 2px #e5e7eb;
        }

        .avatar-placeholder {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: #e5e7eb;
            margin: 0 auto 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            color: #9ca3af;
            border: 3px solid #fff;
            box-shadow: 0 0 0 2px #e5e7eb;
        }

        .profile-name {
            font-size: 17px;
            font-weight: 700;
            color: #111827;
            margin-bottom: 2px;
        }

        .profile-username {
            color: #6b7280;
            font-size: 13px;
            margin-bottom: 8px;
        }

        .profile-bio {
            font-size: 13px;
            color: #374151;
            line-height: 1.5;
        }

        /* =========================================================
           BLOCKS
        ========================================================= */
        .block {
            margin-bottom: 12px;
        }

        .block-text {
            font-size: 14px;
            text-align: center;
            color: #374151;
            line-height: 1.6;
        }

        .block-link a {
            display: block;
            padding: 14px;
            border-radius: 12px;
            border: 1px solid #e5e7eb;
            text-align: center;
            text-decoration: none;
            color: #111;
            font-weight: 500;
            transition: all 0.2s;
            background: #fff;
        }

        .block-link a:hover {
            border-color: #2563eb;
            background: #eff6ff;
        }

        .block-image img {
            width: 100%;
            border-radius: 12px;
        }

        .block-video iframe {
            width: 100%;
            height: 200px;
            border-radius: 12px;
            border: none;
        }

        /* =========================================================
           PRODUCT CARD — MINIMALIST BLUE
        ========================================================= */
        .block-product {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            overflow: hidden;
            transition: box-shadow 0.2s, transform 0.2s, border-color 0.2s;
            cursor: pointer;
        }

        .block-product:hover {
            box-shadow: 0 4px 16px rgba(37, 99, 235, 0.1);
            transform: translateY(-2px);
            border-color: #bfdbfe;
        }

        .product-image-wrapper {
            width: 100%;
            height: 200px;
            background: #f3f4f6;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .product-image-wrapper img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .product-image-placeholder {
            width: 56px;
            height: 56px;
            background: #eff6ff;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 26px;
        }

        .product-details {
            padding: 14px 16px 16px;
        }

        .product-badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            background: #eff6ff;
            color: #2563eb;
            font-size: 11px;
            font-weight: 600;
            padding: 3px 8px;
            border-radius: 6px;
            margin-bottom: 8px;
            letter-spacing: 0.3px;
        }

        .product-title {
            font-size: 15px;
            font-weight: 600;
            color: #111827;
            margin-bottom: 10px;
            line-height: 1.4;
        }

        .product-price-section {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 14px;
        }

        .product-current-price {
            font-size: 18px;
            font-weight: 700;
            color: #2563eb;
        }

        .product-original-price {
            font-size: 13px;
            color: #9ca3af;
            text-decoration: line-through;
        }

        .product-discount-badge {
            background: #fee2e2;
            color: #dc2626;
            font-size: 11px;
            font-weight: 600;
            padding: 2px 6px;
            border-radius: 4px;
        }

        .product-cta {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            background: #2563eb;
            color: #fff;
            font-weight: 600;
            font-size: 14px;
            padding: 11px 16px;
            border-radius: 8px;
            text-decoration: none;
            transition: background 0.2s;
        }

        .product-cta:hover {
            background: #1d4ed8;
        }

        .product-cta svg {
            width: 16px;
            height: 16px;
            flex-shrink: 0;
        }

        /* =========================================================
           EMPTY STATE
        ========================================================= */
        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: #9ca3af;
        }

        .empty-icon {
            width: 64px;
            height: 64px;
            background: #f3f4f6;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 12px;
            font-size: 24px;
        }

        /* =========================================================
           PRODUCT GRID (untuk tab Produk)
        ========================================================= */
        .products-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
        }

        .product-card {
            background: #fff;
            border-radius: 12px;
            overflow: hidden;
            border: 1px solid #e5e7eb;
            transition: all 0.2s;
            cursor: pointer;
        }

        .product-card:hover {
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            transform: translateY(-2px);
        }

        .product-image {
            width: 100%;
            aspect-ratio: 1;
            background: #f3f4f6;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .product-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .product-info {
            padding: 12px;
        }

        .product-info .product-title {
            font-size: 14px;
            font-weight: 600;
            color: #111827;
            margin-bottom: 4px;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .product-price {
            font-size: 14px;
            font-weight: 700;
            color: #2563eb;
        }

        .product-price-discount {
            color: #ef4444;
        }

        .product-price-original {
            font-size: 11px;
            text-decoration: line-through;
            color: #9ca3af;
            margin-left: 4px;
        }

        .product-detail-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.45);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 9999;
            padding: 20px;
        }

        .product-detail-box {
            position: relative;
            background: white;
            width: 100%;
            max-width: 420px;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 20px 50px rgba(0,0,0,0.15);
            display: flex;
            flex-direction: column;
        }

        .product-detail-header {
            position: fixed;
            top: 15px;
            left: 15px;
            z-index: 10;
        }

        .product-detail-header button {
            background: rgba(255,255,255,0.9);
            backdrop-filter: blur(4px);
            border: none;
            border-radius: 50%;
            width: 35px;
            height: 35px;
            font-weight: bold;
            cursor: pointer;
        }

        .product-detail-image {
            width: 100%;
            height: 220px;
            background: #f3f4f6;
        }

        .product-detail-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .product-detail-content {
            padding: 20px;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .product-detail-content h2 {
            font-size: 20px;
            font-weight: 700;
        }

        .detail-price {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .final-price {
            font-size: 22px;
            font-weight: 700;
            color: #2563eb;
        }

        .original-price {
            text-decoration: line-through;
            color: #999;
            font-size: 14px;
        }

        .stock-info {
            font-size: 13px;
            color: #555;
        }

        .detail-description {
            font-size: 14px;
            color: #444;
            line-height: 1.6;
        }

        .detail-buttons {
            display: flex;
            gap: 10px;
            padding: 16px;
            border-top: 1px solid #e5e7eb;
            background: #fff;
        }

        .btn-cart {
            width: 48px;
            height: 48px;
            min-width: 48px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid #2563eb;
            border-radius: 10px;
            background: #fff;
            font-size: 20px;
            cursor: pointer;
            transition: background 0.2s;
        }

        .btn-cart:hover {
            background: #eff6ff;
        }

        .btn-buy {
            flex: 1;
            padding: 12px;
            background: #2563eb;
            color: white;
            border-radius: 10px;
            font-weight: 600;
            font-size: 15px;
            border: none;
            cursor: pointer;
            transition: background 0.2s;
        }

        .btn-buy:hover {
            background: #1d4ed8;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body>

<!-- NAVBAR -->
<div class="navbar">
    <div class="navbar-container">
        <div class="navbar-left">
            <div class="hamburger" id="hamburger">
                <span></span>
                <span></span>
                <span></span>
            </div>
            <div class="navbar-title">{{ $user->name }}</div>
        </div>
        <div class="navbar-right">
            <div class="nav-icon" id="cartBtn">
                <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                <span class="cart-badge">0</span>
            </div>
        </div>
    </div>
</div>

<!-- SIDEBAR OVERLAY -->
<div class="sidebar-overlay" id="sidebarOverlay"></div>

<!-- SIDEBAR -->
<div class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <h3>Menu</h3>
    </div>
    <div class="sidebar-menu">
        @if($user->pages && $user->pages->count() > 0)
            @foreach($user->pages as $userPage)
                <a href="#" class="menu-item {{ $loop->first ? 'active' : '' }}" data-tab="page-{{ $userPage->id }}">
                    <span class="menu-text">{{ $userPage->title }}</span>
                </a>
            @endforeach
        @endif
    </div>
</div>

<!-- MAIN CONTENT -->
<div class="container">

    @if($user->pages && $user->pages->count() > 0)
        @foreach($user->pages as $userPage)
            <div class="tab-content {{ $loop->first ? 'active' : '' }}" id="tab-page-{{ $userPage->id }}">

                {{-- USER PROFILE — ditampilkan di SEMUA halaman --}}
                <div class="user-profile">
                    @if($user->avatar)
                        <img
                            src="{{ asset('storage/' . $user->avatar) }}"
                            class="avatar"
                            alt="{{ $user->name }}"
                        >
                    @else
                        <div class="avatar-placeholder">👤</div>
                    @endif

                    <div class="profile-name">{{ $user->name }}</div>
                    <div class="profile-username">{{ '@' . $user->username }}</div>

                    @if($user->bio)
                        <div class="profile-bio">{{ $user->bio }}</div>
                    @endif
                </div>

                @if($userPage->blocks && $userPage->blocks->count() > 0)
                    @foreach($userPage->blocks->sortBy('position') as $block)

                        {{-- TEXT --}}
                        @if($block->type === 'text')
                            <div class="block block-text">
                                {{ $block->content['text'] ?? '' }}
                            </div>
                        @endif

                        {{-- LINK --}}
                        @if($block->type === 'link')
                            <div class="block block-link">
                                <a href="{{ $block->content['url'] ?? '#' }}" target="_blank">
                                    {{ $block->content['title'] ?? 'Link' }}
                                </a>
                            </div>
                        @endif

                        {{-- IMAGE --}}
                        @if($block->type === 'image')
                            <div class="block block-image">
                                <img src="{{ asset('storage/' . $block->content['image']) }}">
                            </div>
                        @endif

                        {{-- VIDEO --}}
                        @if($block->type === 'video')
                            @php
                                $videoId = $block->content['youtube_id'] ?? '';
                                if (!$videoId) {
                                    $url = $block->content['youtube_url'] ?? '';
                                    parse_str(parse_url($url, PHP_URL_QUERY), $query);
                                    $videoId = $query['v'] ?? '';
                                    if (!$videoId && str_contains($url, 'youtu.be/')) {
                                        $videoId = basename(parse_url($url, PHP_URL_PATH));
                                    }
                                }
                            @endphp
                            <div class="block block-video">
                                <iframe
                                    src="https://www.youtube.com/embed/{{ $videoId }}"
                                    allowfullscreen>
                                </iframe>
                            </div>
                        @endif

                        {{-- PRODUCT — MINIMALIST BLUE --}}
                        @if($block->type === 'product' && isset($block->content['product']))
                            @php
                                $product = $block->content['product'];
                                $productPrice = $product['price'] ?? 0;
                                $productDiscount = $product['discount'] ?? null;
                                $finalPrice = $productDiscount ?? $productPrice;
                                $hasDiscount = $productDiscount && $productDiscount < $productPrice;
                                $discountPercent = $hasDiscount ? round((($productPrice - $productDiscount) / $productPrice) * 100) : 0;
                            @endphp

                            <div class="block block-product" onclick="handleProductClick({{ $block->product_id ?? 'null' }})">
                                <div class="product-image-wrapper">
                                    @if(isset($product['image']))
                                        <img src="{{ asset('storage/' . $product['image']) }}"
                                            alt="{{ $product['title'] ?? 'Product' }}">
                                    @else
                                        <div class="product-image-placeholder">🛍️</div>
                                    @endif
                                </div>

                                <div class="product-details">
                                    @if($hasDiscount)
                                        <span class="product-badge">Diskon {{ $discountPercent }}%</span>
                                    @endif

                                    <div class="product-title">
                                        {{ $product['title'] ?? 'Produk' }}
                                    </div>

                                    <div class="product-price-section">
                                        <div class="product-current-price">
                                            Rp {{ number_format($finalPrice, 0, ',', '.') }}
                                        </div>

                                        @if($hasDiscount)
                                            <div class="product-original-price">
                                                Rp {{ number_format($productPrice, 0, ',', '.') }}
                                            </div>
                                            <div class="product-discount-badge">-{{ $discountPercent }}%</div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endif

                    @endforeach
                @else
                    <div class="empty-state">
                        <div class="empty-icon">📝</div>
                        Halaman ini belum memiliki konten.
                    </div>
                @endif
            </div>
        @endforeach
    @else
        <div class="empty-state">
            <div class="empty-icon">📄</div>
            Belum ada halaman.
        </div>
    @endif

</div>

<div class="product-detail-overlay" id="productDetailModal">
    <div class="product-detail-box">

        <div class="product-detail-header">
            <button onclick="closeProductDetail()">✕</button>
        </div>

        <div class="product-detail-image">
            <img id="detailImage" src="">
        </div>

        <div class="product-detail-content">

            <h2 id="detailTitle"></h2>

            <div class="detail-price">
                <span class="final-price" id="detailFinalPrice"></span>
                <span class="original-price" id="detailOriginalPrice"></span>
            </div>

            <div class="stock-info">
                Stock: <span id="detailStock"></span>
            </div>

            <div class="detail-description">
                <h4>DESCRIPTION</h4>
                <p id="detailDescription"></p>
            </div>

        </div>

        <div class="detail-buttons">
            <button class="btn-cart">
                <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
            </button>
            <button class="btn-buy" id="buyNowBtn">
                Beli Sekarang
            </button>
        </div>

    </div>
</div>


<script>
// ========== HAMBURGER MENU ==========
const hamburger = document.getElementById('hamburger');
const sidebar = document.getElementById('sidebar');
const sidebarOverlay = document.getElementById('sidebarOverlay');

function toggleSidebar() {
    hamburger?.classList.toggle('active');
    sidebar?.classList.toggle('active');
    sidebarOverlay?.classList.toggle('active');
}

hamburger?.addEventListener('click', toggleSidebar);
sidebarOverlay?.addEventListener('click', toggleSidebar);


// ========== TAB SWITCHING ==========
const menuItems = document.querySelectorAll('.menu-item');
const tabContents = document.querySelectorAll('.tab-content');

function switchTab(tabName) {
    menuItems.forEach(m => m.classList.remove('active'));
    document.querySelector(`.menu-item[data-tab="${tabName}"]`)?.classList.add('active');

    tabContents.forEach(c => c.classList.remove('active'));
    document.getElementById(`tab-${tabName}`)?.classList.add('active');

    if (window.innerWidth < 768) {
        toggleSidebar();
    }
}

menuItems.forEach(item => {
    item.addEventListener('click', (e) => {
        e.preventDefault();
        switchTab(item.dataset.tab);
    });
});


// ========== FORMAT RUPIAH ==========
function formatRupiah(number) {
    return 'Rp ' + new Intl.NumberFormat('id-ID').format(number);
}


// ========== PRODUCT DETAIL ==========
function handleProductClick(productId) {
    if (!productId) return;

    fetch(`/api/product/${productId}`)
        .then(res => res.json())
        .then(product => {

            document.getElementById('detailTitle').innerText = product.title;
            document.getElementById('detailDescription').innerText = product.description ?? '';

            // HITUNG FINAL PRICE
            let finalPrice = product.price;

            if (product.discount && product.discount > 0) {
                finalPrice = product.price - (product.price * product.discount / 100);
            }

            document.getElementById('detailFinalPrice').innerText =
                formatRupiah(finalPrice);

            document.getElementById('detailOriginalPrice').innerText =
                product.discount && product.discount > 0
                    ? formatRupiah(product.price)
                    : '';

            document.getElementById('detailStock').innerText =
                product.stock ?? 0;

            document.getElementById('detailImage').src =
                product.image_url ?? 'https://via.placeholder.com/400';

            document.getElementById('buyNowBtn').onclick = function () {
                window.location.href = `/checkout/${product.id}`;
            };

            // PENTING: pakai flex supaya center
          document.getElementById('productDetailModal').style.display = 'flex';
            document.body.style.overflow = 'hidden';
        })
        .catch(err => {
            console.error('Error ambil produk:', err);
        });
}

function closeProductDetail() {
    document.getElementById('productDetailModal').style.display = 'none';
    document.body.style.overflow = 'auto';
}

// ========== CART BUTTON ==========
const cartBtn = document.getElementById('cartBtn');

cartBtn?.addEventListener('click', () => {
    alert('Fitur keranjang akan segera hadir!');
});
</script>


</body>
</html>