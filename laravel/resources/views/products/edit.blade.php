@foreach($products as $product)

<div id="editModal-{{ $product->id }}"
     class="fixed inset-0 z-[9999] hidden">

    {{-- BACKDROP --}}
    <div class="absolute inset-0 backdrop-blur-md bg-black/30"
         onclick="closeEditModal({{ $product->id }})"></div>

    {{-- MODAL BOX --}}
    <div class="relative flex items-center justify-center min-h-screen p-4">

        <div class="bg-white w-full max-w-md rounded-2xl shadow-2xl p-6 animate-fadeIn">

            <h2 class="text-lg font-semibold mb-4">Edit Produk</h2>

            <form method="POST"
                  action="{{ route('products.update', $product->id) }}"
                  enctype="multipart/form-data">

                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label class="text-sm">Gambar</label>
                    <input type="file" name="image"
                           class="w-full mt-1 border rounded p-2 text-sm">
                </div>

                <div class="mb-3">
                    <label class="text-sm">Judul</label>
                    <input type="text" name="title"
                           value="{{ $product->title }}"
                           class="w-full mt-1 border rounded p-2 text-sm">
                </div>

                <div class="mb-3">
                    <label class="text-sm">Deskripsi</label>
                    <textarea name="description"
                        class="w-full mt-1 border rounded p-2 text-sm">{{ $product->description }}</textarea>
                </div>

                <div class="mb-3">
                    <label class="text-sm">Harga</label>
                    <input type="number" name="price"
                           value="{{ $product->price }}"
                           class="w-full mt-1 border rounded p-2 text-sm">
                </div>

                <div class="flex justify-end gap-2 mt-5">
                    <button type="button"
                        onclick="closeEditModal({{ $product->id }})"
                        class="px-4 py-2 text-sm bg-gray-200 rounded-lg">
                        Batal
                    </button>

                    <button type="submit"
                        class="px-4 py-2 text-sm bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        Simpan
                    </button>
                </div>

            </form>
        </div>

    </div>
</div>
<style>
@keyframes fadeIn {
    from { opacity: 0; transform: scale(.95); }
    to { opacity: 1; transform: scale(1); }
}
.animate-fadeIn {
    animation: fadeIn .2s ease-out;
}
</style>

@endforeach
