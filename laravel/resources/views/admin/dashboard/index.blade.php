@extends('admin.layouts.app')
@section('page-title', 'Dashboard')

@push('styles')
<style>
/* ROW 1: Banner kiri + Grafik kanan — sejajar */
.top-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 20px;
  margin-bottom: 20px;
  align-items: stretch;
}

/* BANNER BIRU KIRI */
.banner {
  border-radius: 20px;
  background: linear-gradient(135deg, #152f80 0%, #1a3fa8 35%, #2356e8 70%, #3b82f6 100%);
  padding: 28px 28px;
  position: relative; overflow: hidden;
  display: flex; flex-direction: column; justify-content: space-between;
}
.banner::before { content:''; position:absolute; top:-60px; right:-50px; width:220px; height:220px; border-radius:50%; background:rgba(255,255,255,.07); pointer-events:none; }
.banner::after  { content:''; position:absolute; bottom:-50px; left:-40px; width:180px; height:180px; border-radius:50%; background:rgba(255,255,255,.05); pointer-events:none; }
.banner-hi  { font-size:20px; font-weight:900; color:white; letter-spacing:-.4px; margin-bottom:6px; position:relative;z-index:1; }
.banner-sub { font-size:12.5px; color:rgba(255,255,255,.75); font-weight:600; line-height:1.6; position:relative;z-index:1; }
.banner-chips { display:flex; gap:8px; margin-top:16px; flex-wrap:wrap; position:relative;z-index:1; }
.chip { display:flex; align-items:center; gap:5px; background:rgba(255,255,255,.15); backdrop-filter:blur(6px); border:1px solid rgba(255,255,255,.2); color:white; font-size:11.5px; font-weight:700; padding:5px 12px; border-radius:99px; }
.cdot { width:6px; height:6px; border-radius:50%; flex-shrink:0; }
.banner-stats { display:flex; gap:10px; margin-top:20px; position:relative;z-index:1; }
.bstat { background:rgba(255,255,255,.15); backdrop-filter:blur(8px); border:1px solid rgba(255,255,255,.18); border-radius:12px; padding:12px 16px; text-align:center; flex:1; }
.bstat-val { font-size:20px; font-weight:900; color:white; letter-spacing:-.4px; }
.bstat-lbl { font-size:10px; color:rgba(255,255,255,.7); font-weight:700; margin-top:3px; }

/* CARD GRAFIK KANAN */
.chart-card { background:white; border-radius:20px; border:1.5px solid var(--line); overflow:hidden; display:flex; flex-direction:column; }
.chart-head { padding:18px 20px 14px; border-bottom:1.5px solid var(--line); display:flex; justify-content:space-between; align-items:center; }
.chart-ttl  { font-size:13.5px; font-weight:800; color:var(--ink); }
.chart-sub  { font-size:11px; color:var(--ink4); font-weight:600; margin-top:2px; }
.chart-leg  { display:flex; gap:10px; }
.leg { display:flex; align-items:center; gap:5px; font-size:11px; color:var(--ink3); font-weight:600; }
.legsq { width:10px; height:10px; border-radius:3px; }
.chart-body { padding:18px 20px; flex:1; display:flex; flex-direction:column; }
.chart-foot { font-size:12px; color:var(--ink3); font-weight:600; margin-top:12px; }

/* ROW 2 */
.bottom-grid { display:grid; grid-template-columns:1fr 1fr; gap:20px; align-items:start; }

/* METRICS */
.mrow { display:flex; align-items:center; gap:12px; padding:13px 18px; border-bottom:1.5px solid var(--line); transition:background .12s; }
.mrow:last-child { border-bottom:none; }
.mrow:hover { background:#f7f9ff; }
.mico { width:34px; height:34px; border-radius:10px; display:flex; align-items:center; justify-content:center; font-size:15px; flex-shrink:0; }
.mlbl { font-size:11.5px; color:var(--ink3); font-weight:600; }
.mval { font-size:17px; font-weight:900; color:var(--ink); letter-spacing:-.3px; }
.mtag { margin-left:auto; font-size:10px; font-weight:800; padding:2px 7px; border-radius:6px; white-space:nowrap; }
.tup   { color:#16a34a; background:#dcfce7; }
.tdown { color:var(--rose); background:#fee2e2; }
.tneu  { color:var(--b500); background:#eff3ff; }

/* TOP LINKS */
.tlrow { display:flex; align-items:center; gap:10px; padding:12px 18px; border-bottom:1.5px solid var(--line); transition:background .12s; }
.tlrow:last-child { border-bottom:none; }
.tlrow:hover { background:#f7f9ff; }
.tlrank { font-size:11px; font-weight:900; font-family:var(--mono); width:18px; text-align:center; flex-shrink:0; color:#93c5fd; }
.tlrank.gold { color:var(--amber); }
.tlinfo { flex:1; min-width:0; }
.tlhandle { font-size:12.5px; font-weight:800; color:var(--ink); }
.tlurl { font-size:10.5px; color:var(--ink4); font-family:var(--mono); overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
.tlklik { font-size:12.5px; font-weight:900; color:var(--b500); font-family:var(--mono); }
</style>
@endpush

@section('content')
<div style="display:flex;flex-direction:column;gap:0;">

  {{-- ROW 1: Banner kiri | Grafik kanan --}}
  <div class="top-grid">

    {{-- BANNER BIRU --}}
    <div class="banner">
      <div>
        <div class="banner-hi">Halo, {{ auth()->user()->name }} 👋</div>
        <div class="banner-sub">Platform Payou.id berjalan normal.<br>Pantau aktivitas pengguna dan link di sini.</div>
        <div class="banner-chips">
          <div class="chip"><div class="cdot" style="background:#6ee7b7;"></div>Platform aktif</div>
          <div class="chip"><div class="cdot" style="background:#fde68a;"></div>{{ $stats['new_users_today'] }} user baru hari ini</div>
          @if($stats['suspended_users'] > 0)
          <div class="chip"><div class="cdot" style="background:#fda4af;"></div>{{ $stats['suspended_users'] }} tersuspend</div>
          @endif
        </div>
      </div>
      <div class="banner-stats">
        <div class="bstat"><div class="bstat-val">{{ number_format($stats['total_users']) }}</div><div class="bstat-lbl">Total User</div></div>
        <div class="bstat"><div class="bstat-val">{{ number_format($stats['total_links']) }}</div><div class="bstat-lbl">Total Link</div></div>
        <div class="bstat"><div class="bstat-val">{{ number_format($stats['total_clicks_today']) }}</div><div class="bstat-lbl">Klik Hari Ini</div></div>
      </div>
    </div>

    {{-- GRAFIK --}}
    <div class="chart-card">
      <div class="chart-head">
        <div>
          <div class="chart-ttl">Klik 7 Hari Terakhir</div>
          <div class="chart-sub">{{ now()->subDays(6)->format('d M') }} — {{ now()->format('d M Y') }}</div>
        </div>
        <div class="chart-leg">
          <div class="leg"><div class="legsq" style="background:#2356e8;"></div>Hari ini</div>
          <div class="leg"><div class="legsq" style="background:#dbeafe;"></div>Hari lain</div>
        </div>
      </div>
      <div class="chart-body">
        <div style="flex:1;min-height:180px;">
          <canvas id="clickChart"></canvas>
        </div>
        <div class="chart-foot">
          Total minggu ini: <b style="color:var(--ink);">{{ number_format($clicksChart->sum('total')) }} klik</b>
        </div>
      </div>
    </div>

  </div>

  {{-- ROW 2: Tabel pengguna kiri | Ringkasan kanan --}}
  <div class="bottom-grid">

    {{-- KIRI: PENGGUNA TERBARU --}}
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
                <div class="uav" style="background:linear-gradient(135deg,#3b82f6,#6366f1);">{{ strtoupper(substr($user->name,0,2)) }}</div>
                <div><div class="uname">{{ $user->name }}</div><div class="uslug">{{ $user->email }}</div></div>
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

    {{-- KANAN: RINGKASAN --}}
    <div class="card">
      <div class="card-head"><div><div class="card-title">Ringkasan Platform</div><div class="card-sub">Update real-time</div></div></div>
      <div class="mrow">
        <div class="mico" style="background:#eff3ff;">🧑‍💻</div>
        <div><div class="mlbl">User baru hari ini</div><div class="mval">{{ $stats['new_users_today'] }}</div></div>
        <span class="mtag tup">+{{ $stats['new_users_today'] }}</span>
      </div>
      <div class="mrow">
        <div class="mico" style="background:#dcfce7;">✅</div>
        <div><div class="mlbl">Total user aktif</div><div class="mval">{{ number_format($stats['active_users']) }}</div></div>
        <span class="mtag tneu">{{ $stats['total_users'] > 0 ? round($stats['active_users'] / $stats['total_users'] * 100) : 0 }}%</span>
      </div>
      <div class="mrow">
        <div class="mico" style="background:#fee2e2;">🔒</div>
        <div><div class="mlbl">Akun tersuspend</div><div class="mval">{{ $stats['suspended_users'] }}</div></div>
        @if($stats['suspended_users'] > 0)<span class="mtag tdown">Perlu tinjauan</span>@endif
      </div>
      <div class="mrow">
        <div class="mico" style="background:#fef9c3;">🔗</div>
        <div><div class="mlbl">Total link terdaftar</div><div class="mval">{{ number_format($stats['total_links']) }}</div></div>
      </div>
    </div>

  </div>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
const labels = @json($clicksChart->pluck('date')->map(fn($d) => \Carbon\Carbon::parse($d)->format('d/m')));
const values = @json($clicksChart->pluck('total'));
const bgColors = values.map((_, i) => i === values.length - 1 ? '#2356e8' : '#dbeafe');
const bdColors = values.map((_, i) => i === values.length - 1 ? '#1a3fa8' : '#bfdbfe');

new Chart(document.getElementById('clickChart').getContext('2d'), {
  type: 'bar',
  data: {
    labels,
    datasets: [{
      data: values,
      backgroundColor: bgColors,
      borderColor: bdColors,
      borderWidth: 1.5,
      borderRadius: 8,
      barPercentage: 0.55,
      categoryPercentage: 0.65,
    }]
  },
  options: {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
      legend: { display: false },
      tooltip: {
        backgroundColor: '#0c1533',
        titleColor: '#fff',
        bodyColor: '#c2cfe8',
        cornerRadius: 8,
        padding: 10,
        callbacks: { label: ctx => `  ${ctx.parsed.y} klik` }
      }
    },
    scales: {
      y: { beginAtZero: true, grid: { color: '#f0f4ff' }, ticks: { color: '#7a8db5', font: { size: 11 }, precision: 0 } },
      x: { grid: { display: false }, ticks: { color: '#7a8db5', font: { size: 11 } } }
    }
  }
});
</script>
@endpush