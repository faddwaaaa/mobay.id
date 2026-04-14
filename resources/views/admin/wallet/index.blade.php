@extends('admin.layouts.app')
@section('page-title', 'Dompet Admin')

@section('content')
<div style="display:flex;flex-direction:column;gap:18px;">
  <div>
    <div style="font-size:18px;font-weight:900;color:var(--ink);letter-spacing:-.3px;">Dompet Admin</div>
    <div style="font-size:12px;color:var(--ink3);font-weight:600;margin-top:2px;">
      Saldo untuk menampung fee platform (fee payment dan fee withdraw).
    </div>
  </div>

  <div class="card" style="padding:24px; background:linear-gradient(135deg,#1d4ed8 0%, #2563eb 45%, #38bdf8 100%); border:0; box-shadow:0 14px 30px rgba(37,99,235,.28);">
    <div style="display:flex;align-items:center;justify-content:space-between;gap:12px;">
      <div>
        <div style="display:inline-flex;align-items:center;gap:8px;padding:6px 10px;border-radius:999px;background:rgba(255,255,255,.16);color:#dbeafe;font-size:11px;font-weight:800;">
          SALDO SAAT INI
        </div>
        <div style="margin-top:12px;font-size:40px;font-weight:900;color:#ffffff;letter-spacing:-1px;line-height:1.08;">
          Rp {{ number_format($stats['balance'], 0, ',', '.') }}
        </div>
        <div style="margin-top:8px;font-size:12px;font-weight:600;color:rgba(255,255,255,.9);">
          Akumulasi dana dompet admin dari seluruh mutasi fee platform.
        </div>
        <div style="margin-top:12px;display:flex;gap:16px;flex-wrap:wrap;">
          <div style="font-size:12px;color:#e0ecff;font-weight:700;">Siap ditarik: Rp {{ number_format($stats['available_balance'], 0, ',', '.') }}</div>
          <div style="font-size:12px;color:#e0ecff;font-weight:700;">Pending: Rp {{ number_format($stats['pending_withdrawal_amount'], 0, ',', '.') }}</div>
        </div>
        <a href="#admin-withdraw-form" class="btn" style="margin-top:14px;background:#ffffff;color:#1d4ed8;font-weight:900;">
          Tarik Dana Admin
        </a>
      </div>
      <div style="width:72px;height:72px;border-radius:18px;background:rgba(255,255,255,.2);display:flex;align-items:center;justify-content:center;color:#ffffff;font-size:28px;font-weight:900;">
        Rp
      </div>
    </div>
  </div>

  <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:14px;">
    @foreach([
      ['lbl'=>'Total Fee Masuk','val'=>'Rp '.number_format($stats['total_credit'], 0, ',', '.'),'ico'=>'+','bg'=>'#dcfce7','vc'=>'#15803d'],
      ['lbl'=>'Total Keluar','val'=>'Rp '.number_format($stats['total_debit'], 0, ',', '.'),'ico'=>'-','bg'=>'#fee2e2','vc'=>'#b91c1c'],
      ['lbl'=>'Total Mutasi','val'=>number_format($stats['total_entries'], 0, ',', '.'),'ico'=>'#','bg'=>'#f1f5f9','vc'=>'#334155'],
    ] as $s)
      <div class="card" style="padding:18px 20px;">
        <div style="display:flex;align-items:center;gap:10px;margin-bottom:10px;">
          <div style="width:36px;height:36px;border-radius:10px;background:{{ $s['bg'] }};display:flex;align-items:center;justify-content:center;font-size:15px;font-weight:900;color:{{ $s['vc'] }};">
            {{ $s['ico'] }}
          </div>
          <div style="font-size:11.5px;color:var(--ink3);font-weight:600;">{{ $s['lbl'] }}</div>
        </div>
        <div style="font-size:22px;font-weight:900;color:{{ $s['vc'] }};letter-spacing:-.5px;">{{ $s['val'] }}</div>
      </div>
    @endforeach
  </div>

  <div class="card" id="admin-withdraw-form" style="padding:18px 20px;">
    <div style="font-size:15px;font-weight:900;color:var(--ink);">Penarikan Dompet Admin (Midtrans)</div>
    <div style="font-size:12px;color:var(--ink3);font-weight:600;margin-top:3px;">
      Keamanan: validasi PIN 6 digit + password admin. Proses payout langsung ke Midtrans.
    </div>

    <form method="POST" action="{{ route('admin.wallet.withdraw') }}" style="margin-top:14px;display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:10px;">
      @csrf
      <div>
        <label style="display:block;font-size:11.5px;font-weight:700;color:var(--ink3);margin-bottom:6px;">Bank</label>
        <select name="bank_name" required style="width:100%;padding:9px 10px;border-radius:10px;border:1.5px solid var(--line);background:var(--bg);font-family:var(--font);font-size:12px;color:var(--ink);">
          <option value="">Pilih bank</option>
          @foreach($banks as $bank)
            <option value="{{ $bank }}" {{ old('bank_name') === $bank ? 'selected' : '' }}>{{ $bank }}</option>
          @endforeach
        </select>
      </div>
      <div>
        <label style="display:block;font-size:11.5px;font-weight:700;color:var(--ink3);margin-bottom:6px;">Nominal (Rp)</label>
        <input type="number" name="amount" min="10000" required value="{{ old('amount') }}" style="width:100%;padding:9px 10px;border-radius:10px;border:1.5px solid var(--line);background:#fff;font-family:var(--font);font-size:12px;color:var(--ink);">
      </div>
      <div>
        <label style="display:block;font-size:11.5px;font-weight:700;color:var(--ink3);margin-bottom:6px;">Nama Rekening</label>
        <input type="text" name="account_name" required value="{{ old('account_name') }}" style="width:100%;padding:9px 10px;border-radius:10px;border:1.5px solid var(--line);background:#fff;font-family:var(--font);font-size:12px;color:var(--ink);">
      </div>
      <div>
        <label style="display:block;font-size:11.5px;font-weight:700;color:var(--ink3);margin-bottom:6px;">Nomor Rekening</label>
        <input type="text" name="account_number" required value="{{ old('account_number') }}" style="width:100%;padding:9px 10px;border-radius:10px;border:1.5px solid var(--line);background:#fff;font-family:var(--font);font-size:12px;color:var(--ink);">
      </div>
      <div>
        <label style="display:block;font-size:11.5px;font-weight:700;color:var(--ink3);margin-bottom:6px;">PIN Admin (6 digit)</label>
        <input type="password" name="pin" inputmode="numeric" maxlength="6" required style="width:100%;padding:9px 10px;border-radius:10px;border:1.5px solid var(--line);background:#fff;font-family:var(--font);font-size:12px;color:var(--ink);">
      </div>
      <div>
        <label style="display:block;font-size:11.5px;font-weight:700;color:var(--ink3);margin-bottom:6px;">Password Admin</label>
        <input type="password" name="password" required style="width:100%;padding:9px 10px;border-radius:10px;border:1.5px solid var(--line);background:#fff;font-family:var(--font);font-size:12px;color:var(--ink);">
      </div>
      <div style="grid-column:1 / -1;">
        <label style="display:block;font-size:11.5px;font-weight:700;color:var(--ink3);margin-bottom:6px;">Catatan (opsional)</label>
        <textarea name="notes" rows="2" style="width:100%;padding:9px 10px;border-radius:10px;border:1.5px solid var(--line);background:#fff;font-family:var(--font);font-size:12px;color:var(--ink);resize:vertical;">{{ old('notes') }}</textarea>
      </div>
      <div style="grid-column:1 / -1;display:flex;justify-content:flex-end;">
        <button type="submit" class="btn btn-primary" onclick="return confirm('Proses penarikan dompet admin sekarang?')">Tarik Dana Sekarang</button>
      </div>
    </form>

    @if($errors->any())
      <div class="alert alert-error" style="margin-top:12px;">
        {{ $errors->first() }}
      </div>
    @endif
  </div>

  <div class="card">
    <div class="card-head">
      <div>
        <div class="card-title">Riwayat Mutasi Dompet</div>
        <div class="card-sub">Mutasi fee payment dan fee withdraw akan tercatat otomatis di sini.</div>
      </div>
    </div>

    <table>
      <thead>
      <tr>
        <th>Tanggal</th>
        <th>Sumber</th>
        <th>Tipe</th>
        <th>Nominal</th>
        <th>Saldo Akhir</th>
        <th>Keterangan</th>
      </tr>
      </thead>
      <tbody>
      @forelse($entries as $entry)
        <tr>
          <td class="num">{{ $entry->created_at->format('d M Y, H:i') }}</td>
          <td>
            @if($entry->source === 'fee_payment')
              <span class="badge b-ok">Fee Payment</span>
            @elseif($entry->source === 'fee_withdraw')
              <span class="badge b-pnd">Fee Withdraw</span>
            @else
              <span class="badge b-adm">Manual</span>
            @endif
          </td>
          <td>
            @if($entry->direction === 'credit')
              <span class="badge b-ok">Masuk</span>
            @else
              <span class="badge b-off">Keluar</span>
            @endif
          </td>
          <td style="font-size:13px;font-weight:900;color:var(--ink);font-family:var(--mono);">
            {{ $entry->direction === 'credit' ? '+' : '-' }} Rp {{ number_format($entry->amount, 0, ',', '.') }}
          </td>
          <td style="font-size:13px;font-weight:900;color:var(--ink);font-family:var(--mono);">
            Rp {{ number_format($entry->balance_after, 0, ',', '.') }}
          </td>
          <td style="font-size:12px;color:var(--ink2);">
            {{ $entry->description ?: '-' }}
          </td>
        </tr>
      @empty
        <tr>
          <td colspan="6" style="text-align:center;padding:30px;color:var(--ink4);">
            Belum ada mutasi dompet admin.
          </td>
        </tr>
      @endforelse
      </tbody>
    </table>

    @if($entries->hasPages())
      <div style="padding:14px 18px;border-top:1.5px solid var(--line);display:flex;justify-content:space-between;align-items:center;">
        <div style="font-size:12px;color:var(--ink3);font-weight:600;">
          Menampilkan {{ $entries->firstItem() }}-{{ $entries->lastItem() }} dari {{ $entries->total() }}
        </div>
        <div style="display:flex;gap:4px;">
          @if($entries->onFirstPage())
            <span class="btn btn-ghost" style="padding:5px 10px;font-size:12px;opacity:.4;cursor:default;">Prev</span>
          @else
            <a href="{{ $entries->previousPageUrl() }}" class="btn btn-ghost" style="padding:5px 10px;font-size:12px;">Prev</a>
          @endif
          @if($entries->hasMorePages())
            <a href="{{ $entries->nextPageUrl() }}" class="btn btn-primary" style="padding:5px 10px;font-size:12px;">Next</a>
          @else
            <span class="btn btn-primary" style="padding:5px 10px;font-size:12px;opacity:.4;cursor:default;">Next</span>
          @endif
        </div>
      </div>
    @endif
  </div>

  <div class="card">
    <div class="card-head">
      <div>
        <div class="card-title">Riwayat Penarikan Admin</div>
        <div class="card-sub">Log request withdrawal dompet admin melalui Midtrans IRIS.</div>
      </div>
    </div>
    <table>
      <thead>
      <tr>
        <th>Tanggal</th>
        <th>Nominal</th>
        <th>Rekening Tujuan</th>
        <th>Status</th>
        <th>Payout ID</th>
        <th>Diproses Oleh</th>
      </tr>
      </thead>
      <tbody>
      @forelse($withdrawals as $withdrawal)
        <tr>
          <td class="num">{{ $withdrawal->created_at->format('d M Y, H:i') }}</td>
          <td style="font-size:13px;font-weight:900;color:var(--ink);font-family:var(--mono);">
            Rp {{ number_format($withdrawal->amount, 0, ',', '.') }}
          </td>
          <td>
            <div style="font-size:12px;font-weight:800;color:var(--ink);">{{ $withdrawal->bank_name }}</div>
            <div class="num">{{ $withdrawal->account_number }} a.n {{ $withdrawal->account_name }}</div>
          </td>
          <td>
            @if($withdrawal->status === 'completed')
              <span class="badge b-ok">Selesai</span>
            @elseif($withdrawal->status === 'approved')
              <span class="badge b-adm">Diproses</span>
            @elseif($withdrawal->status === 'pending')
              <span class="badge b-pnd">Pending</span>
            @else
              <span class="badge b-off">Ditolak</span>
            @endif
          </td>
          <td class="num">{{ $withdrawal->payout_id ?: '-' }}</td>
          <td style="font-size:12px;color:var(--ink2);">{{ $withdrawal->processor?->name ?: '-' }}</td>
        </tr>
      @empty
        <tr>
          <td colspan="6" style="text-align:center;padding:24px;color:var(--ink4);">Belum ada riwayat penarikan admin.</td>
        </tr>
      @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection
