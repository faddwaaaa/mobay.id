@extends('layouts.dashboard')

@section('title', 'Pesanan Masuk | Payou.id')

@section('content')
<style>
.riwayat-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
    color: #111827;
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}
.riwayat-header {
    margin-bottom: 22px;
}
.riwayat-header h1 {
    font-size: 26px;
    font-weight: 700;
    margin: 0 0 4px;
}
.riwayat-header p {
    font-size: 14px;
    color: #6b7280;
    margin: 0;
}
.riwayat-tabs {
    display: flex;
    gap: 8px;
    background: #f3f4f6;
    padding: 6px;
    border-radius: 12px;
    border: 1px solid #e5e7eb;
    margin-bottom: 20px;
}
.riwayat-tab {
    flex: 1;
    text-align: center;
    text-decoration: none;
    border-radius: 8px;
    padding: 11px 0;
    font-size: 14px;
    font-weight: 600;
    color: #6b7280;
    transition: .2s ease;
}
.riwayat-tab.active {
    background: #fff;
    color: #2563eb;
    border: 1px solid #e5e7eb;
    box-shadow: 0 2px 8px rgba(37, 99, 235, .1);
}
.riwayat-card {
    display: block;
    text-decoration: none;
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 16px;
    padding: 18px 20px;
    color: inherit;
    transition: .2s ease;
}
.riwayat-card:hover {
    border-color: #2563eb;
    box-shadow: 0 8px 20px rgba(37, 99, 235, .08);
    transform: translateY(-2px);
}
.riwayat-card-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
    flex-wrap: wrap;
    margin-bottom: 10px;
}
.riwayat-code {
    font-family: monospace;
    font-size: 13px;
    font-weight: 700;
    color: #475569;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 999px;
    padding: 4px 10px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    max-width: 180px;
}
.riwayat-badge {
    font-size: 11px;
    font-weight: 700;
    border-radius: 999px;
    padding: 5px 10px;
    flex-shrink: 0;
}
.riwayat-badge.fisik {
    background: #dcfce7;
    border: 1px solid #bbf7d0;
    color: #166534;
}
.riwayat-badge.digital {
    background: #dbeafe;
    border: 1px solid #bfdbfe;
    color: #1d4ed8;
}
.riwayat-title {
    font-size: 15px;
    font-weight: 700;
    color: #0f172a;
    margin: 0 0 7px;
    line-height: 1.45;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}
.riwayat-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    font-size: 12px;
    color: #64748b;
}
.riwayat-amount {
    font-size: 20px;
    font-weight: 800;
    color: #2563eb;
    margin-top: 11px;
}
.riwayat-date {
    font-size: 12px;
    color: #9ca3af;
    margin-top: 2px;
}
.riwayat-empty {
    text-align: center;
    padding: 56px 24px;
    border: 1px dashed #cbd5e1;
    border-radius: 16px;
    background: #fff;
    color: #94a3b8;
    grid-column: 1 / -1;
}
.riwayat-empty i {
    font-size: 40px;
    margin-bottom: 10px;
    display: block;
}

