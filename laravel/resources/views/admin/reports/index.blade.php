{{-- ═══════════════════════════════════════════════════════════════════
     admin/reports/index.blade.php — REDESIGN PROFESIONAL
     ═══════════════════════════════════════════════════════════════════ --}}
@extends('admin.layouts.app')
@section('page-title', 'Laporan Akun')

@php
$reasonLabels = [
    'spam'          => 'Spam',
    'scam'          => 'Penipuan / Scam',
    'hate_speech'   => 'Ujaran Kebencian',
    'adult_content' => 'Konten Dewasa',
    'violence'      => 'Kekerasan',
    'fake_account'  => 'Akun Palsu',
    'copyright'     => 'Hak Cipta',
    'other'         => 'Lainnya',
];
$reasonColors = [
    'spam'          => '#d97706',
    'scam'          => '#dc2626',
    'hate_speech'   => '#7c3aed',
    'adult_content' => '#db2777',
    'violence'      => '#b91c1c',
    'fake_account'  => '#4338ca',
    'copyright'     => '#0284c7',
    'other'         => '#6b7280',
];
$riskWeights = [
    'violence'      => 5,
    'scam'          => 4,
    'hate_speech'   => 4,
    'fake_account'  => 3,
    'adult_content' => 3,
    'copyright'     => 2,
    'spam'          => 1,
    'other'         => 1,
];
@endphp

