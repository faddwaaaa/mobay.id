<section class="-mt-12">
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            Informasi Profil
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            Perbarui informasi profil akun dan alamat email Anda.
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6" enctype="multipart/form-data">
        @csrf
        @method('patch')

        <div>
            <x-input-label for="name" value="Nama Lengkap" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full"
                :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div class="mt-4">
            <x-input-label for="username" value="Username" />
            <x-text-input id="username" name="username" type="text" class="mt-1 block w-full"
                :value="old('username', $user->username)" required />
            <x-input-error class="mt-2" :messages="$errors->get('username')" />
        </div>

        <div>
            <x-input-label for="email" value="Email" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full"
                :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 text-gray-800">
                        Email Anda belum diverifikasi.

                        <button form="send-verification"
                            class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Klik di sini untuk mengirim ulang email verifikasi.
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-green-600">
                            Link verifikasi baru telah dikirim ke email Anda.
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="relative w-20 h-20">
            <!-- FOTO -->
            @if ($user->avatar)
                <img id="avatarPreview"
                    src="{{ asset('storage/'.$user->avatar) }}"
                    class="w-20 h-20 rounded-full object-cover">
            @else
                <img id="avatarPreview"
                    src="{{ asset('img/default-avatar.jpg') }}"
                    class="w-20 h-20 rounded-full object-cover">
            @endif

            <!-- INPUT FILE -->
            <input type="file"
                id="avatarInput"
                name="avatar"
                accept="image/*"
                class="hidden">

            <!-- TOMBOL EDIT -->
            <div onclick="toggleAvatarMenu()"
                class="absolute right-0 bottom-0 bg-white border rounded-full px-2 py-1 text-xs flex items-center gap-1 shadow cursor-pointer">
                <i class="fa-solid fa-pen text-gray-600"></i>
                Edit
            </div>

            <!-- MENU -->
            <div id="avatarMenu"
                class="hidden absolute right-0 bottom-10 bg-white border rounded-xl shadow p-2 w-36">

                <button type="button"
                        onclick="document.getElementById('avatarInput').click()"
                        class="w-full text-left px-3 py-2 hover:bg-gray-100 rounded">
                    Upload Foto
                </button>

                <button type="submit"
                        name="remove_avatar"
                        value="1"
                        class="w-full text-left px-3 py-2 hover:bg-gray-100 rounded text-red-500">
                    Hapus Foto
                </button>
            </div>
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>Simpan</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p x-data="{ show: true }"
                   x-show="show"
                   x-transition
                   x-init="setTimeout(() => show = false, 2000)"
                   class="text-sm text-gray-600">
                    Perubahan berhasil disimpan.
                </p>
            @endif
        </div>
    </form>
</section>
<script>
function toggleAvatarMenu() {
    document.getElementById('avatarMenu').classList.toggle('hidden');
}

document.getElementById('avatarInput').addEventListener('change', function(e) {
    const file = e.target.files[0];

    if (file) {
        const reader = new FileReader();

        reader.onload = function(e) {
            document.getElementById('avatarPreview').src = e.target.result;
        }

        reader.readAsDataURL(file);

        // TUTUP MENU SETELAH PILIH FOTO
        document.getElementById('avatarMenu').classList.add('hidden');
    }
});

document.addEventListener('click', function(event) {
    const menu = document.getElementById('avatarMenu');
    const avatar = document.querySelector('.relative.w-20');

    if (!avatar.contains(event.target)) {
        menu.classList.add('hidden');
    }
});
</script>
