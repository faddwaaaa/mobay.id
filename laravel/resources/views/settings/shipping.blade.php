@extends('layouts.app')

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
                <p class="text-sm text-gray-500 mt-0.5">Atur kelurahan asal pengiriman produk fisik Anda</p>
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
                        <h2 class="text-base font-semibold text-gray-800">Kelurahan Asal Pengiriman</h2>
                        <p class="text-xs text-gray-500 mt-0.5">Digunakan untuk menghitung ongkos kirim ke pembeli via api.co.id (gratis)</p>
                    </div>
                </div>
            </div>

            <div class="p-5">
                @if(!auth()->user()->origin_village_code)
                <div class="mb-4 p-3 bg-amber-50 border border-amber-200 rounded-lg flex items-start gap-2 text-sm text-amber-700">
                    <svg class="w-4 h-4 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <span>Kelurahan asal belum diatur. Pembeli tidak akan bisa melihat ongkir.</span>
                </div>
                @endif

                <form method="POST" action="{{ route('settings.shipping.save') }}">
                    @csrf
                    <div class="mb-5">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Kelurahan / Kecamatan Asal <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="text" id="originSearch"
                                   placeholder="Contoh: Purwokerto Utara, Kembangan..."
                                   class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl text-sm focus:outline-none focus:border-blue-500 transition-colors"
                                   value="{{ auth()->user()->origin_city_name ?? '' }}"
                                   autocomplete="off">
                            <div id="originDropdown"
                                 class="absolute top-full left-0 right-0 mt-1 bg-white border border-gray-200 rounded-xl shadow-lg z-50 max-h-60 overflow-y-auto hidden"></div>
                        </div>
                        <p class="text-xs text-gray-500 mt-1.5">Ketik nama kelurahan atau kecamatan tempat Anda mengirim barang</p>

                        <input type="hidden" name="origin_village_code" id="originVillageCode"
                               value="{{ auth()->user()->origin_village_code ?? '' }}">
                        <input type="hidden" name="origin_city_name" id="originCityName"
                               value="{{ auth()->user()->origin_city_name ?? '' }}">

                        @if(auth()->user()->origin_city_name)
                        <div class="mt-2 inline-flex items-center gap-2 px-3 py-1.5 bg-blue-50 border border-blue-200 rounded-lg text-xs text-blue-700">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                            </svg>
                            {{ auth()->user()->origin_city_name }}
                            @if(auth()->user()->origin_village_code)
                                <span class="text-blue-400">({{ auth()->user()->origin_village_code }})</span>
                            @endif
                        </div>
                        @endif
                    </div>

                    <div class="p-4 bg-gray-50 rounded-xl border border-gray-100 mb-5 text-xs text-gray-600 space-y-1.5">
                        <div class="flex items-start gap-2"><span>✅</span><span><strong>Gratis selamanya</strong> — menggunakan api.co.id, tidak ada biaya subscribe.</span></div>
                        <div class="flex items-start gap-2"><span>📦</span><span>14+ ekspedisi: JNE, J&T, SiCepat, SAP, Anteraja, Lion, ID Express, Ninja, dll.</span></div>
                        <div class="flex items-start gap-2"><span>⚖️</span><span>Pastikan berat produk fisik diisi dalam gram di halaman produk.</span></div>
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

        <div class="mt-4 bg-white rounded-2xl border border-gray-200 shadow-sm p-5">
            <h3 class="text-sm font-semibold text-gray-800 mb-1">🧪 Test Koneksi api.co.id</h3>
            <p class="text-xs text-gray-500 mb-3">Pastikan API key dan endpoint ongkir bekerja</p>
            <div id="testResult" class="text-sm text-gray-500 italic mb-3">Belum ditest</div>
            <button type="button" onclick="testApi()"
                    class="px-4 py-2 text-sm border border-gray-200 rounded-lg hover:bg-gray-50 text-gray-600 transition-colors">
                🔌 Test API Sekarang
            </button>
        </div>
    </div>
</div>

<script>
const originSearch   = document.getElementById('originSearch');
const originDropdown = document.getElementById('originDropdown');
const originVillage  = document.getElementById('originVillageCode');
const originCity     = document.getElementById('originCityName');
let searchTimer = null;

originSearch.addEventListener('input', function(){
    const q = this.value.trim(); clearTimeout(searchTimer);
    if(q.length < 2){ originDropdown.classList.add('hidden'); return; }
    originDropdown.innerHTML = '<div class="px-4 py-2.5 text-xs text-gray-400">Mencari...</div>';
    originDropdown.classList.remove('hidden');
    searchTimer = setTimeout(() => doSearch(q), 350);
});

async function doSearch(q){
    try{
        const res  = await fetch('/api/ongkir/cities?q=' + encodeURIComponent(q));
        const data = await res.json();
        originDropdown.innerHTML = '';
        if(!data.length){ originDropdown.innerHTML='<div class="px-4 py-2.5 text-xs text-gray-400">Kelurahan tidak ditemukan</div>'; return; }
        data.forEach(v => {
            const el = document.createElement('div');
            el.className = 'px-4 py-2.5 cursor-pointer hover:bg-blue-50 border-b border-gray-100 last:border-0';
            el.innerHTML = `<div class="text-sm font-semibold text-gray-800">${v.village_name}</div><div class="text-xs text-gray-400">${v.district_name}, ${v.city_name}, ${v.province}</div>`;
            el.addEventListener('click', () => {
                const label = `${v.village_name}, ${v.district_name}, ${v.city_name}`;
                originSearch.value   = label;
                originVillage.value  = v.village_code;
                originCity.value     = label;
                originDropdown.classList.add('hidden');
            });
            originDropdown.appendChild(el);
        });
    } catch(e){ originDropdown.innerHTML='<div class="px-4 py-2.5 text-xs text-red-500">Gagal memuat data</div>'; }
}

document.addEventListener('click', e => {
    if(!originSearch.contains(e.target)) originDropdown.classList.add('hidden');
});

document.querySelector('form').addEventListener('submit', function(e){
    if(!originVillage.value){ e.preventDefault(); alert('Pilih kelurahan dari daftar dropdown terlebih dahulu'); }
});

async function testApi(){
    const el = document.getElementById('testResult');
    el.innerHTML = '<span style="color:#6b7280">Mengirim request test ke api.co.id...</span>';
    try{
        const res  = await fetch('/api/ongkir/cost', {
            method:'POST',
            headers:{'Content-Type':'application/json','X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content},
            body: JSON.stringify({
                origin_village_code:      '3302230003',  // contoh: Purwokerto
                destination_village_code: '3174040006',  // contoh: Jakarta
                weight: 1000,
            }),
        });
        const data = await res.json();
        if(data.success && data.data && data.data.length){
            el.innerHTML = `<span style="color:#16a34a">✅ Berhasil! ${data.data.length} layanan kurir tersedia. api.co.id bekerja normal.</span>`;
        } else {
            el.innerHTML = `<span style="color:#dc2626">❌ ${data.error || 'Tidak ada data. Periksa RAJAONGKIR_API_KEY di .env'}</span>`;
        }
    } catch(e){ el.innerHTML='<span style="color:#dc2626">❌ Gagal terhubung. Coba lagi.</span>'; }
}
</script>
@endsection