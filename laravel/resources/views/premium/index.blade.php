@extends('layouts.dashboard')
@section('title', 'Upgrade ke Paket Pro | Mobay.id')

@section('content')
@php
    $features = [
        ['label' => 'Link Tak Terbatas', 'free' => 'check', 'pro' => 'check'],
        ['label' => 'Toko Digital', 'free' => 'check', 'pro' => 'check'],
        ['label' => 'Statistik / Pengunjung', 'free' => 'check', 'pro' => 'check'],
        ['label' => 'Desain Tampilan (Template)', 'free' => 'check', 'pro' => 'check'],
        ['label' => 'Ganti Font & Tombol Sesuka Hati', 'free' => 'check', 'pro' => 'check'],
        ['label' => 'Ganti Latar Belakang Sesuka Hati', 'free' => 'check', 'pro' => 'check'],
        ['label' => 'Biaya Transaksi', 'free' => '10% → 5%', 'pro' => '10% → 3%'],
        ['label' => 'Biaya Penarikan Uang', 'free' => 'Rp 5.000', 'pro' => 'GRATIS'],
        ['label' => 'Profil / Tentang Saya', 'free' => 'check', 'pro' => 'check'],
        ['label' => 'Notifikasi lewat Email', 'free' => 'check', 'pro' => 'check'],
        ['label' => 'Unduh Data ke CSV', 'free' => 'check', 'pro' => 'check'],
        ['label' => 'FB Pixel (Iklan Facebook)', 'free' => 'cross', 'pro' => 'check'],
        ['label' => 'Google Analytics', 'free' => 'cross', 'pro' => 'check'],
        ['label' => 'Parameter UTM (Lacak Iklan)', 'free' => 'cross', 'pro' => 'check'],
        ['label' => 'Judul & Deskripsi Halaman (SEO)', 'free' => 'cross', 'pro' => 'check'],
        ['label' => 'Tarik Dana Kapan Saja', 'free' => 'check', 'pro' => 'check'],
        ['label' => 'Hapus Logo lynk.id', 'free' => 'cross', 'pro' => 'check'],
        ['label' => 'Ukuran File Maksimal', 'free' => '100 MB', 'pro' => '5 GB'],
        ['label' => 'Tampilan Produk Kustom', 'free' => 'cross', 'pro' => 'check'],
        ['label' => 'Video E-course', 'free' => '10 Menit', 'pro' => '480 Menit'],
        ['label' => 'Kuesioner / Formulir', 'free' => 'cross', 'pro' => '20 GB'],
    ];

    $renderValue = function ($value) {
        if ($value === 'check') {
            return '<span class="cmp-icon is-check"><i class="fas fa-check"></i></span>';
        }

        if ($value === 'cross') {
            return '<span class="cmp-icon is-cross"><i class="fas fa-xmark"></i></span>';
        }

        return '<span class="cmp-text">' . e($value) . '</span>';
    };
@endphp

