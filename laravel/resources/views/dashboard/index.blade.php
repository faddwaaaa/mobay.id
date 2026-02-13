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
        <div style="font-size:42px;font-weight:800;margin:8px 0;">
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

<!-- ================= CHART KLIK 7 HARI ================= -->
<div class="chart-card"
     style="
        background:#ffffff;
        border-radius:18px;
        padding:24px;
        margin-bottom:32px;
        box-shadow:0 10px 30px rgba(0,0,0,.06);
    ">

    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px;">
        <h3 style="font-size:16px;font-weight:700;">
            Performa Klik 7 Hari Terakhir
        </h3>
        <span style="font-size:13px;color:#6b7280;">
            Total {{ number_format($totalClicks) }} klik
        </span>
    </div>

    <!-- WRAPPER DENGAN HEIGHT -->
    <div style="height:260px; width:100%;">
        <canvas id="clickChart"></canvas>
    </div>
</div>

<!-- ================= RINGKASAN ================= -->
<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:20px;margin-bottom:32px;">
    <div class="stat-card">
        <h4>Total Klik</h4>
        <div class="stat-value">{{ number_format($totalClicks ?? 0) }}</div>
    </div>
    <div class="stat-card">
        <h4>Total Link</h4>
        <div class="stat-value">{{ $totalLinks ?? 0 }}</div>
        <small>{{ $activeLinks ?? 0 }} aktif</small>
    </div>
    <div class="stat-card">
        <h4>Konversi</h4>
        <div class="stat-value">0%</div>
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
document.addEventListener('DOMContentLoaded', function () {
    const ctx = document.getElementById('clickChart').getContext('2d');

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: @json($labels),
            datasets: [{
                label: 'Klik',
                data: @json($data),
                borderColor: '#2563eb',
                backgroundColor: 'rgba(37,99,235,0.15)',
                fill: true,
                tension: 0.4,
                pointRadius: 4,
                pointHoverRadius: 6,
                pointBackgroundColor: '#2563eb',
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0
                    }
                }
            }
        }
    });
});

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