@extends('layouts.dashboard')
@section('title', 'Analitik | Mobay.id')

@section('content')
<div style="padding: 24px; min-height: 100vh;" class="analitik-page">
    <div style="max-width: 1200px; margin: 0 auto;">

        {{-- Header --}}
        <div style="margin-bottom: 28px; display: flex; align-items: center; gap: 12px;">
            <a href="{{ route('dashboard') }}" style="width: 36px; height: 36px; border-radius: 8px; background: #fff; border: 1px solid #e5e7eb; display: flex; align-items: center; justify-content: center; text-decoration: none; color: #374151; flex-shrink: 0;">
                <i class="fas fa-arrow-left" style="font-size: 13px;"></i>
            </a>
            <div>
                <h1 style="margin: 0; font-size: 22px; font-weight: 700; color: #111827;">Analitik</h1>
                <p style="margin: 0; font-size: 13px; color: #6b7280;">Pantau performa link dan aktivitas pengunjung</p>
            </div>
        </div>

        {{-- Layout 2 Kolom utama --}}
        <div class="analitik-grid" style="display: grid; grid-template-columns: 1fr 320px; gap: 20px; align-items: start;">

            {{-- KOLOM KIRI --}}
            <div style="display: flex; flex-direction: column; gap: 20px;">

                {{-- CARD: Total Views & Clicks --}}
                <div style="background: #fff; border-radius: 16px; padding: 24px; border: 1px solid #e5e7eb;">
                    <div class="analitik-card-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 12px;">
                        <div>
                            <h3 style="margin: 0 0 12px 0; font-size: 16px; font-weight: 700; color: #111827;">Total Views & Clicks</h3>
                            <div class="analitik-stat-row" style="display: flex; gap: 28px;">
                                <div style="display: flex; align-items: center; gap: 8px;">
                                    <span style="width: 12px; height: 12px; border-radius: 50%; background: #f59e0b; display: inline-block; flex-shrink: 0;"></span>
                                    <span style="font-size: 13px; color: #6b7280;">Views</span>
                                    <span class="analitik-stat-val" style="font-size: 26px; font-weight: 800; color: #111827; line-height: 1;">{{ number_format($totalProfileViews) }}</span>
                                </div>
                                <div style="display: flex; align-items: center; gap: 8px;">
                                    <span style="width: 12px; height: 12px; border-radius: 50%; background: #2563eb; display: inline-block; flex-shrink: 0;"></span>
                                    <span style="font-size: 13px; color: #6b7280;">Clicks</span>
                                    <span class="analitik-stat-val" style="font-size: 26px; font-weight: 800; color: #111827; line-height: 1;">{{ number_format($totalClicks) }}</span>
                                </div>
                            </div>
                        </div>
                        <div style="display: flex; background: #f3f4f6; border-radius: 8px; padding: 3px; gap: 2px;">
                            <button onclick="filterChart(7, this)" class="filter-btn active-filter" style="padding: 6px 14px; font-size: 13px; font-weight: 500; border: none; background: #2563eb; color: white; border-radius: 6px; cursor: pointer;">7H</button>
                            <button onclick="filterChart(30, this)" class="filter-btn" style="padding: 6px 14px; font-size: 13px; font-weight: 500; border: none; background: transparent; color: #6b7280; border-radius: 6px; cursor: pointer;">30H</button>
                            <button onclick="filterChart(90, this)" class="filter-btn" style="padding: 6px 14px; font-size: 13px; font-weight: 500; border: none; background: transparent; color: #6b7280; border-radius: 6px; cursor: pointer;">Semua</button>
                        </div>
                    </div>
                    <div style="height: 260px;">
                        <canvas id="viewsClicksChart"></canvas>
                    </div>
                </div>

                {{-- CARD: Total Sales --}}
                <div style="background: #fff; border-radius: 16px; padding: 24px; border: 1px solid #e5e7eb;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 12px;">
                        <div>
                            <h3 style="margin: 0 0 12px 0; font-size: 16px; font-weight: 700; color: #111827;">Total Penjualan</h3>
                            <div class="analitik-stat-row" style="display: flex; gap: 28px;">
                                <div style="display: flex; align-items: center; gap: 8px;">
                                    <span style="width: 12px; height: 12px; border-radius: 50%; background: #f59e0b; display: inline-block;"></span>
                                    <span style="font-size: 13px; color: #6b7280;">Jumlah Terjual</span>
                                    <span class="analitik-stat-val" style="font-size: 26px; font-weight: 800; color: #111827; line-height: 1;">{{ number_format($totalSold) }}</span>
                                </div>
                                <div style="display: flex; align-items: center; gap: 8px;">
                                    <span style="width: 12px; height: 12px; border-radius: 50%; background: #10b981; display: inline-block;"></span>
                                    <span style="font-size: 13px; color: #6b7280;">Nilai Penjualan</span>
                                    <span class="analitik-stat-val" style="font-size: 26px; font-weight: 800; color: #111827; line-height: 1;">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</span>
                                </div>
                            </div>
                        </div>
                        <div style="display: flex; background: #f3f4f6; border-radius: 8px; padding: 3px; gap: 2px;">
                            <button onclick="filterSales(7, this)" class="filter-sales-btn" style="padding: 6px 14px; font-size: 13px; font-weight: 500; border: none; background: #10b981; color: white; border-radius: 6px; cursor: pointer;">7H</button>
                            <button onclick="filterSales(30, this)" class="filter-sales-btn" style="padding: 6px 14px; font-size: 13px; font-weight: 500; border: none; background: transparent; color: #6b7280; border-radius: 6px; cursor: pointer;">30H</button>
                            <button onclick="filterSales(90, this)" class="filter-sales-btn" style="padding: 6px 14px; font-size: 13px; font-weight: 500; border: none; background: transparent; color: #6b7280; border-radius: 6px; cursor: pointer;">Semua</button>
                        </div>
                    </div>
                    <div style="height: 200px;">
                        <canvas id="salesChart"></canvas>
                    </div>
                </div>

            </div>

            {{-- KOLOM KANAN --}}
            <div style="display: flex; flex-direction: column; gap: 20px;">

                {{-- CARD: Top 5 Produk --}}
                <div style="background: #fff; border-radius: 16px; padding: 24px; border: 1px solid #e5e7eb;">
                    <div style="margin-bottom: 16px; display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <h3 style="margin: 0 0 2px 0; font-size: 15px; font-weight: 700; color: #111827;">Top 5 Produk</h3>
                            <p style="margin: 0; font-size: 12px; color: #6b7280;">Berdasarkan penjualan</p>
                        </div>
                        <span style="font-size: 12px; color: #6b7280; background: #f3f4f6; padding: 4px 10px; border-radius: 6px;">
                            Penjualan (IDR)
                        </span>
                    </div>

                    @forelse($topProducts as $product)
                    <div style="padding: 10px 0; border-bottom: 1px solid #f3f4f6; display: flex; align-items: center; gap: 10px;">
                        @if($product->image_url)
                            <img src="{{ $product->image_url }}" style="width: 40px; height: 40px; border-radius: 8px; object-fit: cover; flex-shrink: 0;">
                        @else
                            <div style="width: 40px; height: 40px; border-radius: 8px; background: #f3f4f6; display: flex; align-items: center; justify-content: center; flex-shrink: 0; font-size: 18px;">🛍️</div>
                        @endif
                        <div style="flex: 1; min-width: 0;">
                            <p style="margin: 0 0 2px 0; font-size: 13px; font-weight: 600; color: #111827; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $product->title }}</p>
                            <p style="margin: 0; font-size: 12px; color: #6b7280;">{{ number_format($product->sold ?? 0) }} terjual</p>
                        </div>
                        <div style="text-align: right; flex-shrink: 0;">
                            <p style="margin: 0; font-size: 13px; font-weight: 700; color: #111827;">Rp {{ number_format($product->revenue ?? 0, 0, ',', '.') }}</p>
                        </div>
                    </div>
                    @empty
                    <div style="text-align: center; padding: 28px 0; color: #9ca3af;">
                        <p style="margin: 0; font-size: 13px;">Belum ada penjualan</p>
                    </div>
                    @endforelse

                    <div style="margin-top: 16px; padding-top: 12px; border-top: 1px solid #f3f4f6; display: flex; justify-content: space-between;">
                        <span style="font-size: 13px; color: #6b7280;">Total Penjualan</span>
                        <span style="font-size: 13px; font-weight: 700; color: #111827;">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</span>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<style>
    @media (max-width: 900px) {
        .analitik-grid { grid-template-columns: 1fr !important; }
    }
    @media (max-width: 600px) {
        .analitik-page { padding: 16px 12px !important; }
        .analitik-stat-row { flex-wrap: wrap; gap: 12px !important; }
        .analitik-stat-val { font-size: 20px !important; }
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {

    const allDates  = @json(collect($clicksPerDay)->pluck('date')->toArray());
    const allViews  = @json(collect($clicksPerDay)->pluck('views')->toArray());
    const allClicks = @json(collect($clicksPerDay)->pluck('clicks')->toArray());
    const allSales  = @json(collect($clicksPerDay)->pluck('sales')->toArray());

    // ── Views & Clicks BAR Chart ──────────────────────────────
    const vcCtx = document.getElementById('viewsClicksChart').getContext('2d');
    let vcChart = new Chart(vcCtx, {
        type: 'bar',
        data: {
            labels: allDates.slice(-7),
            datasets: [
                {
                    label: 'Views',
                    data: allViews.slice(-7),
                    backgroundColor: '#f59e0b',
                    borderRadius: 6,
                    barPercentage: 0.5,
                    categoryPercentage: 0.6,
                },
                {
                    label: 'Clicks',
                    data: allClicks.slice(-7),
                    backgroundColor: '#2563eb',
                    borderRadius: 6,
                    barPercentage: 0.5,
                    categoryPercentage: 0.6,
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#1e293b', titleColor: '#fff',
                    bodyColor: '#e2e8f0', borderColor: '#334155',
                    borderWidth: 1, cornerRadius: 8,
                }
            },
            scales: {
                y: { beginAtZero: true, grid: { color: '#f1f5f9' }, ticks: { color: '#9ca3af', font: { size: 11 } } },
                x: { grid: { display: false }, ticks: { color: '#9ca3af', font: { size: 11 } } }
            }
        }
    });

    // ── Total Sales LINE Chart ────────────────────────────────
    const salesCtx = document.getElementById('salesChart').getContext('2d');
    let salesChart = new Chart(salesCtx, {
        type: 'line',
        data: {
            labels: allDates.slice(-7),
            datasets: [{
                label: 'Penjualan (IDR)',
                data: allSales.slice(-7),
                borderColor: '#10b981',
                backgroundColor: 'rgba(16,185,129,0.08)',
                borderWidth: 2,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: '#10b981',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 4,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#1e293b', titleColor: '#fff',
                    bodyColor: '#e2e8f0', borderColor: '#334155',
                    borderWidth: 1, cornerRadius: 8,
                    callbacks: { label: ctx => 'Rp ' + ctx.parsed.y.toLocaleString('id-ID') }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: '#f1f5f9' },
                    ticks: {
                        color: '#9ca3af', font: { size: 11 },
                        callback: v => 'Rp ' + (v/1000 >= 1 ? (v/1000)+'rb' : v)
                    }
                },
                x: { grid: { display: false }, ticks: { color: '#9ca3af', font: { size: 11 } } }
            }
        }
    });

    // ── Filter Views & Clicks ─────────────────────────────────
    window.filterChart = function (days, btn) {
        document.querySelectorAll('.filter-btn').forEach(b => {
            b.style.background = 'transparent';
            b.style.color = '#6b7280';
        });
        btn.style.background = '#2563eb';
        btn.style.color = 'white';

        const slice = days === 90 ? allDates.length : days;
        vcChart.data.labels = allDates.slice(-slice);
        vcChart.data.datasets[0].data = allViews.slice(-slice);
        vcChart.data.datasets[1].data = allClicks.slice(-slice);
        vcChart.update();
    };

    // ── Filter Sales ──────────────────────────────────────────
    window.filterSales = function (days, btn) {
        document.querySelectorAll('.filter-sales-btn').forEach(b => {
            b.style.background = 'transparent';
            b.style.color = '#6b7280';
        });
        btn.style.background = '#10b981';
        btn.style.color = 'white';

        const slice = days === 90 ? allDates.length : days;
        salesChart.data.labels = allDates.slice(-slice);
        salesChart.data.datasets[0].data = allSales.slice(-slice);
        salesChart.update();
    };

});
</script>

@endsection
