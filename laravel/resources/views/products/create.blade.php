@extends('layouts.dashboard')

@section('title', 'Tambah Produk')

@section('content')
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
                <h1 class="text-xl font-bold text-gray-800">Tambah Produk Baru</h1>
            </div>
            <p class="text-sm text-gray-600 ml-10">Lengkapi informasi produk Anda</p>
        </div>

        <!-- Form -->
        <form method="POST" action="/produk" enctype="multipart/form-data" class="space-y-6">
            @csrf
            
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Left Column -->
                <div class="lg:col-span-2 space-y-6">
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
                        
                        <!-- Image Upload Area - Tetap ada untuk upload tambahan -->
                        <div id="imageUploadArea" class="border-2 border-dashed border-gray-300 rounded-xl p-4 text-center hover:border-blue-400 transition-colors cursor-pointer mb-4">
                            <input type="file" name="images[]" multiple 
                                   class="hidden" 
                                   id="imageUpload"
                                   accept="image/*">
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
                            <input type="text" name="title" 
                                   class="form-input"
                                   placeholder="Contoh: Template Website Premium"
                                   required>
                        </div>
                        <div>
                            <label class="form-label">Deskripsi Produk</label>
                            <textarea name="description" 
                                      class="form-textarea"
                                      rows="4"
                                      placeholder="Jelaskan detail produk, fitur, dan manfaat yang didapatkan..."
                                      required></textarea>
                            <p class="text-xs text-gray-500 mt-2">Minimal 100 karakter untuk deskripsi yang optimal</p>
                        </div>
                    </div>

                    <!-- Files Upload -->
                    <div class="card-gradient p-5">
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
                        <div id="fileUploadArea" class="border-2 border-dashed border-gray-300 rounded-xl p-5 text-center hover:border-green-400 transition-colors cursor-pointer mb-4">
                            <input type="file" name="files[]" multiple 
                                   class="hidden" 
                                   id="fileUpload">
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
                            <label class="form-label">Harga (Rp)</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                </div>
                                <input type="number" name="price" 
                                       class="form-input pl-10"
                                       placeholder="0"
                                       min="0"
                                       required>
                            </div>
                        </div>

                        <!-- Discount -->
                        <div>
                            <label class="form-label">
                                Diskon 
                                <span class="text-sm font-normal text-gray-500">(opsional)</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                </div>
                                <input type="number" name="discount" 
                                       class="form-input pl-10"
                                       placeholder="0"
                                       min="0"
                                       max="100">
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
                                    <input type="checkbox" name="stock_toggle" id="stockCheck" class="toggle-checkbox">
                                    <label for="stockCheck" class="toggle-label"></label>
                                </div>
                            </div>
                            <input type="number" name="stock" 
                                   id="stockInput" 
                                   class="form-input hidden"
                                   placeholder="Jumlah stok tersedia"
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
                                    <input type="checkbox" name="limit_toggle" id="limitCheck" class="toggle-checkbox">
                                    <label for="limitCheck" class="toggle-label"></label>
                                </div>
                            </div>
                            <input type="number" name="purchase_limit" 
                                   id="limitInput" 
                                   class="form-input hidden"
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
                        Tambah Produk
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
    // Setup toggle functionality
    function setupToggle(checkboxId, inputId) {
        const checkbox = document.getElementById(checkboxId);
        const input = document.getElementById(inputId);
        
        checkbox.addEventListener('change', function() {
            if (this.checked) {
                input.classList.remove('hidden');
                input.focus();
            } else {
                input.classList.add('hidden');
                input.value = '';
            }
        });
    }
    
    setupToggle('stockCheck', 'stockInput');
    setupToggle('limitCheck', 'limitInput');
    
    // ========== IMAGE UPLOAD FUNCTIONALITY ==========
    const imageUpload = document.getElementById('imageUpload');
    const imageUploadArea = document.getElementById('imageUploadArea');
    const imagePreviewGrid = document.getElementById('imagePreviewGrid');
    let uploadedImages = [];
    
    // Click on upload area to trigger file input
    imageUploadArea.addEventListener('click', function() {
        imageUpload.click();
    });
    
    // Handle image selection
    imageUpload.addEventListener('change', function(e) {
        const files = Array.from(e.target.files);
        handleImageFiles(files);
        this.value = ''; // Reset input untuk upload file yang sama lagi
    });
    
    // Handle image files
    function handleImageFiles(files) {
        files.forEach((file, index) => {
            // Validate file type
            if (!file.type.match('image.*')) {
                showAlert('error', `File "${file.name}" bukan gambar. Hanya file gambar yang diperbolehkan.`);
                return;
            }
            
            // Validate file size (5MB max)
            if (file.size > 5 * 1024 * 1024) {
                showAlert('error', `File "${file.name}" terlalu besar. Maksimal 5MB per gambar.`);
                return;
            }
            
            const reader = new FileReader();
            reader.onload = function(event) {
                const imageData = {
                    id: Date.now() + index,
                    name: file.name,
                    url: event.target.result,
                    file: file
                };
                
                uploadedImages.push(imageData);
                updateImagePreview();
                
                // Update upload area text
                updateUploadAreaText();
            };
            reader.readAsDataURL(file);
        });
    }
    
    // Update image preview
    function updateImagePreview() {
        imagePreviewGrid.innerHTML = '';
        
        uploadedImages.forEach((image, index) => {
            const div = document.createElement('div');
            div.className = 'preview-image';
            div.innerHTML = `
                <img src="${image.url}" alt="Preview ${index + 1}" loading="lazy">
                <div class="remove-btn" data-index="${index}" title="Hapus gambar">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </div>
            `;
            imagePreviewGrid.appendChild(div);
        });
        
        // Add click listener for remove buttons
        document.querySelectorAll('.remove-btn').forEach(button => {
            button.addEventListener('click', function(e) {
                e.stopPropagation();
                const index = parseInt(this.getAttribute('data-index'));
                
                // Remove from array
                uploadedImages.splice(index, 1);
                
                // Update preview
                updateImagePreview();
                
                // Update upload area text
                updateUploadAreaText();
                
                showAlert('success', 'Gambar berhasil dihapus');
            });
        });
    }
    
    // Update upload area text
    function updateUploadAreaText() {
        const uploadText = imageUploadArea.querySelector('p:first-of-type');
        const countText = uploadedImages.length > 0 ? 
            `<span class="upload-counter">${uploadedImages.length}</span>` : '';
        
        uploadText.innerHTML = `Klik untuk ${uploadedImages.length > 0 ? 'tambah' : 'upload'} gambar ${countText}`;
    }
    
    // ========== FILE UPLOAD FUNCTIONALITY ==========
    const fileUpload = document.getElementById('fileUpload');
    const fileUploadArea = document.getElementById('fileUploadArea');
    const fileList = document.getElementById('fileList');
    let uploadedFiles = [];
    
    // Click on upload area to trigger file input
    fileUploadArea.addEventListener('click', function() {
        fileUpload.click();
    });
    
    // Handle file selection
    fileUpload.addEventListener('change', function(e) {
        const files = Array.from(e.target.files);
        handleFileUpload(files);
        this.value = ''; // Reset input untuk upload file yang sama lagi
    });
    
    // Handle file upload
    function handleFileUpload(files) {
        files.forEach((file, index) => {
            const fileData = {
                id: Date.now() + index,
                name: file.name,
                size: file.size,
                type: file.type,
                file: file
            };
            
            uploadedFiles.push(fileData);
            updateFileList();
            updateFileUploadAreaText();
            
            showAlert('success', `File "${file.name}" berhasil ditambahkan`);
        });
    }
    
    // Update file list display
    function updateFileList() {
        fileList.innerHTML = '';
        
        uploadedFiles.forEach((fileData, index) => {
            const fileItem = document.createElement('div');
            fileItem.className = 'file-item';
            fileItem.setAttribute('data-index', index);
            
            // Get file icon based on extension
            const extension = fileData.name.split('.').pop().toLowerCase();
            let iconPath = 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z';
            
            if (['zip', 'rar', '7z', 'tar', 'gz'].includes(extension)) {
                iconPath = 'M12 19l9 2-9-18-9 18 9-2zm0 0v-8';
            } else if (['pdf'].includes(extension)) {
                iconPath = 'M10 8v8m4-8v8m6-4A9 9 0 111 12a9 9 0 0118 0z';
            } else if (['jpg', 'jpeg', 'png', 'gif', 'bmp', 'svg'].includes(extension)) {
                iconPath = 'M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z';
            } else if (['mp3', 'wav', 'ogg', 'm4a'].includes(extension)) {
                iconPath = 'M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3';
            } else if (['mp4', 'avi', 'mov', 'wmv', 'mkv'].includes(extension)) {
                iconPath = 'M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z';
            }
            
            fileItem.innerHTML = `
                <div class="file-info">
                    <svg class="file-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="${iconPath}" />
                    </svg>
                    <span class="file-name">${fileData.name}</span>
                    <span class="file-size">${formatFileSize(fileData.size)}</span>
                </div>
                <div class="file-remove" title="Hapus file">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </div>
            `;
            
            fileList.appendChild(fileItem);
        });
        
        // Add click listener for remove buttons
        document.querySelectorAll('.file-remove').forEach(button => {
            button.addEventListener('click', function(e) {
                e.stopPropagation();
                const fileItem = this.closest('.file-item');
                const index = parseInt(fileItem.getAttribute('data-index'));
                
                // Remove from array
                const removedFile = uploadedFiles.splice(index, 1)[0];
                
                // Update list
                updateFileList();
                updateFileUploadAreaText();
                
                showAlert('success', `File "${removedFile.name}" berhasil dihapus`);
            });
        });
    }
    
    // Update file upload area text
    function updateFileUploadAreaText() {
        const uploadText = fileUploadArea.querySelector('p:first-of-type');
        const countText = uploadedFiles.length > 0 ? 
            `<span class="upload-counter">${uploadedFiles.length}</span>` : '';
        
        uploadText.innerHTML = `Klik untuk ${uploadedFiles.length > 0 ? 'tambah' : 'upload'} file ${countText}`;
    }
    
    // Format file size
    function formatFileSize(bytes) {
        if (bytes < 1024) return bytes + ' B';
        else if (bytes < 1048576) return (bytes / 1024).toFixed(1) + ' KB';
        else return (bytes / 1048576).toFixed(1) + ' MB';
    }
    
    // Alert notification
    function showAlert(type, message) {
        // Remove existing alert
        const existingAlert = document.querySelector('.custom-alert');
        if (existingAlert) {
            existingAlert.remove();
        }
        
        // Create alert
        const alert = document.createElement('div');
        alert.className = `custom-alert fixed top-4 right-4 z-50 px-4 py-3 rounded-lg shadow-lg transition-all duration-300 transform translate-x-full ${type === 'success' ? 'bg-green-50 border border-green-200 text-green-800' : 'bg-red-50 border border-red-200 text-red-800'}`;
        alert.innerHTML = `
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    ${type === 'success' ? 
                        '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />' : 
                        '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />'}
                </svg>
                <span class="text-sm font-medium">${message}</span>
            </div>
        `;
        
        document.body.appendChild(alert);
        
        // Show alert
        setTimeout(() => {
            alert.classList.remove('translate-x-full');
            alert.classList.add('translate-x-0');
        }, 10);
        
        // Hide after 3 seconds
        setTimeout(() => {
            alert.classList.remove('translate-x-0');
            alert.classList.add('translate-x-full');
            setTimeout(() => alert.remove(), 300);
        }, 3000);
    }
    
    // Update file input on form submit
    document.querySelector('form').addEventListener('submit', function(e) {
        // Update images files
        const imageDataTransfer = new DataTransfer();
        uploadedImages.forEach(image => {
            imageDataTransfer.items.add(image.file);
        });
        imageUpload.files = imageDataTransfer.files;
        
        // Update files files
        const fileDataTransfer = new DataTransfer();
        uploadedFiles.forEach(fileData => {
            fileDataTransfer.items.add(fileData.file);
        });
        fileUpload.files = fileDataTransfer.files;
        
        // Validation
        if (uploadedImages.length === 0) {
            e.preventDefault();
            showAlert('error', 'Harap upload minimal 1 gambar produk');
            return;
        }
        
        if (uploadedFiles.length === 0) {
            e.preventDefault();
            showAlert('error', 'Harap upload minimal 1 file produk');
            return;
        }
    });
    
    // Price formatting
    const priceInput = document.querySelector('input[name="price"]');
    priceInput.addEventListener('blur', function() {
        if (this.value) {
            const formatted = new Intl.NumberFormat('id-ID').format(this.value.replace(/\./g, ''));
            this.value = formatted;
        }
    });
    
    priceInput.addEventListener('focus', function() {
        this.value = this.value.replace(/\./g, '');
    });
});
</script>
@endsection