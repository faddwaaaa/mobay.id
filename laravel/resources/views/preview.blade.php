<!DOCTYPE html>
<html>
<head>
    <title>Preview {{ $user->username }}</title>
</head>
<body>

    <h1>{{ $user->name }}</h1>
    <p>@{{ $user->username }}</p>

    @if($page)
        <h2>Page Aktif</h2>

        @foreach ($page->blocks as $block)
            <div style="margin-bottom:10px;">
                {{ $block->title ?? 'Block' }}
            </div>
        @endforeach
    @else
        <p>Belum ada page aktif</p>
    @endif

</body>
</html>
