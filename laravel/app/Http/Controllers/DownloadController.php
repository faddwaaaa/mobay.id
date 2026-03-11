<?php

namespace App\Http\Controllers;

use App\Models\DownloadToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DownloadController extends Controller
{
    // Tampilkan halaman verifikasi email
    public function show(string $token)
    {
        $downloadToken = DownloadToken::where('token', $token)->firstOrFail();

        if ($downloadToken->isExpired()) {
            return view('digital.download-error', ['message' => 'Link download sudah kadaluarsa.']);
        }

        if ($downloadToken->isMaxDownloads()) {
            return view('digital.download-error', ['message' => 'Batas maksimal download sudah tercapai.']);
        }

        return view('digital.download-verify', [
            'token'      => $token,
            'product'    => $downloadToken->order->product->name,
            'remaining'  => $downloadToken->max_downloads - $downloadToken->download_count,
            'expires_at' => $downloadToken->expires_at->format('d M Y H:i'),
        ]);
    }

    // Verifikasi email & langsung download
    public function verify(Request $request, string $token)
    {
        $request->validate(['email' => 'required|email']);

        $downloadToken = DownloadToken::where('token', $token)->firstOrFail();

        if ($downloadToken->isExpired()) {
            return view('digital.download-error', ['message' => 'Link download sudah kadaluarsa.']);
        }

        if ($downloadToken->isMaxDownloads()) {
            return view('digital.download-error', ['message' => 'Batas maksimal download sudah tercapai.']);
        }

        if (strtolower($request->email) !== strtolower($downloadToken->buyer_email)) {
            return back()->withErrors(['email' => 'Email tidak sesuai dengan email pembelian.'])->withInput();
        }

        // Tambah download count
        $downloadToken->increment('download_count');

        // Ambil file
        $filePath = $downloadToken->order->product->file_path;

        if (!Storage::exists($filePath)) {
            return view('digital.download-error', ['message' => 'File tidak ditemukan, hubungi CS.']);
        }

        return Storage::download($filePath, basename($filePath));
    }
}