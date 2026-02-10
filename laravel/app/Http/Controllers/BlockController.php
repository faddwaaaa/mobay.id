<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Block;
use Illuminate\Support\Facades\Storage;

class BlockController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'page_id' => 'required|exists:pages,id',
            'type' => 'required|in:text,link,video,image',
        ]);

        $content = [];

        // TEXT
        if ($request->type === 'text') {
            $content['text'] = $request->input('content.text');
        }

        // LINK
        if ($request->type === 'link') {
            $content = [
                'title' => $request->input('content.title'),
                'url'   => $request->input('content.url'),
            ];
        }

        // VIDEO (YouTube)
        if ($request->type === 'video') {
            $content['url'] = $request->input('content.url');
        }

        // IMAGE UPLOAD
        if ($request->type === 'image' && $request->hasFile('image')) {
            $path = $request->file('image')->store('blocks', 'public');
            $content['image'] = $path;
        }
        

        Block::create([
            'page_id'  => $request->page_id,
            'type'     => $request->type,
            'content'  => $content,
            'position' => Block::where('page_id', $request->page_id)->max('position') + 1
        ]);

        return response()->json(['success' => true]);
    }

    public function reorder(Request $request)
    {
        foreach ($request->all() as $item) {
            Block::where('id', $item['id'])
                ->update(['position' => $item['position']]);
        }

        return response()->json(['success' => true]);
    }

    public function destroy(Block $block)
{
    $pageId = $block->page_id;
    $block->delete();

    $blocks = Block::where('page_id', $pageId)
        ->orderBy('position')
        ->get();

    foreach ($blocks as $index => $item) {
        $item->update(['position' => $index + 1]);
    }

    return response()->json(['success' => true]);
}

}

