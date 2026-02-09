@extends('layouts.dashboard')
@section('title', 'QR Code')

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
                    <h1 style="margin: 0; font-size: 24px; font-weight: 600; color: #0f172a;">QR Code Saya</h1>
                    <p style="margin: 0; font-size: 14px; color: #94a3b8;">Bagikan QR Code untuk mempromosikan halaman Anda</p>
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
                <div style="background: #f8fafc; border: 2px dashed #e2e8f0; border-radius: 12px; padding: 32px; margin-bottom: 20px; display: flex; justify-content: center; align-items: center; min-height: 280px;">
                    <div id="qrcode-main"></div>
                </div>

                {{-- URL Display --}}
                <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 12px 16px; margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
                    <i class="fas fa-link" style="color: #94a3b8; font-size: 14px;"></i>
                    <p style="margin: 0; font-size: 14px; color: #475569; font-weight: 500; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; flex: 1;" id="qr-url-text">
                        payou.id/{{ $userSlug ?? 'username' }}
                    </p>
                </div>

                {{-- Action Buttons - 3 BUTTONS --}}
                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 10px; margin-bottom: 16px;">
                    <button onclick="copyLink()" id="copy-btn" style="padding: 12px 16px; background: #3b82f6; color: white; border: none; border-radius: 8px; font-size: 13px; font-weight: 500; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 6px; transition: all 0.2s;">
                        <i class="far fa-copy" style="font-size: 12px;"></i>
                        <span>Salin</span>
                    </button>
                    
                    <button onclick="downloadQR()" style="padding: 12px 16px; background: #ffffff; color: #475569; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 13px; font-weight: 500; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 6px; transition: all 0.2s;">
                        <i class="fas fa-download" style="font-size: 12px;"></i>
                        <span>Download</span>
                    </button>

                    <button onclick="shareWhatsApp()" style="padding: 12px 16px; background: #25D366; color: white; border: none; border-radius: 8px; font-size: 13px; font-weight: 500; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 6px; transition: all 0.2s;">
                        <i class="fab fa-whatsapp" style="font-size: 12px;"></i>
                        <span>Share</span>
                    </button>
                </div>

                {{-- Share Button --}}
                <button onclick="shareQR()" style="width: 100%; padding: 12px 20px; background: #f8fafc; color: #475569; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 14px; font-weight: 500; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px; transition: all 0.2s;">
                    <i class="fas fa-share-alt" style="font-size: 13px;"></i>
                    <span>Bagikan QR Code</span>
                </button>
            </div>

            {{-- Right Column - Info & Customization --}}
            <div style="display: flex; flex-direction: column; gap: 20px;">
                
                <!-- {{-- Statistics Card --}}
                <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                    <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 16px;">
                        <div style="width: 36px; height: 36px; background: #f1f5f9; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-chart-line" style="font-size: 14px; color: #475569;"></i>
                        </div>
                        <h3 style="margin: 0; font-size: 16px; font-weight: 600; color: #0f172a;">Statistik QR Code</h3>
                    </div>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                        <div style="background: #f8fafc; border-radius: 8px; padding: 16px;">
                            <p style="margin: 0 0 4px 0; font-size: 12px; color: #94a3b8; font-weight: 500;">Total Scan</p>
                            <p style="margin: 0; font-size: 24px; font-weight: 600; color: #0f172a;">{{ $totalScans ?? 0 }}</p>
                        </div>
                        <div style="background: #f8fafc; border-radius: 8px; padding: 16px;">
                            <p style="margin: 0 0 4px 0; font-size: 12px; color: #94a3b8; font-weight: 500;">Scan Hari Ini</p>
                            <p style="margin: 0; font-size: 24px; font-weight: 600; color: #0f172a;">{{ $todayScans ?? 0 }}</p>
                        </div>
                    </div>
                </div> -->

                {{-- Customization Card --}}
                <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                    <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 16px;">
                        <div style="width: 36px; height: 36px; background: #f1f5f9; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-palette" style="font-size: 14px; color: #475569;"></i>
                        </div>
                        <h3 style="margin: 0; font-size: 16px; font-weight: 600; color: #0f172a;">Kustomisasi QR Code</h3>
                    </div>

                    {{-- Size Options --}}
                    <div style="margin-bottom: 16px;">
                        <label style="display: block; font-size: 13px; font-weight: 500; color: #475569; margin-bottom: 8px;">Ukuran QR Code</label>
                        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 8px;">
                            <button onclick="changeQRSize(180)" class="size-btn active" data-size="180" style="padding: 10px; background: #3b82f6; color: white; border: 1px solid #3b82f6; border-radius: 6px; font-size: 13px; font-weight: 500; cursor: pointer; transition: all 0.2s;">
                                Kecil
                            </button>
                            <button onclick="changeQRSize(240)" class="size-btn" data-size="240" style="padding: 10px; background: #ffffff; color: #475569; border: 1px solid #e2e8f0; border-radius: 6px; font-size: 13px; font-weight: 500; cursor: pointer; transition: all 0.2s;">
                                Sedang
                            </button>
                            <button onclick="changeQRSize(300)" class="size-btn" data-size="300" style="padding: 10px; background: #ffffff; color: #475569; border: 1px solid #e2e8f0; border-radius: 6px; font-size: 13px; font-weight: 500; cursor: pointer; transition: all 0.2s;">
                                Besar
                            </button>
                        </div>
                    </div>

                    {{-- Format Options --}}
                    <div style="margin-bottom: 16px;">
                        <label style="display: block; font-size: 13px; font-weight: 500; color: #475569; margin-bottom: 8px;">Format Download</label>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px;">
                            <button onclick="setFormat('png')" class="format-btn active" data-format="png" style="padding: 10px; background: #3b82f6; color: white; border: 1px solid #3b82f6; border-radius: 6px; font-size: 13px; font-weight: 500; cursor: pointer; transition: all 0.2s;">
                                <i class="far fa-file-image" style="font-size: 12px; margin-right: 4px;"></i> PNG
                            </button>
                            <button onclick="setFormat('svg')" class="format-btn" data-format="svg" style="padding: 10px; background: #ffffff; color: #475569; border: 1px solid #e2e8f0; border-radius: 6px; font-size: 13px; font-weight: 500; cursor: pointer; transition: all 0.2s;">
                                <i class="far fa-file-code" style="font-size: 12px; margin-right: 4px;"></i> SVG
                            </button>
                        </div>
                    </div>

                    {{-- Quality Info --}}
                    <div style="background: #f0f9ff; border: 1px solid #bae6fd; border-radius: 8px; padding: 12px; display: flex; gap: 10px;">
                        <i class="fas fa-info-circle" style="color: #0284c7; font-size: 14px; margin-top: 2px;"></i>
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
                            <p style="margin: 0; font-size: 13px; color: #475569; line-height: 1.6;">Gunakan ukuran minimal 3x3 cm untuk hasil scan terbaik</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Use Cases Section --}}
        <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
            <h3 style="margin: 0 0 20px 0; font-size: 16px; font-weight: 600; color: #0f172a;">Ide Pemanfaatan QR Code</h3>
            
            <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px;">
                {{-- Use Case 1 --}}
                <div style="text-align: center; padding: 20px; background: #f8fafc; border-radius: 10px; border: 1px solid #e2e8f0;">
                    <div style="width: 48px; height: 48px; background: #eff6ff; border-radius: 10px; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 12px;">
                        <i class="fas fa-utensils" style="font-size: 20px; color: #3b82f6;"></i>
                    </div>
                    <h4 style="margin: 0 0 6px 0; font-size: 14px; font-weight: 600; color: #0f172a;">Menu Digital</h4>
                    <p style="margin: 0; font-size: 12px; color: #94a3b8; line-height: 1.5;">Letakkan di meja restoran untuk akses menu online</p>
                </div>

                {{-- Use Case 2 --}}
                <div style="text-align: center; padding: 20px; background: #f8fafc; border-radius: 10px; border: 1px solid #e2e8f0;">
                    <div style="width: 48px; height: 48px; background: #f0fdf4; border-radius: 10px; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 12px;">
                        <i class="fas fa-store" style="font-size: 20px; color: #10b981;"></i>
                    </div>
                    <h4 style="margin: 0 0 6px 0; font-size: 14px; font-weight: 600; color: #0f172a;">Etalase Toko</h4>
                    <p style="margin: 0; font-size: 12px; color: #94a3b8; line-height: 1.5;">Tempelkan di etalase untuk katalog produk digital</p>
                </div>

                {{-- Use Case 3 --}}
                <div style="text-align: center; padding: 20px; background: #f8fafc; border-radius: 10px; border: 1px solid #e2e8f0;">
                    <div style="width: 48px; height: 48px; background: #fef3c7; border-radius: 10px; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 12px;">
                        <i class="fas fa-bullhorn" style="font-size: 20px; color: #f59e0b;"></i>
                    </div>
                    <h4 style="margin: 0 0 6px 0; font-size: 14px; font-weight: 600; color: #0f172a;">Media Promosi</h4>
                    <p style="margin: 0; font-size: 12px; color: #94a3b8; line-height: 1.5;">Cetak di brosur dan spanduk promosi</p>
                </div>

                {{-- Use Case 4 --}}
                <div style="text-align: center; padding: 20px; background: #f8fafc; border-radius: 10px; border: 1px solid #e2e8f0;">
                    <div style="width: 48px; height: 48px; background: #fce7f3; border-radius: 10px; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 12px;">
                        <i class="fas fa-id-card" style="font-size: 20px; color: #ec4899;"></i>
                    </div>
                    <h4 style="margin: 0 0 6px 0; font-size: 14px; font-weight: 600; color: #0f172a;">Kartu Nama</h4>
                    <p style="margin: 0; font-size: 12px; color: #94a3b8; line-height: 1.5;">Tambahkan di kartu nama untuk kontak digital</p>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- QR Code Library --}}
