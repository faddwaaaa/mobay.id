{{-- 
    QR CODE MODAL COMPONENT
    Include di layout: @include('components.qr-modal')
    Panggil dari tombol: onclick="openQRModal('{{ $userSlug }}')"

    ⚠️  Komponen ini menggunakan @push('modals') sehingga
        overlay dirender di body level dan bisa menutupi sidebar.
--}}

{{-- QR Code Library (di <head> atau sebelum </body>, tidak butuh @push) --}}
<script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>

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
            <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:10px; padding:20px;
                        margin-bottom:16px; display:flex; justify-content:center;">
                <div id="qrcode"></div>
            </div>

            <!-- URL Display -->
            <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:8px; padding:10px 12px;
                        margin-bottom:16px; display:flex; align-items:center; gap:8px;">
                <i class="fas fa-link" style="color:#94a3b8; font-size:13px; flex-shrink:0;"></i>
                <p id="qr-url-display"
                   style="margin:0; font-size:13px; color:#475569; font-weight:500;
                          white-space:nowrap; overflow:hidden; text-overflow:ellipsis; flex:1;">
                </p>
            </div>

            <!-- Action Buttons -->
            <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:8px;">
                <button onclick="copyQRLink()" id="copy-link-btn"
                        style="padding:10px 12px; background:#3b82f6; color:white; border:none; border-radius:8px;
                               font-size:12px; font-weight:500; cursor:pointer;
                               display:flex; align-items:center; justify-content:center; gap:6px; transition:all 0.2s;"
                        onmouseenter="this.style.background='#2563eb'; this.style.boxShadow='0 2px 8px rgba(59,130,246,0.3)';"
                        onmouseleave="if(!this.classList.contains('copy-success')){this.style.background='#3b82f6'; this.style.boxShadow='none';}">
                    <i class="far fa-copy" style="font-size:11px;"></i>
                    <span>Salin</span>
                </button>

                <button onclick="downloadQRCode()"
                        style="padding:10px 12px; background:#ffffff; color:#475569; border:1px solid #e2e8f0;
                               border-radius:8px; font-size:12px; font-weight:500; cursor:pointer;
                               display:flex; align-items:center; justify-content:center; gap:6px; transition:all 0.2s;"
                        onmouseenter="this.style.background='#f8fafc'; this.style.borderColor='#cbd5e1';"
                        onmouseleave="this.style.background='#ffffff'; this.style.borderColor='#e2e8f0';">
                    <i class="fas fa-download" style="font-size:11px;"></i>
                    <span>Download</span>
                </button>

                <button onclick="shareToWhatsApp()"
                        style="padding:10px 12px; background:#25D366; color:white; border:none; border-radius:8px;
                               font-size:12px; font-weight:500; cursor:pointer;
                               display:flex; align-items:center; justify-content:center; gap:6px; transition:all 0.2s;"
                        onmouseenter="this.style.background='#20BA5A'; this.style.boxShadow='0 2px 8px rgba(37,211,102,0.3)';"
                        onmouseleave="this.style.background='#25D366'; this.style.boxShadow='none';">
                    <i class="fab fa-whatsapp" style="font-size:11px;"></i>
                    <span>Whatsapp</span>
                </button>
            </div>
        </div>
    </div>
</div>

<style>
#qrcode { display:flex; justify-content:center; align-items:center; }
#qrcode img, #qrcode canvas { border-radius:6px; }
.copy-success { background:#10b981 !important; box-shadow:none !important; }

@media (max-width:480px) {
    #qrModalCard { width:95%; max-width:95%; }
}
</style>

<script>
let currentQRCode = null;
let currentUrl    = '';

function openQRModal(username) {
    const overlay     = document.getElementById('qrModalOverlay');
    const modal       = document.getElementById('qr-modal');
    const card        = document.getElementById('qrModalCard');
    const urlDisplay  = document.getElementById('qr-url-display');
    const qrcodeEl    = document.getElementById('qrcode');

    currentUrl = '{{ url('/') }}' + '/' + username;
    urlDisplay.textContent = currentUrl;

    // Reset & generate QR
    qrcodeEl.innerHTML = '';
    currentQRCode = new QRCode(qrcodeEl, {
        text: currentUrl,
        width: 180, height: 180,
        colorDark: '#000000', colorLight: '#ffffff',
        correctLevel: QRCode.CorrectLevel.H,
    });

    // Tampilkan
    overlay.classList.remove('hidden');
    modal.classList.remove('hidden');
    modal.style.display      = 'flex';
    modal.style.pointerEvents = 'auto';

    card.getBoundingClientRect(); // force reflow
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

        // Reset copy button
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
    const canvas = document.querySelector('#qrcode canvas');
    if (!canvas) return;
    const link      = document.createElement('a');
    link.download   = `qrcode-${currentUrl.split('/').pop()}.png`;
    link.href       = canvas.toDataURL('image/png', 1.0);
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

function shareToWhatsApp() {
    const msg = `Halo,\nSaya ingin membagikan tautan resmi saya melalui pesan ini:\n${currentUrl}\nSilakan diakses sesuai kebutuhan.\nTerima kasih.`;
    window.open(`https://wa.me/?text=${encodeURIComponent(msg)}`, '_blank');
}

document.addEventListener('keydown', e => {
    if (e.key === 'Escape') {
        const modal = document.getElementById('qr-modal');
        if (modal && modal.style.display === 'flex') closeQRModal();
    }
});
</script>
@endpush