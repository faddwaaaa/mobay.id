{{--
    Xendit Payment Modal Component
    ================================
    Usage in any Blade view:
        <x-payment-modal :amount="150000" :order-id="$order->id" />
    
    Or include directly:
        @include('components.payment-modal', ['amount' => 150000, 'orderId' => $order->id])
--}}

@props([
    'amount'  => 0,
    'orderId' => null,
])

{{-- =====================================================================
     MODAL OVERLAY
     ===================================================================== --}}
<div id="xendit-payment-modal"
     style="display:none; position:fixed; inset:0; z-index:9999;
            background:rgba(10,10,10,0.6); backdrop-filter:blur(4px);
            align-items:center; justify-content:center; padding:1rem;">

    {{-- Modal shell --}}
    <div id="xpm-shell"
         style="background:#fff; border-radius:16px; width:100%; max-width:480px;
                box-shadow:0 32px 64px rgba(0,0,0,0.18); overflow:hidden;
                font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif;
                position:relative; max-height:90vh; display:flex; flex-direction:column;">

        {{-- ── SCREEN 1: Select payment method ── --}}
        <div id="xpm-screen-select" style="flex:1; overflow-y:auto; display:flex; flex-direction:column;">

            {{-- Header --}}
            <div style="padding:1.25rem 1.5rem 1rem; border-bottom:1px solid #f0f0f0; flex-shrink:0;">
                <div style="display:flex; align-items:flex-start; justify-content:space-between;">
                    <div>
                        <p style="margin:0 0 2px; font-size:11px; font-weight:600; letter-spacing:.08em; color:#999; text-transform:uppercase;">Pilih Metode Pembayaran</p>
                        <p style="margin:0; font-size:20px; font-weight:700; color:#111; letter-spacing:-.3px;">
                            Rp <span id="xpm-display-amount">0</span>
                        </p>
                    </div>
                    <div style="display:flex; align-items:center; gap:8px;">
                        <span style="font-size:10px; font-weight:600; letter-spacing:.05em; color:#aaa; background:#f5f5f5; padding:3px 9px; border-radius:4px; border:1px solid #eee;">XENDIT</span>
                        <button onclick="closeXenditModal()"
                                style="width:30px;height:30px;border-radius:50%;border:1px solid #e8e8e8;
                                       background:transparent;cursor:pointer;display:flex;align-items:center;
                                       justify-content:center;color:#777;font-size:14px;"
                                title="Tutup">
                            <svg width="12" height="12" viewBox="0 0 12 12" fill="none">
                                <path d="M1 1l10 10M11 1L1 11" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- Customer info inputs --}}
                <div style="margin-top:1rem; display:grid; grid-template-columns:1fr 1fr; gap:8px;">
                    <input id="xpm-name" type="text" placeholder="Nama Lengkap"
                           style="padding:8px 10px;border:1px solid #e5e5e5;border-radius:8px;
                                  font-size:13px;color:#111;outline:none;width:100%;box-sizing:border-box;"
                           onfocus="this.style.borderColor='#333'" onblur="this.style.borderColor='#e5e5e5'"/>
                    <input id="xpm-email" type="email" placeholder="Email"
                           style="padding:8px 10px;border:1px solid #e5e5e5;border-radius:8px;
                                  font-size:13px;color:#111;outline:none;width:100%;box-sizing:border-box;"
                           onfocus="this.style.borderColor='#333'" onblur="this.style.borderColor='#e5e5e5'"/>
                </div>
                <input id="xpm-phone" type="tel" placeholder="Nomor HP (opsional)"
                       style="margin-top:8px;padding:8px 10px;border:1px solid #e5e5e5;border-radius:8px;
                              font-size:13px;color:#111;outline:none;width:100%;box-sizing:border-box;"
                       onfocus="this.style.borderColor='#333'" onblur="this.style.borderColor='#e5e5e5'"/>
            </div>

            {{-- Tabs --}}
            <div style="display:flex; gap:0; border-bottom:1px solid #f0f0f0; padding:0 1.5rem; flex-shrink:0;">
                @foreach([
                    ['id'=>'tab-va',    'label'=>'Virtual Account', 'key'=>'va'],
                    ['id'=>'tab-qris',  'label'=>'QRIS',            'key'=>'qris'],
                    ['id'=>'tab-ewallet','label'=>'E-Wallet',        'key'=>'ewallet'],
                    ['id'=>'tab-retail','label'=>'Minimarket',       'key'=>'retail'],
                ] as $tab)
                <button id="{{ $tab['id'] }}"
                        onclick="xpmSwitchTab('{{ $tab['key'] }}')"
                        data-tab="{{ $tab['key'] }}"
                        style="padding:10px 12px;border:none;background:transparent;cursor:pointer;
                               font-size:12px;font-weight:600;color:#999;border-bottom:2px solid transparent;
                               white-space:nowrap;transition:all .15s;">
                    {{ $tab['label'] }}
                </button>
                @endforeach
            </div>

            {{-- Search (VA only) --}}
            <div id="xpm-search-wrap" style="padding:10px 1.5rem 0; flex-shrink:0; display:none;">
                <div style="position:relative;">
                    <svg width="14" height="14" viewBox="0 0 14 14" fill="none"
                         style="position:absolute;left:10px;top:50%;transform:translateY(-50%);">
                        <circle cx="5.5" cy="5.5" r="4.5" stroke="#aaa" stroke-width="1.2"/>
                        <path d="M9 9l3 3" stroke="#aaa" stroke-width="1.2" stroke-linecap="round"/>
                    </svg>
                    <input id="xpm-search" type="text" placeholder="Cari bank..."
                           oninput="xpmFilterBanks()"
                           style="width:100%;box-sizing:border-box;padding:8px 10px 8px 30px;
                                  border:1px solid #e5e5e5;border-radius:8px;font-size:13px;
                                  color:#111;outline:none;"
                           onfocus="this.style.borderColor='#333'" onblur="this.style.borderColor='#e5e5e5'"/>
                </div>
            </div>

            {{-- Method list --}}
            <div id="xpm-method-list"
                 style="flex:1;overflow-y:auto;padding:8px 1rem 1rem;">
            </div>

            {{-- Footer --}}
            <div style="padding:.875rem 1.5rem;border-top:1px solid #f0f0f0;flex-shrink:0;
                        display:flex;align-items:center;gap:8px;">
                <svg width="13" height="13" viewBox="0 0 13 13" fill="none">
                    <rect x=".5" y="2.5" width="12" height="9" rx="2" stroke="#bbb" stroke-width="1.1"/>
                    <path d="M.5 5.5h12" stroke="#bbb" stroke-width="1.1"/>
                    <circle cx="3" cy="8.5" r=".8" fill="#bbb"/>
                </svg>
                <p style="margin:0;font-size:11px;color:#bbb;">
                    Transaksi diproses aman melalui Xendit &mdash; berlisensi Bank Indonesia.
                </p>
            </div>
        </div>

        {{-- ── SCREEN 2: Payment detail / instruction ── --}}
        <div id="xpm-screen-detail" style="display:none; flex:1; overflow-y:auto; flex-direction:column;">

            {{-- Detail header --}}
            <div style="padding:1.25rem 1.5rem 1rem; border-bottom:1px solid #f0f0f0; flex-shrink:0;
                        display:flex; align-items:center; gap:12px;">
                <button onclick="xpmBack()"
                        style="width:30px;height:30px;border-radius:50%;border:1px solid #e8e8e8;
                               background:transparent;cursor:pointer;display:flex;align-items:center;
                               justify-content:center;flex-shrink:0;">
                    <svg width="10" height="10" viewBox="0 0 10 10" fill="none">
                        <path d="M7 1L3 5l4 4" stroke="#555" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>
                <div style="flex:1;">
                    <p style="margin:0;font-size:11px;font-weight:600;letter-spacing:.08em;color:#999;text-transform:uppercase;" id="xpm-detail-type">Virtual Account</p>
                    <p style="margin:0;font-size:17px;font-weight:700;color:#111;" id="xpm-detail-bank">BNI</p>
                </div>
                <button onclick="closeXenditModal()"
                        style="width:30px;height:30px;border-radius:50%;border:1px solid #e8e8e8;
                               background:transparent;cursor:pointer;display:flex;align-items:center;
                               justify-content:center;color:#777;">
                    <svg width="12" height="12" viewBox="0 0 12 12" fill="none">
                        <path d="M1 1l10 10M11 1L1 11" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                    </svg>
                </button>
            </div>

            {{-- Loading state --}}
            <div id="xpm-loading" style="padding:3rem 1.5rem; text-align:center;">
                <div id="xpm-spinner"
                     style="width:36px;height:36px;border:3px solid #f0f0f0;
                            border-top-color:#222;border-radius:50%;margin:0 auto 1rem;
                            animation:xpmSpin 0.8s linear infinite;"></div>
                <p style="margin:0;font-size:13px;color:#888;">Membuat pembayaran...</p>
            </div>

            {{-- Detail content --}}
            <div id="xpm-detail-content" style="display:none; padding:1.25rem 1.5rem; flex:1;">

                {{-- Amount row --}}
                <div style="background:#fafafa;border:1px solid #f0f0f0;border-radius:10px;
                            padding:.875rem 1rem;margin-bottom:1rem;
                            display:flex;align-items:center;justify-content:space-between;">
                    <p style="margin:0;font-size:12px;color:#999;">Total Pembayaran</p>
                    <p style="margin:0;font-size:16px;font-weight:700;color:#111;">
                        Rp <span id="xpm-detail-amount">0</span>
                    </p>
                </div>

                {{-- VA info --}}
                <div id="xpm-va-section" style="display:none;">
                    <p style="font-size:12px;color:#999;margin:0 0 6px;">Nomor Virtual Account</p>
                    <div style="display:flex;align-items:center;gap:10px;
                                background:#f8f8f8;border:1px solid #ebebeb;border-radius:10px;
                                padding:.875rem 1rem;">
                        <p id="xpm-va-number"
                           style="margin:0;font-size:22px;font-weight:700;color:#111;letter-spacing:2px;flex:1;word-break:break-all;">
                            —
                        </p>
                        <button onclick="xpmCopy('xpm-va-number', this)"
                                style="padding:5px 12px;border:1px solid #ddd;border-radius:6px;
                                       background:#fff;font-size:12px;font-weight:600;color:#555;
                                       cursor:pointer;flex-shrink:0;">
                            Salin
                        </button>
                    </div>
                    <p style="font-size:11px;color:#bbb;margin:6px 0 0;">
                        Berlaku hingga: <span id="xpm-va-expires">—</span>
                    </p>
                    <div style="margin-top:1.25rem;">
                        <p style="font-size:12px;font-weight:600;color:#555;margin:0 0 8px;">Cara Pembayaran:</p>
                        <ol id="xpm-va-steps" style="margin:0;padding-left:1.25rem;font-size:13px;color:#555;line-height:1.8;">
                        </ol>
                    </div>
                </div>

                {{-- QRIS info --}}
                <div id="xpm-qris-section" style="display:none;text-align:center;">
                    <p style="font-size:12px;color:#999;margin:0 0 12px;">
                        Scan QR code di bawah ini menggunakan aplikasi mobile banking atau e-wallet apapun.
                    </p>
                    <div style="display:inline-block;padding:12px;border:1px solid #ebebeb;
                                border-radius:12px;background:#fff;">
                        <div id="xpm-qr-canvas" style="width:200px;height:200px;"></div>
                    </div>
                    <p style="font-size:11px;color:#bbb;margin:10px 0 0;">
                        QR berlaku: <span id="xpm-qr-expires">—</span>
                    </p>
                    <div style="margin-top:1.25rem;text-align:left;">
                        <p style="font-size:12px;font-weight:600;color:#555;margin:0 0 8px;">Cara Pembayaran:</p>
                        <ol style="margin:0;padding-left:1.25rem;font-size:13px;color:#555;line-height:1.8;">
                            <li>Buka aplikasi mobile banking atau e-wallet Anda.</li>
                            <li>Pilih menu <strong>Bayar</strong> atau <strong>Scan QR</strong>.</li>
                            <li>Arahkan kamera ke QR code di atas.</li>
                            <li>Periksa detail pembayaran dan konfirmasi.</li>
                        </ol>
                    </div>
                </div>

                {{-- E-Wallet info --}}
                <div id="xpm-ewallet-section" style="display:none;">
                    <div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:10px;
                                padding:.875rem 1rem;margin-bottom:1rem;">
                        <p style="margin:0;font-size:13px;color:#166534;line-height:1.6;">
                            Anda akan diarahkan ke halaman <strong id="xpm-ewallet-name">e-wallet</strong>
                            untuk menyelesaikan pembayaran. Pastikan saldo mencukupi.
                        </p>
                    </div>
                    <a id="xpm-ewallet-url" href="#" target="_blank"
                       style="display:block;width:100%;box-sizing:border-box;padding:.875rem;
                              background:#111;color:#fff;text-align:center;border-radius:10px;
                              font-size:14px;font-weight:600;text-decoration:none;letter-spacing:.02em;">
                        Lanjutkan ke Pembayaran
                    </a>
                    <p style="font-size:11px;color:#bbb;text-align:center;margin:8px 0 0;">
                        Anda akan meninggalkan halaman ini sementara.
                    </p>
                </div>

                {{-- Retail OTC info --}}
                <div id="xpm-retail-section" style="display:none;">
                    <p style="font-size:12px;color:#999;margin:0 0 6px;">Kode Pembayaran</p>
                    <div style="display:flex;align-items:center;gap:10px;
                                background:#f8f8f8;border:1px solid #ebebeb;border-radius:10px;
                                padding:.875rem 1rem;">
                        <p id="xpm-retail-code"
                           style="margin:0;font-size:26px;font-weight:700;color:#111;letter-spacing:4px;flex:1;">
                            —
                        </p>
                        <button onclick="xpmCopy('xpm-retail-code', this)"
                                style="padding:5px 12px;border:1px solid #ddd;border-radius:6px;
                                       background:#fff;font-size:12px;font-weight:600;color:#555;
                                       cursor:pointer;flex-shrink:0;">
                            Salin
                        </button>
                    </div>
                    <p style="font-size:11px;color:#bbb;margin:6px 0 0;">
                        Berlaku hingga: <span id="xpm-retail-expires">—</span>
                    </p>
                    <div style="margin-top:1.25rem;">
                        <p style="font-size:12px;font-weight:600;color:#555;margin:0 0 8px;">Cara Pembayaran:</p>
                        <ol id="xpm-retail-steps" style="margin:0;padding-left:1.25rem;font-size:13px;color:#555;line-height:1.8;">
                        </ol>
                    </div>
                </div>

                {{-- Status polling --}}
                <div id="xpm-status-row" style="margin-top:1.25rem;padding:.75rem 1rem;
                     border:1px solid #f0f0f0;border-radius:10px;
                     display:flex;align-items:center;justify-content:space-between;">
                    <div style="display:flex;align-items:center;gap:8px;">
                        <div id="xpm-status-dot"
                             style="width:8px;height:8px;border-radius:50%;background:#f59e0b;
                                    animation:xpmPulse 1.5s ease-in-out infinite;"></div>
                        <p id="xpm-status-text" style="margin:0;font-size:12px;color:#777;">
                            Menunggu pembayaran...
                        </p>
                    </div>
                    <button onclick="xpmCheckStatus()"
                            style="font-size:11px;font-weight:600;color:#555;background:transparent;
                                   border:1px solid #e5e5e5;padding:4px 10px;border-radius:5px;cursor:pointer;">
                        Cek Status
                    </button>
                </div>
            </div>

            {{-- Detail footer --}}
            <div style="padding:.875rem 1.5rem;border-top:1px solid #f0f0f0;flex-shrink:0;
                        display:flex;align-items:center;gap:8px;">
                <svg width="13" height="13" viewBox="0 0 13 13" fill="none">
                    <rect x=".5" y="2.5" width="12" height="9" rx="2" stroke="#bbb" stroke-width="1.1"/>
                    <path d="M.5 5.5h12" stroke="#bbb" stroke-width="1.1"/>
                    <circle cx="3" cy="8.5" r=".8" fill="#bbb"/>
                </svg>
                <p style="margin:0;font-size:11px;color:#bbb;">
                    Transaksi diproses aman melalui Xendit &mdash; berlisensi Bank Indonesia.
                </p>
            </div>
        </div>

    </div>{{-- /shell --}}