@media (max-width: 768px) {
    .orders-grid { grid-template-columns: 1fr !important; }
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
                <h1 style="margin:0;font-size:24px;font-weight:600;color:#000000;">Pesanan Masuk</h1>
                <p style="margin:0;font-size:14px;color:#797979;">Lihat daftar pesanan dari pembeli berdasarkan kategori produk.</p>
            </div>
        </div>
    </div>

    <div class="riwayat-tabs">
        <a href="{{ route('orders.index', ['type' => 'fisik']) }}" class="riwayat-tab {{ $type === 'fisik' ? 'active' : '' }}">Pesanan Fisik</a>
        <a href="{{ route('orders.index', ['type' => 'digital']) }}" class="riwayat-tab {{ $type === 'digital' ? 'active' : '' }}">Pesanan Digital</a>
    </div>

    <div class="orders-grid" style="display:grid;grid-template-columns:1fr 1fr;gap:14px;">
        @if($orders->count() === 0)
            <div class="riwayat-empty">
                <i class="fas fa-inbox"></i>
                Belum ada pesanan untuk kategori ini.
            </div>
        @else
            @foreach($orders as $order)
                @php
                    $notes = $order->order_notes ?? [];
                    $qty = (int) ($notes['qty'] ?? 1);
                    $buyerName = $notes['buyer_name'] ?? '-';
                    $productTitle = $notes['product_title'] ?? ($order->order_product->title ?? '-');
                    $typeBadge = ($notes['product_type'] ?? 'fisik') === 'digital' ? 'digital' : 'fisik';
                @endphp
                <a href="{{ route('orders.show', $order->id) }}" class="riwayat-card">
                    <div class="riwayat-card-header">
                        <span class="riwayat-code">{{ $order->order_id }}</span>
                        <span class="riwayat-badge {{ $typeBadge }}">{{ strtoupper($typeBadge) }}</span>
                    </div>
                    <div class="riwayat-title">{{ $productTitle }}</div>
                    <div class="riwayat-meta">
                        <span><strong>Pembeli:</strong> {{ $buyerName }}</span>
                        <span><strong>Qty:</strong> {{ $qty }}</span>
                    </div>
                    <div class="riwayat-amount">Rp {{ number_format((int) $order->amount, 0, ',', '.') }}</div>
                    <div class="riwayat-date">{{ optional($order->created_at)->format('d M Y, H:i') }} WIB</div>
                </a>
            @endforeach
        @endif
    </div>

    @if($orders->hasPages())
    <div style="margin-top:20px;display:flex;align-items:center;justify-content:center;gap:6px;flex-wrap:wrap;">
        {{-- Tombol Sebelumnya --}}
        @if($orders->onFirstPage())
            <span style="padding:8px 16px;border-radius:8px;border:1px solid #e5e7eb;background:#f9fafb;color:#cbd5e0;font-size:13px;font-weight:600;cursor:not-allowed;">
                <i class="fas fa-chevron-left" style="margin-right:5px;"></i> Sebelumnya
            </span>
        @else
            <a href="{{ $orders->appends(['type' => $type])->previousPageUrl() }}"
            style="padding:8px 16px;border-radius:8px;border:1px solid #e5e7eb;background:#fff;color:#374151;font-size:13px;font-weight:600;text-decoration:none;transition:all .15s;"
            onmouseenter="this.style.borderColor='#2563eb';this.style.color='#2563eb'"
            onmouseleave="this.style.borderColor='#e5e7eb';this.style.color='#374151'">
                <i class="fas fa-chevron-left" style="margin-right:5px;"></i> Sebelumnya
            </a>
        @endif

        {{-- Nomor Halaman --}}
        @foreach($orders->appends(['type' => $type])->getUrlRange(1, $orders->lastPage()) as $page => $url)
            @if($page == $orders->currentPage())
                <span style="padding:8px 14px;border-radius:8px;border:1px solid #2563eb;background:#2563eb;color:#fff;font-size:13px;font-weight:700;min-width:38px;text-align:center;">
                    {{ $page }}
                </span>
            @else
                <a href="{{ $url }}"
                style="padding:8px 14px;border-radius:8px;border:1px solid #e5e7eb;background:#fff;color:#374151;font-size:13px;font-weight:600;text-decoration:none;min-width:38px;text-align:center;transition:all .15s;"
                onmouseenter="this.style.borderColor='#2563eb';this.style.color='#2563eb'"
                onmouseleave="this.style.borderColor='#e5e7eb';this.style.color='#374151'">
                    {{ $page }}
                </a>
            @endif
        @endforeach

        {{-- Tombol Berikutnya --}}
        @if($orders->hasMorePages())
            <a href="{{ $orders->appends(['type' => $type])->nextPageUrl() }}"
            style="padding:8px 16px;border-radius:8px;border:1px solid #e5e7eb;background:#fff;color:#374151;font-size:13px;font-weight:600;text-decoration:none;transition:all .15s;"
            onmouseenter="this.style.borderColor='#2563eb';this.style.color='#2563eb'"
            onmouseleave="this.style.borderColor='#e5e7eb';this.style.color='#374151'">
                Berikutnya <i class="fas fa-chevron-right" style="margin-left:5px;"></i>
            </a>
        @else
            <span style="padding:8px 16px;border-radius:8px;border:1px solid #e5e7eb;background:#f9fafb;color:#cbd5e0;font-size:13px;font-weight:600;cursor:not-allowed;">
                Berikutnya <i class="fas fa-chevron-right" style="margin-left:5px;"></i>
            </span>
        @endif
    </div>

    {{-- Info halaman --}}
    <div style="text-align:center;margin-top:10px;font-size:12px;color:#9ca3af;">
        Menampilkan {{ $orders->firstItem() }}–{{ $orders->lastItem() }} dari {{ $orders->total() }} pesanan
    </div>
    @endif
</div>
@endsection