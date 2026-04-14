{{-- ============================================================
     resources/views/admin/transactions/show.blade.php
============================================================ --}}
@extends('admin.layouts.app')
@section('page-title', 'Detail Transaksi')

@section('content')
<div style="max-width:680px;display:flex;flex-direction:column;gap:18px;">

  <a href="{{ route('admin.transactions.index') }}" style="display:inline-flex;align-items:center;gap:6px;font-size:12.5px;font-weight:700;color:var(--sky5);text-decoration:none;">
    ← Kembali ke Daftar Transaksi
  </a>

  <div class="card">
    <div class="card-head">
      <div>
        <div class="card-title">Detail Transaksi</div>
        <div class="card-sub">{{ $transaction->order_id ?? '-' }}</div>
      </div>
      @if($transaction->status === 'success')
        <span class="badge b-ok" style="font-size:12px;padding:5px 12px;">Berhasil</span>
      @elseif($transaction->status === 'pending')
        <span class="badge b-pnd" style="font-size:12px;padding:5px 12px;">Pending</span>
      @else
        <span class="badge b-off" style="font-size:12px;padding:5px 12px;">Gagal</span>
      @endif
    </div>
    <div style="padding:20px;display:flex;flex-direction:column;gap:0;">
      @foreach([
        ['lbl'=>'Order ID','val'=>$transaction->order_id ?? '—'],
        ['lbl'=>'Transaction ID','val'=>$transaction->transaction_id ?? '—'],
        ['lbl'=>'Pembeli','val'=>$transaction->user?->name.' (@'.$transaction->user?->username.')'],
        ['lbl'=>'Produk','val'=>$transaction->product_title],
        ['lbl'=>'Jumlah','val'=>'Rp '.number_format($transaction->amount)],
        ['lbl'=>'Metode Pembayaran','val'=>$transaction->payment_method ?? '—'],
        ['lbl'=>'Status','val'=>ucfirst($transaction->status)],
        ['lbl'=>'Catatan','val'=>json_decode($transaction->notes, true)['buyer_notes'] ?? '—'],
        ['lbl'=>'IP Address','val'=>$transaction->ip_address ?? '—'],
        ['lbl'=>'Tanggal','val'=>$transaction->created_at->format('d M Y, H:i:s')],
      ] as $row)
      <div style="display:flex;align-items:center;padding:11px 0;border-bottom:1.5px solid var(--line);">
        <div style="width:180px;font-size:12px;color:var(--ink3);font-weight:600;flex-shrink:0;">{{ $row['lbl'] }}</div>
        <div style="font-size:13px;font-weight:700;color:var(--ink);font-family:{{ in_array($row['lbl'],['Order ID','Transaction ID','IP Address'])?'var(--mono)':'var(--font)' }};">{{ $row['val'] }}</div>
      </div>
      @endforeach
    </div>
  </div>

</div>
@endsection