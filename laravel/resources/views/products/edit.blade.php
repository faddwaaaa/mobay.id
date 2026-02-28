@foreach($products as $product)
@push('modals')

{{-- OVERLAY --}}
<div id="editModalOverlay-{{ $product->id }}"
     class="fixed inset-0 hidden"
     style="z-index:9990; background:rgba(15,23,42,0.5); backdrop-filter:blur(5px); -webkit-backdrop-filter:blur(5px);
            opacity:0; transition:opacity 0.2s ease;"
     onclick="closeEditModal({{ $product->id }})">
</div>

{{-- MODAL --}}
<div id="editModal-{{ $product->id }}"
     class="fixed inset-0 hidden"
     style="z-index:9999; display:none; align-items:center; justify-content:center; padding:16px; pointer-events:none;">

    <div id="editModalCard-{{ $product->id }}"
         class="bg-white w-full max-w-2xl rounded-2xl shadow-2xl max-h-[92vh] flex flex-col pointer-events-auto"
         style="opacity:0; transform:translateY(20px); transition:opacity 0.25s cubic-bezier(.16,1,.3,1), transform 0.25s cubic-bezier(.16,1,.3,1);">

        {{-- HEADER --}}
        <div class="flex items-center justify-between p-5 border-b border-gray-100 flex-shrink-0">
            <div class="flex items-center gap-3">
                <div class="p-2 rounded-lg"
                     style="{{ ($product->product_type ?? 'fisik') === 'digital' ? 'background:#eff6ff' : 'background:#f0fdf4' }}">
                    <svg class="w-5 h-5" style="{{ ($product->product_type ?? 'fisik') === 'digital' ? 'color:#2563eb' : 'display:none' }}"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <svg class="w-5 h-5" style="{{ ($product->product_type ?? 'fisik') !== 'digital' ? 'color:#16a34a' : 'display:none' }}"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-base font-bold text-gray-800">
                        Edit Produk
                        <span style="font-size:11px; padding:2px 8px; border-radius:999px; font-weight:600; margin-left:6px;
                                     {{ ($product->product_type ?? 'fisik') === 'digital' ? 'background:#eff6ff; color:#2563eb' : 'background:#f0fdf4; color:#16a34a' }}">
                            {{ ($product->product_type ?? 'fisik') === 'digital' ? 'Digital' : 'Fisik' }}
                        </span>
                    </h2>
                    <p class="text-xs text-gray-500 mt-0.5">Perbarui informasi produk Anda</p>
                </div>
            </div>
            <button onclick="closeEditModal({{ $product->id }})"
                    class="p-2 rounded-lg hover:bg-gray-100 transition-colors text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- BODY --}}
        <div class="p-5 overflow-y-auto flex-1">
            <form id="editForm-{{ $product->id }}"
                  method="POST"
                  action="{{ route('products.update', $product->id) }}"
                  enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <input type="hidden" name="product_type" value="{{ $product->product_type ?? 'fisik' }}">

                <div class="space-y-5">

                    {{-- 2. GAMBAR SAAT INI --}}
                    <div class="edit-card p-4">
                        <div class="flex items-center justify-between mb-3">
                            <div>
                                <h3 class="text-sm font-semibold text-gray-800">Gambar Saat Ini</h3>
                                <p class="text-xs text-gray-500 mt-0.5">Centang gambar yang ingin dihapus</p>
                            </div>
                            <div class="p-2 bg-orange-50 rounded-lg">
                                <svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                        </div>
                        @if($product->images->count())
                        <div class="grid grid-cols-3 sm:grid-cols-4 gap-2">
                            @foreach($product->images as $img)
                            <label class="edit-img-item group cursor-pointer block relative rounded-xl overflow-hidden border-2 border-transparent hover:border-red-400 transition-all">
                                <input type="checkbox" name="delete_images[]" value="{{ $img->id }}" class="hidden peer">
                                <img src="{{ asset('storage/'.$img->image) }}" class="h-20 w-full object-cover">
                                <div class="absolute inset-0 bg-red-500/0 peer-checked:bg-red-500/40 transition-all flex items-center justify-center">
                                    <div class="opacity-0 peer-checked:opacity-100 bg-white rounded-full p-1 transition-all scale-50 peer-checked:scale-100">
                                        <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </div>
                                </div>
                                <div class="absolute top-1.5 left-1.5 bg-white/90 rounded px-1.5 py-0.5 text-[10px] font-medium text-gray-600 shadow-sm opacity-0 group-hover:opacity-100 transition-opacity">Hapus</div>
                            </label>
                            @endforeach
                        </div>
                        @else
                        <div class="text-center py-4 text-sm text-gray-400">Tidak ada gambar</div>
                        @endif
                    </div>

                    {{-- 3. TAMBAH GAMBAR BARU --}}
                    <div class="edit-card p-4">
                        <div class="flex items-center justify-between mb-3">
                            <div>
                                <h3 class="text-sm font-semibold text-gray-800">Tambah Gambar Baru</h3>
                                <p class="text-xs text-gray-500 mt-0.5">PNG, JPG, JPEG — dikompresi otomatis</p>
                            </div>
                            <div class="p-2 bg-blue-50 rounded-lg">
                                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                            </div>
                        </div>
                        <div class="border-2 border-dashed border-gray-200 rounded-xl p-4 text-center hover:border-blue-400 transition-colors cursor-pointer">
                            <input type="file" id="editNewImages-{{ $product->id }}" name="images[]" multiple accept="image/*" class="hidden">
                            <label for="editNewImages-{{ $product->id }}" class="cursor-pointer block">
                                <div class="mx-auto w-10 h-10 bg-blue-50 rounded-full flex items-center justify-center mb-2">
                                    <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                    </svg>
                                </div>
                                <p class="text-sm text-gray-700 font-medium" id="editImgUploadText-{{ $product->id }}">Klik untuk upload gambar baru</p>
                                <p class="text-xs text-gray-400 mt-1">Gambar akan dikompresi otomatis</p>
                            </label>
                        </div>
                        <div id="editImgStatus-{{ $product->id }}" class="hidden mt-2"></div>
                        <div id="editImgPreview-{{ $product->id }}" class="grid grid-cols-3 sm:grid-cols-4 gap-2 mt-3"></div>
                    </div>

                    {{-- 4. INFO PRODUK --}}
                    <div class="edit-card p-4 space-y-4">
                        <h3 class="text-sm font-semibold text-gray-800 mb-1">Informasi Produk</h3>
                        <div>
                            <label class="edit-label">Judul Produk</label>
                            <input type="text" name="title" value="{{ $product->title }}" class="edit-input" placeholder="Judul produk Anda">
                        </div>
                        <div>
                            <label class="edit-label">Deskripsi Produk</label>
                            <textarea name="description" rows="4" class="edit-textarea" placeholder="Deskripsikan produk Anda...">{{ $product->description }}</textarea>
                        </div>
                    </div>

                    {{-- 5. FILE UNTUK PEMBELI (hanya digital) --}}
                    @php
                        $existingPlatform = 'upload';
                        $existingFileUrl  = '';
                        if (isset($product->files) && $product->files->count()) {
                            $firstFile        = $product->files->first();
                            $existingPlatform = $firstFile->platform ?? 'upload';
                            $existingFileUrl  = $firstFile->file_url ?? '';
                        }
                    @endphp

                    <div id="editFileSection-{{ $product->id }}"
                         class="edit-card p-4 {{ ($product->product_type ?? 'fisik') === 'digital' ? '' : 'hidden' }}">

                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <h3 class="text-sm font-semibold text-gray-800">File untuk Pembeli</h3>
                                <p class="text-xs text-gray-500 mt-0.5">File atau link yang diterima pembeli setelah transaksi</p>
                            </div>
                            <div class="p-2 bg-blue-50 rounded-lg">
                                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </div>
                        </div>

                        {{-- Platform Selector --}}
                        <div class="mb-4">
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Platform</p>
                            <div class="flex flex-wrap gap-2" id="editPlatformBtns-{{ $product->id }}">
                                <button type="button" class="edit-platform-btn {{ $existingPlatform === 'upload'  ? 'active' : '' }}" data-platform="upload">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                                    Upload
                                </button>
                                <button type="button" class="edit-platform-btn {{ $existingPlatform === 'dropbox' ? 'active' : '' }}" data-platform="dropbox">
                                    <svg class="w-3 h-3" viewBox="0 0 24 24" fill="currentColor"><path d="M6 2L0 6l6 4-6 4 6 4 6-4-6-4 6-4L6 2zm12 0l-6 4 6 4-6 4 6 4 6-4-6-4 6-4-6-4zM6 16.5L0 12.5l6 4zm12 0l6-4-6 4z"/></svg>
                                    Dropbox
                                </button>
                                <button type="button" class="edit-platform-btn {{ $existingPlatform === 'gdrive'  ? 'active' : '' }}" data-platform="gdrive">
                                    <svg class="w-3 h-3" viewBox="0 0 24 24" fill="currentColor"><path d="M4.585 18.832L6.17 21.5a2 2 0 001.732 1h8.196a2 2 0 001.732-1l1.585-2.668H4.585zM12 3L2 19.5h4L12 8l6 11.5h4L12 3zM8 14l4-7 4 7H8z"/></svg>
                                    G-Drive
                                </button>
                                <button type="button" class="edit-platform-btn {{ $existingPlatform === 'other'   ? 'active' : '' }}" data-platform="other">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                                    Other
                                </button>
                            </div>
                        </div>

                        <input type="hidden" name="file_platform" id="editFilePlatform-{{ $product->id }}" value="{{ $existingPlatform }}">

                        {{-- PANEL: Upload --}}
                        <div id="editPanelUpload-{{ $product->id }}" class="{{ $existingPlatform === 'upload' ? '' : 'hidden' }}">
                            @if(isset($product->files) && $product->files->where('platform', 'upload')->count())
                            <div class="mb-3">
                                <p class="text-xs font-semibold text-gray-600 mb-2">File Saat Ini</p>
                                <div class="space-y-2">
                                    @foreach($product->files->where('platform', 'upload') as $file)
                                    <div class="flex items-center justify-between p-2.5 bg-gray-50 rounded-lg border border-gray-200 group">
                                        <div class="flex items-center gap-2 min-w-0 flex-1">
                                            <div class="p-1.5 bg-white rounded border border-gray-200 flex-shrink-0">
                                                <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                            </div>
                                            <span class="text-xs text-gray-700 truncate">{{ basename($file->file ?? 'File') }}</span>
                                        </div>
                                        <label class="flex items-center gap-1.5 text-xs text-red-500 cursor-pointer flex-shrink-0 ml-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                            <input type="checkbox" name="delete_files[]" value="{{ $file->id }}" class="w-3.5 h-3.5 accent-red-500">
                                            Hapus
                                        </label>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            @endif
                            <div class="border-2 border-dashed border-gray-200 rounded-xl p-4 text-center hover:border-blue-400 transition-colors cursor-pointer">
                                <input type="file" id="editFileUpload-{{ $product->id }}" name="files[]" multiple class="hidden"
                                       onchange="editHandleFiles(event, {{ $product->id }})">
                                <label for="editFileUpload-{{ $product->id }}" class="cursor-pointer block">
                                    <div class="mx-auto w-10 h-10 bg-blue-50 rounded-full flex items-center justify-center mb-2">
                                        <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                        </svg>
                                    </div>
                                    <p class="text-sm text-gray-700 font-medium">Klik untuk upload file baru</p>
                                    <p class="text-xs text-gray-400 mt-1">ZIP, RAR, PDF, DOC, atau format lainnya</p>
                                    <p class="text-xs text-blue-400 mt-1">※ Bisa upload multiple file sekaligus</p>
                                </label>
                            </div>
                            <div id="editFileList-{{ $product->id }}" class="space-y-2 mt-3"></div>
                        </div>

                        {{-- PANEL: Dropbox --}}
                        <div id="editPanelDropbox-{{ $product->id }}" class="{{ $existingPlatform === 'dropbox' ? '' : 'hidden' }}">
                            <div class="edit-platform-url-wrap" style="--accent:#0061ff;">
                                <div class="edit-platform-url-icon" style="background:#e8f0ff;">
                                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="#0061ff"><path d="M6 2L0 6l6 4-6 4 6 4 6-4-6-4 6-4L6 2zm12 0l-6 4 6 4-6 4 6 4 6-4-6-4 6-4-6-4zM6 16.5L0 12.5l6 4zm12 0l6-4-6 4z"/></svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs font-semibold text-gray-700 mb-1">Link Dropbox</p>
                                    <input type="url" id="editUrlDropbox-{{ $product->id }}" class="edit-platform-url-input"
                                           placeholder="https://www.dropbox.com/s/..."
                                           value="{{ $existingPlatform === 'dropbox' ? $existingFileUrl : '' }}"
                                           oninput="editValidateUrl(this, 'dropbox.com', {{ $product->id }}, 'Dropbox')">
                                    <p class="text-xs text-gray-400 mt-1">Pastikan link bersifat publik atau "Anyone with link"</p>
                                </div>
                            </div>
                            <div id="editFeedbackDropbox-{{ $product->id }}" class="edit-url-feedback hidden mt-2"></div>
                        </div>

                        {{-- PANEL: Google Drive --}}
                        <div id="editPanelGdrive-{{ $product->id }}" class="{{ $existingPlatform === 'gdrive' ? '' : 'hidden' }}">
                            <div class="edit-platform-url-wrap" style="--accent:#34a853;">
                                <div class="edit-platform-url-icon" style="background:#e6f4ea;">
                                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="#34a853"><path d="M4.585 18.832L6.17 21.5a2 2 0 001.732 1h8.196a2 2 0 001.732-1l1.585-2.668H4.585zM12 3L2 19.5h4L12 8l6 11.5h4L12 3zM8 14l4-7 4 7H8z"/></svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs font-semibold text-gray-700 mb-1">Link Google Drive</p>
                                    <input type="url" id="editUrlGdrive-{{ $product->id }}" class="edit-platform-url-input"
                                           placeholder="https://drive.google.com/file/d/..."
                                           value="{{ $existingPlatform === 'gdrive' ? $existingFileUrl : '' }}"
                                           oninput="editValidateUrl(this, 'drive.google.com', {{ $product->id }}, 'Google Drive')">
                                    <p class="text-xs text-gray-400 mt-1">Pastikan sharing diset ke "Anyone with link can view"</p>
                                </div>
                            </div>
                            <div id="editFeedbackGdrive-{{ $product->id }}" class="edit-url-feedback hidden mt-2"></div>
                        </div>

                        {{-- PANEL: Other --}}
                        <div id="editPanelOther-{{ $product->id }}" class="{{ $existingPlatform === 'other' ? '' : 'hidden' }}">
                            <div class="edit-platform-url-wrap" style="--accent:#6366f1;">
                                <div class="edit-platform-url-icon" style="background:#eef2ff;">
                                    <svg class="w-4 h-4" fill="none" stroke="#6366f1" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs font-semibold text-gray-700 mb-1">URL File / Link Download</p>
                                    <input type="url" id="editUrlOther-{{ $product->id }}" class="edit-platform-url-input"
                                           placeholder="https://..."
                                           value="{{ $existingPlatform === 'other' ? $existingFileUrl : '' }}"
                                           oninput="editValidateUrl(this, null, {{ $product->id }}, 'Other')">
                                    <p class="text-xs text-gray-400 mt-1">Bisa berupa link WeTransfer, Mediafire, OneDrive, dsb.</p>
                                </div>
                            </div>
                            <div id="editFeedbackOther-{{ $product->id }}" class="edit-url-feedback hidden mt-2"></div>
                        </div>

                    </div>

                    {{-- 6. HARGA & DISKON --}}
                    <div class="edit-card p-4 space-y-4">
                        <h3 class="text-sm font-semibold text-gray-800 mb-1">Harga & Diskon</h3>
                        <div>
                            <label class="edit-label">Harga Normal (Rp)</label>
                            <input type="text" id="editPrice-{{ $product->id }}" name="price"
                                   value="{{ number_format($product->price,0,',','.') }}"
                                   oninput="formatRupiah(this)" class="edit-input" placeholder="Contoh: 100.000">
                            <p class="text-xs text-gray-400 mt-1">Harga akan otomatis diformat: Rp 100.000</p>
                        </div>
                        <div>
                            <label class="edit-label">Harga Setelah Diskon <span class="text-xs font-normal text-gray-400">(opsional)</span></label>
                            <input type="text" id="editDiscount-{{ $product->id }}" name="discount"
                                   value="{{ $product->discount ? number_format($product->discount,0,',','.') : '' }}"
                                   oninput="formatRupiah(this)" class="edit-input" placeholder="Kosongkan jika tidak ada diskon">
                            <p class="text-xs text-blue-500 mt-1 flex items-center gap-1">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>
                                Masukkan harga setelah diskon, bukan persentase
                            </p>
                        </div>
                    </div>

                    {{-- 7. STOK & BATAS PEMBELIAN --}}
                    <div class="edit-card p-4 space-y-1">
                        <h3 class="text-sm font-semibold text-gray-800 mb-3">
                            {{ ($product->product_type ?? 'fisik') === 'digital' ? 'Batas Pembelian' : 'Stok & Batas Pembelian' }}
                        </h3>
                        <div class="py-3 border-b border-gray-100"
                             style="{{ ($product->product_type ?? 'fisik') === 'digital' ? 'display:none' : '' }}">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-700">Kelola Stok</p>
                                    <p class="text-xs text-gray-400 mt-0.5">Aktifkan untuk mengatur jumlah stok</p>
                                </div>
                                <div class="edit-toggle-container">
                                    <input type="checkbox" name="stock_toggle" id="editStockCheck-{{ $product->id }}"
                                           class="edit-toggle-checkbox" {{ $product->stock !== null ? 'checked' : '' }}
                                           onchange="editToggleInput(this, 'editStockInput-{{ $product->id }}')">
                                    <label for="editStockCheck-{{ $product->id }}" class="edit-toggle-label"></label>
                                </div>
                            </div>
                            <input type="number" name="stock" id="editStockInput-{{ $product->id }}"
                                   class="edit-input mt-3 {{ $product->stock !== null ? '' : 'hidden' }}"
                                   value="{{ $product->stock }}" placeholder="Jumlah stok tersedia" min="1">
                        </div>
                        <div class="py-3">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-700">Batas Pembelian</p>
                                    <p class="text-xs text-gray-400 mt-0.5">Batasi pembelian per pengguna</p>
                                </div>
                                <div class="edit-toggle-container">
                                    <input type="checkbox" name="limit_toggle" id="editLimitCheck-{{ $product->id }}"
                                           class="edit-toggle-checkbox" {{ $product->purchase_limit !== null ? 'checked' : '' }}
                                           onchange="editToggleInput(this, 'editLimitInput-{{ $product->id }}')">
                                    <label for="editLimitCheck-{{ $product->id }}" class="edit-toggle-label"></label>
                                </div>
                            </div>
                            <input type="number" name="purchase_limit" id="editLimitInput-{{ $product->id }}"
                                   class="edit-input mt-3 {{ $product->purchase_limit !== null ? '' : 'hidden' }}"
                                   value="{{ $product->purchase_limit }}" placeholder="Maksimal beli per user" min="1">
                        </div>
                    </div>

                </div>
            </form>
        </div>

        {{-- FOOTER --}}
        <div class="p-5 border-t border-gray-100 flex items-center justify-between gap-3 bg-gray-50/60 rounded-b-2xl flex-shrink-0">
            <p class="text-xs text-gray-400 flex items-center gap-1">
                <svg class="w-3.5 h-3.5 text-yellow-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                </svg>
                Pastikan data sudah benar sebelum menyimpan
            </p>
            <div class="flex gap-2 flex-shrink-0">
                <button type="button" onclick="closeEditModal({{ $product->id }})"
                        class="px-4 py-2 text-sm font-medium bg-white border border-gray-200 rounded-lg hover:bg-gray-50 text-gray-600 transition-colors">
                    Batal
                </button>
                <button type="submit" form="editForm-{{ $product->id }}"
                        class="px-5 py-2 text-sm font-semibold text-white rounded-lg shadow-sm hover:shadow-md transform hover:-translate-y-0.5 transition-all duration-200 flex items-center gap-2"
                        style="{{ ($product->product_type ?? 'fisik') === 'digital' ? 'background:linear-gradient(to right,#2563eb,#1d4ed8)' : 'background:linear-gradient(to right,#16a34a,#15803d)' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Simpan Perubahan
                </button>
            </div>
        </div>

    </div>
