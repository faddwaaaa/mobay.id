@extends('layouts.dashboard')

@section('title', 'Riwayat Transaksi | Payou.id')

@section('content')
<style>
    .tabs {
        display: flex;
        gap: 8px;
        border-bottom: 2px solid #e5e7eb;
        margin-bottom: 24px;
    }
    
    .tab {
        padding: 12px 20px;
        border: none;
        background: none;
        color: #6b7280;
        font-weight: 600;
        font-size: 14px;
        cursor: pointer;
        border-bottom: 3px solid transparent;
        transition: all 0.2s;
    }
    
    .tab:hover {
        color: #2563eb;
    }
    
    .tab.active {
        color: #2563eb;
        border-bottom-color: #2563eb;
    }
    
    .tab-content {
        display: none;
    }
    
    .tab-content.active {
        display: block;
    }
    
    .transaction-card {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        padding: 16px;
        margin-bottom: 12px;
        cursor: pointer;
        transition: all 0.2s;
    }
    
    .transaction-card:hover {
        border-color: #2563eb;
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.1);
    }
    
    .transaction-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 8px;
    }
    
    .transaction-code {
        font-size: 13px;
        font-weight: 700;
        color: #111827;
        font-family: monospace;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    .copy-btn {
        padding: 4px 8px;
        border: 1px solid #d1d5db;
        border-radius: 6px;
        background: #f9fafb;
        color: #6b7280;
        font-size: 11px;
        cursor: pointer;
        transition: all 0.2s;
    }
    
    .copy-btn:hover {
        background: #2563eb;
        color: #fff;
        border-color: #2563eb;
    }
    
    .status-badge {
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
    }
    
    .status-success {
        background: #d1fae5;
        color: #065f46;
    }
    
    .status-pending {
        background: #fef3c7;
        color: #92400e;
    }
    
    .status-failed {
        background: #fee2e2;
        color: #991b1b;
    }
    
    .transaction-amount {
        font-size: 18px;
        font-weight: 800;
        color: #2563eb;
        margin-bottom: 4px;
    }
    
    .transaction-date {
        font-size: 12px;
        color: #9ca3af;
    }
    
    .modal {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.5);
        z-index: 9999;
        align-items: center;
        justify-content: center;
        padding: 16px;
    }
    
    .modal.active {
        display: flex;
    }
    
    .modal-content {
        background: #fff;
        border-radius: 16px;
        max-width: 500px;
        width: 100%;
        max-height: 90vh;
        overflow-y: auto;
    }
    
    .modal-header {
        padding: 20px;
        border-bottom: 1px solid #e5e7eb;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .modal-header h3 {
        font-size: 18px;
        font-weight: 700;
        color: #111827;
    }
    
    .close-modal {
        background: none;
        border: none;
        font-size: 24px;
        color: #6b7280;
        cursor: pointer;
    }
    
    .modal-body {
        padding: 20px;
    }
    
    .detail-row {
        display: flex;
        justify-content: space-between;
        padding: 12px 0;
        border-bottom: 1px solid #f3f4f6;
    }
    
    .detail-row:last-child {
        border-bottom: none;
    }
    
    .detail-label {
        font-size: 13px;
        color: #6b7280;
    }
    
    .detail-value {
        font-size: 14px;
        font-weight: 600;
        color: #111827;
        text-align: right;
    }
    
    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #9ca3af;
    }
    
    .empty-state-icon {
        font-size: 48px;
        margin-bottom: 12px;
    }

    @media (max-width: 768px) {
        .transaction-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 8px;
        }
        
        .modal-content {
            max-width: 100%;
        }
    }
</style>

<div style="margin-bottom: 24px;">
    <h1 style="font-size: 24px; font-weight: 700; margin-bottom: 4px;">Riwayat Transaksi</h1>
    <p style="font-size: 14px; color: #6b7280;">Lihat semua pembayaran dan penarikan Anda</p>
</div>