<div class="pro-page">
    <section class="pro-hero">
        <div class="hero-copy">
            <div class="hero-kicker">Naik ke Pro</div>
            <h1>Upgrade ke Paket Pro</h1>
            <p class="hero-subtitle">Buka fitur-fitur canggih dan kembangkan toko digitalmu lebih cepat.</p>
        </div>

        <div class="hero-pricing-card">
            <div class="offer-pill">
                <i class="fas fa-bolt"></i>
                Penawaran terbatas
            </div>
            <div class="hero-price-list">
                <div class="hero-price-item">
                    <span class="hero-price-label">Bayar Bulanan</span>
                    <strong>IDR 49.900/bulan</strong>
                </div>
                <div class="hero-price-item">
                    <span class="hero-price-label">Bayar Tahunan</span>
                    <strong>IDR 500.000/tahun</strong>
                </div>
            </div>
            <p class="hero-note">Harga terjangkau, fitur lengkap — cocok untuk pemula hingga penjual profesional.</p>
        </div>
    </section>

    <section class="pro-section">
        <div class="section-head">
            <div>
                <span class="section-tag">Perbandingan</span>
                <h2>Perbandingan Fitur</h2>
            </div>
            <p>Lihat perbedaan fitur antara akun Gratis dan akun Pro dalam satu tabel yang mudah dibaca.</p>
        </div>

        <div class="compare-shell">
            <table class="compare-table">
                <thead>
                    <tr>
                        <th>Fitur</th>
                        <th>Gratis</th>
                        <th>Pro</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($features as $feature)
                        <tr>
                            <td class="feature-name">{{ $feature['label'] }}</td>
                            <td>{!! $renderValue($feature['free']) !!}</td>
                            <td>{!! $renderValue($feature['pro']) !!}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>

    <section class="pro-section">
        <div class="section-head">
            <div>
                <span class="section-tag">Harga</span>
                <h2>Pilih Paketmu</h2>
            </div>
            <p>Pilih cara bayar yang paling cocok untuk rencana jualan kamu.</p>
        </div>

        <div class="plan-grid">
            <article class="plan-card">
                <div class="plan-card-top">
                    <span class="plan-chip">Paket Bulanan</span>
                    <h3>IDR 49.900/bulan</h3>
                    <p>Bayar setiap bulan, bisa berhenti kapan saja</p>
                </div>
                <button type="button" class="upgrade-btn">Upgrade Sekarang</button>
            </article>

            <article class="plan-card plan-card-highlight">
                <div class="plan-card-top">
                    <span class="plan-chip plan-chip-highlight">Paling Hemat 🔥</span>
                    <h3>IDR 500.000/tahun</h3>
                    <p>Bayar setahun sekali (hemat 2 bulan gratis!)</p>
                </div>
                <button type="button" class="upgrade-btn">Upgrade Sekarang</button>
            </article>
        </div>
    </section>
</div>

<style>
.pro-page {
    max-width: 1120px;
    margin: 0 auto;
    display: grid;
    gap: 20px;
    color: #163022;
    font-family: 'Plus Jakarta Sans', sans-serif;
}

.pro-hero,
.pro-section {
    background: rgba(255, 255, 255, 0.94);
    border: 1px solid #dfece4;
    border-radius: 28px;
    box-shadow: 0 16px 36px rgba(22, 48, 34, 0.06);
}

.pro-hero {
    display: grid;
    grid-template-columns: minmax(0, 1.3fr) minmax(320px, .9fr);
    gap: 18px;
    padding: 28px;
    background:
        radial-gradient(circle at top left, rgba(134, 239, 172, 0.18), transparent 28%),
        linear-gradient(180deg, rgba(255,255,255,0.98), rgba(246, 253, 248, 0.98));
}

.hero-kicker,
.section-tag {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-height: 30px;
    padding: 0 12px;
    border-radius: 999px;
    background: #effaf1;
    border: 1px solid #cdeed5;
    color: #15803d;
    font-size: 12px;
    font-weight: 800;
    letter-spacing: .04em;
    text-transform: uppercase;
}

.pro-hero h1,
.pro-section h2 {
    margin: 14px 0 0;
    color: #163022;
    letter-spacing: -0.03em;
    font-weight: 800;
}

.pro-hero h1 {
    font-size: 40px;
    line-height: 1.08;
    max-width: 560px;
}

.hero-subtitle {
    margin: 14px 0 0;
    font-size: 16px;
    line-height: 1.7;
    color: #5d7567;
    max-width: 560px;
}