<script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>

<style>
/* Button Hover Effects */
button {
    transition: all 0.2s ease;
}

button:hover {
    transform: translateY(-1px);
    opacity: 0.9;
}

button:active {
    transform: translateY(0);
}

/* Primary Button */
button[onclick="copyLink()"]:hover {
    background: #2563eb !important;
    box-shadow: 0 2px 8px rgba(59, 130, 246, 0.2);
}

/* Secondary Buttons */
button[onclick="downloadQR()"]:hover,
button[onclick="shareQR()"]:hover {
    background: #f1f5f9 !important;
    border-color: #cbd5e1 !important;
}

/* WhatsApp Button Hover */
button[onclick="shareWhatsApp()"]:hover {
    background: #20BA5A !important;
    box-shadow: 0 2px 8px rgba(37, 211, 102, 0.3);
}

/* Back Button */
a[href*="dashboard"]:hover {
    background: #f8fafc !important;
    border-color: #cbd5e1 !important;
}

/* Copy Success State */
.copy-success {
    background: #10b981 !important;
}

/* Size Buttons */
.size-btn.active {
    background: #3b82f6 !important;
    color: white !important;
    border-color: #3b82f6 !important;
}

.size-btn:not(.active):hover {
    background: #f1f5f9 !important;
    border-color: #cbd5e1 !important;
}

