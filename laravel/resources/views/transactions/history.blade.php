@extends('layouts.dashboard')

@section('title', 'Riwayat Transaksi | Payou.id')

@section('content')
<style>
/* ====================================
   RIWAYAT TRANSAKSI - FIXED VERSION
   ==================================== */

/* Reset dasar untuk halaman riwayat */
.riwayat-container * {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

.riwayat-container {
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    color: #111827;
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
}

/* Header */
.riwayat-header {
    margin-bottom: 25px;
}

.riwayat-header h1 {
    font-size: 26px;
    font-weight: 700;
    color: #111827;
    margin-bottom: 5px;
}

.riwayat-header p {
    font-size: 14px;
    color: #6b7280;
}

/* Tabs */
.riwayat-tabs {
    display: flex;
    gap: 8px;
    background: #f3f4f6;
    padding: 6px;
    border-radius: 12px;
    margin-bottom: 25px;
    border: 1px solid #e5e7eb;
}

.riwayat-tab {
    flex: 1;
    padding: 12px 0;
    border: none;
    background: transparent;
    font-weight: 600;
    font-size: 14px;
    border-radius: 8px;
    cursor: pointer;
    color: #6b7280;
    text-align: center;
    transition: all 0.2s;
}

.riwayat-tab.active {
    background: white;
    color: #2563eb;
    box-shadow: 0 2px 8px rgba(37, 99, 235, 0.1);
    border: 1px solid #e5e7eb;
}

.riwayat-tab:hover:not(.active) {
    color: #2563eb;
    background: rgba(255, 255, 255, 0.7);
}

/* Tab Content */
.riwayat-content {
    display: none;
}

.riwayat-content.active {
    display: block;
}

/* Cards */
.riwayat-card {
    display: block;
    background: white;
    border-radius: 16px;
    padding: 20px;
    margin-bottom: 15px;
    border: 1px solid #e5e7eb;
    text-decoration: none;
    color: inherit;
    transition: all 0.2s;
}

.riwayat-card:hover {
    border-color: #2563eb;
    box-shadow: 0 8px 20px rgba(37, 99, 235, 0.08);
    transform: translateY(-2px);
}

/* Card Header */
.riwayat-card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 12px;
    flex-wrap: wrap;
    gap: 10px;
}

.riwayat-code {
    display: flex;
    align-items: center;
    gap: 10px;
    font-family: monospace;
    font-size: 13px;
    font-weight: 600;
    background: #f9fafb;
    padding: 4px 10px 4px 12px;
    border-radius: 20px;
    border: 1px solid #e5e7eb;
}

/* Copy Button */
.riwayat-copy-btn {
    font-size: 11px;
    font-weight: 500;
    padding: 5px 12px;
    border-radius: 16px;
    border: 1px solid #e5e7eb;
    background: white;
    color: #4b5563;
    cursor: pointer;
    transition: all 0.2s;
}

.riwayat-copy-btn:hover {
    background: #2563eb;
    color: white;
    border-color: #2563eb;
}

.riwayat-copy-btn.copied {
    background: #10b981 !important;
    color: white !important;
    border-color: #10b981 !important;
}

/* Status Badges */
.riwayat-badge {
    font-size: 12px;
    font-weight: 600;
    padding: 6px 16px;
    border-radius: 100px;
    min-width: 90px;
    text-align: center;
}

.badge-success {
    background: #dcfce7;
    color: #166534;
    border: 1px solid #bbf7d0;
}

.badge-pending {
    background: #fef9c3;
    color: #854d0e;
    border: 1px solid #fde047;
}

.badge-failed {
    background: #fee2e2;
    color: #991b1b;
    border: 1px solid #fecaca;
}

/* Amount */
.riwayat-amount {
    font-size: 22px;
    font-weight: 700;
    color: #2563eb;
    margin-bottom: 5px;
}

/* Date */
.riwayat-date {
    font-size: 13px;
    color: #9ca3af;
    display: flex;
    align-items: center;
    gap: 5px;
}

.riwayat-date:before {
    content: "📅";
    font-size: 11px;
    opacity: 0.7;
}

/* Empty State */
.riwayat-empty {
    text-align: center;
    padding: 60px 30px;
    background: white;
    border-radius: 16px;
    border: 1px dashed #d1d5db;
}

.riwayat-empty-icon {
    font-size: 48px;
    margin-bottom: 15px;
    opacity: 0.7;
}

.riwayat-empty p {
    font-size: 15px;
    color: #6b7280;
}

/* Pagination */
.riwayat-pagination {
    margin-top: 30px;
    display: flex;
    justify-content: center;
}

.riwayat-pagination .pagination {
    display: flex;
    gap: 5px;
    list-style: none;
}

.riwayat-pagination .page-item {
    display: inline-block;
}

.riwayat-pagination .page-link {
    display: block;
    padding: 8px 14px;
    border: 1px solid #e5e7eb;
    background: white;
    color: #4b5563;
    border-radius: 8px;
    text-decoration: none;
    font-size: 14px;
    transition: all 0.2s;
}

.riwayat-pagination .page-link:hover {
    background: #f3f4f6;
    border-color: #9ca3af;
    color: #111827;
}

.riwayat-pagination .active .page-link {
    background: #2563eb;
    border-color: #2563eb;
    color: white;
}

.riwayat-pagination .disabled .page-link {
    opacity: 0.5;
    pointer-events: none;
    background: #f3f4f6;
}

