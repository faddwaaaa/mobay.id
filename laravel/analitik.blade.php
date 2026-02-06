@extends('layouts.dashboard')
@section('title', 'Analitik')

@section('content')
<div style="min-height: 100vh; background: #f8fafc; padding: 24px;">
    <div style="max-width: 1400px; margin: 0 auto;">
        
        {{-- Header Section --}}
        <div style="margin-bottom: 24px;">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 8px;">
                <div style="display: flex; align-items: center; gap: 12px;">
                    <a href="{{ route('dashboard') }}" style="width: 36px; height: 36px; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 8px; display: flex; align-items: center; justify-content: center; text-decoration: none; transition: all 0.2s;">
                        <i class="fas fa-arrow-left" style="font-size: 14px; color: #475569;"></i>
                    </a>
                    <div>
                        <h1 style="margin: 0; font-size: 24px; font-weight: 600; color: #0f172a;">Analitik</h1>
                        <p style="margin: 0; font-size: 14px; color: #94a3b8;">Pantau performa link dan aktivitas pengunjung Anda</p>
                    </div>
                </div>
                
                {{-- Date Range Selector --}}
                <div style="display: flex; gap: 8px;">
                    <button onclick="setDateRange('7')" class="date-range-btn active" data-range="7" style="padding: 10px 16px; background: #3b82f6; color: white; border: 1px solid #3b82f6; border-radius: 8px; font-size: 13px; font-weight: 500; cursor: pointer; transition: all 0.2s;">
                        7 Hari
                    </button>
                    <button onclick="setDateRange('30')" class="date-range-btn" data-range="30" style="padding: 10px 16px; background: #ffffff; color: #475569; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 13px; font-weight: 500; cursor: pointer; transition: all 0.2s;">
                        30 Hari
                    </button>
                    <button onclick="setDateRange('90')" class="date-range-btn" data-range="90" style="padding: 10px 16px; background: #ffffff; color: #475569; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 13px; font-weight: 500; cursor: pointer; transition: all 0.2s;">
                        90 Hari
                    </button>
                </div>
            </div>
        </div>

        {{-- Key Metrics Cards --}}
        <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; margin-bottom: 24px;">
            
            {{-- Total Klik --}}
            <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px;">
                    <div style="width: 40px; height: 40px; background: #eff6ff; border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-mouse-pointer" style="font-size: 16px; color: #3b82f6;"></i>
                    </div>
                    <span style="font-size: 12px; color: #10b981; font-weight: 600; display: flex; align-items: center; gap: 4px;">
                        <i class="fas fa-arrow-up" style="font-size: 10px;"></i> +12.5%
                    </span>
                </div>
                <h3 style="margin: 0 0 4px 0; font-size: 28px; font-weight: 700; color: #0f172a;">{{ $totalClicks ?? 1248 }}</h3>
                <p style="margin: 0; font-size: 13px; color: #94a3b8; font-weight: 500;">Total Klik</p>
            </div>

            {{-- Link Aktif --}}
            <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px;">
                    <div style="width: 40px; height: 40px; background: #f0fdf4; border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-link" style="font-size: 16px; color: #10b981;"></i>
                    </div>
                    <span style="font-size: 12px; color: #3b82f6; font-weight: 600; display: flex; align-items: center; gap: 4px;">
                        <i class="fas fa-arrow-up" style="font-size: 10px;"></i> +3
                    </span>
                </div>
                <h3 style="margin: 0 0 4px 0; font-size: 28px; font-weight: 700; color: #0f172a;">{{ $activeLinks ?? 15 }}</h3>
                <p style="margin: 0; font-size: 13px; color: #94a3b8; font-weight: 500;">Link Aktif</p>
            </div>

            {{-- CTR (Click Through Rate) --}}
            <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px;">
                    <div style="width: 40px; height: 40px; background: #fef3c7; border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-chart-line" style="font-size: 16px; color: #f59e0b;"></i>
                    </div>
                    <span style="font-size: 12px; color: #10b981; font-weight: 600; display: flex; align-items: center; gap: 4px;">
                        <i class="fas fa-arrow-up" style="font-size: 10px;"></i> +2.1%
                    </span>
                </div>
                <h3 style="margin: 0 0 4px 0; font-size: 28px; font-weight: 700; color: #0f172a;">{{ $ctr ?? 8.4 }}%</h3>
                <p style="margin: 0; font-size: 13px; color: #94a3b8; font-weight: 500;">Click Rate</p>
            </div>

            {{-- Pengunjung Unik --}}
            <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px;">
                    <div style="width: 40px; height: 40px; background: #fce7f3; border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-users" style="font-size: 16px; color: #ec4899;"></i>
                    </div>
                    <span style="font-size: 12px; color: #10b981; font-weight: 600; display: flex; align-items: center; gap: 4px;">
                        <i class="fas fa-arrow-up" style="font-size: 10px;"></i> +8.7%
                    </span>
                </div>
                <h3 style="margin: 0 0 4px 0; font-size: 28px; font-weight: 700; color: #0f172a;">{{ $uniqueVisitors ?? 892 }}</h3>
                <p style="margin: 0; font-size: 13px; color: #94a3b8; font-weight: 500;">Pengunjung Unik</p>
            </div>
        </div>

        {{-- Charts Section --}}
        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 20px; margin-bottom: 24px;">
            
            {{-- Main Chart - Clicks Over Time --}}
            <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px;">
                    <div>
                        <h3 style="margin: 0 0 4px 0; font-size: 16px; font-weight: 600; color: #0f172a;">Performa Klik</h3>
                        <p style="margin: 0; font-size: 13px; color: #94a3b8;">Tren klik dalam 7 hari terakhir</p>
                    </div>
                    <div style="display: flex; gap: 16px;">
                        <div style="display: flex; align-items: center; gap: 6px;">
                            <div style="width: 12px; height: 12px; background: #3b82f6; border-radius: 3px;"></div>
                            <span style="font-size: 12px; color: #64748b;">Klik</span>
                        </div>
                        <div style="display: flex; align-items: center; gap: 6px;">
                            <div style="width: 12px; height: 12px; background: #10b981; border-radius: 3px;"></div>
                            <span style="font-size: 12px; color: #64748b;">Pengunjung</span>
                        </div>
                    </div>
                </div>
                <canvas id="clicksChart" style="max-height: 300px;"></canvas>
            </div>

            {{-- Device Distribution --}}
            <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                <h3 style="margin: 0 0 4px 0; font-size: 16px; font-weight: 600; color: #0f172a;">Perangkat</h3>
                <p style="margin: 0 0 20px 0; font-size: 13px; color: #94a3b8;">Distribusi perangkat pengunjung</p>
                <canvas id="deviceChart" style="max-height: 240px;"></canvas>
                
                {{-- Device Stats --}}
                <div style="margin-top: 20px; display: flex; flex-direction: column; gap: 12px;">
                    <div style="display: flex; align-items: center; justify-content: space-between;">
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <div style="width: 8px; height: 8px; background: #3b82f6; border-radius: 50%;"></div>
                            <span style="font-size: 13px; color: #475569;">Mobile</span>
                        </div>
                        <span style="font-size: 13px; font-weight: 600; color: #0f172a;">68%</span>
                    </div>
                    <div style="display: flex; align-items: center; justify-content: space-between;">
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <div style="width: 8px; height: 8px; background: #10b981; border-radius: 50%;"></div>
                            <span style="font-size: 13px; color: #475569;">Desktop</span>
                        </div>
                        <span style="font-size: 13px; font-weight: 600; color: #0f172a;">27%</span>
                    </div>
                    <div style="display: flex; align-items: center; justify-content: space-between;">
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <div style="width: 8px; height: 8px; background: #f59e0b; border-radius: 50%;"></div>
                            <span style="font-size: 13px; color: #475569;">Tablet</span>
                        </div>
                        <span style="font-size: 13px; font-weight: 600; color: #0f172a;">5%</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Top Links and Geographic Data --}}
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 24px;">
            
            {{-- Top Performing Links --}}
            <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px;">
                    <div>
                        <h3 style="margin: 0 0 4px 0; font-size: 16px; font-weight: 600; color: #0f172a;">Link Terpopuler</h3>
                        <p style="margin: 0; font-size: 13px; color: #94a3b8;">Link dengan klik terbanyak</p>
                    </div>
                    <a href="{{ route('links.index') }}" style="font-size: 13px; color: #3b82f6; text-decoration: none; font-weight: 500;">
                        Lihat Semua <i class="fas fa-arrow-right" style="font-size: 11px; margin-left: 4px;"></i>
                    </a>
                </div>

                <div style="display: flex; flex-direction: column; gap: 12px;">
                    {{-- Link Item 1 --}}
                    <div style="padding: 16px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; transition: all 0.2s; cursor: pointer;" onmouseover="this.style.borderColor='#cbd5e1'" onmouseout="this.style.borderColor='#e2e8f0'">
                        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 8px;">
                            <div style="display: flex; align-items: center; gap: 10px; flex: 1; min-width: 0;">
                                <div style="width: 36px; height: 36px; background: #eff6ff; border-radius: 8px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                    <i class="fas fa-link" style="font-size: 14px; color: #3b82f6;"></i>
                                </div>
                                <div style="flex: 1; min-width: 0;">
                                    <p style="margin: 0 0 2px 0; font-size: 14px; font-weight: 600; color: #0f172a; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">Promo Spesial Ramadan</p>
                                    <p style="margin: 0; font-size: 12px; color: #94a3b8; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">payou.id/promo-ramadan</p>
                                </div>
                            </div>
                            <div style="text-align: right; flex-shrink: 0; margin-left: 12px;">
                                <p style="margin: 0 0 2px 0; font-size: 18px; font-weight: 700; color: #0f172a;">342</p>
                                <p style="margin: 0; font-size: 11px; color: #10b981; font-weight: 600;">+15.2%</p>
                            </div>
                        </div>
                        <div style="width: 100%; height: 6px; background: #e2e8f0; border-radius: 3px; overflow: hidden;">
                            <div style="width: 85%; height: 100%; background: linear-gradient(90deg, #3b82f6, #2563eb); border-radius: 3px;"></div>
                        </div>
                    </div>

                    {{-- Link Item 2 --}}
                    <div style="padding: 16px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; transition: all 0.2s; cursor: pointer;" onmouseover="this.style.borderColor='#cbd5e1'" onmouseout="this.style.borderColor='#e2e8f0'">
                        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 8px;">
                            <div style="display: flex; align-items: center; gap: 10px; flex: 1; min-width: 0;">
                                <div style="width: 36px; height: 36px; background: #f0fdf4; border-radius: 8px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                    <i class="fas fa-shopping-cart" style="font-size: 14px; color: #10b981;"></i>
                                </div>
                                <div style="flex: 1; min-width: 0;">
                                    <p style="margin: 0 0 2px 0; font-size: 14px; font-weight: 600; color: #0f172a; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">Katalog Produk Terbaru</p>
                                    <p style="margin: 0; font-size: 12px; color: #94a3b8; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">payou.id/katalog-2024</p>
                                </div>
                            </div>
                            <div style="text-align: right; flex-shrink: 0; margin-left: 12px;">
                                <p style="margin: 0 0 2px 0; font-size: 18px; font-weight: 700; color: #0f172a;">287</p>
                                <p style="margin: 0; font-size: 11px; color: #10b981; font-weight: 600;">+8.4%</p>
                            </div>
                        </div>
                        <div style="width: 100%; height: 6px; background: #e2e8f0; border-radius: 3px; overflow: hidden;">
                            <div style="width: 71%; height: 100%; background: linear-gradient(90deg, #10b981, #059669); border-radius: 3px;"></div>
                        </div>
                    </div>

                    {{-- Link Item 3 --}}
                    <div style="padding: 16px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; transition: all 0.2s; cursor: pointer;" onmouseover="this.style.borderColor='#cbd5e1'" onmouseout="this.style.borderColor='#e2e8f0'">
                        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 8px;">
                            <div style="display: flex; align-items: center; gap: 10px; flex: 1; min-width: 0;">
                                <div style="width: 36px; height: 36px; background: #fef3c7; border-radius: 8px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                    <i class="fas fa-gift" style="font-size: 14px; color: #f59e0b;"></i>
                                </div>
                                <div style="flex: 1; min-width: 0;">
                                    <p style="margin: 0 0 2px 0; font-size: 14px; font-weight: 600; color: #0f172a; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">Giveaway Instagram</p>
                                    <p style="margin: 0; font-size: 12px; color: #94a3b8; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">payou.id/giveaway-ig</p>
                                </div>
                            </div>
                            <div style="text-align: right; flex-shrink: 0; margin-left: 12px;">
                                <p style="margin: 0 0 2px 0; font-size: 18px; font-weight: 700; color: #0f172a;">213</p>
                                <p style="margin: 0; font-size: 11px; color: #ef4444; font-weight: 600;">-2.1%</p>
                            </div>
                        </div>
                        <div style="width: 100%; height: 6px; background: #e2e8f0; border-radius: 3px; overflow: hidden;">
                            <div style="width: 53%; height: 100%; background: linear-gradient(90deg, #f59e0b, #d97706); border-radius: 3px;"></div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Geographic Data & Traffic Sources --}}
            <div style="display: flex; flex-direction: column; gap: 20px;">
                
                {{-- Top Cities --}}
                <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                    <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 16px;">
                        <div style="width: 36px; height: 36px; background: #f1f5f9; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-map-marker-alt" style="font-size: 14px; color: #475569;"></i>
                        </div>
                        <h3 style="margin: 0; font-size: 16px; font-weight: 600; color: #0f172a;">Kota Teratas</h3>
                    </div>

                    <div style="display: flex; flex-direction: column; gap: 12px;">
                        <div style="display: flex; align-items: center; justify-content: space-between; padding: 12px; background: #f8fafc; border-radius: 8px;">
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <span style="font-size: 18px;">🇮🇩</span>
                                <div>
                                    <p style="margin: 0; font-size: 14px; font-weight: 600; color: #0f172a;">Jakarta</p>
                                    <p style="margin: 0; font-size: 12px; color: #94a3b8;">Indonesia</p>
                                </div>
                            </div>
                            <span style="font-size: 15px; font-weight: 700; color: #3b82f6;">42%</span>
                        </div>

                        <div style="display: flex; align-items: center; justify-content: space-between; padding: 12px; background: #f8fafc; border-radius: 8px;">
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <span style="font-size: 18px;">🇮🇩</span>
                                <div>
                                    <p style="margin: 0; font-size: 14px; font-weight: 600; color: #0f172a;">Surabaya</p>
                                    <p style="margin: 0; font-size: 12px; color: #94a3b8;">Indonesia</p>
                                </div>
                            </div>
                            <span style="font-size: 15px; font-weight: 700; color: #10b981;">28%</span>
                        </div>

                        <div style="display: flex; align-items: center; justify-content: space-between; padding: 12px; background: #f8fafc; border-radius: 8px;">
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <span style="font-size: 18px;">🇮🇩</span>
                                <div>
                                    <p style="margin: 0; font-size: 14px; font-weight: 600; color: #0f172a;">Bandung</p>
                                    <p style="margin: 0; font-size: 12px; color: #94a3b8;">Indonesia</p>
                                </div>
                            </div>
                            <span style="font-size: 15px; font-weight: 700; color: #f59e0b;">18%</span>
                        </div>
                    </div>
                </div>

                {{-- Traffic Sources --}}
                <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                    <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 16px;">
                        <div style="width: 36px; height: 36px; background: #f1f5f9; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-share-alt" style="font-size: 14px; color: #475569;"></i>
                        </div>
                        <h3 style="margin: 0; font-size: 16px; font-weight: 600; color: #0f172a;">Sumber Traffic</h3>
                    </div>

                    <div style="display: flex; flex-direction: column; gap: 12px;">
                        <div style="display: flex; align-items: center; justify-content: space-between;">
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <div style="width: 32px; height: 32px; background: #eff6ff; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                                    <i class="fab fa-instagram" style="font-size: 14px; color: #3b82f6;"></i>
                                </div>
                                <span style="font-size: 14px; color: #475569; font-weight: 500;">Instagram</span>
                            </div>
                            <span style="font-size: 14px; font-weight: 700; color: #0f172a;">45%</span>
                        </div>
                        <div style="width: 100%; height: 6px; background: #e2e8f0; border-radius: 3px; overflow: hidden; margin-top: -4px;">
                            <div style="width: 45%; height: 100%; background: #3b82f6; border-radius: 3px;"></div>
                        </div>

                        <div style="display: flex; align-items: center; justify-content: space-between;">
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <div style="width: 32px; height: 32px; background: #dcfce7; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                                    <i class="fab fa-whatsapp" style="font-size: 14px; color: #10b981;"></i>
                                </div>
                                <span style="font-size: 14px; color: #475569; font-weight: 500;">WhatsApp</span>
                            </div>
                            <span style="font-size: 14px; font-weight: 700; color: #0f172a;">32%</span>
                        </div>
                        <div style="width: 100%; height: 6px; background: #e2e8f0; border-radius: 3px; overflow: hidden; margin-top: -4px;">
                            <div style="width: 32%; height: 100%; background: #10b981; border-radius: 3px;"></div>
                        </div>

                        <div style="display: flex; align-items: center; justify-content: space-between;">
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <div style="width: 32px; height: 32px; background: #dbeafe; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                                    <i class="fab fa-facebook" style="font-size: 14px; color: #2563eb;"></i>
                                </div>
                                <span style="font-size: 14px; color: #475569; font-weight: 500;">Facebook</span>
                            </div>
                            <span style="font-size: 14px; font-weight: 700; color: #0f172a;">15%</span>
                        </div>
                        <div style="width: 100%; height: 6px; background: #e2e8f0; border-radius: 3px; overflow: hidden; margin-top: -4px;">
                            <div style="width: 15%; height: 100%; background: #2563eb; border-radius: 3px;"></div>
                        </div>

                        <div style="display: flex; align-items: center; justify-content: space-between;">
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <div style="width: 32px; height: 32px; background: #f3f4f6; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-globe" style="font-size: 14px; color: #6b7280;"></i>
                                </div>
                                <span style="font-size: 14px; color: #475569; font-weight: 500;">Lainnya</span>
                            </div>
                            <span style="font-size: 14px; font-weight: 700; color: #0f172a;">8%</span>
                        </div>
                        <div style="width: 100%; height: 6px; background: #e2e8f0; border-radius: 3px; overflow: hidden; margin-top: -4px;">
                            <div style="width: 8%; height: 100%; background: #6b7280; border-radius: 3px;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Time-based Analytics --}}
        <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.05); margin-bottom: 24px;">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px;">
                <div>
                    <h3 style="margin: 0 0 4px 0; font-size: 16px; font-weight: 600; color: #0f172a;">Aktivitas Per Jam</h3>
                    <p style="margin: 0; font-size: 13px; color: #94a3b8;">Waktu paling aktif pengunjung Anda</p>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: repeat(12, 1fr); gap: 8px;">
                <div style="text-align: center;">
                    <div style="height: 80px; background: #e2e8f0; border-radius: 6px; margin-bottom: 8px; display: flex; align-items: flex-end; overflow: hidden;">
                        <div style="width: 100%; height: 25%; background: linear-gradient(180deg, #3b82f6, #2563eb); border-radius: 6px 6px 0 0;"></div>
                    </div>
                    <p style="margin: 0; font-size: 11px; color: #94a3b8;">00-02</p>
                </div>
                <div style="text-align: center;">
                    <div style="height: 80px; background: #e2e8f0; border-radius: 6px; margin-bottom: 8px; display: flex; align-items: flex-end; overflow: hidden;">
                        <div style="width: 100%; height: 15%; background: linear-gradient(180deg, #3b82f6, #2563eb); border-radius: 6px 6px 0 0;"></div>
                    </div>
                    <p style="margin: 0; font-size: 11px; color: #94a3b8;">02-04</p>
                </div>
                <div style="text-align: center;">
                    <div style="height: 80px; background: #e2e8f0; border-radius: 6px; margin-bottom: 8px; display: flex; align-items: flex-end; overflow: hidden;">
                        <div style="width: 100%; height: 20%; background: linear-gradient(180deg, #3b82f6, #2563eb); border-radius: 6px 6px 0 0;"></div>
                    </div>
                    <p style="margin: 0; font-size: 11px; color: #94a3b8;">04-06</p>
                </div>
                <div style="text-align: center;">
                    <div style="height: 80px; background: #e2e8f0; border-radius: 6px; margin-bottom: 8px; display: flex; align-items: flex-end; overflow: hidden;">
                        <div style="width: 100%; height: 45%; background: linear-gradient(180deg, #3b82f6, #2563eb); border-radius: 6px 6px 0 0;"></div>
                    </div>
                    <p style="margin: 0; font-size: 11px; color: #94a3b8;">06-08</p>
                </div>
                <div style="text-align: center;">
                    <div style="height: 80px; background: #e2e8f0; border-radius: 6px; margin-bottom: 8px; display: flex; align-items: flex-end; overflow: hidden;">
                        <div style="width: 100%; height: 75%; background: linear-gradient(180deg, #10b981, #059669); border-radius: 6px 6px 0 0;"></div>
                    </div>
                    <p style="margin: 0; font-size: 11px; color: #94a3b8;">08-10</p>
                </div>
                <div style="text-align: center;">
                    <div style="height: 80px; background: #e2e8f0; border-radius: 6px; margin-bottom: 8px; display: flex; align-items: flex-end; overflow: hidden;">
                        <div style="width: 100%; height: 85%; background: linear-gradient(180deg, #10b981, #059669); border-radius: 6px 6px 0 0;"></div>
                    </div>
                    <p style="margin: 0; font-size: 11px; color: #94a3b8;">10-12</p>
                </div>
                <div style="text-align: center;">
                    <div style="height: 80px; background: #e2e8f0; border-radius: 6px; margin-bottom: 8px; display: flex; align-items: flex-end; overflow: hidden;">
                        <div style="width: 100%; height: 90%; background: linear-gradient(180deg, #10b981, #059669); border-radius: 6px 6px 0 0;"></div>
                    </div>
                    <p style="margin: 0; font-size: 11px; color: #94a3b8;">12-14</p>
                </div>
                <div style="text-align: center;">
                    <div style="height: 80px; background: #e2e8f0; border-radius: 6px; margin-bottom: 8px; display: flex; align-items: flex-end; overflow: hidden;">
                        <div style="width: 100%; height: 100%; background: linear-gradient(180deg, #f59e0b, #d97706); border-radius: 6px 6px 0 0;"></div>
                    </div>
                    <p style="margin: 0; font-size: 11px; color: #94a3b8;">14-16</p>
                </div>
                <div style="text-align: center;">
                    <div style="height: 80px; background: #e2e8f0; border-radius: 6px; margin-bottom: 8px; display: flex; align-items: flex-end; overflow: hidden;">
                        <div style="width: 100%; height: 95%; background: linear-gradient(180deg, #f59e0b, #d97706); border-radius: 6px 6px 0 0;"></div>
                    </div>
                    <p style="margin: 0; font-size: 11px; color: #94a3b8;">16-18</p>
                </div>
                <div style="text-align: center;">
                    <div style="height: 80px; background: #e2e8f0; border-radius: 6px; margin-bottom: 8px; display: flex; align-items: flex-end; overflow: hidden;">
                        <div style="width: 100%; height: 88%; background: linear-gradient(180deg, #10b981, #059669); border-radius: 6px 6px 0 0;"></div>
                    </div>
                    <p style="margin: 0; font-size: 11px; color: #94a3b8;">18-20</p>
                </div>
                <div style="text-align: center;">
                    <div style="height: 80px; background: #e2e8f0; border-radius: 6px; margin-bottom: 8px; display: flex; align-items: flex-end; overflow: hidden;">
                        <div style="width: 100%; height: 70%; background: linear-gradient(180deg, #3b82f6, #2563eb); border-radius: 6px 6px 0 0;"></div>
                    </div>
                    <p style="margin: 0; font-size: 11px; color: #94a3b8;">20-22</p>
                </div>
                <div style="text-align: center;">
                    <div style="height: 80px; background: #e2e8f0; border-radius: 6px; margin-bottom: 8px; display: flex; align-items: flex-end; overflow: hidden;">
                        <div style="width: 100%; height: 50%; background: linear-gradient(180deg, #3b82f6, #2563eb); border-radius: 6px 6px 0 0;"></div>
                    </div>
                    <p style="margin: 0; font-size: 11px; color: #94a3b8;">22-24</p>
                </div>
            </div>

            <div style="display: flex; justify-content: center; gap: 24px; margin-top: 20px;">
                <div style="display: flex; align-items: center; gap: 8px;">
                    <div style="width: 16px; height: 16px; background: linear-gradient(180deg, #3b82f6, #2563eb); border-radius: 4px;"></div>
                    <span style="font-size: 12px; color: #64748b;">Rendah</span>
                </div>
                <div style="display: flex; align-items: center; gap: 8px;">
                    <div style="width: 16px; height: 16px; background: linear-gradient(180deg, #10b981, #059669); border-radius: 4px;"></div>
                    <span style="font-size: 12px; color: #64748b;">Sedang</span>
                </div>
                <div style="display: flex; align-items: center; gap: 8px;">
                    <div style="width: 16px; height: 16px; background: linear-gradient(180deg, #f59e0b, #d97706); border-radius: 4px;"></div>
                    <span style="font-size: 12px; color: #64748b;">Tinggi</span>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Chart.js Library --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<style>
