@extends('admin.layouts.app')
@section('page-title', 'Pengguna')

@section('content')
<div style="display:flex;flex-direction:column;gap:18px;">

  {{-- HEADER ROW --}}
  <div style="display:flex;align-items:center;justify-content:space-between;">
    <div>
      <div style="font-size:18px;font-weight:900;color:var(--ink);letter-spacing:-.3px;">Manajemen Pengguna</div>
      <div style="font-size:12px;color:var(--ink3);font-weight:600;margin-top:2px;">Total {{ $users->total() }} pengguna terdaftar</div>
    </div>
  </div>

  {{-- FILTER BAR --}}
  <div class="card" style="overflow:visible;">
    <div style="padding:14px 18px;">
      <form method="GET" action="{{ route('admin.users.index') }}" style="display:flex;gap:10px;align-items:center;flex-wrap:wrap;">
        <div class="searchbox" style="width:260px;">
          <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="var(--ink4)" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
          <input name="search" placeholder="Cari nama, email, username…" value="{{ request('search') }}">
        </div>
        <select name="status" style="padding:7px 12px;border-radius:10px;border:1.5px solid var(--line);background:var(--bg);font-family:var(--font);font-size:12.5px;color:var(--ink2);outline:none;">
          <option value="">Semua Status</option>
          <option value="active" {{ request('status')==='active'?'selected':'' }}>Aktif</option>
          <option value="suspended" {{ request('status')==='suspended'?'selected':'' }}>Suspended</option>
        </select>
        <button type="submit" class="btn btn-primary">Filter</button>
        @if(request('search') || request('status'))
          <a href="{{ route('admin.users.index') }}" class="btn btn-ghost">Reset</a>
        @endif
      </form>
    </div>
  </div>

  {{-- TABLE --}}
  <div class="card">
    <table>
      <thead>
        <tr>
          <th>Pengguna</th>
          <th>Username</th>
          <th>Role</th>
          <th>Bergabung</th>
          <th>Status</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse($users as $user)
        <tr>
          <td>
            <div class="ucell">
              <div class="uav" style="background:linear-gradient(135deg,#38bdf8,#818cf8);">{{ strtoupper(substr($user->name,0,2)) }}</div>
              <div>
                <div class="uname">{{ $user->name }}</div>
                <div class="uslug">{{ $user->email }}</div>
              </div>
            </div>
          </td>
          <td class="num">{{ $user->username }}</td>
          <td>
            @if($user->role === 'admin')
              <span class="badge b-adm">Admin</span>
            @else
              <span class="badge" style="color:var(--ink3);background:var(--bg);">User</span>
            @endif
          </td>
          <td class="num">{{ $user->created_at->format('d M Y') }}</td>
          <td>
            @if($user->is_suspended ?? false)
              <span class="badge b-off">Suspended</span>
            @else
              <span class="badge b-ok">Aktif</span>
            @endif
          </td>
          <td>
            <div style="display:flex;gap:6px;align-items:center;">
              <a href="{{ route('admin.users.show', $user) }}" class="btn btn-ghost" style="padding:5px 10px;font-size:11.5px;">Detail</a>

              @if($user->id !== auth()->id())
                @if($user->is_suspended ?? false)
                  <form method="POST" action="{{ route('admin.users.unsuspend', $user) }}">
                    @csrf @method('PATCH')
                    <button type="submit" class="btn btn-primary" style="padding:5px 10px;font-size:11.5px;">Aktifkan</button>
                  </form>
                @else
                  <form method="POST" action="{{ route('admin.users.suspend', $user) }}"
                        onsubmit="return confirm('Suspend {{ $user->username }}?')">
                    @csrf @method('PATCH')
                    <button type="submit" class="btn btn-danger" style="padding:5px 10px;font-size:11.5px;">Suspend</button>
                  </form>
                @endif
              @endif
            </div>
          </td>
        </tr>
        @empty
        <tr><td colspan="6" style="text-align:center;padding:30px;color:var(--ink4);">Tidak ada pengguna ditemukan</td></tr>
        @endforelse
      </tbody>
    </table>

    {{-- PAGINATION --}}
    @if($users->hasPages())
    <div style="padding:14px 18px;border-top:1.5px solid var(--line);display:flex;justify-content:space-between;align-items:center;">
      <div style="font-size:12px;color:var(--ink3);font-weight:600;">
        Menampilkan {{ $users->firstItem() }}–{{ $users->lastItem() }} dari {{ $users->total() }}
      </div>
      <div style="display:flex;gap:4px;">
        @if($users->onFirstPage())
          <span class="btn btn-ghost" style="padding:5px 10px;font-size:12px;opacity:.4;cursor:default;">← Prev</span>
        @else
          <a href="{{ $users->previousPageUrl() }}" class="btn btn-ghost" style="padding:5px 10px;font-size:12px;">← Prev</a>
        @endif
        @if($users->hasMorePages())
          <a href="{{ $users->nextPageUrl() }}" class="btn btn-primary" style="padding:5px 10px;font-size:12px;">Next →</a>
        @else
          <span class="btn btn-primary" style="padding:5px 10px;font-size:12px;opacity:.4;cursor:default;">Next →</span>
        @endif
      </div>
    </div>
    @endif
  </div>

</div>
@endsection