@extends('layouts.dashboard')

@section('title', 'Riwayat Transaksi | Mobay.id')

@section('content')
<style>
.history-shell {
    --surface: rgba(255, 255, 255, 0.94);
    --surface-strong: #ffffff;
    --line: #dbe4f0;
    --line-soft: #e9eff7;
    --text: #0f172a;
    --muted: #64748b;
    --muted-soft: #94a3b8;
    --brand: #2563eb;
    --brand-dark: #1d4ed8;
    --brand-soft: #eff6ff;
    --success-bg: #ecfdf3;
    --success-text: #166534;
    --success-line: #bbf7d0;
    --warning-bg: #fffbeb;
    --warning-text: #92400e;
    --warning-line: #fde68a;
    --danger-bg: #fef2f2;
    --danger-text: #b91c1c;
    --danger-line: #fecaca;
    max-width: 1180px;
    margin: 0 auto;
    color: var(--text);
    font-family: 'Plus Jakarta Sans', sans-serif;
}

.history-hero {
    position: relative;
    overflow: hidden;
    background:
        radial-gradient(circle at top right, rgba(37, 99, 235, 0.14), transparent 35%),
        linear-gradient(135deg, rgba(255, 255, 255, 0.97), rgba(244, 248, 255, 0.95));
    border: 1px solid rgba(219, 228, 240, 0.9);
    border-radius: 26px;
    padding: 26px;
    box-shadow: 0 18px 40px rgba(15, 23, 42, 0.06);
}

.history-hero::after {
    content: '';
    position: absolute;
    right: -46px;
    top: -46px;
    width: 178px;
    height: 178px;
    border-radius: 999px;
    background: rgba(37, 99, 235, 0.08);
}

.hero-row {
    position: relative;
    z-index: 1;
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 18px;
    flex-wrap: wrap;
}

.hero-heading {
    display: flex;
    gap: 14px;
    align-items: flex-start;
}

.hero-back {
    width: 44px;
    height: 44px;
    border-radius: 14px;
    flex-shrink: 0;
    border: 1px solid var(--line);
    background: rgba(255, 255, 255, 0.88);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    color: #334155;
    text-decoration: none;
    transition: .2s ease;
}

.hero-back:hover {
    border-color: rgba(37, 99, 235, 0.35);
    color: var(--brand);
    transform: translateY(-1px);
}

.hero-pill {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 10px;
    padding: 7px 12px;
    border-radius: 999px;
    background: rgba(239, 246, 255, 0.95);
    border: 1px solid rgba(191, 219, 254, 0.9);
    color: var(--brand);
    font-size: 12px;
    font-weight: 700;
}

.hero-title {
    margin: 0;
    font-size: 30px;
    line-height: 1.12;
    letter-spacing: -0.03em;
    font-weight: 800;
}

.hero-copy {
    max-width: 650px;
    margin: 8px 0 0;
    font-size: 14px;
    line-height: 1.7;
    color: var(--muted);
}

.overview-grid {
    position: relative;
    z-index: 1;
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 12px;
    margin-top: 20px;
}

.overview-card {
    padding: 16px 18px;
    background: rgba(255, 255, 255, 0.78);
    border: 1px solid rgba(219, 228, 240, 0.95);
    border-radius: 18px;
    backdrop-filter: blur(10px);
}

.overview-label {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 8px;
    font-size: 12px;
    font-weight: 700;
    color: var(--muted);
}

.overview-value {
    font-size: 24px;
    line-height: 1.1;
    font-weight: 800;
    letter-spacing: -0.03em;
}

.overview-note {
    margin-top: 6px;
    font-size: 12px;
    color: var(--muted-soft);
}

.section-toolbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 14px;
    flex-wrap: wrap;
    margin-top: 22px;
    margin-bottom: 18px;
}

.tab-switcher {
    display: inline-grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 6px;
    width: min(100%, 420px);
    padding: 6px;
    background: rgba(255, 255, 255, 0.72);
    border: 1px solid var(--line);
    border-radius: 18px;
}

.tab-link {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    min-height: 46px;
    border-radius: 14px;
    text-decoration: none;
    color: var(--muted);
    font-size: 14px;
    font-weight: 700;
    transition: .2s ease;
}

