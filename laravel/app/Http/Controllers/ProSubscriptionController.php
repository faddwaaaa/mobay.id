<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Services\ProSubscriptionService;

class ProSubscriptionController extends Controller
{
    protected $proService;

    public function __construct(ProSubscriptionService $proService)
    {
        $this->proService = $proService;
    }

    /**
     * Buat invoice QRIS untuk paket Pro
     */
    public function createInvoice(Request $request)
    {
        $request->validate([
            'package' => 'required|in:monthly,yearly',
        ]);

        $user = Auth::user();
        $package = $request->package;

        // Buat invoice di Xendit
        $invoiceData = $this->proService->createInvoice($user, $package);

        if (!$invoiceData['success']) {
            return response()->json([
                'success' => false,
                'error' => $invoiceData['error'] ?? 'Gagal membuat invoice',
            ], 500);
        }

        // Return QRIS data
        return response()->json([
            'success' => true,
            'invoice_id' => $invoiceData['invoice_id'],
            'external_id' => $invoiceData['external_id'],
            'amount' => $invoiceData['amount'],
            'qr_code' => $invoiceData['qr_code'],
            'invoice_url' => $invoiceData['invoice_url'],
            'package' => $package,
        ]);
    }

    /**
     * Endpoint untuk menerima callback/webhook dari Xendit
     */
    public function handleCallback(Request $request)
    {
        // Validasi callback token dari Xendit
        if (!$this->proService->validateCallback($request->all())) {
            return response()->json(['success' => false, 'error' => 'Invalid callback token'], 403);
        }

        // Handle payment success
        $success = $this->proService->handlePaymentSuccess($request->all());

        return response()->json(['success' => $success]);
    }

    /**
     * Cek status Pro user
     */
    public function checkStatus(Request $request)
    {
        $user = Auth::user();

        return response()->json([
            'is_pro' => $user->isPro(),
            'is_pro_active' => $user->isProActive(),
            'pro_until' => $user->pro_until,
            'pro_type' => $user->pro_type,
            'remaining_days' => $user->getProRemainingDays(),
        ]);
    }

    /**
     * Halaman success setelah pembayaran
     */
    public function paymentSuccess()
    {
        $user = Auth::user();
        return view('pro.payment-success', [
            'user' => $user,
            'pro_until' => $user->pro_until,
            'pro_type' => $user->pro_type,
        ]);
    }

    /**
     * Halaman failed sekalau pembayaran gagal
     */
    public function paymentFailed()
    {
        return view('pro.payment-failed');
    }
}
