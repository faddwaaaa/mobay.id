@foreach($products as $product)

<div id="editModal-{{ $product->id }}"
     class="fixed inset-0 z-[9999] hidden edit-modal-overlay">

    {{-- BACKDROP --}}
    <div class="absolute inset-0 backdrop-blur-sm bg-black/40"
         onclick="closeEditModal({{ $product->id }})"></div>

    {{-- MODAL WRAPPER --}}
    <div class="relative flex items-center justify-center min-h-screen p-4">

        {{-- MODAL BOX --}}
        <div class="edit-modal-box bg-white w-full max-w-2xl rounded-2xl shadow-2xl
                    max-h-[92vh] flex flex-col">

            {{-- HEADER --}}
            <div class="edit-modal-header flex items-center justify-between p-5 border-b border-gray-100">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-blue-50 rounded-lg">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-gray-800">Edit Produk</h2>
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

            {{-- BODY (scrollable) --}}
            <div class="p-5 overflow-y-auto flex-1">

                <form id="editForm-{{ $product->id }}"
                      method="POST"
                      action="{{ route('products.update', $product->id) }}"
                      enctype="multipart/form-data">

                    @csrf
                    @method('PUT')

                    <div class="space-y-5">

                        {{-- ===== GAMBAR LAMA ===== --}}
                        <div class="edit-card p-4">
                            <div class="flex items-center justify-between mb-3">
                                <div>
                                    <h3 class="text-sm font-semibold text-gray-800">Gambar Saat Ini</h3>
                                    <p class="text-xs text-gray-500 mt-0.5">Centang gambar yang ingin dihapus</p>
                                </div>
                                <div class="p-2 bg-orange-50 rounded-lg">
                                    <svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                            </div>

                            @if($product->images->count())
                            <div class="grid grid-cols-3 sm:grid-cols-4 gap-2">
                                @foreach($product->images as $img)
                                <label class="edit-img-item group cursor-pointer block relative rounded-xl overflow-hidden border-2 border-transparent hover:border-red-400 transition-all">
                                    <input type="checkbox"
                                           name="delete_images[]"
                                           value="{{ $img->id }}"
                                           class="hidden peer">
                                    <img src="{{ asset('storage/'.$img->image) }}"
                                         class="h-20 w-full object-cover">
                                    {{-- overlay when checked --}}
                                    <div class="absolute inset-0 bg-red-500/0 peer-checked:bg-red-500/40 transition-all flex items-center justify-center">
                                        <div class="opacity-0 peer-checked:opacity-100 bg-white rounded-full p-1 transition-all scale-50 peer-checked:scale-100">
                                            <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </div>
                                    </div>
                                    {{-- badge --}}
                                    <div class="absolute top-1.5 left-1.5 bg-white/90 rounded px-1.5 py-0.5 text-[10px] font-medium text-gray-600 shadow-sm opacity-0 group-hover:opacity-100 transition-opacity">
                                        Hapus
                                    </div>
                                </label>
                                @endforeach
                            </div>
                            @else
                            <div class="text-center py-4 text-sm text-gray-400">Tidak ada gambar</div>
                            @endif
                        </div>

                        {{-- ===== TAMBAH GAMBAR BARU ===== --}}
                        <div class="edit-card p-4">
                            <div class="flex items-center justify-between mb-3">
                                <div>
                                    <h3 class="text-sm font-semibold text-gray-800">Tambah Gambar Baru</h3>
                                    <p class="text-xs text-gray-500 mt-0.5">PNG, JPG, JPEG (Max 5MB per gambar)</p>
                                </div>
                                <div class="p-2 bg-blue-50 rounded-lg">
                                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                    </svg>
                                </div>
                            </div>

                            <div class="border-2 border-dashed border-gray-200 rounded-xl p-4 text-center hover:border-blue-400 transition-colors cursor-pointer">
                                <input type="file"
                                       id="editNewImages-{{ $product->id }}"
                                       name="new_images[]"
                                       multiple
                                       accept="image/*"
                                       class="hidden"
                                       onchange="previewNewImages(event, {{ $product->id }})">
                                <label for="editNewImages-{{ $product->id }}" class="cursor-pointer block">
                                    <div class="mx-auto w-10 h-10 bg-blue-50 rounded-full flex items-center justify-center mb-2">
                                        <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                        </svg>
                                    </div>
                                    <p class="text-sm text-gray-700 font-medium">Klik untuk upload gambar baru</p>
                                </label>
                            </div>

                            <div id="preview-new-images-{{ $product->id }}"
                                 class="grid grid-cols-3 sm:grid-cols-4 gap-2 mt-3"></div>
                        </div>

                        {{-- ===== INFO PRODUK ===== --}}
                        <div class="edit-card p-4 space-y-4">
                            <h3 class="text-sm font-semibold text-gray-800 mb-1">Informasi Produk</h3>

                            {{-- Judul --}}
                            <div>
                                <label class="edit-label">Judul Produk</label>
                                <input type="text"
                                       name="title"
                                       value="{{ $product->title }}"
                                       class="edit-input"
                                       placeholder="Judul produk Anda">
                            </div>

                            {{-- Deskripsi --}}
                            <div>
                                <label class="edit-label">Deskripsi Produk</label>
                                <textarea name="description"
                                          rows="4"
                                          class="edit-textarea"
                                          placeholder="Deskripsikan produk Anda...">{{ $product->description }}</textarea>
                            </div>
                        </div>

                        {{-- ===== HARGA & DISKON ===== --}}
                        <div class="edit-card p-4 space-y-4">
                            <h3 class="text-sm font-semibold text-gray-800 mb-1">Harga & Diskon</h3>

                            {{-- Harga --}}
                            <div>
                                <label class="edit-label">Harga Normal (Rp)</label>
                                <input type="text"
                                       id="price-{{ $product->id }}"
                                       name="price"
                                       value="{{ number_format($product->price,0,',','.') }}"
                                       oninput="formatRupiah(this)"
                                       class="edit-input"
                                       placeholder="Contoh: 100.000">
                                <p class="text-xs text-gray-400 mt-1">Harga akan otomatis diformat: Rp 100.000</p>
                            </div>

                            {{-- Diskon --}}
                            <div>
                                <label class="edit-label">
                                    Harga Setelah Diskon
                                    <span class="text-xs font-normal text-gray-400">(opsional)</span>
                                </label>
                                <input type="text"
                                       id="discount-{{ $product->id }}"
                                       name="discount"
                                       value="{{ $product->discount ? number_format($product->discount,0,',','.') : '' }}"
                                       oninput="formatRupiah(this)"
                                       class="edit-input"
                                       placeholder="Kosongkan jika tidak ada diskon">
                                <p class="text-xs text-blue-500 mt-1 flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                    </svg>
                                    Masukkan harga setelah diskon, bukan persentase
                                </p>
                            </div>
                        </div>

                        {{-- ===== KELOLA STOK & BATAS PEMBELIAN ===== --}}
                        <div class="edit-card p-4 space-y-1">
                            <h3 class="text-sm font-semibold text-gray-800 mb-3">Stok & Batas Pembelian</h3>

                            {{-- Kelola Stok --}}
                            <div class="py-3 border-b border-gray-100">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm font-medium text-gray-700">Kelola Stok</p>
                                        <p class="text-xs text-gray-400 mt-0.5">Aktifkan untuk mengatur jumlah stok</p>
                                    </div>
                                    <div class="edit-toggle-container">
                                        <input type="checkbox"
                                               name="stock_toggle"
                                               id="editStockCheck-{{ $product->id }}"
                                               class="edit-toggle-checkbox"
                                               {{ $product->stock !== null ? 'checked' : '' }}
                                               onchange="editToggleInput(this, 'editStockInput-{{ $product->id }}')">
                                        <label for="editStockCheck-{{ $product->id }}" class="edit-toggle-label"></label>
                                    </div>
                                </div>
                                <input type="number"
                                       name="stock"
                                       id="editStockInput-{{ $product->id }}"
                                       class="edit-input mt-3 {{ $product->stock !== null ? '' : 'hidden' }}"
                                       value="{{ $product->stock }}"
                                       placeholder="Jumlah stok tersedia"
                                       min="1">
                            </div>

                            {{-- Batas Pembelian --}}
                            <div class="py-3">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm font-medium text-gray-700">Batas Pembelian</p>
                                        <p class="text-xs text-gray-400 mt-0.5">Batasi pembelian per pengguna</p>
                                    </div>
                                    <div class="edit-toggle-container">
                                        <input type="checkbox"
                                               name="limit_toggle"
                                               id="editLimitCheck-{{ $product->id }}"
                                               class="edit-toggle-checkbox"
                                               {{ $product->purchase_limit !== null ? 'checked' : '' }}
                                               onchange="editToggleInput(this, 'editLimitInput-{{ $product->id }}')">
                                        <label for="editLimitCheck-{{ $product->id }}" class="edit-toggle-label"></label>
                                    </div>
                                </div>
                                <input type="number"
                                       name="purchase_limit"
                                       id="editLimitInput-{{ $product->id }}"
                                       class="edit-input mt-3 {{ $product->purchase_limit !== null ? '' : 'hidden' }}"
                                       value="{{ $product->purchase_limit }}"
                                       placeholder="Maksimal beli per user"
                                       min="1">
                            </div>
                        </div>

                    </div>{{-- end space-y-5 --}}

                </form>
            </div>{{-- END BODY --}}

            {{-- FOOTER --}}
            <div class="p-5 border-t border-gray-100 flex items-center justify-between gap-3 bg-gray-50/60 rounded-b-2xl">
                <p class="text-xs text-gray-400 flex items-center gap-1">
                    <svg class="w-3.5 h-3.5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    Pastikan data sudah benar sebelum menyimpan
                </p>
                <div class="flex gap-2">
                    <button type="button"
                            onclick="closeEditModal({{ $product->id }})"
                            class="px-4 py-2 text-sm font-medium bg-white border border-gray-200 rounded-lg hover:bg-gray-50 text-gray-600 transition-colors">
                        Batal
                    </button>
                    <button type="submit"
                            form="editForm-{{ $product->id }}"
                            class="px-5 py-2 text-sm font-semibold bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white rounded-lg shadow-sm hover:shadow-md transform hover:-translate-y-0.5 transition-all duration-200 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Simpan Perubahan
                    </button>
                </div>
            </div>

        </div>
    </div>
