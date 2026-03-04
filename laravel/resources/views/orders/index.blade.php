@extends('layouts.dashboard')

@section('title', 'Pesanan Masuk | Payou.id')

@section('content')
<style>
.orders-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
    color: #111827;
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}
.orders-header {
    margin-bottom: 22px;
}
.orders-header h1 {
    font-size: 26px;
    font-weight: 700;
    margin: 0 0 4px;
}
.orders-header p {
    font-size: 14px;
    color: #6b7280;
    margin: 0;
}
.orders-tabs {
    display: flex;
    gap: 8px;
    background: #f3f4f6;
    padding: 6px;
    border-radius: 12px;
    border: 1px solid #e5e7eb;
    margin-bottom: 20px;
}
.orders-tab {
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
.orders-tab.active {
    background: #fff;
    color: #2563eb;
    border: 1px solid #e5e7eb;
    box-shadow: 0 2px 8px rgba(37, 99, 235, .1);
}
.orders-card {
    display: block;
    text-decoration: none;
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 16px;
    padding: 18px 20px;
    margin-bottom: 12px;
    color: inherit;
    transition: .2s ease;
}
.orders-card:hover {
    border-color: #2563eb;
    box-shadow: 0 8px 20px rgba(37, 99, 235, .08);
    transform: translateY(-2px);
}
.orders-card-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
    flex-wrap: wrap;
    margin-bottom: 10px;
}
.orders-code {
    font-family: monospace;
    font-size: 13px;
    font-weight: 700;
    color: #475569;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 999px;
    padding: 4px 10px;
}
.orders-badge {
    font-size: 11px;
    font-weight: 700;
    border-radius: 999px;
    padding: 5px 10px;
}
.orders-badge.fisik {
    background: #dcfce7;
    border: 1px solid #bbf7d0;
    color: #166534;
}
.orders-badge.digital {
    background: #dbeafe;
    border: 1px solid #bfdbfe;
    color: #1d4ed8;
}
.orders-title {
    font-size: 16px;
    font-weight: 700;
    color: #0f172a;
    margin: 0 0 7px;
    line-height: 1.45;
}
.orders-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    font-size: 12px;
    color: #64748b;
}
.orders-amount {
    font-size: 21px;
    font-weight: 800;
    color: #2563eb;
    margin-top: 11px;
}
.orders-date {
    font-size: 12px;
    color: #9ca3af;
    margin-top: 2px;
}
.orders-empty {
    text-align: center;
    padding: 56px 24px;
    border: 1px dashed #cbd5e1;
    border-radius: 16px;
    background: #fff;
    color: #94a3b8;
}
.orders-empty i {
    font-size: 40px;
    margin-bottom: 10px;
    display: block;
}
</style>

<div class="orders-container">
    <div class="orders-header">
        <h1>Pesanan Masuk</h1>
        <p>Lihat daftar pesanan dari pembeli berdasarkan kategori produk.</p>
    </div>

    <div class="orders-tabs">
        <a href="{{ route('orders.index', ['type' => 'fisik']) }}" class="orders-tab {{ $type === 'fisik' ? 'active' : '' }}">Pesanan Fisik</a>
        <a href="{{ route('orders.index', ['type' => 'digital']) }}" class="orders-tab {{ $type === 'digital' ? 'active' : '' }}">Pesanan Digital</a>
    </div>

    @if($orders->count() === 0)
        <div class="orders-empty">
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
            <a href="{{ route('orders.show', $order->id) }}" class="orders-card">
                <div class="orders-card-head">
                    <span class="orders-code">{{ $order->order_id }}</span>
                    <span class="orders-badge {{ $typeBadge }}">{{ strtoupper($typeBadge) }}</span>
                </div>
                <div class="orders-title">{{ $productTitle }}</div>
                <div class="orders-meta">
                    <span><strong>Pembeli:</strong> {{ $buyerName }}</span>
                    <span><strong>Qty:</strong> {{ $qty }}</span>
                </div>
                <div class="orders-amount">Rp {{ number_format((int) $order->amount, 0, ',', '.') }}</div>
                <div class="orders-date">{{ optional($order->created_at)->format('d M Y, H:i') }} WIB</div>
            </a>
        @endforeach

        <div style="margin-top:16px;">
            {{ $orders->appends(['type' => $type])->links() }}
        </div>
    @endif
</div>
@endsection
