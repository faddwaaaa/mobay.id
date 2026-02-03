<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>{{ $user->name }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex justify-center">

    <div class="w-full max-w-md bg-white min-h-screen p-6">

        <!-- PROFILE -->
        <div class="text-center mb-6">
            <div class="w-24 h-24 mx-auto rounded-full bg-gray-300 mb-3"></div>
            <h1 class="text-xl font-bold">{{ $user->name }}</h1>
            <p class="text-gray-500 text-sm">{{ '@' . $user->username }}</p>
        </div>

        <!-- BLOCKS -->
        <div class="space-y-4">
            @if($page)
                @foreach($page->blocks as $block)

                    @if($block->type === 'text')
                        <p class="text-gray-800 text-center">
                            {{ $block->content['text'] ?? '' }}
                        </p>

                    @elseif($block->type === 'link')
                        <a href="{{ $block->content['url'] ?? '#' }}"
                           target="_blank"
                           class="block w-full text-center py-3 rounded-lg bg-blue-600 text-white font-medium">
                            {{ $block->content['title'] ?? 'Link' }}
                        </a>

                    @elseif($block->type === 'image')
                        <img src="{{ $block->content['url'] ?? '' }}"
                             class="w-full rounded-lg">

                    @endif

                @endforeach
            @else
                <p class="text-center text-gray-400">Belum ada konten</p>
            @endif
        </div>

    </div>

</body>
</html>