.hero-pricing-card {
    padding: 22px;
    border-radius: 24px;
    background: linear-gradient(180deg, #f2fbf4, #ebf8ee);
    border: 1px solid #cfead6;
    box-shadow: inset 0 1px 0 rgba(255,255,255,.85);
}

.offer-pill {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 8px 12px;
    border-radius: 999px;
    background: #dff7e5;
    color: #15803d;
    font-size: 12px;
    font-weight: 800;
}

.hero-price-list {
    display: grid;
    gap: 14px;
    margin-top: 20px;
}

.hero-price-item {
    padding: 16px 18px;
    border-radius: 20px;
    background: rgba(255, 255, 255, 0.82);
    border: 1px solid #d6ebdb;
}

.hero-price-label {
    display: block;
    margin-bottom: 6px;
    font-size: 12px;
    font-weight: 700;
    color: #6b7f73;
    text-transform: uppercase;
    letter-spacing: .06em;
}

.hero-price-item strong {
    font-size: 28px;
    line-height: 1.1;
    color: #163022;
    font-weight: 800;
    letter-spacing: -0.03em;
}

.hero-note {
    margin: 16px 0 0;
    font-size: 13px;
    line-height: 1.7;
    color: #6b7f73;
}

.pro-section {
    padding: 24px;
}

.section-head {
    display: flex;
    align-items: end;
    justify-content: space-between;
    gap: 16px;
    margin-bottom: 18px;
}

.pro-section h2 {
    font-size: 30px;
    line-height: 1.15;
}

.section-head p {
    max-width: 360px;
    margin: 0;
    font-size: 14px;
    line-height: 1.7;
    color: #5d7567;
}

.compare-shell {
    overflow: auto;
    border: 1px solid #e3efe7;
    border-radius: 24px;
}

.compare-table {
    width: 100%;
    min-width: 720px;
    border-collapse: collapse;
    background: #ffffff;
}

.compare-table th,
.compare-table td {
    padding: 16px 18px;
    border-bottom: 1px solid #edf4ef;
    text-align: left;
    vertical-align: middle;
}

.compare-table thead th {
    background: #f7fcf8;
    color: #6b7f73;
    font-size: 12px;
    font-weight: 800;
    letter-spacing: .08em;
    text-transform: uppercase;
}

.compare-table tbody tr:nth-child(even) {
    background: #fbfefb;
}

.feature-name {
    font-size: 14px;
    font-weight: 700;
    color: #163022;
}

.cmp-icon,
.cmp-text {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 36px;
    min-height: 36px;
    border-radius: 999px;
    font-size: 14px;
    font-weight: 800;
}

.cmp-icon.is-check {
    background: #ecfdf3;
    color: #16a34a;
}

.cmp-icon.is-cross {
    background: #fef2f2;
    color: #dc2626;
}

.cmp-text {
    justify-content: flex-start;
    min-width: auto;
    min-height: auto;
    border-radius: 0;
    color: #163022;
}

.plan-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 16px;
}

.plan-card {
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    gap: 18px;
    padding: 22px;
    border-radius: 24px;
    border: 1px solid #dcebe0;
    background: linear-gradient(180deg, #ffffff, #f7fcf8);
    box-shadow: 0 12px 24px rgba(22, 48, 34, 0.04);
}

.plan-card-highlight {
    border-color: #bce8c8;
    background: linear-gradient(180deg, #f5fff7, #edf9f0);
}

.plan-card-top h3 {
    margin: 14px 0 8px;
    font-size: 30px;
    line-height: 1.1;
    font-weight: 800;
    color: #163022;
    letter-spacing: -0.03em;
}

.plan-card-top p {
    margin: 0;
    font-size: 14px;
    color: #5d7567;
}

.plan-chip {
    display: inline-flex;
    align-items: center;
    min-height: 30px;
    padding: 0 12px;
    border-radius: 999px;
    background: #f2f8f3;
    border: 1px solid #deece1;
    color: #3d5a48;
    font-size: 12px;
    font-weight: 800;
}

.plan-chip-highlight {
    background: #ddf8e4;
    border-color: #b7ebc4;
    color: #15803d;
}

.upgrade-btn {
    width: 100%;
    border: none;
    border-radius: 18px;
    min-height: 52px;
    background: linear-gradient(135deg, #22c55e, #16a34a);
    color: #ffffff;
    font-size: 15px;
    font-weight: 800;
    cursor: pointer;
    box-shadow: 0 16px 28px rgba(34, 197, 94, 0.22);
    transition: transform .18s ease, box-shadow .18s ease, filter .18s ease;
}

.upgrade-btn:hover {
    transform: translateY(-1px);
    filter: brightness(1.02);
    box-shadow: 0 18px 30px rgba(34, 197, 94, 0.28);
}

@media (max-width: 900px) {
    .pro-hero,
    .plan-grid {
        grid-template-columns: 1fr;
    }

    .section-head {
        align-items: flex-start;
        flex-direction: column;
    }
}

@media (max-width: 640px) {
    .pro-page {
        gap: 14px;
    }

    .pro-hero,
    .pro-section {
        padding: 18px;
        border-radius: 22px;
    }

    .pro-hero h1,
    .pro-section h2 {
        font-size: 24px;
    }

    .hero-price-item strong,
    .plan-card-top h3 {
        font-size: 24px;
    }
}
</style>
@endsection