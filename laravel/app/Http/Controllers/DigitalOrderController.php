<?php

namespace App\Http\Controllers;

use App\Models\DigitalProduct;
use App\Models\DigitalOrder;
use App\Models\DownloadToken;
use App\Services\DigitalOrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

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

        return redirect()->route('payment.show', $order->order_code);
    }

    /**
     * Simulasi / callback pembayaran berhasil
     */
    public function paymentSuccess(Request $request, string $orderCode)
    {
        $order = DigitalOrder::where('order_code', $orderCode)
            ->where('status', 'pending')
            ->firstOrFail();

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

        return Storage::disk('public')->download(
            $result['file_path'],
            $result['file_name']
        );
    }

    /**
     * Laporan masalah produk digital — dikirim ke CS
     * POST /api/report-digital-problem
     */
    public function reportProblem(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'name'        => 'required|string|max:100',
            'email'       => 'required|email|max:150',
            'description' => 'required|string|max:2000',
            'product'     => 'nullable|string|max:200',
            'order_id'    => 'nullable|string|max:100',
            'token'       => 'nullable|string|max:100',
        ]);

        try {
            Mail::send([], [], function ($mail) use ($request) {
                $mail->to('smeganemolab@gmail.com')
                     ->subject('[Laporan Masalah] Produk: ' . ($request->product ?? '-'))
                     ->html(
                        '<div style="font-family:sans-serif;max-width:600px;margin:0 auto;padding:24px;background:#fff;">
                            <div style="background:#dc2626;padding:20px 24px;border-radius:12px 12px 0 0;">
                                <h2 style="color:#fff;margin:0;font-size:18px;">Laporan Masalah Produk Digital</h2>
                            </div>
                            <div style="border:1px solid #e5e7eb;border-top:none;border-radius:0 0 12px 12px;padding:24px;">
                                <table style="width:100%;border-collapse:collapse;font-size:14px;">
                                    <tr style="border-bottom:1px solid #f3f4f6;">
                                        <td style="padding:10px 0;color:#6b7280;width:140px;">Nama Pembeli</td>
                                        <td style="padding:10px 0;font-weight:700;color:#111827;">' . e($request->name) . '</td>
                                    </tr>
                                    <tr style="border-bottom:1px solid #f3f4f6;">
                                        <td style="padding:10px 0;color:#6b7280;">Email Pembeli</td>
                                        <td style="padding:10px 0;font-weight:700;color:#111827;">' . e($request->email) . '</td>
                                    </tr>
                                    <tr style="border-bottom:1px solid #f3f4f6;">
                                        <td style="padding:10px 0;color:#6b7280;">Produk</td>
                                        <td style="padding:10px 0;font-weight:700;color:#111827;">' . e($request->product ?? '-') . '</td>
                                    </tr>
                                    <tr style="border-bottom:1px solid #f3f4f6;">
                                        <td style="padding:10px 0;color:#6b7280;">Nomor Order</td>
                                        <td style="padding:10px 0;font-weight:700;color:#111827;">' . e($request->order_id ?? '-') . '</td>
                                    </tr>
                                    <tr style="border-bottom:1px solid #f3f4f6;">
                                        <td style="padding:10px 0;color:#6b7280;">Token</td>
                                        <td style="padding:10px 0;font-size:12px;color:#6b7280;">' . e($request->token ?? '-') . '</td>
                                    </tr>
                                    <tr>
                                        <td style="padding:10px 0;color:#6b7280;vertical-align:top;">Deskripsi Masalah</td>
                                        <td style="padding:10px 0;color:#111827;line-height:1.6;">' . nl2br(e($request->description)) . '</td>
                                    </tr>
                                </table>
                                <div style="margin-top:20px;padding:12px 16px;background:#fef2f2;border-radius:8px;font-size:13px;color:#dc2626;">
                                    Laporan dikirim pada: ' . now()->format('d M Y H:i') . ' WIB
                                </div>
                            </div>
                        </div>'
                     );
            });

            Log::info('Laporan masalah digital dikirim', [
                'from'    => $request->email,
                'product' => $request->product,
                'order'   => $request->order_id,
            ]);

            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            Log::error('Gagal kirim laporan masalah: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}