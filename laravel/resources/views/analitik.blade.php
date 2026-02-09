@extends('layouts.dashboard')
@section('title', 'Analitik')

@section('content')
<div style="min-height: 100vh; background: #f8fafc; padding: 24px;">
    <div style="max-width: 1400px; margin: 0 auto;">
        
        {{-- Header --}}
        <div style="margin-bottom: 24px;">
            <div style="display: flex; align-items: center; gap: 12px;">
                <a href="{{ route('dashboard') }}" style="width: 36px; height: 36px; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 8px; display: flex; align-items: center; justify-content: center; text-decoration: none;">
                    <i class="fas fa-arrow-left" style="font-size: 14px; color: #475569;"></i>
                </a>
                <div>
                    <h1 style="margin: 0; font-size: 24px; font-weight: 600; color: #0f172a;">Analitik</h1>
                    <p style="margin: 0; font-size: 14px; color: #94a3b8;">Pantau performa link dan aktivitas pengunjung</p>
                </div>
            </div>
        </div>

        {{-- Layout Grid --}}
        <div style="display: grid; grid-template-columns: repeat(12, 1fr); gap: 20px;">
            
            {{-- Column 1: Key Metrics (Full Width) --}}
            <div style="grid-column: span 12;">
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 16px;">
                    
                    {{-- Total Klik --}}
                    <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                        <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 12px;">
                            <div style="width: 48px; height: 48px; background: #eff6ff; border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-mouse-pointer" style="font-size: 18px; color: #3b82f6;"></i>
                            </div>
                            <div>
                                <h3 style="margin: 0; font-size: 28px; font-weight: 700; color: #0f172a;">{{ number_format($totalClicks) }}</h3>
                                <p style="margin: 0; font-size: 13px; color: #94a3b8;">Total Klik</p>
                            </div>
                        </div>
                        <div style="display: flex; align-items: center; gap: 6px;">
                            <i class="fas fa-arrow-up" style="font-size: 12px; color: #10b981;"></i>
                            <span style="font-size: 12px; color: #64748b;">Lifetime</span>
                        </div>
                    </div>

                    {{-- Total Link --}}
                    <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                        <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 12px;">
                            <div style="width: 48px; height: 48px; background: #f0fdf4; border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-link" style="font-size: 18px; color: #10b981;"></i>
                            </div>
                            <div>
                                <h3 style="margin: 0; font-size: 28px; font-weight: 700; color: #0f172a;">{{ number_format($totalLinks) }}</h3>
                                <p style="margin: 0; font-size: 13px; color: #94a3b8;">Total Link</p>
                            </div>
                        </div>
                        <div style="display: flex; align-items: center; gap: 6px;">
                            <i class="fas fa-check-circle" style="font-size: 12px; color: #10b981;"></i>
                            <span style="font-size: 12px; color: #64748b;">Aktif & Nonaktif</span>
                        </div>
                    </div>

                    {{-- Pengunjung Unik --}}
                    <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                        <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 12px;">
                            <div style="width: 48px; height: 48px; background: #fce7f3; border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-users" style="font-size: 18px; color: #ec4899;"></i>
                            </div>
                            <div>
                                <h3 style="margin: 0; font-size: 28px; font-weight: 700; color: #0f172a;">{{ number_format($uniqueVisitors) }}</h3>
                                <p style="margin: 0; font-size: 13px; color: #94a3b8;">Pengunjung Unik</p>
                            </div>
                        </div>
                        <div style="display: flex; align-items: center; gap: 6px;">
                            <i class="fas fa-user-check" style="font-size: 12px; color: #3b82f6;"></i>
                            <span style="font-size: 12px; color: #64748b;">Berdasarkan IP</span>
                        </div>
                    </div>

                    {{-- CTR --}}
                    <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                        <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 12px;">
                            <div style="width: 48px; height: 48px; background: #fef3c7; border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-chart-line" style="font-size: 18px; color: #f59e0b;"></i>
                            </div>
                            <div>
                                <h3 style="margin: 0; font-size: 28px; font-weight: 700; color: #0f172a;">{{ number_format($ctr, 1) }}%</h3>
                                <p style="margin: 0; font-size: 13px; color: #94a3b8;">Click Rate</p>
                            </div>
                        </div>
                        <div style="display: flex; align-items: center; gap: 6px;">
                            <i class="fas fa-percentage" style="font-size: 12px; color: #f59e0b;"></i>
                            <span style="font-size: 12px; color: #64748b;">Rata-rata klik per sesi</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Column 2: Performance Chart (2/3 width) --}}
            <div style="grid-column: span 8;">
                <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 24px; height: 100%; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                    <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 20px;">
                        <div>
                            <h3 style="margin: 0 0 4px 0; font-size: 16px; font-weight: 600; color: #0f172a;">Performa Klik 7 Hari</h3>
                            <p style="margin: 0; font-size: 13px; color: #94a3b8;">Tren klik dan pengunjung harian</p>
                        </div>
                        <div style="display: flex; gap: 8px; background: #f8fafc; padding: 4px; border-radius: 8px;">
                            <button style="padding: 6px 12px; font-size: 13px; border: none; background: #3b82f6; color: white; border-radius: 6px; cursor: pointer;">7D</button>
                            <button style="padding: 6px 12px; font-size: 13px; border: none; background: transparent; color: #64748b; border-radius: 6px; cursor: pointer;">30D</button>
                            <button style="padding: 6px 12px; font-size: 13px; border: none; background: transparent; color: #64748b; border-radius: 6px; cursor: pointer;">All</button>
                        </div>
                    </div>
                    <canvas id="clicksChart" style="max-height: 300px;"></canvas>
                </div>
            </div>

            {{-- Column 3: Device Stats (1/3 width) --}}
            <div style="grid-column: span 4;">
                <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 24px; height: 100%; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                    <h3 style="margin: 0 0 4px 0; font-size: 16px; font-weight: 600; color: #0f172a;">Distribusi Perangkat</h3>
                    <p style="margin: 0 0 20px 0; font-size: 13px; color: #94a3b8;">Perangkat yang digunakan pengunjung</p>
                    
                    <div style="position: relative; height: 160px; margin-bottom: 20px;">
                        <canvas id="deviceChart"></canvas>
                    </div>
                    
                    <div style="margin-top: 20px; display: flex; flex-direction: column; gap: 12px;">
                        <div style="display: flex; align-items: center; justify-content: space-between; padding: 10px; background: #f8fafc; border-radius: 8px;">
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <div style="width: 12px; height: 12px; background: #3b82f6; border-radius: 4px;"></div>
                                <div>
                                    <p style="margin: 0; font-size: 13px; font-weight: 600; color: #0f172a;">Mobile</p>
                                    <p style="margin: 0; font-size: 11px; color: #94a3b8;">Smartphone</p>
                                </div>
                            </div>
                            <span style="font-size: 16px; font-weight: 700; color: #0f172a;">{{ $deviceStats['mobile'] }}%</span>
                        </div>
                        
                        <div style="display: flex; align-items: center; justify-content: space-between; padding: 10px; background: #f8fafc; border-radius: 8px;">
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <div style="width: 12px; height: 12px; background: #10b981; border-radius: 4px;"></div>
                                <div>
                                    <p style="margin: 0; font-size: 13px; font-weight: 600; color: #0f172a;">Desktop</p>
                                    <p style="margin: 0; font-size: 11px; color: #94a3b8;">PC & Laptop</p>
                                </div>
                            </div>
                            <span style="font-size: 16px; font-weight: 700; color: #0f172a;">{{ $deviceStats['desktop'] }}%</span>
                        </div>
                        
                        <div style="display: flex; align-items: center; justify-content: space-between; padding: 10px; background: #f8fafc; border-radius: 8px;">
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <div style="width: 12px; height: 12px; background: #f59e0b; border-radius: 4px;"></div>
                                <div>
                                    <p style="margin: 0; font-size: 13px; font-weight: 600; color: #0f172a;">Tablet</p>
                                    <p style="margin: 0; font-size: 11px; color: #94a3b8;">iPad & Tablet</p>
                                </div>
                            </div>
                            <span style="font-size: 16px; font-weight: 700; color: #0f172a;">{{ $deviceStats['tablet'] }}%</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Column 4: Top Links (1/2 width) --}}
            <div style="grid-column: span 6;">
                <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 24px; height: 100%; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                        <div>
                            <h3 style="margin: 0 0 4px 0; font-size: 16px; font-weight: 600; color: #0f172a;">Link Terpopuler</h3>
                            <p style="margin: 0; font-size: 13px; color: #94a3b8;">Top 5 link dengan klik terbanyak</p>
                        </div>
                        <span style="font-size: 12px; color: #3b82f6; font-weight: 600;">{{ now()->format('M Y') }}</span>
                    </div>

                    <div style="display: flex; flex-direction: column; gap: 12px;">
                        @forelse($topLinks as $index => $link)
                            <div style="padding: 16px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; transition: all 0.2s; cursor: pointer;"
                                 onmouseover="this.style.borderColor='#3b82f6'; this.style.boxShadow='0 2px 8px rgba(59,130,246,0.1)'"
                                 onmouseout="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'">
                                <div style="display: flex; align-items: center; gap: 12px;">
                                    <div style="width: 36px; height: 36px; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                                        <span style="font-size: 14px; font-weight: 700; color: #3b82f6;">#{{ $index + 1 }}</span>
                                    </div>
                                    <div style="flex: 1; min-width: 0;">
                                        <p style="margin: 0 0 4px 0; font-size: 14px; font-weight: 600; color: #0f172a; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                            {{ $link->title ?? 'Untitled Link' }}
                                        </p>
                                        <div style="display: flex; align-items: center; gap: 8px;">
                                            <span style="font-size: 12px; color: #3b82f6; font-weight: 500;">
                                                payou.id/{{ $link->short_code }}
                                            </span>
                                            <div style="display: flex; align-items: center; gap: 4px;">
                                                <i class="fas fa-calendar" style="font-size: 10px; color: #94a3b8;"></i>
                                                <span style="font-size: 11px; color: #94a3b8;">
                                                    {{ $link->created_at->format('d M') }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div style="text-align: right;">
                                        <p style="margin: 0; font-size: 18px; font-weight: 700; color: #0f172a;">{{ $link->clicks_count }}</p>
                                        <p style="margin: 0; font-size: 11px; color: #94a3b8;">klik</p>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div style="text-align: center; padding: 40px 0;">
                                <div style="width: 48px; height: 48px; background: #f1f5f9; border-radius: 12px; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px;">
                                    <i class="fas fa-chart-bar" style="font-size: 20px; color: #94a3b8;"></i>
                                </div>
                                <p style="margin: 0; font-size: 14px; color: #64748b;">Belum ada data klik</p>
                                <p style="margin: 4px 0 0 0; font-size: 12px; color: #94a3b8;">Buat link untuk mulai melacak klik</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- Column 5: Traffic Sources (1/2 width) --}}
            <div style="grid-column: span 6;">
                <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 24px; height: 100%; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                        <div>
                            <h3 style="margin: 0 0 4px 0; font-size: 16px; font-weight: 600; color: #0f172a;">Sumber Traffic</h3>
                            <p style="margin: 0; font-size: 13px; color: #94a3b8;">Dari mana pengunjung datang</p>
                        </div>
                        <span style="font-size: 12px; color: #3b82f6; font-weight: 600;">Total: 100%</span>
                    </div>

                    <div style="display: flex; flex-direction: column; gap: 16px;">
                        @forelse($trafficSources as $source)
                            <div style="padding: 12px;">
                                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 8px;">
                                    <div style="display: flex; align-items: center; gap: 10px;">
                                        <div style="width: 32px; height: 32px; background: {{ $source['color'] }}10; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                                            <i class="{{ $source['icon'] }}" style="font-size: 14px; color: {{ $source['color'] }};"></i>
                                        </div>
                                        <div>
                                            <p style="margin: 0; font-size: 14px; font-weight: 600; color: #0f172a;">{{ $source['name'] }}</p>
                                            <p style="margin: 0; font-size: 11px; color: #94a3b8;">{{ $source['description'] }}</p>
                                        </div>
                                    </div>
                                    <div style="text-align: right;">
                                        <p style="margin: 0; font-size: 16px; font-weight: 700; color: #0f172a;">{{ $source['percentage'] }}%</p>
                                        <p style="margin: 0; font-size: 11px; color: #94a3b8;">{{ $source['count'] }} klik</p>
                                    </div>
                                </div>
                                <div style="width: 100%; height: 6px; background: #f1f5f9; border-radius: 3px; overflow: hidden;">
                                    <div style="width: {{ $source['percentage'] }}%; height: 100%; background: {{ $source['color'] }}; border-radius: 3px; transition: width 0.3s;"></div>
                                </div>
                            </div>
                        @empty
                            <div style="text-align: center; padding: 40px 0;">
                                <div style="width: 48px; height: 48px; background: #f1f5f9; border-radius: 12px; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px;">
                                    <i class="fas fa-globe" style="font-size: 20px; color: #94a3b8;"></i>
                                </div>
                                <p style="margin: 0; font-size: 14px; color: #64748b;">Belum ada data traffic</p>
                                <p style="margin: 4px 0 0 0; font-size: 12px; color: #94a3b8;">Klik akan muncul di sini</p>
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
    // Clicks Chart
    const clicksCtx = document.getElementById('clicksChart').getContext('2d');
    new Chart(clicksCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode(collect($clicksPerDay)->pluck('day')->toArray()) !!},
            datasets: [{
                label: 'Klik',
                data: {!! json_encode(collect($clicksPerDay)->pluck('clicks')->toArray()) !!},
                borderColor: '#3b82f6',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                borderWidth: 2,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: '#3b82f6',
                pointBorderColor: '#ffffff',
                pointBorderWidth: 2,
                pointRadius: 4
            }, {
                label: 'Pengunjung',
                data: {!! json_encode(collect($clicksPerDay)->pluck('visitors')->toArray()) !!},
                borderColor: '#10b981',
                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                borderWidth: 2,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: '#10b981',
                pointBorderColor: '#ffffff',
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
                    backgroundColor: '#1e293b',
                    titleColor: '#ffffff',
                    bodyColor: '#e2e8f0',
                    borderColor: '#334155',
                    borderWidth: 1,
                    cornerRadius: 8
                }
            },
            scales: {
                y: { 
                    beginAtZero: true, 
                    grid: { 
                        color: '#f1f5f9',
                        drawBorder: false
                    },
                    ticks: {
                        color: '#64748b',
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
                        color: '#64748b',
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
    new Chart(deviceCtx, {
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
                    backgroundColor: '#1e293b',
                    titleColor: '#ffffff',
                    bodyColor: '#e2e8f0',
                    borderColor: '#334155',
                    borderWidth: 1,
                    cornerRadius: 8
                }
            },
            cutout: '75%'
        }
    });
});
</script>

@endsection