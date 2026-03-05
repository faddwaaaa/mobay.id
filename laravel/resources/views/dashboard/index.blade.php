@extends('layouts.dashboard')

@section('title', 'Dashboard | Payou.id')

@php
    use Illuminate\Support\Str;
    $user = Auth::user();
@endphp

@section('content')
@include('components.qr-modal')

<style>
@media (max-width: 768px) {
    .page-header { flex-direction: column !important; align-items: flex-start !important; }
    .page-header > div:first-child h1 { font-size: 20px !important; }
    .page-header > div:first-child > div { font-size: 13px !important; }
    .page-header button { width: 100%; justify-content: center; }
    .balance-card { grid-template-columns: 1fr !important; padding: 24px !important; text-align: center; }
    .balance-card > div:first-child { margin-bottom: 20px; }
    .balance-card > div:first-child > div:nth-child(2) { font-size: 36px !important; }
    .balance-card > div:last-child { justify-content: center !important; }
    .balance-card button { width: 100%; }
    .chart-card { padding: 16px !important; }
    .chart-card > div:first-child { flex-direction: column; align-items: flex-start !important; gap: 8px; }
    .chart-card h3 { font-size: 15px !important; }
    .stat-card h4 { font-size: 13px !important; }
    .stat-card .stat-value { font-size: 28px !important; }
}
@media (max-width: 480px) {
    .page-header > div:first-child h1 { font-size: 18px !important; }
    .balance-card > div:first-child > div:nth-child(2) { font-size: 32px !important; }
    .stat-card { padding: 16px !important; }
    .date-range-btn { padding: 8px 12px !important; font-size: 12px !important; }
}
</style>

<!-- ================= HEADER ================= -->
<div class="page-header" style="display:flex;justify-content:space-between;align-items:center;gap:16px;margin-bottom:24px;">
    <div>
        <h1 style="font-size:24px;font-weight:700;margin-bottom:4px;">Dashboard</h1>
        <div style="font-size:14px;color:#374151;margin-bottom:4px;">
            Selamat datang kembali, <strong>{{ $user->name }}</strong> 👋
        </div>
        <div style="display:flex;align-items:center;gap:6px;font-size:13px;font-weight:600;">
            <i class="fas fa-link" style="font-size:12px;color:#2563eb;"></i>
            <a href="{{ url('/' . $user->username) }}" target="_blank" style="color:#2563eb;text-decoration:none;">
                {{ url('/' . $user->username) }}
            </a>
        </div>
    </div>
    <button type="button" onclick="openQRModal('{{ $user->username }}')"
            style="display:flex;align-items:center;gap:8px;padding:10px 16px;border-radius:12px;
                   border:1px solid #dbeafe;background:#eff6ff;color:#2563eb;font-size:14px;
                   font-weight:600;cursor:pointer;white-space:nowrap;">
        <i class="fas fa-share-alt"></i>
        <span>Bagikan Link Anda</span>
    </button>
</div>

