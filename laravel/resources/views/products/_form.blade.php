<div class="min-h-screen bg-gradient-to-br from-gray-50 to-blue-50 p-4 md:p-6">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="mb-6">
            <div class="flex items-center gap-3 mb-1">
                <a href="{{ url()->previous() }}" class="p-2 rounded-lg hover:bg-white transition-colors">
                    <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                </a>
                <h1 class="text-xl font-bold">
                    {{ isset($product) ? 'Edit Produk' : 'Tambah Produk Baru' }}
                </h1>
                <br>
                <p class="text-sm text-gray-500">
                    {{-- {{ isset($product) ? 'Perbarui informasi produk Anda' : 'Lengkapi informasi produk Anda' }} --}}
                </p>
            </div>
        </div>

        <!-- Form -->
        <form method="POST"
              action="{{ isset($product) ? route('products.update', $product->id) : route('products.store') }}"
              enctype="multipart/form-data"
              class="space-y-6">
            @csrf
            @if(isset($product))
                @method('PUT')
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Left Column -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Product Type -->
                    <div class="card-gradient p-5">
                        <label class="form-label">Jenis Produk</label>
                        <select name="product_type" id="productType" class="form-input" required>
                            <option value="">Pilih Jenis Produk</option>
                            <option value="umkm" {{ old('product_type', $product->product_type ?? '') == 'umkm' ? 'selected' : '' }}>Produk UMKM (Fisik)</option>
                            <option value="digital" {{ old('product_type', $product->product_type ?? '') == 'digital' ? 'selected' : '' }}>Produk Digital</option>
                        </select>
                        <p class="text-xs text-gray-500 mt-2">Pilih jenis produk untuk menyesuaikan pengaturan penjualan</p>
                    </div>

                    <!-- Images Upload -->
                    <div class="card-gradient p-5">
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-800 mb-1">Gambar Produk</h3>
                                <p class="text-sm text-gray-500">Upload minimal 1 gambar produk</p>
                            </div>
                            <div class="p-2.5 bg-blue-50 rounded-lg">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                        </div>

                        <!-- Image Upload Area -->
                        <div id="imageUploadArea"
                             class="border-2 border-dashed border-gray-300 rounded-xl p-4 text-center hover:border-blue-400 transition-colors cursor-pointer mb-4">
                            <input type="file"
                                   id="imageUpload"
                                   name="images[]"
                                   multiple
                                   accept="image/*"
                                   class="hidden">
                            <label for="imageUpload" class="cursor-pointer block">
                                <div class="mx-auto w-12 h-12 bg-blue-50 rounded-full flex items-center justify-center mb-3">
                                    <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                    </svg>
                                </div>
                                <p class="text-gray-700 font-medium text-sm">Klik untuk upload gambar</p>
                                <p class="text-xs text-gray-500 mt-1">PNG, JPG, JPEG (Max 5MB per gambar)</p>
                            </label>
                        </div>

                        <!-- Image Preview Grid -->
                        <div id="imagePreviewContainer" class="mt-4">
                            <div id="imagePreviewGrid" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3"></div>
                        </div>
                    </div>

                    <!-- Product Info -->
                    <div class="card-gradient p-5 space-y-5">
                        <div>
                            <label class="form-label">Judul Produk</label>
                            <input type="text"
                                   name="title"
                                   class="form-input"
                                   value="{{ old('title', $product->title ?? '') }}"
                                   placeholder="Contoh: Template Website Premium"
                                   required>
                        </div>
                        <div>
                            <label class="form-label">Deskripsi Produk</label>
                            <textarea name="description"
                                      class="form-textarea"
                                      rows="4"
                                      placeholder="Jelaskan detail produk, fitur, dan manfaat yang didapatkan..."
                                      required>{{ old('description', $product->description ?? '') }}</textarea>
                            <p class="text-xs text-gray-500 mt-2">Minimal 100 karakter untuk deskripsi yang optimal</p>
                        </div>
                    </div>

                    <!-- Files Upload -->
                    <div id="fileSection"
                         class="card-gradient p-5 {{ old('product_type', $product->product_type ?? '') == 'digital' ? '' : 'hidden' }}">
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-800 mb-1">File Produk</h3>
                                <p class="text-sm text-gray-500">File yang akan diterima pembeli</p>
                            </div>
                            <div class="p-2.5 bg-green-50 rounded-lg">
                                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                        </div>

                        <!-- File Upload Area -->
                        <div id="fileUploadArea"
                             class="border-2 border-dashed border-gray-300 rounded-xl p-5 text-center hover:border-green-400 transition-colors cursor-pointer mb-4">
                            <input type="file" name="files[]" multiple class="hidden" id="fileUpload">
                            <label for="fileUpload" class="cursor-pointer block">
                                <div class="mx-auto w-12 h-12 bg-green-50 rounded-full flex items-center justify-center mb-3">
                                    <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                    </svg>
                                </div>
                                <p class="text-gray-700 font-medium text-sm">Upload file produk</p>
                                <p class="text-xs text-gray-500 mt-1">ZIP, RAR, PDF, DOC, atau format lainnya</p>
                                <p class="text-xs text-blue-500 mt-2">※ Bisa upload multiple file sekaligus</p>
                            </label>
                        </div>

                        <!-- File List -->
                        <div id="fileList" class="space-y-2"></div>
                    </div>
                </div>

                <!-- Right Column -->
                <div class="space-y-6">
                    <!-- Pricing & Settings -->
                    <div class="card-gradient p-5 space-y-5">
                        <h3 class="text-lg font-semibold text-gray-800 mb-2">Harga & Pengaturan</h3>

                        <!-- Price -->
                        <div>
                            <label class="form-label">Harga Normal (Rp)</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 text-sm"></span>
                                </div>
                                <input type="text" name="price"
                                       id="priceInput"
                                       class="form-input pl-12"
                                       value="{{ old('price', isset($product) ? number_format($product->price,0,',','.') : '') }}"
                                       placeholder="0"
                                       required>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Harga akan otomatis diformat: Rp 100.000</p>
                        </div>

                        <!-- Discount -->
                        <div>
                            <label class="form-label">
                                Harga Setelah Diskon
                                <span class="text-sm font-normal text-gray-500">(opsional)</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 text-sm"></span>
                                </div>
                                <input type="text" name="discount"
                                       id="discountInput"
                                       class="form-input pl-12"
                                       value="{{ old('discount', isset($product->discount) ? number_format($product->discount,0,',','.') : '') }}"
                                       placeholder="0">
                            </div>
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

                        <!-- Stock Toggle -->
                        <div class="pt-4 border-t border-gray-100">
                            <div class="flex items-center justify-between mb-3">
                                <div>
                                    <label class="form-label mb-0">Kelola Stok</label>
                                    <p class="text-xs text-gray-500">Aktifkan untuk mengatur jumlah stok</p>
                                </div>
                                <div class="toggle-container">
                                    <input type="checkbox" name="stock_toggle" id="stockCheck" class="toggle-checkbox" {{ old('stock_toggle', isset($product->stock)) ? 'checked' : '' }}>
                                    <label for="stockCheck" class="toggle-label"></label>
                                </div>
                            </div>
                            <input type="number" name="stock"
                                   id="stockInput"
                                   class="form-input {{ old('stock_toggle', isset($product->stock)) ? '' : 'hidden' }}"
                                   placeholder="Jumlah stok tersedia"
                                   value="{{ old('stock', $product->stock ?? '') }}"
                                   min="1">
                        </div>

                        <!-- Purchase Limit -->
                        <div class="pt-4 border-t border-gray-100">
                            <div class="flex items-center justify-between mb-3">
                                <div>
                                    <label class="form-label mb-0">Batas Pembelian</label>
                                    <p class="text-xs text-gray-500">Batasi pembelian per pengguna</p>
                                </div>
                                <div class="toggle-container">
                                    <input type="checkbox" name="limit_toggle" id="limitCheck" class="toggle-checkbox" {{ old('limit_toggle', isset($product->purchase_limit)) ? 'checked' : '' }}>
                                    <label for="limitCheck" class="toggle-label"></label>
                                </div>
                            </div>
                            <input type="number" name="purchase_limit"
                                   id="limitInput"
                                   class="form-input {{ old('limit_toggle', isset($product->purchase_limit)) ? '' : 'hidden' }}"
                                   value="{{ old('purchase_limit', $product->purchase_limit ?? '') }}"
                                   placeholder="Maksimal beli per user"
                                   min="1">
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit"
                            class="w-full bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white py-3 px-4 rounded-lg font-semibold text-base shadow-md hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-200 flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                        {{ isset($product) ? 'Update Produk' : 'Tambah Produk' }}
                    </button>

                    <!-- Help Text -->
                    <div class="p-3 bg-blue-50 rounded-lg border border-blue-100">
                        <div class="flex items-start gap-2">
                            <svg class="w-4 h-4 text-blue-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <div class="text-xs text-gray-700">
                                <p class="font-medium text-blue-700">Tips</p>
                                <p class="mt-0.5">Pastikan informasi produk sudah benar sebelum dipublikasikan.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<style>
    .card-gradient {
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        border: 1px solid rgba(229, 231, 235, 0.6);
        transition: box-shadow 0.2s ease;
    }

    .card-gradient:hover {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.06);
    }

    .form-input {
        width: 100%;
        padding: 10px 12px;
        border: 1.5px solid #e5e7eb;
        border-radius: 8px;
        font-size: 14px;
        transition: all 0.2s ease;
        background: white;
    }

    .form-input:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }

    .form-textarea {
        width: 100%;
        padding: 10px 12px;
        border: 1.5px solid #e5e7eb;
        border-radius: 8px;
        font-size: 14px;
        transition: all 0.2s ease;
        resize: vertical;
        min-height: 100px;
        background: white;
    }

    .form-textarea:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }

    .form-label {
        display: block;
        font-size: 13px;
        font-weight: 600;
        color: #374151;
        margin-bottom: 6px;
    }

    .toggle-container {
        position: relative;
    }

    .toggle-checkbox {
        display: none;
    }

    .toggle-label {
        display: block;
        width: 44px;
        height: 24px;
        background: #e5e7eb;
        border-radius: 999px;
        position: relative;
        cursor: pointer;
        transition: background 0.2s ease;
    }

    .toggle-label::after {
        content: '';
        position: absolute;
        top: 2px;
        left: 2px;
        width: 20px;
        height: 20px;
        background: white;
        border-radius: 50%;
        transition: transform 0.2s ease;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }

    .toggle-checkbox:checked + .toggle-label {
        background: #3b82f6;
    }

    .toggle-checkbox:checked + .toggle-label::after {
        transform: translateX(20px);
    }

    .preview-image {
        position: relative;
        border-radius: 6px;
        overflow: hidden;
        aspect-ratio: 1;
        background: #f5f5f5;
    }

    .preview-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s ease;
    }

    .preview-image:hover img {
        transform: scale(1.05);
    }

    .remove-btn {
        position: absolute;
        top: 4px;
        right: 4px;
        width: 24px;
        height: 24px;
        background: rgba(239, 68, 68, 0.9);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        opacity: 0;
        transition: opacity 0.2s, transform 0.2s;
        transform: scale(0.8);
        z-index: 10;
    }

    .preview-image:hover .remove-btn {
        opacity: 1;
        transform: scale(1);
    }

    .remove-btn:hover {
        background: rgba(220, 38, 38, 1);
    }

    .remove-btn svg {
        width: 12px;
        height: 12px;
        color: white;
    }

    .existing-badge {
        position: absolute;
        top: 4px;
        left: 4px;
        background: rgba(34, 197, 94, 0.9);
        color: white;
        font-size: 10px;
        padding: 2px 6px;
        border-radius: 4px;
        z-index: 10;
    }

    .file-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 10px 12px;
        background: #f8fafc;
        border-radius: 8px;
        border: 1px solid #e2e8f0;
        transition: all 0.2s ease;
    }

    .file-item:hover {
        background: #f1f5f9;
        border-color: #cbd5e1;
    }

    .file-info {
        display: flex;
        align-items: center;
        gap: 10px;
        flex: 1;
        min-width: 0;
    }

    .file-icon {
        width: 16px;
        height: 16px;
        color: #64748b;
        flex-shrink: 0;
    }

    .file-name {
        font-size: 13px;
        color: #334155;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        flex: 1;
    }

    .file-size {
        font-size: 11px;
        color: #94a3b8;
        background: #f1f5f9;
        padding: 2px 6px;
        border-radius: 4px;
        margin-left: 8px;
        flex-shrink: 0;
    }

    .file-remove {
        width: 20px;
        height: 20px;
        background: #fef2f2;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s ease;
        flex-shrink: 0;
        margin-left: 8px;
    }

    .file-remove:hover {
        background: #fee2e2;
    }

    .file-remove svg {
        width: 10px;
        height: 10px;
        color: #ef4444;
    }

    .upload-counter {
        display: inline-block;
        background: #3b82f6;
        color: white;
        font-size: 11px;
        padding: 2px 6px;
        border-radius: 10px;
        margin-left: 6px;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // ========== GLOBAL VARIABLES ==========
    const productType = document.getElementById('productType');
    const fileSection = document.getElementById('fileSection');
    const priceInput = document.getElementById('priceInput');
    const discountInput = document.getElementById('discountInput');
    const discountPreview = document.getElementById('discountPreview');
    const discountAmount = document.getElementById('discountAmount');
    const discountPercentage = document.getElementById('discountPercentage');

    // Image upload variables
    const imageUpload = document.getElementById('imageUpload');
    const imageUploadArea = document.getElementById('imageUploadArea');
    const imagePreviewGrid = document.getElementById('imagePreviewGrid');
    let uploadedImages = [];
    let existingImages = [];
    let imagesToDelete = []; // Array untuk menyimpan ID gambar yang akan dihapus

    // File upload variables
    const fileUpload = document.getElementById('fileUpload');
    const fileUploadArea = document.getElementById('fileUploadArea');
    const fileList = document.getElementById('fileList');
    let uploadedFiles = [];
    let existingFiles = [];
    let filesToDelete = []; // Array untuk menyimpan path file yang akan dihapus

    // ========== INITIALIZE EXISTING DATA ==========
    @if(isset($product) && $product->images)
        @foreach($product->images as $image)
            existingImages.push({
                id: {{ $image->id }},
                name: "{{ $image->filename ?? 'gambar' }}",
                url: "{{ asset('storage/'.$image->image_path) }}",
                path: "{{ $image->image_path }}",
                isExisting: true
            });
        @endforeach
    @endif

    @if(isset($product) && $product->files)
        @php
            $files = is_array($product->files) ? $product->files : json_decode($product->files, true);
        @endphp
        @if(is_array($files))
            @foreach($files as $file)
                existingFiles.push({
                    id: "{{ uniqid() }}",
                    name: "{{ basename(is_array($file) ? $file['path'] : $file) }}",
                    path: "{{ is_array($file) ? $file['path'] : $file }}",
                    isExisting: true
                });
            @endforeach
        @endif
    @endif

    // ========== FUNCTIONS ==========
    function formatRupiah(angka) {
        if (!angka) return '';
        let number = angka.toString().replace(/[^,\d]/g, '');
        return number.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    }

    function cleanRupiah(formatted) {
        return parseInt(formatted.replace(/\./g, '')) || 0;
    }

    function showAlert(type, message) {
        const existingAlert = document.querySelector('.custom-alert');
        if (existingAlert) existingAlert.remove();

        const alert = document.createElement('div');
        alert.className = `custom-alert fixed top-4 right-4 z-50 px-4 py-3 rounded-lg shadow-lg transition-all duration-300 transform translate-x-full ${type === 'success' ? 'bg-green-50 border border-green-200 text-green-800' : 'bg-red-50 border border-red-200 text-red-800'}`;
        alert.innerHTML = `
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    ${type === 'success' ? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />' : '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />'}
                </svg>
                <span class="text-sm font-medium">${message}</span>
            </div>
        `;
        document.body.appendChild(alert);

        setTimeout(() => alert.classList.remove('translate-x-full'), 10);
        setTimeout(() => {
            alert.classList.add('translate-x-full');
            setTimeout(() => alert.remove(), 300);
        }, 3000);
    }

    function formatFileSize(bytes) {
        if (bytes < 1024) return bytes + ' B';
        else if (bytes < 1048576) return (bytes / 1024).toFixed(1) + ' KB';
        else return (bytes / 1048576).toFixed(1) + ' MB';
    }

    // ========== UPDATE DISCOUNT PREVIEW ==========
    function updateDiscountPreview() {
        const priceValue = cleanRupiah(priceInput.value);
        const discountValue = cleanRupiah(discountInput.value);

        if (priceValue > 0 && discountValue > 0) {
            if (discountValue >= priceValue) {
                discountPreview.classList.add('hidden');
                discountInput.classList.add('border-red-300');
                discountInput.classList.remove('border-gray-300');

                let errorMsg = discountInput.parentElement.nextElementSibling;
                if (errorMsg && errorMsg.querySelector('svg')) {
                    errorMsg.innerHTML = `
                        <svg class="w-3 h-3 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                        <span class="text-red-600">Harga diskon harus lebih rendah dari harga normal</span>
                    `;
                }
            } else {
                discountInput.classList.remove('border-red-300');
                const savings = priceValue - discountValue;
                const percentage = ((savings / priceValue) * 100).toFixed(0);

                discountAmount.textContent = `Rp ${formatRupiah(savings)}`;
                discountPercentage.textContent = `(${percentage}% OFF)`;
                discountPreview.classList.remove('hidden');

                let errorMsg = discountInput.parentElement.nextElementSibling;
                if (errorMsg) {
                    errorMsg.innerHTML = `
                        <svg class="w-3 h-3 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                        </svg>
                        Masukkan harga setelah diskon, bukan persentase
                    `;
                }
            }
        } else {
            discountPreview.classList.add('hidden');
            discountInput.classList.remove('border-red-300');
        }
    }

    // ========== IMAGE PREVIEW FUNCTIONS ==========
    function updateImagePreview() {
        imagePreviewGrid.innerHTML = '';

        // Show existing images first
        existingImages.forEach((image, index) => {
            const div = document.createElement('div');
            div.className = 'preview-image';
            div.innerHTML = `
                <img src="${image.url}" alt="Existing Image ${index + 1}" loading="lazy">
                <div class="existing-badge">Existing</div>
                <div class="remove-btn" data-existing-id="${image.id}" data-existing-index="${index}" data-type="existing" title="Hapus gambar">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </div>
            `;
            imagePreviewGrid.appendChild(div);
        });

        // Show new uploaded images
        uploadedImages.forEach((image, index) => {
            const div = document.createElement('div');
            div.className = 'preview-image';
            div.innerHTML = `
                <img src="${image.url}" alt="Preview ${index + 1}" loading="lazy">
                <div class="remove-btn" data-upload-index="${index}" data-type="upload" title="Hapus gambar">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </div>
            `;
            imagePreviewGrid.appendChild(div);
        });

        // Add click listeners for remove buttons
        document.querySelectorAll('.remove-btn').forEach(button => {
            button.addEventListener('click', function(e) {
                e.stopPropagation();
                const type = this.getAttribute('data-type');

                if (type === 'existing') {
                    const id = this.getAttribute('data-existing-id');
                    const index = parseInt(this.getAttribute('data-existing-index'));
                    
                    // Tambahkan ke daftar gambar yang akan dihapus
                    if (id) {
                        imagesToDelete.push(parseInt(id));
                    }
                    
                    // Hapus dari array existingImages
                    existingImages.splice(index, 1);
                    showAlert('success', 'Gambar akan dihapus saat update');
                } else if (type === 'upload') {
                    const index = parseInt(this.getAttribute('data-upload-index'));
                    uploadedImages.splice(index, 1);
                    showAlert('success', 'Gambar berhasil dihapus');
                }

                updateImagePreview();
                updateUploadAreaText();
            });
        });
    }

    function updateUploadAreaText() {
        const uploadText = imageUploadArea.querySelector('p:first-of-type');
        const totalCount = existingImages.length + uploadedImages.length;
        const countText = totalCount > 0 ? `<span class="upload-counter">${totalCount}</span>` : '';
        uploadText.innerHTML = `Klik untuk ${uploadedImages.length > 0 ? 'tambah' : 'upload'} gambar ${countText}`;
    }

    // ========== FILE LIST FUNCTIONS ==========
    function updateFileList() {
        fileList.innerHTML = '';

        // Show existing files first
        existingFiles.forEach((file, index) => {
            const fileItem = document.createElement('div');
            fileItem.className = 'file-item';
            fileItem.setAttribute('data-existing-index', index);
            fileItem.setAttribute('data-existing-path', file.path);

            fileItem.innerHTML = `
                <div class="file-info">
                    <svg class="file-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <span class="file-name">${file.name}</span>
                    <span class="file-size">Existing</span>
                </div>
                <div class="file-remove" title="Hapus file">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </div>
            `;
            fileList.appendChild(fileItem);
        });

        // Show new uploaded files
        uploadedFiles.forEach((file, index) => {
            const fileItem = document.createElement('div');
            fileItem.className = 'file-item';
            fileItem.setAttribute('data-upload-index', index);

            fileItem.innerHTML = `
                <div class="file-info">
                    <svg class="file-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <span class="file-name">${file.name}</span>
                    <span class="file-size">${formatFileSize(file.size)}</span>
                </div>
                <div class="file-remove" title="Hapus file">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </div>
            `;
            fileList.appendChild(fileItem);
        });

        // Add click listeners for remove buttons
        document.querySelectorAll('.file-remove').forEach(button => {
            button.addEventListener('click', function(e) {
                e.stopPropagation();
                const fileItem = this.closest('.file-item');

                if (fileItem.hasAttribute('data-existing-index')) {
                    const index = parseInt(fileItem.getAttribute('data-existing-index'));
                    const path = fileItem.getAttribute('data-existing-path');
                    
                    // Tambahkan ke daftar file yang akan dihapus
                    if (path) {
                        filesToDelete.push(path);
                    }
                    
                    // Hapus dari array existingFiles
                    existingFiles.splice(index, 1);
                    showAlert('success', 'File existing akan dihapus saat update');
                } else if (fileItem.hasAttribute('data-upload-index')) {
                    const index = parseInt(fileItem.getAttribute('data-upload-index'));
                    const removed = uploadedFiles.splice(index, 1)[0];
                    showAlert('success', `File "${removed.name}" berhasil dihapus`);
                }

                updateFileList();
                updateFileUploadAreaText();
            });
        });
    }

    function updateFileUploadAreaText() {
        const uploadText = fileUploadArea.querySelector('p:first-of-type');
        const totalCount = existingFiles.length + uploadedFiles.length;
        const countText = totalCount > 0 ? `<span class="upload-counter">${totalCount}</span>` : '';
        uploadText.innerHTML = `Klik untuk ${uploadedFiles.length > 0 ? 'tambah' : 'upload'} file ${countText}`;
    }

    // ========== EVENT LISTENERS ==========

    // Product type toggle
    function toggleFileSection() {
        if (productType.value === 'digital') {
            fileSection.classList.remove('hidden');
        } else {
            fileSection.classList.add('hidden');
            uploadedFiles = [];
            existingFiles = [];
            updateFileList();
            updateFileUploadAreaText();
        }
    }

    productType.addEventListener('change', toggleFileSection);
    toggleFileSection();

    // Price formatting
    priceInput.addEventListener('input', function(e) {
        e.target.value = formatRupiah(e.target.value);
        updateDiscountPreview();
    });

    discountInput.addEventListener('input', function(e) {
        e.target.value = formatRupiah(e.target.value);
        updateDiscountPreview();
    });

    // Stock toggle
    const stockCheck = document.getElementById('stockCheck');
    const stockInput = document.getElementById('stockInput');
    stockCheck.addEventListener('change', () => stockInput.classList.toggle('hidden'));

    // Limit toggle
    const limitCheck = document.getElementById('limitCheck');
    const limitInput = document.getElementById('limitInput');
    limitCheck.addEventListener('change', () => limitInput.classList.toggle('hidden'));

    // Image upload handling
    imageUpload.addEventListener('change', function(e) {
        const files = Array.from(e.target.files);
        files.forEach((file, index) => {
            if (!file.type.match('image.*')) {
                showAlert('error', `File "${file.name}" bukan gambar. Hanya file gambar yang diperbolehkan.`);
                return;
            }
            if (file.size > 5 * 1024 * 1024) {
                showAlert('error', `File "${file.name}" terlalu besar. Maksimal 5MB per gambar.`);
                return;
            }

            const reader = new FileReader();
            reader.onload = function(event) {
                uploadedImages.push({
                    id: Date.now() + index,
                    name: file.name,
                    url: event.target.result,
                    file: file
                });
                updateImagePreview();
                updateUploadAreaText();
            };
            reader.readAsDataURL(file);
        });
        this.value = '';
    });

    // File upload handling
    fileUpload.addEventListener('change', function(e) {
        const files = Array.from(e.target.files);
        files.forEach((file, index) => {
            uploadedFiles.push({
                id: Date.now() + index,
                name: file.name,
                size: file.size,
                type: file.type,
                file: file
            });
        });
        updateFileList();
        updateFileUploadAreaText();
        showAlert('success', `${files.length} file berhasil ditambahkan`);
        this.value = '';
    });

    // Form submission
    document.querySelector('form').addEventListener('submit', function(e) {
        const priceValue = cleanRupiah(priceInput.value);
        const discountValue = cleanRupiah(discountInput.value);

        if (priceValue <= 0) {
            e.preventDefault();
            showAlert('error', 'Harga produk harus lebih dari 0');
            priceInput.focus();
            return;
        }

        if (discountValue > 0 && discountValue >= priceValue) {
            e.preventDefault();
            showAlert('error', 'Harga diskon harus lebih rendah dari harga normal');
            discountInput.focus();
            return;
        }

        priceInput.value = priceValue;
        discountInput.value = discountValue || '';

        // Handle image files
        const imageDataTransfer = new DataTransfer();
        uploadedImages.forEach(image => imageDataTransfer.items.add(image.file));
        imageUpload.files = imageDataTransfer.files;

        // Handle product files
        const fileDataTransfer = new DataTransfer();
        uploadedFiles.forEach(fileData => fileDataTransfer.items.add(fileData.file));
        fileUpload.files = fileDataTransfer.files;

        // Validation for digital products
        if (productType.value === 'digital' && uploadedFiles.length === 0 && existingFiles.length === 0) {
            e.preventDefault();
            showAlert('error', 'Produk digital wajib memiliki file');
            return;
        }

        // Add hidden inputs for existing images to be kept
        existingImages.forEach(image => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'keep_images[]';
            input.value = image.id;
            this.appendChild(input);
        });

        // Add hidden inputs for images to be deleted
        imagesToDelete.forEach(id => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'delete_images[]';
            input.value = id;
            this.appendChild(input);
        });

        // Add hidden inputs for existing files to be kept
        existingFiles.forEach(file => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'keep_files[]';
            input.value = file.path;
            this.appendChild(input);
        });

        // Add hidden inputs for files to be deleted
        filesToDelete.forEach(path => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'delete_files[]';
            input.value = path;
            this.appendChild(input);
        });
    });

    // Initialize previews with existing data
    updateImagePreview();
    updateUploadAreaText();
    updateFileList();
    updateFileUploadAreaText();
});
</script>