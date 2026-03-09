{{-- resources/views/admin/reports/show.blade.php --}}
@extends('admin.layouts.app')

@section('page-title', 'Detail Laporan')

@section('content')

@php
    $statusColor = match($report->status) {
        'pending'  => 'b-pnd',
        'reviewed' => 'b-ok',
        'rejected' => 'b-off',
        default    => '',
    };
    $statusLabel = match($report->status) {
        'pending'  => 'Menunggu Tinjauan',
        'reviewed' => 'Sudah Ditinjau',
        'rejected' => 'Ditolak',
        default    => ucfirst($report->status),
    };
    $reasonLabels = [
        'spam'          => ['label' => 'Spam',                 'bg' => '#fff7ed', 'color' => '#c2410c'],
        'scam'          => ['label' => 'Penipuan / Scam',      'bg' => '#fee2e2', 'color' => '#b91c1c'],
        'hate_speech'   => ['label' => 'Ujaran Kebencian',     'bg' => '#f3e8ff', 'color' => '#7e22ce'],
        'adult_content' => ['label' => 'Konten Dewasa',        'bg' => '#fce7f3', 'color' => '#be185d'],
        'violence'      => ['label' => 'Kekerasan / Ancaman',  'bg' => '#fee2e2', 'color' => '#991b1b'],
        'fake_account'  => ['label' => 'Akun Palsu',           'bg' => '#fef9c3', 'color' => '#92400e'],
        'copyright'     => ['label' => 'Pelanggaran Hak Cipta','bg' => '#eff3ff', 'color' => '#1d4ed8'],
        'other'         => ['label' => 'Lainnya',              'bg' => '#f1f5f9', 'color' => '#475569'],
    ];
    $r = $reasonLabels[$report->reason] ?? ['label' => $report->reason, 'bg' => '#f1f5f9', 'color' => '#475569'];

    $score = $report->risk_score ?? 0;
    $level = $report->risk_level ?? 'RENDAH';
    $riskColor = match($level) {
        'KRITIS' => ['bar' => '#ef4444', 'text' => '#b91c1c', 'bg' => '#fee2e2'],
        'TINGGI' => ['bar' => '#f97316', 'text' => '#c2410c', 'bg' => '#ffedd5'],
        'SEDANG' => ['bar' => '#f59e0b', 'text' => '#92400e', 'bg' => '#fef9c3'],
        default  => ['bar' => '#22c55e', 'text' => '#15803d', 'bg' => '#dcfce7'],
    };
    $pct = min(100, ($score / 40) * 100);
    $ip  = $report->reporter_ip ?? $report->ip_address ?? null;
@endphp

