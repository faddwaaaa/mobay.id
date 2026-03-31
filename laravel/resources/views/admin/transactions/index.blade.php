@extends('admin.layouts.app')
@section('page-title', 'Transaksi')

@section('content')
<div style="display:flex;flex-direction:column;gap:18px;">

  <div>
    <div style="font-size:18px;font-weight:900;color:var(--ink);letter-spacing:-.3px;">History Pembayaran</div>
    <div style="font-size:12px;color:var(--ink3);font-weight:600;margin-top:2px;">Semua transaksi di platform Mobay.id</div>
  </div>

  {{-- STAT CARDS --}}
  <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:14px;">
    @foreach([
      ['lbl'=>'Total Transaksi','val'=>$stats['total'],'ico'=>'🧾','bg'=>'#e0f2fe','vc'=>'var(--ink)'],
      ['lbl'=>'Berhasil','val'=>$stats['success'],'ico'=>'✅','bg'=>'#dcfce7','vc'=>'#16a34a'],
      ['lbl'=>'Pending','val'=>$stats['pending'],'ico'=>'⏳','bg'=>'#fef9c3','vc'=>'#b45309'],
      ['lbl'=>'Total Revenue','val'=>'Rp '.number_format($stats['revenue']),'ico'=>'💰','bg'=>'#ede9fe','vc'=>'#7c3aed'],
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

  {{-- FILTER --}}
  <div class="card" style="overflow:visible;">
    <div style="padding:14px 18px;">
      <form method="GET" action="{{ route('admin.transactions.index') }}" style="display:flex;gap:10px;align-items:center;flex-wrap:wrap;">
        <div class="searchbox" style="width:260px;">
          <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="var(--ink4)" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
          <input name="search" placeholder="Cari order ID, nama user…" value="{{ request('search') }}">
        </div>
        <select name="status" style="padding:7px 12px;border-radius:10px;border:1.5px solid var(--line);background:var(--bg);font-family:var(--font);font-size:12.5px;color:var(--ink2);outline:none;">
          <option value="">Semua Status</option>
          <option value="success" {{ request('status')==='success'?'selected':'' }}>Berhasil</option>
          <option value="pending" {{ request('status')==='pending'?'selected':'' }}>Pending</option>
          <option value="failed"  {{ request('status')==='failed' ?'selected':'' }}>Gagal</option>
        </select>
        <button type="submit" class="btn btn-primary">Filter</button>
        @if(request('search') || request('status'))
          <a href="{{ route('admin.transactions.index') }}" class="btn btn-ghost">Reset</a>
        @endif
      </form>
    </div>
  </div>

  {{-- TABLE --}}
  <div class="card">
    <table>
      <thead>
        <tr>
          <th>Order ID</th>
          <th>Pembeli</th>
          <th>Produk</th>
          <th>Jumlah</th>
          <th>Metode</th>
          <th>Tanggal</th>
          <th>Status</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse($transactions as $trx)
        <tr>
          <td>
            <div style="font-size:12px;font-weight:800;color:var(--ink);font-family:var(--mono);">{{ $trx->order_id ?? '-' }}</div>
            <div style="font-size:10px;color:var(--ink4);font-family:var(--mono);">{{ $trx->transaction_id ?? '-' }}</div>
          </td>
          <td>
            @if($trx->user)
            <div class="ucell">
              <div class="uav" style="background:linear-gradient(135deg,#38bdf8,#818cf8);width:26px;height:26px;font-size:9px;">{{ strtoupper(substr($trx->user->name,0,2)) }}</div>
              <div>
                <div style="font-size:12px;font-weight:800;color:var(--ink);">{{ $trx->user->name }}</div>
                <div style="font-size:10px;color:var(--ink4);font-family:var(--mono);">{{ $trx->user->username }}</div>
              </div>
            </div>
            @else <span style="color:var(--ink4);">—</span>
            @endif
          </td>
          <td style="font-size:12px;font-weight:700;color:var(--ink2);">{{ $trx->product_title }}</td>
          <td style="font-size:13px;font-weight:900;color:var(--ink);font-family:var(--mono);">Rp {{ number_format($trx->amount) }}</td>
          <td class="num">{{ $trx->payment_method }}</td>
          <td class="num">{{ $trx->created_at->format('d M Y') }}</td>
          <td>
            @if($trx->status === 'success')
              <span class="badge b-ok">Berhasil</span>
            @elseif($trx->status === 'pending')
              <span class="badge b-pnd">Pending</span>
            @else
              <span class="badge b-off">Gagal</span>
            @endif
          </td>
          <td>
            <a href="{{ route('admin.transactions.show', $trx) }}" class="btn btn-ghost" style="padding:5px 10px;font-size:11.5px;">Detail</a>
          </td>
        </tr>
        @empty
        <tr><td colspan="8" style="text-align:center;padding:30px;color:var(--ink4);">Belum ada transaksi</td></tr>
        @endforelse
      </tbody>
    </table>

    @if($transactions->hasPages())
    <div style="padding:14px 18px;border-top:1.5px solid var(--line);display:flex;justify-content:space-between;align-items:center;">
      <div style="font-size:12px;color:var(--ink3);font-weight:600;">Menampilkan {{ $transactions->firstItem() }}–{{ $transactions->lastItem() }} dari {{ $transactions->total() }}</div>
      <div style="display:flex;gap:4px;">
        @if($transactions->onFirstPage())
          <span class="btn btn-ghost" style="padding:5px 10px;font-size:12px;opacity:.4;cursor:default;">← Prev</span>
        @else
          <a href="{{ $transactions->previousPageUrl() }}" class="btn btn-ghost" style="padding:5px 10px;font-size:12px;">← Prev</a>
        @endif
        @if($transactions->hasMorePages())
          <a href="{{ $transactions->nextPageUrl() }}" class="btn btn-primary" style="padding:5px 10px;font-size:12px;">Next →</a>
        @else
          <span class="btn btn-primary" style="padding:5px 10px;font-size:12px;opacity:.4;cursor:default;">Next →</span>
        @endif
      </div>
    </div>
    @endif
  </div>

</div>
@endsection