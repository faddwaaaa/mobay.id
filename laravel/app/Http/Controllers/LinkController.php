<?php

namespace App\Http\Controllers;

<<<<<<< Updated upstream
use App\Models\Page;
=======
>>>>>>> Stashed changes
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LinkController extends Controller
{
    public function index()
    {
<<<<<<< Updated upstream
        $pages = Page::where('user_id', Auth::id())
                    ->orderBy('position')
                    ->get();
        
        return view('dashboard.links.index', compact('pages'));
    }
}
=======
        $user = Auth::user();

        // nanti kalau sudah ada tabel links, ini tinggal diganti
        $links = [];

        return view('dashboard.links.index', compact('user', 'links'));
    }
}
>>>>>>> Stashed changes
