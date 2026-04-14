@extends('layouts.dashboard')
@section('title', 'Paket Pro | Toko')

@php
    use App\Models\User;

    $user = Auth::user();
    $isProUser = method_exists($user, 'isPro') ? $user->isPro() : in_array((string) data_get($user, 'subscription_plan'), ['pro', 'premium'], true);
    $freeAccess = User::FREE_APPEARANCE_ACCESS;
    $proAccess = User::PRO_APPEARANCE_ACCESS;
    $proMonthlyLink = 'https://wa.me/6285600489815?text=' . rawurlencode('Halo Admin Mobay.id, saya ingin beli Pro bulanan seharga Rp 49.900.');
    $proYearlyLink = 'https://wa.me/6285600489815?text=' . rawurlencode('Halo Admin Mobay.id, saya ingin beli Pro tahunan seharga Rp 500.000.');

    $comparisonRows = [
        [
            'label' => 'Analitik',
            'free' => 'Analitik dasar: views profil, klik produk, dan ringkasan utama.',
            'pro' => 'Analitik lanjutan dengan konversi, tren, insight penjualan, dan performa produk.',
        ],
        [
            'label' => 'Ekspor data',
            'free' => 'Belum tersedia.',
            'pro' => 'Ekspor laporan analitik ke CSV dan Excel.',
        ],
        [
            'label' => 'Background profil',
            'free' => 'Warna solid dan gradient.',
            'pro' => 'Warna, gradient, upload gambar sendiri, dan wallpaper siap pakai.',
        ],
        [
            'label' => 'Gaya tombol',
            'free' => count($freeAccess['button_styles']) . ' gaya dasar yang simpel dan aman dipakai.',
            'pro' => count($proAccess['button_styles']) . ' gaya termasuk neon, kaca, ghost, dan minimal.',
        ],
        [
            'label' => 'Font',
            'free' => count($freeAccess['fonts']) . ' font profesional untuk kebutuhan dasar brand.',
            'pro' => count($proAccess['fonts']) . ' font untuk tampilan brand yang lebih fleksibel.',
        ],
        [
            'label' => 'Layout produk',
            'free' => count($freeAccess['block_layouts']) . ' layout: standar, ringkas, dan kisi.',
            'pro' => count($proAccess['block_layouts']) . ' layout termasuk sorotan produk.',
        ],
        [
            'label' => 'Link sosial',
            'free' => 'Hingga ' . $freeAccess['max_social_links'] . ' link sosial.',
            'pro' => 'Hingga ' . $proAccess['max_social_links'] . ' link sosial.',
        ],
        [
            'label' => 'Rekening bank',
            'free' => 'Hingga 2 rekening bank.',
            'pro' => 'Hingga 5 rekening bank.',
        ],
    ];
@endphp

