@extends('layouts.dashboard')
@section('title', 'Analitik')

@section('content')
<div style="min-height: 100vh; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 24px;">
    <div style="max-width: 1200px; margin: 0 auto;">
        
        {{-- Header Section --}}
        <div style="margin-bottom: 32px;">
            <div style="display: flex; flex-direction: column; gap: 16px;">
                <div style="display: flex; align-items: center; justify-content: space-between;">
                    <div style="display: flex; align-items: center; gap: 12px;">
                        <a href="{{ route('dashboard') }}" style="width: 40px; height: 40px; background: rgba(255,255,255,0.2); backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.3); border-radius: 12px; display: flex; align-items: center; justify-content: center; text-decoration: none; transition: all 0.3s;">
                            <i class="fas fa-arrow-left" style="font-size: 16px; color: white;"></i>
                        </a>
                        <div>
                            <h1 style="margin: 0; font-size: 28px; font-weight: 700; color: white;">Analytics Dashboard</h1>
                            <p style="margin: 0; font-size: 16px; color: rgba(255,255,255,0.8);">Monitor your link performance</p>
                        </div>
                    </div>
                    
                    {{-- Privacy Toggle --}}
                    <div style="display: flex; align-items: center; gap: 12px;">
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <span style="font-size: 14px; color: rgba(255,255,255,0.8);">Hide Data</span>
                            <label class="switch">
                                <input type="checkbox" id="privacyToggle" onchange="togglePrivacyMode()">
                                <span class="slider round"></span>
                            </label>
                        </div>
                    </div>
                </div>
                
                {{-- Controls Row --}}
                <div style="display: flex; flex-wrap: wrap; gap: 12px; align-items: center; justify-content: space-between;">
                    {{-- Date Range Selector --}}
                    <div style="display: flex; gap: 8px;">
                        <button onclick="setDateRange('7')" class="date-range-btn active" data-range="7" style="padding: 12px 20px; background: rgba(255,255,255,0.2); backdrop-filter: blur(10px); color: white; border: 1px solid rgba(255,255,255,0.3); border-radius: 12px; font-size: 14px; font-weight: 500; cursor: pointer; transition: all 0.3s;">
                            7 Days
                        </button>
                        <button onclick="setDateRange('30')" class="date-range-btn" data-range="30" style="padding: 12px 20px; background: rgba(255,255,255,0.1); backdrop-filter: blur(10px); color: rgba(255,255,255,0.8); border: 1px solid rgba(255,255,255,0.2); border-radius: 12px; font-size: 14px; font-weight: 500; cursor: pointer; transition: all 0.3s;">
                            30 Days
                        </button>
                        <button onclick="setDateRange('90')" class="date-range-btn" data-range="90" style="padding: 12px 20px; background: rgba(255,255,255,0.1); backdrop-filter: blur(10px); color: rgba(255,255,255,0.8); border: 1px solid rgba(255,255,255,0.2); border-radius: 12px; font-size: 14px; font-weight: 500; cursor: pointer; transition: all 0.3s;">
                            90 Days
                        </button>
                    </div>
                    
                    {{-- View Toggle --}}
                    <div style="display: flex; gap: 4px; background: rgba(255,255,255,0.1); backdrop-filter: blur(10px); border-radius: 12px; padding: 4px; border: 1px solid rgba(255,255,255,0.2);">
                        <button onclick="setViewMode('chart')" id="chartViewBtn" class="view-toggle-btn active" style="padding: 10px 16px; background: rgba(255,255,255,0.2); color: white; border: none; border-radius: 8px; font-size: 13px; font-weight: 500; cursor: pointer; transition: all 0.3s;">
                            <i class="fas fa-chart-line" style="margin-right: 6px;"></i>Chart
                        </button>
                        <button onclick="setViewMode('table')" id="tableViewBtn" class="view-toggle-btn" style="padding: 10px 16px; background: transparent; color: rgba(255,255,255,0.7); border: none; border-radius: 8px; font-size: 13px; font-weight: 500; cursor: pointer; transition: all 0.3s;">
                            <i class="fas fa-table" style="margin-right: 6px;"></i>Table
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Key Metrics Cards --}}
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px; margin-bottom: 32px;">
            
            {{-- Total Klik --}}
            <div class="metric-card" style="background: rgba(255,255,255,0.95); backdrop-filter: blur(20px); border: 1px solid rgba(255,255,255,0.2); border-radius: 16px; padding: 24px; box-shadow: 0 8px 32px rgba(0,0,0,0.1); transition: all 0.3s;" onclick="showMetricDetail('total-clicks')">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px;">
                    <div style="width: 48px; height: 48px; background: linear-gradient(135deg, #3b82f6, #1d4ed8); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-mouse-pointer" style="font-size: 18px; color: white;"></i>
                    </div>
                    <span class="metric-change" style="font-size: 12px; color: #10b981; font-weight: 600; display: flex; align-items: center; gap: 4px; background: rgba(16, 185, 129, 0.1); padding: 4px 8px; border-radius: 12px;">
                        <i class="fas fa-arrow-up" style="font-size: 10px;"></i> +12.5%
                    </span>
                </div>
                <h3 class="metric-value" style="margin: 0 0 8px 0; font-size: 32px; font-weight: 800; color: #0f172a; filter: blur(0px);">1,248</h3>
                <p style="margin: 0; font-size: 14px; color: #64748b; font-weight: 500;">Total Clicks</p>
            </div>

            {{-- Link Aktif --}}
            <div class="metric-card" style="background: rgba(255,255,255,0.95); backdrop-filter: blur(20px); border: 1px solid rgba(255,255,255,0.2); border-radius: 16px; padding: 24px; box-shadow: 0 8px 32px rgba(0,0,0,0.1); transition: all 0.3s;" onclick="showMetricDetail('active-links')">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px;">
                    <div style="width: 48px; height: 48px; background: linear-gradient(135deg, #10b981, #059669); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-link" style="font-size: 18px; color: white;"></i>
                    </div>
                    <span class="metric-change" style="font-size: 12px; color: #3b82f6; font-weight: 600; display: flex; align-items: center; gap: 4px; background: rgba(59, 130, 246, 0.1); padding: 4px 8px; border-radius: 12px;">
                        <i class="fas fa-arrow-up" style="font-size: 10px;"></i> +3
                    </span>
                </div>
                <h3 class="metric-value" style="margin: 0 0 8px 0; font-size: 32px; font-weight: 800; color: #0f172a; filter: blur(0px);">15</h3>
                <p style="margin: 0; font-size: 14px; color: #64748b; font-weight: 500;">Active Links</p>
            </div>

            {{-- CTR (Click Through Rate) --}}
            <div class="metric-card" style="background: rgba(255,255,255,0.95); backdrop-filter: blur(20px); border: 1px solid rgba(255,255,255,0.2); border-radius: 16px; padding: 24px; box-shadow: 0 8px 32px rgba(0,0,0,0.1); transition: all 0.3s;" onclick="showMetricDetail('ctr')">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px;">
                    <div style="width: 48px; height: 48px; background: linear-gradient(135deg, #f59e0b, #d97706); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-chart-line" style="font-size: 18px; color: white;"></i>
                    </div>
                    <span class="metric-change" style="font-size: 12px; color: #10b981; font-weight: 600; display: flex; align-items: center; gap: 4px; background: rgba(16, 185, 129, 0.1); padding: 4px 8px; border-radius: 12px;">
                        <i class="fas fa-arrow-up" style="font-size: 10px;"></i> +2.1%
                    </span>
                </div>
                <h3 class="metric-value" style="margin: 0 0 8px 0; font-size: 32px; font-weight: 800; color: #0f172a; filter: blur(0px);">8.4%</h3>
                <p style="margin: 0; font-size: 14px; color: #64748b; font-weight: 500;">Click Rate</p>
            </div>

            {{-- Pengunjung Unik --}}
            <div class="metric-card" style="background: rgba(255,255,255,0.95); backdrop-filter: blur(20px); border: 1px solid rgba(255,255,255,0.2); border-radius: 16px; padding: 24px; box-shadow: 0 8px 32px rgba(0,0,0,0.1); transition: all 0.3s;" onclick="showMetricDetail('unique-visitors')">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px;">
                    <div style="width: 48px; height: 48px; background: linear-gradient(135deg, #ec4899, #db2777); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-users" style="font-size: 18px; color: white;"></i>
                    </div>
                    <span class="metric-change" style="font-size: 12px; color: #10b981; font-weight: 600; display: flex; align-items: center; gap: 4px; background: rgba(16, 185, 129, 0.1); padding: 4px 8px; border-radius: 12px;">
                        <i class="fas fa-arrow-up" style="font-size: 10px;"></i> +8.7%
                    </span>
                </div>
                <h3 class="metric-value" style="margin: 0 0 8px 0; font-size: 32px; font-weight: 800; color: #0f172a; filter: blur(0px);">892</h3>
                <p style="margin: 0; font-size: 14px; color: #64748b; font-weight: 500;">Unique Visitors</p>
            </div>
        </div>

        {{-- Charts Section --}}
        <div style="display: grid; grid-template-columns: 1fr; gap: 24px; margin-bottom: 32px;">
            
            {{-- Main Chart - Clicks Over Time --}}
            <div style="background: rgba(255,255,255,0.95); backdrop-filter: blur(20px); border: 1px solid rgba(255,255,255,0.2); border-radius: 16px; padding: 32px; box-shadow: 0 8px 32px rgba(0,0,0,0.1);">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 24px;">
                    <div>
                        <h3 style="margin: 0 0 8px 0; font-size: 20px; font-weight: 700; color: #0f172a;">Performance Overview</h3>
                        <p style="margin: 0; font-size: 14px; color: #64748b;">Click trends over the last 7 days</p>
                    </div>
                    <div style="display: flex; gap: 20px;">
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <div style="width: 14px; height: 14px; background: linear-gradient(135deg, #3b82f6, #1d4ed8); border-radius: 4px;"></div>
                            <span style="font-size: 13px; color: #475569; font-weight: 500;">Clicks</span>
                        </div>
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <div style="width: 14px; height: 14px; background: linear-gradient(135deg, #10b981, #059669); border-radius: 4px;"></div>
                            <span style="font-size: 13px; color: #475569; font-weight: 500;">Visitors</span>
                        </div>
                    </div>
                </div>
                <canvas id="clicksChart" style="max-height: 350px;" class="chart-loading"></canvas>
            </div>

            {{-- Device Distribution & Top Links --}}
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px;">
                
                {{-- Device Distribution --}}
                <div style="background: rgba(255,255,255,0.95); backdrop-filter: blur(20px); border: 1px solid rgba(255,255,255,0.2); border-radius: 16px; padding: 24px; box-shadow: 0 8px 32px rgba(0,0,0,0.1);">
                    <h3 style="margin: 0 0 8px 0; font-size: 18px; font-weight: 700; color: #0f172a;">Device Distribution</h3>
                    <p style="margin: 0 0 24px 0; font-size: 14px; color: #64748b;">Visitor device breakdown</p>
                    <canvas id="deviceChart" style="max-height: 200px;" class="chart-loading"></canvas>
                    
                    {{-- Device Stats --}}
                    <div style="margin-top: 24px; display: flex; flex-direction: column; gap: 16px;">
                        <div style="display: flex; align-items: center; justify-content: space-between;">
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <div style="width: 10px; height: 10px; background: linear-gradient(135deg, #3b82f6, #1d4ed8); border-radius: 50%;"></div>
                                <span style="font-size: 14px; color: #475569; font-weight: 500;">Mobile</span>
                            </div>
                            <span style="font-size: 14px; font-weight: 700; color: #0f172a;">68%</span>
                        </div>
                        <div style="display: flex; align-items: center; justify-content: space-between;">
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <div style="width: 10px; height: 10px; background: linear-gradient(135deg, #10b981, #059669); border-radius: 50%;"></div>
                                <span style="font-size: 14px; color: #475569; font-weight: 500;">Desktop</span>
                            </div>
                            <span style="font-size: 14px; font-weight: 700; color: #0f172a;">27%</span>
                        </div>
                        <div style="display: flex; align-items: center; justify-content: space-between;">
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <div style="width: 10px; height: 10px; background: linear-gradient(135deg, #f59e0b, #d97706); border-radius: 50%;"></div>
                                <span style="font-size: 14px; color: #475569; font-weight: 500;">Tablet</span>
                            </div>
                            <span style="font-size: 14px; font-weight: 700; color: #0f172a;">5%</span>
                        </div>
                    </div>
                </div>

                {{-- Top Performing Links --}}
                <div style="background: rgba(255,255,255,0.95); backdrop-filter: blur(20px); border: 1px solid rgba(255,255,255,0.2); border-radius: 16px; padding: 24px; box-shadow: 0 8px 32px rgba(0,0,0,0.1);">
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 24px;">
                        <div>
                            <h3 style="margin: 0 0 4px 0; font-size: 18px; font-weight: 700; color: #0f172a;">Top Links</h3>
                            <p style="margin: 0; font-size: 14px; color: #64748b;">Most clicked links</p>
                        </div>
                        <a href="{{ route('links.index') }}" style="font-size: 13px; color: #3b82f6; text-decoration: none; font-weight: 500; padding: 8px 12px; background: rgba(59, 130, 246, 0.1); border-radius: 8px; transition: all 0.2s;">
                            View All <i class="fas fa-arrow-right" style="font-size: 11px; margin-left: 4px;"></i>
                        </a>
                    </div>

                    <div style="display: flex; flex-direction: column; gap: 16px;">
                        {{-- Link Item 1 --}}
                        <div style="padding: 20px; background: rgba(59, 130, 246, 0.05); border: 1px solid rgba(59, 130, 246, 0.1); border-radius: 12px; transition: all 0.3s; cursor: pointer;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 8px 25px rgba(59, 130, 246, 0.15)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none'">
                            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px;">
                                <div style="display: flex; align-items: center; gap: 12px; flex: 1; min-width: 0;">
                                    <div style="width: 40px; height: 40px; background: linear-gradient(135deg, #3b82f6, #1d4ed8); border-radius: 10px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                        <i class="fas fa-link" style="font-size: 16px; color: white;"></i>
                                    </div>
                                    <div style="flex: 1; min-width: 0;">
                                        <p style="margin: 0 0 4px 0; font-size: 15px; font-weight: 600; color: #0f172a; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">Promo Ramadan</p>
                                        <p style="margin: 0; font-size: 13px; color: #64748b; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">mobay.id/promo-ramadan</p>
                                    </div>
                                </div>
                                <div style="text-align: right; flex-shrink: 0; margin-left: 16px;">
                                    <p class="link-clicks" style="margin: 0 0 4px 0; font-size: 20px; font-weight: 800; color: #0f172a;">342</p>
                                    <p style="margin: 0; font-size: 12px; color: #10b981; font-weight: 600;">+15.2%</p>
                                </div>
                            </div>
                            <div style="width: 100%; height: 8px; background: rgba(59, 130, 246, 0.1); border-radius: 4px; overflow: hidden;">
                                <div style="width: 85%; height: 100%; background: linear-gradient(90deg, #3b82f6, #1d4ed8); border-radius: 4px;"></div>
                            </div>
                        </div>
                
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
                                    <p style="margin: 0; font-size: 12px; color: #94a3b8; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">mobay.id/promo-ramadan</p>
                                </div>
                            </div>
                            <div style="text-align: right; flex-shrink: 0; margin-left: 12px;">
                                <p style="margin: 0 0 2px 0; font-size: 18px; font-weight: 700; color: #0f172a;">342</p>
                                <p style="margin: 0; font-size: 11px; color: #10b981; font-weight: 600;">+15.2%</p>
                            </div>
                        </div>
                        <div style="width: 100%; height: 6px; background: #e2e8f0; border-radius: 3px; overflow: hidden;" class="progress-bar">
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
                                    <p style="margin: 0; font-size: 12px; color: #94a3b8; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">mobay.id/katalog-2024</p>
                                </div>
                            </div>
                            <div style="text-align: right; flex-shrink: 0; margin-left: 12px;">
                                <p style="margin: 0 0 2px 0; font-size: 18px; font-weight: 700; color: #0f172a;">287</p>
                                <p style="margin: 0; font-size: 11px; color: #10b981; font-weight: 600;">+8.4%</p>
                            </div>
                        </div>
                        <div style="width: 100%; height: 6px; background: #e2e8f0; border-radius: 3px; overflow: hidden;" class="progress-bar">
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
                                    <p style="margin: 0; font-size: 12px; color: #94a3b8; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">mobay.id/giveaway-ig</p>
                                </div>
                            </div>
                            <div style="text-align: right; flex-shrink: 0; margin-left: 12px;">
                                <p style="margin: 0 0 2px 0; font-size: 18px; font-weight: 700; color: #0f172a;">213</p>
                                <p style="margin: 0; font-size: 11px; color: #ef4444; font-weight: 600;">-2.1%</p>
                            </div>
                        </div>
                        <div style="width: 100%; height: 6px; background: #e2e8f0; border-radius: 3px; overflow: hidden;" class="progress-bar">
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

        {{-- Advanced Analytics Charts --}}
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 24px; margin-bottom: 32px;">
            
            {{-- Conversion Rate Chart --}}
            <div style="background: rgba(255,255,255,0.95); backdrop-filter: blur(20px); border: 1px solid rgba(255,255,255,0.2); border-radius: 16px; padding: 32px; box-shadow: 0 8px 32px rgba(0,0,0,0.1);">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 24px;">
                    <div>
                        <h3 style="margin: 0 0 8px 0; font-size: 20px; font-weight: 700; color: #0f172a;">Conversion Rate</h3>
                        <p style="margin: 0; font-size: 14px; color: #64748b;">Sales conversion performance</p>
                    </div>
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <div style="width: 12px; height: 12px; background: linear-gradient(135deg, #10b981, #059669); border-radius: 50%;"></div>
                        <span style="font-size: 18px; font-weight: 800; color: #0f172a;">3.2%</span>
                    </div>
                </div>
                <canvas id="conversionChart" style="max-height: 200px;" class="chart-loading"></canvas>
                <div style="margin-top: 20px; display: flex; justify-content: space-between; align-items: center;">
                    <div style="text-align: center;">
                        <p style="margin: 0; font-size: 12px; color: #64748b;">Target</p>
                        <p style="margin: 0; font-size: 16px; font-weight: 700; color: #10b981;">5.0%</p>
                    </div>
                    <div style="text-align: center;">
                        <p style="margin: 0; font-size: 12px; color: #64748b;">Growth</p>
                        <p style="margin: 0; font-size: 16px; font-weight: 700; color: #10b981;">+12.5%</p>
                    </div>
                    <div style="text-align: center;">
                        <p style="margin: 0; font-size: 12px; color: #64748b;">Best Day</p>
                        <p style="margin: 0; font-size: 16px; font-weight: 700; color: #0f172a;">Friday</p>
                    </div>
                </div>
            </div>

            {{-- Revenue Per Click Chart --}}
            <div style="background: rgba(255,255,255,0.95); backdrop-filter: blur(20px); border: 1px solid rgba(255,255,255,0.2); border-radius: 16px; padding: 32px; box-shadow: 0 8px 32px rgba(0,0,0,0.1);">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 24px;">
                    <div>
                        <h3 style="margin: 0 0 8px 0; font-size: 20px; font-weight: 700; color: #0f172a;">Revenue Per Click</h3>
                        <p style="margin: 0; font-size: 14px; color: #64748b;">Average earnings per click</p>
                    </div>
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <span style="font-size: 18px; font-weight: 800; color: #0f172a;">$2.45</span>
                        <span style="font-size: 12px; color: #10b981; font-weight: 600; background: rgba(16, 185, 129, 0.1); padding: 4px 8px; border-radius: 12px;">+8.3%</span>
                    </div>
                </div>
                <canvas id="rpcChart" style="max-height: 200px;" class="chart-loading"></canvas>
                <div style="margin-top: 20px; display: flex; justify-content: space-between; align-items: center;">
                    <div style="text-align: center;">
                        <p style="margin: 0; font-size: 12px; color: #64748b;">This Week</p>
                        <p style="margin: 0; font-size: 16px; font-weight: 700; color: #0f172a;">$2.45</p>
                    </div>
                    <div style="text-align: center;">
                        <p style="margin: 0; font-size: 12px; color: #64748b;">Last Week</p>
                        <p style="margin: 0; font-size: 16px; font-weight: 700; color: #64748b;">$2.26</p>
                    </div>
                    <div style="text-align: center;">
                        <p style="margin: 0; font-size: 12px; color: #64748b;">Best</p>
                        <p style="margin: 0; font-size: 16px; font-weight: 700; color: #10b981;">$3.12</p>
                    </div>
                </div>
            </div>

            {{-- View to Click Conversion --}}
            <div style="background: rgba(255,255,255,0.95); backdrop-filter: blur(20px); border: 1px solid rgba(255,255,255,0.2); border-radius: 16px; padding: 32px; box-shadow: 0 8px 32px rgba(0,0,0,0.1);">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 24px;">
                    <div>
                        <h3 style="margin: 0 0 8px 0; font-size: 20px; font-weight: 700; color: #0f172a;">View to Click Rate</h3>
                        <p style="margin: 0; font-size: 14px; color: #64748b;">Conversion from views to clicks</p>
                    </div>
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <div style="width: 12px; height: 12px; background: linear-gradient(135deg, #3b82f6, #1d4ed8); border-radius: 50%;"></div>
                        <span style="font-size: 18px; font-weight: 800; color: #0f172a;">24.8%</span>
                    </div>
                </div>
                <canvas id="vtcChart" style="max-height: 200px;" class="chart-loading"></canvas>
                <div style="margin-top: 20px; display: flex; justify-content: space-between; align-items: center;">
                    <div style="text-align: center;">
                        <p style="margin: 0; font-size: 12px; color: #64748b;">Mobile</p>
                        <p style="margin: 0; font-size: 16px; font-weight: 700; color: #3b82f6;">28.5%</p>
                    </div>
                    <div style="text-align: center;">
                        <p style="margin: 0; font-size: 12px; color: #64748b;">Desktop</p>
                        <p style="margin: 0; font-size: 16px; font-weight: 700; color: #10b981;">22.1%</p>
                    </div>
                    <div style="text-align: center;">
                        <p style="margin: 0; font-size: 12px; color: #64748b;">Tablet</p>
                        <p style="margin: 0; font-size: 16px; font-weight: 700; color: #f59e0b;">18.3%</p>
                    </div>
                </div>
            </div>

            {{-- Total Sales Chart --}}
            <div style="background: rgba(255,255,255,0.95); backdrop-filter: blur(20px); border: 1px solid rgba(255,255,255,0.2); border-radius: 16px; padding: 32px; box-shadow: 0 8px 32px rgba(0,0,0,0.1);">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 24px;">
                    <div>
                        <h3 style="margin: 0 0 8px 0; font-size: 20px; font-weight: 700; color: #0f172a;">Total Sales</h3>
                        <p style="margin: 0; font-size: 14px; color: #64748b;">Revenue generated this period</p>
                    </div>
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <span style="font-size: 18px; font-weight: 800; color: #0f172a;">$3,067</span>
                        <span style="font-size: 12px; color: #10b981; font-weight: 600; background: rgba(16, 185, 129, 0.1); padding: 4px 8px; border-radius: 12px;">+15.2%</span>
                    </div>
                </div>
                <canvas id="salesChart" style="max-height: 200px;" class="chart-loading"></canvas>
                <div style="margin-top: 20px; display: flex; justify-content: space-between; align-items: center;">
                    <div style="text-align: center;">
                        <p style="margin: 0; font-size: 12px; color: #64748b;">This Month</p>
                        <p style="margin: 0; font-size: 16px; font-weight: 700; color: #0f172a;">$3,067</p>
                    </div>
                    <div style="text-align: center;">
                        <p style="margin: 0; font-size: 12px; color: #64748b;">Last Month</p>
                        <p style="margin: 0; font-size: 16px; font-weight: 700; color: #64748b;">$2,662</p>
                    </div>
                    <div style="text-align: center;">
                        <p style="margin: 0; font-size: 12px; color: #64748b;">Avg/Day</p>
                        <p style="margin: 0; font-size: 16px; font-weight: 700; color: #10b981;">$102</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Chart Detail Modal --}}
