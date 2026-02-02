@extends('layouts.dashboard')

@section('content')
@php
    $user = auth()->user();
    $activePage = $pages->first();
@endphp

<div class="max-w-6xl mx-auto">

    <!-- HEADER -->
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900 mb-2">Link Saya</h1>
        <p class="text-gray-600">
            Link Saya:
            <span class="text-blue-600 font-medium">
                {{ url('/' . $user->username) }}
            </span>
        </p>
    </div>

    <!-- DUA KOLOM -->
    <div class="flex flex-col lg:flex-row gap-6">

        <!-- KIRI -->
        <div class="lg:w-2/3 space-y-6">

            <!-- YOUR PAGES -->
            <div class="bg-white rounded-xl shadow">
                <div class="px-6 py-4 border-b">
                    <h2 class="font-bold text-gray-900">Your Pages</h2>
                </div>

                @foreach ($pages as $page)
                <div class="px-6 py-4 border-b flex items-center justify-between hover:bg-gray-50">
                    <h3 class="font-medium text-gray-900">
                        {{ $page->title }}
                    </h3>

                    <input type="checkbox" {{ $page->is_active ? 'checked' : '' }}>
                </div>
                @endforeach

                <div class="px-6 py-4">
                    <form action="/pages" method="POST">
                        @csrf
                        <input type="hidden" name="title" value="Page {{ $pages->count() + 1 }}">
                        <button class="flex items-center text-blue-600 hover:text-blue-800 font-medium">
                            + Add new page
                        </button>
                    </form>
                </div>
            </div>

            <!-- BLOCK LIST -->
            <div class="bg-white rounded-xl shadow">
                <div class="px-6 py-4 border-b">
                    <h2 class="font-bold text-gray-900">Block List</h2>
                </div>

                <!-- ADD BLOCK -->
                <div class="p-6 border-b">
                    <h3 class="font-medium text-gray-900 mb-3">Add new block</h3>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">

                        <button onclick="addBlock('text')" class="border rounded-lg p-4 hover:bg-blue-50">
                            Text
                        </button>

                        <button onclick="addBlock('image')" class="border rounded-lg p-4 hover:bg-blue-50">
                            Image
                        </button>

                        <button onclick="addBlock('link')" class="border rounded-lg p-4 hover:bg-blue-50">
                            Link
                        </button>

                        <button onclick="addBlock('video')" class="border rounded-lg p-4 hover:bg-blue-50">
                            Video
                        </button>

                    </div>
                </div>

                <!-- BLOCK DATA -->
                <div class="p-6">
                    @if($activePage && $activePage->blocks->count())
                    <ul id="blockList" class="space-y-3">
                        @foreach($activePage->blocks as $block)
                        <li
                            data-id="{{ $block->id }}"
                            class="p-4 border rounded-lg bg-gray-50 cursor-move">
                            {{ strtoupper($block->type) }}
                        </li>
                        @endforeach
                    </ul>
                    @else
                    <div class="text-center py-8 text-gray-400 border border-dashed rounded-lg">
                        <p>Belum ada block</p>
                        <p class="text-sm">Tambahkan block untuk menampilkan konten</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- KANAN - PREVIEW -->
        <div class="mx-auto w-[280px] h-[560px] rounded-[36px] border-[10px] border-gray-900 shadow-xl bg-black overflow-hidden">

            <!-- NOTCH -->
            <div class="h-6 bg-black flex justify-center items-end">
                <div class="w-20 h-1.5 bg-gray-700 rounded-full mb-1"></div>
            </div>

            <!-- IFRAME -->
            <iframe
                id="preview"
                src="{{ url('/preview/'.$user->username) }}"
                class="w-full h-full bg-white"
                frameborder="0">
            </iframe>

        </div>

    </div>
</div>

<!-- SORTABLE -->
<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
<script>
if (document.getElementById('blockList')) {
    Sortable.create(blockList, {
        animation: 150,
        onEnd() {
            let order = [];
            document.querySelectorAll('#blockList li').forEach((el, index) => {
                order.push({
                    id: el.dataset.id,
                    position: index + 1
                });
            });

            fetch('/blocks/reorder', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(order)
            }).then(() => {
                document.getElementById('preview').contentWindow.location.reload();
            });
        }
    });
}

function addBlock(type) {
    fetch('/blocks', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            page_id: {{ $activePage->id ?? 'null' }},
            type: type,
            content: { text: 'Contoh ' + type }
        })
    }).then(() => {
        location.reload();
    });
}
</script>
@endsection