</div>

@endpush
@endforeach

<style>
.edit-card { background:white; border-radius:12px; border:1.5px solid rgba(229,231,235,0.7); box-shadow:0 1px 4px rgba(0,0,0,0.04); transition:box-shadow .2s; }
.edit-card:hover { box-shadow:0 3px 10px rgba(0,0,0,0.07); }
.edit-label { display:block; font-size:12px; font-weight:600; color:#374151; margin-bottom:5px; }
.edit-input { width:100%; padding:9px 12px; border:1.5px solid #e5e7eb; border-radius:8px; font-size:13px; transition:all .2s; background:white; color:#1f2937; }
.edit-input:focus { outline:none; border-color:#3b82f6; box-shadow:0 0 0 3px rgba(59,130,246,.1); }
.edit-textarea { width:100%; padding:9px 12px; border:1.5px solid #e5e7eb; border-radius:8px; font-size:13px; transition:all .2s; resize:vertical; min-height:90px; background:white; color:#1f2937; }
.edit-textarea:focus { outline:none; border-color:#3b82f6; box-shadow:0 0 0 3px rgba(59,130,246,.1); }
.edit-img-item:has(input:checked) { border-color:#ef4444 !important; opacity:.75; }
.edit-img-item:has(input:checked) > div { background-color:rgba(239,68,68,.35); }
.edit-img-item:has(input:checked) > div > div { opacity:1; transform:scale(1); }
.edit-toggle-container { position:relative; }
.edit-toggle-checkbox { display:none; }
.edit-toggle-label { display:block; width:44px; height:24px; background:#e5e7eb; border-radius:999px; position:relative; cursor:pointer; transition:background .2s ease; }
.edit-toggle-label::after { content:''; position:absolute; top:2px; left:2px; width:20px; height:20px; background:white; border-radius:50%; transition:transform .2s ease; box-shadow:0 1px 3px rgba(0,0,0,.15); }
.edit-toggle-checkbox:checked + .edit-toggle-label { background:#3b82f6; }
.edit-toggle-checkbox:checked + .edit-toggle-label::after { transform:translateX(20px); }
.edit-platform-btn { display:inline-flex; align-items:center; gap:5px; padding:6px 14px; border-radius:999px; font-size:12px; font-weight:500; border:1.5px solid #d1d5db; color:#374151; background:white; cursor:pointer; transition:all 0.18s; user-select:none; }
.edit-platform-btn:hover { border-color:#3b82f6; color:#2563eb; background:#eff6ff; }
.edit-platform-btn.active { background:#2563eb; color:white; border-color:#2563eb; box-shadow:0 2px 8px rgba(37,99,235,0.22); }
.edit-platform-url-wrap { display:flex; align-items:flex-start; gap:10px; background:#fafafa; border:1.5px solid #e5e7eb; border-radius:10px; padding:12px 14px; transition:border-color 0.2s; }
.edit-platform-url-wrap:focus-within { border-color:var(--accent,#2563eb); box-shadow:0 0 0 3px color-mix(in srgb,var(--accent,#2563eb) 10%,transparent); }
.edit-platform-url-icon { width:34px; height:34px; border-radius:8px; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
.edit-platform-url-input { width:100%; padding:7px 10px; border:1.5px solid #e5e7eb; border-radius:7px; font-size:12px; background:white; transition:all 0.2s; color:#1f2937; }
.edit-platform-url-input:focus { outline:none; border-color:var(--accent,#2563eb); }
.edit-url-feedback { padding:7px 10px; border-radius:7px; font-size:11px; display:flex; align-items:center; gap:5px; }
.edit-url-feedback.valid { background:#f0fdf4; color:#16a34a; border:1px solid #bbf7d0; }
.edit-url-feedback.invalid { background:#fef2f2; color:#dc2626; border:1px solid #fecaca; }
.edit-preview-img-wrap { position:relative; border-radius:8px; overflow:hidden; aspect-ratio:1; background:#f5f5f5; }
.edit-preview-img-wrap img { width:100%; height:100%; object-fit:cover; transition:transform 0.3s; }
.edit-preview-img-wrap:hover img { transform:scale(1.05); }
.edit-preview-remove { position:absolute; top:4px; right:4px; width:22px; height:22px; background:rgba(239,68,68,0.9); border-radius:50%; display:flex; align-items:center; justify-content:center; cursor:pointer; opacity:0; transition:opacity 0.2s,transform 0.2s; transform:scale(0.8); }
.edit-preview-img-wrap:hover .edit-preview-remove { opacity:1; transform:scale(1); }
.edit-img-ready-dot { position:absolute; bottom:4px; right:4px; width:7px; height:7px; border-radius:50%; background:#2563eb; border:1.5px solid white; box-shadow:0 1px 3px rgba(0,0,0,0.2); }
@keyframes editSpin { from { transform:rotate(0deg); } to { transform:rotate(360deg); } }
</style>

<script>
function openEditModal(id) {
    const overlay = document.getElementById('editModalOverlay-' + id);
    const modal   = document.getElementById('editModal-' + id);
    const card    = document.getElementById('editModalCard-' + id);
    if (!modal) return;
    overlay.classList.remove('hidden');
    modal.classList.remove('hidden');
    modal.style.display = 'flex';
    modal.style.pointerEvents = 'auto';
    card.getBoundingClientRect();
    overlay.style.opacity = '1';
    card.style.opacity    = '1';
    card.style.transform  = 'translateY(0)';
    document.body.style.overflow = 'hidden';
}

function closeEditModal(id) {
    const overlay = document.getElementById('editModalOverlay-' + id);
    const modal   = document.getElementById('editModal-' + id);
    const card    = document.getElementById('editModalCard-' + id);
    if (!modal) return;
    overlay.style.opacity = '0';
    card.style.opacity    = '0';
    card.style.transform  = 'translateY(20px)';
    setTimeout(() => {
        modal.style.display = 'none';
        modal.classList.add('hidden');
        overlay.classList.add('hidden');
        document.body.style.overflow = '';
    }, 250);
}

document.addEventListener('keydown', function(e) {
    if (e.key !== 'Escape') return;
    document.querySelectorAll('[id^="editModal-"]').forEach(modal => {
        if (modal.style.display === 'flex') closeEditModal(modal.id.replace('editModal-', ''));
    });
});

function _setupEditPlatformSwitcher(productId) {
    const container = document.getElementById('editPlatformBtns-' + productId);
    if (!container) return;
    const platformHidden = document.getElementById('editFilePlatform-' + productId);
    const panels = {
        upload:  document.getElementById('editPanelUpload-'  + productId),
        dropbox: document.getElementById('editPanelDropbox-' + productId),
        gdrive:  document.getElementById('editPanelGdrive-'  + productId),
        other:   document.getElementById('editPanelOther-'   + productId),
    };
    const urlInputs = {
        dropbox: document.getElementById('editUrlDropbox-' + productId),
        gdrive:  document.getElementById('editUrlGdrive-'  + productId),
        other:   document.getElementById('editUrlOther-'   + productId),
    };
    function syncUrlNames(active) {
        Object.entries(urlInputs).forEach(([key, inp]) => {
            if (!inp) return;
            if (key === active) { inp.setAttribute('name', 'file_url'); inp.disabled = false; }
            else { inp.removeAttribute('name'); inp.disabled = true; }
        });
    }
    syncUrlNames(platformHidden.value);
    container.addEventListener('click', function(e) {
        const btn = e.target.closest('.edit-platform-btn');
        if (!btn) return;
        const platform = btn.dataset.platform;
        container.querySelectorAll('.edit-platform-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        Object.entries(panels).forEach(([key, el]) => { if (el) el.classList.toggle('hidden', key !== platform); });
        platformHidden.value = platform;
        syncUrlNames(platform);
    });
}

function editValidateUrl(input, domain, productId, platformName) {
    const key  = input.id.replace('editUrl', '').replace('-' + productId, '');
    const fbEl = document.getElementById('editFeedback' + key + '-' + productId);
    if (!fbEl) return;
    const val = input.value.trim();
    if (!val) { fbEl.classList.add('hidden'); return; }
    const isValid = domain ? val.includes(domain) : val.startsWith('http');
    fbEl.classList.remove('hidden', 'valid', 'invalid');
    fbEl.classList.add(isValid ? 'valid' : 'invalid');
    fbEl.innerHTML = isValid
        ? `<svg style="width:12px;height:12px;flex-shrink:0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg> Link valid`
        : `<svg style="width:12px;height:12px;flex-shrink:0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg> Pastikan link dari ${platformName || 'URL yang valid'}`;
}

const _editUploadedFiles = {};
function editHandleFiles(event, productId) {
    if (!_editUploadedFiles[productId]) _editUploadedFiles[productId] = [];
    Array.from(event.target.files).forEach((file, i) => {
        _editUploadedFiles[productId].push({ id: Date.now() + i, name: file.name, size: file.size, file });
    });
    editRenderFileList(productId);
}
function editRenderFileList(productId) {
    const container = document.getElementById('editFileList-' + productId);
    if (!container) return;
    const files = _editUploadedFiles[productId] || [];
    container.innerHTML = '';
    files.forEach((f, index) => {
        const div = document.createElement('div');
        div.className = 'flex items-center justify-between p-2.5 bg-blue-50 rounded-lg border border-blue-200';
        div.innerHTML = `
            <div class="flex items-center gap-2 min-w-0 flex-1">
                <div class="p-1.5 bg-white rounded border border-blue-200 flex-shrink-0">
                    <svg class="w-3.5 h-3.5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
                <span class="text-xs text-gray-700 truncate">${f.name}</span>
                <span class="text-[10px] text-gray-400 bg-white px-1.5 py-0.5 rounded border flex-shrink-0">${editFormatSize(f.size)}</span>
            </div>
            <button type="button" onclick="editRemoveFile(${productId}, ${index})"
                    class="w-5 h-5 bg-red-100 hover:bg-red-200 rounded-full flex items-center justify-center ml-2 flex-shrink-0 transition-colors">
                <svg class="w-2.5 h-2.5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>`;
        container.appendChild(div);
    });
    const input = document.getElementById('editFileUpload-' + productId);
    if (input) { const dt = new DataTransfer(); files.forEach(f => dt.items.add(f.file)); input.files = dt.files; }
}
function editRemoveFile(productId, index) {
    if (_editUploadedFiles[productId]) { _editUploadedFiles[productId].splice(index, 1); editRenderFileList(productId); }
}
function editFormatSize(bytes) {
    if (bytes < 1024) return bytes + ' B';
    if (bytes < 1048576) return (bytes / 1024).toFixed(1) + ' KB';
    return (bytes / 1048576).toFixed(1) + ' MB';
}

function editToggleInput(checkbox, inputId) {
    const input = document.getElementById(inputId);
    if (!input) return;
    input.classList.toggle('hidden', !checkbox.checked);
    if (checkbox.checked) input.focus(); else input.value = '';
}

function formatRupiah(input) {
    const angka = input.value.replace(/[^0-9]/g, '');
    input.value = angka ? new Intl.NumberFormat('id-ID').format(angka) : '';
}

function editCompressImage(file) {
    const maxSizeKB = 150, maxWidth = 1024, quality = 0.75;
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
                        resolve({ file: new File([blob], file.name.replace(/\.[^.]+$/, '.jpg'), { type: 'image/jpeg', lastModified: Date.now() }), previewUrl: URL.createObjectURL(blob) });
                    }, 'image/jpeg', q);
                }
                tryCompress();
            };
            img.src = e.target.result;
        };
        reader.readAsDataURL(file);
    });
}

const _editUploadedImages = {};
function _setupEditImageCompression(productId) {
    const input     = document.getElementById('editNewImages-' + productId);
    const statusEl  = document.getElementById('editImgStatus-' + productId);
    const previewEl = document.getElementById('editImgPreview-' + productId);
    const labelText = document.getElementById('editImgUploadText-' + productId);
    if (!input) return;
    if (_editUploadedImages[productId] === undefined) _editUploadedImages[productId] = [];
    input.addEventListener('change', async function() {
        const files = Array.from(this.files).filter(f => f.type.match('image.*'));
        if (!files.length) return;
        statusEl.classList.remove('hidden');
        statusEl.innerHTML = `<div style="display:flex;align-items:center;gap:6px;color:#9ca3af;font-size:11px;"><svg style="width:12px;height:12px;animation:editSpin 1s linear infinite;flex-shrink:0;" fill="none" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" style="opacity:.3"/><path fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" style="opacity:.7"/></svg>Mengompresi gambar...</div>`;
        const results = await Promise.allSettled(files.map(f => editCompressImage(f)));
        results.forEach((r, i) => { if (r.status === 'fulfilled') _editUploadedImages[productId].push({ id: Date.now() + i, file: r.value.file, previewUrl: r.value.previewUrl }); });
        _renderEditImagePreview(productId, previewEl, labelText, statusEl, input);
        statusEl.innerHTML = `<div style="display:flex;align-items:center;gap:5px;color:#9ca3af;font-size:11px;"><span style="width:7px;height:7px;border-radius:50%;background:#2563eb;display:inline-block;flex-shrink:0;"></span>${_editUploadedImages[productId].length} gambar siap diupload</div>`;
        this.value = '';
    });
}
function _renderEditImagePreview(productId, previewEl, labelText, statusEl, input) {
    const images = _editUploadedImages[productId] || [];
    previewEl.innerHTML = '';
    images.forEach((img, idx) => {
        const div = document.createElement('div');
        div.className = 'edit-preview-img-wrap';
        div.innerHTML = `<img src="${img.previewUrl}" loading="lazy"><div class="edit-img-ready-dot"></div><div class="edit-preview-remove" data-idx="${idx}"><svg style="width:10px;height:10px;color:white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></div>`;
        previewEl.appendChild(div);
    });
    previewEl.querySelectorAll('.edit-preview-remove').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.stopPropagation();
            _editUploadedImages[productId].splice(+this.dataset.idx, 1);
            _renderEditImagePreview(productId, previewEl, labelText, statusEl, input);
            if (!_editUploadedImages[productId].length) statusEl.classList.add('hidden');
            else statusEl.innerHTML = `<div style="display:flex;align-items:center;gap:5px;color:#9ca3af;font-size:11px;"><span style="width:7px;height:7px;border-radius:50%;background:#2563eb;display:inline-block;flex-shrink:0;"></span>${_editUploadedImages[productId].length} gambar siap diupload</div>`;
        });
    });
    const dt = new DataTransfer();
    images.forEach(img => dt.items.add(img.file));
    input.files = dt.files;
    labelText.textContent = images.length ? `Tambah lagi (${images.length} dipilih)` : 'Klik untuk upload gambar baru';
}

document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('[id^="editNewImages-"]').forEach(el => {
        const productId = el.id.replace('editNewImages-', '');
        _setupEditImageCompression(productId);
        _setupEditPlatformSwitcher(productId);
    });
});
</script>