{{--
    resources/views/components/notification-bell.blade.php
    Di-include di layout SETELAH </aside>, bukan di dalam sidebar.
--}}

{{-- Panel notifikasi --}}
<div class="notif-panel" id="notifPanel" role="dialog" aria-label="Panel Notifikasi">
    <div class="notif-panel-header">
        <span class="notif-panel-title">Notifikasi</span>
        <div class="notif-header-actions">
            <button class="notif-mark-all-btn" id="notifMarkAll">Tandai Semua Dibaca</button>
            <button class="notif-delete-all-btn" id="notifDeleteAll" title="Hapus Semua">
                <i class="fas fa-trash-alt"></i>
            </button>
        </div>
    </div>
    <div class="notif-panel-body" id="notifPanelBody">
        <div class="notif-empty-state">
            <i class="fas fa-bell"></i>
            <p>Ups, belum ada notifikasi</p>
        </div>
    </div>

    {{-- Footer: Lihat Semua --}}
    <div class="notif-panel-footer">
        <a href="{{ route('notifications.index') }}" class="notif-view-all-btn">
            <i class="fas fa-list"></i> Lihat Semua Notifikasi
        </a>
    </div>

</div>
<div class="notif-backdrop" id="notifBackdrop"></div>

{{-- Audio elements untuk notifikasi --}}
<audio id="notifAudioPayment" preload="auto">
    <source src="/sounds/payment.mp3" type="audio/mpeg">
</audio>
<audio id="notifAudioCheckout" preload="auto">
    <source src="/sounds/checkout.mp3" type="audio/mpeg">
</audio>

<style>
/* ---------- Bell Button ---------- */
.notif-bell-btn {
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
    width: 38px;
    height: 38px;
    border-radius: 10px;
    border: none;
    background: transparent;
    color: #9aaabb;
    font-size: 17px;
    cursor: pointer;
    transition: background 0.17s ease, color 0.17s ease;
    flex-shrink: 0;
}

.notif-bell-btn i { line-height: 1; }

