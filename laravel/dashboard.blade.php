@extends('layouts.dashboard')

@section('title', 'Dashboard | Payou.id')

@php
    $user = Auth::user();
    $userSlug = $user->profile?->username ?? Str::slug($user->name);
@endphp

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800">Dashboard</h1>
        <p class="text-gray-600 mt-2">Selamat datang kembali, {{ Auth::user()->name }} 👋</p>
    </div>

    <!-- Action Button -->
    <div class="mb-6">
        <a href="{{ route('links.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Buat Link Baru
        </a>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total Klik -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Total Klik</p>
                    <p class="text-2xl font-bold text-gray-800">{{ number_format($totalClicks) }}</p>
                    <p class="text-xs text-gray-500 mt-1">Semua waktu</p>
                </div>
                <div class="p-3 bg-blue-100 rounded-full">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Total Link -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Total Link</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $totalLinks }}</p>
                    <p class="text-xs text-gray-500 mt-1">{{ $activeLinks }} aktif</p>
                </div>
                <div class="p-3 bg-green-100 rounded-full">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Saldo Tersedia -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Saldo Tersedia</p>
                    <p class="text-2xl font-bold text-gray-800">{{ 'Rp ' . number_format($balance ?? 0, 0, ',', '.') }}</p>
                    <p class="text-xs text-gray-500 mt-1">Saldo aktif</p>
                </div>
                <div class="p-3 bg-purple-100 rounded-full">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Konversi -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Konversi</p>
                    <p class="text-2xl font-bold text-gray-800">0%</p>
                    <p class="text-xs text-gray-500 mt-1">0 transaksi</p>
                </div>
                <div class="p-3 bg-yellow-100 rounded-full">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <!-- Link Terbaru -->
        <div class="lg:col-span-2 bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-bold text-gray-800">📊 Link Terbaru</h2>
                <a href="{{ route('links.index') }}" class="text-blue-600 hover:text-blue-700 text-sm font-medium">Lihat Semua →</a>
            </div>
            
            <div class="space-y-4">
                @forelse($links as $link)
                    <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition">
                        <h3 class="font-semibold text-gray-800 mb-1">{{ $link->title }}</h3>
                        <p class="text-sm text-gray-500 mb-2">{{ $link->url }}</p>
                        <div class="flex items-center justify-between">
                            <a href="{{ url('payou.id/' . $userSlug . '/' . $link->slug) }}" 
                               class="text-blue-600 hover:underline text-sm" 
                               target="_blank">
                                payou.id/{{ $userSlug }}/{{ $link->slug }}
                            </a>
                            <span class="text-sm text-gray-600">
                                <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                                {{ $link->clicks_count }} klik
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8 text-gray-500">
                        <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                        </svg>
                        <p>Belum ada link.</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Aksi Cepat -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4">⚡ Aksi Cepat</h2>
            
            <div class="space-y-3">
                <button class="w-full flex items-center p-3 bg-gray-50 hover:bg-gray-100 rounded-lg transition">
                    <svg class="w-5 h-5 mr-3 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path>
                    </svg>
                    Buat QR Code
                </button>
                
                <button class="w-full flex items-center p-3 bg-gray-50 hover:bg-gray-100 rounded-lg transition">
                    <svg class="w-5 h-5 mr-3 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"></path>
                    </svg>
                    Ubah Tema
                </button>
                
                <button class="w-full flex items-center p-3 bg-gray-50 hover:bg-gray-100 rounded-lg transition">
                    <svg class="w-5 h-5 mr-3 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                    </svg>
                    Export Data
                </button>
                
                <button class="w-full flex items-center p-3 bg-gray-50 hover:bg-gray-100 rounded-lg transition">
                    <svg class="w-5 h-5 mr-3 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"></path>
                    </svg>
                    Bagikan Halaman
                </button>
            </div>
        </div>
    </div>

    <!-- Saldo & Transaksi Section -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <h2 class="text-xl font-bold text-gray-800 mb-4">💰 Saldo & Transaksi</h2>
        
        <div class="flex items-center justify-between mb-4">
            <div>
                <p class="text-sm text-gray-600">Total Saldo</p>
                <p class="text-3xl font-bold text-gray-800">{{ 'Rp ' . number_format($balance ?? 0, 0, ',', '.') }}</p>
            </div>
            <div class="flex gap-3">
                <button onclick="openTopUpModal()" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Top Up
                </button>
                <button onclick="openWithdrawModal()" class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Tarik
                </button>
            </div>
        </div>
    </div>

    <!-- Performance Chart -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-xl font-bold text-gray-800 mb-4">📈 Performa 7 Hari Terakhir</h2>
        
        <div class="flex items-end justify-between h-64 gap-2">
            @foreach ($data as $index => $value)
                @php
                    $height = $maxClick > 0 ? ($value / $maxClick) * 100 : 0;
                @endphp
                <div class="flex-1 flex flex-col items-center">
                    <div class="w-full bg-blue-500 rounded-t hover:bg-blue-600 transition cursor-pointer relative group" 
                         style="height: {{ $height }}%;" 
                         data-day="{{ $labels[$index] }}" 
                         data-value="{{ $value }}">
                        <div class="absolute bottom-full mb-2 left-1/2 transform -translate-x-1/2 bg-gray-800 text-white px-2 py-1 rounded text-xs opacity-0 group-hover:opacity-100 transition whitespace-nowrap">
                            {{ $value }} klik
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        
        <div class="flex justify-between mt-4 text-xs text-gray-600">
            @foreach ($labels as $label)
                <div class="flex-1 text-center">{{ $label }}</div>
            @endforeach
        </div>
    </div>
