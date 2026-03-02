@foreach($products as $product)
@push('modals')

<div id="editModalOverlay-{{ $product->id }}"
     class="fixed inset-0 hidden"
     style="z-index:9990; background:rgba(15,23,42,0.5); backdrop-filter:blur(5px); -webkit-backdrop-filter:blur(5px);
            opacity:0; transition:opacity 0.2s ease;"
     onclick="closeEditModal({{ $product->id }})">
</div>

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

                    {{-- GAMBAR SAAT INI --}}
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

                    {{-- TAMBAH GAMBAR BARU --}}
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
                            {{-- input ini HANYA trigger, file asli di _editUploadedImages[] --}}
                            <input type="file" id="editNewImages-{{ $product->id }}" multiple accept="image/*" class="hidden">
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

                    {{-- INFO PRODUK --}}
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

                    {{-- FILE UNTUK PEMBELI (hanya digital) --}}
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
                        <div class="mb-4">
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Platform</p>
                            <div class="flex flex-wrap gap-2" id="editPlatformBtns-{{ $product->id }}">
                                <button type="button" class="edit-platform-btn {{ $existingPlatform === 'upload'  ? 'active' : '' }}" data-platform="upload">Upload</button>
                                <button type="button" class="edit-platform-btn {{ $existingPlatform === 'dropbox' ? 'active' : '' }}" data-platform="dropbox">Dropbox</button>
                                <button type="button" class="edit-platform-btn {{ $existingPlatform === 'gdrive'  ? 'active' : '' }}" data-platform="gdrive">G-Drive</button>
                                <button type="button" class="edit-platform-btn {{ $existingPlatform === 'other'   ? 'active' : '' }}" data-platform="other">Other</button>
                            </div>
                        </div>
                        <input type="hidden" name="file_platform" id="editFilePlatform-{{ $product->id }}" value="{{ $existingPlatform }}">
                        <div id="editPanelUpload-{{ $product->id }}" class="{{ $existingPlatform === 'upload' ? '' : 'hidden' }}">
                            @if(isset($product->files) && $product->files->where('platform', 'upload')->count())
                            <div class="mb-3">
                                <p class="text-xs font-semibold text-gray-600 mb-2">File Saat Ini</p>
                                <div class="space-y-2">
                                    @foreach($product->files->where('platform', 'upload') as $file)
                                    <div class="flex items-center justify-between p-2.5 bg-gray-50 rounded-lg border border-gray-200 group">
                                        <span class="text-xs text-gray-700 truncate">{{ basename($file->file ?? 'File') }}</span>
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
                                    <p class="text-sm text-gray-700 font-medium">Klik untuk upload file baru</p>
                                    <p class="text-xs text-gray-400 mt-1">ZIP, RAR, PDF, DOC, atau format lainnya</p>
                                </label>
                            </div>
                            <div id="editFileList-{{ $product->id }}" class="space-y-2 mt-3"></div>
                        </div>
                        <div id="editPanelDropbox-{{ $product->id }}" class="{{ $existingPlatform === 'dropbox' ? '' : 'hidden' }}">
                            <input type="url" id="editUrlDropbox-{{ $product->id }}" class="edit-input"
                                   placeholder="https://www.dropbox.com/s/..."
                                   value="{{ $existingPlatform === 'dropbox' ? $existingFileUrl : '' }}"
                                   oninput="editValidateUrl(this, 'dropbox.com', {{ $product->id }}, 'Dropbox')">
                        </div>
                        <div id="editPanelGdrive-{{ $product->id }}" class="{{ $existingPlatform === 'gdrive' ? '' : 'hidden' }}">
                            <input type="url" id="editUrlGdrive-{{ $product->id }}" class="edit-input"
                                   placeholder="https://drive.google.com/file/d/..."
                                   value="{{ $existingPlatform === 'gdrive' ? $existingFileUrl : '' }}"
                                   oninput="editValidateUrl(this, 'drive.google.com', {{ $product->id }}, 'Google Drive')">
                        </div>
                        <div id="editPanelOther-{{ $product->id }}" class="{{ $existingPlatform === 'other' ? '' : 'hidden' }}">
                            <input type="url" id="editUrlOther-{{ $product->id }}" class="edit-input"
                                   placeholder="https://..."
                                   value="{{ $existingPlatform === 'other' ? $existingFileUrl : '' }}"
                                   oninput="editValidateUrl(this, null, {{ $product->id }}, 'Other')">
                        </div>
                    </div>

                    {{-- HARGA & DISKON --}}
                    <div class="edit-card p-4 space-y-4">
                        <h3 class="text-sm font-semibold text-gray-800 mb-1">Harga & Diskon</h3>
                        <div>
                            <label class="edit-label">Harga Normal (Rp)</label>
                            <input type="text" id="editPrice-{{ $product->id }}" name="price"
                                   value="{{ number_format($product->price,0,',','.') }}"
                                   oninput="formatRupiah(this)" class="edit-input" placeholder="Contoh: 100.000">
                        </div>
                        <div>
                            <label class="edit-label">Harga Setelah Diskon <span class="text-xs font-normal text-gray-400">(opsional)</span></label>
                            <input type="text" id="editDiscount-{{ $product->id }}" name="discount"
                                   value="{{ $product->discount ? number_format($product->discount,0,',','.') : '' }}"
                                   oninput="formatRupiah(this)" class="edit-input" placeholder="Kosongkan jika tidak ada diskon">
                        </div>
                    </div>

                    {{-- ONGKOS KIRIM (hanya fisik) --}}
                    @if(($product->product_type ?? 'fisik') === 'fisik')
                    <div class="edit-card p-4">
                        <h3 class="text-sm font-semibold text-gray-800 mb-3">Ongkos Kirim</h3>
                        <div class="flex items-center justify-between mb-3">
                            <div>
                                <p class="text-sm font-medium text-gray-700">Tetapkan Ongkir</p>
                                <p class="text-xs text-gray-400 mt-0.5">Ditambahkan ke total pembayaran pembeli</p>
                            </div>
                            <div class="edit-toggle-container">
                                <input type="checkbox" id="editShippingCheck-{{ $product->id }}"
                                       class="edit-toggle-checkbox"
                                       {{ $product->shipping_cost ? 'checked' : '' }}
                                       onchange="editToggleShipping(this, {{ $product->id }})">
                                <label for="editShippingCheck-{{ $product->id }}" class="edit-toggle-label-amber"></label>
                            </div>
                        </div>
                        <input type="text" name="shipping_cost" id="editShippingInput-{{ $product->id }}"
                               class="edit-input {{ $product->shipping_cost ? '' : 'hidden' }}"
                               value="{{ $product->shipping_cost ? number_format($product->shipping_cost, 0, ',', '.') : '' }}"
                               placeholder="Contoh: 15.000"
                               oninput="formatRupiah(this)">
                    </div>
                    @endif

                    {{-- STOK & BATAS PEMBELIAN --}}
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
                <button type="button" onclick="submitEditForm({{ $product->id }})"
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
/* Toggle biru (stok & limit) */
.edit-toggle-container { position:relative; }
.edit-toggle-checkbox { display:none; }
.edit-toggle-label { display:block; width:44px; height:24px; background:#e5e7eb; border-radius:999px; position:relative; cursor:pointer; transition:background .2s ease; }
.edit-toggle-label::after { content:''; position:absolute; top:2px; left:2px; width:20px; height:20px; background:white; border-radius:50%; transition:transform .2s ease; box-shadow:0 1px 3px rgba(0,0,0,.15); }
.edit-toggle-checkbox:checked + .edit-toggle-label { background:#3b82f6; }
.edit-toggle-checkbox:checked + .edit-toggle-label::after { transform:translateX(20px); }
/* Toggle amber (ongkir) */
.edit-toggle-label-amber { display:block; width:44px; height:24px; background:#e5e7eb; border-radius:999px; position:relative; cursor:pointer; transition:background .2s ease; }
.edit-toggle-label-amber::after { content:''; position:absolute; top:2px; left:2px; width:20px; height:20px; background:white; border-radius:50%; transition:transform .2s ease; box-shadow:0 1px 3px rgba(0,0,0,.15); }
.edit-toggle-checkbox:checked + .edit-toggle-label-amber { background:#f59e0b; }
.edit-toggle-checkbox:checked + .edit-toggle-label-amber::after { transform:translateX(20px); }
.edit-platform-btn { display:inline-flex; align-items:center; gap:5px; padding:6px 14px; border-radius:999px; font-size:12px; font-weight:500; border:1.5px solid #d1d5db; color:#374151; background:white; cursor:pointer; transition:all 0.18s; }
.edit-platform-btn:hover { border-color:#3b82f6; color:#2563eb; background:#eff6ff; }
.edit-platform-btn.active { background:#2563eb; color:white; border-color:#2563eb; }
.edit-preview-img-wrap { position:relative; border-radius:8px; overflow:hidden; aspect-ratio:1; background:#f5f5f5; }
.edit-preview-img-wrap img { width:100%; height:100%; object-fit:cover; }
.edit-preview-remove { position:absolute; top:4px; right:4px; width:22px; height:22px; background:rgba(239,68,68,0.9); border-radius:50%; display:flex; align-items:center; justify-content:center; cursor:pointer; opacity:0; transition:opacity 0.2s; }
.edit-preview-img-wrap:hover .edit-preview-remove { opacity:1; }
.edit-img-ready-dot { position:absolute; bottom:4px; right:4px; width:7px; height:7px; border-radius:50%; background:#2563eb; border:1.5px solid white; }
@keyframes editSpin { from { transform:rotate(0deg); } to { transform:rotate(360deg); } }
</style>

<script>
// ===== MODAL OPEN/CLOSE =====
function openEditModal(id) {
    const overlay = document.getElementById('editModalOverlay-' + id);
    const modal   = document.getElementById('editModal-' + id);
    const card    = document.getElementById('editModalCard-' + id);
    if (!modal) return;
    overlay.classList.remove('hidden');
    modal.classList.remove('hidden');
    modal.style.display = 'flex';
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

// ===== SUBMIT DENGAN FORMDATA MANUAL (fix gambar + ongkir) =====
async function submitEditForm(productId) {
    const form = document.getElementById('editForm-' + productId);
    if (!form) return;

    // Bersihkan format titik dari harga
    const priceEl    = document.getElementById('editPrice-'        + productId);
    const discountEl = document.getElementById('editDiscount-'     + productId);
    const shippingEl = document.getElementById('editShippingInput-' + productId);

    const cleanNum = (el) => {
        if (!el) return '';
        const n = parseInt((el.value || '').replace(/\./g, ''), 10);
        return isNaN(n) ? '' : n;
    };

    if (priceEl)    priceEl.value    = cleanNum(priceEl);
    if (discountEl) discountEl.value = cleanNum(discountEl) || '';
    if (shippingEl) shippingEl.value = cleanNum(shippingEl) || '';

    // Bangun FormData dari form (sudah include semua field termasuk shipping_cost)
    const fd = new FormData(form);

    // Tambah gambar baru dari array JS (BUKAN dari input file)
    const images = _editUploadedImages[productId] || [];
    // Hapus images[] yang mungkin sudah ada di FormData
    fd.delete('images[]');
    images.forEach(img => {
        fd.append('images[]', img.file, img.file.name);
    });

    // Disable tombol
    const saveBtn = form.closest('.bg-white').querySelector('[onclick^="submitEditForm"]');
    if (saveBtn) { saveBtn.disabled = true; saveBtn.style.opacity = '0.7'; }

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
            window.location.href = res.url || window.location.href;
        } else {
            const text = await res.text();
            document.open(); document.write(text); document.close();
        }
    } catch(err) {
        // Fallback: submit form biasa
        form.submit();
    }
}

// ===== TOGGLE HELPERS =====
function editToggleInput(checkbox, inputId) {
    const input = document.getElementById(inputId);
    if (!input) return;
    input.classList.toggle('hidden', !checkbox.checked);
    if (checkbox.checked) input.focus(); else input.value = '';
}
function editToggleShipping(cb, productId) {
    const inp = document.getElementById('editShippingInput-' + productId);
    if (!inp) return;
    inp.classList.toggle('hidden', !cb.checked);
    if (cb.checked) inp.focus(); else inp.value = '';
}

// ===== FORMAT RUPIAH =====
function formatRupiah(input) {
    const angka = input.value.replace(/[^0-9]/g, '');
    input.value = angka ? new Intl.NumberFormat('id-ID').format(angka) : '';
}

// ===== PLATFORM SWITCHER =====
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
    const val = input.value.trim();
    if (!val) return;
    const isValid = domain ? val.includes(domain) : val.startsWith('http');
    if (!isValid) input.style.borderColor = '#fca5a5';
    else input.style.borderColor = '#86efac';
}

// ===== FILE LIST =====
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
        div.innerHTML = `<span class="text-xs text-gray-700 truncate">${f.name}</span>
            <button type="button" onclick="editRemoveFile(${productId}, ${index})"
                    class="w-5 h-5 bg-red-100 hover:bg-red-200 rounded-full flex items-center justify-center ml-2 flex-shrink-0">
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

// ===== IMAGE COMPRESSION =====
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
        statusEl.innerHTML = `<div style="display:flex;align-items:center;gap:6px;color:#9ca3af;font-size:11px;"><svg style="width:12px;height:12px;animation:editSpin 1s linear infinite;flex-shrink:0;" fill="none" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" style="opacity:.3"/><path fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" style="opacity:.7"/></svg>Mengompresi...</div>`;
        const results = await Promise.allSettled(files.map(f => editCompressImage(f)));
        results.forEach((r, i) => { if (r.status === 'fulfilled') _editUploadedImages[productId].push({ id: Date.now() + i, file: r.value.file, previewUrl: r.value.previewUrl }); });
        _renderEditImagePreview(productId, previewEl, labelText, statusEl);
        statusEl.innerHTML = `<div style="display:flex;align-items:center;gap:5px;color:#9ca3af;font-size:11px;"><span style="width:7px;height:7px;border-radius:50%;background:#2563eb;display:inline-block;flex-shrink:0;"></span>${_editUploadedImages[productId].length} gambar siap diupload</div>`;
        this.value = '';
    });
}
function _renderEditImagePreview(productId, previewEl, labelText, statusEl) {
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
            _renderEditImagePreview(productId, previewEl, labelText, statusEl);
            if (!_editUploadedImages[productId].length) statusEl.classList.add('hidden');
        });
    });
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