<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $transactions = Transaction::with(['user', 'product'])
            ->when($request->search, function ($q) use ($request) {
                $q->where('order_id', 'like', "%{$request->search}%")
                  ->orWhere('transaction_id', 'like', "%{$request->search}%")
                  ->orWhereHas('user', fn($q) => $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('email', 'like', "%{$request->search}%"));
            })
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $stats = [
            'total'   => Transaction::count(),
            'success' => Transaction::where('status', 'success')->count(),
            'pending' => Transaction::where('status', 'pending')->count(),
            'failed'  => Transaction::where('status', 'failed')->count(),
            'revenue' => Transaction::where('status', 'success')->sum('amount'),
        ];

        return view('admin.transactions.index', compact('transactions', 'stats'));
    }

    public function show(Transaction $transaction)
    {
        $transaction->load(['user', 'product']);
        return view('admin.transactions.show', compact('transaction'));
    }
}