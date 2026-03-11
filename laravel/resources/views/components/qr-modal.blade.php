{{-- 
    QR CODE MODAL COMPONENT
    Include di layout: @include('components.qr-modal')
    Panggil dari tombol: onclick="openQRModal('{{ $userSlug }}')"
--}}

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

@push('modals')

{{-- OVERLAY --}}
<div id="qrModalOverlay"
     class="fixed inset-0 hidden"
     style="z-index:9990; background:rgba(15,23,42,0.5); backdrop-filter:blur(5px); -webkit-backdrop-filter:blur(5px);
            opacity:0; transition:opacity 0.2s ease;"
     onclick="closeQRModal()">
</div>

{{-- MODAL --}}
<div id="qr-modal"
     class="fixed inset-0 hidden"
     style="z-index:9999; display:none; align-items:center; justify-content:center; padding:16px; pointer-events:none;">

    <div id="qrModalCard"
         style="background:#ffffff; border-radius:12px; width:90%; max-width:380px;
                box-shadow:0 4px 20px rgba(0,0,0,0.08); overflow:hidden; pointer-events:auto;
                opacity:0; transform:translateY(20px);
                transition:opacity 0.2s ease, transform 0.2s ease;">

        <!-- Header -->
        <div style="padding:20px 24px; border-bottom:1px solid #f1f5f9; display:flex; align-items:center; justify-content:space-between;">
            <div style="display:flex; align-items:center; gap:10px;">
                <div style="width:36px; height:36px; background:#f1f5f9; border-radius:8px; display:flex; align-items:center; justify-content:center;">
                    <i class="fas fa-qrcode" style="font-size:16px; color:#475569;"></i>
                </div>
                <div>
                    <h3 style="margin:0; font-size:16px; font-weight:600; color:#0f172a;">QR Code</h3>
                    <p style="margin:0; font-size:12px; color:#94a3b8;">Scan untuk mengunjungi halaman</p>
                </div>
            </div>
            <button onclick="closeQRModal()"
                    style="background:transparent; border:none; color:#94a3b8; font-size:20px; cursor:pointer;
                           padding:4px; width:32px; height:32px; border-radius:6px;
                           display:flex; align-items:center; justify-content:center; transition:all 0.2s;"
                    onmouseenter="this.style.background='#f1f5f9'; this.style.color='#475569';"
                    onmouseleave="this.style.background='transparent'; this.style.color='#94a3b8';">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <!-- Body -->
        <div style="padding:24px;">
            <!-- QR Code Container -->
            <div style="background:linear-gradient(145deg,#ffffff 0%,#f8fbff 100%); border:2px solid #e6f0ff; border-radius:16px; padding:20px;
                        margin-bottom:16px; display:flex; justify-content:center; position:relative; box-shadow:0 8px 20px rgba(0,102,204,0.08);">
                <div style="position:absolute; top:50%; left:50%; transform:translate(-50%,-50%); width:64px; height:36px; background:#ffffff; border-radius:10px; display:flex; align-items:center; justify-content:center; box-shadow:0 3px 12px rgba(0,102,204,0.22); border:2px solid #e6f0ff; z-index:10; pointer-events:none;">
                    <span style="color:#0066CC; font-weight:800; font-size:12px; letter-spacing:-0.3px; line-height:1;">payou<span style="color:#3b82f6;">.</span>id</span>
                </div>
                <div id="qrcode" style="position:relative; filter:drop-shadow(0 4px 8px rgba(0,102,204,0.1));"></div>
            </div>

            <!-- URL Display -->
            <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:8px; padding:10px 12px;
                        margin-bottom:16px; display:flex; align-items:center; gap:8px;">
                <i class="fas fa-link" style="color:#94a3b8; font-size:13px; flex-shrink:0;"></i>
                <p id="qr-url-display"
                   style="margin:0; font-size:13px; color:#475569; font-weight:500;
                          white-space:nowrap; overflow:hidden; text-overflow:ellipsis; flex:1;"></p>
            </div>

            <!-- Action Buttons -->
            <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:8px;">
                <button type="button" onclick="copyQRLink()" id="copy-link-btn"
                        style="padding:10px 12px; background:#3b82f6; color:white; border:none; border-radius:8px;
                               font-size:12px; font-weight:500; cursor:pointer;
                               display:flex; align-items:center; justify-content:center; gap:6px; transition:all 0.2s;"
                        onmouseenter="this.style.background='#2563eb'; this.style.boxShadow='0 2px 8px rgba(59,130,246,0.3)';"
                        onmouseleave="if(!this.classList.contains('copy-success')){this.style.background='#3b82f6'; this.style.boxShadow='none';}">
                    <i class="far fa-copy" style="font-size:11px;"></i>
                    <span>Salin</span>
                </button>

                <button type="button" onclick="downloadQRCode()"
                        style="padding:10px 12px; background:#ffffff; color:#475569; border:1px solid #e2e8f0;
                               border-radius:8px; font-size:12px; font-weight:500; cursor:pointer;
                               display:flex; align-items:center; justify-content:center; gap:6px; transition:all 0.2s;"
                        onmouseenter="this.style.background='#f8fafc'; this.style.borderColor='#cbd5e1';"
                        onmouseleave="this.style.background='#ffffff'; this.style.borderColor='#e2e8f0';">
                    <i class="fas fa-download" style="font-size:11px;"></i>
                    <span>Download</span>
                </button>

                <button type="button" onclick="shareToWhatsApp(event)" id="whatsapp-share-btn"
                        style="padding:10px 12px; background:#25D366; color:white; border:none; border-radius:8px;
                               font-size:12px; font-weight:500; cursor:pointer;
                               display:flex; align-items:center; justify-content:center; gap:6px; transition:all 0.2s;"
                        onmouseenter="this.style.background='#20BA5A'; this.style.boxShadow='0 2px 8px rgba(37,211,102,0.3)';"
                        onmouseleave="this.style.background='#25D366'; this.style.boxShadow='none';">
                    <i class="fab fa-whatsapp" style="font-size:11px;"></i>
                    <span>WA Cepat</span>
                </button>
            </div>
        </div>
    </div>
