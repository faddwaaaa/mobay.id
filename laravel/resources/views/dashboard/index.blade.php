@extends('layouts.dashboard')

@section('title', 'Dashboard | Payou.id')

@php
    $user = Auth::user();
    $userSlug = $user->profile?->username ?? Str::slug($user->name);
@endphp

@section('content')

<!-- PAGE HEADER -->
<div class="page-header">
    <div>
        <h1>Dashboard</h1>
        <p class="subtitle">
            Selamat datang kembali, <strong>{{ Auth::user()->name }}</strong> 👋
        </p>
    </div>
    <button class="btn-create-link">
        <i class="fas fa-plus-circle"></i>
        <span>Buat Link Baru</span>
    </button>
</div>

<!-- STATS CARDS -->
<div class="stats-cards">

    <div class="stat-card">
        <div class="stat-header">
            <h3>Total Klik</h3>
        </div>
        <div class="stat-value">
            {{ number_format($totalClicks) }}
        </div>
        <div class="stat-footer">
            <i class="fas fa-calendar"></i>
            <span>Semua waktu</span>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-header">
            <h3>Total Link</h3>
        </div>
        <div class="stat-value">
            {{ $totalLinks }}
        </div>
        <div class="stat-footer">
            <i class="fas fa-check-circle"></i>
            <span>{{ $activeLinks }} aktif</span>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-header">
            <h3>Saldo Tersedia</h3>
        </div>
        <div class="stat-value">
            {{ 'Rp ' . number_format($balance ?? 0, 0, ',', '.') }}
        </div>
        <div class="stat-footer">
            <i class="fas fa-wallet"></i>
            <span>Saldo aktif</span>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-header">
            <h3>Konversi</h3>
        </div>
        <div class="stat-value">
            0%
        </div>
        <div class="stat-footer">
            <i class="fas fa-shopping-cart"></i>
            <span>0 transaksi</span>
        </div>
    </div>

</div>

<!-- RECENT LINKS -->
<div class="content-section">
    <div class="section-header">
        <h2><i class="fas fa-link"></i> Link Terbaru</h2>
        <a href="#" class="view-all">
            Lihat Semua <i class="fas fa-arrow-right"></i>
        </a>
    </div>

    <div class="links-grid">
        @forelse($links as $link)
            <div class="link-card">
                <div class="link-card-header">
                    <div class="link-icon">
                        <i class="fas fa-link"></i>
                    </div>
                    <div class="link-status {{ $link->is_active ? 'active' : 'inactive' }}"></div>
                </div>

                <h3>{{ $link->title }}</h3>
                <p class="link-description">{{ $link->url }}</p>

                <div class="link-url">
                    <span>payou.id/{{ $userSlug }}/{{ $link->slug }}</span>
                    <button class="copy-btn"
                        data-url="{{ url($userSlug.'/'.$link->slug) }}">
                        <i class="far fa-copy"></i>
                    </button>
                </div>

                <div class="link-stats">
                    <div class="stat">
                        <i class="fas fa-mouse-pointer"></i>
                        <span>{{ $link->clicks_count }} klik</span>
                    </div>
                </div>

                <div class="link-actions">
                    <button class="btn-action edit"><i class="fas fa-edit"></i></button>
                    <button class="btn-action qr"><i class="fas fa-qrcode"></i></button>
                    <button class="btn-action analytics"><i class="fas fa-chart-bar"></i></button>
                </div>
            </div>
        @empty
            <p>Belum ada link.</p>
        @endforelse
    </div>
</div>

