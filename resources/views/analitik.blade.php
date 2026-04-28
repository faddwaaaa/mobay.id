@extends('layouts.dashboard')
@section('title', 'Analitik | Mobay.id')

@section('content')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=DM+Mono:wght@400;500&display=swap');

    .an-page, .an-page * { box-sizing: border-box; }

    .an-page {
        font-family: 'Plus Jakarta Sans', sans-serif;
        background: #f5f7fa;
        min-height: 100vh;
        padding: 28px 20px;
        overflow-x: clip;
    }

    .an-shell {
        max-width: 1100px;
        width: 100%;
        margin: 0 auto;
    }

    /* ── Header ── */
    .an-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 16px;
        flex-wrap: wrap;
        margin-bottom: 28px;
    }

    .an-header-left h1 {
        margin: 0 0 6px;
        font-size: 24px;
        font-weight: 800;
        color: #0f1923;
        letter-spacing: -0.4px;
    }

    .an-header-left p {
        margin: 0;
        font-size: 14px;
        color: #6b7280;
    }

    .an-export-wrap {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
        align-items: center;
    }

    .an-btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 10px 18px;
        font-size: 13px;
        font-weight: 700;
        border-radius: 12px;
        border: 1.5px solid transparent;
        cursor: pointer;
        text-decoration: none;
        font-family: inherit;
        transition: opacity 0.15s;
    }
    .an-btn:hover { opacity: 0.85; }

    .an-btn--outline {
        background: #fff;
        border-color: #e2e8f0;
        color: #374151;
    }

    .an-btn--primary {
        background: #1d4ed8;
        color: #fff;
    }

    .an-btn--gold {
        background: #f59e0b;
        color: #1e293b;
    }

    .an-free-banner {
        background: #fff7ed;
        border: 1.5px solid #fed7aa;
        border-radius: 14px;
        padding: 14px 18px;
        margin-bottom: 22px;
        font-size: 14px;
        color: #7c2d12;
        line-height: 1.6;
    }
    .an-free-banner strong { color: #9a3412; }

    /* ── Chart card ── */
    .an-card {
        background: #fff;
        border: 1.5px solid #e8ecf0;
        border-radius: 20px;
        overflow: hidden;
        margin-bottom: 16px;
    }

    .an-card-inner {
        padding: 22px 24px;
    }

    .an-card-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 12px;
        flex-wrap: wrap;
        margin-bottom: 18px;
    }

    .an-card-title {
        margin: 0 0 4px;
        font-size: 16px;
        font-weight: 700;
        color: #0f1923;
    }

    .an-card-sub {
        margin: 0;
        font-size: 13px;
        color: #94a3b8;
    }

    /* ── Filter bar ── */
    .an-filter-bar {
        display: flex;
        align-items: center;
        gap: 6px;
        flex-wrap: wrap;
    }

    .an-filter-pill {
        padding: 7px 14px;
        font-size: 13px;
        font-weight: 600;
        border-radius: 999px;
        border: 1.5px solid #e2e8f0;
        background: #f8fafc;
        color: #64748b;
        cursor: pointer;
        font-family: inherit;
        transition: all 0.15s;
    }

    .an-filter-pill:hover { border-color: #bfdbfe; color: #1d4ed8; }

    .an-filter-pill.active {
        background: #0f1923;
        color: #fff;
        border-color: #0f1923;
    }

    .an-date-range {
        display: flex;
        align-items: center;
        gap: 6px;
        flex-wrap: wrap;
    }

    .an-date-input {
        padding: 7px 11px;
        border: 1.5px solid #e2e8f0;
        border-radius: 10px;
        font-size: 13px;
        font-weight: 500;
        font-family: inherit;
        color: #374151;
        background: #f8fafc;
        outline: none;
        width: 140px;
    }
    .an-date-input:focus { border-color: #93c5fd; background: #fff; }

    .an-date-sep {
        font-size: 13px;
        color: #94a3b8;
        font-weight: 500;
    }

    .an-apply-btn {
        padding: 7px 14px;
        font-size: 13px;
        font-weight: 700;
        border-radius: 10px;
        background: #1d4ed8;
        color: #fff;
        border: none;
        cursor: pointer;
        font-family: inherit;
    }

    /* ── Legend ── */
    .an-legend {
        display: flex;
        gap: 16px;
        flex-wrap: wrap;
        margin-bottom: 14px;
    }

    .an-legend-item {
        display: flex;
        align-items: center;
        gap: 7px;
        font-size: 13px;
        color: #6b7280;
        font-weight: 500;
    }

    .an-legend-dot {
        width: 12px;
        height: 12px;
        border-radius: 3px;
        flex-shrink: 0;
    }

    /* ── Chart containers ── */
    .an-chart-box { position: relative; width: 100%; }
    .an-chart-box--tall { height: 240px; }
    .an-chart-box--med  { height: 200px; }

    /* ── Pro locked ── */
    .an-locked-overlay {
        border: 1.5px dashed #cbd5e1;
        border-radius: 14px;
        background: #f8fafc;
        padding: 36px 24px;
        text-align: center;
    }
    .an-locked-overlay p { margin: 0 0 14px; font-size: 15px; color: #374151; }

    /* ── Two-column layout ── */
    .an-two-col {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
        margin-bottom: 16px;
    }

    /* ── Top products ── */
    .an-product-row {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 0;
        border-bottom: 1px solid #f1f5f9;
    }
    .an-product-row:last-of-type { border-bottom: none; }

    .an-product-rank {
        width: 28px;
        height: 28px;
        border-radius: 8px;
        background: #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 13px;
        font-weight: 700;
        color: #64748b;
        flex-shrink: 0;
    }
    .an-product-rank.top { background: #fef9c3; color: #854d0e; }

    .an-product-icon {
        width: 40px;
        height: 40px;
        border-radius: 12px;
        background: #f3f4f6;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        font-size: 16px;
        overflow: hidden;
    }
    .an-product-icon img { width: 100%; height: 100%; object-fit: cover; }

    .an-product-info { flex: 1; min-width: 0; }
    .an-product-name {
        font-size: 14px;
        font-weight: 600;
        color: #111827;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .an-product-meta { font-size: 12px; color: #94a3b8; margin-top: 2px; }

    .an-product-val {
        flex-shrink: 0;
        font-size: 14px;
        font-weight: 700;
        color: #111827;
        font-family: 'DM Mono', monospace;
        text-align: right;
    }

    .an-total-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-top: 14px;
        margin-top: 6px;
        border-top: 1.5px solid #f1f5f9;
    }
    .an-total-label { font-size: 13px; color: #6b7280; font-weight: 600; }
    .an-total-val { font-size: 15px; font-weight: 700; color: #111827; font-family: 'DM Mono', monospace; }

    /* ── Insight cards ── */
    .an-insight-section { margin-bottom: 16px; }

    .an-insight-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 12px;
    }

    .an-insight-card {
        background: #fff;
        border: 1.5px solid #e8ecf0;
        border-radius: 16px;
        padding: 18px;
    }

    .an-insight-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        padding: 4px 10px;
        border-radius: 999px;
        margin-bottom: 14px;
    }

    .an-insight-badge--green { background: #dcfce7; color: #15803d; }
    .an-insight-badge--orange { background: #fff7ed; color: #c2410c; }
    .an-insight-badge--blue { background: #dbeafe; color: #1d4ed8; }

    .an-insight-item {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        margin-bottom: 12px;
    }
    .an-insight-item:last-child { margin-bottom: 0; }

    .an-insight-icon {
        font-size: 18px;
        line-height: 1;
        flex-shrink: 0;
        margin-top: 1px;
    }

    .an-insight-text {
        font-size: 13px;
        line-height: 1.65;
        color: #4b5563;
    }
    .an-insight-text strong { color: #0f1923; font-weight: 700; }

    /* ── Upsell ── */
    .an-upsell {
        background: linear-gradient(135deg, #eff6ff, #ffffff);
        border: 1.5px solid #bfdbfe;
        border-radius: 18px;
        padding: 20px 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        flex-wrap: wrap;
        margin-bottom: 16px;
    }
    .an-upsell-label { font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.06em; color: #1d4ed8; margin-bottom: 6px; }
    .an-upsell-title { font-size: 15px; font-weight: 700; color: #0f1923; margin: 0 0 4px; }
    .an-upsell-sub { font-size: 13px; color: #64748b; margin: 0; }

    /* ── Responsive ── */
    @media (max-width: 860px) {
        .an-two-col { grid-template-columns: 1fr; }
        .an-insight-grid { grid-template-columns: 1fr; }
    }

    @media (max-width: 600px) {
        .an-page { padding: 18px 14px; }
        .an-header-left h1 { font-size: 20px; }
        .an-card-inner { padding: 16px; }
        .an-card-header { flex-direction: column; align-items: flex-start; }
        .an-filter-bar { width: 100%; }
        .an-date-input { width: 130px; }
        .an-chart-box--tall { height: 200px; }
        .an-chart-box--med  { height: 170px; }
        .an-upsell { flex-direction: column; align-items: flex-start; }
    }
</style>

<div class="an-page">
<div class="an-shell">

    {{-- Header --}}
    <div class="an-header">
        <div class="an-header-left">
            <h1>Analitik Toko</h1>
        </div>
        @if($isProUser)
            <div class="an-export-wrap">
                <a href="{{ route('analitik.export', ['format' => 'csv']) }}" class="an-btn an-btn--outline">⬇ CSV</a>
                <a href="{{ route('analitik.export', ['format' => 'excel']) }}" class="an-btn an-btn--primary">⬇ Excel</a>
            </div>
        @else
            <div class="an-export-wrap">
                <a href="{{ route('premium.index') }}" class="an-btn an-btn--gold">⭐ Upgrade ke Pro</a>
            </div>
        @endif
    </div>

    @if(!$isProUser)
        <div class="an-free-banner">
            <strong>Kamu menggunakan akun Gratis.</strong> Beberapa fitur seperti nilai penjualan, konversi, dan ekspor data hanya tersedia di akun Pro.
        </div>
    @endif

    {{-- ── Chart 1: Views & Klik ── --}}
    <div class="an-card">
        <div class="an-card-inner">
            <div class="an-card-header">
                <div>
                    <div class="an-card-title">👁️ Views & Klik Harian</div>
                    <div class="an-card-sub">Berapa orang melihat dan mengklik produk kamu setiap hari</div>
                </div>
                <div>
                    <div class="an-filter-bar" id="vc-filter-bar">
                        <button class="an-filter-pill active" onclick="setVcFilter(7, this)">7 Hari</button>
                        <button class="an-filter-pill" onclick="setVcFilter(30, this)">30 Hari</button>
                        <button class="an-filter-pill" onclick="setVcFilter(9999, this)">Semua</button>
                    </div>
                    <div class="an-date-range" style="margin-top: 8px;" id="vc-custom-range">
                        <input type="date" class="an-date-input" id="vc-date-from" placeholder="Dari tanggal">
                        <span class="an-date-sep">→</span>
                        <input type="date" class="an-date-input" id="vc-date-to" placeholder="Sampai tanggal">
                        <button class="an-apply-btn" onclick="applyVcCustomRange()">Terapkan</button>
                    </div>
                </div>
            </div>
            <div class="an-legend">
                <div class="an-legend-item"><span class="an-legend-dot" style="background:#f59e0b;"></span>Views</div>
                <div class="an-legend-item"><span class="an-legend-dot" style="background:#2563eb;"></span>Klik</div>
            </div>
            <div class="an-chart-box an-chart-box--tall">
                <canvas id="viewsClicksChart"></canvas>
            </div>
        </div>
    </div>

    {{-- ── Chart 2: Penjualan ── --}}
    <div class="an-card">
        <div class="an-card-inner">
            <div class="an-card-header">
                <div>
                    <div class="an-card-title">💰 Tren Penjualan</div>
                    <div class="an-card-sub">Nilai transaksi harian di toko kamu</div>
                </div>
                @if($isProUser)
                    <div>
                        <div class="an-filter-bar" id="sales-filter-bar">
                            <button class="an-filter-pill active" onclick="setSalesFilter(7, this)">7 Hari</button>
                            <button class="an-filter-pill" onclick="setSalesFilter(30, this)">30 Hari</button>
                            <button class="an-filter-pill" onclick="setSalesFilter(9999, this)">Semua</button>
                        </div>
                        <div class="an-date-range" style="margin-top: 8px;">
                            <input type="date" class="an-date-input" id="sales-date-from">
                            <span class="an-date-sep">→</span>
                            <input type="date" class="an-date-input" id="sales-date-to">
                            <button class="an-apply-btn" onclick="applySalesCustomRange()">Terapkan</button>
                        </div>
                    </div>
                @endif
            </div>

            @if($isProUser)
                <div class="an-chart-box an-chart-box--med">
                    <canvas id="salesChart"></canvas>
                </div>
            @else
                <div class="an-locked-overlay">
                    <p>📈 Grafik tren penjualan hanya tersedia untuk akun <strong>Pro</strong>.</p>
                    <a href="{{ route('premium.index') }}" class="an-btn an-btn--gold">⭐ Upgrade ke Pro</a>
                </div>
            @endif
        </div>
    </div>

    {{-- ── Top Products + (kanan kosong atau stats) ── --}}
    <div class="an-two-col">
        <div class="an-card" style="margin-bottom: 0;">
            <div class="an-card-inner">
                <div class="an-card-title" style="margin-bottom: 4px;">🏆 Top 5 Produk</div>
                <div class="an-card-sub" style="margin-bottom: 18px;">Produk yang paling banyak terjual</div>

                @forelse($topProducts as $i => $product)
                    <div class="an-product-row">
                        <div class="an-product-rank {{ $i === 0 ? 'top' : '' }}">{{ $i + 1 }}</div>
                        <div class="an-product-icon">
                            @if($product->image_url)
                                <img src="{{ $product->image_url }}" alt="">
                            @else
                                🛍️
                            @endif
                        </div>
                        <div class="an-product-info">
                            <div class="an-product-name">{{ $product->title }}</div>
                            <div class="an-product-meta">{{ number_format($product->sold ?? 0) }} terjual</div>
                        </div>
                        <div class="an-product-val">Rp {{ number_format($product->revenue ?? 0, 0, ',', '.') }}</div>
                    </div>
                @empty
                    <p style="margin: 20px 0; text-align: center; font-size: 14px; color: #9ca3af;">Belum ada penjualan</p>
                @endforelse

                <div class="an-total-row">
                    <span class="an-total-label">Total semua penjualan</span>
                    <span class="an-total-val">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        <div class="an-card" style="margin-bottom: 0;">
            <div class="an-card-inner">
                <div class="an-card-title" style="margin-bottom: 4px;">📋 Ringkasan Singkat</div>
                <div class="an-card-sub" style="margin-bottom: 18px;">Apa yang terjadi di toko kamu periode ini</div>

                <div style="display: flex; flex-direction: column; gap: 14px;">
                    <div style="background: #f0fdf4; border-radius: 14px; padding: 16px;">
                        <div style="font-size: 12px; font-weight: 700; color: #15803d; margin-bottom: 6px; text-transform: uppercase; letter-spacing: 0.04em;">✅ Hari Terbaik</div>
                        <div style="font-size: 14px; color: #0f1923; line-height: 1.6;">
                            Trafik terbanyak pada <strong>{{ $advancedStats['best_traffic_label'] ?? '-' }}</strong>.<br>
                            Penjualan terbesar pada <strong>{{ $advancedStats['best_revenue_label'] ?? '-' }}</strong>
                            <span style="color: #6b7280;">(Rp {{ number_format($advancedStats['best_revenue_amount'] ?? 0, 0, ',', '.') }})</span>.
                        </div>
                    </div>

                    <div style="background: #fff7ed; border-radius: 14px; padding: 16px;">
                        <div style="font-size: 12px; font-weight: 700; color: #c2410c; margin-bottom: 6px; text-transform: uppercase; letter-spacing: 0.04em;">💡 Saran</div>
                        <div style="font-size: 14px; color: #0f1923; line-height: 1.6;">
                            Kamu aktif berjualan <strong>{{ number_format($advancedStats['active_sales_days'] ?? 0) }} hari</strong>.<br>
                            Rata-rata penjualan per hari: <strong>Rp {{ number_format($advancedStats['avg_revenue_per_sale'] ?? 0, 0, ',', '.') }}</strong>.<br>
                            Coba tambah foto produk atau bundling untuk meningkatkan penjualan.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Upsell --}}
    @unless($isProUser)
        <div class="an-upsell" style="margin-top: 16px;">
            <div>
                <div class="an-upsell-label">✨ Fitur Pro</div>
                <p class="an-upsell-title">Lihat lebih banyak data untuk kembangkan toko kamu</p>
                <p class="an-upsell-sub">Nilai penjualan lengkap, konversi klik ke beli, tren mingguan, dan ekspor laporan ke Excel/CSV.</p>
            </div>
            <a href="{{ route('premium.index') }}" class="an-btn an-btn--gold" style="white-space: nowrap;">⭐ Lihat Paket Pro</a>
        </div>
    @endunless

</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const allDates  = @json(collect($clicksPerDay)->pluck('date')->toArray());
    const allViews  = @json(collect($clicksPerDay)->pluck('views')->toArray());
    const allClicks = @json(collect($clicksPerDay)->pluck('clicks')->toArray());
    const allSales  = @json(collect($clicksPerDay)->pluck('sales')->toArray());

    const sharedTooltip = {
        backgroundColor: '#0f1923',
        titleColor: '#f9fafb',
        bodyColor: '#d1d5db',
        borderColor: '#374151',
        borderWidth: 1,
        cornerRadius: 10,
        padding: 12,
        titleFont: { size: 13, weight: '600', family: "'Plus Jakarta Sans', sans-serif" },
        bodyFont: { size: 13, family: "'Plus Jakarta Sans', sans-serif" },
    };

    const gridColor = '#f1f5f9';
    const tickColor = '#94a3b8';
    const tickFont  = { size: 12, family: "'Plus Jakarta Sans', sans-serif" };

    // ── Views & Klik chart ──
    const vcChart = new Chart(document.getElementById('viewsClicksChart').getContext('2d'), {
        type: 'bar',
        data: {
            labels: allDates.slice(-7),
            datasets: [
                {
                    label: 'Views',
                    data: allViews.slice(-7),
                    backgroundColor: '#f59e0b',
                    borderRadius: 8,
                    barPercentage: 0.5,
                    categoryPercentage: 0.65,
                },
                {
                    label: 'Klik',
                    data: allClicks.slice(-7),
                    backgroundColor: '#2563eb',
                    borderRadius: 8,
                    barPercentage: 0.5,
                    categoryPercentage: 0.65,
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false }, tooltip: sharedTooltip },
            scales: {
                y: { beginAtZero: true, grid: { color: gridColor }, ticks: { color: tickColor, font: tickFont } },
                x: { grid: { display: false }, ticks: { color: tickColor, font: tickFont } }
            }
        }
    });

    @if($isProUser)
    // ── Sales chart ──
    const salesChart = new Chart(document.getElementById('salesChart').getContext('2d'), {
        type: 'line',
        data: {
            labels: allDates.slice(-7),
            datasets: [{
                label: 'Penjualan (IDR)',
                data: allSales.slice(-7),
                borderColor: '#10b981',
                backgroundColor: 'rgba(16,185,129,0.10)',
                borderWidth: 2.5,
                fill: true,
                tension: 0.38,
                pointRadius: 4,
                pointHoverRadius: 6,
                pointBackgroundColor: '#10b981',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    ...sharedTooltip,
                    callbacks: { label: ctx => 'Rp ' + ctx.parsed.y.toLocaleString('id-ID') }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: gridColor },
                    ticks: {
                        color: tickColor, font: tickFont,
                        callback: val => val >= 1000000 ? 'Rp '+(val/1000000).toFixed(1)+'jt'
                                       : val >= 1000 ? 'Rp '+(val/1000).toFixed(0)+'rb'
                                       : 'Rp '+val
                    }
                },
                x: { grid: { display: false }, ticks: { color: tickColor, font: tickFont } }
            }
        }
    });
    @endif

    // ── Helper: slice by date range ──
    function sliceByDays(days) {
        return days >= allDates.length ? [0, allDates.length] : [allDates.length - days, allDates.length];
    }

    function sliceByDateRange(from, to) {
        const start = allDates.findIndex(d => d >= from);
        let end = allDates.length;
        for (let i = allDates.length - 1; i >= 0; i--) {
            if (allDates[i] <= to) { end = i + 1; break; }
        }
        return [Math.max(0, start), end];
    }

    // ── Views & Klik filter ──
    window.setVcFilter = function (days, btn) {
        document.querySelectorAll('#vc-filter-bar .an-filter-pill').forEach(b => b.classList.remove('active'));
        if (btn) btn.classList.add('active');
        const [s, e] = sliceByDays(days);
        vcChart.data.labels = allDates.slice(s, e);
        vcChart.data.datasets[0].data = allViews.slice(s, e);
        vcChart.data.datasets[1].data = allClicks.slice(s, e);
        vcChart.update();
    };

    window.applyVcCustomRange = function () {
        const from = document.getElementById('vc-date-from').value;
        const to   = document.getElementById('vc-date-to').value;
        if (!from || !to) return;
        document.querySelectorAll('#vc-filter-bar .an-filter-pill').forEach(b => b.classList.remove('active'));
        const [s, e] = sliceByDateRange(from, to);
        vcChart.data.labels = allDates.slice(s, e);
        vcChart.data.datasets[0].data = allViews.slice(s, e);
        vcChart.data.datasets[1].data = allClicks.slice(s, e);
        vcChart.update();
    };

    @if($isProUser)
    // ── Sales filter ──
    window.setSalesFilter = function (days, btn) {
        document.querySelectorAll('#sales-filter-bar .an-filter-pill').forEach(b => b.classList.remove('active'));
        if (btn) btn.classList.add('active');
        const [s, e] = sliceByDays(days);
        salesChart.data.labels = allDates.slice(s, e);
        salesChart.data.datasets[0].data = allSales.slice(s, e);
        salesChart.update();
    };

    window.applySalesCustomRange = function () {
        const from = document.getElementById('sales-date-from').value;
        const to   = document.getElementById('sales-date-to').value;
        if (!from || !to) return;
        document.querySelectorAll('#sales-filter-bar .an-filter-pill').forEach(b => b.classList.remove('active'));
        const [s, e] = sliceByDateRange(from, to);
        salesChart.data.labels = allDates.slice(s, e);
        salesChart.data.datasets[0].data = allSales.slice(s, e);
        salesChart.update();
    };
    @endif
});
</script>
@endsection