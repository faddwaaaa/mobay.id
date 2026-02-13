<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - {{ $product->title ?? 'Product' }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: system-ui, -apple-system, sans-serif;
            background: #f9fafb;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .checkout-container {
            max-width: 500px;
            width: 100%;
            background: white;
            border-radius: 16px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        .checkout-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 24px;
            text-align: center;
        }

        .checkout-header h1 {
            font-size: 24px;
            margin-bottom: 8px;
        }

        .checkout-header p {
            opacity: 0.9;
            font-size: 14px;
        }

        .checkout-body {
            padding: 32px 24px;
        }

        .product-info {
            display: flex;
            gap: 16px;
            padding-bottom: 24px;
            border-bottom: 2px solid #f3f4f6;
            margin-bottom: 24px;
        }

        .product-image {
            width: 100px;
            height: 100px;
            border-radius: 12px;
            object-fit: cover;
            background: #f3f4f6;
        }

        .product-details h2 {
            font-size: 18px;
            margin-bottom: 8px;
            color: #111827;
        }

        .product-price {
            font-size: 24px;
            font-weight: 800;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .coming-soon {
            text-align: center;
            padding: 40px 20px;
        }

        .coming-soon-icon {
            font-size: 64px;
            margin-bottom: 16px;
        }

        .coming-soon h3 {
            font-size: 20px;
            color: #111827;
            margin-bottom: 8px;
        }

        .coming-soon p {
            color: #6b7280;
            margin-bottom: 24px;
        }

        .btn-back {
            display: inline-block;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 12px 32px;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
        }

        .btn-back:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(102, 126, 234, 0.4);
        }
    </style>
</head>
<body>
    <div class="checkout-container">
        <div class="checkout-header">
            <h1>🛒 Checkout</h1>
            <p>Halaman Pembayaran</p>
        </div>

        <div class="checkout-body">
            @if(isset($product))
            <div class="product-info">
                @if($product->images && $product->images->count() > 0)
                    <img src="{{ asset('storage/' . $product->images->first()->image) }}" 
                         class="product-image" 
                         alt="{{ $product->title }}">
                @else
                    <div class="product-image"></div>
                @endif
                
                <div class="product-details">
                    <h2>{{ $product->title }}</h2>
                    <div class="product-price">
                        Rp {{ number_format($product->discount ?? $product->price, 0, ',', '.') }}
                    </div>
                </div>
            </div>
            @endif

            <div class="coming-soon">
                <div class="coming-soon-icon">🚧</div>
                <h3>Fitur Pembayaran Sedang Dikembangkan</h3>
                <p>Halaman checkout dan pembayaran akan segera tersedia!</p>
                <a href="javascript:history.back()" class="btn-back">
                    ← Kembali
                </a>
            </div>
        </div>
    </div>
</body>
</html>