@extends('layouts.dashboard')
@section('title', 'QR Code | Payou.id')

@section('content')
<div style="min-height: 100vh; background: #f8fafc; padding: 24px;">
    <div style="max-width: 1200px; margin: 0 auto;">

        {{-- Header Section --}}
        <div style="margin-bottom: 24px;">
            <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 8px;">
                <a href="{{ route('dashboard') }}" style="width: 36px; height: 36px; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 8px; display: flex; align-items: center; justify-content: center; text-decoration: none; transition: all 0.2s;">
                    <i class="fas fa-arrow-left" style="font-size: 14px; color: #475569;"></i>
                </a>
                <div>
                    <h1 style="margin: 0; font-size: 24px; font-weight: 600; color: #000000;">QR Code Saya</h1>
                    <p style="margin: 0; font-size: 14px; color: #797979;">Bagikan QR Code untuk mempromosikan halaman Anda</p>
                </div>
            </div>
        </div>

        {{-- Main Grid --}}
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-bottom: 24px;">

            {{-- Left Column - QR Code Display --}}
            <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 32px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">

                {{-- QR Code Header --}}
                <div style="text-align: center; margin-bottom: 24px;">
                    <div style="width: 48px; height: 48px; background: #f1f5f9; border-radius: 10px; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 12px;">
                        <i class="fas fa-qrcode" style="font-size: 20px; color: #475569;"></i>
                    </div>
                    <h2 style="margin: 0 0 6px 0; font-size: 18px; font-weight: 600; color: #0f172a;">QR Code Anda</h2>
                    <p style="margin: 0; font-size: 13px; color: #94a3b8;">Scan untuk mengunjungi halaman</p>
                </div>

                {{-- QR Code Container --}}
                <div style="background: linear-gradient(145deg, #ffffff 0%, #f8fbff 100%); border: 2px solid #e6f0ff; border-radius: 16px; padding: 32px; margin-bottom: 20px; display: flex; justify-content: center; align-items: center; min-height: 280px; position: relative; box-shadow: 0 8px 20px rgba(0,102,204,0.08);">
                    {{-- Logo payou.id di tengah QR --}}
                    <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 64px; height: 36px; background: #ffffff; border-radius: 10px; display: flex; align-items: center; justify-content: center; box-shadow: 0 3px 12px rgba(0,102,204,0.22); border: 2px solid #e6f0ff; z-index: 10; pointer-events: none;">
                        <span style="color: #0066CC; font-weight: 800; font-size: 12px; letter-spacing: -0.3px; line-height: 1;">payou<span style="color: #3b82f6;">.</span>id</span>
                    </div>
                    <div id="qrcode-main" style="filter: drop-shadow(0 4px 8px rgba(0,102,204,0.1));"></div>
                </div>

                {{-- URL Display --}}
                <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 12px 16px; margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
                    <i class="fas fa-link" style="color: #94a3b8; font-size: 14px;"></i>
                    <p style="margin: 0; font-size: 14px; color: #475569; font-weight: 500; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; flex: 1;">
                        {{ url('/' . ($userSlug ?? 'username')) }}
                    </p>
                </div>

                {{-- Action Buttons Row 1 --}}
                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 10px; margin-bottom: 10px;">
                    <button onclick="copyLink()" id="copy-btn"
                        style="padding: 12px 16px; background: #3b82f6; color: white; border: none; border-radius: 8px; font-size: 13px; font-weight: 500; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 6px;">
                        <i class="far fa-copy" style="font-size: 12px;"></i>
                        <span>Salin</span>
                    </button>

                    <button onclick="downloadQR()"
                        style="padding: 12px 16px; background: #ffffff; color: #475569; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 13px; font-weight: 500; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 6px;">
                        <i class="fas fa-download" style="font-size: 12px;"></i>
                        <span>Download</span>
                    </button>

                    <button onclick="shareWhatsApp()" id="whatsapp-share-btn"
                        style="padding: 12px 16px; background: #25D366; color: white; border: none; border-radius: 8px; font-size: 13px; font-weight: 500; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 6px;">
                        <i class="fab fa-whatsapp" style="font-size: 12px;"></i>
                        <span>WhatsApp</span>
                    </button>
                </div>

                {{-- Action Buttons Row 2 --}}
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                    <button onclick="printQR()"
                        style="padding: 12px 16px; background: #f8fafc; color: #475569; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 13px; font-weight: 500; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 6px;">
                        <i class="fas fa-print" style="font-size: 12px;"></i>
                        <span>Print</span>
                    </button>

                    <button onclick="shareQR()"
                        style="padding: 12px 16px; background: #f8fafc; color: #475569; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 13px; font-weight: 500; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 6px;">
                        <i class="fas fa-share-alt" style="font-size: 12px;"></i>
                        <span>Bagikan</span>
                    </button>
                </div>
            </div>

            {{-- Right Column --}}
            <div style="display: flex; flex-direction: column; gap: 20px;">

                {{-- Customization Card --}}
                <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                    <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 20px;">
                        <div style="width: 36px; height: 36px; background: #f1f5f9; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-palette" style="font-size: 14px; color: #475569;"></i>
                        </div>
                        <h3 style="margin: 0; font-size: 16px; font-weight: 600; color: #0f172a;">Kustomisasi QR Code</h3>
                    </div>

                    {{-- Warna QR --}}
                    <div style="margin-bottom: 20px;">
                        <label style="display: block; font-size: 13px; font-weight: 500; color: #475569; margin-bottom: 10px;">Warna QR Code</label>
                        <div style="display: flex; gap: 10px; flex-wrap: wrap; align-items: center;">
                            <button onclick="changeColor('#0066CC', this)" class="color-btn active" title="Biru Payou"
                                style="width: 38px; height: 38px; background: #0066CC; border-radius: 50%; border: 3px solid white; cursor: pointer; box-shadow: 0 0 0 3px rgba(0,102,204,0.35); transition: all 0.2s;">
                            </button>
                            <button onclick="changeColor('#111827', this)" class="color-btn" title="Hitam"
                                style="width: 38px; height: 38px; background: #111827; border-radius: 50%; border: 3px solid #e2e8f0; cursor: pointer; transition: all 0.2s;">
                            </button>
                            <button onclick="changeColor('#16a34a', this)" class="color-btn" title="Hijau"
                                style="width: 38px; height: 38px; background: #16a34a; border-radius: 50%; border: 3px solid #e2e8f0; cursor: pointer; transition: all 0.2s;">
                            </button>
                            <button onclick="changeColor('#dc2626', this)" class="color-btn" title="Merah"
                                style="width: 38px; height: 38px; background: #dc2626; border-radius: 50%; border: 3px solid #e2e8f0; cursor: pointer; transition: all 0.2s;">
                            </button>
                            <button onclick="changeColor('#7c3aed', this)" class="color-btn" title="Ungu"
                                style="width: 38px; height: 38px; background: #7c3aed; border-radius: 50%; border: 3px solid #e2e8f0; cursor: pointer; transition: all 0.2s;">
                            </button>
                            <button onclick="changeColor('#ea580c', this)" class="color-btn" title="Oranye"
                                style="width: 38px; height: 38px; background: #ea580c; border-radius: 50%; border: 3px solid #e2e8f0; cursor: pointer; transition: all 0.2s;">
                            </button>
                            <label title="Pilih Warna Kustom"
                                style="width: 38px; height: 38px; border-radius: 50%; border: 2px dashed #cbd5e1; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all 0.2s; overflow: hidden; position: relative;">
                                <i class="fas fa-plus" style="font-size: 13px; color: #94a3b8; pointer-events: none; position: relative; z-index: 1;"></i>
                                <input type="color" id="custom-color-picker" value="#0066CC"
                                    oninput="changeColor(this.value, this.parentElement)"
                                    style="position: absolute; opacity: 0; width: 100%; height: 100%; cursor: pointer; top: 0; left: 0; z-index: 2;">
                            </label>
                        </div>
                        <p style="margin: 10px 0 0 0; font-size: 11px; color: #94a3b8;">
                            Warna aktif: <span id="color-label" style="font-weight: 600; color: #0066CC;">#0066CC (Biru Payou)</span>
                        </p>
                        <div style="margin-top: 8px; background: #fffbeb; border: 1px solid #fde68a; border-radius: 6px; padding: 8px 10px; display: flex; align-items: center; gap: 7px;">
                            <i class="fas fa-exclamation-triangle" style="color: #d97706; font-size: 11px; flex-shrink: 0;"></i>
                            <p style="margin: 0; font-size: 11px; color: #92400e; line-height: 1.5;">Perubahan warna hanya berlaku untuk <strong>Download</strong>. Share WhatsApp tetap menggunakan warna biru Payou.</p>
                        </div>
                    </div>

                    <div style="border-top: 1px solid #f1f5f9; margin-bottom: 20px;"></div>

                    {{-- Download Info --}}
                    <div style="margin-bottom: 16px;">
                        <label style="display: block; font-size: 13px; font-weight: 500; color: #475569; margin-bottom: 8px;">Tentang Download PNG</label>
                        <div style="background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 8px; padding: 12px 14px; display: flex; gap: 10px;">
                            <i class="fas fa-check-circle" style="color: #16a34a; font-size: 14px; margin-top: 1px; flex-shrink: 0;"></i>
                            <p style="margin: 0; font-size: 12px; color: #15803d; line-height: 1.6;">
                                File download berupa <strong>QR Code bersih (PNG)</strong> — siap ditempel ke desain brosur, kartu nama, banner, atau media promosi lainnya sesuai kebutuhan.
                            </p>
                        </div>
                    </div>

                    {{-- Quality Info --}}
                    <div style="background: #f0f9ff; border: 1px solid #bae6fd; border-radius: 8px; padding: 12px; display: flex; gap: 10px;">
                        <i class="fas fa-info-circle" style="color: #0284c7; font-size: 14px; margin-top: 2px; flex-shrink: 0;"></i>
                        <p style="margin: 0; font-size: 12px; color: #0369a1; line-height: 1.5;">QR Code menggunakan tingkat koreksi error tinggi (Level H) untuk pemindaian optimal bahkan jika sebagian kode rusak atau kotor.</p>
                    </div>
                </div>

                {{-- Usage Tips Card --}}
                <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                    <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 16px;">
                        <div style="width: 36px; height: 36px; background: #f1f5f9; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-lightbulb" style="font-size: 14px; color: #475569;"></i>
                        </div>
                        <h3 style="margin: 0; font-size: 16px; font-weight: 600; color: #0f172a;">Tips Penggunaan</h3>
                    </div>
                    <div style="display: flex; flex-direction: column; gap: 12px;">
                        <div style="display: flex; gap: 10px;">
                            <i class="fas fa-check-circle" style="color: #10b981; font-size: 14px; margin-top: 2px;"></i>
                            <p style="margin: 0; font-size: 13px; color: #475569; line-height: 1.6;">Cetak QR Code pada brosur, kartu nama, atau spanduk untuk promosi offline</p>
                        </div>
                        <div style="display: flex; gap: 10px;">
                            <i class="fas fa-check-circle" style="color: #10b981; font-size: 14px; margin-top: 2px;"></i>
                            <p style="margin: 0; font-size: 13px; color: #475569; line-height: 1.6;">Bagikan di media sosial untuk meningkatkan jangkauan pelanggan</p>
                        </div>
                        <div style="display: flex; gap: 10px;">
                            <i class="fas fa-check-circle" style="color: #10b981; font-size: 14px; margin-top: 2px;"></i>
                            <p style="margin: 0; font-size: 13px; color: #475569; line-height: 1.6;">Pastikan QR Code tercetak dengan jelas dan tidak blur</p>
                        </div>
                        <div style="display: flex; gap: 10px;">
                            <i class="fas fa-check-circle" style="color: #10b981; font-size: 14px; margin-top: 2px;"></i>
                            <p style="margin: 0; font-size: 13px; color: #475569; line-height: 1.6;">Gunakan ukuran minimal 3×3 cm untuk hasil scan terbaik</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Use Cases Section --}}
        <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
            <h3 style="margin: 0 0 20px 0; font-size: 16px; font-weight: 600; color: #0f172a;">Ide Pemanfaatan QR Code</h3>
            <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px;">
                <div class="use-case-card" style="text-align: center; padding: 20px; background: #f8fafc; border-radius: 10px; border: 1px solid #e2e8f0;">
                    <div class="use-case-icon" style="width: 48px; height: 48px; background: #eff6ff; border-radius: 10px; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 12px;">
                        <i class="fas fa-utensils" style="font-size: 20px; color: #3b82f6;"></i>
                    </div>
                    <h4 style="margin: 0 0 6px 0; font-size: 14px; font-weight: 600; color: #0f172a;">Menu Digital</h4>
                    <p style="margin: 0; font-size: 12px; color: #94a3b8; line-height: 1.5;">Letakkan di meja restoran untuk akses menu online</p>
                </div>
                <div class="use-case-card" style="text-align: center; padding: 20px; background: #f8fafc; border-radius: 10px; border: 1px solid #e2e8f0;">
                    <div class="use-case-icon" style="width: 48px; height: 48px; background: #f0fdf4; border-radius: 10px; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 12px;">
                        <i class="fas fa-store" style="font-size: 20px; color: #10b981;"></i>
                    </div>
                    <h4 style="margin: 0 0 6px 0; font-size: 14px; font-weight: 600; color: #0f172a;">Etalase Toko</h4>
                    <p style="margin: 0; font-size: 12px; color: #94a3b8; line-height: 1.5;">Tempelkan di etalase untuk katalog produk digital</p>
                </div>
                <div class="use-case-card" style="text-align: center; padding: 20px; background: #f8fafc; border-radius: 10px; border: 1px solid #e2e8f0;">
                    <div class="use-case-icon" style="width: 48px; height: 48px; background: #fef3c7; border-radius: 10px; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 12px;">
                        <i class="fas fa-bullhorn" style="font-size: 20px; color: #f59e0b;"></i>
                    </div>
                    <h4 style="margin: 0 0 6px 0; font-size: 14px; font-weight: 600; color: #0f172a;">Media Promosi</h4>
                    <p style="margin: 0; font-size: 12px; color: #94a3b8; line-height: 1.5;">Cetak di brosur dan spanduk promosi</p>
                </div>
                <div class="use-case-card" style="text-align: center; padding: 20px; background: #f8fafc; border-radius: 10px; border: 1px solid #e2e8f0;">
                    <div class="use-case-icon" style="width: 48px; height: 48px; background: #fce7f3; border-radius: 10px; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 12px;">
                        <i class="fas fa-id-card" style="font-size: 20px; color: #ec4899;"></i>
                    </div>
                    <h4 style="margin: 0 0 6px 0; font-size: 14px; font-weight: 600; color: #0f172a;">Kartu Nama</h4>
                    <p style="margin: 0; font-size: 12px; color: #94a3b8; line-height: 1.5;">Tambahkan di kartu nama untuk kontak digital</p>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ════════════════════════════════════════════════
     POSTER HIDDEN — hanya untuk WhatsApp Share
     ════════════════════════════════════════════════ --}}