<!-- Tabs -->
<div class="tabs">
    <button class="tab active" onclick="switchTab('payments')">Pembayaran</button>
    <button class="tab" onclick="switchTab('withdrawals')">Penarikan</button>
</div>

<!-- Tab Content: Pembayaran -->
<div id="tab-payments" class="tab-content active">
    @forelse($payments as $payment)
        <div class="transaction-card" onclick="showPaymentDetail({{ $payment->id }})">
            <div class="transaction-header">
                <div class="transaction-code">
                    <span>{{ $payment->order_id }}</span>
                    <button class="copy-btn" onclick="event.stopPropagation(); copyToClipboard('{{ $payment->order_id }}', this)">
                        Salin
                    </button>
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
        <div style="margin-top: 24px;">
            {{ $payments->links() }}
        </div>
    @endif
</div>

<!-- Tab Content: Penarikan -->
<div id="tab-withdrawals" class="tab-content">
    @forelse($withdrawals as $withdrawal)
        <div class="transaction-card" onclick="showWithdrawalDetail({{ $withdrawal->id }})">
            <div class="transaction-header">
                <div class="transaction-code">
                    <span>{{ $withdrawal->payout_id ?? 'WD-' . $withdrawal->id }}</span>
                    <button class="copy-btn" onclick="event.stopPropagation(); copyToClipboard('{{ $withdrawal->payout_id ?? 'WD-' . $withdrawal->id }}', this)">
                        Salin
                    </button>
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
        <div style="margin-top: 24px;">
            {{ $withdrawals->links() }}
        </div>
    @endif
</div>

<!-- Modal Detail Pembayaran -->
<div id="modal-payment" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Detail Pembayaran</h3>
            <button class="close-modal" onclick="closeModal('modal-payment')">×</button>
        </div>
        <div class="modal-body" id="payment-detail-content">
            <p style="text-align: center; color: #9ca3af;">Memuat...</p>
        </div>
    </div>
</div>

<!-- Modal Detail Penarikan -->
<div id="modal-withdrawal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Detail Penarikan</h3>
            <button class="close-modal" onclick="closeModal('modal-withdrawal')">×</button>
        </div>
        <div class="modal-body" id="withdrawal-detail-content">
            <p style="text-align: center; color: #9ca3af;">Memuat...</p>
        </div>
    </div>
</div>

<script>
function switchTab(tabName) {
    // Hide all tabs
    document.querySelectorAll('.tab-content').forEach(el => el.classList.remove('active'));
    document.querySelectorAll('.tab').forEach(el => el.classList.remove('active'));
    
    // Show selected tab
    document.getElementById('tab-' + tabName).classList.add('active');
    event.target.classList.add('active');
}

function copyToClipboard(text, btn) {
    navigator.clipboard.writeText(text).then(() => {
        const originalText = btn.innerText;
        btn.innerText = '✓ Tersalin';
        btn.style.background = '#10b981';
        btn.style.color = '#fff';
        btn.style.borderColor = '#10b981';
        
        setTimeout(() => {
            btn.innerText = originalText;
            btn.style.background = '';
            btn.style.color = '';
            btn.style.borderColor = '';
        }, 2000);
    });
}