</div>

@endforeach

<style>
/* ===== MODAL ANIMATION ===== */
@keyframes editModalIn {
    from { opacity: 0; transform: scale(.96) translateY(8px); }
    to   { opacity: 1; transform: scale(1) translateY(0); }
}

.edit-modal-box {
    animation: editModalIn .22s cubic-bezier(.16,1,.3,1);
}

/* ===== CARD ===== */
.edit-card {
    background: white;
    border-radius: 12px;
    border: 1.5px solid rgba(229, 231, 235, 0.7);
    box-shadow: 0 1px 4px rgba(0,0,0,0.04);
    transition: box-shadow .2s;
}

.edit-card:hover {
    box-shadow: 0 3px 10px rgba(0,0,0,0.07);
}

/* ===== FORM ELEMENTS ===== */
.edit-label {
    display: block;
    font-size: 12px;
    font-weight: 600;
    color: #374151;
    margin-bottom: 5px;
}

.edit-input {
    width: 100%;
    padding: 9px 12px;
    border: 1.5px solid #e5e7eb;
    border-radius: 8px;
    font-size: 13px;
    transition: all .2s;
    background: white;
    color: #1f2937;
}

.edit-input:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59,130,246,.1);
}

.edit-textarea {
    width: 100%;
    padding: 9px 12px;
    border: 1.5px solid #e5e7eb;
    border-radius: 8px;
    font-size: 13px;
    transition: all .2s;
    resize: vertical;
    min-height: 90px;
    background: white;
    color: #1f2937;
}

