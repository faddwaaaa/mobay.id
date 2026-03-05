@extends('layouts.dashboard')
@section('title', 'Premium | Payou.id')

@section('content')
<div class="premium-page">
    <section class="premium-hero">
        <div class="hero-badge">Payou Pro Seller</div>
        <h1>Jualan lebih meyakinkan, cepat closing, dan terlihat jauh lebih profesional.</h1>
        <p>
            Upgrade ke Premium untuk tampilan toko yang lebih kuat, fitur promosi lebih lengkap,
            dan alat penjualan yang membantu seller naik kelas.
        </p>
        <div class="hero-prices">
            <div class="price-card">
                <div class="plan">Bulanan</div>
                <div class="price">Rp50.000<span>/bulan</span></div>
                <button type="button" class="btn-buy" disabled>Segera Hadir</button>
            </div>
            <div class="price-card highlight">
                <div class="save-tag">Hemat 2 Bulan</div>
                <div class="plan">Tahunan</div>
                <div class="price">Rp550.000<span>/tahun</span></div>
                <button type="button" class="btn-buy" disabled>Segera Hadir</button>
            </div>
        </div>
        <div class="demo-note">Fitur pembayaran paket masih tahap tampilan (belum aktif).</div>
    </section>

    <section class="premium-compare">
        <div class="section-head">
            <h2>Perbandingan Free vs Premium</h2>
            <p>Gaya presentasi dibuat agar pembeli lebih percaya dan seller lebih mudah closing.</p>
        </div>

        <div class="compare-wrap">
            <table class="compare-table">
                <thead>
                    <tr>
                        <th>Fitur</th>
                        <th>Free</th>
                        <th>Premium</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Kustomisasi tampilan halaman</td>
                        <td><span class="badge no">Terbatas</span></td>
                        <td><span class="badge yes">Penuh</span></td>
                    </tr>
                    <tr>
                        <td>Prioritas tampilan produk unggulan</td>
                        <td><span class="badge no">Tidak</span></td>
                        <td><span class="badge yes">Ya</span></td>
                    </tr>
                    <tr>
                        <td>Komponen promosi (banner, CTA premium)</td>
                        <td><span class="badge no">Tidak</span></td>
                        <td><span class="badge yes">Lengkap</span></td>
                    </tr>
                    <tr>
                        <td>Analitik penjualan lebih detail</td>
                        <td><span class="badge no">Dasar</span></td>
                        <td><span class="badge yes">Lanjutan</span></td>
                    </tr>
                    <tr>
                        <td>Badge toko profesional</td>
                        <td><span class="badge no">Tidak</span></td>
                        <td><span class="badge yes">Ya</span></td>
                    </tr>
                    <tr>
                        <td>Dukungan prioritas</td>
                        <td><span class="badge no">Normal</span></td>
                        <td><span class="badge yes">Prioritas</span></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </section>

    <section class="premium-proof">
        <div class="proof-card">
            <h3>Kenapa seller butuh Premium?</h3>
            <ul>
                <li>Landing jualan terlihat lebih eksklusif dan meyakinkan.</li>
                <li>Highlight produk utama lebih jelas, cocok untuk dorong konversi.</li>
                <li>Brand personal seller terasa lebih profesional dibanding toko biasa.</li>
            </ul>
        </div>
        <div class="proof-card dark">
            <h3>Target hasil setelah upgrade</h3>
            <ul>
                <li>Nilai order rata-rata naik lewat tampilan yang lebih premium.</li>
                <li>Pengunjung lebih cepat paham produk dan lebih cepat checkout.</li>
                <li>Toko lebih siap dipakai untuk scale campaign dan traffic besar.</li>
            </ul>
        </div>
    </section>
</div>