<div id="chartDetailModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;" class="modal-overlay">
    <div style="background: #ffffff; border-radius: 16px; padding: 24px; max-width: 500px; width: 90%; max-height: 80vh; overflow-y: auto; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1), 0 10px 10px -5px rgba(0,0,0,0.04);" class="modal-content">
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px;">
            <h3 style="margin: 0; font-size: 18px; font-weight: 600; color: #0f172a;">Detail Data</h3>
            <button onclick="closeChartDetailModal()" style="background: none; border: none; font-size: 20px; color: #94a3b8; cursor: pointer; padding: 4px;">&times;</button>
        </div>
        <div id="modalContent">
            <!-- Content will be populated by JavaScript -->
        </div>
    </div>
</div>

{{-- Chart.js Library --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-zoom@2.0.1/dist/chartjs-plugin-zoom.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-date-fns@3.0.0/dist/chartjs-adapter-date-fns.bundle.min.js"></script>

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

/* Modal Animations */
@keyframes modalFadeIn {
    from {
        opacity: 0;
        transform: scale(0.9) translateY(-20px);
    }
    to {
        opacity: 1;
        transform: scale(1) translateY(0);
    }
}

@keyframes modalFadeOut {
    from {
        opacity: 1;
        transform: scale(1) translateY(0);
    }
    to {
        opacity: 0;
        transform: scale(0.9) translateY(-20px);
    }
}

.modal-overlay {
    animation: modalFadeIn 0.3s ease-out;
}

/* Enhanced Chart Interactions */
#clicksChart, #deviceChart {
    transition: all 0.3s ease;
}

#clicksChart:hover, #deviceChart:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px -8px rgba(0,0,0,0.1);
}

