@extends('admin.layouts.app')
@section('page-title', 'Penarikan')

@section('content')
<div style="display:flex;flex-direction:column;gap:18px;">

  <div>
    <div style="font-size:18px;font-weight:900;color:var(--ink);letter-spacing:-.3px;">History Penarikan</div>
    <div style="font-size:12px;color:var(--ink3);font-weight:600;margin-top:2px;">Kelola semua permintaan penarikan saldo</div>
  </div>

  {{-- STAT CARDS --}}
  <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:14px;">
    @foreach([
      ['lbl'=>'Total Request','val'=>$stats['total'],'ico'=>'📋','bg'=>'#e0f2fe'],
      ['lbl'=>'Pending','val'=>$stats['pending'],'ico'=>'⏳','bg'=>'#fef9c3'],
      ['lbl'=>'Disetujui','val'=>$stats['approved'],'ico'=>'✅','bg'=>'#dcfce7'],
      ['lbl'=>'Total Dicairkan','val'=>'Rp '.number_format($stats['total_amount']),'ico'=>'💸','bg'=>'#ede9fe'],
    ] as $s)
    <div class="card" style="padding:18px 20px;">
      <div style="display:flex;align-items:center;gap:10px;margin-bottom:10px;">
        <div style="width:36px;height:36px;border-radius:10px;background:{{ $s['bg'] }};display:flex;align-items:center;justify-content:center;font-size:16px;">{{ $s['ico'] }}</div>
        <div style="font-size:11.5px;color:var(--ink3);font-weight:600;">{{ $s['lbl'] }}</div>
      </div>
      <div style="font-size:22px;font-weight:900;color:var(--ink);letter-spacing:-.5px;">{{ $s['val'] }}</div>
    </div>
    @endforeach
  </div>

  {{-- FILTER --}}
  <div class="card" style="overflow:visible;">
    <div style="padding:14px 18px;">
      <form method="GET" action="{{ route('admin.withdrawals.index') }}" style="display:flex;gap:10px;align-items:center;flex-wrap:wrap;">
        <div class="searchbox" style="width:260px;">
          <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="var(--ink4)" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
          <input name="search" placeholder="Cari nama user…" value="{{ request('search') }}">
        </div>
        <select name="status" style="padding:7px 12px;border-radius:10px;border:1.5px solid var(--line);background:var(--bg);font-family:var(--font);font-size:12.5px;color:var(--ink2);outline:none;">
          <option value="">Semua Status</option>
          <option value="pending"  {{ request('status')==='pending' ?'selected':'' }}>Pending</option>
          <option value="approved" {{ request('status')==='approved'?'selected':'' }}>Disetujui</option>
          <option value="rejected" {{ request('status')==='rejected'?'selected':'' }}>Ditolak</option>
        </select>
        <button type="submit" class="btn btn-primary">Filter</button>
        @if(request('search') || request('status'))
          <a href="{{ route('admin.withdrawals.index') }}" class="btn btn-ghost">Reset</a>
        @endif
      </form>
    </div>
  </div>

  {{-- TABLE --}}
  <div class="card">
    <table>
      <thead>
        <tr>
          <th>User</th>
          <th>Jumlah</th>
          <th>Bank</th>
          <th>No. Rekening</th>
          <th>Tanggal</th>
          <th>Status</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse($withdrawals as $wd)
        <tr>
          <td>
            @if($wd->user)
            <div class="ucell">
              <div class="uav" style="background:linear-gradient(135deg,#38bdf8,#818cf8);width:26px;height:26px;font-size:9px;">{{ strtoupper(substr($wd->user->name,0,2)) }}</div>
              <div>
                <div style="font-size:12px;font-weight:800;color:var(--ink);">{{ $wd->user->name }}</div>
                <div style="font-size:10px;color:var(--ink4);font-family:var(--mono);">{{ $wd->user->username }}</div>
              </div>
            </div>
            @else <span style="color:var(--ink4);">—</span>
            @endif
          </td>
          <td style="font-size:13px;font-weight:900;color:var(--ink);font-family:var(--mono);">Rp {{ number_format($wd->amount) }}</td>
          <td style="font-size:12px;font-weight:700;color:var(--ink2);">{{ $wd->bank_name ?? '—' }}</td>
          <td>
            <div style="font-size:12px;font-weight:700;color:var(--ink);font-family:var(--mono);">{{ $wd->account_number ?? '—' }}</div>
            <div style="font-size:10px;color:var(--ink4);">{{ $wd->account_name ?? '' }}</div>
          </td>
          <td class="num">{{ $wd->created_at->format('d M Y') }}</td>
          <td>
            @if($wd->status === 'approved')
              <span class="badge b-ok">Disetujui</span>
            @elseif($wd->status === 'pending')
              <span class="badge b-pnd">Pending</span>
            @else
              <span class="badge b-off">Ditolak</span>
            @endif
          </td>
          <td>
            <div style="display:flex;gap:6px;align-items:center;">
              <a href="{{ route('admin.withdrawals.show', $wd) }}" class="btn btn-ghost" style="padding:5px 10px;font-size:11.5px;">Detail</a>
              @if($wd->status === 'pending')
                <form method="POST" action="{{ route('admin.withdrawals.approve', $wd) }}"
                      onsubmit="return confirm('Setujui penarikan Rp {{ number_format($wd->amount) }}?')">
                  @csrf @method('PATCH')
                  <button type="submit" class="btn btn-primary" style="padding:5px 10px;font-size:11.5px;">Setujui</button>
                </form>
                <form method="POST" action="{{ route('admin.withdrawals.reject', $wd) }}"
                      onsubmit="return confirm('Tolak penarikan ini?')">
                  @csrf @method('PATCH')
                  <input type="hidden" name="reason" value="Ditolak oleh admin.">
                  <button type="submit" class="btn btn-danger" style="padding:5px 10px;font-size:11.5px;">Tolak</button>
                </form>
              @endif
            </div>
          </td>
        </tr>
        @empty
        <tr><td colspan="7" style="text-align:center;padding:30px;color:var(--ink4);">Belum ada permintaan penarikan</td></tr>
        @endforelse
      </tbody>
    </table>

    @if($withdrawals->hasPages())
    <div style="padding:14px 18px;border-top:1.5px solid var(--line);display:flex;justify-content:space-between;align-items:center;">
      <div style="font-size:12px;color:var(--ink3);font-weight:600;">Menampilkan {{ $withdrawals->firstItem() }}–{{ $withdrawals->lastItem() }} dari {{ $withdrawals->total() }}</div>
      <div style="display:flex;gap:4px;">
        @if($withdrawals->onFirstPage())
          <span class="btn btn-ghost" style="padding:5px 10px;font-size:12px;opacity:.4;cursor:default;">← Prev</span>
        @else
          <a href="{{ $withdrawals->previousPageUrl() }}" class="btn btn-ghost" style="padding:5px 10px;font-size:12px;">← Prev</a>
        @endif
        @if($withdrawals->hasMorePages())
          <a href="{{ $withdrawals->nextPageUrl() }}" class="btn btn-primary" style="padding:5px 10px;font-size:12px;">Next →</a>
        @else
          <span class="btn btn-primary" style="padding:5px 10px;font-size:12px;opacity:.4;cursor:default;">Next →</span>
        @endif
      </div>
    </div>
    @endif
  </div>

</div>
@endsection