<div class="min-h-screen bg-gradient-to-br from-gray-50 to-blue-50 p-4 md:p-6">
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
                        <h1 class="text-xl font-bold text-gray-800">Tambah Produk Digital</h1>
                        <span style="font-size: 12px; background: #eff6ff; color: #2563eb; padding: 3px 10px; border-radius: 999px; font-weight: 600;">Digital</span>
                    </div>
                    <p class="text-sm text-gray-500 mt-0.5">File, template, e-book, dan produk digital lainnya</p>
                </div>
            </div>
        </div>

        {{-- Form --}}
        <form method="POST" action="{{ route('products.store') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf
            <input type="hidden" name="product_type" value="digital">

            @if(request('redirect'))
                <input type="hidden" name="redirect" value="{{ request('redirect') }}">
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                {{-- ===== KIRI ===== --}}
                <div class="lg:col-span-2 space-y-6">

                    {{-- Gambar Tampilan Produk --}}
                    <div class="card-section p-5">
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <h3 class="text-base font-semibold text-gray-800">Gambar Tampilan Produk</h3>
                                <p class="text-sm text-gray-500">Upload minimal 1 gambar produk</p>
                            </div>
                            <div class="p-2.5 bg-blue-50 rounded-lg">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                        </div>
                        <div class="border-2 border-dashed border-gray-300 rounded-xl p-4 text-center hover:border-blue-400 transition-colors cursor-pointer mb-4">
                            <input type="file" id="imageUpload" name="images[]" multiple accept="image/*" class="hidden">
                            <label for="imageUpload" class="cursor-pointer block">
                                <div class="mx-auto w-12 h-12 bg-blue-50 rounded-full flex items-center justify-center mb-3">
                                    <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                    </svg>
                                </div>
                                <p class="text-gray-700 font-medium text-sm" id="imageUploadText">Klik untuk upload gambar tampilan</p>
                                <p class="text-xs text-gray-400 mt-1">PNG, JPG, JPEG (Maks 10MB per gambar)</p>
                            </label>
                        </div>
                        <div id="imageStatus" class="hidden mb-3"></div>
                        <div id="imagePreviewGrid" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3"></div>
                    </div>

                    {{-- Info Produk --}}
                    <div class="card-section p-5 space-y-5">
                        <div>
                            <label class="form-label">Judul Produk</label>
                            <input type="text" name="title" class="form-input" placeholder="Contoh: Template Website Premium" required>
                        </div>
                        <div>
                            <label class="form-label">Deskripsi Produk</label>
                            <textarea name="description" class="form-textarea" rows="4"
                                placeholder="Jelaskan detail produk, fitur, dan manfaat yang didapatkan..." required></textarea>
                            <p class="text-xs text-gray-500 mt-2">Minimal 100 karakter untuk deskripsi yang optimal</p>
                        </div>
                    </div>

                    {{-- File Produk untuk Pembeli --}}
                    <div class="card-section p-5">
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <h3 class="text-base font-semibold text-gray-800">File untuk Pembeli <span class="text-red-500">*</span></h3>
                                <p class="text-sm text-gray-500">File yang diterima pembeli setelah transaksi</p>
                            </div>
                            <div class="p-2.5 bg-green-50 rounded-lg">
                                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                        </div>

                        <div class="border-2 border-dashed border-gray-300 rounded-xl p-5 text-center hover:border-green-400 transition-colors cursor-pointer mb-4">
                            <input type="file" name="files[]" multiple class="hidden" id="fileUpload"
                                   accept="image/*,.zip,.rar,.pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt,.csv,.mp3,.mp4,.mov,.ai,.psd,.fig,.sketch">
                            <label for="fileUpload" class="cursor-pointer block">
                                <div class="mx-auto w-12 h-12 bg-green-50 rounded-full flex items-center justify-center mb-3">
                                    <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                    </svg>
                                </div>
                                <p class="text-gray-700 font-medium text-sm" id="fileUploadText">Upload file untuk pembeli</p>
                                <p class="text-xs text-gray-400 mt-1">ZIP, RAR, PDF, DOC, gambar, dan format lainnya</p>
                                <p class="text-xs text-blue-400 mt-2">※ Bisa upload multiple file sekaligus</p>
                            </label>
                        </div>
                        <div id="fileStatus" class="hidden mb-3"></div>
                        <div id="fileList" class="space-y-2"></div>
                    </div>

                </div>

                {{-- ===== KANAN ===== --}}
                <div class="space-y-6">

                    <div class="card-section p-5 space-y-5">
                        <h3 class="text-base font-semibold text-gray-800">Harga & Pengaturan</h3>

                        <div>
                            <label class="form-label">Harga Normal (Rp)</label>
                            <input type="text" name="price" id="priceInput" class="form-input" placeholder="0" required>
                            <p class="text-xs text-gray-500 mt-1">Harga akan otomatis diformat: Rp 100.000</p>
                        </div>

                        <div>
                            <label class="form-label">Harga Setelah Diskon <span class="text-sm font-normal text-gray-500">(opsional)</span></label>
                            <input type="text" name="discount" id="discountInput" class="form-input" placeholder="0">
                            <p class="text-xs text-blue-600 mt-1">
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

                        <div class="pt-4 border-t border-gray-100">
                            <div class="flex items-center justify-between mb-3">
                                <div>
                                    <label class="form-label mb-0">Batas Pembelian</label>
                                    <p class="text-xs text-gray-500">Batasi pembelian per pengguna</p>
                                </div>
                                <div class="toggle-container">
                                    <input type="checkbox" name="limit_toggle" id="limitCheck" class="toggle-checkbox">
                                    <label for="limitCheck" class="toggle-label"></label>
                                </div>
                            </div>
                            <input type="number" name="purchase_limit" id="limitInput"
                                class="form-input hidden" placeholder="Maksimal beli per user" min="1">
                        </div>
                    </div>

                    <button type="submit" id="submitBtn"
                        class="w-full bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white py-3 px-4 rounded-lg font-semibold text-base shadow-md hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-200 flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                        <span id="submitBtnText">Tambah Produk Digital</span>
                    </button>

                    <div class="p-3 bg-blue-50 rounded-lg border border-blue-100">
                        <div class="flex items-start gap-2">
                            <svg class="w-4 h-4 text-blue-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <div class="text-xs text-gray-700">
                                <p class="font-medium text-blue-700">Tips Produk Digital</p>
                                <p class="mt-0.5">Pastikan file sudah lengkap. Pembeli akan langsung mengunduh file setelah pembayaran berhasil.</p>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </form>
    </div>