/* Format Buttons */
.format-btn.active {
    background: #3b82f6 !important;
    color: white !important;
    border-color: #3b82f6 !important;
}

.format-btn:not(.active):hover {
    background: #f1f5f9 !important;
    border-color: #cbd5e1 !important;
}

/* QR Code Styling */
#qrcode-main {
    display: flex;
    justify-content: center;
    align-items: center;
}

#qrcode-main img,
#qrcode-main canvas {
    border-radius: 8px;
}

/* Responsive */
@media (max-width: 1024px) {
    div[style*="grid-template-columns: 1fr 1fr"] {
        grid-template-columns: 1fr !important;
    }
    
    div[style*="grid-template-columns: repeat(4, 1fr)"] {
        grid-template-columns: repeat(2, 1fr) !important;
    }
}

@media (max-width: 640px) {
    div[style*="grid-template-columns: repeat(3, 1fr)"],
    div[style*="grid-template-columns: 1fr 1fr 1fr"] {
        grid-template-columns: 1fr !important;
    }
}
</style>

<script>
// Global Variables
let qrCodeInstance = null;
let currentUrl = 'https://payou.id/{{ $userSlug ?? 'username' }}';
let currentSize = 180;
let currentFormat = 'png';

// Initialize QR Code on Page Load
document.addEventListener('DOMContentLoaded', function() {
    generateQRCode(currentSize);
});

/**
 * Generate QR Code
 */
function generateQRCode(size = 180) {
    const container = document.getElementById('qrcode-main');
    container.innerHTML = '';
    
    qrCodeInstance = new QRCode(container, {
        text: currentUrl,
        width: size,
        height: size,
        colorDark: "#000000",
        colorLight: "#ffffff",
        correctLevel: QRCode.CorrectLevel.H,
    });
    
    currentSize = size;
    console.log('QR Code generated:', { url: currentUrl, size: size });
}

