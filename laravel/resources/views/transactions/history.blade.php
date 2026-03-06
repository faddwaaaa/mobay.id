@extends('layouts.dashboard')

@section('title', 'Riwayat Transaksi | Payou.id')

@section('content')
<style>
.riwayat-container {
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    color: #111827;
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
}

/* TABS */
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
    text-decoration: none;
    display: block;
}
.riwayat-tab.active {
    background: white;
    color: #2563eb;
    box-shadow: 0 2px 8px rgba(37,99,235,.1);
    border: 1px solid #e5e7eb;
}
.riwayat-tab:hover:not(.active) {
    color: #2563eb;
    background: rgba(255,255,255,.7);
}

/* Pagination */
.riwayat-pagination {
    margin-top: 24px;
    display: flex;
    justify-content: center;
}
.riwayat-pagination nav { display: flex; justify-content: center; }
.riwayat-pagination .pagination { display: flex; gap: 5px; list-style: none; margin: 0; padding: 0; }
.riwayat-pagination span[aria-current="page"] span,
.riwayat-pagination .page-item.active .page-link {
    background: #2563eb !important; border-color: #2563eb !important; color: white !important;
}

@media (max-width: 768px) {
    .riwayat-grid { grid-template-columns: 1fr !important; }
}

/* TAB CONTENT */
.riwayat-content { display: none; }
.riwayat-content.active { display: block; }

/* GRID 2 KOLOM */
.riwayat-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }

.riwayat-card {
    display: block;
    background: white;
    border-radius: 16px;
    padding: 20px;
    border: 1px solid #e5e7eb;
    text-decoration: none;
    color: inherit;
    transition: all 0.2s;
}
.riwayat-card:hover {
    border-color: #2563eb;
    box-shadow: 0 8px 20px rgba(37,99,235,.08);
    transform: translateY(-2px);
}

/* CARD HEADER */
.riwayat-card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 12px;
    gap: 10px;
    flex-wrap: wrap;
}
.riwayat-code {
    display: flex;
    align-items: center;
    gap: 8px;
    font-family: monospace;
    font-size: 12px;
    font-weight: 600;
    background: #f9fafb;
    padding: 4px 10px 4px 12px;
    border-radius: 20px;
    border: 1px solid #e5e7eb;
    overflow: hidden;
    max-width: 100%;
}
.riwayat-code span {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    max-width: 160px;
}

