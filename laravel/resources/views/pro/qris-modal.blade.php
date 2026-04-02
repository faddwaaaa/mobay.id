<!-- QRIS Payment Modal -->
<div id="proQrisModal" class="pro-qris-modal" style="display: none;">
    <div class="pro-qris-overlay" onclick="closeProQrisModal()"></div>
    <div class="pro-qris-container">
        <div class="pro-qris-header">
            <h3 id="proQrisTitle" class="pro-qris-title">QRIS Pro</h3>
            <button type="button" class="pro-qris-close" onclick="closeProQrisModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <div class="pro-qris-body">
            <div class="pro-qris-amount-section">
                <p class="pro-qris-label">Jumlah Pembayaran</p>
                <p id="proQrisAmount" class="pro-qris-amount">Rp 49.900</p>
            </div>

            <div class="pro-qris-code-section">
                <p class="pro-qris-label" style="text-align: center; margin-bottom: 16px;">Scan QRIS di e-wallet/bank Anda</p>
                <div class="pro-qris-code-wrapper">
                    <img id="proQrisCodeImg" src="" alt="QRIS Code" class="pro-qris-code-img" />
                </div>
            </div>

            <div class="pro-qris-info">
                <p class="pro-qris-info-text">
                    <i class="fas fa-info-circle" style="color: #3b82f6; margin-right: 8px;"></i>
                    Proses pembayaran akan diproses otomatis setelah Anda menyelesaikan scan & transfer.
                </p>
            </div>

            <div class="pro-qris-type-info">
                <p class="pro-qris-type-label">Paket yang dipilih</p>
                <p id="proQrisTypeValue" class="pro-qris-type-value">Pro Bulanan (30 hari)</p>
            </div>
        </div>

        <div class="pro-qris-footer">
            <button type="button" class="pro-qris-btn-secondary" onclick="closeProQrisModal()">
                Tutup
            </button>
        </div>
    </div>
</div>

<style>
.pro-qris-modal {
    position: fixed;
    inset: 0;
    z-index: 999999;
    display: flex;
    align-items: center;
    justify-content: center;
}

.pro-qris-overlay {
    position: fixed;
    inset: 0;
    background: rgba(0, 0, 0, 0.5);
    backdrop-filter: blur(4px);
    -webkit-backdrop-filter: blur(4px);
    cursor: pointer;
    z-index: 999999;
}

.pro-qris-container {
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    z-index: 1000000;
    background: #ffffff;
    border-radius: 20px;
    width: 90%;
    max-width: 420px;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.25);
    overflow: hidden;
    display: flex;
    flex-direction: column;
    max-height: 85vh;
    overflow-y: auto;
}

.pro-qris-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 24px;
    border-bottom: 1px solid #e5e7eb;
    background: linear-gradient(135deg, #f0f9ff 0%, #f8fbff 100%);
}

.pro-qris-title {
    margin: 0;
    font-size: 20px;
    font-weight: 700;
    color: #1d4ed8;
}

.pro-qris-close {
    background: none;
    border: none;
    font-size: 24px;
    color: #64748b;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    width: 40px;
    height: 40px;
    border-radius: 8px;
    transition: all 0.2s;
}

.pro-qris-close:hover {
    background: rgba(0, 0, 0, 0.05);
    color: #1e3a8a;
}

.pro-qris-body {
    padding: 28px 24px;
    flex: 1;
}

.pro-qris-amount-section {
    text-align: center;
    margin-bottom: 24px;
    padding: 16px;
    background: #f0f9ff;
    border-radius: 12px;
    border: 1px solid #bfdbfe;
}

.pro-qris-label {
    margin: 0 0 6px;
    font-size: 12px;
    font-weight: 600;
    color: #64748b;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.pro-qris-amount {
    margin: 0;
    font-size: 32px;
    font-weight: 800;
    color: #1d4ed8;
}

.pro-qris-code-section {
    margin-bottom: 24px;
    text-align: center;
}

.pro-qris-code-wrapper {
    background: #f8fafc;
    border: 2px solid #e2e8f0;
    border-radius: 16px;
    padding: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    min-height: 220px;
}

.pro-qris-code-img {
    max-width: 100%;
    max-height: 200px;
    object-fit: contain;
}

.pro-qris-info {
    background: #eff6ff;
    border: 1px solid #bfdbfe;
    border-radius: 12px;
    padding: 12px 14px;
    margin-bottom: 16px;
}

.pro-qris-info-text {
    margin: 0;
    font-size: 13px;
    color: #1e3a8a;
    line-height: 1.6;
}

