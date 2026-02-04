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
                    {{-- <a href="{{ route('qr.index') }}"> --}}
                            <p class="font-semibold">Buat QR Code</p>
                        </a>
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
                    <button id="btn-withdraw" class="btn-balance withdraw" onclick="openWithdrawModal()">
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

<!-- MODAL PENARIKAN - SIMPLE ALERT STYLE -->
<div id="withdraw-modal" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 99999; align-items: center; justify-content: center;">
    <div style="background: white; border-radius: 16px; width: 90%; max-width: 480px; max-height: 90vh; overflow-y: auto; box-shadow: 0 10px 40px rgba(0,0,0,0.2);">
        
        <!-- Header -->
        <div style="padding: 24px 24px 16px 24px; border-bottom: 2px solid #f0f0f0; display: flex; align-items: center; justify-content: space-between;">
            <div style="display: flex; align-items: center; gap: 10px;">
                <i class="fas fa-money-bill-wave" style="font-size: 24px; color: #3b82f6;"></i>
                <h3 style="margin: 0; font-size: 20px; font-weight: 700; color: #1a1a1a;">Tarik Saldo</h3>
            </div>
            <button onclick="closeWithdrawModal()" style="background: none; border: none; font-size: 24px; color: #666; cursor: pointer; padding: 8px; border-radius: 8px; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <!-- Body -->
        <div style="padding: 24px;">
            
            <!-- Alert Info Saldo & Minimal -->
            <div style="background: #e8f4fd; border: 2px solid #3b82f6; border-radius: 12px; padding: 16px; margin-bottom: 24px;">
                <div style="display: flex; align-items: start; gap: 12px;">
                    <i class="fas fa-info-circle" style="font-size: 20px; color: #1e40af; margin-top: 2px;"></i>
                    <div style="flex: 1;">
                        <div style="font-weight: 600; color: #1e40af; margin-bottom: 8px; font-size: 14px;">Informasi Penarikan</div>
                        <div style="font-size: 13px; color: #1e40af; line-height: 1.6;">
                            <div style="margin-bottom: 6px;">
                                <strong>Saldo Tersedia:</strong> {{ 'Rp ' . number_format($balance ?? 0, 0, ',', '.') }}
                            </div>
                            <div>
                                <strong>Minimal Penarikan:</strong> Rp 50.000
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form -->
            <form id="form-withdraw" onsubmit="handleWithdrawSubmit(event)">
                @csrf
                
                <!-- Amount -->
                <div style="margin-bottom: 16px;">
                    <label style="display: block; margin-bottom: 6px; font-weight: 600; color: #1a1a1a; font-size: 14px;">
                        Jumlah Penarikan (Rp) <span style="color: #ef4444;">*</span>
                    </label>
                    <input type="number" 
                           name="amount" 
                           min="50000" 
                           max="{{ $balance ?? 0 }}" 
                           step="1000"
                           placeholder="Contoh: 100000"
                           style="width: 100%; padding: 12px 14px; border: 2px solid #e0e0e0; border-radius: 10px; font-size: 14px; box-sizing: border-box;"
                           required>
                </div>

                <!-- Bank -->
                <div style="margin-bottom: 16px;">
                    <label style="display: block; margin-bottom: 6px; font-weight: 600; color: #1a1a1a; font-size: 14px;">
                        Nama Bank <span style="color: #ef4444;">*</span>
                    </label>
                    <select name="bank_name" 
                            style="width: 100%; padding: 12px 14px; border: 2px solid #e0e0e0; border-radius: 10px; font-size: 14px; box-sizing: border-box;"
                            required>
                        <option value="">Pilih Bank</option>
                        <option value="BCA">Bank Central Asia (BCA)</option>
                        <option value="BNI">Bank Negara Indonesia (BNI)</option>
                        <option value="BRI">Bank Rakyat Indonesia (BRI)</option>
                        <option value="MANDIRI">Bank Mandiri</option>
                        <option value="CIMB">CIMB Niaga</option>
                        <option value="PERMATA">Bank Permata</option>
                        <option value="BSI">Bank Syariah Indonesia (BSI)</option>
                        <option value="DANAMON">Bank Danamon</option>
                    </select>
                </div>

                <!-- Account Number -->
                <div style="margin-bottom: 16px;">
                    <label style="display: block; margin-bottom: 6px; font-weight: 600; color: #1a1a1a; font-size: 14px;">
                        Nomor Rekening <span style="color: #ef4444;">*</span>
                    </label>
                    <input type="text" 
                           name="account_number" 
                           pattern="[0-9]+"
                           maxlength="20"
                           placeholder="Contoh: 1234567890"
                           style="width: 100%; padding: 12px 14px; border: 2px solid #e0e0e0; border-radius: 10px; font-size: 14px; box-sizing: border-box;"
                           required>
                </div>

                <!-- Account Name -->
                <div style="margin-bottom: 16px;">
                    <label style="display: block; margin-bottom: 6px; font-weight: 600; color: #1a1a1a; font-size: 14px;">
                        Nama Pemilik Rekening <span style="color: #ef4444;">*</span>
                    </label>
                    <input type="text" 
                           name="account_name" 
                           placeholder="Sesuai rekening bank"
                           style="width: 100%; padding: 12px 14px; border: 2px solid #e0e0e0; border-radius: 10px; font-size: 14px; box-sizing: border-box;"
                           required>
                </div>

                <!-- Notes -->
                <div style="margin-bottom: 20px;">
                    <label style="display: block; margin-bottom: 6px; font-weight: 600; color: #1a1a1a; font-size: 14px;">
                        Catatan (Opsional)
                    </label>
                    <textarea name="notes" 
                              rows="3"
                              maxlength="500"
                              placeholder="Tambahkan catatan..."
                              style="width: 100%; padding: 12px 14px; border: 2px solid #e0e0e0; border-radius: 10px; font-size: 14px; box-sizing: border-box; resize: vertical;"></textarea>
                </div>

                <!-- Buttons -->
                <div style="display: flex; gap: 12px;">
                    <button type="button" 
                            onclick="closeWithdrawModal()" 
                            style="flex: 1; padding: 14px; background: #f0f0f0; color: #1a1a1a; border: none; border-radius: 10px; font-size: 15px; font-weight: 600; cursor: pointer;">
                        Batal
                    </button>
                    <button type="submit" 
                            id="submit-withdraw-btn"
                            style="flex: 1; padding: 14px; background: #3b82f6; color: white; border: none; border-radius: 10px; font-size: 15px; font-weight: 600; cursor: pointer;">
                        <i class="fas fa-check"></i> Ajukan Penarikan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Withdraw Modal Functions