/* Button Hover Effects */
button {
    transition: all 0.2s ease;
}

button:hover {
    transform: translateY(-1px);
}

button:active {
    transform: translateY(0);
}

/* Date Range Buttons */
.date-range-btn.active {
    background: #3b82f6 !important;
    color: white !important;
    border-color: #3b82f6 !important;
}

.date-range-btn:not(.active):hover {
    background: #f1f5f9 !important;
    border-color: #cbd5e1 !important;
}

/* Back Button */
a[href*="dashboard"]:hover {
    background: #f8fafc !important;
    border-color: #cbd5e1 !important;
}

/* Responsive */
@media (max-width: 1200px) {
    div[style*="grid-template-columns: 2fr 1fr"] {
        grid-template-columns: 1fr !important;
    }
    
    div[style*="grid-template-columns: repeat(4, 1fr)"] {
        grid-template-columns: repeat(2, 1fr) !important;
    }
}

@media (max-width: 768px) {
    div[style*="grid-template-columns: 1fr 1fr"] {
        grid-template-columns: 1fr !important;
    }
    
    div[style*="grid-template-columns: repeat(12, 1fr)"] {
        grid-template-columns: repeat(6, 1fr) !important;
    }
}
</style>

<script>
// Initialize Charts on Page Load
document.addEventListener('DOMContentLoaded', function() {
    initializeClicksChart();
    initializeDeviceChart();
});