/* Switch Toggle Styles */
.switch {
    position: relative;
    display: inline-block;
    width: 50px;
    height: 24px;
}

.switch input {
    opacity: 0;
    width: 0;
    height: 0;
}

.slider {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: rgba(255,255,255,0.2);
    transition: .4s;
    border-radius: 24px;
}

.slider:before {
    position: absolute;
    content: "";
    height: 18px;
    width: 18px;
    left: 3px;
    bottom: 3px;
    background-color: white;
    transition: .4s;
    border-radius: 50%;
    box-shadow: 0 2px 4px rgba(0,0,0,0.2);
}

input:checked + .slider {
    background-color: rgba(16, 185, 129, 0.8);
}

input:checked + .slider:before {
    transform: translateX(26px);
}

/* Privacy Mode Styles */
.privacy-blur .metric-value {
    filter: blur(8px) !important;
    transition: filter 0.3s ease;
}

.privacy-blur .metric-change {
    filter: blur(4px) !important;
    transition: filter 0.3s ease;
}

.privacy-blur .link-clicks {
    filter: blur(6px) !important;
    transition: filter 0.3s ease;
}

.privacy-blur .progress-bar::after {
    display: none !important;
}

/* Loading Animation */
.chart-loading {
    position: relative;
    overflow: hidden;
}