<div id="qr-poster-design" style="position:fixed; top:-9999px; left:-9999px; width:500px; height:550px; background:#ffffff; padding:25px; box-sizing:border-box; font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Helvetica,Arial,sans-serif; display:flex; flex-direction:column;">
    <div style="display:flex; align-items:center; gap:12px; margin-bottom:15px; flex-shrink:0;">
        <div style="display:flex; flex-direction:column;">
            <span style="font-size:22px; font-weight:700; color:#0066CC; letter-spacing:-0.5px; line-height:1.2;">payou.id</span>
            <span style="font-size:13px; color:#64748B; margin-top:2px;">Digital Business Card</span>
        </div>
    </div>
    {{-- Username Badge dihapus --}}
    <div style="display:flex; justify-content:center; align-items:center; margin-bottom:20px; background:linear-gradient(145deg,#ffffff,#f8fbff); padding:20px; border-radius:24px; box-shadow:0 15px 35px rgba(0,102,204,0.15); border:1px solid #e6f0ff; flex:1; position:relative;">
        {{-- Logo payou.id pill di tengah QR poster --}}
        <div style="position:absolute; top:50%; left:50%; transform:translate(-50%,-50%); width:72px; height:40px; background:#ffffff; border-radius:11px; display:flex; align-items:center; justify-content:center; box-shadow:0 4px 14px rgba(0,102,204,0.22); border:2px solid #e6f0ff; z-index:10; pointer-events:none;">
            <span style="color:#0066CC; font-weight:800; font-size:13px; letter-spacing:-0.3px; line-height:1;">payou<span style="color:#3b82f6;">.</span>id</span>
        </div>
        <div id="poster-qrcode" style="display:flex; justify-content:center; align-items:center;"></div>
    </div>
    <div style="text-align:center; margin-bottom:10px; flex-shrink:0;">
        <span style="color:#0066CC; font-weight:600; font-size:14px; padding:6px; display:inline-block;" id="poster-watermark">@{{ $userSlug ?? 'username' }}</span>
    </div>
    <div style="display:flex; justify-content:center; gap:30px; margin-bottom:10px; flex-shrink:0;">
        <div style="display:flex; align-items:center; gap:6px;">
            <i class="fas fa-shield-alt" style="color:#10b981; font-size:14px;"></i>
            <span style="color:#475569; font-size:12px; font-weight:500;">Secure</span>
        </div>
        <div style="display:flex; align-items:center; gap:6px;">
            <i class="fas fa-check-circle" style="color:#0066CC; font-size:14px;"></i>
            <span style="color:#475569; font-size:12px; font-weight:500;">Verified</span>
        </div>
        <div style="display:flex; align-items:center; gap:6px;">
            <i class="fas fa-clock" style="color:#f59e0b; font-size:14px;"></i>
            <span style="color:#475569; font-size:12px; font-weight:500;">24/7 Active</span>
        </div>
    </div>
    <div style="border-top:1px solid #E2E8F0; padding-top:10px; text-align:center; flex-shrink:0;">
        <span style="color:#94A3B8; font-size:10px;">© 2026 payou.id · All rights reserved</span>
    </div>