<!-- TWO COLUMN SECTION -->
<div class="two-column-section">

    <div class="column">

        <!-- QUICK ACTION -->
        <div class="content-section">
            <div class="section-header">
                <h2><i class="fas fa-bolt"></i> Aksi Cepat</h2>
            </div>

            <div class="quick-actions">
                <button class="quick-action-btn">
                    <div class="action-icon"><i class="fas fa-qrcode"></i></div>
                    <span>Buat QR Code</span>
                </button>

                <button class="quick-action-btn">
                    <div class="action-icon"><i class="fas fa-paint-brush"></i></div>
                    <span>Ubah Tema</span>
                </button>

                <button class="quick-action-btn">
                    <div class="action-icon"><i class="fas fa-file-export"></i></div>
                    <span>Export Data</span>
                </button>

                <button class="quick-action-btn">
                    <div class="action-icon"><i class="fas fa-share-alt"></i></div>
                    <span>Bagikan Halaman</span>
                </button>
            </div>
        </div>

        <!-- SALDO -->
        <div class="content-section">
            <div class="section-header">
                <h2><i class="fas fa-money-bill-wave"></i> Saldo & Transaksi</h2>
            </div>

            <div class="balance-overview">
                <div class="total-balance">
                    <h3>Total Saldo</h3>
                    <div class="balance-amount">{{ 'Rp ' . number_format($balance ?? 0, 0, ',', '.') }}</div>
                </div>

                <div class="balance-actions">
                    <button id="btn-topup" class="btn-balance topup">
                        <i class="fas fa-plus-circle"></i> Top Up
                    </button>
                    <button id="btn-withdraw" class="btn-balance withdraw">
                        <i class="fas fa-arrow-up"></i> Tarik
                    </button>
                </div>

                <div id="topup-form" style="display:none; margin-top:12px;">
                    <form id="form-topup">
                        @csrf
                        <label for="amount">Jumlah Top Up (Rp)</label>
                        <input type="number" id="amount" name="amount" min="{{ config('midtrans.topup.min_amount', 10000) }}" max="{{ config('midtrans.topup.max_amount', 10000000) }}" required>
                        <button type="submit" class="btn btn-primary">Bayar</button>
                        <button type="button" id="cancel-topup" class="btn">Batal</button>
                    </form>
                </div>
            </div>
        </div>

    </div>

    <!-- RIGHT COLUMN -->
    <div class="column">

        <!-- CHART -->
        <div class="content-section">
            <div class="section-header">
                <h2><i class="fas fa-chart-line"></i> Performa 7 Hari Terakhir</h2>
            </div>

<div class="chart-container">
    <div class="chart-bars">
        @foreach ($data as $index => $value)
            @php
                $height = $maxClick > 0 ? ($value / $maxClick) * 100 : 0;
            @endphp

            <div class="chart-bar"
                style="height: {{ $height }}%;"
                data-day="{{ $labels[$index] }}"
                data-value="{{ $value }}">
            </div>
        @endforeach
    </div>
</div>


                <div class="chart-labels">
                    @foreach ($labels as $label)
                        <span>{{ $label }}</span>
                    @endforeach
                </div>
            </div>
        </div>
        </div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const btnTopup = document.getElementById('btn-topup');
    const topupFormWrap = document.getElementById('topup-form');
    const cancelTopup = document.getElementById('cancel-topup');
    const formTopup = document.getElementById('form-topup');

    if (btnTopup) {
        btnTopup.addEventListener('click', () => {
            topupFormWrap.style.display = topupFormWrap.style.display === 'none' ? 'block' : 'none';
        });
    }

    if (cancelTopup) {
        cancelTopup.addEventListener('click', () => topupFormWrap.style.display = 'none');
    }

    if (formTopup) {
        formTopup.addEventListener('submit', async (e) => {
            e.preventDefault();
            const amount = parseInt(document.getElementById('amount').value, 10);
            if (!amount || amount <= 0) {
                alert('Masukkan jumlah top-up yang valid.');
                return;
            }

            try {
                const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                const res = await fetch('/api/topup', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ amount })
                });

                const data = await res.json();
                if (!res.ok) {
                    alert(data.message || 'Gagal membuat transaksi top-up');
                    return;
                }

                // If Midtrans snap token returned, you can integrate Snap.js here to open payment.
                if (data.snap_token) {
                    alert('Transaksi dibuat. Token Snap tersedia.');
                } else {
                    alert(data.message || 'Top-up dibuat.');
                }

                // Optionally refresh the page to update balance after payment success
                // location.reload();
            } catch (err) {
                console.error(err);
                alert('Terjadi kesalahan saat membuat top-up.');
            }
        });
    }
});
</script>
@endpush

@endsection
