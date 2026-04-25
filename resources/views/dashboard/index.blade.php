@extends('layouts.dashboard')

@section('title', 'Dashboard | Mobay.id')

@php
    use Illuminate\Support\Str;
    $user = Auth::user();
    $isProUser = method_exists($user, 'isPro') ? $user->isPro() : in_array((string) data_get($user, 'subscription_plan'), ['pro', 'premium'], true);
    $isProActive = method_exists($user, 'isProActive') ? $user->isProActive() : $isProUser;
    $isExpiredProUser = method_exists($user, 'hasExpiredProAccess') ? $user->hasExpiredProAccess() : false;
    $publicProfileUrl = url('/' . $user->username);
    $savedAccountsCount = $user->paymentAccounts()->count();
    $primaryAccount = $user->paymentAccounts()->where('is_default', true)->first()
                    ?? $user->paymentAccounts()->first();
@endphp

@section('content')
@include('components.qr-modal')

<style>
    .quick-actions {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(185px, 1fr));
        gap: 14px;
        margin-bottom: 28px;
    }
    .quick-action-card {
        position: relative;
        overflow: hidden;
        min-height: 132px;
        padding: 18px;
        border-radius: 18px;
        color: #ffffff;
        text-decoration: none;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        gap: 18px;
        box-shadow: 0 12px 30px rgba(15, 23, 42, 0.12);
        transition: transform 0.2s ease, box-shadow 0.2s ease, filter 0.2s ease;
    }
    .quick-action-card::before {
        content: '';
        position: absolute;
        inset: auto -24px -38px auto;
        width: 110px;
        height: 110px;
        border-radius: 999px;
        background: rgba(255, 255, 255, 0.12);
    }
    .quick-action-card::after {
        content: '';
        position: absolute;
        top: -26px;
        right: -12px;
        width: 82px;
        height: 82px;
        border-radius: 999px;
        background: rgba(255, 255, 255, 0.08);
    }
    .quick-action-card:hover,
    .quick-action-card:focus-visible {
        transform: translateY(-3px);
        filter: saturate(1.05);
        box-shadow: 0 18px 36px rgba(15, 23, 42, 0.18);
    }
    .quick-action-card:focus-visible {
        outline: 3px solid rgba(255, 255, 255, 0.75);
        outline-offset: 2px;
    }
    .quick-action-top,
    .quick-action-bottom {
        position: relative;
        z-index: 1;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
    }
    .quick-action-label {
        display: inline-flex;
        align-items: center;
        width: fit-content;
        padding: 5px 10px;
        border-radius: 999px;
        background: rgba(255, 255, 255, 0.18);
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 0.04em;
        text-transform: uppercase;
    }
    .quick-action-icon {
        width: 46px;
        height: 46px;
        border-radius: 14px;
        background: rgba(255, 255, 255, 0.16);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.14);
        flex-shrink: 0;
    }
    .quick-action-icon i {
        font-size: 18px;
    }
    .quick-action-content {
        position: relative;
        z-index: 1;
        display: flex;
        flex-direction: column;
        gap: 6px;
    }
    .quick-action-title {
        margin: 0;
        font-size: 18px;
        font-weight: 800;
        line-height: 1.15;
    }
    .quick-action-desc {
        margin: 0;
        font-size: 13px;
        line-height: 1.5;
        color: rgba(255, 255, 255, 0.9);
    }
    .quick-action-arrow {
        font-size: 13px;
        opacity: 0.88;
    }
    .quick-action-links { background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%); }
    .quick-action-products { background: linear-gradient(135deg, #059669 0%, #047857 100%); }
    .quick-action-appearance { background: linear-gradient(135deg, #7c3aed 0%, #6d28d9 100%); }
    .quick-action-history { background: linear-gradient(135deg, #d97706 0%, #b45309 100%); }

@media (max-width: 768px) {
    .page-header { flex-direction: column !important; align-items: flex-start !important; }
    .page-header > div:first-child h1 { font-size: 20px !important; }
    .page-header > div:first-child > div { font-size: 13px !important; }
    .page-header button { width: 100%; justify-content: center; }
    .dashboard-top-grid { grid-template-columns: 1fr !important; }
    .balance-card { padding: 24px !important; text-align: center; }
    .balance-card > div:first-child { margin-bottom: 20px; }
    .balance-card > div:first-child > div:nth-child(2) { font-size: 36px !important; }
    .balance-card > div:last-child { justify-content: center !important; }
    .balance-card button { width: 100%; }
    .account-card { margin-top: 0 !important; }
    .chart-card { padding: 16px !important; }
    .chart-card > div:first-child { flex-direction: column; align-items: flex-start !important; gap: 8px; }
    .chart-card h3 { font-size: 15px !important; }
    .stat-card h4 { font-size: 13px !important; }
    .stat-card .stat-value { font-size: 28px !important; }
    .quick-actions { grid-template-columns: 1fr 1fr; }
    .quick-action-card { min-height: 122px; padding: 16px; }
    .quick-action-title { font-size: 16px; }
    .quick-action-desc { font-size: 12px; }
}
@media (max-width: 480px) {
    .page-header > div:first-child h1 { font-size: 18px !important; }
    .balance-card > div:first-child > div:nth-child(2) { font-size: 32px !important; }
    .stat-card { padding: 16px !important; }
    .date-range-btn { padding: 8px 12px !important; font-size: 12px !important; }
    .quick-actions { grid-template-columns: 1fr; }
}
</style>

<!-- ================= HEADER ================= -->
<div style="margin-bottom: 24px;">
            <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 8px;">
                <!-- <a href="{{ route('dashboard') }}" style="width: 36px; height: 36px; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 8px; display: flex; align-items: center; justify-content: center; text-decoration: none; transition: all 0.2s;">
                    <i class="fas fa-arrow-left" style="font-size: 14px; color: #475569;"></i>
                </a> -->
                <div>
                    <h1 style="margin: 0; font-size: 24px; font-weight: 600; color: #000000;">Dashboard</h1>
                    <p style="margin: 0; font-size: 14px; color: #797979;">Dashboard <strong>{{ $user->name }}</strong></p>
                </div>
            </div>
        </div>

@if (session('success'))
    <div style="margin-bottom:18px;padding:14px 16px;border-radius:14px;border:1px solid #bbf7d0;background:#f0fdf4;color:#166534;display:flex;align-items:center;gap:10px;">
        <i class="fas fa-circle-check"></i>
        <span style="font-size:14px;font-weight:600;">{{ session('success') }}</span>
    </div>
@endif

<!-- ================= QUICK ACTION BUTTONS ================= -->
{{-- <div class="quick-actions">
    <a href="{{ route('links.index') }}" class="quick-action-card quick-action-links">
        <div class="quick-action-top">
            <span class="quick-action-label">Akses Cepat</span>
            <span class="quick-action-icon"><i class="fas fa-link"></i></span>
        </div>
        <div class="quick-action-bottom">
            <div class="quick-action-content">
                <h3 class="quick-action-title">Kelola Link</h3>
                <p class="quick-action-desc">Atur tautan utama dan susun halaman bio kamu.</p>
            </div>
            <i class="fas fa-arrow-right quick-action-arrow"></i>
        </div>
    </a>

    <a href="{{ route('products.manage') }}" class="quick-action-card quick-action-products">
        <div class="quick-action-top">
            <span class="quick-action-label">Jualan</span>
            <span class="quick-action-icon"><i class="fas fa-box"></i></span>
        </div>
        <div class="quick-action-bottom">
            <div class="quick-action-content">
                <h3 class="quick-action-title">Produk</h3>
                <p class="quick-action-desc">Tambah, edit, dan pantau produk digital maupun fisik.</p>
            </div>
            <i class="fas fa-arrow-right quick-action-arrow"></i>
        </div>
    </a>

    <a href="{{ route('dashboard.appearance') }}" class="quick-action-card quick-action-appearance">
        <div class="quick-action-top">
            <span class="quick-action-label">Branding</span>
            <span class="quick-action-icon"><i class="fas fa-palette"></i></span>
        </div>
        <div class="quick-action-bottom">
            <div class="quick-action-content">
                <h3 class="quick-action-title">Tampilan</h3>
                <p class="quick-action-desc">Sesuaikan warna, gaya, dan nuansa profil publik.</p>
            </div>
            <i class="fas fa-arrow-right quick-action-arrow"></i>
        </div>
    </a>

    <a href="{{ url('/riwayat') }}" class="quick-action-card quick-action-history">
        <div class="quick-action-top">
            <span class="quick-action-label">Keuangan</span>
            <span class="quick-action-icon"><i class="fas fa-history"></i></span>
        </div>
        <div class="quick-action-bottom">
            <div class="quick-action-content">
                <h3 class="quick-action-title">Riwayat</h3>
                <p class="quick-action-desc">Lihat transaksi terbaru, pembayaran, dan penarikan saldo.</p>
            </div>
            <i class="fas fa-arrow-right quick-action-arrow"></i>
        </div>
    </a>
</div> --}}


<!-- ================= SALDO ================= -->
{{-- ================= SALDO CARD — ganti bagian balance-card di dashboard ================= --}}
<div class="dashboard-top-grid"
     style="display:grid;grid-template-columns:minmax(320px,1.15fr) minmax(280px,0.85fr);gap:18px;align-items:stretch;margin-bottom:32px;">
<div class="balance-card"
     style="position:relative;overflow:hidden;border-radius:20px;padding:20px 22px;
             background:linear-gradient(135deg, #1a3fa8 0%, #2356e8 45%, #3b82f6 100%);
             color:#ffffff;box-shadow:0 12px 40px rgba(35,86,232,.35);display:flex;">

    {{-- Dekorasi lingkaran kanan --}}
    <div style="position:absolute;top:-40px;right:-40px;width:200px;height:200px;
                border-radius:50%;background:rgba(255,255,255,.08);pointer-events:none;"></div>
    <div style="position:absolute;top:20px;right:40px;width:130px;height:130px;
                border-radius:50%;background:rgba(255,255,255,.06);pointer-events:none;"></div>
    <div style="position:absolute;bottom:-50px;right:80px;width:100px;height:100px;
                border-radius:50%;background:rgba(255,255,255,.05);pointer-events:none;"></div>

    {{-- Isi card --}}
    <div style="position:relative;z-index:1;display:flex;flex-direction:column;gap:12px;justify-content:space-between;min-height:100%;width:100%;">

        <div style="display:flex;align-items:center;justify-content:space-between;gap:10px;">
            <div style="font-size:13px;font-weight:700;opacity:.9;letter-spacing:.03em;text-transform:uppercase;">
                {{ $primaryAccount ? 'Saldo Tersedia' : 'Saldo Mobay.id' }}
            </div>
            <span style="padding:5px 10px;border-radius:999px;background:rgba(255,255,255,.18);
                         font-size:11px;font-weight:700;white-space:nowrap;">
                Real-time
            </span>
        </div>

        {{-- Saldo --}}
        <div style="margin-bottom:4px;">
            <div style="font-size:52px;font-weight:800;line-height:.96;
                        font-family:'Plus Jakarta Sans',sans-serif;letter-spacing:-1px;">
                Rp {{ number_format($balance ?? 0, 0, ',', '.') }}
            </div>
            <div style="margin-top:8px;font-size:12px;opacity:.86;">
                
            </div>
        </div>

        <div style="display:flex;gap:8px;flex-wrap:wrap;">
            <div style="padding:7px 10px;border-radius:999px;background:rgba(255,255,255,.12);font-size:12px;font-weight:600;">
                Saldo siap ditarik
            </div>
            <div style="padding:7px 10px;border-radius:999px;background:rgba(255,255,255,.12);font-size:12px;font-weight:600;">
                Proses cepat
            </div>
        </div>

        {{-- Tombol tarik saldo --}}
        <div style="margin-top:0;display:flex;gap:10px;flex-wrap:wrap;">
            <button id="btn-withdraw" onclick="openWithdrawModal()"
                    style="display:inline-flex;align-items:center;justify-content:center;gap:8px;flex:1;min-width:150px;
                           padding:12px 18px;border-radius:12px;border:none;cursor:pointer;
                           font-size:14px;font-weight:800;font-family:'Plus Jakarta Sans',sans-serif;
                           background:#ffffff;color:#1a3fa8;
                           box-shadow:0 6px 20px rgba(0,0,0,.18);
                           transition:transform .15s,box-shadow .15s;white-space:nowrap;"
                    onmouseenter="this.style.transform='translateY(-1px)';this.style.boxShadow='0 8px 24px rgba(0,0,0,.22)'"
                    onmouseleave="this.style.transform='';this.style.boxShadow='0 6px 20px rgba(0,0,0,.18)'">
                <i class="fas fa-arrow-up" style="font-size:13px;"></i>
                Tarik Saldo
            </button>
            <a href="{{ url('/riwayat') }}"
               style="display:inline-flex;align-items:center;justify-content:center;gap:8px;flex:1;min-width:150px;
                      padding:12px 18px;border-radius:12px;border:1px solid rgba(255,255,255,.4);
                      color:#fff;text-decoration:none;font-size:14px;font-weight:700;background:rgba(255,255,255,.08);">
                <i class="fas fa-clock-rotate-left" style="font-size:12px;"></i>
                Riwayat
            </a>
        </div>
    </div>
</div>

<div class="account-card"
     style="background:#ffffff;border-radius:20px;padding:22px;border:1px solid #e5e7eb;
            box-shadow:0 10px 28px rgba(0,0,0,.05);display:flex;flex-direction:column;justify-content:space-between;">
    <div>
        <div style="display:flex;align-items:center;justify-content:space-between;gap:10px;margin-bottom:14px;">
            <div style="display:flex;align-items:center;gap:10px;min-width:0;">
                <div style="width:38px;height:38px;border-radius:12px;background:#eaf2ff;color:#1d4ed8;
                            display:flex;align-items:center;justify-content:center;font-size:15px;font-weight:800;flex-shrink:0;">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <div style="min-width:0;">
                    <div style="font-size:15px;font-weight:800;color:#111827;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                        {{ $user->name }}
                    </div>
                    <div style="font-size:12px;color:#6b7280;">{{ '@' . $user->username }}</div>
                </div>
            </div>
            <!-- <span style="padding:5px 10px;border-radius:999px;background:#ecfdf5;color:#047857;
                         font-size:11px;font-weight:700;white-space:nowrap;">
                {{ ($activeLinks ?? 0) > 0 ? 'Akun Aktif' : 'Belum Aktif' }}
            </span> -->
        </div>

        <div style="display:grid;gap:10px;margin-bottom:14px;">
            <div style="padding:11px 12px;background:{{ $isProUser ? '#ecfdf5' : '#fffaf3' }};border:1px solid {{ $isProUser ? '#bbf7d0' : '#fde3b0' }};border-radius:12px;">
                <div style="display:flex;align-items:center;justify-content:space-between;gap:10px;flex-wrap:wrap;">
                    <div>
                        <div style="font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.06em;">Status Langganan</div>
                        <div style="font-size:15px;font-weight:800;color:#111827;margin-top:4px;">
                            {{ $isProActive ? 'Pro Aktif' : ($isExpiredProUser ? 'Pro Habis' : 'Free') }}
                        </div>
                        <div style="font-size:12px;color:#6b7280;margin-top:3px;">
                            @if ($isProActive && $user->pro_until)
                                Berlaku sampai {{ $user->pro_until->format('d M Y H:i') }}.
                            @elseif ($isExpiredProUser && $user->pro_until)
                                Masa Pro berakhir pada {{ $user->pro_until->format('d M Y H:i') }}. Akun tidak bisa kembali ke Free.
                            @else
                                Mode default akun saat ini Free.
                            @endif
                        </div>
                    </div>

                    @if (!$isProActive)
                        <form method="POST" action="{{ route('dashboard.subscription.testing-activate') }}">
                            @csrf
                            <button type="submit"
                                    style="display:inline-flex;align-items:center;justify-content:center;gap:6px;padding:8px 11px;border:1px solid #bfdbfe;border-radius:999px;background:#eff6ff;color:#1d4ed8;font-size:11px;font-weight:700;cursor:pointer;white-space:nowrap;box-shadow:none;">
                                <i class="fas fa-flask"></i>
                                Aktifkan Pro Testing
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            <div style="padding:11px 12px;background:#f8fafc;border:1px solid #eef2f7;border-radius:12px;">
                <div style="font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.06em;">Link Publik</div>
                <a href="{{ $publicProfileUrl }}" target="_blank"
                   style="display:block;font-size:13px;font-weight:600;color:#2563eb;text-decoration:none;margin-top:3px;word-break:break-all;">
                    {{ $publicProfileUrl }}
                </a>
            </div>
            <div style="display:grid;grid-template-columns:repeat(3, 1fr);gap:8px;">
                {{-- ✅ DIUBAH: Total Link → Total Pesanan --}}
                <div style="padding:10px;border-radius:12px;background:#f8fafc;border:1px solid #eef2f7;">
                    <div style="font-size:11px;color:#6b7280;font-weight:700;">Total Pesanan</div>
                    <div style="font-size:13px;font-weight:800;color:#0f172a;margin-top:2px;">{{ $totalOrders ?? 0 }}</div>
                </div>
                {{-- ✅ DIUBAH: Link Aktif → Total Produk --}}
                <div style="padding:10px;border-radius:12px;background:#f8fafc;border:1px solid #eef2f7;">
                    <div style="font-size:11px;color:#6b7280;font-weight:700;">Total Produk</div>
                    <div style="font-size:13px;font-weight:800;color:#0f172a;margin-top:2px;">{{ $totalProducts ?? 0 }}</div>
                </div>
                <div style="padding:10px;border-radius:12px;background:#f8fafc;border:1px solid #eef2f7;">
                    <div style="font-size:11px;color:#6b7280;font-weight:700;">Rekening</div>
                    <div style="font-size:13px;font-weight:800;color:#0f172a;margin-top:2px;">{{ $savedAccountsCount }}</div>
                </div>
            </div>
        </div>
    </div>

    <div style="display:flex;gap:10px;flex-wrap:wrap;">
        <button type="button" onclick="openQRModal('{{ $user->username }}')"
                style="flex:1;min-width:130px;display:inline-flex;align-items:center;justify-content:center;gap:8px;
                       border:none;border-radius:12px;padding:11px 12px;background:#2563eb;color:#fff;
                       font-size:13px;font-weight:700;cursor:pointer;">
            <i class="fas fa-share-alt"></i> Bagikan
        </button>
        <button type="button" onclick="copyPublicProfileLink('{{ $publicProfileUrl }}')"
                style="flex:1;min-width:130px;display:inline-flex;align-items:center;justify-content:center;gap:8px;
                       border:1px solid #d1d5db;border-radius:12px;padding:11px 12px;background:#fff;color:#1f2937;
                       font-size:13px;font-weight:700;cursor:pointer;">
            <i class="fas fa-copy"></i> Salin Link
        </button>
    </div>
</div>
</div>

<!-- ================= NOTIFICATIONS & ALERTS ================= -->
<div id="notificationsContainer" style="margin-bottom:32px;">
    <!-- STORAGE WARNING -->
    @if($storageInfo['percentage'] > 80)
    <div style="background:linear-gradient(135deg, #fef3c7 0%, #fcd34d 100%);border-radius:14px;border-left:4px solid #f59e0b;
                padding:16px 20px;margin-bottom:12px;display:flex;align-items:center;justify-content:space-between;gap:16px;">
        <div style="display:flex;align-items:center;gap:12px;flex:1;min-width:0;">
            <div style="width:40px;height:40px;background:rgba(245, 158, 11, 0.1);border-radius:10px;
                        display:flex;align-items:center;justify-content:center;font-size:20px;color:#f59e0b;flex-shrink:0;">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <div style="min-width:0;">
                <p style="margin:0 0 4px;font-size:13px;font-weight:700;color:#78350f;">Penyimpanan Hampir Penuh!</p>
                <p style="margin:0;font-size:12px;color:#92400e;">{{ $storageInfo['percentage'] }}% dari {{ $storageInfo['limit_formatted'] }} sudah terpakai</p>
            </div>
        </div>
        <button onclick="document.getElementById('storageCard').click()"
                style="padding:8px 14px;background:#f59e0b;color:#ffffff;border:none;border-radius:8px;
                       font-size:12px;font-weight:600;cursor:pointer;white-space:nowrap;flex-shrink:0;">
            Upgrade
        </button>
    </div>
    @endif

    <!-- PRO UPGRADE RECOMMENDATION -->
    @if(!$isProUser)
    <div style="background:linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);border-radius:14px;border-left:4px solid #2563eb;
                padding:16px 20px;margin-bottom:12px;display:flex;align-items:center;justify-content:space-between;gap:16px;">
        <div style="display:flex;align-items:center;gap:12px;flex:1;min-width:0;">
            <div style="width:40px;height:40px;background:rgba(37, 99, 235, 0.1);border-radius:10px;
                        display:flex;align-items:center;justify-content:center;font-size:20px;color:#2563eb;flex-shrink:0;">
                <i class="fas fa-crown"></i>
            </div>
            <div style="min-width:0;">
                <p style="margin:0 0 4px;font-size:13px;font-weight:700;color:#1e40af;">Tingkatkan ke Pro 🚀</p>
                <p style="margin:0;font-size:12px;color:#1e40af;">Dapatkan penyimpanan 5x lebih besar & fitur premium lainnya</p>
            </div>
        </div>
        <a href="{{ route('dashboard.appearance') }}"
           style="padding:8px 14px;background:#2563eb;color:#ffffff;border:none;border-radius:8px;
                  font-size:12px;font-weight:600;cursor:pointer;white-space:nowrap;text-decoration:none;
                  display:flex;align-items:center;gap:5px;flex-shrink:0;">
            <i class="fas fa-arrow-right" style="font-size:10px;"></i> Lihat Paket
        </a>
    </div>
    @endif

    <!-- RECENT ACTIVITY ALERT -->
    @php
        $now = \Carbon\Carbon::now();
        $recentActivity = $now->diffInHours(\Carbon\Carbon::now()->subHours(24)) < 24;
    @endphp
    @if(!$recentActivity && array_sum($data) > 0)
    <div style="background:linear-gradient(135deg, #e9d5ff 0%, #d8b4fe 100%);border-radius:14px;border-left:4px solid #7c3aed;
                padding:16px 20px;display:flex;align-items:center;justify-content:space-between;gap:16px;">
        <div style="display:flex;align-items:center;gap:12px;flex:1;min-width:0;">
            <div style="width:40px;height:40px;background:rgba(124, 58, 237, 0.1);border-radius:10px;
                        display:flex;align-items:center;justify-content:center;font-size:20px;color:#7c3aed;flex-shrink:0;">
                <i class="fas fa-info-circle"></i>
            </div>
            <div style="min-width:0;">
                <p style="margin:0 0 4px;font-size:13px;font-weight:700;color:#581c87;">Tidak ada aktivitas 24 jam terakhir</p>
                <p style="margin:0;font-size:12px;color:#6b21a8;">Coba bagikan link profil Anda untuk mendapatkan visitors baru</p>
            </div>
        </div>
        <button onclick="openQRModal('{{ $user->username }}')"
                style="padding:8px 14px;background:#7c3aed;color:#ffffff;border:none;border-radius:8px;
                       font-size:12px;font-weight:600;cursor:pointer;white-space:nowrap;flex-shrink:0;">
            Bagikan
        </button>
    </div>
    @endif
</div>

<style>
@media (max-width: 640px) {
    .balance-card {
        padding: 18px 16px !important;
    }
    .balance-card [style*="font-size:52px"] {
        font-size: 40px !important;
    }
    .account-card {
        padding: 18px !important;
    }
    .account-card [style*="grid-template-columns:repeat(3, 1fr)"] {
        grid-template-columns: 1fr !important;
    }
    .balance-card [style*="font-size:15px"][style*="font-weight:700"] {
        font-size: 13px !important;
    }
    #btn-withdraw {
        width: 100%;
        justify-content: center;
    }
</style>

<!-- ================= CHART ================= -->
<div class="chart-card"
     style="background:#ffffff;border-radius:18px;padding:24px;margin-bottom:32px;box-shadow:0 10px 30px rgba(0,0,0,.06);">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;flex-wrap:wrap;gap:12px;">
        <div>
            <h3 style="font-size:16px;font-weight:700;margin:0 0 8px;">Total Views & Clicks</h3>
            <div style="display:flex;gap:20px;">
                <div style="display:flex;align-items:center;gap:8px;">
                    <span title="Jumlah pengunjung yang membuka halaman profilmu" 
                        style="width:12px;height:12px;border-radius:50%;background:#f59e0b;display:inline-block;cursor:help;"></span>
                    <span title="Jumlah pengunjung yang membuka halaman profilmu"
                        style="font-size:13px;color:#6b7280;cursor:help;border-bottom:1px dashed #d1d5db;">Views</span>
                    <span style="font-size:22px;font-weight:800;color:#111;" id="total-views-label">{{ number_format(array_sum($data)) }}</span>
                </div>
                <div style="display:flex;align-items:center;gap:8px;">
                    <span title="Jumlah pengunjung yang mengklik produk di halamanmu"
      style="width:12px;height:12px;border-radius:50%;background:#2563eb;display:inline-block;cursor:help;"></span>
<span title="Jumlah pengunjung yang mengklik produk di halamanmu"
      style="font-size:13px;color:#6b7280;cursor:help;border-bottom:1px dashed #d1d5db;">Clicks</span>
                    <span style="font-size:22px;font-weight:800;color:#111;" id="total-click-label">{{ number_format(array_sum($clicksData)) }}</span>
                </div>
            </div>
        </div>
        <div style="display:flex;align-items:center;gap:8px;position:relative;">
            <div style="display:flex;align-items:center;gap:8px;padding:10px 14px;border:1.5px solid #e5e7eb;
                        border-radius:12px;background:#f9fafb;cursor:pointer;font-size:13px;color:#374151;"
                 onclick="toggleDatePicker()">
                <i class="fas fa-calendar-alt" style="color:#6b7280;"></i>
                <span id="date-range-label">7 Hari Terakhir</span>
                <i class="fas fa-chevron-down" style="color:#6b7280;font-size:11px;"></i>
            </div>
            <div id="date-picker-dropdown"
                 style="display:none;position:absolute;top:calc(100% + 8px);right:0;background:#fff;
                        border:1.5px solid #e5e7eb;border-radius:14px;padding:8px;
                        box-shadow:0 10px 40px rgba(0,0,0,.12);z-index:100;min-width:200px;">
                <div onclick="selectRange(7, '7 Hari Terakhir')" class="range-option" style="padding:10px 14px;border-radius:8px;cursor:pointer;font-size:13px;">7 Hari Terakhir</div>
                <div onclick="selectRange(14, '14 Hari Terakhir')" class="range-option" style="padding:10px 14px;border-radius:8px;cursor:pointer;font-size:13px;">14 Hari Terakhir</div>
                <div onclick="selectRange(30, '30 Hari Terakhir')" class="range-option" style="padding:10px 14px;border-radius:8px;cursor:pointer;font-size:13px;">30 Hari Terakhir</div>
                <hr style="margin:6px 0;border:none;border-top:1px solid #f3f4f6;">
                <div style="padding:10px 14px;font-size:13px;color:#374151;font-weight:600;">Pilih Tanggal:</div>
                <div style="padding:0 14px 10px;display:flex;flex-direction:column;gap:8px;">
                    <input type="date" id="date-start" style="padding:8px;border:1.5px solid #e5e7eb;border-radius:8px;font-size:12px;width:100%;">
                    <input type="date" id="date-end" style="padding:8px;border:1.5px solid #e5e7eb;border-radius:8px;font-size:12px;width:100%;">
                    <button onclick="applyCustomRange()" style="padding:8px;background:#2563eb;color:#fff;border:none;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;">Terapkan</button>
                </div>
            </div>
        </div>
    </div>
    <div style="height:260px;width:100%;">
        <canvas id="clickChart"></canvas>
    </div>
</div>

<!-- ================= JS CHART ================= -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
let chartInstance = null;
const initialLabels = @json($labels);
const initialViews  = @json($data);
const initialClicks = @json($clicksData);

async function copyPublicProfileLink(link) {
    try {
        if (navigator.clipboard?.writeText) {
            await navigator.clipboard.writeText(link);
        } else {
            const tempInput = document.createElement('input');
            tempInput.value = link;
            document.body.appendChild(tempInput);
            tempInput.select();
            document.execCommand('copy');
            tempInput.remove();
        }
        if (typeof wdToast === 'function') {
            wdToast('success', 'Link profil berhasil disalin.');
        } else {
            alert('Link profil berhasil disalin.');
        }
    } catch (e) {
        if (typeof wdToast === 'function') {
            wdToast('error', 'Gagal menyalin link. Coba lagi.');
        } else {
            alert('Gagal menyalin link. Coba lagi.');
        }
    }
}

function buildChart(labels, views, clicks) {
    const ctx = document.getElementById('clickChart').getContext('2d');
    if (chartInstance) chartInstance.destroy();
    chartInstance = new Chart(ctx, {
        type: 'bar',
        data: {
            labels,
            datasets: [
                { label:'Views', data:views, backgroundColor:'#f59e0b', borderRadius:6, barPercentage:0.5, categoryPercentage:0.6 },
                { label:'Klik Produk', data:clicks, backgroundColor:'#2563eb', borderRadius:6, barPercentage:0.5, categoryPercentage:0.6 }
            ]
        },
        options: {
            responsive:true, maintainAspectRatio:false,
            plugins: { legend:{display:false}, tooltip:{callbacks:{label:ctx=>` ${ctx.dataset.label}: ${ctx.parsed.y}`}} },
            scales: { y:{beginAtZero:true,ticks:{precision:0},grid:{color:'#f3f4f6'}}, x:{grid:{display:false}} }
        }
    });
}

document.addEventListener('DOMContentLoaded', () => buildChart(initialLabels, initialViews, initialClicks));

function toggleDatePicker() {
    const dd = document.getElementById('date-picker-dropdown');
    dd.style.display = dd.style.display === 'none' ? 'block' : 'none';
}

document.addEventListener('click', e => {
    const dd = document.getElementById('date-picker-dropdown');
    if (!e.target.closest('#date-picker-dropdown') && !e.target.closest('[onclick="toggleDatePicker()"]'))
        dd.style.display = 'none';
});

function selectRange(days, label) {
    document.getElementById('date-range-label').textContent = label;
    document.getElementById('date-picker-dropdown').style.display = 'none';
    fetchChartData(days, null, null);
}

function applyCustomRange() {
    const start = document.getElementById('date-start').value;
    const end   = document.getElementById('date-end').value;
    if (!start || !end) return alert('Pilih tanggal mulai dan akhir');
    document.getElementById('date-range-label').textContent = `${start} – ${end}`;
    document.getElementById('date-picker-dropdown').style.display = 'none';
    fetchChartData(null, start, end);
}

async function fetchChartData(days, start, end) {
    const params = new URLSearchParams();
    if (days)  params.append('days', days);
    if (start) params.append('start', start);
    if (end)   params.append('end', end);
    try {
        const res  = await fetch(`/dashboard/chart-data?${params}`);
        const json = await res.json();
        buildChart(json.labels, json.views, json.clicks);
        document.getElementById('total-views-label').textContent  = json.total_views.toLocaleString('id-ID');
        document.getElementById('total-click-label').textContent  = json.total_clicks.toLocaleString('id-ID');
    } catch(e) { console.error(e); }
}

document.querySelectorAll('.range-option').forEach(el => {
    el.addEventListener('mouseenter', () => el.style.background = '#f3f4f6');
    el.addEventListener('mouseleave', () => el.style.background = 'transparent');
});
</script>

<!-- ================= STORAGE INFO — CLICKABLE ================= -->
<div id="storageCard" style="background:#ffffff;border-radius:18px;padding:24px;margin-bottom:32px;box-shadow:0 10px 30px rgba(0,0,0,.06);cursor:pointer;transition:all 0.3s ease;">
    <div style="display:flex;align-items:center;gap:12px;margin-bottom:16px;">
        <i class="fas fa-database" style="font-size:20px;color:#2563eb;"></i>
        <h3 style="font-size:16px;font-weight:700;margin:0;color:#111827;">Penyimpanan</h3>
    </div>
    
    <!-- Storage Usage -->
    <div style="margin-bottom:16px;">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px;">
            <span style="font-size:13px;color:#6b7280;font-weight:600;">
                Penggunaan Penyimpanan
            </span>
            <span style="font-size:13px;font-weight:700;color:#2563eb;">{{ $storageInfo['percentage'] }}%</span>
        </div>
        <div style="width:100%;background:#e5e7eb;border-radius:999px;height:8px;overflow:hidden;">
            <div style="background:linear-gradient(90deg, #2563eb, #3b82f6);height:100%;transition:width 0.3s ease;width:{{ min($storageInfo['percentage'], 100) }}%;"></div>
        </div>
    </div>

    <!-- Available Storage -->
    <div style="background:#ecf0ff;border:1px solid #bfdbfe;border-radius:12px;padding:12px 14px;margin-bottom:12px;">
        <p style="font-size:13px;color:#1e40af;margin:0;">
            <span style="font-weight:700;">{{ $storageInfo['percentage'] < 100 ? 'Masih ada ruang' : 'Penyimpanan penuh' }}</span> untuk upload
        </p>
    </div>

    <!-- Plan Info -->
    <div style="border-top:1px solid #e5e7eb;padding-top:12px;">
        <p style="font-size:12px;color:#6b7280;margin:0 0 6px;font-weight:600;">
            @if($storageInfo['plan'] === 'Pro')
                <i class="fas fa-crown" style="color:#fbbf24;margin-right:4px;"></i> Akun Pro
            @else
                <i class="fas fa-rocket" style="color:#ec4899;margin-right:4px;"></i> Akun Free
            @endif
        </p>
        <p style="font-size:12px;color:#6b7280;margin:0;line-height:1.5;">
            @if($storageInfo['plan'] === 'Free')
                <span style="font-weight:700;color:#ec4899;">👉 Klik untuk upgrade</span> ke Pro dan dapatkan penyimpanan 5x lebih besar!
            @else
                Anda menggunakan akun Pro dengan penyimpanan 5x lebih besar dari Free. Nikmati upload dan manajemen file tanpa khawatir!
            @endif
        </p>
    </div>
</div>

<script>
const isProUser = @json($isProUser);
const storageCard = document.getElementById('storageCard');

if (!isProUser) {
    storageCard.addEventListener('click', () => {
        const modal = document.getElementById('upgradeModal');
        const overlay = document.getElementById('upgradeOverlay');
        if (modal && overlay) {
            modal.style.display = 'flex';
            setTimeout(() => {
                modal.style.opacity = '1';
                modal.style.transform = 'translateY(0)';
                overlay.style.opacity = '1';
                overlay.style.pointerEvents = 'auto';
            }, 10);
        }
    });
    
    storageCard.addEventListener('mouseenter', function() {
        this.style.background = '#f0f9ff';
        this.style.boxShadow = '0 10px 40px rgba(37, 99, 235, 0.15)';
        this.style.transform = 'translateY(-2px)';
    });
    
    storageCard.addEventListener('mouseleave', function() {
        this.style.background = '#ffffff';
        this.style.boxShadow = '0 10px 30px rgba(0,0,0,.06)';
        this.style.transform = 'translateY(0)';
    });
}
</script>

@endsection

{{-- ============================================================
     MODAL WITHDRAW — IMPROVED VERSION
     - Breakdown amount, fee, total potongan
     - Tombol custom nominal
============================================================ --}}
{{-- ============================================================
     MODAL WITHDRAW — IMPROVED VERSION
     - Breakdown amount, fee, total potongan
     - Tombol custom nominal
     - Keterangan jumlah yang akan ditarik
============================================================ --}}
@push('modals')

<!-- ================= UPGRADE MODAL OVERLAY ================= -->
<div id="upgradeOverlay"
     style="position:fixed;inset:0;z-index:8990;background:rgba(15,23,42,0.5);
            backdrop-filter:blur(5px);-webkit-backdrop-filter:blur(5px);
            opacity:0;pointer-events:none;transition:opacity 0.22s ease;" 
     onclick="closeUpgradeModal()">
</div>

<!-- ================= UPGRADE MODAL ================= -->
<div id="upgradeModal"
     style="position:fixed;inset:0;z-index:8999;display:none;
            align-items:center;justify-content:center;padding:16px;">
    <div style="background:#ffffff;width:100%;max-width:500px;max-height:90vh;
                border-radius:20px;box-shadow:0 24px 64px rgba(0,0,0,.22);
                overflow:hidden;display:flex;flex-direction:column;
                opacity:0;transform:translateY(20px);
                transition:opacity 0.22s ease,transform 0.22s ease;">
        
        <!-- Header dengan background gradient -->
        <div style="background:linear-gradient(135deg, #2563eb 0%, #3b82f6 100%);
                    padding:32px 28px 24px;color:#ffffff;position:relative;overflow:hidden;">
            <div style="position:absolute;top:-40px;right:-40px;width:200px;height:200px;
                        border-radius:50%;background:rgba(255,255,255,.08);"></div>
            <div style="position:absolute;bottom:-30px;left:-40px;width:150px;height:150px;
                        border-radius:50%;background:rgba(255,255,255,.06);"></div>
            
            <div style="position:relative;z-index:1;">
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;">
                    <div style="display:flex;align-items:center;gap:12px;">
                        <div style="width:48px;height:48px;background:rgba(255,255,255,.2);
                                    border-radius:14px;display:flex;align-items:center;justify-content:center;
                                    font-size:24px;">
                            <i class="fas fa-rocket"></i>
                        </div>
                        <h2 style="margin:0;font-size:24px;font-weight:800;">Upgrade ke Pro</h2>
                    </div>
                    <button onclick="closeUpgradeModal()"
                            style="border:none;background:rgba(255,255,255,.2);color:#ffffff;
                                   width:36px;height:36px;border-radius:10px;font-size:18px;cursor:pointer;
                                   display:flex;align-items:center;justify-content:center;
                                   transition:background 0.2s;padding:0;"
                            onmouseenter="this.style.background='rgba(255,255,255,.3)'"
                            onmouseleave="this.style.background='rgba(255,255,255,.2)'">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <p style="margin:0;font-size:14px;opacity:.95;">Dapatkan penyimpanan 5x lebih besar dan fitur premium lainnya</p>
            </div>
        </div>
        
        <!-- Body -->
        <div style="padding:28px;flex:1;overflow-y:auto;">
            <div style="margin-bottom:28px;">
                <h3 style="font-size:14px;font-weight:700;color:#374151;margin:0 0 16px;text-transform:uppercase;letter-spacing:0.05em;">
                    Keuntungan Akun Pro
                </h3>
                <div style="display:flex;flex-direction:column;gap:12px;">
                    <div style="display:flex;align-items:center;gap:12px;padding:12px;background:#f0f9ff;border-radius:10px;border:1px solid #bfdbfe;">
                        <i class="fas fa-database" style="color:#2563eb;font-size:16px;flex-shrink:0;"></i>
                        <div>
                            <p style="margin:0;font-size:13px;font-weight:600;color:#1e40af;">Penyimpanan 50 MB</p>
                            <p style="margin:2px 0 0;font-size:12px;color:#6b7280;">5x lebih besar dari Akun Free</p>
                        </div>
                    </div>
                    <div style="display:flex;align-items:center;gap:12px;padding:12px;background:#f0f9ff;border-radius:10px;border:1px solid #bfdbfe;">
                        <i class="fas fa-crown" style="color:#fbbf24;font-size:16px;flex-shrink:0;"></i>
                        <div>
                            <p style="margin:0;font-size:13px;font-weight:600;color:#1e40af;">Fitur Premium</p>
                            <p style="margin:2px 0 0;font-size:12px;color:#6b7280;">Akses ke semua fitur eksklusif</p>
                        </div>
                    </div>
                    <div style="display:flex;align-items:center;gap:12px;padding:12px;background:#f0f9ff;border-radius:10px;border:1px solid #bfdbfe;">
                        <i class="fas fa-zap" style="color:#f59e0b;font-size:16px;flex-shrink:0;"></i>
                        <div>
                            <p style="margin:0;font-size:13px;font-weight:600;color:#1e40af;">Prioritas Support</p>
                            <p style="margin:2px 0 0;font-size:12px;color:#6b7280;">Support lebih cepat dan responsif</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Action Buttons -->
        <div style="padding:20px 28px;border-top:1px solid #e5e7eb;background:#f9fafb;display:flex;gap:10px;">
            <button onclick="closeUpgradeModal()"
                    style="flex:1;padding:12px 16px;border:1.5px solid #e5e7eb;background:#ffffff;
                           border-radius:10px;font-size:13px;font-weight:600;color:#6b7280;
                           cursor:pointer;transition:all 0.2s;font-family:'Plus Jakarta Sans',sans-serif;"
                    onmouseenter="this.style.background='#f3f4f6';this.style.borderColor='#d1d5db'"
                    onmouseleave="this.style.background='#ffffff';this.style.borderColor='#e5e7eb'">
                Nanti dulu
            </button>
            <a href="{{ route('dashboard.appearance') }}"
               style="flex:1;padding:12px 16px;background:linear-gradient(135deg, #2563eb 0%, #3b82f6 100%);
                      border-radius:10px;font-size:13px;font-weight:700;color:#ffffff;cursor:pointer;
                      text-decoration:none;display:flex;align-items:center;justify-content:center;gap:8px;
                      transition:all 0.2s;border:none;font-family:'Plus Jakarta Sans',sans-serif;">
                <i class="fas fa-star" style="font-size:12px;"></i>
                Upgrade Sekarang
            </a>
        </div>
    </div>
</div>

<script>
function closeUpgradeModal() {
    const modal = document.getElementById('upgradeModal');
    const overlay = document.getElementById('upgradeOverlay');
    if (modal && overlay) {
        modal.style.opacity = '0';
        modal.style.transform = 'translateY(20px)';
        overlay.style.opacity = '0';
        overlay.style.pointerEvents = 'none';
        setTimeout(() => {
            modal.style.display = 'none';
        }, 220);
    }
}

// Show modal animation
const originalOpenUpgrade = function() {
    const modal = document.getElementById('upgradeModal');
    const overlay = document.getElementById('upgradeOverlay');
    if (modal && overlay) {
        modal.style.display = 'flex';
        setTimeout(() => {
            modal.style.opacity = '1';
            modal.style.transform = 'translateY(0)';
            overlay.style.opacity = '1';
        }, 10);
    }
};

// Override click handler to show animation
const storageCardClick = document.getElementById('storageCard');
if (storageCardClick && !isProUser) {
    storageCardClick.removeEventListener('click', () => {});
    storageCardClick.addEventListener('click', originalOpenUpgrade);
}
</script>

{{-- OVERLAY --}}
<div id="withdrawOverlay"
     style="position:fixed;inset:0;z-index:9990;background:rgba(15,23,42,0.5);
            backdrop-filter:blur(5px);-webkit-backdrop-filter:blur(5px);
            opacity:0;pointer-events:none;transition:opacity 0.22s ease;">
</div>

{{-- MODAL --}}
<div id="withdraw-modal"
     style="position:fixed;inset:0;z-index:9999;display:none;
            align-items:center;justify-content:center;padding:16px;">

    <div id="withdrawCard"
         style="background:#ffffff;width:100%;max-width:520px;max-height:92vh;
                border-radius:18px;box-shadow:0 24px 64px rgba(0,0,0,.22);
                overflow:hidden;display:flex;flex-direction:column;
                opacity:0;transform:translateY(20px);
                transition:opacity 0.22s ease,transform 0.22s ease;">

        {{-- ── HEADER ─────────────────────────────────────────────── --}}
        <div style="padding:18px 22px;border-bottom:1px solid #f1f5f9;
                    display:flex;align-items:center;justify-content:space-between;flex-shrink:0;">
            <div style="display:flex;align-items:center;gap:10px;">
                <div style="width:36px;height:36px;border-radius:10px;background:#e8f0fe;
                            display:flex;align-items:center;justify-content:center;color:#2356e8;">
                    <i class="fas fa-arrow-up" style="font-size:14px;"></i>
                </div>
                <div>
                    <h3 style="margin:0;font-size:17px;font-weight:700;color:#0f172a;">Tarik Saldo</h3>
                    <p style="margin:0;font-size:12px;color:#94a3b8;">Minimal penarikan Rp {{ number_format(config('xendit.withdrawal.min_amount', 20000), 0, ',', '.') }}</p>
                </div>
            </div>
            <button onclick="closeWithdrawModal()"
                    style="border:none;background:none;cursor:pointer;color:#94a3b8;
                           width:32px;height:32px;border-radius:8px;font-size:20px;
                           display:flex;align-items:center;justify-content:center;
                           transition:background .15s;"
                    onmouseenter="this.style.background='#f1f5f9'"
                    onmouseleave="this.style.background='none'">
                <i class="fas fa-xmark"></i>
            </button>
        </div>

        {{-- ── BODY ───────────────────────────────────────────────── --}}
        <div style="padding:22px;overflow-y:auto;flex:1;">

            {{-- Saldo info --}}
            <div style="background:linear-gradient(135deg,#1e40af,#2563eb);border-radius:14px;
                        padding:16px 20px;margin-bottom:20px;color:#fff;">
                <div style="font-size:12px;opacity:.8;margin-bottom:4px;">Saldo Tersedia</div>
                <div style="font-size:26px;font-weight:800;font-family:'Plus Jakarta Sans',sans-serif;">
                    Rp <span id="wd-balance-display">{{ number_format($balance ?? 0,0,',','.') }}</span>
                </div>
            </div>

            {{-- ── Pilih Rekening ─────────────────────────────────── --}}
            <div style="margin-bottom:18px;">
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:10px;">
                    <label style="font-size:12px;font-weight:700;color:#556070;
                                  text-transform:uppercase;letter-spacing:.05em;">
                        <i class="fas fa-building-columns" style="color:#2356e8;margin-right:4px;"></i>
                        Rekening Tujuan
                    </label>
                    <a href="{{ route('payment.accounts.index') }}"
                       style="font-size:12px;font-weight:600;color:#2356e8;
                              text-decoration:none;display:flex;align-items:center;gap:5px;
                              padding:5px 10px;background:#e8f0fe;border-radius:99px;
                              border:1px solid #c7d8ff;transition:background .15s;"
                       onmouseenter="this.style.background='#dbeafe'"
                       onmouseleave="this.style.background='#e8f0fe'">
                        <i class="fas fa-plus" style="font-size:10px;"></i>
                        Kelola Rekening
                    </a>
                </div>

                @php
                    $savedAccounts = auth()->user()->paymentAccounts()
                        ->orderByDesc('is_default')
                        ->orderBy('created_at')
                        ->get();
                @endphp

                @if($savedAccounts->isNotEmpty())
                    <div id="wd-account-list" style="display:flex;flex-direction:column;gap:8px;">
                        @foreach($savedAccounts as $acc)
                            @php
                                $decryptedNumber = $acc->account_number_encrypted;
                                $masked = '•••• ' . $acc->account_number_last4;
                                $bankColors = [
                                    'BCA' => ['bg'=>'#e3eeff','color'=>'#003d82'],
                                    'BRI' => ['bg'=>'#e3e8f5','color'=>'#003087'],
                                    'BNI' => ['bg'=>'#fff0e8','color'=>'#f26722'],
                                    'MANDIRI' => ['bg'=>'#e3eeff','color'=>'#00529b'],
                                    'BSI' => ['bg'=>'#e8f5e9','color'=>'#1b5e20'],
                                ];
                                $clr = $bankColors[$acc->bank_code] ?? ['bg'=>'#f1f5f9','color'=>'#64748b'];
                            @endphp
                            <label class="wd-account-option"
                                   style="display:flex;align-items:center;gap:12px;
                                          padding:13px 14px;border-radius:11px;cursor:pointer;
                                          border:1.5px solid #e8edf5;background:#fff;
                                          transition:border-color .15s,background .15s;"
                                   data-account-id="{{ $acc->id }}"
                                   data-holder="{{ $acc->account_holder_encrypted }}"
                                   data-number="{{ $decryptedNumber }}"
                                   data-bank="{{ $acc->bank_code }}"
                                   data-bank-name="{{ $acc->bank_name }}"
                                   data-masked="{{ $masked }}">

                                <input type="radio" name="wd_account"
                                       value="{{ $acc->id }}"
                                       {{ $acc->is_default ? 'checked' : '' }}
                                       style="accent-color:#2356e8;width:16px;height:16px;flex-shrink:0;"
                                       onchange="onAccountSelect(this)">

                                <div style="width:40px;height:40px;border-radius:8px;flex-shrink:0;
                                            display:flex;align-items:center;justify-content:center;
                                            font-size:10px;font-weight:800;letter-spacing:.3px;
                                            background:{{ $clr['bg'] }};color:{{ $clr['color'] }};">
                                    {{ Str::limit($acc->bank_code, 4, '') }}
                                </div>

                                <div style="flex:1;min-width:0;">
                                    <div style="font-size:13.5px;font-weight:700;color:#111827;
                                                white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                                        {{ $acc->account_holder_encrypted }}
                                    </div>
                                    <div style="font-size:12px;color:#9aaabb;margin-top:2px;">
                                        {{ $masked }} &middot; {{ $acc->bank_name }}
                                    </div>
                                </div>

                                @if($acc->is_default)
                                    <span style="font-size:10px;font-weight:700;padding:3px 8px;
                                                 border-radius:99px;background:#e8f0fe;color:#2356e8;
                                                 border:1px solid #c7d8ff;flex-shrink:0;">
                                        <i class="fas fa-star" style="font-size:8px;"></i> Utama
                                    </span>
                                @endif
                            </label>
                        @endforeach
                    </div>

                @else
                    <div style="text-align:center;padding:28px 20px;background:#f8fafd;
                                border:1.5px dashed #dde5f0;border-radius:12px;">
                        <div style="width:46px;height:46px;background:#e8f0fe;border-radius:50%;
                                    display:flex;align-items:center;justify-content:center;
                                    margin:0 auto 12px;color:#2356e8;font-size:18px;">
                            <i class="fas fa-building-columns"></i>
                        </div>
                        <div style="font-size:14px;font-weight:700;color:#334155;margin-bottom:6px;">
                            Belum ada rekening tersimpan
                        </div>
                        <div style="font-size:12.5px;color:#9aaabb;margin-bottom:16px;">
                            Tambahkan rekening bank terlebih dahulu
                        </div>
                        <a href="{{ route('payment.accounts.index') }}"
                           style="display:inline-flex;align-items:center;gap:7px;padding:10px 18px;
                                  background:#2356e8;color:#fff;border-radius:9px;text-decoration:none;
                                  font-size:13px;font-weight:700;box-shadow:0 4px 14px rgba(35,86,232,.25);">
                            <i class="fas fa-plus"></i> Tambah Rekening Bank
                        </a>
                    </div>
                @endif
            </div>

            @if($savedAccounts->isNotEmpty())

                <div style="height:1px;background:#f1f5f9;margin-bottom:18px;"></div>

                {{-- ── Jumlah Penarikan ──────────────────────────── --}}
                <div style="margin-bottom:18px;">
                    <label style="font-size:12px;font-weight:700;color:#556070;
                                  text-transform:uppercase;letter-spacing:.05em;
                                  display:block;margin-bottom:8px;">
                        <i class="fas fa-coins" style="color:#2356e8;margin-right:4px;"></i>
                        Jumlah Penarikan
                    </label>

                    {{-- Quick amount buttons --}}
                    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:7px;margin-bottom:10px;">
                        @foreach([20000,50000,100000] as $preset)
                            <button type="button"
                                    onclick="setAmount({{ $preset }}, this)"
                                    class="wd-preset-btn"
                                    style="padding:9px 6px;border-radius:8px;border:1.5px solid #e2e8f0;
                                           background:#f8fafd;font-size:12px;font-weight:600;
                                           color:#475569;cursor:pointer;transition:all .15s;">
                                {{ number_format($preset,0,',','.') }}
                            </button>
                        @endforeach
                    </div>

                    {{-- Tombol Custom Nominal --}}
                    <div id="custom-amount-trigger" style="margin-bottom:12px;">
                        <button type="button"
                                onclick="showCustomInput()"
                                style="width:100%;padding:10px;border-radius:8px;
                                       border:1.5px dashed #cbd5e1;background:#f8fafc;
                                       font-size:13px;font-weight:600;color:#64748b;
                                       cursor:pointer;transition:all .15s;
                                       display:flex;align-items:center;justify-content:center;gap:8px;"
                                onmouseenter="this.style.borderColor='#2563eb';this.style.color='#2563eb'"
                                onmouseleave="this.style.borderColor='#cbd5e1';this.style.color='#64748b'">
                            <i class="fas fa-edit"></i>
                            Masukkan Nominal Custom
                        </button>
                    </div>

                    {{-- Custom Input (hidden initially) --}}
                    <div id="custom-amount-input" style="display:none;">
                        <div style="position:relative;margin-bottom:8px;">
                            <span style="position:absolute;left:13px;top:50%;transform:translateY(-50%);
                                         font-size:14px;font-weight:700;color:#9aaabb;">Rp</span>
                            <input type="number" id="wd-amount-input"
                                    min="{{ config('xendit.withdrawal.min_amount', 20000) }}" max="{{ $balance ?? 0 }}"
                                   placeholder="0"
                                   oninput="onAmountInput()"
                                   style="width:100%;padding:12px 13px 12px 36px;
                                          border:1.5px solid #e2e8f0;border-radius:9px;
                                          font-size:18px;font-weight:700;color:#111827;
                                          outline:none;font-family:'Plus Jakarta Sans',sans-serif;
                                          transition:border-color .18s,box-shadow .18s;background:#f8fafd;"
                                   onfocus="this.style.borderColor='#2356e8';this.style.boxShadow='0 0 0 3px rgba(35,86,232,.1)';this.style.background='#fff';"
                                   onblur="this.style.borderColor='#e2e8f0';this.style.boxShadow='none';" />
                        </div>
                        <button type="button"
                                onclick="hideCustomInput()"
                                style="width:100%;padding:8px;border-radius:6px;
                                       border:none;background:#f1f5f9;color:#64748b;
                                       font-size:12px;font-weight:600;cursor:pointer;">
                            <i class="fas fa-times"></i> Batal
                        </button>
                    </div>

                    <div id="wd-amount-hint"
                         style="font-size:11.5px;color:#9aaabb;margin-top:8px;display:flex;align-items:center;gap:5px;">
                        <i class="fas fa-circle-info"></i>
                        <span>Pilih nominal atau masukkan nominal custom</span>
                    </div>

                    {{-- ── Keterangan Jumlah Akan Ditarik ──────────── --}}
                    <div id="wd-selected-amount-info"
                         style="display:none;margin-top:10px;padding:12px 14px;
                                background:#e8f0fe;border:1.5px solid #c7d8ff;border-radius:10px;
                                align-items:center;justify-content:space-between;
                                transition:all .2s ease;">
                        <div style="display:flex;align-items:center;gap:8px;">
                            <i class="fas fa-money-bill-wave" style="color:#2356e8;font-size:14px;"></i>
                            <span style="font-size:13px;font-weight:600;color:#334155;">Jumlah akan ditarik:</span>
                        </div>
                        <span id="wd-selected-amount-value"
                              style="font-size:16px;font-weight:800;color:#2356e8;
                                     font-family:'Plus Jakarta Sans',sans-serif;">
                            Rp 0
                        </span>
                    </div>

                </div>

                {{-- ── Breakdown Biaya ────────────────────────────── --}}
                <div id="wd-breakdown"
                     style="display:none;background:#f8fafd;border:1px solid #e2e8f0;
                            border-radius:11px;padding:14px 16px;margin-bottom:18px;">
                    <div style="font-size:11.5px;font-weight:700;color:#9aaabb;
                                text-transform:uppercase;letter-spacing:.07em;margin-bottom:10px;
                                display:flex;align-items:center;gap:6px;">
                        <i class="fas fa-file-invoice-dollar"></i>
                        Detail Penarikan
                    </div>
                    <div style="display:flex;flex-direction:column;gap:10px;">
                        {{-- Jumlah Penarikan --}}
                        <div style="display:flex;justify-content:space-between;align-items:center;">
                            <span style="font-size:13px;color:#64748b;">Jumlah Penarikan</span>
                            <span id="breakdown-amount" style="font-size:15px;font-weight:700;color:#111827;">Rp 0</span>
                        </div>

                        {{-- Fee Withdraw --}}
                        <div style="display:flex;justify-content:space-between;align-items:center;">
                            <span style="font-size:13px;color:#64748b;display:flex;align-items:center;gap:5px;">
                                Fee Withdraw
                                <span style="font-size:10px;padding:2px 6px;background:#fef3c7;
                                             color:#854d0e;border-radius:4px;font-weight:600;">Fixed</span>
                            </span>
                            <span id="breakdown-fee" style="font-size:15px;font-weight:700;color:#b45309;">Rp 6.660</span>
                        </div>

                        {{-- Yang Diterima --}}
                        <div style="display:flex;justify-content:space-between;align-items:center;">
                            <span style="font-size:13px;color:#64748b;">Yang Diterima</span>
                            <span id="breakdown-received" style="font-size:15px;font-weight:700;color:#15803d;">Rp 0</span>
                        </div>

                        <div style="height:1px;background:#e2e8f0;"></div>

                        {{-- Total Potongan Saldo --}}
                        <div style="display:flex;justify-content:space-between;align-items:center;">
                            <span style="font-size:13px;color:#111827;font-weight:600;">Total Potongan Saldo</span>
                            <span id="breakdown-total" style="font-size:16px;font-weight:800;color:#dc2626;">Rp 0</span>
                        </div>

                        {{-- Sisa Saldo --}}
                        <div style="padding:10px;background:#eff6ff;border:1px solid #bfdbfe;border-radius:8px;">
                            <div style="display:flex;justify-content:space-between;align-items:center;">
                                <span style="font-size:12px;color:#1e40af;font-weight:600;">Sisa Saldo Setelah Penarikan</span>
                                <span id="breakdown-remaining" style="font-size:15px;font-weight:800;color:#1e40af;">Rp 0</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ── Catatan ────────────────────────────────────── --}}
                <div style="margin-bottom:18px;">
                    <label style="font-size:12px;font-weight:700;color:#556070;
                                  text-transform:uppercase;letter-spacing:.05em;
                                  display:block;margin-bottom:8px;">
                        <i class="fas fa-note-sticky" style="color:#2356e8;margin-right:4px;"></i>
                        Catatan <span style="font-weight:400;text-transform:none;color:#c4cdd9;">(opsional)</span>
                    </label>
                    <textarea id="wd-notes"
                              rows="2"
                              placeholder="Tambahkan catatan jika diperlukan..."
                              style="width:100%;padding:11px 13px;border:1.5px solid #e2e8f0;
                                     border-radius:9px;font-size:13px;font-family:'Plus Jakarta Sans',sans-serif;
                                     color:#111827;resize:none;outline:none;background:#f8fafd;
                                     transition:border-color .18s,box-shadow .18s;"
                              onfocus="this.style.borderColor='#2356e8';this.style.boxShadow='0 0 0 3px rgba(35,86,232,.1)';this.style.background='#fff';"
                              onblur="this.style.borderColor='#e2e8f0';this.style.boxShadow='none';"></textarea>
                </div>

                {{-- ── Footer Buttons ──────────────────────────────── --}}
                <div style="display:flex;gap:10px;">
                    <button type="button" onclick="closeWithdrawModal()"
                            style="flex:1;padding:13px;border-radius:9px;border:1.5px solid #e2e8f0;
                                   background:#fff;color:#475569;font-size:13.5px;font-weight:600;
                                   cursor:pointer;font-family:'Plus Jakarta Sans',sans-serif;
                                   transition:all .15s;"
                            onmouseenter="this.style.background='#f1f5f9'"
                            onmouseleave="this.style.background='#fff'">
                        Batal
                    </button>
                    <button type="button" id="submit-withdraw-btn" onclick="handleWithdrawSubmit()"
                            style="flex:2;padding:13px;border-radius:9px;border:none;
                                   background:#2356e8;color:#fff;font-size:13.5px;font-weight:700;
                                   cursor:pointer;font-family:'Plus Jakarta Sans',sans-serif;
                                   display:flex;align-items:center;justify-content:center;gap:8px;
                                   box-shadow:0 4px 14px rgba(35,86,232,.3);
                                   transition:all .18s;"
                            onmouseenter="this.style.background='#1a44c4'"
                            onmouseleave="this.style.background='#2356e8'">
                        <i class="fas fa-arrow-up" id="wd-submit-icon"></i>
                        <span id="wd-submit-text">Ajukan Penarikan</span>
                        <svg id="wd-spinner" style="display:none;width:15px;height:15px;animation:wdSpin .7s linear infinite;"
                             viewBox="0 0 24 24" fill="none">
                            <circle cx="12" cy="12" r="10" stroke="rgba(255,255,255,.3)" stroke-width="3"/>
                            <path d="M12 2a10 10 0 0 1 10 10" stroke="#fff" stroke-width="3" stroke-linecap="round"/>
                        </svg>
                    </button>
                </div>

            @endif
        </div>
    </div>
</div>

<style>
@keyframes wdSpin { to { transform: rotate(360deg); } }
.wd-account-option:has(input:checked) { border-color: #2356e8 !important; background: #f5f8ff !important; }
.wd-preset-btn:hover { background: #e8f0fe !important; border-color: #c7d8ff !important; color: #2356e8 !important; }
.wd-preset-btn.active { background: #2356e8 !important; border-color: #2356e8 !important; color: #fff !important; }
@keyframes wdToastIn { from { transform: translateX(16px); opacity: 0; } to { transform: translateX(0); opacity: 1; } }
@keyframes wdFadeIn { from { opacity: 0; transform: translateY(-4px); } to { opacity: 1; transform: translateY(0); } }
#wd-selected-amount-info { animation: wdFadeIn .2s ease; }
</style>

<script>
const WD_BALANCE    = {{ $balance ?? 0 }};
const WD_MIN_AMOUNT = {{ config('xendit.withdrawal.min_amount', 20000) }};
const WD_FEE        = {{ config('payment.withdraw_fee', config('xendit.disbursement.fee_flat', 6660)) }};
const WD_ROUTES     = { store: '/withdrawal' };
const WD_CSRF       = () => document.querySelector('meta[name="csrf-token"]').content;
let   wdSelectedAcc = null;
let   wdSelectedAmount = 0;

function openWithdrawModal() {
    const overlay = document.getElementById('withdrawOverlay');
    const modal   = document.getElementById('withdraw-modal');
    const card    = document.getElementById('withdrawCard');
    modal.style.display = 'flex';
    card.getBoundingClientRect();
    overlay.style.opacity = '1';
    overlay.style.pointerEvents = 'auto';
    card.style.opacity = '1';
    card.style.transform = 'translateY(0)';
    document.body.style.overflow = 'hidden';
    const defaultRadio = document.querySelector('#wd-account-list input[type="radio"]:checked');
    if (defaultRadio) onAccountSelect(defaultRadio);
}

function closeWithdrawModal() {
    const overlay = document.getElementById('withdrawOverlay');
    const modal = document.getElementById('withdraw-modal');
    const card = document.getElementById('withdrawCard');
    overlay.style.opacity = '0';
    overlay.style.pointerEvents = 'none';
    card.style.opacity = '0';
    card.style.transform = 'translateY(20px)';
    setTimeout(() => {
        modal.style.display = 'none';
        document.body.style.overflow = '';
        hideCustomInput();
        wdSelectedAmount = 0;
        document.querySelectorAll('.wd-preset-btn').forEach(b => b.classList.remove('active'));
        document.getElementById('wd-breakdown').style.display = 'none';
        showSelectedAmountInfo(0);
    }, 220);
}

document.getElementById('withdrawOverlay').addEventListener('click', closeWithdrawModal);
document.addEventListener('keydown', e => { if (e.key === 'Escape') closeWithdrawModal(); });

function onAccountSelect(radio) {
    const label = radio.closest('label.wd-account-option');
    wdSelectedAcc = {
        id: label.dataset.accountId,
        holder: label.dataset.holder,
        number: label.dataset.number,
        bank: label.dataset.bank,
        bankName: label.dataset.bankName,
        masked: label.dataset.masked,
    };
    document.querySelectorAll('.wd-account-option').forEach(l => {
        const r = l.querySelector('input[type="radio"]');
        if (r && r.checked) {
            l.style.borderColor = '#2356e8';
            l.style.background = '#f5f8ff';
        } else {
            l.style.borderColor = '#e8edf5';
            l.style.background = '#fff';
        }
    });
    updateBreakdown();
}

function showCustomInput() {
    document.getElementById('custom-amount-trigger').style.display = 'none';
    document.getElementById('custom-amount-input').style.display = 'block';
    document.querySelectorAll('.wd-preset-btn').forEach(b => b.classList.remove('active'));
    showSelectedAmountInfo(0);
    setTimeout(() => document.getElementById('wd-amount-input').focus(), 100);
}

function hideCustomInput(skipReset) {
    document.getElementById('custom-amount-trigger').style.display = 'block';
    document.getElementById('custom-amount-input').style.display = 'none';
    document.getElementById('wd-amount-input').value = '';
    if (!skipReset) {
        wdSelectedAmount = 0;
        showSelectedAmountInfo(0);
        updateBreakdown();
    }
}

function setAmount(amount, btn) {
    hideCustomInput(true); // skip reset
    wdSelectedAmount = amount;
    document.querySelectorAll('.wd-preset-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    showSelectedAmountInfo(amount);
    updateBreakdown();
}

function onAmountInput() {
    const val = parseInt(document.getElementById('wd-amount-input').value) || 0;
    wdSelectedAmount = val;
    const hint = document.getElementById('wd-amount-hint');
    const input = document.getElementById('wd-amount-input');

    if (val <= 0) {
        hint.innerHTML = '<i class="fas fa-circle-info"></i><span>Masukkan nominal penarikan</span>';
        hint.style.color = '#9aaabb';
        showSelectedAmountInfo(0);
    } else if (val < WD_MIN_AMOUNT) {
        hint.innerHTML = `<i class="fas fa-triangle-exclamation"></i><span>Minimal penarikan Rp ${formatRp(WD_MIN_AMOUNT)}</span>`;
        hint.style.color = '#e53e3e';
        input.style.borderColor = '#e53e3e';
        showSelectedAmountInfo(val, true);
    } else if (val > WD_BALANCE) {
        hint.innerHTML = `<i class="fas fa-triangle-exclamation"></i><span>Saldo tidak cukup (termasuk fee Rp ${formatRp(WD_FEE)})</span>`;
        hint.style.color = '#e53e3e';
        input.style.borderColor = '#e53e3e';
        showSelectedAmountInfo(val, true);
    } else {
        hint.innerHTML = '<i class="fas fa-check"></i><span>Nominal valid</span>';
        hint.style.color = '#16a34a';
        input.style.borderColor = '#22c55e';
        showSelectedAmountInfo(val);
    }
    updateBreakdown();
}

function showSelectedAmountInfo(amount, isError = false) {
    const el = document.getElementById('wd-selected-amount-info');
    const valEl = document.getElementById('wd-selected-amount-value');
    if (!amount || amount <= 0) {
        el.style.display = 'none';
        return;
    }
    el.style.display = 'flex';
    el.style.background = isError ? '#fef2f2' : '#e8f0fe';
    el.style.borderColor = isError ? '#fca5a5' : '#c7d8ff';
    valEl.style.color = isError ? '#dc2626' : '#2356e8';
    valEl.textContent = 'Rp ' + formatRp(amount);

    // Ganti icon sesuai state
    const icon = el.querySelector('i');
    if (icon) {
        icon.className = isError
            ? 'fas fa-triangle-exclamation'
            : 'fas fa-money-bill-wave';
        icon.style.color = isError ? '#dc2626' : '#2356e8';
    }
}

function formatRp(n) {
    return n.toLocaleString('id-ID');
}

function updateBreakdown() {
    const breakdown = document.getElementById('wd-breakdown');
    const isValid = wdSelectedAmount >= WD_MIN_AMOUNT && wdSelectedAmount <= WD_BALANCE && wdSelectedAcc;

    if (!isValid || wdSelectedAmount === 0) {
        breakdown.style.display = 'none';
        return;
    }

    const totalDeduction = wdSelectedAmount; // Only amount, fee is deducted from amount
    const receivedAmount = wdSelectedAmount - WD_FEE;
    const remaining = WD_BALANCE - totalDeduction;

    breakdown.style.display = 'block';
    document.getElementById('breakdown-amount').textContent = 'Rp ' + formatRp(wdSelectedAmount);
    document.getElementById('breakdown-fee').textContent = 'Rp ' + formatRp(WD_FEE);
    document.getElementById('breakdown-received').textContent = 'Rp ' + formatRp(receivedAmount);
    document.getElementById('breakdown-total').textContent = 'Rp ' + formatRp(totalDeduction);
    document.getElementById('breakdown-remaining').textContent = 'Rp ' + formatRp(remaining);
}

async function handleWithdrawSubmit() {
    if (!wdSelectedAcc) {
        wdToast('error', 'Pilih rekening tujuan terlebih dahulu.');
        return;
    }
    if (wdSelectedAmount < WD_MIN_AMOUNT) {
        wdToast('error', `Minimal penarikan Rp ${formatRp(WD_MIN_AMOUNT)}.`);
        return;
    }
    if (wdSelectedAmount > WD_BALANCE) {
        wdToast('error', `Saldo tidak cukup. Total potongan Rp ${formatRp(wdSelectedAmount)}.`);
        return;
    }

    wdSetLoading(true);
    const notes = document.getElementById('wd-notes')?.value || '';
    const formData = new FormData();
    formData.append('_token', WD_CSRF());
    formData.append('amount', wdSelectedAmount);
    formData.append('payment_account_id', wdSelectedAcc.id);
    formData.append('bank_code', wdSelectedAcc.bank);
    formData.append('bank_name', wdSelectedAcc.bankName);
    formData.append('account_number', wdSelectedAcc.number);
    formData.append('account_name', wdSelectedAcc.holder);
    formData.append('notes', notes);

    try {
        const res = await fetch(WD_ROUTES.store, {
            method: 'POST',
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': WD_CSRF() },
            body: formData,
        });
        const result = await res.json();

        if (result.success) {
            closeWithdrawModal();
            wdToast('success', result.message || 'Penarikan berhasil diajukan.');
            setTimeout(() => window.location.reload(), 1200);
        } else {
            wdToast('error', result.message || 'Gagal mengajukan penarikan.');
            wdSetLoading(false);
        }
    } catch(e) {
        wdToast('error', 'Terjadi kesalahan. Silakan coba lagi.');
        wdSetLoading(false);
    }
}

function wdSetLoading(loading) {
    const btn = document.getElementById('submit-withdraw-btn');
    const icon = document.getElementById('wd-submit-icon');
    const txt = document.getElementById('wd-submit-text');
    const spin = document.getElementById('wd-spinner');
    btn.disabled = loading;
    icon.style.display = loading ? 'none' : '';
    spin.style.display = loading ? 'block' : 'none';
    txt.textContent = loading ? 'Memproses...' : 'Ajukan Penarikan';
}

function wdToast(type, message) {
    const colors = { success: '#22c55e', error: '#e53e3e', warning: '#f59e0b', info: '#2356e8' };
    const icons = { success: 'fa-circle-check', error: 'fa-circle-xmark', warning: 'fa-triangle-exclamation', info: 'fa-circle-info' };
    const el = document.createElement('div');
    el.style.cssText = `position:fixed;bottom:22px;right:22px;z-index:99999;
        background:#fff;border:1px solid #e2e8f0;border-left:3px solid ${colors[type]};
        border-radius:10px;padding:12px 16px;display:flex;align-items:center;gap:10px;
        font-size:13.5px;font-weight:500;color:#334155;min-width:240px;max-width:320px;
        box-shadow:0 8px 28px rgba(0,0,0,.1);font-family:'Plus Jakarta Sans',sans-serif;
        animation:wdToastIn .25s ease;`;
    el.innerHTML = `<i class="fas ${icons[type]}" style="color:${colors[type]};font-size:15px;flex-shrink:0;"></i><span>${message}</span>`;
    document.body.appendChild(el);
    setTimeout(() => {
        el.style.transition = 'opacity .25s,transform .25s';
        el.style.opacity = '0';
        el.style.transform = 'translateX(16px)';
        setTimeout(() => el.remove(), 250);
    }, 3600);
}
</script>

@endpush
