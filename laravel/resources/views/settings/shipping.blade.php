@extends('layouts.dashboard')

@section('title', 'Pengaturan Pengiriman | Payou.id')

@section('content')
<div class="min-h-screen bg-gray-50 p-4 md:p-6">
    <div class="max-w-2xl mx-auto">

        <div class="mb-6 flex items-center gap-3">
            <a href="{{ route('dashboard') }}" class="p-2 rounded-lg hover:bg-white transition-colors">
                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <div>
                <h1 class="text-xl font-bold text-gray-800">Pengaturan Pengiriman</h1>
                <p class="text-sm text-gray-500 mt-0.5">Atur kota asal pengiriman produk fisik Anda</p>
            </div>
        </div>

        @if(session('success'))
        <div class="mb-4 p-3 bg-green-50 border border-green-200 rounded-lg flex items-center gap-2 text-sm text-green-700">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            {{ session('success') }}
        </div>
        @endif

        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="p-5 border-b border-gray-100">
                <div class="flex items-center gap-3">
                    <div class="p-2.5 bg-blue-50 rounded-xl">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-base font-semibold text-gray-800">Kota Asal Pengiriman</h2>
                        <p class="text-xs text-gray-500 mt-0.5">Digunakan untuk menghitung ongkos kirim ke pembeli via Binderbyte API</p>
                    </div>
                </div>
            </div>

            <div class="p-5">
                @if(!auth()->user()->origin_city_name)
                <div class="mb-4 p-3 bg-amber-50 border border-amber-200 rounded-lg flex items-start gap-2 text-sm text-amber-700">
                    <svg class="w-4 h-4 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <span>Kota asal belum diatur. Produk fisik Anda tidak akan menampilkan ongkir kepada pembeli.</span>
                </div>
                @endif

                <form method="POST" action="{{ route('settings.shipping.save') }}">
                    @csrf
                    <div class="mb-5">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Kota / Kabupaten Asal <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="text" id="originCitySearch"
                                   placeholder="Ketik nama kota, misal: Purwokerto..."
                                   class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl text-sm focus:outline-none focus:border-blue-500 transition-colors"
                                   value="{{ auth()->user()->origin_city_name ?? '' }}"
                                   autocomplete="off">
                            <div id="originDropdown"
                                 class="absolute top-full left-0 right-0 mt-1 bg-white border border-gray-200 rounded-xl shadow-lg z-50 max-h-56 overflow-y-auto hidden"></div>
                        </div>
                        <input type="hidden" name="origin_city_name" id="originCityName"
                               value="{{ auth()->user()->origin_city_name ?? '' }}">
                        <input type="hidden" name="origin_city_id" id="originCityId"
                               value="{{ auth()->user()->origin_city_id ?? '' }}">

                        @if(auth()->user()->origin_city_name)
                        <div class="mt-2 inline-flex items-center gap-2 px-3 py-1.5 bg-blue-50 border border-blue-200 rounded-lg text-xs text-blue-700">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                            </svg>
                            {{ auth()->user()->origin_city_name }}
                        </div>
                        @endif
                    </div>

                    <div class="p-4 bg-gray-50 rounded-xl border border-gray-100 mb-5 text-xs text-gray-600 space-y-1.5">
                        <div class="flex items-start gap-2">
                            <span>ℹ️</span>
                            <span>Ongkir dihitung dari kota ini ke kota tujuan pembeli menggunakan <strong>Binderbyte API</strong> (gratis).</span>
                        </div>
                        <div class="flex items-start gap-2">
                            <span>📦</span>
                            <span>Pembeli bisa memilih kurir (JNE, SiCepat, J&T, Anteraja, dll) saat checkout.</span>
                        </div>
                        <div class="flex items-start gap-2">
                            <span>⚖️</span>
                            <span>Pastikan berat setiap produk fisik sudah diisi dengan benar (dalam gram).</span>
                        </div>
                    </div>

                    <button type="submit"
                            class="w-full py-3 px-4 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl transition-colors flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Simpan Pengaturan
                    </button>
                </form>
            </div>
        </div>

        {{-- TEST ONGKIR --}}
        <div class="mt-4 bg-white rounded-2xl border border-gray-200 shadow-sm p-5">
            <h3 class="text-sm font-semibold text-gray-800 mb-3">🧪 Test Koneksi Binderbyte</h3>
            <p class="text-xs text-gray-500 mb-3">Cek apakah API key Anda bekerja dengan baik</p>
            <div id="testResult" class="text-sm text-gray-500 italic mb-3">Belum ditest</div>
            <button type="button" onclick="testApi()"
                    class="px-4 py-2 text-sm border border-gray-200 rounded-lg hover:bg-gray-50 text-gray-600 transition-colors">
                🔌 Test API Sekarang
            </button>
        </div>

    </div>
