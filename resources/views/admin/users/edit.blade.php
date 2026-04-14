{{-- ============================================================
     resources/views/admin/users/edit.blade.php
============================================================ --}}
@extends('admin.layouts.app')
@section('page-title', 'Edit Pengguna')

@section('content')
<div style="max-width:560px;">
  <a href="{{ route('admin.users.show', $user) }}" style="display:inline-flex;align-items:center;gap:6px;font-size:12.5px;font-weight:700;color:var(--sky5);text-decoration:none;margin-bottom:18px;">
    ← Kembali ke Detail
  </a>

  <div class="card">
    <div class="card-head"><div class="card-title">Edit Data Pengguna</div></div>
    <div style="padding:22px;">
      <form method="POST" action="{{ route('admin.users.update', $user) }}" style="display:flex;flex-direction:column;gap:16px;">
        @csrf @method('PUT')

        @foreach([['name'=>'name','lbl'=>'Nama Lengkap','type'=>'text','val'=>$user->name],['name'=>'email','lbl'=>'Email','type'=>'email','val'=>$user->email]] as $f)
        <div>
          <label style="display:block;font-size:12px;font-weight:800;color:var(--ink2);margin-bottom:6px;">{{ $f['lbl'] }}</label>
          <input type="{{ $f['type'] }}" name="{{ $f['name'] }}" value="{{ old($f['name'], $f['val']) }}"
            style="width:100%;padding:10px 14px;border-radius:10px;border:1.5px solid var(--line);background:var(--bg);font-family:var(--font);font-size:13px;color:var(--ink);outline:none;transition:border-color .15s;"
            onfocus="this.style.borderColor='#38bdf8'" onblur="this.style.borderColor='var(--line)'">
          @error($f['name'])<div style="font-size:11px;color:var(--rose);margin-top:4px;font-weight:700;">{{ $message }}</div>@enderror
        </div>
        @endforeach

        <div>
          <label style="display:block;font-size:12px;font-weight:800;color:var(--ink2);margin-bottom:6px;">Role</label>
          <select name="role" style="width:100%;padding:10px 14px;border-radius:10px;border:1.5px solid var(--line);background:var(--bg);font-family:var(--font);font-size:13px;color:var(--ink);outline:none;">
            <option value="user"  {{ $user->role==='user' ?'selected':'' }}>User</option>
            <option value="admin" {{ $user->role==='admin'?'selected':'' }}>Admin</option>
          </select>
        </div>

        <div style="display:flex;gap:10px;margin-top:4px;">
          <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
          <a href="{{ route('admin.users.show', $user) }}" class="btn btn-ghost">Batal</a>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection