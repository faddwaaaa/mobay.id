<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserProfile;
use App\Models\Click;
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

        // Default page pertama
        $page = $user->pages->first();

        return view('public.index', compact(
            'user',
            'page'
        ));
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

        // Increment counter
        $link->increment('clicks');

        // Record detail click
        Click::create([
            'link_id'    => $link->id,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'referrer'   => request()->header('referer')
        ]);

        return redirect($link->url);
    }
}
