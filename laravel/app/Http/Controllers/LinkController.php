<?php

namespace App\Http\Controllers;

use App\Models\Link;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LinkController extends Controller
{
    public function index()
    {
        /** @var User $user */
        $user = Auth::user();

        $links = $user->links()
            ->orderBy('position', 'asc')
            ->get();

        return view('links.index', compact('links'));
    }

    public function store(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();

        $request->validate([
            'title' => 'required|string|max:100',
            'url' => 'required|url',
            'icon' => 'nullable|string|max:50',
        ]);

        $position = $user->links()->max('position') ?? 0;

        Link::create([
            'user_id' => $user->id,
            'title' => $request->title,
            'url' => $request->url,
            'icon' => $request->icon,
            'position' => $position + 1,
            'is_active' => true,
        ]);

        return redirect()->back()->with('success', 'Link added successfully!');
    }
}
