@extends('layouts.dashboard')

@section('title', 'Detail Pembayaran | Payou.id')

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
    }
    
    .detail-title {
        font-size: 20px;
        font-weight: 700;
        color: #111827;
        margin-bottom: 8px;
    }
    
    .status-row {
        display: flex;
        align-items: center;
        gap: 12px;
    }
    
    .order-code {
        font-size: 14px;
        color: #6b7280;
        font-family: monospace;
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
    
    .product-section {
        padding: 24px;
        border-bottom: 1px solid #f3f4f6;
    }
    
    .product-row {
        display: flex;
        gap: 16px;
    }
    
    .product-image {
        width: 120px;
        height: 120px;
        border-radius: 12px;
        object-fit: cover;
        background: #f3f4f6;
        flex-shrink: 0;
        border: 1px solid #e5e7eb;
    }
    
    .product-image-placeholder {
        width: 120px;
        height: 120px;
        border-radius: 12px;
        background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 48px;
        flex-shrink: 0;
        border: 1px solid #e5e7eb;
    }
    
    .product-info {
        flex: 1;
    }
    
    .product-type-badge {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 6px;
        font-size: 11px;
        font-weight: 600;
        margin-bottom: 8px;
    }
    
    .badge-digital {
        background: #eff6ff;
        color: #2563eb;
    }
    
    .badge-physical {
        background: #f0fdf4;
        color: #16a34a;
    }
    
    .product-name {
        font-size: 16px;
        font-weight: 700;
        color: #111827;
        margin-bottom: 8px;
        line-height: 1.4;
    }
    
    .product-qty {
        font-size: 13px;
        color: #6b7280;
        margin-bottom: 12px;
    }
    
    .product-price {
        font-size: 18px;
        font-weight: 800;
        color: #2563eb;
    }
    
    .detail-section {
        padding: 24px;
    }
    
    .section-title {
        font-size: 14px;
        font-weight: 700;
        color: #111827;
        margin-bottom: 16px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .detail-grid {
        display: grid;
        gap: 16px;
    }
    
    .detail-item {
        display: flex;
        justify-content: space-between;
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
    }
    
    .detail-value {
        font-size: 14px;
        font-weight: 600;
        color: #111827;
        text-align: right;
    }
    
    .detail-value.highlight {
        color: #2563eb;
        font-size: 16px;
    }
    
    .buyer-info {
        background: #f9fafb;
        border-radius: 12px;
        padding: 16px;
    }
    
    .buyer-row {
        display: flex;
        gap: 12px;
        margin-bottom: 12px;
    }
    
    .buyer-row:last-child {
        margin-bottom: 0;
    }
    
    .buyer-icon {
        width: 36px;
        height: 36px;
        border-radius: 8px;
        background: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #2563eb;
        flex-shrink: 0;
    }
    
    .buyer-detail {
        flex: 1;
    }
    
    .buyer-label {
        font-size: 11px;
        color: #9ca3af;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 4px;
    }
    
    .buyer-value {
        font-size: 14px;
        font-weight: 600;
        color: #111827;
    }
    
    .alert-info {
        background: #eff6ff;
        border: 1px solid #bfdbfe;
        border-radius: 12px;
        padding: 16px;
        display: flex;
        gap: 12px;
        margin-top: 16px;
    }
    
    .alert-icon {
        font-size: 20px;
        color: #2563eb;
        flex-shrink: 0;
    }
    
    .alert-text {
        font-size: 13px;
        color: #1e40af;
        line-height: 1.6;
    }

    @media (max-width: 768px) {
        .product-row {
            flex-direction: column;
        }
        
        .product-image,
        .product-image-placeholder {
            width: 100%;
            height: 200px;
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

    <!-- Header Card -->
    <div class="detail-card">
        <div class="detail-header">
            <div class="detail-title">Detail Pembayaran</div>
            <div class="status-row">
                <span class="order-code">{{ $transaction->order_id }}</span>
                <button class="copy-code-btn" onclick="copyToClipboard('{{ $transaction->order_id }}', this)">
                    <i class="fas fa-copy"></i> Salin
                </button>
                <span class="status-badge status-{{ $transaction->status === 'settlement' ? 'success' : ($transaction->status === 'pending' ? 'pending' : 'failed') }}">
                    {{ $transaction->status === 'settlement' ? 'Pembayaran Berhasil' : ($transaction->status === 'pending' ? 'Menunggu Pembayaran' : 'Pembayaran Gagal') }}
                </span>
            </div>
        </div>

        <!-- Product Section -->
        @if(isset($notes['product_id']))
            @php
                $product = \App\Models\Product::with('images')->find($notes['product_id']);
            @endphp
            
            @if($product)
                <div class="product-section">
                    <div class="product-row">
                        @if($product->images && $product->images->count() > 0)
                            <img src="{{ asset('storage/' . $product->images->first()->image) }}" 
                                 class="product-image" 
                                 alt="{{ $product->title }}">
                        @else
                            <div class="product-image-placeholder">
                                🛍️
                            </div>
                        @endif

                        <div class="product-info">
                            <span class="product-type-badge badge-{{ $notes['product_type'] === 'digital' ? 'digital' : 'physical' }}">
                                {{ $notes['product_type'] === 'digital' ? '📦 Produk Digital' : '🚚 Produk Fisik' }}
                            </span>
                            <div class="product-name">{{ $notes['product_title'] ?? $product->title }}</div>
                            <div class="product-qty">
                                Jumlah: {{ $notes['qty'] ?? 1 }} item
                                @if(isset($notes['unit_price']))
                                    × Rp {{ number_format($notes['unit_price'], 0, ',', '.') }}
                                @endif
                            </div>
                            <div class="product-price">
                                Rp {{ number_format($transaction->amount, 0, ',', '.') }}
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        @endif

        <!-- Transaction Details -->
        <div class="detail-section">
            <div class="section-title">Informasi Transaksi</div>
            <div class="detail-grid">
                <div class="detail-item">
                    <span class="detail-label">ID Transaksi</span>
                    <span class="detail-value">{{ $transaction->transaction_id ?? '-' }}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Metode Pembayaran</span>
                    <span class="detail-value">{{ strtoupper($transaction->payment_method ?? '-') }}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Waktu Transaksi</span>
                    <span class="detail-value">{{ $transaction->created_at->format('d M Y, H:i') }} WIB</span>
                </div>
                @if(isset($notes['base_total']))
                <div class="detail-item">
                    <span class="detail-label">Total Dasar Produk</span>
                    <span class="detail-value">Rp {{ number_format((int) $notes['base_total'], 0, ',', '.') }}</span>
                </div>
                @endif
                @if(isset($notes['payment_fee_amount']))
                <div class="detail-item">
                    <span class="detail-label">Biaya Layanan</span>
                    <span class="detail-value">Rp {{ number_format((int) $notes['payment_fee_amount'], 0, ',', '.') }}</span>
                </div>
                @endif
                <div class="detail-item">
                    <span class="detail-label">Total Pembayaran</span>
                    <span class="detail-value highlight">Rp {{ number_format($transaction->amount, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        <!-- Buyer Information -->
        @if(isset($notes['buyer_name']) || isset($notes['buyer_email']) || isset($notes['buyer_phone']))
            <div class="detail-section" style="background: #fafbfc; border-top: 1px solid #f3f4f6;">
                <div class="section-title">Informasi Pembeli</div>
                <div class="buyer-info">
                    @if(isset($notes['buyer_name']))
                        <div class="buyer-row">
                            <div class="buyer-icon">
                                <i class="fas fa-user"></i>
                            </div>
                            <div class="buyer-detail">
                                <div class="buyer-label">Nama Lengkap</div>
                                <div class="buyer-value">{{ $notes['buyer_name'] }}</div>
                            </div>
                        </div>
                    @endif

                    @if(isset($notes['buyer_email']))
                        <div class="buyer-row">
                            <div class="buyer-icon">
                                <i class="fas fa-envelope"></i>
                            </div>
                            <div class="buyer-detail">
                                <div class="buyer-label">Email</div>
                                <div class="buyer-value">{{ $notes['buyer_email'] }}</div>
                            </div>
                        </div>
                    @endif

                    @if(isset($notes['buyer_phone']))
                        <div class="buyer-row">
                            <div class="buyer-icon">
                                <i class="fas fa-phone"></i>
                            </div>
                            <div class="buyer-detail">
                                <div class="buyer-label">No. WhatsApp</div>
                                <div class="buyer-value">{{ $notes['buyer_phone'] }}</div>
                            </div>
                        </div>
                    @endif

                    @if(isset($notes['buyer_address']))
                        <div class="buyer-row">
                            <div class="buyer-icon">
                                <i class="fas fa-map-marker-alt"></i>
                            </div>
                            <div class="buyer-detail">
                                <div class="buyer-label">Alamat Pengiriman</div>
                                <div class="buyer-value">{{ $notes['buyer_address'] }}</div>
                            </div>
                        </div>
                    @endif

                    @if(isset($notes['buyer_notes']))
                        <div class="buyer-row">
                            <div class="buyer-icon">
                                <i class="fas fa-sticky-note"></i>
                            </div>
                            <div class="buyer-detail">
                                <div class="buyer-label">Catatan</div>
                                <div class="buyer-value">{{ $notes['buyer_notes'] }}</div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        @endif
    </div>

    <!-- Info Alert -->
    <div class="alert-info">
        <div class="alert-icon">ℹ️</div>
        <div class="alert-text">
            <strong>Butuh bantuan?</strong> Jika ada kendala dengan transaksi ini, hubungi customer service kami dengan menyertakan kode transaksi di atas.
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
