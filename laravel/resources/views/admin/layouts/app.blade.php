<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Payou.id Admin — @yield('title', 'Dashboard')</title>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
<meta name="csrf-token" content="{{ csrf_token() }}">
<style>
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
:root {
  --sb-bg:    #1a3fa8;
  --sb-hover: rgba(255,255,255,.1);
  --sb-line:  rgba(255,255,255,.1);
  --sb-text:  rgba(255,255,255,.6);
  --bg:       #f0f4ff;
  --white:    #ffffff;
  --line:     #e4ecff;
  --ink:      #0c1533;
  --ink2:     #2d3d6b;
  --ink3:     #7a8db5;
  --ink4:     #c2cfe8;
  --b400:     #3b82f6;
  --b500:     #2356e8;
  --b600:     #1a3fa8;
  --green:    #22c55e;
  --rose:     #f43f5e;
  --amber:    #f59e0b;
  --font:     'Plus Jakarta Sans', sans-serif;
  --mono:     'DM Mono', monospace;
  --sw:       230px;
  --r:        16px;
}
body { font-family: var(--font); background: var(--bg); color: var(--ink); display: flex; height: 100vh; overflow: hidden; font-size: 13.5px; }
::-webkit-scrollbar { width: 3px; }
::-webkit-scrollbar-thumb { background: rgba(255,255,255,.15); border-radius: 3px; }