<!-- ================= SALDO ================= -->
{{-- ================= SALDO CARD — ganti bagian balance-card di dashboard ================= --}}
<div class="balance-card"
     style="position:relative;overflow:hidden;border-radius:20px;padding:28px 32px;
            margin-bottom:32px;
            background:linear-gradient(135deg, #1a3fa8 0%, #2356e8 45%, #3b82f6 100%);
            color:#ffffff;box-shadow:0 12px 40px rgba(35,86,232,.35);">

    {{-- Dekorasi lingkaran kanan --}}
    <div style="position:absolute;top:-40px;right:-40px;width:200px;height:200px;
                border-radius:50%;background:rgba(255,255,255,.08);pointer-events:none;"></div>
    <div style="position:absolute;top:20px;right:40px;width:130px;height:130px;
                border-radius:50%;background:rgba(255,255,255,.06);pointer-events:none;"></div>
    <div style="position:absolute;bottom:-50px;right:80px;width:100px;height:100px;
                border-radius:50%;background:rgba(255,255,255,.05);pointer-events:none;"></div>

    {{-- Isi card --}}
    <div style="position:relative;z-index:1;">

        {{-- Label bank (dari rekening utama jika ada) --}}
        @php
            $primaryAccount = auth()->user()->paymentAccounts()->where('is_default', true)->first()
                            ?? auth()->user()->paymentAccounts()->first();
        @endphp

        @if($primaryAccount)
            <div style="font-size:15px;font-weight:700;margin-bottom:18px;opacity:.95;letter-spacing:.01em;">
                Saldo Tersedia
            </div>
        @else
            <div style="font-size:15px;font-weight:700;margin-bottom:18px;opacity:.7;">
                Saldo Payou.id
            </div>
        @endif

        {{-- Saldo --}}
        <div style="margin-bottom:22px;">
            <div style="font-size:11px;font-weight:600;opacity:.7;letter-spacing:.08em;
                        text-transform:uppercase;margin-bottom:6px;"></div>
            <div style="font-size:42px;font-weight:800;line-height:1;
                        font-family:'Plus Jakarta Sans',sans-serif;letter-spacing:-.5px;">
                Rp {{ number_format($balance ?? 0, 0, ',', '.') }}
            </div>
        </div>

        {{-- Tombol tarik saldo --}}
        <div style="margin-top:20px;">
            <button id="btn-withdraw" onclick="openWithdrawModal()"
                    style="display:inline-flex;align-items:center;gap:8px;
                           padding:11px 22px;border-radius:12px;border:none;cursor:pointer;
                           font-size:14px;font-weight:700;font-family:'Plus Jakarta Sans',sans-serif;
                           background:#ffffff;color:#1a3fa8;
                           box-shadow:0 6px 20px rgba(0,0,0,.18);
                           transition:transform .15s,box-shadow .15s;white-space:nowrap;"
                    onmouseenter="this.style.transform='translateY(-1px)';this.style.boxShadow='0 8px 24px rgba(0,0,0,.22)'"
                    onmouseleave="this.style.transform='';this.style.boxShadow='0 6px 20px rgba(0,0,0,.18)'">
                <i class="fas fa-arrow-up" style="font-size:13px;"></i>
                Tarik Saldo
            </button>
        </div>
    </div>
</div>

<style>
@media (max-width: 640px) {
    .balance-card {
        padding: 22px 20px !important;
    }
    .balance-card [style*="font-size:42px"] {
        font-size: 32px !important;
    }
    .balance-card [style*="font-size:15px"][style*="font-weight:700"] {
        font-size: 13px !important;
    }
    #btn-withdraw {
        width: 100%;
        justify-content: center;
    }
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
                    <span title="Jumlah pengunjung yang mengklik link atau produk di halamanmu"
      style="width:12px;height:12px;border-radius:50%;background:#2563eb;display:inline-block;cursor:help;"></span>
<span title="Jumlah pengunjung yang mengklik link atau produk di halamanmu"
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

@endsection

{{-- ============================================================
     MODAL WITHDRAW — versi baru pakai rekening tersimpan
     Letakkan di @push('modals') pada dashboard blade
============================================================ --}}
@push('modals')

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
                    <p style="margin:0;font-size:12px;color:#94a3b8;">Min. penarikan Rp 10.000</p>
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

                {{-- Ada rekening --}}
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
                                    'BCA'     => ['bg'=>'#e3eeff','color'=>'#003d82'],
                                    'BRI'     => ['bg'=>'#e3e8f5','color'=>'#003087'],
                                    'BNI'     => ['bg'=>'#fff0e8','color'=>'#f26722'],
                                    'MANDIRI' => ['bg'=>'#e3eeff','color'=>'#00529b'],
                                    'BSI'     => ['bg'=>'#e8f5e9','color'=>'#1b5e20'],
                                    'CIMB'    => ['bg'=>'#fce4ec','color'=>'#b71c1c'],
                                    'GOPAY'   => ['bg'=>'#e8f5e9','color'=>'#00880a'],
                                    'OVO'     => ['bg'=>'#f3e5f5','color'=>'#6a1b9a'],
                                    'DANA'    => ['bg'=>'#e3f2fd','color'=>'#1565c0'],
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

                    {{-- Preview rekening terpilih --}}
                    <div id="wd-account-preview"
                         style="display:flex;align-items:center;gap:10px;margin-top:10px;
                                padding:10px 13px;background:#f0f9ff;border:1px solid #bae6fd;
                                border-radius:9px;font-size:12.5px;color:#0369a1;">
                        <i class="fas fa-circle-check" style="color:#0284c7;font-size:13px;flex-shrink:0;"></i>
                        <span id="wd-preview-text">—</span>
                    </div>

                @else
                    {{-- Tidak ada rekening --}}
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
                            Tambahkan rekening bank terlebih dahulu untuk melakukan penarikan
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
                        @foreach([10000,25000,50000,100000,500000,1000000] as $preset)
                            <button type="button"
                                    onclick="setAmount({{ $preset }})"
                                    class="wd-preset-btn"
                                    style="padding:9px 6px;border-radius:8px;border:1.5px solid #e2e8f0;
                                           background:#f8fafd;font-size:12px;font-weight:600;
                                           color:#475569;cursor:pointer;transition:all .15s;">
                                Rp {{ number_format($preset,0,',','.') }}
                            </button>
                        @endforeach
                    </div>

                    <div style="position:relative;">
                        <span style="position:absolute;left:13px;top:50%;transform:translateY(-50%);
                                     font-size:14px;font-weight:700;color:#9aaabb;">Rp</span>
                        <input type="number" id="wd-amount-input"
                               min="10000" max="{{ $balance ?? 0 }}"
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

                    <div id="wd-amount-hint"
                         style="font-size:11.5px;color:#9aaabb;margin-top:5px;display:flex;align-items:center;gap:5px;">
                        <i class="fas fa-circle-info"></i>
                        <span>Masukkan nominal yang ingin ditarik</span>
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

                {{-- ── Ringkasan ───────────────────────────────────── --}}
                <div id="wd-summary"
                     style="display:none;background:#f8fafd;border:1px solid #e2e8f0;
                            border-radius:11px;padding:14px 16px;margin-bottom:18px;">
                    <div style="font-size:11.5px;font-weight:700;color:#9aaabb;
                                text-transform:uppercase;letter-spacing:.07em;margin-bottom:10px;">
                        Ringkasan Penarikan
                    </div>
                    <div style="display:flex;flex-direction:column;gap:8px;">
                        <div style="display:flex;justify-content:space-between;font-size:13px;">
                            <span style="color:#64748b;">Rekening tujuan</span>
                            <span id="sum-account" style="font-weight:700;color:#111827;text-align:right;max-width:60%;">—</span>
                        </div>
                        <div style="display:flex;justify-content:space-between;font-size:13px;">
                            <span style="color:#64748b;">Jumlah ditarik</span>
                            <span id="sum-amount" style="font-weight:700;color:#2356e8;">—</span>
                        </div>
                        <div style="height:1px;background:#f1f5f9;"></div>
                        <div style="display:flex;justify-content:space-between;font-size:13px;">
                            <span style="color:#64748b;">Sisa saldo</span>
                            <span id="sum-remaining" style="font-weight:700;color:#111827;">—</span>
                        </div>
                    </div>
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

.wd-account-option:has(input:checked) {
    border-color: #2356e8 !important;
    background: #f5f8ff !important;
}

.wd-preset-btn:hover {
    background: #e8f0fe !important;
    border-color: #c7d8ff !important;
    color: #2356e8 !important;
}

.wd-preset-btn.active {
    background: #2356e8 !important;
    border-color: #2356e8 !important;
    color: #fff !important;
}
</style>

<script>
// ── State ──────────────────────────────────────────────────────────────────
const WD_BALANCE    = {{ $balance ?? 0 }};
const WD_ROUTES     = { store: '/withdrawal' };
const WD_CSRF       = () => document.querySelector('meta[name="csrf-token"]').content;
let   wdSelectedAcc = null;

// ── Open / Close ───────────────────────────────────────────────────────────
function openWithdrawModal() {
    const overlay = document.getElementById('withdrawOverlay');
    const modal   = document.getElementById('withdraw-modal');
    const card    = document.getElementById('withdrawCard');

    modal.style.display = 'flex';
    card.getBoundingClientRect(); // reflow

    overlay.style.opacity     = '1';
    overlay.style.pointerEvents = 'auto';
    card.style.opacity        = '1';
    card.style.transform      = 'translateY(0)';
    document.body.style.overflow = 'hidden';

    // Auto-select default account
    const defaultRadio = document.querySelector('#wd-account-list input[type="radio"]:checked');
    if (defaultRadio) onAccountSelect(defaultRadio);
}

function closeWithdrawModal() {
    const overlay = document.getElementById('withdrawOverlay');
    const modal   = document.getElementById('withdraw-modal');
    const card    = document.getElementById('withdrawCard');

    overlay.style.opacity      = '0';
    overlay.style.pointerEvents = 'none';
    card.style.opacity         = '0';
    card.style.transform       = 'translateY(20px)';

    setTimeout(() => {
        modal.style.display = 'none';
        document.body.style.overflow = '';
    }, 220);
}

document.getElementById('withdrawOverlay').addEventListener('click', closeWithdrawModal);
document.addEventListener('keydown', e => { if (e.key === 'Escape') closeWithdrawModal(); });

// ── Account select ─────────────────────────────────────────────────────────
function onAccountSelect(radio) {
    const label   = radio.closest('label.wd-account-option');
    const holder  = label.dataset.holder;
    const masked  = label.dataset.masked;
    const bankName= label.dataset.bankName;

    wdSelectedAcc = {
        id:       label.dataset.accountId,
        holder:   holder,
        number:   label.dataset.number,
        bank:     label.dataset.bank,
        bankName: bankName,
        masked:   masked,
    };

    // Style semua option
    document.querySelectorAll('.wd-account-option').forEach(l => {
        const r = l.querySelector('input[type="radio"]');
        if (r && r.checked) {
            l.style.borderColor = '#2356e8';
            l.style.background  = '#f5f8ff';
        } else {
            l.style.borderColor = '#e8edf5';
            l.style.background  = '#fff';
        }
    });

    document.getElementById('wd-preview-text').textContent =
        `${holder} · ${masked} (${bankName})`;

    updateSummary();
}

// ── Amount ─────────────────────────────────────────────────────────────────
function setAmount(amount) {
    document.getElementById('wd-amount-input').value = amount;
    document.querySelectorAll('.wd-preset-btn').forEach(btn => btn.classList.remove('active'));
    event.currentTarget.classList.add('active');
    onAmountInput();
}

function onAmountInput() {
    const input = document.getElementById('wd-amount-input');
    const val   = parseInt(input.value) || 0;
    const hint  = document.getElementById('wd-amount-hint');

    // Clear preset active jika user ketik manual
    if (document.activeElement === input) {
        document.querySelectorAll('.wd-preset-btn').forEach(b => b.classList.remove('active'));
    }

    if (val <= 0) {
        hint.innerHTML = '<i class="fas fa-circle-info"></i><span>Masukkan nominal yang ingin ditarik</span>';
        hint.style.color = '#9aaabb';
    } else if (val < 10000) {
        hint.innerHTML = '<i class="fas fa-triangle-exclamation"></i><span>Minimal penarikan Rp 10.000</span>';
        hint.style.color = '#e53e3e';
        input.style.borderColor = '#e53e3e';
    } else if (val > WD_BALANCE) {
        hint.innerHTML = '<i class="fas fa-triangle-exclamation"></i><span>Melebihi saldo tersedia</span>';
        hint.style.color = '#e53e3e';
        input.style.borderColor = '#e53e3e';
    } else {
        hint.innerHTML = '<i class="fas fa-check"></i><span>Nominal valid</span>';
        hint.style.color = '#16a34a';
        input.style.borderColor = '#22c55e';
    }

    updateSummary();
}

// ── Summary ────────────────────────────────────────────────────────────────
function formatRp(n) {
    return 'Rp ' + n.toLocaleString('id-ID');
}

function updateSummary() {
    const summary   = document.getElementById('wd-summary');
    const val       = parseInt(document.getElementById('wd-amount-input')?.value) || 0;
    const isValid   = val >= 10000 && val <= WD_BALANCE && wdSelectedAcc;

    if (!isValid) { summary.style.display = 'none'; return; }

    summary.style.display = 'block';
    document.getElementById('sum-account').textContent  =
        `${wdSelectedAcc.holder} · ${wdSelectedAcc.masked}`;
    document.getElementById('sum-amount').textContent   = formatRp(val);
    document.getElementById('sum-remaining').textContent = formatRp(WD_BALANCE - val);
}

// ── Submit ─────────────────────────────────────────────────────────────────
async function handleWithdrawSubmit() {
    const val   = parseInt(document.getElementById('wd-amount-input').value) || 0;
    const notes = document.getElementById('wd-notes')?.value || '';

    if (!wdSelectedAcc) {
        wdToast('error', 'Pilih rekening tujuan terlebih dahulu.'); return;
    }
    if (val < 10000) {
        wdToast('error', 'Minimal penarikan Rp 10.000.'); return;
    }
    if (val > WD_BALANCE) {
        wdToast('error', 'Nominal melebihi saldo tersedia.'); return;
    }

    wdSetLoading(true);

    const formData = new FormData();
    formData.append('_token',         WD_CSRF());
    formData.append('amount',         val);
    formData.append('payment_account_id', wdSelectedAcc.id);
    formData.append('bank_code',      wdSelectedAcc.bank);
    formData.append('bank_name',      wdSelectedAcc.bankName);
    formData.append('account_number', wdSelectedAcc.number);
    formData.append('account_name',   wdSelectedAcc.holder);
    formData.append('notes',          notes);

    try {
        const res    = await fetch(WD_ROUTES.store, {
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
    const btn  = document.getElementById('submit-withdraw-btn');
    const icon = document.getElementById('wd-submit-icon');
    const txt  = document.getElementById('wd-submit-text');
    const spin = document.getElementById('wd-spinner');
    btn.disabled       = loading;
    icon.style.display = loading ? 'none' : '';
    spin.style.display = loading ? 'block' : 'none';
    txt.textContent    = loading ? 'Memproses...' : 'Ajukan Penarikan';
}

// ── Toast notif ringan ─────────────────────────────────────────────────────
function wdToast(type, message) {
    const colors = { success: '#22c55e', error: '#e53e3e', warning: '#f59e0b', info: '#2356e8' };
    const icons  = { success: 'fa-circle-check', error: 'fa-circle-xmark', warning: 'fa-triangle-exclamation', info: 'fa-circle-info' };
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
        el.style.opacity = '0'; el.style.transform = 'translateX(16px)';
        setTimeout(() => el.remove(), 250);
    }, 3600);
}
</script>

<style>
@keyframes wdToastIn {
    from { transform: translateX(16px); opacity: 0; }
    to   { transform: translateX(0); opacity: 1; }
}
</style>

@endpush