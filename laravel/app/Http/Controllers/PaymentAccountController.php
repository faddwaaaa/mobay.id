<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePaymentAccountRequest;
use App\Models\PaymentAccount;
use App\Models\PaymentAccountAuditLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PaymentAccountController extends Controller
{
    private const MAX_ACCOUNTS    = 5;
    private const PIN_RATE_KEY    = 'pin_verify:{userId}';
    private const PIN_MAX_TRIES   = 3;
    private const PIN_DECAY_SEC   = 300; // 5 minutes

    private const BANK_NAMES = [
        'BCA'     => 'Bank Central Asia',
        'BRI'     => 'Bank Rakyat Indonesia',
        'BNI'     => 'Bank Negara Indonesia',
        'MANDIRI' => 'Bank Mandiri',
        'BSI'     => 'Bank Syariah Indonesia',
        'CIMB'    => 'CIMB Niaga',
        'DANAMON' => 'Danamon',
        'PERMATA' => 'Permata Bank',
        'GOPAY'   => 'GoPay',
        'OVO'     => 'OVO',
        'DANA'    => 'DANA',
    ];

    // -----------------------------------------------------------------------
    // Show page
    // -----------------------------------------------------------------------

    public function index(): View
{
    $accounts = PaymentAccount::forUser(auth()->id())
        ->orderByDesc('is_default')
        ->orderBy('created_at')
        ->get()
        ->map(fn ($a) => $a->toSafeArray());

    return view('payment.accounts', [
        'accounts'       => $accounts,
        'maxAccounts'    => self::MAX_ACCOUNTS,   // <-- ini sudah ada
        'remainingSlots' => self::MAX_ACCOUNTS - $accounts->count(),
        'bankList'       => self::BANK_NAMES,
    ]);
}

    // -----------------------------------------------------------------------
    // Store new account
    // -----------------------------------------------------------------------

    public function store(StorePaymentAccountRequest $request): JsonResponse
    {
        $user = auth()->user();

        // --- PIN rate limit check ---
        $pinKey = str_replace('{userId}', $user->id, self::PIN_RATE_KEY);

        if (RateLimiter::tooManyAttempts($pinKey, self::PIN_MAX_TRIES)) {
            $seconds = RateLimiter::availableIn($pinKey);
            PaymentAccountAuditLog::record($user->id, 'pin_lockout');

            return response()->json([
                'success' => false,
                'message' => "Terlalu banyak percobaan PIN. Coba lagi dalam {$seconds} detik.",
                'locked'  => true,
                'retry_after' => $seconds,
            ], 429);
        }

        // --- Verify PIN ---
        if (! Hash::check($request->pin, $user->pin_hash)) {
            RateLimiter::hit($pinKey, self::PIN_DECAY_SEC);

            $remaining = self::PIN_MAX_TRIES - RateLimiter::attempts($pinKey);
            PaymentAccountAuditLog::record($user->id, 'pin_failed');

            return response()->json([
                'success'   => false,
                'message'   => "PIN salah. Sisa percobaan: {$remaining}",
                'remaining' => $remaining,
            ], 422);
        }

        RateLimiter::clear($pinKey);

        // --- Account limit check ---
        $count = PaymentAccount::forUser($user->id)->count();
        if ($count >= self::MAX_ACCOUNTS) {
            return response()->json([
                'success' => false,
                'message' => 'Batas maksimal ' . self::MAX_ACCOUNTS . ' rekening tercapai.',
            ], 422);
        }

        // --- Duplicate check ---
        $last4 = substr($request->account_number, -4);
        $exists = PaymentAccount::forUser($user->id)
            ->where('bank_code', $request->bank_code)
            ->where('account_number_last4', $last4)
            ->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'Rekening ini sudah tersimpan.',
            ], 422);
        }

        // --- If set as default, unset others ---
        if ($request->boolean('is_default') || $count === 0) {
            PaymentAccount::forUser($user->id)->update(['is_default' => false]);
        }

        // --- Save (encrypted automatically via model cast) ---
        $account = PaymentAccount::create([
            'user_id'                  => $user->id,
            'bank_code'                => strtoupper($request->bank_code),
            'bank_name'                => self::BANK_NAMES[$request->bank_code] ?? $request->bank_code,
            'account_number_encrypted' => $request->account_number,  // cast encrypts it
            'account_number_last4'     => $last4,
            'account_holder_encrypted' => $request->account_holder,  // cast encrypts it
            'label'                    => $request->label,
            'is_default'               => $request->boolean('is_default') || $count === 0,
            'is_verified'              => true, // already verified via verifyAccount endpoint
        ]);

        PaymentAccountAuditLog::record($user->id, 'account_created', $account->id, [
            'bank_code' => $account->bank_code,
            'last4'     => $last4,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Rekening berhasil disimpan.',
            'account' => $account->toSafeArray(),
        ]);
    }

    // -----------------------------------------------------------------------
    // Set as default
    // -----------------------------------------------------------------------

    public function setDefault(Request $request, PaymentAccount $paymentAccount): JsonResponse
    {
        $this->authorizeAccount($paymentAccount);

        PaymentAccount::forUser(auth()->id())->update(['is_default' => false]);
        $paymentAccount->update(['is_default' => true]);

        PaymentAccountAuditLog::record(auth()->id(), 'set_default', $paymentAccount->id);

        return response()->json(['success' => true, 'message' => 'Rekening utama diperbarui.']);
    }

    // -----------------------------------------------------------------------
    // Delete account
    // -----------------------------------------------------------------------

    public function destroy(Request $request, PaymentAccount $paymentAccount): JsonResponse
    {
        $this->authorizeAccount($paymentAccount);

        $request->validate(['pin' => ['required', 'string', 'digits:6']]);

        $user   = auth()->user();
        $pinKey = str_replace('{userId}', $user->id, self::PIN_RATE_KEY);

        if (RateLimiter::tooManyAttempts($pinKey, self::PIN_MAX_TRIES)) {
            return response()->json([
                'success' => false,
                'message' => 'Akun dikunci sementara. Coba lagi nanti.',
                'locked'  => true,
            ], 429);
        }

        if (! Hash::check($request->pin, $user->pin_hash)) {
            RateLimiter::hit($pinKey, self::PIN_DECAY_SEC);
            $remaining = self::PIN_MAX_TRIES - RateLimiter::attempts($pinKey);

            return response()->json([
                'success'   => false,
                'message'   => "PIN salah. Sisa percobaan: {$remaining}",
                'remaining' => $remaining,
            ], 422);
        }

        RateLimiter::clear($pinKey);

        $wasDefault = $paymentAccount->is_default;
        $bankCode   = $paymentAccount->bank_code;
        $last4      = $paymentAccount->account_number_last4;

        // Soft delete — keeps record for audit
        $paymentAccount->delete();

        // Re-assign default if needed
        if ($wasDefault) {
            $next = PaymentAccount::forUser($user->id)->first();
            $next?->update(['is_default' => true]);
        }

        PaymentAccountAuditLog::record($user->id, 'account_deleted', $paymentAccount->id, [
            'bank_code' => $bankCode,
            'last4'     => $last4,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Rekening berhasil dihapus.',
        ]);
    }

    // -----------------------------------------------------------------------
    // Verify bank account via Midtrans (Bank Account Inquiry)
    // -----------------------------------------------------------------------

    public function verifyAccount(Request $request): JsonResponse
    {
        $request->validate([
            'bank_code'      => ['required', 'string', 'max:20'],
            'account_number' => ['required', 'string', 'digits_between:10,16'],
        ]);

        $user    = auth()->user();
        $rateKey = 'verify_account:' . $user->id;

        if (RateLimiter::tooManyAttempts($rateKey, 5)) {
            PaymentAccountAuditLog::record($user->id, 'verify_rate_limited');
            return response()->json([
                'success' => false,
                'message' => 'Terlalu banyak percobaan verifikasi. Tunggu beberapa saat.',
            ], 429);
        }

        RateLimiter::hit($rateKey, 60);

        try {
            // Midtrans Sandbox — Bank Account Inquiry
            // Docs: https://docs.midtrans.com/reference/bank-transfer-inquiry
            $serverKey = config('services.midtrans.server_key');
            $baseUrl   = config('services.midtrans.is_production')
                ? 'https://api.midtrans.com'
                : 'https://api.sandbox.midtrans.com';

            $response = Http::withBasicAuth($serverKey, '')
                ->timeout(10)
                ->post("{$baseUrl}/v2/bank_accounts/validate", [
                    'bank'           => strtolower($request->bank_code),
                    'account_number' => $request->account_number,
                ]);

            if ($response->successful() && isset($response['account_name'])) {
                PaymentAccountAuditLog::record($user->id, 'account_verified', null, [
                    'bank_code' => $request->bank_code,
                    'last4'     => substr($request->account_number, -4),
                ]);

                return response()->json([
                    'success'      => true,
                    'account_name' => $response['account_name'],
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Rekening tidak ditemukan atau tidak valid.',
            ], 422);

        } catch (\Exception $e) {
            \Log::error('Midtrans account verification failed', [
                'user_id' => $user->id,
                'error'   => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal menghubungi layanan bank. Coba beberapa saat lagi.',
            ], 503);
        }
    }

    // -----------------------------------------------------------------------
    // Private helpers
    // -----------------------------------------------------------------------

    private function authorizeAccount(PaymentAccount $account): void
    {
        abort_unless($account->user_id === auth()->id(), 403, 'Forbidden');
    }
}