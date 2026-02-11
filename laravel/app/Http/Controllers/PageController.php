<?php

namespace App\Http\Controllers;

use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class PageController extends Controller
{
    public function index(Request $request)
    {
        $pages = Page::where('user_id', Auth::id())
            ->orderBy('created_at', 'asc')
            ->get();

        return view('pages.index', compact('pages'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
        ]);

        Page::create([
            'user_id' => Auth::id(),
            'title' => $request->title,
            'slug' => Str::slug($request->title),
        ]);

        return redirect()->route('links.index')
            ->with('success', 'Halaman berhasil ditambahkan');
    }

    public function update(Request $request, Page $page)
    {
        abort_if($page->user_id !== Auth::id(), 403);

        $request->validate([
            'title' => 'required|string|max:255',
        ]);

        $page->update([
            'title' => $request->title,
            'slug' => Str::slug($request->title),
        ]);

        return back()->with('success', 'Halaman berhasil diupdate');
    }

    public function destroy(Page $page)
    {
        abort_if($page->user_id !== Auth::id(), 403);

        $page->delete();

        return back()->with('success', 'Halaman berhasil dihapus');
    }
}