.chart-loading::after {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
    animation: loading 1.5s infinite;
}

@keyframes loading {
    0% { left: -100%; }
    100% { left: 100%; }
}

/* Enhanced Button Styles */
.date-range-btn {
    position: relative;
    overflow: hidden;
}

.date-range-btn::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
    transition: left 0.5s;
}

.date-range-btn:hover::before {
    left: 100%;
}

/* View Toggle Styles */
.view-toggle-btn.active {
    background: #ffffff !important;
    color: #0f172a !important;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1) !important;
}

.view-toggle-btn:not(.active):hover {
    background: rgba(255,255,255,0.5) !important;
    color: #475569 !important;
}

/* Enhanced Card Interactions */
.metric-card {
    transition: all 0.3s ease;
    cursor: pointer;
}

.metric-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 24px -8px rgba(0,0,0,0.15);
}

.metric-card:active {
    transform: translateY(-2px);
}
</style>

<script>
 * Initialize Clicks Over Time Chart
 */
function initializeClicksChart() {
    const ctx = document.getElementById('clicksChart').getContext('2d');
    
    const chart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'],
            datasets: [
                {
                    label: 'Klik',
                    data: [145, 178, 192, 168, 225, 248, 201],
                    borderColor: '#3b82f6',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 6,
                    pointHoverRadius: 8,
                    pointBackgroundColor: '#3b82f6',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 3,
                    pointHoverBackgroundColor: '#1d4ed8',
                    pointHoverBorderColor: '#ffffff',
                    pointHoverBorderWidth: 3
                },
                {
                    label: 'Pengunjung',
                    data: [98, 132, 158, 141, 187, 210, 168],
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 6,
                    pointHoverRadius: 8,
                    pointBackgroundColor: '#10b981',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 3,
                    pointHoverBackgroundColor: '#047857',
                    pointHoverBorderColor: '#ffffff',
                    pointHoverBorderWidth: 3
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                intersect: false,
                mode: 'index'
            },
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    enabled: true,
                    backgroundColor: 'rgba(15, 23, 42, 0.95)',
                    titleColor: '#ffffff',
                    bodyColor: '#e2e8f0',
                    borderColor: '#334155',
                    borderWidth: 1,
                    padding: 16,
                    cornerRadius: 12,
                    displayColors: true,
                    titleFont: {
                        size: 14,
                        weight: '600'
                    },
                    bodyFont: {
                        size: 13
                    },
                    callbacks: {
                        title: function(context) {
                            return 'Hari ' + context[0].label;
                        },
                        label: function(context) {
                            let label = context.dataset.label || '';
                            if (label) {
                                label += ': ';
                            }
                            label += context.parsed.y;
                            if (context.datasetIndex === 0) {
                                label += ' klik';
                            } else {
                                label += ' pengunjung';
                            }
                            return label;
                        },
                        afterBody: function(context) {
                            if (context.length > 1) {
                                const clicks = context[0].parsed.y;
                                const visitors = context[1].parsed.y;
                                const ctr = clicks > 0 ? ((clicks / visitors) * 100).toFixed(1) : 0;
                                return '\nClick Rate: ' + ctr + '%';
                            }
                            return '';
                        }
                    }
                },
                zoom: {
                    pan: {
                        enabled: true,
                        mode: 'x',
                        modifierKey: 'ctrl'
                    },
                    zoom: {
                        wheel: {
                            enabled: true
                        },
                        pinch: {
                            enabled: true
                        },
                        mode: 'x'
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
                            size: 12,
                            weight: '500'
                        },
                        padding: 10
                    },
                    border: {
                        display: false
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
                            size: 12,
                            weight: '500'
                        },
                        padding: 10
                    },
                    border: {
                        display: false
                    }
                }
            },
            elements: {
                point: {
                    hoverRadius: 8
                }
            },
            animation: {
                duration: 2000,
                easing: 'easeInOutQuart'
            },
            onClick: function(event, elements) {
                if (elements.length > 0) {
                    const element = elements[0];
                    const datasetIndex = element.datasetIndex;
                    const dataIndex = element.index;
                    const value = this.data.datasets[datasetIndex].data[dataIndex];
                    const label = this.data.labels[dataIndex];
                    
                    showChartDetailModal(label, this.data.datasets[datasetIndex].label, value, datasetIndex);
                }
            }
        }
    });

    // Add reset zoom button
    const resetZoomBtn = document.createElement('button');
    resetZoomBtn.innerHTML = '<i class="fas fa-undo"></i> Reset Zoom';
    resetZoomBtn.style.cssText = `
        position: absolute;
        top: 10px;
        right: 10px;
        background: #3b82f6;
        color: white;
        border: none;
        padding: 8px 12px;
        border-radius: 6px;
        font-size: 12px;
        cursor: pointer;
        display: none;
        z-index: 10;
        transition: all 0.2s;
    `;
    resetZoomBtn.onclick = () => chart.resetZoom();
    
    // Show/hide reset button based on zoom state
    chart.options.plugins.zoom.zoom.onZoom = () => resetZoomBtn.style.display = 'block';
    chart.options.plugins.zoom.pan.onPan = () => resetZoomBtn.style.display = 'block';
    
    document.getElementById('clicksChart').parentNode.style.position = 'relative';
    document.getElementById('clicksChart').parentNode.appendChild(resetZoomBtn);
}

