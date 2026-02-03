<?php

namespace App\Http\Controllers;

use App\Models\Withdrawal;
use App\Models\User;
use App\Services\MidtransPayoutService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WithdrawalController extends Controller
{
    protected $midtransService;

    public function __construct(MidtransPayoutService $midtransService)
    {
        $this->midtransService = $midtransService;
    }

    /**
     * Display a listing of withdrawals
     */
    public function index()
    {
        $withdrawals = Withdrawal::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('withdrawals.index', compact('withdrawals'));
    }

    /**
     * Store a newly created withdrawal request and process immediately
     */
    public function store(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:50000|max:10000000',
            'bank_name' => 'required|string|max:100',
            'account_number' => 'required|string|max:50',
            'account_name' => 'required|string|max:100',
            'notes' => 'nullable|string|max:500',
        ], [
            'amount.required' => 'Jumlah penarikan harus diisi',
            'amount.min' => 'Minimal penarikan adalah Rp 50.000',
            'amount.max' => 'Maksimal penarikan adalah Rp 10.000.000',
            'bank_name.required' => 'Nama bank harus diisi',
            'account_number.required' => 'Nomor rekening harus diisi',
            'account_name.required' => 'Nama pemilik rekening harus diisi',
        ]);

        $user = Auth::user();
        $amount = $request->amount;

        // Check if user has sufficient balance
        if ($user->balance < $amount) {
            return response()->json([
                'success' => false,
                'message' => 'Saldo tidak mencukupi untuk melakukan penarikan'
            ], 400);
        }

        // Check for pending withdrawals
        $pendingWithdrawal = Withdrawal::where('user_id', $user->id)
            ->whereIn('status', ['pending', 'approved'])
            ->exists();

        if ($pendingWithdrawal) {
            return response()->json([
                'success' => false,
                'message' => 'Anda masih memiliki penarikan yang sedang diproses'
            ], 400);
        }

        DB::beginTransaction();
        try {
            // Prepare payout data
            $payoutData = [
                'account_name' => $request->account_name,
                'account_number' => $request->account_number,
                'bank_name' => strtoupper($request->bank_name),
                'amount' => $amount,
                'email' => $user->email,
                'notes' => $request->notes ?? 'Penarikan saldo dari Payou.id',
            ];

            // Process payout immediately via Midtrans
            $payoutResult = $this->midtransService->createPayout($payoutData);

            if (!$payoutResult['success']) {
                throw new \Exception($payoutResult['message'] ?? 'Gagal memproses penarikan ke Midtrans');
            }

            $payoutResponse = $payoutResult['data'];
            $payoutStatus = $payoutResponse['payouts'][0]['status'] ?? 'pending';
            $referenceNo = $payoutResponse['payouts'][0]['reference_no'] ?? null;

            // Create withdrawal record
            $withdrawal = Withdrawal::create([
                'user_id' => $user->id,
                'amount' => $amount,
                'bank_name' => strtoupper($request->bank_name),
                'account_number' => $request->account_number,
                'account_name' => $request->account_name,
                'notes' => $request->notes,
                'status' => $payoutStatus === 'processed' ? 'completed' : 'approved',
                'payout_id' => $referenceNo,
                'midtrans_response' => $payoutResponse,
                'approved_by' => $user->id, // Self approval
                'approved_at' => now(),
                'ip_address' => $request->ip(),
            ]);

            // Deduct balance from user
            $user->decrement('balance', $amount);

            // Create transaction record
            DB::table('transactions')->insert([
                'user_id' => $user->id,
                'order_id' => 'WD-' . $withdrawal->id . '-' . time(),
                'transaction_id' => $referenceNo ?? 'WITHDRAWAL-' . $withdrawal->id,
                'amount' => $amount,
                'status' => 'settlement',
                'payment_method' => 'withdrawal',
                'midtrans_response' => json_encode($payoutResponse),
                'notes' => 'Penarikan saldo ke ' . $request->bank_name . ' - ' . $request->account_number,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Penarikan berhasil diproses! Dana akan masuk ke rekening Anda dalam 1-3 hari kerja.',
                'data' => [
                    'withdrawal_id' => $withdrawal->id,
                    'reference_no' => $referenceNo,
                    'amount' => $withdrawal->formatted_amount,
                    'status' => $withdrawal->status,
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Withdrawal Process Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memproses penarikan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cancel withdrawal request (only if still pending or failed)
     */
    public function cancel($id)
    {
        $withdrawal = Withdrawal::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        if (!in_array($withdrawal->status, ['pending', 'approved'])) {
            return response()->json([
                'success' => false,
                'message' => 'Penarikan dengan status ' . $withdrawal->status . ' tidak dapat dibatalkan'
            ], 400);
        }

        DB::beginTransaction();
        try {
            $withdrawal->update([
                'status' => 'cancelled',
                'rejection_reason' => 'Dibatalkan oleh user',
            ]);

            // Refund balance
            $withdrawal->user->increment('balance', $withdrawal->amount);

            // Update transaction
            DB::table('transactions')
                ->where('transaction_id', $withdrawal->payout_id ?? 'WITHDRAWAL-' . $withdrawal->id)
                ->update([
                    'status' => 'cancelled',
                    'notes' => 'Penarikan dibatalkan oleh user',
                    'updated_at' => now(),
                ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Penarikan berhasil dibatalkan dan saldo dikembalikan ke akun Anda'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Withdrawal Cancel Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal membatalkan penarikan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Check withdrawal status from Midtrans
     */
    public function checkStatus($id)
    {
        $withdrawal = Withdrawal::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        if (!$withdrawal->payout_id) {
            return response()->json([
                'success' => false,
                'message' => 'Penarikan ini belum memiliki reference number dari Midtrans'
            ], 400);
        }

        $statusResult = $this->midtransService->getPayoutStatus($withdrawal->payout_id);

        if ($statusResult['success']) {
            $payoutData = $statusResult['data'];
            $status = $payoutData['status'] ?? null;
            
            // Update withdrawal status based on Midtrans response
            if ($status === 'processed') {
                $withdrawal->update([
                    'status' => 'completed',
                    'midtrans_response' => $payoutData,
                ]);

                DB::table('transactions')
                    ->where('transaction_id', $withdrawal->payout_id)
                    ->update([
                        'status' => 'settlement',
                        'updated_at' => now(),
                    ]);
            } elseif ($status === 'failed') {
                $withdrawal->update([
                    'status' => 'rejected',
                    'rejection_reason' => $payoutData['failure_reason'] ?? 'Gagal diproses oleh bank',
                    'midtrans_response' => $payoutData,
                ]);

                // Refund balance if failed
                $withdrawal->user->increment('balance', $withdrawal->amount);

                DB::table('transactions')
                    ->where('transaction_id', $withdrawal->payout_id)
                    ->update([
                        'status' => 'failed',
                        'updated_at' => now(),
                    ]);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'status' => $withdrawal->status,
                    'midtrans_status' => $status,
                    'reference_no' => $withdrawal->payout_id,
                ]
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Gagal memeriksa status penarikan dari Midtrans'
        ], 500);
    }

    /**
     * Get list of supported banks
     */
    public function getBanks()
    {
        $banks = [
            ['code' => 'BCA', 'name' => 'Bank Central Asia (BCA)'],
            ['code' => 'BNI', 'name' => 'Bank Negara Indonesia (BNI)'],
            ['code' => 'BRI', 'name' => 'Bank Rakyat Indonesia (BRI)'],
            ['code' => 'MANDIRI', 'name' => 'Bank Mandiri'],
            ['code' => 'CIMB', 'name' => 'CIMB Niaga'],
            ['code' => 'PERMATA', 'name' => 'Bank Permata'],
            ['code' => 'BNI SYARIAH', 'name' => 'BNI Syariah'],
            ['code' => 'BSI', 'name' => 'Bank Syariah Indonesia (BSI)'],
            ['code' => 'DANAMON', 'name' => 'Bank Danamon'],
            ['code' => 'MEGA', 'name' => 'Bank Mega'],
            ['code' => 'PANIN', 'name' => 'Bank Panin'],
            ['code' => 'MUAMALAT', 'name' => 'Bank Muamalat'],
            ['code' => 'OCBC', 'name' => 'OCBC NISP'],
            ['code' => 'MAYBANK', 'name' => 'Maybank Indonesia'],
            ['code' => 'BTPN', 'name' => 'Bank BTPN'],
            ['code' => 'JENIUS', 'name' => 'Jenius (BTPN)'],
            ['code' => 'SINARMAS', 'name' => 'Bank Sinarmas'],
        ];

        return response()->json([
            'success' => true,
            'data' => $banks
        ]);
    }
}
