<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Download - Mobay.id</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        :root {
            --brand: #2563eb;
            --brand-light: #eff6ff;
            --ok: #16a34a;
            --ok-light: #f0fdf4;
            --danger: #dc2626;
            --danger-light: #fef2f2;
            --ink: #0f172a;
            --muted: #64748b;
            --line: #e2e8f0;
            --radius: 20px;
        }
        body {
            font-family: 'Nunito', sans-serif;
            background: linear-gradient(135deg, #dbeafe 0%, #f1f5f9 50%, #ede9fe 100%);
            min-height: 100vh;
            display: flex; align-items: center; justify-content: center;
            padding: 24px 16px;
            color: var(--ink);
        }
        .container { width: 100%; max-width: 480px; }

        /* LOGO */
        .logo-bar { text-align: center; margin-bottom: 20px; }

        /* CARD */
        .card {
            background: #fff;
            border-radius: var(--radius);
            box-shadow: 0 20px 60px rgba(15,23,42,.12), 0 4px 16px rgba(15,23,42,.06);
            overflow: hidden;
        }

        /* CARD HEADER */
        .card-header {
            background: linear-gradient(135deg, #1d4ed8, #2563eb, #3b82f6);
            padding: 28px 28px 24px; text-align: center;
            position: relative; overflow: hidden;
        }
        .card-header::before {
            content: ''; position: absolute;
            width: 200px; height: 200px; border-radius: 50%;
            background: rgba(255,255,255,.08); top: -80px; right: -60px;
        }
        .card-header::after {
            content: ''; position: absolute;
            width: 120px; height: 120px; border-radius: 50%;
            background: rgba(255,255,255,.06); bottom: -40px; left: -30px;
        }
        .icon-wrap {
            width: 64px; height: 64px;
            background: rgba(255,255,255,.2); border-radius: 18px;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 14px; position: relative; z-index: 1;
        }
        .icon-wrap svg { width: 32px; height: 32px; }
        .card-header h1 { font-size: 22px; font-weight: 900; color: #fff; margin-bottom: 6px; position: relative; z-index: 1; }
        .card-header p { font-size: 14px; color: rgba(255,255,255,.85); position: relative; z-index: 1; }
        .card-header strong { color: #fff; }

        /* CARD BODY */
        .card-body { padding: 24px 28px; }

        /* INFO BOX */
        .info-box {
            background: var(--brand-light); border: 1.5px solid #bfdbfe;
            border-radius: 14px; padding: 14px 16px; margin-bottom: 20px;
        }
        .info-row { display: flex; justify-content: space-between; align-items: center; font-size: 14px; padding: 4px 0; }
        .info-row .k { color: var(--muted); }
        .info-row .v { font-weight: 800; color: var(--brand); }

        /* ERROR BOX */
        .error-box {
            background: var(--danger-light); border: 1.5px solid #fecaca;
            border-radius: 12px; padding: 12px 16px; margin-bottom: 20px;
            font-size: 14px; color: var(--danger); font-weight: 700;
            display: flex; align-items: center; gap: 8px;
        }
        .error-box svg { flex-shrink: 0; width: 16px; height: 16px; }

        /* FORM */
        label { display: block; font-size: 14px; font-weight: 800; color: #374151; margin-bottom: 8px; }
        input[type="email"] {
            width: 100%; padding: 14px 16px;
            border: 2px solid var(--line); border-radius: 12px;
            font-size: 15px; font-family: 'Nunito', sans-serif;
            color: var(--ink); outline: none;
            transition: border-color .2s, box-shadow .2s; margin-bottom: 6px;
        }
        input[type="email"]:focus { border-color: var(--brand); box-shadow: 0 0 0 4px rgba(37,99,235,.1); }
        .hint { font-size: 13px; color: var(--muted); margin-bottom: 20px; line-height: 1.5; }

        /* BTN DOWNLOAD */
        .btn-download {
            width: 100%;
            background: linear-gradient(135deg, #1d4ed8, #2563eb);
            color: #fff; border: none; border-radius: 14px; padding: 15px;
            font-size: 16px; font-weight: 900; font-family: 'Nunito', sans-serif;
            cursor: pointer; transition: all .2s;
            box-shadow: 0 8px 24px rgba(37,99,235,.3);
            display: flex; align-items: center; justify-content: center; gap: 8px;
        }
        .btn-download svg { width: 20px; height: 20px; }
        .btn-download:hover { transform: translateY(-1px); box-shadow: 0 12px 32px rgba(37,99,235,.35); }
        .btn-download:active { transform: translateY(0); }

        /* DIVIDER */
        .divider { display: flex; align-items: center; gap: 12px; margin: 22px 0; }
        .divider::before, .divider::after { content: ''; flex: 1; height: 1px; background: var(--line); }
        .divider span { font-size: 12px; color: var(--muted); font-weight: 700; white-space: nowrap; }

        /* TOGGLE MASALAH */
        .masalah-toggle {
            width: 100%;
            background: var(--danger-light); border: 1.5px solid #fecaca;
            border-radius: 14px; padding: 13px 16px;
            font-size: 14px; font-weight: 800; font-family: 'Nunito', sans-serif;
            color: var(--danger); cursor: pointer; transition: all .2s;
            display: flex; align-items: center; justify-content: center; gap: 8px;
        }
        .masalah-toggle svg { width: 16px; height: 16px; }
        .masalah-toggle:hover { background: #fee2e2; }

        /* MASALAH FORM */
        .masalah-form {
            display: none; margin-top: 16px;
            border: 1.5px solid #fecaca; border-radius: 16px; padding: 20px;
            animation: slideDown .25s ease;
        }
        .masalah-form.show { display: block; }
        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-8px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .masalah-form h3 { font-size: 15px; font-weight: 900; color: var(--ink); margin-bottom: 16px; display: flex; align-items: center; gap: 8px; }
        .masalah-form h3 svg { width: 18px; height: 18px; }
        .form-group { margin-bottom: 14px; }
        .form-group label { font-size: 13px; margin-bottom: 6px; }
        .form-group input[type="text"],
        .form-group input[type="email"],
        .form-group textarea {
            width: 100%; padding: 11px 14px;
            border: 1.5px solid var(--line); border-radius: 10px;
            font-size: 14px; font-family: 'Nunito', sans-serif;
            color: var(--ink); outline: none;
            transition: border-color .2s; resize: vertical;
        }
        .form-group input:focus,
        .form-group textarea:focus { border-color: var(--danger); box-shadow: 0 0 0 3px rgba(220,38,38,.08); }
        .form-group textarea { min-height: 90px; }

        /* BTN WA */
        .btn-wa {
            width: 100%; background: #16a34a; color: #fff;
            border: none; border-radius: 12px; padding: 12px;
            font-size: 14px; font-weight: 900; font-family: 'Nunito', sans-serif;
            cursor: pointer; transition: background .2s;
            display: flex; align-items: center; justify-content: center; gap: 8px;
        }
        .btn-wa svg { width: 18px; height: 18px; }
        .btn-wa:hover { background: #15803d; }

        /* FOOTER */
        .footer { text-align: center; margin-top: 18px; font-size: 12px; color: var(--muted); }
        .footer a { color: var(--brand); text-decoration: none; font-weight: 700; }

        @keyframes spin { to { transform: rotate(360deg); } }
    </style>
</head>
<body>
<div class="container">

    <div class="logo-bar">
        <img src="{{ asset('img/logo.png') }}" alt="Mobay.id" style="height:40px;">
    </div>

    <div class="card">

        <div class="card-header">
            <div class="icon-wrap">
                <svg fill="none" stroke="#fff" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
            </div>
            <h1>Download File</h1>
            <p>Produk: <strong>{{ $product }}</strong></p>
        </div>

        <div class="card-body">

            <div class="info-box">
                <div class="info-row">
                    <span class="k">Sisa download</span>
                    <span class="v">{{ $remaining }}x lagi</span>
                </div>
                <div class="info-row">
                    <span class="k">Link berlaku hingga</span>
                    <span class="v">{{ $expires_at }}</span>
                </div>
            </div>

            @if($errors->any())
                <div class="error-box">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                    </svg>
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('download.verify', $token) }}">
                @csrf
                <label>Masukkan email pembelian kamu</label>
                <input type="email" name="email" required
                    placeholder="email@kamu.com"
                    value="{{ old('email') }}"/>
                <div class="hint">Email harus sama dengan yang digunakan saat pembelian.</div>

                <button type="submit" class="btn-download">
                    <svg fill="none" stroke="#fff" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    Verifikasi &amp; Download
                </button>
            </form>

            <div class="divider">
                <span>Ada masalah dengan file?</span>
            </div>

            <button class="masalah-toggle" onclick="toggleMasalah()" id="toggleBtn">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                </svg>
                Laporkan Masalah ke CS
            </button>

            <div class="masalah-form" id="masalahForm">
                <h3>
                    <svg fill="none" stroke="var(--danger)" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                    Kirim Laporan ke Customer Service
                </h3>

                <div class="form-group">
                    <label>Nama kamu <span style="color:var(--danger)">*</span></label>
                    <input type="text" id="reportName" placeholder="Nama lengkap kamu"/>
                </div>
                <div class="form-group">
                    <label>Email kamu <span style="color:var(--danger)">*</span></label>
                    <input type="email" id="reportEmail" placeholder="email@kamu.com"/>
                </div>
                <div class="form-group">
                    <label>Nomor Order</label>
                    <input type="text" id="reportOrder" placeholder="Contoh: PAYOU-XXXXXXXX"/>
                </div>
                <div class="form-group">
                    <label>Deskripsi masalah <span style="color:var(--danger)">*</span></label>
                    <textarea id="reportDesc" placeholder="Ceritakan masalah yang kamu alami, misalnya: file tidak bisa dibuka, file kosong, link error, dll..."></textarea>
                </div>

                <button class="btn-wa" id="btnReport" onclick="kirimLaporan()">
                    <svg fill="currentColor" viewBox="0 0 24 24">
                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/>
                        <path d="M12 0C5.373 0 0 5.373 0 12c0 2.123.554 4.117 1.528 5.845L.057 23.547a.75.75 0 00.921.921l5.702-1.471A11.95 11.95 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 22c-1.907 0-3.7-.505-5.25-1.385l-.374-.217-3.884 1.002 1.002-3.884-.217-.374A9.953 9.953 0 012 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10z"/>
                    </svg>
                    Chat CS via WhatsApp
                </button>
            </div>

        </div>
    </div>

    <div class="footer">
        Dilindungi oleh <a href="/">Mobay.id</a> &middot; Transaksi aman &amp; terenkripsi
    </div>

</div>
<script>
function toggleMasalah() {
    const form = document.getElementById('masalahForm');
    const btn  = document.getElementById('toggleBtn');
    form.classList.toggle('show');
    if (form.classList.contains('show')) {
        btn.innerHTML = `<svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="16" height="16"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg> Tutup Form Laporan`;
    } else {
        btn.innerHTML = `<svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="16" height="16"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg> Laporkan Masalah ke CS`;
    }
}

function kirimLaporan() {
    const name  = document.getElementById('reportName').value.trim();
    const email = document.getElementById('reportEmail').value.trim();
    const order = document.getElementById('reportOrder').value.trim();
    const desc  = document.getElementById('reportDesc').value.trim();

    if (!name || !email || !desc) {
        alert('Nama, email, dan deskripsi masalah wajib diisi!');
        return;
    }

    const text = encodeURIComponent(
        'Halo CS Mobay.id, saya ingin melaporkan masalah:\n\n' +
        'Nama: ' + name + '\n' +
        'Email: ' + email + '\n' +
        'Order: ' + (order || '-') + '\n' +
        'Produk: {{ $product }}' + '\n\n' +
        'Masalah:\n' + desc
    );

    window.open('https://wa.me/6285185025484?text=' + text, '_blank');
}
</script>
</body>
</html>
