<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>{{ $user->name }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: system-ui, -apple-system, sans-serif; background: #f9fafb; }

        .navbar {
            background: #fff; border-bottom: 1px solid #e5e7eb;
            position: sticky; top: 0; z-index: 100;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }
        .navbar-container {
            max-width: 420px; margin: 0 auto; padding: 12px 16px;
            display: flex; justify-content: space-between; align-items: center;
        }
        .navbar-left { display: flex; align-items: center; gap: 12px; }
        .hamburger {
            width: 32px; height: 32px;
            display: flex; flex-direction: column; justify-content: center; gap: 4px;
            cursor: pointer; padding: 4px; border-radius: 6px; transition: background 0.2s;
        }
        .hamburger:hover { background: #f3f4f6; }
        .hamburger span { width: 100%; height: 2px; background: #374151; border-radius: 2px; transition: all 0.3s ease; }
        .hamburger.active span:nth-child(1) { transform: rotate(45deg) translate(5px, 5px); }
        .hamburger.active span:nth-child(2) { opacity: 0; }
        .hamburger.active span:nth-child(3) { transform: rotate(-45deg) translate(5px, -5px); }
        .navbar-title { font-size: 16px; font-weight: 600; color: #111827; }
        .navbar-right { display: flex; gap: 8px; }
        .nav-icon {
            width: 36px; height: 36px; display: flex; align-items: center; justify-content: center;
            border-radius: 8px; cursor: pointer; transition: background 0.2s; position: relative;
        }
        .nav-icon:hover { background: #f3f4f6; }
        .cart-badge {
            position: absolute; top: 2px; right: 2px;
            background: #ef4444; color: white; font-size: 10px; font-weight: 600;
            width: 16px; height: 16px; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
        }

        .sidebar-overlay {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0,0,0,0.5); z-index: 200;
            opacity: 0; visibility: hidden; transition: all 0.3s ease;
        }
        .sidebar-overlay.active { opacity: 1; visibility: visible; }
        .sidebar {
            position: fixed; top: 0; left: 0; width: 280px; height: 100%;
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
        .menu-icon { width: 20px; height: 20px; display: flex; align-items: center; justify-content: center; }
        .menu-text { font-size: 14px; font-weight: 500; flex: 1; }

        .container { max-width: 420px; margin: 0 auto; padding: 24px 16px; }
        .tab-content { display: none; }
        .tab-content.active { display: block; }

        .user-profile {
            text-align: center; margin-bottom: 24px;
            padding-bottom: 20px; border-bottom: 1px solid #e5e7eb;
        }
        .avatar {
            width: 80px; height: 80px; border-radius: 50%; background: #e5e7eb;
            margin: 0 auto 10px; display: block; object-fit: cover;
            border: 3px solid #fff; box-shadow: 0 0 0 2px #e5e7eb;
        }
        .avatar-placeholder {
            width: 80px; height: 80px; border-radius: 50%; background: #e5e7eb;
            margin: 0 auto 10px; display: flex; align-items: center; justify-content: center;
            font-size: 28px; color: #9ca3af;
            border: 3px solid #fff; box-shadow: 0 0 0 2px #e5e7eb;
        }
        .profile-name    { font-size: 17px; font-weight: 700; color: #111827; margin-bottom: 2px; }
        .profile-username{ color: #6b7280; font-size: 13px; margin-bottom: 8px; }
        .profile-bio     { font-size: 13px; color: #374151; line-height: 1.5; }

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
        .block-video iframe { width: 100%; height: 200px; border-radius: 12px; border: none; }

        /* ── Product skeleton ── */
        .product-skeleton { background: #fff; border: 1px solid #e5e7eb; border-radius: 12px; overflow: hidden; }
        .skeleton-img  { width: 100%; height: 200px; background: linear-gradient(90deg,#f3f4f6 25%,#e5e7eb 50%,#f3f4f6 75%); background-size: 200% 100%; animation: shimmer 1.4s infinite; }
        .skeleton-body { padding: 14px 16px; }
        .skeleton-line { height: 12px; background: linear-gradient(90deg,#f3f4f6 25%,#e5e7eb 50%,#f3f4f6 75%); background-size: 200% 100%; animation: shimmer 1.4s infinite; border-radius: 6px; margin-bottom: 8px; }
        .skeleton-line.w60 { width: 60%; }
        .skeleton-line.w40 { width: 40%; }
        @keyframes shimmer { to { background-position: -200% 0; } }

        /* ── Product card ── */
        .block-product {
            background: #fff; border: 1px solid #e5e7eb; border-radius: 12px;
            overflow: hidden; transition: box-shadow 0.2s, transform 0.2s, border-color 0.2s; cursor: pointer;
        }
        .block-product:hover { box-shadow: 0 4px 16px rgba(37,99,235,0.1); transform: translateY(-2px); border-color: #bfdbfe; }
        .product-image-wrapper {
            width: 100%; height: 200px; background: #f3f4f6;
            display: flex; align-items: center; justify-content: center; overflow: hidden;
        }
        .product-image-wrapper img { width: 100%; height: 100%; object-fit: cover; }
        .product-image-placeholder {
            width: 56px; height: 56px; background: #eff6ff;
            border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 26px;
        }
        .product-details { padding: 14px 16px 16px; }
        .product-badge {
            display: inline-flex; align-items: center; gap: 4px;
            background: #eff6ff; color: #2563eb; font-size: 11px; font-weight: 600;
            padding: 3px 8px; border-radius: 6px; margin-bottom: 8px; letter-spacing: 0.3px;
        }
        .product-title   { font-size: 15px; font-weight: 600; color: #111827; margin-bottom: 10px; line-height: 1.4; }
        .product-price-section { display: flex; align-items: center; gap: 8px; margin-bottom: 14px; }
        .product-current-price  { font-size: 18px; font-weight: 700; color: #2563eb; }
        .product-original-price { font-size: 13px; color: #9ca3af; text-decoration: line-through; }
        .product-discount-badge { background: #fee2e2; color: #dc2626; font-size: 11px; font-weight: 600; padding: 2px 6px; border-radius: 4px; }
        .product-cta {
            display: flex; align-items: center; justify-content: center; gap: 8px;
            background: #2563eb; color: #fff; font-weight: 600; font-size: 14px;
            padding: 11px 16px; border-radius: 8px; text-decoration: none; transition: background 0.2s;
        }
        .product-cta:hover { background: #1d4ed8; }
        .product-cta svg { width: 16px; height: 16px; flex-shrink: 0; }

        .empty-state { text-align: center; padding: 40px 20px; color: #9ca3af; }
        .empty-icon  { width: 64px; height: 64px; background: #f3f4f6; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 12px; font-size: 24px; }

        .products-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 12px; }
        .product-card  { background: #fff; border-radius: 12px; overflow: hidden; border: 1px solid #e5e7eb; transition: all 0.2s; cursor: pointer; }
        .product-card:hover { box-shadow: 0 4px 12px rgba(0,0,0,0.08); transform: translateY(-2px); }
        .product-image { width: 100%; aspect-ratio: 1; background: #f3f4f6; display: flex; align-items: center; justify-content: center; }
        .product-image img { width: 100%; height: 100%; object-fit: cover; }
        .product-info  { padding: 12px; }
        .product-info .product-title { font-size: 14px; font-weight: 600; color: #111827; margin-bottom: 4px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
        .product-price { font-size: 14px; font-weight: 700; color: #2563eb; }
        .product-price-discount  { color: #ef4444; }
        .product-price-original  { font-size: 11px; text-decoration: line-through; color: #9ca3af; margin-left: 4px; }
    </style>
</head>
<body>

<div class="navbar">
    <div class="navbar-container">
        <div class="navbar-left">
            <div class="hamburger" id="hamburger">
                <span></span><span></span><span></span>
            </div>
            <div class="navbar-title">{{ $user->name }}</div>
        </div>
        <div class="navbar-right">
            <div class="nav-icon" id="cartBtn">
                <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                <span class="cart-badge">0</span>
            </div>
        </div>
    </div>
</div>

<div class="sidebar-overlay" id="sidebarOverlay"></div>
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

<div class="container">
    @if($user->pages && $user->pages->count() > 0)
        @foreach($user->pages as $userPage)
            <div class="tab-content {{ $loop->first ? 'active' : '' }}"
                 id="tab-page-{{ $userPage->id }}">

                <div class="user-profile">
                    @if($user->avatar)
                        <img src="{{ asset('storage/' . $user->avatar) }}" class="avatar" alt="{{ $user->name }}">
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

                        @elseif($block->type === 'link')
                            <div class="block block-link">
                                <a href="{{ $block->content['url'] ?? '#' }}" target="_blank">
                                    {{ $block->content['title'] ?? 'Link' }}
                                </a>
                            </div>

                        @elseif($block->type === 'image')
                            <div class="block block-image">
                                <img src="{{ asset('storage/' . $block->content['image']) }}">
                            </div>

                        @elseif($block->type === 'video')
                            @php
                                $url     = $block->content['url'] ?? '';
                                $videoId = '';
                                if ($url) {
                                    $p = parse_url($url);
                                    if (isset($p['host'])) {
                                        if (str_contains($p['host'], 'youtube.com') && isset($p['query'])) {
                                            parse_str($p['query'], $q); $videoId = $q['v'] ?? '';
                                        }
                                        if (str_contains($p['host'], 'youtu.be'))
                                            $videoId = ltrim($p['path'], '/');
                                        if (str_contains($p['path'] ?? '', '/shorts/')) {
                                            $seg = explode('/', trim($p['path'], '/'));
                                            $videoId = $seg[1] ?? '';
                                        }
                                    }
                                }
                                // fallback ke youtube_id jika ada
                                if (!$videoId) $videoId = $block->content['youtube_id'] ?? '';
                            @endphp
                            @if($videoId)
                                <div class="block block-video">
                                    <iframe src="https://www.youtube.com/embed/{{ $videoId }}"
                                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                            allowfullscreen></iframe>
                                </div>
                            @else
                                <div class="block block-text">Link video tidak valid.</div>
                            @endif

                        @elseif($block->type === 'product' && isset($block->product_id) && $block->product_id)
                            {{--
                                ✅ LIVE SYNC: Skeleton dulu, lalu fetch data terbaru via API.
                                Tidak pakai snapshot $block->content['product'] yang bisa basi.
                            --}}
                            <div class="block"
                                 id="preview-product-block-{{ $block->id }}"
                                 data-product-id="{{ $block->product_id }}">
                                <div class="product-skeleton">
                                    <div class="skeleton-img"></div>
                                    <div class="skeleton-body">
                                        <div class="skeleton-line"></div>
                                        <div class="skeleton-line w60"></div>
                                        <div class="skeleton-line w40"></div>
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

<script>
// ─── Hamburger ───
const hamburger      = document.getElementById('hamburger');
const sidebar        = document.getElementById('sidebar');
const sidebarOverlay = document.getElementById('sidebarOverlay');

function toggleSidebar() {
    hamburger.classList.toggle('active');
    sidebar.classList.toggle('active');
    sidebarOverlay.classList.toggle('active');
}
hamburger.addEventListener('click', toggleSidebar);
sidebarOverlay.addEventListener('click', toggleSidebar);

// ─── Tab switching ───
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

// ─── Cart button ───
document.getElementById('cartBtn').addEventListener('click', () => {
    alert('Fitur keranjang akan segera hadir!');
});

// ─── Product click ───
function handleProductClick(productId) {
    if (!productId) return;
    const isPreview = window.location.pathname.includes('/preview/');
    if (!isPreview) window.location.href = `/checkout/${productId}`;
}

// ─── Helpers ───
function fmtRp(n) {
    return 'Rp ' + new Intl.NumberFormat('id-ID').format(Math.round(n));
}
function escH(str) {
    if (!str) return '';
    const d = document.createElement('div');
    d.textContent = str;
    return d.innerHTML;
}

// ─────────────────────────────────────────────────────────────
// LIVE FETCH — render setiap blok produk dari API, bukan snapshot
// ─────────────────────────────────────────────────────────────
function renderProductBlocks() {
    document.querySelectorAll('[id^="preview-product-block-"]').forEach(container => {
        const productId = container.getAttribute('data-product-id');
        if (!productId) return;

        fetch(`/api/product/${productId}`)
            .then(r => r.json())
            .then(prod => {
                const price    = prod.price    ?? 0;
                const discount = prod.discount ?? null;
                const final    = (discount && discount > 0 && discount < price) ? discount : price;
                const hasDis   = final < price;
                const discPct  = hasDis ? Math.round(((price - final) / price) * 100) : 0;

                const imgHtml = prod.image_url
                    ? `<img src="${prod.image_url}" alt="${escH(prod.title)}">`
                    : `<div class="product-image-placeholder">🛍️</div>`;

                const badgeHtml = hasDis
                    ? `<span class="product-badge">Diskon ${discPct}%</span>` : '';

                const priceHtml = hasDis
                    ? `<div class="product-current-price">${fmtRp(final)}</div>
                       <div class="product-original-price">${fmtRp(price)}</div>
                       <div class="product-discount-badge">-${discPct}%</div>`
                    : `<div class="product-current-price">${fmtRp(final)}</div>`;

                container.innerHTML = `
                    <div class="block-product" onclick="handleProductClick(${prod.id})">
                        <div class="product-image-wrapper">${imgHtml}</div>
                        <div class="product-details">
                            ${badgeHtml}
                            <div class="product-title">${escH(prod.title)}</div>
                            <div class="product-price-section">${priceHtml}</div>
                        </div>
                    </div>`;
            })
            .catch(() => {
                container.innerHTML = `
                    <div style="background:#fff;border:1px solid #e5e7eb;border-radius:12px;
                                padding:20px;text-align:center;color:#9ca3af;font-size:13px;">
                        Produk tidak tersedia
                    </div>`;
            });
    });
}

document.addEventListener('DOMContentLoaded', renderProductBlocks);
</script>
</body>
</html>