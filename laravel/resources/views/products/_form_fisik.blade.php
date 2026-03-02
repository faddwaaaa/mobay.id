<div class="min-h-screen bg-gradient-to-br from-gray-50 to-green-50 p-4 md:p-6">
    <div class="max-w-7xl mx-auto">

        {{-- Header --}}
        <div class="mb-6">
            <div class="flex items-center gap-3 mb-1">
                <a href="{{ route('products.manage') }}" class="p-2 rounded-lg hover:bg-white transition-colors">
                    <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                </a>
                <div>
                    <div class="flex items-center gap-2">
                        <h1 class="text-xl font-bold text-gray-800">Tambah Produk Fisik</h1>
                        <span style="font-size: 12px; background: #f0fdf4; color: #16a34a; padding: 3px 10px; border-radius: 999px; font-weight: 600;">Fisik</span>
                    </div>
                    <p class="text-sm text-gray-500 mt-0.5">Produk fisik, barang, dan produk lainnya</p>
                </div>
            </div>
        </div>

        {{-- 
            PERBAIKAN: Hapus action dari form HTML, submit sepenuhnya lewat fetch + FormData manual.
            Ini memastikan: (1) gambar dari array JS terkirim, (2) shipping_cost terkirim sebagai angka bersih.
        --}}
        <form id="createFisikForm" method="POST" action="{{ route('products.store') }}" class="space-y-6">
            @csrf
            <input type="hidden" name="product_type" value="fisik">

            @if(request('redirect'))
                <input type="hidden" name="redirect" value="{{ request('redirect') }}">
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                {{-- ===== KIRI ===== --}}
                <div class="lg:col-span-2 space-y-6">

                    {{-- Gambar Produk --}}
                    <div class="card-section-green p-5">
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <h3 class="text-base font-semibold text-gray-800">Gambar Produk</h3>
                                <p class="text-sm text-gray-500">Upload minimal 1 gambar produk</p>
                            </div>
                            <div class="p-2.5 bg-green-50 rounded-lg">
                                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                        </div>
                        <div id="imageUploadArea" class="border-2 border-dashed border-gray-300 rounded-xl p-4 text-center hover:border-green-400 transition-colors cursor-pointer mb-4">
                            {{-- Input ini HANYA trigger, file asli di uploadedImages[] --}}
                            <input type="file" id="imageUpload" multiple accept="image/*" class="hidden">
                            <label for="imageUpload" class="cursor-pointer block">
                                <div class="mx-auto w-12 h-12 bg-green-50 rounded-full flex items-center justify-center mb-3">
                                    <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                    </svg>
                                </div>
                                <p class="text-gray-700 font-medium text-sm" id="imageUploadText">Klik untuk upload gambar</p>
                                <p class="text-xs text-gray-400 mt-1">PNG, JPG, JPEG (Maks 10MB per gambar)</p>
                            </label>
                        </div>
                        <div id="compressionStatus" class="hidden mb-3"></div>
                        <div id="imagePreviewGrid" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3"></div>
                    </div>

                    {{-- Info Produk --}}
                    <div class="card-section-green p-5 space-y-5">
                        <div>
                            <label class="form-label">Judul Produk</label>
                            <input type="text" name="title" class="form-input-green" placeholder="Contoh: Keripik Tempe Original 250gr" required>
                        </div>
                        <div>
                            <label class="form-label">Deskripsi Produk</label>
                            <textarea name="description" class="form-textarea-green" rows="4"
                                placeholder="Jelaskan detail produk, bahan, ukuran, dan cara pemesanan..." required></textarea>
                            <p class="text-xs text-gray-500 mt-2">Minimal 100 karakter untuk deskripsi yang optimal</p>
                        </div>
                    </div>

                </div>

                {{-- ===== KANAN ===== --}}
                <div class="space-y-6">

                    <div class="card-section-green p-5 space-y-5">
                        <h3 class="text-base font-semibold text-gray-800">Harga & Pengaturan</h3>

                        <div>
                            <label class="form-label">Harga Normal (Rp)</label>
                            <input type="text" name="price" id="priceInput" class="form-input-green" placeholder="0" required>
                            <p class="text-xs text-gray-500 mt-1">Harga akan otomatis diformat: Rp 100.000</p>
                        </div>

                        <div>
                            <label class="form-label">Harga Setelah Diskon <span class="text-sm font-normal text-gray-500">(opsional)</span></label>
                            <input type="text" name="discount" id="discountInput" class="form-input-green" placeholder="0">
                            <p class="text-xs text-green-700 mt-1">
                                <svg class="w-3 h-3 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                </svg>
                                Masukkan harga setelah diskon, bukan persentase
                            </p>
                            <div id="discountPreview" class="hidden mt-2 p-2 bg-green-50 border border-green-200 rounded-md">
                                <p class="text-xs text-green-700">
                                    <span class="font-semibold">Hemat:</span>
                                    <span id="discountAmount"></span>
                                    <span id="discountPercentage" class="ml-1"></span>
                                </p>
                            </div>
                        </div>

                        {{-- ONGKOS KIRIM --}}
                        {{-- <div class="pt-4 border-t border-gray-100">
                            <div class="flex items-center justify-between mb-3">
                                <div>
                                    <label class="form-label mb-0">Ongkos Kirim</label>
                                    <p class="text-xs text-gray-500">Tetapkan ongkir khusus untuk produk ini</p>
                                </div>
                                <div class="toggle-container">
                                    <input type="checkbox" id="shippingCheck" class="toggle-checkbox">
                                    <label for="shippingCheck" class="toggle-label-green"></label>
                                </div>
                            </div>

                                FIX: Field ini TIDAK punya name="shipping_cost" di HTML.
                                Nilai akan diambil JS dan dikirim sebagai angka bersih via FormData.

                            <input type="text" id="shippingInput"
                                class="form-input-green hidden" placeholder="Contoh: 15.000">
                            <p class="text-xs text-gray-500 mt-1 hidden" id="shippingHint">
                                Ongkir ditambahkan ke total pembayaran pembeli.
                            </p>
                        </div> --}}

                        {{-- Kelola Stok --}}
                        <div class="pt-4 border-t border-gray-100">
                            <div class="flex items-center justify-between mb-3">
                                <div>
                                    <label class="form-label mb-0">Kelola Stok</label>
                                    <p class="text-xs text-gray-500">Aktifkan untuk mengatur jumlah stok</p>
                                </div>
                                <div class="toggle-container">
                                    <input type="checkbox" name="stock_toggle" id="stockCheck" class="toggle-checkbox">
                                    <label for="stockCheck" class="toggle-label-green"></label>
                                </div>
                            </div>
                            <input type="number" name="stock" id="stockInput"
                                class="form-input-green hidden" placeholder="Jumlah stok tersedia" min="1">
                        </div>

                        {{-- Batas Pembelian --}}
                        <div class="pt-4 border-t border-gray-100">
                            <div class="flex items-center justify-between mb-3">
                                <div>
                                    <label class="form-label mb-0">Batas Pembelian</label>
                                    <p class="text-xs text-gray-500">Batasi pembelian per pengguna</p>
                                </div>
                                <div class="toggle-container">
                                    <input type="checkbox" name="limit_toggle" id="limitCheck" class="toggle-checkbox">
                                    <label for="limitCheck" class="toggle-label-green"></label>
                                </div>
                            </div>
                            <input type="number" name="purchase_limit" id="limitInput"
                                class="form-input-green hidden" placeholder="Maksimal beli per user" min="1">
                        </div>
                    </div>

                    {{-- FIX: type="button" + onclick agar tidak trigger HTML form submit --}}
                    <button type="button" id="submitBtn" onclick="submitFisikForm()"
                        class="w-full text-white py-3 px-4 rounded-lg font-semibold text-base shadow-md hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-200 flex items-center justify-center gap-2"
                        style="background: linear-gradient(to right, #16a34a, #15803d);">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                        <span id="submitBtnText">Tambah Produk Fisik</span>
                    </button>

                    <div class="p-3 bg-green-50 rounded-lg border border-green-100">
                        <div class="flex items-start gap-2">
                            <svg class="w-4 h-4 text-green-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <div class="text-xs text-gray-700">
                                <p class="font-medium text-green-700">Tips Produk Fisik</p>
                                <p class="mt-0.5">Aktifkan kelola stok agar pembeli tidak bisa memesan melebihi stok yang tersedia.</p>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </form>
    </div>
