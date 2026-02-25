{{-- 
    QR CODE MODAL COMPONENT - MINIMALIST MODERN PROFESSIONAL
    Selaras dengan Dashboard Payou.id
    
    Cara Pakai:
    1. Include di layout: @include('components.qr-modal')
    2. Panggil dari tombol: onclick="openQRModal('{{ $userSlug }}')"
--}}

<!-- QR CODE MODAL -->
<div id="qr-modal" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.4); z-index: 99999; align-items: center; justify-content: center; backdrop-filter: blur(4px);">
    <div style="background: #ffffff; border-radius: 12px; width: 90%; max-width: 380px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); overflow: hidden;">
        
        <!-- Header -->
        <div style="padding: 20px 24px; border-bottom: 1px solid #f1f5f9; display: flex; align-items: center; justify-content: space-between;">
            <div style="display: flex; align-items: center; gap: 10px;">
                <div style="width: 36px; height: 36px; background: #f1f5f9; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                    <i class="fas fa-qrcode" style="font-size: 16px; color: #475569;"></i>
                </div>
                <div>
                    <h3 style="margin: 0; font-size: 16px; font-weight: 600; color: #0f172a;">QR Code</h3>
                    <p style="margin: 0; font-size: 12px; color: #94a3b8;">Scan untuk mengunjungi halaman</p>
                </div>
            </div>
            <button onclick="closeQRModal()" style="background: transparent; border: none; color: #94a3b8; font-size: 20px; cursor: pointer; padding: 4px; width: 32px; height: 32px; border-radius: 6px; display: flex; align-items: center; justify-content: center; transition: all 0.2s;">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <!-- Body -->
        <div style="padding: 24px;">
            
            <!-- QR Code Container -->
            <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 20px; margin-bottom: 16px; display: flex; justify-content: center;">
                <div id="qrcode"></div>
            </div>

            <!-- URL Display -->
            <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 10px 12px; margin-bottom: 16px; display: flex; align-items: center; gap: 8px;">
                <i class="fas fa-link" style="color: #94a3b8; font-size: 13px;"></i>
                <p style="margin: 0; font-size: 13px; color: #475569; font-weight: 500; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; flex: 1;" id="qr-url-display">
                    
                </p>
            </div>

            <!-- Action Buttons -->
            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 8px;">
                <button onclick="copyQRLink()" id="copy-link-btn" style="padding: 10px 12px; background: #3b82f6; color: white; border: none; border-radius: 8px; font-size: 12px; font-weight: 500; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 6px; transition: all 0.2s;">
                    <i class="far fa-copy" style="font-size: 11px;"></i>
                    <span>Salin</span>
                </button>
                
                <button onclick="downloadQRCode()" style="padding: 10px 12px; background: #ffffff; color: #475569; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 12px; font-weight: 500; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 6px; transition: all 0.2s;">
                    <i class="fas fa-download" style="font-size: 11px;"></i>
                    <span>Download</span>
                </button>

                <button onclick="shareToWhatsApp()" style="padding: 10px 12px; background: #25D366; color: white; border: none; border-radius: 8px; font-size: 12px; font-weight: 500; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 6px; transition: all 0.2s;">
                    <i class="fab fa-whatsapp" style="font-size: 11px;"></i>
                    <span>Whatsapp</span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- QR Code Library -->
<script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>



<style>
/* Hover Effects - Subtle & Professional */
#qr-modal button:hover {
    opacity: 0.9;
    transform: translateY(-1px);
}

#qr-modal button:active {
    transform: translateY(0);
}

/* Close Button Hover */
#qr-modal button[onclick="closeQRModal()"]:hover {
    background: #f1f5f9;
    color: #475569;
}

/* Primary Button Hover */
#qr-modal button[onclick="copyQRLink()"]:hover {
    background: #2563eb;
    box-shadow: 0 2px 8px rgba(59, 130, 246, 0.2);
}

/* Secondary Button Hover */
#qr-modal button[onclick="downloadQRCode()"]:hover {
    background: #f8fafc;
    border-color: #cbd5e1;
}

/* WhatsApp Button Hover */
#qr-modal button[onclick="shareToWhatsApp()"]:hover {
    background: #20BA5A;
    box-shadow: 0 2px 8px rgba(37, 211, 102, 0.3);
}

/* Copy Button Success State */
.copy-success {
    background: #10b981 !important;
}

/* QR Code Styling */
#qrcode {
    display: flex;
    justify-content: center;
    align-items: center;
}

#qrcode img {
    border-radius: 6px;
}

#qrcode canvas {
    border-radius: 6px;
}

