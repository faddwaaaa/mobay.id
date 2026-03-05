@extends('admin.layouts.app')
@section('page-title', 'Dashboard')

@section('content')
<div style="display:flex;flex-direction:column;gap:20px;">

  {{-- HERO BANNER --}}
  <div style="border-radius:18px;background:linear-gradient(130deg,#0284c7 0%,#0ea5e9 50%,#38bdf8 100%);padding:26px 30px;display:flex;align-items:center;justify-content:space-between;position:relative;overflow:hidden;">
    <div style="position:absolute;right:-40px;top:-60px;width:200px;height:200px;border-radius:50%;background:rgba(255,255,255,.07);pointer-events:none;"></div>
    <div style="position:absolute;right:120px;bottom:-70px;width:160px;height:160px;border-radius:50%;background:rgba(255,255,255,.05);pointer-events:none;"></div>
    <div style="z-index:1;">
      <div style="font-size:19px;font-weight:900;color:white;margin-bottom:5px;">Halo, {{ auth()->user()->name }} 👋</div>
      <div style="font-size:12.5px;color:rgba(255,255,255,.78);font-weight:600;line-height:1.55;max-width:340px;">Platform Payou.id berjalan normal. Pantau aktivitas pengguna dan link di bawah ini.</div>
      <div style="display:flex;gap:8px;margin-top:14px;">
        <div style="display:flex;align-items:center;gap:5px;background:rgba(255,255,255,.16);border:1px solid rgba(255,255,255,.22);color:white;font-size:11.5px;font-weight:700;padding:5px 11px;border-radius:99px;">
          <div style="width:6px;height:6px;border-radius:50%;background:#6ee7b7;"></div>Platform aktif
        </div>
        <div style="display:flex;align-items:center;gap:5px;background:rgba(255,255,255,.16);border:1px solid rgba(255,255,255,.22);color:white;font-size:11.5px;font-weight:700;padding:5px 11px;border-radius:99px;">
          <div style="width:6px;height:6px;border-radius:50%;background:#fde68a;"></div>{{ $stats['new_users_today'] }} user baru hari ini
        </div>
      </div>
    </div>
    <div style="display:flex;gap:14px;z-index:1;">
      @foreach([['val' => number_format($stats['total_users']), 'lbl' => 'Total User'], ['val' => number_format($stats['total_links']), 'lbl' => 'Total Link'], ['val' => number_format($stats['total_clicks_today']), 'lbl' => 'Klik Hari Ini']] as $hc)
      <div style="background:rgba(255,255,255,.15);backdrop-filter:blur(10px);border:1px solid rgba(255,255,255,.22);border-radius:14px;padding:14px 18px;text-align:center;min-width:82px;">
        <div style="font-size:22px;font-weight:900;color:white;letter-spacing:-.5px;">{{ $hc['val'] }}</div>
        <div style="font-size:10.5px;color:rgba(255,255,255,.72);font-weight:700;margin-top:3px;">{{ $hc['lbl'] }}</div>
      </div>
      @endforeach
    </div>
  </div>

  {{-- MAIN GRID --}}
  <div style="display:grid;grid-template-columns:1fr 300px;gap:18px;align-items:start;">

    {{-- LEFT --}}
    <div style="display:flex;flex-direction:column;gap:18px;">

      {{-- CHART --}}
      <div class="card">
        <div class="card-head">
          <div><div class="card-title">Klik 7 Hari Terakhir</div><div class="card-sub">{{ now()->subDays(6)->format('d M') }} — {{ now()->format('d M Y') }}</div></div>
        </div>
        <div style="padding:18px 20px;">
          <div style="display:flex;align-items:flex-end;gap:9px;height:100px;">
            @foreach($clicksChart as $i => $day)
            @php $max = $clicksChart->max('total') ?: 1; $pct = round(($day->total / $max) * 100); $isToday = $i === count($clicksChart)-1; @endphp
            <div style="flex:1;display:flex;flex-direction:column;align-items:center;gap:5px;">
              <div style="width:100%;border-radius:6px 6px 0 0;min-height:4px;height:{{ $pct }}%;background:{{ $isToday ? 'linear-gradient(180deg,#7dd3fc,#0ea5e9)' : '#e0f2fe' }};transition:filter .15s;"></div>
              <div style="font-size:9px;color:var(--ink4);font-family:var(--mono);">{{ \Carbon\Carbon::parse($day->date)->format('d/m') }}</div>
            </div>
            @endforeach
          </div>
          <div style="display:flex;justify-content:space-between;align-items:center;margin-top:12px;">
            <div style="font-size:12px;color:var(--ink3);font-weight:600;">Total minggu ini: <b style="color:var(--ink);">{{ number_format($clicksChart->sum('total')) }} klik</b></div>
          </div>
        </div>
      </div>

      {{-- USER TABLE --}}
      <div class="card">
        <div class="card-head">
          <div><div class="card-title">Pengguna Terbaru</div><div class="card-sub">5 pendaftaran terakhir</div></div>
          <a href="{{ route('admin.users.index') }}" class="card-action">Lihat semua →</a>
        </div>
        <table>
          <thead><tr><th>Pengguna</th><th>Username</th><th>Bergabung</th><th>Status</th></tr></thead>
          <tbody>
            @foreach($recentUsers as $user)
            <tr>
              <td>
                <div class="ucell">
                  <div class="uav" style="background:linear-gradient(135deg,#38bdf8,#818cf8)">{{ strtoupper(substr($user->name,0,2)) }}</div>
                  <div>
                    <div class="uname">{{ $user->name }}</div>
                    <div class="uslug">{{ $user->email }}</div>
                  </div>
                </div>
              </td>
              <td class="num">{{ $user->username }}</td>
              <td class="num">{{ $user->created_at->format('d M Y') }}</td>
              <td>
                @if($user->is_suspended ?? false)
                  <span class="badge b-off">Suspend</span>
                @elseif($user->role === 'admin')
                  <span class="badge b-adm">Admin</span>
                @else
                  <span class="badge b-ok">Aktif</span>
                @endif
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>

    </div>

    {{-- RIGHT --}}
    <div style="display:flex;flex-direction:column;gap:18px;">

      {{-- QUICK METRICS --}}
      <div class="card">
        <div class="card-head"><div><div class="card-title">Ringkasan</div><div class="card-sub">Hari ini</div></div></div>
        @foreach([
          ['ico'=>'🧑‍💻','bg'=>'#e0f2fe','lbl'=>'User baru hari ini','val'=>$stats['new_users_today'],'tag'=>'+'.($stats['new_users_today']),'tc'=>'tup'],
          ['ico'=>'✅','bg'=>'#dcfce7','lbl'=>'Total user aktif','val'=>$stats['active_users'],'tag'=>null,'tc'=>''],
          ['ico'=>'🔒','bg'=>'#fee2e2','lbl'=>'Akun tersuspend','val'=>$stats['suspended_users'],'tag'=>$stats['suspended_users']>0?'Perlu tinjauan':null,'tc'=>'tdown'],
        ] as $m)
        <div style="display:flex;align-items:center;gap:12px;padding:13px 18px;border-bottom:1.5px solid var(--line);">
          <div style="width:34px;height:34px;border-radius:10px;background:{{ $m['bg'] }};display:flex;align-items:center;justify-content:center;font-size:16px;flex-shrink:0;">{{ $m['ico'] }}</div>
          <div>
            <div style="font-size:11.5px;color:var(--ink3);font-weight:600;">{{ $m['lbl'] }}</div>
            <div style="font-size:17px;font-weight:900;color:var(--ink);letter-spacing:-.3px;">{{ $m['val'] }}</div>
          </div>
          @if($m['tag'])
          <span style="margin-left:auto;font-size:10px;font-weight:800;padding:2px 7px;border-radius:6px;{{ $m['tc']==='tup'?'color:#16a34a;background:#dcfce7;':'color:var(--rose);background:#fee2e2;' }}">{{ $m['tag'] }}</span>
          @endif
        </div>
        @endforeach
      </div>

      {{-- TOP LINKS --}}
      <div class="card">
        <div class="card-head"><div><div class="card-title">Top Link Hari Ini</div><div class="card-sub">Terbanyak diklik</div></div></div>
        @foreach($topLinks as $i => $link)
        <div style="display:flex;align-items:center;gap:11px;padding:12px 18px;border-bottom:1.5px solid var(--line);">
          <div style="font-size:11px;font-weight:900;font-family:var(--mono);width:18px;text-align:center;color:{{ $i===0?'#f59e0b':'#7dd3fc' }};">#{{ $i+1 }}</div>
          <div style="flex:1;min-width:0;">
            <div style="font-size:12.5px;font-weight:800;color:var(--ink);">{{ $link->link_title ?? 'Link #'.$link->link_id }}</div>
            <div style="font-size:10.5px;color:var(--ink4);font-family:var(--mono);overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">ID: {{ $link->link_id }}</div>
          </div>
          <div style="font-size:12.5px;font-weight:900;color:var(--sky5);font-family:var(--mono);">{{ number_format($link->total_clicks) }}</div>
        </div>
        @endforeach
        @if($topLinks->isEmpty())
        <div style="padding:20px;text-align:center;color:var(--ink4);font-size:12px;">Belum ada data klik hari ini</div>
        @endif
      </div>

    </div>
  </div>
</div>
@endsection