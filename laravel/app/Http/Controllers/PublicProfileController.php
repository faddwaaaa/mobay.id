<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserProfile;
use App\Models\Click;
use App\Models\ProfileView;
use Illuminate\Http\Request;

class PublicProfileController extends Controller
{
    public function profile($username)
    {
        $user = User::where('username', $username)
            ->with([
                'pages.blocks',
                'products.images'
            ])
            ->firstOrFail();

        $page = $user->pages->first();

        return view('public.profile', compact(
            'user',
            'page'
        ));
    }

    /**
     * ✅ BARU: Catat kunjungan halaman profil publik
     * Dipanggil via AJAX dari halaman publik saat pertama kali dibuka
     */
    public function trackProfileView($username)
    {
        $user = User::where('username', $username)->firstOrFail();

        ProfileView::create(['user_id' => $user->id]);

        return response()->json(['success' => true]);
    }

    public function redirect($username, $linkId)
    {
        $profile = UserProfile::where('username', $username)
            ->where('is_public', true)
            ->firstOrFail();

        $link = $profile->user->links()
            ->where('id', $linkId)
            ->where('is_active', true)
            ->firstOrFail();

        $link->increment('clicks');

        Click::create([
            'link_id'    => $link->id,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'referrer'   => request()->header('referer')
        ]);

        return redirect($link->url);
    }
}