<style>
.rp-grid      { display: grid; grid-template-columns: 1fr 320px; gap: 18px; align-items: start; }
.rp-row       { display: flex; gap: 0; border-top: 1.5px solid var(--line); }
.rp-row:first-child { border-top: none; }
.rp-label     { width: 180px; flex-shrink: 0; padding: 13px 16px; font-size: 10px; font-weight: 800; letter-spacing: .7px; text-transform: uppercase; color: var(--ink3); display: flex; align-items: flex-start; padding-top: 15px; }
.rp-val       { flex: 1; padding: 12px 16px 12px 0; font-size: 12.5px; color: var(--ink2); display: flex; align-items: center; flex-wrap: wrap; gap: 6px; }
.rp-val a     { color: var(--b500); text-decoration: none; word-break: break-all; }
.rp-val a:hover { text-decoration: underline; }
.rp-val code  { font-family: var(--mono); font-size: 12px; background: var(--bg); border: 1.5px solid var(--line); padding: 3px 10px; border-radius: 7px; color: var(--ink2); }
.rp-desc      { background: var(--bg); border: 1.5px solid var(--line); border-radius: 10px; padding: 10px 14px; font-size: 12.5px; line-height: 1.6; color: var(--ink2); width: 100%; }
.rp-note-box  { background: #eff3ff; border: 1.5px solid #d0d9ff; border-radius: 10px; padding: 10px 14px; font-size: 12px; font-style: italic; color: var(--ink2); width: 100%; }
.rp-freeze    { display: flex; gap: 10px; padding: 12px 16px; background: #fee2e2; border: 1.5px solid #fecaca; border-radius: 12px; margin-bottom: 16px; }
.rp-freeze p  { font-size: 12.5px; font-weight: 700; color: #b91c1c; }
.rp-freeze span { font-size: 11px; color: #ef4444; margin-top: 2px; display: block; }
.side-card    { background: var(--white); border: 1.5px solid var(--line); border-radius: var(--r); overflow: hidden; margin-bottom: 14px; }
.side-head    { padding: 12px 16px; border-bottom: 1.5px solid var(--line); font-size: 10px; font-weight: 800; letter-spacing: .7px; text-transform: uppercase; color: var(--ink3); }
.side-body    { padding: 16px; }
.act-btn      { display: flex; align-items: center; justify-content: center; gap: 7px; width: 100%; padding: 10px 16px; border-radius: 10px; font-family: var(--font); font-size: 12.5px; font-weight: 800; cursor: pointer; border: none; transition: all .15s; margin-bottom: 8px; text-decoration: none; }
.act-btn:last-child { margin-bottom: 0; }
.act-green    { background: #16a34a; color: white; }
.act-green:hover { background: #15803d; }
.act-red      { background: white; color: #b91c1c; border: 1.5px solid #fecaca; }
.act-red:hover { background: #fee2e2; }
.act-ghost    { background: var(--bg); color: var(--ink2); border: 1.5px solid var(--line); }
.act-ghost:hover { background: #eff3ff; border-color: #d0d9ff; }
.act-blue     { background: var(--b500); color: white; }
.act-blue:hover { background: var(--b600); }
.act-dashed   { background: transparent; color: var(--ink3); border: 1.5px dashed var(--line); font-size: 11.5px; }
.act-dashed:hover { border-color: var(--ink3); color: var(--ink2); }
.u-row        { display: flex; align-items: center; gap: 10px; margin-bottom: 14px; }
.u-av         { width: 38px; height: 38px; border-radius: 10px; background: var(--bg); border: 1.5px solid var(--line); display: flex; align-items: center; justify-content: center; flex-shrink: 0; overflow: hidden; }
.u-av img     { width: 100%; height: 100%; object-fit: cover; }
.u-av svg     { color: var(--ink4); }
.u-name       { font-size: 13px; font-weight: 800; color: var(--ink); }
.u-slug       { font-size: 11px; color: var(--ink3); font-family: var(--mono); }
.u-stat       { display: flex; justify-content: space-between; align-items: center; padding: 7px 0; border-top: 1.5px solid var(--line); font-size: 12px; }
.u-stat-label { color: var(--ink3); font-weight: 600; }
.u-stat-val   { font-weight: 800; color: var(--ink2); }
.u-stat-val.danger { color: var(--rose); }
.nav-btns     { display: flex; gap: 8px; }
.nav-btns a   { flex: 1; display: flex; align-items: center; justify-content: center; gap: 5px; padding: 8px; background: white; border: 1.5px solid var(--line); border-radius: 10px; font-size: 12px; font-weight: 700; color: var(--ink2); text-decoration: none; transition: all .15s; }
.nav-btns a:hover { background: var(--bg); border-color: #d0d9ff; color: var(--b500); }
.note-area    { width: 100%; border: 1.5px solid var(--line); border-radius: 10px; padding: 10px 12px; font-family: var(--font); font-size: 12.5px; color: var(--ink); background: var(--bg); outline: none; resize: none; transition: border .15s; margin-bottom: 8px; }
.note-area:focus { border-color: var(--b400); background: white; }
.status-done  { display: flex; align-items: center; gap: 10px; padding: 10px 12px; border-radius: 10px; margin-bottom: 8px; }
.sd-ok  { background: #dcfce7; }
.sd-off { background: #fee2e2; }
.sd-ico { width: 32px; height: 32px; border-radius: 9px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.ico-ok  { background: #bbf7d0; color: #16a34a; }
.ico-off { background: #fecaca; color: #b91c1c; }
.sd-title { font-size: 12.5px; font-weight: 800; }
.sd-sub   { font-size: 11px; margin-top: 2px; }
.sd-ok .sd-title { color: #15803d; }
.sd-ok .sd-sub   { color: #16a34a; }
.sd-off .sd-title { color: #b91c1c; }
.sd-off .sd-sub   { color: #ef4444; }
.ev-grid  { display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px; }
.ev-item  { position: relative; }
.ev-thumb { display: block; aspect-ratio: 1; border-radius: 10px; overflow: hidden; border: 1.5px solid var(--line); background: var(--bg); transition: border-color .15s; }
.ev-thumb:hover { border-color: var(--b400); }
.ev-thumb img { width: 100%; height: 100%; object-fit: cover; }
.ev-fb    { width: 100%; height: 100%; display: none; flex-direction: column; align-items: center; justify-content: center; gap: 6px; color: var(--ink4); font-size: 11px; }
.ev-label { text-align: center; font-size: 11px; color: var(--ink3); font-weight: 600; margin-top: 5px; }
.ev-hover { position: absolute; inset: 0; border-radius: 10px; background: rgba(0,0,0,0); display: flex; align-items: center; justify-content: center; transition: background .15s; }
.ev-item:hover .ev-hover { background: rgba(0,0,0,.2); }
.ev-open  { opacity: 0; width: 28px; height: 28px; background: white; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: var(--ink2); transition: opacity .15s; box-shadow: 0 2px 8px rgba(0,0,0,.15); }
.ev-item:hover .ev-open { opacity: 1; }
.risk-score { font-size: 36px; font-weight: 900; }
.risk-bar-wrap { height: 7px; background: var(--line); border-radius: 99px; overflow: hidden; margin: 8px 0 6px; }
.risk-bar { height: 100%; border-radius: 99px; }
.risk-sub { font-size: 11px; color: var(--ink3); font-weight: 600; }
.divider { border: none; border-top: 1.5px solid var(--line); margin: 10px 0; }
.section-hdr { display: flex; align-items: center; justify-content: space-between; margin-bottom: 18px; }
.section-hdr h1 { font-size: 20px; font-weight: 900; color: var(--ink); letter-spacing: -.3px; }
.page-sub { font-size: 11.5px; color: var(--ink3); font-weight: 600; margin-top: 3px; display: flex; align-items: center; gap: 5px; }
.page-sub a { color: var(--ink3); text-decoration: none; }
.page-sub a:hover { color: var(--b500); }
.page-sub svg { color: var(--ink4); }
</style>

{{-- ── Header ── --}}
<div class="section-hdr">
    <div>
        <h1>Detail Laporan</h1>
        <div class="page-sub">
            <a href="{{ route('admin.reports.index') }}">Laporan Akun</a>
            <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
            <span style="font-family:var(--mono);color:var(--ink2);">{{ $report->ticket_code }}</span>
        </div>
    </div>
    <div class="nav-btns">
        @if($prevReport)
        <a href="{{ route('admin.reports.show', $prevReport) }}">
            <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
            Sebelumnya
        </a>
        @endif
        @if($nextReport)
        <a href="{{ route('admin.reports.show', $nextReport) }}">
            Berikutnya
            <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
        </a>
        @endif
        <a href="{{ route('admin.reports.index') }}">
            <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 10h16M4 14h8"/></svg>
            Semua Laporan
        </a>
    </div>
</div>

{{-- ── Alert freeze ── --}}
@if($report->triggered_freeze)
<div class="rp-freeze">
    <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="#ef4444" stroke-width="2" style="flex-shrink:0;margin-top:1px"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
    <div>
        <p>Peringatan: Laporan ini memicu sistem deteksi volume tinggi</p>
        <span>Akun yang dilaporkan menerima banyak laporan dari IP berbeda dalam waktu singkat. Prioritaskan tinjauan ini.</span>
    </div>
</div>
@endif

{{-- ── Grid utama ── --}}
<div class="rp-grid">

    {{-- KOLOM KIRI --}}
    <div>

        {{-- Informasi Laporan --}}
        <div class="card" style="margin-bottom:14px;">
            <div class="card-head">
                <div>
                    <div class="card-title">Informasi Laporan</div>
                    <div class="card-sub" style="font-family:var(--mono);">{{ $report->ticket_code }}</div>
                </div>
                <span class="badge {{ $statusColor }}">{{ $statusLabel }}</span>
            </div>

            <div>
                <div class="rp-row">
                    <div class="rp-label">Kategori</div>
                    <div class="rp-val">
                        <span style="display:inline-block;padding:3px 10px;border-radius:6px;font-size:11px;font-weight:800;background:{{ $r['bg'] }};color:{{ $r['color'] }};">{{ $r['label'] }}</span>
                    </div>
                </div>

                <div class="rp-row">
                    <div class="rp-label">Deskripsi Pelapor</div>
                    <div class="rp-val" style="align-items:flex-start;padding-top:14px;padding-bottom:14px;">
                        @if($report->detail)
                            <div class="rp-desc">{{ $report->detail }}</div>
                        @else
                            <span style="color:var(--ink4);font-style:italic;">Pelapor tidak mengisi deskripsi tambahan.</span>
                        @endif
                    </div>
                </div>

                <div class="rp-row">
                    <div class="rp-label">URL Dilaporkan</div>
                    <div class="rp-val" style="flex-direction:column;align-items:flex-start;">
                        @if($report->page_url)
                            <a href="{{ $report->page_url }}" target="_blank" style="display:inline-flex;align-items:flex-start;gap:4px;">
                                <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="flex-shrink:0;margin-top:1px"><path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                {{ $report->page_url }}
                            </a>
                            <span style="font-size:11px;color:var(--ink4);">Klik untuk membuka halaman yang dilaporkan di tab baru.</span>
                        @else
                            <span style="color:var(--ink4);">—</span>
                        @endif
                    </div>
                </div>

                <div class="rp-row">
                    <div class="rp-label">IP Pelapor</div>
                    <div class="rp-val">
                        @if($ip)
                            <code>{{ $ip }}</code>
                            <a href="https://ipinfo.io/{{ $ip }}" target="_blank" style="font-size:11px;">Cek lokasi IP</a>
                        @else
                            <span style="color:var(--ink4);">—</span>
                        @endif
                    </div>
                </div>

                @if($report->user_agent)
                <div class="rp-row">
                    <div class="rp-label">Perangkat Pelapor</div>
                    <div class="rp-val" style="word-break:break-all;font-size:11.5px;color:var(--ink3);">{{ $report->user_agent }}</div>
                </div>
                @endif

                <div class="rp-row">
                    <div class="rp-label">Waktu Laporan</div>
                    <div class="rp-val">
                        {{ $report->created_at->format('d M Y, H:i:s') }}
                        <span style="font-size:11px;color:var(--ink4);">({{ $report->created_at->diffForHumans() }})</span>
                    </div>
                </div>

                @if($report->reviewer)
                <div class="rp-row">
                    <div class="rp-label">Ditinjau Oleh</div>
                    <div class="rp-val">
                        {{ $report->reviewer->name }}
                        @if($report->reviewed_at)
                            <span style="color:var(--ink4);font-size:11.5px;">— {{ $report->reviewed_at->format('d M Y, H:i') }}</span>
                        @endif
                    </div>
                </div>
                @endif

                @if($report->moderator_note)
                <div class="rp-row">
                    <div class="rp-label">Catatan Moderator</div>
                    <div class="rp-val" style="align-items:flex-start;padding-top:14px;padding-bottom:14px;">
                        <div class="rp-note-box">"{{ $report->moderator_note }}"</div>
                    </div>
                </div>
                @endif
            </div>
        </div>

        {{-- Bukti Pendukung --}}
        <div class="card">
            <div class="card-head">
                <div>
                    <div class="card-title">Bukti Pendukung</div>
                    <div class="card-sub">
                        @if($report->evidence_paths && count($report->evidence_paths) > 0)
                            {{ count($report->evidence_paths) }} file dilampirkan oleh pelapor
                        @else
                            Tidak ada bukti yang dilampirkan
                        @endif
                    </div>
                </div>
                @if($report->evidence_paths && count($report->evidence_paths) > 0)
                <a href="{{ route('admin.reports.evidence', $report) }}" class="card-action">
                    Unduh Semua
                </a>
                @endif
            </div>

            <div style="padding:16px;">
                @if($report->evidence_paths && count($report->evidence_paths) > 0)
                <div class="ev-grid">
                    @foreach($report->evidence_paths as $idx => $path)
                    <div class="ev-item">
                        <a href="{{ route('admin.reports.evidence.file', [$report, $idx]) }}" target="_blank" class="ev-thumb">
                            <img src="{{ route('admin.reports.evidence.file', [$report, $idx]) }}"
                                 alt="Bukti {{ $idx + 1 }}"
                                 onerror="this.style.display='none';this.nextElementSibling.style.display='flex';">
                            <div class="ev-fb">
                                <svg width="28" height="28" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                File {{ $idx + 1 }}
                            </div>
                        </a>
                        <div class="ev-hover">
                            <a href="{{ route('admin.reports.evidence.file', [$report, $idx]) }}" target="_blank" class="ev-open">
                                <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                            </a>
                        </div>
                        <div class="ev-label">Bukti {{ $idx + 1 }}</div>
                    </div>
                    @endforeach
                </div>
                @else
                <div style="text-align:center;padding:30px 0;">
                    <svg width="36" height="36" fill="none" viewBox="0 0 24 24" stroke="var(--line)" stroke-width="1.5" style="margin:0 auto 10px;display:block;"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    <div style="font-size:13px;font-weight:700;color:var(--ink4);">Tidak ada bukti dilampirkan</div>
                    <div style="font-size:11.5px;color:var(--ink4);margin-top:4px;">Pelapor tidak menyertakan tangkapan layar.</div>
                </div>
                @endif
            </div>
        </div>

    </div>

    {{-- KOLOM KANAN --}}
    <div>

        {{-- Tindakan Moderasi --}}
        <div class="side-card">
            <div class="side-head">Tindakan Moderasi</div>
            <div class="side-body">

                @if($report->status === 'pending')

                    <form method="POST" action="{{ route('admin.reports.updateStatus', $report) }}" style="margin-bottom:8px;">
                        @csrf @method('PATCH')
                        <input type="hidden" name="status" value="reviewed">
                        <button type="submit" class="act-btn act-green">
                            <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            Tandai Sudah Ditinjau
                        </button>
                    </form>

                    <form method="POST" action="{{ route('admin.reports.updateStatus', $report) }}" style="margin-bottom:8px;">
                        @csrf @method('PATCH')
                        <input type="hidden" name="status" value="rejected">
                        <button type="submit" class="act-btn act-red">
                            <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                            Tolak — Laporan Tidak Valid
                        </button>
                    </form>

                    <hr class="divider">

                    <button onclick="toggleNote()" class="act-btn act-ghost" style="margin-bottom:0;">
                        <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        Tambah Catatan Internal
                    </button>
                    <div id="noteBox" style="display:none;margin-top:10px;">
                        <textarea id="noteInput" class="note-area" rows="3" maxlength="500"
                            placeholder="Catatan internal — tidak terlihat oleh pelapor atau akun yang dilaporkan...">{{ $report->moderator_note }}</textarea>
                        <button type="button" onclick="saveNote()" class="act-btn act-blue" style="margin-bottom:0;">
                            Simpan Catatan
                        </button>
                    </div>

                @else

                    <div class="status-done {{ $report->status === 'reviewed' ? 'sd-ok' : 'sd-off' }}">
                        <div class="sd-ico {{ $report->status === 'reviewed' ? 'ico-ok' : 'ico-off' }}">
                            @if($report->status === 'reviewed')
                                <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            @else
                                <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                            @endif
                        </div>
                        <div>
                            <div class="sd-title">{{ $report->status === 'reviewed' ? 'Laporan Sudah Ditinjau' : 'Laporan Ditolak' }}</div>
                            <div class="sd-sub">{{ $report->reviewed_at?->format('d M Y, H:i') }}</div>
                        </div>
                    </div>

                    @if($report->moderator_note)
                    <div class="rp-note-box" style="margin-bottom:10px;">"{{ $report->moderator_note }}"</div>
                    @endif

                    <button onclick="toggleNote()" class="act-btn act-ghost">
                        {{ $report->moderator_note ? 'Edit Catatan' : 'Tambah Catatan' }}
                    </button>
                    <div id="noteBox" style="display:none;margin-top:10px;">
                        <textarea id="noteInput" class="note-area" rows="3" maxlength="500"
                            placeholder="Catatan internal...">{{ $report->moderator_note }}</textarea>
                        <button type="button" onclick="saveNote()" class="act-btn act-blue" style="margin-bottom:8px;">
                            Simpan Catatan
                        </button>
                    </div>

                    <hr class="divider">

                    <form method="POST" action="{{ route('admin.reports.updateStatus', $report) }}">
                        @csrf @method('PATCH')
                        <input type="hidden" name="status" value="pending">
                        <button type="submit" class="act-btn act-dashed" style="margin-bottom:0;">
                            Kembalikan ke Pending
                        </button>
                    </form>

                @endif
            </div>
        </div>

        {{-- Akun yang Dilaporkan --}}
        <div class="side-card">
            <div class="side-head">Akun Dilaporkan</div>
            <div class="side-body">
                <div class="u-row">
                    <div class="u-av">
                        @if($report->reportedUser?->avatar)
                            <img src="{{ asset('storage/' . $report->reportedUser->avatar) }}" alt="">
                        @else
                            <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        @endif
                    </div>
                    <div>
                        <div class="u-name">{{ $report->reportedUser?->name ?? '–' }}</div>
                        <div class="u-slug">@{{ $report->reportedUser?->username ?? '–' }}</div>
                    </div>
                </div>

                <div class="u-stat">
                    <span class="u-stat-label">Email</span>
                    <span class="u-stat-val" style="font-size:11.5px;max-width:160px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $report->reportedUser?->email ?? '–' }}</span>
                </div>
                <div class="u-stat">
                    <span class="u-stat-label">Total laporan diterima</span>
                    <span class="u-stat-val {{ $reportedUserTotalReports >= 5 ? 'danger' : '' }}">{{ $reportedUserTotalReports }}x</span>
                </div>
                <div class="u-stat">
                    <span class="u-stat-label">Bergabung sejak</span>
                    <span class="u-stat-val">{{ $report->reportedUser?->created_at?->format('M Y') ?? '–' }}</span>
                </div>

                <div style="margin-top:14px;display:flex;flex-direction:column;gap:7px;">
                    @if($report->reportedUser)
                    <a href="{{ url('/' . $report->reportedUser->username) }}" target="_blank" class="act-btn btn btn-primary" style="margin-bottom:0;font-size:12px;padding:8px 12px;">
                        <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                        Buka Profil Publik
                    </a>
                    @endif
                    <a href="{{ route('admin.reports.index', ['reported_user' => $report->reported_user_id]) }}" class="act-btn act-ghost" style="margin-bottom:0;font-size:12px;padding:8px 12px;">
                        Semua Laporan Akun Ini
                    </a>
                </div>
            </div>
        </div>

        {{-- Skor Risiko --}}
        <div class="side-card">
            <div class="side-head">Skor Risiko Akun</div>
            <div class="side-body">
                <div style="display:flex;align-items:center;justify-content:space-between;">
                    <span class="risk-score" style="color:{{ $riskColor['text'] }};">{{ $score }}</span>
                    <span style="padding:4px 10px;border-radius:7px;font-size:11px;font-weight:800;background:{{ $riskColor['bg'] }};color:{{ $riskColor['text'] }};">{{ $level }}</span>
                </div>
                <div class="risk-bar-wrap">
                    <div class="risk-bar" style="width:{{ $pct }}%;background:{{ $riskColor['bar'] }};"></div>
                </div>
                <div class="risk-sub">Bobot kategori &times; jumlah laporan akun (maks. 40)</div>
            </div>
        </div>

    </div>
</div>

<script>
function toggleNote() {
    const box = document.getElementById('noteBox');
    const visible = box.style.display !== 'none';
    box.style.display = visible ? 'none' : 'block';
    if (!visible) document.getElementById('noteInput').focus();
}
async function saveNote() {
    const note = document.getElementById('noteInput').value.trim();
    const btn  = event.target;
    btn.disabled    = true;
    btn.textContent = 'Menyimpan...';
    try {
        const res = await fetch('{{ route('admin.reports.saveNote', $report) }}', {
            method:  'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept':       'application/json',
            },
            body: JSON.stringify({ note }),
        });
        const j = await res.json();
        if (j.success) {
            location.reload();
        } else {
            alert('Gagal menyimpan catatan.');
            btn.disabled    = false;
            btn.textContent = 'Simpan Catatan';
        }
    } catch {
        alert('Terjadi kesalahan jaringan.');
        btn.disabled    = false;
        btn.textContent = 'Simpan Catatan';
    }
}
</script>

@endsection