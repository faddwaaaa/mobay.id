{{-- FILE: resources/views/admin/appeals/show.blade.php --}}
@extends('admin.layouts.app')
@section('page-title', 'Detail Banding')
@section('content')

@php
    $bc = match($appeal->status) { 'pending' => 'b-pnd', 'approved' => 'b-ok', 'rejected' => 'b-off', default => '' };
    $bl = match($appeal->status) { 'pending' => 'Menunggu', 'approved' => 'Disetujui', 'rejected' => 'Ditolak', default => $appeal->status };
@endphp

<style>
.ap-grid   { display:grid; grid-template-columns:1fr 300px; gap:18px; align-items:start; }
.rp-row    { display:flex; gap:0; border-top:1.5px solid var(--line); }
.rp-row:first-child { border-top:none; }
.rp-label  { width:160px; flex-shrink:0; padding:13px 16px; font-size:10px; font-weight:800; letter-spacing:.7px; text-transform:uppercase; color:var(--ink3); display:flex; align-items:flex-start; padding-top:15px; }
.rp-val    { flex:1; padding:12px 16px 12px 0; font-size:12.5px; color:var(--ink2); }
.rp-box    { background:var(--bg); border:1.5px solid var(--line); border-radius:10px; padding:12px 14px; font-size:12.5px; line-height:1.65; color:var(--ink2); width:100%; }
.side-card { background:var(--white); border:1.5px solid var(--line); border-radius:var(--r); overflow:hidden; margin-bottom:14px; }
.side-head { padding:12px 16px; border-bottom:1.5px solid var(--line); font-size:10px; font-weight:800; letter-spacing:.7px; text-transform:uppercase; color:var(--ink3); }
.side-body { padding:16px; }
.act-btn   { display:flex; align-items:center; justify-content:center; gap:7px; width:100%; padding:10px 16px; border-radius:10px; font-family:var(--font); font-size:12.5px; font-weight:800; cursor:pointer; border:none; transition:all .15s; margin-bottom:8px; text-decoration:none; }
.u-row     { display:flex; align-items:center; gap:10px; margin-bottom:12px; }
.u-av      { width:38px; height:38px; border-radius:10px; background:var(--bg); border:1.5px solid var(--line); display:flex; align-items:center; justify-content:center; flex-shrink:0; overflow:hidden; }
.u-av img  { width:100%; height:100%; object-fit:cover; }
.u-name    { font-size:13px; font-weight:800; color:var(--ink); }
.u-slug    { font-size:11px; color:var(--ink3); font-family:var(--mono); }
.u-stat    { display:flex; justify-content:space-between; align-items:center; padding:7px 0; border-top:1.5px solid var(--line); font-size:12px; }
.u-stat-label { color:var(--ink3); font-weight:600; }
.u-stat-val   { font-weight:800; color:var(--ink2); }
.note-area { width:100%; border:1.5px solid var(--line); border-radius:10px; padding:10px 12px; font-family:var(--font); font-size:12.5px; color:var(--ink); background:var(--bg); outline:none; resize:none; transition:border .15s; margin-bottom:8px; display:block; }
.note-area:focus { border-color:var(--b400); background:white; }
.section-hdr { display:flex; align-items:center; justify-content:space-between; margin-bottom:18px; }
.section-hdr h1 { font-size:20px; font-weight:900; color:var(--ink); letter-spacing:-.3px; }
.page-sub { font-size:11.5px; color:var(--ink3); font-weight:600; margin-top:3px; display:flex; align-items:center; gap:5px; }
.page-sub a { color:var(--ink3); text-decoration:none; }
.page-sub a:hover { color:var(--b500); }
.divider { border:none; border-top:1.5px solid var(--line); margin:10px 0; }
.result-box { border-radius:10px; padding:12px 14px; display:flex; align-items:flex-start; gap:10px; margin-bottom:10px; }
.result-approved { background:#dcfce7; border:1.5px solid #bbf7d0; }
.result-rejected { background:#fee2e2; border:1.5px solid #fecaca; }
.result-pending  { background:#fef9c3; border:1.5px solid #fde68a; }
.evidence-list { display:grid; gap:10px; }
.evidence-item {
    border:1.5px solid var(--line); border-radius:12px; padding:12px 14px; background:#fbfcff;
}
.evidence-top { display:flex; justify-content:space-between; align-items:flex-start; gap:10px; flex-wrap:wrap; margin-bottom:8px; }
.evidence-code { font-family:var(--mono); font-size:11px; color:var(--ink2); background:white; border:1.5px solid var(--line); border-radius:7px; padding:3px 8px; }
.evidence-meta { font-size:11.5px; color:var(--ink3); line-height:1.5; }
.evidence-actions { display:flex; gap:8px; flex-wrap:wrap; margin-top:10px; }
.evidence-btn {
    display:inline-flex; align-items:center; gap:6px; padding:7px 11px; border-radius:9px;
    font-size:11.5px; font-weight:800; text-decoration:none; border:1.5px solid var(--line);
    background:white; color:var(--ink2);
}
.evidence-btn:hover { background:#eff3ff; border-color:#d0d9ff; color:var(--b500); }
.evidence-gallery { display:grid; grid-template-columns:repeat(auto-fit,minmax(160px,1fr)); gap:12px; margin-top:10px; }
.evidence-thumb {
    border:1.5px solid var(--line); border-radius:12px; overflow:hidden; background:white;
}
.evidence-thumb-media {
    aspect-ratio: 4 / 3;
    background:#eef4ff;
    display:flex;
    align-items:center;
    justify-content:center;
}
.evidence-thumb-media img {
    width:100%;
    height:100%;
    object-fit:cover;
    display:block;
}
.evidence-thumb-body { padding:10px; }
.evidence-thumb-name {
    font-size:11px;
    font-weight:800;
    color:var(--ink2);
    font-family:var(--mono);
    word-break:break-all;
}
</style>

<div class="section-hdr">
    <div>
        <h1>Detail Banding</h1>
        <div class="page-sub">
            <a href="{{ route('admin.appeals.index') }}">Pengajuan Banding</a>
            <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
            <span style="font-family:var(--mono);color:var(--ink2);">{{ $appeal->ticket_code }}</span>
        </div>
    </div>
    <a href="{{ route('admin.appeals.index') }}" style="display:flex;align-items:center;gap:6px;padding:8px 14px;background:white;border:1.5px solid var(--line);border-radius:10px;font-size:12px;font-weight:700;color:var(--ink2);text-decoration:none;">
        <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
        Semua Banding
    </a>
</div>

@if(session('success'))
<div style="background:#dcfce7;border:1.5px solid #bbf7d0;border-radius:10px;padding:12px 16px;margin-bottom:16px;font-size:12.5px;font-weight:700;color:#15803d;">
    {{ session('success') }}
</div>
@endif

<div class="ap-grid">

{{-- KIRI --}}
<div>
    <div class="card">
        <div class="card-head">
            <div>
                <div class="card-title">Isi Pengajuan Banding</div>
                <div class="card-sub" style="font-family:var(--mono);">{{ $appeal->ticket_code }}</div>
            </div>
            <span class="badge {{ $bc }}">{{ $bl }}</span>
        </div>

        <div>
            <div class="rp-row">
                <div class="rp-label">Alasan Banding</div>
                <div class="rp-val" style="padding-top:14px;padding-bottom:14px;">
                    <div class="rp-box">{{ $appeal->reason }}</div>
                </div>
            </div>

            @if($appeal->additional_info)
            <div class="rp-row">
                <div class="rp-label">Info Tambahan</div>
                <div class="rp-val" style="padding-top:14px;padding-bottom:14px;">
                    <div class="rp-box" style="background:#eff3ff;border-color:#d0d9ff;">{{ $appeal->additional_info }}</div>
                </div>
            </div>
            @endif

            <div class="rp-row">
                <div class="rp-label">Bukti Banding</div>
                <div class="rp-val" style="padding-top:14px;padding-bottom:14px;">
                    @if(($appeal->evidence_count ?? 0) > 0)
                        <div class="evidence-item">
                            <div class="evidence-top">
                                <div>
                                    <div class="evidence-code">{{ $appeal->ticket_code }}</div>
                                    <div class="evidence-meta" style="margin-top:6px;">
                                        {{ $appeal->evidence_count }} file bukti dari pengaju banding
                                    </div>
                                </div>
                                <div class="evidence-actions" style="margin-top:0;">
                                    <a href="{{ route('admin.appeals.evidence', $appeal) }}" class="evidence-btn" target="_blank">
                                        Unduh Semua
                                    </a>
                                </div>
                            </div>

                            <div class="evidence-gallery">
                                @foreach(($appeal->evidence_paths ?? []) as $index => $path)
                                    @php
                                        $filename = basename($path);
                                        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
                                        $isImage = in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif'], true);
                                    @endphp
                                    <div class="evidence-thumb">
                                        <div class="evidence-thumb-media">
                                            @if($isImage)
                                                <img src="{{ route('admin.appeals.evidence.file', [$appeal, $index]) }}" alt="Bukti banding {{ $index + 1 }}">
                                            @else
                                                <div class="evidence-meta" style="padding:12px;text-align:center;">
                                                    File {{ strtoupper($ext ?: 'DOC') }}
                                                </div>
                                            @endif
                                        </div>
                                        <div class="evidence-thumb-body">
                                            <div class="evidence-thumb-name">{{ $filename }}</div>
                                            <div class="evidence-actions">
                                                <a href="{{ route('admin.appeals.evidence.file', [$appeal, $index]) }}" class="evidence-btn" target="_blank">
                                                    Buka
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <div class="rp-box" style="font-style:italic;color:var(--ink3);">
                            Pengaju banding belum melampirkan file bukti.
                        </div>
                    @endif
                </div>
            </div>

            <div class="rp-row">
                <div class="rp-label">Bukti Laporan</div>
                <div class="rp-val" style="padding-top:14px;padding-bottom:14px;">
                    @if($relatedReports->isNotEmpty())
                        <div class="evidence-list">
                            @foreach($relatedReports as $report)
                            <div class="evidence-item">
                                <div class="evidence-top">
                                    <div>
                                        <div class="evidence-code">{{ $report->ticket_code }}</div>
                                        <div class="evidence-meta" style="margin-top:6px;">
                                            {{ $report->created_at->format('d M Y, H:i') }}
                                            <span style="color:var(--ink4);">({{ $report->created_at->diffForHumans() }})</span>
                                        </div>
                                    </div>
                                    <div class="evidence-meta" style="text-align:right;">
                                        {{ $report->evidence_count }} file bukti
                                        <div style="margin-top:2px;">Status: {{ ucfirst($report->status) }}</div>
                                    </div>
                                </div>

                                @if($report->detail)
                                    <div class="rp-box" style="padding:10px 12px;">{{ \Illuminate\Support\Str::limit($report->detail, 180) }}</div>
                                @endif

                                <div class="evidence-actions">
                                    <a href="{{ route('admin.reports.show', $report) }}" class="evidence-btn">
                                        Lihat Detail Laporan
                                    </a>
                                    <a href="{{ route('admin.reports.evidence', $report) }}" class="evidence-btn" target="_blank">
                                        Buka Bukti
                                    </a>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="rp-box" style="font-style:italic;color:var(--ink3);">
                            Belum ada laporan dengan file bukti untuk akun ini.
                        </div>
                    @endif
                </div>
            </div>

            <div class="rp-row">
                <div class="rp-label">Waktu Diajukan</div>
                <div class="rp-val">
                    {{ $appeal->created_at->format('d M Y, H:i:s') }}
                    <span style="font-size:11px;color:var(--ink4);">({{ $appeal->created_at->diffForHumans() }})</span>
                </div>
            </div>

            @if($appeal->reviewer)
            <div class="rp-row">
                <div class="rp-label">Ditinjau Oleh</div>
                <div class="rp-val">
                    {{ $appeal->reviewer->name }}
                    @if($appeal->reviewed_at)
                    <span style="color:var(--ink4);font-size:11.5px;">— {{ $appeal->reviewed_at->format('d M Y, H:i') }}</span>
                    @endif
                </div>
            </div>
            @endif

            @if($appeal->admin_note)
            <div class="rp-row">
                <div class="rp-label">Catatan Admin</div>
                <div class="rp-val" style="padding-top:14px;padding-bottom:14px;">
                    <div class="rp-box" style="background:#eff3ff;border-color:#d0d9ff;font-style:italic;">"{{ $appeal->admin_note }}"</div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

{{-- KANAN --}}
<div>

    {{-- Keputusan --}}
    <div class="side-card">
        <div class="side-head">Keputusan Moderasi</div>
        <div class="side-body">

            @if($appeal->status === 'pending')

                {{-- Setujui --}}
                <div id="approveSection">
                    <button onclick="toggleSection('approveForm')" class="act-btn" style="background:#16a34a;color:white;margin-bottom:8px;">
                        <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        Setujui Banding — Pulihkan Akun
                    </button>
                    <div id="approveForm" style="display:none;margin-bottom:12px;">
                        <div style="background:#dcfce7;border:1.5px solid #bbf7d0;border-radius:10px;padding:10px 12px;margin-bottom:10px;">
                            <p style="font-size:11.5px;font-weight:800;color:#15803d;margin-bottom:3px;">Konfirmasi Pemulihan Akun</p>
                            <p style="font-size:11px;color:#16a34a;line-height:1.5;">Penangguhan akan dicabut dan akun @{{ $appeal->user?->username }} dapat diakses kembali.</p>
                        </div>
                        <form method="POST" action="{{ route('admin.appeals.approve', $appeal) }}">
                            @csrf @method('PATCH')
                            <textarea name="admin_note" class="note-area" rows="2" maxlength="1000"
                                placeholder="Catatan keputusan (opsional)..."></textarea>
                            <button type="submit" class="act-btn" style="background:#16a34a;color:white;margin-bottom:0;">
                                Konfirmasi — Pulihkan Akun
                            </button>
                        </form>
                        <button onclick="toggleSection('approveForm')" style="background:none;border:none;font-size:11.5px;color:var(--ink3);cursor:pointer;margin-top:6px;display:block;width:100%;text-align:center;">Batal</button>
                    </div>
                </div>

                <hr class="divider">

                {{-- Tolak --}}
                <button onclick="toggleSection('rejectForm')" class="act-btn" style="background:white;color:#b91c1c;border:1.5px solid #fecaca;margin-bottom:0;">
                    <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    Tolak Banding
                </button>
                <div id="rejectForm" style="display:none;margin-top:10px;">
                    <div style="background:#fee2e2;border:1.5px solid #fecaca;border-radius:10px;padding:10px 12px;margin-bottom:10px;">
                        <p style="font-size:11.5px;font-weight:800;color:#b91c1c;margin-bottom:3px;">Konfirmasi Penolakan Banding</p>
                        <p style="font-size:11px;color:#ef4444;line-height:1.5;">Akun tetap ditangguhkan. Pengguna akan diberitahu bahwa banding ditolak.</p>
                    </div>
                    <form method="POST" action="{{ route('admin.appeals.reject', $appeal) }}">
                        @csrf @method('PATCH')
                        <textarea name="admin_note" class="note-area" rows="2" required maxlength="1000"
                            placeholder="Alasan penolakan (wajib diisi — akan ditampilkan ke pengguna)..."></textarea>
                        <button type="submit" class="act-btn" style="background:#b91c1c;color:white;margin-bottom:0;">
                            Konfirmasi — Tolak Banding
                        </button>
                    </form>
                    <button onclick="toggleSection('rejectForm')" style="background:none;border:none;font-size:11.5px;color:var(--ink3);cursor:pointer;margin-top:6px;display:block;width:100%;text-align:center;">Batal</button>
                </div>

            @elseif($appeal->status === 'approved')
                <div class="result-box result-approved">
                    <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="#16a34a" stroke-width="2.5" style="flex-shrink:0;margin-top:1px;"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    <div>
                        <div style="font-size:12.5px;font-weight:800;color:#15803d;">Banding Disetujui</div>
                        <div style="font-size:11.5px;color:#16a34a;margin-top:2px;">Akun telah dipulihkan pada {{ $appeal->reviewed_at?->format('d M Y, H:i') }}.</div>
                    </div>
                </div>

            @else
                <div class="result-box result-rejected">
                    <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="#b91c1c" stroke-width="2.5" style="flex-shrink:0;margin-top:1px;"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    <div>
                        <div style="font-size:12.5px;font-weight:800;color:#b91c1c;">Banding Ditolak</div>
                        <div style="font-size:11.5px;color:#ef4444;margin-top:2px;">Ditolak pada {{ $appeal->reviewed_at?->format('d M Y, H:i') }}.</div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- Info Akun --}}
    <div class="side-card">
        <div class="side-head">Akun Pengaju</div>
        <div class="side-body">
            <div class="u-row">
                <div class="u-av">
                    @if($appeal->user?->avatar)
                        <img src="{{ asset('storage/' . $appeal->user->avatar) }}" alt="">
                    @else
                        <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    @endif
                </div>
                <div>
                    <div class="u-name">{{ $appeal->user?->name ?? '–' }}</div>
                    <div class="u-slug">@{{ $appeal->user?->username ?? '–' }}</div>
                </div>
            </div>

            <div class="u-stat">
                <span class="u-stat-label">Email</span>
                <span class="u-stat-val" style="font-size:11px;max-width:150px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $appeal->user?->email ?? '–' }}</span>
            </div>
            <div class="u-stat">
                <span class="u-stat-label">Status akun</span>
                <span class="u-stat-val">
                    @if($appeal->user?->is_suspended)
                        <span style="color:#b91c1c;">Ditangguhkan</span>
                    @else
                        <span style="color:#16a34a;">Aktif</span>
                    @endif
                </span>
            </div>
            <div class="u-stat">
                <span class="u-stat-label">Bergabung</span>
                <span class="u-stat-val">{{ $appeal->user?->created_at?->format('M Y') ?? '–' }}</span>
            </div>

            <div style="margin-top:12px;">
                <a href="{{ route('admin.reports.index', ['reported_user' => $appeal->user_id]) }}"
                   class="act-btn" style="background:var(--bg);color:var(--ink2);border:1.5px solid var(--line);font-size:12px;padding:8px;margin-bottom:0;">
                    Lihat Riwayat Laporan Akun Ini
                </a>
            </div>
        </div>
    </div>

</div>
</div>

<script>
function toggleSection(id) {
    const el = document.getElementById(id);
    el.style.display = el.style.display === 'none' ? 'block' : 'none';
}
</script>

@endsection