/* Responsive */
@media (max-width: 480px) {
    #qr-modal > div {
        width: 95%;
        max-width: 95%;
    }
    
    #qr-modal .header h3 {
        font-size: 15px;
    }
    
    #qr-modal button {
        font-size: 11px;
        padding: 9px 10px;
    }
}

/* Smooth Animations */
@keyframes fadeIn {
    from {
        opacity: 0;
        transform: scale(0.95);
    }
    to {
        opacity: 1;
        transform: scale(1);
    }
}

#qr-modal[style*="display: flex"] > div {
    animation: fadeIn 0.2s ease-out;
}
</style>

<script>
// Global variables
let currentQRCode = null;
let currentUrl = '';

/**
 * Open QR Modal
 * @param {string} username - Username untuk generate URL
 */
function openQRModal(username) {
    const modal = document.getElementById('qr-modal');
    const qrcodeContainer = document.getElementById('qrcode');
    const urlDisplay = document.getElementById('qr-url-display');
    
    // Generate URL pakai url() helper Laravel — otomatis ikut APP_URL di .env
    currentUrl = '{{ url('/') }}' + '/' + username;
    
    // Tampilkan URL persis sama dengan yang di-generate QR
    urlDisplay.textContent = currentUrl;
    
    // Clear previous QR code
    qrcodeContainer.innerHTML = '';
    
    // Generate new QR code
    currentQRCode = new QRCode(qrcodeContainer, {
        text: currentUrl,
        width: 180,
        height: 180,
        colorDark: "#000000",
        colorLight: "#ffffff",
        correctLevel: QRCode.CorrectLevel.H,
    });
    
    // Show modal
    modal.style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

/**
 * Close QR Modal
 */
function closeQRModal() {
    const modal = document.getElementById('qr-modal');
    modal.style.display = 'none';
    document.body.style.overflow = '';
    
    // Reset copy button
    const copyBtn = document.getElementById('copy-link-btn');
    copyBtn.innerHTML = '<i class="far fa-copy" style="font-size: 11px;"></i><span>Salin</span>';
    copyBtn.classList.remove('copy-success');
}

/**
 * Copy QR Link to Clipboard
 */
async function copyQRLink() {
    const copyBtn = document.getElementById('copy-link-btn');
    
    try {
        await navigator.clipboard.writeText(currentUrl);
        
        copyBtn.innerHTML = '<i class="fas fa-check" style="font-size: 11px;"></i><span>Tersalin!</span>';
        copyBtn.classList.add('copy-success');
        
        setTimeout(() => {
            copyBtn.innerHTML = '<i class="far fa-copy" style="font-size: 11px;"></i><span>Salin</span>';
            copyBtn.classList.remove('copy-success');
        }, 2000);
    } catch (err) {
        try {
            const textArea = document.createElement('textarea');
            textArea.value = currentUrl;
            textArea.style.position = 'fixed';
            textArea.style.opacity = '0';
            document.body.appendChild(textArea);
            textArea.select();
            document.execCommand('copy');
            document.body.removeChild(textArea);
            
            copyBtn.innerHTML = '<i class="fas fa-check" style="font-size: 11px;"></i><span>Tersalin!</span>';
            copyBtn.classList.add('copy-success');
            
            setTimeout(() => {
                copyBtn.innerHTML = '<i class="far fa-copy" style="font-size: 11px;"></i><span>Salin</span>';
                copyBtn.classList.remove('copy-success');
            }, 2000);
        } catch (fallbackErr) {
            alert('Gagal menyalin. Link: ' + currentUrl);
        }
    }
}

/**
 * Download QR Code as PNG
 */
function downloadQRCode() {
    try {
        const qrcodeContainer = document.getElementById('qrcode');
        const canvas = qrcodeContainer.querySelector('canvas');
        
        if (!canvas) return;
        
        const url = canvas.toDataURL('image/png', 1.0);
        const link = document.createElement('a');
        const username = currentUrl.split('/').pop();
        
        link.download = `qrcode-${username}.png`;
        link.href = url;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    } catch (err) {
        alert('Gagal mendownload QR Code');
    }
}

/**
 * Share to WhatsApp
 */
function shareToWhatsApp() {
    const message = `Halo,\nSaya ingin membagikan tautan resmi saya melalui pesan ini:\n${currentUrl}\nSilakan diakses sesuai kebutuhan.\nTerima kasih.`;
    const whatsappUrl = `https://wa.me/?text=${encodeURIComponent(message)}`;
    window.open(whatsappUrl, '_blank');
}

// Close modal when clicking outside
document.addEventListener('click', function(event) {
    const modal = document.getElementById('qr-modal');
    if (event.target === modal) closeQRModal();
});

// Close modal with ESC key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        const modal = document.getElementById('qr-modal');
        if (modal && modal.style.display === 'flex') closeQRModal();
    }
});
</script>