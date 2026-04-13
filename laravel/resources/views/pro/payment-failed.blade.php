@extends('layouts.dashboard')
@section('title', 'Pembayaran Gagal | Toko')

@section('content')
<div class="pro-payment-failed-page">
    <div class="pro-payment-failed-container">
        <div class="pro-payment-failed-card">
            <div class="pro-payment-failed-icon">
                <i class="fas fa-exclamation-circle"></i>
            </div>

            <h1 class="pro-payment-failed-title">Pembayaran Gagal</h1>

            <p class="pro-payment-failed-subtitle">
                Maaf, pembayaran Anda tidak dapat diproses. Silakan coba lagi atau hubungi customer support kami.
            </p>

            <div class="pro-payment-failed-reasons">
                <h3 class="pro-payment-reasons-title">Kemungkinan Penyebab:</h3>
                <ul class="pro-payment-reasons-list">
                    <li><i class="fas fa-info-circle"></i> Saldo di e-wallet/rekening tidak cukup</li>
                    <li><i class="fas fa-info-circle"></i> Dokumen/persetujuan finansial belum lengkap</li>
                    <li><i class="fas fa-info-circle"></i> Koneksi internet terputus saat proses</li>
                    <li><i class="fas fa-info-circle"></i> Limit transaksi harian sudah tercapai</li>
                </ul>
            </div>

            <div class="pro-payment-failed-actions">
                <a href="{{ route('premium.index') }}" class="pro-payment-btn pro-payment-btn-primary">
                    <i class="fas fa-redo"></i>
                    Coba Lagi
                </a>
                <a href="{{ route('dashboard') }}" class="pro-payment-btn pro-payment-btn-secondary">
                    <i class="fas fa-arrow-left"></i>
                    Kembali ke Dashboard
                </a>
            </div>

            <div class="pro-payment-failed-support">
                <p class="pro-payment-support-text">
                    Masih ada masalah? 
                    <a href="https://wa.me/6285600489815" target="_blank" class="pro-payment-support-link">
                        Hubungi customer support via WhatsApp
                    </a>
                </p>
            </div>
        </div>
    </div>
</div>

<style>
.pro-payment-failed-page {
    min-height: 100vh;
    background: linear-gradient(135deg, #f87171 0%, #dc2626 100%);
    padding: 40px 20px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.pro-payment-failed-container {
    width: 100%;
    max-width: 600px;
}

.pro-payment-failed-card {
    background: #ffffff;
    border-radius: 24px;
    padding: 48px 32px;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
    text-align: center;
}

.pro-payment-failed-icon {
    font-size: 80px;
    color: #dc2626;
    margin-bottom: 24px;
    animation: shake 0.5s ease-in-out;
}

.pro-payment-failed-title {
    margin: 0 0 16px;
    font-size: 32px;
    font-weight: 800;
    color: #0f172a;
}

.pro-payment-failed-subtitle {
    margin: 0 0 32px;
    font-size: 16px;
    color: #475569;
    line-height: 1.6;
}

.pro-payment-failed-reasons {
    background: #fef2f2;
    border: 1px solid #fecaca;
    border-radius: 16px;
    padding: 24px;
    margin-bottom: 24px;
    text-align: left;
}

.pro-payment-reasons-title {
    margin: 0 0 16px;
    font-size: 16px;
    font-weight: 700;
    color: #991b1b;
}

.pro-payment-reasons-list {
    margin: 0;
    padding: 0;
    list-style: none;
}

.pro-payment-reasons-list li {
    display: flex;
    gap: 12px;
    align-items: flex-start;
    margin-bottom: 10px;
    font-size: 14px;
    color: #475569;
    line-height: 1.5;
}

.pro-payment-reasons-list li:last-child {
    margin-bottom: 0;
}

.pro-payment-reasons-list i {
    color: #dc2626;
    min-width: 20px;
    text-align: center;
    margin-top: 2px;
}

.pro-payment-failed-actions {
    display: grid;
    gap: 12px;
    margin-bottom: 24px;
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
    background: linear-gradient(135deg, #f87171 0%, #dc2626 100%);
    color: #ffffff;
    box-shadow: 0 8px 20px rgba(220, 38, 38, 0.3);
}

.pro-payment-btn-primary:hover {
    box-shadow: 0 12px 30px rgba(220, 38, 38, 0.4);
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

.pro-payment-failed-support {
    padding-top: 24px;
    border-top: 1px solid #e5e7eb;
}

.pro-payment-support-text {
    margin: 0;
    font-size: 14px;
    color: #475569;
}

.pro-payment-support-link {
    color: #0ea5e9;
    text-decoration: none;
    font-weight: 600;
    transition: color 0.2s;
}

.pro-payment-support-link:hover {
    color: #0284c7;
    text-decoration: underline;
}

@keyframes shake {
    0%, 100% { transform: translateX(0); }
    25% { transform: translateX(-10px); }
    75% { transform: translateX(10px); }
}

@media (max-width: 640px) {
    .pro-payment-failed-card {
        padding: 32px 20px;
    }

    .pro-payment-failed-icon {
        font-size: 60px;
    }

    .pro-payment-failed-title {
        font-size: 24px;
    }

    .pro-payment-failed-subtitle {
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