</div>

<style>
.card-section {
    background: white; border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.04);
    border: 1px solid rgba(229,231,235,0.6);
}
.form-input {
    width: 100%; padding: 10px 12px;
    border: 1.5px solid #e5e7eb; border-radius: 8px;
    font-size: 14px; transition: all 0.2s; background: white;
}
.form-input:focus { outline: none; border-color: #3b82f6; box-shadow: 0 0 0 3px rgba(59,130,246,0.1); }
.form-textarea {
    width: 100%; padding: 10px 12px;
    border: 1.5px solid #e5e7eb; border-radius: 8px;
    font-size: 14px; transition: all 0.2s; resize: vertical; min-height: 100px; background: white;
}
.form-textarea:focus { outline: none; border-color: #3b82f6; box-shadow: 0 0 0 3px rgba(59,130,246,0.1); }
.form-label { display: block; font-size: 13px; font-weight: 600; color: #374151; margin-bottom: 6px; }
.toggle-container { position: relative; }
.toggle-checkbox { display: none; }
.toggle-label {
    display: block; width: 44px; height: 24px;
    background: #e5e7eb; border-radius: 999px; position: relative; cursor: pointer; transition: background 0.2s;
}
.toggle-label::after {
    content: ''; position: absolute; top: 2px; left: 2px;
    width: 20px; height: 20px; background: white; border-radius: 50%;
    transition: transform 0.2s; box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}
.toggle-checkbox:checked + .toggle-label { background: #3b82f6; }
.toggle-checkbox:checked + .toggle-label::after { transform: translateX(20px); }
.preview-image { position: relative; border-radius: 6px; overflow: hidden; aspect-ratio: 1; background: #f5f5f5; }
.preview-image img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.3s; }
.preview-image:hover img { transform: scale(1.05); }
.remove-img-btn {
    position: absolute; top: 4px; right: 4px;
    width: 24px; height: 24px; background: rgba(239,68,68,0.9); border-radius: 50%;
    display: flex; align-items: center; justify-content: center; cursor: pointer;
    opacity: 0; transition: opacity 0.2s, transform 0.2s; transform: scale(0.8);
}
.preview-image:hover .remove-img-btn { opacity: 1; transform: scale(1); }
.img-ready-dot {
    position: absolute; bottom: 5px; right: 5px;
    width: 8px; height: 8px; border-radius: 50%;
    background: #2563eb; border: 1.5px solid white;
    box-shadow: 0 1px 3px rgba(0,0,0,0.2);
}
.file-item {
    display: flex; align-items: center; justify-content: space-between;
    padding: 10px 12px; background: #f8fafc; border-radius: 8px; border: 1px solid #e2e8f0;
}
.file-item:hover { background: #f1f5f9; }
.file-info { display: flex; align-items: center; gap: 8px; flex: 1; min-width: 0; }
.file-name { font-size: 13px; color: #334155; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; flex: 1; }
.file-size { font-size: 11px; color: #94a3b8; background: #f1f5f9; padding: 2px 6px; border-radius: 4px; flex-shrink: 0; }
.file-remove {
    width: 20px; height: 20px; background: #fef2f2; border-radius: 50%;
    display: flex; align-items: center; justify-content: center; cursor: pointer; margin-left: 8px; flex-shrink: 0;
}
.file-remove:hover { background: #fee2e2; }
@keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {

    // ===== TOGGLE =====
    const limitCheck = document.getElementById('limitCheck');
    const limitInput = document.getElementById('limitInput');
    limitCheck.addEventListener('change', function () {
        limitInput.classList.toggle('hidden', !this.checked);
        if (this.checked) limitInput.focus(); else limitInput.value = '';
    });

    // ===== FORMAT RUPIAH =====
    function formatRupiah(val) {
        return val.toString().replace(/[^,\d]/g, '').replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    }
    function cleanRupiah(val) { return parseInt(val.replace(/\./g, '')) || 0; }

    const priceInput      = document.getElementById('priceInput');
    const discountInput   = document.getElementById('discountInput');
    const discountPreview = document.getElementById('discountPreview');
    const discountAmount  = document.getElementById('discountAmount');
    const discountPct     = document.getElementById('discountPercentage');

    priceInput.addEventListener('input',    function () { this.value = formatRupiah(this.value); updateDiscountPreview(); });
    discountInput.addEventListener('input', function () { this.value = formatRupiah(this.value); updateDiscountPreview(); });

    function updateDiscountPreview() {
        const p = cleanRupiah(priceInput.value);
        const d = cleanRupiah(discountInput.value);
        if (p > 0 && d > 0 && d < p) {
            discountAmount.textContent = 'Rp ' + formatRupiah(String(p - d));
            discountPct.textContent    = '(' + Math.round(((p - d) / p) * 100) + '% OFF)';
            discountPreview.classList.remove('hidden');
        } else {
            discountPreview.classList.add('hidden');
        }
    }

    // ===== COMPRESSION CONFIG (internal) =====
    const COMPRESS_CONFIG = {
        thumbnail: { maxSizeKB: 150,  maxWidth: 1024, quality: 0.75, minQuality: 0.20, step: 0.08 },
        hd:        { maxSizeKB: 1024, maxWidth: 1920, quality: 0.88, minQuality: 0.65, step: 0.05 }
    };

    function compressImage(file, mode) {
        const cfg = COMPRESS_CONFIG[mode];
        return new Promise((resolve, reject) => {
            const reader = new FileReader();
            reader.onerror = () => reject(new Error('read error'));
            reader.onload = function(e) {
                const img = new Image();
                img.onerror = () => reject(new Error('load error'));
                img.onload = function() {
                    const canvas = document.createElement('canvas');
                    let { width, height } = img;
                    if (width > cfg.maxWidth) { height = Math.round(height * cfg.maxWidth / width); width = cfg.maxWidth; }
                    canvas.width = width; canvas.height = height;
                    canvas.getContext('2d').drawImage(img, 0, 0, width, height);
                    let q = cfg.quality;
                    function tryCompress() {
                        canvas.toBlob(blob => {
                            if (!blob) { reject(new Error('blob error')); return; }
                            if (blob.size / 1024 > cfg.maxSizeKB && q > cfg.minQuality) { q -= cfg.step; tryCompress(); return; }
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

    function isImg(file) { return file.type.match('image.*'); }

    // Subtle inline status — abu-abu kecil, tidak mencolok
    function showProcessing(el, msg) {
        el.classList.remove('hidden');
        el.innerHTML = `<div style="display:flex;align-items:center;gap:6px;color:#9ca3af;font-size:11px;">
            <svg style="width:12px;height:12px;animation:spin 1s linear infinite;flex-shrink:0;" fill="none" viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" style="opacity:.3"/>
                <path fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" style="opacity:.7"/>
            </svg>${msg}</div>`;
    }

    function showReady(el, count, dotColor) {
        el.classList.remove('hidden');
        el.innerHTML = `<div style="display:flex;align-items:center;gap:5px;color:#9ca3af;font-size:11px;">
            <span style="width:7px;height:7px;border-radius:50%;background:${dotColor};display:inline-block;flex-shrink:0;"></span>
            ${count} file siap diupload
        </div>`;
    }

    // ===== GAMBAR TAMPILAN — thumbnail =====
    const imageUpload      = document.getElementById('imageUpload');
    const imagePreviewGrid = document.getElementById('imagePreviewGrid');
    const imageUploadText  = document.getElementById('imageUploadText');
    const imageStatus      = document.getElementById('imageStatus');
    let uploadedImages     = [];

    imageUpload.addEventListener('change', async function () {
        const files = Array.from(this.files).filter(isImg);
        if (!files.length) return;

        showProcessing(imageStatus, 'Memproses gambar...');

        const results = await Promise.allSettled(files.map(f => compressImage(f, 'thumbnail')));
        results.forEach((r, i) => {
            if (r.status === 'fulfilled')
                uploadedImages.push({ id: Date.now() + i, file: r.value.file, previewUrl: r.value.previewUrl });
        });

        renderImagePreview();
        showReady(imageStatus, uploadedImages.length, '#2563eb');
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
                if (!uploadedImages.length) imageStatus.classList.add('hidden');
                else showReady(imageStatus, uploadedImages.length, '#2563eb');
            });
        });
        imageUploadText.textContent = uploadedImages.length
            ? `Tambah gambar (${uploadedImages.length} terpilih)`
            : 'Klik untuk upload gambar tampilan';
    }

    // ===== FILE UNTUK PEMBELI =====
    // Gambar  → compress HD (internal, tidak ditampilkan ke user)
    // Lainnya → utuh
    const fileUpload     = document.getElementById('fileUpload');
    const fileList       = document.getElementById('fileList');
    const fileUploadText = document.getElementById('fileUploadText');
    const fileStatus     = document.getElementById('fileStatus');
    let uploadedFiles    = [];

    fileUpload.addEventListener('change', async function () {
        const files      = Array.from(this.files);
        const imgFiles   = files.filter(isImg);
        const otherFiles = files.filter(f => !isImg(f));

        if (imgFiles.length) showProcessing(fileStatus, 'Memproses file...');

        const imgResults = imgFiles.length
            ? await Promise.allSettled(imgFiles.map(f => compressImage(f, 'hd')))
            : [];

        imgResults.forEach((r, i) => {
            if (r.status === 'fulfilled')
                uploadedFiles.push({ id: Date.now() + i, name: r.value.file.name, sizeLabel: formatSize(r.value.file.size), file: r.value.file });
        });

        otherFiles.forEach((f, i) => {
            uploadedFiles.push({ id: Date.now() + 1000 + i, name: f.name, sizeLabel: formatSize(f.size), file: f });
        });

        renderFileList();

        if (uploadedFiles.length) showReady(fileStatus, uploadedFiles.length, '#16a34a');
        else fileStatus.classList.add('hidden');

        this.value = '';
    });

    function renderFileList() {
        fileList.innerHTML = '';
        uploadedFiles.forEach((f, idx) => {
            const div = document.createElement('div');
            div.className = 'file-item';
            div.innerHTML = `
                <div class="file-info">
                    <svg style="width:16px;height:16px;color:#64748b;flex-shrink:0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <span class="file-name">${f.name}</span>
                    <span class="file-size">${f.sizeLabel}</span>
                </div>
                <div class="file-remove" data-idx="${idx}">
                    <svg style="width:10px;height:10px;color:#ef4444" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </div>`;
            fileList.appendChild(div);
        });
        fileList.querySelectorAll('.file-remove').forEach(btn => {
            btn.addEventListener('click', function () {
                uploadedFiles.splice(+this.dataset.idx, 1);
                renderFileList();
                if (!uploadedFiles.length) fileStatus.classList.add('hidden');
                else showReady(fileStatus, uploadedFiles.length, '#16a34a');
            });
        });
        fileUploadText.textContent = uploadedFiles.length
            ? `Tambah file (${uploadedFiles.length} terpilih)`
            : 'Upload file untuk pembeli';
    }

    function formatSize(bytes) {
        if (bytes < 1024)    return bytes + ' B';
        if (bytes < 1048576) return (bytes / 1024).toFixed(1) + ' KB';
        return (bytes / 1048576).toFixed(1) + ' MB';
    }

    // ===== FORM SUBMIT =====
    document.querySelector('form').addEventListener('submit', function (e) {
        const p = cleanRupiah(priceInput.value);
        const d = cleanRupiah(discountInput.value);
        if (p <= 0)          { e.preventDefault(); alert('Harga produk harus lebih dari 0'); return; }
        if (d > 0 && d >= p) { e.preventDefault(); alert('Harga diskon harus lebih rendah dari harga normal'); return; }
        if (!uploadedFiles.length) { e.preventDefault(); alert('Produk digital wajib memiliki minimal 1 file'); return; }

        priceInput.value    = p;
        discountInput.value = d || '';

        if (uploadedImages.length) {
            const dt = new DataTransfer();
            uploadedImages.forEach(img => dt.items.add(img.file));
            imageUpload.files = dt.files;
        }

        const dt = new DataTransfer();
        uploadedFiles.forEach(f => dt.items.add(f.file));
        fileUpload.files = dt.files;

        const btn = document.getElementById('submitBtn');
        btn.disabled = true; btn.style.opacity = '0.7';
        document.getElementById('submitBtnText').textContent = 'Menyimpan...';
    });
});
</script>