/**
 * Initialize Device Distribution Chart
 */
function initializeDeviceChart() {
    const ctx = document.getElementById('deviceChart').getContext('2d');
    
    const chart = new Chart(ctx, {
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
                hoverOffset: 8,
                hoverBackgroundColor: [
                    '#2563eb',
                    '#059669',
                    '#d97706'
                ]
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
                    enabled: true,
                    backgroundColor: 'rgba(15, 23, 42, 0.95)',
                    titleColor: '#ffffff',
                    bodyColor: '#e2e8f0',
                    borderColor: '#334155',
                    borderWidth: 1,
                    padding: 16,
                    cornerRadius: 12,
                    titleFont: {
                        size: 14,
                        weight: '600'
                    },
                    bodyFont: {
                        size: 13
                    },
                    callbacks: {
                        label: function(context) {
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = ((context.parsed / total) * 100).toFixed(1);
                            return context.label + ': ' + context.parsed + ' (' + percentage + '%)';
                        }
                    }
                }
            },
            cutout: '70%',
            animation: {
                animateRotate: true,
                animateScale: true,
                duration: 2000,
                easing: 'easeInOutQuart'
            },
            onClick: function(event, elements) {
                if (elements.length > 0) {
                    const element = elements[0];
                    const dataIndex = element.index;
                    const label = this.data.labels[dataIndex];
                    const value = this.data.datasets[0].data[dataIndex];
                    
                    showDeviceDetailModal(label, value);
                }
            }
        }
    });

    // Add export button
    const exportBtn = document.createElement('button');
    exportBtn.innerHTML = '<i class="fas fa-download"></i>';
    exportBtn.title = 'Export Chart';
    exportBtn.style.cssText = `
        position: absolute;
        top: 10px;
        right: 10px;
        background: #ffffff;
        color: #64748b;
        border: 1px solid #e2e8f0;
        padding: 8px;
        border-radius: 6px;
        font-size: 12px;
        cursor: pointer;
        z-index: 10;
        transition: all 0.2s;
    `;
    exportBtn.onmouseover = () => {
        exportBtn.style.background = '#f8fafc';
        exportBtn.style.color = '#0f172a';
    };
    exportBtn.onmouseout = () => {
        exportBtn.style.background = '#ffffff';
        exportBtn.style.color = '#64748b';
    };
    exportBtn.onclick = () => {
        const link = document.createElement('a');
        link.download = 'device-distribution.png';
        link.href = chart.toBase64Image();
        link.click();
    };
    
    document.getElementById('deviceChart').parentNode.style.position = 'relative';
    document.getElementById('deviceChart').parentNode.appendChild(exportBtn);
}

