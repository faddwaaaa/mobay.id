@extends('layouts.dashboard')

@section('title', 'Detail Penarikan | Payou.id')

@section('content')
<style>
    .detail-container {
        max-width: 800px;
        margin: 0 auto;
    }
    
    .back-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 16px;
        border-radius: 8px;
        border: 1px solid #e5e7eb;
        background: #fff;
        color: #374151;
        text-decoration: none;
        font-size: 14px;
        font-weight: 500;
        margin-bottom: 20px;
        transition: all 0.2s;
    }
    
    .back-btn:hover {
        background: #f9fafb;
        border-color: #2563eb;
        color: #2563eb;
    }
    
    .detail-card {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 16px;
        overflow: hidden;
        margin-bottom: 16px;
    }
    
    .detail-header {
        padding: 24px;
        border-bottom: 1px solid #e5e7eb;
        background: linear-gradient(135deg, #f9fafb 0%, #ffffff 100%);
    }
    
    .detail-title {
        font-size: 20px;
        font-weight: 700;
        color: #111827;
        margin-bottom: 12px;
    }
    
    .status-timeline {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-top: 16px;
    }
    
    .timeline-step {
        flex: 1;
        text-align: center;
        position: relative;
    }
    
    .timeline-step::after {
        content: '';
        position: absolute;
        top: 16px;
        left: 50%;
        width: 100%;
        height: 2px;
        background: #e5e7eb;
        z-index: 0;
    }
    
    .timeline-step:last-child::after {
        display: none;
    }
    
    .timeline-icon {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: #f3f4f6;
        border: 2px solid #e5e7eb;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 8px;
        font-size: 12px;
        color: #9ca3af;
        position: relative;
        z-index: 1;
    }
    
    .timeline-step.active .timeline-icon {
        background: #2563eb;
        border-color: #2563eb;
        color: #fff;
    }
    
    .timeline-step.completed .timeline-icon {
        background: #10b981;
        border-color: #10b981;
        color: #fff;
    }
    
    .timeline-label {
        font-size: 11px;
        color: #6b7280;
        font-weight: 500;
    }
    
    .timeline-step.active .timeline-label,
    .timeline-step.completed .timeline-label {
        color: #111827;
        font-weight: 600;
    }
    
    .header-info {
        display: flex;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
    }
    
    .payout-code {
        font-size: 14px;
        color: #6b7280;
        font-family: monospace;
        font-weight: 600;
    }
    
    .copy-code-btn {
        padding: 6px 12px;
        border: 1px solid #e5e7eb;
        border-radius: 6px;
        background: #f9fafb;
        color: #374151;
        font-size: 12px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s;
    }
    
    .copy-code-btn:hover {
        background: #2563eb;
        color: #fff;
        border-color: #2563eb;
    }
    
    .status-badge {
        padding: 6px 16px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }
    
    .status-completed {
        background: #d1fae5;
        color: #065f46;
    }
    
    .status-pending {
        background: #fef3c7;
        color: #92400e;
    }
    
    .status-approved {
        background: #dbeafe;
        color: #1e40af;
    }
    
    .status-rejected {
        background: #fee2e2;
        color: #991b1b;
    }
    
    .amount-display {
        text-align: center;
        padding: 32px 24px;
        background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
        border-bottom: 1px solid #e5e7eb;
    }
    
    .amount-label {
        font-size: 13px;
        color: #6b7280;
        margin-bottom: 8px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .amount-value {
        font-size: 36px;
        font-weight: 800;
        color: #2563eb;
        line-height: 1;
    }
    
    .detail-section {
        padding: 24px;
        border-bottom: 1px solid #f3f4f6;
    }
    
    .detail-section:last-child {
        border-bottom: none;
    }
    
    .section-title {
        font-size: 14px;
        font-weight: 700;
        color: #111827;
        margin-bottom: 16px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    .section-icon {
        width: 24px;
        height: 24px;
        border-radius: 6px;
        background: #eff6ff;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #2563eb;
        font-size: 12px;
    }
    
    .bank-card {
        background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
        border-radius: 12px;
        padding: 20px;
        color: #fff;
        position: relative;
        overflow: hidden;
    }
    
    .bank-card::before {
        content: '';
        position: absolute;
        top: -50px;
        right: -50px;
        width: 150px;
        height: 150px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 50%;
    }
    
    .bank-name {
        font-size: 18px;
        font-weight: 700;
        margin-bottom: 16px;
    }
    
    .account-info {
        display: grid;
        gap: 12px;
    }
    
    .account-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .account-label {
        font-size: 12px;
        opacity: 0.8;
    }
    
    .account-value {
        font-size: 16px;
        font-weight: 600;
        font-family: monospace;
    }
    
    .detail-grid {
        display: grid;
        gap: 16px;
    }
    
    .detail-item {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        padding-bottom: 12px;
        border-bottom: 1px solid #f3f4f6;
    }
    
    .detail-item:last-child {
        border-bottom: none;
        padding-bottom: 0;
    }
    
    .detail-label {
        font-size: 13px;
        color: #6b7280;
        flex-shrink: 0;
        margin-right: 16px;
    }
    
    .detail-value {
        font-size: 14px;
        font-weight: 600;
        color: #111827;
        text-align: right;
        flex: 1;
    }
    
    .alert-box {
        border-radius: 12px;
        padding: 16px;
        display: flex;
        gap: 12px;
        margin-bottom: 16px;
    }
    
    .alert-info {
        background: #eff6ff;
        border: 1px solid #bfdbfe;
    }
    
    .alert-warning {
        background: #fffbeb;
        border: 1px solid #fde68a;
    }
    
    .alert-danger {
        background: #fef2f2;
        border: 1px solid #fecaca;
    }
    
    .alert-success {
        background: #f0fdf4;
        border: 1px solid #bbf7d0;
    }
    
    .alert-icon {
        font-size: 20px;
        flex-shrink: 0;
    }
    
    .alert-info .alert-icon {
        color: #2563eb;
    }
    
    .alert-warning .alert-icon {
        color: #f59e0b;
    }
    
    .alert-danger .alert-icon {
        color: #ef4444;
    }
    
    .alert-success .alert-icon {
        color: #10b981;
    }
    
    .alert-content {
        flex: 1;
    }
    
    .alert-title {
        font-size: 14px;
        font-weight: 700;
        margin-bottom: 4px;
    }
    
    .alert-text {
        font-size: 13px;
        line-height: 1.6;
        color: #374151;
    }
    
    .action-buttons {
        display: flex;
        gap: 12px;
        padding: 24px;
        background: #fafbfc;
        border-top: 1px solid #e5e7eb;
    }
    
    .btn {
        flex: 1;
        padding: 12px 20px;
        border-radius: 8px;
        border: none;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
        text-align: center;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }
    
    .btn-primary {
        background: #2563eb;
        color: #fff;
    }
    
    .btn-primary:hover {
        background: #1d4ed8;
    }
    
    .btn-secondary {
        background: #fff;
        color: #374151;
        border: 1px solid #e5e7eb;
    }
    
    .btn-secondary:hover {
        background: #f9fafb;
    }
    
    .btn-danger {
        background: #ef4444;
        color: #fff;
    }
    
    .btn-danger:hover {
        background: #dc2626;
    }

    @media (max-width: 768px) {
        .amount-value {
            font-size: 28px;
        }
        
        .status-timeline {
            flex-direction: column;
            gap: 16px;
        }
        
        .timeline-step::after {
            top: 0;
            left: 16px;
            width: 2px;
            height: 100%;
        }
        
        .timeline-step {
            display: flex;
            align-items: center;
            gap: 12px;
            text-align: left;
        }
        
        .timeline-icon {
            margin: 0;
        }
        
        .action-buttons {
            flex-direction: column;
        }
        
        .detail-item {
            flex-direction: column;
            gap: 4px;
        }
        
        .detail-value {
            text-align: left;
        }
    }
</style>

<div class="detail-container">
    <a href="{{ route('transactions.history') }}" class="back-btn">
        <i class="fas fa-arrow-left"></i>
        Kembali ke Riwayat
    </a>

    <!-- Status Alert -->
    @if($withdrawal->status === 'pending')
        <div class="alert-box alert-warning">
            <div class="alert-icon">⏳</div>
            <div class="alert-content">
                <div class="alert-title">Penarikan Sedang Diproses</div>
                <div class="alert-text">Penarikan Anda sedang ditinjau oleh tim kami. Proses ini biasanya memakan waktu 1-3 hari kerja.</div>
            </div>
        </div>
    @elseif($withdrawal->status === 'approved')
        <div class="alert-box alert-info">
            <div class="alert-icon">✓</div>
            <div class="alert-content">
                <div class="alert-title">Penarikan Disetujui</div>
                <div class="alert-text">Penarikan telah disetujui dan sedang dalam proses transfer ke rekening Anda.</div>
            </div>
        </div>
    @elseif($withdrawal->status === 'completed')
        <div class="alert-box alert-success">
            <div class="alert-icon">🎉</div>
            <div class="alert-content">
                <div class="alert-title">Penarikan Selesai</div>
                <div class="alert-text">Dana telah berhasil ditransfer ke rekening Anda. Silakan cek mutasi rekening Anda.</div>
            </div>
        </div>
    @elseif($withdrawal->status === 'rejected')
        <div class="alert-box alert-danger">
            <div class="alert-icon">⚠️</div>
            <div class="alert-content">
                <div class="alert-title">Penarikan Ditolak</div>
                <div class="alert-text">
                    @if($withdrawal->rejection_reason)
                        {{ $withdrawal->rejection_reason }}
                    @else
                        Penarikan ditolak oleh sistem. Saldo telah dikembalikan ke akun Anda.
                    @endif
                </div>
            </div>
        </div>
    @endif

    <!-- Main Card -->
    <div class="detail-card">
        <!-- Header -->
        <div class="detail-header">
            <div class="detail-title">Detail Penarikan</div>
            <div class="header-info">
                <span class="payout-code">{{ $withdrawal->payout_id ?? 'WD-' . $withdrawal->id }}</span>
                <button class="copy-code-btn" onclick="copyToClipboard('{{ $withdrawal->payout_id ?? 'WD-' . $withdrawal->id }}', this)">
                    <i class="fas fa-copy"></i> Salin Kode
                </button>
                <span class="status-badge status-{{ $withdrawal->status }}">
                    @if($withdrawal->status === 'pending')
                        Menunggu Persetujuan
                    @elseif($withdrawal->status === 'approved')
                        Disetujui
                    @elseif($withdrawal->status === 'completed')
                        Selesai
                    @elseif($withdrawal->status === 'rejected')
                        Ditolak
                    @else
                        {{ ucfirst($withdrawal->status) }}
                    @endif
                </span>
            </div>

            <!-- Status Timeline -->
            <div class="status-timeline">
                <div class="timeline-step {{ in_array($withdrawal->status, ['pending', 'approved', 'completed']) ? 'completed' : '' }}">
                    <div class="timeline-icon">
                        <i class="fas fa-paper-plane"></i>
                    </div>
                    <div class="timeline-label">Diajukan</div>
                </div>
                <div class="timeline-step {{ in_array($withdrawal->status, ['approved', 'completed']) ? 'completed' : ($withdrawal->status === 'pending' ? 'active' : '') }}">
                    <div class="timeline-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="timeline-label">Ditinjau</div>
                </div>
                <div class="timeline-step {{ $withdrawal->status === 'completed' ? 'completed' : ($withdrawal->status === 'approved' ? 'active' : '') }}">
                    <div class="timeline-icon">
                        <i class="fas fa-university"></i>
                    </div>
                    <div class="timeline-label">Transfer</div>
                </div>
                <div class="timeline-step {{ $withdrawal->status === 'completed' ? 'completed' : '' }}">
                    <div class="timeline-icon">
                        <i class="fas fa-check"></i>
                    </div>
                    <div class="timeline-label">Selesai</div>
                </div>
            </div>
        </div>

        <!-- Amount Display -->
        <div class="amount-display">
            <div class="amount-label">Jumlah Penarikan</div>
            <div class="amount-value">Rp {{ number_format($withdrawal->amount, 0, ',', '.') }}</div>
        </div>

        <!-- Bank Information -->
        <div class="detail-section">
            <div class="section-title">
                <span class="section-icon"><i class="fas fa-university"></i></span>
                Informasi Rekening Tujuan
            </div>
            <div class="bank-card">
                <div class="bank-name">{{ $withdrawal->bank_name }}</div>
                <div class="account-info">
                    <div class="account-row">
                        <span class="account-label">Nomor Rekening</span>
                        <span class="account-value">{{ $withdrawal->account_number }}</span>
                    </div>
                    <div class="account-row">
                        <span class="account-label">Nama Pemilik</span>
                        <span class="account-value">{{ $withdrawal->account_name }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Transaction Details -->
        <div class="detail-section">
            <div class="section-title">
                <span class="section-icon"><i class="fas fa-info-circle"></i></span>
                Detail Transaksi
            </div>
            <div class="detail-grid">
                <div class="detail-item">
                    <span class="detail-label">Kode Penarikan</span>
                    <span class="detail-value">{{ $withdrawal->payout_id ?? 'WD-' . $withdrawal->id }}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Status</span>
                    <span class="detail-value">
                        @if($withdrawal->status === 'pending')
                            Menunggu Persetujuan
                        @elseif($withdrawal->status === 'approved')
                            Disetujui
                        @elseif($withdrawal->status === 'completed')
                            Selesai
                        @elseif($withdrawal->status === 'rejected')
                            Ditolak
                        @else
                            {{ ucfirst($withdrawal->status) }}
                        @endif
                    </span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Tanggal Pengajuan</span>
                    <span class="detail-value">{{ $withdrawal->created_at->format('d M Y, H:i') }} WIB</span>
                </div>
                @if($withdrawal->approved_at)
                    <div class="detail-item">
                        <span class="detail-label">Tanggal Disetujui</span>
                        <span class="detail-value">{{ $withdrawal->approved_at->format('d M Y, H:i') }} WIB</span>
                    </div>
                @endif
                @if($withdrawal->notes)
                    <div class="detail-item">
                        <span class="detail-label">Catatan</span>
                        <span class="detail-value">{{ $withdrawal->notes }}</span>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Help Info -->
    <div class="alert-box alert-info">
        <div class="alert-icon">💬</div>
        <div class="alert-content">
            <div class="alert-title">Butuh Bantuan?</div>
            <div class="alert-text">
                Jika dana belum masuk atau ada kendala, hubungi customer service kami dengan menyertakan <strong>kode penarikan: {{ $withdrawal->payout_id ?? 'WD-' . $withdrawal->id }}</strong>
            </div>
        </div>
    </div>
</div>

<script>
function copyToClipboard(text, btn) {
    navigator.clipboard.writeText(text).then(() => {
        const originalHTML = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-check"></i> Tersalin!';
        btn.style.background = '#10b981';
        btn.style.color = '#fff';
        btn.style.borderColor = '#10b981';
        
        setTimeout(() => {
            btn.innerHTML = originalHTML;
            btn.style.background = '';
            btn.style.color = '';
            btn.style.borderColor = '';
        }, 2000);
    });
}
</script>
@endsection