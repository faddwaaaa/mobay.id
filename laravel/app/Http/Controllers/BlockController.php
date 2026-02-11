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
            'type' => 'required|in:text,link,image,video',
        ]);

        $page = Page::findOrFail($request->page_id);
        abort_if($page->user_id !== Auth::id(), 403);

        $content = [];
        
        if ($request->type === 'text') {
            $content['text'] = $request->input('content.text');
        }
        
        if ($request->type === 'link') {
            $content['title'] = $request->input('content.title');
            $content['url'] = $request->input('content.url');
        }
        
        if ($request->type === 'image' && $request->hasFile('image')) {
            $path = $request->file('image')->store('blocks/images', 'public');
            $content['image'] = $path;
        }
        
        if ($request->type === 'video' && $request->hasFile('video')) {
            $path = $request->file('video')->store('blocks/videos', 'public');
            $content['video'] = $path;
        }

        $lastPosition = Block::where('page_id', $page->id)->max('position') ?? 0;

        Block::create([
            'page_id' => $page->id,
            'type' => $request->type,
            'content' => $content,
            'position' => $lastPosition + 1,
        ]);

        return response()->json(['success' => true]);
    }

    public function update(Request $request, Block $block)
    {
        abort_if($block->page->user_id !== Auth::id(), 403);

        $content = $block->content;
        
        if ($request->type === 'text') {
            $content['text'] = $request->input('content.text');
        }
        
        if ($request->type === 'link') {
            $content['title'] = $request->input('content.title');
            $content['url'] = $request->input('content.url');
        }
        
        if ($request->type === 'image' && $request->hasFile('image')) {
            // Delete old image
            if (isset($content['image'])) {
                Storage::disk('public')->delete($content['image']);
            }
            $path = $request->file('image')->store('blocks/images', 'public');
            $content['image'] = $path;
        }
        
        if ($request->type === 'video' && $request->hasFile('video')) {
            // Delete old video
            if (isset($content['video'])) {
                Storage::disk('public')->delete($content['video']);
            }
            $path = $request->file('video')->store('blocks/videos', 'public');
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