</div>

{{-- Libraries --}}
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

<style>
button { transition: all 0.2s ease; }
button:hover { transform: translateY(-1px); opacity: 0.9; }
button:active { transform: translateY(0); }

#qrcode-main { display: flex; justify-content: center; align-items: center; }
#qrcode-main img, #qrcode-main canvas { border-radius: 8px; width: 180px !important; height: 180px !important; }

.copy-success { background: #10b981 !important; }
.color-btn { outline: none; }
.color-btn.active {
    border-color: white !important;
    box-shadow: 0 0 0 3px rgba(0,0,0,0.18) !important;
    transform: scale(1.13) !important;
}

.share-loading { opacity: 0.7; pointer-events: none; position: relative; }
.share-loading::after {
    content: ''; position: absolute;
    width: 14px; height: 14px;
    border: 2px solid #ffffff; border-top-color: transparent;
    border-radius: 50%; animation: spin 0.8s linear infinite;
    right: 10px; top: 50%; transform: translateY(-50%);
}
@keyframes spin { to { transform: translateY(-50%) rotate(360deg); } }

@keyframes iconBounce { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-7px)} }
@keyframes iconPulse  { 0%,100%{transform:scale(1)}    50%{transform:scale(1.2)} }
.use-case-card:nth-child(1) .use-case-icon { animation: iconBounce 2s ease-in-out infinite 0s; }
.use-case-card:nth-child(2) .use-case-icon { animation: iconBounce 2s ease-in-out infinite 0.35s; }
.use-case-card:nth-child(3) .use-case-icon { animation: iconPulse  1.8s ease-in-out infinite 0.7s; }
.use-case-card:nth-child(4) .use-case-icon { animation: iconBounce 2s ease-in-out infinite 1.05s; }
.use-case-card { transition: box-shadow 0.3s ease, transform 0.3s ease; cursor: default; }
.use-case-card:hover { transform: translateY(-5px); box-shadow: 0 10px 28px rgba(0,0,0,0.10) !important; }
.use-case-card:hover .use-case-icon { animation: iconBounce 0.5s ease-in-out infinite !important; }

