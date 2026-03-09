<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Download - Payou.id</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-lg max-w-md w-full p-8">

        <div class="text-center mb-8">
            <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-gray-800">Download File</h1>
            <p class="text-gray-500 mt-2">Produk: <strong class="text-gray-700">{{ $product }}</strong></p>
        </div>

        {{-- Info token --}}
        <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 mb-6 text-sm">
            <div class="flex justify-between text-blue-700 mb-1">
                <span>Sisa download:</span>
                <strong>{{ $remaining }}x lagi</strong>
            </div>
            <div class="flex justify-between text-blue-700">
                <span>Link berlaku hingga:</span>
                <strong>{{ $expires_at }}</strong>
            </div>
        </div>

        {{-- Error --}}
        @if($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-700 rounded-xl p-4 mb-6 text-sm">
                {{ $errors->first() }}
            </div>
        @endif

        {{-- Form verifikasi email --}}
        <form method="POST" action="{{ route('download.verify', $token) }}">
            @csrf
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Masukkan email pembelian kamu
                </label>
                <input
                    type="email"
                    name="email"
                    required
                    placeholder="email@kamu.com"
                    value="{{ old('email') }}"
                    class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                />
                <p class="text-xs text-gray-400 mt-2">Email harus sama dengan email yang digunakan saat pembelian.</p>
            </div>

            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 rounded-xl transition-colors">
                ⬇️ Verifikasi & Download
            </button>
        </form>

    </div>
</body>
</html>