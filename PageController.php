<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PageController extends Controller
{
    /**
     * Show service page
     */
    public function service()
    {
        return view('pages.service');
    }

    /**
     * Show FAQ page
     */
    public function faq()
    {
        return view('pages.faq');
    }
}
