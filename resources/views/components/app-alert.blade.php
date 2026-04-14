<style>
    .app-alert-backdrop,
    .app-confirm-backdrop {
        position: fixed;
        inset: 0;
        background: rgba(15, 23, 42, 0.5);
        backdrop-filter: blur(5px);
        -webkit-backdrop-filter: blur(5px);
        display: none;
        align-items: center;
        justify-content: center;
        padding: 16px;
        z-index: 12000;
    }
    .app-confirm-backdrop { z-index: 12010; }
    .app-alert-backdrop.show,
    .app-confirm-backdrop.show { display: flex; }
    .app-alert-card,
    .app-confirm-card {
        width: 100%;
        max-width: 380px;
        background: #ffffff;
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        overflow: hidden;
        font-family: 'Plus Jakarta Sans', sans-serif;
        animation: appAlertSlideIn 0.25s cubic-bezier(0.34, 1.56, 0.64, 1) both;
    }
    @keyframes appAlertSlideIn {
        from { opacity: 0; transform: translateY(-12px) scale(0.97); }
        to { opacity: 1; transform: translateY(0) scale(1); }
    }
    .app-alert-header,
    .app-confirm-header {
        padding: 20px 24px;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
    }
    .app-alert-title-wrap {
        display: flex;
        align-items: center;
        gap: 10px;
        min-width: 0;
    }
    .app-alert-icon {
        width: 36px;
        height: 36px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .app-alert-card[data-variant="error"] .app-alert-icon {
        background: #fee2e2;
        color: #dc2626;
    }
    .app-alert-card[data-variant="success"] .app-alert-icon {
        background: #dcfce7;
        color: #16a34a;
    }
    .app-alert-card[data-variant="warning"] .app-alert-icon {
        background: #fff7ed;
        color: #ea580c;
    }
    .app-alert-card[data-variant="info"] .app-alert-icon {
        background: #eff6ff;
        color: #2563eb;
    }
    .app-alert-title,
    .app-confirm-title {
        margin: 0;
        font-size: 18px;
        font-weight: 700;
        color: #111827;
        line-height: 1.3;
    }
    .app-alert-close {
        background: transparent;
        border: none;
        color: #9ca3af;
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.2s;
        flex-shrink: 0;
    }
    .app-alert-close:hover {
        background: #f3f4f6;
        color: #4b5563;
    }
    .app-alert-body,
    .app-confirm-body {
        padding: 24px;
    }
    .app-alert-text,
    .app-confirm-text {
        font-size: 14px;
        color: #4b5563;
        line-height: 1.7;
        white-space: pre-line;
    }
    .app-alert-list {
        margin: 14px 0 0;
        padding-left: 18px;
        font-size: 13px;
        line-height: 1.7;
        color: #4b5563;
        display: none;
    }
    .app-alert-footer,
    .app-confirm-footer {
        padding: 24px;
        border-top: 1px solid #f1f5f9;
        background: #f9fafb;
        display: flex;
        justify-content: flex-end;
        gap: 12px;
    }
    .app-alert-btn,
    .app-confirm-cancel,
    .app-confirm-submit {
        border: none;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 600;
        padding: 10px 16px;
        cursor: pointer;
        transition: all 0.2s;
    }
    .app-alert-card[data-variant="error"] .app-alert-btn {
        background: #dc2626;
        color: #ffffff;
    }
    .app-alert-card[data-variant="error"] .app-alert-btn:hover {
        background: #b91c1c;
    }
    .app-alert-card[data-variant="success"] .app-alert-btn {
        background: #16a34a;
        color: #ffffff;
    }
    .app-alert-card[data-variant="success"] .app-alert-btn:hover {
        background: #15803d;
    }
    .app-alert-card[data-variant="warning"] .app-alert-btn {
        background: #ea580c;
        color: #ffffff;
    }
    .app-alert-card[data-variant="warning"] .app-alert-btn:hover {
        background: #c2410c;
    }
    .app-alert-card[data-variant="info"] .app-alert-btn {
        background: #2563eb;
        color: #ffffff;
    }
    .app-alert-card[data-variant="info"] .app-alert-btn:hover {
        background: #1d4ed8;
    }
    .app-confirm-cancel {
        background: #e5e7eb;
        color: #374151;
    }
    .app-confirm-cancel:hover {
        background: #d1d5db;
    }
    .app-confirm-submit {
        background: #dc2626;
        color: #ffffff;
    }
    .app-confirm-submit:hover {
        background: #b91c1c;
    }
</style>

<div id="appAlertModal" class="app-alert-backdrop" onclick="window.closeAppAlert?.(event)">
    <div id="appAlertCard" class="app-alert-card" data-variant="info" role="dialog" aria-modal="true" aria-labelledby="appAlertTitle">
        <div class="app-alert-header">
            <div class="app-alert-title-wrap">
                <div class="app-alert-icon" aria-hidden="true">
                    <svg id="appAlertIconSvg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8.25h.008v.008H12V8.25zm-.75 3h1.5v4.5h-1.5v-4.5zm9.75.75a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h3 id="appAlertTitle" class="app-alert-title">Informasi</h3>
            </div>
            <button type="button" class="app-alert-close" onclick="window.closeAppAlert?.()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="app-alert-body">
            <div id="appAlertText" class="app-alert-text"></div>
            <ul id="appAlertList" class="app-alert-list"></ul>
        </div>
        <div class="app-alert-footer">
            <button type="button" class="app-alert-btn" onclick="window.closeAppAlert?.()">Mengerti</button>
        </div>
    </div>
</div>

<div id="appConfirmModal" class="app-confirm-backdrop" onclick="window.closeAppConfirm?.(false, event)">
    <div class="app-confirm-card" role="dialog" aria-modal="true" aria-labelledby="appConfirmTitle">
        <div class="app-confirm-header">
            <h3 id="appConfirmTitle" class="app-confirm-title">Konfirmasi</h3>
            <button type="button" class="app-alert-close" onclick="window.closeAppConfirm?.(false)">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="app-confirm-body">
            <div id="appConfirmText" class="app-confirm-text">Apakah Anda yakin?</div>
        </div>
        <div class="app-confirm-footer">
            <button type="button" id="appConfirmCancel" class="app-confirm-cancel" onclick="window.closeAppConfirm?.(false)">Batal</button>
            <button type="button" id="appConfirmSubmit" class="app-confirm-submit" onclick="window.closeAppConfirm?.(true)">Ya, lanjutkan</button>
        </div>
    </div>
</div>

<script>
(() => {
    if (window.__appAlertInitialized) return;
    window.__appAlertInitialized = true;

    const backdrop = document.getElementById('appAlertModal');
    const card = document.getElementById('appAlertCard');
    const titleEl = document.getElementById('appAlertTitle');
    const textEl = document.getElementById('appAlertText');
    const listEl = document.getElementById('appAlertList');
    const iconEl = document.getElementById('appAlertIconSvg');
    const confirmBackdrop = document.getElementById('appConfirmModal');
    const confirmTitleEl = document.getElementById('appConfirmTitle');
    const confirmTextEl = document.getElementById('appConfirmText');
    const confirmSubmitEl = document.getElementById('appConfirmSubmit');
    const confirmCancelEl = document.getElementById('appConfirmCancel');
    let confirmResolver = null;

    const icons = {
        error: '<path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m0 3.75h.008v.008H12v-.008zm9-3.75a9 9 0 11-18 0 9 9 0 0118 0z"/>',
        success: '<path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75l2.25 2.25L15 9.75m6 2.25a9 9 0 11-18 0 9 9 0 0118 0z"/>',
        warning: '<path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m0 3.75h.008v.008H12v-.008zm-8.25 2.25h16.5a1.5 1.5 0 001.3-2.25L13.3 4.5a1.5 1.5 0 00-2.6 0L2.45 17.25a1.5 1.5 0 001.3 2.25z"/>',
        info: '<path stroke-linecap="round" stroke-linejoin="round" d="M12 8.25h.008v.008H12V8.25zm-.75 3h1.5v4.5h-1.5v-4.5zm9.75.75a9 9 0 11-18 0 9 9 0 0118 0z"/>'
    };

    function normalizeAlertOptions(input) {
        if (typeof input === 'object' && input !== null) {
            return {
                title: input.title,
                message: input.message ?? '',
                variant: input.variant,
                items: Array.isArray(input.items) ? input.items : [],
            };
        }

        const raw = String(input ?? '').trim();
        let variant = 'info';
        let title = 'Informasi';
        let message = raw;

        if (raw.startsWith('❌')) {
            variant = 'error';
            title = 'Terjadi Kendala';
            message = raw.replace(/^❌\s*/, '');
        } else if (raw.startsWith('✅') || raw.startsWith('✓')) {
            variant = 'success';
            title = 'Berhasil';
            message = raw.replace(/^[✅✓]\s*/, '');
        } else if (raw.startsWith('⚠️') || raw.startsWith('⚠')) {
            variant = 'warning';
            title = 'Perlu Diperhatikan';
            message = raw.replace(/^⚠️?\s*/, '');
        }

        return { title, message, variant, items: [] };
    }

    window.showAppAlert = function (input) {
        const options = normalizeAlertOptions(input);
        card.dataset.variant = options.variant || 'info';
        titleEl.textContent = options.title || 'Informasi';
        textEl.textContent = options.message || '';
        iconEl.innerHTML = icons[card.dataset.variant] || icons.info;

        const items = (options.items || []).filter(Boolean);
        if (items.length) {
            listEl.innerHTML = items.map(item => `<li>${item}</li>`).join('');
            listEl.style.display = 'block';
        } else {
            listEl.innerHTML = '';
            listEl.style.display = 'none';
        }

        backdrop.classList.add('show');
    };

    window.closeAppAlert = function (event) {
        if (event && event.target !== backdrop) return;
        backdrop.classList.remove('show');
    };

    window.showAppConfirm = function (message, options = {}) {
        confirmTitleEl.textContent = options.title || 'Konfirmasi';
        confirmTextEl.textContent = message || 'Apakah Anda yakin?';
        confirmSubmitEl.textContent = options.confirmText || 'Ya, lanjutkan';
        confirmCancelEl.textContent = options.cancelText || 'Batal';
        confirmSubmitEl.style.background = options.variant === 'primary' ? '#2563eb' : '#dc2626';
        confirmSubmitEl.onmouseenter = function () {
            this.style.background = options.variant === 'primary' ? '#1d4ed8' : '#b91c1c';
        };
        confirmSubmitEl.onmouseleave = function () {
            this.style.background = options.variant === 'primary' ? '#2563eb' : '#dc2626';
        };
        confirmBackdrop.classList.add('show');

        return new Promise((resolve) => {
            confirmResolver = resolve;
        });
    };

    window.closeAppConfirm = function (result, event) {
        if (event && event.target !== confirmBackdrop) return;
        confirmBackdrop.classList.remove('show');
        if (typeof confirmResolver === 'function') {
            const resolver = confirmResolver;
            confirmResolver = null;
            resolver(Boolean(result));
        }
    };

    window.appAlert = window.showAppAlert;
    window.appConfirm = window.showAppConfirm;
    window.alert = window.showAppAlert;

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape' && backdrop.classList.contains('show')) {
            backdrop.classList.remove('show');
        }
        if (event.key === 'Escape' && confirmBackdrop.classList.contains('show')) {
            window.closeAppConfirm(false);
        }
    });
})();
</script>
