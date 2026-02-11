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
           TABS NAVIGATION (Hanya untuk custom pages user)
        ========================================================= */
        .tabs-container {
            background: #fff;
            border-bottom: 1px solid #e5e7eb;
            position: sticky;
            top: 57px;
            z-index: 99;
        }

        .tabs-wrapper {
            max-width: 420px;
            margin: 0 auto;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .tabs-wrapper::-webkit-scrollbar {
            display: none;
        }

        .tabs {
            display: flex;
            padding: 0 16px;
            gap: 4px;
            min-width: min-content;
        }

        .tab {
            padding: 12px 16px;
            font-size: 14px;
            font-weight: 500;
            color: #6b7280;
            border-bottom: 2px solid transparent;
            cursor: pointer;
            white-space: nowrap;
            transition: all 0.2s;
        }

        .tab:hover {
            color: #374151;
        }

        .tab.active {
            color: #2563eb;
            border-bottom-color: #2563eb;
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

        .avatar {
            width: 96px;
            height: 96px;
            border-radius: 50%;
            background: #d1d5db;
            margin: 0 auto 12px;
            display: block;
        }

        h1 {
            text-align: center;
            font-size: 20px;
            margin: 8px 0 4px;
            color: #111827;
        }

        .username {
            text-align: center;
            color: #6b7280;
            font-size: 14px;
            margin-bottom: 12px;
        }

        .bio {
            text-align: center;
            font-size: 14px;
            margin-bottom: 24px;
            color: #374151;
            line-height: 1.5;
        }

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

        .product-title {
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
    </style>
</head>
<body>

<!-- ================= NAVBAR ================= -->
<!-- NAVBAR -->
<div class="navbar">
    <div class="navbar-container">

        <div class="navbar-left">
            <div class="hamburger" id="hamburger">
                <span></span>
                <span></span>
                <span></span>
            </div>

            <div class="navbar-title">
                {{ $user->name }}
            </div>
        </div>

        <div class="navbar-right">
            <div class="nav-icon" id="cartBtn">
                <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round"
                          stroke-linejoin="round"
                          stroke-width="2"
                          d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293
                             2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0
                             100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                <span class="cart-badge">0</span>
            </div>
        </div>

    </div>
</div>

<!-- OVERLAY -->
<div class="sidebar-overlay" id="sidebarOverlay"></div>

<!-- SIDEBAR -->
<div class="sidebar" id="sidebar">

    <div class="sidebar-header">
        <h3>Menu</h3>
    </div>

    <div class="sidebar-menu">
        @foreach($user->pages as $page)
            <a href="#"
               class="menu-item {{ $loop->first ? 'active' : '' }}"
               data-tab="page-{{ $page->id }}">
                <span class="menu-text">
                    {{ $page->title }}
                </span>
            </a>
        @endforeach
    </div>

</div>



<!-- TABS (Hanya untuk custom pages, bukan Home & Produk) -->
@if(isset($userPages) && $userPages->count() > 0)
<div class="tabs-container">
    <div class="tabs-wrapper">
        <div class="tabs">
            @foreach($userPages as $userPage)
                <div class="tab {{ $loop->first ? 'active' : '' }}" data-tab="page-{{ $userPage->id }}">
                    {{ $userPage->name ?? 'Halaman' }}
                </div>
            @endforeach
        </div>
    </div>
</div>
@endif

<!-- MAIN CONTENT -->
<div class="container">

    <!-- PROFILE HEADER (TAMPIL DI SEMUA PAGE) -->
    <div style="text-align:center; margin-bottom:28px;">

        @if($user->avatar)
            <img src="{{ asset('storage/' . $user->avatar) }}"
                 class="avatar"
                 style="object-fit:cover; margin:0 auto;">
        @else
            <div class="avatar"></div>
        @endif

        <h1 style="margin-top:12px;">
            {{ $user->name }}
        </h1>

        <div class="username">
            {{ '@' . $user->username }}
        </div>

        @if($user->bio)
            <div class="bio">
                {{ $user->bio }}
            </div>
        @endif

    </div>


    <!-- SEMUA PAGE -->
    @foreach($user->pages as $page)
        <div class="tab-content {{ $loop->first ? 'active' : '' }}"
             id="tab-page-{{ $page->id }}">

            @if($page->blocks->count() > 0)

                @foreach($page->blocks->sortBy('position') as $block)

                    @if($block->type === 'text')
                        <div class="block block-text">
                            {{ $block->content['text'] ?? '' }}
                        </div>
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
                            $url = $block->content['url'] ?? '';
                            parse_str(parse_url($url, PHP_URL_QUERY), $query);
                            $videoId = $query['v'] ?? '';
                            if (!$videoId && str_contains($url, 'youtu.be/')) {
                                $videoId = basename(parse_url($url, PHP_URL_PATH));
                            }
                        @endphp

                        <div class="block block-video">
                            <iframe
                                src="https://www.youtube.com/embed/{{ $videoId }}"
                                allowfullscreen>
                            </iframe>
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

</div>




<script>
document.addEventListener("DOMContentLoaded", function () {

    const hamburger = document.getElementById("hamburger");
    const sidebar = document.getElementById("sidebar");
    const overlay = document.getElementById("sidebarOverlay");
    const menuItems = document.querySelectorAll(".menu-item");
    const tabContents = document.querySelectorAll(".tab-content");

    function toggleSidebar() {
        sidebar.classList.toggle("active");
        overlay.classList.toggle("active");
        hamburger.classList.toggle("active");
    }

    if (hamburger) {
        hamburger.addEventListener("click", toggleSidebar);
    }

    if (overlay) {
        overlay.addEventListener("click", toggleSidebar);
    }

    menuItems.forEach(item => {
        item.addEventListener("click", function (e) {
            e.preventDefault();

            const tabName = this.dataset.tab;

            menuItems.forEach(m => m.classList.remove("active"));
            this.classList.add("active");

            tabContents.forEach(content => {
                content.classList.remove("active");
            });

            const target = document.getElementById("tab-" + tabName);
            if (target) {
                target.classList.add("active");
            }

            toggleSidebar();
        });
    });

});
</script>



</body>
</html>

