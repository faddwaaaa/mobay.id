@php
    use Illuminate\Support\Str;

    $user = Auth::user();
    $userSlug = $user->username;
    $avatar = Auth::user()->avatar ?? null;
    $premiumDate = data_get($user, 'premium_until') ?: data_get($user, 'premium_expires_at');
    $hasPremiumDate = false;

    if (!empty($premiumDate)) {
        try {
            $hasPremiumDate = \Carbon\Carbon::parse($premiumDate)->isFuture();
        } catch (\Throwable $e) {
            $hasPremiumDate = false;
        }
    }

    $isPremiumUser = method_exists($user, 'isPro')
        ? $user->isPro()
        : (
            (bool) data_get($user, 'is_premium') ||
            $hasPremiumDate ||
            in_array((string) data_get($user, 'plan'), ['pro', 'premium'], true) ||
            in_array((string) data_get($user, 'subscription_plan'), ['pro', 'premium'], true)
        );
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    {{-- <script>
        (function() {
            const t = localStorage.getItem('payou_theme');
            if (t === 'dark' || (!t && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            }
        })();
    </script> --}}
    <link rel="stylesheet" href="...">
    <title>@yield('title', 'Dashboard | Mobay.id')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="{{ asset('css/profile.css') }}">
    {{-- <link rel="stylesheet" href="{{ asset('css/darkmode.css') }}"> --}}

    <style>
/* ── TOKENS ──────────────────────────────────────── */
:root {
    --sb-bg:            #ffffff;
    --sb-text:          #4b5563;
    --sb-text-muted:    #9ca3af;
    --sb-icon:          #6b7280;
    --sb-hover:         #f3f4f6;
    --sb-active:        #1d4ed8;
    --sb-active-text:   #ffffff;
    --sb-border:        #e5e7eb;
    --page-bg:          linear-gradient(180deg, #eef4ff 0%, #f5f8ff 52%, #edf3ff 100%);
    --sidebar-w:        250px;
    --nav-font:         'Plus Jakarta Sans', sans-serif;
    --accent:           #1d4ed8;
    --text-primary:     #111827;
    --r-nav:            10px;
    --r-card:           10px;
    --ease:             0.18s ease;
}

/* ── RESET ───────────────────────────────────────── */
*, *::before, *::after { box-sizing: border-box; }
html, body { margin: 0; padding: 0; min-height: 100vh; }

/* ── PAGE BACKGROUND ─────────────────────────────── */
.s-page-bg {
    position: fixed;
    inset: 0;
    background: var(--page-bg);
    z-index: -1;
}

.pro-reminder-modal {
    position: fixed;
    inset: 0;
    display: none;
    align-items: center;
    justify-content: center;
    padding: 24px;
    background:
        radial-gradient(circle at top, rgba(249, 115, 22, 0.18), transparent 34%),
        rgba(15, 23, 42, 0.52);
    backdrop-filter: blur(10px);
    z-index: 120;
}

.pro-reminder-modal.is-open {
    display: flex;
}

.pro-reminder-card {
    position: relative;
    width: min(100%, 540px);
    overflow: hidden;
    border-radius: 28px;
    background:
        radial-gradient(circle at top right, rgba(255, 255, 255, 0.42), transparent 30%),
        linear-gradient(145deg, #fff7ed 0%, #ffffff 42%, #fff1f2 100%);
    border: 1px solid rgba(251, 146, 60, 0.3);
    box-shadow: 0 32px 80px rgba(15, 23, 42, 0.28);
    color: #7c2d12;
    animation: proReminderPop .28s ease-out;
}

.pro-reminder-card::before {
    content: '';
    position: absolute;
    inset: auto -52px -48px auto;
    width: 180px;
    height: 180px;
    border-radius: 999px;
    background: rgba(251, 146, 60, 0.14);
}

.pro-reminder-card::after {
    content: '';
    position: absolute;
    inset: -48px auto auto -38px;
    width: 150px;
    height: 150px;
    border-radius: 999px;
    background: rgba(244, 63, 94, 0.1);
}

.pro-reminder-shell {
    position: relative;
    z-index: 1;
    padding: 28px;
}

.pro-reminder-close {
    position: absolute;
    top: 18px;
    right: 18px;
    width: 40px;
    height: 40px;
    border: none;
    border-radius: 999px;
    background: rgba(255, 255, 255, 0.78);
    color: #9a3412;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 10px 24px rgba(15, 23, 42, 0.1);
    transition: transform .18s ease, background .18s ease, color .18s ease;
}

.pro-reminder-close:hover {
    transform: rotate(90deg);
    background: #fff;
    color: #7c2d12;
}

.pro-reminder-top {
    display: flex;
    align-items: flex-start;
    gap: 16px;
}

.pro-reminder-icon {
    width: 64px;
    height: 64px;
    border-radius: 20px;
    flex-shrink: 0;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    background: linear-gradient(135deg, #f97316 0%, #ef4444 100%);
    box-shadow: 0 18px 32px rgba(249, 115, 22, 0.3);
    font-size: 24px;
}

.pro-reminder-kicker {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 7px 12px;
    border-radius: 999px;
    background: rgba(255, 237, 213, 0.92);
    color: #c2410c;
    font-size: 11px;
    font-weight: 800;
    letter-spacing: .06em;
    text-transform: uppercase;
}

.pro-reminder-title {
    margin: 14px 0 8px;
    font-size: 30px;
    line-height: 1.05;
    letter-spacing: -0.04em;
    color: #7c2d12;
}

.pro-reminder-copy {
    margin: 0;
    font-size: 14px;
    line-height: 1.8;
    color: #9a3412;
}

.pro-reminder-meta {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 12px;
    margin-top: 22px;
}

.pro-reminder-stat {
    padding: 15px 16px;
    border-radius: 18px;
    background: rgba(255, 255, 255, 0.72);
    border: 1px solid rgba(251, 146, 60, 0.18);
}

.pro-reminder-label {
    font-size: 11px;
    font-weight: 800;
    letter-spacing: .07em;
    text-transform: uppercase;
    color: #c2410c;
}

.pro-reminder-value {
    margin-top: 8px;
    font-size: 24px;
    font-weight: 800;
    line-height: 1.1;
    color: #7c2d12;
}

.pro-reminder-timeline {
    display: flex;
    gap: 8px;
    margin-top: 20px;
    flex-wrap: wrap;
}

.pro-reminder-day {
    min-width: 44px;
    padding: 9px 10px;
    border-radius: 14px;
    text-align: center;
    font-size: 12px;
    font-weight: 800;
    color: #9a3412;
    background: rgba(255, 255, 255, 0.58);
    border: 1px solid rgba(251, 146, 60, 0.18);
}

.pro-reminder-day.is-active {
    color: #fff;
    background: linear-gradient(135deg, #ea580c 0%, #ef4444 100%);
    border-color: transparent;
    box-shadow: 0 14px 26px rgba(239, 68, 68, 0.18);
}

.pro-reminder-note {
    margin-top: 18px;
    padding: 14px 16px;
    border-radius: 18px;
    background: rgba(255, 247, 237, 0.82);
    border: 1px dashed rgba(251, 146, 60, 0.34);
    font-size: 13px;
    line-height: 1.7;
    color: #9a3412;
}

.pro-reminder-actions {
    display: flex;
    gap: 10px;
    margin-top: 22px;
    flex-wrap: wrap;
}

.pro-reminder-btn {
    min-height: 48px;
    padding: 0 18px;
    border: none;
    border-radius: 16px;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 9px;
    font-size: 13px;
    font-weight: 800;
    transition: transform .18s ease, box-shadow .18s ease, background .18s ease;
    cursor: pointer;
    font-family: inherit;
}

.pro-reminder-btn:hover {
    transform: translateY(-1px);
}

.pro-reminder-btn--primary {
    color: #fff;
    background: linear-gradient(135deg, #ea580c 0%, #ef4444 100%);
    box-shadow: 0 18px 34px rgba(234, 88, 12, 0.28);
}

.pro-reminder-btn--ghost {
    color: #9a3412;
    background: rgba(255, 255, 255, 0.82);
    border: 1px solid rgba(251, 146, 60, 0.22);
}

@keyframes proReminderPop {
    from {
        opacity: 0;
        transform: translateY(18px) scale(.97);
    }
    to {
        opacity: 1;
        transform: translateY(0) scale(1);
    }
}

/* ── SIDEBAR ─────────────────────────────────────── */
.s-sidebar {
    position: fixed;
    top: 0; left: 0;
    width: var(--sidebar-w);
    height: 100vh;
    background: var(--sb-bg);
    border-right: 1px solid var(--sb-border);
    display: flex;
    flex-direction: column;
    z-index: 40;
    overflow: hidden;
    box-shadow: 1px 0 8px rgba(0, 0, 0, 0.05);
    font-family: var(--nav-font);
    -webkit-font-smoothing: antialiased;
}

.s-sidebar::before,
.s-sidebar::after { display: none; }

.s-logo,
.s-top-account,
.s-nav,
.s-footer { position: relative; z-index: 1; }

/* ── LOGO ────────────────────────────────────────── */
.s-logo {
    padding: 16px 20px 14px;
    border-bottom: 1px solid var(--sb-border);
    flex-shrink: 0;
    text-align: center;
    background: #ffffff;
}

.s-logo a {
    display: inline-flex;
    align-items: center;
    justify-content: center;
}

.s-logo img {
    height: 58px;
    width: auto;
    object-fit: contain;
    display: block;
    margin: 0 auto;
}

.s-premium-wrap {
    margin-top: 8px;
}

.s-premium-btn,
.s-premium-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 7px;
    width: auto;
    max-width: 100%;
    min-height: 28px;
    border-radius: 999px;
    font-size: 11px;
    font-weight: 700;
    text-decoration: none;
    transition: all var(--ease);
}

.s-premium-btn {
    gap: 6px;
    padding: 6px 10px;
    border: 1px solid #dbeafe;
    background: #f8fbff;
    color: #1d4ed8;
    box-shadow: none;
}

.s-premium-btn:hover {
    border-color: #bfdbfe;
    background: #eff6ff;
    color: #1e40af;
    transform: translateY(-1px);
}

.s-premium-btn i {
    font-size: 10px;
}

.s-premium-badge {
    padding: 6px 10px;
    border: 1px solid #bbf7d0;
    background: #f0fdf4;
    color: #15803d;
    box-shadow: none;
    cursor: pointer;
}

.s-premium-badge:hover {
    border-color: #86efac;
    background: #ecfdf5;
    color: #166534;
    transform: translateY(-1px);
}

/* ── TOP ACCOUNT ─────────────────────────────────── */
.s-top-account {
    flex-shrink: 0;
    padding: 8px 8px 9px;
    border-bottom: 1px solid var(--sb-border);
    overflow: visible;
    background: #fafafa;
}

.s-footer-top {
    display: flex;
    align-items: center;
    gap: 2px;
}

/* ── PROFILE LINK ────────────────────────────────── */
.s-profile-link {
    display: flex;
    align-items: center;
    gap: 9px;
    padding: 7px 9px;
    border-radius: var(--r-card);
    text-decoration: none;
    transition: background var(--ease);
    flex: 1;
    min-width: 0;
}

.s-profile-link:hover { background: var(--sb-hover); }

.s-avatar {
    width: 32px; height: 32px;
    border-radius: 50%;
    overflow: hidden;
    flex-shrink: 0;
    border: 1.5px solid #d1d5db;
}

.s-avatar img { width: 100%; height: 100%; object-fit: cover; }

.s-profile-info { flex: 1; min-width: 0; }

.s-profile-name {
    font-size: 12.5px;
    font-weight: 600;
    font-family: var(--nav-font);
    color: var(--text-primary);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    line-height: 1.35;
}

.s-profile-uname {
    font-size: 11px;
    font-family: var(--nav-font);
    color: var(--sb-text-muted);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.s-profile-arrow {
    font-size: 9px;
    color: #d1d5db;
    flex-shrink: 0;
}

/* ── NAV ─────────────────────────────────────────── */
.s-nav {
    flex: 1;
    padding: 6px 8px;
    display: flex;
    flex-direction: column;
    gap: 1px;
    overflow-y: auto;
    overflow-x: hidden;
}

.s-nav::-webkit-scrollbar { width: 0; }

/* ── SECTION LABEL ───────────────────────────────── */
.s-label {
    padding: 12px 10px 4px;
    font-size: 10px;
    font-weight: 700;
    letter-spacing: .09em;
    text-transform: uppercase;
    color: var(--sb-text-muted);
    font-family: var(--nav-font);
}

/* ── NAV ITEM ────────────────────────────────────── */
.s-nav-item {
    display: flex;
    align-items: center;
    gap: 9px;
    padding: 8px 10px;
    border-radius: var(--r-nav);
    color: var(--sb-text);
    font-size: 13px;
    font-weight: 500;
    font-family: var(--nav-font);
    text-decoration: none;
    transition: background var(--ease), color var(--ease);
    white-space: nowrap;
    cursor: pointer;
    border: none;
    background: transparent;
    width: 100%;
    text-align: left;
    line-height: 1;
    margin: 1px 0;
}

.s-nav-item:hover {
    background: var(--sb-hover);
    color: var(--text-primary);
}

.s-nav-item:hover .s-icon {
    color: var(--accent);
    background: #eff6ff;
}

.s-nav-item.active {
    background: var(--sb-active);
    color: #ffffff;
    font-weight: 600;
}

.s-nav-item.active .s-icon {
    background: rgba(255,255,255,0.18);
    color: #ffffff;
}

/* ── ICON BUBBLE ─────────────────────────────────── */
.s-icon {
    width: 28px; height: 28px;
    border-radius: 7px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    color: var(--sb-icon);
    background: transparent;
    flex-shrink: 0;
    transition: background var(--ease), color var(--ease);
}

/* ── DIVIDER ─────────────────────────────────────── */
.s-divider {
    height: 1px;
    background: var(--sb-border);
    margin: 4px 8px;
}

/* ── TEMA DROPDOWN ───────────────────────────────── */
.s-theme-toggle {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 8px 10px;
    border-radius: var(--r-nav);
    color: var(--sb-text);
    font-size: 13px;
    font-weight: 500;
    font-family: var(--nav-font);
    cursor: pointer;
    transition: background var(--ease), color var(--ease);
    user-select: none;
    margin: 1px 0;
}

.s-theme-toggle:hover {
    background: var(--sb-hover);
    color: var(--text-primary);
}

.s-theme-toggle:hover .s-icon {
    color: var(--accent);
    background: #eff6ff;
}

.s-theme-toggle .s-left { display: flex; align-items: center; gap: 9px; }

.s-arrow {
    font-size: 9px;
    color: #d1d5db;
    transition: transform var(--ease);
}

.s-theme-toggle.open .s-arrow { transform: rotate(180deg); }

.s-theme-menu {
    display: none;
    flex-direction: column;
    margin: 2px 0 2px 38px;
    gap: 1px;
}

.s-theme-menu.open { display: flex; }

.s-theme-opt {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 7px 10px;
    border-radius: 7px;
    border: none;
    background: transparent;
    color: var(--sb-text);
    font-size: 12.5px;
    font-family: var(--nav-font);
    cursor: pointer;
    width: 100%;
    text-align: left;
    transition: background var(--ease);
}

.s-theme-opt:hover {
    background: var(--sb-hover);
    color: var(--text-primary);
}

.s-theme-opt .check-icon { margin-left: auto; opacity: 0; font-size: 10px; }

/* ── FOOTER ──────────────────────────────────────── */
.s-footer {
    flex-shrink: 0;
    padding: 8px 8px 12px;
    border-top: 1px solid var(--sb-border);
    background: #fafafa;
}

/* ── LOGOUT ──────────────────────────────────────── */
.s-logout {
    display: flex;
    align-items: center;
    gap: 9px;
    width: 100%;
    padding: 8px 10px;
    border-radius: var(--r-nav);
    border: none;
    background: transparent;
    color: #ef4444;
    font-size: 13px;
    font-weight: 500;
    font-family: var(--nav-font);
    cursor: pointer;
    text-decoration: none;
    transition: background var(--ease);
}

.s-logout:hover { background: #fef2f2; }

.s-logout .s-icon {
    color: #ef4444;
    font-size: 12px;
}

/* ── MAIN CONTENT ────────────────────────────────── */
.main-wrap { margin-left: var(--sidebar-w); min-height: 100vh; }

.content-pad {
    padding: 28px 30px;
    min-height: 100vh;
    background: transparent;
}

.content-pad h1,
.dashboard-title {
    color: var(--text-primary);
    font-weight: 700;
}

/* ── MOBILE TOPBAR ───────────────────────────────── */
.m-topbar {
    display: none;
    position: fixed;
    top: 0; left: 0; right: 0;
    height: 62px;                          /* ↑ was 50px */
    background: rgba(255,255,255,0.97);
    backdrop-filter: blur(14px);
    -webkit-backdrop-filter: blur(14px);
    border-bottom: 1px solid var(--sb-border);
    z-index: 300;
    align-items: center;
    padding: 0 16px;                       /* ↑ was 13px */
    gap: 12px;                             /* ↑ was 10px */
    font-family: var(--nav-font);
}

.m-topbar-logo { margin-right: auto; }
.m-topbar-logo img { height: 42px; }      /* ↑ was 34px */

.m-hamburger {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 42px; height: 42px;             /* ↑ was 32x32 */
    border-radius: 10px;                   /* ↑ was 7px */
    border: none;
    background: transparent;
    color: var(--sb-text);
    font-size: 19px;                       /* ↑ was 15px */
    cursor: pointer;
    flex-shrink: 0;
    transition: background var(--ease);
    font-family: inherit;
}

.m-hamburger:hover { background: var(--sb-hover); }

/* ── OVERLAY MOBILE ──────────────────────────────── */
.s-overlay {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.3);
    z-index: 39;
    backdrop-filter: blur(2px);
}

.s-overlay.active { display: block; }

/* ── MODAL FIX ───────────────────────────────────── */
#qr-modal,
#withdraw-modal {
    left: 0 !important;
    top: 0 !important;
    width: 100vw !important;
    height: 100vh !important;
    margin: 0 !important;
}

/* ── RESPONSIVE ──────────────────────────────────── */
@media (max-width: 768px) {
    .m-topbar { display: flex; }
    .s-sidebar {
        transform: translateX(-100%);
        transition: transform 0.24s cubic-bezier(.4,0,.2,1);
        z-index: 41;
    }
    .s-sidebar.active { transform: translateX(0); }
    .s-overlay { z-index: 40; }
    .main-wrap { margin-left: 0; margin-top: 62px; } /* ↑ sesuai tinggi topbar baru */
    .content-pad { padding: 16px 15px; }

    /* ── SIDEBAR MOBILE: lebih besar & mudah disentuh ── */
    .s-nav-item {
        font-size: 15px;                   /* ↑ was 13px */
        padding: 12px 12px;                /* ↑ was 8px 10px */
        gap: 11px;
    }

    .s-icon {
        width: 34px; height: 34px;         /* ↑ was 28x28 */
        font-size: 14px;                   /* ↑ was 12px */
        border-radius: 9px;
    }

    .s-avatar {
        width: 38px; height: 38px;         /* ↑ was 32x32 */
    }

    .s-profile-name { font-size: 14px; }   /* ↑ was 12.5px */
    .s-profile-uname { font-size: 12px; }  /* ↑ was 11px */

    .s-label {
        font-size: 11px;                   /* ↑ was 10px */
        padding: 14px 10px 5px;
    }

    .s-theme-toggle {
        font-size: 15px;                   /* ↑ was 13px */
        padding: 12px 12px;                /* ↑ was 8px 10px */
    }

    .s-logout {
        font-size: 15px;                   /* ↑ was 13px */
        padding: 12px 12px;                /* ↑ was 8px 10px */
    }

    .pro-reminder-modal {
        padding: 14px;
    }

    .pro-reminder-shell {
        padding: 22px 18px 18px;
    }

    .pro-reminder-top {
        flex-direction: column;
    }

    .pro-reminder-title {
        font-size: 24px;
    }

    .pro-reminder-meta {
        grid-template-columns: 1fr;
    }

    .pro-reminder-actions {
        flex-direction: column;
    }

    .pro-reminder-btn {
        width: 100%;
    }
}

@media (max-width: 480px) {
    .content-pad { padding: 13px 11px; }
}

/* ── FADE IN ─────────────────────────────────────── */
body:not(.preload) .content-pad {
    animation: pgFadeUp .3s ease both;
}

@keyframes pgFadeUp {
    from { opacity: 0; transform: translateY(7px); }
    to   { opacity: 1; transform: translateY(0); }
}
    </style>
    @stack('styles')
</head>
<body class="preload">

<div class="s-page-bg"></div>
<div class="s-overlay" id="sOverlay"></div>

<!-- ── MOBILE TOPBAR ───────────────────────────────────────── -->
<div class="m-topbar">
    <button class="m-hamburger" id="mHamburger" aria-label="Buka menu">
        <i class="fas fa-bars"></i>
    </button>
    <div class="m-topbar-logo">
        <a href="{{ route('dashboard') }}">
            <img src="{{ asset('img/logo.png') }}" alt="Mobay.id">
        </a>
    </div>
    <button class="notif-bell-btn notif-bell-trigger" id="notifBellBtnMobile" aria-label="Notifikasi">
        <i class="fas fa-bell"></i>
        <span class="notif-badge notif-badge-trigger" id="notifBadgeMobile"></span>
    </button>
</div>

<!-- ── SIDEBAR ──────────────────────────────────────────────── -->
<aside class="s-sidebar" id="sSidebar">

    <!-- Logo -->
    <div class="s-logo">
        <a href="{{ route('dashboard') }}">
            <img src="{{ asset('img/logo.png') }}" alt="Mobay.id">
        </a>
        <div class="s-premium-wrap">
            @if($isPremiumUser)
                <a href="{{ route('premium.index') }}" class="s-premium-badge">
                    <i class="fas fa-circle-check"></i>
                    Pro aktif
                </a>
            @else
                <a href="{{ route('premium.index') }}" class="s-premium-btn">
                    <i class="fas fa-sparkles"></i>
                    Coba Pro
                </a>
            @endif
        </div>
    </div>

    <!-- Profile -->
    <div class="s-top-account">
        <div class="s-footer-top">
            <a href="{{ route('dashboard.profile') }}" class="s-profile-link">
                <div class="s-avatar">
                    @if ($avatar)
                        <img src="{{ Str::startsWith($avatar, ['http://','https://']) ? $avatar : asset('storage/'.$avatar) }}"
                             alt="{{ Auth::user()->name }}">
                    @else
                        <img src="{{ asset('img/default-avatar.jpg') }}" alt="Avatar">
                    @endif
                </div>
                <div class="s-profile-info">
                    <div class="s-profile-name">{{ Auth::user()->name }}</div>
                    <div class="s-profile-uname">&#64;{{ $user->username }}</div>
                </div>
                <i class="fas fa-chevron-right s-profile-arrow"></i>
            </a>
            <button class="notif-bell-btn notif-bell-trigger" id="notifBellBtnSidebar" aria-label="Notifikasi">
                <i class="fas fa-bell"></i>
                <span class="notif-badge notif-badge-trigger" id="notifBadgeSidebar"></span>
            </button>
        </div>
    </div>

    <!-- Nav -->
    <nav class="s-nav">
        <div class="s-label">Menu</div>

        <a href="{{ route('dashboard') }}"
           class="s-nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <span class="s-icon"><i class="fas fa-house"></i></span>
            Dashboard
        </a>

        <a href="{{ route('links.index') }}"
           class="s-nav-item {{ request()->routeIs('links.*') ? 'active' : '' }}">
            <span class="s-icon"><i class="fas fa-link"></i></span>
            Link Saya
        </a>

        <a href="{{ route('dashboard.appearance') }}"
        class="s-nav-item {{ request()->routeIs('dashboard.appearance') ? 'active' : '' }}">
            <span class="s-icon"><i class="fas fa-palette"></i></span>
            Tampilan
        </a>

        <a href="{{ route('analitik.index') }}"
           class="s-nav-item {{ request()->routeIs('analitik.*') ? 'active' : '' }}">
            <span class="s-icon"><i class="fas fa-chart-bar"></i></span>
            Analitik
        </a>

        {{--
        <a href="{{ route('qrcode.show') }}"
           class="s-nav-item {{ request()->routeIs('qrcode.show') ? 'active' : '' }}">
            <span class="s-icon"><i class="fas fa-qrcode"></i></span>
            QR Code
        </a>
        --}}

        <a href="{{ route('products.manage') }}"
           class="s-nav-item {{ request()->routeIs('products.manage') ? 'active' : '' }}">
            <span class="s-icon"><i class="fas fa-shopping-bag"></i></span>
            Produk
        </a>

        <a href="{{ route('orders.index') }}"
           class="s-nav-item {{ request()->routeIs('orders.*') ? 'active' : '' }}">
            <span class="s-icon"><i class="fas fa-box-open"></i></span>
            Pesanan
        </a>

        <a href="{{ route('transactions.history') }}"
           class="s-nav-item {{ request()->routeIs('transactions.*') ? 'active' : '' }}">
            <span class="s-icon"><i class="fas fa-file-lines"></i></span>
            Riwayat Transaksi
        </a>

        <a href="{{ route('payment.accounts.index') }}"
           class="s-nav-item {{ request()->routeIs('payment.accounts.*') ? 'active' : '' }}">
            <span class="s-icon"><i class="fas fa-building-columns"></i></span>
            Rekening Bank
        </a>

        <a href="{{ route('settings.shipping') }}"
           class="s-nav-item {{ request()->routeIs('settings.shipping') ? 'active' : '' }}">
            <span class="s-icon"><i class="fas fa-truck"></i></span>
            Pengaturan Pengiriman
        </a>

        <div class="s-divider"></div>
        {{-- <div class="s-label">Preferensi</div>

        <div>
            <div class="s-theme-toggle" id="sThemeToggle">
                <div class="s-left">
                    <span class="s-icon"><i class="fas fa-swatchbook"></i></span>
                    Tema
                </div>
                <i class="fas fa-chevron-down s-arrow"></i>
            </div>
            <div class="s-theme-menu" id="sThemeMenu">
                <button class="s-theme-opt" data-theme="light">
                    <i class="fas fa-sun"></i> Terang
                    <i class="fas fa-check check-icon"></i>
                </button>
                <button class="s-theme-opt" data-theme="dark">
                    <i class="fas fa-moon"></i> Gelap
                    <i class="fas fa-check check-icon"></i>
                </button>
            </div>
        </div>
    </nav> --}}

    <!-- Logout -->
    <div class="s-footer">
        <a href="{{ route('logout') }}"
           class="s-logout"
           onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
            <span class="s-icon"><i class="fas fa-right-from-bracket"></i></span>
            Keluar
        </a>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display:none;">
            @csrf
        </form>
    </div>

</aside>

<!-- ── CONTENT ───────────────────────────────────────────────── -->
<div class="main-wrap">
    <main class="content-pad">
        @yield('content')
    </main>
</div>

@if(!empty($proExpiryReminder))
    <div
        id="proExpiryReminderModal"
        class="pro-reminder-modal"
        role="dialog"
        aria-modal="true"
        aria-labelledby="proExpiryReminderTitle"
        aria-describedby="proExpiryReminderCopy"
    >
        <div class="pro-reminder-card">
            <button
                type="button"
                class="pro-reminder-close"
                id="proExpiryReminderClose"
                aria-label="Tutup peringatan masa aktif Pro"
            >
                <i class="fas fa-xmark"></i>
            </button>
            <div class="pro-reminder-shell">
                <div class="pro-reminder-top">
                    <div class="pro-reminder-icon">
                        <i class="fas fa-hourglass-half"></i>
                    </div>
                    <div style="min-width:0;">
                        <div class="pro-reminder-kicker">
                            <i class="fas fa-bell"></i>
                            Peringatan Perpanjangan Pro
                        </div>
                        <h2 class="pro-reminder-title" id="proExpiryReminderTitle">
                            Sisa {{ $proExpiryReminder['remaining_days'] }} hari sebelum akses Pro berhenti.
                        </h2>
                        <p class="pro-reminder-copy" id="proExpiryReminderCopy">
                            Peringatan ini tampil otomatis saat Anda membuka area dashboard pada H-5 sampai H-1. Tutup dengan tombol silang jika sudah dibaca, lalu lanjutkan pembayaran agar akun tetap aktif tanpa putus akses.
                        </p>
                    </div>
                </div>

                <div class="pro-reminder-meta">
                    <div class="pro-reminder-stat">
                        <div class="pro-reminder-label">Sisa Hari</div>
                        <div class="pro-reminder-value">{{ $proExpiryReminder['remaining_days'] }} hari</div>
                    </div>
                    <div class="pro-reminder-stat">
                        <div class="pro-reminder-label">Berakhir Pada</div>
                        <div class="pro-reminder-value" style="font-size:18px;">
                            {{ $proExpiryReminder['expired_at'] ?? '-' }}
                        </div>
                    </div>
                </div>

                <div class="pro-reminder-timeline" aria-hidden="true">
                    @foreach ([5, 4, 3, 2, 1] as $reminderDay)
                        <div class="pro-reminder-day {{ (int) $proExpiryReminder['remaining_days'] === $reminderDay ? 'is-active' : '' }}">
                            H-{{ $reminderDay }}
                        </div>
                    @endforeach
                </div>

                <div class="pro-reminder-note">
                    Setelah masa aktif habis, akun akan masuk ke halaman Pro expired dan tidak bisa kembali ke mode Free. Perpanjang Pro sebelum waktunya selesai supaya operasional tetap lancar.
                </div>

                <div class="pro-reminder-actions">
                    <a href="{{ route('premium.index') }}" class="pro-reminder-btn pro-reminder-btn--primary">
                        <i class="fas fa-wallet"></i>
                        Lihat Paket Pro
                    </a>
                    <button type="button" class="pro-reminder-btn pro-reminder-btn--ghost" id="proExpiryReminderDismiss">
                        <i class="fas fa-check"></i>
                        Tutup Dulu
                    </button>
                </div>
            </div>
        </div>
    </div>
@endif

<script src="{{ asset('js/dashboard.js') }}"></script>
{{-- <script src="{{ asset('js/darkmode.js') }}"></script> --}}

@include('components.notification-bell')
@include('components.app-alert')
@stack('modals')

<script>
window.addEventListener('load', function () {
    document.body.classList.remove('preload');
});

document.addEventListener('DOMContentLoaded', function () {
    var hamburger = document.getElementById('mHamburger');
    var sidebar   = document.getElementById('sSidebar');
    var overlay   = document.getElementById('sOverlay');
    var proReminderModal = document.getElementById('proExpiryReminderModal');
    var proReminderClose = document.getElementById('proExpiryReminderClose');
    var proReminderDismiss = document.getElementById('proExpiryReminderDismiss');

    function openSidebar()  {
        sidebar.classList.add('active');
        overlay.classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function closeSidebar() {
        sidebar.classList.remove('active');
        overlay.classList.remove('active');
        document.body.style.overflow = '';
    }

    function openProReminderModal() {
        if (!proReminderModal) return;
        proReminderModal.classList.add('is-open');
        document.body.style.overflow = 'hidden';
    }

    function closeProReminderModal() {
        if (!proReminderModal) return;
        proReminderModal.classList.remove('is-open');
        if (!sidebar || !sidebar.classList.contains('active')) {
            document.body.style.overflow = '';
        }
    }

    hamburger && hamburger.addEventListener('click', function (e) {
        e.stopPropagation();
        sidebar.classList.contains('active') ? closeSidebar() : openSidebar();
    });

    overlay && overlay.addEventListener('click', closeSidebar);

    sidebar.querySelectorAll('a.s-nav-item, a.s-profile-link, a.s-logout').forEach(function (el) {
        el.addEventListener('click', function () {
            if (window.innerWidth <= 768) closeSidebar();
        });
    });

    window.addEventListener('resize', function () {
        if (window.innerWidth > 768) closeSidebar();
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            if (proReminderModal && proReminderModal.classList.contains('is-open')) {
                closeProReminderModal();
                return;
            }

            closeSidebar();
        }
    });

    proReminderClose && proReminderClose.addEventListener('click', closeProReminderModal);
    proReminderDismiss && proReminderDismiss.addEventListener('click', closeProReminderModal);

    proReminderModal && proReminderModal.addEventListener('click', function (e) {
        if (e.target === proReminderModal) {
            closeProReminderModal();
        }
    });

    if (proReminderModal) {
        window.setTimeout(openProReminderModal, 180);
    }

    // var themeToggle = document.getElementById('sThemeToggle');
    // var themeMenu   = document.getElementById('sThemeMenu');

    // themeToggle && themeToggle.addEventListener('click', function () {
    //     themeMenu.classList.toggle('open');
    //     themeToggle.classList.toggle('open');
    // });
});
</script>
@stack('scripts')
</body>
</html>
