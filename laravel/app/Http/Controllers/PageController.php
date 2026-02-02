<?php

namespace App\Http\Controllers;

use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class PageController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255'
        ]);

        Page::create([
            'user_id' => Auth::id(),
            'title' => $request->title,
            'slug' => Str::slug($request->title),
            'position' => Page::where('user_id', Auth::id())->count() + 1
        ]);

        return back()->with('success', 'Page berhasil dibuat');
    }

    // 🔵 Method BARU: Update page
    public function update(Request $request, Page $page)
    {
        // Authorization check
        if ($page->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'title' => 'required|string|max:255'
        ]);

        $page->update([
            'title' => $request->title,
            'slug' => Str::slug($request->title)
        ]);

        return back()->with('success', 'Page berhasil diperbarui');
    }

    // 🔵 Method BARU: Delete page
    public function destroy(Page $page)
    {
        // Authorization check
        if ($page->user_id !== Auth::id()) {
            abort(403);
        }

        $page->delete();

        return back()->with('success', 'Page berhasil dihapus');
    }

    // 🔵 Method BARU: Edit form (optional)
    public function edit(Page $page)
    {
        if ($page->user_id !== Auth::id()) {
            abort(403);
        }

        return view('pages.edit', compact('page'));
    }
}