@media (max-width: 1024px) {
    div[style*="grid-template-columns: 1fr 1fr"] { grid-template-columns: 1fr !important; }
    div[style*="grid-template-columns: repeat(4, 1fr)"] { grid-template-columns: repeat(2, 1fr) !important; }
}
@media (max-width: 640px) {
    div[style*="grid-template-columns: 1fr 1fr 1fr"] { grid-template-columns: 1fr !important; }
}
</style>

<script>
let currentUrl      = '{{ url("/" . ($userSlug ?? "username")) }}';
let currentUsername = '{{ $userSlug ?? "username" }}';
let currentColor    = '#0066CC';

const COLOR_NAMES = {
    '#0066CC': 'Biru Payou',
    '#111827': 'Hitam',
    '#16a34a': 'Hijau',
    '#dc2626': 'Merah',
    '#7c3aed': 'Ungu',
    '#ea580c': 'Oranye',
};

document.addEventListener('DOMContentLoaded', function () {
    generateQRCode();
    document.getElementById('poster-watermark').textContent = '@' + currentUsername;
});

function generateQRCode() {
    const container = document.getElementById('qrcode-main');
    container.innerHTML = '';
    new QRCode(container, {
        text: currentUrl,
        width: 180, height: 180,
        colorDark: currentColor,
        colorLight: '#ffffff',
        correctLevel: QRCode.CorrectLevel.H,
    });
}