<style>
  :root {
    --font-display: 'DM Sans', 'Segoe UI', sans-serif;
    --font-mono: 'JetBrains Mono', 'Fira Mono', monospace;
    --c-bg: #f8f9fb;
    --c-surface: #ffffff;
    --c-border: #e4e7ec;
    --c-border-strong: #cdd2da;
    --c-ink: #0f1923;
    --c-ink2: #374151;
    --c-ink3: #6b7280;
    --c-ink4: #9ca3af;
    --c-accent: #1d4ed8;
    --c-accent-soft: #eff6ff;
    --c-danger: #dc2626;
    --c-danger-soft: #fef2f2;
    --c-warning: #d97706;
    --c-warning-soft: #fffbeb;
    --c-success: #16a34a;
    --c-success-soft: #f0fdf4;
    --radius: 10px;
    --shadow-sm: 0 1px 3px rgba(0,0,0,.07), 0 1px 2px rgba(0,0,0,.04);
    --shadow: 0 4px 12px rgba(0,0,0,.08), 0 1px 3px rgba(0,0,0,.05);
  }

  .rp-wrap { display:flex; flex-direction:column; gap:22px; padding:24px; font-family:var(--font-display); }

  /* ── HEADER ── */
  .rp-header { display:flex; align-items:flex-start; justify-content:space-between; flex-wrap:wrap; gap:14px; }
  .rp-header-left h1 { font-size:22px; font-weight:800; color:var(--c-ink); letter-spacing:-.4px; margin:0 0 4px; }
  .rp-header-left p  { font-size:12.5px; color:var(--c-ink3); margin:0; line-height:1.6; }
  .rp-header-actions { display:flex; gap:8px; }

  /* ── NOTICE PANEL ── */
  .rp-notice {
    background: #fefce8;
    border: 1.5px solid #fde047;
    border-left: 4px solid #eab308;
    border-radius: var(--radius);
    padding: 14px 18px;
  }
  .rp-notice-title { font-size:13px; font-weight:800; color:#854d0e; margin:0 0 6px; }
  .rp-notice-body  { font-size:12px; color:#713f12; line-height:1.7; margin:0; }
  .rp-notice-body li { margin-bottom:3px; }

  /* ── STAT CARDS ── */
  .rp-stats { display:grid; grid-template-columns:repeat(auto-fit,minmax(148px,1fr)); gap:12px; }
  .rp-stat {
    background:var(--c-surface);
    border:1.5px solid var(--c-border);
    border-radius:var(--radius);
    padding:16px 18px;
    box-shadow:var(--shadow-sm);
    transition:box-shadow .2s;
  }
  .rp-stat:hover { box-shadow:var(--shadow); }
  .rp-stat-label { font-size:11px; font-weight:700; color:var(--c-ink3); text-transform:uppercase; letter-spacing:.5px; margin-bottom:8px; }
  .rp-stat-value { font-size:26px; font-weight:900; letter-spacing:-.6px; color:var(--c-ink); }
  .rp-stat-value.danger  { color:var(--c-danger); }
  .rp-stat-value.success { color:var(--c-success); }
  .rp-stat-value.warning { color:var(--c-warning); }
  .rp-stat-value.accent  { color:var(--c-accent); }

  /* ── ANALYTICS ROW ── */
  .rp-analytics { display:grid; grid-template-columns:1fr 1fr; gap:16px; }
  @media(max-width:700px){ .rp-analytics { grid-template-columns:1fr; } }
  .rp-card {
    background:var(--c-surface);
    border:1.5px solid var(--c-border);
    border-radius:var(--radius);
    box-shadow:var(--shadow-sm);
    overflow:hidden;
  }
  .rp-card-head {
    padding:13px 18px;
    border-bottom:1.5px solid var(--c-border);
    font-size:12px; font-weight:800; color:var(--c-ink); text-transform:uppercase; letter-spacing:.5px;
  }
  .rp-card-body { padding:16px 18px; }

  /* Chart bars */
  .rp-bars { display:flex; align-items:flex-end; gap:6px; height:72px; }
  .rp-bar-col { flex:1; display:flex; flex-direction:column; align-items:center; gap:4px; }
  .rp-bar-count { font-size:9px; color:var(--c-ink3); font-weight:700; font-family:var(--font-mono); }
  .rp-bar-fill  { width:100%; border-radius:3px 3px 0 0; background:var(--c-border); min-height:4px; transition:height .4s; }
  .rp-bar-fill.active { background:var(--c-accent); }
  .rp-bar-day   { font-size:8.5px; color:var(--c-ink4); font-weight:600; }

  /* Category bars */
  .rp-catbar { display:flex; flex-direction:column; gap:7px; }
  .rp-catbar-row { display:flex; align-items:center; gap:8px; }
  .rp-catbar-label { font-size:11px; color:var(--c-ink2); font-weight:600; min-width:120px; }
  .rp-catbar-track { flex:1; height:7px; background:#f3f4f6; border-radius:4px; overflow:hidden; }
  .rp-catbar-fill  { height:100%; border-radius:4px; transition:width .5s; }
  .rp-catbar-cnt   { font-size:11px; color:var(--c-ink3); font-weight:700; min-width:26px; text-align:right; font-family:var(--font-mono); }

  /* ── HIGH RISK ── */
  .rp-highrisk {
    background:var(--c-surface);
    border:1.5px solid #fecaca;
    border-radius:var(--radius);
    box-shadow:var(--shadow-sm);
    overflow:hidden;
  }
  .rp-highrisk-head {
    padding:14px 18px;
    border-bottom:1.5px solid #fecaca;
    background:#fff5f5;
  }
  .rp-highrisk-head-title { font-size:13px; font-weight:800; color:#be123c; margin:0 0 3px; }
  .rp-highrisk-head-sub   { font-size:11.5px; color:#9f1239; margin:0; }

  /* ── FILTER ── */
  .rp-filter { background:var(--c-surface); border:1.5px solid var(--c-border); border-radius:var(--radius); padding:14px 18px; box-shadow:var(--shadow-sm); }
  .rp-filter form { display:flex; gap:8px; align-items:center; flex-wrap:wrap; }
  .rp-input, .rp-select {
    padding:7px 11px;
    border-radius:8px;
    border:1.5px solid var(--c-border);
    background:var(--c-bg);
    font-family:var(--font-display);
    font-size:12.5px;
    color:var(--c-ink2);
    outline:none;
    transition:border-color .2s;
  }
  .rp-input:focus, .rp-select:focus { border-color:var(--c-accent); }
  .rp-input { min-width:180px; }

  /* ── BUTTONS ── */
  .btn-primary {
    padding:7px 14px; background:var(--c-accent); color:#fff;
    border:none; border-radius:8px; font-size:12px; font-weight:700;
    cursor:pointer; font-family:var(--font-display); transition:background .2s;
    text-decoration:none; display:inline-block;
  }
  .btn-primary:hover { background:#1e40af; }
  .btn-ghost {
    padding:7px 14px; background:transparent; color:var(--c-ink2);
    border:1.5px solid var(--c-border); border-radius:8px; font-size:12px; font-weight:700;
    cursor:pointer; font-family:var(--font-display); transition:all .2s;
    text-decoration:none; display:inline-block;
  }
  .btn-ghost:hover { background:var(--c-bg); border-color:var(--c-border-strong); }
  .btn-sm { padding:5px 9px; font-size:11px; }
  .btn-danger {
    padding:5px 9px; background:var(--c-danger-soft); color:var(--c-danger);
    border:1.5px solid #fca5a5; border-radius:8px; font-size:11px; font-weight:700;
    cursor:pointer; font-family:var(--font-display); transition:all .2s;
  }
  .btn-danger:hover { background:#fee2e2; }
  .btn-success {
    padding:5px 9px; background:var(--c-success-soft); color:var(--c-success);
    border:1.5px solid #86efac; border-radius:8px; font-size:11px; font-weight:700;
    cursor:pointer; font-family:var(--font-display); transition:all .2s;
  }
  .btn-success:hover { background:#dcfce7; }
  .btn-note {
    padding:5px 9px; background:#f8fafc; color:var(--c-ink2);
    border:1.5px solid var(--c-border); border-radius:8px; font-size:11px; font-weight:700;
    cursor:pointer; font-family:var(--font-display); transition:all .2s;
  }
  .btn-note:hover { background:#f1f5f9; }
  .btn-detail {
    padding:5px 9px; background:var(--c-accent-soft); color:var(--c-accent);
    border:1.5px solid #bfdbfe; border-radius:8px; font-size:11px; font-weight:700;
    cursor:pointer; font-family:var(--font-display); transition:all .2s;
    text-decoration:none; display:inline-block;
  }
  .btn-detail:hover { background:#dbeafe; }

  /* ── TABLE ── */
  .rp-table-wrap { background:var(--c-surface); border:1.5px solid var(--c-border); border-radius:var(--radius); box-shadow:var(--shadow-sm); overflow:hidden; }
  .rp-table-scroll { overflow-x:auto; }
  table.rp-table { width:100%; border-collapse:collapse; }
  table.rp-table thead th {
    background:#f8f9fb; border-bottom:1.5px solid var(--c-border);
    padding:11px 14px; font-size:11px; font-weight:800; color:var(--c-ink3);
    text-transform:uppercase; letter-spacing:.5px; white-space:nowrap; text-align:left;
  }
  table.rp-table thead th.num { text-align:center; }
  table.rp-table tbody tr { border-bottom:1px solid var(--c-border); transition:background .15s; }
  table.rp-table tbody tr:last-child { border-bottom:none; }
  table.rp-table tbody tr:hover { background:#fafbfc; }
  table.rp-table tbody td { padding:12px 14px; font-size:12.5px; color:var(--c-ink2); vertical-align:top; }
  table.rp-table tbody td.num { text-align:center; }
  table.rp-table tbody tr.highrisk-row { background:#fff8f8; }

  /* ── BADGES ── */
  .badge {
    display:inline-block; padding:3px 9px; border-radius:20px;
    font-size:11px; font-weight:700; letter-spacing:.2px;
  }
  .badge-pending  { background:#fef9c3; color:#92400e; border:1px solid #fde68a; }
  .badge-reviewed { background:#dcfce7; color:#166534; border:1px solid #86efac; }
  .badge-rejected { background:#fee2e2; color:#991b1b; border:1px solid #fca5a5; }

  .risk-badge {
    display:inline-block; padding:3px 8px; border-radius:6px;
    font-size:10.5px; font-weight:800; letter-spacing:.3px;
  }

  .reason-tag {
    display:inline-block; padding:3px 9px; border-radius:6px;
    font-size:11px; font-weight:700;
  }

  /* ── PAGINATION ── */
  .rp-pagination {
    padding:13px 18px; border-top:1.5px solid var(--c-border);
    display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:8px;
  }
  .rp-pagination-info { font-size:12px; color:var(--c-ink3); font-weight:600; }
  .rp-pagination-nav  { display:flex; align-items:center; gap:6px; }
  .rp-page-cur { padding:5px 12px; font-size:12px; color:var(--c-ink3); font-weight:700; font-family:var(--font-mono); }

  /* ── MODAL ── */
  .rp-modal-overlay {
    display:none; position:fixed; inset:0;
    background:rgba(15,25,35,0.5); z-index:9999;
    align-items:center; justify-content:center; padding:16px;
    backdrop-filter:blur(2px);
  }
  .rp-modal {
    background:#fff; border-radius:14px; width:100%; max-width:440px;
    box-shadow:0 24px 60px rgba(0,0,0,.18); overflow:hidden;
  }
  .rp-modal-head {
    padding:15px 18px; border-bottom:1.5px solid var(--c-border);
    display:flex; justify-content:space-between; align-items:center;
  }
  .rp-modal-head-title { font-size:14px; font-weight:800; color:var(--c-ink); }
  .rp-modal-close {
    background:#f1f5f9; border:none; border-radius:7px;
    width:28px; height:28px; cursor:pointer; font-size:14px;
    display:flex; align-items:center; justify-content:center; color:var(--c-ink3);
    transition:background .2s;
  }
  .rp-modal-close:hover { background:#e2e8f0; }
  .rp-modal-body { padding:18px; }
  .rp-modal-footer { display:flex; gap:8px; margin-top:14px; }
  .rp-textarea {
    width:100%; box-sizing:border-box;
    border:1.5px solid var(--c-border); border-radius:9px;
    padding:10px 13px; font-size:13px; font-family:var(--font-display);
    resize:vertical; outline:none; color:var(--c-ink2);
    transition:border-color .2s;
  }
  .rp-textarea:focus { border-color:var(--c-accent); }
</style>

@section('content')
<div class="rp-wrap">

  {{-- ─── PAGE HEADER ─── --}}
  <div class="rp-header">
    <div class="rp-header-left">
      <h1>Manajemen Laporan Akun</h1>
      <p>Tinjau dan moderasi laporan dari pengunjung terhadap akun seller. Setiap tindakan suspend wajib melalui proses tinjauan manual yang cermat.</p>
    </div>
    <div class="rp-header-actions">
      <a href="{{ route('admin.reports.index') }}?export=csv" class="btn-ghost">Unduh CSV</a>
    </div>
  </div>

  {{-- ─── PERINGATAN ADMIN ─── --}}
  <div class="rp-notice">
    <p class="rp-notice-title">Peringatan Moderasi — Baca Sebelum Mengambil Tindakan</p>
    <ul class="rp-notice-body">
      <li><strong>Jangan sembarangan menangguhkan akun.</strong> Setiap tindakan suspend berdampak langsung pada mata pencaharian seller dan kepercayaan platform.</li>
      <li>Verifikasi bukti secara menyeluruh sebelum mengubah status. Laporan palsu atau balas dendam antar pengguna adalah hal yang nyata terjadi.</li>
      <li>Gunakan fitur <em>Catatan Moderator</em> untuk mendokumentasikan alasan setiap keputusan. Akuntabilitas adalah kewajiban, bukan pilihan.</li>
      <li>Untuk kasus ambigu, eskalasikan ke supervisor — jangan bertindak sendiri. Tindakan yang terburu-buru dapat menyebabkan kerugian yang tidak dapat dipulihkan.</li>
    </ul>
  </div>

  {{-- ─── STAT CARDS ─── --}}
  <div class="rp-stats">
    <div class="rp-stat">
      <div class="rp-stat-label">Total Laporan</div>
      <div class="rp-stat-value">{{ $stats['total'] }}</div>
    </div>
    <div class="rp-stat">
      <div class="rp-stat-label">Perlu Ditinjau</div>
      <div class="rp-stat-value warning">{{ $stats['pending'] }}</div>
    </div>
    <div class="rp-stat">
      <div class="rp-stat-label">Sudah Ditinjau</div>
      <div class="rp-stat-value success">{{ $stats['reviewed'] }}</div>
    </div>
    <div class="rp-stat">
      <div class="rp-stat-label">Ditolak</div>
      <div class="rp-stat-value danger">{{ $stats['rejected'] }}</div>
    </div>
    <div class="rp-stat">
      <div class="rp-stat-label">Akun Unik Dilaporkan</div>
      <div class="rp-stat-value accent">{{ $stats['unique_reported_users'] ?? '–' }}</div>
    </div>
    <div class="rp-stat">
      <div class="rp-stat-label">Laporan Hari Ini</div>
      <div class="rp-stat-value">{{ $stats['today'] ?? 0 }}</div>
    </div>
  </div>

  {{-- ─── ANALITIK ROW ─── --}}
  <div class="rp-analytics">

    {{-- Trend 7 hari --}}
    <div class="rp-card">
      <div class="rp-card-head">Tren Laporan — 7 Hari Terakhir</div>
      <div class="rp-card-body">
        <div class="rp-bars">
          @foreach($stats['daily_trend'] ?? [] as $day)
          @php $pct = $stats['daily_trend_max'] > 0 ? ($day['count'] / $stats['daily_trend_max'] * 100) : 0; @endphp
          <div class="rp-bar-col">
            <div class="rp-bar-count">{{ $day['count'] }}</div>
            <div class="rp-bar-fill {{ $day['count'] > 0 ? 'active' : '' }}" style="height:{{ max(4, $pct * 0.6) }}px;"></div>
            <div class="rp-bar-day">{{ $day['label'] }}</div>
          </div>
          @endforeach
        </div>
      </div>
    </div>

    {{-- Distribusi kategori --}}
    <div class="rp-card">
      <div class="rp-card-head">Distribusi Kategori Laporan</div>
      <div class="rp-card-body">
        <div class="rp-catbar">
          @foreach($stats['by_reason'] ?? [] as $r => $cnt)
          @php $pct = $stats['total'] > 0 ? round($cnt / $stats['total'] * 100) : 0; @endphp
          <div class="rp-catbar-row">
            <div class="rp-catbar-label">{{ $reasonLabels[$r] ?? $r }}</div>
            <div class="rp-catbar-track">
              <div class="rp-catbar-fill" style="width:{{ $pct }}%;background:{{ $reasonColors[$r] ?? '#9ca3af' }};"></div>
            </div>
            <div class="rp-catbar-cnt">{{ $cnt }}</div>
          </div>
          @endforeach
        </div>
      </div>
    </div>
  </div>

  {{-- ─── HIGH RISK ACCOUNTS ─── --}}
  @if(!empty($highRiskAccounts))
  <div class="rp-highrisk">
    <div class="rp-highrisk-head">
      <p class="rp-highrisk-head-title">Akun Risiko Tinggi</p>
      <p class="rp-highrisk-head-sub">Akun dengan jumlah laporan terbanyak atau berbobot tinggi — memerlukan prioritas tinjauan manual segera.</p>
    </div>
    <div style="overflow-x:auto;">
      <table class="rp-table">
        <thead>
          <tr>
            <th>Akun</th>
            <th class="num">Jml Laporan</th>
            <th class="num">Risk Score</th>
            <th>Kategori Dominan</th>
            <th>Status Terakhir</th>
            <th>Tindakan</th>
          </tr>
        </thead>
        <tbody>
          @foreach($highRiskAccounts as $acc)
          @php
            $score = collect($acc->report_reasons ?? [])->reduce(fn($c, $r) => $c + ($riskWeights[$r] ?? 1), 0);
            $dominantReason = collect($acc->report_reasons ?? [])->groupBy(fn($x)=>$x)->sortByDesc(fn($g)=>$g->count())->keys()->first();
            $scoreColor = $score >= 15 ? '#b91c1c' : ($score >= 8 ? '#b45309' : '#15803d');
            $scoreBg    = $score >= 15 ? '#fee2e2' : ($score >= 8 ? '#fef3c7' : '#dcfce7');
            $scoreLbl   = $score >= 15 ? 'Kritis' : ($score >= 8 ? 'Tinggi' : 'Sedang');
          @endphp
          <tr class="highrisk-row">
            <td>
              <div style="font-weight:800;color:var(--c-ink);">{{ $acc->name }}</div>
              <div style="font-size:10.5px;color:var(--c-ink4);font-family:var(--font-mono);">{{ '@'.$acc->username }}</div>
            </td>
            <td class="num" style="font-weight:900;color:var(--c-danger);font-size:16px;">{{ $acc->reports_count }}</td>
            <td class="num">
              <span class="risk-badge" style="background:{{ $scoreBg }};color:{{ $scoreColor }};">{{ $scoreLbl }} &middot; {{ $score }}</span>
            </td>
            <td>
              <span class="reason-tag" style="background:{{ ($reasonColors[$dominantReason] ?? '#9ca3af') }}18;color:{{ $reasonColors[$dominantReason] ?? '#9ca3af' }};">
                {{ $reasonLabels[$dominantReason] ?? $dominantReason }}
              </span>
            </td>
            <td>
              @if($acc->latest_report_status === 'pending')
                <span class="badge badge-pending">Pending</span>
              @elseif($acc->latest_report_status === 'reviewed')
                <span class="badge badge-reviewed">Reviewed</span>
              @else
                <span class="badge badge-rejected">Rejected</span>
              @endif
            </td>
            <td>
              <div style="display:flex;gap:5px;flex-wrap:wrap;">
                {{-- Detail report terakhir milik akun ini (ambil report pertama) --}}
                @if($acc->latestReport)
                  <a href="{{ route('admin.reports.show', $acc->latestReport) }}" class="btn-detail btn-sm">Detail</a>
                @endif
                <a href="{{ route('admin.reports.index') }}?reported_user={{ $acc->id }}" class="btn-ghost btn-sm">Semua Laporan</a>
              </div>
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
  @endif

  {{-- ─── FILTER ─── --}}
  <div class="rp-filter">
    <form method="GET" action="{{ route('admin.reports.index') }}">
      <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama atau username..." class="rp-input">
      <select name="status" class="rp-select">
        <option value="">Semua Status</option>
        <option value="pending"  {{ request('status')==='pending'  ?'selected':'' }}>Pending</option>
        <option value="reviewed" {{ request('status')==='reviewed' ?'selected':'' }}>Reviewed</option>
        <option value="rejected" {{ request('status')==='rejected' ?'selected':'' }}>Rejected</option>
      </select>
      <select name="reason" class="rp-select">
        <option value="">Semua Kategori</option>
        @foreach($reasonLabels as $key => $label)
          <option value="{{ $key }}" {{ request('reason')===$key ? 'selected' : '' }}>{{ $label }}</option>
        @endforeach
      </select>
      <select name="risk" class="rp-select">
        <option value="">Semua Risiko</option>
        <option value="high"   {{ request('risk')==='high'   ?'selected':'' }}>Risiko Tinggi</option>
        <option value="medium" {{ request('risk')==='medium' ?'selected':'' }}>Risiko Sedang</option>
        <option value="low"    {{ request('risk')==='low'    ?'selected':'' }}>Risiko Rendah</option>
      </select>
      <select name="sort" class="rp-select">
        <option value="latest"      {{ request('sort')==='latest'      ?'selected':'' }}>Terbaru</option>
        <option value="oldest"      {{ request('sort')==='oldest'      ?'selected':'' }}>Terlama</option>
        <option value="most_report" {{ request('sort')==='most_report' ?'selected':'' }}>Paling Banyak Dilaporkan</option>
      </select>
      <button type="submit" class="btn-primary">Terapkan Filter</button>
      @if(request()->hasAny(['status','reason','q','risk','sort']))
        <a href="{{ route('admin.reports.index') }}" class="btn-ghost">Reset</a>
      @endif
    </form>
  </div>

  {{-- ─── TABEL LAPORAN UTAMA ─── --}}
  <div class="rp-table-wrap">
    <div class="rp-table-scroll">
      <table class="rp-table">
        <thead>
          <tr>
            <th style="min-width:130px;">Waktu</th>
            <th style="min-width:160px;">Akun Dilaporkan</th>
            <th>Kategori</th>
            <th style="min-width:220px;">Deskripsi &amp; Bukti</th>
            <th class="num">Risk Score</th>
            <th style="min-width:140px;">Sumber Laporan</th>
            <th style="min-width:110px;">Reviewer</th>
            <th>Status</th>
            <th style="min-width:220px;">Tindakan</th>
          </tr>
        </thead>
        <tbody>
          @forelse($reports as $report)
          @php
            $weight    = $riskWeights[$report->reason] ?? 1;
            $totalRep  = $report->reportedUser?->reports_count ?? 1;
            $riskScore = $weight * min($totalRep, 10);
            if ($riskScore >= 30)       { $rLabel = 'Kritis'; $rColor = '#b91c1c'; $rBg = '#fee2e2'; }
            elseif ($riskScore >= 15)   { $rLabel = 'Tinggi'; $rColor = '#b45309'; $rBg = '#fef3c7'; }
            elseif ($riskScore >= 5)    { $rLabel = 'Sedang'; $rColor = '#1d4ed8'; $rBg = '#eff6ff'; }
            else                        { $rLabel = 'Rendah'; $rColor = '#15803d'; $rBg = '#f0fdf4'; }
          @endphp
          <tr>
            <td>
              <div style="font-weight:700;color:var(--c-ink);font-family:var(--font-mono);font-size:12px;">{{ $report->created_at->format('d M Y') }}</div>
              <div style="font-size:10.5px;color:var(--c-ink4);margin-top:2px;">{{ $report->created_at->format('H:i') }} &middot; {{ $report->created_at->diffForHumans() }}</div>
            </td>

            <td>
              @if($report->reportedUser)
                <div style="font-weight:800;color:var(--c-ink);">{{ $report->reportedUser->name }}</div>
                <div style="font-size:10.5px;color:var(--c-ink4);font-family:var(--font-mono);">{{ '@'.$report->reportedUser->username }}</div>
                <div style="margin-top:4px;display:flex;gap:8px;flex-wrap:wrap;">
                  <a href="{{ url('/'.$report->reportedUser->username) }}" target="_blank"
                     style="font-size:10px;color:var(--c-accent);text-decoration:none;font-weight:700;">Lihat Profil</a>
                  <a href="{{ route('admin.reports.index') }}?reported_user={{ $report->reportedUser->id }}"
                     style="font-size:10px;color:var(--c-ink3);text-decoration:none;">
                    {{ $report->reportedUser->reports_count ?? 0 }}x dilaporkan
                  </a>
                </div>
              @else
                <span style="color:var(--c-ink4);font-size:12px;font-style:italic;">Akun dihapus</span>
              @endif
            </td>

            <td>
              <span class="reason-tag" style="background:{{ ($reasonColors[$report->reason] ?? '#9ca3af') }}18;color:{{ $reasonColors[$report->reason] ?? '#9ca3af' }};">
                {{ $reasonLabels[$report->reason] ?? ucfirst($report->reason) }}
              </span>
            </td>

            <td style="max-width:240px;">
              <div style="font-size:12px;color:var(--c-ink2);line-height:1.55;">
                @if($report->detail)
                  {{ Str::limit($report->detail, 140) }}
                @else
                  <span style="color:var(--c-ink4);font-style:italic;">Tanpa deskripsi</span>
                @endif
              </div>
              @if($report->evidence_count > 0)
              <div style="margin-top:5px;display:flex;align-items:center;gap:6px;">
                <span style="font-size:10px;background:#eff6ff;color:#1d4ed8;padding:2px 7px;border-radius:5px;font-weight:700;border:1px solid #bfdbfe;">
                  {{ $report->evidence_count }} file bukti
                </span>
                <button onclick="viewEvidence({{ $report->id }})"
                        style="font-size:10px;color:var(--c-accent);background:none;border:none;cursor:pointer;padding:0;font-weight:700;">Lihat</button>
              </div>
              @endif
            </td>

            <td class="num">
              <span class="risk-badge" style="background:{{ $rBg }};color:{{ $rColor }};">{{ $rLabel }}</span>
              <div style="font-size:9px;color:var(--c-ink4);margin-top:3px;font-family:var(--font-mono);">skor {{ $riskScore }}</div>
            </td>

            <td>
              <div style="font-size:11px;color:var(--c-ink3);font-family:var(--font-mono);">{{ $report->reporter_ip ?: '–' }}</div>
              @if($report->page_url)
              <div style="font-size:10px;color:var(--c-ink4);max-width:160px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;margin-top:2px;" title="{{ $report->page_url }}">
                {{ $report->page_url }}
              </div>
              @endif
            </td>

            <td>
              @if($report->reviewer)
                <div style="font-weight:700;color:var(--c-ink);font-size:12px;">{{ $report->reviewer->name }}</div>
                <div style="font-size:10.5px;color:var(--c-ink4);margin-top:2px;">{{ $report->reviewed_at?->format('d M H:i') }}</div>
              @else
                <span style="color:var(--c-ink4);">—</span>
              @endif
            </td>

            <td>
              @if($report->status === 'pending')
                <span class="badge badge-pending">Pending</span>
              @elseif($report->status === 'reviewed')
                <span class="badge badge-reviewed">Reviewed</span>
              @else
                <span class="badge badge-rejected">Rejected</span>
              @endif
            </td>

            <td>
              <div style="display:flex;gap:5px;flex-wrap:wrap;align-items:center;">
                {{-- Detail --}}
                <a href="{{ route('admin.reports.show', $report) }}" class="btn-detail btn-sm">Detail</a>

                {{-- Review --}}
                @if($report->status !== 'reviewed')
                <form method="POST" action="{{ route('admin.reports.updateStatus', $report) }}" style="margin:0;">
                  @csrf @method('PATCH')
                  <input type="hidden" name="status" value="reviewed">
                  <button type="submit" class="btn-success">Setujui</button>
                </form>
                @endif

                {{-- Reject --}}
                @if($report->status !== 'rejected')
                <form method="POST" action="{{ route('admin.reports.updateStatus', $report) }}" style="margin:0;">
                  @csrf @method('PATCH')
                  <input type="hidden" name="status" value="rejected">
                  <button type="submit" class="btn-danger">Tolak</button>
                </form>
                @endif

                {{-- Catatan --}}
                <button onclick="openNote({{ $report->id }}, '{{ addslashes($report->moderator_note ?? '') }}')" class="btn-note">Catatan</button>
              </div>
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="9" style="text-align:center;padding:40px 20px;color:var(--c-ink4);font-size:13px;">
              Tidak ada laporan yang sesuai dengan filter yang diterapkan.
            </td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    {{-- Pagination --}}
    @if($reports->hasPages())
    <div class="rp-pagination">
      <div class="rp-pagination-info">
        Menampilkan {{ $reports->firstItem() }}–{{ $reports->lastItem() }} dari {{ $reports->total() }} laporan
      </div>
      <div class="rp-pagination-nav">
        @if($reports->onFirstPage())
          <span class="btn-ghost btn-sm" style="opacity:.4;cursor:default;">Sebelumnya</span>
        @else
          <a href="{{ $reports->previousPageUrl() }}" class="btn-ghost btn-sm">Sebelumnya</a>
        @endif
        <span class="rp-page-cur">{{ $reports->currentPage() }} / {{ $reports->lastPage() }}</span>
        @if($reports->hasMorePages())
          <a href="{{ $reports->nextPageUrl() }}" class="btn-primary btn-sm">Selanjutnya</a>
        @else
          <span class="btn-primary btn-sm" style="opacity:.4;cursor:default;">Selanjutnya</span>
        @endif
      </div>
    </div>
    @endif
  </div>

  {{-- ─── MODAL CATATAN MODERATOR ─── --}}
  <div id="noteModal" class="rp-modal-overlay">
    <div class="rp-modal">
      <div class="rp-modal-head">
        <div class="rp-modal-head-title">Catatan Moderator</div>
        <button onclick="closeNote()" class="rp-modal-close">&#x2715;</button>
      </div>
      <div class="rp-modal-body">
        <p style="font-size:12px;color:var(--c-ink3);margin:0 0 10px;line-height:1.6;">
          Tuliskan alasan dan konteks keputusan moderasi secara jelas. Catatan ini bersifat internal dan merupakan bentuk akuntabilitas tim.
        </p>
        <textarea id="noteText" rows="5" class="rp-textarea"
                  placeholder="Contoh: Laporan dikonfirmasi setelah verifikasi bukti gambar dan riwayat transaksi. Ditemukan indikasi penipuan pada 3 transaksi terakhir..."></textarea>
        <div class="rp-modal-footer">
          <button onclick="closeNote()" class="btn-ghost" style="flex:1;padding:10px;font-size:13px;">Batal</button>
          <button onclick="saveNote()" class="btn-primary" style="flex:1;padding:10px;font-size:13px;border-radius:9px;">Simpan Catatan</button>
        </div>
      </div>
    </div>
  </div>

</div>

<script>
let activeNoteReportId = null;

function openNote(id, existing) {
  activeNoteReportId = id;
  document.getElementById('noteText').value = existing || '';
  document.getElementById('noteModal').style.display = 'flex';
}

function closeNote() {
  document.getElementById('noteModal').style.display = 'none';
  activeNoteReportId = null;
}

async function saveNote() {
  const note = document.getElementById('noteText').value.trim();
  if (!activeNoteReportId) return;
  try {
    const res = await fetch(`/admin/reports/${activeNoteReportId}/note`, {
      method: 'PATCH',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        'Accept': 'application/json',
      },
      body: JSON.stringify({ note }),
    });
    if (!res.ok) throw new Error();
    closeNote();
    const flash = document.createElement('div');
    flash.textContent = 'Catatan berhasil disimpan.';
    flash.style.cssText = 'position:fixed;bottom:24px;left:50%;transform:translateX(-50%);background:#0f1923;color:#fff;padding:9px 20px;border-radius:40px;font-size:13px;font-weight:600;z-index:99999;box-shadow:0 4px 14px rgba(0,0,0,.3);';
    document.body.appendChild(flash);
    setTimeout(() => flash.remove(), 2500);
  } catch {
    alert('Gagal menyimpan catatan. Silakan coba lagi.');
  }
}

function viewEvidence(id) {
  window.open(`/admin/reports/${id}/evidence`, '_blank');
}

// Close modal on overlay click
document.getElementById('noteModal').addEventListener('click', function(e) {
  if (e.target === this) closeNote();
});
</script>

@endsection