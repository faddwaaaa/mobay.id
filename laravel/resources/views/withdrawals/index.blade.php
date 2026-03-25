@extends('layouts.dashboard')

@section('title', 'Riwayat Penarikan | Mobay.id')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Riwayat Penarikan</h1>
                <p class="text-gray-600 mt-2">Kelola permintaan penarikan saldo Anda</p>
            </div>
            <a href="{{ route('dashboard') }}" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 transition">
                ← Kembali
            </a>
        </div>
    </div>

    <!-- Stats Summary -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow-md p-6">
            <p class="text-sm text-gray-600 mb-1">Total Penarikan</p>
            <p class="text-2xl font-bold text-gray-800">{{ $withdrawals->total() }}</p>
        </div>
        <div class="bg-blue-50 rounded-lg shadow-md p-6">
            <p class="text-sm text-gray-600 mb-1">Diproses</p>
            <p class="text-2xl font-bold text-blue-600">{{ $withdrawals->where('status', 'approved')->count() }}</p>
        </div>
        <div class="bg-green-50 rounded-lg shadow-md p-6">
            <p class="text-sm text-gray-600 mb-1">Selesai</p>
            <p class="text-2xl font-bold text-green-600">{{ $withdrawals->where('status', 'completed')->count() }}</p>
        </div>
        <div class="bg-red-50 rounded-lg shadow-md p-6">
            <p class="text-sm text-gray-600 mb-1">Gagal/Dibatalkan</p>
            <p class="text-2xl font-bold text-red-600">{{ $withdrawals->whereIn('status', ['rejected', 'cancelled'])->count() }}</p>
        </div>
    </div>

    <!-- Withdrawal List -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-xl font-bold text-gray-800">Daftar Penarikan</h2>
        </div>

        @if($withdrawals->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bank</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rekening</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($withdrawals as $withdrawal)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    #{{ $withdrawal->id }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $withdrawal->created_at->format('d M Y H:i') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                                    {{ $withdrawal->formatted_amount }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $withdrawal->bank_name }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <div>{{ $withdrawal->account_number }}</div>
                                    <div class="text-xs text-gray-400">{{ $withdrawal->account_name }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    {!! $withdrawal->status_badge !!}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <div class="flex items-center gap-2">
                                        <button onclick="viewDetail({{ $withdrawal->id }})" 
                                                class="text-blue-600 hover:text-blue-800">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                            </svg>
                                        </button>
                                        
                                        @if(in_array($withdrawal->status, ['approved']))
                                            <button onclick="cancelWithdrawal({{ $withdrawal->id }})" 
                                                    class="text-red-600 hover:text-red-800"
                                                    title="Batalkan Penarikan">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                </svg>
                                            </button>
                                        @endif
                                        
                                        @if(in_array($withdrawal->status, ['approved', 'completed']))
                                            <button onclick="checkStatus({{ $withdrawal->id }})" 
                                                    class="text-green-600 hover:text-green-800"
                                                    title="Refresh Status">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                                </svg>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $withdrawals->links() }}
            </div>
        @else
            <div class="p-12 text-center">
                <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <p class="text-gray-500 mb-4">Belum ada riwayat penarikan</p>
                <a href="{{ route('dashboard') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    Buat Penarikan Pertama
                </a>
            </div>
        @endif
    </div>
</div>

<!-- Detail Modal -->
<div id="detailModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 w-full max-w-lg">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-xl font-bold">Detail Penarikan</h3>
            <button onclick="closeDetailModal()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        <div id="detailContent" class="space-y-4">
            <!-- Content will be loaded here -->
        </div>
    </div>
</div>

@push('scripts')
<script>
function viewDetail(id) {
    // Implement view detail functionality
    alert('Detail penarikan ID: ' + id);
}

async function cancelWithdrawal(id) {
    if (await window.appConfirm('Apakah Anda yakin ingin membatalkan penarikan ini? Saldo akan dikembalikan ke akun Anda.', {
        title: 'Batalkan Penarikan',
        confirmText: 'Ya, batalkan'
    })) {
        fetch(`/withdrawal/${id}/cancel`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            },
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                alert('✅ ' + result.message);
                window.location.reload();
            } else {
                alert('❌ ' + result.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('❌ Terjadi kesalahan. Silakan coba lagi.');
        });
    }
}

async function checkStatus(id) {
    if (await window.appConfirm('Refresh status penarikan dari Midtrans?', {
        title: 'Refresh Status',
        confirmText: 'Ya, refresh',
        variant: 'primary'
    })) {
        fetch(`/withdrawal/${id}/check-status`, {
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            },
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                alert('✅ Status: ' + result.data.status + '\nMidtrans Status: ' + result.data.midtrans_status);
                window.location.reload();
            } else {
                alert('❌ ' + result.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('❌ Terjadi kesalahan. Silakan coba lagi.');
        });
    }
}

function closeDetailModal() {
    document.getElementById('detailModal').classList.add('hidden');
    document.getElementById('detailModal').classList.remove('flex');
}
</script>
@endpush

@endsection