function changeColor(color, el) {
    currentColor = color;
    document.querySelectorAll('.color-btn').forEach(btn => {
        btn.classList.remove('active');
        btn.style.borderColor = '#e2e8f0';
        btn.style.boxShadow   = '';
        btn.style.transform   = '';
    });
    if (el && el.tagName === 'BUTTON') el.classList.add('active');
    const name    = COLOR_NAMES[color.toUpperCase()] || COLOR_NAMES[color] || 'Kustom';
    const labelEl = document.getElementById('color-label');
    labelEl.textContent = `${color.toUpperCase()} (${name})`;
    labelEl.style.color = color;
    generateQRCode();
}

async function copyLink() {
    const btn = document.getElementById('copy-btn');
    const ok  = () => {
        btn.innerHTML = '<i class="fas fa-check" style="font-size:12px;"></i><span>Tersalin!</span>';
        btn.classList.add('copy-success');
        setTimeout(() => {
            btn.innerHTML = '<i class="far fa-copy" style="font-size:12px;"></i><span>Salin</span>';
            btn.classList.remove('copy-success');
            btn.style.background = '#3b82f6';
        }, 2000);
    };
    try {
        await navigator.clipboard.writeText(currentUrl); ok();
    } catch {
        try {
            const ta = document.createElement('textarea');
            ta.value = currentUrl; ta.style.cssText = 'position:fixed;opacity:0;';
            document.body.appendChild(ta); ta.select();
            document.execCommand('copy'); document.body.removeChild(ta); ok();
        } catch { alert('Gagal menyalin. Link: ' + currentUrl); }
    }
}

