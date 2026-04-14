<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Page;
use App\Models\Click;
use Illuminate\Http\Request;

class BioController extends Controller
{
    public function index(Request $request)
    {
        // Query dari User agar semua user tampil, meski belum punya page
        $query = User::with(['pages'])
            ->withCount('clicks')           // dari tabel clicks, FK: user_id
            ->withCount('links')            // total link milik user
            ->withSum('links', 'views')     // total views dari kolom views di tabel links
            ->where('role', 'user');        // hanya tampilkan role user, bukan admin

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(fn($q) => $q
                ->where('name', 'like', "%{$search}%")
                ->orWhere('username', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")
            );
        }

        if ($request->filled('status')) {
            match($request->status) {
                'active'  => $query->where('is_suspended', false),
                'suspend' => $query->where('is_suspended', true),
                default   => null,
            };
        }

        if ($request->filled('plan')) {
            $query->where('subscription_plan', $request->plan);
        }

        $users = $query->latest()->paginate(15)->withQueryString();

        $total  = User::where('role', 'user')->count();
        $active = User::where('role', 'user')->where('is_suspended', false)->count();

        $stats = [
            'total_pages'     => $total,
            'new_pages_today' => User::where('role', 'user')->whereDate('created_at', today())->count(),
            'total_clicks'    => Click::count(),
            'clicks_percent'  => 8,
            'active_pages'    => $active,
            'active_percent'  => $total > 0 ? round($active / $total * 100) : 0,
            'reported_pages'  => 0,
        ];

        return view('admin.bio.index', compact('users', 'stats'));
    }

    public function show(string $username)
    {
        $user  = User::where('username', $username)->with(['profile', 'socialLinks'])->firstOrFail();

        // Klik dihitung dari tabel clicks lewat relasi user->clicks
        // QR Scan dihitung dari tabel qr_scans lewat relasi user->qrScans
        $links = $user->links()->orderByDesc('id')->get();

        $stats = [
            'total_clicks' => $user->clicks()->count(),
            'total_views'  => $user->links()->sum('views'),
            'total_links'  => $user->links()->count(),
        ];

        return view('admin.bio.show', compact('user', 'links', 'stats'));
    }

    public function suspend(int $id)
    {
        $user = User::findOrFail($id);
        $user->update(['is_suspended' => true]);

        return back()->with('success', "Halaman @{$user->username} berhasil disuspend.");
    }

    public function unsuspend(int $id)
    {
        $user = User::findOrFail($id);
        $user->update(['is_suspended' => false]);

        return back()->with('success', "Halaman @{$user->username} berhasil diaktifkan.");
    }

    public function verify(int $id)
    {
        $user = User::findOrFail($id);
        $user->update(['is_verified' => true]);

        return back()->with('success', "@{$user->username} berhasil diverifikasi.");
    }
}