/**
 * Initialize Clicks Over Time Chart
 */
function initializeClicksChart() {
    const ctx = document.getElementById('clicksChart').getContext('2d');
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'],
            datasets: [
                {
                    label: 'Klik',
                    data: [145, 178, 192, 168, 225, 248, 201],
                    borderColor: '#3b82f6',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    pointBackgroundColor: '#3b82f6',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2
                },
                {
                    label: 'Pengunjung',
                    data: [98, 132, 158, 141, 187, 210, 168],
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    pointBackgroundColor: '#10b981',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: '#ffffff',
                    titleColor: '#0f172a',
                    bodyColor: '#475569',
                    borderColor: '#e2e8f0',
                    borderWidth: 1,
                    padding: 12,
                    displayColors: true,
                    callbacks: {
                        label: function(context) {
                            return context.dataset.label + ': ' + context.parsed.y + ' klik';
                        }
                    }
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
                        color: '#94a3b8',
                        font: {
                            size: 11
                        }
                    }
                },
                x: {
                    grid: {
                        display: false,
                        drawBorder: false
                    },
                    ticks: {
                        color: '#94a3b8',
                        font: {
                            size: 11
                        }
                    }
                }
            }
        }
    });
}

/**
 * Initialize Device Distribution Chart
 */
function initializeDeviceChart() {
    const ctx = document.getElementById('deviceChart').getContext('2d');
    
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Mobile', 'Desktop', 'Tablet'],
            datasets: [{
                data: [68, 27, 5],
                backgroundColor: [
                    '#3b82f6',
                    '#10b981',
                    '#f59e0b'
                ],
                borderWidth: 0,
                hoverOffset: 4
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
                    backgroundColor: '#ffffff',
                    titleColor: '#0f172a',
                    bodyColor: '#475569',
                    borderColor: '#e2e8f0',
                    borderWidth: 1,
                    padding: 12,
                    callbacks: {
                        label: function(context) {
                            return context.label + ': ' + context.parsed + '%';
                        }
                    }
                }
            },
            cutout: '70%'
        }
    });
}

/**
 * Change Date Range
 */
function setDateRange(days) {
    // Update active button
    document.querySelectorAll('.date-range-btn').forEach(btn => {
        btn.classList.remove('active');
        btn.style.background = '#ffffff';
        btn.style.color = '#475569';
        btn.style.borderColor = '#e2e8f0';
    });
    
    event.target.classList.add('active');
    event.target.style.background = '#3b82f6';
    event.target.style.color = 'white';
    event.target.style.borderColor = '#3b82f6';
    
    // Here you would typically fetch new data based on the selected range
    console.log('Date range changed to:', days, 'days');
}
</script>

@endsection
