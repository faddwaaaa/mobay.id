<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            Ubah Password
        </h2>
        <p class="mt-1 text-sm text-gray-600">
            Pastikan akun Anda menggunakan password yang kuat dan aman.
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="mt-6 space-y-5">
        @csrf
        @method('put')

        {{-- Password Saat Ini --}}
        <div>
            <label for="update_password_current_password" class="block text-sm font-medium text-gray-700 mb-1">
                Password Saat Ini
            </label>
            <div class="relative">
                <input id="update_password_current_password"
                    name="current_password"
                    type="password"
                    autocomplete="current-password"
                    class="w-full px-4 py-3 pr-11 rounded-xl border border-gray-200 bg-gray-50 text-gray-900 text-sm
                           placeholder-gray-400 transition-all duration-150
                           focus:outline-none focus:bg-white focus:ring-2 focus:ring-blue-400/25 focus:border-blue-400
                           hover:border-gray-300">
                <button type="button" onclick="togglePass('update_password_current_password', this)"
                    class="absolute inset-y-0 right-0 flex items-center px-3.5 text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="fa-solid fa-eye text-xs"></i>
                </button>
            </div>
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-1" />
        </div>

        {{-- Password Baru --}}
        <div>
            <label for="update_password_password" class="block text-sm font-medium text-gray-700 mb-1">
                Password Baru
            </label>
            <div class="relative">
                <input id="update_password_password"
                    name="password"
                    type="password"
                    autocomplete="new-password"
                    class="w-full px-4 py-3 pr-11 rounded-xl border border-gray-200 bg-gray-50 text-gray-900 text-sm
                           placeholder-gray-400 transition-all duration-150
                           focus:outline-none focus:bg-white focus:ring-2 focus:ring-blue-400/25 focus:border-blue-400
                           hover:border-gray-300">
                <button type="button" onclick="togglePass('update_password_password', this)"
                    class="absolute inset-y-0 right-0 flex items-center px-3.5 text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="fa-solid fa-eye text-xs"></i>
                </button>
            </div>
            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-1" />
        </div>

        {{-- Konfirmasi Password --}}
        <div>
            <label for="update_password_password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">
                Konfirmasi Password Baru
            </label>
            <div class="relative">
                <input id="update_password_password_confirmation"
                    name="password_confirmation"
                    type="password"
                    autocomplete="new-password"
                    class="w-full px-4 py-3 pr-11 rounded-xl border border-gray-200 bg-gray-50 text-gray-900 text-sm
                           placeholder-gray-400 transition-all duration-150
                           focus:outline-none focus:bg-white focus:ring-2 focus:ring-blue-400/25 focus:border-blue-400
                           hover:border-gray-300">
                <button type="button" onclick="togglePass('update_password_password_confirmation', this)"
                    class="absolute inset-y-0 right-0 flex items-center px-3.5 text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="fa-solid fa-eye text-xs"></i>
                </button>
            </div>
            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-1" />
        </div>

        {{-- Submit --}}
        <div class="flex items-center gap-4 pt-2">
            <button type="submit"
                class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold
                       rounded-lg shadow-sm transition-all duration-200 hover:shadow-md
                       focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                Simpan Password
            </button>

            @if (session('status') === 'password-updated')
                <p x-data="{ show: true }"
                   x-show="show"
                   x-transition
                   x-init="setTimeout(() => show = false, 2000)"
                   class="text-sm text-green-600 flex items-center gap-1">
                    <i class="fa-solid fa-circle-check"></i>
                    Password berhasil diperbarui.
                </p>
            @endif
        </div>
    </form>
</section>

<script>
function togglePass(inputId, btn) {
    const input = document.getElementById(inputId);
    const icon  = btn.querySelector('i');
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.replace('fa-eye', 'fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.replace('fa-eye-slash', 'fa-eye');
    }
}
</script>