/* COPY BUTTON */
.riwayat-copy-btn {
    font-size: 11px;
    font-weight: 500;
    padding: 4px 10px;
    border-radius: 16px;
    border: 1px solid #e5e7eb;
    background: white;
    color: #4b5563;
    cursor: pointer;
    transition: all 0.2s;
    flex-shrink: 0;
}
.riwayat-copy-btn:hover { background: #2563eb; color: white; border-color: #2563eb; }
.riwayat-copy-btn.copied { background: #10b981 !important; color: white !important; border-color: #10b981 !important; }

/* STATUS BADGE */
.riwayat-badge {
    font-size: 11px;
    font-weight: 700;
    padding: 4px 12px;
    border-radius: 99px;
    white-space: nowrap;
    flex-shrink: 0;
}
.badge-success { background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
.badge-pending  { background: #fef9c3; color: #854d0e; border: 1px solid #fde047; }
.badge-failed   { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }

/* AMOUNT & DATE */
.riwayat-amount {
    font-size: 20px;
    font-weight: 700;
    color: #2563eb;
    margin-bottom: 5px;
}
.riwayat-date {
    font-size: 12.5px;
    color: #9ca3af;
    display: flex;
    align-items: center;
    gap: 5px;
}

/* EMPTY STATE */
.riwayat-empty {
    text-align: center;
    padding: 60px 30px;
    background: white;
    border-radius: 16px;
    border: 1px dashed #d1d5db;
    grid-column: 1 / -1;
}
.riwayat-empty-icon { font-size: 48px; margin-bottom: 15px; opacity: .7; }
.riwayat-empty p { font-size: 15px; color: #6b7280; }

/* PAGINATION */
.riwayat-pagination { margin-top: 24px; display: flex; justify-content: center; }
.riwayat-pagination .pagination { display: flex; gap: 5px; list-style: none; }
.riwayat-pagination .page-link {
    display: block; padding: 8px 14px;
    border: 1px solid #e5e7eb; background: white;
    color: #4b5563; border-radius: 8px;
    text-decoration: none; font-size: 14px; transition: all .2s;
}
.riwayat-pagination .page-link:hover { background: #f3f4f6; border-color: #9ca3af; color: #111827; }
.riwayat-pagination .active .page-link { background: #2563eb; border-color: #2563eb; color: white; }
.riwayat-pagination .disabled .page-link { opacity: .5; pointer-events: none; background: #f3f4f6; }

/* RESPONSIVE */
@media (max-width: 768px) {
    .riwayat-grid { grid-template-columns: 1fr; }
    .riwayat-container { padding: 15px; }
}
</style>

<div class="riwayat-container">

    {{-- HEADER --}}
    <div style="margin-bottom:24px;">
        <div style="display:flex;align-items:center;gap:12px;margin-bottom:8px;">
            <a href="{{ route('dashboard') }}" style="width:36px;height:36px;background:#ffffff;border:1px solid #e2e8f0;border-radius:8px;display:flex;align-items:center;justify-content:center;text-decoration:none;transition:all .2s;">
                <i class="fas fa-arrow-left" style="font-size:14px;color:#475569;"></i>
            </a>
            <div>
                <h1 style="margin:0;font-size:24px;font-weight:600;color:#000000;">Riwayat Transaksi</h1>
                <p style="margin:0;font-size:14px;color:#797979;">Lihat semua pembayaran dan penarikan Anda</p>
            </div>
        </div>
    </div>

    {{-- TABS --}}
    <div class="riwayat-tabs">
        <a href="?tab=payments" class="riwayat-tab {{ request('tab','payments') === 'payments' ? 'active' : '' }}">Pembayaran</a>
        <a href="?tab=withdrawals" class="riwayat-tab {{ request('tab') === 'withdrawals' ? 'active' : '' }}">Penarikan</a>
    </div>

    {{-- TAB: PEMBAYARAN --}}
    <div id="tab-payments" class="riwayat-content {{ request('tab','payments') === 'payments' ? 'active' : '' }}">
        <div class="riwayat-grid">
            @forelse($payments as $payment)
            <a href="{{ route('transactions.payment-detail', $payment->id) }}" class="riwayat-card">
                <div class="riwayat-card-header">
                    <div class="riwayat-code">
                        <span>{{ $payment->order_id }}</span>
                        <button class="riwayat-copy-btn" onclick="event.preventDefault();event.stopPropagation();copyToClipboard('{{ $payment->order_id }}',this)">Salin</button>
                    </div>
                    <span class="riwayat-badge {{ $payment->status === 'settlement' ? 'badge-success' : ($payment->status === 'pending' ? 'badge-pending' : 'badge-failed') }}">
                        {{ $payment->status === 'settlement' ? 'Berhasil' : ($payment->status === 'pending' ? 'Menunggu' : 'Gagal') }}
                    </span>
                </div>
                <div class="riwayat-amount">Rp {{ number_format($payment->amount, 0, ',', '.') }}</div>
                <div class="riwayat-date">📅 {{ $payment->created_at->format('d M Y, H:i') }}</div>
            </a>
            @empty
            <div class="riwayat-empty">
                <div class="riwayat-empty-icon">💳</div>
                <p>Belum ada riwayat pembayaran</p>
            </div>
            @endforelse
        </div>
        @if($payments->hasPages())
        <div class="riwayat-pagination">
            {{ $payments->appends(['tab' => 'payments', 'wpage' => request('wpage')])->links() }}
        </div>
        @endif
    </div>

    {{-- TAB: PENARIKAN --}}
    <div id="tab-withdrawals" class="riwayat-content {{ request('tab') === 'withdrawals' ? 'active' : '' }}">
        <div class="riwayat-grid">
            @forelse($withdrawals as $withdrawal)
            <a href="{{ route('transactions.withdrawal-detail', $withdrawal->id) }}" class="riwayat-card">
                <div class="riwayat-card-header">
                    <div class="riwayat-code">
                        <span>{{ $withdrawal->payout_id ?? 'WD-'.$withdrawal->id }}</span>
                        <button class="riwayat-copy-btn" onclick="event.preventDefault();event.stopPropagation();copyToClipboard('{{ $withdrawal->payout_id ?? 'WD-'.$withdrawal->id }}',this)">Salin</button>
                    </div>
                    <span class="riwayat-badge {{ $withdrawal->status === 'approved' ? 'badge-success' : ($withdrawal->status === 'pending' ? 'badge-pending' : 'badge-failed') }}">
                        {{ $withdrawal->status === 'approved' ? 'Disetujui' : ($withdrawal->status === 'pending' ? 'Menunggu' : 'Ditolak') }}
                    </span>
                </div>
                <div class="riwayat-amount">Rp {{ number_format($withdrawal->amount, 0, ',', '.') }}</div>
                <div class="riwayat-date">📅 {{ $withdrawal->created_at->format('d M Y, H:i') }}</div>
            </a>
            @empty
            <div class="riwayat-empty">
                <div class="riwayat-empty-icon">💰</div>
                <p>Belum ada riwayat penarikan</p>
            </div>
            @endforelse
        </div>
        @if($withdrawals->hasPages())
        <div class="riwayat-pagination">
            {{ $withdrawals->appends(['tab' => 'withdrawals', 'page' => request('page')])->links() }}
        </div>
        @endif
    </div>

</div>

<script>
function copyToClipboard(text, btn) {
    navigator.clipboard.writeText(text).then(() => {
        const orig = btn.innerText;
        btn.classList.add('copied');
        btn.innerText = '✓ Tersalin';
        setTimeout(() => { btn.classList.remove('copied'); btn.innerText = orig; }, 2000);
    });
}
</script>
@endsection