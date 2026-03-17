{{-- resources/views/notifications/index.blade.php --}}
@extends('layouts.dashboard') {{-- sesuaikan dengan nama layout kamu --}}

@section('title', 'Semua Notifikasi')

@section('content')
<div class="notif-page-wrapper">

    {{-- Header --}}
    <div class="notif-page-header">
        <div class="notif-page-header-left">
            <h1 class="notif-page-title">
                <i class="fas fa-bell"></i> Notifikasi
            </h1>
            <p class="notif-page-sub">Semua aktivitas dan pembaruan akun kamu</p>
        </div>
        <div class="notif-page-header-right">
            @if($unreadCount > 0)
            <form method="POST" action="{{ route('notifications.markAll') }}">
                @csrf
                <button type="submit" class="notif-page-markall-btn">
                    <i class="fas fa-check-double"></i> Tandai Semua Dibaca
                    <span class="notif-page-unread-badge">{{ $unreadCount }}</span>
                </button>
            </form>
            @endif
        </div>
    </div>

    {{-- Search + Filter Bar --}}
    <div class="notif-page-toolbar">
        <form method="GET" action="{{ route('notifications.index') }}" class="notif-search-form">
            <div class="notif-search-wrap">
                <i class="fas fa-search notif-search-icon"></i>
                <input
                    type="text"
                    name="q"
                    value="{{ request('q') }}"
                    placeholder="Cari notifikasi..."
                    class="notif-search-input"
                    autocomplete="off"
                >
                @if(request('q'))
                <a href="{{ route('notifications.index', array_diff_key(request()->query(), ['q'=>''])) }}" class="notif-search-clear">
                    <i class="fas fa-times"></i>
                </a>
                @endif
            </div>

            {{-- Filter Tipe --}}
            <div class="notif-filter-tabs">
                @php
                    $types = [
                        ''         => ['label' => 'Semua',     'icon' => 'fas fa-th'],
                        'order'    => ['label' => 'Pesanan',   'icon' => 'fas fa-shopping-bag'],
                        'payment'  => ['label' => 'Pembayaran','icon' => 'fas fa-circle-check'],
                        'checkout' => ['label' => 'Checkout',  'icon' => 'fas fa-cash-register'],
                    ];
                    $activeType = request('type', '');
                @endphp
                @foreach($types as $val => $meta)
                <button
                    type="submit"
                    name="type"
                    value="{{ $val }}"
                    class="notif-filter-tab {{ $activeType === $val ? 'active' : '' }}"
                >
                    <i class="{{ $meta['icon'] }}"></i>
                    <span>{{ $meta['label'] }}</span>
                </button>
                @endforeach
            </div>
        </form>

        {{-- Hapus semua --}}
        <form method="POST" action="{{ route('notifications.destroyAll') }}" id="deleteAllForm" style="margin-left:auto">
            @csrf
            @method('DELETE')
            <button type="button" onclick="confirmDeleteAll()" class="notif-page-deleteall-btn" title="Hapus Semua">
                <i class="fas fa-trash-alt"></i>
                <span>Hapus Semua</span>
            </button>
        </form>
    </div>

    {{-- Info hasil pencarian --}}
    @if(request('q'))
    <div class="notif-search-result-info">
        <i class="fas fa-search"></i>
        Hasil pencarian untuk <strong>"{{ request('q') }}"</strong>
        &mdash; {{ $notifications->total() }} notifikasi ditemukan
    </div>
    @endif

    {{-- List --}}
    <div class="notif-page-list">
        @forelse($notifications as $notif)
        <div class="notif-page-item {{ $notif->is_read ? '' : 'unread' }}" data-id="{{ $notif->id }}">

            {{-- Icon --}}
            <div class="notif-page-icon {{ $notif->type }}">
                @php
                    $iconMap = [
                        'payment'  => 'fas fa-circle-check',
                        'checkout' => 'fas fa-cash-register',
                        'order'    => 'fas fa-shopping-bag',
                    ];
                @endphp
                <i class="{{ $iconMap[$notif->type] ?? ($notif->icon ?? 'fas fa-bell') }}"></i>
            </div>

            {{-- Konten --}}
            <div class="notif-page-content">
                <div class="notif-page-item-head">
                    <span class="notif-page-item-title">{{ $notif->title }}</span>
                    @if(!$notif->is_read)
                    <span class="notif-page-dot"></span>
                    @endif
                    <span class="notif-page-type-badge {{ $notif->type }}">
                        @php
                            $typeLabel = ['order'=>'Pesanan','payment'=>'Pembayaran','checkout'=>'Checkout'];
                        @endphp
                        {{ $typeLabel[$notif->type] ?? ucfirst($notif->type) }}
                    </span>
                </div>
                <p class="notif-page-msg">{{ $notif->message }}</p>
                <span class="notif-page-time">
                    <i class="fas fa-clock"></i>
                    {{ $notif->created_at->diffForHumans() }}
                    &middot; {{ $notif->created_at->format('d M Y, H:i') }}
                </span>
            </div>

            {{-- Aksi --}}
            <div class="notif-page-actions">
                @if(!$notif->is_read)
                <form method="POST" action="{{ route('notifications.read', $notif->id) }}" class="d-inline">
                    @csrf
                    <button type="submit" class="notif-page-action-btn read" title="Tandai Dibaca">
                        <i class="fas fa-check"></i>
                    </button>
                </form>
                @endif
                <form method="POST" action="{{ route('notifications.destroy', $notif->id) }}" class="d-inline" onsubmit="return confirm('Hapus notifikasi ini?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="notif-page-action-btn delete" title="Hapus">
                        <i class="fas fa-trash"></i>
                    </button>
                </form>
                @if($notif->link)
                <a href="{{ $notif->link }}" class="notif-page-action-btn link" title="Lihat Detail">
                    <i class="fas fa-arrow-right"></i>
                </a>
                @endif
            </div>
        </div>
        @empty
        @if(request('q') || request('type'))
        {{-- Empty karena filter/search → tampilan informatif --}}
        <div class="notif-page-empty">
            <div class="notif-page-empty-icon">
                <i class="fas fa-bell-slash"></i>
            </div>
            <h3>Tidak ada notifikasi</h3>
            <p>
                @if(request('q'))
                    Tidak ada notifikasi yang cocok dengan pencarian "<strong>{{ request('q') }}</strong>"
                @else
                    Tidak ada notifikasi dengan filter ini
                @endif
            </p>
            <a href="{{ route('notifications.index') }}" class="notif-page-reset-btn">
                <i class="fas fa-redo"></i> Reset Filter
            </a>
        </div>
        @else
        {{-- Empty state asli — sama persis dengan popup --}}
        <div class="notif-empty-state">
            <i class="fas fa-bell"></i>
            <p>Ups, belum ada notifikasi</p>
        </div>
        @endif
        @endforelse
    </div>

    {{-- Pagination --}}
    @if($notifications->hasPages())
    <div class="notif-page-pagination">
        {{-- Prev --}}
        @if($notifications->onFirstPage())
            <span class="notif-pag-btn disabled"><i class="fas fa-chevron-left"></i></span>
        @else
            <a href="{{ $notifications->previousPageUrl() }}&{{ http_build_query(request()->except('page')) }}" class="notif-pag-btn">
                <i class="fas fa-chevron-left"></i>
            </a>
        @endif

        {{-- Pages --}}
        @foreach($notifications->getUrlRange(max(1,$notifications->currentPage()-2), min($notifications->lastPage(),$notifications->currentPage()+2)) as $page => $url)
            @if($page == $notifications->currentPage())
                <span class="notif-pag-btn active">{{ $page }}</span>
            @else
                <a href="{{ $url }}&{{ http_build_query(request()->except('page')) }}" class="notif-pag-btn">{{ $page }}</a>
            @endif
        @endforeach

        {{-- Next --}}
        @if($notifications->hasMorePages())
            <a href="{{ $notifications->nextPageUrl() }}&{{ http_build_query(request()->except('page')) }}" class="notif-pag-btn">
                <i class="fas fa-chevron-right"></i>
            </a>
        @else
            <span class="notif-pag-btn disabled"><i class="fas fa-chevron-right"></i></span>
        @endif

        <span class="notif-pag-info">
            {{ $notifications->firstItem() }}–{{ $notifications->lastItem() }} dari {{ $notifications->total() }}
        </span>
    </div>
    @endif

