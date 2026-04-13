@extends('admin.layouts.app')
@section('page-title', 'Detail Pengguna — @' . $user->username)

@push('styles')
<style>
:root {
  --r: #dc2626; --r-bg: #fef2f2;
  --g: #16a34a; --g-bg: #f0fdf4;
  --b: #2563eb; --b-bg: #eff6ff;
  --v: #7c3aed; --v-bg: #f5f3ff;
  --a: #d97706; --a-bg: #fffbeb;
  --ink0: #0f172a; --ink2: #475569; --ink3: #94a3b8;
  --card-bg: #ffffff;
  --line-c: #e2e8f0;
  --hover-bg: #f8fafc;
  --T: .18s cubic-bezier(.4,0,.2,1);
}

.detail-wrap { display: flex; flex-direction: column; gap: 0; }

/* ── Back link ── */
.back-link {
  display: inline-flex; align-items: center; gap: 6px;
  font-size: 13px; font-weight: 700; color: var(--b);
  text-decoration: none; margin-bottom: 20px;
  transition: gap var(--T), opacity var(--T);
}
.back-link:hover { gap: 10px; opacity: .75; }

/* ── 3-column grid ── */
.detail-grid {
  display: grid;
  grid-template-columns: 260px 1fr 224px;
  gap: 16px;
  align-items: start;
}

/* ══ COL 1 · PROFILE CARD ══ */
.profile-card {
  background: var(--card-bg);
  border-radius: 18px;
  border: 1px solid var(--line-c);
  overflow: hidden;
  box-shadow: 0 1px 4px rgba(0,0,0,.05);
  transition: box-shadow var(--T);
}
.profile-card:hover { box-shadow: 0 6px 24px rgba(0,0,0,.07); }

