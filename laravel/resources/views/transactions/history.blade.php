@extends('layouts.dashboard')

@section('title', 'Riwayat Transaksi | Payou.id')

@section('content')
<style>
    .tabs { display:flex; gap:8px; border-bottom:2px solid #e5e7eb; margin-bottom:24px; }
    .tab { padding:12px 20px; border:none; background:none; color:#6b7280; font-weight:600; font-size:14px; cursor:pointer; border-bottom:3px solid transparent; transition:all 0.2s; }
    .tab:hover { color:#2563eb; }
    .tab.active { color:#2563eb; border-bottom-color:#2563eb; }
    .tab-content { display:none; }
    .tab-content.active { display:block; }

    .transaction-card { background:#fff; border:1px solid #e5e7eb; border-radius:12px; padding:16px; margin-bottom:12px; cursor:pointer; transition:all 0.2s; }
    .transaction-card:hover { border-color:#2563eb; box-shadow:0 4px 12px rgba(37,99,235,0.1); }

    .transaction-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:8px; }
    .transaction-code { font-size:13px; font-weight:700; color:#111827; font-family:monospace; display:flex; align-items:center; gap:8px; }

    .copy-btn { padding:4px 8px; border:1px solid #d1d5db; border-radius:6px; background:#f9fafb; color:#6b7280; font-size:11px; cursor:pointer; transition:all 0.2s; }
    .copy-btn:hover { background:#2563eb; color:#fff; border-color:#2563eb; }

    .status-badge { padding:4px 12px; border-radius:20px; font-size:11px; font-weight:600; }
    .status-success { background:#d1fae5; color:#065f46; }
    .status-pending  { background:#fef3c7; color:#92400e; }
    .status-failed   { background:#fee2e2; color:#991b1b; }

    .transaction-amount { font-size:18px; font-weight:800; color:#2563eb; margin-bottom:4px; }
    .transaction-date   { font-size:12px; color:#9ca3af; }

    .detail-row { display:flex; justify-content:space-between; padding:12px 0; border-bottom:1px solid #f3f4f6; }
    .detail-row:last-child { border-bottom:none; }
    .detail-label { font-size:13px; color:#6b7280; }
    .detail-value { font-size:14px; font-weight:600; color:#111827; text-align:right; }

    .empty-state { text-align:center; padding:60px 20px; color:#9ca3af; }
    .empty-state-icon { font-size:48px; margin-bottom:12px; }

    @media (max-width:768px) {
        .transaction-header { flex-direction:column; align-items:flex-start; gap:8px; }
    }
</style>

<div style="margin-bottom:24px;">
    <h1 style="font-size:24px; font-weight:700; margin-bottom:4px;">Riwayat Transaksi</h1>
    <p style="font-size:14px; color:#6b7280;">Lihat semua pembayaran dan penarikan Anda</p>
</div>

<!-- Tabs -->
<div class="tabs">
    <button class="tab active" onclick="switchTab('payments')">Pembayaran</button>
    <button class="tab" onclick="switchTab('withdrawals')">Penarikan</button>
</div>

<!-- Tab: Pembayaran -->
<div id="tab-payments" class="tab-content active">
    @forelse($payments as $payment)
        <div class="transaction-card" onclick="showPaymentDetail({{ $payment->id }})">
            <div class="transaction-header">
                <div class="transaction-code">
                    <span>{{ $payment->order_id }}</span>
                    <button class="copy-btn" onclick="event.stopPropagation(); copyToClipboard('{{ $payment->order_id }}', this)">Salin</button>
                </div>
                <span class="status-badge status-{{ $payment->status === 'settlement' ? 'success' : ($payment->status === 'pending' ? 'pending' : 'failed') }}">
                    {{ $payment->status === 'settlement' ? 'Berhasil' : ($payment->status === 'pending' ? 'Menunggu' : 'Gagal') }}
                </span>
            </div>
            <div class="transaction-amount">Rp {{ number_format($payment->amount, 0, ',', '.') }}</div>
            <div class="transaction-date">{{ $payment->created_at->format('d M Y, H:i') }}</div>
        </div>
    @empty
        <div class="empty-state">
            <div class="empty-state-icon">💳</div>
            <p>Belum ada riwayat pembayaran</p>
        </div>
    @endforelse
    @if($payments->hasPages())
        <div style="margin-top:24px;">{{ $payments->links() }}</div>
    @endif
</div>

<!-- Tab: Penarikan -->
<div id="tab-withdrawals" class="tab-content">
    @forelse($withdrawals as $withdrawal)
        <div class="transaction-card" onclick="showWithdrawalDetail({{ $withdrawal->id }})">
            <div class="transaction-header">
                <div class="transaction-code">
                    <span>{{ $withdrawal->payout_id ?? 'WD-' . $withdrawal->id }}</span>
                    <button class="copy-btn" onclick="event.stopPropagation(); copyToClipboard('{{ $withdrawal->payout_id ?? 'WD-' . $withdrawal->id }}', this)">Salin</button>
                </div>
                <span class="status-badge status-{{ $withdrawal->status === 'completed' ? 'success' : ($withdrawal->status === 'pending' || $withdrawal->status === 'approved' ? 'pending' : 'failed') }}">
                    {{ $withdrawal->status === 'completed' ? 'Selesai' : ($withdrawal->status === 'pending' ? 'Menunggu' : ($withdrawal->status === 'approved' ? 'Disetujui' : 'Gagal')) }}
                </span>
            </div>
            <div class="transaction-amount">Rp {{ number_format($withdrawal->amount, 0, ',', '.') }}</div>
            <div class="transaction-date">{{ $withdrawal->created_at->format('d M Y, H:i') }}</div>
        </div>
    @empty
        <div class="empty-state">
            <div class="empty-state-icon">💰</div>
            <p>Belum ada riwayat penarikan</p>
        </div>
    @endforelse
    @if($withdrawals->hasPages())
        <div style="margin-top:24px;">{{ $withdrawals->links() }}</div>
    @endif
</div>

@endsection

@push('modals')

{{-- ============================================================
     OVERLAY GLOBAL (dipakai bersama oleh kedua modal)
============================================================ --}}
<div id="riwayatOverlay"
     class="fixed inset-0 hidden"
     style="z-index:9990; background:rgba(15,23,42,0.5); backdrop-filter:blur(5px); -webkit-backdrop-filter:blur(5px);
            opacity:0; transition:opacity 0.2s ease;"
     onclick="_closeActiveModal()">
</div>

{{-- ============================================================
     MODAL DETAIL PEMBAYARAN
============================================================ --}}
<div id="modal-payment"
     class="fixed inset-0 hidden"
     style="z-index:9999; display:none; align-items:center; justify-content:center; padding:16px; pointer-events:none;">

    <div id="modal-payment-card"
         style="background:#fff; border-radius:16px; max-width:500px; width:100%; max-height:90vh; overflow-y:auto;
                pointer-events:auto; opacity:0; transform:translateY(20px);
                transition:opacity 0.2s ease, transform 0.2s ease;">

        <div style="padding:20px; border-bottom:1px solid #e5e7eb; display:flex; justify-content:space-between; align-items:center; position:sticky; top:0; background:#fff; z-index:1;">
            <h3 style="font-size:18px; font-weight:700; color:#111827; margin:0;">Detail Pembayaran</h3>
            <button onclick="closeModal('modal-payment')"
                    style="background:none; border:none; font-size:22px; color:#6b7280; cursor:pointer;
                           width:32px; height:32px; display:flex; align-items:center; justify-content:center;
                           border-radius:8px; transition:background 0.15s;"
                    onmouseenter="this.style.background='#f3f4f6'" onmouseleave="this.style.background='none'">
                ×
            </button>
        </div>

        <div id="payment-detail-content" style="padding:20px;">
            <p style="text-align:center; color:#9ca3af;">Memuat...</p>
        </div>
    </div>
</div>

{{-- ============================================================
     MODAL DETAIL PENARIKAN
============================================================ --}}
<div id="modal-withdrawal"
     class="fixed inset-0 hidden"
     style="z-index:9999; display:none; align-items:center; justify-content:center; padding:16px; pointer-events:none;">

    <div id="modal-withdrawal-card"
         style="background:#fff; border-radius:16px; max-width:500px; width:100%; max-height:90vh; overflow-y:auto;
                pointer-events:auto; opacity:0; transform:translateY(20px);
                transition:opacity 0.2s ease, transform 0.2s ease;">

        <div style="padding:20px; border-bottom:1px solid #e5e7eb; display:flex; justify-content:space-between; align-items:center; position:sticky; top:0; background:#fff; z-index:1;">
            <h3 style="font-size:18px; font-weight:700; color:#111827; margin:0;">Detail Penarikan</h3>
            <button onclick="closeModal('modal-withdrawal')"
                    style="background:none; border:none; font-size:22px; color:#6b7280; cursor:pointer;
                           width:32px; height:32px; display:flex; align-items:center; justify-content:center;
                           border-radius:8px; transition:background 0.15s;"
                    onmouseenter="this.style.background='#f3f4f6'" onmouseleave="this.style.background='none'">
                ×
            </button>
        </div>

        <div id="withdrawal-detail-content" style="padding:20px;">
            <p style="text-align:center; color:#9ca3af;">Memuat...</p>
        </div>
    </div>
</div>

<script>
// ===== MODAL HELPERS =====
let _activeModalId = null;

function _openModal(modalId) {
    const overlay = document.getElementById('riwayatOverlay');
    const modal   = document.getElementById(modalId);
    const card    = document.getElementById(modalId + '-card');

    _activeModalId = modalId;

    overlay.classList.remove('hidden');
    modal.classList.remove('hidden');
    modal.style.display      = 'flex';
    modal.style.pointerEvents = 'auto';

    card.getBoundingClientRect(); // force reflow
    overlay.style.opacity = '1';
    card.style.opacity    = '1';
    card.style.transform  = 'translateY(0)';

    document.body.style.overflow = 'hidden';
}

function closeModal(modalId) {
    const overlay = document.getElementById('riwayatOverlay');
    const modal   = document.getElementById(modalId);
    const card    = document.getElementById(modalId + '-card');

    overlay.style.opacity = '0';
    card.style.opacity    = '0';
    card.style.transform  = 'translateY(20px)';

    setTimeout(() => {
        modal.style.display = 'none';
        modal.classList.add('hidden');
        overlay.classList.add('hidden');
        document.body.style.overflow = '';
        _activeModalId = null;
    }, 200);
}

function _closeActiveModal() {
    if (_activeModalId) closeModal(_activeModalId);
}

document.addEventListener('keydown', e => {
    if (e.key === 'Escape' && _activeModalId) closeModal(_activeModalId);
});

// ===== TABS =====
function switchTab(tabName) {
    document.querySelectorAll('.tab-content').forEach(el => el.classList.remove('active'));
    document.querySelectorAll('.tab').forEach(el => el.classList.remove('active'));
    document.getElementById('tab-' + tabName).classList.add('active');
    event.target.classList.add('active');
}

// ===== COPY =====
function copyToClipboard(text, btn) {
    navigator.clipboard.writeText(text).then(() => {
        const orig = btn.innerText;
        btn.innerText = '✓ Tersalin';
        btn.style.background  = '#10b981';
        btn.style.color       = '#fff';
        btn.style.borderColor = '#10b981';
        setTimeout(() => {
            btn.innerText = orig;
            btn.style.background = btn.style.color = btn.style.borderColor = '';
        }, 2000);
    });
}

// ===== DETAIL ROWS HELPER =====
function _detailRow(label, value, style = '') {
    return `<div class="detail-row">
        <span class="detail-label">${label}</span>
        <span class="detail-value" ${style ? `style="${style}"` : ''}>${value}</span>
    </div>`;
}

// ===== PAYMENT DETAIL =====
async function showPaymentDetail(id) {
    const content = document.getElementById('payment-detail-content');
    content.innerHTML = '<p style="text-align:center;color:#9ca3af;padding:20px 0;">Memuat...</p>';
    _openModal('modal-payment');

    try {
        const res    = await fetch(`/riwayat/pembayaran/${id}`);
        const result = await res.json();

        if (result.success) {
            const d     = result.data;
            const notes = d.notes || {};
            const statusLabel = d.status === 'settlement' ? 'Berhasil' : (d.status === 'pending' ? 'Menunggu' : 'Gagal');
            const statusColor = d.status === 'settlement' ? '#065f46' : (d.status === 'pending' ? '#92400e' : '#991b1b');
            const statusBg    = d.status === 'settlement' ? '#d1fae5' : (d.status === 'pending' ? '#fef3c7' : '#fee2e2');

            content.innerHTML =
                _detailRow('Kode Transaksi', d.order_id) +
                _detailRow('ID Transaksi', d.transaction_id || '-') +
                _detailRow('Jumlah', `Rp ${new Intl.NumberFormat('id-ID').format(d.amount)}`, 'color:#2563eb;') +
                _detailRow('Status', `<span style="background:${statusBg};color:${statusColor};padding:3px 10px;border-radius:20px;font-size:12px;">${statusLabel}</span>`) +
                _detailRow('Metode', (d.payment_method?.toUpperCase() || '-')) +
                (notes.product_title ? _detailRow('Produk', notes.product_title) : '') +
                (notes.buyer_name    ? _detailRow('Nama Pembeli', notes.buyer_name) : '') +
                _detailRow('Tanggal', d.created_at);
        } else {
            content.innerHTML = '<p style="text-align:center;color:#ef4444;">Gagal memuat detail</p>';
        }
    } catch {
        content.innerHTML = '<p style="text-align:center;color:#ef4444;">Gagal memuat detail</p>';
    }
}

// ===== WITHDRAWAL DETAIL =====
async function showWithdrawalDetail(id) {
    const content = document.getElementById('withdrawal-detail-content');
    content.innerHTML = '<p style="text-align:center;color:#9ca3af;padding:20px 0;">Memuat...</p>';
    _openModal('modal-withdrawal');

    try {
        const res    = await fetch(`/riwayat/penarikan/${id}`);
        const result = await res.json();

        if (result.success) {
            const d = result.data;
            const statusLabel = d.status === 'completed' ? 'Selesai' : (d.status === 'pending' ? 'Menunggu' : (d.status === 'approved' ? 'Disetujui' : 'Gagal'));
            const statusColor = d.status === 'completed' ? '#065f46' : (d.status === 'failed' ? '#991b1b' : '#92400e');
            const statusBg    = d.status === 'completed' ? '#d1fae5' : (d.status === 'failed' ? '#fee2e2' : '#fef3c7');

            content.innerHTML =
                _detailRow('Kode Penarikan', d.payout_id || 'WD-' + d.id) +
                _detailRow('Jumlah', `Rp ${new Intl.NumberFormat('id-ID').format(d.amount)}`, 'color:#2563eb;') +
                _detailRow('Status', `<span style="background:${statusBg};color:${statusColor};padding:3px 10px;border-radius:20px;font-size:12px;">${statusLabel}</span>`) +
                _detailRow('Bank', d.bank_name) +
                _detailRow('Nomor Rekening', d.account_number) +
                _detailRow('Nama Rekening', d.account_name) +
                (d.notes            ? _detailRow('Catatan', d.notes) : '') +
                (d.rejection_reason ? _detailRow('Alasan Ditolak', d.rejection_reason, 'color:#ef4444;') : '') +
                _detailRow('Tanggal Pengajuan', d.created_at) +
                (d.approved_at      ? _detailRow('Tanggal Disetujui', d.approved_at) : '');
        } else {
            content.innerHTML = '<p style="text-align:center;color:#ef4444;">Gagal memuat detail</p>';
        }
    } catch {
        content.innerHTML = '<p style="text-align:center;color:#ef4444;">Gagal memuat detail</p>';
    }
}
</script>

@endpush