async function showPaymentDetail(id) {
    const modal = document.getElementById('modal-payment');
    const content = document.getElementById('payment-detail-content');
    
    modal.classList.add('active');
    content.innerHTML = '<p style="text-align: center; color: #9ca3af;">Memuat...</p>';
    
    try {
        const response = await fetch(`/riwayat/pembayaran/${id}`);
        const result = await response.json();
        
        if (result.success) {
            const data = result.data;
            const notes = data.notes || {};
            
            content.innerHTML = `
                <div class="detail-row">
                    <span class="detail-label">Kode Transaksi</span>
                    <span class="detail-value">${data.order_id}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">ID Transaksi</span>
                    <span class="detail-value">${data.transaction_id || '-'}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Jumlah</span>
                    <span class="detail-value" style="color: #2563eb;">Rp ${new Intl.NumberFormat('id-ID').format(data.amount)}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Status</span>
                    <span class="detail-value">${data.status === 'settlement' ? 'Berhasil' : (data.status === 'pending' ? 'Menunggu' : 'Gagal')}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Metode</span>
                    <span class="detail-value">${data.payment_method?.toUpperCase() || '-'}</span>
                </div>
                ${notes.product_title ? `
                <div class="detail-row">
                    <span class="detail-label">Produk</span>
                    <span class="detail-value">${notes.product_title}</span>
                </div>
                ` : ''}
                ${notes.buyer_name ? `
                <div class="detail-row">
                    <span class="detail-label">Nama Pembeli</span>
                    <span class="detail-value">${notes.buyer_name}</span>
                </div>
                ` : ''}
                <div class="detail-row">
                    <span class="detail-label">Tanggal</span>
                    <span class="detail-value">${data.created_at}</span>
                </div>
            `;
        }
    } catch (error) {
        content.innerHTML = '<p style="text-align: center; color: #ef4444;">Gagal memuat detail</p>';
    }
}

async function showWithdrawalDetail(id) {
    const modal = document.getElementById('modal-withdrawal');
    const content = document.getElementById('withdrawal-detail-content');
    
    modal.classList.add('active');
    content.innerHTML = '<p style="text-align: center; color: #9ca3af;">Memuat...</p>';
    
    try {
        const response = await fetch(`/riwayat/penarikan/${id}`);
        const result = await response.json();
        
        if (result.success) {
            const data = result.data;
            
            content.innerHTML = `
                <div class="detail-row">
                    <span class="detail-label">Kode Penarikan</span>
                    <span class="detail-value">${data.payout_id || 'WD-' + data.id}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Jumlah</span>
                    <span class="detail-value" style="color: #2563eb;">Rp ${new Intl.NumberFormat('id-ID').format(data.amount)}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Status</span>
                    <span class="detail-value">${data.status === 'completed' ? 'Selesai' : (data.status === 'pending' ? 'Menunggu' : (data.status === 'approved' ? 'Disetujui' : 'Gagal'))}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Bank</span>
                    <span class="detail-value">${data.bank_name}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Nomor Rekening</span>
                    <span class="detail-value">${data.account_number}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Nama Rekening</span>
                    <span class="detail-value">${data.account_name}</span>
                </div>
                ${data.notes ? `
                <div class="detail-row">
                    <span class="detail-label">Catatan</span>
                    <span class="detail-value">${data.notes}</span>
                </div>
                ` : ''}
                ${data.rejection_reason ? `
                <div class="detail-row">
                    <span class="detail-label">Alasan Ditolak</span>
                    <span class="detail-value" style="color: #ef4444;">${data.rejection_reason}</span>
                </div>
                ` : ''}
                <div class="detail-row">
                    <span class="detail-label">Tanggal Pengajuan</span>
                    <span class="detail-value">${data.created_at}</span>
                </div>
                ${data.approved_at ? `
                <div class="detail-row">
                    <span class="detail-label">Tanggal Disetujui</span>
                    <span class="detail-value">${data.approved_at}</span>
                </div>
                ` : ''}
            `;
        }
    } catch (error) {
        content.innerHTML = '<p style="text-align: center; color: #ef4444;">Gagal memuat detail</p>';
    }
}

function closeModal(modalId) {
    document.getElementById(modalId).classList.remove('active');
}

// Close modal when clicking outside
document.querySelectorAll('.modal').forEach(modal => {
    modal.addEventListener('click', function(e) {
        if (e.target === this) {
            this.classList.remove('active');
        }
    });
});

// Close modal on ESC key
document.addEventListener('keydown', e => {
    if (e.key === 'Escape') {
        document.querySelectorAll('.modal').forEach(modal => {
            modal.classList.remove('active');
        });
    }
});
</script>
@endsection