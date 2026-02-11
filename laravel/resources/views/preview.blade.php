<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>{{ $user->name }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <style>
        body {
            margin: 0;
            font-family: system-ui, sans-serif;
            background: #fff;
        }

        .container {
            max-width: 420px;
            margin: 0 auto;
            padding: 24px 16px;
        }

        .avatar {
            width: 96px;
            height: 96px;
            border-radius: 50%;
            background: #d1d5db;
            margin: 0 auto 12px;
            display: block;
        }

        h1 {
            text-align: center;
            font-size: 20px;
            margin: 8px 0 4px;
        }

        .username {
            text-align: center;
            color: #6b7280;
            font-size: 14px;
            margin-bottom: 12px;
        }

        .bio {
            text-align: center;
            font-size: 14px;
            margin-bottom: 24px;
        }

        .block {
            margin-bottom: 12px;
        }

        .block-text {
            font-size: 14px;
            text-align: center;
        }

        .block-link a {
            display: block;
            padding: 14px;
            border-radius: 12px;
            border: 1px solid #e5e7eb;
            text-align: center;
            text-decoration: none;
            color: #111;
            font-weight: 500;
        }

        .block-image img {
            width: 100%;
            border-radius: 12px;
        }

        .block-video iframe {
            width: 100%;
            height: 200px;
            border-radius: 12px;
            border: none;
        }
    </style>
</head>
<body>

<div class="container">

    @if($user->avatar)
        <img 
            src="{{ asset('storage/' . $user->avatar) }}" 
            class="avatar"
            style="object-fit:cover;"
        >
    @else
        <div class="avatar"></div>
    @endif

    <h1>{{ $user->name }}</h1>
    <div class="username">{{ '@' . $user->username }}</div>

    @if($user->bio)
        <div class="bio">{{ $user->bio }}</div>
    @endif

    @if(!$page)

        <div style="text-align:center; margin-top:15px; color:#9ca3af;">
            Belum ada Halaman.
        </div>

    @elseif($page->blocks->count() === 0)

        <div style="text-align:center; margin-top:15px; color:#9ca3af;">
            Halaman ini belum memiliki konten.
        </div>

    @else

        @foreach($page->blocks->sortBy('position') as $block)

            {{-- TEXT --}}
            @if($block->type === 'text')
                <div class="block block-text">
                    {{ $block->content['text'] ?? '' }}
                </div>
            @endif

            {{-- LINK --}}
            @if($block->type === 'link')
                <div class="block block-link">
                    <a href="{{ $block->content['url'] ?? '#' }}" target="_blank">
                        {{ $block->content['title'] ?? 'Link' }}
                    </a>
                </div>
            @endif

            {{-- IMAGE --}}
            @if($block->type === 'image')
                <div class="block block-image">
                    <img src="{{ asset('storage/' . $block->content['image']) }}">
                </div>
            @endif

            {{-- VIDEO --}}
            @if($block->type === 'video')
                @php
                    $url = $block->content['url'] ?? '';
                    parse_str(parse_url($url, PHP_URL_QUERY), $query);
                    $videoId = $query['v'] ?? '';

                    // kalau format youtu.be
                    if (!$videoId && str_contains($url, 'youtu.be/')) {
                        $videoId = basename(parse_url($url, PHP_URL_PATH));
                    }
                @endphp

                <div class="block block-video">
                    <iframe
                        src="https://www.youtube.com/embed/{{ $videoId }}"
                        allowfullscreen>
                    </iframe>
                </div>
            @endif
        @endforeach

    @endif

</div>

</body>
</html>