</div>

<!-- Modal Top Up (Existing) -->
<div id="topUpModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 w-full max-w-md">
        <h3 class="text-xl font-bold mb-4">Top Up Saldo</h3>
        <form action="{{ route('topup.store') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Jumlah Top Up (Rp)</label>
                <input type="number" name="amount" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" min="20000" step="1000" required>
                <p class="text-xs text-gray-500 mt-1">Minimal Rp 20.000</p>
            </div>
            <div class="flex gap-3">
                <button type="submit" class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    Bayar
                </button>
                <button type="button" onclick="closeTopUpModal()" class="flex-1 px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 transition">
                    Batal
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Penarikan (NEW) -->
<div id="withdrawModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 w-full max-w-md max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-xl font-bold">Tarik Saldo</h3>
            <button onclick="closeWithdrawModal()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <div class="mb-4 p-4 bg-blue-50 border border-blue-200 rounded-lg">
            <p class="text-sm text-blue-800">
                <strong>Saldo Tersedia:</strong> {{ 'Rp ' . number_format($balance ?? 0, 0, ',', '.') }}
            </p>
        </div>

        <form id="withdrawForm" onsubmit="submitWithdrawal(event)">
            @csrf
            
            <!-- Jumlah Penarikan -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Jumlah Penarikan (Rp) <span class="text-red-500">*</span></label>
                <input type="number" 
                       name="amount" 
                       id="withdrawAmount"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent" 
                       min="50000" 
                       max="{{ $balance ?? 0 }}"
                       step="1000" 
                       required>
                <p class="text-xs text-gray-500 mt-1">Minimal Rp 50.000 | Maksimal Rp 10.000.000</p>
            </div>

            <!-- Nama Bank -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Nama Bank <span class="text-red-500">*</span></label>
                <select name="bank_name" 
                        id="bankName"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent" 
                        required>
                    <option value="">Pilih Bank</option>
                    <option value="BCA">Bank Central Asia (BCA)</option>
                    <option value="BNI">Bank Negara Indonesia (BNI)</option>
                    <option value="BRI">Bank Rakyat Indonesia (BRI)</option>
                    <option value="MANDIRI">Bank Mandiri</option>
                    <option value="CIMB">CIMB Niaga</option>
                    <option value="PERMATA">Bank Permata</option>
                    <option value="BNI SYARIAH">BNI Syariah</option>
                    <option value="BSI">Bank Syariah Indonesia (BSI)</option>
                    <option value="DANAMON">Bank Danamon</option>
                    <option value="MEGA">Bank Mega</option>
                    <option value="PANIN">Bank Panin</option>
                    <option value="MUAMALAT">Bank Muamalat</option>
                    <option value="OCBC">OCBC NISP</option>
                    <option value="MAYBANK">Maybank Indonesia</option>
                    <option value="BTPN">Bank BTPN</option>
                    <option value="JENIUS">Jenius (BTPN)</option>
                    <option value="SINARMAS">Bank Sinarmas</option>
                </select>
            </div>

            <!-- Nomor Rekening -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Nomor Rekening <span class="text-red-500">*</span></label>
                <input type="text" 
                       name="account_number" 
                       id="accountNumber"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent" 
                       pattern="[0-9]+"
                       maxlength="20"
                       required>
                <p class="text-xs text-gray-500 mt-1">Masukkan nomor rekening tanpa spasi</p>
            </div>

            <!-- Nama Pemilik Rekening -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Nama Pemilik Rekening <span class="text-red-500">*</span></label>
                <input type="text" 
                       name="account_name" 
                       id="accountName"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent" 
                       required>
                <p class="text-xs text-gray-500 mt-1">Sesuai dengan nama di rekening bank</p>
            </div>

            <!-- Catatan (Optional) -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Catatan (Opsional)</label>
                <textarea name="notes" 
                          id="notes"
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent" 
                          rows="3"
                          maxlength="500"></textarea>
            </div>

            <!-- Info Penarikan -->
            <div class="mb-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                <p class="text-xs text-yellow-800">
                    <strong>⚠️ Informasi Penting:</strong><br>
                    • Penarikan akan langsung diproses ke rekening Anda<br>
                    • Dana akan masuk dalam 1-3 hari kerja<br>
                    • Pastikan data rekening sudah benar<br>
                    • Saldo akan dikurangi setelah penarikan berhasil diproses<br>
                    • Anda dapat membatalkan penarikan jika masih dalam proses
                </p>
            </div>

            <!-- Submit Buttons -->
            <div class="flex gap-3">
                <button type="submit" 
                        id="submitWithdrawBtn"
                        class="flex-1 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition flex items-center justify-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Ajukan Penarikan
                </button>
                <button type="button" 
                        onclick="closeWithdrawModal()" 
                        class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 transition">
                    Batal
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
// Top Up Modal Functions
function openTopUpModal() {
    document.getElementById('topUpModal').classList.remove('hidden');
    document.getElementById('topUpModal').classList.add('flex');
}

function closeTopUpModal() {
    document.getElementById('topUpModal').classList.add('hidden');
    document.getElementById('topUpModal').classList.remove('flex');
}

// Withdraw Modal Functions
function openWithdrawModal() {
    document.getElementById('withdrawModal').classList.remove('hidden');
    document.getElementById('withdrawModal').classList.add('flex');
}

function closeWithdrawModal() {
    document.getElementById('withdrawModal').classList.add('hidden');
    document.getElementById('withdrawModal').classList.remove('flex');
    document.getElementById('withdrawForm').reset();
}

// Submit Withdrawal
async function submitWithdrawal(event) {
    event.preventDefault();
    
    const submitBtn = document.getElementById('submitWithdrawBtn');
    const originalBtnText = submitBtn.innerHTML;
    
    // Disable button and show loading
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<svg class="animate-spin h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Memproses...';
    
    const formData = new FormData(event.target);
    
    try {
        const response = await fetch('{{ route("withdrawal.store") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                'Accept': 'application/json',
            },
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            // Show success message
            alert('✅ ' + result.message);
            
            // Close modal and reload page
            closeWithdrawModal();
            window.location.reload();
        } else {
            // Show error message
            alert('❌ ' + result.message);
            
            // Re-enable button
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalBtnText;
        }
    } catch (error) {
        console.error('Error:', error);
        alert('❌ Terjadi kesalahan saat memproses penarikan. Silakan coba lagi.');
        
        // Re-enable button
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalBtnText;
    }
}

// Format number input
document.getElementById('withdrawAmount')?.addEventListener('input', function(e) {
    let value = e.target.value.replace(/\D/g, '');
    e.target.value = value;
});

document.getElementById('accountNumber')?.addEventListener('input', function(e) {
    let value = e.target.value.replace(/\D/g, '');
    e.target.value = value;
});

// Close modal when clicking outside
window.onclick = function(event) {
    const topUpModal = document.getElementById('topUpModal');
    const withdrawModal = document.getElementById('withdrawModal');
    
    if (event.target === topUpModal) {
        closeTopUpModal();
    }
    if (event.target === withdrawModal) {
        closeWithdrawModal();
    }
}
</script>
@endpush

@endsection
