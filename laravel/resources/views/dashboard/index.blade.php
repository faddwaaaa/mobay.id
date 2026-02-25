@extends('layouts.dashboard')

@section('title', 'Dashboard | Payou.id')

@php
    use Illuminate\Support\Str;
    $user = Auth::user();
@endphp

@section('content')
@include('components.qr-modal')

<style>
/* ================= RESPONSIVE STYLES ================= */
@media (max-width: 768px) {
    .page-header {
        flex-direction: column !important;
        align-items: flex-start !important;
    }
    
    .page-header > div:first-child h1 {
        font-size: 20px !important;
    }
    
    .page-header > div:first-child > div {
        font-size: 13px !important;
    }
    
    .page-header button {
        width: 100%;
        justify-content: center;
    }
    
    .balance-card {
        grid-template-columns: 1fr !important;
        padding: 24px !important;
        text-align: center;
    }
    
    .balance-card > div:first-child {
        margin-bottom: 20px;
    }
    
    .balance-card > div:first-child > div:nth-child(2) {
        font-size: 36px !important;
    }
    
    .balance-card > div:last-child {
        justify-content: center !important;
    }
    
    .balance-card button {
        width: 100%;
    }
    
    .chart-card {
        padding: 16px !important;
    }
    
    .chart-card > div:first-child {
        flex-direction: column;
        align-items: flex-start !important;
        gap: 8px;
    }
    
    .chart-card h3 {
        font-size: 15px !important;
    }
    
    .stat-card h4 {
        font-size: 13px !important;
    }
    
    .stat-card .stat-value {
        font-size: 28px !important;
    }
    
    #withdraw-modal > div {
        margin: 16px;
        max-width: calc(100% - 32px) !important;
    }
}

@media (max-width: 480px) {
    .page-header > div:first-child h1 {
        font-size: 18px !important;
    }
    
    .balance-card > div:first-child > div:nth-child(2) {
        font-size: 32px !important;
    }
    
    .stat-card {
        padding: 16px !important;
    }
    
    .date-range-btn {
        padding: 8px 12px !important;
        font-size: 12px !important;
    }
}
</style>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
</head>

<!-- ================= HEADER ================= -->
<div class="page-header"
     style="
        display:flex;
        justify-content:space-between;
        align-items:center;
        gap:16px;
        margin-bottom:24px;
     ">

    <!-- INFO AKUN -->
    <div>
        <h1 style="font-size:24px;font-weight:700;margin-bottom:4px;">
            Dashboard
        </h1>

        <div style="font-size:14px;color:#374151;margin-bottom:4px;">
            Selamat datang kembali, <strong>{{ $user->name }}</strong> 👋
        </div>

        <div style="
            display:flex;
            align-items:center;
            gap:6px;
            font-size:13px;
            font-weight:600;
        ">

            <i class="fas fa-link" style="font-size:12px;color:#2563eb;"></i>

            <a href="{{ url('/' . $user->username) }}"
            target="_blank"
            style="
                    color:#2563eb;
                    text-decoration:none;
            ">
                {{ url('/' . $user->username) }}
            </a>
        </div>
    </div>

    <!-- SHARE BUTTON -->
    <button type="button"
            onclick="openQRModal('{{ $user->username }}')"
            style="
                display:flex;
                align-items:center;
                gap:8px;
                padding:10px 16px;
                border-radius:12px;
                border:1px solid #dbeafe;
                background:#eff6ff;
                color:#2563eb;
                font-size:14px;
                font-weight:600;
                cursor:pointer;
                position:relative;
                z-index:60;
                white-space:nowrap;
            ">
        <i class="fas fa-share-alt"></i>
        <span>Bagikan Link Anda</span>
    </button>
</div>

