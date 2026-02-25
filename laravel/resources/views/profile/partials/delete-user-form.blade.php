<section class="space-y-4">
    <header>
        <h2 class="text-lg font-semibold text-gray-900">Hapus Akun</h2>
        <p class="mt-1 text-sm text-gray-500">
            Jika akun dihapus, seluruh data dan informasi akan dihapus secara permanen dan tidak dapat dipulihkan. Pastikan untuk mencadangkan data penting sebelum melanjutkan.
    </header>

    <button type="button" onclick="openDeleteModal()"
        class="inline-flex items-center gap-2 px-4 py-2 bg-red-500 hover:bg-red-600
               text-white text-sm font-semibold rounded-lg shadow-sm shadow-red-200
               transition-all duration-150 active:scale-95">
        <i class="fa-solid fa-trash-can text-xs"></i>
        Hapus Akun
    </button>
</section>

{{-- BACKDROP --}}
<div id="deleteModalBackdrop"
     class="fixed inset-0 z-[199] hidden"
     style="background: rgba(15,23,42,0.5); backdrop-filter: blur(6px); -webkit-backdrop-filter: blur(6px); opacity: 0; transition: opacity 0.2s ease;"
     onclick="closeDeleteModal()">
</div>

{{-- MODAL --}}
<div id="deleteModal"
     class="fixed inset-0 z-[200] hidden items-center justify-center p-4 pointer-events-none">

    <div id="deleteModalCard"
         class="bg-white w-full max-w-md rounded-2xl pointer-events-auto"
         style="opacity: 0; transform: translateY(20px); transition: opacity 0.2s ease, transform 0.2s ease;
                box-shadow: 0 20px 60px -10px rgba(0,0,0,0.25), 0 0 0 1px rgba(0,0,0,0.05);">

        {{-- Header --}}
        <div class="px-6 pt-6 pb-5 flex items-start gap-4">
            <div class="flex-shrink-0 w-11 h-11 rounded-xl flex items-center justify-center"
                 style="background: linear-gradient(135deg, #fee2e2, #fecaca);">
                <i class="fa-solid fa-trash-can text-red-500" style="font-size:15px;"></i>
            </div>
            <div class="flex-1 min-w-0">
                <div class="flex items-center justify-between">
                    <h3 class="text-[15px] font-bold text-gray-900">Hapus Akun Secara Permanen</h3>
                    <button type="button" onclick="closeDeleteModal()"
                        class="ml-3 flex-shrink-0 w-7 h-7 flex items-center justify-center rounded-lg
                               text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-all duration-150">
                        <i class="fas fa-times text-xs"></i>
                    </button>
                </div>
                <p class="mt-1.5 text-sm text-gray-500 leading-relaxed">
                    Tindakan ini <span class="font-semibold text-gray-700">tidak dapat dibatalkan</span>.
                    Semua data, riwayat, dan pengaturan Anda akan hilang selamanya.
                </p>
            </div>
        </div>

        {{-- Warning banner --}}
        <div class="mx-6 mb-5 px-4 py-3 rounded-xl flex items-center gap-3"
             style="background: #fff7ed; border: 1px solid #fed7aa;">
            <i class="fa-solid fa-triangle-exclamation text-orange-400 text-xs flex-shrink-0"></i>
            <p class="text-xs text-orange-700 leading-relaxed">
                Akun yang dihapus <strong>tidak dapat dipulihkan</strong>.
            </p>
        </div>

        <form method="post" action="{{ route('profile.destroy') }}">
            @csrf
            @method('delete')

            <div class="px-6 pb-2">
                <label for="del_password" class="block text-xs font-semibold text-gray-500 uppercase tracking-widest mb-2">
                    Konfirmasi dengan Password
                </label>
                <div class="relative">
                    <input id="del_password"
                        name="password"
                        type="password"
                        autocomplete="current-password"
                        placeholder="Masukkan password Anda"
                        class="w-full px-4 py-3 pr-11 rounded-xl border text-sm text-gray-900
                               placeholder-gray-400 transition-all duration-150
                               focus:outline-none focus:ring-2 focus:ring-red-400/25 focus:border-red-400
                               @error('password', 'userDeletion') border-red-300 bg-red-50 @else border-gray-200 bg-gray-50 focus:bg-white @enderror"
                        style="font-size: 13.5px;">
                    <button type="button" onclick="toggleDelPass()" tabindex="-1"
                        class="absolute inset-y-0 right-0 flex items-center px-3.5 text-gray-400 hover:text-gray-600 transition-colors">
                        <i id="delPassIcon" class="fa-solid fa-eye text-xs"></i>
                    </button>
                </div>

                @if ($errors->userDeletion->has('password'))
                    <div class="mt-2 flex items-center gap-2 px-3 py-2 rounded-lg bg-red-50 border border-red-100">
                        <i class="fa-solid fa-circle-exclamation text-red-400 text-xs flex-shrink-0"></i>
                        <p class="text-xs text-red-600">{{ $errors->userDeletion->first('password') }}</p>
                    </div>
                @endif
            </div>

            {{-- Footer --}}
            <div class="px-6 py-5 flex gap-3">
                <button type="button" onclick="closeDeleteModal()"
                    class="flex-1 py-2.5 text-sm font-medium text-gray-600 bg-white border border-gray-200
                           rounded-xl hover:bg-gray-50 hover:border-gray-300 transition-all duration-150">
                    Batal
                </button>
                <button type="submit"
                    class="flex-1 py-2.5 text-sm font-semibold text-white rounded-xl transition-all duration-150 active:scale-95"
                    style="background: linear-gradient(135deg, #ef4444, #dc2626);
                           box-shadow: 0 4px 14px -2px rgba(220,38,38,0.4);">
                    <i class="fa-solid fa-trash-can text-xs mr-1.5"></i>
                    Hapus Akun
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openDeleteModal() {
    const backdrop = document.getElementById('deleteModalBackdrop');
    const modal    = document.getElementById('deleteModal');
    const card     = document.getElementById('deleteModalCard');

    // Tampilkan elemen dulu
    backdrop.classList.remove('hidden');
    modal.classList.remove('hidden');
    modal.style.display = 'flex';

    // Force reflow agar transisi berjalan dari nilai awal
    card.getBoundingClientRect();

    // Animasi masuk
    backdrop.style.opacity       = '1';
    card.style.opacity           = '1';
    card.style.transform         = 'translateY(0)';

    document.body.style.overflow = 'hidden';
    setTimeout(() => document.getElementById('del_password').focus(), 200);
}

function closeDeleteModal() {
    const backdrop = document.getElementById('deleteModalBackdrop');
    const modal    = document.getElementById('deleteModal');
    const card     = document.getElementById('deleteModalCard');

    // Animasi keluar
    backdrop.style.opacity = '0';
    card.style.opacity     = '0';
    card.style.transform   = 'translateY(20px)';

    setTimeout(() => {
        modal.style.display = 'none';
        modal.classList.add('hidden');
        backdrop.classList.add('hidden');
        document.body.style.overflow = '';
    }, 200);
}

function toggleDelPass() {
    const input = document.getElementById('del_password');
    const icon  = document.getElementById('delPassIcon');
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.replace('fa-eye', 'fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.replace('fa-eye-slash', 'fa-eye');
    }
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeDeleteModal();
});

@if ($errors->userDeletion->isNotEmpty())
    document.addEventListener('DOMContentLoaded', () => openDeleteModal());
@endif
</script>