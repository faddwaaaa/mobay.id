@extends('layouts.dashboard')

@section('title', 'Pesanan Masuk | Mobay.id')

@section('content')
<style>
.page-shell {
    --surface: rgba(255, 255, 255, 0.92);
    --surface-strong: #ffffff;
    --line: #dbe4f0;
    --line-soft: #e9eff7;
    --text: #0f172a;
    --muted: #64748b;
    --muted-soft: #94a3b8;
    --brand: #2563eb;
    --brand-soft: #eff6ff;
    --green-soft: #ecfdf3;
    --green-text: #166534;
    --blue-soft: #eef4ff;
    --blue-text: #1d4ed8;
    max-width: 1180px;
    margin: 0 auto;
    color: var(--text);
    font-family: 'Plus Jakarta Sans', sans-serif;
}

.page-hero {
    position: relative;
    overflow: hidden;
    background:
        radial-gradient(circle at top right, rgba(37, 99, 235, 0.14), transparent 32%),
        linear-gradient(135deg, rgba(255, 255, 255, 0.96), rgba(244, 248, 255, 0.96));
    border: 1px solid rgba(219, 228, 240, 0.9);
    border-radius: 26px;
    padding: 26px;
    box-shadow: 0 18px 40px rgba(15, 23, 42, 0.06);
}

.page-hero::after {
    content: '';
    position: absolute;
    right: -42px;
    top: -42px;
    width: 170px;
    height: 170px;
    border-radius: 999px;
    background: rgba(37, 99, 235, 0.08);
}

.hero-top {
    position: relative;
    z-index: 1;
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 18px;
    flex-wrap: wrap;
}

.hero-head {
    display: flex;
    align-items: flex-start;
    gap: 14px;
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

.hero-eyebrow {
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
    letter-spacing: .02em;
}

.hero-title {
    margin: 0;
    font-size: 30px;
    line-height: 1.12;
    letter-spacing: -0.03em;
    font-weight: 800;
}

.hero-subtitle {
    margin: 8px 0 0;
    max-width: 640px;
    font-size: 14px;
    line-height: 1.7;
    color: var(--muted);
}

.hero-summary {
    position: relative;
    z-index: 1;
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 12px;
    margin-top: 20px;
}

.hero-stat {
    padding: 16px 18px;
    background: rgba(255, 255, 255, 0.78);
    border: 1px solid rgba(219, 228, 240, 0.95);
    border-radius: 18px;
    backdrop-filter: blur(10px);
}

.hero-stat-label {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 8px;
    font-size: 12px;
    font-weight: 700;
    color: var(--muted);
}

.hero-stat-value {
    font-size: 24px;
    line-height: 1.1;
    font-weight: 800;
    letter-spacing: -0.03em;
}

.hero-stat-note {
    margin-top: 6px;
    font-size: 12px;
    color: var(--muted-soft);
}

.toolbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 14px;
    flex-wrap: wrap;
    margin-top: 22px;
    margin-bottom: 18px;
}

.segmented {
    display: inline-grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 6px;
    width: min(100%, 420px);
    padding: 6px;
    background: rgba(255, 255, 255, 0.7);
    border: 1px solid var(--line);
    border-radius: 18px;
    box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.7);
}