</div>

<style>
.card-section-green { background: white; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.04); border: 1px solid rgba(229,231,235,0.6); }
.form-input-green { width: 100%; padding: 10px 12px; border: 1.5px solid #e5e7eb; border-radius: 8px; font-size: 14px; transition: all 0.2s; background: white; }
.form-input-green:focus { outline: none; border-color: #16a34a; box-shadow: 0 0 0 3px rgba(22,163,74,0.1); }
.form-textarea-green { width: 100%; padding: 10px 12px; border: 1.5px solid #e5e7eb; border-radius: 8px; font-size: 14px; transition: all 0.2s; resize: vertical; min-height: 100px; background: white; }
.form-textarea-green:focus { outline: none; border-color: #16a34a; box-shadow: 0 0 0 3px rgba(22,163,74,0.1); }
.form-label { display: block; font-size: 13px; font-weight: 600; color: #374151; margin-bottom: 6px; }
.toggle-container { position: relative; }
.toggle-checkbox { display: none; }
.toggle-label-green { display: block; width: 44px; height: 24px; background: #e5e7eb; border-radius: 999px; position: relative; cursor: pointer; transition: background 0.2s; }
.toggle-label-green::after { content: ''; position: absolute; top: 2px; left: 2px; width: 20px; height: 20px; background: white; border-radius: 50%; transition: transform 0.2s; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
.toggle-checkbox:checked + .toggle-label-green { background: #16a34a; }
.toggle-checkbox:checked + .toggle-label-green::after { transform: translateX(20px); }
.preview-image { position: relative; border-radius: 6px; overflow: hidden; aspect-ratio: 1; background: #f5f5f5; }
.preview-image img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.3s; }
.preview-image:hover img { transform: scale(1.05); }
.remove-img-btn { position: absolute; top: 4px; right: 4px; width: 24px; height: 24px; background: rgba(239,68,68,0.9); border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; opacity: 0; transition: opacity 0.2s, transform 0.2s; transform: scale(0.8); }
.preview-image:hover .remove-img-btn { opacity: 1; transform: scale(1); }
.img-ready-dot { position: absolute; bottom: 5px; right: 5px; width: 8px; height: 8px; border-radius: 50%; background: #16a34a; border: 1.5px solid white; box-shadow: 0 1px 3px rgba(0,0,0,0.2); }
@keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {

    // ===== TOGGLE STOK & LIMIT =====
    function setupToggle(checkId, inputId) {
        const cb  = document.getElementById(checkId);
        const inp = document.getElementById(inputId);
        if (!cb || !inp) return;
        cb.addEventListener('change', function () {
            inp.classList.toggle('hidden', !this.checked);
            if (this.checked) inp.focus(); else inp.value = '';
        });
    }
    setupToggle('stockCheck', 'stockInput');
    setupToggle('limitCheck', 'limitInput');

    // ===== TOGGLE ONGKIR =====
    const shippingCheck = document.getElementById('shippingCheck');
    const shippingInput = document.getElementById('shippingInput');
    const shippingHint  = document.getElementById('shippingHint');
    shippingCheck.addEventListener('change', function () {
        shippingInput.classList.toggle('hidden', !this.checked);
        shippingHint.classList.toggle('hidden', !this.checked);
        if (this.checked) shippingInput.focus();
        else shippingInput.value = '';
    });
    // Format ongkir saat diketik
    shippingInput.addEventListener('input', function () {
        this.value = fmtRupiah(this.value);
    });

    // ===== FORMAT RUPIAH =====
    function fmtRupiah(val) {
        return (val || '').toString().replace(/[^0-9]/g, '').replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    }
    function cleanRupiah(val) {
        const n = parseInt((val || '').toString().replace(/\./g, ''), 10);
        return isNaN(n) ? 0 : n;
    }

    const priceInput      = document.getElementById('priceInput');
    const discountInput   = document.getElementById('discountInput');
    const discountPreview = document.getElementById('discountPreview');
    const discountAmount  = document.getElementById('discountAmount');
    const discountPct     = document.getElementById('discountPercentage');

    priceInput.addEventListener('input',    function () { this.value = fmtRupiah(this.value); updateDiscountPreview(); });
    discountInput.addEventListener('input', function () { this.value = fmtRupiah(this.value); updateDiscountPreview(); });

    function updateDiscountPreview() {
        const p = cleanRupiah(priceInput.value);
        const d = cleanRupiah(discountInput.value);
        if (p > 0 && d > 0 && d < p) {
            discountAmount.textContent = 'Rp ' + fmtRupiah(String(p - d));
            discountPct.textContent    = '(' + Math.round(((p - d) / p) * 100) + '% OFF)';
            discountPreview.classList.remove('hidden');
        } else {
            discountPreview.classList.add('hidden');
        }
    }

    // ===== IMAGE COMPRESSION =====
    function compressImage(file, maxSizeKB, maxWidth, quality) {
        maxSizeKB = maxSizeKB || 150;
        maxWidth  = maxWidth  || 1024;
        quality   = quality   || 0.75;
        return new Promise((resolve, reject) => {
            const reader = new FileReader();
            reader.onerror = () => reject(new Error('read error'));
            reader.onload = function(e) {
                const img = new Image();
                img.onerror = () => reject(new Error('load error'));
                img.onload = function() {
                    const canvas = document.createElement('canvas');
                    let { width, height } = img;
                    if (width > maxWidth) { height = Math.round(height * maxWidth / width); width = maxWidth; }
                    canvas.width = width; canvas.height = height;
                    canvas.getContext('2d').drawImage(img, 0, 0, width, height);
                    let q = quality;
                    function tryCompress() {
                        canvas.toBlob(blob => {
                            if (!blob) { reject(new Error('blob error')); return; }
                            if (blob.size / 1024 > maxSizeKB && q > 0.2) { q -= 0.08; tryCompress(); return; }
                            resolve({
                                file: new File([blob], file.name.replace(/\.[^.]+$/, '.jpg'), { type: 'image/jpeg', lastModified: Date.now() }),
                                previewUrl: URL.createObjectURL(blob)
                            });
                        }, 'image/jpeg', q);
                    }
                    tryCompress();
                };
                img.src = e.target.result;
            };
            reader.readAsDataURL(file);
        });
    }

    // ===== IMAGE UPLOAD =====
    const imageUpload       = document.getElementById('imageUpload');
    const imagePreviewGrid  = document.getElementById('imagePreviewGrid');
    const imageUploadText   = document.getElementById('imageUploadText');
    const compressionStatus = document.getElementById('compressionStatus');
    let uploadedImages = [];

    imageUpload.addEventListener('change', async function () {
        const files = Array.from(this.files).filter(f => f.type.match('image.*'));
        if (!files.length) return;

        compressionStatus.classList.remove('hidden');
        compressionStatus.innerHTML = `<div style="display:flex;align-items:center;gap:6px;color:#9ca3af;font-size:11px;"><svg style="width:12px;height:12px;animation:spin 1s linear infinite;flex-shrink:0;" fill="none" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" style="opacity:.3"/><path fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" style="opacity:.7"/></svg>Memproses gambar...</div>`;

        const results = await Promise.allSettled(files.map(f => compressImage(f)));
        results.forEach((r, i) => {
            if (r.status === 'fulfilled') {
                uploadedImages.push({ id: Date.now() + i, file: r.value.file, previewUrl: r.value.previewUrl });
            }
        });

        renderImagePreview();
        compressionStatus.innerHTML = `<div style="display:flex;align-items:center;gap:5px;color:#9ca3af;font-size:11px;"><span style="width:7px;height:7px;border-radius:50%;background:#16a34a;display:inline-block;flex-shrink:0;"></span>${uploadedImages.length} gambar siap diupload</div>`;
        this.value = '';
    });

    function renderImagePreview() {
        imagePreviewGrid.innerHTML = '';
        uploadedImages.forEach((img, idx) => {
            const div = document.createElement('div');
            div.className = 'preview-image';
            div.innerHTML = `
                <img src="${img.previewUrl}" loading="lazy">
                <div class="img-ready-dot"></div>
                <div class="remove-img-btn" data-idx="${idx}">
                    <svg style="width:12px;height:12px;color:white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </div>`;
            imagePreviewGrid.appendChild(div);
        });
        imagePreviewGrid.querySelectorAll('.remove-img-btn').forEach(btn => {
            btn.addEventListener('click', function (e) {
                e.stopPropagation();
                uploadedImages.splice(+this.dataset.idx, 1);
                renderImagePreview();
                if (!uploadedImages.length) compressionStatus.classList.add('hidden');
                else compressionStatus.innerHTML = `<div style="display:flex;align-items:center;gap:5px;color:#9ca3af;font-size:11px;"><span style="width:7px;height:7px;border-radius:50%;background:#16a34a;display:inline-block;flex-shrink:0;"></span>${uploadedImages.length} gambar siap diupload</div>`;
            });
        });
        imageUploadText.textContent = uploadedImages.length ? `Tambah gambar (${uploadedImages.length} terpilih)` : 'Klik untuk upload gambar';
    }

    // ===== FORM SUBMIT via FETCH + FORMDATA MANUAL =====
    // FIX UTAMA: Submit via fetch agar:
    //   1. Gambar dari array JS (bukan dari input file) bisa dikirim
    //   2. shipping_cost dikirim sebagai ANGKA BERSIH (tanpa titik format rupiah)
    //   3. price & discount juga dikirim sebagai angka bersih
    window.submitFisikForm = async function() {
        const form = document.getElementById('createFisikForm');

        const p = cleanRupiah(priceInput.value);
        const d = cleanRupiah(discountInput.value);
        if (p <= 0)          { alert('Harga produk harus lebih dari 0'); return; }
        if (d > 0 && d >= p) { alert('Harga diskon harus lebih rendah dari harga normal'); return; }

        const btn = document.getElementById('submitBtn');
        btn.disabled = true; btn.style.opacity = '0.7';
        document.getElementById('submitBtnText').textContent = 'Menyimpan...';

        // Bangun FormData dari form (ambil semua field teks/hidden/number)
        const fd = new FormData(form);

        // Override harga dengan angka bersih
        fd.set('price',    p);
        fd.set('discount', d || '');

        // FIX: Tambah shipping_cost sebagai angka bersih (bukan format "15.000")
        // shippingInput tidak punya name di HTML, jadi kita set manual di sini
        if (shippingCheck.checked && shippingInput.value.trim()) {
            fd.set('shipping_cost', cleanRupiah(shippingInput.value));
        }
        // Jika tidak dicentang, tidak dikirim → server anggap NULL

        // Tambahkan gambar dari array JS (BUKAN dari input file)
        fd.delete('images[]');
        uploadedImages.forEach(img => {
            fd.append('images[]', img.file, img.file.name);
        });

        try {
            const res = await fetch(form.action, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') },
                body: fd,
            });

            if (res.redirected) {
                window.location.href = res.url;
                return;
            }
            if (res.ok) {
                window.location.href = res.url || '{{ route("products.manage") }}';
            } else {
                const text = await res.text();
                document.open(); document.write(text); document.close();
            }
        } catch (err) {
            console.error('Submit error:', err);
            alert('Terjadi kesalahan. Silakan coba lagi.');
            btn.disabled = false; btn.style.opacity = '1';
            document.getElementById('submitBtnText').textContent = 'Tambah Produk Fisik';
        }
    };
});
</script>