{{--
    resources/views/components/notification-bell.blade.php
    Di-include di layout SETELAH </aside>, bukan di dalam sidebar.
--}}

{{-- Panel notifikasi --}}
<div class="notif-panel" id="notifPanel" role="dialog" aria-label="Panel Notifikasi">
    <div class="notif-panel-header">
        <span class="notif-panel-title">Notifikasi</span>
        <button class="notif-mark-all-btn" id="notifMarkAll">Tandai Semua Dibaca</button>
    </div>
    <div class="notif-panel-body" id="notifPanelBody">
        <div class="notif-empty-state">
            <i class="fas fa-bell"></i>
            <p>Ups, belum ada notifikasi</p>
        </div>
    </div>
</div>
<div class="notif-backdrop" id="notifBackdrop"></div>

<style>
/* ---------- Bell Button (ada di dalam .s-footer-top di layout) ---------- */
.notif-bell-btn {
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
    width: 32px;
    height: 32px;
    border-radius: 8px;
    border: none;
    background: transparent;
    color: #9aaabb;
    font-size: 14px;
    cursor: pointer;
    transition: background 0.17s ease, color 0.17s ease;
    flex-shrink: 0;
}

.notif-bell-btn:hover {
    background: var(--nav-hover-bg, #f3f7ff);
    color: var(--accent, #2356e8);
}

.notif-bell-btn.has-notif { color: var(--accent, #2356e8); }

/* Badge — HIDDEN by default */
.notif-badge {
    position: absolute;
    top: 3px;
    right: 3px;
    min-width: 15px;
    height: 15px;
    border-radius: 999px;
    background: #e53e3e;
    color: #fff;
    font-size: 8.5px;
    font-weight: 700;
    font-family: var(--nav-font, 'Plus Jakarta Sans', sans-serif);
    display: none;
    align-items: center;
    justify-content: center;
    padding: 0 3px;
    border: 1.5px solid #fff;
    pointer-events: none;
    line-height: 1;
}

.notif-badge.visible {
    display: flex;
    animation: badgePop .2s cubic-bezier(.36,.07,.19,.97) both;
}

@keyframes badgePop {
    0%   { transform: scale(0.5); opacity: 0; }
    70%  { transform: scale(1.2); }
    100% { transform: scale(1);   opacity: 1; }
}

/* ---------- Panel ---------- */
.notif-panel {
    position: fixed;
    bottom: 80px;
    left: calc(var(--sidebar-w, 220px) + 16px);
    width: 380px;
    max-height: 560px;
    background: #fff;
    border-radius: 14px;
    box-shadow: 0 8px 40px rgba(35,86,232,.13), 0 2px 8px rgba(0,0,0,.08);
    border: 1px solid #e8edf5;
    z-index: 99999;
    display: flex;
    flex-direction: column;
    overflow: hidden;
    opacity: 0;
    transform: translateY(8px) scale(.97);
    pointer-events: none;
    transition: opacity .18s ease, transform .18s ease;
}

.notif-panel.open {
    opacity: 1;
    transform: translateY(0) scale(1);
    pointer-events: all;
}

.notif-panel-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 16px 18px 14px;
    border-bottom: 1px solid #eff2f7;
    flex-shrink: 0;
}

.notif-panel-title {
    font-family: var(--nav-font, 'Plus Jakarta Sans', sans-serif);
    font-size: 15px;
    font-weight: 700;
    color: #111827;
}

.notif-mark-all-btn {
    background: none;
    border: none;
    font-family: var(--nav-font, 'Plus Jakarta Sans', sans-serif);
    font-size: 11.5px;
    font-weight: 600;
    color: var(--accent, #2356e8);
    cursor: pointer;
    padding: 3px 6px;
    border-radius: 5px;
    transition: background .15s;
}

.notif-mark-all-btn:hover { background: #e8f0fe; }

/* ---------- Body ---------- */
.notif-panel-body {
    flex: 1;
    overflow-y: auto;
    padding: 5px 0;
}

.notif-panel-body::-webkit-scrollbar { width: 4px; }
.notif-panel-body::-webkit-scrollbar-thumb { background: #d4dcea; border-radius: 4px; }

/* ---------- Empty State ---------- */
.notif-empty-state {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    height: 340px;
    gap: 12px;
    border: 2px dashed #dde5f0;
    border-radius: 12px;
    margin: 14px;
}

.notif-empty-state i { font-size: 36px; color: #c4cdd9; }
.notif-empty-state p {
    font-family: var(--nav-font, 'Plus Jakarta Sans', sans-serif);
    font-size: 13.5px;
    margin: 0;
    color: #a0aec0;
}

/* ---------- Notif Item ---------- */
.notif-item {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    padding: 13px 16px;
    cursor: pointer;
    transition: background .14s;
    border-bottom: 1px solid #f4f7fc;
}

.notif-item:last-child  { border-bottom: none; }
.notif-item:hover       { background: #f7f9ff; }
.notif-item.unread      { background: #f0f5ff; }
.notif-item.unread:hover{ background: #e8f0fe; }

.notif-item-icon {
    width: 40px; height: 40px;
    border-radius: 11px;
    display: flex; align-items: center; justify-content: center;
    font-size: 14px; flex-shrink: 0; margin-top: 1px;
}

.notif-item-icon.order   { background: #fff3e0; color: #f57c00; }
.notif-item-icon.payment { background: #e8f5e9; color: #2e7d32; }

.notif-item-content { flex: 1; min-width: 0; }

.notif-item-title {
    font-family: var(--nav-font, 'Plus Jakarta Sans', sans-serif);
    font-size: 13.5px; font-weight: 600; color: #111827;
    margin-bottom: 3px;
    white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
}

.notif-item-msg {
    font-family: var(--nav-font, 'Plus Jakarta Sans', sans-serif);
    font-size: 12.5px; color: #6b7c93; line-height: 1.45;
    overflow: hidden;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
}

.notif-item-time {
    font-size: 11px; color: #a0aec0; margin-top: 4px;
    font-family: var(--nav-font, 'Plus Jakarta Sans', sans-serif);
}

.notif-unread-dot {
    width: 7px; height: 7px; border-radius: 50%;
    background: var(--accent, #2356e8);
    flex-shrink: 0; margin-top: 5px;
}

/* ---------- Backdrop ---------- */
.notif-backdrop {
    display: none; position: fixed; inset: 0;
    z-index: 99998; background: transparent;
}
.notif-backdrop.active { display: block; }

/* ---------- Mobile ---------- */
@media (max-width: 768px) {
    .notif-panel { left: 10px; right: 10px; width: auto; bottom: 60px; }
}

/* ---------- Dark mode ---------- */
body.dark .notif-panel        { background: #1e2535; border-color: #2d3748; }
body.dark .notif-panel-header { border-color: #2d3748; }
body.dark .notif-panel-title  { color: #f0f4ff; }
body.dark .notif-item         { border-color: #252e42; }
body.dark .notif-item:hover   { background: #252e42; }
body.dark .notif-item.unread  { background: #1a2540; }
body.dark .notif-item-title   { color: #e8edf8; }
body.dark .notif-item-msg     { color: #8899b4; }
body.dark .notif-empty-state  { border-color: #2d3748; }
body.dark .notif-badge        { border-color: #1e2535; }
</style>

<script>
(function () {
    const POLL_INTERVAL = 3000;
    const API_LIST      = '/notifications';
    const API_MARK_ALL  = '/notifications/mark-all';
    const API_MARK_ONE  = function(id) { return '/notifications/' + id + '/read'; };
    const CSRF          = document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').content : '';

    const panel      = document.getElementById('notifPanel');
    const panelBody  = document.getElementById('notifPanelBody');
    const markAllBtn = document.getElementById('notifMarkAll');
    const backdrop   = document.getElementById('notifBackdrop');

    // Bell sudah ada di HTML layout (bukan di-inject JS)
    let bellBtn  = null;
    let badge    = null;

    let prevUnread    = -1;
    let panelOpen     = false;
    let notifications = [];

    // ---- Browser Push Notification ----
    var notifPermission = 'default';

    function requestNotifPermission() {
        if (!('Notification' in window)) return;
        if (Notification.permission === 'granted') {
            notifPermission = 'granted';
            return;
        }
        if (Notification.permission !== 'denied') {
            Notification.requestPermission().then(function(permission) {
                notifPermission = permission;
            });
        } else {
            notifPermission = Notification.permission;
        }
    }

    function showBrowserNotif(title, message, type) {
        if (!('Notification' in window)) return;
        if (Notification.permission !== 'granted') return;
        var icon = type === 'payment'
            ? 'https://cdn-icons-png.flaticon.com/512/190/190411.png'
            : 'https://cdn-icons-png.flaticon.com/512/891/891462.png';
        try {
            var n = new Notification(title, {
                body: message,
                icon: icon,
                badge: icon,
                tag: 'payou-notif-' + Date.now(),
                requireInteraction: false,
            });
            n.onclick = function() {
                window.focus();
                n.close();
            };
            setTimeout(function() { n.close(); }, 6000);
        } catch(e) {}
    }
    var AudioCtxClass = window.AudioContext || window.webkitAudioContext;
    var ctx           = null;
    var audioReady    = false; // true setelah user gesture pertama

    function getCtx() {
        if (!AudioCtxClass) return null;
        if (!ctx) ctx = new AudioCtxClass();
        return ctx;
    }

    // Unlock AudioContext pada interaksi pertama user (klik/touch/keydown)
    function unlockAudio() {
        var ac = getCtx();
        if (!ac) return;
        if (ac.state === 'suspended') {
            ac.resume().then(function() { audioReady = true; });
        } else {
            audioReady = true;
        }
    }

    // Pasang listener sekali saja
    ['click','touchstart','keydown'].forEach(function(evt) {
        document.addEventListener(evt, function handler() {
            unlockAudio();
            document.removeEventListener(evt, handler);
        }, { once: true });
    });

    function playOrderSound() {
        // Pesanan masuk: "Tinggg" — sine, naik oktaf
        try {
            var ac = getCtx();
            if (!ac) return;
            if (ac.state === 'suspended') { ac.resume(); return; }

            var osc = ac.createOscillator();
            var env = ac.createGain();
            osc.connect(env);
            env.connect(ac.destination);

            osc.type = 'sine';
            osc.frequency.setValueAtTime(660, ac.currentTime);
            osc.frequency.exponentialRampToValueAtTime(1320, ac.currentTime + 0.15);

            env.gain.setValueAtTime(0.0001, ac.currentTime);
            env.gain.exponentialRampToValueAtTime(0.4, ac.currentTime + 0.05);
            env.gain.exponentialRampToValueAtTime(0.0001, ac.currentTime + 0.7);

            osc.start(ac.currentTime);
            osc.stop(ac.currentTime + 0.75);
        } catch(e) { console.warn('playOrderSound error', e); }
    }

    function playPaymentSound() {
        // Pembayaran masuk: "Cengkringg" — triangle, 2 nada sparkly
        try {
            var ac = getCtx();
            if (!ac) return;
            if (ac.state === 'suspended') { ac.resume(); return; }

            // Nada 1
            var o1 = ac.createOscillator(); var g1 = ac.createGain();
            o1.connect(g1); g1.connect(ac.destination);
            o1.type = 'triangle';
            o1.frequency.setValueAtTime(1047, ac.currentTime);
            g1.gain.setValueAtTime(0.0001, ac.currentTime);
            g1.gain.exponentialRampToValueAtTime(0.35, ac.currentTime + 0.03);
            g1.gain.exponentialRampToValueAtTime(0.0001, ac.currentTime + 0.3);
            o1.start(ac.currentTime);
            o1.stop(ac.currentTime + 0.35);

            // Nada 2 (sedikit terlambat, lebih tinggi)
            var o2 = ac.createOscillator(); var g2 = ac.createGain();
            o2.connect(g2); g2.connect(ac.destination);
            o2.type = 'triangle';
            o2.frequency.setValueAtTime(1568, ac.currentTime + 0.13);
            g2.gain.setValueAtTime(0.0001, ac.currentTime + 0.13);
            g2.gain.exponentialRampToValueAtTime(0.28, ac.currentTime + 0.16);
            g2.gain.exponentialRampToValueAtTime(0.0001, ac.currentTime + 0.6);
            o2.start(ac.currentTime + 0.13);
            o2.stop(ac.currentTime + 0.65);
        } catch(e) { console.warn('playPaymentSound error', e); }
    }

    // ---- Helpers ----
    function timeAgo(dateStr) {
        var diff = Math.floor((Date.now() - new Date(dateStr).getTime()) / 1000);
        if (diff < 60)    return 'Baru saja';
        if (diff < 3600)  return Math.floor(diff/60) + ' menit lalu';
        if (diff < 86400) return Math.floor(diff/3600) + ' jam lalu';
        return Math.floor(diff/86400) + ' hari lalu';
    }

    function escHtml(str) {
        return String(str).replace(/[&<>"']/g, function(c) {
            return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c];
        });
    }

    // ---- Badge: hanya tampil jika count > 0 ----
    function updateBadge(count) {
        if (!badge) return;
        if (count > 0) {
            badge.textContent = count > 99 ? '99+' : count;
            badge.classList.add('visible');
            bellBtn && bellBtn.classList.add('has-notif');
        } else {
            badge.classList.remove('visible');
            bellBtn && bellBtn.classList.remove('has-notif');
        }
    }

    // ---- Render list ----
    function render(list) {
        panelBody.innerHTML = '';
        if (!list.length) {
            panelBody.innerHTML = '<div class="notif-empty-state"><i class="fas fa-bell"></i><p>Ups, belum ada notifikasi</p></div>';
            return;
        }
        list.forEach(function(n) {
            var iconClass = n.type === 'payment' ? 'fas fa-circle-check' : (n.icon || 'fas fa-shopping-bag');
            var typeClass = n.type === 'payment' ? 'payment' : 'order';
            var el = document.createElement('div');
            el.className = 'notif-item' + (n.is_read ? '' : ' unread');
            el.dataset.id = n.id;
            el.innerHTML =
                '<div class="notif-item-icon ' + typeClass + '"><i class="' + iconClass + '"></i></div>' +
                '<div class="notif-item-content">' +
                    '<div class="notif-item-title">' + escHtml(n.title) + '</div>' +
                    '<div class="notif-item-msg">' + escHtml(n.message) + '</div>' +
                    '<div class="notif-item-time">' + timeAgo(n.created_at) + '</div>' +
                '</div>' +
                (!n.is_read ? '<div class="notif-unread-dot"></div>' : '');
            el.addEventListener('click', function() { markOne(n.id, el, n.link); });
            panelBody.appendChild(el);
        });
    }

    // ---- Fetch ----
    async function fetchNotifs() {
        try {
            var res = await fetch(API_LIST, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
            if (!res.ok) return;
            var data = await res.json();
            notifications = data.notifications;
            var newUnread = data.unread_count;

            if (prevUnread >= 0 && newUnread > prevUnread) {
                var newest     = notifications.slice(0, newUnread - prevUnread);
                var hasPayment = newest.some(function(n) { return n.type === 'payment' && !n.is_read; });
                var hasOrder   = newest.some(function(n) { return n.type === 'order'   && !n.is_read; });
                if (hasPayment) {
                    playPaymentSound();
                    var pn = newest.find(function(n) { return n.type === 'payment' && !n.is_read; });
                    if (pn) showBrowserNotif(pn.title, pn.message, 'payment');
                } else if (hasOrder) {
                    playOrderSound();
                    var on = newest.find(function(n) { return n.type === 'order' && !n.is_read; });
                    if (on) showBrowserNotif(on.title, on.message, 'order');
                }
            }

            prevUnread = newUnread;
            updateBadge(newUnread);
            if (panelOpen) render(notifications);
        } catch(e) {}
    }

    // ---- Mark read ----
    async function markOne(id, el, link) {
        try {
            await fetch(API_MARK_ONE(id), {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': CSRF, 'X-Requested-With': 'XMLHttpRequest' }
            });
            el.classList.remove('unread');
            var dot = el.querySelector('.notif-unread-dot');
            if (dot) dot.remove();
            prevUnread = Math.max(0, prevUnread - 1);
            updateBadge(prevUnread);
            if (link) { closePanel(); window.location.href = link; }
        } catch(e) {}
    }

    async function markAll() {
        try {
            await fetch(API_MARK_ALL, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': CSRF, 'X-Requested-With': 'XMLHttpRequest' }
            });
            prevUnread = 0;
            updateBadge(0);
            document.querySelectorAll('.notif-item.unread').forEach(function(el) {
                el.classList.remove('unread');
                var dot = el.querySelector('.notif-unread-dot');
                if (dot) dot.remove();
            });
        } catch(e) {}
    }

    // ---- Panel ----
    function openPanel()  { panelOpen = true;  render(notifications); panel.classList.add('open');    backdrop.classList.add('active'); }
    function closePanel() { panelOpen = false; panel.classList.remove('open'); backdrop.classList.remove('active'); }

    // ---- Init setelah DOM siap ----
    document.addEventListener('DOMContentLoaded', function () {
        bellBtn = document.getElementById('notifBellBtn');
        badge   = document.getElementById('notifBadge');

        // Minta izin notifikasi browser
        requestNotifPermission();

        if (bellBtn) {
            bellBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                unlockAudio(); // pastikan audio siap saat user klik lonceng
                panelOpen ? closePanel() : openPanel();
            });
        }

        backdrop.addEventListener('click', closePanel);
        markAllBtn.addEventListener('click', function(e) { e.stopPropagation(); markAll(); });
        document.addEventListener('keydown', function(e) { if (e.key === 'Escape' && panelOpen) closePanel(); });

        fetchNotifs();
        setInterval(fetchNotifs, POLL_INTERVAL);
    });

})();
</script>