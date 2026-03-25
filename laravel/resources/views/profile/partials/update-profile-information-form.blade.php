<section class="-mt-20">
    <header>
        <h2 class="text-2xl font-bold text-gray-900 tracking-tight">
            Informasi Profil
        </h2>
        <p class="mt-1 text-sm text-gray-600">
            Perbarui informasi profil akun dan alamat email Anda.
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-5" enctype="multipart/form-data">
        @csrf
        @method('patch')

        {{-- Nama Lengkap --}}
        <div>
            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
            <input id="name" name="name" type="text"
                value="{{ old('name', $user->name) }}"
                required autofocus autocomplete="name"
                class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 text-gray-900 text-sm
                       placeholder-gray-400 transition-all duration-150
                       focus:outline-none focus:bg-white focus:ring-2 focus:ring-blue-400/25 focus:border-blue-400
                       hover:border-gray-300">
            <x-input-error class="mt-1" :messages="$errors->get('name')" />
        </div>

        {{-- Username --}}
        <div>
            <label for="username" class="block text-sm font-medium text-gray-700 mb-1">Username</label>
            <div class="relative">
                <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-gray-400 text-sm select-none pointer-events-none">
                    mobay.id/
                </span>
                <input id="username" name="username" type="text"
                    value="{{ old('username', $user->username) }}"
                    required
                    class="w-full pl-[4.8rem] pr-4 py-3 rounded-xl border border-gray-200 bg-gray-50 text-gray-900 text-sm
                           placeholder-gray-400 transition-all duration-150
                           focus:outline-none focus:bg-white focus:ring-2 focus:ring-blue-400/25 focus:border-blue-400
                           hover:border-gray-300">
            </div>
            <x-input-error class="mt-1" :messages="$errors->get('username')" />
        </div>

        {{-- Email --}}
        <div>
            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
            <input id="email" name="email" type="email"
                value="{{ old('email', $user->email) }}"
                required autocomplete="username"
                class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 text-gray-900 text-sm
                       placeholder-gray-400 transition-all duration-150
                       focus:outline-none focus:bg-white focus:ring-2 focus:ring-blue-400/25 focus:border-blue-400
                       hover:border-gray-300">
            <x-input-error class="mt-1" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div class="mt-2 p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                    <p class="text-sm text-yellow-800">
                        Email Anda belum diverifikasi.
                        <button form="send-verification"
                            class="underline font-medium hover:text-yellow-900 focus:outline-none">
                            Kirim ulang email verifikasi.
                        </button>
                    </p>
                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-1 font-medium text-sm text-green-600">
                            Link verifikasi baru telah dikirim ke email Anda.
                        </p>
                    @endif
                </div>
            @endif
        </div>

        {{-- Avatar --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Foto Profil</label>
            <div class="flex items-center gap-4">
                <div class="relative w-20 h-20 flex-shrink-0">
                    @if ($user->avatar)
                        <img id="avatarPreview"
                            src="{{ asset('storage/'.$user->avatar) }}"
                            class="w-20 h-20 rounded-full object-cover ring-2 ring-gray-200">
                    @else
                        <img id="avatarPreview"
                            src="{{ asset('img/default-avatar.jpg') }}"
                            class="w-20 h-20 rounded-full object-cover ring-2 ring-gray-200">
                    @endif

                    <input type="file" id="avatarInput" name="avatar" accept="image/*" class="hidden">

                    <div onclick="toggleAvatarMenu()"
                        class="absolute right-0 bottom-0 bg-white border border-gray-200 rounded-full w-7 h-7
                               flex items-center justify-content-center shadow-md cursor-pointer hover:bg-gray-50
                               transition-colors duration-150"
                        style="display:flex; align-items:center; justify-content:center;">
                        <i class="fa-solid fa-pen text-gray-600" style="font-size:10px;"></i>
                    </div>

                    <div id="avatarMenu"
                        class="hidden absolute left-24 bottom-0 bg-white border border-gray-200 rounded-xl shadow-lg p-1.5 w-40 z-10">
                        <button type="button"
                                onclick="document.getElementById('avatarInput').click()"
                                class="w-full text-left px-3 py-2 text-sm hover:bg-gray-50 rounded-lg flex items-center gap-2 text-gray-700">
                            <i class="fa-solid fa-upload text-blue-500 w-4"></i>
                            Upload Foto
                        </button>
                        <button type="submit"
                                name="remove_avatar"
                                value="1"
                                class="w-full text-left px-3 py-2 text-sm hover:bg-red-50 rounded-lg flex items-center gap-2 text-red-500">
                            <i class="fa-solid fa-trash w-4"></i>
                            Hapus Foto
                        </button>
                    </div>
                </div>

                <div class="text-sm text-gray-500">
                    <p class="font-medium text-gray-700">Ganti foto profil</p>
                    <p class="text-xs mt-0.5">JPG, PNG. Maks 2MB.</p>
                </div>
            </div>
        </div>

        {{-- Submit --}}
        <div class="flex items-center gap-4 pt-2">
            <button type="submit"
                class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold
                       rounded-lg shadow-sm transition-all duration-200 hover:shadow-md
                       focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                Simpan Perubahan
            </button>

            @if (session('status') === 'profile-updated')
                <p x-data="{ show: true }"
                   x-show="show"
                   x-transition
                   x-init="setTimeout(() => show = false, 2000)"
                   class="text-sm text-green-600 flex items-center gap-1">
                    <i class="fa-solid fa-circle-check"></i>
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
        document.getElementById('avatarMenu').classList.add('hidden');
    }
});

document.addEventListener('click', function(event) {
    const menu = document.getElementById('avatarMenu');
    const avatar = document.querySelector('.relative.w-20');
    if (avatar && !avatar.contains(event.target)) {
        menu.classList.add('hidden');
    }
});
</script>
