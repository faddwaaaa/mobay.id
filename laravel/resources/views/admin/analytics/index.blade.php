@extends('admin.layouts.app')
@section('page-title', 'Analytics')

@section('content')
<div style="display:flex;flex-direction:column;gap:18px;">

  <div style="font-size:18px;font-weight:900;color:var(--ink);letter-spacing:-.3px;">Analytics Platform</div>

  {{-- CHART KLIK HARIAN --}}
  <div class="card">
    <div class="card-head" style="flex-wrap:wrap;gap:10px;">
      <div>
        <div class="card-title">Klik Harian</div>
        <div class="card-sub">{{ $dateFrom->format('d M Y') }} — {{ $dateTo->format('d M Y') }}</div>
      </div>
      {{-- FILTER --}}
      <form method="GET" action="{{ route('admin.analytics.index') }}" style="display:flex;gap:8px;align-items:center;flex-wrap:wrap;" id="filterForm">
        <select name="days" id="daysSelect" style="padding:6px 10px;border-radius:9px;border:1.5px solid var(--line);font-size:12px;color:var(--ink2);background:var(--bg);outline:none;">
          <option value="7"  {{ request('days',30)==7  && !request('date_from') ?'selected':'' }}>7 Hari Terakhir</option>
          <option value="14" {{ request('days',30)==14 && !request('date_from') ?'selected':'' }}>14 Hari Terakhir</option>
          <option value="30" {{ (!request('days') || request('days')==30) && !request('date_from') ?'selected':'' }}>30 Hari Terakhir</option>
          <option value="custom" {{ request('date_from')?'selected':'' }}>Pilih Tanggal</option>
        </select>
        <div id="customRange" style="display:{{ request('date_from')?'flex':'none' }};gap:6px;align-items:center;">
          <input type="date" name="date_from" value="{{ request('date_from') }}" style="padding:6px 10px;border-radius:9px;border:1.5px solid var(--line);font-size:12px;color:var(--ink2);background:var(--bg);outline:none;">
          <span style="font-size:12px;color:var(--ink3);">–</span>
          <input type="date" name="date_to" value="{{ request('date_to') }}" style="padding:6px 10px;border-radius:9px;border:1.5px solid var(--line);font-size:12px;color:var(--ink2);background:var(--bg);outline:none;">
        </div>
        <button type="submit" class="btn btn-primary" style="padding:6px 14px;font-size:12px;">Terapkan</button>
      </form>
    </div>
    <div style="padding:20px;">
      <canvas id="clicksChart" height="80"></canvas>
      <div style="margin-top:12px;font-size:12px;color:var(--ink3);font-weight:600;">
        Total: <b style="color:var(--ink);">{{ number_format($clicksDaily->sum('total')) }} klik</b>
      </div>
    </div>
  </div>

  {{-- USER GROWTH --}}
  <div class="card">
    <div class="card-head">
      <div>
        <div class="card-title">Pertumbuhan User {{ now()->year }}</div>
        <div class="card-sub">Per bulan</div>
      </div>
    </div>
    <div style="padding:20px;">
      <canvas id="userChart" height="60"></canvas>
      <div style="margin-top:12px;font-size:12px;color:var(--ink3);font-weight:600;">
        Total tahun ini: <b style="color:var(--ink);">{{ number_format($userGrowth->sum('total')) }} user</b>
      </div>
    </div>
  </div>

</div>

{{-- Chart.js --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
<script>
// Data dari controller
const clickLabels = @json($clicksDaily->pluck('date'));
const clickData   = @json($clicksDaily->pluck('total'));
const userLabels  = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
@php
  $userMap = $userGrowth->pluck('total','month');
  $userArr = [];
  for($i=1;$i<=12;$i++) $userArr[] = $userMap[$i] ?? 0;
@endphp
const userData = @json($userArr);

// Clicks Chart
new Chart(document.getElementById('clicksChart'), {
  type: 'bar',
  data: {
    labels: clickLabels,
    datasets: [{
      label: 'Klik',
      data: clickData,
      backgroundColor: 'rgba(14,165,233,0.25)',
      borderColor: '#0ea5e9',
      borderWidth: 2,
      borderRadius: 6,
      hoverBackgroundColor: 'rgba(14,165,233,0.5)',
    }]
  },
  options: {
    responsive: true,
    plugins: {
      legend: { display: false },
      tooltip: {
        callbacks: {
          title: ctx => ctx[0].label,
          label: ctx => ` Klik: ${ctx.parsed.y.toLocaleString('id-ID')}`
        },
        backgroundColor: '#1e293b',
        titleColor: '#94a3b8',
        bodyColor: '#f1f5f9',
        padding: 10,
        cornerRadius: 8,
      }
    },
    scales: {
      x: { grid: { display: false }, ticks: { font: { size: 11 } } },
      y: { grid: { color: '#f1f5f9' }, ticks: { font: { size: 11 } }, beginAtZero: true }
    }
  }
});

// User Growth Chart
new Chart(document.getElementById('userChart'), {
  type: 'bar',
  data: {
    labels: userLabels,
    datasets: [{
      label: 'User Baru',
      data: userData,
      backgroundColor: ctx => ctx.dataIndex === {{ now()->month - 1 }}
        ? 'rgba(14,165,233,0.5)' : 'rgba(14,165,233,0.15)',
      borderColor: '#0ea5e9',
      borderWidth: 2,
      borderRadius: 6,
    }]
  },
  options: {
    responsive: true,
    plugins: {
      legend: { display: false },
      tooltip: {
        callbacks: {
          label: ctx => ` User baru: ${ctx.parsed.y}`
        },
        backgroundColor: '#1e293b',
        titleColor: '#94a3b8',
        bodyColor: '#f1f5f9',
        padding: 10,
        cornerRadius: 8,
      }
    },
    scales: {
      x: { grid: { display: false }, ticks: { font: { size: 11 } } },
      y: { grid: { color: '#f1f5f9' }, ticks: { font: { size: 11 } }, beginAtZero: true }
    }
  }
});

// Toggle custom date range
document.getElementById('daysSelect').addEventListener('change', function() {
  document.getElementById('customRange').style.display = this.value === 'custom' ? 'flex' : 'none';
});
</script>
@endsection