<!-- ================= SALDO HIGHLIGHT ================= -->
<div class="balance-card"
     style="
        background:linear-gradient(135deg,#1e40af,#2563eb);
        color:#ffffff;
        border-radius:20px;
        padding:32px;
        margin-bottom:32px;
        display:grid;
        grid-template-columns:2fr 1fr;
        gap:24px;
        align-items:center;
    ">
    <div>
        <div style="font-size:14px;opacity:.85;">Saldo Tersedia</div>
        <div style="font-size:44px;font-weight:700;margin:8px 0;font-family: 'poppins';">
            Rp {{ number_format($balance ?? 0,0,',','.') }}
        </div>
        <div style="font-size:13px;opacity:.85;">
            Penghasilan siap ditarik
        </div>
    </div>

    <!-- BUTTON TARIK -->
    <div style="display:flex;justify-content:flex-end;">
        <button id="btn-withdraw"
                onclick="openWithdrawModal()"
                style="
                    display:flex;
                    align-items:center;
                    gap:10px;
                    padding:14px 22px;
                    border-radius:14px;
                    border:none;
                    cursor:pointer;
                    font-size:15px;
                    font-weight:700;
                    background:#ffffff;
                    color:#1e40af;
                    box-shadow:0 10px 30px rgba(0,0,0,.25);
                    white-space:nowrap;
                ">
            <i class="fas fa-arrow-up"></i>
            Tarik Saldo
        </button>
    </div>
</div>

<!-- ================= CHART VIEWS & KLIK ================= -->
<div class="chart-card"
     style="
        background:#ffffff;
        border-radius:18px;
        padding:24px;
        margin-bottom:32px;
        box-shadow:0 10px 30px rgba(0,0,0,.06);
    ">

    <!-- HEADER -->
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;flex-wrap:wrap;gap:12px;">
        <div>
            <h3 style="font-size:16px;font-weight:700;margin:0 0 8px;">
                Total Views & Clicks
            </h3>
            <div style="display:flex;gap:20px;">
                <div style="display:flex;align-items:center;gap:8px;">
                    <span style="width:12px;height:12px;border-radius:50%;background:#f59e0b;display:inline-block;"></span>
                    <span style="font-size:13px;color:#6b7280;">Views</span>
                    <span style="font-size:22px;font-weight:800;color:#111;" id="total-views-label">{{ number_format(array_sum($data)) }}</span>
                </div>
                <div style="display:flex;align-items:center;gap:8px;">
                    <span style="width:12px;height:12px;border-radius:50%;background:#2563eb;display:inline-block;"></span>
                    <span style="font-size:13px;color:#6b7280;">Clicks</span>
                    <span style="font-size:22px;font-weight:800;color:#111;" id="total-click-label">{{ number_format(array_sum($clicksData)) }}</span>
                </div>
            </div>
        </div>

        <!-- DATE RANGE PICKER -->
        <div style="display:flex;align-items:center;gap:8px;">
            <div style="
                display:flex;
                align-items:center;
                gap:8px;
                padding:10px 14px;
                border:1.5px solid #e5e7eb;
                border-radius:12px;
                background:#f9fafb;
                cursor:pointer;
                font-size:13px;
                color:#374151;
            " onclick="toggleDatePicker()">
                <i class="fas fa-calendar-alt" style="color:#6b7280;"></i>
                <span id="date-range-label">7 Hari Terakhir</span>
                <i class="fas fa-chevron-down" style="color:#6b7280;font-size:11px;"></i>
            </div>

            <!-- DROPDOWN -->
            <div id="date-picker-dropdown" style="
                display:none;
                position:absolute;
                background:#fff;
                border:1.5px solid #e5e7eb;
                border-radius:14px;
                padding:8px;
                box-shadow:0 10px 40px rgba(0,0,0,.12);
                z-index:1000;
                min-width:200px;
            ">
                <div onclick="selectRange(7, '7 Hari Terakhir')" class="range-option" style="padding:10px 14px;border-radius:8px;cursor:pointer;font-size:13px;">7 Hari Terakhir</div>
                <div onclick="selectRange(14, '14 Hari Terakhir')" class="range-option" style="padding:10px 14px;border-radius:8px;cursor:pointer;font-size:13px;">14 Hari Terakhir</div>
                <div onclick="selectRange(30, '30 Hari Terakhir')" class="range-option" style="padding:10px 14px;border-radius:8px;cursor:pointer;font-size:13px;">30 Hari Terakhir</div>
                <hr style="margin:6px 0;border:none;border-top:1px solid #f3f4f6;">
                <div style="padding:10px 14px;font-size:13px;color:#374151;font-weight:600;">Pilih Tanggal:</div>
                <div style="padding:0 14px 10px;display:flex;flex-direction:column;gap:8px;">
                    <input type="date" id="date-start" style="padding:8px;border:1.5px solid #e5e7eb;border-radius:8px;font-size:12px;width:100%;">
                    <input type="date" id="date-end" style="padding:8px;border:1.5px solid #e5e7eb;border-radius:8px;font-size:12px;width:100%;">
                    <button onclick="applyCustomRange()" style="padding:8px;background:#2563eb;color:#fff;border:none;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;">
                        Terapkan
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- CANVAS -->
    <div style="height:260px;width:100%;">
        <canvas id="clickChart"></canvas>
    </div>
</div>



<!-- ================= WITHDRAW MODAL ================= -->
<div id="withdraw-modal"
     style="display:none;position:fixed;inset:0;
            background:rgba(0,0,0,.5);
            z-index:99999;
            align-items:center;
            justify-content:center;
            padding:16px;">

    <div style="
        background:#ffffff;
        width:100%;
        max-width:560px;
        max-height:90vh;
        border-radius:18px;
        box-shadow:0 20px 60px rgba(0,0,0,.3);
        overflow:hidden;
        display:flex;
        flex-direction:column;
    ">

        <!-- HEADER -->
        <div style="
            padding:20px 24px;
            border-bottom:1px solid #eee;
            display:flex;
            align-items:center;
            justify-content:space-between;
        ">
            <div style="display:flex;align-items:center;gap:10px;">
                <div style="
                    width:36px;height:36px;
                    border-radius:10px;
                    background:#e0edff;
                    display:flex;
                    align-items:center;
                    justify-content:center;
                    color:#2563eb;
                ">
                    <i class="fas fa-wallet"></i>
                </div>
                <h3 style="margin:0;font-size:18px;font-weight:700;">
                    Tarik Saldo
                </h3>
            </div>
            <button onclick="closeWithdrawModal()"
                    style="border:none;background:none;
                           font-size:24px;cursor:pointer;color:#6b7280;">
                ×
            </button>
        </div>

        <!-- BODY -->
        <div style="padding:24px;overflow-y:auto;">

            <!-- INFO -->
            <div style="
                background:#eef6ff;
                border:2px solid #3b82f6;
                border-radius:14px;
                padding:16px;
                margin-bottom:20px;
                font-size:14px;
                color:#1e40af;
            ">
                <strong>Informasi Penarikan</strong><br>
                Saldo Tersedia:
                <b>Rp {{ number_format($balance ?? 0,0,',','.') }}</b><br>
                Minimal Penarikan:
                <b>Rp 50.000</b>
            </div>

            <!-- FORM -->
            <form id="form-withdraw"
                  onsubmit="handleWithdrawSubmit(event)"
                  style="display:flex;flex-direction:column;gap:14px;">
                @csrf

                <div>
                    <label style="font-weight:600;font-size:14px;margin-bottom:6px;display:block;">
                        Jumlah Penarikan (Rp) *
                    </label>
                    <input type="number"
                           name="amount"
                           min="50000"
                           max="{{ $balance ?? 0 }}"
                           placeholder="Contoh: 100000"
                           required
                           style="width:100%;padding:12px;
                                  border:2px solid #e5e7eb;
                                  border-radius:10px;
                                  font-size:14px;">
                </div>

                <div>
                    <label style="font-weight:600;font-size:14px;margin-bottom:6px;display:block;">
                        Nama Bank *
                    </label>
                    <select name="bank_name" required
                            style="width:100%;padding:12px;
                                   border:2px solid #e5e7eb;
                                   border-radius:10px;
                                   font-size:14px;">
                        <option value="">Pilih Bank</option>
                        <option value="BCA">BCA</option>
                        <option value="BNI">BNI</option>
                        <option value="BRI">BRI</option>
                        <option value="MANDIRI">Mandiri</option>
                        <option value="CIMB">CIMB</option>
                    </select>
                </div>

                <div>
                    <label style="font-weight:600;font-size:14px;margin-bottom:6px;display:block;">
                        Nomor Rekening *
                    </label>
                    <input type="text"
                           name="account_number"
                           placeholder="1234567890"
                           required
                           style="width:100%;padding:12px;
                                  border:2px solid #e5e7eb;
                                  border-radius:10px;
                                  font-size:14px;">
                </div>

                <div>
                    <label style="font-weight:600;font-size:14px;margin-bottom:6px;display:block;">
                        Nama Pemilik Rekening *
                    </label>
                    <input type="text"
                           name="account_name"
                           placeholder="Sesuai rekening bank"
                           required
                           style="width:100%;padding:12px;
                                  border:2px solid #e5e7eb;
                                  border-radius:10px;
                                  font-size:14px;">
                </div>

                <div>
                    <label style="font-weight:600;font-size:14px;margin-bottom:6px;display:block;">
                        Catatan (Opsional)
                    </label>
                    <textarea name="notes" rows="3"
                              style="width:100%;padding:12px;
                                     border:2px solid #e5e7eb;
                                     border-radius:10px;
                                     font-size:14px;
                                     resize:vertical;"></textarea>
                </div>

                <div style="display:flex;gap:12px;margin-top:8px;">
                    <button type="button"
                            onclick="closeWithdrawModal()"
                            style="flex:1;padding:14px;
                                   border-radius:10px;
                                   border:none;
                                   background:#f1f5f9;
                                   color:#374151;
                                   font-size:14px;
                                   font-weight:600;
                                   cursor:pointer;">
                        Batal
                    </button>
                    <button type="submit"
                            id="submit-withdraw-btn"
                            style="flex:1;padding:14px;
                                   border-radius:10px;
                                   border:none;
                                   background:#2563eb;
                                   color:#fff;
                                   font-size:14px;
                                   font-weight:600;
                                   cursor:pointer;">
                        Ajukan Penarikan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ================= JS ================= -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
let chartInstance = null;

// Data awal dari blade
const initialLabels = @json($labels);
const initialViews  = @json($data);          // data klik/views halaman
const initialClicks = @json($clicksData);

function buildChart(labels, views, clicks) {
    const ctx = document.getElementById('clickChart').getContext('2d');

    if (chartInstance) chartInstance.destroy();

    chartInstance = new Chart(ctx, {
        type: 'bar',
        data: {
            labels,
            datasets: [
                {
                    label: 'Views',
                    data: views,
                    backgroundColor: '#f59e0b',
                    borderRadius: 6,
                    barPercentage: 0.5,
                    categoryPercentage: 0.6,
                },
                {
                    label: 'Klik Produk',
                    data: clicks,
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
                    callbacks: {
                        label: ctx => ` ${ctx.dataset.label}: ${ctx.parsed.y}`
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { precision: 0 },
                    grid: { color: '#f3f4f6' }
                },
                x: {
                    grid: { display: false }
                }
            }
        }
    });
}

document.addEventListener('DOMContentLoaded', () => {
    buildChart(initialLabels, initialViews, initialClicks);
});

// ===== DATE PICKER =====
function toggleDatePicker() {
    const dd = document.getElementById('date-picker-dropdown');
    dd.style.display = dd.style.display === 'none' ? 'block' : 'none';
}

document.addEventListener('click', e => {
    const dd = document.getElementById('date-picker-dropdown');
    if (!e.target.closest('#date-picker-dropdown') && !e.target.closest('[onclick="toggleDatePicker()"]')) {
        dd.style.display = 'none';
    }
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
        document.getElementById('total-clicks-label').textContent = json.total_clicks.toLocaleString('id-ID');
    } catch(e) {
        console.error(e);
    }
}

// ===== RANGE OPTION HOVER =====
document.querySelectorAll('.range-option').forEach(el => {
    el.addEventListener('mouseenter', () => el.style.background = '#f3f4f6');
    el.addEventListener('mouseleave', () => el.style.background = 'transparent');
});
//--------

function openWithdrawModal() {
    const modal = document.getElementById('withdraw-modal');
    modal.style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function closeWithdrawModal() {
    const modal = document.getElementById('withdraw-modal');
    modal.style.display = 'none';
    document.body.style.overflow = '';
}

async function handleWithdrawSubmit(event) {
    event.preventDefault();

    const btn = document.getElementById('submit-withdraw-btn');
    const original = btn.innerText;

    btn.disabled = true;
    btn.innerText = 'Memproses...';

    const formData = new FormData(event.target);
    const token = document
        .querySelector('meta[name="csrf-token"]')
        .getAttribute('content');

    try {
        const res = await fetch('/withdrawal', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': token,
                'Accept': 'application/json'
            },
            body: formData
        });

        const result = await res.json();

        if (result.success) {
            alert(result.message);
            window.location.reload();
        } else {
            alert(result.message || 'Gagal');
            btn.disabled = false;
            btn.innerText = original;
        }
    } catch (e) {
        alert('Terjadi kesalahan');
        btn.disabled = false;
        btn.innerText = original;
    }
}

document.addEventListener('keydown', e => {
    if (e.key === 'Escape') closeWithdrawModal();
});

// Close modal when clicking outside
document.getElementById('withdraw-modal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeWithdrawModal();
    }
});
</script>

@endsection