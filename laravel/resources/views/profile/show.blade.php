<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Profil Pengguna') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <!-- Main Profile Card -->
            <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                <!-- Header Background -->
                <div class="h-32 bg-blue-600"></div>

                <!-- Profile Content -->
                <div class="px-6 sm:px-8 pb-8">
                    <!-- Avatar and Basic Info -->
                    <div class="flex flex-col sm:flex-row sm:items-end sm:space-x-6 -mt-16 relative z-10 mb-8">
                        <!-- Avatar -->
                        <div class="flex-shrink-0">
                            <div class="w-32 h-32 bg-white rounded-lg border-4 border-blue-600 overflow-hidden shadow-md">
                                @if ($user->avatar)
                                    <img src="{{ asset('storage/' . $user->avatar) }}" 
                                         alt="{{ $user->name }}" 
                                         class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center">
                                        <svg class="w-16 h-16 text-white" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Basic Info and Edit Button -->
                        <div class="mt-4 sm:mt-0 flex-grow">
                            <h1 class="text-3xl font-bold text-gray-900">{{ $user->name }}</h1>
                            <p class="text-gray-600 text-lg mt-1">{{ $user->email }}</p>
                        </div>

                        <!-- Edit Profile Button -->
                        <div class="mt-4 sm:mt-0 flex-shrink-0">
                            <a href="{{ route('profile.edit') }}" 
                               class="inline-flex items-center px-6 py-2 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition duration-200 shadow-md">
                                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                </svg>
                                {{ __('Edit Profil') }}
                            </a>
                        </div>
                    </div>

                    <hr class="my-8 border-gray-200">

                    <!-- Profile Information Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <!-- Email Information -->
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-gray-700 uppercase tracking-wide">
                                {{ __('Email') }}
                            </label>
                            <p class="text-lg text-gray-900 font-medium">{{ $user->email }}</p>
                        </div>

                        <!-- Email Verification Status -->
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-gray-700 uppercase tracking-wide">
                                {{ __('Status Verifikasi Email') }}
                            </label>
                            <div class="flex items-center space-x-3">
                                @if ($user->email_verified_at)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full bg-green-100 text-green-800 text-sm font-semibold">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                        </svg>
                                        {{ __('Terverifikasi') }}
                                    </span>
                                    <span class="text-sm text-gray-600">
                                        {{ __('pada') }} {{ $user->email_verified_at->format('d M Y, H:i') }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1 rounded-full bg-yellow-100 text-yellow-800 text-sm font-semibold">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                        </svg>
                                        {{ __('Belum Terverifikasi') }}
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- Login Method -->
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-gray-700 uppercase tracking-wide">
                                {{ __('Metode Login') }}
                            </label>
                            <div class="flex items-center space-x-3">
                                @if ($user->google_id)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full bg-red-100 text-red-800 text-sm font-semibold">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 0C4.477 0 0 4.484 0 10.017c0 4.425 2.865 8.18 6.839 9.49.5.092.682-.217.682-.482 0-.237-.008-.868-.013-1.703-2.782.603-3.369-1.343-3.369-1.343-.454-1.156-1.11-1.463-1.11-1.463-.908-.62.069-.608.069-.608 1.003.07 1.531 1.032 1.531 1.032.892 1.53 2.341 1.546 2.914 1.194.092-.929.35-1.546.636-1.903-2.22-.253-4.555-1.11-4.555-4.943 0-1.091.39-1.984 1.029-2.683-.103-.253-.446-1.27.098-2.647 0 0 .84-.269 2.75 1.025A9.578 9.578 0 0110 4.817c.85.004 1.705.114 2.504.336 1.909-1.294 2.747-1.025 2.747-1.025.546 1.377.203 2.394.1 2.647.64.699 1.028 1.592 1.028 2.683 0 3.842-2.339 4.687-4.566 4.935.359.309.678.919.678 1.852 0 1.336-.012 2.415-.012 2.743 0 .267.18.578.688.48C17.138 18.194 20 14.440 20 10.017 20 4.484 15.522 0 10 0z" clip-rule="evenodd" />
                                        </svg>
                                        {{ __('Google') }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1 rounded-full bg-blue-100 text-blue-800 text-sm font-semibold">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" />
                                            <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" />
                                        </svg>
                                        {{ __('Email') }}
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- Join Date -->
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-gray-700 uppercase tracking-wide">
                                {{ __('Tanggal Bergabung') }}
                            </label>
                            <div class="flex items-center space-x-3">
                                <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v2h16V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a2 2 0 012 2v2H4V9a2 2 0 012-2h12a2 2 0 012 2v2h-4v-2a2 2 0 012-2H6zm12 3H2v5a2 2 0 002 2h12a2 2 0 002-2v-5z" clip-rule="evenodd" />
                                </svg>
                                <p class="text-lg text-gray-900 font-medium">
                                    {{ $user->created_at->format('d F Y') }}
                                </p>
                            </div>
                            <p class="text-sm text-gray-600 mt-1">
                                {{ __('sejak') }} {{ $user->created_at->diffForHumans() }}
                            </p>
                        </div>
                    </div>

                    <hr class="my-8 border-gray-200">

                    <!-- Additional Notes Section -->
                    <div class="bg-blue-50 border-l-4 border-blue-600 p-4 rounded">
                        <p class="text-sm text-gray-700">
                            <strong class="text-blue-900">{{ __('Catatan:') }}</strong>
                            {{ __('Untuk mengubah informasi profil Anda, silakan klik tombol Edit Profil di atas.') }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Profile Stats (Optional - untuk pengembangan lebih lanjut) -->
            <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Note: Statistik ini membutuhkan relasi links dan clicks di database
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="text-center">
                        <p class="text-4xl font-bold text-blue-600">{{ $user->links->count() }}</p>
                        <p class="text-gray-600 text-sm mt-2">{{ __('Total Links') }}</p>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-6">
                    <div class="text-center">
                        <p class="text-4xl font-bold text-blue-600">{{ $user->links->sum('clicks_count') ?? 0 }}</p>
                        <p class="text-gray-600 text-sm mt-2">{{ __('Total Clicks') }}</p>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-6">
                    <div class="text-center">
                        <p class="text-4xl font-bold text-blue-600">{{ $user->socialLinks->count() }}</p>
                        <p class="text-gray-600 text-sm mt-2">{{ __('Social Links') }}</p>
                    </div>
                </div>
                -->
            </div>
        </div>
    </div>
</x-app-layout>
