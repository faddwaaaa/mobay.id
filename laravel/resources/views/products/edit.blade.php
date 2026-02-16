@foreach($products as $product)

<div id="editModal-{{ $product->id }}"
     class="fixed inset-0 z-[9999] hidden">

    {{-- BACKDROP --}}
    <div class="absolute inset-0 backdrop-blur-md bg-black/30"
         onclick="closeEditModal({{ $product->id }})"></div>

    {{-- MODAL WRAPPER --}}
    <div class="relative flex items-center justify-center min-h-screen p-4">

        {{-- MODAL BOX --}}
        <div class="bg-white w-full max-w-md rounded-2xl shadow-2xl
                    max-h-[90vh] flex flex-col animate-fadeIn">

            {{-- HEADER --}}
            <div class="p-5 border-b">
                <h2 class="text-lg font-semibold">Edit Produk</h2>
            </div>

            {{-- BODY --}}
            <div class="p-5 overflow-y-auto flex-1">

                <form method="POST"
                      action="{{ route('products.update', $product->id) }}"
                      enctype="multipart/form-data">

                    @csrf
                    @method('PUT')

                    {{-- GAMBAR LAMA --}}
                    <div class="mb-4">
                        <label class="text-sm font-medium">Gambar Lama</label>

                        <div class="grid grid-cols-3 gap-2 mt-2">
                            @foreach($product->images as $img)
                            <div class="relative group">
                                <img src="{{ asset('storage/'.$img->image) }}"
                                     class="rounded-lg h-24 w-full object-cover">

                                <label class="absolute top-1 right-1 bg-white rounded px-1 text-xs shadow">
                                    <input type="checkbox"
                                           name="delete_images[]"
                                           value="{{ $img->id }}">
                                    Hapus
                                </label>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- TAMBAH GAMBAR BARU --}}
                    <div class="mb-4">
                        <label class="text-sm font-medium">Tambah Gambar Baru</label>

                        <input type="file"
                               name="new_images[]"
                               multiple
                               accept="image/*"
                               onchange="previewNewImages(event, {{ $product->id }})"
                               class="w-full mt-1 border rounded p-2 text-sm">

                        <div id="preview-new-images-{{ $product->id }}"
                             class="grid grid-cols-3 gap-2 mt-3"></div>
                    </div>

                    {{-- JUDUL --}}
                    <div class="mb-4">
                        <label class="text-sm font-medium">Judul</label>
                        <input type="text"
                               name="title"
                               value="{{ $product->title }}"
                               class="w-full mt-1 border rounded p-2 text-sm">
                    </div>

                    {{-- DESKRIPSI --}}
                    <div class="mb-4">
                        <label class="text-sm font-medium">Deskripsi</label>
                        <textarea name="description"
                                  class="w-full mt-1 border rounded p-2 text-sm">{{ $product->description }}</textarea>
                    </div>

                    {{-- HARGA --}}
                    <div class="mb-4">
                        <label class="text-sm font-medium">Harga</label>
                        <input type="text"
                               id="price-{{ $product->id }}"
                               name="price"
                               value="{{ number_format($product->price,0,',','.') }}"
                               oninput="formatRupiah(this)"
                               class="w-full mt-1 border rounded p-2 text-sm">
                    </div>

                    {{-- DISKON --}}
                    <div class="mb-4">
                        <label class="text-sm font-medium">Diskon</label>
                        <input type="text"
                               id="discount-{{ $product->id }}"
                               name="discount"
                               value="{{ number_format($product->discount ?? 0,0,',','.') }}"
                               oninput="formatRupiah(this)"
                               class="w-full mt-1 border rounded p-2 text-sm"
                               placeholder="Kosongkan jika tidak ada diskon">
                    </div>

            </div> {{-- END BODY --}}

            {{-- FOOTER --}}
            <div class="p-5 border-t flex justify-end gap-2">

                <button type="button"
                        onclick="closeEditModal({{ $product->id }})"
                        class="px-4 py-2 text-sm bg-gray-200 rounded-lg">
                    Batal
                </button>

                <button type="submit"
                        class="px-4 py-2 text-sm bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    Simpan
                </button>

                </form>

            </div>

        </div>
    </div>
</div>

@endforeach

<style>
    @keyframes fadeIn {
    from { opacity: 0; transform: scale(.95); }
    to { opacity: 1; transform: scale(1); }
}
.animate-fadeIn {
    animation: fadeIn .2s ease-out;
}
</style>

<script>

// ================= PREVIEW IMAGE =================
function previewNewImages(event, productId){

    const container = document.getElementById(
        'preview-new-images-' + productId
    );

    container.innerHTML = "";

    const files = event.target.files;

    if (!files.length) return;

    Array.from(files).forEach((file, index) => {

        if (!file.type.startsWith("image/")) return;

        const reader = new FileReader();

        reader.onload = function(e){

            const div = document.createElement("div");
            div.className = "relative group";

            div.innerHTML = `
                <img src="${e.target.result}"
                     class="rounded-lg h-24 w-full object-cover">

                <button type="button"
                    onclick="removePreviewImage(this, ${index}, ${productId})"
                    class="absolute top-1 right-1 bg-white text-xs px-2 py-1 rounded shadow">
                    X
                </button>
            `;

            container.appendChild(div);
        };

        reader.readAsDataURL(file);

    });

}


// ================= REMOVE PREVIEW =================
function removePreviewImage(btn, index, productId){

    btn.parentElement.remove();

    const input = document.querySelector(
        `#editModal-${productId} input[name="new_images[]"]`
    );

    const dt = new DataTransfer();

    Array.from(input.files).forEach((file, i)=>{
        if(i !== index) dt.items.add(file);
    });

    input.files = dt.files;
}


// ================= FORMAT RUPIAH =================
function formatRupiah(input){

    let angka = input.value.replace(/[^0-9]/g,'');

    if(!angka){
        input.value = '';
        return;
    }

    input.value = new Intl.NumberFormat('id-ID').format(angka);
}

</script>
