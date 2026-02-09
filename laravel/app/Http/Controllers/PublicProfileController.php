<?php

namespace App\Http\Controllers;

use App\Models\UserProfile;
use App\Models\Click;
use Illuminate\Http\Request;

class PublicProfileController extends Controller
{
    public function profile($username)
    {
        $profile = UserProfile::where('username', $username)
            ->where('is_public', true)
            ->with(['links' => function($query) {
                $query->where('is_active', true)
                    ->orderBy('position');
            }, 'socialLinks'])
            ->firstOrFail();

        // Increment views
        $profile->increment('views');

        return view('public.profile', compact('profile'));
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

        // Increment click count
        $link->increment('clicks');

        // Record click details
        Click::create([
            'link_id' => $link->id,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'referrer' => request()->header('referer')
        ]);

        return redirect($link->url);
    }
}