.tab-link.active {
    background: linear-gradient(135deg, var(--brand), var(--brand-dark));
    color: #ffffff;
    box-shadow: 0 12px 28px rgba(37, 99, 235, 0.2);
}

.tab-link:not(.active):hover {
    background: rgba(255, 255, 255, 0.9);
    color: var(--text);
}

.section-note {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    padding: 12px 14px;
    border-radius: 16px;
    background: rgba(255, 255, 255, 0.82);
    border: 1px solid var(--line);
    color: var(--muted);
    font-size: 13px;
}

.section-note strong {
    color: var(--text);
}

.history-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 18px;
}

.history-card {
    position: relative;
    overflow: hidden;
    display: flex;
    flex-direction: column;
    min-height: 248px;
    padding: 20px;
    background: linear-gradient(180deg, rgba(255, 255, 255, 0.97), rgba(248, 251, 255, 0.99));
    border: 1px solid rgba(219, 228, 240, 0.95);
    border-radius: 22px;
    text-decoration: none;
    color: inherit;
    box-shadow: 0 14px 32px rgba(15, 23, 42, 0.05);
    transition: .22s ease;
}

.history-card::before {
    content: '';
    position: absolute;
    inset: 0;
    background: linear-gradient(135deg, rgba(37, 99, 235, 0.05), transparent 45%);
    opacity: 0;
    transition: opacity .22s ease;
}

.history-card:hover {
    transform: translateY(-3px);
    border-color: rgba(96, 165, 250, 0.85);
    box-shadow: 0 18px 34px rgba(37, 99, 235, 0.12);
}

.history-card:hover::before {
    opacity: 1;
}

.history-card > * {
    position: relative;
    z-index: 1;
}

.card-head {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 12px;
    margin-bottom: 16px;
}

.card-code {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    max-width: 100%;
    padding: 7px 12px;
    border-radius: 999px;
    background: #f8fbff;
    border: 1px solid var(--line-soft);
    color: #475569;
    font-size: 12px;
    font-weight: 800;
}

.card-code span {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.copy-button {
    width: 34px;
    height: 34px;
    border: 1px solid var(--line);
    border-radius: 12px;
    background: rgba(255, 255, 255, 0.94);
    color: #475569;
    cursor: pointer;
    transition: .2s ease;
    flex-shrink: 0;
}

.copy-button:hover {
    border-color: rgba(37, 99, 235, 0.35);
    color: var(--brand);
}

.copy-button.copied {
    background: #10b981;
    border-color: #10b981;
    color: #ffffff;
}

.status-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 7px 12px;
    border-radius: 999px;
    font-size: 11px;
    font-weight: 800;
    letter-spacing: .04em;
    text-transform: uppercase;
}

.status-badge.success {
    background: var(--success-bg);
    color: var(--success-text);
    border: 1px solid var(--success-line);
}

.status-badge.pending {
    background: var(--warning-bg);
    color: var(--warning-text);
    border: 1px solid var(--warning-line);
}

.status-badge.failed {
    background: var(--danger-bg);
    color: var(--danger-text);
    border: 1px solid var(--danger-line);
}

.entry-title {
    margin: 0 0 8px;
    font-size: 18px;
    line-height: 1.45;
    letter-spacing: -0.02em;
    font-weight: 800;
}

.entry-subtitle {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 18px;
    font-size: 13px;
    color: var(--muted);
}

.entry-subtitle i {
    color: var(--brand);
}

.entry-meta {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 12px;
    margin-bottom: 18px;
}

.meta-card {
    padding: 12px 14px;
    border-radius: 16px;
    background: rgba(248, 250, 252, 0.9);
    border: 1px solid var(--line-soft);
}

.meta-label {
    margin-bottom: 6px;
    font-size: 11px;
    font-weight: 700;
    color: var(--muted-soft);
    text-transform: uppercase;
    letter-spacing: .06em;
}

.meta-value {
    font-size: 14px;
    line-height: 1.5;
    font-weight: 700;
    color: var(--text);
    word-break: break-word;
}

.entry-footer {
    margin-top: auto;
    padding-top: 16px;
    border-top: 1px solid var(--line-soft);
    display: flex;
    align-items: flex-end;
    justify-content: space-between;
    gap: 12px;
}

