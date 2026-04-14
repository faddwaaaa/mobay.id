<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>{{ $user->name }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        /* ─── FIX IFRAME: semua fixed element center dengan benar ─── */
        html, body {
            width: 375px;
            max-width: 375px;
            overflow-x: hidden;
            scrollbar-width: none;
            -ms-overflow-style: none;
        }
        html::-webkit-scrollbar,
        body::-webkit-scrollbar {
            display: none;
        }

        body {
            font-family: system-ui, -apple-system, sans-serif;
            background: #f9fafb;
            min-height: 100vh;
        }

        .page-wrapper {
            width: 375px;
            max-width: 375px;
            background: #f9fafb;
            min-height: 100vh;
            position: relative;
            overflow-x: hidden;
            scrollbar-width: none;
        }
        .page-wrapper::-webkit-scrollbar { display: none; }

        /* ─── FIX: semua overlay/modal fixed pakai lebar 375px, mulai dari top:0 ─── */
        .product-detail-overlay,
        .cart-overlay,
        .fullmenu-overlay,
        .search-results-overlay {
            width: 375px !important;
            left: 0 !important;
            right: auto !important;
            top: 0 !important;
            bottom: 0 !important;
            max-width: 375px !important;
        }

        .search-results-panel {
            left: 0 !important;
            right: 0 !important;
            width: 375px !important;
            max-width: 375px !important;
        }

        .cart-drawer {
            left: 0 !important;
            right: auto !important;
            top: 0 !important;
            bottom: 0 !important;
            width: 375px !important;
            max-width: 375px !important;
            transform: translateX(100%) !important;
        }
        .cart-drawer.active {
            transform: translateX(0) !important;
        }

        /* Modal produk - overlay full, box tepat center */
        .product-detail-overlay {
            display: none;
            justify-content: center !important;
            align-items: center !important;
            padding: 0 20px !important;
            box-sizing: border-box !important;
        }
        .product-detail-box {
            width: 100% !important;
            max-width: 100% !important;
            margin: 0 !important;
            position: relative !important;
            left: 0 !important;
            right: 0 !important;
            transform: none !important;
            float: none !important;
        }

        .toast {
            position: fixed;
            bottom: 24px;
            left: 20px !important;
            right: 20px !important;
            width: auto !important;
            transform: translateY(80px);
            background: #111827; color: #fff;
            padding: 10px 20px; border-radius: 50px;
            font-size: 13px; font-weight: 500; z-index: 9999;
            opacity: 0; transition: all 0.35s cubic-bezier(.34,1.56,.64,1);
            white-space: nowrap; pointer-events: none;
            text-align: center;
        }
        .toast.show { opacity: 1; transform: translateY(0); }
        .toast.success { background: #16a34a; }
        .toast.error   { background: #dc2626; }

        .navbar {
            background: #fff; border-bottom: 1px solid #e5e7eb;
            position: sticky; top: 0; z-index: 100;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }
        .navbar-container {
            padding: 12px 16px;
            display: flex; justify-content: space-between; align-items: center;
        }
        .navbar-left  { display: flex; align-items: center; gap: 12px; }
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

        .hamburger {
            width: 34px; height: 34px;
            display: grid;
            grid-template-columns: repeat(3, 4px);
            grid-template-rows: repeat(3, 4px);
            gap: 4px;
            place-content: center;
            cursor: pointer;
            border-radius: 8px;
        }
        .hamburger:active { transform: scale(0.93); }
        .hamburger span {
            width: 4px; height: 4px;
            background: #374151;
            border-radius: 50%;
            display: block;
        }

        .search-bar-wrap {
            position: sticky; top: 61px; z-index: 99;
            background: #fff;
            border-bottom: 1px solid #e5e7eb;
            max-height: 0; overflow: hidden;
            transition: max-height 0.35s cubic-bezier(.4,0,.2,1), box-shadow 0.35s ease;
        }
        .search-bar-wrap.open {
            max-height: 72px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.06);
        }
        .search-bar-inner {
            padding: 12px 16px;
            display: flex; align-items: center; gap: 8px;
        }
        .search-back-btn {
            width: 34px; height: 34px; flex-shrink: 0;
            display: flex; align-items: center; justify-content: center;
            border: none; background: none; cursor: pointer;
            border-radius: 8px; color: #6b7280; transition: background 0.2s;
        }
        .search-back-btn:hover { background: #f3f4f6; }
        .search-input-wrap { flex: 1; position: relative; }
        .search-input {
            width: 100%; height: 40px;
            padding: 0 38px 0 14px;
            border: 1.5px solid #e5e7eb; border-radius: 10px;
            font-size: 14px; color: #111827; background: #f9fafb;
            outline: none; transition: border-color 0.2s, background 0.2s;
            -webkit-appearance: none;
            appearance: none;
        }
        .search-input:focus { border-color: #2563eb; background: #fff; }
        .search-input::placeholder { color: #9ca3af; }
        .search-input::-webkit-search-decoration,
        .search-input::-webkit-search-cancel-button,
        .search-input::-webkit-search-results-button,
        .search-input::-webkit-search-results-decoration {
            -webkit-appearance: none;
            appearance: none;
            display: none !important;
        }
        .search-clear-btn {
            position: absolute; right: 10px; top: 50%; transform: translateY(-50%);
            width: 20px; height: 20px; border-radius: 50%;
            background: #d1d5db; color: #fff;
            border: none; cursor: pointer; display: none;
            align-items: center; justify-content: center;
            padding: 0;
        }
        .search-clear-btn svg { width: 10px; height: 10px; }
        .search-clear-btn.visible { display: flex; }
        .search-clear-btn:hover { background: #9ca3af; }

        .search-results-overlay {
            position: fixed; inset: 0;
            background: rgba(0,0,0,0.35);
            backdrop-filter: blur(2px);
            z-index: 98;
            opacity: 0; visibility: hidden;
            transition: opacity 0.25s, visibility 0.25s;
        }
        .search-results-overlay.active { opacity: 1; visibility: visible; }

        .search-results-panel {
            position: absolute;
            top: 133px;
            left: 0; right: 0;
            max-height: calc(100vh - 153px);
            background: #fff; border-radius: 0 0 16px 16px;
            overflow-y: auto; z-index: 99;
            box-shadow: 0 8px 32px rgba(0,0,0,0.12);
            padding-bottom: 12px;
            opacity: 0; transform: translateY(-8px);
            transition: opacity 0.25s, transform 0.25s;
            pointer-events: none;
        }
        .search-results-panel.active {
            opacity: 1; transform: translateY(0);
            pointer-events: auto;
        }

        .search-result-item {
            display: flex; align-items: center; gap: 12px;
            padding: 12px 16px; cursor: pointer;
            transition: background 0.15s;
            border-bottom: 1px solid #f9fafb;
        }
        .search-result-item:last-child { border-bottom: none; }
        .search-result-item:hover { background: #f9fafb; }

        .search-result-thumb {
            width: 46px; height: 46px; border-radius: 10px;
            flex-shrink: 0; overflow: hidden;
            display: flex; align-items: center; justify-content: center;
        }
        .search-result-thumb img { width: 100%; height: 100%; object-fit: cover; }
        .thumb-product { background: #eff6ff; color: #2563eb; }
        .thumb-link    { background: #f0fdf4; color: #16a34a; }
        .thumb-text    { background: #fffbeb; color: #d97706; }
        .thumb-other   { background: #f3f4f6; color: #6b7280; }

        .search-result-info { flex: 1; min-width: 0; }
        .search-result-title {
            font-size: 14px; font-weight: 600; color: #111827;
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis; margin-bottom: 2px;
        }
        .search-result-title mark {
            background: #dbeafe; color: #1d4ed8;
            border-radius: 2px; padding: 0 1px; font-weight: 700;
        }
        .search-result-meta  { font-size: 12px; color: #6b7280; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .search-result-price { font-size: 13px; font-weight: 700; color: #2563eb; }
        .search-result-type-badge {
            font-size: 10px; font-weight: 600; padding: 2px 7px; border-radius: 20px;
            text-transform: uppercase; letter-spacing: 0.4px; flex-shrink: 0;
        }
        .badge-product { background: #dbeafe; color: #2563eb; }
        .badge-link    { background: #dcfce7; color: #16a34a; }
        .badge-text    { background: #fef9c3; color: #a16207; }
        .badge-other   { background: #f3f4f6; color: #6b7280; }

        .search-state {
            display: flex; flex-direction: column; align-items: center;
            justify-content: center; padding: 32px 20px; gap: 8px;
            color: #9ca3af; text-align: center;
        }
        .search-state-icon { margin-bottom: 4px; display: flex; align-items: center; justify-content: center; }
        .search-state p { font-size: 13px; }
        .search-state strong { display: block; font-size: 15px; color: #374151; margin-bottom: 4px; }
        .search-section-label {
            font-size: 11px; font-weight: 700; color: #9ca3af;
            letter-spacing: 0.8px; text-transform: uppercase;
            padding: 10px 16px 4px;
        }

        .fullmenu-overlay {
            position: fixed; inset: 0;
            background: rgba(0, 0, 0, 0.85);
            backdrop-filter: blur(4px);
            z-index: 500;
            opacity: 0; visibility: hidden;
            transition: opacity 0.25s ease, visibility 0.25s ease;
            display: flex; flex-direction: column;
            align-items: center;
        }
        .fullmenu-overlay.active { opacity: 1; visibility: visible; }

        .fullmenu-body {
            flex: 1; display: flex; flex-direction: column;
            align-items: center; justify-content: center;
            overflow-y: auto; width: 100%; padding: 60px 20px 40px;
        }

        .fullmenu-close-wrap {
            width: 100%; max-width: 320px;
            position: absolute; top: 16px;
            left: 50%; transform: translateX(-50%);
            display: flex; justify-content: flex-start;
        }
        .fullmenu-close {
            width: 36px; height: 36px; border-radius: 8px;
            background: rgba(255,255,255,0.1); border: none;
            color: #fff; font-size: 18px; cursor: pointer;
            display: flex; align-items: center; justify-content: center;
        }
        .fullmenu-close:hover { background: #ff0000; }

        .fullmenu-section-label {
            font-size: 10px; font-weight: 700; color: rgba(255,255,255,0.35);
            letter-spacing: 1px; text-transform: uppercase;
            text-align: center; margin-bottom: 8px; margin-top: 24px;
            width: 100%; max-width: 320px;
        }
        .fullmenu-section-label:first-child { margin-top: 0; }

        .fullmenu-item {
            display: flex; align-items: center; justify-content: center; gap: 12px;
            padding: 14px 32px; color: rgba(255,255,255,0.85);
            cursor: pointer; border-radius: 12px;
            font-size: 17px; font-weight: 500;
            width: 100%; max-width: 320px; text-align: center;
            transform: translateY(10px); opacity: 0;
            transition: transform 0.25s ease, opacity 0.25s ease, background 0.15s, color 0.15s;
        }
        .fullmenu-overlay.active .fullmenu-item { transform: translateY(0); opacity: 1; }
        .fullmenu-overlay.active .fullmenu-item:nth-child(1) { transition-delay: 0.05s; }
        .fullmenu-overlay.active .fullmenu-item:nth-child(2) { transition-delay: 0.10s; }
        .fullmenu-overlay.active .fullmenu-item:nth-child(3) { transition-delay: 0.15s; }
        .fullmenu-overlay.active .fullmenu-item:nth-child(4) { transition-delay: 0.20s; }
        .fullmenu-overlay.active .fullmenu-item:nth-child(5) { transition-delay: 0.25s; }

        .fullmenu-item:hover { background: rgba(255,255,255,0.08); color: #fff; }
        .fullmenu-item.active { color: #fff; background: rgba(59,130,246,0.2); }
        .fullmenu-item svg { width: 18px; height: 18px; flex-shrink: 0; opacity: 0.7; }
        .fullmenu-item.active svg { opacity: 1; }

        .fullmenu-divider {
            height: 1px; background: rgba(255,255,255,0.08);
            width: 100%; max-width: 320px; margin: 12px 0;
        }

        .container { padding: 24px 16px; }
        .tab-content { display: none; }
        .tab-content.active { display: block; }

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
            color: #9ca3af;
            border: 3px solid #fff; box-shadow: 0 0 0 2px #e5e7eb;
        }
        .profile-name     { font-size: 17px; font-weight: 700; color: #111827; margin-bottom: 2px; }
        .profile-username { color: #6b7280; font-size: 13px; margin-bottom: 8px; }
        .profile-bio      { font-size: 13px; color: #374151; line-height: 1.5; }

        .block { margin-bottom: 12px; }
        .block-text  { font-size: 14px; text-align: center; color: #374151; line-height: 1.6; }
        .block-link a {
            display: block; padding: 14px; border-radius: 12px;
            border: 1px solid #e5e7eb; text-align: center;
            text-decoration: none; color: #111; font-weight: 500;
            transition: all 0.2s; background: #fff;
        }
        .block-link a:hover { border-color: #2563eb; background: #eff6ff; }
        .block-image img   { width: 100%; border-radius: 12px; }
        .block-video iframe { width: 100%; height: 200px; border-radius: 12px; border: none; }

        .product-skeleton { background: #fff; border: 1px solid #e5e7eb; border-radius: 12px; overflow: hidden; }
        .skeleton-img  { width: 100%; height: 200px; background: linear-gradient(90deg,#f3f4f6 25%,#e5e7eb 50%,#f3f4f6 75%); background-size: 200% 100%; animation: shimmer 1.4s infinite; }
        .skeleton-body { padding: 14px 16px; }
        .skeleton-line { height: 12px; background: linear-gradient(90deg,#f3f4f6 25%,#e5e7eb 50%,#f3f4f6 75%); background-size: 200% 100%; animation: shimmer 1.4s infinite; border-radius: 6px; margin-bottom: 8px; }
        .skeleton-line.w60 { width: 60%; }
        .skeleton-line.w40 { width: 40%; }
        @keyframes shimmer { to { background-position: -200% 0; } }

        .block-product {
            background: #fff; border: 1px solid #e5e7eb; border-radius: 12px;
            overflow: hidden; transition: box-shadow 0.2s, transform 0.2s, border-color 0.2s; cursor: pointer;
        }
        .block-product:hover { box-shadow: 0 4px 16px rgba(37,99,235,0.1); transform: translateY(-2px); border-color: #bfdbfe; }
        .product-image-wrapper { width: 100%; height: 200px; background: #f3f4f6; display: flex; align-items: center; justify-content: center; overflow: hidden; }
        .product-image-wrapper img { width: 100%; height: 100%; object-fit: cover; }
        .product-image-placeholder { width: 56px; height: 56px; background: #eff6ff; border-radius: 10px; display: flex; align-items: center; justify-content: center; color: #2563eb; }
        .product-details { padding: 14px 16px 16px; }
        .product-badge { display: inline-flex; align-items: center; gap: 4px; background: #eff6ff; color: #2563eb; font-size: 11px; font-weight: 600; padding: 3px 8px; border-radius: 6px; margin-bottom: 8px; letter-spacing: 0.3px; }
        .product-title  { font-size: 15px; font-weight: 600; color: #111827; margin-bottom: 10px; line-height: 1.4; }
        .product-price-section { display: flex; align-items: center; gap: 8px; margin-bottom: 14px; }
        .product-current-price  { font-size: 18px; font-weight: 700; color: #2563eb; }
        .product-original-price { font-size: 13px; color: #9ca3af; text-decoration: line-through; }
        .product-discount-badge { background: #fee2e2; color: #dc2626; font-size: 11px; font-weight: 600; padding: 2px 6px; border-radius: 4px; }

        .empty-state { text-align: center; padding: 40px 20px; color: #9ca3af; }
        .empty-icon  { width: 64px; height: 64px; background: #f3f4f6; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 12px; color: #d1d5db; }

        .product-detail-overlay {
            position: fixed;
            background: rgba(0,0,0,0.55);
            z-index: 9999;
        }
        .product-detail-box {
            position: relative; background: white;
            width: 100%;
            border-radius: 20px; overflow: hidden; box-shadow: 0 20px 50px rgba(0,0,0,0.2);
            display: flex; flex-direction: column; max-height: 85vh;
        }
        .product-detail-close {
            position: absolute; top: 12px; left: 12px; z-index: 10;
            background: rgba(255,255,255,0.9); backdrop-filter: blur(4px);
            border: none; border-radius: 50%;
            width: 36px; height: 36px; font-weight: bold; cursor: pointer; font-size: 16px;
        }
        .product-detail-image  { width: 100%; height: 220px; background: #f3f4f6; flex-shrink: 0; }
        .product-detail-image img { width: 100%; height: 100%; object-fit: cover; }
        .product-detail-content { padding: 20px; display: flex; flex-direction: column; gap: 12px; overflow-y: auto; }
        .product-detail-content h2 { font-size: 22px; font-weight: 700; }
        .detail-price   { display: flex; align-items: center; gap: 10px; }
        .final-price    { font-size: 24px; font-weight: 700; color: #2563eb; }
        .original-price { text-decoration: line-through; color: #999; font-size: 16px; }
        .discount-badge-detail { background: #fee2e2; color: #dc2626; font-size: 13px; font-weight: 600; padding: 3px 8px; border-radius: 6px; }
        .stock-info     { font-size: 15px; color: #555; }
        .detail-description { font-size: 15px; color: #444; line-height: 1.6; }
        .detail-buttons { display: flex; gap: 10px; padding: 16px; border-top: 1px solid #e5e7eb; background: #fff; flex-shrink: 0; }
        .btn-cart {
            width: 52px; height: 52px; min-width: 52px;
            display: flex; align-items: center; justify-content: center;
            border: 1px solid #2563eb; border-radius: 10px; background: #fff;
            font-size: 20px; cursor: pointer; transition: all 0.2s;
        }
        .btn-cart:hover   { background: #eff6ff; }
        .btn-cart.loading { opacity: 0.6; pointer-events: none; }
        .btn-buy { flex: 1; padding: 14px; background: #2563eb; color: white; border-radius: 10px; font-weight: 600; font-size: 16px; border: none; cursor: pointer; transition: background 0.2s; }
        .btn-buy:hover { background: #1d4ed8; }

        .cart-overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.45); z-index: 500; opacity: 0; visibility: hidden; transition: all 0.3s; }
        .cart-overlay.active { opacity: 1; visibility: visible; }
        .cart-drawer { position: fixed; top: 0; bottom: 0; width: 375px; max-width: 375px; background: #fff; z-index: 501; transform: translateX(100%); transition: transform 0.3s ease; display: flex; flex-direction: column; overflow: hidden; }
        .cart-drawer.active { transform: translateX(0); }
        .cart-header { display: flex; align-items: center; justify-content: space-between; padding: 16px 20px; border-bottom: 1px solid #e5e7eb; flex-shrink: 0; }
        .cart-header h3 { font-size: 17px; font-weight: 700; color: #111827; }
        .cart-close { width: 32px; height: 32px; border-radius: 50%; background: #f3f4f6; border: none; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 14px; }
        .cart-close:hover { background: #e5e7eb; }
        .cart-items { flex: 1; overflow-y: auto; padding: 16px 20px; }
        .cart-item { display: flex; gap: 12px; padding: 12px 0; border-bottom: 1px solid #f3f4f6; animation: fadeInUp 0.2s ease; }
        .cart-item-img { width: 64px; height: 64px; border-radius: 10px; background: #f3f4f6; flex-shrink: 0; overflow: hidden; }
        .cart-item-img img { width: 100%; height: 100%; object-fit: cover; }
        .cart-item-img-placeholder { width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; color: #9ca3af; }
        .cart-item-info { flex: 1; min-width: 0; }
        .cart-item-title { font-size: 13px; font-weight: 600; color: #111827; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; margin-bottom: 4px; }
        .cart-item-price { font-size: 13px; font-weight: 700; color: #2563eb; margin-bottom: 8px; }
        .qty-control { display: inline-flex; align-items: center; border: 1px solid #e5e7eb; border-radius: 8px; overflow: hidden; }
        .qty-btn { width: 28px; height: 28px; border: none; background: #f9fafb; cursor: pointer; font-size: 15px; font-weight: 600; display: flex; align-items: center; justify-content: center; transition: background 0.2s; color: #374151; }
        .qty-btn:hover { background: #e5e7eb; }
        .qty-btn:disabled { opacity: 0.4; cursor: not-allowed; }
        .qty-value { min-width: 32px; text-align: center; font-size: 13px; font-weight: 600; padding: 0 4px; color: #111827; background: #fff; }
        .cart-item-remove { align-self: flex-start; margin-top: 2px; flex-shrink: 0; width: 26px; height: 26px; border-radius: 6px; background: none; border: none; cursor: pointer; display: flex; align-items: center; justify-content: center; color: #9ca3af; transition: all 0.2s; }
        .cart-item-remove:hover { background: #fee2e2; color: #dc2626; }
        .cart-empty { display: flex; flex-direction: column; align-items: center; justify-content: center; height: 200px; gap: 12px; color: #9ca3af; }
        .cart-empty p { font-size: 14px; }
        .cart-footer { padding: 16px 20px; border-top: 1px solid #e5e7eb; flex-shrink: 0; background: #fff; }
        .cart-summary { display: flex; justify-content: space-between; align-items: center; margin-bottom: 14px; }
        .cart-summary-label { font-size: 13px; color: #6b7280; }
        .cart-summary-total { font-size: 18px; font-weight: 700; color: #111827; }
        .btn-checkout { width: 100%; padding: 14px; background: #2563eb; color: white; border: none; border-radius: 12px; font-size: 15px; font-weight: 600; cursor: pointer; transition: background 0.2s; }
        .btn-checkout:hover { background: #1d4ed8; }
        .btn-checkout:disabled { opacity: 0.5; cursor: not-allowed; }
        .cart-loading { display: flex; align-items: center; justify-content: center; height: 120px; color: #6b7280; font-size: 14px; gap: 8px; }

        @keyframes spin { to { transform: rotate(360deg); } }
        .spinner { width: 18px; height: 18px; border: 2px solid #e5e7eb; border-top-color: #2563eb; border-radius: 50%; animation: spin 0.6s linear infinite; }
        @keyframes fadeInUp { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    </style>
</head>
<body>

@php
    $requestedPageId = request()->query('page');
@endphp

<div class="toast" id="toast"></div>

<div class="page-wrapper">

{{-- ═══════════ NAVBAR ═══════════ --}}
<div class="navbar">
    <div class="navbar-container">
        <div class="navbar-left">
            <div class="hamburger" id="hamburger">
                <span></span><span></span><span></span>
                <span></span><span></span><span></span>
                <span></span><span></span><span></span>
            </div>
            <div class="navbar-title">{{ $user->name }}</div>
        </div>
        <div class="navbar-right">
            <div class="nav-icon" id="searchBtn" title="Cari">
                <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z"/>
                </svg>
            </div>
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

{{-- ═══════════ SEARCH BAR ═══════════ --}}
<div class="search-bar-wrap" id="searchBarWrap">
    <div class="search-bar-inner">
        <button class="search-back-btn" id="searchBackBtn" title="Tutup">
            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </button>
        <div class="search-input-wrap">
            <input type="text" class="search-input" id="searchInput"
                   placeholder="Cari produk, link, konten..." autocomplete="off">
            <button class="search-clear-btn" id="searchClearBtn" title="Hapus">
                <svg viewBox="0 0 10 10" fill="currentColor">
                    <path d="M6.41 5l2.3-2.29a1 1 0 00-1.42-1.42L5 3.59 2.71 1.29A1 1 0 001.29 2.71L3.59 5 1.29 7.29a1 1 0 001.42 1.42L5 6.41l2.29 2.3a1 1 0 001.42-1.42z"/>
                </svg>
            </button>
        </div>
    </div>
</div>

<div class="search-results-overlay" id="searchResultsOverlay"></div>
<div class="search-results-panel" id="searchResultsPanel"></div>

{{-- ═══════════ FULLSCREEN MENU ═══════════ --}}
<div class="fullmenu-overlay" id="fullmenuOverlay">
    <div class="fullmenu-close-wrap">
        <button class="fullmenu-close" id="fullmenuClose">✕</button>
    </div>
    <div class="fullmenu-body">
        @if($user->pages && $user->pages->count() > 0)
            <div class="fullmenu-section-label">Halaman</div>
            @foreach($user->pages as $userPage)
                @php
                    $isActiveMenu = $requestedPageId
                        ? (string)$userPage->id === (string)$requestedPageId
                        : $loop->first;
                @endphp
                <div class="fullmenu-item {{ $isActiveMenu ? 'active' : '' }}"
                     data-tab="page-{{ $userPage->id }}">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    {{ $userPage->title }}
                </div>
            @endforeach
        @endif
        <div class="fullmenu-divider"></div>
        <div class="fullmenu-section-label">Ruang Pengguna</div>
        <div class="fullmenu-item">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
            </svg>
            Masuk / Daftar
        </div>
    </div>
</div>

{{-- ═══════════ KONTEN UTAMA ═══════════ --}}
<div class="container">
    @if($user->pages && $user->pages->count() > 0)
        @foreach($user->pages as $userPage)
            @php
                $isActiveTab = $requestedPageId
                    ? (string)$userPage->id === (string)$requestedPageId
                    : $loop->first;
            @endphp
            <div class="tab-content {{ $isActiveTab ? 'active' : '' }}"
                 id="tab-page-{{ $userPage->id }}">

                <div class="user-profile">
                    @if($user->avatar)
                        <img src="{{ asset('storage/' . $user->avatar) }}" class="avatar" alt="{{ $user->name }}">
                    @else
                        <div class="avatar-placeholder">
                            <svg width="32" height="32" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                      d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
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
                            <div class="block block-text" id="block-{{ $block->id }}">{{ $block->content['text'] ?? '' }}</div>

                        @elseif($block->type === 'link')
                            <div class="block block-link" id="block-{{ $block->id }}">
                                <a href="{{ $block->content['url'] ?? '#' }}" target="_blank">
                                    {{ $block->content['title'] ?? 'Link' }}
                                </a>
                            </div>

                        @elseif($block->type === 'image')
                            <div class="block block-image" id="block-{{ $block->id }}">
                                <img src="{{ asset('storage/' . $block->content['image']) }}">
                            </div>

                        @elseif($block->type === 'video')
                            @php
                                $videoId = $block->content['youtube_id'] ?? '';
                                if (!$videoId) {
                                    $url = $block->content['youtube_url'] ?? '';
                                    parse_str(parse_url($url, PHP_URL_QUERY), $query);
                                    $videoId = $query['v'] ?? '';
                                    if (!$videoId && str_contains($url, 'youtu.be/'))
                                        $videoId = basename(parse_url($url, PHP_URL_PATH));
                                }
                            @endphp
                            <div class="block block-video" id="block-{{ $block->id }}">
                                <iframe src="https://www.youtube.com/embed/{{ $videoId }}" allowfullscreen></iframe>
                            </div>

                        @elseif($block->type === 'product' && isset($block->product_id) && $block->product_id)
                            <div class="block" id="product-block-{{ $block->id }}" data-product-id="{{ $block->product_id }}">
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
                        <div class="empty-icon">
                            <svg width="28" height="28" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                      d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        Halaman ini belum memiliki konten.
                    </div>
                @endif
            </div>
        @endforeach
    @else
        <div class="empty-state">
            <div class="empty-icon">
                <svg width="28" height="28" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                </svg>
            </div>
            Belum ada halaman.
        </div>
    @endif
</div>

{{-- ═══════════ PRODUCT DETAIL MODAL ═══════════ --}}
<div class="product-detail-overlay" id="productDetailModal">
    <div class="product-detail-box">
        <button class="product-detail-close" onclick="closeProductDetail()">✕</button>
        <div class="product-detail-image">
            <img id="detailImage" src="" alt="" style="display:none;">
        </div>
        <div class="product-detail-content">
            <h2 id="detailTitle"></h2>
            <div class="detail-price">
                <span class="final-price"    id="detailFinalPrice"></span>
                <span class="original-price" id="detailOriginalPrice"></span>
                <span class="discount-badge-detail" id="detailDiscountBadge" style="display:none;"></span>
            </div>
            <div class="stock-info" id="detailStockWrap">Stok: <span id="detailStock"></span></div>
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

{{-- ═══════════ CART DRAWER ═══════════ --}}
<div class="cart-overlay" id="cartOverlay"></div>
<div class="cart-drawer" id="cartDrawer">
    <div class="cart-header">
        <h3>Keranjang Belanja</h3>
        <button class="cart-close" onclick="closeCart()">✕</button>
    </div>
    <div class="cart-items" id="cartItems">
        <div class="cart-loading"><div class="spinner"></div> Memuat...</div>
    </div>
    <div class="cart-footer" id="cartFooter" style="display:none">
        <div class="cart-summary">
            <span class="cart-summary-label">Total Pembayaran</span>
            <span class="cart-summary-total" id="cartTotal">Rp 0</span>
        </div>
        <button class="btn-checkout" id="btnCheckout" onclick="handleCheckout()">
            Lanjut ke Pembayaran
        </button>
    </div>
</div>
<script>
const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

function formatRupiah(n) {
    return 'Rp ' + new Intl.NumberFormat('id-ID').format(Math.round(n));
}
let toastTimer;
function showToast(msg, type = 'default') {
    const t = document.getElementById('toast');
    t.textContent = msg;
    t.className = `toast ${type} show`;
    clearTimeout(toastTimer);
    toastTimer = setTimeout(() => t.classList.remove('show'), 2800);
}
async function apiCall(url, method = 'GET', body = null) {
    const opts = {
        method,
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }
    };
    if (body) opts.body = JSON.stringify(body);
    const res  = await fetch(url, opts);
    const data = await res.json();
    if (!res.ok) throw new Error(data.message || 'Terjadi kesalahan.');
    return data;
}
function escHtml(str) {
    if (!str) return '';
    const d = document.createElement('div');
    d.textContent = str;
    return d.innerHTML;
}

const SEARCH_ICONS = {
    product: `<svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>`,
    link:    `<svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>`,
    text:    `<svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>`,
    other:   `<svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 10h16M4 14h8m-8 4h4"/></svg>`,
};
const THUMB_CLASS = { product: 'thumb-product', link: 'thumb-link', text: 'thumb-text', other: 'thumb-other' };

const PRODUCT_PLACEHOLDER_SVG = `<svg width="28" height="28" fill="none" stroke="#2563eb" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>`;
const CART_PLACEHOLDER_SVG    = `<svg width="24" height="24" fill="none" stroke="#9ca3af" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>`;
const CART_EMPTY_SVG          = `<svg width="40" height="40" fill="none" stroke="#d1d5db" stroke-width="1.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>`;

const hamburger       = document.getElementById('hamburger');
const fullmenuOverlay = document.getElementById('fullmenuOverlay');
const fullmenuClose   = document.getElementById('fullmenuClose');

let scrollY = 0;
function openMenu() {
    scrollY = window.scrollY;
    document.body.style.position = 'fixed';
    document.body.style.top = `-${scrollY}px`;
    document.body.style.width = '375px';
    fullmenuOverlay.classList.add('active');
}
function closeMenu() {
    fullmenuOverlay.classList.remove('active');
    document.body.style.position = '';
    document.body.style.top = '';
    document.body.style.width = '375px';
    window.scrollTo(0, scrollY);
}
hamburger.addEventListener('click', openMenu);
fullmenuClose.addEventListener('click', closeMenu);

document.querySelectorAll('.fullmenu-item[data-tab]').forEach(item => {
    item.addEventListener('click', () => {
        const tab = item.dataset.tab;
        document.querySelectorAll('.fullmenu-item').forEach(n => n.classList.remove('active'));
        item.classList.add('active');
        document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
        document.getElementById(`tab-${tab}`)?.classList.add('active');
        closeMenu();
    });
});

function renderProductBlock(container, product) {
    const price      = parseFloat(product.price)       || 0;
    const discount   = parseFloat(product.discount)    || 0;
    const finalPrice = parseFloat(product.final_price) ||
                       ((discount > 0 && discount < price) ? discount : price);
    const hasDis  = finalPrice < price;
    const discPct = hasDis ? Math.round(((price - finalPrice) / price) * 100) : 0;

    const imgHtml   = product.image_url
        ? `<img src="${product.image_url}" alt="${escHtml(product.title)}">`
        : `<div class="product-image-placeholder">${PRODUCT_PLACEHOLDER_SVG}</div>`;
    const badgeHtml = hasDis ? `<span class="product-badge">Diskon ${discPct}%</span>` : '';
    const priceHtml = hasDis
        ? `<div class="product-current-price">${formatRupiah(finalPrice)}</div>
           <div class="product-original-price">${formatRupiah(price)}</div>
           <div class="product-discount-badge">-${discPct}%</div>`
        : `<div class="product-current-price">${formatRupiah(finalPrice)}</div>`;

    container.innerHTML = `
        <div class="block-product" onclick="handleProductClick(${product.id})">
            <div class="product-image-wrapper">${imgHtml}</div>
            <div class="product-details">
                ${badgeHtml}
                <div class="product-title">${escHtml(product.title)}</div>
                <div class="product-price-section">${priceHtml}</div>
            </div>
        </div>`;
}

document.addEventListener('DOMContentLoaded', function () {
    fetch(`/api/profile/{{ $user->username }}/view`, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': csrfToken, 'Content-Type': 'application/json' }
    }).catch(() => {});

    const productContainers = document.querySelectorAll('[data-product-id]');
    if (productContainers.length > 0) {
        const ids = [...new Set(
            [...productContainers].map(el => el.getAttribute('data-product-id'))
        )].join(',');

        fetch(`/api/products/batch?ids=${ids}`)
            .then(r => r.json())
            .then(productsMap => {
                productContainers.forEach(container => {
                    const pid     = container.getAttribute('data-product-id');
                    const product = productsMap[pid];
                    if (product) {
                        renderProductBlock(container, product);
                    } else {
                        container.innerHTML = `<div style="background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:20px;text-align:center;color:#9ca3af;font-size:13px;">Produk tidak tersedia</div>`;
                    }
                });
            })
            .catch(() => {
                productContainers.forEach(container => {
                    const pid = container.getAttribute('data-product-id');
                    fetch(`/api/product/${pid}/data`)
                        .then(r => r.json())
                        .then(p  => renderProductBlock(container, p))
                        .catch(() => {
                            container.innerHTML = `<div style="background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:20px;text-align:center;color:#9ca3af;font-size:13px;">Produk tidak tersedia</div>`;
                        });
                });
            });
    }

    apiCall('/api/cart').then(d => updateBadge(d.total_items)).catch(() => {});
});

function updateBadge(count) {
    const badge = document.getElementById('cartBadge');
    badge.textContent = count;
    badge.classList.toggle('visible', count > 0);
}

let currentProductId = null;

function handleProductClick(productId) {
    if (!productId) return;
    fetch(`/api/product/${productId}/view`, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': csrfToken, 'Content-Type': 'application/json' }
    }).catch(() => {});

    fetch(`/api/product/${productId}/data`)
        .then(r => r.json())
        .then(product => {
            currentProductId = product.id;

            const price      = parseFloat(product.price)       || 0;
            const discount   = parseFloat(product.discount)    || 0;
            const finalPrice = parseFloat(product.final_price) ||
                               ((discount > 0 && discount < price) ? discount : price);
            const hasDis  = finalPrice < price;
            const discPct = hasDis ? Math.round(((price - finalPrice) / price) * 100) : 0;

            document.getElementById('detailTitle').textContent = product.title;
            document.getElementById('detailFinalPrice').textContent = formatRupiah(finalPrice);
            document.getElementById('detailOriginalPrice').textContent = hasDis ? formatRupiah(price) : '';

            const discBadge = document.getElementById('detailDiscountBadge');
            if (hasDis) {
                discBadge.textContent    = `-${discPct}%`;
                discBadge.style.display  = 'inline-block';
            } else {
                discBadge.style.display  = 'none';
            }

            const stockWrap = document.getElementById('detailStockWrap');
            if (product.product_type === 'digital' || product.stock === null) {
                stockWrap.style.display = 'none';
            } else {
                stockWrap.style.display = '';
                document.getElementById('detailStock').textContent = product.stock;
            }

            document.getElementById('detailDescription').textContent = product.description ?? '';

            const img = document.getElementById('detailImage');
            if (product.image_url) { img.src = product.image_url; img.style.display = 'block'; }
            else img.style.display = 'none';

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

document.getElementById('btnAddToCart').addEventListener('click', async () => {
    if (!currentProductId) return;
    const btn = document.getElementById('btnAddToCart');
    btn.classList.add('loading');
    try {
        const data = await apiCall('/api/cart/add', 'POST', { product_id: currentProductId, quantity: 1 });
        updateBadge(data.total_items);
        showToast('Produk ditambahkan ke keranjang!', 'success');
    } catch (err) { showToast(err.message, 'error'); }
    finally { btn.classList.remove('loading'); }
});

const cartOverlay = document.getElementById('cartOverlay');
const cartDrawer  = document.getElementById('cartDrawer');

function openCart()  { cartOverlay.classList.add('active'); cartDrawer.classList.add('active'); document.body.style.overflow = 'hidden'; loadCart(); }
function closeCart() { cartOverlay.classList.remove('active'); cartDrawer.classList.remove('active'); document.body.style.overflow = 'auto'; }
cartOverlay.addEventListener('click', closeCart);
document.getElementById('cartBtn').addEventListener('click', openCart);

async function loadCart() {
    const container = document.getElementById('cartItems');
    const footer    = document.getElementById('cartFooter');
    container.innerHTML = `<div class="cart-loading"><div class="spinner"></div> Memuat...</div>`;
    footer.style.display = 'none';
    try { renderCartItems(await apiCall('/api/cart')); }
    catch { container.innerHTML = `<div class="cart-empty">${CART_EMPTY_SVG}<p>Gagal memuat keranjang.</p></div>`; }
}

function renderCartItems(data) {
    const container   = document.getElementById('cartItems');
    const footer      = document.getElementById('cartFooter');
    const totalEl     = document.getElementById('cartTotal');
    const checkoutBtn = document.getElementById('btnCheckout');

    if (!data.items || data.items.length === 0) {
        container.innerHTML = `<div class="cart-empty">${CART_EMPTY_SVG}<p>Keranjangmu masih kosong.</p></div>`;
        footer.style.display = 'none'; return;
    }

    container.innerHTML = data.items.map(item => `
        <div class="cart-item" id="cart-item-${item.id}">
            <div class="cart-item-img">
                ${item.image_url
                    ? `<img src="${item.image_url}" alt="${escHtml(item.title)}">`
                    : `<div class="cart-item-img-placeholder">${CART_PLACEHOLDER_SVG}</div>`}
            </div>
            <div class="cart-item-info">
                <div class="cart-item-title">${escHtml(item.title)}</div>
                <div class="cart-item-price">
                    ${formatRupiah(item.final_price)}
                    ${item.has_discount ? `<span style="font-size:11px;color:#9ca3af;text-decoration:line-through;margin-left:4px;">${formatRupiah(item.original_price)}</span>` : ''}
                </div>
                <div class="qty-control">
                    <button class="qty-btn" onclick="changeQty(${item.id},${item.quantity - 1},${item.stock})" ${item.quantity <= 1 ? 'disabled' : ''}>−</button>
                    <span class="qty-value">${item.quantity}</span>
                    <button class="qty-btn" onclick="changeQty(${item.id},${item.quantity + 1},${item.stock})" ${item.quantity >= item.stock ? 'disabled' : ''}>+</button>
                </div>
            </div>
            <button class="cart-item-remove" onclick="removeItem(${item.id})" title="Hapus">
                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
            </button>
        </div>`).join('');

    totalEl.textContent   = formatRupiah(data.total_price);
    footer.style.display  = 'block';
    checkoutBtn.disabled  = false;
}

async function changeQty(id, newQty, stock) {
    if (newQty < 1 || newQty > stock) return;
    try {
        const data = await apiCall(`/api/cart/${id}`, 'PATCH', { quantity: newQty });
        updateBadge(data.total_items);
        renderCartItems(await apiCall('/api/cart'));
    } catch (err) { showToast(err.message, 'error'); }
}

async function removeItem(id) {
    const itemEl = document.getElementById(`cart-item-${id}`);
    if (itemEl) { itemEl.style.opacity = '0.5'; itemEl.style.transition = 'opacity 0.2s'; }
    try {
        const data = await apiCall(`/api/cart/${id}`, 'DELETE');
        updateBadge(data.total_items);
        showToast('Produk dihapus dari keranjang.');
        renderCartItems(await apiCall('/api/cart'));
    } catch (err) { showToast(err.message, 'error'); if (itemEl) itemEl.style.opacity = '1'; }
}

async function handleCheckout() {
    try {
        const data = await apiCall('/api/cart');
        if (!data.items || data.items.length === 0) { showToast('Keranjang kosong.', 'error'); return; }
        if (data.items.length === 1) { window.location.href = `/checkout/${data.items[0].product_id}`; return; }
        if (await window.appConfirm(`Keranjang berisi ${data.items.length} produk.\nCheckout per produk. Lanjut "${data.items[0].title}"?`, {
            title: 'Lanjut Checkout',
            confirmText: 'Ya, lanjut',
            variant: 'primary'
        }))
            window.location.href = `/checkout/${data.items[0].product_id}`;
    } catch { showToast('Gagal memproses checkout.', 'error'); }
}

const USERNAME             = '{{ $user->username }}';
const searchBtn            = document.getElementById('searchBtn');
const searchBarWrap        = document.getElementById('searchBarWrap');
const searchBackBtn        = document.getElementById('searchBackBtn');
const searchInput          = document.getElementById('searchInput');
const searchClearBtn       = document.getElementById('searchClearBtn');
const searchResultsOverlay = document.getElementById('searchResultsOverlay');
const searchResultsPanel   = document.getElementById('searchResultsPanel');

let searchDebounce = null;

function openSearch()  { searchBarWrap.classList.add('open'); setTimeout(() => searchInput.focus(), 350); }
function closeSearch() { searchBarWrap.classList.remove('open'); hideResults(); searchInput.value = ''; searchClearBtn.classList.remove('visible'); }
function showResults() { searchResultsOverlay.classList.add('active'); searchResultsPanel.classList.add('active'); }
function hideResults() { searchResultsOverlay.classList.remove('active'); searchResultsPanel.classList.remove('active'); }

searchBtn.addEventListener('click', openSearch);
searchBackBtn.addEventListener('click', closeSearch);
searchResultsOverlay.addEventListener('click', closeSearch);
searchClearBtn.addEventListener('click', () => { searchInput.value = ''; searchClearBtn.classList.remove('visible'); hideResults(); searchInput.focus(); });
document.addEventListener('keydown', e => { if (e.key === 'Escape' && searchBarWrap.classList.contains('open')) closeSearch(); });

function highlight(text, query) {
    if (!query || !text) return escHtml(text);
    const escaped = query.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
    return escHtml(text).replace(new RegExp(escaped, 'gi'), m => `<mark>${m}</mark>`);
}

function renderState(stateType, title, sub) {
    const icons = {
        loading:   `<svg width="28" height="28" fill="none" stroke="#9ca3af" stroke-width="1.8" viewBox="0 0 24 24" style="animation:spin .8s linear infinite"><path stroke-linecap="round" stroke-linejoin="round" d="M12 2v4m0 12v4M4.93 4.93l2.83 2.83m8.48 8.48l2.83 2.83M2 12h4m12 0h4M4.93 19.07l2.83-2.83m8.48-8.48l2.83-2.83"/></svg>`,
        noresult:  `<svg width="28" height="28" fill="none" stroke="#9ca3af" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11A6 6 0 115 11a6 6 0 0112 0z"/></svg>`,
        error:     `<svg width="28" height="28" fill="none" stroke="#ef4444" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>`,
    };
    searchResultsPanel.innerHTML = `
        <div class="search-state">
            <div class="search-state-icon">${icons[stateType] || icons.noresult}</div>
            <strong>${escHtml(title)}</strong>
            ${sub ? `<p>${escHtml(sub)}</p>` : ''}
        </div>`;
}

function normalizeItem(raw) {
    const type  = raw.type || 'other';
    const title = raw.title || raw.url || '(tanpa judul)';
    return {
        id:          raw.id          ?? null,
        type,
        title,
        subtitle:    raw.subtitle    ?? null,
        image_url:   raw.image_url   ?? null,
        url:         raw.url         ?? null,
        price:       raw.price       ?? 0,
        final_price: raw.final_price ?? raw.price ?? 0,
        block_id:    raw.block_id    ?? raw.id    ?? null,
    };
}

function renderResults(results, query) {
    if (!Array.isArray(results) || !results.length) {
        renderState('noresult', 'Tidak ditemukan', `Tidak ada hasil untuk "${query}"`);
        return;
    }

    const labelMap = { product: 'Produk', link: 'Link', text: 'Konten', other: 'Lainnya' };
    const groups   = { product: [], link: [], text: [], other: [] };
    results.map(normalizeItem).forEach(item => {
        const key = groups[item.type] !== undefined ? item.type : 'other';
        groups[key].push(item);
    });

    let html = '';
    for (const [type, items] of Object.entries(groups)) {
        if (!items.length) continue;
        html += `<div class="search-section-label">${labelMap[type] || 'Lainnya'}</div>`;
        items.forEach(item => {
            const thumbClass = THUMB_CLASS[type] || THUMB_CLASS.other;
            const thumbInner = item.image_url
                ? `<img src="${item.image_url}" alt="">`
                : SEARCH_ICONS[type] || SEARCH_ICONS.other;
            const badge   = `<span class="search-result-type-badge badge-${type in groups ? type : 'other'}">${labelMap[type] || 'Lainnya'}</span>`;
            const subHtml = type === 'product'
                ? `<div class="search-result-price">${formatRupiah(item.final_price)}</div>`
                : (item.subtitle ? `<div class="search-result-meta">${escHtml(String(item.subtitle))}</div>` : '');

            const itemJson = JSON.stringify(item)
                .replace(/</g, '\\u003c').replace(/>/g, '\\u003e').replace(/&/g, '\\u0026');

            html += `<div class="search-result-item" onclick='handleSearchResultClick(${itemJson})'>
                <div class="search-result-thumb ${item.image_url ? '' : thumbClass}">${thumbInner}</div>
                <div class="search-result-info">
                    <div class="search-result-title">${highlight(String(item.title), query)}</div>
                    ${subHtml}
                </div>
                ${badge}
            </div>`;
        });
    }
    searchResultsPanel.innerHTML = html;
}

function handleSearchResultClick(item) {
    if (item.type === 'product') {
        closeSearch(); handleProductClick(item.id);
    } else if (item.type === 'link') {
        window.open(item.url, '_blank');
    } else {
        closeSearch();
        const blockEl = document.getElementById(`block-${item.block_id}`);
        if (blockEl) {
            const tabContent = blockEl.closest('.tab-content');
            if (tabContent && !tabContent.classList.contains('active')) {
                document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
                tabContent.classList.add('active');
                const tabId = tabContent.id.replace('tab-', '');
                document.querySelectorAll('.fullmenu-item[data-tab]').forEach(n => {
                    n.classList.toggle('active', n.dataset.tab === tabId);
                });
            }
            setTimeout(() => blockEl.scrollIntoView({ behavior: 'smooth', block: 'center' }), 100);
        }
    }
}

searchInput.addEventListener('input', () => {
    const q = searchInput.value.trim();
    searchClearBtn.classList.toggle('visible', q.length > 0);
    clearTimeout(searchDebounce);
    if (!q) { hideResults(); return; }
    showResults();
    renderState('loading', 'Mencari...', '');
    searchDebounce = setTimeout(() => doSearch(q), 350);
});

async function doSearch(query) {
    try {
        const res = await fetch(
            `/search?username=${encodeURIComponent(USERNAME)}&q=${encodeURIComponent(query)}`,
            { headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken } }
        );
        const contentType = res.headers.get('content-type') || '';
        if (!contentType.includes('application/json')) {
            renderState('error', 'Route tidak ditemukan', `Status ${res.status}`);
            return;
        }
        const json = await res.json();
        let results = Array.isArray(json) ? json : (json.results ?? json.data ?? json.items ?? []);
        if (!results.length && typeof json === 'object') {
            for (const val of Object.values(json)) {
                if (Array.isArray(val) && val.length > 0) { results = val; break; }
            }
        }
        renderResults(results, query);
    } catch (err) {
        renderState('error', 'Gagal mencari', err?.message || 'Terjadi kesalahan');
    }
}
</script>

</div>
@include('components.app-alert')
</body>
</html>