/**
 * Initialize Conversion Rate Chart
 */
function initializeConversionChart() {
    const ctx = document.getElementById('conversionChart').getContext('2d');
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
            datasets: [{
                label: 'Conversion Rate',
                data: [2.8, 3.1, 2.9, 3.4, 3.8, 3.2, 3.0],
                borderColor: '#10b981',
                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointRadius: 6,
                pointHoverRadius: 8,
                pointBackgroundColor: '#10b981',
                pointBorderColor: '#ffffff',
                pointBorderWidth: 3
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
                    backgroundColor: 'rgba(15, 23, 42, 0.95)',
                    titleColor: '#ffffff',
                    bodyColor: '#e2e8f0',
                    borderColor: '#334155',
                    borderWidth: 1,
                    padding: 12,
                    cornerRadius: 8,
                    callbacks: {
                        label: function(context) {
                            return context.parsed.y + '% conversion rate';
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(255,255,255,0.1)',
                        drawBorder: false
                    },
                    ticks: {
                        color: '#64748b',
                        font: {
                            size: 11
                        },
                        callback: function(value) {
                            return value + '%';
                        }
                    }
                },
                x: {
                    grid: {
                        display: false,
                        drawBorder: false
                    },
                    ticks: {
                        color: '#64748b',
                        font: {
                            size: 11
                        }
                    }
                }
            },
            elements: {
                point: {
                    hoverRadius: 8
                }
            },
            animation: {
                duration: 2000,
                easing: 'easeInOutQuart'
            }
        }
    });
}

/**
 * Initialize Revenue Per Click Chart
 */
function initializeRpcChart() {
    const ctx = document.getElementById('rpcChart').getContext('2d');
    
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
            datasets: [{
                label: 'Revenue Per Click',
                data: [2.1, 2.3, 2.4, 2.6, 2.8, 2.2, 2.5],
                backgroundColor: 'rgba(59, 130, 246, 0.8)',
                borderColor: '#3b82f6',
                borderWidth: 0,
                borderRadius: 8,
                borderSkipped: false,
                hoverBackgroundColor: 'rgba(59, 130, 246, 1)'
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
                    backgroundColor: 'rgba(15, 23, 42, 0.95)',
                    titleColor: '#ffffff',
                    bodyColor: '#e2e8f0',
                    borderColor: '#334155',
                    borderWidth: 1,
                    padding: 12,
                    cornerRadius: 8,
                    callbacks: {
                        label: function(context) {
                            return '$' + context.parsed.y + ' per click';
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(255,255,255,0.1)',
                        drawBorder: false
                    },
                    ticks: {
                        color: '#64748b',
                        font: {
                            size: 11
                        },
                        callback: function(value) {
                            return '$' + value;
                        }
                    }
                },
                x: {
                    grid: {
                        display: false,
                        drawBorder: false
                    },
                    ticks: {
                        color: '#64748b',
                        font: {
                            size: 11
                        }
                    }
                }
            },
            animation: {
                duration: 2000,
                easing: 'easeInOutQuart',
                delay: function(context) {
                    return context.dataIndex * 200;
                }
            }
        }
    });
}

/**
 * Initialize View to Click Conversion Chart
 */
function initializeVtcChart() {
    const ctx = document.getElementById('vtcChart').getContext('2d');
    
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Mobile', 'Desktop', 'Tablet'],
            datasets: [{
                data: [28.5, 22.1, 18.3],
                backgroundColor: [
                    'rgba(59, 130, 246, 0.8)',
                    'rgba(16, 185, 129, 0.8)',
                    'rgba(245, 158, 11, 0.8)'
                ],
                borderWidth: 0,
                hoverOffset: 8,
                hoverBackgroundColor: [
                    'rgba(59, 130, 246, 1)',
                    'rgba(16, 185, 129, 1)',
                    'rgba(245, 158, 11, 1)'
                ]
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
                    backgroundColor: 'rgba(15, 23, 42, 0.95)',
                    titleColor: '#ffffff',
                    bodyColor: '#e2e8f0',
                    borderColor: '#334155',
                    borderWidth: 1,
                    padding: 12,
                    cornerRadius: 8,
                    callbacks: {
                        label: function(context) {
                            return context.label + ': ' + context.parsed + '%';
                        }
                    }
                }
            },
            cutout: '75%',
            animation: {
                animateRotate: true,
                animateScale: true,
                duration: 2000,
                easing: 'easeInOutQuart'
            }
        }
    });
}

/**
 * Initialize Total Sales Chart
 */
