<html>
<head>
    <meta charset="utf-8">
    <title>Ekspor Analitik Pro</title>
</head>
<body>
    <table border="1">
        <tr><th colspan="2">Ringkasan Analitik Pro</th></tr>
        <tr><td>Periode</td><td>{{ $analyticsRangeLabel }}</td></tr>
        <tr><td>Total Views Profil</td><td>{{ $totalProfileViews }}</td></tr>
        <tr><td>Total Klik Produk</td><td>{{ $totalClicks }}</td></tr>
        <tr><td>Total Produk Terjual</td><td>{{ $totalSold }}</td></tr>
        <tr><td>Total Pendapatan</td><td>{{ $totalRevenue }}</td></tr>
        <tr><td>Konversi View ke Klik (%)</td><td>{{ $advancedStats['conversion_view_to_click'] ?? 0 }}</td></tr>
        <tr><td>Konversi Klik ke Penjualan (%)</td><td>{{ $advancedStats['conversion_click_to_sale'] ?? 0 }}</td></tr>
        <tr><td>Rata-rata Nilai per Penjualan</td><td>{{ $advancedStats['avg_revenue_per_sale'] ?? 0 }}</td></tr>
        <tr><td>Pendapatan per Klik</td><td>{{ $advancedStats['revenue_per_click'] ?? 0 }}</td></tr>
        <tr><td>Hari Trafik Tertinggi</td><td>{{ $advancedStats['best_traffic_label'] ?? '-' }} ({{ $advancedStats['best_traffic_total'] ?? 0 }})</td></tr>
        <tr><td>Hari Pendapatan Tertinggi</td><td>{{ $advancedStats['best_revenue_label'] ?? '-' }} ({{ $advancedStats['best_revenue_amount'] ?? 0 }})</td></tr>
        <tr><td>Hari Penjualan Aktif</td><td>{{ $advancedStats['active_sales_days'] ?? 0 }}</td></tr>
        <tr><td>Hari Trafik Aktif</td><td>{{ $advancedStats['active_traffic_days'] ?? 0 }}</td></tr>
    </table>

    <br>

    <table border="1">
        <tr>
            <th colspan="4">Top Produk Berdasarkan Penjualan</th>
        </tr>
        <tr>
            <th>Produk</th>
            <th>Terjual</th>
            <th>Dilihat</th>
            <th>Pendapatan</th>
        </tr>
        @foreach($topProducts as $product)
            <tr>
                <td>{{ $product->title }}</td>
                <td>{{ $product->sold ?? 0 }}</td>
                <td>{{ $product->views_count ?? 0 }}</td>
                <td>{{ $product->revenue ?? 0 }}</td>
            </tr>
        @endforeach
    </table>

    <br>

    <table border="1">
        <tr>
            <th colspan="4">Top Produk Berdasarkan Dilihat</th>
        </tr>
        <tr>
            <th>Produk</th>
            <th>Dilihat</th>
            <th>Terjual</th>
            <th>Pendapatan</th>
        </tr>
        @foreach($topViewedProducts as $product)
            <tr>
                <td>{{ $product->title }}</td>
                <td>{{ $product->views_count ?? 0 }}</td>
                <td>{{ $product->sold ?? 0 }}</td>
                <td>{{ $product->revenue ?? 0 }}</td>
            </tr>
        @endforeach
    </table>

    <br>

    <table border="1">
        <tr>
            <th colspan="4">Performa Harian</th>
        </tr>
        <tr>
            <th>Tanggal</th>
            <th>Views Profil</th>
            <th>Klik Produk</th>
            <th>Pendapatan</th>
        </tr>
        @foreach($clicksPerDay as $day)
            <tr>
                <td>{{ $day['full_date'] }}</td>
                <td>{{ $day['views'] }}</td>
                <td>{{ $day['clicks'] }}</td>
                <td>{{ $day['sales'] }}</td>
            </tr>
        @endforeach
    </table>
</body>
</html>
