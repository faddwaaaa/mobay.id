@extends('admin.layouts.app')
@section('page-title', 'Laporan Profil')

@php
    $reasonLabels = [
        'spam' => 'Spam',
        'scam' => 'Penipuan / Scam',
        'hate_speech' => 'Ujaran Kebencian',
        'adult_content' => 'Konten Dewasa',
        'violence' => 'Kekerasan',
        'fake_account' => 'Akun Palsu',
        'copyright' => 'Pelanggaran Hak Cipta',
        'other' => 'Lainnya',
    ];
@endphp

@section('content')
<div style="display:flex;flex-direction:column;gap:18px;">

  <div>
    <div style="font-size:18px;font-weight:900;color:var(--ink);letter-spacing:-.3px;">Laporan Halaman Publik</div>
    <div style="font-size:12px;color:var(--ink3);font-weight:600;margin-top:2px;">Laporan dari pengunjung terhadap profil publik seller.</div>
  </div>

  <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:14px;">
    @foreach([
      ['lbl'=>'Total Laporan','val'=>$stats['total'],'ico'=>'📋','bg'=>'#e0f2fe','vc'=>'var(--ink)'],
      ['lbl'=>'Pending','val'=>$stats['pending'],'ico'=>'⏳','bg'=>'#fef9c3','vc'=>'#b45309'],
      ['lbl'=>'Reviewed','val'=>$stats['reviewed'],'ico'=>'✅','bg'=>'#dcfce7','vc'=>'#16a34a'],
      ['lbl'=>'Rejected','val'=>$stats['rejected'],'ico'=>'🚫','bg'=>'#fee2e2','vc'=>'#be123c'],
    ] as $s)
    <div class="card" style="padding:18px 20px;">
      <div style="display:flex;align-items:center;gap:10px;margin-bottom:10px;">
        <div style="width:36px;height:36px;border-radius:10px;background:{{ $s['bg'] }};display:flex;align-items:center;justify-content:center;font-size:16px;">{{ $s['ico'] }}</div>
        <div style="font-size:11.5px;color:var(--ink3);font-weight:600;">{{ $s['lbl'] }}</div>
      </div>
      <div style="font-size:22px;font-weight:900;color:{{ $s['vc'] }};letter-spacing:-.5px;">{{ $s['val'] }}</div>
    </div>
    @endforeach
  </div>

  <div class="card" style="overflow:visible;">
    <div style="padding:14px 18px;">
      <form method="GET" action="{{ route('admin.reports.index') }}" style="display:flex;gap:10px;align-items:center;flex-wrap:wrap;">
        <select name="status" style="padding:7px 12px;border-radius:10px;border:1.5px solid var(--line);background:var(--bg);font-family:var(--font);font-size:12.5px;color:var(--ink2);outline:none;">
          <option value="">Semua Status</option>
          <option value="pending" {{ request('status')==='pending'?'selected':'' }}>Pending</option>
          <option value="reviewed" {{ request('status')==='reviewed'?'selected':'' }}>Reviewed</option>
          <option value="rejected" {{ request('status')==='rejected'?'selected':'' }}>Rejected</option>
        </select>
        <select name="reason" style="padding:7px 12px;border-radius:10px;border:1.5px solid var(--line);background:var(--bg);font-family:var(--font);font-size:12.5px;color:var(--ink2);outline:none;">
          <option value="">Semua Alasan</option>
          @foreach($reasonLabels as $key => $label)
            <option value="{{ $key }}" {{ request('reason')===$key ? 'selected' : '' }}>{{ $label }}</option>
          @endforeach
        </select>
        <button type="submit" class="btn btn-primary">Filter</button>
        @if(request('status') || request('reason'))
          <a href="{{ route('admin.reports.index') }}" class="btn btn-ghost">Reset</a>
        @endif
      </form>
    </div>
  </div>

  <div class="card">
    <table>
      <thead>
        <tr>
          <th>Waktu</th>
          <th>Dilaporkan</th>
          <th>Alasan</th>
          <th>Detail</th>
          <th>Sumber</th>
          <th>Status</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse($reports as $report)
        <tr>
          <td class="num">{{ $report->created_at->format('d M Y H:i') }}</td>
          <td>
            @if($report->reportedUser)
              <div style="font-weight:800;color:var(--ink);">{{ $report->reportedUser->name }}</div>
              <div style="font-size:10.5px;color:var(--ink4);font-family:var(--mono);">{{ '@'.$report->reportedUser->username }}</div>
            @else
              <span style="color:var(--ink4);">User tidak ditemukan</span>
            @endif
          </td>
          <td style="font-size:12px;font-weight:700;color:var(--ink2);">{{ $reasonLabels[$report->reason] ?? ucfirst($report->reason) }}</td>
          <td style="max-width:260px;">
            <div style="font-size:12px;color:var(--ink2);line-height:1.45;">
              {{ $report->detail ?: 'Tanpa detail tambahan' }}
            </div>
          </td>
          <td>
            <div style="font-size:11px;color:var(--ink3);font-family:var(--mono);">{{ $report->reporter_ip ?: '-' }}</div>
            <div style="font-size:10px;color:var(--ink4);max-width:210px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $report->page_url ?: '-' }}</div>
          </td>
          <td>
            @if($report->status === 'pending')
              <span class="badge b-pnd">Pending</span>
            @elseif($report->status === 'reviewed')
              <span class="badge b-ok">Reviewed</span>
            @else
              <span class="badge b-off">Rejected</span>
            @endif
          </td>
          <td>
            <div style="display:flex;gap:6px;flex-wrap:wrap;">
              @if($report->status !== 'reviewed')
              <form method="POST" action="{{ route('admin.reports.updateStatus', $report) }}">
                @csrf @method('PATCH')
                <input type="hidden" name="status" value="reviewed">
                <button class="btn btn-primary" style="padding:5px 10px;font-size:11px;">Review</button>
              </form>
              @endif
              @if($report->status !== 'rejected')
              <form method="POST" action="{{ route('admin.reports.updateStatus', $report) }}">
                @csrf @method('PATCH')
                <input type="hidden" name="status" value="rejected">
                <button class="btn btn-danger" style="padding:5px 10px;font-size:11px;">Reject</button>
              </form>
              @endif
            </div>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="7" style="text-align:center;padding:28px;color:var(--ink4);">Belum ada laporan masuk</td>
        </tr>
        @endforelse
      </tbody>
    </table>

    @if($reports->hasPages())
    <div style="padding:14px 18px;border-top:1.5px solid var(--line);display:flex;justify-content:space-between;align-items:center;">
      <div style="font-size:12px;color:var(--ink3);font-weight:600;">Menampilkan {{ $reports->firstItem() }}–{{ $reports->lastItem() }} dari {{ $reports->total() }}</div>
      <div style="display:flex;gap:4px;">
        @if($reports->onFirstPage())
          <span class="btn btn-ghost" style="padding:5px 10px;font-size:12px;opacity:.4;cursor:default;">← Prev</span>
        @else
          <a href="{{ $reports->previousPageUrl() }}" class="btn btn-ghost" style="padding:5px 10px;font-size:12px;">← Prev</a>
        @endif
        @if($reports->hasMorePages())
          <a href="{{ $reports->nextPageUrl() }}" class="btn btn-primary" style="padding:5px 10px;font-size:12px;">Next →</a>
        @else
          <span class="btn btn-primary" style="padding:5px 10px;font-size:12px;opacity:.4;cursor:default;">Next →</span>
        @endif
      </div>
    </div>
    @endif
  </div>

</div>
@endsection