@section('content')
<style>
    .premium-page,
    .premium-page * {
        box-sizing: border-box;
    }

    .premium-page {
        min-height: 100vh;
        padding: 22px;
        background:
            radial-gradient(circle at top left, rgba(96, 165, 250, 0.18), transparent 26%),
            radial-gradient(circle at top right, rgba(34, 197, 94, 0.1), transparent 24%),
            #f8fafc;
        font-family: 'Plus Jakarta Sans', sans-serif;
        color: #0f172a;
    }

    .premium-shell {
        max-width: 1160px;
        margin: 0 auto;
    }

    .premium-hero {
        background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
        border: 1px solid #e2e8f0;
        border-radius: 28px;
        padding: 28px;
        margin-bottom: 14px;
        margin-bottom: 18px;
        overflow: hidden;
        position: relative;
        box-shadow: 0 30px 80px rgba(15, 23, 42, 0.06);
    }

    .premium-hero::before,
    .premium-hero::after {
        content: '';
        position: absolute;
        border-radius: 999px;
        pointer-events: none;
    }

    .premium-hero::before {
        width: 280px;
        height: 280px;
        right: -120px;
        top: -120px;
        background: radial-gradient(circle, rgba(37, 99, 235, 0.12) 0%, rgba(37, 99, 235, 0) 72%);
    }

    .premium-hero::after {
        width: 240px;
        height: 240px;
        left: -100px;
        bottom: -120px;
        background: radial-gradient(circle, rgba(14, 165, 233, 0.08) 0%, rgba(14, 165, 233, 0) 75%);
    }

    .premium-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 7px 12px;
        border-radius: 999px;
        background: #eff6ff;
        color: #1d4ed8;
        border: 1px solid #dbeafe;
        font-size: 12px;
        font-weight: 700;
    }

    .premium-title {
        margin: 16px 0 10px;
        font-size: 36px;
        line-height: 1.12;
        letter-spacing: -0.03em;
        font-weight: 800;
        color: #0f172a;
        max-width: 720px;
    }

    .premium-subtitle {
        margin: 0;
        max-width: 650px;
        font-size: 14px;
        line-height: 1.8;
        color: #475569;
    }

    .premium-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-top: 20px;
    }

    .premium-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        min-height: 44px;
        padding: 0 16px;
        border-radius: 14px;
        border: 1px solid #dbeafe;
        background: #fff;
        color: #1e3a8a;
        text-decoration: none;
        font-size: 13px;
        font-weight: 700;
    }

    .premium-btn--primary {
        background: #1d4ed8;
        border-color: #1d4ed8;
        color: #fff;
        box-shadow: 0 14px 34px rgba(29, 78, 216, 0.18);
    }

    .premium-summary-bar {
        display: grid;
        grid-template-columns: minmax(220px, 0.9fr) repeat(3, minmax(0, 1fr));
        gap: 12px;
        margin-bottom: 18px;
    }

    .premium-summary-card {
        background: rgba(255, 255, 255, 0.92);
        border: 1px solid #e5e7eb;
        border-radius: 20px;
        padding: 16px 18px;
        min-width: 0;
        box-shadow: 0 10px 24px rgba(15, 23, 42, 0.04);
    }

    .premium-summary-card--status {
        background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
        border-color: #dbeafe;
    }

    .premium-summary-label {
        margin: 0 0 8px;
        font-size: 11px;
        font-weight: 800;
        letter-spacing: 0.06em;
        text-transform: uppercase;
        color: #64748b;
    }

    .premium-summary-value {
        margin: 0;
        font-size: 20px;
        line-height: 1.2;
        font-weight: 800;
        color: #0f172a;
    }

    .premium-summary-text {
        margin: 10px 0 0;
        font-size: 12px;
        line-height: 1.7;
        color: #475569;
    }

    .premium-plan-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 16px;
        margin-bottom: 18px;
    }

    .premium-plan {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 24px;
        padding: 22px;
        min-width: 0;
        position: relative;
        overflow: hidden;
    }

    .premium-plan--active {
        border-color: #bfdbfe;
        box-shadow: 0 16px 40px rgba(37, 99, 235, 0.09);
    }

    .premium-plan-tag {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 6px 10px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 800;
        letter-spacing: 0.04em;
        text-transform: uppercase;
        background: #f8fafc;
        color: #475569;
        border: 1px solid #e2e8f0;
    }

    .premium-plan--active .premium-plan-tag {
        background: #eff6ff;
        border-color: #dbeafe;
        color: #1d4ed8;
    }

    .premium-plan h2 {
        margin: 16px 0 8px;
        font-size: 24px;
        font-weight: 800;
        color: #0f172a;
    }

    .premium-plan p {
        margin: 0;
        font-size: 14px;
        line-height: 1.75;
        color: #475569;
    }

    .premium-plan-accent {
        width: 100%;
        height: 4px;
        border-radius: 999px;
        margin: -4px 0 16px;
        background: linear-gradient(90deg, #cbd5e1, #e2e8f0);
    }

    .premium-plan--active .premium-plan-accent {
        background: linear-gradient(90deg, #1d4ed8, #60a5fa);
    }

    .premium-list {
        display: grid;
        gap: 10px;
        margin: 18px 0 0;
        padding: 0;
        list-style: none;
    }

    .premium-list li {
        display: flex;
        gap: 10px;
        align-items: flex-start;
        font-size: 13px;
        line-height: 1.7;
        color: #334155;
    }

    .premium-list i {
        width: 20px;
        margin-top: 1px;
        color: #2563eb;
        text-align: center;
        flex-shrink: 0;
    }

    .premium-pricing-actions {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 14px;
        margin-top: 20px;
    }

    .premium-pricing-section {
        margin-bottom: 18px;
        padding: 24px;
        border-radius: 28px;
        background: linear-gradient(180deg, #f8fbff 0%, #eef5ff 100%);
        border: 1px solid #dbeafe;
        box-shadow: 0 20px 50px rgba(37, 99, 235, 0.08);
    }

    .premium-pricing-head {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        gap: 14px;
        flex-wrap: wrap;
        margin-bottom: 6px;
    }

    .premium-pricing-kicker {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 7px 12px;
        border-radius: 999px;
        background: rgba(255, 255, 255, 0.8);
        border: 1px solid #dbeafe;
        color: #1d4ed8;
        font-size: 12px;
        font-weight: 800;
    }

    .premium-pricing-title {
        margin: 14px 0 0;
        font-size: 28px;
        line-height: 1.15;
        letter-spacing: -0.03em;
        font-weight: 800;
        color: #0f172a;
    }

    .premium-pricing-subtitle {
        margin: 8px 0 0;
        max-width: 760px;
        font-size: 13px;
        line-height: 1.8;
        color: #475569;
    }

    .premium-price-card {
        min-width: 0;
        padding: 22px;
        border-radius: 24px;
        border: 1px solid #dbeafe;
        background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.7);
        position: relative;
        overflow: hidden;
        min-height: 232px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .premium-price-card--yearly {
        background: linear-gradient(180deg, #eff6ff 0%, #eef4ff 100%);
        border-color: #93c5fd;
        box-shadow: 0 22px 42px rgba(37, 99, 235, 0.16);
        transform: translateY(-4px);
    }

    .premium-price-card::before {
        content: '';
        position: absolute;
        inset: auto -25px -35px auto;
        width: 120px;
        height: 120px;
        border-radius: 999px;
        background: radial-gradient(circle, rgba(147, 197, 253, 0.24), rgba(147, 197, 253, 0));
        pointer-events: none;
    }

    .premium-price-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        margin-bottom: 10px;
    }

    .premium-price-label {
        font-size: 12px;
        font-weight: 800;
        color: #334155;
        letter-spacing: 0.03em;
        text-transform: uppercase;
    }

    .premium-price-meta {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 8px;
    }

    .premium-price-dot {
        width: 9px;
        height: 9px;
        border-radius: 999px;
        background: #60a5fa;
        box-shadow: 0 0 0 4px rgba(96, 165, 250, 0.14);
    }

    .premium-price-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 24px;
        padding: 0 9px;
        border-radius: 999px;
        background: #dbeafe;
        color: #1d4ed8;
        font-size: 10px;
        font-weight: 800;
    }

    .premium-price-value {
        margin: 0;
        font-size: 34px;
        line-height: 1.15;
        font-weight: 800;
        color: #0f172a;
        letter-spacing: -0.03em;
    }

    .premium-price-sub {
        margin: 10px 0 18px;
        font-size: 12px;
        line-height: 1.7;
        color: #475569;
    }

    .premium-buy-btn {
        display: inline-flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        width: 100%;
        min-height: 48px;
        padding: 0 14px 0 16px;
        border-radius: 16px;
        text-decoration: none;
        font-size: 13px;
        font-weight: 800;
        border: 1px solid #bfdbfe;
        background: rgba(255, 255, 255, 0.95);
        color: #1d4ed8;
        box-shadow: 0 8px 18px rgba(37, 99, 235, 0.08);
    }

    .premium-buy-btn--primary {
        background: linear-gradient(135deg, #1d4ed8 0%, #2563eb 100%);
        border-color: #1d4ed8;
        color: #fff;
        box-shadow: 0 16px 26px rgba(29, 78, 216, 0.2);
    }

    .premium-buy-btn-copy {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        min-width: 0;
        flex: 1;
    }

    .premium-buy-btn-text {
        min-width: 0;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .premium-buy-btn-icon {
        width: 28px;
        height: 28px;
        border-radius: 10px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: rgba(37, 99, 235, 0.1);
        color: #1d4ed8;
        flex-shrink: 0;
    }

    .premium-buy-btn--primary .premium-buy-btn-icon {
        background: rgba(255, 255, 255, 0.16);
        color: #fff;
    }

    .premium-buy-btn-arrow {
        width: 28px;
        height: 28px;
        border-radius: 999px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: rgba(37, 99, 235, 0.08);
        color: inherit;
        flex-shrink: 0;
    }

    .premium-buy-btn--primary .premium-buy-btn-arrow {
        background: rgba(255, 255, 255, 0.16);
    }

    .premium-spotlight-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 14px;
        margin-bottom: 18px;
    }

    .premium-spotlight-card {
        min-width: 0;
        background: rgba(255, 255, 255, 0.86);
        border: 1px solid #e5e7eb;
        border-radius: 22px;
        padding: 18px;
        box-shadow: 0 12px 26px rgba(15, 23, 42, 0.04);
    }

    .premium-spotlight-icon {
        width: 40px;
        height: 40px;
        border-radius: 14px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 12px;
        font-size: 15px;
        color: #1e3a8a;
        background: #eff6ff;
    }

    .premium-spotlight-card h3 {
        margin: 0 0 6px;
        font-size: 15px;
        font-weight: 800;
        color: #0f172a;
    }

    .premium-spotlight-card p {
        margin: 0;
        font-size: 13px;
        line-height: 1.75;
        color: #475569;
    }

    .premium-table-wrap {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 26px;
        overflow: hidden;
    }

    .premium-table-head {
        padding: 22px 24px 16px;
        border-bottom: 1px solid #e5e7eb;
    }

    .premium-table-head h3 {
        margin: 0;
        font-size: 22px;
        font-weight: 800;
        color: #0f172a;
    }

    .premium-table-head p {
        margin: 8px 0 0;
        font-size: 13px;
        line-height: 1.7;
        color: #64748b;
        max-width: 760px;
    }

    .premium-compare-grid {
        display: grid;
        grid-template-columns: minmax(180px, 0.85fr) minmax(0, 1fr) minmax(0, 1fr);
    }

    .premium-compare-cell {
        padding: 18px 24px;
        border-bottom: 1px solid #eef2f7;
        min-width: 0;
    }

    .premium-compare-grid > .premium-compare-cell:nth-child(3n + 2),
    .premium-compare-grid > .premium-compare-cell:nth-child(3n + 3) {
        background: #fff;
    }

    .premium-compare-grid > .premium-compare-cell:nth-child(3n + 1) {
        background: #fbfdff;
    }

    .premium-compare-grid > .premium-compare-cell:nth-last-child(-n + 3) {
        border-bottom: none;
    }

    .premium-compare-head {
        font-size: 12px;
        font-weight: 800;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .premium-compare-title {
        font-size: 14px;
        line-height: 1.5;
        font-weight: 800;
        color: #0f172a;
    }

    .premium-compare-copy {
        font-size: 13px;
        line-height: 1.8;
        color: #475569;
    }

    .premium-footnote {
        margin-top: 16px;
        padding: 16px 18px;
        border-radius: 18px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        font-size: 13px;
        line-height: 1.8;
        color: #475569;
    }

    @media (max-width: 1080px) {
        .premium-summary-bar,
        .premium-spotlight-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 820px) {
        .premium-page {
            padding: 16px;
        }

        .premium-title {
            font-size: 30px;
        }

        .premium-plan-grid,
        .premium-summary-bar,
        .premium-spotlight-grid,
        .premium-pricing-actions {
            grid-template-columns: 1fr;
        }

        .premium-price-card--yearly {
            transform: none;
        }

        .premium-pricing-section {
            padding: 18px;
        }

        .premium-pricing-title {
            font-size: 24px;
        }

        .premium-compare-grid {
            grid-template-columns: 1fr;
        }

        .premium-compare-grid > .premium-compare-cell {
            border-bottom: 1px solid #eef2f7;
            background: #fff !important;
        }

        .premium-compare-grid > .premium-compare-cell:nth-child(3n + 1) {
            padding-bottom: 8px;
            background: #f8fbff !important;
        }

        .premium-compare-grid > .premium-compare-cell:nth-child(3n + 2),
        .premium-compare-grid > .premium-compare-cell:nth-child(3n + 3) {
            padding-top: 10px;
        }
    }

    @media (max-width: 560px) {
        .premium-hero,
        .premium-plan,
        .premium-table-head,
        .premium-compare-cell {
            padding-left: 16px;
            padding-right: 16px;
        }

        .premium-title {
            font-size: 24px;
        }

        .premium-btn {
            width: 100%;
        }

        .premium-buy-btn {
            min-height: 46px;
            padding-right: 12px;
        }
    }
</style>

<div class="premium-page">
    <div class="premium-shell">
        <section class="premium-hero">
            <div>
                <div class="premium-badge">
                    <i class="fas fa-gem"></i>
                    Paket yang mengikuti kebutuhan toko saat ini
                </div>
                <h1 class="premium-title">Perbandingan Free dan Pro yang lebih jelas, ringan, dan sesuai fitur yang benar-benar aktif.</h1>
                <p class="premium-subtitle">
                    Halaman ini sudah disederhanakan supaya lebih mudah dipahami. Free tetap cukup untuk mulai jualan, sementara Pro fokus memberi alat tambahan yang paling terasa saat toko mulai ramai: analitik lanjutan, ekspor data, fleksibilitas desain, dan kapasitas operasional yang lebih besar.
                </p>
                <div class="premium-actions">
                    <a href="{{ route('dashboard.appearance') }}" class="premium-btn premium-btn--primary">
                        <i class="fas fa-palette"></i>
                        Lihat editor tampilan
                    </a>
                    <a href="{{ route('analitik.index') }}" class="premium-btn">
                        <i class="fas fa-chart-line"></i>
                        Buka analitik
                    </a>
                </div>
            </div>
        </section>

        <section class="premium-summary-bar">
            <article class="premium-summary-card premium-summary-card--status">
                <p class="premium-summary-label">Mode akun saat ini</p>
                <p class="premium-summary-value">{{ $isProUser ? 'Pro aktif' : 'Free aktif' }}</p>
                <p class="premium-summary-text">{{ $isProUser ? 'Akses fitur lanjutan sudah terbuka untuk analitik, tampilan, dan operasional toko.' : 'Cukup untuk mulai jualan, lalu upgrade saat butuh insight dan kontrol yang lebih dalam.' }}</p>
            </article>
            <article class="premium-summary-card">
                <p class="premium-summary-label">Fokus utama</p>
                <p class="premium-summary-value">Analitik lebih dalam</p>
                <p class="premium-summary-text">Tren, konversi, insight penjualan, dan performa produk yang lebih membantu saat mengambil keputusan.</p>
            </article>
            <article class="premium-summary-card">
                <p class="premium-summary-label">Nilai tambah Pro</p>
                <p class="premium-summary-value">CSV + Excel</p>
                <p class="premium-summary-text">Laporan analitik bisa dibawa keluar untuk evaluasi manual, tim, atau arsip internal.</p>
            </article>
            <article class="premium-summary-card">
                <p class="premium-summary-label">Fleksibilitas visual</p>
                <p class="premium-summary-value">{{ count($proAccess['fonts']) }} font, gambar, premium style</p>
                <p class="premium-summary-text">Lebih banyak pilihan untuk membuat tampilan toko terasa rapi dan lebih matang tanpa terlalu teknis.</p>
            </article>
        </section>

        <section class="premium-plan-grid">
            <article class="premium-plan {{ !$isProUser ? 'premium-plan--active' : '' }}">
                <div class="premium-plan-accent"></div>
                <span class="premium-plan-tag">
                    <i class="fas fa-seedling"></i>
                    Free
                </span>
                <h2>Mulai dengan alat inti</h2>
                <p>Pas untuk penjual yang baru mulai dan butuh halaman toko yang cepat jadi, bersih, dan cukup kuat untuk menerima trafik awal.</p>
                <ul class="premium-list">
                    <li><i class="fas fa-check"></i><span>Analitik dasar untuk memantau views, klik, dan performa utama.</span></li>
                    <li><i class="fas fa-check"></i><span>2 rekening bank untuk operasional awal tanpa ribet.</span></li>
                    <li><i class="fas fa-check"></i><span>Desain dasar yang tetap rapi: warna, gradient, font inti, dan layout produk utama.</span></li>
                </ul>
            </article>

            <article class="premium-plan {{ $isProUser ? 'premium-plan--active' : '' }}">
                <div class="premium-plan-accent"></div>
                <span class="premium-plan-tag">
                    <i class="fas fa-crown"></i>
                    Pro
                </span>
                <h2>Naik level saat toko berkembang</h2>
                <p>Dibuat untuk penjual yang sudah butuh lebih banyak fleksibilitas visual dan analitik yang membantu mengambil keputusan lebih cepat.</p>
                <ul class="premium-list">
                    <li><i class="fas fa-check"></i><span>Analitik lanjutan dengan tren, konversi, insight penjualan, dan performa produk.</span></li>
                    <li><i class="fas fa-check"></i><span>Ekspor CSV dan Excel untuk rekap, evaluasi, atau pelaporan tim.</span></li>
                    <li><i class="fas fa-check"></i><span>5 rekening bank, background gambar, lebih banyak font, dan gaya tombol premium.</span></li>
                </ul>
            </article>
        </section>

        <section class="premium-pricing-section">
            <div class="premium-pricing-head">
                <div>
                    <div class="premium-pricing-kicker">
                        <i class="fas fa-bolt"></i>
                        Pilihan paket Pro
                    </div>
                    <h3 class="premium-pricing-title">Pilih paket yang paling cocok untuk ritme jualanmu.</h3>
                    <p class="premium-pricing-subtitle">Dua kartu ini sengaja dibuat berdiri sendiri supaya opsi pembelian langsung terlihat. Paket tahunan saya tonjolkan karena nilainya lebih hemat untuk toko yang sudah rutin aktif.</p>
                </div>
            </div>

            <div class="premium-pricing-actions">
                <div class="premium-price-card">
                    <div class="premium-price-head">
                        <div class="premium-price-meta">
                            <span class="premium-price-dot"></span>
                            <span class="premium-price-label">Pro Bulanan</span>
                        </div>
                    </div>
                    <p class="premium-price-value">Rp 49.900</p>
                    <p class="premium-price-sub">Cocok untuk coba fitur Pro dulu dan lihat dampaknya ke tampilan serta analitik tokomu.</p>
                    <button type="button" class="premium-buy-btn" onclick="showProQrisModal('monthly')">
                        <span class="premium-buy-btn-copy">
                            <span class="premium-buy-btn-icon"><i class="fas fa-qrcode"></i></span>
                            <span class="premium-buy-btn-text">Pilih paket bulanan</span>
                        </span>
                        <span class="premium-buy-btn-arrow"><i class="fas fa-arrow-right"></i></span>
                    </button>
                </div>
                <div class="premium-price-card premium-price-card--yearly">
                    <div class="premium-price-head">
                        <div class="premium-price-meta">
                            <span class="premium-price-dot"></span>
                            <span class="premium-price-label">Pro Tahunan</span>
                        </div>
                        <span class="premium-price-badge">Lebih hemat</span>
                    </div>
                    <p class="premium-price-value">Rp 500.000</p>
                    <p class="premium-price-sub">Pilihan terbaik kalau kamu sudah rutin jualan dan ingin biaya langganan lebih efisien sepanjang tahun.</p>
                    <button type="button" class="premium-buy-btn premium-buy-btn--primary" onclick="showProQrisModal('yearly')">
                        <span class="premium-buy-btn-copy">
                            <span class="premium-buy-btn-icon"><i class="fas fa-qrcode"></i></span>
                            <span class="premium-buy-btn-text">Pilih paket tahunan</span>
                        </span>
                        <span class="premium-buy-btn-arrow"><i class="fas fa-arrow-right"></i></span>
                    </button>
                </div>
            </div>
        </section>

        <section class="premium-spotlight-grid">
            <article class="premium-spotlight-card">
                <div class="premium-spotlight-icon"><i class="fas fa-chart-line"></i></div>
                <h3>Baca momentum lebih cepat</h3>
                <p>Analitik Pro dibuat untuk membantu membaca tren, konversi, dan performa produk tanpa menebak-nebak.</p>
            </article>
            <article class="premium-spotlight-card">
                <div class="premium-spotlight-icon"><i class="fas fa-swatchbook"></i></div>
                <h3>Tampilan toko lebih fleksibel</h3>
                <p>Background gambar, tombol premium, font tambahan, dan layout sorotan membuat halaman terasa lebih siap jualan.</p>
            </article>
            <article class="premium-spotlight-card">
                <div class="premium-spotlight-icon"><i class="fas fa-building-columns"></i></div>
                <h3>Operasional lebih longgar</h3>
                <p>Batas rekening bank yang lebih besar membantu saat toko mulai memakai lebih dari satu rekening penerimaan.</p>
            </article>
            <article class="premium-spotlight-card">
                <div class="premium-spotlight-icon"><i class="fas fa-file-arrow-down"></i></div>
                <h3>Data siap dibawa keluar</h3>
                <p>Ekspor CSV dan Excel memudahkan rekap manual, laporan tim, atau analisis lanjutan di luar dashboard.</p>
            </article>
        </section>

        <section class="premium-table-wrap">
            <div class="premium-table-head">
                <h3>Perbandingan fitur Free dan Pro</h3>
                <p>Urutannya saya rapikan berdasarkan fitur yang paling sering terasa langsung oleh penjual: analitik, ekspor, tampilan profil, kapasitas link, dan operasional pembayaran.</p>
            </div>

            <div class="premium-compare-grid">
                <div class="premium-compare-cell premium-compare-head">Fitur</div>
                <div class="premium-compare-cell premium-compare-head">Free</div>
                <div class="premium-compare-cell premium-compare-head">Pro</div>

                @foreach($comparisonRows as $row)
                    <div class="premium-compare-cell premium-compare-title">{{ $row['label'] }}</div>
                    <div class="premium-compare-cell premium-compare-copy">{{ $row['free'] }}</div>
                    <div class="premium-compare-cell premium-compare-copy">{{ $row['pro'] }}</div>
                @endforeach
            </div>
        </section>


    </div>
</div>
    <!-- Include QRIS Modal -->
    @include('pro.qris-modal')
@endsection
