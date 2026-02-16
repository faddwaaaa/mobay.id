<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LandingController extends Controller
{
    /**
     * Show landing/home page
     */
    public function index()
    {
        return view('landing.index');
    }

    /**
     * Show service page
     */
    public function service()
    {
        return view('landing.service');
    }

    /**
     * Show FAQ page
     */
    public function faq()
    {
        return view('landing.faq');
    }
}