<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QRCodeController extends Controller
{
    /**
     * Display the QR Code page
     *
     * @return \Illuminate\View\View
     */
    public function show()
    {
        $user = Auth::user();
        
        // Ambil data user
        $userSlug = $user->slug ?? $user->username;
        
        // Ambil statistik scan (sesuaikan dengan struktur database Anda)
        // Ini contoh, sesuaikan dengan model dan relasi Anda
        $totalScans = $user->qr_scans ?? 0;
        $todayScans = $this->getTodayScans($user);
        
        return view('qr-code', compact('userSlug', 'totalScans', 'todayScans'));
    }
    
    /**
     * Get today's QR scan count
     *
     * @param  \App\Models\User  $user
     * @return int
     */
    private function getTodayScans($user)
    {
        // Contoh implementasi - sesuaikan dengan struktur database Anda
        // Asumsi ada tabel qr_scans dengan kolom user_id dan created_at
        
        // return $user->qrScans()
        //     ->whereDate('created_at', today())
        //     ->count();
        
        // Atau jika disimpan di cache/session:
        return $user->today_qr_scans ?? 0;
    }
    
    /**
     * Track QR Code scan (untuk API atau AJAX)
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function trackScan(Request $request)
    {
        try {
            $user = Auth::user();
            
            // Increment total scans
            $user->increment('qr_scans');
            
            // Track today's scans (contoh menggunakan cache)
            // Cache::increment("qr_scans_today_{$user->id}", 1, now()->endOfDay());
            
            // Atau simpan ke database log scans
            // QRScan::create([
            //     'user_id' => $user->id,
            //     'ip_address' => $request->ip(),
            //     'user_agent' => $request->userAgent(),
            //     'scanned_at' => now(),
            // ]);
            
            return response()->json([
                'success' => true,
                'total_scans' => $user->qr_scans,
                'message' => 'Scan tracked successfully'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to track scan'
            ], 500);
        }
    }
}