function openWithdrawModal() {
    const modal = document.getElementById('withdraw-modal');
    if (modal) {
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }
}

function closeWithdrawModal() {
    const modal = document.getElementById('withdraw-modal');
    const form = document.getElementById('form-withdraw');
    if (modal) {
        modal.style.display = 'none';
        document.body.style.overflow = '';
        if (form) form.reset();
    }
}

// Handle Form Submit
async function handleWithdrawSubmit(event) {
    event.preventDefault();
    
    const submitBtn = document.getElementById('submit-withdraw-btn');
    const originalBtnHtml = submitBtn.innerHTML;
    
    // Disable and show loading
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memproses...';
    
    const formData = new FormData(event.target);
    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    try {
        const response = await fetch('/withdrawal', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': token,
                'Accept': 'application/json',
            },
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            alert('✅ ' + result.message);
            closeWithdrawModal();
            window.location.reload();
        } else {
            alert('❌ ' + result.message);
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalBtnHtml;
        }
    } catch (error) {
        console.error('Error:', error);
        alert('❌ Terjadi kesalahan saat memproses penarikan. Silakan coba lagi.');
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalBtnHtml;
    }
}

// Close on ESC key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeWithdrawModal();
    }
});
</script>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const btnTopup = document.getElementById('btn-topup');
    const topupFormWrap = document.getElementById('topup-form');
    const cancelTopup = document.getElementById('cancel-topup');
    const formTopup = document.getElementById('form-topup');

    // Top Up Button
    if (btnTopup) {
        btnTopup.addEventListener('click', () => {
            topupFormWrap.style.display = topupFormWrap.style.display === 'none' ? 'block' : 'none';
        });
    }

    if (cancelTopup) {
        cancelTopup.addEventListener('click', () => topupFormWrap.style.display = 'none');
    }

    // Top Up Form Submit
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

                if (data.snap_token) {
                    alert('Transaksi dibuat. Token Snap tersedia.');
                } else {
                    alert(data.message || 'Top-up dibuat.');
                }
            } catch (err) {
                console.error(err);
                alert('Terjadi kesalahan saat membuat top-up.');
            }
        });
    }

    // Input validation - only numbers
    const accountNumberInput = document.querySelector('input[name="account_number"]');
    const amountInput = document.querySelector('input[name="amount"]');
    
    if (accountNumberInput) {
        accountNumberInput.addEventListener('input', function(e) {
            this.value = this.value.replace(/\D/g, '');
        });
    }
    
    if (amountInput) {
        amountInput.addEventListener('input', function(e) {
            this.value = this.value.replace(/\D/g, '');
        });
    }
});
</script>
@endpush

@endsection