function downloadQR() {
    const srcCanvas = document.querySelector('#qrcode-main canvas');
    if (!srcCanvas) { alert('QR Code belum siap, coba lagi.'); return; }

    const QR_SIZE = 400;
    const PAD     = 20;
    const LABEL_H = 40;
    const W = QR_SIZE + PAD * 2;
    const H = QR_SIZE + PAD * 2 + LABEL_H;

    const out = document.createElement('canvas');
    out.width = W; out.height = H;
    const ctx = out.getContext('2d');

    ctx.fillStyle = '#ffffff';
    ctx.fillRect(0, 0, W, H);
    ctx.drawImage(srcCanvas, PAD, PAD, QR_SIZE, QR_SIZE);

    // ── Logo payou.id — pill landscape di tengah ──
    const BOX_W = 110; const BOX_H = 46;
    const logoX = (W - BOX_W) / 2;
    const logoY = PAD + (QR_SIZE - BOX_H) / 2;
    ctx.shadowColor = 'rgba(0,102,204,0.22)'; ctx.shadowBlur = 16; ctx.shadowOffsetY = 3;
    ctx.fillStyle = '#ffffff';
    ctx.beginPath(); ctx.roundRect(logoX, logoY, BOX_W, BOX_H, 12); ctx.fill();
    ctx.shadowColor = 'transparent'; ctx.shadowBlur = 0; ctx.shadowOffsetY = 0;
    // border tipis biru muda
    ctx.strokeStyle = '#e6f0ff'; ctx.lineWidth = 2;
    ctx.beginPath(); ctx.roundRect(logoX, logoY, BOX_W, BOX_H, 12); ctx.stroke();
    // "payou" biru tua
    ctx.fillStyle = '#0066CC'; ctx.font = 'bold 18px Arial, sans-serif';
    ctx.textAlign = 'center'; ctx.textBaseline = 'middle';
    ctx.fillText('payou', W / 2 - 12, logoY + BOX_H / 2);
    // titik biru muda
    ctx.fillStyle = '#3b82f6';
    ctx.fillText('.', W / 2 + 20, logoY + BOX_H / 2);
    // "id" biru tua
    ctx.fillStyle = '#0066CC';
    ctx.fillText('id', W / 2 + 32, logoY + BOX_H / 2);
    ctx.textBaseline = 'alphabetic';

    // watermark @username — ikut warna QR aktif
    ctx.fillStyle = currentColor;
    ctx.font      = 'bold 15px Arial, sans-serif';
    ctx.textAlign = 'center';
    ctx.fillText('@' + currentUsername, W / 2, PAD + QR_SIZE + 26);

    const link    = document.createElement('a');
    link.download = `payou-${currentUsername}.png`;
    link.href     = out.toDataURL('image/png', 1.0);
    document.body.appendChild(link); link.click(); document.body.removeChild(link);
}

