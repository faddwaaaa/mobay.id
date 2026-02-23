@extends('layouts.dashboard')
@section('title', 'Analitik | Payou.id')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/analitik-darkmode.css') }}">
@endpush

@section('content')
<div style="min-height: 100vh; padding: 24px;" class="analitik-page">
    <div style="max-width: 1400px; margin: 0 auto;">
        
        {{-- Header --}}
        <div style="margin-bottom: 24px;">
            <div style="display: flex; align-items: center; gap: 12px;">
                <a href="{{ route('dashboard') }}" class="back-button" style="width: 36px; height: 36px; border-radius: 8px; display: flex; align-items: center; justify-content: center; text-decoration: none;">
                    <i class="fas fa-arrow-left" style="font-size: 14px;"></i>
                </a>
                <div>
                    <h1 class="page-title" style="margin: 0; font-size: 24px; font-weight: 600; color: #000000;">Analitik</h1>
                    <p class="page-subtitle" style="margin: 0; font-size: 14px; color: #797979;">Pantau performa link dan aktivitas pengunjung</p>
                </div>
            </div>
        </div>

        {{-- Layout Grid --}}
        <div style="display: grid; grid-template-columns: repeat(12, 1fr); gap: 20px;">
            
            {{-- Column 1: Key Metrics (Full Width) --}}
            <div style="grid-column: span 12;">
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 16px;">
                    
                    {{-- Total Klik --}}
                    <div class="stat-card" style="border-radius: 12px; padding: 20px;">
                        <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 12px;">
                            <div style="width: 48px; height: 48px; background: #eff6ff; border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-mouse-pointer" style="font-size: 18px; color: #3b82f6;"></i>
                            </div>
                            <div>
                                <h3 class="stat-value" style="margin: 0; font-size: 28px; font-weight: 700;">{{ number_format($totalClicks) }}</h3>
                                <p class="stat-label" style="margin: 0; font-size: 13px;">Total Klik</p>
                            </div>
                        </div>
                        <div style="display: flex; align-items: center; gap: 6px;">
                            <i class="fas fa-arrow-up" style="font-size: 12px; color: #10b981;"></i>
                            <span class="stat-desc" style="font-size: 12px;">Lifetime</span>
                        </div>
                    </div>

                    {{-- Total Link --}}
                    <div class="stat-card" style="border-radius: 12px; padding: 20px;">
                        <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 12px;">
                            <div style="width: 48px; height: 48px; background: #f0fdf4; border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-link" style="font-size: 18px; color: #10b981;"></i>
                            </div>
                            <div>
                                <h3 class="stat-value" style="margin: 0; font-size: 28px; font-weight: 700;">{{ number_format($totalLinks) }}</h3>
                                <p class="stat-label" style="margin: 0; font-size: 13px;">Total Link</p>
                            </div>
                        </div>
                        <div style="display: flex; align-items: center; gap: 6px;">
                            <i class="fas fa-check-circle" style="font-size: 12px; color: #10b981;"></i>
                            <span class="stat-desc" style="font-size: 12px;">Aktif & Nonaktif</span>
                        </div>
                    </div>

                    {{-- Pengunjung Unik --}}
                    <div class="stat-card" style="border-radius: 12px; padding: 20px;">
                        <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 12px;">
                            <div style="width: 48px; height: 48px; background: #fce7f3; border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-users" style="font-size: 18px; color: #ec4899;"></i>
                            </div>
                            <div>
                                <h3 class="stat-value" style="margin: 0; font-size: 28px; font-weight: 700;">{{ number_format($uniqueVisitors) }}</h3>
                                <p class="stat-label" style="margin: 0; font-size: 13px;">Pengunjung Unik</p>
                            </div>
                        </div>
                        <div style="display: flex; align-items: center; gap: 6px;">
                            <i class="fas fa-user-check" style="font-size: 12px; color: #3b82f6;"></i>
                            <span class="stat-desc" style="font-size: 12px;">Berdasarkan IP</span>
                        </div>
                    </div>

                    {{-- CTR --}}
                    <div class="stat-card" style="border-radius: 12px; padding: 20px;">
                        <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 12px;">
                            <div style="width: 48px; height: 48px; background: #fef3c7; border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-chart-line" style="font-size: 18px; color: #f59e0b;"></i>
                            </div>
                            <div>
                                <h3 class="stat-value" style="margin: 0; font-size: 28px; font-weight: 700;">{{ number_format($ctr, 1) }}%</h3>
                                <p class="stat-label" style="margin: 0; font-size: 13px;">Click Rate</p>
                            </div>
                        </div>
                        <div style="display: flex; align-items: center; gap: 6px;">
                            <i class="fas fa-percentage" style="font-size: 12px; color: #f59e0b;"></i>
                            <span class="stat-desc" style="font-size: 12px;">Rata-rata klik per sesi</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Column 2: Performance Chart (2/3 width) --}}
            <div style="grid-column: span 8;">
                <div class="chart-card" style="border-radius: 12px; padding: 24px; height: 100%;">
                    <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 20px;">
                        <div>
                            <h3 class="card-title" style="margin: 0 0 4px 0; font-size: 16px; font-weight: 600;">Performa Klik 7 Hari</h3>
                            <p class="card-subtitle" style="margin: 0; font-size: 13px;">Tren klik dan pengunjung harian</p>
                        </div>
                        <div class="tab-buttons" style="display: flex; gap: 8px; padding: 4px; border-radius: 8px;">
                            <button class="tab-btn active" style="padding: 6px 12px; font-size: 13px; border: none; background: #3b82f6; color: white; border-radius: 6px; cursor: pointer;">7D</button>
                            <button class="tab-btn" style="padding: 6px 12px; font-size: 13px; border: none; background: transparent; border-radius: 6px; cursor: pointer;">30D</button>
                            <button class="tab-btn" style="padding: 6px 12px; font-size: 13px; border: none; background: transparent; border-radius: 6px; cursor: pointer;">All</button>
                        </div>
                    </div>
                    <canvas id="clicksChart" style="max-height: 300px;"></canvas>
                </div>
            </div>

            {{-- Column 3: Device Stats (1/3 width) --}}
            <div style="grid-column: span 4;">
                <div class="device-card" style="border-radius: 12px; padding: 24px; height: 100%;">
                    <h3 class="card-title" style="margin: 0 0 4px 0; font-size: 16px; font-weight: 600;">Distribusi Perangkat</h3>
                    <p class="card-subtitle" style="margin: 0 0 20px 0; font-size: 13px;">Perangkat yang digunakan pengunjung</p>
                    
                    <div style="position: relative; height: 160px; margin-bottom: 20px;">
                        <canvas id="deviceChart"></canvas>
                    </div>
                    
                    <div style="margin-top: 20px; display: flex; flex-direction: column; gap: 12px;">
                        <div class="device-item" style="display: flex; align-items: center; justify-content: space-between; padding: 10px; border-radius: 8px;">
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <div style="width: 12px; height: 12px; background: #3b82f6; border-radius: 4px;"></div>
                                <div>
                                    <p class="device-name" style="margin: 0; font-size: 13px; font-weight: 600;">Mobile</p>
                                    <p class="device-desc" style="margin: 0; font-size: 11px;">Smartphone</p>
                                </div>
                            </div>
                            <span class="device-percentage" style="font-size: 16px; font-weight: 700;">{{ $deviceStats['mobile'] }}%</span>
                        </div>
                        
                        <div class="device-item" style="display: flex; align-items: center; justify-content: space-between; padding: 10px; border-radius: 8px;">
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <div style="width: 12px; height: 12px; background: #10b981; border-radius: 4px;"></div>
                                <div>
                                    <p class="device-name" style="margin: 0; font-size: 13px; font-weight: 600;">Desktop</p>
                                    <p class="device-desc" style="margin: 0; font-size: 11px;">PC & Laptop</p>
                                </div>
                            </div>
                            <span class="device-percentage" style="font-size: 16px; font-weight: 700;">{{ $deviceStats['desktop'] }}%</span>
                        </div>
                        
                        <div class="device-item" style="display: flex; align-items: center; justify-content: space-between; padding: 10px; border-radius: 8px;">
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <div style="width: 12px; height: 12px; background: #f59e0b; border-radius: 4px;"></div>
                                <div>
                                    <p class="device-name" style="margin: 0; font-size: 13px; font-weight: 600;">Tablet</p>
                                    <p class="device-desc" style="margin: 0; font-size: 11px;">iPad & Tablet</p>
                                </div>
                            </div>
                            <span class="device-percentage" style="font-size: 16px; font-weight: 700;">{{ $deviceStats['tablet'] }}%</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Column 4: Top Links (1/2 width) --}}
            <div style="grid-column: span 6;">
                <div class="links-card" style="border-radius: 12px; padding: 24px; height: 100%;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                        <div>
                            <h3 class="card-title" style="margin: 0 0 4px 0; font-size: 16px; font-weight: 600;">Link Terpopuler</h3>
                            <p class="card-subtitle" style="margin: 0; font-size: 13px;">Top 5 link dengan klik terbanyak</p>
                        </div>
                        <span class="card-badge" style="font-size: 12px; color: #3b82f6; font-weight: 600;">{{ now()->format('M Y') }}</span>
                    </div>

                    <div style="display: flex; flex-direction: column; gap: 12px;">
                        @forelse($topLinks as $index => $link)
                            <div class="link-item" style="padding: 16px; border-radius: 10px; transition: all 0.2s; cursor: pointer;">
                                <div style="display: flex; align-items: center; gap: 12px;">
                                    <div class="rank-badge" style="width: 36px; height: 36px; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                                        <span style="font-size: 14px; font-weight: 700; color: #3b82f6;">#{{ $index + 1 }}</span>
                                    </div>
                                    <div style="flex: 1; min-width: 0;">
                                        <p class="link-title" style="margin: 0 0 4px 0; font-size: 14px; font-weight: 600; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                            {{ $link->title ?? 'Untitled Link' }}
                                        </p>
                                        <div style="display: flex; align-items: center; gap: 8px;">
                                            <span class="link-short" style="font-size: 12px; color: #3b82f6; font-weight: 500;">
                                                payou.id/{{ $link->short_code }}
                                            </span>
                                            <div style="display: flex; align-items: center; gap: 4px;">
                                                <i class="fas fa-calendar" style="font-size: 10px; color: #94a3b8;"></i>
                                                <span class="link-date" style="font-size: 11px;">
                                                    {{ $link->created_at->format('d M') }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div style="text-align: right;">
                                        <p class="link-clicks" style="margin: 0; font-size: 18px; font-weight: 700;">{{ $link->clicks_count }}</p>
                                        <p class="link-clicks-label" style="margin: 0; font-size: 11px;">klik</p>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="empty-state" style="text-align: center; padding: 40px 0;">
                                <div class="empty-icon" style="width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px;">
                                    <i class="fas fa-chart-bar" style="font-size: 20px;"></i>
                                </div>
                                <p class="empty-text" style="margin: 0; font-size: 14px;">Belum ada data klik</p>
                                <p class="empty-subtext" style="margin: 4px 0 0 0; font-size: 12px;">Buat link untuk mulai melacak klik</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- Column 5: Traffic Sources (1/2 width) --}}
            <div style="grid-column: span 6;">
                <div class="traffic-card" style="border-radius: 12px; padding: 24px; height: 100%;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                        <div>
                            <h3 class="card-title" style="margin: 0 0 4px 0; font-size: 16px; font-weight: 600;">Sumber Traffic</h3>
                            <p class="card-subtitle" style="margin: 0; font-size: 13px;">Dari mana pengunjung datang</p>
                        </div>
                        <span class="card-badge" style="font-size: 12px; color: #3b82f6; font-weight: 600;">Total: 100%</span>
                    </div>

                    <div style="display: flex; flex-direction: column; gap: 16px;">
                        @forelse($trafficSources as $source)
                            <div class="traffic-item" style="padding: 12px;">
                                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 8px;">
                                    <div style="display: flex; align-items: center; gap: 10px;">
                                        <div style="width: 32px; height: 32px; background: {{ $source['color'] }}10; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                                            <i class="{{ $source['icon'] }}" style="font-size: 14px; color: {{ $source['color'] }};"></i>
                                        </div>
                                        <div>
                                            <p class="traffic-name" style="margin: 0; font-size: 14px; font-weight: 600;">{{ $source['name'] }}</p>
                                            <p class="traffic-desc" style="margin: 0; font-size: 11px;">{{ $source['description'] }}</p>
                                        </div>
                                    </div>
                                    <div style="text-align: right;">
                                        <p class="traffic-percentage" style="margin: 0; font-size: 16px; font-weight: 700;">{{ $source['percentage'] }}%</p>
                                        <p class="traffic-count" style="margin: 0; font-size: 11px;">{{ $source['count'] }} klik</p>
                                    </div>
                                </div>
                                <div class="progress-bar" style="width: 100%; height: 6px; border-radius: 3px; overflow: hidden;">
                                    <div style="width: {{ $source['percentage'] }}%; height: 100%; background: {{ $source['color'] }}; border-radius: 3px; transition: width 0.3s;"></div>
                                </div>
                            </div>
                        @empty
                            <div class="empty-state" style="text-align: center; padding: 40px 0;">
                                <div class="empty-icon" style="width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px;">
                                    <i class="fas fa-globe" style="font-size: 20px;"></i>
                                </div>
                                <p class="empty-text" style="margin: 0; font-size: 14px;">Belum ada data traffic</p>
                                <p class="empty-subtext" style="margin: 4px 0 0 0; font-size: 12px;">Klik akan muncul di sini</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Check if dark mode is active
    const isDarkMode = document.documentElement.classList.contains('dark');
    
    // Colors based on theme
    const gridColor = isDarkMode ? '#334155' : '#f1f5f9';
    const textColor = isDarkMode ? '#cbd5e1' : '#64748b';
    const tooltipBg = isDarkMode ? '#1e293b' : '#1e293b';
    const tooltipBorder = isDarkMode ? '#475569' : '#334155';
    
    // Clicks Chart
    const clicksCtx = document.getElementById('clicksChart').getContext('2d');
    const clicksChart = new Chart(clicksCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode(collect($clicksPerDay)->pluck('day')->toArray()) !!},
            datasets: [{
                label: 'Klik',
                data: {!! json_encode(collect($clicksPerDay)->pluck('clicks')->toArray()) !!},
                borderColor: '#3b82f6',
                backgroundColor: isDarkMode ? 'rgba(59, 130, 246, 0.15)' : 'rgba(59, 130, 246, 0.1)',
                borderWidth: 2,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: '#3b82f6',
                pointBorderColor: isDarkMode ? '#1e293b' : '#ffffff',
                pointBorderWidth: 2,
                pointRadius: 4
            }, {
                label: 'Pengunjung',
                data: {!! json_encode(collect($clicksPerDay)->pluck('visitors')->toArray()) !!},
                borderColor: '#10b981',
                backgroundColor: isDarkMode ? 'rgba(16, 185, 129, 0.15)' : 'rgba(16, 185, 129, 0.1)',
                borderWidth: 2,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: '#10b981',
                pointBorderColor: isDarkMode ? '#1e293b' : '#ffffff',
                pointBorderWidth: 2,
                pointRadius: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { 
                legend: { 
                    display: false 
                },
                tooltip: {
                    backgroundColor: tooltipBg,
                    titleColor: '#ffffff',
                    bodyColor: '#e2e8f0',
                    borderColor: tooltipBorder,
                    borderWidth: 1,
                    cornerRadius: 8
                }
            },
            scales: {
                y: { 
                    beginAtZero: true, 
                    grid: { 
                        color: gridColor,
                        drawBorder: false
                    },
                    ticks: {
                        color: textColor,
                        font: {
                            size: 11
                        }
                    }
                },
                x: { 
                    grid: { 
                        display: false 
                    },
                    ticks: {
                        color: textColor,
                        font: {
                            size: 11
                        }
                    }
                }
            }
        }
    });

    // Device Chart
    const deviceCtx = document.getElementById('deviceChart').getContext('2d');
    const deviceChart = new Chart(deviceCtx, {
        type: 'doughnut',
        data: {
            labels: ['Mobile', 'Desktop', 'Tablet'],
            datasets: [{
                data: [{{ $deviceStats['mobile'] }}, {{ $deviceStats['desktop'] }}, {{ $deviceStats['tablet'] }}],
                backgroundColor: ['#3b82f6', '#10b981', '#f59e0b'],
                borderWidth: 0,
                hoverOffset: 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { 
                legend: { 
                    display: false 
                },
                tooltip: {
                    backgroundColor: tooltipBg,
                    titleColor: '#ffffff',
                    bodyColor: '#e2e8f0',
                    borderColor: tooltipBorder,
                    borderWidth: 1,
                    cornerRadius: 8
                }
            },
            cutout: '75%'
        }
    });
    
    // Update charts when theme changes
    if (window.darkModeManager) {
        const originalSetTheme = window.darkModeManager.setTheme;
        window.darkModeManager.setTheme = function(theme) {
            originalSetTheme.call(this, theme);
            
            // Update chart colors
            const newIsDark = theme === 'dark';
            const newGridColor = newIsDark ? '#334155' : '#f1f5f9';
            const newTextColor = newIsDark ? '#cbd5e1' : '#64748b';
            const newTooltipBg = newIsDark ? '#1e293b' : '#1e293b';
            const newTooltipBorder = newIsDark ? '#475569' : '#334155';
            
            // Update clicks chart
            clicksChart.data.datasets[0].backgroundColor = newIsDark ? 'rgba(59, 130, 246, 0.15)' : 'rgba(59, 130, 246, 0.1)';
            clicksChart.data.datasets[0].pointBorderColor = newIsDark ? '#1e293b' : '#ffffff';
            clicksChart.data.datasets[1].backgroundColor = newIsDark ? 'rgba(16, 185, 129, 0.15)' : 'rgba(16, 185, 129, 0.1)';
            clicksChart.data.datasets[1].pointBorderColor = newIsDark ? '#1e293b' : '#ffffff';
            clicksChart.options.scales.y.grid.color = newGridColor;
            clicksChart.options.scales.y.ticks.color = newTextColor;
            clicksChart.options.scales.x.ticks.color = newTextColor;
            clicksChart.options.plugins.tooltip.backgroundColor = newTooltipBg;
            clicksChart.options.plugins.tooltip.borderColor = newTooltipBorder;
            clicksChart.update();
            
            // Update device chart
            deviceChart.options.plugins.tooltip.backgroundColor = newTooltipBg;
            deviceChart.options.plugins.tooltip.borderColor = newTooltipBorder;
            deviceChart.update();
        };
    }
});
</script>

