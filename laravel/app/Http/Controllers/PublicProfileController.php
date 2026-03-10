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
            'products.images',
            'profile'           // ← tambah ini
        ])
        ->firstOrFail();

    $page    = $user->pages->first();
    $profile = $user->profile;

    // Parse social_links dari JSON kolom
    $socialLinks = [];
    if ($profile && $profile->social_links) {
        $decoded = is_array($profile->social_links)
            ? $profile->social_links
            : json_decode($profile->social_links, true);
        $socialLinks = is_array($decoded) ? $decoded : [];
    }

    // Fallback: cek kolom individual (telegram, instagram, dll)
    if (empty($socialLinks) && $profile) {
        $platforms = ['telegram','website','email_social','discord','tiktok',
                      'instagram','youtube','twitch','linkedin','twitter_x',
                      'facebook','behance','dribbble','whatsapp','spotify','threads'];
        foreach ($platforms as $p) {
            if (!empty($profile->$p)) {
                $key = $p === 'twitter_x' ? 'x' : ($p === 'email_social' ? 'email' : $p);
                $socialLinks[$key] = $profile->$p;
            }
        }
    }

    return view('public.profile', compact(
        'user',
        'page',
        'profile',
        'socialLinks'
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