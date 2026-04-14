<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Pesanan Dikirim</title>
</head>
<body style="margin:0;padding:0;background:#f4f4f5;font-family:Arial,sans-serif;color:#1a1a1a">
  <div style="max-width:600px;margin:40px auto;background:#ffffff;border-radius:12px;overflow:hidden;box-shadow:0 2px 8px rgba(0,0,0,0.06)">

    <div style="background:#16a34a;padding:36px 32px;text-align:center">
      <div style="font-size:48px;margin-bottom:8px">🚚</div>
      <h1 style="margin:0;color:#ffffff;font-size:22px;font-weight:700">Pesanan Sedang Dikirim!</h1>
      <p style="margin:8px 0 0;color:#bbf7d0;font-size:14px">{{ config('app.name') }}</p>
    </div>

    <div style="padding:32px">
      <p style="margin:0 0 8px;font-size:16px">Halo, <strong>{{ $order->buyer_name }}</strong>!</p>
      <p style="margin:0 0 28px;color:#4b5563;font-size:14px;line-height:1.6">
        Pesanan kamu sudah dalam perjalanan. Gunakan nomor resi di bawah untuk melacak paketmu.
      </p>

      {{-- Tracking Info --}}
      <div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:10px;padding:24px;margin-bottom:28px;text-align:center">
        <p style="margin:0 0 6px;font-size:12px;color:#15803d;text-transform:uppercase;letter-spacing:.5px;font-weight:600">Nomor Resi</p>
        <p style="margin:0 0 16px;font-size:26px;font-weight:700;letter-spacing:2px;color:#14532d">{{ $order->tracking_number }}</p>
        <p style="margin:0 0 4px;font-size:13px;color:#6b7280">Kurir</p>
        <p style="margin:0 0 20px;font-size:15px;font-weight:600;color:#374151">
          {{ strtoupper($order->courier_code) }}
          @if($order->courier_service) — {{ $order->courier_service }} @endif
        </p>
        <a href="{{ $order->tracking_url ?? 'https://biteship.com/id/track/' . $order->tracking_number }}"
           style="display:inline-block;background:#16a34a;color:#ffffff;text-decoration:none;padding:11px 28px;border-radius:8px;font-size:14px;font-weight:600">
          Lacak Paket
        </a>
      </div>

      {{-- Order Summary --}}
      <div style="background:#f8fafc;border-radius:8px;padding:20px;margin-bottom:24px">
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
            <td style="font-size:13px;color:#6b7280;padding:5px 0">Tanggal Kirim</td>
            <td style="font-size:13px;text-align:right">{{ $order->shipped_at->format('d M Y, H:i') }} WIB</td>
          </tr>
          @if($order->estimated_arrival)
          <tr>
            <td style="font-size:13px;color:#6b7280;padding:5px 0">Estimasi Tiba</td>
            <td style="font-size:13px;font-weight:600;text-align:right;color:#16a34a">{{ $order->estimated_arrival }}</td>
          </tr>
          @endif
        </table>
      </div>

      {{-- Alamat --}}
      <h3 style="margin:0 0 10px;font-size:13px;color:#374151;text-transform:uppercase;letter-spacing:.5px">Dikirim ke</h3>
      <div style="font-size:14px;line-height:1.8;color:#374151;margin-bottom:28px">
        <strong>{{ $order->buyer_name }}</strong><br>
        {{ $order->shipping_address }}<br>
        {{ $order->shipping_city }}, {{ $order->shipping_province }} {{ $order->shipping_postal_code }}
      </div>

      <p style="margin:0;font-size:13px;color:#9ca3af;text-align:center">
        Ada pertanyaan? Hubungi kami di
        <a href="mailto:{{ config('mail.from.address') }}" style="color:#16a34a">{{ config('mail.from.address') }}</a>
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