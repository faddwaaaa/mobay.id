@extends('admin.layouts.app')
@section('page-title', 'Pengaturan')

@section('content')
<div style="max-width:600px;display:flex;flex-direction:column;gap:18px;">

  <div style="font-size:18px;font-weight:900;color:var(--ink);letter-spacing:-.3px;">Pengaturan Platform</div>

  <div class="card">
    <div class="card-head"><div class="card-title">Konfigurasi Umum</div></div>
    <div style="padding:22px;">
      <form method="POST" action="{{ route('admin.settings.update') }}" style="display:flex;flex-direction:column;gap:16px;">
        @csrf @method('PUT')

        {{-- Maintenance Mode --}}
        <div style="display:flex;align-items:center;justify-content:space-between;padding:14px 16px;background:var(--bg);border-radius:12px;border:1.5px solid var(--line);">
          <div>
            <div style="font-size:13px;font-weight:800;color:var(--ink);">Maintenance Mode</div>
            <div style="font-size:11.5px;color:var(--ink3);margin-top:2px;">Nonaktifkan akses publik sementara</div>
          </div>
          <label style="position:relative;display:inline-block;width:44px;height:24px;cursor:pointer;">
            <input type="checkbox" name="maintenance" style="opacity:0;width:0;height:0;" id="toggle-maintenance">
            <span style="position:absolute;top:0;left:0;right:0;bottom:0;background:var(--sky2);border-radius:99px;transition:.3s;"></span>
            <span style="position:absolute;content:'';height:18px;width:18px;left:3px;bottom:3px;background:white;border-radius:50%;transition:.3s;box-shadow:0 1px 4px rgba(0,0,0,.15);"></span>
          </label>
        </div>

        {{-- Registrasi --}}
        <div style="display:flex;align-items:center;justify-content:space-between;padding:14px 16px;background:var(--bg);border-radius:12px;border:1.5px solid var(--line);">
          <div>
            <div style="font-size:13px;font-weight:800;color:var(--ink);">Buka Registrasi</div>
            <div style="font-size:11.5px;color:var(--ink3);margin-top:2px;">Izinkan pendaftaran akun baru</div>
          </div>
          <label style="position:relative;display:inline-block;width:44px;height:24px;cursor:pointer;">
            <input type="checkbox" name="open_registration" checked style="opacity:0;width:0;height:0;">
            <span style="position:absolute;top:0;left:0;right:0;bottom:0;background:var(--sky5);border-radius:99px;transition:.3s;"></span>
            <span style="position:absolute;content:'';height:18px;width:18px;left:23px;bottom:3px;background:white;border-radius:50%;transition:.3s;box-shadow:0 1px 4px rgba(0,0,0,.15);"></span>
          </label>
        </div>

        {{-- Max Links --}}
        <div>
          <label style="display:block;font-size:12px;font-weight:800;color:var(--ink2);margin-bottom:6px;">Batas Link per User</label>
          <input type="number" name="max_links" value="50" min="1"
            style="width:100%;padding:10px 14px;border-radius:10px;border:1.5px solid var(--line);background:var(--bg);font-family:var(--font);font-size:13px;color:var(--ink);outline:none;"
            onfocus="this.style.borderColor='#38bdf8'" onblur="this.style.borderColor='var(--line)'">
          <div style="font-size:11px;color:var(--ink4);margin-top:4px;font-weight:600;">Jumlah maksimal link yang bisa dibuat per pengguna</div>
        </div>

        <div style="padding-top:4px;">
          <button type="submit" class="btn btn-primary">💾 Simpan Pengaturan</button>
        </div>
      </form>
    </div>
  </div>

  {{-- INFO SISTEM --}}
  <div class="card">
    <div class="card-head"><div class="card-title">Info Sistem</div></div>
    <div style="padding:16px 20px;display:flex;flex-direction:column;gap:10px;">
      @foreach([['lbl'=>'Laravel Version','val'=>app()->version()],['lbl'=>'PHP Version','val'=>PHP_VERSION],['lbl'=>'Environment','val'=>app()->environment()],['lbl'=>'Server Time','val'=>now()->format('d M Y, H:i:s')]] as $row)
      <div style="display:flex;justify-content:space-between;align-items:center;padding:8px 0;border-bottom:1.5px solid var(--line);">
        <span style="font-size:12px;color:var(--ink3);font-weight:600;">{{ $row['lbl'] }}</span>
        <span style="font-size:12px;font-weight:800;color:var(--ink);font-family:var(--mono);">{{ $row['val'] }}</span>
      </div>
      @endforeach
    </div>
  </div>

</div>
@endsection