</div>

<script>
const originSearch   = document.getElementById('originCitySearch');
const originDropdown = document.getElementById('originDropdown');
const originHidden   = document.getElementById('originCityName');
const originIdHidden = document.getElementById('originCityId');
const shippingForm   = document.querySelector('form[action="{{ route('settings.shipping.save') }}"]');
let searchTimer = null;

originSearch.addEventListener('input', function() {
    const q = this.value.trim();
    originIdHidden.value = '';
    originHidden.value = q;
    clearTimeout(searchTimer);
    if (q.length < 2) { originDropdown.classList.add('hidden'); return; }
    originDropdown.innerHTML = '<div class="p-3 text-xs text-gray-400">Mencari...</div>';
    originDropdown.classList.remove('hidden');
    searchTimer = setTimeout(() => doSearch(q), 300);
});

async function doSearch(q) {
    try {
        const res  = await fetch('/api/ongkir/cities?q=' + encodeURIComponent(q));
        const data = await res.json();
        originDropdown.innerHTML = '';
        if (!data.length) {
            originDropdown.innerHTML = `
                <div class="p-3 text-xs text-gray-500">
                    Kota tidak ditemukan. Jika data kota belum tersedia, jalankan:
                    <code class="text-[11px] bg-gray-100 px-1.5 py-0.5 rounded">php artisan rajaongkir:sync</code>
                </div>`;
            return;
        }
        data.forEach(city => {
            const el = document.createElement('div');
            el.className = 'px-4 py-2.5 text-sm text-gray-700 cursor-pointer hover:bg-blue-50 border-b border-gray-100 last:border-0';
            el.innerHTML = `<span class="font-medium">${city.city_name}</span> <span class="text-gray-400 text-xs">${city.province}</span>`;
            el.addEventListener('click', () => {
                originSearch.value  = city.city_name;
                originHidden.value  = city.city_name;
                originIdHidden.value = city.city_id;
                originDropdown.classList.add('hidden');
            });
            originDropdown.appendChild(el);
        });
    } catch(e) {
        originDropdown.innerHTML = '<div class="p-3 text-xs text-red-500">Gagal memuat data</div>';
    }
}

document.addEventListener('click', e => {
    if (!originSearch.contains(e.target) && !originDropdown.contains(e.target)) {
        originDropdown.classList.add('hidden');
    }
});

shippingForm.addEventListener('submit', function(e) {
    if (!originIdHidden.value) {
        e.preventDefault();
        alert('Pilih kota dari daftar autocomplete terlebih dahulu.');
        originSearch.focus();
    }
});

async function testApi() {
    const el = document.getElementById('testResult');
    el.innerHTML = '<span class="text-gray-500">Mengirim request ke Binderbyte...</span>';
    try {
        const res  = await fetch('/api/ongkir/cost', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify({
                origin_city: 'jakarta',
                destination_city: 'surabaya',
                weight: 1000,
            }),
        });
        const data = await res.json();
        if (data.success && data.data.length) {
            el.innerHTML = `<span style="color:#16a34a">✅ Berhasil! ${data.data.length} layanan kurir tersedia. API key bekerja dengan baik.</span>`;
        } else {
            el.innerHTML = `<span style="color:#dc2626">❌ API terhubung tapi tidak ada data. Periksa RAJAONGKIR_API_KEY di .env<br><small>${data.error || ''}</small></span>`;
        }
    } catch(e) {
        el.innerHTML = `<span style="color:#dc2626">❌ Gagal terhubung. Periksa koneksi dan API key.</span>`;
    }
}
</script>
@endsection
