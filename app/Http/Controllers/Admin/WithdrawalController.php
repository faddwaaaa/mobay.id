<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Withdrawal;
use Illuminate\Http\Request;

class WithdrawalController extends Controller
{
    public function index(Request $request)
    {
        $withdrawals = Withdrawal::with('user')
            ->when($request->search, function ($q) use ($request) {
                $q->whereHas('user', fn($q) => $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('username', 'like', "%{$request->search}%"));
            })
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $stats = [
            'total'    => Withdrawal::count(),
            'pending'  => Withdrawal::where('status', 'pending')->count(),
            'approved' => Withdrawal::where('status', 'approved')->count(),
            'rejected' => Withdrawal::where('status', 'rejected')->count(),
            'total_amount' => Withdrawal::where('status', 'approved')->sum('amount'),
        ];

        return view('admin.withdrawals.index', compact('withdrawals', 'stats'));
    }

    public function show(Withdrawal $withdrawal)
    {
        $withdrawal->load('user');
        return view('admin.withdrawals.show', compact('withdrawal'));
    }

    public function approve(Withdrawal $withdrawal)
    {
        if ($withdrawal->status !== 'pending') {
            return back()->with('error', 'Hanya penarikan pending yang bisa disetujui.');
        }

        $withdrawal->update([
            'status'      => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        return back()->with('success', "Penarikan Rp " . number_format($withdrawal->amount) . " berhasil disetujui.");
    }

    public function reject(Request $request, Withdrawal $withdrawal)
    {
        if ($withdrawal->status !== 'pending') {
            return back()->with('error', 'Hanya penarikan pending yang bisa ditolak.');
        }

        $withdrawal->update([
            'status'           => 'rejected',
            'rejection_reason' => $request->reason ?? 'Ditolak oleh admin.',
        ]);

        return back()->with('success', "Penarikan berhasil ditolak.");
    }
}