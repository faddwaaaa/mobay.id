<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\User;

class LinkController extends Controller
{
    public function index()
    {
        /** @var User $user */
        $user = Auth::user(); // ✅ METHOD RESMI

        $pages = $user->pages()
            ->with('blocks')
            ->orderBy('position')
            ->get();

        return view('dashboard.links.index', compact('pages'));
    }
}

