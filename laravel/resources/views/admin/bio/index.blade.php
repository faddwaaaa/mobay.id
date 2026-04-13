@extends('admin.layouts.app')
@section('page-title', 'Konten & Link')

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

.bio-wrap { display: flex; flex-direction: column; gap: 20px; }

/* ── Stat grid ── */
.top-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 14px;
}
.stat-card {
  background: var(--card-bg);
  border-radius: 16px;
  border: 1px solid var(--line-c);
  padding: 18px 20px 16px;
  display: flex; flex-direction: column; gap: 10px;
  transition: box-shadow var(--T), border-color var(--T), transform var(--T);
  cursor: default;
}
.stat-card:hover {
  box-shadow: 0 8px 28px rgba(0,0,0,.07);
  border-color: #cbd5e1;
  transform: translateY(-2px);
}
.stat-icon-wrap {
  width: 38px; height: 38px; border-radius: 10px;
  display: flex; align-items: center; justify-content: center;
  flex-shrink: 0;
}
.si-blue   { background: var(--b-bg); color: var(--b); }
.si-violet { background: var(--v-bg); color: var(--v); }
.si-green  { background: var(--g-bg); color: var(--g); }
.si-red    { background: #fff1f2;     color: #e11d48; }

.stat-val { font-size: 28px; font-weight: 800; color: var(--ink0); letter-spacing: -.8px; line-height: 1; }
.stat-lbl { font-size: 11.5px; color: var(--ink2); font-weight: 600; }
.stat-tag {
  display: inline-flex; align-items: center; gap: 4px;
  font-size: 11px; font-weight: 700; padding: 3px 9px;
  border-radius: 20px; width: fit-content;
}
.tag-up  { color: var(--g); background: var(--g-bg); }
.tag-neu { color: var(--b); background: var(--b-bg); }

/* ── Filter bar ── */
.filter-bar {
  display: flex; gap: 10px; align-items: center; flex-wrap: wrap;
}
.search-wrap {
  display: flex; align-items: center; gap: 8px;
  background: var(--card-bg);
  border: 1px solid var(--line-c);
  border-radius: 10px; padding: 9px 14px;
  flex: 1; min-width: 220px;
  transition: border-color var(--T), box-shadow var(--T);
}
.search-wrap:focus-within {
  border-color: #93c5fd;
  box-shadow: 0 0 0 3px rgba(59,130,246,.12);
}
.search-wrap input {
  border: none; outline: none; background: none;
  font-size: 13px; color: var(--ink0); width: 100%;
}
.search-wrap input::placeholder { color: var(--ink3); }
.search-icon { color: var(--ink3); flex-shrink: 0; }

.fselect {
  border: 1px solid var(--line-c); border-radius: 10px;
  padding: 9px 14px; font-size: 13px; font-weight: 600;
  background: var(--card-bg); color: var(--ink0);
  cursor: pointer; outline: none;
  transition: border-color var(--T), box-shadow var(--T);
}
.fselect:focus {
  border-color: #93c5fd;
  box-shadow: 0 0 0 3px rgba(59,130,246,.12);
}

.filter-btn {
  display: inline-flex; align-items: center; gap: 6px;
  border: 1px solid var(--line-c); border-radius: 10px;
  padding: 9px 16px; font-size: 13px; font-weight: 700;
  background: var(--card-bg); color: var(--ink0);
  cursor: pointer; white-space: nowrap;
  transition: background var(--T), border-color var(--T), color var(--T), box-shadow var(--T);
}
.filter-btn:hover {
  background: var(--b-bg); border-color: #93c5fd;
  color: var(--b); box-shadow: 0 2px 8px rgba(59,130,246,.12);
}

/* ── Table card ── */
.table-card {
  background: var(--card-bg);
  border-radius: 16px;
  border: 1px solid var(--line-c);
  overflow: hidden;
  box-shadow: 0 1px 3px rgba(0,0,0,.04);
}
table { width: 100%; border-collapse: collapse; }
thead { background: #f8fafc; }
th {
  font-size: 10.5px; font-weight: 800; color: var(--ink3);
  text-align: left; padding: 12px 18px;
  letter-spacing: .7px; text-transform: uppercase;
  border-bottom: 1px solid var(--line-c);
}
td {
  font-size: 13px; color: var(--ink0);
  padding: 14px 18px;
  border-bottom: 1px solid var(--line-c);
  vertical-align: middle;
}
tr:last-child td { border-bottom: none; }
tbody tr { transition: background var(--T); }
tbody tr:hover td { background: var(--hover-bg); }

/* User cell */
.ucell { display: flex; align-items: center; gap: 10px; }
.uav {
  width: 36px; height: 36px; border-radius: 10px;
  background: linear-gradient(135deg, #4f46e5, #7c3aed);
  display: flex; align-items: center; justify-content: center;
  font-size: 12px; font-weight: 900; color: white;
  letter-spacing: -.5px; flex-shrink: 0;
}
.uname  { font-size: 13px; font-weight: 700; color: var(--ink0); }
.uemail { font-size: 11px; color: var(--ink3); margin-top: 1px; }

/* URL pill */
.url-pill {
  display: inline-flex; align-items: center; gap: 5px;
  background: var(--b-bg); border: 1px solid #bfdbfe;
  padding: 4px 10px; border-radius: 8px;
  font-size: 11.5px; color: var(--b);
  font-family: var(--mono, monospace); font-weight: 600;
  transition: background var(--T), border-color var(--T);
}
.url-pill:hover { background: #dbeafe; border-color: #93c5fd; }

/* Badges */
.badge {
  display: inline-flex; align-items: center; gap: 4px;
  font-size: 10.5px; font-weight: 700;
  padding: 3px 10px; border-radius: 20px;
}
.b-ok  { color: var(--g); background: var(--g-bg); }
.b-off { color: var(--r); background: var(--r-bg); }
.b-vip { color: var(--v); background: var(--v-bg); }
.b-neu { color: var(--ink2); background: var(--hover-bg); }

.num { font-family: var(--mono, monospace); font-size: 13px; font-weight: 700; }

/* Action btn */
.act-btn {
  display: inline-flex; align-items: center; gap: 5px;
  border: 1px solid var(--line-c); border-radius: 8px;
  padding: 5px 12px; font-size: 12px; font-weight: 700;
  cursor: pointer; background: var(--card-bg); color: var(--ink0);
  text-decoration: none;
  transition: background var(--T), border-color var(--T),
              box-shadow var(--T), color var(--T), transform var(--T);
}
.act-btn:hover {
  background: var(--b-bg); border-color: #93c5fd;
  box-shadow: 0 2px 8px rgba(59,130,246,.12);
  color: var(--b); transform: translateY(-1px);
}
.act-btn:active { transform: translateY(0); }

/* Pagination */
.pag-wrap {
  display: flex; align-items: center; justify-content: space-between;
  padding: 14px 18px; border-top: 1px solid var(--line-c);
  background: #fafbfc;
}
.pag-info { font-size: 12px; color: var(--ink3); font-weight: 600; }
.pag-btns { display: flex; gap: 4px; }
.pag-btn {
  border: 1px solid var(--line-c); border-radius: 8px;
  padding: 6px 11px; font-size: 12px; font-weight: 700;
  cursor: pointer; background: var(--card-bg); color: var(--ink0);
  text-decoration: none; display: inline-flex; align-items: center;
  transition: background var(--T), border-color var(--T), color var(--T);
}
.pag-btn.active { background: var(--b); color: white; border-color: var(--b); }
.pag-btn:hover:not(.active) { background: var(--hover-bg); border-color: #cbd5e1; }
button.pag-btn:disabled { opacity: .35; cursor: not-allowed; }

/* Empty */
.empty-state { text-align: center; padding: 56px 20px; color: var(--ink3); font-weight: 600; font-size: 13px; }
</style>
@endpush

@section('content')
<div class="bio-wrap">

  {{-- ── STAT CARDS ── --}}
  <div class="top-grid">

    <div class="stat-card">
      <div class="stat-icon-wrap si-blue">
        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/>
          <path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/>
        </svg>
      </div>
      <div class="stat-val">{{ number_format($stats['total_pages']) }}</div>
      <div class="stat-lbl">Total Halaman</div>
      <span class="stat-tag tag-up">
        <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><polyline points="18 15 12 9 6 15"/></svg>
        +{{ $stats['new_pages_today'] }} hari ini
      </span>
    </div>

    <div class="stat-card">
      <div class="stat-icon-wrap si-violet">
        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5"/>
        </svg>
      </div>
      <div class="stat-val">{{ number_format($stats['total_clicks']) }}</div>
      <div class="stat-lbl">Total Klik</div>
      <span class="stat-tag tag-up">
        <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><polyline points="18 15 12 9 6 15"/></svg>
        +{{ $stats['clicks_percent'] }}% minggu ini
      </span>
    </div>

    <div class="stat-card">
      <div class="stat-icon-wrap si-green">
        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <rect x="3" y="3" width="18" height="18" rx="2"/>
          <path d="M3 9h18M9 21V9"/>
        </svg>
      </div>
      <div class="stat-val">{{ number_format($stats['total_links'] ?? 0) }}</div>
      <div class="stat-lbl">Total Link Dibuat</div>
      <span class="stat-tag tag-neu">semua pengguna</span>
    </div>

    <div class="stat-card">
      <div class="stat-icon-wrap si-red">
        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
          <circle cx="12" cy="12" r="3"/>
        </svg>
      </div>
      <div class="stat-val">{{ number_format($stats['total_views'] ?? 0) }}</div>
      <div class="stat-lbl">Total Views</div>
      <span class="stat-tag tag-neu">semua halaman</span>
    </div>

  </div>

  {{-- ── FILTER ── --}}
  <div class="filter-bar">
    <div class="search-wrap">
      <svg class="search-icon" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
      </svg>
      <input type="text" id="searchInput" placeholder="Cari username atau URL halaman…"
        value="{{ request('search') }}" onkeydown="if(event.key==='Enter')applyFilter()"/>
    </div>
    <select class="fselect" id="statusFilter" onchange="applyFilter()">
      <option value="">Semua Status</option>
      <option value="active"  {{ request('status') === 'active'  ? 'selected' : '' }}>Aktif</option>
      <option value="suspend" {{ request('status') === 'suspend' ? 'selected' : '' }}>Suspend</option>
    </select>
    <button onclick="applyFilter()" class="filter-btn">
      <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
        <line x1="4" y1="6" x2="20" y2="6"/><line x1="8" y1="12" x2="16" y2="12"/>
        <line x1="11" y1="18" x2="13" y2="18"/>
      </svg>
      Filter
    </button>
  </div>

  {{-- ── TABLE ── --}}
  <div class="table-card">
    <table>
      <thead>
        <tr>
          <th>Pengguna</th>
          <th>URL Halaman</th>
          <th>Jumlah Link</th>
          <th>Total Klik</th>
          <th>Total Views</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse($users as $user)
        <tr>
          <td>
            <div class="ucell">
              <div class="uav">{{ strtoupper(substr($user->name, 0, 2)) }}</div>
              <div>
                <div class="uname">{{ $user->name }}</div>
                <div class="uemail">{{ $user->email }}</div>
              </div>
            </div>
          </td>
          <td>
            <span class="url-pill">
              <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                <path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/>
                <path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/>
              </svg>
              /{{ $user->username }}
            </span>
          </td>
          <td class="num">{{ number_format($user->links_count ?? 0) }}</td>
          <td class="num">{{ number_format($user->clicks_count ?? 0) }}</td>
          <td class="num">{{ number_format($user->links_sum_views ?? 0) }}</td>
          <td>
            <a href="{{ route('admin.bio.show', $user->username) }}" class="act-btn">
              <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                <circle cx="12" cy="12" r="3"/>
              </svg>
              Detail
            </a>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="6">
            <div class="empty-state">
              <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" style="display:block;margin:0 auto 12px;opacity:.25">
                <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
              </svg>
              Tidak ada pengguna ditemukan
            </div>
          </td>
        </tr>
        @endforelse
      </tbody>
    </table>

    <div class="pag-wrap">
      <div class="pag-info">
        Menampilkan {{ $users->firstItem() }}–{{ $users->lastItem() }}
        dari <strong style="color:var(--ink0)">{{ number_format($users->total()) }}</strong> pengguna
      </div>
      <div class="pag-btns">
        @if($users->onFirstPage())
          <button class="pag-btn" disabled>
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><polyline points="15 18 9 12 15 6"/></svg>
          </button>
        @else
          <a href="{{ $users->previousPageUrl() }}" class="pag-btn">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><polyline points="15 18 9 12 15 6"/></svg>
          </a>
        @endif
        @foreach($users->getUrlRange(max(1,$users->currentPage()-2), min($users->lastPage(),$users->currentPage()+2)) as $pg => $url)
          <a href="{{ $url }}" class="pag-btn {{ $pg == $users->currentPage() ? 'active' : '' }}">{{ $pg }}</a>
        @endforeach
        @if($users->hasMorePages())
          <a href="{{ $users->nextPageUrl() }}" class="pag-btn">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><polyline points="9 18 15 12 9 6"/></svg>
          </a>
        @else
          <button class="pag-btn" disabled>
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><polyline points="9 18 15 12 9 6"/></svg>
          </button>
        @endif
      </div>
    </div>
  </div>

</div>
@endsection

@push('scripts')
<script>
function applyFilter() {
  const url = new URL(window.location.href);
  url.searchParams.set('search', document.getElementById('searchInput').value);
  url.searchParams.set('status', document.getElementById('statusFilter').value);
  url.searchParams.set('page', 1);
  window.location.href = url.toString();
}
</script>
@endpush