/* Header */
.profile-header {
  background: linear-gradient(135deg, #1e1b4b 0%, #3730a3 50%, #4f46e5 100%);
  padding: 22px 18px 18px;
  display: flex; flex-direction: column; align-items: center; gap: 8px;
  position: relative;
}
.profile-header::after {
  content: '';
  position: absolute; inset: 0;
  background: radial-gradient(circle at 80% 15%, rgba(255,255,255,.09) 0%, transparent 60%);
  pointer-events: none;
}
.profile-av {
  width: 56px; height: 56px; border-radius: 14px;
  background: rgba(255,255,255,.15);
  border: 2px solid rgba(255,255,255,.28);
  display: flex; align-items: center; justify-content: center;
  font-size: 18px; font-weight: 900; color: white;
  letter-spacing: -.5px; position: relative; z-index: 1;
}
.profile-name {
  font-size: 14px; font-weight: 800; color: white;
  letter-spacing: -.2px; z-index: 1; text-align: center;
}
.profile-handle {
  font-size: 10.5px; color: rgba(255,255,255,.55);
  font-family: var(--mono, monospace); font-weight: 600;
  z-index: 1; text-align: center;
}
.header-badges { display: flex; gap: 6px; flex-wrap: wrap; justify-content: center; z-index: 1; }
.hbadge {
  display: inline-flex; align-items: center; gap: 4px;
  font-size: 10px; font-weight: 700; padding: 3px 9px;
  border-radius: 20px;
}
.hb-vip { background: rgba(199,210,254,.2); color: #c7d2fe; border: 1px solid rgba(199,210,254,.3); }
.hb-ok  { background: rgba(167,243,208,.18); color: #6ee7b7; border: 1px solid rgba(110,231,183,.3); }
.hb-off { background: rgba(254,202,202,.2);  color: #fca5a5; border: 1px solid rgba(252,165,165,.3); }
.hb-neu { background: rgba(255,255,255,.1);  color: rgba(255,255,255,.6); border: 1px solid rgba(255,255,255,.2); }

/* Body rows */
.profile-body { padding: 14px 16px; }
.profile-bio-box {
  background: var(--hover-bg);
  border-radius: 10px; padding: 10px 12px;
  margin-bottom: 12px;
  font-size: 12px; color: var(--ink2); line-height: 1.65;
  border: 1px solid var(--line-c);
}
.info-row {
  display: flex; justify-content: space-between; align-items: center;
  padding: 9px 0; border-bottom: 1px solid var(--line-c);
  transition: background var(--T);
}
.info-row:last-child { border-bottom: none; }
.info-lbl { font-size: 11px; color: var(--ink3); font-weight: 600; }
.info-val { font-size: 11.5px; font-weight: 700; color: var(--ink0); text-align: right; max-width: 60%; word-break: break-all; }

/* ══ COL 2 · STATS + LINKS ══ */
.mid-col { display: flex; flex-direction: column; gap: 14px; }

.stats-row { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }

.stat-big {
  background: var(--card-bg);
  border-radius: 16px; border: 1px solid var(--line-c);
  padding: 18px 20px;
  transition: box-shadow var(--T), transform var(--T), border-color var(--T);
  position: relative; overflow: hidden;
}
.stat-big:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 24px rgba(0,0,0,.07);
  border-color: #cbd5e1;
}
.stat-big-icon {
  width: 36px; height: 36px; border-radius: 9px;
  display: flex; align-items: center; justify-content: center;
  margin-bottom: 12px;
}
.sbi-violet { background: var(--v-bg); color: var(--v); }
.sbi-green  { background: var(--g-bg); color: var(--g); }

.sb-label { font-size: 10.5px; font-weight: 700; text-transform: uppercase; letter-spacing: .6px; color: var(--ink3); margin-bottom: 4px; }
.sb-val   { font-size: 30px; font-weight: 800; letter-spacing: -1.5px; line-height: 1; color: var(--ink0); margin-bottom: 3px; font-family: var(--mono, monospace); }
.sb-sub   { font-size: 11px; font-weight: 600; color: var(--ink3); }

/* Link list */
.links-card {
  background: var(--card-bg); border-radius: 16px;
  border: 1px solid var(--line-c); overflow: hidden;
  box-shadow: 0 1px 3px rgba(0,0,0,.04);
}
.card-head {
  padding: 13px 16px; background: var(--hover-bg);
  border-bottom: 1px solid var(--line-c);
  display: flex; justify-content: space-between; align-items: center;
}
.card-head-left { display: flex; align-items: center; gap: 7px; }
.card-title { font-size: 13px; font-weight: 800; color: var(--ink0); }
.card-sub   { font-size: 11px; color: var(--ink3); font-weight: 600; }

.link-row {
  display: flex; align-items: center; gap: 10px;
  padding: 11px 16px; border-bottom: 1px solid var(--line-c);
  transition: background var(--T);
}
.link-row:last-child { border-bottom: none; }
.link-row:hover { background: var(--hover-bg); }

.link-num {
  width: 22px; height: 22px; border-radius: 7px; flex-shrink: 0;
  background: var(--hover-bg); border: 1px solid var(--line-c);
  display: flex; align-items: center; justify-content: center;
  font-size: 10px; font-weight: 800; color: var(--ink2);
}
.link-ico {
  width: 32px; height: 32px; border-radius: 9px; flex-shrink: 0;
  background: var(--b-bg); border: 1px solid #bfdbfe;
  display: flex; align-items: center; justify-content: center;
  color: var(--b);
}
.link-info { flex: 1; min-width: 0; }
.link-title { font-size: 12.5px; font-weight: 700; color: var(--ink0); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.link-url   { font-size: 10.5px; color: var(--ink3); font-family: var(--mono, monospace); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }

.link-bar-col { width: 64px; flex-shrink: 0; }
.link-bar-bg  { height: 4px; background: var(--line-c); border-radius: 4px; overflow: hidden; margin-bottom: 3px; }
.link-bar-fill { height: 100%; border-radius: 4px; background: linear-gradient(90deg, var(--b), #60a5fa); transition: width .3s ease; }
.link-bar-pct  { font-size: 9.5px; font-weight: 700; color: var(--ink3); text-align: right; }

.link-chips { display: flex; flex-direction: column; gap: 3px; flex-shrink: 0; align-items: flex-end; }
.chip {
  font-size: 10.5px; font-weight: 700; font-family: var(--mono, monospace);
  padding: 2px 8px; border-radius: 6px; display: inline-flex; align-items: center; gap: 3px;
}
.chip-click { color: var(--v); background: var(--v-bg); }
.chip-view  { color: var(--g); background: var(--g-bg); }

.empty-link { padding: 36px 16px; text-align: center; font-size: 12.5px; font-weight: 600; color: var(--ink3); }

/* ══ COL 3 · QR + ACTIONS ══ */
.right-col { display: flex; flex-direction: column; gap: 12px; }

.qr-card {
  background: var(--card-bg); border-radius: 16px;
  border: 1px solid var(--line-c); padding: 14px;
  display: flex; flex-direction: column; align-items: center; gap: 10px;
  box-shadow: 0 1px 3px rgba(0,0,0,.04);
  transition: box-shadow var(--T);
}
.qr-card:hover { box-shadow: 0 6px 20px rgba(0,0,0,.07); }
.qr-card-head { display: flex; justify-content: space-between; align-items: center; width: 100%; }
.qr-title { font-size: 12px; font-weight: 800; color: var(--ink0); display: flex; align-items: center; gap: 6px; }
.qr-badge { font-size: 10px; font-weight: 700; padding: 2px 8px; border-radius: 6px; background: var(--g-bg); color: var(--g); border: 1px solid #bbf7d0; }
#qrcode { display: flex; justify-content: center; }
#qrcode canvas, #qrcode img { border-radius: 10px; }
.qr-url {
  font-size: 9.5px; color: var(--ink3); font-family: var(--mono, monospace);
  word-break: break-all; text-align: center; line-height: 1.55;
  background: var(--hover-bg); padding: 6px 10px;
  border-radius: 8px; width: 100%;
  border: 1px solid var(--line-c);
}
.qr-dl-btn {
  width: 100%; padding: 9px; border-radius: 10px; border: 1px solid #bfdbfe;
  font-size: 12px; font-weight: 700; cursor: pointer;
  background: var(--b-bg); color: var(--b);
  transition: background var(--T), border-color var(--T), color var(--T), box-shadow var(--T);
  display: flex; align-items: center; justify-content: center; gap: 6px;
}
.qr-dl-btn:hover {
  background: var(--b); color: white; border-color: var(--b);
  box-shadow: 0 4px 12px rgba(37,99,235,.25);
}

.action-card {
  background: var(--card-bg); border-radius: 16px;
  border: 1px solid var(--line-c); padding: 14px;
  box-shadow: 0 1px 3px rgba(0,0,0,.04);
}
.action-title {
  font-size: 10px; font-weight: 800; color: var(--ink3);
  text-transform: uppercase; letter-spacing: .7px; margin-bottom: 10px;
}
.action-btn {
  width: 100%; padding: 9px 12px; border-radius: 10px;
  font-size: 12px; font-weight: 700; cursor: pointer;
  border: 1px solid var(--line-c); background: var(--card-bg); color: var(--ink0);
  margin-bottom: 7px; transition: background var(--T), border-color var(--T), color var(--T), box-shadow var(--T), transform var(--T);
  display: flex; align-items: center; justify-content: center; gap: 7px;
  text-decoration: none;
}
.action-btn:last-child { margin-bottom: 0; }
.action-btn:hover {
  background: var(--hover-bg); border-color: #cbd5e1;
  transform: translateY(-1px);
  box-shadow: 0 3px 10px rgba(0,0,0,.06);
}
.action-btn:active { transform: translateY(0); }
.action-btn.primary {
  background: linear-gradient(135deg, #4f46e5, #7c3aed);
  color: white; border-color: transparent;
  box-shadow: 0 3px 12px rgba(79,70,229,.25);
}
.action-btn.primary:hover {
  box-shadow: 0 6px 20px rgba(79,70,229,.35);
  transform: translateY(-1px);
}
</style>
@endpush

@section('content')
<div class="detail-wrap">

  <a href="{{ route('admin.bio.index') }}" class="back-link">
    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
      <polyline points="15 18 9 12 15 6"/>
    </svg>
    Kembali ke Konten &amp; Link
  </a>

  <div class="detail-grid">

    {{-- ░░ COL 1 · PROFILE ░░ --}}
    <div class="profile-card">
      <div class="profile-header">
        <div class="profile-av">{{ strtoupper(substr($user->name, 0, 2)) }}</div>
        <div class="profile-name">{{ $user->name }}</div>
        <div class="profile-handle">/{{ $user->username }}</div>
        <div class="header-badges">
          @if(in_array($user->subscription_plan, ['pro', 'premium']))
            <span class="hbadge hb-vip">
              <svg width="9" height="9" viewBox="0 0 24 24" fill="currentColor"><path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/></svg>
              Pro
            </span>
          @else
            <span class="hbadge hb-neu">Free</span>
          @endif
          @if($user->is_suspended)
            <span class="hbadge hb-off">
              <svg width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><circle cx="12" cy="12" r="10"/><path d="M4.93 4.93l14.14 14.14"/></svg>
              Suspend
            </span>
          @else
            <span class="hbadge hb-ok">
              <svg width="7" height="7" viewBox="0 0 10 10"><circle cx="5" cy="5" r="5" fill="currentColor"/></svg>
              Aktif
            </span>
          @endif
        </div>
      </div>

      <div class="profile-body">
        <div class="profile-bio-box">{{ $user->profile->bio ?? 'Tidak ada bio.' }}</div>

        <div class="info-row">
          <span class="info-lbl">Email</span>
          <span class="info-val">{{ $user->email }}</span>
        </div>
        <div class="info-row">
          <span class="info-lbl">Bergabung</span>
          <span class="info-val">{{ $user->created_at->format('d M Y') }}</span>
        </div>
        <div class="info-row">
          <span class="info-lbl">Total Klik</span>
          <span class="info-val" style="color:var(--v)">{{ number_format($user->clicks_count ?? 0) }}</span>
        </div>
        <div class="info-row">
          <span class="info-lbl">Total Views</span>
          <span class="info-val" style="color:var(--g)">{{ number_format($user->links_sum_views ?? 0) }}</span>
        </div>
      </div>
    </div>

    {{-- ░░ COL 2 · STATS + LINKS ░░ --}}
    <div class="mid-col">

      <div class="stats-row">
        <div class="stat-big">
          <div class="stat-big-icon sbi-violet">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5"/>
            </svg>
          </div>
          <div class="sb-label">Total Klik</div>
          <div class="sb-val">{{ number_format($stats['total_clicks']) }}</div>
          <div class="sb-sub">dari semua link</div>
        </div>
        <div class="stat-big">
          <div class="stat-big-icon sbi-green">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
              <circle cx="12" cy="12" r="3"/>
            </svg>
          </div>
          <div class="sb-label">Total Views</div>
          <div class="sb-val">{{ number_format($stats['total_views']) }}</div>
          <div class="sb-sub">kunjungan halaman</div>
        </div>
      </div>

      <div class="links-card">
        <div class="card-head">
          <div class="card-head-left">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" style="color:var(--ink3)">
              <path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/>
              <path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/>
            </svg>
            <div class="card-title">Daftar Link</div>
          </div>
          <div class="card-sub">{{ $links->count() }} link</div>
        </div>

        @php $maxClicks = $links->max(fn($l) => $l->click()->count()) ?: 1; @endphp

        @if($links->count())
          @foreach($links as $i => $link)
          @php
            $clicks = $link->click()->count();
            $views  = $link->views ?? 0;
            $pct    = round(($clicks / $maxClicks) * 100);
          @endphp
          <div class="link-row">
            <div class="link-num">{{ $i + 1 }}</div>
            <div class="link-ico">
              <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                <path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/>
                <path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/>
              </svg>
            </div>
            <div class="link-info">
              <div class="link-title">{{ $link->title ?? 'Tanpa judul' }}</div>
              <div class="link-url">{{ $link->url }}</div>
            </div>
            <div class="link-bar-col">
              <div class="link-bar-bg">
                <div class="link-bar-fill" style="width:{{ $pct }}%"></div>
              </div>
              <div class="link-bar-pct">{{ $pct }}%</div>
            </div>
            <div class="link-chips">
              <span class="chip chip-click">
                <svg width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M15 15l-2 5L9 9l11 4-5 2z"/></svg>
                {{ number_format($clicks) }}
              </span>
              <span class="chip chip-view">
                <svg width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                {{ number_format($views) }}
              </span>
            </div>
          </div>
          @endforeach
        @else
          <div class="empty-link">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" style="display:block;margin:0 auto 10px;opacity:.25">
              <path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/>
              <path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/>
            </svg>
            Belum ada link yang dibuat
          </div>
        @endif
      </div>

    </div>

    {{-- ░░ COL 3 · QR + ACTIONS ░░ --}}
    <div class="right-col">

      <div class="qr-card">
        <div class="qr-card-head">
          <span class="qr-title">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
              <rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/>
              <rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/>
            </svg>
            QR Code
          </span>
          <span class="qr-badge">PNG ready</span>
        </div>
        <div id="qrcode"></div>
        <div class="qr-url">{{ url('/') }}/{{ $user->username }}</div>
        <button class="qr-dl-btn" onclick="downloadQR()">
          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
            <polyline points="7 10 12 15 17 10"/>
            <line x1="12" y1="15" x2="12" y2="3"/>
          </svg>
          Download QR
        </button>
      </div>

      <div class="action-card">
        <div class="action-title">Aksi Admin</div>
        <a href="{{ url('/') }}/{{ $user->username }}" target="_blank" class="action-btn primary">
          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/>
            <polyline points="15 3 21 3 21 9"/>
            <line x1="10" y1="14" x2="21" y2="3"/>
          </svg>
          Buka Halaman Pengguna
        </a>
      </div>

    </div>

  </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script>
const bioUrl = @json(url('/') . '/' . $user->username);
const qr = new QRCode(document.getElementById('qrcode'), {
  text: bioUrl, width: 152, height: 152,
  colorDark: '#1e1b4b', colorLight: '#ffffff',
  correctLevel: QRCode.CorrectLevel.H,
});
function downloadQR() {
  setTimeout(() => {
    const canvas = document.querySelector('#qrcode canvas');
    if (canvas) { const a = document.createElement('a'); a.href = canvas.toDataURL('image/png'); a.download = 'qr-{{ $user->username }}.png'; a.click(); return; }
    const img = document.querySelector('#qrcode img');
    if (img) { const a = document.createElement('a'); a.href = img.src; a.download = 'qr-{{ $user->username }}.png'; a.click(); }
  }, 100);
}
</script>
@endsection