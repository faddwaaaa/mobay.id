@extends('admin.layouts.app')
@section('page-title', 'Pengajuan Banding')
@section('content')

<style>
.section-hdr { display:flex; align-items:center; justify-content:space-between; margin-bottom:20px; gap:12px; flex-wrap:wrap; }
.section-hdr h1 { font-size:20px; font-weight:900; color:var(--ink); letter-spacing:-.3px; }
.filter-tabs { display:flex; gap:6px; margin-bottom:18px; flex-wrap:wrap; }
.ftab { padding:7px 14px; border-radius:9px; font-size:12px; font-weight:800; cursor:pointer; border:1.5px solid var(--line); background:white; color:var(--ink3); text-decoration:none; transition:all .15s; display:flex; align-items:center; gap:5px; }
.ftab:hover { background:var(--bg); border-color:#d0d9ff; color:var(--ink2); }
.ftab.active { background:#eff3ff; border-color:#d0d9ff; color:var(--b500); }
.ftab .cnt { background:var(--b500); color:white; border-radius:99px; padding:1px 7px; font-size:10.5px; }
.ftab.active .cnt { background:white; color:var(--b500); }

.appeal-wrap { background:white; border:1.5px solid var(--line); border-radius:16px; overflow:hidden; }
.appeal-head {
    padding:16px 18px; border-bottom:1.5px solid var(--line);
    display:flex; justify-content:space-between; align-items:center; gap:10px; flex-wrap:wrap;
}
.appeal-title { font-size:13px; font-weight:900; color:var(--ink); }
.appeal-sub { font-size:11.5px; color:var(--ink3); margin-top:2px; }
.appeal-count { font-size:12px; font-weight:700; color:var(--ink3); }
.appeal-list { display:grid; gap:14px; padding:16px; background:linear-gradient(180deg, #fff 0%, #f8fbff 100%); }
.appeal-card {
    background:#fff; border:1.5px solid var(--line); border-radius:14px;
    padding:16px; display:grid; gap:14px;
    box-shadow:0 1px 2px rgba(12,21,51,.03);
}
.appeal-card + .appeal-card { position:relative; }
.appeal-card + .appeal-card::before {
    content:''; position:absolute; top:-8px; left:16px; right:16px; height:1px;
    background:linear-gradient(90deg, transparent, #dbe7ff, transparent);
}
.appeal-top { display:flex; justify-content:space-between; align-items:flex-start; gap:10px; flex-wrap:wrap; }
.ticket {
    display:inline-flex; align-items:center; padding:4px 9px; border-radius:8px;
    background:var(--bg); border:1.5px solid var(--line); font-family:var(--mono); font-size:11.5px; color:var(--ink2);
}
.badge-pnd { background:#fef9c3; color:#92400e; border:1.5px solid #fde68a; }
.badge-app { background:#dcfce7; color:#15803d; border:1.5px solid #bbf7d0; }
.badge-rej { background:#fee2e2; color:#b91c1c; border:1.5px solid #fecaca; }
.badge { padding:4px 10px; border-radius:999px; font-size:11px; font-weight:800; white-space:nowrap; display:inline-flex; align-items:center; }
.u-mini { display:flex; align-items:center; gap:10px; }
.u-av { width:42px; height:42px; border-radius:12px; background:var(--bg); border:1.5px solid var(--line); display:flex; align-items:center; justify-content:center; overflow:hidden; flex-shrink:0; }
.u-av img { width:100%; height:100%; object-fit:cover; }
.u-name { font-size:13px; font-weight:800; color:var(--ink); }
.u-slug { font-size:11px; color:var(--ink3); font-family:var(--mono); }
.appeal-grid { display:grid; grid-template-columns:repeat(3, minmax(0, 1fr)); gap:10px; }
.meta-box { border:1px solid #edf2ff; border-radius:12px; padding:12px; background:#fbfcff; }
.meta-label { font-size:10.5px; font-weight:800; letter-spacing:.4px; text-transform:uppercase; color:var(--ink4); margin-bottom:4px; }
.meta-value { font-size:12.5px; color:var(--ink2); line-height:1.55; }
.btn-detail { display:inline-flex; align-items:center; gap:6px; padding:8px 13px; background:var(--bg); border:1.5px solid var(--line); border-radius:10px; font-size:11.5px; font-weight:800; color:var(--ink2); text-decoration:none; transition:all .15s; }
.btn-detail:hover { background:#eff3ff; border-color:#d0d9ff; color:var(--b500); }
.appeal-actions { display:flex; justify-content:flex-end; }
.empty { text-align:center; padding:40px 0; }
.empty svg { margin:0 auto 12px; display:block; }
.empty p { font-size:13px; font-weight:700; color:var(--ink4); }
.empty span { font-size:11.5px; color:var(--ink4); }
.pager {
    padding:14px 16px; border-top:1.5px solid var(--line);
    display:flex; justify-content:space-between; align-items:center; gap:10px; flex-wrap:wrap;
}
.pager-info { font-size:12px; color:var(--ink3); font-weight:700; }
.pager-nav { display:flex; align-items:center; gap:8px; flex-wrap:wrap; }
.pager-btn, .pager-current {
    min-width:38px; height:38px; padding:0 12px; border-radius:10px;
    display:inline-flex; align-items:center; justify-content:center;
    border:1.5px solid var(--line); background:#fff; text-decoration:none;
    font-size:12px; font-weight:800;
}
.pager-btn { color:var(--ink2); }
.pager-btn:hover { background:#eff3ff; border-color:#d0d9ff; color:var(--b500); }
.pager-current { background:#eff3ff; border-color:#d0d9ff; color:var(--b500); }
.pager-dots { color:var(--ink4); font-weight:800; padding:0 4px; }

@media (max-width: 860px) {
    .appeal-grid { grid-template-columns:1fr 1fr; }
}
@media (max-width: 560px) {
    .appeal-list { padding:12px; }
    .appeal-card { padding:14px; }
    .appeal-grid { grid-template-columns:1fr; }
    .appeal-actions { justify-content:stretch; }
    .appeal-actions .btn-detail { width:100%; justify-content:center; }
}
</style>

<div class="section-hdr">
    <div>
        <h1>Pengajuan Banding</h1>
        <div style="font-size:11.5px;color:var(--ink3);font-weight:600;margin-top:3px;">
            Dipisah per kartu dan dibatasi 5 data per halaman agar lebih ringan dibuka.
        </div>
    </div>
</div>

<div class="filter-tabs">
    @foreach(['pending' => 'Menunggu', 'approved' => 'Disetujui', 'rejected' => 'Ditolak', 'all' => 'Semua'] as $key => $label)
    <a href="{{ route('admin.appeals.index', ['status' => $key]) }}"
       class="ftab {{ $status === $key ? 'active' : '' }}">
        {{ $label }}
        <span class="cnt">{{ $counts[$key] }}</span>
    </a>
    @endforeach
</div>

@if(session('success'))
<div style="background:#dcfce7;border:1.5px solid #bbf7d0;border-radius:10px;padding:12px 16px;margin-bottom:16px;font-size:12.5px;font-weight:700;color:#15803d;">
    {{ session('success') }}
</div>
@endif

<div class="appeal-wrap">
    <div class="appeal-head">
        <div>
            <div class="appeal-title">Daftar Banding</div>
            <div class="appeal-sub">Data ke-6 otomatis masuk halaman berikutnya, mirip pola pagination pencarian.</div>
        </div>
        <div class="appeal-count">
            @if($appeals->count())
                Menampilkan {{ $appeals->firstItem() }}–{{ $appeals->lastItem() }} dari {{ $appeals->total() }} data
            @else
                Tidak ada data
            @endif
        </div>
    </div>

    @if($appeals->isEmpty())
    <div class="empty">
        <svg width="40" height="40" fill="none" viewBox="0 0 24 24" stroke="var(--line)" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
        </svg>
        <p>Tidak ada pengajuan banding</p>
        <span>{{ $status === 'pending' ? 'Belum ada banding yang menunggu tinjauan.' : 'Tidak ada data untuk filter ini.' }}</span>
    </div>
    @else
    <div class="appeal-list">
        @foreach($appeals as $appeal)
        @php
            $bc = match($appeal->status) { 'pending' => 'badge-pnd', 'approved' => 'badge-app', 'rejected' => 'badge-rej', default => '' };
            $bl = match($appeal->status) { 'pending' => 'Menunggu', 'approved' => 'Disetujui', 'rejected' => 'Ditolak', default => $appeal->status };
        @endphp
        <article class="appeal-card">
            <div class="appeal-top">
                <div class="ticket">{{ $appeal->ticket_code }}</div>
                <span class="badge {{ $bc }}">{{ $bl }}</span>
            </div>

            <div class="u-mini">
                <div class="u-av">
                    @if($appeal->user?->avatar)
                        <img src="{{ asset('storage/' . $appeal->user->avatar) }}" alt="">
                    @else
                        <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="var(--ink4)" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    @endif
                </div>
                <div>
                    <div class="u-name">{{ $appeal->user?->name ?? '–' }}</div>
                    <div class="u-slug">@{{ $appeal->user?->username ?? '–' }}</div>
                </div>
            </div>

            <div class="appeal-grid">
                <div class="meta-box">
                    <div class="meta-label">Diajukan</div>
                    <div class="meta-value">
                        {{ $appeal->created_at->format('d M Y, H:i') }}
                        <div style="font-size:11px;color:var(--ink3);margin-top:2px;">{{ $appeal->created_at->diffForHumans() }}</div>
                    </div>
                </div>
                <div class="meta-box">
                    <div class="meta-label">Ditinjau Oleh</div>
                    <div class="meta-value">
                        {{ $appeal->reviewer?->name ?? '—' }}
                        @if($appeal->reviewed_at)
                        <div style="font-size:11px;color:var(--ink3);margin-top:2px;">{{ $appeal->reviewed_at->format('d M Y, H:i') }}</div>
                        @endif
                    </div>
                </div>
                <div class="meta-box">
                    <div class="meta-label">Status Saat Ini</div>
                    <div class="meta-value">{{ $bl }}</div>
                </div>
            </div>

            <div class="appeal-actions">
                <a href="{{ route('admin.appeals.show', $appeal) }}" class="btn-detail">
                    Detail
                    <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                </a>
            </div>
        </article>
        @endforeach
    </div>

    @if($appeals->hasPages())
    <div class="pager">
        <div class="pager-info">
            Halaman {{ $appeals->currentPage() }} dari {{ $appeals->lastPage() }}
        </div>
        <div class="pager-nav">
            @if($appeals->onFirstPage())
                <span class="pager-btn" style="opacity:.45;pointer-events:none;">Prev</span>
            @else
                <a href="{{ $appeals->previousPageUrl() }}" class="pager-btn">Prev</a>
            @endif

            @foreach($appeals->getUrlRange(max(1, $appeals->currentPage() - 2), min($appeals->lastPage(), $appeals->currentPage() + 2)) as $page => $url)
                @if($page == $appeals->currentPage())
                    <span class="pager-current">{{ $page }}</span>
                @else
                    <a href="{{ $url }}" class="pager-btn">{{ $page }}</a>
                @endif
            @endforeach

            @if($appeals->hasMorePages())
                <a href="{{ $appeals->nextPageUrl() }}" class="pager-btn">Next</a>
            @else
                <span class="pager-btn" style="opacity:.45;pointer-events:none;">Next</span>
            @endif
        </div>
    </div>
    @endif
    @endif
</div>

@endsection
