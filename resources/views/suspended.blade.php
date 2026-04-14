<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Akun Ditangguhkan - Mobay.id</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    body {
        font-family: 'Plus Jakarta Sans', sans-serif;
        background: #f0f4ff;
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 24px;
    }
    .wrap {
        background: white;
        border-radius: 20px;
        border: 1.5px solid #e4ecff;
        padding: 44px 40px;
        max-width: 520px;
        width: 100%;
        text-align: center;
        box-shadow: 0 8px 40px rgba(35,86,232,.07);
    }
    .logo { font-size: 13px; font-weight: 900; color: #c2cfe8; margin-bottom: 28px; letter-spacing: -.2px; }
    .icon-wrap {
        width: 68px; height: 68px;
        background: #fee2e2; border-radius: 18px;
        display: flex; align-items: center; justify-content: center;
        margin: 0 auto 20px;
    }
    h1 { font-size: 20px; font-weight: 900; color: #0c1533; letter-spacing: -.4px; margin-bottom: 8px; }
    .sub { font-size: 13.5px; color: #7a8db5; line-height: 1.65; font-weight: 500; margin-bottom: 24px; }
    .info-box {
        background: #fff7ed; border: 1.5px solid #fed7aa;
        border-radius: 12px; padding: 14px 18px;
        text-align: left; margin-bottom: 28px;
    }
    .info-box p { font-size: 11.5px; font-weight: 800; color: #c2410c; margin-bottom: 6px; }
    .info-box ul { list-style: none; }
    .info-box ul li {
        font-size: 11.5px; color: #92400e; font-weight: 600;
        padding: 3px 0; display: flex; align-items: flex-start; gap: 7px;
    }
    .info-box ul li::before {
        content: ''; width: 5px; height: 5px; background: #f97316;
        border-radius: 50%; flex-shrink: 0; margin-top: 5px;
    }
    .tabs { display: flex; gap: 0; border: 1.5px solid #e4ecff; border-radius: 12px; overflow: hidden; margin-bottom: 24px; }
    .tab-btn {
        flex: 1; padding: 10px; background: white; border: none;
        font-family: 'Plus Jakarta Sans', sans-serif; font-size: 12.5px;
        font-weight: 700; color: #7a8db5; cursor: pointer; transition: all .15s;
    }
    .tab-btn.active { background: #eff3ff; color: #2356e8; }
    .tab-btn:not(:last-child) { border-right: 1.5px solid #e4ecff; }
    .tab-panel { display: none; }
    .tab-panel.active { display: block; }
    .appeal-status {
        border-radius: 12px; padding: 16px; margin-bottom: 16px;
        display: flex; align-items: flex-start; gap: 12px; text-align: left;
    }
    .appeal-status.pending { background: #fef9c3; border: 1.5px solid #fde68a; }
    .appeal-status.approved { background: #dcfce7; border: 1.5px solid #bbf7d0; }
    .appeal-status.rejected { background: #fee2e2; border: 1.5px solid #fecaca; }
    .as-icon { width: 36px; height: 36px; border-radius: 10px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
    .pending .as-icon { background: #fef08a; color: #92400e; }
    .approved .as-icon { background: #bbf7d0; color: #15803d; }
    .rejected .as-icon { background: #fecaca; color: #b91c1c; }
    .as-title { font-size: 13px; font-weight: 800; margin-bottom: 3px; }
    .pending .as-title { color: #92400e; }
    .approved .as-title { color: #15803d; }
    .rejected .as-title { color: #b91c1c; }
    .as-sub { font-size: 11.5px; font-weight: 600; line-height: 1.5; }
    .pending .as-sub { color: #a16207; }
    .approved .as-sub { color: #16a34a; }
    .rejected .as-sub { color: #ef4444; }
    .as-ticket { font-size: 11px; font-family: monospace; margin-top: 5px; opacity: .75; }
    .form-group { text-align: left; margin-bottom: 14px; }
    .form-label { font-size: 11.5px; font-weight: 800; color: #2d3d6b; margin-bottom: 6px; display: block; }
    .form-label span { color: #ef4444; }
    textarea {
        width: 100%; border: 1.5px solid #e4ecff; border-radius: 10px;
        padding: 10px 14px; font-family: 'Plus Jakarta Sans', sans-serif;
        font-size: 12.5px; color: #0c1533; background: #f8faff;
        outline: none; resize: none; transition: border .15s;
    }
    textarea:focus { border-color: #2356e8; background: white; }
    .char-count { font-size: 10.5px; color: #7a8db5; text-align: right; margin-top: 4px; }
    .field-error {
        font-size: 10.5px;
        color: #dc2626;
        margin-top: 6px;
        font-weight: 700;
        display: none;
    }
    .form-group.has-error .form-label,
    .form-group.has-error .form-label span {
        color: #dc2626 !important;
    }
    .form-group.has-error textarea,
    .form-group.has-error .upload-box {
        border-color: #f87171;
        background: #fff5f5;
    }
    .form-group.has-error .char-count {
        color: #dc2626;
    }
    .upload-box {
        border: 1.5px dashed #c9d8ff;
        border-radius: 12px;
        background: #f8faff;
        padding: 14px;
        text-align: left;
    }
    .upload-box input { display: block; width: 100%; font-size: 12px; color: #2d3d6b; }
    .upload-help { font-size: 11px; color: #7a8db5; line-height: 1.5; margin-top: 8px; }
    .upload-list { display: grid; gap: 8px; margin-top: 12px; }
    .upload-item {
        display: flex; justify-content: space-between; align-items: center; gap: 10px;
        padding: 9px 10px; border-radius: 10px; background: white; border: 1.5px solid #e4ecff;
    }
    .upload-name { font-size: 11.5px; font-weight: 700; color: #2d3d6b; word-break: break-all; text-align: left; }
    .upload-meta { font-size: 10.5px; color: #7a8db5; margin-top: 2px; }
    .upload-remove {
        border: none; background: #fee2e2; color: #b91c1c; width: 28px; height: 28px;
        border-radius: 8px; cursor: pointer; font-weight: 800; flex-shrink: 0;
    }
    .btn-primary {
        display: flex; align-items: center; justify-content: center; gap: 7px;
        width: 100%; background: #1a3fa8; color: white;
        font-family: 'Plus Jakarta Sans', sans-serif;
        font-size: 13px; font-weight: 800; padding: 12px 24px;
        border-radius: 11px; border: none; cursor: pointer;
        transition: background .15s; margin-bottom: 10px;
    }
    .btn-primary:hover:not(:disabled) { background: #153090; }
    .btn-primary:disabled { opacity: .6; cursor: not-allowed; }
    .btn-logout {
        display: flex; align-items: center; justify-content: center; gap: 7px;
        width: 100%; background: #f0f4ff; color: #7a8db5;
        font-family: 'Plus Jakarta Sans', sans-serif;
        font-size: 12.5px; font-weight: 700; padding: 10px 24px;
        border-radius: 11px; border: 1.5px solid #e4ecff; cursor: pointer;
        transition: all .15s; text-decoration: none;
    }
    .btn-logout:hover { background: #e4ecff; color: #2d3d6b; }
    .contact { font-size: 11.5px; color: #7a8db5; font-weight: 600; margin-top: 14px; }
    .contact a { color: #2356e8; text-decoration: none; font-weight: 700; }
    .contact a:hover { text-decoration: underline; }
    .alert-msg { border-radius: 10px; padding: 10px 14px; font-size: 12px; font-weight: 700; margin-bottom: 12px; text-align: left; }
    .alert-success { background: #dcfce7; color: #15803d; border: 1.5px solid #bbf7d0; }
    .alert-error { background: #fee2e2; color: #b91c1c; border: 1.5px solid #fecaca; }
    .modal-backdrop {
        position: fixed;
        inset: 0;
        background: rgba(12, 21, 51, .45);
        display: none;
        align-items: center;
        justify-content: center;
        padding: 20px;
        z-index: 1000;
    }
    .modal-backdrop.show { display: flex; }
    .modal-card {
        width: 100%;
        max-width: 420px;
        background: #fff;
        border-radius: 18px;
        padding: 22px 20px 18px;
        border: 1.5px solid #fecaca;
        box-shadow: 0 20px 60px rgba(12, 21, 51, .18);
        text-align: left;
    }
    .modal-head {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        margin-bottom: 14px;
    }
    .modal-icon {
        width: 42px;
        height: 42px;
        border-radius: 12px;
        background: #fee2e2;
        color: #b91c1c;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .modal-title {
        font-size: 15px;
        font-weight: 800;
        color: #0c1533;
        margin-bottom: 4px;
    }
    .modal-text {
        font-size: 12px;
        line-height: 1.6;
        color: #5b6785;
    }
    .modal-list {
        margin: 14px 0 0;
        padding-left: 18px;
        color: #b91c1c;
        font-size: 12px;
        font-weight: 700;
        line-height: 1.7;
    }
    .modal-actions {
        display: flex;
        justify-content: flex-end;
        margin-top: 18px;
    }
    .modal-btn {
        border: none;
        border-radius: 10px;
        background: #1a3fa8;
        color: #fff;
        font-family: 'Plus Jakarta Sans', sans-serif;
        font-size: 12.5px;
        font-weight: 800;
        padding: 10px 16px;
        cursor: pointer;
    }
    @media (max-width: 640px) {
        .wrap { padding: 28px 22px; }
    }
    </style>
</head>
<body>
<div class="wrap">
    <div class="logo">Mobay.id</div>

    <div class="icon-wrap">
        <svg width="32" height="32" fill="none" viewBox="0 0 24 24" stroke="#b91c1c" stroke-width="1.8">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
        </svg>
    </div>

    <h1>Akun Anda Ditangguhkan</h1>
    <p class="sub">Tim moderasi Mobay.id telah menangguhkan akun Anda. Semua akses ke dashboard, toko, dan profil publik Anda dinonaktifkan sementara.</p>

    <div class="info-box">
        <p>Selama penangguhan, Anda tidak dapat:</p>
        <ul>
            <li>Mengakses dashboard dan pengaturan</li>
            <li>Menerima pembayaran atau transaksi baru</li>
            <li>Profil publik dan toko tidak dapat diakses pengunjung</li>
            <li>Melakukan penarikan saldo</li>
        </ul>
    </div>

    <div class="tabs">
        <button class="tab-btn active" onclick="switchTab('banding')">Ajukan Banding</button>
        <button class="tab-btn" onclick="switchTab('status')">Status Banding</button>
    </div>

    <div id="tab-banding" class="tab-panel active">
        <div id="formArea">
            <div class="form-group" data-field="reason">
                <label class="form-label">Alasan Banding <span>*</span></label>
                <textarea id="reason" rows="5" maxlength="2000" placeholder="Jelaskan mengapa Anda merasa penangguhan ini tidak tepat. Sertakan kronologi yang relevan dan konteks pendukung. Minimal 30 karakter."></textarea>
                <div class="char-count"><span id="reasonCount">0</span>/2000</div>
                <div class="field-error" id="reasonError"></div>
            </div>

            <div class="form-group" data-field="additional_info">
                <label class="form-label">Informasi Tambahan <span style="color:#7a8db5;font-weight:600;">(opsional)</span></label>
                <textarea id="additionalInfo" rows="2" maxlength="1000" placeholder="Tambahkan konteks lain yang mendukung banding Anda..."></textarea>
                <div class="char-count"><span id="addCount">0</span>/1000</div>
                <div class="field-error" id="additionalInfoError"></div>
            </div>

            <div class="form-group" data-field="evidence">
                <label class="form-label">Upload Bukti <span style="color:#7a8db5;font-weight:600;">(opsional, maks 3 gambar)</span></label>
                <div class="upload-box">
                    <input id="appealEvidence" type="file" accept="image/jpeg,image/png,image/webp" multiple>
                    <div class="upload-help">Format `jpg`, `png`, atau `webp`. Gambar dikompresi otomatis sebelum dikirim.</div>
                    <div id="uploadStatus" class="upload-help" style="display:none;"></div>
                    <div id="uploadList" class="upload-list" style="display:none;"></div>
                </div>
                <div class="field-error" id="evidenceError"></div>
            </div>

            <button id="submitBtn" onclick="submitAppeal()" class="btn-primary" type="button">
                <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                Kirim Pengajuan Banding
            </button>
        </div>

        <div id="successArea" style="display:none;">
            <div class="appeal-status pending">
                <div class="as-icon">
                    <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <div class="as-title">Banding Berhasil Diajukan</div>
                    <div class="as-sub">Tim moderasi akan meninjau pengajuan Anda dalam 1-3 hari kerja.</div>
                    <div class="as-ticket" id="successTicket"></div>
                </div>
            </div>
        </div>
    </div>

    <div id="tab-status" class="tab-panel">
        <div id="statusLoading" style="text-align:center;padding:20px 0;color:#7a8db5;font-size:13px;font-weight:600;">
            Memuat status banding...
        </div>
        <div id="statusContent" style="display:none;"></div>
    </div>

    <form method="POST" action="{{ route('logout') }}" style="margin-top:4px;">
        @csrf
        <button type="submit" class="btn-logout">
            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
            Keluar dari Akun
        </button>
    </form>

    <p class="contact">Pertanyaan lain? <a href="mailto:support@mobay.id">support@mobay.id</a></p>
</div>

<div id="errorModal" class="modal-backdrop" onclick="handleModalBackdrop(event)">
    <div class="modal-card" role="dialog" aria-modal="true" aria-labelledby="errorModalTitle">
        <div class="modal-head">
            <div class="modal-icon">
                <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m0 3.75h.008v.008H12v-.008zm9-3.75a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <div id="errorModalTitle" class="modal-title">Periksa Form Banding</div>
                <div id="errorModalText" class="modal-text">Masih ada bagian yang perlu diperbaiki sebelum form bisa dikirim.</div>
            </div>
        </div>
        <ul id="errorModalList" class="modal-list" style="display:none;"></ul>
        <div class="modal-actions">
            <button type="button" class="modal-btn" onclick="closeErrorModal()">Mengerti</button>
        </div>
    </div>
</div>

<script>
const CSRF = document.querySelector('meta[name="csrf-token"]').content;
const evidenceInput = document.getElementById('appealEvidence');
const uploadList = document.getElementById('uploadList');
const uploadStatus = document.getElementById('uploadStatus');
const errorModal = document.getElementById('errorModal');
const errorModalText = document.getElementById('errorModalText');
const errorModalList = document.getElementById('errorModalList');
let compressedEvidenceFiles = [];

function switchTab(tab) {
    document.querySelectorAll('.tab-btn').forEach((button, index) => {
        button.classList.toggle('active', (index === 0 && tab === 'banding') || (index === 1 && tab === 'status'));
    });
    document.getElementById('tab-banding').classList.toggle('active', tab === 'banding');
    document.getElementById('tab-status').classList.toggle('active', tab === 'status');
    if (tab === 'status') loadStatus();
}

function fieldConfig(field) {
    const map = {
        reason: {
            group: document.querySelector('.form-group[data-field="reason"]'),
            input: document.getElementById('reason'),
            error: document.getElementById('reasonError'),
        },
        additional_info: {
            group: document.querySelector('.form-group[data-field="additional_info"]'),
            input: document.getElementById('additionalInfo'),
            error: document.getElementById('additionalInfoError'),
        },
        evidence: {
            group: document.querySelector('.form-group[data-field="evidence"]'),
            input: document.getElementById('appealEvidence'),
            error: document.getElementById('evidenceError'),
        },
    };

    return map[field] || null;
}

function clearFieldError(field) {
    const config = fieldConfig(field);
    if (!config) return;
    config.group.classList.remove('has-error');
    config.error.style.display = 'none';
    config.error.textContent = '';
}

function setFieldError(field, message) {
    const config = fieldConfig(field);
    if (!config) return;
    config.group.classList.add('has-error');
    config.error.textContent = message;
    config.error.style.display = 'block';
}

function clearFormErrors() {
    ['reason', 'additional_info', 'evidence'].forEach(clearFieldError);
}

function showErrorModal(messages, fallbackMessage) {
    const items = Array.isArray(messages) ? messages.filter(Boolean) : [];
    errorModalText.textContent = fallbackMessage || 'Masih ada bagian yang perlu diperbaiki sebelum form bisa dikirim.';
    errorModalList.innerHTML = items.map(message => `<li>${message}</li>`).join('');
    errorModalList.style.display = items.length ? 'block' : 'none';
    errorModal.classList.add('show');
}

function closeErrorModal() {
    errorModal.classList.remove('show');
}

function handleModalBackdrop(event) {
    if (event.target === errorModal) closeErrorModal();
}

document.getElementById('reason').addEventListener('input', function () {
    document.getElementById('reasonCount').textContent = this.value.length;
    if (this.value.trim().length >= 30) clearFieldError('reason');
});

document.getElementById('additionalInfo').addEventListener('input', function () {
    document.getElementById('addCount').textContent = this.value.length;
    if (this.value.length <= 1000) clearFieldError('additional_info');
});

function showAlert(type, msg) {
    if (type === 'error') {
        showErrorModal([msg], msg);
    }
}

function formatKB(bytes) {
    return (bytes / 1024).toFixed(0) + ' KB';
}

function renderEvidenceList() {
    if (!compressedEvidenceFiles.length) {
        uploadList.style.display = 'none';
        uploadList.innerHTML = '';
        uploadStatus.style.display = 'none';
        uploadStatus.textContent = '';
        return;
    }

    uploadStatus.style.display = 'block';
    uploadStatus.textContent = `${compressedEvidenceFiles.length}/3 gambar siap diupload`;
    uploadList.style.display = 'grid';
    uploadList.innerHTML = compressedEvidenceFiles.map((file, index) => `
        <div class="upload-item">
            <div>
                <div class="upload-name">${file.name}</div>
                <div class="upload-meta">${formatKB(file.size)}</div>
            </div>
            <button class="upload-remove" type="button" onclick="removeEvidence(${index})">×</button>
        </div>
    `).join('');
}

function removeEvidence(index) {
    compressedEvidenceFiles.splice(index, 1);
    renderEvidenceList();
}

function compressImage(file, maxSizeKB = 300, maxWidth = 1600, quality = 0.82) {
    return new Promise((resolve, reject) => {
        if (!file.type.startsWith('image/')) {
            reject(new Error('File harus berupa gambar.'));
            return;
        }

        const reader = new FileReader();
        reader.onload = () => {
            const img = new Image();
            img.onload = () => {
                let { width, height } = img;

                if (width > maxWidth) {
                    height = Math.round((height * maxWidth) / width);
                    width = maxWidth;
                }

                const canvas = document.createElement('canvas');
                canvas.width = width;
                canvas.height = height;
                const ctx = canvas.getContext('2d');
                ctx.drawImage(img, 0, 0, width, height);

                const compressLoop = (currentQuality) => {
                    canvas.toBlob(blob => {
                        if (!blob) {
                            reject(new Error('Gagal memproses gambar.'));
                            return;
                        }

                        if ((blob.size / 1024) > maxSizeKB && currentQuality > 0.45) {
                            compressLoop(currentQuality - 0.08);
                            return;
                        }

                        resolve(new File([blob], file.name.replace(/\.(heic|heif)$/i, '.jpg'), {
                            type: 'image/jpeg',
                            lastModified: Date.now(),
                        }));
                    }, 'image/jpeg', currentQuality);
                };

                compressLoop(quality);
            };
            img.onerror = () => reject(new Error('Gagal membaca gambar.'));
            img.src = reader.result;
        };
        reader.onerror = () => reject(new Error('Gagal membaca file.'));
        reader.readAsDataURL(file);
    });
}

evidenceInput.addEventListener('change', async function () {
    const selectedFiles = Array.from(this.files || []);
    this.value = '';

    if (!selectedFiles.length) return;

    if (compressedEvidenceFiles.length + selectedFiles.length > 3) {
        setFieldError('evidence', 'Maksimal 3 gambar bukti dapat diunggah.');
        showAlert('error', 'Maksimal 3 gambar bukti.');
        return;
    }

    clearFieldError('evidence');
    uploadStatus.style.display = 'block';
    uploadStatus.textContent = 'Menyiapkan gambar...';

    try {
        for (const file of selectedFiles) {
            const compressed = await compressImage(file);
            compressedEvidenceFiles.push(compressed);
        }
        renderEvidenceList();
    } catch (error) {
        uploadStatus.style.display = 'none';
        setFieldError('evidence', error.message || 'Gagal memproses gambar.');
        showAlert('error', error.message || 'Gagal memproses gambar.');
    }
});

async function submitAppeal() {
    const reason = document.getElementById('reason').value.trim();
    const info = document.getElementById('additionalInfo').value.trim();
    const btn = document.getElementById('submitBtn');
    clearFormErrors();
    closeErrorModal();

    if (reason.length < 30) {
        setFieldError('reason', 'Alasan banding minimal 30 karakter.');
        showAlert('error', 'Alasan banding minimal 30 karakter. Mohon jelaskan lebih detail.');
        document.getElementById('reason').focus();
        return;
    }

    btn.disabled = true;
    btn.textContent = 'Mengirim...';

    try {
        const formData = new FormData();
        formData.append('reason', reason);
        formData.append('additional_info', info);
        compressedEvidenceFiles.forEach(file => formData.append('evidence[]', file));

        const res = await fetch('{{ route('appeal.store') }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': CSRF,
                'Accept': 'application/json',
            },
            body: formData,
        });

        const data = await res.json().catch(() => null);

        if (res.ok && data?.success) {
            document.getElementById('formArea').style.display = 'none';
            document.getElementById('successArea').style.display = 'block';
            document.getElementById('successTicket').textContent = 'Kode banding: ' + data.ticket;
            compressedEvidenceFiles = [];
            renderEvidenceList();
            return;
        }

        let message = data?.message || 'Terjadi kesalahan. Coba lagi.';
        if (data?.errors) {
            const modalMessages = [];

            Object.entries(data.errors).forEach(([field, messages]) => {
                const normalizedField = field.startsWith('evidence.') ? 'evidence' : field;
                const firstMessage = Array.isArray(messages) ? messages[0] : messages;

                if (!firstMessage) return;

                setFieldError(normalizedField, firstMessage);
                modalMessages.push(firstMessage);
            });

            if (modalMessages.length) {
                showErrorModal(modalMessages, 'Beberapa bagian form masih belum sesuai.');
                const firstField = ['reason', 'additional_info', 'evidence'].find(name => {
                    const config = fieldConfig(name);
                    return config?.group.classList.contains('has-error');
                });
                const firstConfig = firstField ? fieldConfig(firstField) : null;
                if (firstConfig?.input) {
                    firstConfig.input.focus();
                }
                return;
            }
        }

        showAlert('error', message);
    } catch {
        showAlert('error', 'Terjadi kesalahan jaringan. Coba lagi.');
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg> Kirim Pengajuan Banding';
    }
}

document.addEventListener('keydown', function (event) {
    if (event.key === 'Escape') {
        closeErrorModal();
    }
});

async function loadStatus() {
    const loading = document.getElementById('statusLoading');
    const content = document.getElementById('statusContent');
    loading.style.display = 'block';
    content.style.display = 'none';

    try {
        const res = await fetch('{{ route('appeal.status') }}', {
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': CSRF
            }
        });
        const data = await res.json();

        loading.style.display = 'none';
        content.style.display = 'block';

        if (!data.has_appeal) {
            content.innerHTML = `
                <div style="text-align:center;padding:24px 0;">
                    <svg width="36" height="36" fill="none" viewBox="0 0 24 24" stroke="#c2cfe8" stroke-width="1.5" style="margin:0 auto 10px;display:block;"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <div style="font-size:13px;font-weight:700;color:#7a8db5;">Belum ada pengajuan banding</div>
                    <div style="font-size:11.5px;color:#7a8db5;margin-top:4px;">Ajukan banding melalui tab "Ajukan Banding"</div>
                </div>`;
            return;
        }

        const a = data.appeal;
        const icons = {
            pending: '<svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
            approved: '<svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>',
            rejected: '<svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>',
        };
        const titles = {
            pending: 'Banding Sedang Ditinjau',
            approved: 'Banding Disetujui',
            rejected: 'Banding Ditolak'
        };
        const subs = {
            pending: 'Tim moderasi sedang memproses pengajuan Anda. Harap tunggu 1-3 hari kerja.',
            approved: 'Banding Anda disetujui. Penangguhan akun telah dicabut.',
            rejected: a.admin_note ? ('Banding Anda ditolak. Catatan admin: ' + a.admin_note) : 'Banding Anda ditolak oleh tim moderasi.',
        };

        content.innerHTML = `
            <div class="appeal-status ${a.status}">
                <div class="as-icon">${icons[a.status]}</div>
                <div>
                    <div class="as-title">${titles[a.status]}</div>
                    <div class="as-sub">${subs[a.status]}</div>
                    <div class="as-ticket">Kode: ${a.ticket_code} | Diajukan ${a.created_at}</div>
                    ${a.evidence_count > 0 ? `<div class="as-ticket">${a.evidence_count} gambar bukti terlampir</div>` : ''}
                </div>
            </div>
            ${a.status === 'approved' ? '<div class="alert-msg alert-success">Akun Anda sudah dipulihkan. Silakan logout lalu login kembali untuk mengakses dashboard.</div>' : ''}
        `;
    } catch {
        loading.style.display = 'none';
        content.style.display = 'block';
        content.innerHTML = '<div class="alert-msg alert-error">Gagal memuat status. Refresh halaman dan coba lagi.</div>';
    }
}
</script>
</body>
</html>
