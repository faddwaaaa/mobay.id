<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LinkController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // nanti kalau sudah ada tabel links, ini tinggal diganti
        $links = [];

        return view('dashboard.links.index', compact('user', 'links'));
    }
}