</div>

{{-- DESAIN POSTER UNTUK SHARE (HIDDEN) --}}
<div id="qr-poster-design" style="position:fixed; top:-9999px; left:-9999px; width:500px; height:550px; background:#ffffff; padding:25px; box-sizing:border-box; font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Helvetica,Arial,sans-serif; display:flex; flex-direction:column;">

    <!-- Header dengan logo Payou -->
    <div style="display:flex; align-items:center; gap:12px; margin-bottom:15px; flex-shrink:0;">
        <div style="display:flex; flex-direction:column;">
            <span style="font-size:22px; font-weight:700; color:#0066CC; letter-spacing:-0.5px; line-height:1.2;">payou.id</span>
            <span style="font-size:13px; color:#64748B; margin-top:2px;">Digital Business Card</span>
        </div>
    </div>

    {{-- Username Badge dihapus sesuai permintaan --}}

    <!-- QR Code Container dengan logo Payou di tengah -->
    <div style="display:flex; justify-content:center; align-items:center; margin-bottom:20px; background:linear-gradient(145deg,#ffffff,#f8fbff); padding:20px; border-radius:24px; box-shadow:0 15px 35px rgba(0,102,204,0.15); border:1px solid #e6f0ff; flex:1; position:relative;">
        <div style="position:absolute; top:50%; left:50%; transform:translate(-50%,-50%); width:72px; height:40px; background:#ffffff; border-radius:11px; display:flex; align-items:center; justify-content:center; box-shadow:0 4px 14px rgba(0,102,204,0.22); border:2px solid #e6f0ff; z-index:10; pointer-events:none;">
            <span style="color:#0066CC; font-weight:800; font-size:13px; letter-spacing:-0.3px; line-height:1;">payou<span style="color:#3b82f6;">.</span>id</span>
        </div>
        <div id="poster-qrcode" style="display:flex; justify-content:center; align-items:center;"></div>
    </div>

    <!-- URL watermark (menggantikan @username) -->
    <div style="text-align:center; margin-bottom:10px; flex-shrink:0;">
        <span style="color:#0066CC; font-weight:600; font-size:14px; padding:6px; display:inline-block;" id="poster-watermark"></span>
    </div>

    <!-- Trust Badges -->
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

    <!-- Footer -->
    <div style="border-top:1px solid #E2E8F0; padding-top:10px; text-align:center; flex-shrink:0;">
        <span style="color:#94A3B8; font-size:10px;">© 2026 payou.id · All rights reserved</span>
    </div>
</div>

<style>
#qrcode { display:flex; justify-content:center; align-items:center; }
#qrcode img, #qrcode canvas { border-radius:8px; width:180px; height:180px; }
.copy-success { background:#10b981 !important; box-shadow:none !important; }
.share-loading { opacity:0.7; pointer-events:none; position:relative; }
.share-loading::after {
    content:''; position:absolute; width:20px; height:20px;
    border:2px solid #ffffff; border-top-color:transparent;
    border-radius:50%; animation:spin 0.8s linear infinite;
    right:10px; top:50%; transform:translateY(-50%);
}
@keyframes spin { to { transform:translateY(-50%) rotate(360deg); } }
@media (max-width:480px) { #qrModalCard { width:95%; max-width:95%; } }
</style>

<script>
let currentQRCode   = null;
let currentUrl      = '';
let currentUsername = '';

function buildShareCaption() {
    return `Halo,\n\nSaya ingin berbagi kartu digital Payou.id saya.\n${currentUrl}\n\nScan QR pada gambar untuk membuka profil saya.`;
}

async function buildPosterImageUrl() {
    const posterDesign = document.getElementById('qr-poster-design');
    const posterQrContainer = document.getElementById('poster-qrcode');

    posterQrContainer.innerHTML = '';
    document.getElementById('poster-watermark').textContent = '@' + currentUsername;

    new QRCode(posterQrContainer, {
        text: currentUrl,
        width: 220,
        height: 220,
        colorDark: '#0066CC',
        colorLight: '#ffffff',
        correctLevel: QRCode.CorrectLevel.H,
    });

    await new Promise(resolve => setTimeout(resolve, 450));

    const canvas = await html2canvas(posterDesign, {
        scale: 2,
        backgroundColor: '#ffffff',
        allowTaint: false,
        useCORS: true,
        logging: false,
        windowWidth: 500,
        windowHeight: 550,
    });

    const blob = await new Promise(resolve => canvas.toBlob(resolve, 'image/png', 1.0));
    if (!blob) throw new Error('Gagal membuat gambar share.');

    const imageUrl = URL.createObjectURL(blob);

    return {
        imageUrl,
        revoke() {
            URL.revokeObjectURL(imageUrl);
        },
    };
}

function getShareFallbackMessage() {
    if (['localhost', '127.0.0.1'].includes(window.location.hostname)) {
        return 'Web Share API untuk file belum didukung di browser ini atau dibatasi karena aplikasi masih berjalan di localhost.';
    }

    return 'Browser ini belum mendukung berbagi gambar melalui Web Share API.';
}

async function fallbackShareText(captionText) {
    try {
        await navigator.clipboard.writeText(captionText);
        alert(`${getShareFallbackMessage()}\n\nCaption sudah disalin. Anda bisa menempelkannya secara manual.`);
    } catch {
        alert(`${getShareFallbackMessage()}\n\nCaption:\n\n${captionText}`);
    }
}

async function shareOrderQR(imageUrl, captionText) {
    const response = await fetch(imageUrl);
    if (!response.ok) {
        throw new Error('Gagal mengambil gambar QR.');
    }

    const blob = await response.blob();
    const file = new File([blob], `payou-${currentUsername}.png`, {
        type: blob.type || 'image/png',
    });

    if (navigator.share && typeof navigator.canShare === 'function' && navigator.canShare({ files: [file] })) {
        await navigator.share({
            title: `Payou.id - @${currentUsername}`,
            text: captionText,
            files: [file],
        });
        return true;
    }

    await fallbackShareText(captionText);
    return false;
}

async function handleQRShare(buttonId = 'whatsapp-share-btn') {
    const shareBtn = buttonId ? document.getElementById(buttonId) : null;

    if (shareBtn) {
        shareBtn.classList.add('share-loading');
        shareBtn.dataset.originalHtml = shareBtn.innerHTML;
        shareBtn.innerHTML = '<span>Menyiapkan...</span>';
    }

    let posterAsset = null;

    try {
        posterAsset = await buildPosterImageUrl();
        await shareOrderQR(posterAsset.imageUrl, buildShareCaption());
    } catch (error) {
        console.error('Error:', error);
        if (error.name !== 'AbortError' && !error.message?.includes('cancel')) {
            await fallbackShareText(buildShareCaption());
        }
    } finally {
        if (posterAsset) {
            posterAsset.revoke();
        }

        if (shareBtn) {
            shareBtn.classList.remove('share-loading');
            shareBtn.innerHTML = shareBtn.dataset.originalHtml || shareBtn.innerHTML;
            delete shareBtn.dataset.originalHtml;
        }
    }
}

function openQRModal(username) {
    const overlay    = document.getElementById('qrModalOverlay');
    const modal      = document.getElementById('qr-modal');
    const card       = document.getElementById('qrModalCard');
    const urlDisplay = document.getElementById('qr-url-display');
    const qrcodeEl   = document.getElementById('qrcode');

    currentUsername = username;
    currentUrl      = '{{ url('/') }}' + '/' + username;
    urlDisplay.textContent = currentUrl;

    // poster-watermark menampilkan @username
    document.getElementById('poster-watermark').textContent = '@' + username;

    qrcodeEl.innerHTML = '';
    currentQRCode = new QRCode(qrcodeEl, {
        text: currentUrl,
        width: 180,
        height: 180,
        colorDark: '#0066CC',
        colorLight: '#ffffff',
        correctLevel: QRCode.CorrectLevel.H,
    });

    overlay.classList.remove('hidden');
    modal.classList.remove('hidden');
    modal.style.display       = 'flex';
    modal.style.pointerEvents = 'auto';

    card.getBoundingClientRect();
    overlay.style.opacity = '1';
    card.style.opacity    = '1';
    card.style.transform  = 'translateY(0)';

    document.body.style.overflow = 'hidden';
}

function closeQRModal() {
    const overlay = document.getElementById('qrModalOverlay');
    const modal   = document.getElementById('qr-modal');
    const card    = document.getElementById('qrModalCard');

    overlay.style.opacity = '0';
    card.style.opacity    = '0';
    card.style.transform  = 'translateY(20px)';

    setTimeout(() => {
        modal.style.display = 'none';
        modal.classList.add('hidden');
        overlay.classList.add('hidden');
        document.body.style.overflow = '';

        const copyBtn = document.getElementById('copy-link-btn');
        if (copyBtn) {
            copyBtn.innerHTML = '<i class="far fa-copy" style="font-size:11px;"></i><span>Salin</span>';
            copyBtn.classList.remove('copy-success');
            copyBtn.style.background = '#3b82f6';
        }
    }, 200);
}

async function copyQRLink() {
    const copyBtn = document.getElementById('copy-link-btn');
    const success = () => {
        copyBtn.innerHTML = '<i class="fas fa-check" style="font-size:11px;"></i><span>Tersalin!</span>';
        copyBtn.classList.add('copy-success');
        setTimeout(() => {
            copyBtn.innerHTML = '<i class="far fa-copy" style="font-size:11px;"></i><span>Salin</span>';
            copyBtn.classList.remove('copy-success');
            copyBtn.style.background = '#3b82f6';
        }, 2000);
    };
    try {
        await navigator.clipboard.writeText(currentUrl);
        success();
    } catch {
        try {
            const ta = document.createElement('textarea');
            ta.value = currentUrl;
            ta.style.cssText = 'position:fixed;opacity:0;';
            document.body.appendChild(ta);
            ta.select();
            document.execCommand('copy');
            document.body.removeChild(ta);
            success();
        } catch {
            alert('Gagal menyalin. Link: ' + currentUrl);
        }
    }
}

function downloadQRCode() {
    const srcCanvas = document.querySelector('#qrcode canvas');
    if (!srcCanvas) return;

    const QR_SIZE = 400;
    const PAD     = 20;
    const LABEL_H = 40;
    const W = QR_SIZE + PAD * 2;
    const H = QR_SIZE + PAD * 2 + LABEL_H;

    const out = document.createElement('canvas');
    out.width  = W;
    out.height = H;
    const ctx  = out.getContext('2d');

    ctx.fillStyle = '#ffffff';
    ctx.fillRect(0, 0, W, H);
    ctx.drawImage(srcCanvas, PAD, PAD, QR_SIZE, QR_SIZE);

    // Logo "payou.id" — pill landscape di tengah
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
    // teks "payou" biru tua
    ctx.fillStyle = '#0066CC'; ctx.font = 'bold 18px Arial, sans-serif';
    ctx.textAlign = 'center'; ctx.textBaseline = 'middle';
    ctx.fillText('payou', W / 2 - 12, logoY + BOX_H / 2);
    // titik biru muda
    ctx.fillStyle = '#3b82f6';
    ctx.fillText('.', W / 2 + 20, logoY + BOX_H / 2);
    // teks "id" biru tua
    ctx.fillStyle = '#0066CC';
    ctx.fillText('id', W / 2 + 32, logoY + BOX_H / 2);
    ctx.textBaseline = 'alphabetic';

    // @username sebagai watermark di download
    ctx.fillStyle = '#0066CC';
    ctx.font      = 'bold 15px Arial, sans-serif';
    ctx.textAlign = 'center';
    ctx.fillText('@' + currentUsername, W / 2, PAD + QR_SIZE + 26);

    const link    = document.createElement('a');
    link.download = `payou-${currentUsername}.png`;
    link.href     = out.toDataURL('image/png', 1.0);
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

async function legacyShareToWhatsApp() {
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
            width: 220,
            height: 220,
            colorDark: '#0066CC',
            colorLight: '#ffffff',
            correctLevel: QRCode.CorrectLevel.H,
        });

        await new Promise(resolve => setTimeout(resolve, 500));

        const canvas = await html2canvas(posterDesign, {
            scale: 2,
            backgroundColor: '#ffffff',
            allowTaint: false,
            useCORS: true,
            logging: false,
            windowWidth: 500,
            windowHeight: 550,
        });

        const message = `Halo 👋\n\nSaya ingin berbagi kartu digital Payou.id saya:\n${currentUrl}\n\nSilakan scan QR code di bawah ini untuk terhubung dengan saya.\n\nTerima kasih! 🙏`;

        if (navigator.canShare && navigator.canShare({ files: [new File([], 'test.png')] })) {
            const blob = await new Promise(resolve => canvas.toBlob(resolve, 'image/png', 1.0));
            const file = new File([blob], `payou-${currentUsername}.png`, { type: 'image/png' });
            await navigator.share({
                title: `Payou.id - @${currentUsername}`,
                text: message,
                files: [file]
            });
        } else {
            const link    = document.createElement('a');
            link.download = `payou-${currentUsername}.png`;
            link.href     = canvas.toDataURL('image/png');
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            setTimeout(() => {
                window.open(`https://web.whatsapp.com/send?text=${encodeURIComponent(message)}`, '_blank');
            }, 1000);
            alert('✓ Gambar sudah didownload\n\nSilakan kirim gambar + pesan ini ke kontak Anda di WhatsApp Web');
        }

    } catch (error) {
        console.error('Error:', error);
        if (error.name !== 'AbortError' && !error.message.includes('cancel')) {
            const fallbackMsg = `Halo,\n\nSilakan kunjungi profil Payou.id saya:\n${currentUrl}`;
            window.open(`https://wa.me/?text=${encodeURIComponent(fallbackMsg)}`, '_blank');
        }
    } finally {
        shareBtn.classList.remove('share-loading');
        shareBtn.innerHTML = '<i class="fab fa-whatsapp" style="font-size:11px;"></i><span>WhatsApp</span>';
    }
}

// Override share flow: HP pakai Web Share + file, desktop fallback cepat untuk WhatsApp Web
async function shareToWhatsApp(event) {
    event?.preventDefault();
    await handleQRShare('whatsapp-share-btn');
}

document.addEventListener('keydown', e => {
    if (e.key === 'Escape') {
        const modal = document.getElementById('qr-modal');
        if (modal && modal.style.display === 'flex') closeQRModal();
    }
});
</script>
@endpush