function printQR() {
    const srcCanvas = document.querySelector('#qrcode-main canvas');
    if (!srcCanvas) { alert('QR Code belum siap.'); return; }
    const dataUrl = srcCanvas.toDataURL('image/png', 1.0);
    const win = window.open('', '_blank', 'width=520,height=620');
    win.document.write(`<!DOCTYPE html>
<html><head><title>Print QR Code — @${currentUsername}</title>
<style>
  *{margin:0;padding:0;box-sizing:border-box;}
  body{display:flex;flex-direction:column;align-items:center;justify-content:center;min-height:100vh;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Arial,sans-serif;background:#fff;padding:32px;}
  .logo{font-size:22px;font-weight:700;color:#0066CC;letter-spacing:-0.5px;text-align:center;}
  .sub{font-size:12px;color:#64748B;text-align:center;margin-top:4px;margin-bottom:20px;}
  .qr-wrap{border:2px solid #e6f0ff;border-radius:16px;padding:24px;background:linear-gradient(145deg,#fff,#f8fbff);box-shadow:0 8px 24px rgba(0,102,204,0.10);margin-bottom:16px;}
  .qr-wrap img{display:block;width:240px;height:240px;border-radius:6px;}
  .username{font-size:15px;font-weight:700;color:${currentColor};text-align:center;margin-bottom:6px;}
  .url{font-size:11px;color:#94a3b8;text-align:center;}
  @media print{button{display:none!important;}}
</style></head><body>
  <div class="logo">payou.id</div>
  <div class="sub">Digital Business Card</div>
  <div class="qr-wrap"><img src="${dataUrl}" alt="QR Code"></div>
  <div class="username">@${currentUsername}</div>
  <div class="url">${currentUrl}</div>
  <script>window.onload=function(){setTimeout(function(){window.print();},400);};<\/script>
</body></html>`);
    win.document.close();
}

