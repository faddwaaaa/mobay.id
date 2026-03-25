@extends('layouts.dashboard')

@section('title', 'Detail Pesanan | Mobay.id')

@section('content')
@php
    $productType = ($notes['product_type'] ?? $product->product_type ?? 'fisik') === 'digital' ? 'digital' : 'fisik';
    $statusClass = $order->status === 'settlement' ? 'success' : ($order->status === 'pending' ? 'pending' : 'failed');
@endphp

<style>
.order-detail-wrap {
    max-width: 1080px;
    margin: 0 auto;
    color: #111827;
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}
.order-back {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 8px 14px;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    text-decoration: none;
    color: #475569;
    font-size: 13px;
    font-weight: 600;
    background: #fff;
    margin-bottom: 14px;
}
.order-top {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 12px;
    flex-wrap: wrap;
    margin-bottom: 16px;
}
.order-title {
    font-size: 27px;
    font-weight: 800;
    margin: 0 0 4px;
}
.order-sub {
    margin: 0;
    font-size: 13px;
    color: #64748b;
}
.wa-btn {
    display: inline-flex;
    align-items: center;
    gap: 9px;
    border-radius: 14px;
    padding: 12px 20px;
    text-decoration: none;
    color: #fff;
    font-size: 13px;
    font-weight: 700;
    background: linear-gradient(135deg, #16a34a, #22c55e);
    box-shadow: 0 10px 22px rgba(34, 197, 94, .35);
}
.wa-btn:hover {
    filter: brightness(1.02);
    transform: translateY(-1px);
}
.summary-strip {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 10px;
    margin-bottom: 12px;
}
.summary-box {
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 12px 14px;
}
.summary-box .k {
    color: #64748b;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .06em;
    margin-bottom: 4px;
}
.summary-box .v {
    font-size: 16px;
    font-weight: 800;
    color: #0f172a;
}
.summary-box .v.total {
    color: #2563eb;
}
.order-grid {
    display: grid;
    gap: 12px;
    grid-template-columns: 1.15fr .85fr;
}
.card {
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    padding: 18px;
}
.card-title {
    margin: 0 0 12px;
    font-size: 12px;
    font-weight: 800;
    color: #94a3b8;
    letter-spacing: .08em;
    text-transform: uppercase;
}
.status-pill {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 12px;
    border-radius: 999px;
    font-size: 11px;
    font-weight: 700;
}
.status-pill.success { background: #dcfce7; border: 1px solid #bbf7d0; color: #166534; }
.status-pill.pending { background: #fef9c3; border: 1px solid #fde047; color: #854d0e; }
.status-pill.failed { background: #fee2e2; border: 1px solid #fecaca; color: #991b1b; }
.line {
    display: flex;
    justify-content: space-between;
    gap: 10px;
    padding: 8px 0;
    border-bottom: 1px dashed #e2e8f0;
    font-size: 13px;
}
.line:last-child {
    border-bottom: none;
}
.line .label {
    color: #64748b;
}
.line .value {
    color: #0f172a;
    font-weight: 600;
    text-align: right;
}
.line.total .value {
    color: #2563eb;
    font-size: 20px;
    font-weight: 800;
}
.product-name {
    font-size: 17px;
    font-weight: 800;
    margin-bottom: 10px;
}
.type-pill {
    display: inline-block;
    padding: 4px 10px;
    border-radius: 8px;
    font-size: 11px;
    font-weight: 700;
    margin-bottom: 10px;
}
.type-pill.fisik { background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
.type-pill.digital { background: #dbeafe; color: #1d4ed8; border: 1px solid #bfdbfe; }
.buyer-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 10px 12px;
    font-size: 13px;
}
.buyer-item {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    padding: 10px;
}
.buyer-item.full {
    grid-column: 1 / -1;
}
.buyer-label {
    color: #94a3b8;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .05em;
    margin-bottom: 4px;
}
.buyer-value {
    color: #0f172a;
    font-weight: 600;
    word-break: break-word;
}
@media (max-width: 900px) {
    .summary-strip { grid-template-columns: 1fr; }
    .order-grid { grid-template-columns: 1fr; }
    .buyer-grid { grid-template-columns: 1fr; }
}
</style>

<div class="order-detail-wrap">
    <a href="{{ route('orders.index', ['type' => $productType]) }}" class="order-back">
        <i class="fas fa-arrow-left"></i>
        Kembali ke Pesanan
    </a>

    <div class="order-top">
        <div>
            <h1 class="order-title">Detail Pesanan</h1>
            <p class="order-sub">Order ID: {{ $order->order_id }}</p>
        </div>
        @if($waLink)
            <a href="{{ $waLink }}" class="wa-btn" target="_blank">
                <i class="fab fa-whatsapp" style="font-size:16px;"></i>
                WhatsApp Pembeli
            </a>
        @endif
    </div>

    <div class="summary-strip">
        <div class="summary-box">
            <div class="k">Status Pembayaran</div>
            <div class="v">{{ strtoupper((string) $order->status) }}</div>
        </div>
        <div class="summary-box">
            <div class="k">Jenis Produk</div>
            <div class="v">{{ strtoupper($productType) }}</div>
        </div>
        <div class="summary-box">
            <div class="k">Total Dibayar</div>
            <div class="v total">Rp {{ number_format((int) $order->amount, 0, ',', '.') }}</div>
        </div>
    </div>

    <div class="order-grid">
        <div class="card">
            <div class="card-title">Produk & Pengiriman</div>
            <span class="type-pill {{ $productType }}">{{ strtoupper($productType) }}</span>
            <div class="product-name">{{ $notes['product_title'] ?? $product->title }}</div>

            <div class="line">
                <span class="label">Jumlah</span>
                <span class="value">{{ (int) ($notes['qty'] ?? 1) }}</span>
            </div>
            <div class="line">
                <span class="label">Harga Satuan</span>
                <span class="value">Rp {{ number_format((int) ($notes['unit_price'] ?? 0), 0, ',', '.') }}</span>
            </div>
            <div class="line">
                <span class="label">Subtotal Produk</span>
                <span class="value">Rp {{ number_format((int) ($notes['subtotal'] ?? 0), 0, ',', '.') }}</span>
            </div>
            <div class="line">
                <span class="label">Ongkos Kirim</span>
                <span class="value">Rp {{ number_format((int) ($notes['shipping_cost'] ?? 0), 0, ',', '.') }}</span>
            </div>
            <div class="line total">
                <span class="label">Total Dibayar</span>
                <span class="value">Rp {{ number_format((int) $order->amount, 0, ',', '.') }}</span>
            </div>
        </div>

        <div class="card">
            <div class="card-title">Status & Waktu</div>
            <div style="margin-bottom:12px;">
                <span class="status-pill {{ $statusClass }}">
                    <i class="fas fa-circle" style="font-size:8px;"></i>
                    {{ strtoupper($order->status) }}
                </span>
            </div>

            <div class="line">
                <span class="label">Dibuat</span>
                <span class="value">{{ optional($order->created_at)->format('d M Y, H:i') }} WIB</span>
            </div>
            <div class="line">
                <span class="label">Metode Bayar</span>
                <span class="value">{{ strtoupper((string) ($order->payment_method ?? '-')) }}</span>
            </div>
            <div class="line">
                <span class="label">Transaction ID</span>
                <span class="value">{{ $order->transaction_id ?? '-' }}</span>
            </div>
            <div class="line">
                <span class="label">Kurir</span>
                <span class="value">{{ strtoupper((string) ($notes['selected_courier'] ?? '-')) }}</span>
            </div>
            <div class="line">
                <span class="label">Layanan</span>
                <span class="value">{{ $notes['selected_service'] ?? '-' }}</span>
            </div>
        </div>
    </div>

    <div class="card" style="margin-top:12px;">
        <div class="card-title">Data Pembeli</div>
        <div class="buyer-grid">
            <div class="buyer-item">
                <div class="buyer-label">Nama</div>
                <div class="buyer-value">{{ $notes['buyer_name'] ?? '-' }}</div>
            </div>
            <div class="buyer-item">
                <div class="buyer-label">No. WhatsApp</div>
                <div class="buyer-value">{{ $notes['buyer_phone'] ?? '-' }}</div>
            </div>
            <div class="buyer-item">
                <div class="buyer-label">Email</div>
                <div class="buyer-value">{{ $notes['buyer_email'] ?? '-' }}</div>
            </div>
            <div class="buyer-item">
                <div class="buyer-label">Area Tujuan</div>
                <div class="buyer-value">{{ $notes['destination_label'] ?? '-' }}</div>
            </div>
            <div class="buyer-item full">
                <div class="buyer-label">Alamat Lengkap</div>
                <div class="buyer-value">{{ $notes['buyer_address'] ?? '-' }}</div>
            </div>
            <div class="buyer-item full">
                <div class="buyer-label">Catatan Pembeli</div>
                <div class="buyer-value">{{ $notes['buyer_notes'] ?? '-' }}</div>
            </div>
        </div>
    </div>
</div>
@endsection