</div>{{-- /overlay --}}


{{-- =====================================================================
     STYLES
     ===================================================================== --}}
<style>
@keyframes xpmSpin  { to { transform: rotate(360deg); } }
@keyframes xpmPulse { 0%,100% { opacity:1; } 50% { opacity:.3; } }

#xendit-payment-modal * { box-sizing: border-box; }

.xpm-method-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 10px 10px;
    border-radius: 10px;
    cursor: pointer;
    transition: background .12s;
    border: 1px solid transparent;
}
.xpm-method-item:hover { background: #f8f8f8; }
.xpm-method-item:active { background: #f0f0f0; }

.xpm-method-icon {
    width: 42px;
    height: 42px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 11px;
    font-weight: 700;
    letter-spacing: .03em;
    color: #fff;
    flex-shrink: 0;
}

.xpm-method-name  { margin:0; font-size:14px; font-weight:600; color:#111; }
.xpm-method-desc  { margin:0; font-size:12px; color:#999; }
.xpm-method-badge {
    font-size:10px; font-weight:600; background:#f5f5f5; border:1px solid #eee;
    color:#888; padding:2px 7px; border-radius:4px; flex-shrink:0;
}

.xpm-section-label {
    font-size:10px;font-weight:700;letter-spacing:.1em;text-transform:uppercase;
    color:#ccc;padding:10px 10px 4px;
}

/* Tab active */
.xpm-tab-active {
    color: #111 !important;
    border-bottom-color: #111 !important;
}
</style>


{{-- =====================================================================
     JAVASCRIPT
     ===================================================================== --}}
<script>
(function() {

    // ── Config ──────────────────────────────────────────────────────
    const CSRF = document.querySelector('meta[name="csrf-token"]')?.content ?? '';
    const ORDER_ID = '{{ $orderId ?? "ORD-" . time() }}';
    const AMOUNT   = {{ (int) $amount }};

    // ── State ───────────────────────────────────────────────────────
    let activeTab = 'va';
    let currentPaymentId = null;
    let pollTimer = null;

    // ── Bank / method data ──────────────────────────────────────────
    const METHODS = {
        va: [
            { code:'BCA_VIRTUAL_ACCOUNT',   name:'BCA',              full:'Bank Central Asia',        color:'#003087' },
            { code:'BNI_VIRTUAL_ACCOUNT',   name:'BNI',              full:'Bank Negara Indonesia',    color:'#F78000' },
            { code:'BRI_VIRTUAL_ACCOUNT',   name:'BRI',              full:'Bank Rakyat Indonesia',    color:'#005BAA' },
            { code:'MANDIRI_VIRTUAL_ACCOUNT',name:'Mandiri',         full:'Bank Mandiri',             color:'#0A3E8C' },
            { code:'PERMATA_VIRTUAL_ACCOUNT',name:'Permata',         full:'Bank Permata',             color:'#0F4C81' },
            { code:'CIMB_VIRTUAL_ACCOUNT',  name:'CIMB Niaga',       full:'CIMB Niaga',               color:'#C8202F' },
            { code:'BSI_VIRTUAL_ACCOUNT',   name:'BSI',              full:'Bank Syariah Indonesia',   color:'#005C2E' },
            { code:'BJB_VIRTUAL_ACCOUNT',   name:'BJB',              full:'Bank Jabar Banten',        color:'#00529B' },
            { code:'BTN_VIRTUAL_ACCOUNT',   name:'BTN',              full:'Bank Tabungan Negara',     color:'#FFC20E', textColor:'#333' },
            { code:'NOBU_VIRTUAL_ACCOUNT',  name:'Nobu Bank',        full:'National Nobu Bank',       color:'#D22027' },
            { code:'BNC_VIRTUAL_ACCOUNT',   name:'Neo Commerce',     full:'Bank Neo Commerce',        color:'#FF6900' },
            { code:'SAHABAT_SAMPOERNA_VIRTUAL_ACCOUNT', name:'Sampoerna', full:'Bank Sahabat Sampoerna', color:'#444' },
        ],
        qris: [
            { code:'QRIS', name:'QRIS', full:'Semua e-wallet & m-banking', color:'#E63946', badge:'Direkomendasikan' },
        ],
        ewallet: [
            { code:'DANA',      name:'DANA',      full:'Dompet Digital DANA',    color:'#118EEA' },
            { code:'OVO',       name:'OVO',        full:'OVO',                    color:'#4B2F8C' },
            { code:'SHOPEEPAY', name:'ShopeePay', full:'Shopee Pay',             color:'#EE4D2D' },
            { code:'LINKAJA',   name:'LinkAja',    full:'LinkAja',                color:'#E3141A' },
            { code:'ASTRAPAY',  name:'AstraPay',   full:'AstraPay',               color:'#E9272E' },
            { code:'JENIUSPAY', name:'Jenius Pay', full:'Jenius Pay',             color:'#00B2C2' },
        ],
        retail: [
            { code:'ALFAMART',  name:'Alfamart',   full:'Alfamart / Alfamidi',    color:'#E31E24' },
            { code:'INDOMARET', name:'Indomaret',  full:'Indomaret',              color:'#E6003A' },
        ],
    };

    // ── VA instructions ─────────────────────────────────────────────
    const VA_INSTRUCTIONS = {
        BCA_VIRTUAL_ACCOUNT: [
            'Buka aplikasi myBCA atau kunjungi ATM BCA.',
            'Pilih <strong>Transfer</strong> &rarr; <strong>Virtual Account</strong>.',
            'Masukkan nomor VA di atas dan konfirmasi.',
            'Simpan bukti pembayaran.',
        ],
        BNI_VIRTUAL_ACCOUNT: [
            'Buka aplikasi BNI Mobile Banking atau ATM BNI.',
            'Pilih <strong>Transfer</strong> &rarr; <strong>Virtual Account</strong>.',
            'Masukkan nomor VA di atas, lanjutkan dan konfirmasi.',
            'Simpan bukti pembayaran.',
        ],
        BRI_VIRTUAL_ACCOUNT: [
            'Buka BRImo atau kunjungi ATM BRI.',
            'Pilih <strong>Pembayaran</strong> &rarr; <strong>BRIVA</strong>.',
            'Masukkan nomor VA di atas, cek detail, lalu konfirmasi.',
            'Simpan bukti pembayaran.',
        ],
        MANDIRI_VIRTUAL_ACCOUNT: [
            'Buka aplikasi Livin\' by Mandiri atau ATM Mandiri.',
            'Pilih <strong>Bayar</strong> &rarr; <strong>Multipayment</strong>.',
            'Masukkan kode perusahaan <strong>70014</strong> dan nomor VA.',
            'Konfirmasi dan simpan bukti.',
        ],
        DEFAULT: [
            'Buka aplikasi mobile banking Anda.',
            'Pilih menu <strong>Transfer</strong> atau <strong>Virtual Account</strong>.',
            'Masukkan nomor VA di atas dan konfirmasi jumlah.',
            'Simpan bukti pembayaran.',
        ],
    };

    const RETAIL_INSTRUCTIONS = {
        ALFAMART: [
            'Datangi kasir Alfamart / Alfamidi terdekat.',
            'Tunjukkan kode pembayaran di atas ke kasir.',
            'Bayar sejumlah Rp <strong id="retail-amt-a">0</strong>.',
            'Simpan struk pembayaran sebagai bukti.',
        ],
        INDOMARET: [
            'Datangi kasir Indomaret terdekat.',
            'Tunjukkan kode pembayaran di atas ke kasir.',
            'Bayar sejumlah Rp <strong id="retail-amt-i">0</strong>.',
            'Simpan struk pembayaran sebagai bukti.',
        ],
    };

    // ── Helpers ─────────────────────────────────────────────────────
    function fmt(n) {
        return Number(n).toLocaleString('id-ID');
    }
    function initials(name) {
        return name.split(' ').slice(0,2).map(w=>w[0]).join('').toUpperCase();
    }
    function getExpiry(isoString) {
        if (!isoString) return '—';
        const d = new Date(isoString);
        return d.toLocaleString('id-ID', {day:'2-digit',month:'long',year:'numeric',hour:'2-digit',minute:'2-digit'});
    }

    // ── Render method list ───────────────────────────────────────────
    function renderMethods(tab, filter='') {
        const list = document.getElementById('xpm-method-list');
        const data = METHODS[tab] || [];
        const lower = filter.toLowerCase();
        const filtered = filter
            ? data.filter(m => m.name.toLowerCase().includes(lower) || m.full.toLowerCase().includes(lower))
            : data;

        if (!filtered.length) {
            list.innerHTML = '<p style="text-align:center;color:#bbb;font-size:13px;padding:2rem 0;">Tidak ditemukan.</p>';
            return;
        }

        const labels = {va:'Bank', qris:'QR Code', ewallet:'Dompet Digital', retail:'Minimarket'};
        let html = `<div class="xpm-section-label">${labels[tab] ?? tab}</div>`;
        filtered.forEach(m => {
            const ic = m.textColor
                ? `<div class="xpm-method-icon" style="background:${m.color};color:${m.textColor};">${initials(m.name)}</div>`
                : `<div class="xpm-method-icon" style="background:${m.color};">${initials(m.name)}</div>`;

            const badge = m.badge
                ? `<span class="xpm-method-badge" style="background:#fef3c7;border-color:#fde68a;color:#92400e;">${m.badge}</span>`
                : '';

            html += `
            <div class="xpm-method-item" onclick="window.xpmSelectMethod('${m.code}','${m.name}','${tab}')">
                ${ic}
                <div style="flex:1;min-width:0;">
                    <p class="xpm-method-name">${m.name}</p>
                    <p class="xpm-method-desc">${m.full}</p>
                </div>
                ${badge}
                <svg width="7" height="12" viewBox="0 0 7 12" fill="none" style="flex-shrink:0;">
                    <path d="M1 1l5 5-5 5" stroke="#ccc" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>`;
        });
        list.innerHTML = html;
    }

    // ── Tab switch ───────────────────────────────────────────────────
    window.xpmSwitchTab = function(tab) {
        activeTab = tab;
        document.querySelectorAll('#xendit-payment-modal [data-tab]').forEach(el => {
            el.classList.toggle('xpm-tab-active', el.dataset.tab === tab);
        });
        document.getElementById('xpm-search-wrap').style.display = tab === 'va' ? 'block' : 'none';
        document.getElementById('xpm-search').value = '';
        renderMethods(tab);
    };

    // ── Bank search ──────────────────────────────────────────────────
    window.xpmFilterBanks = function() {
        renderMethods(activeTab, document.getElementById('xpm-search').value);
    };

    // ── Select method & call API ─────────────────────────────────────
    window.xpmSelectMethod = function(code, name, tab) {
        const nameEl  = document.getElementById('xpm-name').value.trim();
        const emailEl = document.getElementById('xpm-email').value.trim();

        if (!nameEl) { alert('Mohon isi nama lengkap Anda.'); return; }
        if (!emailEl || !/\S+@\S+\.\S+/.test(emailEl)) { alert('Mohon isi email yang valid.'); return; }

        // Show detail screen
        document.getElementById('xpm-screen-select').style.display = 'none';
        document.getElementById('xpm-screen-detail').style.display = 'flex';
        document.getElementById('xpm-detail-type').textContent = {va:'Virtual Account',qris:'QRIS',ewallet:'E-Wallet',retail:'Minimarket'}[tab]??tab;
        document.getElementById('xpm-detail-bank').textContent = name;
        document.getElementById('xpm-detail-amount').textContent = fmt(AMOUNT);
        document.getElementById('xpm-loading').style.display = 'block';
        document.getElementById('xpm-detail-content').style.display = 'none';
        ['va','qris','ewallet','retail'].forEach(s => {
            document.getElementById(`xpm-${s}-section`).style.display = 'none';
        });

        // Call Laravel endpoint
        fetch('/payment/create', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CSRF,
                'Accept': 'application/json',
            },
            body: JSON.stringify({
                channel_code: code,
                amount:       AMOUNT,
                order_id:     ORDER_ID,
                name:         nameEl,
                email:        emailEl,
                phone:        document.getElementById('xpm-phone').value.trim(),
            }),
        })
        .then(r => r.json())
        .then(res => {
            document.getElementById('xpm-loading').style.display = 'none';
            document.getElementById('xpm-detail-content').style.display = 'block';

            if (!res.success) {
                showError(res.message ?? 'Gagal membuat pembayaran.');
                return;
            }

            currentPaymentId = res.payment_request_id;
            const actions = res.actions ?? [];

            if (tab === 'va') {
                renderVA(actions, name, code);
            } else if (tab === 'qris') {
                renderQRIS(actions);
            } else if (tab === 'ewallet') {
                renderEwallet(actions, name);
            } else if (tab === 'retail') {
                renderRetail(actions, code);
            }

            startPolling();
        })
        .catch(() => {
            document.getElementById('xpm-loading').style.display = 'none';
            document.getElementById('xpm-detail-content').style.display = 'block';
            showError('Koneksi gagal. Periksa jaringan Anda dan coba lagi.');
        });
    };

    // ── Render VA ────────────────────────────────────────────────────
    function renderVA(actions, name, code) {
        const vaAction = actions.find(a => a.descriptor === 'VIRTUAL_ACCOUNT_NUMBER');
        const expiresAction = actions.find(a => a.descriptor === 'EXPIRY_DATE');
        const vaNum = vaAction?.value ?? '—';
        const expires = expiresAction?.value ?? null;

        document.getElementById('xpm-va-section').style.display = 'block';
        document.getElementById('xpm-va-number').textContent = vaNum;
        document.getElementById('xpm-va-expires').textContent = expires ? getExpiry(expires) : '24 jam dari sekarang';

        const steps = VA_INSTRUCTIONS[code] ?? VA_INSTRUCTIONS.DEFAULT;
        document.getElementById('xpm-va-steps').innerHTML = steps.map(s => `<li>${s}</li>`).join('');
    }

    // ── Render QRIS ──────────────────────────────────────────────────
    function renderQRIS(actions) {
        const qrAction = actions.find(a => a.descriptor === 'QR_STRING');
        const qrString = qrAction?.value ?? null;
        const expiresAction = actions.find(a => a.descriptor === 'EXPIRY_DATE');

        document.getElementById('xpm-qris-section').style.display = 'block';
        document.getElementById('xpm-qr-expires').textContent = expiresAction?.value ? getExpiry(expiresAction.value) : '—';

        const canvas = document.getElementById('xpm-qr-canvas');
        canvas.innerHTML = '';

        if (qrString) {
            // Use qrcode.js from CDN
            const script = document.createElement('script');
            script.src = 'https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js';
            script.onload = function() {
                new QRCode(canvas, {
                    text:         qrString,
                    width:        200,
                    height:       200,
                    colorDark:    '#111111',
                    colorLight:   '#ffffff',
                    correctLevel: QRCode.CorrectLevel.M,
                });
            };
            // If already loaded
            if (window.QRCode) {
                script.onload();
            } else {
                document.head.appendChild(script);
            }
        } else {
            canvas.innerHTML = '<p style="color:#bbb;font-size:12px;text-align:center;padding:4rem 0;">QR tidak tersedia</p>';
        }
    }

    // ── Render E-Wallet ──────────────────────────────────────────────
    function renderEwallet(actions, name) {
        const urlAction = actions.find(a => a.descriptor === 'WEB_URL' || a.descriptor === 'DEEPLINK_URL');
        const url = urlAction?.value ?? '#';

        document.getElementById('xpm-ewallet-section').style.display = 'block';
        document.getElementById('xpm-ewallet-name').textContent = name;
        document.getElementById('xpm-ewallet-url').href = url;
    }

    // ── Render Retail ────────────────────────────────────────────────
    function renderRetail(actions, code) {
        const codeAction = actions.find(a => a.descriptor === 'PAYMENT_CODE');
        const expiresAction = actions.find(a => a.descriptor === 'EXPIRY_DATE');
        const payCode = codeAction?.value ?? '—';

        document.getElementById('xpm-retail-section').style.display = 'block';
        document.getElementById('xpm-retail-code').textContent = payCode;
        document.getElementById('xpm-retail-expires').textContent = expiresAction?.value ? getExpiry(expiresAction.value) : '—';

        const steps = RETAIL_INSTRUCTIONS[code] ?? RETAIL_INSTRUCTIONS.ALFAMART;
        document.getElementById('xpm-retail-steps').innerHTML = steps.map(s => `<li>${s.replace('0', fmt(AMOUNT))}</li>`).join('');
    }

    // ── Status polling ────────────────────────────────────────────────
    function startPolling() {
        if (pollTimer) clearInterval(pollTimer);
        pollTimer = setInterval(xpmCheckStatus, 10000);
    }

    window.xpmCheckStatus = function() {
        if (!currentPaymentId) return;
        fetch(`/payment/status/${currentPaymentId}`, {
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF }
        })
        .then(r => r.json())
        .then(res => {
            if (!res.success) return;
            const dot  = document.getElementById('xpm-status-dot');
            const text = document.getElementById('xpm-status-text');
            const statusMap = {
                SUCCEEDED:          { color:'#16a34a', label:'Pembayaran berhasil!',         anim:'none' },
                ACCEPTING_PAYMENTS: { color:'#f59e0b', label:'Menunggu pembayaran...',       anim:'xpmPulse 1.5s ease-in-out infinite' },
                REQUIRES_ACTION:    { color:'#f59e0b', label:'Menunggu tindakan...',          anim:'xpmPulse 1.5s ease-in-out infinite' },
                FAILED:             { color:'#dc2626', label:'Pembayaran gagal.',             anim:'none' },
                EXPIRED:            { color:'#9ca3af', label:'Pembayaran kedaluwarsa.',       anim:'none' },
                CANCELED:           { color:'#9ca3af', label:'Pembayaran dibatalkan.',        anim:'none' },
            };
            const s = statusMap[res.status] ?? { color:'#9ca3af', label:res.status, anim:'none' };
            dot.style.background = s.color;
            dot.style.animation  = s.anim;
            text.textContent     = s.label;

            if (res.status === 'SUCCEEDED') {
                clearInterval(pollTimer);
                setTimeout(() => { window.location.href = '/payment/success'; }, 1500);
            }
            if (['FAILED','EXPIRED','CANCELED'].includes(res.status)) {
                clearInterval(pollTimer);
            }
        })
        .catch(() => {});
    };

    // ── Error display ─────────────────────────────────────────────────
    function showError(msg) {
        document.getElementById('xpm-detail-content').innerHTML = `
        <div style="text-align:center;padding:2rem 0;">
            <div style="width:48px;height:48px;border-radius:50%;background:#fee2e2;
                        display:flex;align-items:center;justify-content:center;margin:0 auto 1rem;">
                <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                    <path d="M10 6v5M10 14h.01" stroke="#dc2626" stroke-width="2" stroke-linecap="round"/>
                    <circle cx="10" cy="10" r="9" stroke="#dc2626" stroke-width="1.5"/>
                </svg>
            </div>
            <p style="font-size:14px;font-weight:600;color:#111;margin:0 0 6px;">Pembayaran Gagal</p>
            <p style="font-size:13px;color:#888;margin:0 0 1.5rem;">${msg}</p>
            <button onclick="xpmBack()"
                    style="padding:8px 20px;background:#111;color:#fff;border:none;
                           border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;">
                Coba Lagi
            </button>
        </div>`;
    }

    // ── Copy to clipboard ─────────────────────────────────────────────
    window.xpmCopy = function(id, btn) {
        const text = document.getElementById(id).textContent.trim();
        navigator.clipboard.writeText(text).then(() => {
            const orig = btn.textContent;
            btn.textContent = 'Tersalin';
            btn.style.background = '#f0fdf4';
            btn.style.borderColor = '#bbf7d0';
            btn.style.color = '#166534';
            setTimeout(() => {
                btn.textContent = orig;
                btn.style.background = '#fff';
                btn.style.borderColor = '#ddd';
                btn.style.color = '#555';
            }, 2000);
        });
    };

    // ── Back ──────────────────────────────────────────────────────────
    window.xpmBack = function() {
        if (pollTimer) clearInterval(pollTimer);
        currentPaymentId = null;
        document.getElementById('xpm-screen-detail').style.display = 'none';
        document.getElementById('xpm-screen-select').style.display = 'flex';
    };

    // ── Open / Close ──────────────────────────────────────────────────
    window.openXenditModal = function() {
        const modal = document.getElementById('xendit-payment-modal');
        modal.style.display = 'flex';
        document.getElementById('xpm-display-amount').textContent = fmt(AMOUNT);
        xpmSwitchTab('va');
        document.querySelector('.xpm-tab-active')?.classList.remove('xpm-tab-active');
        document.getElementById('tab-va').classList.add('xpm-tab-active');
    };

    window.closeXenditModal = function() {
        if (pollTimer) clearInterval(pollTimer);
        document.getElementById('xendit-payment-modal').style.display = 'none';
        xpmBack();
    };

    // Close on overlay click
    document.getElementById('xendit-payment-modal').addEventListener('click', function(e) {
        if (e.target === this) closeXenditModal();
    });

    // Init tab highlight
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('tab-va').classList.add('xpm-tab-active');
    });

})();
</script>