/* Responsive */
@media (max-width: 768px) {
    .riwayat-container {
        padding: 15px;
    }
    
    .riwayat-card-header {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .riwayat-code {
        width: 100%;
        justify-content: space-between;
    }
    
    .riwayat-amount {
        font-size: 20px;
    }
}

@media (max-width: 480px) {
    .riwayat-tabs {
        flex-direction: column;
    }
    
    .riwayat-header h1 {
        font-size: 22px;
    }
}
</style>

<div class="riwayat-container">
    <!-- Header -->
        {{-- Header Section --}}
        <div style="margin-bottom: 24px;">
            <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 8px;">
                <a href="{{ route('dashboard') }}" style="width: 36px; height: 36px; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 8px; display: flex; align-items: center; justify-content: center; text-decoration: none; transition: all 0.2s;">
                    <i class="fas fa-arrow-left" style="font-size: 14px; color: #475569;"></i>
                </a>
                <div>
                    <h1 style="margin: 0; font-size: 24px; font-weight: 600; color: #000000;">Riwayat Transaksi</h1>
                    <p style="margin: 0; font-size: 14px; color: #797979;">Lihat semua pembayaran dan penarikan Anda</p>
                </div>
            </div>
        </div>

    <!-- Tabs -->
    <div class="riwayat-tabs">
        <button class="riwayat-tab active" onclick="switchTab('payments')">Pembayaran</button>
        <button class="riwayat-tab" onclick="switchTab('withdrawals')">Penarikan</button>
    </div>

    <!-- Tab Content: Pembayaran -->
    <div id="tab-payments" class="riwayat-content active">
        @forelse($payments as $payment)
            <a href="{{ route('transactions.payment-detail', $payment->id) }}" class="riwayat-card">
                <div class="riwayat-card-header">
                    <div class="riwayat-code">
                        <span>{{ $payment->order_id }}</span>
                        <button class="riwayat-copy-btn" onclick="event.preventDefault(); event.stopPropagation(); copyToClipboard('{{ $payment->order_id }}', this)">
                            Salin
                        </button>
                    </div>
                    <span class="riwayat-badge badge-{{ $payment->status === 'settlement' ? 'success' : ($payment->status === 'pending' ? 'pending' : 'failed') }}">
                        {{ $payment->status === 'settlement' ? 'Berhasil' : ($payment->status === 'pending' ? 'Menunggu' : 'Gagal') }}
                    </span>
                </div>
                <div class="riwayat-amount">Rp {{ number_format($payment->amount, 0, ',', '.') }}</div>
                <div class="riwayat-date">{{ $payment->created_at->format('d M Y, H:i') }}</div>
            </a>
        @empty
            <div class="riwayat-empty">
                <div class="riwayat-empty-icon">💳</div>
                <p>Belum ada riwayat pembayaran</p>
            </div>
        @endforelse

        @if($payments->hasPages())
            <div class="riwayat-pagination">
                {{ $payments->links() }}
            </div>
        @endif
    </div>

    <!-- Tab Content: Penarikan -->
    <div id="tab-withdrawals" class="riwayat-content">
        @forelse($withdrawals as $withdrawal)
            <a href="{{ route('transactions.withdrawal-detail', $withdrawal->id) }}" class="riwayat-card">
                <div class="riwayat-card-header">
                    <div class="riwayat-code">
                        <span>{{ $withdrawal->payout_id ?? 'WD-' . $withdrawal->id }}</span>
                        <button class="riwayat-copy-btn" onclick="event.preventDefault(); event.stopPropagation(); copyToClipboard('{{ $withdrawal->payout_id ?? 'WD-' . $withdrawal->id }}', this)">
                            Salin
                        </button>
                    </div>
                    <span class="riwayat-badge badge-{{ $withdrawal->status === 'completed' ? 'success' : ($withdrawal->status === 'pending' ? 'pending' : 'failed') }}">
                        {{ $withdrawal->status === 'completed' ? 'Selesai' : ($withdrawal->status === 'pending' ? 'Menunggu' : 'Gagal') }}
                    </span>
                </div>
                <div class="riwayat-amount">Rp {{ number_format($withdrawal->amount, 0, ',', '.') }}</div>
                <div class="riwayat-date">{{ $withdrawal->created_at->format('d M Y, H:i') }}</div>
            </a>
        @empty
            <div class="riwayat-empty">
                <div class="riwayat-empty-icon">💰</div>
                <p>Belum ada riwayat penarikan</p>
            </div>
        @endforelse

        @if($withdrawals->hasPages())
            <div class="riwayat-pagination">
                {{ $withdrawals->links() }}
            </div>
        @endif
    </div>
</div>

<script>
function switchTab(tabName) {
    // Sembunyikan semua content
    document.querySelectorAll('.riwayat-content').forEach(el => {
        el.classList.remove('active');
    });
    
    // Non-aktifkan semua tab
    document.querySelectorAll('.riwayat-tab').forEach(el => {
        el.classList.remove('active');
    });
    
    // Aktifkan tab yang dipilih
    document.getElementById('tab-' + tabName).classList.add('active');
    event.target.classList.add('active');
}

function copyToClipboard(text, btn) {
    navigator.clipboard.writeText(text).then(() => {
        const originalText = btn.innerText;
        btn.classList.add('copied');
        btn.innerText = '✓ Tersalin';
        
        setTimeout(() => {
            btn.classList.remove('copied');
            btn.innerText = originalText;
        }, 2000);
    });
}
</script>
@endsection