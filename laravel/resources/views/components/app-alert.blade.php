<style>
    .app-alert-backdrop {
        position: fixed;
        inset: 0;
        background: rgba(12, 21, 51, .45);
        display: none;
        align-items: center;
        justify-content: center;
        padding: 20px;
        z-index: 12000;
    }
    .app-alert-backdrop.show { display: flex; }
    .app-alert-card {
        width: 100%;
        max-width: 420px;
        background: #fff;
        border-radius: 18px;
        padding: 22px 20px 18px;
        border: 1.5px solid #dbe4ff;
        box-shadow: 0 20px 60px rgba(12, 21, 51, .18);
        text-align: left;
        font-family: 'Plus Jakarta Sans', sans-serif;
    }
    .app-alert-head {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        margin-bottom: 14px;
    }
    .app-alert-icon {
        width: 42px;
        height: 42px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .app-alert-card[data-variant="error"] .app-alert-icon {
        background: #fee2e2;
        color: #b91c1c;
    }
    .app-alert-card[data-variant="success"] .app-alert-icon {
        background: #dcfce7;
        color: #15803d;
    }
    .app-alert-card[data-variant="warning"] .app-alert-icon {
        background: #fff7ed;
        color: #c2410c;
    }
    .app-alert-card[data-variant="info"] .app-alert-icon {
        background: #eff6ff;
        color: #1d4ed8;
    }
    .app-alert-card[data-variant="error"] {
        border-color: #fecaca;
    }
    .app-alert-card[data-variant="success"] {
        border-color: #bbf7d0;
    }
    .app-alert-card[data-variant="warning"] {
        border-color: #fed7aa;
    }
    .app-alert-title {
        font-size: 15px;
        font-weight: 800;
        color: #0c1533;
        margin-bottom: 4px;
    }
    .app-alert-text {
        font-size: 12px;
        line-height: 1.7;
        color: #5b6785;
        white-space: pre-line;
    }
    .app-alert-list {
        margin: 14px 0 0;
        padding-left: 18px;
        font-size: 12px;
        font-weight: 700;
        line-height: 1.7;
        color: #334155;
        display: none;
    }
    .app-alert-card[data-variant="error"] .app-alert-list { color: #b91c1c; }
    .app-alert-card[data-variant="success"] .app-alert-list { color: #15803d; }
    .app-alert-card[data-variant="warning"] .app-alert-list { color: #c2410c; }
    .app-alert-actions {
        display: flex;
        justify-content: flex-end;
        margin-top: 18px;
    }
    .app-alert-btn {
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
</style>

<div id="appAlertModal" class="app-alert-backdrop" onclick="window.closeAppAlert?.(event)">
    <div id="appAlertCard" class="app-alert-card" data-variant="info" role="dialog" aria-modal="true" aria-labelledby="appAlertTitle">
        <div class="app-alert-head">
            <div class="app-alert-icon" id="appAlertIcon" aria-hidden="true">
                <svg id="appAlertIconSvg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8.25h.008v.008H12V8.25zm-.75 3h1.5v4.5h-1.5v-4.5zm9.75.75a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <div id="appAlertTitle" class="app-alert-title">Informasi</div>
                <div id="appAlertText" class="app-alert-text"></div>
            </div>
        </div>
        <ul id="appAlertList" class="app-alert-list"></ul>
        <div class="app-alert-actions">
            <button type="button" class="app-alert-btn" onclick="window.closeAppAlert?.()">Mengerti</button>
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

    window.appAlert = window.showAppAlert;
    window.alert = window.showAppAlert;

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape' && backdrop.classList.contains('show')) {
            backdrop.classList.remove('show');
        }
    });
})();
</script>
