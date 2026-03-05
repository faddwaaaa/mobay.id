@extends('admin.layouts.app')
@section('page-title', 'Semua Link')

@section('content')
<div style="display:flex;flex-direction:column;gap:18px;">

  <div>
    <div style="font-size:18px;font-weight:900;color:var(--ink);letter-spacing:-.3px;">Manajemen Link</div>
    <div style="font-size:12px;color:var(--ink3);font-weight:600;margin-top:2px;">Total {{ $links->total() }} link terdaftar</div>
  </div>

  {{-- FILTER --}}
  <div class="card" style="overflow:visible;">
    <div style="padding:14px 18px;">
      <form method="GET" action="{{ route('admin.links.index') }}" style="display:flex;gap:10px;align-items:center;flex-wrap:wrap;">
        <div class="searchbox" style="width:260px;">
          <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="var(--ink4)" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
          <input name="search" placeholder="Cari judul atau URL…" value="{{ request('search') }}">
        </div>
        <select name="status" style="padding:7px 12px;border-radius:10px;border:1.5px solid var(--line);background:var(--bg);font-family:var(--font);font-size:12.5px;color:var(--ink2);outline:none;">
          <option value="">Semua Status</option>
          <option value="active"   {{ request('status')==='active'?'selected':'' }}>Aktif</option>
          <option value="inactive" {{ request('status')==='inactive'?'selected':'' }}>Nonaktif</option>
        </select>
        <button type="submit" class="btn btn-primary">Filter</button>
        @if(request('search') || request('status'))
          <a href="{{ route('admin.links.index') }}" class="btn btn-ghost">Reset</a>
        @endif
      </form>
    </div>
  </div>

  {{-- TABLE --}}
  <div class="card">
    <table>
      <thead>
        <tr>
          <th>Judul Link</th>
          <th>Pemilik</th>
          <th>URL Tujuan</th>
          <th>Dibuat</th>
          <th>Status</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse($links as $link)
        <tr>
          <td>
            <div style="font-size:13px;font-weight:800;color:var(--ink);">{{ $link->content['title'] ?? 'Tanpa Judul' }}</div>
            <div style="font-size:10.5px;color:var(--ink4);font-family:var(--mono);">ID: {{ $link->id }}</div>
          </td>
          <td>
            @if($link->page?->user)
          <div class="ucell">
            <div class="uav" style="background:linear-gradient(135deg,#38bdf8,#818cf8);width:24px;height:24px;font-size:9px;">{{ strtoupper(substr($link->page->user->name,0,2)) }}</div>
            <div style="font-size:12px;font-weight:700;color:var(--ink);">{{ $link->page->user->username }}</div>
          </div>
          @else
          <span style="color:var(--ink4);font-size:12px;">—</span>
          @endif
          </td>
          <td>
            <div style="font-size:11.5px;font-family:var(--mono);color:var(--sky5);max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
             {{ $link->content['url'] ?? '—' }}
            </div>
          </td>
          <td class="num">{{ $link->created_at->format('d M Y') }}</td>
          <td>
            @if($link->is_active ?? true)
              <span class="badge b-ok">Aktif</span>
            @else
              <span class="badge b-off">Nonaktif</span>
            @endif
          </td>
          <td>
            <div style="display:flex;gap:6px;">
              <form method="POST" action="{{ route('admin.links.toggle', $link) }}">
                @csrf @method('PATCH')
                <button type="submit" class="btn {{ ($link->is_active??true) ? 'btn-danger' : 'btn-primary' }}" style="padding:5px 10px;font-size:11.5px;">
                  {{ ($link->is_active??true) ? 'Nonaktifkan' : 'Aktifkan' }}
                </button>
              </form>
              <form method="POST" action="{{ route('admin.links.destroy', $link) }}"
                    onsubmit="return confirm('Hapus link ini?')">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-danger" style="padding:5px 10px;font-size:11.5px;">Hapus</button>
              </form>
            </div>
          </td>
        </tr>
        @empty
        <tr><td colspan="6" style="text-align:center;padding:30px;color:var(--ink4);">Tidak ada link ditemukan</td></tr>
        @endforelse
      </tbody>
    </table>

    @if($links->hasPages())
    <div style="padding:14px 18px;border-top:1.5px solid var(--line);display:flex;justify-content:space-between;align-items:center;">
      <div style="font-size:12px;color:var(--ink3);font-weight:600;">Menampilkan {{ $links->firstItem() }}–{{ $links->lastItem() }} dari {{ $links->total() }}</div>
      <div style="display:flex;gap:4px;">
        @if($links->onFirstPage())
          <span class="btn btn-ghost" style="padding:5px 10px;font-size:12px;opacity:.4;cursor:default;">← Prev</span>
        @else
          <a href="{{ $links->previousPageUrl() }}" class="btn btn-ghost" style="padding:5px 10px;font-size:12px;">← Prev</a>
        @endif
        @if($links->hasMorePages())
          <a href="{{ $links->nextPageUrl() }}" class="btn btn-primary" style="padding:5px 10px;font-size:12px;">Next →</a>
        @else
          <span class="btn btn-primary" style="padding:5px 10px;font-size:12px;opacity:.4;cursor:default;">Next →</span>
        @endif
      </div>
    </div>
    @endif
  </div>

</div>
@endsection