/**
 * Copy Link to Clipboard
 */
async function copyLink() {
    const copyBtn = document.getElementById('copy-btn');
    
    try {
        await navigator.clipboard.writeText(currentUrl);
        
        // Success feedback
        copyBtn.innerHTML = '<i class="fas fa-check" style="font-size: 12px;"></i><span>Tersalin!</span>';
        copyBtn.classList.add('copy-success');
        
        setTimeout(() => {
            copyBtn.innerHTML = '<i class="far fa-copy" style="font-size: 12px;"></i><span>Salin</span>';
            copyBtn.classList.remove('copy-success');
        }, 2000);
        
    } catch (err) {
        // Fallback
        const textArea = document.createElement('textarea');
        textArea.value = currentUrl;
        textArea.style.position = 'fixed';
        textArea.style.opacity = '0';
        document.body.appendChild(textArea);
        textArea.select();
        document.execCommand('copy');
        document.body.removeChild(textArea);
        
        copyBtn.innerHTML = '<i class="fas fa-check" style="font-size: 12px;"></i><span>Tersalin!</span>';
        copyBtn.classList.add('copy-success');
        
        setTimeout(() => {
            copyBtn.innerHTML = '<i class="far fa-copy" style="font-size: 12px;"></i><span>Salin</span>';
            copyBtn.classList.remove('copy-success');
        }, 2000);
    }
}

/**
 * Download QR Code
 */
function downloadQR() {
    try {
        const container = document.getElementById('qrcode-main');
        const canvas = container.querySelector('canvas');
        
        if (!canvas) {
            console.error('QR Code canvas not found');
            return;
        }
        
        const url = canvas.toDataURL('image/png', 1.0);
        const link = document.createElement('a');
        const username = currentUrl.split('/').pop();
        
        link.download = `qrcode-payou-${username}-${currentSize}x${currentSize}.${currentFormat}`;
        link.href = url;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        
        console.log('QR Code downloaded');
    } catch (err) {
        console.error('Download failed:', err);
        alert('Gagal mendownload QR Code');
    }
}

/**
 * Share to WhatsApp
 */
function shareWhatsApp() {
    // Format pesan WhatsApp
    const message = `Halo,
Saya ingin membagikan tautan resmi saya melalui pesan ini:
${currentUrl}
Silakan diakses sesuai kebutuhan.
Terima kasih.`;
    
    // Encode message untuk URL
    const encodedMessage = encodeURIComponent(message);
    
    // WhatsApp Web/App URL
    const whatsappUrl = `https://wa.me/?text=${encodedMessage}`;
    
    // Buka WhatsApp di tab baru
    window.open(whatsappUrl, '_blank');
    
    console.log('Sharing to WhatsApp:', currentUrl);
}

/**
 * Share QR Code
 */
async function shareQR() {
    if (navigator.share) {
        try {
            await navigator.share({
                title: 'QR Code Payou.id',
                text: 'Scan QR Code ini untuk mengunjungi halaman saya di Payou.id',
                url: currentUrl
            });
        } catch (err) {
            console.log('Share cancelled or failed:', err);
        }
    } else {
        // Fallback: copy link
        copyLink();
    }
}

/**
 * Change QR Size
 */
function changeQRSize(size) {
    // Update active button
    document.querySelectorAll('.size-btn').forEach(btn => {
        btn.classList.remove('active');
        btn.style.background = '#ffffff';
        btn.style.color = '#475569';
        btn.style.borderColor = '#e2e8f0';
    });
    
    event.target.classList.add('active');
    event.target.style.background = '#3b82f6';
    event.target.style.color = 'white';
    event.target.style.borderColor = '#3b82f6';
    
    // Regenerate QR Code
    generateQRCode(size);
}

/**
 * Set Download Format
 */
function setFormat(format) {
    currentFormat = format;
    
    // Update active button
    document.querySelectorAll('.format-btn').forEach(btn => {
        btn.classList.remove('active');
        btn.style.background = '#ffffff';
        btn.style.color = '#475569';
        btn.style.borderColor = '#e2e8f0';
    });
    
    event.target.classList.add('active');
    event.target.style.background = '#3b82f6';
    event.target.style.color = 'white';
    event.target.style.borderColor = '#3b82f6';
}
</script>

@endsection