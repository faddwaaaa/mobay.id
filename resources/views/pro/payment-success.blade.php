@extends('layouts.dashboard')
@section('title', 'Pembayaran Pro Berhasil | Toko')

@section('content')
<div class="pro-payment-success-page">
    <div class="pro-payment-success-container">
        <div class="pro-payment-success-card">
            <div class="pro-payment-success-icon">
                <i class="fas fa-check-circle"></i>
            </div>

            <h1 class="pro-payment-success-title">Pembayaran Berhasil!</h1>

            <p class="pro-payment-success-subtitle">
                Terima kasih sudah upgrade ke Pro. Akun Anda sekarang sudah aktif dengan semua fitur Pro.
            </p>

            <div class="pro-payment-success-details">
                <div class="pro-payment-detail-item">
                    <span class="pro-payment-detail-label">Paket:</span>
                    <span class="pro-payment-detail-value">
                        Pro {{ $pro_type === 'yearly' ? 'Tahunan (365 hari)' : 'Bulanan (30 hari)' }}
                    </span>
                </div>

                <div class="pro-payment-detail-item">
                    <span class="pro-payment-detail-label">Aktif Hingga:</span>
                    <span class="pro-payment-detail-value">
                        {{ $pro_until->format('d M Y H:i') }}
                    </span>
                </div>

                <div class="pro-payment-detail-item">
                    <span class="pro-payment-detail-label">Sisa Hari:</span>
                    <span class="pro-payment-detail-value">
                        {{ $pro_until->diffInDays(now()) }} hari
                    </span>
                </div>
            </div>

            <div class="pro-payment-success-features">
                <h3 class="pro-payment-features-title">Fitur yang Sudah Aktif:</h3>
                <ul class="pro-payment-features-list">
                    <li><i class="fas fa-check"></i> Analitik lanjutan dengan tren dan insight penjualan</li>
                    <li><i class="fas fa-check"></i> Ekspor data ke CSV dan Excel</li>
                    <li><i class="fas fa-check"></i> Background gambar dan gaya tombol premium</li>
                    <li><i class="fas fa-check"></i> Lebih banyak font dan opsi customization</li>
                    <li><i class="fas fa-check"></i> Batas rekening bank lebih besar</li>
                </ul>
            </div>

            <div class="pro-payment-success-actions">
                <a href="{{ route('dashboard.appearance') }}" class="pro-payment-btn pro-payment-btn-primary">
                    <i class="fas fa-palette"></i>
                    Akses Editor Tampilan
                </a>
                <a href="{{ route('analitik.index') }}" class="pro-payment-btn pro-payment-btn-secondary">
                    <i class="fas fa-chart-line"></i>
                    Lihat Analitik Pro
                </a>
                <a href="{{ route('dashboard') }}" class="pro-payment-btn pro-payment-btn-secondary">
                    <i class="fas fa-arrow-left"></i>
                    Kembali ke Dashboard
                </a>
            </div>
        </div>
    </div>
</div>

<style>
.pro-payment-success-page {
    min-height: 100vh;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    padding: 40px 20px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.pro-payment-success-container {
    width: 100%;
    max-width: 600px;
}

.pro-payment-success-card {
    background: #ffffff;
    border-radius: 24px;
    padding: 48px 32px;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
    text-align: center;
}

.pro-payment-success-icon {
    font-size: 80px;
    color: #10b981;
    margin-bottom: 24px;
    animation: scaleIn 0.6s ease-out;
}

.pro-payment-success-title {
    margin: 0 0 16px;
    font-size: 32px;
    font-weight: 800;
    color: #0f172a;
}

.pro-payment-success-subtitle {
    margin: 0 0 32px;
    font-size: 16px;
    color: #475569;
    line-height: 1.6;
}

.pro-payment-success-details {
    background: #f0fdf4;
    border: 1px solid #bbf7d0;
    border-radius: 16px;
    padding: 24px;
    margin-bottom: 24px;
    text-align: left;
}

.pro-payment-detail-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 0;
    border-bottom: 1px solid #d1fae5;
}

.pro-payment-detail-item:last-child {
    border-bottom: none;
}

.pro-payment-detail-label {
    font-weight: 600;
    color: #047857;
}

.pro-payment-detail-value {
    font-weight: 700;
    color: #0f172a;
}

.pro-payment-success-features {
    text-align: left;
    margin-bottom: 32px;
    padding: 24px;
    background: #f8fafc;
    border-radius: 16px;
    border: 1px solid #e2e8f0;
}

.pro-payment-features-title {
    margin: 0 0 16px;
    font-size: 16px;
    font-weight: 700;
    color: #0f172a;
}

.pro-payment-features-list {
    margin: 0;
    padding: 0;
    list-style: none;
}

.pro-payment-features-list li {
    display: flex;
    gap: 12px;
    align-items: flex-start;
    margin-bottom: 10px;
    font-size: 14px;
    color: #475569;
    line-height: 1.5;
}

.pro-payment-features-list li:last-child {
    margin-bottom: 0;
}

.pro-payment-features-list i {
    color: #10b981;
    font-weight: 700;
    min-width: 20px;
    text-align: center;
    margin-top: 2px;
}

.pro-payment-success-actions {
    display: grid;
    gap: 12px;
}

.pro-payment-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    min-height: 48px;
    padding: 0 24px;
    border-radius: 12px;
    text-decoration: none;
    font-size: 14px;
    font-weight: 700;
    cursor: pointer;
    border: none;
    transition: all 0.3s;
}

.pro-payment-btn-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: #ffffff;
    box-shadow: 0 8px 20px rgba(102, 126, 234, 0.3);
}

.pro-payment-btn-primary:hover {
    box-shadow: 0 12px 30px rgba(102, 126, 234, 0.4);
    transform: translateY(-2px);
}

.pro-payment-btn-secondary {
    background: #f1f5f9;
    color: #1e293b;
    border: 1px solid #e2e8f0;
}

.pro-payment-btn-secondary:hover {
    background: #e2e8f0;
    border-color: #cbd5e1;
}

@keyframes scaleIn {
    from {
        transform: scale(0);
        opacity: 0;
    }
    to {
        transform: scale(1);
        opacity: 1;
    }
}

@media (max-width: 640px) {
    .pro-payment-success-card {
        padding: 32px 20px;
    }

    .pro-payment-success-icon {
        font-size: 60px;
    }

    .pro-payment-success-title {
        font-size: 24px;
    }

    .pro-payment-success-subtitle {
        font-size: 14px;
        margin-bottom: 24px;
    }

    .pro-payment-btn {
        min-height: 44px;
        font-size: 13px;
    }
}
</style>
@endsection