<style>
/* Additional styles for better dark mode support */
.analitik-page .stat-card,
.analitik-page .chart-card,
.analitik-page .device-card,
.analitik-page .links-card,
.analitik-page .traffic-card {
    background: var(--bg-primary);
    border: 1px solid var(--border-color);
    box-shadow: 0 1px 3px rgba(0,0,0,0.05);
}

.dark .analitik-page .stat-card,
.dark .analitik-page .chart-card,
.dark .analitik-page .device-card,
.dark .analitik-page .links-card,
.dark .analitik-page .traffic-card {
    box-shadow: 0 1px 3px rgba(0,0,0,0.3);
}

.analitik-page .page-title,
.analitik-page .stat-value,
.analitik-page .card-title,
.analitik-page .device-name,
.analitik-page .link-title,
.analitik-page .link-clicks,
.analitik-page .traffic-name,
.analitik-page .traffic-percentage {
    color: var(--text-primary);
}

.analitik-page .page-subtitle,
.analitik-page .stat-label,
.analitik-page .card-subtitle,
.analitik-page .stat-desc {
    color: var(--text-tertiary);
}

.analitik-page .device-desc,
.analitik-page .link-date,
.analitik-page .link-clicks-label,
.analitik-page .traffic-desc,
.analitik-page .traffic-count {
    color: var(--text-tertiary);
}

.analitik-page .device-item,
.analitik-page .link-item {
    background: var(--bg-secondary);
}

.analitik-page .link-item:hover {
    background: var(--hover-bg);
    border-color: var(--accent-color);
}

.analitik-page .rank-badge {
    background: var(--bg-secondary);
    border: 1px solid var(--border-color);
}

.analitik-page .back-button {
    background: var(--bg-primary);
    border: 1px solid var(--border-color);
}

.analitik-page .back-button i {
    color: var(--text-secondary);
}

.analitik-page .tab-buttons {
    background: var(--bg-tertiary);
}

.analitik-page .tab-btn {
    color: var(--text-secondary);
}

.analitik-page .tab-btn.active {
    background: var(--accent-color) !important;
    color: white !important;
}

.analitik-page .empty-icon {
    background: var(--bg-tertiary);
}

.analitik-page .empty-icon i {
    color: var(--text-tertiary);
}

.analitik-page .empty-text {
    color: var(--text-secondary);
}

.analitik-page .empty-subtext {
    color: var(--text-tertiary);
}

.analitik-page .progress-bar {
    background: var(--bg-tertiary);
}
</style>

@endsection