.amount-stack {
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.amount-label {
    font-size: 11px;
    font-weight: 700;
    color: var(--muted-soft);
    text-transform: uppercase;
    letter-spacing: .06em;
}

.amount-value {
    font-size: 26px;
    line-height: 1;
    font-weight: 800;
    letter-spacing: -0.04em;
    color: var(--brand);
}

.date-stack {
    font-size: 12px;
    line-height: 1.6;
    text-align: right;
    color: var(--muted);
}

.date-stack strong {
    display: block;
    font-size: 13px;
    color: var(--text);
}

.empty-state {
    grid-column: 1 / -1;
    padding: 56px 24px;
    text-align: center;
    background: rgba(255, 255, 255, 0.86);
    border: 1px dashed #c7d4e5;
    border-radius: 24px;
    box-shadow: 0 10px 24px rgba(15, 23, 42, 0.04);
}

.empty-icon {
    width: 68px;
    height: 68px;
    margin: 0 auto 16px;
    border-radius: 20px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: var(--brand-soft);
    color: var(--brand);
    font-size: 24px;
}

.empty-state h3 {
    margin: 0 0 8px;
    font-size: 20px;
    font-weight: 800;
}

.empty-state p {
    margin: 0;
    font-size: 14px;
    line-height: 1.7;
    color: var(--muted);
}

.pagination-wrap {
    margin-top: 22px;
}

.pagination-bar {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    flex-wrap: wrap;
}

.page-pill,
.page-number {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 42px;
    min-height: 42px;
    padding: 0 14px;
    border-radius: 14px;
    border: 1px solid var(--line);
    background: rgba(255, 255, 255, 0.92);
    color: #334155;
    text-decoration: none;
    font-size: 13px;
    font-weight: 700;
    transition: .2s ease;
}

.page-pill:hover,
.page-number:hover {
    border-color: rgba(37, 99, 235, 0.42);
    color: var(--brand);
}

.page-number.active {
    background: linear-gradient(135deg, var(--brand), var(--brand-dark));
    border-color: transparent;
    color: #ffffff;
    box-shadow: 0 12px 24px rgba(37, 99, 235, 0.18);
}

.page-pill.disabled {
    color: #cbd5e1;
    background: #f8fafc;
    cursor: not-allowed;
}

.pagination-meta {
    margin-top: 12px;
    text-align: center;
    font-size: 13px;
    color: var(--muted);
}

.pagination-meta strong {
    color: var(--text);
}

.history-panel {
    display: none;
}

.history-panel.active {
    display: block;
}

@media (max-width: 900px) {
    .overview-grid,
    .history-grid {
        grid-template-columns: 1fr;
    }

    .entry-footer {
        align-items: flex-start;
        flex-direction: column;
    }

    .date-stack {
        text-align: left;
    }
}

@media (max-width: 640px) {
    .history-hero {
        padding: 20px;
        border-radius: 22px;
    }

    .hero-title {
        font-size: 24px;
    }

    .section-toolbar {
        align-items: stretch;
    }

    .tab-switcher,
    .section-note {
        width: 100%;
    }

    .entry-meta {
        grid-template-columns: 1fr;
    }

    .history-grid {
        grid-template-columns: 1fr;
        gap: 14px;
    }
}
</style>

@php
    $activeTab = request('tab', 'payments');
    $paymentsTotal = $payments->total();
    $withdrawalsTotal = $withdrawals->total();
    $paymentsAmount = $payments->getCollection()->sum('amount');
    $withdrawalsAmount = $withdrawals->getCollection()->sum('amount');
    $lastPaymentDate = $payments->first()?->created_at;
    $lastWithdrawalDate = $withdrawals->first()?->created_at;

    $paymentStatusMeta = function ($status) {
        return match ($status) {
            'settlement' => ['label' => 'Berhasil', 'class' => 'success'],
            'pending' => ['label' => 'Menunggu', 'class' => 'pending'],
            default => ['label' => 'Gagal', 'class' => 'failed'],
        };
    };

    $withdrawalStatusMeta = function ($status) {
        return match ($status) {
            'approved' => ['label' => 'Disetujui', 'class' => 'success'],
            'pending' => ['label' => 'Menunggu', 'class' => 'pending'],
            default => ['label' => 'Ditolak', 'class' => 'failed'],
        };
    };
@endphp

<div class="history-shell">
    <section class="history-hero">
        <div class="hero-row">
            <div class="hero-heading">
                <a href="{{ route('dashboard') }}" class="hero-back" aria-label="Kembali ke dashboard">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <div>
                    <div class="hero-pill">
                        <i class="fas fa-clock-rotate-left"></i>
                        Riwayat yang lebih rapi dan mudah dipantau
                    </div>
                    <h1 class="hero-title">Riwayat Transaksi</h1>
                    
                </div>
            </div>
        </div>

        <div class="overview-grid">
            <div class="overview-card">
                <div class="overview-label">
                    <i class="fas fa-credit-card"></i>
                    Total pembayaran
                </div>
                <div class="overview-value">{{ number_format($paymentsTotal, 0, ',', '.') }}</div>
                <div class="overview-note">
                    <!-- Nominal halaman ini: Rp {{ number_format($paymentsAmount, 0, ',', '.') }} -->
                </div>
            </div>
            <div class="overview-card">
                <div class="overview-label">
                    <i class="fas fa-building-columns"></i>
                    Total penarikan
                </div>
                <div class="overview-value">{{ number_format($withdrawalsTotal, 0, ',', '.') }}</div>
                <div class="overview-note">
                    <!-- Nominal halaman ini: Rp {{ number_format($withdrawalsAmount, 0, ',', '.') }} -->
                </div>
            </div>
            <div class="overview-card">
                <div class="overview-label">
                    <i class="fas fa-calendar-check"></i>
                    Aktivitas terbaru
                </div>
                <div class="overview-value" style="font-size:20px;">
                    {{ $activeTab === 'withdrawals' ? ($lastWithdrawalDate?->format('d M Y') ?? '-') : ($lastPaymentDate?->format('d M Y') ?? '-') }}
                </div>
                <div class="overview-note">
                    {{ $activeTab === 'withdrawals' ? ($lastWithdrawalDate?->format('H:i') ? $lastWithdrawalDate->format('H:i') . ' WIB' : 'Belum ada penarikan') : ($lastPaymentDate?->format('H:i') ? $lastPaymentDate->format('H:i') . ' WIB' : 'Belum ada pembayaran') }}
                </div>
            </div>
        </div>
    </section>

    <div class="section-toolbar">
        <div class="tab-switcher">
            <a href="{{ request()->url() }}?tab=payments{{ request('wpage') ? '&wpage=' . request('wpage') : '' }}" class="tab-link {{ $activeTab === 'payments' ? 'active' : '' }}">
                <i class="fas fa-credit-card"></i>
                Pembayaran
            </a>
            <a href="{{ request()->url() }}?tab=withdrawals{{ request('page') ? '&page=' . request('page') : '' }}{{ request('wpage') ? '&wpage=' . request('wpage') : '' }}" class="tab-link {{ $activeTab === 'withdrawals' ? 'active' : '' }}">
                <i class="fas fa-building-columns"></i>
                Penarikan
            </a>
        </div>

        <div class="section-note">
            <i class="fas fa-circle-info" style="color:#2563eb;"></i>
            <span>
                <strong>{{ $activeTab === 'withdrawals' ? $withdrawalsTotal : $paymentsTotal }}</strong>
                data {{ $activeTab === 'withdrawals' ? 'penarikan' : 'pembayaran' }} tersedia.
            </span>
        </div>
    </div>

    <div class="history-panel {{ $activeTab === 'payments' ? 'active' : '' }}">
        <div class="history-grid">
            @forelse($payments as $payment)
                @php
                    $notes = is_string($payment->notes) ? json_decode($payment->notes, true) : ($payment->notes ?? []);
                    $status = $paymentStatusMeta($payment->status);
                    $paymentMethod = strtoupper((string) ($payment->payment_method ?? '-'));
                    $buyerName = $notes['buyer_name'] ?? '-';
                @endphp

                <a href="{{ route('transactions.payment-detail', $payment->id) }}" class="history-card">
                    <div class="card-head">
                        <div class="card-code">
                            <i class="fas fa-hashtag" style="color:#2563eb;"></i>
                            <span>{{ $payment->order_id }}</span>
                        </div>
                        <div style="display:flex;align-items:center;gap:8px;">
                            <button class="copy-button" type="button" onclick="copyToClipboard('{{ $payment->order_id }}', this)" aria-label="Salin kode transaksi">
                                <i class="fas fa-copy"></i>
                            </button>
                            <span class="status-badge {{ $status['class'] }}">
                                <i class="fas fa-circle" style="font-size:7px;"></i>
                                {{ $status['label'] }}
                            </span>
                        </div>
                    </div>

                    <h3 class="entry-title">Pembayaran {{ $paymentMethod }}</h3>

                    <div class="entry-subtitle">
                        <i class="fas fa-user"></i>
                        <span>{{ $buyerName !== '-' ? 'Pembeli: ' . $buyerName : 'Transaksi masuk dari akun Anda' }}</span>
                    </div>

                    <div class="entry-meta">
                        <div class="meta-card">
                            <div class="meta-label">Metode</div>
                            <div class="meta-value">{{ $paymentMethod }}</div>
                        </div>
                        <div class="meta-card">
                            <div class="meta-label">Status</div>
                            <div class="meta-value">{{ $status['label'] }}</div>
                        </div>
                    </div>

                    <div class="entry-footer">
                        <div class="amount-stack">
                            <div class="amount-label">Total pembayaran</div>
                            <div class="amount-value">Rp {{ number_format($payment->amount, 0, ',', '.') }}</div>
                        </div>
                        <div class="date-stack">
                            <strong>{{ $payment->created_at->format('d M Y') }}</strong>
                            {{ $payment->created_at->format('H:i') }} WIB
                        </div>
                    </div>
                </a>
            @empty
                <div class="empty-state">
                    <div class="empty-icon">
                        <i class="fas fa-credit-card"></i>
                    </div>
                    <h3>Belum ada riwayat pembayaran</h3>
                    <p>
                        Saat ada pembayaran baru, daftar transaksi akan tampil di sini dalam format kartu yang lebih bersih dan mudah dipindai.
                    </p>
                </div>
            @endforelse
        </div>

        @if($payments->hasPages())
            <div class="pagination-wrap">
                <div class="pagination-bar">
                    @if($payments->onFirstPage())
                        <span class="page-pill disabled"><i class="fas fa-chevron-left"></i></span>
                    @else
                        <a href="{{ $payments->appends(['tab' => 'payments', 'wpage' => request('wpage')])->previousPageUrl() }}" class="page-pill" aria-label="Halaman pembayaran sebelumnya">
                            <i class="fas fa-chevron-left"></i>
                        </a>
                    @endif

                    @foreach($payments->appends(['tab' => 'payments', 'wpage' => request('wpage')])->getUrlRange(1, $payments->lastPage()) as $page => $url)
                        @if($page == $payments->currentPage())
                            <span class="page-number active">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" class="page-number">{{ $page }}</a>
                        @endif
                    @endforeach

                    @if($payments->hasMorePages())
                        <a href="{{ $payments->appends(['tab' => 'payments', 'wpage' => request('wpage')])->nextPageUrl() }}" class="page-pill" aria-label="Halaman pembayaran berikutnya">
                            <i class="fas fa-chevron-right"></i>
                        </a>
                    @else
                        <span class="page-pill disabled"><i class="fas fa-chevron-right"></i></span>
                    @endif
                </div>

                <div class="pagination-meta">
                    Menampilkan
                    <strong>{{ $payments->firstItem() }}</strong>
                    -
                    <strong>{{ $payments->lastItem() }}</strong>
                    dari
                    <strong>{{ $payments->total() }}</strong>
                    pembayaran.
                </div>
            </div>
        @endif
    </div>

    <div class="history-panel {{ $activeTab === 'withdrawals' ? 'active' : '' }}">
        <div class="history-grid">
            @forelse($withdrawals as $withdrawal)
                @php
                    $withdrawCode = $withdrawal->payout_id ?? ('WD-' . $withdrawal->id);
                    $status = $withdrawalStatusMeta($withdrawal->status);
                    $bankLabel = trim(($withdrawal->bank_name ?? '-') . (($withdrawal->account_number ?? null) ? ' - ' . $withdrawal->account_number : ''));
                @endphp

                <a href="{{ route('transactions.withdrawal-detail', $withdrawal->id) }}" class="history-card">
                    <div class="card-head">
                        <div class="card-code">
                            <i class="fas fa-hashtag" style="color:#2563eb;"></i>
                            <span>{{ $withdrawCode }}</span>
                        </div>
                        <div style="display:flex;align-items:center;gap:8px;">
                            <button class="copy-button" type="button" onclick="copyToClipboard('{{ $withdrawCode }}', this)" aria-label="Salin kode penarikan">
                                <i class="fas fa-copy"></i>
                            </button>
                            <span class="status-badge {{ $status['class'] }}">
                                <i class="fas fa-circle" style="font-size:7px;"></i>
                                {{ $status['label'] }}
                            </span>
                        </div>
                    </div>

                    <h3 class="entry-title">Penarikan dana</h3>

                    <div class="entry-subtitle">
                        <i class="fas fa-building-columns"></i>
                        <span>{{ $bankLabel !== '-' ? $bankLabel : 'Tujuan rekening belum tersedia' }}</span>
                    </div>

                    <div class="entry-meta">
                        <div class="meta-card">
                            <div class="meta-label">Bank tujuan</div>
                            <div class="meta-value">{{ strtoupper((string) ($withdrawal->bank_name ?? '-')) }}</div>
                        </div>
                        <div class="meta-card">
                            <div class="meta-label">Status</div>
                            <div class="meta-value">{{ $status['label'] }}</div>
                        </div>
                    </div>

                    <div class="entry-footer">
                        <div class="amount-stack">
                            <div class="amount-label">Nominal penarikan</div>
                            <div class="amount-value">Rp {{ number_format($withdrawal->amount, 0, ',', '.') }}</div>
                        </div>
                        <div class="date-stack">
                            <strong>{{ $withdrawal->created_at->format('d M Y') }}</strong>
                            {{ $withdrawal->created_at->format('H:i') }} WIB
                        </div>
                    </div>
                </a>
            @empty
                <div class="empty-state">
                    <div class="empty-icon">
                        <i class="fas fa-building-columns"></i>
                    </div>
                    <h3>Belum ada riwayat penarikan</h3>
                    <p>
                        Penarikan yang Anda ajukan akan tampil di sini, lengkap dengan status dan detail utama yang lebih mudah dipantau.
                    </p>
                </div>
            @endforelse
        </div>

        @if($withdrawals->hasPages())
            <div class="pagination-wrap">
                <div class="pagination-bar">
                    @if($withdrawals->onFirstPage())
                        <span class="page-pill disabled"><i class="fas fa-chevron-left"></i></span>
                    @else
                        <a href="{{ $withdrawals->appends(['tab' => 'withdrawals', 'page' => request('page')])->previousPageUrl() }}" class="page-pill" aria-label="Halaman penarikan sebelumnya">
                            <i class="fas fa-chevron-left"></i>
                        </a>
                    @endif

                    @foreach($withdrawals->appends(['tab' => 'withdrawals', 'page' => request('page')])->getUrlRange(1, $withdrawals->lastPage()) as $page => $url)
                        @if($page == $withdrawals->currentPage())
                            <span class="page-number active">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" class="page-number">{{ $page }}</a>
                        @endif
                    @endforeach

                    @if($withdrawals->hasMorePages())
                        <a href="{{ $withdrawals->appends(['tab' => 'withdrawals', 'page' => request('page')])->nextPageUrl() }}" class="page-pill" aria-label="Halaman penarikan berikutnya">
                            <i class="fas fa-chevron-right"></i>
                        </a>
                    @else
                        <span class="page-pill disabled"><i class="fas fa-chevron-right"></i></span>
                    @endif
                </div>

                <div class="pagination-meta">
                    Menampilkan
                    <strong>{{ $withdrawals->firstItem() }}</strong>
                    -
                    <strong>{{ $withdrawals->lastItem() }}</strong>
                    dari
                    <strong>{{ $withdrawals->total() }}</strong>
                    penarikan.
                </div>
            </div>
        @endif
    </div>
</div>

<script>
function copyToClipboard(text, btn) {
    navigator.clipboard.writeText(text).then(function () {
        var original = btn.innerHTML;
        btn.classList.add('copied');
        btn.innerHTML = '<i class="fas fa-check"></i>';

        setTimeout(function () {
            btn.classList.remove('copied');
            btn.innerHTML = original;
        }, 1800);
    });
}
</script>
@endsection
