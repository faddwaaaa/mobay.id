<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Block;

class BlockController extends Controller
{
    public function store(Request $request)
    {
        Block::create([
            'page_id' => $request->page_id,
            'type' => $request->type,
            'content' => $request->content,
            'position' => Block::where('page_id', $request->page_id)->count() + 1
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
}