.segmented-link {
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

.segmented-link.active {
    background: linear-gradient(135deg, #2563eb, #1d4ed8);
    color: #ffffff;
    box-shadow: 0 12px 28px rgba(37, 99, 235, 0.2);
}

.segmented-link:not(.active):hover {
    background: rgba(255, 255, 255, 0.9);
    color: var(--text);
}

.toolbar-note {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    padding: 12px 14px;
    border-radius: 16px;
    background: rgba(255, 255, 255, 0.8);
    border: 1px solid var(--line);
    color: var(--muted);
    font-size: 13px;
}

.toolbar-note strong {
    color: var(--text);
}

.card-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 18px;
}

.order-card {
    position: relative;
    overflow: hidden;
    display: flex;
    flex-direction: column;
    min-height: 240px;
    padding: 20px;
    background: linear-gradient(180deg, rgba(255, 255, 255, 0.96), rgba(248, 251, 255, 0.98));
    border: 1px solid rgba(219, 228, 240, 0.95);
    border-radius: 22px;
    text-decoration: none;
    color: inherit;
    box-shadow: 0 14px 32px rgba(15, 23, 42, 0.05);
    transition: .22s ease;
}

.order-card::before {
    content: '';
    position: absolute;
    inset: 0;
    background: linear-gradient(135deg, rgba(37, 99, 235, 0.04), transparent 42%);
    opacity: 0;
    transition: opacity .22s ease;
}

.order-card:hover {
    transform: translateY(-3px);
    border-color: rgba(96, 165, 250, 0.85);
    box-shadow: 0 18px 34px rgba(37, 99, 235, 0.12);
}

.order-card:hover::before {
    opacity: 1;
}

.order-card > * {
    position: relative;
    z-index: 1;
}

.card-top {
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

.type-badge {
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

.type-badge.fisik {
    background: var(--green-soft);
    color: var(--green-text);
    border: 1px solid #bbf7d0;
}

.type-badge.digital {
    background: var(--blue-soft);
    color: var(--blue-text);
    border: 1px solid #bfdbfe;
}

.card-title {
    margin: 0 0 8px;
    font-size: 18px;
    line-height: 1.45;
    font-weight: 800;
    letter-spacing: -0.02em;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.card-subtitle {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 18px;
    font-size: 13px;
    color: var(--muted);
}

.card-subtitle i {
    color: var(--brand);
}

.card-meta {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 12px;
    margin-bottom: 18px;
}

.meta-box {
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

.card-footer {
    margin-top: auto;
    padding-top: 16px;
    border-top: 1px solid var(--line-soft);
    display: flex;
    align-items: flex-end;
    justify-content: space-between;
    gap: 12px;
}

.amount-block {
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

.date-value {
    font-size: 12px;
    line-height: 1.6;
    text-align: right;
    color: var(--muted);
}

.date-value strong {
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

.empty-state-icon {
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
    background: linear-gradient(135deg, #2563eb, #1d4ed8);
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

@media (max-width: 900px) {
    .hero-summary,
    .card-grid {
        grid-template-columns: 1fr;
    }

    .card-footer {
        align-items: flex-start;
        flex-direction: column;
    }

    .date-value {
        text-align: left;
    }
}

@media (max-width: 640px) {
    .page-shell {
        padding-bottom: 12px;
    }

    .page-hero {
        padding: 20px;
        border-radius: 22px;
    }

    .hero-head {
        align-items: stretch;
    }

    .hero-title {
        font-size: 24px;
    }

    .toolbar {
        align-items: stretch;
    }

    .segmented,
    .toolbar-note {
        width: 100%;
    }

    .card-meta {
        grid-template-columns: 1fr;
    }

    .card-grid {
        grid-template-columns: 1fr;
        gap: 14px;
    }
}
</style>

@php
    $ordersCount = $orders->total();
    $visibleOrders = $orders->count();
    $firstOrder = $orders->first();
    $latestOrderDate = $firstOrder?->created_at;
@endphp

<div class="page-shell">
    <section class="page-hero">
        <div class="hero-top">
            <div class="hero-head">
                <a href="{{ route('dashboard') }}" class="hero-back" aria-label="Kembali ke dashboard">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <div>
                    <div class="hero-eyebrow">
                        <i class="fas fa-box-open"></i>
                        Kelola pesanan dengan tampilan yang lebih rapi
                    </div>
                    <h1 class="hero-title">Pesanan Masuk</h1>
                   
                </div>
            </div>
        </div>

        <div class="hero-summary">
            <div class="hero-stat">
                <div class="hero-stat-label">
                    <i class="fas fa-layer-group"></i>
                    Total pesanan kategori ini
                </div>
                <div class="hero-stat-value">{{ number_format($ordersCount, 0, ',', '.') }}</div>
                <!-- <div class="hero-stat-note">Pagination aktif setiap 10 kartu</div> -->
            </div>
            <div class="hero-stat">
                <div class="hero-stat-label">
                    <i class="fas fa-grip"></i>
                    Kartu pada halaman ini
                </div>
                <div class="hero-stat-value">{{ $visibleOrders }}/10</div>
                <!-- <div class="hero-stat-note">Tersusun 2 kartu per baris</div> -->
            </div>
            <div class="hero-stat">
                <div class="hero-stat-label">
                    <i class="fas fa-clock"></i>
                    Pesanan terbaru
                </div>
                <div class="hero-stat-value" style="font-size:20px;">
                    {{ $latestOrderDate ? $latestOrderDate->format('d M Y') : '-' }}
                </div>
                <div class="hero-stat-note">
                    {{ $latestOrderDate ? $latestOrderDate->format('H:i') . ' WIB' : 'Belum ada data' }}
                </div>
            </div>
        </div>
    </section>

    <div class="toolbar">
        <div class="segmented">
            <a href="{{ route('orders.index', ['type' => 'fisik']) }}" class="segmented-link {{ $type === 'fisik' ? 'active' : '' }}">
                <i class="fas fa-box"></i>
                Pesanan Fisik
            </a>
            <a href="{{ route('orders.index', ['type' => 'digital']) }}" class="segmented-link {{ $type === 'digital' ? 'active' : '' }}">
                <i class="fas fa-file-arrow-down"></i>
                Pesanan Digital
            </a>
        </div>

        <div class="toolbar-note">
            <i class="fas fa-circle-info" style="color:#2563eb;"></i>
            <span><strong>{{ $ordersCount }}</strong> pesanan ditemukan untuk kategori <strong>{{ $type === 'digital' ? 'digital' : 'fisik' }}</strong>.</span>
        </div>
    </div>

    <div class="card-grid">
        @forelse($orders as $order)
            @php
                $notes = $order->order_notes ?? [];
                $qty = (int) ($notes['qty'] ?? 1);
                $buyerName = $notes['buyer_name'] ?? '-';
                $productTitle = $notes['product_title'] ?? ($order->order_product->title ?? '-');
                $typeBadge = ($notes['product_type'] ?? 'fisik') === 'digital' ? 'digital' : 'fisik';
            @endphp

            <a href="{{ route('orders.show', $order->id) }}" class="order-card">
                <div class="card-top">
                    <div class="card-code">
                        <i class="fas fa-receipt" style="color:#2563eb;"></i>
                        <span>{{ $order->order_id }}</span>
                    </div>
                    <span class="type-badge {{ $typeBadge }}">
                        <i class="fas {{ $typeBadge === 'digital' ? 'fa-file-lines' : 'fa-box' }}"></i>
                        {{ $typeBadge }}
                    </span>
                </div>

                <h3 class="card-title">{{ $productTitle }}</h3>

                <div class="card-subtitle">
                    <i class="fas fa-user"></i>
                    <span>Pembeli: {{ $buyerName }}</span>
                </div>

                <div class="card-meta">
                    <div class="meta-box">
                        <div class="meta-label">Jumlah</div>
                        <div class="meta-value">{{ $qty }} item</div>
                    </div>
                    <div class="meta-box">
                        <div class="meta-label">Status</div>
                        <div class="meta-value">Pembayaran selesai</div>
                    </div>
                </div>

                <div class="card-footer">
                    <div class="amount-block">
                        <div class="amount-label">Total pembayaran</div>
                        <div class="amount-value">Rp {{ number_format((int) $order->amount, 0, ',', '.') }}</div>
                    </div>
                    <div class="date-value">
                        <strong>{{ optional($order->created_at)->format('d M Y') }}</strong>
                        {{ optional($order->created_at)->format('H:i') }} WIB
                    </div>
                </div>
            </a>
        @empty
            <div class="empty-state">
                <div class="empty-state-icon">
                    <i class="fas fa-inbox"></i>
                </div>
                <h3>Belum ada pesanan</h3>
                <p>
                    Saat ada pembeli yang melakukan transaksi pada kategori ini, pesanan akan muncul di sini
                    dalam tampilan kartu yang tetap rapi dan mudah dibaca.
                </p>
            </div>
        @endforelse
    </div>

    @if($orders->hasPages())
        <div class="pagination-wrap">
            <div class="pagination-bar">
                @if($orders->onFirstPage())
                    <span class="page-pill disabled">
                        <i class="fas fa-chevron-left"></i>
                    </span>
                @else
                    <a href="{{ $orders->appends(['type' => $type])->previousPageUrl() }}" class="page-pill" aria-label="Halaman sebelumnya">
                        <i class="fas fa-chevron-left"></i>
                    </a>
                @endif

                @foreach($orders->appends(['type' => $type])->getUrlRange(1, $orders->lastPage()) as $page => $url)
                    @if($page == $orders->currentPage())
                        <span class="page-number active">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}" class="page-number">{{ $page }}</a>
                    @endif
                @endforeach

                @if($orders->hasMorePages())
                    <a href="{{ $orders->appends(['type' => $type])->nextPageUrl() }}" class="page-pill" aria-label="Halaman berikutnya">
                        <i class="fas fa-chevron-right"></i>
                    </a>
                @else
                    <span class="page-pill disabled">
                        <i class="fas fa-chevron-right"></i>
                    </span>
                @endif
            </div>

            <div class="pagination-meta">
                Menampilkan
                <strong>{{ $orders->firstItem() }}</strong>
                -
                <strong>{{ $orders->lastItem() }}</strong>
                dari
                <strong>{{ $orders->total() }}</strong>
                pesanan.
            </div>
        </div>
    @endif
</div>
@endsection