/* SIDEBAR */
.sidebar {
  width: var(--sw); flex-shrink: 0; height: 100vh;
  background: var(--sb-bg);
  display: flex; flex-direction: column;
  position: relative; overflow: hidden;
}
.sidebar::before {
  content: ''; position: absolute; top: -60px; right: -60px;
  width: 200px; height: 200px; border-radius: 50%;
  background: rgba(255,255,255,.05); pointer-events: none;
}
.sidebar::after {
  content: ''; position: absolute; bottom: 60px; left: -80px;
  width: 200px; height: 200px; border-radius: 50%;
  background: rgba(255,255,255,.04); pointer-events: none;
}
.logo { padding: 22px 20px 18px; display: flex; align-items: center; gap: 10px; border-bottom: 1px solid var(--sb-line); position: relative; z-index: 1; }
.logo-icon { width: 36px; height: 36px; border-radius: 11px; background: rgba(255,255,255,.2); border: 1px solid rgba(255,255,255,.25); display: flex; align-items: center; justify-content: center; font-weight: 900; font-size: 16px; color: white; flex-shrink: 0; }
.logo-name { font-size: 16px; font-weight: 900; color: white; letter-spacing: -.3px; }
.logo-tag  { font-size: 9px; font-weight: 700; color: rgba(255,255,255,.45); letter-spacing: 1.2px; text-transform: uppercase; }
.nav { flex: 1; padding: 14px 12px; display: flex; flex-direction: column; gap: 2px; overflow-y: auto; position: relative; z-index: 1; }
.nav-section { font-size: 8.5px; font-weight: 800; letter-spacing: 1.8px; text-transform: uppercase; color: rgba(255,255,255,.3); padding: 12px 10px 5px; }
.nav-item { display: flex; align-items: center; gap: 10px; padding: 9px 12px; border-radius: 11px; color: var(--sb-text); font-size: 13px; font-weight: 600; cursor: pointer; text-decoration: none; transition: background .15s, color .15s; }
.nav-item:hover { background: var(--sb-hover); color: white; }
.nav-item.active { background: rgba(255,255,255,.18); color: white; font-weight: 800; border: 1px solid rgba(255,255,255,.2); }
.nav-item.active .nav-ico, .nav-item:hover .nav-ico { opacity: 1; }
.nav-ico { width: 17px; height: 17px; flex-shrink: 0; opacity: .7; }
.nav-badge { margin-left: auto; font-size: 9.5px; font-weight: 800; background: rgba(255,255,255,.15); color: white; padding: 2px 7px; border-radius: 99px; }
.nav-badge.red { background: rgba(244,63,94,.3); color: #fda4af; }
.sidebar-foot { padding: 12px; border-top: 1px solid var(--sb-line); position: relative; z-index: 1; }
.profile-row { display: flex; align-items: center; gap: 9px; padding: 8px 10px; border-radius: 11px; cursor: pointer; transition: background .15s; }
.profile-row:hover { background: var(--sb-hover); }
.p-av { width: 32px; height: 32px; border-radius: 9px; flex-shrink: 0; background: rgba(255,255,255,.2); border: 1px solid rgba(255,255,255,.25); display: flex; align-items: center; justify-content: center; font-size: 11px; font-weight: 900; color: white; }
.p-name { font-size: 12.5px; font-weight: 800; color: white; }
.p-role { font-size: 10.5px; color: rgba(255,255,255,.45); font-weight: 600; }

/* MAIN */
.main { flex: 1; display: flex; flex-direction: column; overflow: hidden; }
.topbar { height: 56px; background: var(--white); border-bottom: 1.5px solid var(--line); display: flex; align-items: center; padding: 0 24px; gap: 12px; flex-shrink: 0; }
.tb-left { flex: 1; }
.tb-page  { font-size: 15px; font-weight: 900; color: var(--ink); letter-spacing: -.3px; }
.tb-crumb { font-size: 11px; color: var(--ink3); font-weight: 600; margin-top: 1px; }
.tb-crumb span { color: var(--b500); }
.searchbox { display: flex; align-items: center; gap: 7px; background: var(--bg); border: 1.5px solid var(--line); border-radius: 10px; padding: 7px 12px; width: 200px; transition: all .15s; }
.searchbox:focus-within { border-color: var(--b400); background: white; }
.searchbox input { border: none; background: none; outline: none; font-family: var(--font); font-size: 12.5px; color: var(--ink); width: 100%; }
.searchbox input::placeholder { color: var(--ink4); }
.icon-btn { width: 34px; height: 34px; border-radius: 9px; border: 1.5px solid var(--line); background: white; display: flex; align-items: center; justify-content: center; cursor: pointer; color: var(--ink3); transition: all .15s; position: relative; }
.icon-btn:hover { border-color: var(--b400); color: var(--b500); background: var(--bg); }
.ndot { position: absolute; top: 5px; right: 5px; width: 7px; height: 7px; background: var(--rose); border-radius: 50%; border: 1.5px solid white; }
.content { flex: 1; overflow-y: auto; padding: 22px 24px; }

/* CARD */
.card { background: var(--white); border-radius: var(--r); border: 1.5px solid var(--line); overflow: hidden; }
.card-head { padding: 15px 20px 12px; display: flex; align-items: center; justify-content: space-between; border-bottom: 1.5px solid var(--line); }
.card-title { font-size: 13.5px; font-weight: 800; color: var(--ink); }
.card-sub { font-size: 11px; color: var(--ink4); font-weight: 600; margin-top: 1px; }
.card-action { font-size: 11.5px; font-weight: 800; color: var(--b500); background: #eff3ff; border: 1.5px solid #d0d9ff; padding: 5px 12px; border-radius: 8px; cursor: pointer; text-decoration: none; transition: background .14s; }
.card-action:hover { background: #dce4ff; }

/* TABLE */
table { width: 100%; border-collapse: collapse; }
thead tr { background: #f7f9ff; }
th { padding: 10px 16px; text-align: left; font-size: 10px; font-weight: 800; letter-spacing: .7px; text-transform: uppercase; color: var(--b500); white-space: nowrap; }
td { padding: 12px 16px; border-top: 1.5px solid var(--line); font-size: 12.5px; color: var(--ink2); }
tbody tr { transition: background .12s; }
tbody tr:hover { background: #f7f9ff; }
.num { font-family: var(--mono); font-size: 12px; color: var(--ink3); }
.ucell { display: flex; align-items: center; gap: 9px; }
.uav { width: 30px; height: 30px; border-radius: 8px; font-size: 11px; font-weight: 900; color: white; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.uname { font-size: 12.5px; font-weight: 800; color: var(--ink); }
.uslug { font-size: 10.5px; color: var(--ink4); font-family: var(--mono); }
.badge { display: inline-flex; align-items: center; gap: 4px; padding: 3px 9px; border-radius: 6px; font-size: 10.5px; font-weight: 800; }
.badge::before { content: ''; width: 5px; height: 5px; border-radius: 50%; background: currentColor; }
.b-ok  { color: #16a34a; background: #dcfce7; }
.b-off { color: var(--rose); background: #fee2e2; }
.b-pnd { color: #b45309; background: #fef9c3; }
.b-adm { color: var(--b500); background: #eff3ff; }
.alert { padding: 12px 16px; border-radius: 10px; font-size: 12.5px; font-weight: 700; margin-bottom: 16px; }
.alert-success { background: #dcfce7; color: #15803d; border: 1.5px solid #bbf7d0; }
.alert-error   { background: #fee2e2; color: #b91c1c; border: 1.5px solid #fecaca; }
.btn { display: inline-flex; align-items: center; gap: 6px; padding: 8px 16px; border-radius: 10px; font-family: var(--font); font-size: 12.5px; font-weight: 800; cursor: pointer; border: none; transition: all .15s; text-decoration: none; }
.btn-primary { background: var(--b500); color: white; box-shadow: 0 3px 12px rgba(35,86,232,.25); }
.btn-primary:hover { background: var(--b600); }
.btn-danger  { background: #fee2e2; color: var(--rose); }
.btn-danger:hover  { background: #fecaca; }
.btn-ghost   { background: var(--bg); color: var(--ink2); border: 1.5px solid var(--line); }
.btn-ghost:hover   { background: #eff3ff; border-color: #d0d9ff; }
@keyframes fadeUp { from { opacity:0; transform:translateY(8px); } to { opacity:1; transform:translateY(0); } }
.fade-up { animation: fadeUp .3s ease both; }
</style>
@stack('styles')
</head>
<body>

<aside class="sidebar">
  <div class="logo">
    <div class="logo-icon">P</div>
    <div>
      <div class="logo-name">Payou.id</div>
      <div class="logo-tag">Admin Panel</div>
    </div>
  </div>
  <nav class="nav">
    <div class="nav-section">Menu Utama</div>
    <a href="{{ route('admin.dashboard') }}" class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
      <svg class="nav-ico" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/></svg>
      Dashboard
    </a>
    <a href="{{ route('admin.users.index') }}" class="nav-item {{ request()->routeIs('admin.users*') ? 'active' : '' }}">
      <svg class="nav-ico" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0"/></svg>
      Pengguna
    </a>
  
    <a href="{{ route('admin.analytics.index') }}" class="nav-item {{ request()->routeIs('admin.analytics*') ? 'active' : '' }}">
      <svg class="nav-ico" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
      Analytics
    </a>
    <div class="nav-section">Keuangan</div>
    <a href="{{ route('admin.wallet.index') }}" class="nav-item {{ request()->routeIs('admin.wallet*') ? 'active' : '' }}">
      <svg class="nav-ico" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a5 5 0 00-10 0v2M5 9h14a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2v-8a2 2 0 012-2zm7 4v4m-2-2h4"/></svg>
      Dompet Admin
    </a>
    <a href="{{ route('admin.transactions.index') }}" class="nav-item {{ request()->routeIs('admin.transactions*') ? 'active' : '' }}">
      <svg class="nav-ico" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
      Transaksi
    </a>
    <a href="{{ route('admin.withdrawals.index') }}" class="nav-item {{ request()->routeIs('admin.withdrawals*') ? 'active' : '' }}">
      <svg class="nav-ico" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
      Penarikan
    </a>
    <div class="nav-section">Sistem</div>
    <a href="{{ route('admin.settings.index') }}" class="nav-item {{ request()->routeIs('admin.settings*') ? 'active' : '' }}">
      <svg class="nav-ico" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
      Pengaturan
    </a>

  <a href="{{ route('admin.reports.index') }}"
    class="nav-item {{ request()->routeIs('admin.reports*') ? 'active' : '' }}">
    <svg class="nav-ico" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7 8h10M7 12h6m-6 4h8M5 3h14a2 2 0 012 2v14a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2z"/></svg>
    Laporan Profil
  </a>
  
  </nav>
  <div class="sidebar-foot">
    <div class="profile-row">
      <div class="p-av">{{ strtoupper(substr(auth()->user()->name, 0, 2)) }}</div>
      <div>
        <div class="p-name">{{ auth()->user()->name }}</div>
        <div class="p-role">Administrator</div>
      </div>
      <form method="POST" action="{{ route('logout') }}" style="margin-left:auto">
        @csrf
        <button type="submit" style="background:none;border:none;cursor:pointer;color:rgba(255,255,255,.35);display:flex;align-items:center;transition:color .15s;" onmouseenter="this.style.color='white'" onmouseleave="this.style.color='rgba(255,255,255,.35)'">
          <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
        </button>
      </form>
    </div>
  </div>
</aside>

<div class="main">
  <header class="topbar">
    <div class="tb-left">
      <div class="tb-page">@yield('page-title', 'Dashboard')</div>
      <div class="tb-crumb">Admin / <span>@yield('page-title', 'Dashboard')</span></div>
    </div>
    <div class="searchbox">
      <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="var(--ink4)" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
      <input placeholder="Cari pengguna, link…">
    </div>
    <button class="icon-btn">
      <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
      <span class="ndot"></span>
    </button>
  </header>

  <div class="content fade-up">
    @if(session('success'))
      <div class="alert alert-success">✓ {{ session('success') }}</div>
    @endif
    @if(session('error'))
      <div class="alert alert-error">✕ {{ session('error') }}</div>
    @endif
    @yield('content')
  </div>
</div>

@stack('scripts')
</body>
</html>