async function shareWhatsApp() {
    const shareBtn          = document.getElementById('whatsapp-share-btn');
    const posterDesign      = document.getElementById('qr-poster-design');
    const posterQrContainer = document.getElementById('poster-qrcode');

    shareBtn.classList.add('share-loading');
    shareBtn.innerHTML = '<span>Menyiapkan...</span>';

    try {
        posterQrContainer.innerHTML = '';
        document.getElementById('poster-watermark').textContent = '@' + currentUsername;

        new QRCode(posterQrContainer, {
            text: currentUrl,
            width: 220, height: 220,
            colorDark: '#0066CC',
            colorLight: '#ffffff',
            correctLevel: QRCode.CorrectLevel.H,
        });

        await new Promise(resolve => setTimeout(resolve, 500));

        const canvas = await html2canvas(posterDesign, {
            scale: 2, backgroundColor: '#ffffff',
            allowTaint: false, useCORS: true, logging: false,
            windowWidth: 500, windowHeight: 550,
        });

        const message = `Halo 👋\n\nSaya ingin berbagi kartu digital Payou.id saya:\n${currentUrl}\n\nSilakan scan QR code di bawah ini untuk terhubung dengan saya.\n\nTerima kasih! 🙏`;

        if (navigator.canShare && navigator.canShare({ files: [new File([], 'test.png')] })) {
            const blob = await new Promise(resolve => canvas.toBlob(resolve, 'image/png', 1.0));
            const file = new File([blob], `payou-${currentUsername}.png`, { type: 'image/png' });
            await navigator.share({ title: `Payou.id - @${currentUsername}`, text: message, files: [file] });
        } else {
            const link    = document.createElement('a');
            link.download = `payou-${currentUsername}.png`;
            link.href     = canvas.toDataURL('image/png');
            document.body.appendChild(link); link.click(); document.body.removeChild(link);
            setTimeout(() => {
                window.open(`https://web.whatsapp.com/send?text=${encodeURIComponent(message)}`, '_blank');
            }, 1000);
            alert('✓ Gambar sudah didownload\n\nSilakan kirim gambar + pesan ini ke kontak Anda di WhatsApp Web');
        }
    } catch (error) {
        console.error('Error:', error);
        if (error.name !== 'AbortError' && !error.message?.includes('cancel')) {
            window.open(`https://wa.me/?text=${encodeURIComponent(`Halo,\n\nSilakan kunjungi profil Payou.id saya:\n${currentUrl}`)}`, '_blank');
        }
    } finally {
        shareBtn.classList.remove('share-loading');
        shareBtn.innerHTML = '<i class="fab fa-whatsapp" style="font-size:12px;"></i><span>WhatsApp</span>';
    }
}

async function shareQR() {
    if (navigator.share) {
        try {
            await navigator.share({ title: 'QR Code Payou.id', text: 'Scan QR Code ini untuk mengunjungi halaman saya', url: currentUrl });
        } catch (err) { /* cancelled */ }
    } else {
        copyLink();
    }
}
</script>

@endsection