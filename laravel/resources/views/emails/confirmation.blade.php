<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Konfirmasi Pesanan</title>
</head>
<body style="margin:0;padding:0;background:#f4f4f5;font-family:Arial,sans-serif;color:#1a1a1a">
  <div style="max-width:600px;margin:40px auto;background:#ffffff;border-radius:12px;overflow:hidden;box-shadow:0 2px 8px rgba(0,0,0,0.06)">

    {{-- Header --}}
    <div style="background:#2563eb;padding:36px 32px;text-align:center">
      <h1 style="margin:0;color:#ffffff;font-size:22px;font-weight:700">{{ config('app.name') }}</h1>
      <p style="margin:8px 0 0;color:#bfdbfe;font-size:14px">Konfirmasi Pesanan</p>
    </div>

    <div style="padding:32px">
      <p style="margin:0 0 8px;font-size:16px">Halo, <strong>{{ $order->buyer_name }}</strong>!</p>
      <p style="margin:0 0 24px;color:#4b5563;font-size:14px;line-height:1.6">
        Pesanan kamu berhasil kami terima. Kami akan segera memproses dan mengirimkan paketmu.
      </p>

      {{-- Order Info --}}
      <div style="background:#f8fafc;border-radius:8px;padding:20px;margin-bottom:24px">
        <table style="width:100%;border-collapse:collapse">
          <tr>
            <td style="font-size:13px;color:#6b7280;padding:5px 0">Kode Pesanan</td>
            <td style="font-size:13px;font-weight:700;text-align:right;letter-spacing:.5px">{{ $order->order_code }}</td>
          </tr>
          <tr>
            <td style="font-size:13px;color:#6b7280;padding:5px 0">Tanggal</td>
            <td style="font-size:13px;text-align:right">{{ $order->created_at->format('d M Y, H:i') }} WIB</td>
          </tr>
          <tr>
            <td style="font-size:13px;color:#6b7280;padding:5px 0">Status</td>
            <td style="text-align:right">
              <span style="background:#dcfce7;color:#15803d;font-size:12px;font-weight:600;padding:2px 10px;border-radius:99px">Dibayar</span>
            </td>
          </tr>
        </table>
      </div>

      {{-- Detail Produk --}}
      <h3 style="margin:0 0 12px;font-size:13px;color:#374151;text-transform:uppercase;letter-spacing:.5px">Detail Produk</h3>
      <table style="width:100%;border-collapse:collapse;margin-bottom:24px">
        <thead>
          <tr style="border-bottom:2px solid #e5e7eb">
            <th style="text-align:left;font-size:12px;color:#6b7280;padding:8px 0;font-weight:600">Produk</th>
            <th style="text-align:center;font-size:12px;color:#6b7280;padding:8px 0;font-weight:600">Qty</th>
            <th style="text-align:right;font-size:12px;color:#6b7280;padding:8px 0;font-weight:600">Harga</th>
          </tr>
        </thead>
        <tbody>
          <tr style="border-bottom:1px solid #f3f4f6">
            <td style="padding:12px 0;font-size:14px">{{ $order->product_name }}</td>
            <td style="padding:12px 0;font-size:14px;text-align:center">{{ $order->quantity }}</td>
            <td style="padding:12px 0;font-size:14px;text-align:right">Rp {{ number_format($order->product_price * $order->quantity, 0, ',', '.') }}</td>
          </tr>
        </tbody>
        <tfoot>
          @if($order->shipping_cost > 0)
          <tr>
            <td colspan="2" style="padding:10px 0 4px;font-size:13px;color:#6b7280">Ongkos Kirim</td>
            <td style="padding:10px 0 4px;font-size:13px;text-align:right;color:#6b7280">Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</td>
          </tr>
          @endif
          <tr>
            <td colspan="2" style="padding:8px 0 0;font-size:15px;font-weight:700">Total</td>
            <td style="padding:8px 0 0;font-size:15px;font-weight:700;text-align:right;color:#2563eb">{{ $order->formattedTotal() }}</td>
          </tr>
        </tfoot>
      </table>

      {{-- Alamat Pengiriman --}}
      <h3 style="margin:0 0 10px;font-size:13px;color:#374151;text-transform:uppercase;letter-spacing:.5px">Alamat Pengiriman</h3>
      <div style="background:#f8fafc;border-radius:8px;padding:16px;margin-bottom:28px;font-size:14px;line-height:1.8;color:#374151">
        <strong>{{ $order->buyer_name }}</strong><br>
        {{ $order->shipping_address }}<br>
        {{ $order->shipping_city }}, {{ $order->shipping_province }} {{ $order->shipping_postal_code }}<br>
        @if($order->buyer_phone)
          {{ $order->buyer_phone }}
        @endif
      </div>

      <p style="margin:0;font-size:13px;color:#9ca3af;text-align:center;line-height:1.6">
        Ada pertanyaan? Hubungi kami di
        <a href="mailto:{{ config('mail.from.address') }}" style="color:#2563eb">{{ config('mail.from.address') }}</a>
      </p>
    </div>

    {{-- Footer --}}
    <div style="background:#f8fafc;padding:20px 32px;text-align:center;border-top:1px solid #e5e7eb">
      <p style="margin:0;font-size:12px;color:#9ca3af">
        © {{ date('Y') }} {{ config('app.name') }}. Email ini dikirim otomatis, mohon tidak membalas.
      </p>
    </div>

  </div>
</body>
</html>