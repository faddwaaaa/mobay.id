<?php

namespace App\Http\Controllers;

use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class PageController extends Controller
{
    /**
     * Store a newly created page
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
        ]);

        $slug = Str::slug($request->title);

        $originalSlug = $slug;
        $counter = 1;

        while (Page::where('user_id', Auth::id())
                ->where('slug', $slug)
                ->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        $page = Page::create([
            'user_id' => Auth::id(),
            'title' => $request->title,
            'slug' => $slug,
            'is_active' => true,
            'position' => (Page::where('user_id', Auth::id())->max('position') ?? 0) + 1,
        ]);

        return redirect()->route('links.index', [
            'page' => $page->id
        ])->with('success', 'Halaman berhasil dibuat!');
    }

    /**
     * Update the specified page
     */
    public function update(Request $request, Page $page)
    {
        // Pastikan page milik user yang login
        abort_if($page->user_id !== Auth::id(), 403);

        // Tidak bisa edit halaman "Utama"
        if (strtolower($page->title) === 'utama') {
            return response()->json([
                'success' => false,
                'message' => 'Halaman Utama tidak dapat diubah'
            ], 403);
        }

        $request->validate([
            'title' => 'required|string|max:255',
        ]);

        $slug = Str::slug($request->title);
        
        // Check if slug exists (except current page)
        $originalSlug = $slug;
        $counter = 1;
        while (Page::where('user_id', Auth::id())
                   ->where('slug', $slug)
                   ->where('id', '!=', $page->id)
                   ->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        $page->update([
            'title' => $request->title,
            'slug' => $slug,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Halaman berhasil diupdate'
        ]);
    }

    /**
     * Remove the specified page
     */
    public function destroy(Page $page)
    {
        // Pastikan page milik user yang login
        abort_if($page->user_id !== Auth::id(), 403);

        // Tidak bisa hapus halaman "Utama"
        if (strtolower($page->title) === 'utama') {
            return back()->with('error', 'Halaman Utama tidak dapat dihapus');
        }

        $page->delete();

        return redirect()->route('links.index')->with('success', 'Halaman berhasil dihapus!');
    }
}