.edit-textarea:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59,130,246,.1);
}

/* ===== OLD IMAGE ITEMS: use peer for the checkbox trick ===== */
.edit-img-item:has(input:checked) {
    border-color: #ef4444 !important;
    opacity: .75;
}

.edit-img-item:has(input:checked) > div {
    background-color: rgba(239,68,68,.35);
}

.edit-img-item:has(input:checked) > div > div {
    opacity: 1;
    transform: scale(1);
}

/* ===== TOGGLE ===== */
.edit-toggle-container { position: relative; }

.edit-toggle-checkbox { display: none; }

.edit-toggle-label {
    display: block;
    width: 44px;
    height: 24px;
    background: #e5e7eb;
    border-radius: 999px;
    position: relative;
    cursor: pointer;
    transition: background .2s ease;
}

.edit-toggle-label::after {
    content: '';
    position: absolute;
    top: 2px;
    left: 2px;
    width: 20px;
    height: 20px;
    background: white;
    border-radius: 50%;
    transition: transform .2s ease;
    box-shadow: 0 1px 3px rgba(0,0,0,.15);
}

.edit-toggle-checkbox:checked + .edit-toggle-label {
    background: #3b82f6;
}

.edit-toggle-checkbox:checked + .edit-toggle-label::after {
    transform: translateX(20px);
}