.notif-bell-btn:hover {
    background: var(--nav-hover-bg, #f3f7ff);
    color: var(--accent, #2356e8);
}

.notif-bell-btn.has-notif { color: var(--accent, #2356e8); }

/* Badge */
.notif-badge {
    position: absolute;
    top: 2px; right: 2px;
    min-width: 15px; height: 15px;
    border-radius: 999px;
    background: #e53e3e;
    color: #fff;
    font-size: 8.5px; font-weight: 700;
    font-family: var(--nav-font, 'Plus Jakarta Sans', sans-serif);
    display: none;
    align-items: center; justify-content: center;
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
    top: 90px;
    left: calc(var(--sidebar-w, 220px) + 16px);
    width: 380px;
    max-height: 530px;
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
    font-size: 15px; font-weight: 700;
    color: #111827;
}

/* Header actions group */
.notif-header-actions {
    display: flex;
    align-items: center;
    gap: 6px;
}

.notif-mark-all-btn {
    background: none; border: none;
    font-family: var(--nav-font, 'Plus Jakarta Sans', sans-serif);
    font-size: 11.5px; font-weight: 600;
    color: var(--accent, #2356e8);
    cursor: pointer;
    padding: 3px 6px;
    border-radius: 5px;
    transition: background .15s;
}

.notif-mark-all-btn:hover { background: #e8f0fe; }

/* Tombol hapus semua */
.notif-delete-all-btn {
    background: none; border: none;
    color: #cbd5e0;
    font-size: 12px;
    cursor: pointer;
    padding: 4px 6px;
    border-radius: 5px;
    transition: background .15s, color .15s;
    display: flex; align-items: center; justify-content: center;
}

.notif-delete-all-btn:hover {
    background: #fff5f5;
    color: #e53e3e;
}

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
    font-size: 13.5px; margin: 0; color: #a0aec0;
}


/* ---------- Panel Footer ---------- */
.notif-panel-footer {
    padding: 10px 14px;
    border-top: 1px solid #eff2f7;
    flex-shrink: 0;
}

.notif-view-all-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 7px;
    width: 100%;
    padding: 9px;
    border-radius: 9px;
    background: #f0f5ff;
    color: var(--accent, #2356e8);
    font-family: var(--nav-font, 'Plus Jakarta Sans', sans-serif);
    font-size: 13px;
    font-weight: 600;
    text-decoration: none;
    transition: background .15s;
}

.notif-view-all-btn:hover {
    background: #e1ebff;
}

body.dark .notif-panel-footer { border-color: #2d3748; }
body.dark .notif-view-all-btn { background: #1a2540; color: #7ea4f4; }
body.dark .notif-view-all-btn:hover { background: #1e2d50; }

/* ---------- Notif Item ---------- */
.notif-item {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    padding: 13px 16px;
    cursor: pointer;
    transition: background .14s;
    border-bottom: 1px solid #f4f7fc;
    position: relative;
}

.notif-item:last-child  { border-bottom: none; }
.notif-item:hover       { background: #f7f9ff; }
.notif-item.unread      { background: #f0f5ff; }
.notif-item.unread:hover{ background: #e8f0fe; }

/* Tombol hapus per item — muncul saat hover, ditengah secara vertikal */
.notif-item-delete {
    width: 30px; height: 30px;
    border-radius: 8px;
    background: none; border: none;
    color: #c4cdd9;
    font-size: 15px;
    display: flex; align-items: center; justify-content: center;
    cursor: pointer;
    opacity: 0;
    flex-shrink: 0;
    align-self: center;
    transition: opacity .15s, background .15s, color .15s;
    z-index: 2;
}

.notif-item:hover .notif-item-delete { opacity: 1; }

.notif-item-delete:hover {
    background: #fff5f5;
    color: #e53e3e;
}

.notif-item-icon {
    width: 40px; height: 40px;
    border-radius: 11px;
    display: flex; align-items: center; justify-content: center;
    font-size: 14px; flex-shrink: 0; margin-top: 1px;
}

.notif-item-icon.order   { background: #fff3e0; color: #f57c00; }
.notif-item-icon.payment { background: #e8f5e9; color: #2e7d32; }
.notif-item-icon.checkout { background: #eef2ff; color: #4338ca; }

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
    .notif-panel { left: 10px; right: 10px; width: auto; bottom: 60px; top: auto; }
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
body.dark .notif-delete-all-btn { color: #4a5568; }
body.dark .notif-delete-all-btn:hover { background: #2d1f1f; color: #fc8181; }
body.dark .notif-item-delete  { color: #4a5568; }
body.dark .notif-item-delete:hover { background: #2d1f1f; color: #fc8181; }
</style>

<script>
(function () {
    var POLL_INTERVAL  = 3000;
    var API_LIST       = '/notifications';
    var API_MARK_ALL   = '/notifications/mark-all';
    var API_MARK_ONE   = function(id) { return '/notifications/' + id + '/read'; };
    var API_DELETE_ONE = function(id) { return '/notifications/' + id; };
    var API_DELETE_ALL = '/notifications/all';
    var CSRF           = document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').content : '';

    var panel         = document.getElementById('notifPanel');
    var panelBody     = document.getElementById('notifPanelBody');
    var markAllBtn    = document.getElementById('notifMarkAll');
    var deleteAllBtn  = document.getElementById('notifDeleteAll');
    var backdrop      = document.getElementById('notifBackdrop');

    var audioPayment  = document.getElementById('notifAudioPayment');
    var audioCheckout = document.getElementById('notifAudioCheckout');

    var bellBtns   = [];
    var badgeEls   = [];
    var activeBell = null;

    var prevUnread     = -1;
    var panelOpen      = false;
    var notifications  = [];
    var justDeletedAll = false; // FIX: sentinel agar polling tidak anggap notif lama sebagai baru

    // ---- Browser Push Notification ----
    function requestNotifPermission() {
        if (!('Notification' in window)) return;
        if (Notification.permission === 'granted') return;
        if (Notification.permission !== 'denied') {
            Notification.requestPermission().then(function(p) {});
        }
    }

    function showBrowserNotif(title, message, type) {
        if (!('Notification' in window) || Notification.permission !== 'granted') return;
        var icon = type === 'payment'
            ? 'https://cdn-icons-png.flaticon.com/512/190/190411.png'
            : (type === 'checkout'
                ? 'https://cdn-icons-png.flaticon.com/512/1170/1170576.png'
                : 'https://cdn-icons-png.flaticon.com/512/891/891462.png');
        try {
            var n = new Notification(title, { body: message, icon: icon, tag: 'payou-notif-' + Date.now() });
            n.onclick = function() { window.focus(); n.close(); };
            setTimeout(function() { n.close(); }, 6000);
        } catch(e) {}
    }

    // ---- Audio ----
    function unlockAudio() {
        [audioPayment, audioCheckout].forEach(function(el) {
            if (!el) return;
            el.volume = 0;
            var p = el.play();
            if (p && p.then) {
                p.then(function() { el.pause(); el.currentTime = 0; el.volume = 1; })
                 .catch(function() { el.volume = 1; });
            } else { el.pause(); el.currentTime = 0; el.volume = 1; }
        });
    }

    ['click','touchstart','keydown'].forEach(function(evt) {
        document.addEventListener(evt, function handler() {
            unlockAudio();
            document.removeEventListener(evt, handler);
        }, { once: true });
    });

    function playPaymentSound() {
        if (!audioPayment) return;
        try { audioPayment.currentTime = 0; audioPayment.volume = 1; var p = audioPayment.play(); if (p && p.catch) p.catch(function(){}); } catch(e) {}
    }

    function playCheckoutSound() {
        if (!audioCheckout) return;
        try { audioCheckout.currentTime = 0; audioCheckout.volume = 1; var p = audioCheckout.play(); if (p && p.catch) p.catch(function(){}); } catch(e) {}
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
            return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":"&#39;"}[c];
        });
    }

    // ---- Badge ----
    function updateBadge(count) {
        if (!badgeEls.length) return;
        if (count > 0) {
            badgeEls.forEach(function(badge) {
                badge.textContent = count > 99 ? '99+' : count;
                badge.classList.add('visible');
            });
            bellBtns.forEach(function(btn) { btn.classList.add('has-notif'); });
        } else {
            badgeEls.forEach(function(badge) { badge.classList.remove('visible'); });
            bellBtns.forEach(function(btn) { btn.classList.remove('has-notif'); });
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
            var iconClass = n.type === 'payment'
                ? 'fas fa-circle-check'
                : (n.type === 'checkout' ? 'fas fa-cash-register' : (n.icon || 'fas fa-shopping-bag'));
            var typeClass = n.type === 'payment' ? 'payment' : (n.type === 'checkout' ? 'checkout' : 'order');
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
                (!n.is_read ? '<div class="notif-unread-dot"></div>' : '') +
                '<button class="notif-item-delete" title="Hapus notifikasi"><i class="fas fa-times"></i></button>';

            // Klik item → mark read
            el.addEventListener('click', function(e) {
                if (e.target.closest('.notif-item-delete')) return;
                markOne(n.id, el, n.link);
            });

            // Klik tombol hapus
            el.querySelector('.notif-item-delete').addEventListener('click', function(e) {
                e.stopPropagation();
                deleteOne(n.id, el);
            });

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

            // FIX: Jika baru saja delete all, sync prevUnread ke server tanpa bunyi suara
            if (justDeletedAll) {
                justDeletedAll = false;
                prevUnread = newUnread;
                updateBadge(newUnread);
                if (panelOpen) render(notifications);
                return;
            }

            if (prevUnread >= 0 && newUnread > prevUnread) {
                var newest      = notifications.slice(0, newUnread - prevUnread);
                var hasPayment  = newest.some(function(n) { return n.type === 'payment'  && !n.is_read; });
                var hasCheckout = newest.some(function(n) { return n.type === 'checkout' && !n.is_read; });
                var hasOrder    = newest.some(function(n) { return n.type === 'order'    && !n.is_read; });
                if (hasPayment) {
                    playPaymentSound();
                    var pn = newest.find(function(n) { return n.type === 'payment' && !n.is_read; });
                    if (pn) showBrowserNotif(pn.title, pn.message, 'payment');
                } else if (hasCheckout) {
                    playCheckoutSound();
                    var cn = newest.find(function(n) { return n.type === 'checkout' && !n.is_read; });
                    if (cn) showBrowserNotif(cn.title, cn.message, 'checkout');
                } else if (hasOrder) {
                    playCheckoutSound();
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

    // ---- Delete ----
    async function deleteOne(id, el) {
        try {
            var wasUnread = el.classList.contains('unread');
            el.style.transition = 'opacity .18s, transform .18s';
            el.style.opacity = '0';
            el.style.transform = 'translateX(16px)';
            var res = await fetch(API_DELETE_ONE(id), {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': CSRF, 'X-Requested-With': 'XMLHttpRequest' }
            });
            if (!res.ok) {
                // Batalkan animasi jika server gagal
                el.style.opacity = '1';
                el.style.transform = 'translateX(0)';
                return;
            }
            setTimeout(function() { el.remove(); checkEmpty(); }, 180);
            notifications = notifications.filter(function(n) { return n.id != id; });
            if (wasUnread) {
                prevUnread = Math.max(0, prevUnread - 1);
                updateBadge(prevUnread);
            }
        } catch(e) {}
    }

    // FIX: deleteAll — cek res.ok + set justDeletedAll agar polling tidak bunyi suara
    async function deleteAll() {
        if (!notifications.length) return;
        if (!confirm('Hapus semua notifikasi?')) return;
        try {
            var res = await fetch(API_DELETE_ALL, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': CSRF, 'X-Requested-With': 'XMLHttpRequest' }
            });
            if (!res.ok) return; // Hentikan jika server gagal hapus
            notifications  = [];
            prevUnread     = 0;
            justDeletedAll = true; // Sentinel: polling berikutnya skip deteksi "notif baru"
            updateBadge(0);
            render([]);
        } catch(e) {}
    }

    function checkEmpty() {
        if (!panelBody.querySelector('.notif-item')) {
            panelBody.innerHTML = '<div class="notif-empty-state"><i class="fas fa-bell"></i><p>Ups, belum ada notifikasi</p></div>';
        }
    }

    // ---- Panel ----
    function positionPanelNearBell() {
        var anchorBtn = activeBell || bellBtns[0];
        if (!anchorBtn || !panel) return;
        if (window.innerWidth <= 768) { panel.style.top = ''; panel.style.left = ''; return; }
        var rect = anchorBtn.getBoundingClientRect();
        var panelWidth = 380, gap = 10;
        var top  = Math.max(12, rect.top - 2);
        var left = rect.right + gap;
        var maxLeft = window.innerWidth - panelWidth - 12;
        if (left > maxLeft) left = maxLeft;
        panel.style.top = top + 'px';
        panel.style.left = left + 'px';
    }

    function openPanel()  { panelOpen = true;  render(notifications); positionPanelNearBell(); panel.classList.add('open'); backdrop.classList.add('active'); }
    function closePanel() { panelOpen = false; panel.classList.remove('open'); backdrop.classList.remove('active'); }

    // ---- Init ----
    document.addEventListener('DOMContentLoaded', function () {
        bellBtns = Array.prototype.slice.call(document.querySelectorAll('.notif-bell-trigger'));
        badgeEls = Array.prototype.slice.call(document.querySelectorAll('.notif-badge-trigger'));

        requestNotifPermission();

        bellBtns.forEach(function(btn) {
            btn.addEventListener('click', function(e) {
                e.stopPropagation();
                activeBell = btn;
                unlockAudio();
                panelOpen ? closePanel() : openPanel();
            });
        });

        backdrop.addEventListener('click', closePanel);
        markAllBtn.addEventListener('click', function(e) { e.stopPropagation(); markAll(); });
        deleteAllBtn.addEventListener('click', function(e) { e.stopPropagation(); deleteAll(); });
        document.addEventListener('keydown', function(e) { if (e.key === 'Escape' && panelOpen) closePanel(); });
        window.addEventListener('resize', function() { if (panelOpen) positionPanelNearBell(); });

        fetchNotifs();
        setInterval(fetchNotifs, POLL_INTERVAL);
    });

})();
</script>