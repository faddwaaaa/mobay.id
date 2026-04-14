@extends('admin.layouts.app')
@section('page-title', 'Detail Penarikan')

@section('content')
<div style="max-width:680px;display:flex;flex-direction:column;gap:18px;">

  <a href="{{ route('admin.withdrawals.index') }}" style="display:inline-flex;align-items:center;gap:6px;font-size:12.5px;font-weight:700;color:var(--sky5);text-decoration:none;">
    ← Kembali ke Daftar Penarikan
  </a>

  <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;align-items:start;">

    {{-- DETAIL CARD --}}
    <div class="card" style="grid-column:1/-1;">
      <div class="card-head">
        <div>
          <div class="card-title">Detail Penarikan</div>
          <div class="card-sub">ID #{{ $withdrawal->id }}</div>
        </div>
        @if($withdrawal->status === 'approved')
          <span class="badge b-ok" style="font-size:12px;padding:5px 12px;">Disetujui</span>
        @elseif($withdrawal->status === 'pending')
          <span class="badge b-pnd" style="font-size:12px;padding:5px 12px;">Pending</span>
        @else
          <span class="badge b-off" style="font-size:12px;padding:5px 12px;">Ditolak</span>
        @endif
      </div>
      <div style="padding:20px;display:flex;flex-direction:column;">
        @foreach([
          ['lbl'=>'User','val'=>($withdrawal->user?->name ?? '—').' (@'.($withdrawal->user?->username ?? '—').')'],
          ['lbl'=>'Jumlah','val'=>'Rp '.number_format($withdrawal->amount)],
          ['lbl'=>'Bank','val'=>$withdrawal->bank_name ?? '—'],
          ['lbl'=>'Nama Rekening','val'=>$withdrawal->account_name ?? '—'],
          ['lbl'=>'Nomor Rekening','val'=>$withdrawal->account_number ?? '—'],
          ['lbl'=>'Payout ID','val'=>$withdrawal->payout_id ?? '—'],
          ['lbl'=>'Catatan','val'=>$withdrawal->notes ?? '—'],
          ['lbl'=>'Alasan Ditolak','val'=>$withdrawal->rejection_reason ?? '—'],
          ['lbl'=>'Disetujui Oleh','val'=>$withdrawal->approved_by ? \App\Models\User::find($withdrawal->approved_by)?->name ?? '—' : '—'],
          ['lbl'=>'Disetujui Pada','val'=>$withdrawal->approved_at ? \Carbon\Carbon::parse($withdrawal->approved_at)->format('d M Y, H:i') : '—'],
          ['lbl'=>'Tanggal Request','val'=>$withdrawal->created_at->format('d M Y, H:i:s')],
        ] as $row)
        <div style="display:flex;align-items:center;padding:11px 0;border-bottom:1.5px solid var(--line);">
          <div style="width:180px;font-size:12px;color:var(--ink3);font-weight:600;flex-shrink:0;">{{ $row['lbl'] }}</div>
          <div style="font-size:13px;font-weight:700;color:var(--ink);">{{ $row['val'] }}</div>
        </div>
        @endforeach
      </div>
    </div>

  </div>

  {{-- ACTION --}}
  @if($withdrawal->status === 'pending')
  <div class="card">
    <div class="card-head"><div class="card-title">Tindakan</div></div>
    <div style="padding:16px 18px;display:flex;gap:10px;align-items:center;">
      <form method="POST" action="{{ route('admin.withdrawals.approve', $withdrawal) }}"
            onsubmit="return confirm('Setujui penarikan Rp {{ number_format($withdrawal->amount) }}?')">
        @csrf @method('PATCH')
        <button type="submit" class="btn btn-primary">✅ Setujui Penarikan</button>
      </form>

      <form method="POST" action="{{ route('admin.withdrawals.reject', $withdrawal) }}"
            onsubmit="return confirm('Tolak penarikan ini?')" style="display:flex;gap:8px;align-items:center;flex:1;">
        @csrf @method('PATCH')
        <input type="text" name="reason" placeholder="Alasan penolakan…"
          style="flex:1;padding:8px 12px;border-radius:10px;border:1.5px solid var(--line);background:var(--bg);font-family:var(--font);font-size:12.5px;color:var(--ink);outline:none;">
        <button type="submit" class="btn btn-danger">✕ Tolak</button>
      </form>
    </div>
  </div>
  @endif

</div>
@endsection