function initializeSalesChart() {
    const ctx = document.getElementById('salesChart').getContext('2d');
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
            datasets: [
                {
                    label: 'Sales',
                    data: [420, 380, 510, 480, 620, 390, 450],
                    borderColor: '#8b5cf6',
                    backgroundColor: 'rgba(139, 92, 246, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 6,
                    pointHoverRadius: 8,
                    pointBackgroundColor: '#8b5cf6',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 3
                },
                {
                    label: 'Target',
                    data: [400, 400, 400, 400, 400, 400, 400],
                    borderColor: 'rgba(156, 163, 175, 0.5)',
                    backgroundColor: 'transparent',
                    borderWidth: 2,
                    borderDash: [5, 5],
                    fill: false,
                    pointRadius: 0,
                    tension: 0
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                intersect: false,
                mode: 'index'
            },
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: 'rgba(15, 23, 42, 0.95)',
                    titleColor: '#ffffff',
                    bodyColor: '#e2e8f0',
                    borderColor: '#334155',
                    borderWidth: 1,
                    padding: 12,
                    cornerRadius: 8,
                    callbacks: {
                        label: function(context) {
                            if (context.datasetIndex === 0) {
                                return '$' + context.parsed.y;
                            }
                            return '';
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(255,255,255,0.1)',
                        drawBorder: false
                    },
                    ticks: {
                        color: '#64748b',
                        font: {
                            size: 11
                        },
                        callback: function(value) {
                            return '$' + value;
                        }
                    }
                },
                x: {
                    grid: {
                        display: false,
                        drawBorder: false
                    },
                    ticks: {
                        color: '#64748b',
                        font: {
                            size: 11
                        }
                    }
                }
            },
            elements: {
                point: {
                    hoverRadius: 8
                }
            },
            animation: {
                duration: 2000,
                easing: 'easeInOutQuart'
            }
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

/**
 * Set View Mode
 */
function setViewMode(mode) {
    const chartViewBtn = document.getElementById('chartViewBtn');
    const tableViewBtn = document.getElementById('tableViewBtn');
    const chartContainer = document.querySelector('div[style*="grid-template-columns: 1fr"]');
    
    if (mode === 'chart') {
        chartViewBtn.classList.add('active');
        tableViewBtn.classList.remove('active');
        // Show charts (default view)
        chartContainer.style.display = 'grid';
    } else {
        tableViewBtn.classList.add('active');
        chartViewBtn.classList.remove('active');
        // Hide charts and show table view (you would implement table view here)
        chartContainer.style.display = 'none';
        // For now, just hide - you could add a table implementation
        alert('Table view akan diimplementasikan dalam update berikutnya!');
    }
}

/**
 * Toggle Privacy Mode
 */
function togglePrivacyMode() {
    const body = document.body;
    const toggle = document.getElementById('privacyToggle');
    
    if (toggle.checked) {
        body.classList.add('privacy-blur');
        // Store preference
        localStorage.setItem('privacyMode', 'true');
    } else {
        body.classList.remove('privacy-blur');
        // Store preference
        localStorage.setItem('privacyMode', 'false');
    }
}

/**
 * Initialize Privacy Mode on Load
 */
document.addEventListener('DOMContentLoaded', function() {
    // Check stored preference
    const privacyMode = localStorage.getItem('privacyMode');
    const toggle = document.getElementById('privacyToggle');
    
    if (privacyMode === 'true') {
        toggle.checked = true;
        document.body.classList.add('privacy-blur');
    }
    
    initializeClicksChart();
    initializeDeviceChart();
    initializeConversionChart();
    initializeRpcChart();
    initializeVtcChart();
    initializeSalesChart();
    
    // Remove loading animations after charts are initialized
    setTimeout(() => {
        document.getElementById('clicksChart').classList.remove('chart-loading');
        document.getElementById('deviceChart').classList.remove('chart-loading');
        document.getElementById('conversionChart').classList.remove('chart-loading');
        document.getElementById('rpcChart').classList.remove('chart-loading');
        document.getElementById('vtcChart').classList.remove('chart-loading');
        document.getElementById('salesChart').classList.remove('chart-loading');
    }, 500);
});

/**
 * Show Chart Detail Modal
 */
function showChartDetailModal(day, metric, value, datasetIndex) {
    const modal = document.getElementById('chartDetailModal');
    const modalContent = document.getElementById('modalContent');
    
    const colors = ['#3b82f6', '#10b981'];
    const icons = ['fas fa-mouse-pointer', 'fas fa-users'];
    const descriptions = [
        'Jumlah total klik yang terjadi pada hari tersebut',
        'Jumlah pengunjung unik yang mengakses link'
    ];
    
    modalContent.innerHTML = `
        <div style="text-align: center; margin-bottom: 20px;">
            <div style="width: 60px; height: 60px; background: ${colors[datasetIndex]}; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px;">
                <i class="${icons[datasetIndex]}" style="font-size: 24px; color: white;"></i>
            </div>
            <h4 style="margin: 0 0 8px 0; font-size: 24px; font-weight: 700; color: #0f172a;">${value.toLocaleString()}</h4>
            <p style="margin: 0; font-size: 16px; color: #64748b;">${metric} - ${day}</p>
        </div>
        
        <div style="background: #f8fafc; border-radius: 12px; padding: 16px; margin-bottom: 16px;">
            <p style="margin: 0; font-size: 14px; color: #475569; line-height: 1.5;">
                ${descriptions[datasetIndex]}
            </p>
        </div>
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
            <div style="background: #f8fafc; border-radius: 8px; padding: 12px; text-align: center;">
                <p style="margin: 0 0 4px 0; font-size: 12px; color: #94a3b8;">Rata-rata Harian</p>
                <p style="margin: 0; font-size: 16px; font-weight: 600; color: #0f172a;">${Math.round(value * 0.85).toLocaleString()}</p>
            </div>
            <div style="background: #f8fafc; border-radius: 8px; padding: 12px; text-align: center;">
                <p style="margin: 0 0 4px 0; font-size: 12px; color: #94a3b8;">Target</p>
                <p style="margin: 0; font-size: 16px; font-weight: 600; color: #0f172a;">${Math.round(value * 1.2).toLocaleString()}</p>
            </div>
        </div>
    `;
    
    modal.style.display = 'flex';
    modal.style.animation = 'modalFadeIn 0.3s ease-out';
}

/**
 * Close Chart Detail Modal
 */
function closeChartDetailModal() {
    const modal = document.getElementById('chartDetailModal');
    modal.style.animation = 'modalFadeOut 0.3s ease-in';
    setTimeout(() => {
        modal.style.display = 'none';
    }, 300);
}

// Close modal when clicking outside
document.getElementById('chartDetailModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeChartDetailModal();
    }
});

/**
 * Show Device Detail Modal
 */
