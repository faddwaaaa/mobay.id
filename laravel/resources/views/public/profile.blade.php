<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>{{ $user->name }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: system-ui, -apple-system, sans-serif; background: #f9fafb; }

        /* =========================================================
           TOAST NOTIFICATION
        ========================================================= */
        .toast {
            position: fixed;
            bottom: 24px;
            left: 50%;
            transform: translateX(-50%) translateY(80px);
            background: #111827;
            color: #fff;
            padding: 10px 20px;
            border-radius: 50px;
            font-size: 13px;
            font-weight: 500;
            z-index: 9999;
            opacity: 0;
            transition: all 0.35s cubic-bezier(.34,1.56,.64,1);
            white-space: nowrap;
            pointer-events: none;
        }
        .toast.show {
            opacity: 1;
            transform: translateX(-50%) translateY(0);
        }
        .toast.success { background: #16a34a; }
        .toast.error   { background: #dc2626; }

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
        .navbar-left { display: flex; align-items: center; gap: 12px; }
        .hamburger {
            width: 32px; height: 32px;
            display: flex; flex-direction: column; justify-content: center; gap: 4px;
            cursor: pointer; padding: 4px; border-radius: 6px; transition: background 0.2s;
        }
        .hamburger:hover { background: #f3f4f6; }
        .hamburger span {
            width: 100%; height: 2px; background: #374151;
            border-radius: 2px; transition: all 0.3s ease;
        }
        .hamburger.active span:nth-child(1) { transform: rotate(45deg) translate(5px, 5px); }
        .hamburger.active span:nth-child(2) { opacity: 0; }
        .hamburger.active span:nth-child(3) { transform: rotate(-45deg) translate(5px, -5px); }
        .navbar-title { font-size: 16px; font-weight: 600; color: #111827; }
        .navbar-right { display: flex; gap: 8px; }
        .nav-icon {
            width: 36px; height: 36px;
            display: flex; align-items: center; justify-content: center;
            border-radius: 8px; cursor: pointer; transition: background 0.2s; position: relative;
        }
        .nav-icon:hover { background: #f3f4f6; }
        .cart-badge {
            position: absolute; top: 2px; right: 2px;
            background: #ef4444; color: white;
            font-size: 10px; font-weight: 600;
            width: 16px; height: 16px; border-radius: 50%;
            display: none; align-items: center; justify-content: center;
        }
        .cart-badge.visible { display: flex; }

        /* =========================================================
           SIDEBAR
        ========================================================= */
        .sidebar-overlay {
            position: fixed; top: 0; left: 0;
            width: 100%; height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 200; opacity: 0; visibility: hidden; transition: all 0.3s ease;
        }
        .sidebar-overlay.active { opacity: 1; visibility: visible; }
        .sidebar {
            position: fixed; top: 0; left: 0;
            width: 280px; height: 100%;
            background: #fff; z-index: 201;
            transform: translateX(-100%); transition: transform 0.3s ease; overflow-y: auto;
        }
        .sidebar.active { transform: translateX(0); }
        .sidebar-header { padding: 20px 16px; border-bottom: 1px solid #e5e7eb; }
        .sidebar-header h3 { font-size: 18px; font-weight: 600; color: #111827; }
        .sidebar-menu { padding: 8px 0; }
        .menu-item {
            display: flex; align-items: center; gap: 12px;
            padding: 12px 16px; color: #374151;
            text-decoration: none; transition: background 0.2s; cursor: pointer;
        }
        .menu-item:hover { background: #f3f4f6; }
        .menu-item.active { background: #eff6ff; color: #2563eb; border-left: 3px solid #2563eb; }
        .menu-text { font-size: 14px; font-weight: 500; flex: 1; }

        /* =========================================================
           MAIN CONTENT
        ========================================================= */
        .container { max-width: 420px; margin: 0 auto; padding: 24px 16px; }
        .tab-content { display: none; }
        .tab-content.active { display: block; }

        /* =========================================================
           PROFILE
        ========================================================= */
        .user-profile {
            text-align: center; margin-bottom: 24px;
            padding-bottom: 20px; border-bottom: 1px solid #e5e7eb;
        }
        .avatar {
            width: 80px; height: 80px; border-radius: 50%;
            background: #e5e7eb; margin: 0 auto 10px;
            display: block; object-fit: cover;
            border: 3px solid #fff; box-shadow: 0 0 0 2px #e5e7eb;
        }
        .avatar-placeholder {
            width: 80px; height: 80px; border-radius: 50%;
            background: #e5e7eb; margin: 0 auto 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 28px; color: #9ca3af;
            border: 3px solid #fff; box-shadow: 0 0 0 2px #e5e7eb;
        }
        .profile-name { font-size: 17px; font-weight: 700; color: #111827; margin-bottom: 2px; }
        .profile-username { color: #6b7280; font-size: 13px; margin-bottom: 8px; }
        .profile-bio { font-size: 13px; color: #374151; line-height: 1.5; }

        /* =========================================================
           BLOCKS
        ========================================================= */
        .block { margin-bottom: 12px; }
        .block-text { font-size: 14px; text-align: center; color: #374151; line-height: 1.6; }
        .block-link a {
            display: block; padding: 14px; border-radius: 12px;
            border: 1px solid #e5e7eb; text-align: center;
            text-decoration: none; color: #111; font-weight: 500;
            transition: all 0.2s; background: #fff;
        }
        .block-link a:hover { border-color: #2563eb; background: #eff6ff; }
        .block-image img { width: 100%; border-radius: 12px; }
        .block-video iframe {
            width: 100%; height: 200px; border-radius: 12px; border: none;
        }

        /* =========================================================
           PRODUCT BLOCK
        ========================================================= */
        .block-product {
            background: #fff; border: 1px solid #e5e7eb; border-radius: 12px;
            overflow: hidden; transition: box-shadow 0.2s, transform 0.2s, border-color 0.2s;
            cursor: pointer;
        }
        .block-product:hover {
            box-shadow: 0 4px 16px rgba(37,99,235,0.1);
            transform: translateY(-2px); border-color: #bfdbfe;
        }
        .product-image-wrapper {
            width: 100%; height: 200px; background: #f3f4f6;
            display: flex; align-items: center; justify-content: center; overflow: hidden;
        }
        .product-image-wrapper img { width: 100%; height: 100%; object-fit: cover; }
        .product-image-placeholder {
            width: 56px; height: 56px; background: #eff6ff;
            border-radius: 10px; display: flex;
            align-items: center; justify-content: center; font-size: 26px;
        }
        .product-details { padding: 14px 16px 16px; }
        .product-badge {
            display: inline-flex; align-items: center; gap: 4px;
            background: #eff6ff; color: #2563eb;
            font-size: 11px; font-weight: 600;
            padding: 3px 8px; border-radius: 6px; margin-bottom: 8px; letter-spacing: 0.3px;
        }
        .product-title { font-size: 15px; font-weight: 600; color: #111827; margin-bottom: 10px; line-height: 1.4; }
        .product-price-section { display: flex; align-items: center; gap: 8px; margin-bottom: 14px; }
        .product-current-price { font-size: 18px; font-weight: 700; color: #2563eb; }
        .product-original-price { font-size: 13px; color: #9ca3af; text-decoration: line-through; }
        .product-discount-badge {
            background: #fee2e2; color: #dc2626;
            font-size: 11px; font-weight: 600; padding: 2px 6px; border-radius: 4px;
        }

        /* =========================================================
           EMPTY STATE
        ========================================================= */
        .empty-state { text-align: center; padding: 40px 20px; color: #9ca3af; }
        .empty-icon {
            width: 64px; height: 64px; background: #f3f4f6; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 12px; font-size: 24px;
        }

        /* =========================================================
           PRODUCT DETAIL MODAL
        ========================================================= */
        .product-detail-overlay {
            position: fixed; inset: 0; background: rgba(0,0,0,0.45);
            display: none; justify-content: center; align-items: center; z-index: 9999; padding: 20px;
        }
        .product-detail-box {
            position: relative; background: white; width: 100%; max-width: 420px;
            border-radius: 20px; overflow: hidden; box-shadow: 0 20px 50px rgba(0,0,0,0.15);
            display: flex; flex-direction: column; max-height: 90vh;
        }
        .product-detail-close {
            position: absolute; top: 12px; left: 12px; z-index: 10;
            background: rgba(255,255,255,0.9); backdrop-filter: blur(4px);
            border: none; border-radius: 50%;
            width: 35px; height: 35px; font-weight: bold; cursor: pointer; font-size: 14px;
        }
        .product-detail-image { width: 100%; height: 220px; background: #f3f4f6; flex-shrink: 0; }
        .product-detail-image img { width: 100%; height: 100%; object-fit: cover; }
        .product-detail-content {
            padding: 20px; display: flex; flex-direction: column;
            gap: 12px; overflow-y: auto;
        }
        .product-detail-content h2 { font-size: 20px; font-weight: 700; }
        .detail-price { display: flex; align-items: center; gap: 10px; }
        .final-price { font-size: 22px; font-weight: 700; color: #2563eb; }
        .original-price { text-decoration: line-through; color: #999; font-size: 14px; }
        .stock-info { font-size: 13px; color: #555; }
        .detail-description { font-size: 14px; color: #444; line-height: 1.6; }
        .detail-buttons {
            display: flex; gap: 10px; padding: 16px;
            border-top: 1px solid #e5e7eb; background: #fff; flex-shrink: 0;
        }
        .btn-cart {
            width: 48px; height: 48px; min-width: 48px;
            display: flex; align-items: center; justify-content: center;
            border: 1px solid #2563eb; border-radius: 10px; background: #fff;
            font-size: 20px; cursor: pointer; transition: all 0.2s; position: relative;
        }
        .btn-cart:hover { background: #eff6ff; }
        .btn-cart.loading { opacity: 0.6; pointer-events: none; }
        .btn-buy {
            flex: 1; padding: 12px; background: #2563eb; color: white;
            border-radius: 10px; font-weight: 600; font-size: 15px;
            border: none; cursor: pointer; transition: background 0.2s;
        }
        .btn-buy:hover { background: #1d4ed8; }

        /* =========================================================
           CART DRAWER
        ========================================================= */
        .cart-overlay {
            position: fixed; inset: 0; background: rgba(0,0,0,0.45);
            z-index: 500; opacity: 0; visibility: hidden; transition: all 0.3s;
        }
        .cart-overlay.active { opacity: 1; visibility: visible; }
        .cart-drawer {
            position: fixed; right: 0; top: 0; bottom: 0;
            width: 100%; max-width: 420px;
            background: #fff; z-index: 501;
            transform: translateX(100%); transition: transform 0.3s ease;
            display: flex; flex-direction: column; overflow: hidden;
        }
        .cart-drawer.active { transform: translateX(0); }

        .cart-header {
            display: flex; align-items: center; justify-content: space-between;
            padding: 16px 20px; border-bottom: 1px solid #e5e7eb; flex-shrink: 0;
        }
        .cart-header h3 { font-size: 17px; font-weight: 700; color: #111827; }
        .cart-close {
            width: 32px; height: 32px; border-radius: 50%;
            background: #f3f4f6; border: none; cursor: pointer;
            display: flex; align-items: center; justify-content: center; font-size: 14px;
        }
        .cart-close:hover { background: #e5e7eb; }

        .cart-items { flex: 1; overflow-y: auto; padding: 16px 20px; }

        .cart-item {
            display: flex; gap: 12px; padding: 12px 0;
            border-bottom: 1px solid #f3f4f6; animation: fadeInUp 0.2s ease;
        }
        .cart-item-img {
            width: 64px; height: 64px; border-radius: 10px;
            background: #f3f4f6; flex-shrink: 0; overflow: hidden;
        }
        .cart-item-img img { width: 100%; height: 100%; object-fit: cover; }
        .cart-item-img-placeholder {
            width: 100%; height: 100%;
            display: flex; align-items: center; justify-content: center; font-size: 24px;
        }
        .cart-item-info { flex: 1; min-width: 0; }
        .cart-item-title {
            font-size: 13px; font-weight: 600; color: #111827;
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis; margin-bottom: 4px;
        }
        .cart-item-price { font-size: 13px; font-weight: 700; color: #2563eb; margin-bottom: 8px; }
        .qty-control {
            display: inline-flex; align-items: center; gap: 0;
            border: 1px solid #e5e7eb; border-radius: 8px; overflow: hidden;
        }
        .qty-btn {
            width: 28px; height: 28px; border: none; background: #f9fafb;
            cursor: pointer; font-size: 15px; font-weight: 600;
            display: flex; align-items: center; justify-content: center;
            transition: background 0.2s; color: #374151;
        }
        .qty-btn:hover { background: #e5e7eb; }
        .qty-btn:disabled { opacity: 0.4; cursor: not-allowed; }
        .qty-value {
            min-width: 32px; text-align: center; font-size: 13px; font-weight: 600;
            padding: 0 4px; color: #111827; background: #fff;
        }
        .cart-item-remove {
            align-self: flex-start; margin-top: 2px; flex-shrink: 0;
            width: 26px; height: 26px; border-radius: 6px;
            background: none; border: none; cursor: pointer;
            display: flex; align-items: center; justify-content: center;
            color: #9ca3af; transition: all 0.2s;
        }
        .cart-item-remove:hover { background: #fee2e2; color: #dc2626; }

        .cart-empty {
            display: flex; flex-direction: column;
            align-items: center; justify-content: center;
            height: 200px; gap: 12px; color: #9ca3af;
        }
        .cart-empty-icon { font-size: 40px; }
        .cart-empty p { font-size: 14px; }

        .cart-footer {
            padding: 16px 20px; border-top: 1px solid #e5e7eb;
            flex-shrink: 0; background: #fff;
        }
        .cart-summary {
            display: flex; justify-content: space-between;
            align-items: center; margin-bottom: 14px;
        }
        .cart-summary-label { font-size: 13px; color: #6b7280; }
        .cart-summary-total { font-size: 18px; font-weight: 700; color: #111827; }
        .btn-checkout {
            width: 100%; padding: 14px; background: #2563eb; color: white;
            border: none; border-radius: 12px; font-size: 15px; font-weight: 600;
            cursor: pointer; transition: background 0.2s;
        }
        .btn-checkout:hover { background: #1d4ed8; }
        .btn-checkout:disabled { opacity: 0.5; cursor: not-allowed; }
        .btn-clear {
            width: 100%; padding: 10px; background: none; color: #dc2626;
            border: none; font-size: 13px; cursor: pointer;
            margin-top: 8px; transition: color 0.2s;
        }
        .btn-clear:hover { color: #b91c1c; }

        .cart-loading {
            display: flex; align-items: center; justify-content: center;
            height: 120px; color: #6b7280; font-size: 14px; gap: 8px;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        .spinner {
            width: 18px; height: 18px; border: 2px solid #e5e7eb;
            border-top-color: #2563eb; border-radius: 50%;
            animation: spin 0.6s linear infinite;
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(10px); }
            to   { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>

{{-- TOAST --}}
<div class="toast" id="toast"></div>

<!-- =========================================================
     NAVBAR
========================================================= -->
<div class="navbar">
    <div class="navbar-container">
        <div class="navbar-left">
            <div class="hamburger" id="hamburger">
                <span></span><span></span><span></span>
            </div>
            <div class="navbar-title">{{ $user->name }}</div>
        </div>
        <div class="navbar-right">
            <div class="nav-icon" id="cartBtn" title="Keranjang">
                <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                <span class="cart-badge" id="cartBadge">0</span>
            </div>
        </div>
    </div>
</div>

<!-- SIDEBAR OVERLAY -->
<div class="sidebar-overlay" id="sidebarOverlay"></div>

<!-- SIDEBAR -->
<div class="sidebar" id="sidebar">
    <div class="sidebar-header"><h3>Menu</h3></div>
    <div class="sidebar-menu">
        @if($user->pages && $user->pages->count() > 0)
            @foreach($user->pages as $userPage)
                <a href="#" class="menu-item {{ $loop->first ? 'active' : '' }}"
                   data-tab="page-{{ $userPage->id }}">
                    <span class="menu-text">{{ $userPage->title }}</span>
                </a>
            @endforeach
        @endif
    </div>
</div>

<!-- =========================================================
     MAIN CONTENT
========================================================= -->
<div class="container">

    @if($user->pages && $user->pages->count() > 0)
        @foreach($user->pages as $userPage)
            <div class="tab-content {{ $loop->first ? 'active' : '' }}"
                 id="tab-page-{{ $userPage->id }}">

                {{-- PROFILE --}}
                <div class="user-profile">
                    @if($user->avatar)
                        <img src="{{ asset('storage/' . $user->avatar) }}"
                             class="avatar" alt="{{ $user->name }}">
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

                        @if($block->type === 'text')
                            <div class="block block-text">{{ $block->content['text'] ?? '' }}</div>
                        @endif

                        @if($block->type === 'link')
                            <div class="block block-link">
                                <a href="{{ $block->content['url'] ?? '#' }}" target="_blank">
                                    {{ $block->content['title'] ?? 'Link' }}
                                </a>
                            </div>
                        @endif

                        @if($block->type === 'image')
                            <div class="block block-image">
                                <img src="{{ asset('storage/' . $block->content['image']) }}">
                            </div>
                        @endif

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
                                <iframe src="https://www.youtube.com/embed/{{ $videoId }}"
                                        allowfullscreen></iframe>
                            </div>
                        @endif

                        @if($block->type === 'product' && isset($block->content['product']))
                            @php
                                $product        = $block->content['product'];
                                $productPrice   = $product['price'] ?? 0;
                                $productDiscount= $product['discount'] ?? null;
                                $finalPrice     = $productDiscount ?? $productPrice;
                                $hasDiscount    = $productDiscount && $productDiscount < $productPrice;
                                $discountPercent= $hasDiscount
                                    ? round((($productPrice - $productDiscount) / $productPrice) * 100)
                                    : 0;
                            @endphp
                            <div class="block block-product"
                                 onclick="handleProductClick({{ $block->product_id ?? 'null' }})">
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
                                    <div class="product-title">{{ $product['title'] ?? 'Produk' }}</div>
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

<!-- =========================================================
     PRODUCT DETAIL MODAL
========================================================= -->
<div class="product-detail-overlay" id="productDetailModal">
    <div class="product-detail-box">

        <button class="product-detail-close" onclick="closeProductDetail()">✕</button>

        <div class="product-detail-image">
            <img id="detailImage" src="" alt="">
        </div>

        <div class="product-detail-content">
            <h2 id="detailTitle"></h2>
            <div class="detail-price">
                <span class="final-price" id="detailFinalPrice"></span>
                <span class="original-price" id="detailOriginalPrice"></span>
            </div>
            <div class="stock-info">Stok: <span id="detailStock"></span></div>
            <div class="detail-description">
                <strong style="font-size:13px;color:#6b7280;letter-spacing:.5px;">DESKRIPSI</strong>
                <p id="detailDescription" style="margin-top:6px;"></p>
            </div>
        </div>

        <div class="detail-buttons">
            <button class="btn-cart" id="btnAddToCart" title="Tambah ke keranjang">
                <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
            </button>
            <button class="btn-buy" id="buyNowBtn">Beli Sekarang</button>
        </div>

    </div>
</div>

<!-- =========================================================
     CART DRAWER
========================================================= -->
<div class="cart-overlay" id="cartOverlay"></div>
<div class="cart-drawer" id="cartDrawer">

    <div class="cart-header">
        <h3>🛒 Keranjang Belanja</h3>
        <button class="cart-close" onclick="closeCart()">✕</button>
    </div>

    <div class="cart-items" id="cartItems">
        <div class="cart-loading">
            <div class="spinner"></div> Memuat...
        </div>
    </div>

    <div class="cart-footer" id="cartFooter" style="display:none">
        <div class="cart-summary">
            <span class="cart-summary-label">Total Pembayaran</span>
            <span class="cart-summary-total" id="cartTotal">Rp 0</span>
        </div>
        <button class="btn-checkout" id="btnCheckout" onclick="handleCheckout()">
            Lanjut ke Pembayaran
        </button>
        <!-- <button class="btn-clear" onclick="clearCart()">Kosongkan Keranjang</button> -->
    </div>

</div>


<!-- =========================================================
     JAVASCRIPT
========================================================= -->
<script>
// ─────────────────────────────────────────────────────────────
// HELPERS
// ─────────────────────────────────────────────────────────────
const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

function formatRupiah(number) {
    return 'Rp ' + new Intl.NumberFormat('id-ID').format(Math.round(number));
}

let toastTimer;
function showToast(message, type = 'default') {
    const toast = document.getElementById('toast');
    toast.textContent = message;
    toast.className = `toast ${type} show`;
    clearTimeout(toastTimer);
    toastTimer = setTimeout(() => {
        toast.classList.remove('show');
    }, 2800);
}

async function apiCall(url, method = 'GET', body = null) {
    const options = {
        method,
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json',
        },
    };
    if (body) options.body = JSON.stringify(body);
    const res = await fetch(url, options);
    const data = await res.json();
    if (!res.ok) throw new Error(data.message || 'Terjadi kesalahan.');
    return data;
}

// ─────────────────────────────────────────────────────────────
// CART BADGE
// ─────────────────────────────────────────────────────────────
function updateBadge(count) {
    const badge = document.getElementById('cartBadge');
    badge.textContent = count;
    badge.classList.toggle('visible', count > 0);
}

// Ambil badge saat halaman load
(async () => {
    try {
        const data = await apiCall('/api/cart');
        updateBadge(data.total_items);
    } catch (_) {}
})();

// ─────────────────────────────────────────────────────────────
// HAMBURGER & SIDEBAR
// ─────────────────────────────────────────────────────────────
const hamburger     = document.getElementById('hamburger');
const sidebar       = document.getElementById('sidebar');
const sidebarOverlay= document.getElementById('sidebarOverlay');

function toggleSidebar() {
    hamburger.classList.toggle('active');
    sidebar.classList.toggle('active');
    sidebarOverlay.classList.toggle('active');
}
hamburger.addEventListener('click', toggleSidebar);
sidebarOverlay.addEventListener('click', toggleSidebar);

// ─────────────────────────────────────────────────────────────
// TAB SWITCHING
// ─────────────────────────────────────────────────────────────
document.querySelectorAll('.menu-item').forEach(item => {
    item.addEventListener('click', e => {
        e.preventDefault();
        const tab = item.dataset.tab;
        document.querySelectorAll('.menu-item').forEach(m => m.classList.remove('active'));
        item.classList.add('active');
        document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
        document.getElementById(`tab-${tab}`)?.classList.add('active');
        if (window.innerWidth < 768) toggleSidebar();
    });
});

// ─────────────────────────────────────────────────────────────
// PRODUCT DETAIL MODAL
// ─────────────────────────────────────────────────────────────
let currentProductId = null;

function handleProductClick(productId) {
    if (!productId) return;
    fetch(`/api/product/${productId}`)
        .then(r => r.json())
        .then(product => {
            currentProductId = product.id;

            document.getElementById('detailTitle').textContent       = product.title;
            document.getElementById('detailDescription').textContent = product.description ?? '';
            document.getElementById('detailStock').textContent       = product.stock ?? 0;

            // Sesudah — discount adalah harga diskon langsung
const finalPrice = (product.discount && product.discount > 0 && product.discount < product.price)
    ? product.discount
    : product.price;

const hasDiscount = finalPrice < product.price;

// document.getElementById('detailFinalPrice').textContent = formatRupiah(finalPrice);
// document.getElementById('detailOriginalPrice').textContent = hasDiscount
//     ? formatRupiah(product.price)
//     : '';

            document.getElementById('detailFinalPrice').textContent = formatRupiah(finalPrice);
            document.getElementById('detailOriginalPrice').textContent =
                product.discount > 0 ? formatRupiah(product.price) : '';

            const img = document.getElementById('detailImage');
            img.src = product.image_url ?? '';
            img.style.display = product.image_url ? 'block' : 'none';

            document.getElementById('buyNowBtn').onclick = () => {
                window.location.href = `/checkout/${product.id}`;
            };

            document.getElementById('productDetailModal').style.display = 'flex';
            document.body.style.overflow = 'hidden';
        })
        .catch(() => showToast('Gagal memuat produk.', 'error'));
}

function closeProductDetail() {
    document.getElementById('productDetailModal').style.display = 'none';
    document.body.style.overflow = 'auto';
    currentProductId = null;
}

// Tambah ke keranjang dari modal detail
document.getElementById('btnAddToCart').addEventListener('click', async () => {
    if (!currentProductId) return;
    const btn = document.getElementById('btnAddToCart');
    btn.classList.add('loading');
    try {
        const data = await apiCall('/api/cart/add', 'POST', {
            product_id: currentProductId,
            quantity: 1,
        });
        updateBadge(data.total_items);
        showToast('✓ Produk ditambahkan ke keranjang!', 'success');
    } catch (err) {
        showToast(err.message, 'error');
    } finally {
        btn.classList.remove('loading');
    }
});

// ─────────────────────────────────────────────────────────────
// CART DRAWER
// ─────────────────────────────────────────────────────────────
const cartOverlay = document.getElementById('cartOverlay');
const cartDrawer  = document.getElementById('cartDrawer');

function openCart() {
    cartOverlay.classList.add('active');
    cartDrawer.classList.add('active');
    document.body.style.overflow = 'hidden';
    loadCart();
}

function closeCart() {
    cartOverlay.classList.remove('active');
    cartDrawer.classList.remove('active');
    document.body.style.overflow = 'auto';
}

cartOverlay.addEventListener('click', closeCart);
document.getElementById('cartBtn').addEventListener('click', openCart);

async function loadCart() {
    const container = document.getElementById('cartItems');
    const footer    = document.getElementById('cartFooter');

    container.innerHTML = `<div class="cart-loading"><div class="spinner"></div> Memuat...</div>`;
    footer.style.display = 'none';

    try {
        const data = await apiCall('/api/cart');
        renderCartItems(data);
    } catch {
        container.innerHTML = `<div class="cart-empty">
            <div class="cart-empty-icon">⚠️</div>
            <p>Belum ada barang.</p>
        </div>`;
    }
}

function renderCartItems(data) {
    const container = document.getElementById('cartItems');
    const footer    = document.getElementById('cartFooter');
    const totalEl   = document.getElementById('cartTotal');
    const checkoutBtn = document.getElementById('btnCheckout');

    if (!data.items || data.items.length === 0) {
        container.innerHTML = `<div class="cart-empty">
            <div class="cart-empty-icon">🛒</div>
            <p>Keranjangmu masih kosong.</p>
        </div>`;
        footer.style.display = 'none';
        return;
    }

    container.innerHTML = data.items.map(item => `
        <div class="cart-item" id="cart-item-${item.id}">
            <div class="cart-item-img">
                ${item.image_url
                    ? `<img src="${item.image_url}" alt="${item.title}">`
                    : `<div class="cart-item-img-placeholder">🛍️</div>`}
            </div>
            <div class="cart-item-info">
                <div class="cart-item-title">${item.title}</div>
<div class="cart-item-price">
    ${formatRupiah(item.final_price)}
    ${item.has_discount
        ? `<span style="font-size:11px;color:#9ca3af;text-decoration:line-through;margin-left:4px;">${formatRupiah(item.original_price)}</span>`
        : ''}
</div>                <div class="qty-control">
                    <button class="qty-btn" onclick="changeQty(${item.id}, ${item.quantity - 1}, ${item.stock})"
                        ${item.quantity <= 1 ? 'disabled' : ''}>−</button>
                    <span class="qty-value" id="qty-${item.id}">${item.quantity}</span>
                    <button class="qty-btn" onclick="changeQty(${item.id}, ${item.quantity + 1}, ${item.stock})"
                        ${item.quantity >= item.stock ? 'disabled' : ''}>+</button>
                </div>
            </div>
            <button class="cart-item-remove" onclick="removeItem(${item.id})" title="Hapus">
                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
            </button>
        </div>
    `).join('');

    totalEl.textContent = formatRupiah(data.total_price);
    footer.style.display = 'block';
    checkoutBtn.disabled = data.items.length === 0;
}

async function changeQty(id, newQty, stock) {
    if (newQty < 1 || newQty > stock) return;
    try {
        const data = await apiCall(`/api/cart/${id}`, 'PATCH', { quantity: newQty });
        updateBadge(data.total_items);
        document.getElementById('cartTotal').textContent = formatRupiah(data.total_price);
        // Re-render hanya item ini
        document.getElementById(`qty-${id}`).textContent = newQty;
        // Refresh semua untuk update tombol disable
        renderCartItems(await apiCall('/api/cart'));
    } catch (err) {
        showToast(err.message, 'error');
    }
}

async function removeItem(id) {
    const itemEl = document.getElementById(`cart-item-${id}`);
    if (itemEl) {
        itemEl.style.opacity = '0.5';
        itemEl.style.transition = 'opacity 0.2s';
    }
    try {
        const data = await apiCall(`/api/cart/${id}`, 'DELETE');
        updateBadge(data.total_items);
        showToast('Produk dihapus dari keranjang.', 'default');
        renderCartItems(await apiCall('/api/cart'));
    } catch (err) {
        showToast(err.message, 'error');
        if (itemEl) itemEl.style.opacity = '1';
    }
}

async function clearCart() {
    if (!confirm('Kosongkan semua isi keranjang?')) return;
    try {
        await apiCall('/api/cart', 'DELETE');
        updateBadge(0);
        showToast('Keranjang dikosongkan.', 'default');
        renderCartItems({ items: [], total_price: 0 });
    } catch (err) {
        showToast(err.message, 'error');
    }
}

// Sesudah — checkout item pertama di keranjang
async function handleCheckout() {
    try {
        const data = await apiCall('/api/cart');

        if (!data.items || data.items.length === 0) {
            showToast('Keranjang kosong.', 'error');
            return;
        }

        // Jika hanya 1 produk, langsung ke checkout
        if (data.items.length === 1) {
            window.location.href = `/checkout/${data.items[0].product_id}`;
            return;
        }

        // Jika lebih dari 1 produk, checkout item pertama dulu
        // Atau bisa tampilkan pilihan ke user
        const first = data.items[0];
        const confirm = window.confirm(
            `Keranjang berisi ${data.items.length} produk.\n` +
            `Saat ini checkout dilakukan per produk.\n\n` +
            `Lanjut checkout "${first.title}"?`
        );

        if (confirm) {
            window.location.href = `/checkout/${first.product_id}`;
        }

    } catch (err) {
        showToast('Gagal memproses checkout.', 'error');
    }
}
</script>

</body>
</html>