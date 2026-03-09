<?php

namespace App\Http\Controllers;

use App\Models\DigitalProduct;
use App\Models\DigitalOrder;
use App\Models\DownloadToken;
use App\Services\DigitalOrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class DigitalOrderController extends Controller
{
    public function __construct(
        private DigitalOrderService $orderService
    ) {}

    /**
     * Halaman produk + form order
     */
    public function show(DigitalProduct $product)
    {
        abort_unless($product->is_active, 404);

        return view('digital.order', compact('product'));
    }

    /**
     * Simpan order baru (belum bayar)
     */
    public function store(Request $request, DigitalProduct $product)
    {
        $validator = Validator::make($request->all(), [
            'name'  => 'required|string|max:100',
            'email' => 'required|email|max:150',
        ], [
            'name.required'  => 'Nama wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email'    => 'Format email tidak valid.',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $order = $this->orderService->createOrder($product, [
            'name'  => $request->name,
            'email' => $request->email,
        ]);

        // Redirect ke halaman pembayaran
        return redirect()->route('payment.show', $order->order_code);
    }

    /**
     * Simulasi / callback pembayaran berhasil
     * Di production: ganti dengan webhook dari payment gateway
     */
    public function paymentSuccess(Request $request, string $orderCode)
    {
        $order = DigitalOrder::where('order_code', $orderCode)
            ->where('status', 'pending')
            ->firstOrFail();

        // Proses order: update status + kirim email
        $token = $this->orderService->completeOrder($order);

        return view('digital.payment-success', compact('order', 'token'));
    }

    /**
     * Halaman verifikasi download (pembeli input email)
     */
    public function verifyDownload(Request $request, string $token)
    {
        $validation = $this->orderService->validateToken($token);

        if (!$validation['valid']) {
            return view('digital.download-error', ['message' => $validation['message']]);
        }

        if ($request->isMethod('post')) {
            return $this->processDownload($request, $token);
        }

        return view('digital.download-verify', [
            'token'      => $token,
            'product'    => $validation['product'],
            'remaining'  => $validation['remaining'],
            'expires_at' => $validation['expires_at'],
        ]);
    }

    /**
     * Proses download setelah verifikasi email
     */
    private function processDownload(Request $request, string $tokenString)
    {
        $request->validate([
            'email' => 'required|email',
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email'    => 'Format email tidak valid.',
        ]);

        $result = $this->orderService->processDownload($tokenString, $request->email);

        if (!$result['success']) {
            return back()->withErrors(['email' => $result['message']]);
        }

        // Stream file ke browser
       return Storage::disk('public')->download(
    $result['file_path'],
    $result['file_name']
);
    }
}