/* scrollbar */
.edit-modal-box ::-webkit-scrollbar { width: 4px; }
.edit-modal-box ::-webkit-scrollbar-thumb { background: #d1d5db; border-radius: 4px; }
</style>

<script>
// ================= TOGGLE STOCK/LIMIT =================
function editToggleInput(checkbox, inputId) {
    const input = document.getElementById(inputId);
    if (!input) return;
    if (checkbox.checked) {
        input.classList.remove('hidden');
        input.focus();
    } else {
        input.classList.add('hidden');
        input.value = '';
    }
}

// ================= OPEN / CLOSE =================
function openEditModal(id) {
    const modal = document.getElementById('editModal-' + id);
    if (modal) {
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }
}

function closeEditModal(id) {
    const modal = document.getElementById('editModal-' + id);
    if (modal) {
        modal.classList.add('hidden');
        document.body.style.overflow = '';
    }
}

// Close on Escape
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        document.querySelectorAll('[id^="editModal-"]').forEach(modal => {
            if (!modal.classList.contains('hidden')) {
                const id = modal.id.replace('editModal-', '');
                closeEditModal(id);
            }
        });
    }
});

// ================= PREVIEW IMAGE =================
function previewNewImages(event, productId) {
    const container = document.getElementById('preview-new-images-' + productId);
    container.innerHTML = '';

    const files = event.target.files;
    if (!files.length) return;

    Array.from(files).forEach((file, index) => {
        if (!file.type.startsWith('image/')) return;

        const reader = new FileReader();
        reader.onload = function(e) {
            const div = document.createElement('div');
            div.className = 'relative group rounded-xl overflow-hidden border-2 border-gray-100 hover:border-blue-300 transition-all';
            div.style.aspectRatio = '1';

            div.innerHTML = `
                <img src="${e.target.result}"
                     class="w-full h-full object-cover">
                <button type="button"
                    onclick="removePreviewImage(this, ${index}, ${productId})"
                    class="absolute top-1.5 right-1.5 w-6 h-6 bg-red-500 hover:bg-red-600 rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all shadow-sm">
                    <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            `;

            container.appendChild(div);
        };
        reader.readAsDataURL(file);
    });
}

// ================= REMOVE PREVIEW =================
function removePreviewImage(btn, index, productId) {
    btn.parentElement.remove();

    const input = document.querySelector(`#editModal-${productId} input[name="new_images[]"]`);
    const dt = new DataTransfer();

    Array.from(input.files).forEach((file, i) => {
        if (i !== index) dt.items.add(file);
    });

    input.files = dt.files;
}

// ================= FORMAT RUPIAH =================
function formatRupiah(input) {
    let angka = input.value.replace(/[^0-9]/g, '');
    if (!angka) { input.value = ''; return; }
    input.value = new Intl.NumberFormat('id-ID').format(angka);
}
</script>