</div>

<style>
/* ====== Wrapper ====== */
.notif-page-wrapper {
    max-width: 820px;
    margin: 0 auto;
    padding: 28px 20px 48px;
}

/* ====== Header ====== */
.notif-page-header {
    display: flex;
    align-items: flex-end;
    justify-content: space-between;
    gap: 16px;
    margin-bottom: 24px;
    flex-wrap: wrap;
}

.notif-page-title {
    font-family: var(--nav-font, 'Plus Jakarta Sans', sans-serif);
    font-size: 22px;
    font-weight: 800;
    color: #111827;
    margin: 0 0 4px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.notif-page-title i { color: var(--accent, #2356e8); font-size: 20px; }

.notif-page-sub {
    font-family: var(--nav-font, 'Plus Jakarta Sans', sans-serif);
    font-size: 13.5px;
    color: #6b7c93;
    margin: 0;
}

.notif-page-markall-btn {
    display: flex;
    align-items: center;
    gap: 7px;
    padding: 9px 16px;
    border-radius: 10px;
    border: none;
    background: var(--accent, #2356e8);
    color: #fff;
    font-family: var(--nav-font, 'Plus Jakarta Sans', sans-serif);
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    transition: background .15s;
}

.notif-page-markall-btn:hover { background: #1a46cc; }

.notif-page-unread-badge {
    background: rgba(255,255,255,.28);
    border-radius: 20px;
    padding: 1px 7px;
    font-size: 11px;
}

/* ====== Toolbar ====== */
.notif-page-toolbar {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 18px;
    flex-wrap: wrap;
}

.notif-search-form {
    display: flex;
    align-items: center;
    gap: 10px;
    flex: 1;
    flex-wrap: wrap;
}

.notif-search-wrap {
    position: relative;
    flex: 1;
    min-width: 180px;
}

.notif-search-icon {
    position: absolute;
    left: 13px;
    top: 50%;
    transform: translateY(-50%);
    color: #a0aec0;
    font-size: 13px;
    pointer-events: none;
}

.notif-search-input {
    width: 100%;
    padding: 10px 38px 10px 38px;
    border-radius: 10px;
    border: 1.5px solid #e2e8f0;
    font-family: var(--nav-font, 'Plus Jakarta Sans', sans-serif);
    font-size: 13.5px;
    color: #111827;
    background: #fff;
    outline: none;
    transition: border-color .15s, box-shadow .15s;
    box-sizing: border-box;
}

.notif-search-input:focus {
    border-color: var(--accent, #2356e8);
    box-shadow: 0 0 0 3px rgba(35,86,232,.1);
}

.notif-search-clear {
    position: absolute;
    right: 10px;
    top: 50%;
    transform: translateY(-50%);
    width: 22px; height: 22px;
    display: flex; align-items: center; justify-content: center;
    border-radius: 50%;
    background: #e2e8f0;
    color: #6b7c93;
    font-size: 10px;
    text-decoration: none;
    transition: background .15s;
}

.notif-search-clear:hover { background: #cbd5e0; }

/* Filter Tabs */
.notif-filter-tabs {
    display: flex;
    gap: 5px;
}

.notif-filter-tab {
    display: flex;
    align-items: center;
    gap: 5px;
    padding: 8px 13px;
    border-radius: 9px;
    border: 1.5px solid #e2e8f0;
    background: #fff;
    font-family: var(--nav-font, 'Plus Jakarta Sans', sans-serif);
    font-size: 12.5px;
    font-weight: 600;
    color: #6b7c93;
    cursor: pointer;
    transition: all .15s;
    white-space: nowrap;
}

.notif-filter-tab:hover {
    border-color: var(--accent, #2356e8);
    color: var(--accent, #2356e8);
    background: #f0f5ff;
}

.notif-filter-tab.active {
    background: var(--accent, #2356e8);
    border-color: var(--accent, #2356e8);
    color: #fff;
}

/* Hapus Semua */
.notif-page-deleteall-btn {
    display: flex;
    align-items: center;
    gap: 6px;
    padding: 9px 13px;
    border-radius: 10px;
    border: 1.5px solid #fed7d7;
    background: #fff5f5;
    color: #e53e3e;
    font-family: var(--nav-font, 'Plus Jakarta Sans', sans-serif);
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    transition: all .15s;
    white-space: nowrap;
}

.notif-page-deleteall-btn:hover {
    background: #fed7d7;
    border-color: #e53e3e;
}

/* ====== Search result info ====== */
.notif-search-result-info {
    background: #f0f5ff;
    border: 1px solid #c7d8ff;
    border-radius: 9px;
    padding: 10px 14px;
    font-family: var(--nav-font, 'Plus Jakarta Sans', sans-serif);
    font-size: 13px;
    color: #2356e8;
    margin-bottom: 14px;
    display: flex;
    align-items: center;
    gap: 8px;
}

/* ====== List ====== */
.notif-page-list {
    display: flex;
    flex-direction: column;
    gap: 0;
    border: 1.5px solid #e8edf5;
    border-radius: 14px;
    overflow: hidden;
    background: #fff;
}

.notif-page-item {
    display: flex;
    align-items: flex-start;
    gap: 14px;
    padding: 16px 18px;
    border-bottom: 1px solid #f0f4fb;
    transition: background .14s;
    position: relative;
}

.notif-page-item:last-child { border-bottom: none; }
.notif-page-item:hover { background: #f7f9ff; }
.notif-page-item.unread { background: #f0f5ff; }
.notif-page-item.unread:hover { background: #e8f0fe; }

/* Icon */
.notif-page-icon {
    width: 44px; height: 44px;
    border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    font-size: 16px;
    flex-shrink: 0;
    margin-top: 2px;
}

.notif-page-icon.order    { background: #fff3e0; color: #f57c00; }
.notif-page-icon.payment  { background: #e8f5e9; color: #2e7d32; }
.notif-page-icon.checkout { background: #eef2ff; color: #4338ca; }

/* Content */
.notif-page-content { flex: 1; min-width: 0; }

.notif-page-item-head {
    display: flex;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
    margin-bottom: 4px;
}

.notif-page-item-title {
    font-family: var(--nav-font, 'Plus Jakarta Sans', sans-serif);
    font-size: 14px;
    font-weight: 700;
    color: #111827;
}

.notif-page-dot {
    width: 8px; height: 8px;
    border-radius: 50%;
    background: var(--accent, #2356e8);
    flex-shrink: 0;
}

.notif-page-type-badge {
    font-family: var(--nav-font, 'Plus Jakarta Sans', sans-serif);
    font-size: 10.5px;
    font-weight: 700;
    padding: 2px 8px;
    border-radius: 20px;
    letter-spacing: .3px;
    text-transform: uppercase;
}

.notif-page-type-badge.order    { background: #fff3e0; color: #f57c00; }
.notif-page-type-badge.payment  { background: #e8f5e9; color: #2e7d32; }
.notif-page-type-badge.checkout { background: #eef2ff; color: #4338ca; }

.notif-page-msg {
    font-family: var(--nav-font, 'Plus Jakarta Sans', sans-serif);
    font-size: 13px;
    color: #4a5568;
    margin: 0 0 5px;
    line-height: 1.5;
}

.notif-page-time {
    font-family: var(--nav-font, 'Plus Jakarta Sans', sans-serif);
    font-size: 11.5px;
    color: #a0aec0;
    display: flex;
    align-items: center;
    gap: 5px;
}

/* Actions */
.notif-page-actions {
    display: flex;
    align-items: center;
    gap: 5px;
    flex-shrink: 0;
    opacity: 0;
    transition: opacity .15s;
}

.notif-page-item:hover .notif-page-actions { opacity: 1; }

.notif-page-action-btn {
    width: 32px; height: 32px;
    border-radius: 8px;
    border: none;
    display: flex; align-items: center; justify-content: center;
    font-size: 13px;
    cursor: pointer;
    text-decoration: none;
    transition: background .14s, color .14s;
}

.notif-page-action-btn.read   { background: #e8f5e9; color: #2e7d32; }
.notif-page-action-btn.delete { background: #fff5f5; color: #e53e3e; }
.notif-page-action-btn.link   { background: #eef2ff; color: #4338ca; }

.notif-page-action-btn.read:hover   { background: #c8e6c9; }
.notif-page-action-btn.delete:hover { background: #fed7d7; }
.notif-page-action-btn.link:hover   { background: #c7d2fe; }

/* ====== Empty State ====== */
.notif-page-empty {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 60px 20px;
    text-align: center;
    width: 100%;
}

.notif-page-empty-icon {
    width: 72px; height: 72px;
    border-radius: 20px;
    background: #f0f4fb;
    display: flex; align-items: center; justify-content: center;
    font-size: 30px;
    color: #c4cdd9;
    margin-bottom: 16px;
}

.notif-page-empty h3 {
    font-family: var(--nav-font, 'Plus Jakarta Sans', sans-serif);
    font-size: 16px; font-weight: 700;
    color: #374151; margin: 0 0 6px;
}

.notif-page-empty p {
    font-family: var(--nav-font, 'Plus Jakarta Sans', sans-serif);
    font-size: 13.5px; color: #9ca3af; margin: 0 0 16px;
}

.notif-page-reset-btn {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 9px 18px;
    border-radius: 9px;
    background: #f0f5ff;
    color: var(--accent, #2356e8);
    font-family: var(--nav-font, 'Plus Jakarta Sans', sans-serif);
    font-size: 13px; font-weight: 600;
    text-decoration: none;
    transition: background .15s;
}

.notif-page-reset-btn:hover { background: #e1ebff; }

/* ====== Pagination ====== */
.notif-page-pagination {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 5px;
    margin-top: 24px;
    flex-wrap: wrap;
}

.notif-pag-btn {
    min-width: 36px; height: 36px;
    border-radius: 9px;
    display: inline-flex; align-items: center; justify-content: center;
    padding: 0 8px;
    font-family: var(--nav-font, 'Plus Jakarta Sans', sans-serif);
    font-size: 13.5px; font-weight: 600;
    color: #4a5568;
    background: #fff;
    border: 1.5px solid #e2e8f0;
    text-decoration: none;
    transition: all .14s;
}

.notif-pag-btn:hover:not(.disabled):not(.active) {
    border-color: var(--accent, #2356e8);
    color: var(--accent, #2356e8);
    background: #f0f5ff;
}

.notif-pag-btn.active {
    background: var(--accent, #2356e8);
    border-color: var(--accent, #2356e8);
    color: #fff;
}

.notif-pag-btn.disabled { opacity: .4; cursor: not-allowed; }

.notif-pag-info {
    font-family: var(--nav-font, 'Plus Jakarta Sans', sans-serif);
    font-size: 12.5px;
    color: #a0aec0;
    margin-left: 8px;
}

/* ====== Dark Mode ====== */
body.dark .notif-page-title             { color: #f0f4ff; }
body.dark .notif-page-sub               { color: #8899b4; }
body.dark .notif-page-list              { background: #1e2535; border-color: #2d3748; }
body.dark .notif-page-item              { border-color: #252e42; }
body.dark .notif-page-item:hover        { background: #252e42; }
body.dark .notif-page-item.unread       { background: #1a2540; }
body.dark .notif-page-item.unread:hover { background: #1e2d50; }
body.dark .notif-page-item-title        { color: #e8edf8; }
body.dark .notif-page-msg               { color: #8899b4; }
body.dark .notif-search-input           { background: #1e2535; border-color: #2d3748; color: #e8edf8; }
body.dark .notif-filter-tab             { background: #1e2535; border-color: #2d3748; color: #8899b4; }
body.dark .notif-filter-tab:hover       { background: #1a2540; color: #7ea4f4; border-color: #7ea4f4; }
body.dark .notif-filter-tab.active      { background: var(--accent,#2356e8); color: #fff; }
body.dark .notif-pag-btn                { background: #1e2535; border-color: #2d3748; color: #8899b4; }
body.dark .notif-page-empty h3          { color: #c9d1de; }
body.dark .notif-search-result-info     { background: #1a2540; border-color: #2d3748; color: #7ea4f4; }
body.dark .notif-page-deleteall-btn     { background: #2d1f1f; border-color: #7b2f2f; color: #fc8181; }

/* ====== Empty State (sama dengan popup) ====== */
.notif-page-list .notif-empty-state {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    height: 340px;
    gap: 12px;
    border: 2px dashed #dde5f0;
    border-radius: 12px;
    margin: 14px;
}

.notif-page-list .notif-empty-state i { font-size: 36px; color: #c4cdd9; }
.notif-page-list .notif-empty-state p {
    font-family: var(--nav-font, 'Plus Jakarta Sans', sans-serif);
    font-size: 13.5px; margin: 0; color: #a0aec0;
}

body.dark .notif-page-list .notif-empty-state { border-color: #2d3748; }

/* ====== Responsive ====== */
@media (max-width: 600px) {
    .notif-filter-tabs { display: none; }
    .notif-page-header { flex-direction: column; align-items: flex-start; }
    .notif-page-actions { opacity: 1; }
}
</style>

<script>
function confirmDeleteAll() {
    if (confirm('Hapus semua notifikasi? Tindakan ini tidak bisa dibatalkan.')) {
        document.getElementById('deleteAllForm').submit();
    }
}
</script>
@endsection