function showDeviceDetailModal(device, percentage) {
    const modal = document.getElementById('chartDetailModal');
    const modalContent = document.getElementById('modalContent');
    
    const deviceInfo = {
        'Mobile': {
            icon: 'fas fa-mobile-alt',
            color: '#3b82f6',
            description: 'Pengguna yang mengakses melalui smartphone',
            insights: 'Fokus pada optimasi mobile experience'
        },
        'Desktop': {
            icon: 'fas fa-desktop',
            color: '#10b981',
            description: 'Pengguna yang mengakses melalui komputer',
            insights: 'Manfaatkan fitur desktop yang lebih lengkap'
        },
        'Tablet': {
            icon: 'fas fa-tablet-alt',
            color: '#f59e0b',
            description: 'Pengguna yang mengakses melalui tablet',
            insights: 'Perhatikan layout responsivitas tablet'
        }
    };
    
    const info = deviceInfo[device];
    
    modalContent.innerHTML = `
        <div style="text-align: center; margin-bottom: 20px;">
            <div style="width: 60px; height: 60px; background: ${info.color}; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px;">
                <i class="${info.icon}" style="font-size: 24px; color: white;"></i>
            </div>
            <h4 style="margin: 0 0 8px 0; font-size: 24px; font-weight: 700; color: #0f172a;">${percentage}%</h4>
            <p style="margin: 0; font-size: 16px; color: #64748b;">${device}</p>
        </div>
        
        <div style="background: #f8fafc; border-radius: 12px; padding: 16px; margin-bottom: 16px;">
            <p style="margin: 0 0 8px 0; font-size: 14px; color: #475569; line-height: 1.5;">
                ${info.description}
            </p>
            <p style="margin: 0; font-size: 13px; color: #64748b; font-style: italic;">
                💡 ${info.insights}
            </p>
        </div>
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
            <div style="background: #f8fafc; border-radius: 8px; padding: 12px; text-align: center;">
                <p style="margin: 0 0 4px 0; font-size: 12px; color: #94a3b8;">Total Klik</p>
                <p style="margin: 0; font-size: 16px; font-weight: 600; color: #0f172a;">${Math.round(1248 * (percentage / 100)).toLocaleString()}</p>
            </div>
            <div style="background: #f8fafc; border-radius: 8px; padding: 12px; text-align: center;">
                <p style="margin: 0 0 4px 0; font-size: 12px; color: #94a3b8;">CTR</p>
                <p style="margin: 0; font-size: 16px; font-weight: 600; color: #0f172a;">${(8.4 + (Math.random() - 0.5) * 2).toFixed(1)}%</p>
            </div>
        </div>
    `;
    
    modal.style.display = 'flex';
    modal.style.animation = 'modalFadeIn 0.3s ease-out';
}

/**
 * Show Metric Detail Modal
 */
function showMetricDetail(metricType) {
    const modal = document.getElementById('chartDetailModal');
    const modalContent = document.getElementById('modalContent');
    
    const metricData = {
        'total-clicks': {
            title: 'Total Klik',
            value: '1,248',
            change: '+12.5%',
            changeType: 'positive',
            icon: 'fas fa-mouse-pointer',
            color: '#3b82f6',
            description: 'Total klik yang terjadi di semua link Anda',
            insights: [
                'Peningkatan 12.5% dari minggu lalu',
                'Rata-rata 178 klik per hari',
                'Peak klik terjadi pada hari Jumat'
            ]
        },
        'active-links': {
            title: 'Link Aktif',
            value: '15',
            change: '+3',
            changeType: 'positive',
            icon: 'fas fa-link',
            color: '#10b981',
            description: 'Jumlah link yang sedang aktif dan dapat diklik',
            insights: [
                '3 link baru ditambahkan minggu ini',
                'Link terpopuler: Promo Ramadan',
                '85% link memiliki klik di atas rata-rata'
            ]
        },
        'ctr': {
            title: 'Click Rate',
            value: '8.4%',
            change: '+2.1%',
            changeType: 'positive',
            icon: 'fas fa-chart-line',
            color: '#f59e0b',
            description: 'Rasio klik terhadap pengunjung (Click Through Rate)',
            insights: [
                'CTR di atas rata-rata industri (6.8%)',
                'Mobile CTR: 9.2%, Desktop: 7.1%',
                'Link dengan gambar memiliki CTR 15% lebih tinggi'
            ]
        },
        'unique-visitors': {
            title: 'Pengunjung Unik',
            value: '892',
            change: '+8.7%',
            changeType: 'positive',
            icon: 'fas fa-users',
            color: '#ec4899',
            description: 'Jumlah pengunjung unik yang mengakses link Anda',
            insights: [
                '67% pengunjung kembali (returning visitors)',
                'Durasi rata-rata: 2 menit 34 detik',
                'Conversion rate: 3.2%'
            ]
        }
    };
    
    const data = metricData[metricType];
    
    modalContent.innerHTML = `
        <div style="text-align: center; margin-bottom: 20px;">
            <div style="width: 60px; height: 60px; background: ${data.color}; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px;">
                <i class="${data.icon}" style="font-size: 24px; color: white;"></i>
            </div>
            <h4 style="margin: 0 0 8px 0; font-size: 24px; font-weight: 700; color: #0f172a;">${data.value}</h4>
            <p style="margin: 0; font-size: 16px; color: #64748b;">${data.title}</p>
            <span style="display: inline-flex; align-items: center; gap: 4px; margin-top: 8px; padding: 4px 8px; background: ${data.changeType === 'positive' ? '#dcfce7' : '#fee2e2'}; color: ${data.changeType === 'positive' ? '#166534' : '#dc2626'}; border-radius: 12px; font-size: 12px; font-weight: 600;">
                <i class="fas fa-arrow-${data.changeType === 'positive' ? 'up' : 'down'}" style="font-size: 10px;"></i> ${data.change}
            </span>
        </div>
        
        <div style="background: #f8fafc; border-radius: 12px; padding: 16px; margin-bottom: 16px;">
            <p style="margin: 0 0 12px 0; font-size: 14px; color: #475569; line-height: 1.5;">
                ${data.description}
            </p>
            <div style="display: flex; flex-direction: column; gap: 8px;">
                ${data.insights.map(insight => `
                    <div style="display: flex; align-items: flex-start; gap: 8px;">
                        <i class="fas fa-lightbulb" style="font-size: 12px; color: #f59e0b; margin-top: 2px; flex-shrink: 0;"></i>
                        <p style="margin: 0; font-size: 13px; color: #64748b; line-height: 1.4;">${insight}</p>
                    </div>
                `).join('')}
            </div>
        </div>
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
            <div style="background: #f8fafc; border-radius: 8px; padding: 12px; text-align: center;">
                <p style="margin: 0 0 4px 0; font-size: 12px; color: #94a3b8;">Target Bulan Ini</p>
                <p style="margin: 0; font-size: 16px; font-weight: 600; color: #0f172a;">${metricType === 'total-clicks' ? '2,000' : metricType === 'active-links' ? '20' : metricType === 'ctr' ? '10%' : '1,200'}</p>
            </div>
            <div style="background: #f8fafc; border-radius: 8px; padding: 12px; text-align: center;">
                <p style="margin: 0 0 4px 0; font-size: 12px; color: #94a3b8;">Progress</p>
                <p style="margin: 0; font-size: 16px; font-weight: 600; color: #0f172a;">${metricType === 'total-clicks' ? '62%' : metricType === 'active-links' ? '75%' : metricType === 'ctr' ? '84%' : '74%'}</p>
            </div>
        </div>
    `;
    
    modal.style.display = 'flex';
    modal.style.animation = 'modalFadeIn 0.3s ease-out';
}
</script>

@endsection
