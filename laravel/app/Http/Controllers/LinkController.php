<?php

namespace App\Http\Controllers;

use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LinkController extends Controller
{
    public function index()
    {
        $pages = Page::where('user_id', Auth::id())
                    ->orderBy('position')
                    ->get();
        
        return view('dashboard.links.index', compact('pages'));
    }
}