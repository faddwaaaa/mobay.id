@extends('admin.layouts.app')
@section('page-title', 'Detail Pengguna')

@section('content')
<div style="display:flex;flex-direction:column;gap:18px;">

  {{-- BACK --}}
  <a href="{{ route('admin.users.index') }}" style="display:inline-flex;align-items:center;gap:6px;font-size:12.5px;font-weight:700;color:var(--sky5);text-decoration:none;">
    ← Kembali ke Daftar Pengguna
  </a>

  <div style="display:grid;grid-template-columns:320px 1fr;gap:18px;align-items:start;">

    {{-- PROFILE CARD --}}
    <div style="display:flex;flex-direction:column;gap:14px;">
      <div class="card">
        <div style="padding:24px 20px;text-align:center;border-bottom:1.5px solid var(--line);">
          <div style="width:64px;height:64px;border-radius:16px;background:linear-gradient(135deg,#38bdf8,#818cf8);display:flex;align-items:center;justify-content:center;font-size:22px;font-weight:900;color:white;margin:0 auto 12px;">
            {{ strtoupper(substr($user->name,0,2)) }}
          </div>
          <div style="font-size:16px;font-weight:900;color:var(--ink);">{{ $user->name }}</div>
          <div style="font-size:12px;color:var(--ink3);font-family:var(--mono);margin-top:3px;"><span>@</span>{{ $user->username }}</div>
          <div style="margin-top:10px;">
            @if($user->is_suspended ?? false)
              <span class="badge b-off">Suspended</span>
            @elseif($user->role === 'admin')
              <span class="badge b-adm">Administrator</span>
            @else
              <span class="badge b-ok">Aktif</span>
            @endif
          </div>
        </div>
        <div style="padding:16px 20px;display:flex;flex-direction:column;gap:10px;">
          @foreach([['lbl'=>'Email','val'=>$user->email],['lbl'=>'Bergabung','val'=>$user->created_at->format('d M Y, H:i')],['lbl'=>'Role','val'=>ucfirst($user->role)]] as $row)
          <div style="display:flex;justify-content:space-between;align-items:center;">
            <span style="font-size:11.5px;color:var(--ink3);font-weight:600;">{{ $row['lbl'] }}</span>
            <span style="font-size:12px;font-weight:700;color:var(--ink);">{{ $row['val'] }}</span>
          </div>
          @endforeach
        </div>
      </div>

      {{-- ACTIONS --}}
      <div class="card">
        <div class="card-head"><div class="card-title">Aksi</div></div>
        <div style="padding:14px 18px;display:flex;flex-direction:column;gap:8px;">
          <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-primary" style="justify-content:center;">✏️ Edit Data</a>

          @if($user->id !== auth()->id())
            @if($user->is_suspended ?? false)
              <form method="POST" action="{{ route('admin.users.unsuspend', $user) }}">
                @csrf @method('PATCH')
                <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;background:#dcfce7;color:#16a34a;box-shadow:none;">✓ Aktifkan Akun</button>
              </form>
            @else
              <form method="POST" action="{{ route('admin.users.suspend', $user) }}"
                    onsubmit="return confirm('Yakin suspend @{{ $user->username }}?')">
                @csrf @method('PATCH')
                <button type="submit" class="btn btn-danger" style="width:100%;justify-content:center;">🔒 Suspend Akun</button>
              </form>
            @endif

            <form method="POST" action="{{ route('admin.users.destroy', $user) }}"
                  onsubmit="return confirm('HAPUS permanen akun {{ $user->name }}? Tidak bisa dibatalkan!')">
              @csrf @method('DELETE')
              <button type="submit" class="btn" style="width:100%;justify-content:center;background:#fee2e2;color:var(--rose);">🗑 Hapus Akun</button>
            </form>
          @endif
        </div>
      </div>
    </div>

    {{-- INFO KANAN --}}
    <div class="card">
      <div class="card-head">
        <div><div class="card-title">Informasi Akun</div><div class="card-sub">Detail lengkap pengguna</div></div>
      </div>
      <div style="padding:20px;display:flex;flex-direction:column;gap:14px;">
        @foreach([
          ['lbl'=>'Nama Lengkap','val'=>$user->name],
          ['lbl'=>'Username','val'=>'@'.$user->username],
          ['lbl'=>'Email','val'=>$user->email],
          ['lbl'=>'Role','val'=>ucfirst($user->role)],
          ['lbl'=>'Status','val'=>($user->is_suspended??false)?'Suspended':'Aktif'],
          ['lbl'=>'Tanggal Daftar','val'=>$user->created_at->format('d M Y, H:i')],
          ['lbl'=>'Terakhir Update','val'=>$user->updated_at->format('d M Y, H:i')],
        ] as $row)
        <div style="display:flex;align-items:center;padding:10px 0;border-bottom:1.5px solid var(--line);">
          <div style="width:160px;font-size:12px;color:var(--ink3);font-weight:600;flex-shrink:0;">{{ $row['lbl'] }}</div>
          <div style="font-size:13px;font-weight:700;color:var(--ink);">{{ $row['val'] }}</div>
        </div>
        @endforeach
      </div>
    </div>

  </div>
</div>
@endsection