<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Update Status Pesanan</title>
</head>
<body style="margin:0;padding:0;background:#f4f4f5;font-family:Arial,sans-serif;color:#1a1a1a">
  <div style="max-width:600px;margin:40px auto;background:#ffffff;border-radius:12px;overflow:hidden;box-shadow:0 2px 8px rgba(0,0,0,0.06)">

    @php
      $headerColor = match($biteshipStatus) {
        'delivered'                      => '#2563eb',
        'in_transit', 'dropping_off'     => '#d97706',
        'picked_up', 'allocated'         => '#7c3aed',
        'return_in_transit', 'returned'  => '#dc2626',
        default                          => '#374151',
      };
      $emoji = match($biteshipStatus) {
        'delivered'                      => '✅',
        'in_transit', 'dropping_off'     => '📦',
        'picked_up', 'allocated'         => '🏭',
        'return_in_transit', 'returned'  => '↩️',
        default                          => '🔔',
      };
    @endphp

    <div style="background:{{ $headerColor }};padding:36px 32px;text-align:center">
      <div style="font-size:48px;margin-bottom:8px">{{ $emoji }}</div>
      <h1 style="margin:0;color:#ffffff;font-size:20px;font-weight:700">{{ $statusLabel }}</h1>
      <p style="margin:8px 0 0;color:rgba(255,255,255,0.75);font-size:14px">{{ $order->order_code }}</p>
    </div>

    <div style="padding:32px">
      <p style="margin:0 0 8px;font-size:16px">Halo, <strong>{{ $order->buyer_name }}</strong>!</p>
      <p style="margin:0 0 28px;color:#4b5563;font-size:14px;line-height:1.6">
        @if($biteshipStatus === 'delivered')
          Paket kamu sudah berhasil diterima. Terima kasih sudah berbelanja di {{ config('app.name') }}! 🎉
        @elseif(in_array($biteshipStatus, ['in_transit', 'dropping_off']))
          Paket kamu sedang dalam perjalanan. Harap bersiap menerima paket!
        @elseif(in_array($biteshipStatus, ['picked_up', 'allocated']))
          Paket kamu sudah dijemput oleh kurir dan sedang diproses.
        @elseif(in_array($biteshipStatus, ['return_in_transit', 'returned']))
          Paket kamu sedang dalam proses retur. Tim kami akan menghubungimu segera.
        @else
          Status pesanan kamu telah diperbarui.
        @endif
      </p>

      {{-- Tracking --}}
      @if($order->tracking_number)
      <div style="background:#f8fafc;border-radius:8px;padding:20px;margin-bottom:24px;text-align:center">
        <p style="margin:0 0 4px;font-size:12px;color:#6b7280;text-transform:uppercase;letter-spacing:.5px">Nomor Resi</p>
        <p style="margin:0 0 14px;font-size:20px;font-weight:700;letter-spacing:1px">{{ $order->tracking_number }}</p>
        <a href="{{ $order->tracking_url ?? 'https://biteship.com/id/track/' . $order->tracking_number }}"
           style="display:inline-block;background:{{ $headerColor }};color:#ffffff;text-decoration:none;padding:10px 24px;border-radius:8px;font-size:14px;font-weight:600">
          Lacak Paket
        </a>
      </div>
      @endif

      {{-- Order info --}}
      <div style="border:1px solid #e5e7eb;border-radius:8px;padding:16px;margin-bottom:28px">
        <table style="width:100%;border-collapse:collapse">
          <tr>
            <td style="font-size:13px;color:#6b7280;padding:5px 0">Kode Pesanan</td>
            <td style="font-size:13px;font-weight:700;text-align:right">{{ $order->order_code }}</td>
          </tr>
          <tr>
            <td style="font-size:13px;color:#6b7280;padding:5px 0">Produk</td>
            <td style="font-size:13px;text-align:right">{{ $order->product_name }}</td>
          </tr>
          <tr>
            <td style="font-size:13px;color:#6b7280;padding:5px 0">Kurir</td>
            <td style="font-size:13px;text-align:right">{{ strtoupper($order->courier_code) }}</td>
          </tr>
          <tr>
            <td style="font-size:13px;color:#6b7280;padding:5px 0">Status</td>
            <td style="text-align:right">
              <span style="font-size:12px;font-weight:600;padding:2px 10px;border-radius:99px;background:{{ $headerColor }}22;color:{{ $headerColor }}">
                {{ $statusLabel }}
              </span>
            </td>
          </tr>
        </table>
      </div>

      <p style="margin:0;font-size:13px;color:#9ca3af;text-align:center">
        Ada pertanyaan? Hubungi kami di
        <a href="mailto:{{ config('mail.from.address') }}" style="color:{{ $headerColor }}">{{ config('mail.from.address') }}</a>
      </p>
    </div>

    <div style="background:#f8fafc;padding:20px 32px;text-align:center;border-top:1px solid #e5e7eb">
      <p style="margin:0;font-size:12px;color:#9ca3af">
        © {{ date('Y') }} {{ config('app.name') }}. Email ini dikirim otomatis, mohon tidak membalas.
      </p>
    </div>

  </div>
</body>
</html>