.pro-qris-type-info {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 14px 16px;
    margin-bottom: 8px;
}

.pro-qris-type-label {
    margin: 0 0 6px;
    font-size: 12px;
    font-weight: 600;
    color: #64748b;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.pro-qris-type-value {
    margin: 0;
    font-size: 16px;
    font-weight: 700;
    color: #0f172a;
}

.pro-qris-footer {
    padding: 16px 24px;
    border-top: 1px solid #e5e7eb;
    background: #f8fafc;
    display: flex;
    gap: 12px;
}

.pro-qris-btn-secondary {
    flex: 1;
    min-height: 44px;
    padding: 0 16px;
    border: 1px solid #e2e8f0;
    background: #ffffff;
    color: #475569;
    border-radius: 12px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
}

.pro-qris-btn-secondary:hover {
    background: #f1f5f9;
    border-color: #cbd5e1;
}

@media (max-width: 640px) {
    .pro-qris-container {
        width: 92%;
        border-radius: 16px;
    }

    .pro-qris-header,
    .pro-qris-body,
    .pro-qris-footer {
        padding: 20px 16px;
    }

    .pro-qris-title {
        font-size: 18px;
    }

    .pro-qris-amount {
        font-size: 28px;
    }

    .pro-qris-code-wrapper {
        min-height: 180px;
        padding: 16px;
    }

    .pro-qris-code-img {
        max-height: 160px;
    }
}

/* Scroll styling */
.pro-qris-container::-webkit-scrollbar {
    width: 6px;
}

.pro-qris-container::-webkit-scrollbar-track {
    background: transparent;
}

.pro-qris-container::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 3px;
}

.pro-qris-container::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}
</style>

<script>
// Pindahkan modal ke <body> agar tidak ter-clip oleh transform/filter di layout dashboard
(function() {
    function moveModalToBody() {
        var modal = document.getElementById('proQrisModal');
        if (modal && modal.parentElement !== document.body) {
            document.body.appendChild(modal);
        }
    }
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', moveModalToBody);
    } else {
        moveModalToBody();
    }
})();

async function showProQrisModal(packageType) {
    var modal = document.getElementById('proQrisModal');

    // Pastikan modal sudah di body
    if (modal.parentElement !== document.body) {
        document.body.appendChild(modal);
    }

    var titleEl = document.getElementById('proQrisTitle');
    var amountEl = document.getElementById('proQrisAmount');
    var codeImgEl = document.getElementById('proQrisCodeImg');
    var typeValueEl = document.getElementById('proQrisTypeValue');

    // Set label dan amount berdasarkan paket
    if (packageType === 'monthly') {
        titleEl.textContent = 'QRIS Pro Bulanan';
        amountEl.textContent = 'Rp 49.900';
        typeValueEl.textContent = 'Pro Bulanan (30 hari)';
    } else {
        titleEl.textContent = 'QRIS Pro Tahunan';
        amountEl.textContent = 'Rp 500.000';
        typeValueEl.textContent = 'Pro Tahunan (365 hari)';
    }

    // Show loading state
    codeImgEl.src = '';
    modal.style.display = 'flex';
    document.body.style.overflow = 'hidden';

    // Request ke backend untuk create invoice
    try {
        var csrfToken = document.querySelector('meta[name="csrf-token"]');
        var response = await fetch('/pro/create-invoice', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-Token': csrfToken ? csrfToken.content : '',
            },
            body: JSON.stringify({ package: packageType }),
        });

        var data = await response.json();

        if (data.success) {
            if (data.qr_code) {
                codeImgEl.src = data.qr_code;
            } else {
                codeImgEl.src = 'data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" width="200" height="200"%3E%3Crect fill="%23f0f0f0" width="200" height="200"/%3E%3Ctext x="50%" y="50%" text-anchor="middle" dy=".3em" fill="%23999" font-family="Arial"%3EQR Loading...%3C/text%3E%3C/svg%3E';
            }
        } else {
            alert('Gagal membuat invoice: ' + (data.error || 'Unknown error'));
            closeProQrisModal();
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Terjadi kesalahan: ' + error.message);
        closeProQrisModal();
    }
}

function closeProQrisModal() {
    var modal = document.getElementById('proQrisModal');
    modal.style.display = 'none';
    document.body.style.overflow = '';
}

document.addEventListener('DOMContentLoaded', function () {
    // Close dengan ESC key
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            closeProQrisModal();
        }
    });
});
</script>