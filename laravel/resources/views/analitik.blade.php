@extends('layouts.dashboard')
@section('title', 'Analitik | Mobay.id')

@section('content')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,400;12..96,500;12..96,700&family=DM+Mono:wght@400;500&display=swap');

    .analitik-page,
    .analitik-page * {
        box-sizing: border-box;
    }

    .analitik-page {
        font-family: 'Bricolage Grotesque', sans-serif;
        background: #f8fafc;
        min-height: 100vh;
        padding: 20px;
        overflow-x: clip;
    }

    .an-shell {
        max-width: 1200px;
        width: 100%;
        margin: 0 auto;
    }

    .an-topbar {
        display: flex;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
        margin-bottom: 22px;
    }

    .an-back-btn {
        width: 38px;
        height: 38px;
        border-radius: 10px;
        background: #fff;
        border: 1px solid #e5e7eb;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #374151;
        text-decoration: none;
        flex-shrink: 0;
        font-size: 13px;
    }

    .an-topbar-text {
        min-width: 0;
        flex: 1 1 260px;
    }

    .an-topbar-text h1 {
        margin: 0;
        font-size: 20px;
        font-weight: 700;
        color: #111827;
    }

    .an-topbar-text p {
        margin: 4px 0 0;
        font-size: 12px;
        line-height: 1.6;
        color: #6b7280;
    }

    .an-export-actions {
        margin-left: auto;
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
    }

    .an-export-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        padding: 8px 14px;
        font-size: 12px;
        font-weight: 600;
        color: #1d4ed8;
        background: #fff;
        border: 1px solid #dbeafe;
        border-radius: 10px;
        text-decoration: none;
    }

    .an-export-btn--primary {
        background: #1d4ed8;
        color: #fff;
        border-color: #1d4ed8;
    }

    .an-filter-row {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
        margin-bottom: 18px;
    }

    .an-filter-mobile {
        display: none;
        margin-bottom: 18px;
    }

    .an-filter-label {
        display: block;
        margin-bottom: 8px;
        font-size: 11px;
        font-weight: 700;
        color: #6b7280;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .an-filter-select {
        width: 100%;
        min-height: 42px;
        padding: 10px 14px;
        border: 1px solid #d1d5db;
        border-radius: 12px;
        background: #fff;
        color: #111827;
        font-size: 13px;
        font-weight: 600;
        font-family: inherit;
        outline: none;
    }

    .an-filter-btn {
        padding: 7px 14px;
        font-size: 12px;
        font-weight: 600;
        border-radius: 999px;
        border: 1px solid #e5e7eb;
        background: #fff;
        color: #6b7280;
        cursor: pointer;
        font-family: inherit;
    }

    .an-filter-btn.active {
        background: #111827;
        color: #fff;
        border-color: #111827;
    }

    .an-kpi-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 12px;
        margin-bottom: 14px;
    }

    .an-kpi-card,
    .an-card {
        min-width: 0;
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 16px;
        overflow: hidden;
    }

    .an-kpi-card {
        padding: 16px;
    }

    .an-kpi-card-locked {
        opacity: 0.8;
        position: relative;
    }

    .an-kpi-card-locked::after {
        content: 'Pro';
        position: absolute;
        top: 10px;
        right: 10px;
        background: #f59e0b;
        color: #fff;
        font-size: 10px;
        padding: 2px 6px;
        border-radius: 999px;
        font-weight: 700;
    }

    .an-kpi-accent {
        width: 28px;
        height: 3px;
        border-radius: 999px;
        margin-bottom: 10px;
    }

    .an-kpi-label {
        margin-bottom: 6px;
        font-size: 11px;
        font-weight: 600;
        color: #6b7280;
        letter-spacing: 0.03em;
    }

    .an-kpi-value {
        margin-bottom: 10px;
        font-family: 'DM Mono', monospace;
        font-size: 22px;
        line-height: 1.2;
        font-weight: 700;
        color: #111827;
        word-break: break-word;
    }

    .an-badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        max-width: 100%;
        padding: 4px 8px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 700;
    }

    .an-badge-up { background: #ecfdf5; color: #15803d; }
    .an-badge-down { background: #fef2f2; color: #dc2626; }
    .an-badge-flat { background: #f1f5f9; color: #64748b; }

    .an-bottom-grid {
        display: grid;
        grid-template-columns: minmax(0, 1.1fr) minmax(0, 1fr) minmax(0, 0.95fr);
        gap: 12px;
        align-items: start;
    }

    .an-card-body {
        padding: 18px;
    }

    .an-card-title {
        margin: 0 0 4px;
        font-size: 14px;
        font-weight: 700;
        color: #111827;
    }

    .an-card-sub {
        margin: 0 0 14px;
        font-size: 12px;
        color: #6b7280;
        line-height: 1.6;
    }

    .an-legend {
        display: flex;
        gap: 14px;
        flex-wrap: wrap;
        margin-bottom: 12px;
    }

    .an-legend-item {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 11px;
        color: #6b7280;
    }

    .an-legend-dot {
        width: 10px;
        height: 10px;
        border-radius: 2px;
    }

    .an-chart-box {
        position: relative;
        width: 100%;
    }

    .an-chart-box--tall { height: 220px; }
    .an-chart-box--medium { height: 170px; }
    .an-chart-box--small { height: 110px; }

    .an-bar-row {
        margin-bottom: 14px;
    }

    .an-bar-row:last-child {
        margin-bottom: 0;
    }

    .an-bar-label-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 8px;
        margin-bottom: 6px;
    }

    .an-bar-label {
        min-width: 0;
        font-size: 12px;
        color: #6b7280;
        font-weight: 500;
    }

    .an-bar-pct {
        flex-shrink: 0;
        font-size: 12px;
        font-weight: 700;
        color: #111827;
        font-family: 'DM Mono', monospace;
    }

    .an-bar-track {
        height: 5px;
        background: #f1f5f9;
        border-radius: 999px;
        overflow: hidden;
    }

    .an-bar-fill {
        height: 100%;
        border-radius: inherit;
    }

    .an-divider {
        height: 1px;
        background: #f1f5f9;
        margin: 14px 0;
    }

    .an-mini-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 10px;
        margin-bottom: 14px;
    }

    .an-mini-box {
        min-width: 0;
        padding: 10px 12px;
        border-radius: 12px;
        background: #f8fafc;
    }

    .an-mini-box-label {
        margin-bottom: 4px;
        font-size: 10px;
        font-weight: 700;
        color: #94a3b8;
    }

    .an-mini-box-val {
        font-family: 'DM Mono', monospace;
        font-size: 17px;
        line-height: 1.35;
        font-weight: 700;
        color: #111827;
        word-break: break-word;
    }

    .an-product-row {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 10px 0;
        border-bottom: 1px solid #f3f4f6;
        min-width: 0;
    }

    .an-product-row:last-of-type {
        border-bottom: none;
    }

    .an-product-icon {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        background: #f3f4f6;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        font-size: 14px;
    }

    .an-product-name {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        font-size: 13px;
        font-weight: 600;
        color: #111827;
    }

    .an-product-meta {
        font-size: 11px;
        color: #6b7280;
    }

    .an-product-val {
        flex-shrink: 0;
        text-align: right;
        font-size: 13px;
        font-weight: 700;
        color: #111827;
        font-family: 'DM Mono', monospace;
    }

    .an-total-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        padding-top: 10px;
        margin-top: 4px;
        border-top: 1px solid #f3f4f6;
    }

    .an-total-label {
        font-size: 12px;
        color: #6b7280;
    }

    .an-total-val {
        text-align: right;
        font-size: 13px;
        font-weight: 700;
        color: #111827;
        font-family: 'DM Mono', monospace;
    }

    .an-insight-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 14px;
        margin-top: 12px;
    }

    .an-section-label {
        margin-bottom: 12px;
        font-size: 11px;
        font-weight: 700;
        color: #94a3b8;
        text-transform: uppercase;
        letter-spacing: 0.06em;
    }

    .an-insight-row {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        margin-bottom: 12px;
        min-width: 0;
    }

    .an-insight-dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        flex-shrink: 0;
        margin-top: 5px;
    }

    .an-insight-text {
        min-width: 0;
        font-size: 12px;
        line-height: 1.65;
        color: #6b7280;
    }

    .an-insight-hl {
        font-weight: 700;
        color: #111827;
    }

    .an-upsell {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 14px;
        flex-wrap: wrap;
        padding: 16px 18px;
        margin-top: 12px;
        background: linear-gradient(135deg, #f8fbff 0%, #ffffff 100%);
        border: 1px solid #dbeafe;
        border-radius: 16px;
    }

    @media (max-width: 1200px) {
        .analitik-page { padding: 18px; }
        .an-bottom-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .an-bottom-grid > :last-child { grid-column: 1 / -1; }
    }

    @media (max-width: 1024px) {
        .an-kpi-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    }

    @media (max-width: 820px) {
        .analitik-page { padding: 16px 14px; }
        .an-export-actions { margin-left: 0; width: 100%; }
        .an-bottom-grid,
        .an-insight-grid { grid-template-columns: 1fr; }
        .an-bottom-grid > :last-child { grid-column: auto; }
    }

    @media (max-width: 600px) {
        .analitik-page { padding: 14px 12px; }
        .an-topbar { gap: 10px; margin-bottom: 18px; }
        .an-topbar-text h1 { font-size: 17px; }
        .an-topbar-text p { font-size: 11px; }
        .an-export-actions {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 8px;
        }
        .an-export-btn {
            flex: none;
            min-height: 42px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 700;
            box-shadow: 0 8px 18px rgba(29, 78, 216, 0.10);
        }
        .an-export-btn--primary {
            box-shadow: 0 10px 22px rgba(29, 78, 216, 0.18);
        }
        .an-filter-row { display: none; }
        .an-filter-mobile { display: block; }
        .an-kpi-grid { grid-template-columns: 1fr; }
        .an-kpi-card,
        .an-card-body { padding: 14px; }
        .an-kpi-value { font-size: 18px; }
        .an-card-title { font-size: 13px; }
        .an-card-sub { font-size: 11px; margin-bottom: 12px; }
        .an-chart-box--tall { height: 190px; }
        .an-chart-box--medium { height: 150px; }
        .an-chart-box--small { height: 96px; }
        .an-mini-grid { grid-template-columns: 1fr; }
        .an-product-row { flex-wrap: wrap; align-items: flex-start; }
        .an-product-val { width: 100%; padding-left: 46px; text-align: left; }
    }

    @media (max-width: 420px) {
        .an-export-actions,
        .an-filter-row { grid-template-columns: 1fr; }
        .an-export-btn,
        .an-filter-btn { flex-basis: 100%; }
        .an-product-val { padding-left: 0; }
    }
</style>

<div class="analitik-page">
    <div class="an-shell">
        <div class="an-topbar">
            <a href="{{ route('dashboard') }}" class="an-back-btn">←</a>
            <div class="an-topbar-text">
                <h1>Analitik Toko</h1>
                <p>Pantau performa, penjualan, dan minat pembeli. Periode data {{ $analyticsRangeLabel }}</p>
            </div>
            @if($isProUser)
                <div class="an-export-actions">
                    <a href="{{ route('analitik.export', ['format' => 'csv']) }}" class="an-export-btn">↓ CSV</a>
                    <a href="{{ route('analitik.export', ['format' => 'excel']) }}" class="an-export-btn an-export-btn--primary">↓ Excel</a>
                </div>
            @else
                <div class="an-export-actions" style="display: flex; align-items: center; gap: 10px;">
                    <span style="background: rgba(249, 115, 22, 0.15); color: #c2410c; padding: 8px 12px; border-radius: 10px; font-size: 13px;">Free plan: fitur ekspor tidak tersedia</span>
                    <a href="{{ route('premium.index') }}" class="an-export-btn an-export-btn--primary" style="background: #f59e0b; color: #1e293b;">Upgrade ke Pro</a>
                </div>
            @endif
        </div>

        @if(!$isProUser)
            <div style="background: rgba(255,255,255,0.95); border: 1px solid rgba(226,232,240,0.8); border-radius: 14px; padding: 16px; margin-bottom: 20px; color: #0f172a;">
                <strong>Mode Free</strong> - Hanya metrik dasar ditampilkan. Upgrade ke <strong>Pro</strong> untuk melihat nilai penjualan, konversi lanjutan, analitik klik-to-sale, dan eksport data.
            </div>
        @endif

        <div class="an-filter-row">
            <button class="an-filter-btn active" onclick="setFilter(7, this)">7 hari</button>
            <button class="an-filter-btn" onclick="setFilter(30, this)">30 hari</button>
            <button class="an-filter-btn" onclick="setFilter(90, this)">Semua</button>
        </div>

        <div class="an-filter-mobile">
            <label for="an-filter-select" class="an-filter-label">Periode</label>
            <select id="an-filter-select" class="an-filter-select" onchange="setFilterFromSelect(this)">
                <option value="7" selected>7 hari terakhir</option>
                <option value="30">30 hari terakhir</option>
                <option value="90">Semua data</option>
            </select>
        </div>

        <div class="an-kpi-grid">
            <div class="an-kpi-card">
                <div class="an-kpi-accent" style="background:#2563eb;"></div>
                <div class="an-kpi-label">Total Views</div>
                <div class="an-kpi-value">{{ number_format($totalProfileViews) }}</div>
                @php $trend = $trendStats['views'] ?? null; @endphp
                <span class="an-badge {{ ($trend['direction'] ?? 'flat') === 'up' ? 'an-badge-up' : (($trend['direction'] ?? 'flat') === 'down' ? 'an-badge-down' : 'an-badge-flat') }}">
                    {{ ($trend['direction'] ?? 'flat') === 'up' ? '↑' : (($trend['direction'] ?? 'flat') === 'down' ? '↓' : '→') }}
                    {{ number_format((float) ($trend['percent'] ?? 0), 1) }}%
                </span>
            </div>

            <div class="an-kpi-card">
                <div class="an-kpi-accent" style="background:#f59e0b;"></div>
                <div class="an-kpi-label">Total Klik</div>
                <div class="an-kpi-value">{{ number_format($totalClicks) }}</div>
                @php $trend = $trendStats['clicks'] ?? null; @endphp
                <span class="an-badge {{ ($trend['direction'] ?? 'flat') === 'up' ? 'an-badge-up' : (($trend['direction'] ?? 'flat') === 'down' ? 'an-badge-down' : 'an-badge-flat') }}">
                    {{ ($trend['direction'] ?? 'flat') === 'up' ? '↑' : (($trend['direction'] ?? 'flat') === 'down' ? '↓' : '→') }}
                    {{ number_format((float) ($trend['percent'] ?? 0), 1) }}%
                </span>
            </div>

            @if($isProUser)
                <div class="an-kpi-card">
                    <div class="an-kpi-accent" style="background:#10b981;"></div>
                    <div class="an-kpi-label">Nilai Penjualan</div>
                    <div class="an-kpi-value">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</div>
                    @php $trend = $trendStats['revenue'] ?? null; @endphp
                    <span class="an-badge {{ ($trend['direction'] ?? 'flat') === 'up' ? 'an-badge-up' : (($trend['direction'] ?? 'flat') === 'down' ? 'an-badge-down' : 'an-badge-flat') }}">
                        {{ ($trend['direction'] ?? 'flat') === 'up' ? '↑' : (($trend['direction'] ?? 'flat') === 'down' ? '↓' : '→') }}
                        {{ number_format((float) ($trend['percent'] ?? 0), 1) }}%
                    </span>
                </div>

                <div class="an-kpi-card">
                    <div class="an-kpi-accent" style="background:#8b5cf6;"></div>
                    <div class="an-kpi-label">Konversi Klik ke Beli</div>
                    <div class="an-kpi-value">{{ number_format($advancedStats['conversion_click_to_sale'] ?? 0, 1, ',', '.') }}%</div>
                    @php $trend = $trendStats['conversion_click_to_sale'] ?? null; @endphp
                    <span class="an-badge {{ ($trend['direction'] ?? 'flat') === 'up' ? 'an-badge-up' : (($trend['direction'] ?? 'flat') === 'down' ? 'an-badge-down' : 'an-badge-flat') }}">
                        {{ ($trend['direction'] ?? 'flat') === 'up' ? '↑' : (($trend['direction'] ?? 'flat') === 'down' ? '↓' : '→') }}
                        {{ number_format((float) ($trend['percent'] ?? 0), 1) }}%
                    </span>
                </div>
            @else
                <div class="an-kpi-card an-kpi-card-locked">
                    <div class="an-kpi-accent" style="background:#d1d5db;"></div>
                    <div class="an-kpi-label">Nilai Penjualan</div>
                    <div class="an-kpi-value">-</div>
                    <span class="an-badge an-badge-flat" style="background: rgba(241, 245, 249, 0.9); color: #475569;">PRO</span>
                </div>

                <div class="an-kpi-card an-kpi-card-locked">
                    <div class="an-kpi-accent" style="background:#d1d5db;"></div>
                    <div class="an-kpi-label">Konversi Klik ke Beli</div>
                    <div class="an-kpi-value">-</div>
                    <span class="an-badge an-badge-flat" style="background: rgba(241, 245, 249, 0.9); color: #475569;">PRO</span>
                </div>
            @endif
        </div>

        <div class="an-card" style="margin-bottom: 12px;">
            <div class="an-card-body">
                <div class="an-card-title">Views & Klik Harian</div>
                <div class="an-card-sub">Bandingkan trafik dan minat pembeli per hari.</div>
                <div class="an-legend">
                    <div class="an-legend-item"><span class="an-legend-dot" style="background:#f59e0b;"></span>Views</div>
                    <div class="an-legend-item"><span class="an-legend-dot" style="background:#2563eb;"></span>Klik</div>
                </div>
                <div class="an-chart-box an-chart-box--tall">
                    <canvas id="viewsClicksChart"></canvas>
                </div>
            </div>
        </div>

        <div class="an-bottom-grid">
            <div class="an-card">
                <div class="an-card-body">
                    <div class="an-card-title">Tren Penjualan</div>
                    <div class="an-card-sub">Nilai transaksi harian selama periode yang dipilih.</div>

                    @if($isProUser)
                        <div class="an-chart-box an-chart-box--medium">
                            <canvas id="salesChart"></canvas>
                        </div>
                    @else
                        <div style="border: 1px dashed #cbd5e1; border-radius: 10px; background: #f8fafc; padding: 28px; text-align: center;">
                            <p style="font-size: 14px; color: #334155; margin: 0 0 10px;">Fitur tambahan khusus PRO.</p>
                            <a href="{{ route('premium.index') }}" class="an-export-btn an-export-btn--primary" style="background: #f59e0b; color: white;">Upgrade to Pro untuk melihat tren penjualan</a>
                        </div>
                    @endif
                </div>
            </div>

            <div class="an-card">
                <div class="an-card-body">
                    <div class="an-card-title">Konversi & Pendapatan per Klik</div>
                    <div class="an-card-sub">Melihat efisiensi trafik yang masuk menjadi pembelian.</div>

                    @if($isProUser)
                        <div class="an-mini-grid">
                            <div class="an-mini-box">
                                <div class="an-mini-box-label">View ke Klik</div>
                                <div class="an-mini-box-val">{{ number_format($advancedStats['conversion_view_to_click'] ?? 0, 1, ',', '.') }}%</div>
                            </div>
                            <div class="an-mini-box">
                                <div class="an-mini-box-label">Rp per Klik</div>
                                <div class="an-mini-box-val">Rp {{ number_format($advancedStats['revenue_per_click'] ?? 0, 0, ',', '.') }}</div>
                            </div>
                        </div>
                    @else
                        <div style="border: 1px dashed #cbd5e1; border-radius: 10px; background: #f8fafc; padding: 28px; text-align: center;">
                            <p style="font-size: 14px; color: #334155; margin: 0 0 10px;">Fitur konversi dan pendapatan per klik hanya tersedia untuk akun PRO.</p>
                            <a href="{{ route('premium.index') }}" class="an-export-btn an-export-btn--primary" style="background: #f59e0b; color: white;">Upgrade ke Pro untuk membuka akses</a>
                        </div>
                    @endif
                        </div>
                    </div>
                    <div class="an-chart-box an-chart-box--small">
                        <canvas id="convChart"></canvas>
                    </div>
                </div>
            </div>

            <div class="an-card">
                <div class="an-card-body">
                    <div class="an-card-title">Top 5 Produk</div>
                    <div class="an-card-sub">Produk dengan penjualan terbaik saat ini.</div>

                    @forelse($topProducts as $product)
                        <div class="an-product-row">
                            @if($product->image_url)
                                <img src="{{ $product->image_url }}" style="width:36px;height:36px;border-radius:10px;object-fit:cover;flex-shrink:0;">
                            @else
                                <div class="an-product-icon">🛍️</div>
                            @endif
                            <div style="flex:1;min-width:0;">
                                <div class="an-product-name">{{ $product->title }}</div>
                                <div class="an-product-meta">{{ number_format($product->sold ?? 0) }} terjual</div>
                            </div>
                            <div class="an-product-val">Rp {{ number_format($product->revenue ?? 0, 0, ',', '.') }}</div>
                        </div>
                    @empty
                        <p style="margin:0;padding:20px 0;text-align:center;font-size:13px;color:#9ca3af;">Belum ada penjualan</p>
                    @endforelse

                    <div class="an-total-row">
                        <span class="an-total-label">Total penjualan</span>
                        <span class="an-total-val">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="an-card" style="margin-top: 12px;">
            <div class="an-card-body">
                <div class="an-card-title">Ringkasan & Insight</div>
                <div class="an-card-sub">Poin yang paling layak diperhatikan dalam periode ini.</div>

                <div class="an-insight-grid">
                    <div>
                        <div class="an-section-label">Terbaik Periode Ini</div>
                        <div class="an-insight-row">
                            <div class="an-insight-dot" style="background:#10b981;"></div>
                            <div class="an-insight-text">
                                <span class="an-insight-hl">{{ $advancedStats['best_traffic_label'] ?? '-' }}</span>
                                menjadi hari trafik tertinggi. Jadwalkan promosi di pola hari ini.
                            </div>
                        </div>
                        <div class="an-insight-row">
                            <div class="an-insight-dot" style="background:#2563eb;"></div>
                            <div class="an-insight-text">
                                Hari penjualan terbaik adalah <span class="an-insight-hl">{{ $advancedStats['best_revenue_label'] ?? '-' }}</span> dengan Rp {{ number_format($advancedStats['best_revenue_amount'] ?? 0, 0, ',', '.') }}.
                            </div>
                        </div>
                    </div>

                    <div>
                        <div class="an-section-label">Yang Perlu Ditingkatkan</div>
                        <div class="an-insight-row">
                            <div class="an-insight-dot" style="background:#f59e0b;"></div>
                            <div class="an-insight-text">
                                Konversi klik ke beli masih <span class="an-insight-hl">{{ number_format($advancedStats['conversion_click_to_sale'] ?? 0, 1, ',', '.') }}%</span>. Coba tambah foto, bukti sosial, atau bundling.
                            </div>
                        </div>
                        <div class="an-insight-row">
                            <div class="an-insight-dot" style="background:#8b5cf6;"></div>
                            <div class="an-insight-text">
                                Mayoritas pembeli datang dari mobile. Pastikan halaman publik tetap nyaman dan cepat di HP.
                            </div>
                        </div>
                    </div>

                    <div>
                        <div class="an-section-label">Peluang</div>
                        <div class="an-insight-row">
                            <div class="an-insight-dot" style="background:#ec4899;"></div>
                            <div class="an-insight-text">
                                Kamu aktif berjualan <span class="an-insight-hl">{{ number_format($advancedStats['active_sales_days'] ?? 0) }} hari</span>. Konsistensi promosi bisa masih ditingkatkan.
                            </div>
                        </div>
                        <div class="an-insight-row">
                            <div class="an-insight-dot" style="background:#0f766e;"></div>
                            <div class="an-insight-text">
                                Rata-rata nilai per penjualan saat ini <span class="an-insight-hl">Rp {{ number_format($advancedStats['avg_revenue_per_sale'] ?? 0, 0, ',', '.') }}</span>. Bundle produk bisa membantu menaikkan angka ini.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @unless($isProUser)
            <div class="an-upsell">
                <div>
                    <div style="font-size:11px;font-weight:700;color:#1d4ed8;text-transform:uppercase;letter-spacing:0.04em;margin-bottom:6px;">Fitur Pro</div>
                    <p style="font-size:14px;font-weight:700;color:#111827;margin:0 0 4px;">Buka analitik lanjutan untuk penjual berkembang</p>
                    <p style="font-size:12px;color:#6b7280;margin:0;">Konversi lengkap, produk paling dilirik, tren mingguan, dan ekspor laporan.</p>
                </div>
                <a href="{{ route('premium.index') }}" class="an-export-btn an-export-btn--primary">Lihat Pro</a>
            </div>
        @endunless
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const allDates = @json(collect($clicksPerDay)->pluck('date')->toArray());
    const allViews = @json(collect($clicksPerDay)->pluck('views')->toArray());
    const allClicks = @json(collect($clicksPerDay)->pluck('clicks')->toArray());
    const allSales = @json(collect($clicksPerDay)->pluck('sales')->toArray());
    const allSold = @json(collect($clicksPerDay)->pluck('sold')->toArray());

    const convSeries = allClicks.map((clicks, index) => clicks > 0 ? Number(((allSold[index] / clicks) * 100).toFixed(1)) : 0);
    const sharedTooltip = {
        backgroundColor: '#111827',
        titleColor: '#f9fafb',
        bodyColor: '#d1d5db',
        borderColor: '#374151',
        borderWidth: 1,
        cornerRadius: 8,
        padding: 10,
        titleFont: { size: 12, weight: '500' },
        bodyFont: { size: 12 },
    };
    const gridColor = '#f1f5f9';
    const tickColor = '#9ca3af';

    const vcChart = new Chart(document.getElementById('viewsClicksChart').getContext('2d'), {
        type: 'bar',
        data: {
            labels: allDates.slice(-7),
            datasets: [
                {
                    label: 'Views',
                    data: allViews.slice(-7),
                    backgroundColor: '#f59e0b',
                    borderRadius: 7,
                    barPercentage: 0.48,
                    categoryPercentage: 0.62,
                },
                {
                    label: 'Klik',
                    data: allClicks.slice(-7),
                    backgroundColor: '#2563eb',
                    borderRadius: 7,
                    barPercentage: 0.48,
                    categoryPercentage: 0.62,
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: sharedTooltip
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: gridColor },
                    ticks: { color: tickColor, font: { size: 11 } }
                },
                x: {
                    grid: { display: false },
                    ticks: { color: tickColor, font: { size: 11 } }
                }
            }
        }
    });

    @if($isProUser)
        const salesChart = new Chart(document.getElementById('salesChart').getContext('2d'), {
            type: 'line',
            data: {
                labels: allDates.slice(-7),
                datasets: [{
                    label: 'Penjualan (IDR)',
                    data: allSales.slice(-7),
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16,185,129,0.10)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.38,
                    pointRadius: 3.5,
                    pointHoverRadius: 4.5,
                    pointBackgroundColor: '#10b981',
                    pointBorderWidth: 0,
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
                            color: tickColor,
                            font: { size: 11 },
                            callback: value => 'Rp ' + (value / 1000 >= 1 ? (value / 1000) + 'rb' : value)
                        }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { color: tickColor, font: { size: 11 } }
                    }
                }
            }
        });

        const convChart = new Chart(document.getElementById('convChart').getContext('2d'), {
            type: 'line',
            data: {
                labels: allDates.slice(-14),
                datasets: [{
                    label: 'Konversi Klik ke Beli',
                    data: convSeries.slice(-14),
                    borderColor: '#8b5cf6',
                    backgroundColor: 'rgba(139,92,246,0.12)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 0,
                    pointHoverRadius: 3,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        ...sharedTooltip,
                        callbacks: { label: ctx => ctx.parsed.y.toLocaleString('id-ID') + '%' }
                    }
                },
                scales: {
                    y: { display: false },
                    x: { display: false }
                }
            }
        });
    @endif

    window.setFilter = function (days, button) {
        document.querySelectorAll('.an-filter-btn').forEach(btn => btn.classList.remove('active'));
        if (button) {
            button.classList.add('active');
        }

        const mobileSelect = document.getElementById('an-filter-select');
        if (mobileSelect) {
            mobileSelect.value = String(days);
        }

        const slice = days === 90 ? allDates.length : days;

        vcChart.data.labels = allDates.slice(-slice);
        vcChart.data.datasets[0].data = allViews.slice(-slice);
        vcChart.data.datasets[1].data = allClicks.slice(-slice);
        vcChart.update();

        @if($isProUser)
            salesChart.data.labels = allDates.slice(-slice);
            salesChart.data.datasets[0].data = allSales.slice(-slice);
            salesChart.update();
        @endif
    };

    window.setFilterFromSelect = function (select) {
        const days = Number(select.value);
        const matchingButton = Array.from(document.querySelectorAll('.an-filter-btn'))
            .find(btn => btn.textContent.toLowerCase().includes(days === 90 ? 'semua' : String(days)));

        window.setFilter(days, matchingButton || null);
    };
});
</script>
@endsection
