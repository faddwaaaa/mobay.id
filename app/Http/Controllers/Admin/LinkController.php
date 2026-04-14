<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Block;
use Illuminate\Http\Request;

class LinkController extends Controller
{
    public function index(Request $request)
    {
        $links = Block::query()
            ->where('type', 'link')
            ->with('page.user')
            ->when($request->search, function ($q) use ($request) {
                $q->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(content, '$.title')) LIKE ?", ["%{$request->search}%"])
                  ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(content, '$.url')) LIKE ?", ["%{$request->search}%"]);
            })
            ->when($request->status === 'active',   fn($q) => $q->where('is_active', true))
            ->when($request->status === 'inactive', fn($q) => $q->where('is_active', false))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.links.index', compact('links'));
    }

    public function show($id)
    {
        $link = Block::where('type', 'link')->with('page.user')->findOrFail($id);
        return view('admin.links.show', compact('link'));
    }

    public function destroy($id)
    {
        $link = Block::where('type', 'link')->findOrFail($id);
        $link->delete();

        return redirect()->route('admin.links.index')
            ->with('success', 'Link berhasil dihapus.');
    }

    public function toggle($id)
    {
        $link = Block::where('type', 'link')->findOrFail($id);
        $link->update(['is_active' => !$link->is_active]);

        $status = $link->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return back()->with('success', "Link berhasil {$status}.");
    }
}