<style>
    .premium-page { max-width: 1120px; margin: 0 auto; display: grid; gap: 18px; }
    .premium-hero {
        border-radius: 26px;
        padding: 28px;
        background:
            radial-gradient(circle at 12% 20%, rgba(255,255,255,.22), transparent 38%),
            linear-gradient(130deg, #0f172a 0%, #1e3a8a 46%, #2563eb 100%);
        color: #fff;
        box-shadow: 0 20px 42px rgba(30, 58, 138, .28);
    }
    .hero-badge {
        display: inline-flex;
        background: rgba(255,255,255,.15);
        border: 1px solid rgba(255,255,255,.28);
        border-radius: 999px;
        padding: 6px 12px;
        font-size: 12px;
        font-weight: 700;
        letter-spacing: .06em;
        text-transform: uppercase;
    }
    .premium-hero h1 { margin: 14px 0 8px; font-size: 34px; line-height: 1.18; font-weight: 800; max-width: 850px; }
    .premium-hero p { margin: 0; font-size: 16px; opacity: .95; max-width: 760px; }
    .hero-prices { margin-top: 22px; display: grid; grid-template-columns: repeat(2, minmax(0,1fr)); gap: 12px; }
    .price-card {
        background: rgba(255,255,255,.1);
        border: 1px solid rgba(255,255,255,.24);
        border-radius: 18px;
        padding: 16px;
        backdrop-filter: blur(6px);
    }
    .price-card.highlight { background: linear-gradient(160deg, rgba(254,240,138,.26), rgba(255,255,255,.12)); border-color: rgba(254,240,138,.72); }
    .save-tag {
        display: inline-flex;
        background: #facc15;
        color: #713f12;
        border-radius: 999px;
        font-weight: 800;
        font-size: 11px;
        padding: 4px 8px;
        margin-bottom: 8px;
    }
    .plan { font-size: 13px; font-weight: 700; opacity: .9; }
    .price { margin-top: 6px; font-size: 32px; font-weight: 800; letter-spacing: -.02em; }
    .price span { font-size: 14px; font-weight: 600; opacity: .9; margin-left: 4px; }
    .btn-buy {
        margin-top: 12px;
        border: none;
        border-radius: 11px;
        background: #fff;
        color: #1e40af;
        font-weight: 800;
        font-size: 13px;
        padding: 10px 14px;
        width: 100%;
        opacity: .8;
        cursor: not-allowed;
    }
    .demo-note { margin-top: 12px; font-size: 12px; opacity: .9; }

    .premium-compare, .proof-card {
        border-radius: 22px;
        background: #fff;
        border: 1px solid #e5e7eb;
        box-shadow: 0 10px 26px rgba(15, 23, 42, .05);
    }
    .premium-compare { padding: 18px; }
    .section-head h2 { margin: 0; font-size: 24px; font-weight: 800; color: #0f172a; }
    .section-head p { margin: 4px 0 0; color: #64748b; font-size: 14px; }
    .compare-wrap { margin-top: 14px; overflow: auto; }
    .compare-table { width: 100%; border-collapse: collapse; min-width: 680px; }
    .compare-table th, .compare-table td { padding: 14px 12px; border-bottom: 1px solid #eef2f7; text-align: left; }
    .compare-table th { font-size: 12px; color: #64748b; text-transform: uppercase; letter-spacing: .06em; }
    .compare-table td { font-size: 14px; color: #0f172a; }
    .badge { border-radius: 999px; padding: 5px 10px; font-size: 12px; font-weight: 700; }
    .badge.yes { background: #dcfce7; color: #166534; }
    .badge.no { background: #f1f5f9; color: #475569; }

    .premium-proof { display: grid; grid-template-columns: repeat(2, minmax(0,1fr)); gap: 12px; }
    .proof-card { padding: 18px; }
    .proof-card.dark { background: linear-gradient(135deg, #0f172a 0%, #1f2937 100%); color: #e2e8f0; border-color: #334155; }
    .proof-card h3 { margin: 0 0 10px; font-size: 18px; font-weight: 800; color: inherit; }
    .proof-card ul { margin: 0; padding-left: 18px; display: grid; gap: 8px; color: #475569; }
    .proof-card.dark ul { color: #cbd5e1; }

    @media (max-width: 900px) {
        .premium-hero h1 { font-size: 28px; }
        .hero-prices, .premium-proof { grid-template-columns: 1fr; }
    }
    @media (max-width: 640px) {
        .premium-page { gap: 12px; }
        .premium-hero { padding: 18px; border-radius: 20px; }
        .premium-hero h1 { font-size: 24px; }
        .section-head h2 { font-size: 20px; }
    }

    .dark .premium-compare, .dark .proof-card {
        background: #111827;
        border-color: #334155;
        box-shadow: 0 14px 30px rgba(0, 0, 0, .35);
    }
    .dark .section-head h2, .dark .compare-table td { color: #f8fafc; }
    .dark .section-head p, .dark .compare-table th { color: #94a3b8; }
    .dark .compare-table th, .dark .compare-table td { border-bottom-color: #273449; }
    .dark .badge.no { background: #1e293b; color: #cbd5e1; }
    .dark .proof-card ul { color: #cbd5e1; }
</style>
@endsection
