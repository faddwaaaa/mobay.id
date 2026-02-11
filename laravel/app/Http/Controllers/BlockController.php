<?php

namespace App\Http\Controllers;

use App\Models\Block;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class BlockController extends Controller
{
    public function store(Request $request)
{
    $request->validate([
        'page_id' => 'required|exists:pages,id',
        'type' => 'required|string'
    ]);

    $page = Page::where('id', $request->page_id)
        ->where('user_id', auth()->id())
        ->firstOrFail();

    $position = $page->blocks()->max('position') + 1;

    $content = [];

    if ($request->type === 'text') {
        $content = ['text' => $request->input('content.text')];
    }

    if ($request->type === 'link') {
        $content = [
            'title' => $request->input('content.title'),
            'url'   => $request->input('content.url'),
        ];
    }

    if ($request->type === 'image' && $request->hasFile('image')) {
        $path = $request->file('image')->store('blocks', 'public');
        $content = ['image' => $path];
    }

    if ($request->type === 'video' && $request->hasFile('video')) {
        $path = $request->file('video')->store('blocks', 'public');
        $content = ['video' => $path];
    }

    $page->blocks()->create([
        'type' => $request->type,
        'content' => $content,
        'position' => $position,
    ]);

    return response()->json(['success' => true]);
}


    public function update(Request $request, Block $block)
{
    abort_if($block->page->user_id !== auth()->id(), 403);

    $content = $block->content;

    if ($block->type === 'text') {
        $content['text'] = $request->input('content.text');
    }

    if ($block->type === 'link') {
        $content['title'] = $request->input('content.title');
        $content['url'] = $request->input('content.url');
    }

    if ($block->type === 'image' && $request->hasFile('image')) {
        $path = $request->file('image')->store('blocks', 'public');
        $content['image'] = $path;
    }

    if ($block->type === 'video' && $request->hasFile('video')) {
        $path = $request->file('video')->store('blocks', 'public');
        $content['video'] = $path;
    }

    $block->update(['content' => $content]);

    return response()->json(['success' => true]);
}


    public function destroy(Block $block)
    {
        abort_if($block->page->user_id !== Auth::id(), 403);

        // Delete files if exists
        if (isset($block->content['image'])) {
            Storage::disk('public')->delete($block->content['image']);
        }
        if (isset($block->content['video'])) {
            Storage::disk('public')->delete($block->content['video']);
        }

        $block->delete();

        return response()->json(['success' => true]);
    }

    public function reorder(Request $request)
    {
        $request->validate([
            'order' => 'required|array',
            'order.*.id' => 'required|exists:blocks,id',
            'order.*.position' => 'required|integer',
        ]);

        foreach ($request->order as $item) {
            Block::where('id', $item['id'])->update(['position' => $item['position']]);
        }

        return response()->json(['success' => true]);
    }
}
