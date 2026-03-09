<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>{{ $user->name }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: system-ui, -apple-system, sans-serif; background: #f9fafb; min-height: 100vh; }
        .page-wrapper { max-width: 420px; margin: 0 auto; background: #f9fafb; min-height: 100vh; position: relative; overflow-x: hidden; }

        /* Toast */
        .toast { position: fixed; bottom: 24px; left: 50%; transform: translateX(-50%) translateY(80px); background: #111827; color: #fff; padding: 10px 20px; border-radius: 50px; font-size: 13px; font-weight: 500; z-index: 9999; opacity: 0; transition: all 0.35s cubic-bezier(.34,1.56,.64,1); white-space: nowrap; pointer-events: none; }
        .toast.show    { opacity: 1; transform: translateX(-50%) translateY(0); }
        .toast.success { background: #16a34a; }
        .toast.error   { background: #dc2626; }

        /* Navbar */
        .navbar { background: #fff; border-bottom: 1px solid #e5e7eb; position: sticky; top: 0; z-index: 100; box-shadow: 0 1px 3px rgba(0,0,0,0.05); }
        .navbar-container { padding: 12px 16px; display: flex; justify-content: space-between; align-items: center; }
        .navbar-left  { display: flex; align-items: center; gap: 12px; }
        .navbar-title { font-size: 16px; font-weight: 600; color: #111827; }
        .navbar-right { display: flex; gap: 8px; }
        .nav-icon { width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; border-radius: 8px; cursor: pointer; transition: background 0.2s; position: relative; color: #374151; }
        .nav-icon:hover       { background: #f3f4f6; }
        .nav-icon.report      { color: #be123c; }
        .nav-icon.report:hover { background: #fff1f2; }
        .cart-badge { position: absolute; top: 2px; right: 2px; background: #ef4444; color: white; font-size: 10px; font-weight: 600; width: 16px; height: 16px; border-radius: 50%; display: none; align-items: center; justify-content: center; }
        .cart-badge.visible { display: flex; }
        .hamburger { width: 34px; height: 34px; display: grid; grid-template-columns: repeat(3, 4px); grid-template-rows: repeat(3, 4px); gap: 4px; place-content: center; cursor: pointer; border-radius: 8px; }
        .hamburger span { width: 4px; height: 4px; background: #374151; border-radius: 50%; display: block; }

        /* Search */
        .search-bar-wrap { position: sticky; top: 61px; z-index: 99; background: #fff; border-bottom: 1px solid #e5e7eb; max-height: 0; overflow: hidden; transition: max-height 0.35s cubic-bezier(.4,0,.2,1), box-shadow 0.35s; }
        .search-bar-wrap.open { max-height: 72px; box-shadow: 0 4px 12px rgba(0,0,0,0.06); }
        .search-bar-inner { padding: 12px 16px; display: flex; align-items: center; gap: 8px; }
        .search-back-btn { width: 34px; height: 34px; flex-shrink: 0; display: flex; align-items: center; justify-content: center; border: none; background: none; cursor: pointer; border-radius: 8px; color: #6b7280; }
        .search-back-btn:hover { background: #f3f4f6; }
        .search-input-wrap { flex: 1; position: relative; }
        .search-input { width: 100%; height: 40px; padding: 0 38px 0 14px; border: 1.5px solid #e5e7eb; border-radius: 10px; font-size: 14px; color: #111827; background: #f9fafb; outline: none; -webkit-appearance: none; appearance: none; }
        .search-input:focus { border-color: #2563eb; background: #fff; }
        .search-input::placeholder { color: #9ca3af; }
        .search-input::-webkit-search-decoration, .search-input::-webkit-search-cancel-button { -webkit-appearance: none; display: none !important; }
        .search-clear-btn { position: absolute; right: 10px; top: 50%; transform: translateY(-50%); width: 20px; height: 20px; border-radius: 50%; background: #d1d5db; color: #fff; border: none; cursor: pointer; display: none; align-items: center; justify-content: center; padding: 0; }
        .search-clear-btn svg { width: 10px; height: 10px; }
        .search-clear-btn.visible { display: flex; }
        .search-results-overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.35); backdrop-filter: blur(2px); z-index: 98; opacity: 0; visibility: hidden; transition: opacity 0.25s, visibility 0.25s; }
        .search-results-overlay.active { opacity: 1; visibility: visible; }
        .search-results-panel { position: absolute; top: 133px; left: 0; right: 0; max-height: calc(100vh - 153px); background: #fff; border-radius: 0 0 16px 16px; overflow-y: auto; z-index: 99; box-shadow: 0 8px 32px rgba(0,0,0,0.12); padding-bottom: 12px; opacity: 0; transform: translateY(-8px); transition: opacity 0.25s, transform 0.25s; pointer-events: none; }
        .search-results-panel.active { opacity: 1; transform: translateY(0); pointer-events: auto; }
        .search-result-item { display: flex; align-items: center; gap: 12px; padding: 12px 16px; cursor: pointer; border-bottom: 1px solid #f9fafb; }
        .search-result-item:last-child { border-bottom: none; }
        .search-result-item:hover { background: #f9fafb; }
        .search-result-thumb { width: 46px; height: 46px; border-radius: 10px; flex-shrink: 0; overflow: hidden; display: flex; align-items: center; justify-content: center; }
        .search-result-thumb img { width: 100%; height: 100%; object-fit: cover; }
        .thumb-product { background: #eff6ff; color: #2563eb; }
        .thumb-link    { background: #f0fdf4; color: #16a34a; }
        .thumb-text    { background: #fffbeb; color: #d97706; }
        .thumb-other   { background: #f3f4f6; color: #6b7280; }
        .search-result-info { flex: 1; min-width: 0; }
        .search-result-title { font-size: 14px; font-weight: 600; color: #111827; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; margin-bottom: 2px; }
        .search-result-title mark { background: #dbeafe; color: #1d4ed8; border-radius: 2px; padding: 0 1px; font-weight: 700; }
        .search-result-meta  { font-size: 12px; color: #6b7280; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .search-result-price { font-size: 13px; font-weight: 700; color: #2563eb; }
        .search-result-type-badge { font-size: 10px; font-weight: 600; padding: 2px 7px; border-radius: 20px; text-transform: uppercase; letter-spacing: 0.4px; flex-shrink: 0; }
        .badge-product { background: #dbeafe; color: #2563eb; }
        .badge-link    { background: #dcfce7; color: #16a34a; }
        .badge-text    { background: #fef9c3; color: #a16207; }
        .badge-other   { background: #f3f4f6; color: #6b7280; }
        .search-state { display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 32px 20px; gap: 8px; color: #9ca3af; text-align: center; }
        .search-state-icon { margin-bottom: 4px; }
        .search-state p { font-size: 13px; }
        .search-state strong { display: block; font-size: 15px; color: #374151; margin-bottom: 4px; }
        .search-section-label { font-size: 11px; font-weight: 700; color: #9ca3af; letter-spacing: 0.8px; text-transform: uppercase; padding: 10px 16px 4px; }

        /* ═══════════════════════════════════════
           REPORT MODAL — SVG icons, no emoji
           ═══════════════════════════════════════ */
        .report-modal-overlay { position: fixed; inset: 0; background: rgba(15,23,42,0.6); backdrop-filter: blur(3px); display: none; align-items: center; justify-content: center; z-index: 10000; padding: 16px; }
        .report-modal-overlay.show { display: flex; }
        .report-modal { width: 100%; max-width: 440px; background: #fff; border-radius: 20px; box-shadow: 0 32px 64px rgba(0,0,0,0.22); overflow: hidden; max-height: 92vh; display: flex; flex-direction: column; }

        /* Step bar */
        .report-steps { display: flex; background: #f8fafc; border-bottom: 1px solid #e5e7eb; }
        .report-step { flex: 1; padding: 10px 0; font-size: 11px; font-weight: 700; color: #9ca3af; letter-spacing: .4px; text-transform: uppercase; border-bottom: 2px solid transparent; transition: all .2s; display: flex; align-items: center; justify-content: center; gap: 5px; }
        .report-step.active { color: #2563eb; border-bottom-color: #2563eb; }
        .report-step.done   { color: #16a34a; border-bottom-color: #16a34a; }
        .report-step-num { width: 18px; height: 18px; border-radius: 50%; background: #e5e7eb; color: #6b7280; font-size: 10px; font-weight: 800; display: flex; align-items: center; justify-content: center; transition: all .2s; }
        .report-step.active .report-step-num { background: #2563eb; color: #fff; }
        .report-step.done   .report-step-num { background: #16a34a; color: #fff; }

        /* Header */
        .report-head { padding: 16px 20px 12px; border-bottom: 1px solid #f1f5f9; display: flex; align-items: flex-start; justify-content: space-between; flex-shrink: 0; }
        .report-title    { font-size: 16px; font-weight: 800; color: #0f172a; }
        .report-subtitle { font-size: 12px; color: #64748b; margin-top: 2px; }
        .report-close { width: 32px; height: 32px; border-radius: 8px; border: none; background: #f1f5f9; color: #64748b; cursor: pointer; display: flex; align-items: center; justify-content: center; }
        .report-close:hover { background: #fee2e2; color: #dc2626; }

        .report-body { padding: 16px 20px 20px; overflow-y: auto; flex: 1; }
        .report-step-panel { display: none; }
        .report-step-panel.active { display: block; }

        /* Category cards — SVG only, no emoji */
        .category-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 8px; margin-bottom: 14px; }
        .category-card { border: 1.5px solid #e5e7eb; border-radius: 12px; padding: 12px 10px; cursor: pointer; text-align: center; transition: all .18s; background: #fff; display: block; }
        .category-card:hover    { border-color: #93c5fd; background: #f8fbff; }
        .category-card.selected { border-color: #2563eb; background: #eff6ff; box-shadow: 0 0 0 3px rgba(37,99,235,.1); }
        .category-card input[type="radio"] { display: none; }
        .cat-icon { width: 36px; height: 36px; border-radius: 10px; background: #f1f5f9; display: flex; align-items: center; justify-content: center; margin: 0 auto 8px; color: #64748b; transition: all .18s; }
        .category-card:hover    .cat-icon { background: #dbeafe; color: #2563eb; }
        .category-card.selected .cat-icon { background: #2563eb; color: #fff; }
        .category-label { font-size: 12px; font-weight: 700; color: #1e293b; line-height: 1.3; }
        .category-desc  { font-size: 10.5px; color: #64748b; margin-top: 3px; line-height: 1.35; }

        /* Form */
        .form-label { font-size: 12px; font-weight: 700; color: #374151; margin-bottom: 5px; display: block; }
        .form-label .req { color: #dc2626; }
        .form-label .opt { color: #9ca3af; font-weight: 500; }
        .report-textarea { width: 100%; min-height: 90px; border: 1.5px solid #e5e7eb; border-radius: 10px; padding: 10px 12px; font-size: 13px; color: #0f172a; resize: vertical; outline: none; font-family: inherit; line-height: 1.5; background: #f9fafb; }
        .report-textarea:focus { border-color: #2563eb; background: #fff; box-shadow: 0 0 0 3px rgba(37,99,235,.08); }
        .char-counter { font-size: 11px; color: #9ca3af; text-align: right; margin-top: 3px; }

        /* Evidence upload */
        .evidence-upload-area { border: 1.5px dashed #d1d5db; border-radius: 10px; padding: 14px 16px; cursor: pointer; background: #f9fafb; transition: all .2s; margin-top: 8px; display: flex; align-items: center; gap: 12px; }
        .evidence-upload-area:hover { border-color: #93c5fd; background: #f0f6ff; }
        .evidence-upload-ico { width: 36px; height: 36px; border-radius: 8px; background: #e5e7eb; display: flex; align-items: center; justify-content: center; color: #6b7280; flex-shrink: 0; }
        .evidence-upload-area:hover .evidence-upload-ico { background: #dbeafe; color: #2563eb; }
        .evidence-upload-text strong { font-size: 13px; color: #374151; display: block; }
        .evidence-upload-text p { font-size: 11px; color: #9ca3af; margin-top: 2px; }
        .evidence-preview { display: flex; gap: 8px; flex-wrap: wrap; margin-top: 10px; }
        .evidence-thumb { width: 68px; height: 68px; border-radius: 8px; overflow: hidden; position: relative; border: 1.5px solid #e5e7eb; }
        .evidence-thumb img { width: 100%; height: 100%; object-fit: cover; }
        .evidence-thumb-del { position: absolute; top: 3px; right: 3px; width: 18px; height: 18px; border-radius: 50%; background: rgba(15,23,42,0.65); color: #fff; border: none; cursor: pointer; display: flex; align-items: center; justify-content: center; }
        .evidence-thumb-sz { position: absolute; bottom: 0; left: 0; right: 0; background: rgba(0,0,0,0.5); color: #fff; font-size: 9px; font-weight: 600; text-align: center; padding: 2px 0; }

        /* Compress progress */
        .compress-row { display: none; align-items: center; gap: 8px; margin-top: 8px; padding: 8px 12px; background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 8px; font-size: 12px; color: #1e40af; }
        .compress-row.show { display: flex; }
        .compress-spin { width: 14px; height: 14px; border: 2px solid #bfdbfe; border-top-color: #2563eb; border-radius: 50%; animation: spin .6s linear infinite; flex-shrink: 0; }

        /* Alert boxes */
        .alert-box { border-radius: 10px; padding: 10px 12px; margin-top: 12px; display: flex; gap: 10px; align-items: flex-start; font-size: 12px; line-height: 1.5; }
        .alert-box.warning { background: #fffbeb; border: 1.5px solid #fde68a; color: #92400e; }
        .alert-box.info    { background: #eff6ff; border: 1.5px solid #bfdbfe; color: #1e40af; }
        .alert-icon { flex-shrink: 0; margin-top: 1px; }

        /* Confirm summary */
        .confirm-card { background: #f8fafc; border: 1.5px solid #e2e8f0; border-radius: 12px; padding: 14px 16px; margin-bottom: 12px; }
        .confirm-row { display: flex; gap: 10px; margin-bottom: 8px; font-size: 13px; }
        .confirm-row:last-child { margin-bottom: 0; }
        .confirm-key  { color: #6b7280; font-weight: 600; min-width: 88px; flex-shrink: 0; font-size: 12px; }
        .confirm-val  { color: #111827; font-weight: 700; }
        .confirm-desc { color: #374151; font-size: 12px; line-height: 1.5; }

        /* Nav buttons */
        .report-nav { display: flex; gap: 8px; margin-top: 16px; }
        .report-nav-btn { flex: 1; padding: 11px; border-radius: 10px; font-size: 13px; font-weight: 700; border: none; cursor: pointer; transition: all .18s; }
        .btn-secondary { background: #f1f5f9; color: #475569; }
        .btn-secondary:hover { background: #e2e8f0; }
        .btn-primary  { background: #2563eb; color: #fff; }
        .btn-primary:hover    { background: #1d4ed8; }
        .btn-primary:disabled { opacity: .45; cursor: not-allowed; }
        .btn-danger   { background: #dc2626; color: #fff; }
        .btn-danger:hover    { background: #b91c1c; }
        .btn-danger:disabled { opacity: .55; cursor: not-allowed; }

        /* Success */
        .report-success { padding: 36px 20px; text-align: center; display: none; }
        .report-success.show { display: block; }
        .success-circle { width: 64px; height: 64px; border-radius: 50%; background: #dcfce7; margin: 0 auto 16px; display: flex; align-items: center; justify-content: center; color: #16a34a; }
        .report-success h3 { font-size: 17px; font-weight: 800; color: #111827; margin-bottom: 6px; }
        .report-success p  { font-size: 13px; color: #6b7280; line-height: 1.6; }
        .ticket-box { display: inline-block; margin-top: 14px; background: #f1f5f9; border: 1.5px solid #e2e8f0; border-radius: 8px; padding: 8px 18px; font-size: 13px; font-weight: 700; color: #1e293b; font-family: monospace; letter-spacing: 1.5px; }
        .ticket-label { font-size: 11px; color: #94a3b8; margin-top: 6px; }

        /* Fullmenu */
        .fullmenu-overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.85); backdrop-filter: blur(4px); z-index: 500; opacity: 0; visibility: hidden; transition: opacity .25s, visibility .25s; display: flex; flex-direction: column; align-items: center; }
        .fullmenu-overlay.active { opacity: 1; visibility: visible; }
        .fullmenu-body { flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: center; overflow-y: auto; width: 100%; padding: 60px 20px 40px; }
        .fullmenu-close-wrap { width: 100%; max-width: 320px; position: absolute; top: 16px; left: 50%; transform: translateX(-50%); display: flex; justify-content: flex-start; }
        .fullmenu-close { width: 36px; height: 36px; border-radius: 8px; background: rgba(255,255,255,0.1); border: none; color: #fff; font-size: 18px; cursor: pointer; display: flex; align-items: center; justify-content: center; }
        .fullmenu-close:hover { background: #ef4444; }
        .fullmenu-section-label { font-size: 10px; font-weight: 700; color: rgba(255,255,255,0.35); letter-spacing: 1px; text-transform: uppercase; text-align: center; margin-bottom: 8px; margin-top: 24px; width: 100%; max-width: 320px; }
        .fullmenu-section-label:first-child { margin-top: 0; }
        .fullmenu-item { display: flex; align-items: center; justify-content: center; gap: 12px; padding: 14px 32px; color: rgba(255,255,255,0.85); cursor: pointer; border-radius: 12px; font-size: 17px; font-weight: 500; width: 100%; max-width: 320px; transform: translateY(10px); opacity: 0; transition: transform .25s, opacity .25s, background .15s, color .15s; }
        .fullmenu-overlay.active .fullmenu-item { transform: translateY(0); opacity: 1; }
        .fullmenu-overlay.active .fullmenu-item:nth-child(1) { transition-delay: .05s; }
        .fullmenu-overlay.active .fullmenu-item:nth-child(2) { transition-delay: .10s; }
        .fullmenu-overlay.active .fullmenu-item:nth-child(3) { transition-delay: .15s; }
        .fullmenu-overlay.active .fullmenu-item:nth-child(4) { transition-delay: .20s; }
        .fullmenu-item:hover  { background: rgba(255,255,255,0.08); color: #fff; }
        .fullmenu-item.active { color: #fff; background: rgba(59,130,246,0.2); }
        .fullmenu-item svg { width: 18px; height: 18px; flex-shrink: 0; opacity: .7; }
        .fullmenu-item.active svg { opacity: 1; }
        .fullmenu-divider { height: 1px; background: rgba(255,255,255,0.08); width: 100%; max-width: 320px; margin: 12px 0; }

        /* Content */
        .container { padding: 24px 16px; }
        .tab-content { display: none; }
        .tab-content.active { display: block; }
        .user-profile { text-align: center; margin-bottom: 24px; padding-bottom: 20px; border-bottom: 1px solid #e5e7eb; }
        .avatar { width: 80px; height: 80px; border-radius: 50%; margin: 0 auto 10px; display: block; object-fit: cover; border: 3px solid #fff; box-shadow: 0 0 0 2px #e5e7eb; }
        .avatar-placeholder { width: 80px; height: 80px; border-radius: 50%; background: #e5e7eb; margin: 0 auto 10px; display: flex; align-items: center; justify-content: center; color: #9ca3af; border: 3px solid #fff; box-shadow: 0 0 0 2px #e5e7eb; }
        .profile-name     { font-size: 17px; font-weight: 700; color: #111827; margin-bottom: 2px; }
        .profile-username { color: #6b7280; font-size: 13px; margin-bottom: 8px; }
        .profile-bio      { font-size: 13px; color: #374151; line-height: 1.5; }
        .block { margin-bottom: 12px; }
        .block-text { font-size: 14px; text-align: center; color: #374151; line-height: 1.6; }
        .block-link a { display: block; padding: 14px; border-radius: 12px; border: 1px solid #e5e7eb; text-align: center; text-decoration: none; color: #111; font-weight: 500; transition: all .2s; background: #fff; }
        .block-link a:hover { border-color: #2563eb; background: #eff6ff; }
        .block-image img    { width: 100%; border-radius: 12px; }
        .block-video iframe { width: 100%; height: 200px; border-radius: 12px; border: none; }
        .product-skeleton { background: #fff; border: 1px solid #e5e7eb; border-radius: 12px; overflow: hidden; }
        .skeleton-img  { width: 100%; height: 200px; background: linear-gradient(90deg,#f3f4f6 25%,#e5e7eb 50%,#f3f4f6 75%); background-size: 200% 100%; animation: shimmer 1.4s infinite; }
        .skeleton-body { padding: 14px 16px; }
        .skeleton-line { height: 12px; background: linear-gradient(90deg,#f3f4f6 25%,#e5e7eb 50%,#f3f4f6 75%); background-size: 200% 100%; animation: shimmer 1.4s infinite; border-radius: 6px; margin-bottom: 8px; }
        .skeleton-line.w60 { width: 60%; }
        .skeleton-line.w40 { width: 40%; }
        @keyframes shimmer { to { background-position: -200% 0; } }
        .block-product { background: #fff; border: 1px solid #e5e7eb; border-radius: 12px; overflow: hidden; transition: box-shadow .2s, transform .2s, border-color .2s; cursor: pointer; }
        .block-product:hover { box-shadow: 0 4px 16px rgba(37,99,235,0.1); transform: translateY(-2px); border-color: #bfdbfe; }
        .product-image-wrapper { width: 100%; height: 200px; background: #f3f4f6; display: flex; align-items: center; justify-content: center; overflow: hidden; }
        .product-image-wrapper img { width: 100%; height: 100%; object-fit: cover; }
        .product-image-placeholder { width: 56px; height: 56px; background: #eff6ff; border-radius: 10px; display: flex; align-items: center; justify-content: center; color: #2563eb; }
        .product-details { padding: 14px 16px 16px; }
        .product-badge { display: inline-flex; align-items: center; gap: 4px; background: #eff6ff; color: #2563eb; font-size: 11px; font-weight: 600; padding: 3px 8px; border-radius: 6px; margin-bottom: 8px; }
        .product-title { font-size: 15px; font-weight: 600; color: #111827; margin-bottom: 10px; line-height: 1.4; }
        .product-price-section { display: flex; align-items: center; gap: 8px; }
        .product-current-price  { font-size: 18px; font-weight: 700; color: #2563eb; }
        .product-original-price { font-size: 13px; color: #9ca3af; text-decoration: line-through; }
        .product-discount-badge { background: #fee2e2; color: #dc2626; font-size: 11px; font-weight: 600; padding: 2px 6px; border-radius: 4px; }
        .empty-state { text-align: center; padding: 40px 20px; color: #9ca3af; }
        .empty-icon { width: 64px; height: 64px; background: #f3f4f6; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 12px; color: #d1d5db; }

        /* Product detail */
        .product-detail-overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.45); display: none; justify-content: center; align-items: center; z-index: 9999; padding: 20px; }
        .product-detail-box { position: relative; background: white; width: 100%; max-width: 420px; border-radius: 20px; overflow: hidden; box-shadow: 0 20px 50px rgba(0,0,0,0.15); display: flex; flex-direction: column; max-height: 90vh; }
        .product-detail-close { position: absolute; top: 12px; left: 12px; z-index: 10; background: rgba(255,255,255,0.9); backdrop-filter: blur(4px); border: none; border-radius: 50%; width: 35px; height: 35px; font-weight: bold; cursor: pointer; font-size: 14px; }
        .product-detail-image { width: 100%; height: 220px; background: #f3f4f6; flex-shrink: 0; }
        .product-detail-image img { width: 100%; height: 100%; object-fit: cover; }
        .product-detail-content { padding: 20px; display: flex; flex-direction: column; gap: 12px; overflow-y: auto; }
        .product-detail-content h2 { font-size: 20px; font-weight: 700; }
        .detail-price { display: flex; align-items: center; gap: 10px; }
        .final-price  { font-size: 22px; font-weight: 700; color: #2563eb; }
        .original-price { text-decoration: line-through; color: #999; font-size: 14px; }
        .discount-badge-detail { background: #fee2e2; color: #dc2626; font-size: 12px; font-weight: 600; padding: 3px 8px; border-radius: 6px; }
        .stock-info { font-size: 13px; color: #555; }
        .detail-description { font-size: 14px; color: #444; line-height: 1.6; }
        .detail-buttons { display: flex; gap: 10px; padding: 16px; border-top: 1px solid #e5e7eb; background: #fff; flex-shrink: 0; }
        .btn-cart { width: 48px; height: 48px; min-width: 48px; display: flex; align-items: center; justify-content: center; border: 1px solid #2563eb; border-radius: 10px; background: #fff; cursor: pointer; transition: all .2s; }
        .btn-cart:hover   { background: #eff6ff; }
        .btn-cart.loading { opacity: .6; pointer-events: none; }
        .btn-buy { flex: 1; padding: 12px; background: #2563eb; color: white; border-radius: 10px; font-weight: 600; font-size: 15px; border: none; cursor: pointer; transition: background .2s; }
        .btn-buy:hover { background: #1d4ed8; }

        /* Cart */
        .cart-overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.45); z-index: 500; opacity: 0; visibility: hidden; transition: all .3s; }
        .cart-overlay.active { opacity: 1; visibility: visible; }
        .cart-drawer { position: fixed; right: 0; top: 0; bottom: 0; width: 100%; max-width: 420px; background: #fff; z-index: 501; transform: translateX(100%); transition: transform .3s ease; display: flex; flex-direction: column; }
        .cart-drawer.active { transform: translateX(0); }
        .cart-header { display: flex; align-items: center; justify-content: space-between; padding: 16px 20px; border-bottom: 1px solid #e5e7eb; flex-shrink: 0; }
        .cart-header h3 { font-size: 17px; font-weight: 700; color: #111827; }
        .cart-close { width: 32px; height: 32px; border-radius: 50%; background: #f3f4f6; border: none; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 14px; }
        .cart-close:hover { background: #e5e7eb; }
        .cart-items { flex: 1; overflow-y: auto; padding: 16px 20px; }
        .cart-item { display: flex; gap: 12px; padding: 12px 0; border-bottom: 1px solid #f3f4f6; animation: fadeInUp .2s ease; }
        .cart-item-img { width: 64px; height: 64px; border-radius: 10px; background: #f3f4f6; flex-shrink: 0; overflow: hidden; }
        .cart-item-img img { width: 100%; height: 100%; object-fit: cover; }
        .cart-item-img-ph { width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; color: #9ca3af; }
        .cart-item-info { flex: 1; min-width: 0; }
        .cart-item-title { font-size: 13px; font-weight: 600; color: #111827; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; margin-bottom: 4px; }
        .cart-item-price { font-size: 13px; font-weight: 700; color: #2563eb; margin-bottom: 8px; }
        .qty-control { display: inline-flex; align-items: center; border: 1px solid #e5e7eb; border-radius: 8px; overflow: hidden; }
        .qty-btn { width: 28px; height: 28px; border: none; background: #f9fafb; cursor: pointer; font-size: 15px; font-weight: 600; display: flex; align-items: center; justify-content: center; color: #374151; }
        .qty-btn:hover    { background: #e5e7eb; }
        .qty-btn:disabled { opacity: .4; cursor: not-allowed; }
        .qty-value { min-width: 32px; text-align: center; font-size: 13px; font-weight: 600; padding: 0 4px; color: #111827; background: #fff; }
        .cart-item-remove { align-self: flex-start; margin-top: 2px; flex-shrink: 0; width: 26px; height: 26px; border-radius: 6px; background: none; border: none; cursor: pointer; display: flex; align-items: center; justify-content: center; color: #9ca3af; }
        .cart-item-remove:hover { background: #fee2e2; color: #dc2626; }
        .cart-empty { display: flex; flex-direction: column; align-items: center; justify-content: center; height: 200px; gap: 12px; color: #9ca3af; }
        .cart-empty p { font-size: 14px; }
        .cart-footer { padding: 16px 20px; border-top: 1px solid #e5e7eb; flex-shrink: 0; background: #fff; }
        .cart-summary { display: flex; justify-content: space-between; align-items: center; margin-bottom: 14px; }
        .cart-summary-label { font-size: 13px; color: #6b7280; }
        .cart-summary-total { font-size: 18px; font-weight: 700; color: #111827; }
        .btn-checkout { width: 100%; padding: 14px; background: #2563eb; color: white; border: none; border-radius: 12px; font-size: 15px; font-weight: 600; cursor: pointer; }
        .btn-checkout:hover    { background: #1d4ed8; }
        .btn-checkout:disabled { opacity: .5; cursor: not-allowed; }
        .cart-loading { display: flex; align-items: center; justify-content: center; height: 120px; color: #6b7280; font-size: 14px; gap: 8px; }
        .spinner { width: 18px; height: 18px; border: 2px solid #e5e7eb; border-top-color: #2563eb; border-radius: 50%; animation: spin .6s linear infinite; }

        @keyframes spin     { to { transform: rotate(360deg); } }
        @keyframes fadeInUp { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    </style>
</head>
<body>

<div class="toast" id="toast"></div>
<div class="page-wrapper">

{{-- NAVBAR --}}
<div class="navbar">
    <div class="navbar-container">
        <div class="navbar-left">
            <div class="hamburger" id="hamburger">
                <span></span><span></span><span></span>
                <span></span><span></span><span></span>
                <span></span><span></span><span></span>
            </div>
            <div class="navbar-title">{{ $user->name }}</div>
        </div>
        <div class="navbar-right">
            <div class="nav-icon report" id="reportBtn" title="Laporkan akun ini">
                <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v18m0-11h11l-2 3 2 3H5"/>
                </svg>
            </div>
            <div class="nav-icon" id="searchBtn" title="Cari">
                <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11A6 6 0 115 11a6 6 0 0112 0z"/>
                </svg>
            </div>
            <div class="nav-icon" id="cartBtn" title="Keranjang">
                <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                <span class="cart-badge" id="cartBadge">0</span>
            </div>
        </div>
    </div>
</div>

{{-- SEARCH BAR --}}
<div class="search-bar-wrap" id="searchBarWrap">
    <div class="search-bar-inner">
        <button class="search-back-btn" id="searchBackBtn">
            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </button>
        <div class="search-input-wrap">
            <input type="text" class="search-input" id="searchInput" placeholder="Cari produk, link, konten..." autocomplete="off">
            <button class="search-clear-btn" id="searchClearBtn">
                <svg viewBox="0 0 10 10" fill="currentColor"><path d="M6.41 5l2.3-2.29a1 1 0 00-1.42-1.42L5 3.59 2.71 1.29A1 1 0 001.29 2.71L3.59 5 1.29 7.29a1 1 0 001.42 1.42L5 6.41l2.29 2.3a1 1 0 001.42-1.42z"/></svg>
            </button>
        </div>
    </div>
</div>
<div class="search-results-overlay" id="searchResultsOverlay"></div>
<div class="search-results-panel"   id="searchResultsPanel"></div>

{{-- ══════════════════════════════════════════
     REPORT MODAL — profesional, ikon SVG
     ══════════════════════════════════════════ --}}
<div class="report-modal-overlay" id="reportModalOverlay">
    <div class="report-modal" role="dialog" aria-modal="true" aria-labelledby="reportModalTitle">

        {{-- Step bar --}}
        <div class="report-steps" id="reportStepBar">
            <div class="report-step active" id="rStep1"><div class="report-step-num">1</div>Kategori</div>
            <div class="report-step"        id="rStep2"><div class="report-step-num">2</div>Keterangan</div>
            <div class="report-step"        id="rStep3"><div class="report-step-num">3</div>Konfirmasi</div>
        </div>

        {{-- Header --}}
        <div class="report-head" id="reportHead">
            <div>
                <div class="report-title" id="reportModalTitle">Laporkan Akun</div>
                <div class="report-subtitle" id="reportStepLabel">Pilih kategori yang paling sesuai</div>
            </div>
            <button class="report-close" id="reportCloseBtn" aria-label="Tutup">
                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <div class="report-body">

            {{-- ── STEP 1: Pilih Kategori ── --}}
            <div class="report-step-panel active" id="rPanel1">
                <div class="category-grid">

                    <label class="category-card" data-value="spam">
                        <input type="radio" name="report_reason" value="spam">
                        <div class="cat-icon">
                            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                        </div>
                        <div class="category-label">Spam</div>
                        <div class="category-desc">Iklan berlebihan atau konten berulang</div>
                    </label>

                    <label class="category-card" data-value="scam">
                        <input type="radio" name="report_reason" value="scam">
                        <div class="cat-icon">
                            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
                        </div>
                        <div class="category-label">Penipuan</div>
                        <div class="category-desc">Produk palsu, tidak dikirim, atau scam</div>
                    </label>

                    <label class="category-card" data-value="hate_speech">
                        <input type="radio" name="report_reason" value="hate_speech">
                        <div class="cat-icon">
                            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"/></svg>
                        </div>
                        <div class="category-label">Ujaran Kebencian</div>
                        <div class="category-desc">SARA, ancaman, atau pelecehan</div>
                    </label>

                    <label class="category-card" data-value="adult_content">
                        <input type="radio" name="report_reason" value="adult_content">
                        <div class="cat-icon">
                            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                        </div>
                        <div class="category-label">Konten Tidak Sesuai</div>
                        <div class="category-desc">Konten dewasa tanpa label</div>
                    </label>

                    <label class="category-card" data-value="violence">
                        <input type="radio" name="report_reason" value="violence">
                        <div class="cat-icon">
                            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.618 5.984A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016zM12 9v2m0 4h.01"/></svg>
                        </div>
                        <div class="category-label">Kekerasan</div>
                        <div class="category-desc">Ancaman fisik atau konten brutal</div>
                    </label>

                    <label class="category-card" data-value="fake_account">
                        <input type="radio" name="report_reason" value="fake_account">
                        <div class="cat-icon">
                            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        </div>
                        <div class="category-label">Akun Palsu</div>
                        <div class="category-desc">Menyamar sebagai orang lain</div>
                    </label>

                    <label class="category-card" data-value="copyright">
                        <input type="radio" name="report_reason" value="copyright">
                        <div class="cat-icon">
                            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        </div>
                        <div class="category-label">Hak Cipta</div>
                        <div class="category-desc">Konten tanpa izin pemilik asli</div>
                    </label>

                    <label class="category-card" data-value="other">
                        <input type="radio" name="report_reason" value="other">
                        <div class="cat-icon">
                            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div class="category-label">Lainnya</div>
                        <div class="category-desc">Tidak masuk kategori di atas</div>
                    </label>

                </div>
                <div class="report-nav">
                    <button class="report-nav-btn btn-secondary" id="rCancelBtn">Batal</button>
                    <button class="report-nav-btn btn-primary"   id="rNext1" disabled>Lanjut</button>
                </div>
            </div>

            {{-- ── STEP 2: Keterangan & Bukti ── --}}
            <div class="report-step-panel" id="rPanel2">
                <label class="form-label" for="reportDetail">
                    Deskripsi masalah <span class="req">*</span>
                </label>
                <textarea class="report-textarea" id="reportDetail" maxlength="1000"
                    placeholder="Jelaskan secara ringkas apa yang terjadi. Semakin detail laporan, semakin cepat dapat ditinjau."></textarea>
                <div class="char-counter"><span id="charCount">0</span> / 1000</div>

                <label class="form-label" style="margin-top:14px;">
                    Bukti pendukung <span class="opt">(opsional)</span>
                </label>
                <div class="evidence-upload-area" onclick="document.getElementById('evidenceFileInput').click()">
                    <div class="evidence-upload-ico">
                        <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div class="evidence-upload-text">
                        <strong>Lampirkan tangkapan layar</strong>
                        <p>Format apapun diterima &mdash; maks. 3 gambar, otomatis dikompres</p>
                    </div>
                </div>
                <input type="file" id="evidenceFileInput" accept="image/*" multiple style="display:none"
                       onchange="handleEvidenceFiles(event)">

                <div class="compress-row" id="compressRow">
                    <div class="compress-spin"></div>
                    <span id="compressLabel">Mengompresi gambar...</span>
                </div>
                <div class="evidence-preview" id="evidencePreview"></div>

                <div class="alert-box warning">
                    <div class="alert-icon">
                        <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
                    </div>
                    <div>Penyalahgunaan fitur laporan dapat mengakibatkan <strong>akun Anda sendiri ditinjau oleh tim moderasi</strong>.</div>
                </div>

                <div class="report-nav">
                    <button class="report-nav-btn btn-secondary" id="rBack2">Kembali</button>
                    <button class="report-nav-btn btn-primary"   id="rNext2" disabled>Lanjut</button>
                </div>
            </div>

            {{-- ── STEP 3: Konfirmasi ── --}}
            <div class="report-step-panel" id="rPanel3">
                <div class="confirm-card">
                    <div class="confirm-row">
                        <span class="confirm-key">Akun dilaporkan</span>
                        <span class="confirm-val">{{ $user->name }} <span style="color:#6b7280;font-weight:400;">(@{{ $user->username }})</span></span>
                    </div>
                    <div class="confirm-row">
                        <span class="confirm-key">Kategori</span>
                        <span class="confirm-val" id="confirmReason">–</span>
                    </div>
                    <div class="confirm-row" id="confirmDetailRow" style="display:none">
                        <span class="confirm-key">Deskripsi</span>
                        <span class="confirm-desc" id="confirmDetail">–</span>
                    </div>
                    <div class="confirm-row" id="confirmEvidRow" style="display:none">
                        <span class="confirm-key">Bukti</span>
                        <span class="confirm-val" id="confirmEvidCount">–</span>
                    </div>
                </div>

                <div class="alert-box info">
                    <div class="alert-icon">
                        <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    </div>
                    <div>Laporan ini diproses secara <strong>anonim</strong>. Identitas Anda tidak akan diungkapkan kepada pihak yang dilaporkan. Tim kami akan meninjau dalam <strong>1&ndash;3 hari kerja</strong>.</div>
                </div>

                <div class="report-nav">
                    <button class="report-nav-btn btn-secondary" id="rBack3">Kembali</button>
                    <button class="report-nav-btn btn-danger"    id="rSubmitBtn">Kirim Laporan</button>
                </div>
            </div>

            {{-- ── SUCCESS ── --}}
            <div class="report-success" id="reportSuccess">
                <div class="success-circle">
                    <svg width="28" height="28" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <h3>Laporan Berhasil Dikirim</h3>
                <p>Terima kasih. Tim moderasi akan meninjau laporan ini dalam <strong>1&ndash;3 hari kerja</strong>. Simpan nomor tiket berikut sebagai referensi.</p>
                <div class="ticket-box" id="ticketCode">–</div>
                <div class="ticket-label">Nomor Tiket Laporan</div>
                <br>
                <button class="report-nav-btn btn-primary" style="max-width:180px;margin:0 auto;display:block;"
                        onclick="closeReportModal()">Tutup</button>
            </div>

        </div>
    </div>
</div>

{{-- FULLSCREEN MENU --}}
<div class="fullmenu-overlay" id="fullmenuOverlay">
    <div class="fullmenu-close-wrap">
        <button class="fullmenu-close" id="fullmenuClose">✕</button>
    </div>
    <div class="fullmenu-body">
        @if($user->pages && $user->pages->count() > 0)
            <div class="fullmenu-section-label">Halaman</div>
            @foreach($user->pages as $userPage)
                <div class="fullmenu-item {{ $loop->first ? 'active' : '' }}" data-tab="page-{{ $userPage->id }}">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    {{ $userPage->title }}
                </div>
            @endforeach
        @endif
        <div class="fullmenu-divider"></div>
        <div class="fullmenu-section-label">Akun</div>
        <div class="fullmenu-item" onclick="window.location.href='/login'">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            Masuk / Daftar
        </div>
    </div>
</div>

{{-- MAIN CONTENT --}}
<div class="container">
    @if($user->pages && $user->pages->count() > 0)
        @foreach($user->pages as $userPage)
            <div class="tab-content {{ $loop->first ? 'active' : '' }}" id="tab-page-{{ $userPage->id }}">
                <div class="user-profile">
                    @if($user->avatar)
                        <img src="{{ asset('storage/' . $user->avatar) }}" class="avatar" alt="{{ $user->name }}">
                    @else
                        <div class="avatar-placeholder">
                            <svg width="32" height="32" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        </div>
                    @endif
                    <div class="profile-name">{{ $user->name }}</div>
                    <div class="profile-username">{{ '@' . $user->username }}</div>
                    @if($user->bio)
                        <div class="profile-bio">{{ $user->bio }}</div>
                    @endif
                </div>

                @if($userPage->blocks && $userPage->blocks->count() > 0)
                    @foreach($userPage->blocks->sortBy('position') as $block)
                        @if($block->type === 'text')
                            <div class="block block-text" id="block-{{ $block->id }}">{{ $block->content['text'] ?? '' }}</div>
                        @elseif($block->type === 'link')
                            <div class="block block-link" id="block-{{ $block->id }}">
                                <a href="{{ $block->content['url'] ?? '#' }}" target="_blank">{{ $block->content['title'] ?? 'Link' }}</a>
                            </div>
                        @elseif($block->type === 'image')
                            <div class="block block-image" id="block-{{ $block->id }}">
                                <img src="{{ asset('storage/' . $block->content['image']) }}">
                            </div>
                        @elseif($block->type === 'video')
                            @php
                                $videoId = $block->content['youtube_id'] ?? '';
                                if (!$videoId) {
                                    $url = $block->content['youtube_url'] ?? '';
                                    parse_str(parse_url($url, PHP_URL_QUERY), $query);
                                    $videoId = $query['v'] ?? '';
                                    if (!$videoId && str_contains($url, 'youtu.be/'))
                                        $videoId = basename(parse_url($url, PHP_URL_PATH));
                                }
                            @endphp
                            <div class="block block-video" id="block-{{ $block->id }}">
                                <iframe src="https://www.youtube.com/embed/{{ $videoId }}" allowfullscreen></iframe>
                            </div>
                        @elseif($block->type === 'product' && isset($block->product_id) && $block->product_id)
                            <div class="block" id="product-block-{{ $block->id }}" data-product-id="{{ $block->product_id }}">
                                <div class="product-skeleton">
                                    <div class="skeleton-img"></div>
                                    <div class="skeleton-body">
                                        <div class="skeleton-line"></div>
                                        <div class="skeleton-line w60"></div>
                                        <div class="skeleton-line w40"></div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                @else
                    <div class="empty-state">
                        <div class="empty-icon"><svg width="28" height="28" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg></div>
                        Halaman ini belum memiliki konten.
                    </div>
                @endif
            </div>
        @endforeach
    @else
        <div class="empty-state">
            <div class="empty-icon"><svg width="28" height="28" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg></div>
            Belum ada halaman yang tersedia.
        </div>
    @endif
</div>

{{-- PRODUCT DETAIL MODAL --}}
<div class="product-detail-overlay" id="productDetailModal">
    <div class="product-detail-box">
        <button class="product-detail-close" onclick="closeProductDetail()">✕</button>
        <div class="product-detail-image"><img id="detailImage" src="" alt="" style="display:none;"></div>
        <div class="product-detail-content">
            <h2 id="detailTitle"></h2>
            <div class="detail-price">
                <span class="final-price"          id="detailFinalPrice"></span>
                <span class="original-price"       id="detailOriginalPrice"></span>
                <span class="discount-badge-detail" id="detailDiscountBadge" style="display:none;"></span>
            </div>
            <div class="stock-info" id="detailStockWrap">Stok: <span id="detailStock"></span></div>
            <div class="detail-description">
                <strong style="font-size:13px;color:#6b7280;letter-spacing:.5px;">DESKRIPSI</strong>
                <p id="detailDescription" style="margin-top:6px;"></p>
            </div>
        </div>
        <div class="detail-buttons">
            <button class="btn-cart" id="btnAddToCart">
                <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            </button>
            <button class="btn-buy" id="buyNowBtn">Beli Sekarang</button>
        </div>
    </div>
</div>

{{-- CART DRAWER --}}
<div class="cart-overlay" id="cartOverlay"></div>
<div class="cart-drawer"  id="cartDrawer">
    <div class="cart-header">
        <h3>Keranjang Belanja</h3>
        <button class="cart-close" onclick="closeCart()">✕</button>
    </div>
    <div class="cart-items" id="cartItems">
        <div class="cart-loading"><div class="spinner"></div> Memuat...</div>
    </div>
    <div class="cart-footer" id="cartFooter" style="display:none">
        <div class="cart-summary">
            <span class="cart-summary-label">Total Pembayaran</span>
            <span class="cart-summary-total" id="cartTotal">Rp 0</span>
        </div>
        <button class="btn-checkout" onclick="handleCheckout()">Lanjut ke Pembayaran</button>
    </div>
</div>

</div>{{-- /.page-wrapper --}}

<script>
// ═══════════════════════════════════════════════════
// CONSTANTS & UTILITIES
// ═══════════════════════════════════════════════════
const CSRF            = document.querySelector('meta[name="csrf-token"]').content;
const USERNAME        = '{{ $user->username }}';
const REPORT_ENDPOINT = '{{ route('public.profile.report', $user->username) }}';

const fmt = n => 'Rp ' + new Intl.NumberFormat('id-ID').format(Math.round(n));
const fmtBytes = b => b < 1024 ? b+'B' : b < 1048576 ? (b/1024).toFixed(0)+'KB' : (b/1048576).toFixed(1)+'MB';
const esc = str => { if(!str) return ''; const d=document.createElement('div'); d.textContent=str; return d.innerHTML; };

let _toastT;
function showToast(msg, type='default') {
    const t = document.getElementById('toast');
    t.textContent = msg; t.className = `toast ${type} show`;
    clearTimeout(_toastT); _toastT = setTimeout(()=>t.classList.remove('show'), 2800);
}
async function api(url, method='GET', body=null) {
    const o = { method, headers: {'Content-Type':'application/json','X-CSRF-TOKEN':CSRF,'Accept':'application/json'} };
    if(body) o.body = JSON.stringify(body);
    const r = await fetch(url, o), d = await r.json();
    if(!r.ok) throw new Error(d.message||'Terjadi kesalahan.');
    return d;
}

// ═══════════════════════════════════════════════════
// IMAGE COMPRESSION
// Accepts any format/size → output max 900px JPEG ≤ ~150KB
// ═══════════════════════════════════════════════════
function compressImage(file, maxW=900, quality=0.72) {
    return new Promise(resolve => {
        const reader = new FileReader();
        reader.onload = e => {
            const img = new Image();
            img.onload = () => {
                let w=img.width, h=img.height;
                if(w > maxW) { h = Math.round(h*maxW/w); w = maxW; }
                const c = document.createElement('canvas');
                c.width=w; c.height=h;
                c.getContext('2d').drawImage(img,0,0,w,h);
                c.toBlob(blob => {
                    const f = new File([blob], file.name.replace(/\.[^.]+$/,'')+'.jpg', {type:'image/jpeg'});
                    // If still too big, compress harder
                    if(f.size > 200*1024) {
                        c.toBlob(blob2=>{
                            resolve({ file: new File([blob2], f.name, {type:'image/jpeg'}), preview: c.toDataURL('image/jpeg',0.55) });
                        }, 'image/jpeg', 0.55);
                    } else {
                        resolve({ file: f, preview: c.toDataURL('image/jpeg', quality) });
                    }
                }, 'image/jpeg', quality);
            };
            img.src = e.target.result;
        };
        reader.readAsDataURL(file);
    });
}

// ═══════════════════════════════════════════════════
// HAMBURGER MENU
// ═══════════════════════════════════════════════════
let _sy = 0;
const hamburger = document.getElementById('hamburger');
const fullmenu  = document.getElementById('fullmenuOverlay');
function openMenu()  { _sy=window.scrollY; document.body.style.cssText=`position:fixed;top:-${_sy}px;width:100%;`; fullmenu.classList.add('active'); }
function closeMenu() { fullmenu.classList.remove('active'); document.body.style.cssText=''; window.scrollTo(0,_sy); }
hamburger.addEventListener('click', openMenu);
document.getElementById('fullmenuClose').addEventListener('click', closeMenu);
document.querySelectorAll('.fullmenu-item[data-tab]').forEach(el => el.addEventListener('click', ()=>{
    const tab = el.dataset.tab;
    document.querySelectorAll('.fullmenu-item').forEach(n=>n.classList.remove('active'));
    el.classList.add('active');
    document.querySelectorAll('.tab-content').forEach(c=>c.classList.remove('active'));
    document.getElementById(`tab-${tab}`)?.classList.add('active');
    closeMenu();
}));

// ═══════════════════════════════════════════════════
// SEARCH
// ═══════════════════════════════════════════════════
const searchBarWrap        = document.getElementById('searchBarWrap');
const searchInput          = document.getElementById('searchInput');
const searchClearBtn       = document.getElementById('searchClearBtn');
const searchResultsOverlay = document.getElementById('searchResultsOverlay');
const searchResultsPanel   = document.getElementById('searchResultsPanel');
let _sd = null;

const openSearch  = () => { searchBarWrap.classList.add('open'); setTimeout(()=>searchInput.focus(),350); };
const closeSearch = () => { searchBarWrap.classList.remove('open'); hideR(); searchInput.value=''; searchClearBtn.classList.remove('visible'); };
const showR = () => { searchResultsOverlay.classList.add('active'); searchResultsPanel.classList.add('active'); };
const hideR = () => { searchResultsOverlay.classList.remove('active'); searchResultsPanel.classList.remove('active'); };

document.getElementById('searchBtn').addEventListener('click', openSearch);
document.getElementById('searchBackBtn').addEventListener('click', closeSearch);
searchResultsOverlay.addEventListener('click', closeSearch);
searchClearBtn.addEventListener('click', ()=>{ searchInput.value=''; searchClearBtn.classList.remove('visible'); hideR(); searchInput.focus(); });
document.addEventListener('keydown', e=>{ if(e.key==='Escape') closeSearch(); });

const SI = {
    product:`<svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>`,
    link:`<svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>`,
    text:`<svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>`,
    other:`<svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 10h16M4 14h8m-8 4h4"/></svg>`
};
const TC = { product:'thumb-product', link:'thumb-link', text:'thumb-text', other:'thumb-other' };
const hl = (txt,q) => { if(!q||!txt) return esc(txt); return esc(txt).replace(new RegExp(q.replace(/[.*+?^${}()|[\]\\]/g,'\\$&'),'gi'),m=>`<mark>${m}</mark>`); };

function renderSearchState(type, title, sub) {
    const ic = {
        loading:`<svg width="28" height="28" fill="none" stroke="#9ca3af" stroke-width="1.8" viewBox="0 0 24 24" style="animation:spin .8s linear infinite"><path stroke-linecap="round" stroke-linejoin="round" d="M12 2v4m0 12v4M4.93 4.93l2.83 2.83m8.48 8.48l2.83 2.83M2 12h4m12 0h4M4.93 19.07l2.83-2.83m8.48-8.48l2.83-2.83"/></svg>`,
        noresult:`<svg width="28" height="28" fill="none" stroke="#9ca3af" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11A6 6 0 115 11a6 6 0 0112 0z"/></svg>`,
        error:`<svg width="28" height="28" fill="none" stroke="#ef4444" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>`
    };
    searchResultsPanel.innerHTML=`<div class="search-state"><div class="search-state-icon">${ic[type]||ic.noresult}</div><strong>${esc(title)}</strong>${sub?`<p>${esc(sub)}</p>`:''}</div>`;
}

function renderResults(results, q) {
    if(!results?.length) { renderSearchState('noresult','Tidak ditemukan',`Tidak ada hasil untuk "${q}"`); return; }
    const lm={product:'Produk',link:'Link',text:'Konten',other:'Lainnya'};
    const gr={product:[],link:[],text:[],other:[]};
    results.forEach(r=>{ const k=r.type&&gr[r.type]!==undefined?r.type:'other'; gr[k].push(r); });
    let html='';
    for(const [type,items] of Object.entries(gr)) {
        if(!items.length) continue;
        html+=`<div class="search-section-label">${lm[type]}</div>`;
        items.forEach(it=>{
            const ti=SI[type]||SI.other, thumb=it.image_url?`<img src="${esc(it.image_url)}" alt="">`:ti;
            const sub=type==='product'?`<div class="search-result-price">${fmt(it.final_price??it.price??0)}</div>`:(it.subtitle?`<div class="search-result-meta">${esc(String(it.subtitle))}</div>`:'');
            const j=JSON.stringify(it).replace(/</g,'\\u003c').replace(/>/g,'\\u003e').replace(/&/g,'\\u0026');
            html+=`<div class="search-result-item" onclick='srClick(${j})'><div class="search-result-thumb ${it.image_url?'':(TC[type]||TC.other)}">${thumb}</div><div class="search-result-info"><div class="search-result-title">${hl(String(it.title||it.url||'–'),q)}</div>${sub}</div><span class="search-result-type-badge badge-${type}">${lm[type]}</span></div>`;
        });
    }
    searchResultsPanel.innerHTML=html;
}

function srClick(it) {
    if(it.type==='product'){ closeSearch(); handleProductClick(it.id); }
    else if(it.type==='link'){ window.open(it.url,'_blank'); }
    else {
        closeSearch();
        const el=document.getElementById(`block-${it.block_id??it.id}`);
        if(el){ const tc=el.closest('.tab-content'); if(tc&&!tc.classList.contains('active')){ document.querySelectorAll('.tab-content').forEach(c=>c.classList.remove('active')); tc.classList.add('active'); const tid=tc.id.replace('tab-',''); document.querySelectorAll('.fullmenu-item[data-tab]').forEach(n=>n.classList.toggle('active',n.dataset.tab===tid)); } setTimeout(()=>el.scrollIntoView({behavior:'smooth',block:'center'}),100); }
    }
}

searchInput.addEventListener('input',()=>{
    const q=searchInput.value.trim();
    searchClearBtn.classList.toggle('visible',q.length>0);
    clearTimeout(_sd); if(!q){ hideR(); return; }
    showR(); renderSearchState('loading','Mencari...','');
    _sd=setTimeout(async()=>{
        try {
            const r=await fetch(`/search?username=${encodeURIComponent(USERNAME)}&q=${encodeURIComponent(q)}`,{headers:{'Accept':'application/json','X-CSRF-TOKEN':CSRF}});
            if(!(r.headers.get('content-type')||'').includes('json')){ renderSearchState('error','Route tidak ditemukan',`Status ${r.status}`); return; }
            const j=await r.json(); let res=Array.isArray(j)?j:(j.results??j.data??j.items??[]);
            if(!res.length&&typeof j==='object') for(const v of Object.values(j)) if(Array.isArray(v)&&v.length){res=v;break;}
            renderResults(res,q);
        } catch(e){ renderSearchState('error','Gagal mencari',e.message); }
    },350);
});

// ═══════════════════════════════════════════════════
// PRODUCTS
// ═══════════════════════════════════════════════════
const PPH=`<svg width="28" height="28" fill="none" stroke="#2563eb" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>`;
const CPH=`<svg width="24" height="24" fill="none" stroke="#9ca3af" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>`;
const CEM=`<svg width="40" height="40" fill="none" stroke="#d1d5db" stroke-width="1.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>`;

function renderProductBlock(c,p){
    const px=parseFloat(p.price)||0,dx=parseFloat(p.discount)||0,fp=parseFloat(p.final_price)||((dx>0&&dx<px)?dx:px),hd=fp<px,pct=hd?Math.round(((px-fp)/px)*100):0;
    const img=p.image_url?`<img src="${p.image_url}" alt="${esc(p.title)}">`:`<div class="product-image-placeholder">${PPH}</div>`;
    c.innerHTML=`<div class="block-product" onclick="handleProductClick(${p.id})"><div class="product-image-wrapper">${img}</div><div class="product-details">${hd?`<span class="product-badge">Diskon ${pct}%</span>`:''}<div class="product-title">${esc(p.title)}</div><div class="product-price-section"><div class="product-current-price">${fmt(fp)}</div>${hd?`<div class="product-original-price">${fmt(px)}</div><div class="product-discount-badge">-${pct}%</div>`:''}</div></div></div>`;
}

let _curPid=null;
function handleProductClick(id){
    if(!id) return;
    fetch(`/api/product/${id}/view`,{method:'POST',headers:{'X-CSRF-TOKEN':CSRF,'Content-Type':'application/json'}}).catch(()=>{});
    fetch(`/api/product/${id}/data`).then(r=>r.json()).then(p=>{
        _curPid=p.id;
        const px=parseFloat(p.price)||0,dx=parseFloat(p.discount)||0,fp=parseFloat(p.final_price)||((dx>0&&dx<px)?dx:px),hd=fp<px,pct=hd?Math.round(((px-fp)/px)*100):0;
        document.getElementById('detailTitle').textContent=p.title;
        document.getElementById('detailFinalPrice').textContent=fmt(fp);
        document.getElementById('detailOriginalPrice').textContent=hd?fmt(px):'';
        const db=document.getElementById('detailDiscountBadge'); db.textContent=hd?`-${pct}%`:''; db.style.display=hd?'inline-block':'none';
        const sw=document.getElementById('detailStockWrap'); if(p.product_type==='digital'||p.stock===null){sw.style.display='none';}else{sw.style.display='';document.getElementById('detailStock').textContent=p.stock;}
        document.getElementById('detailDescription').textContent=p.description??'';
        const img=document.getElementById('detailImage'); if(p.image_url){img.src=p.image_url;img.style.display='block';}else img.style.display='none';
        document.getElementById('buyNowBtn').onclick=()=>{window.location.href=`/checkout/${p.id}`;};
        document.getElementById('productDetailModal').style.display='flex';
        document.body.style.overflow='hidden';
    }).catch(()=>showToast('Gagal memuat produk.','error'));
}
function closeProductDetail(){ document.getElementById('productDetailModal').style.display='none'; document.body.style.overflow=''; _curPid=null; }
document.getElementById('btnAddToCart').addEventListener('click',async()=>{
    if(!_curPid) return; const btn=document.getElementById('btnAddToCart'); btn.classList.add('loading');
    try{ const d=await api('/api/cart/add','POST',{product_id:_curPid,quantity:1}); updateBadge(d.total_items); showToast('Produk ditambahkan ke keranjang!','success'); }
    catch(e){ showToast(e.message,'error'); } finally{ btn.classList.remove('loading'); }
});

// ═══════════════════════════════════════════════════
// CART
// ═══════════════════════════════════════════════════
const cartOverlay=document.getElementById('cartOverlay'), cartDrawer=document.getElementById('cartDrawer');
function updateBadge(n){ const b=document.getElementById('cartBadge'); b.textContent=n; b.classList.toggle('visible',n>0); }
function openCart(){ cartOverlay.classList.add('active'); cartDrawer.classList.add('active'); document.body.style.overflow='hidden'; loadCart(); }
function closeCart(){ cartOverlay.classList.remove('active'); cartDrawer.classList.remove('active'); document.body.style.overflow=''; }
cartOverlay.addEventListener('click',closeCart);
document.getElementById('cartBtn').addEventListener('click',openCart);

async function loadCart(){
    const c=document.getElementById('cartItems'),f=document.getElementById('cartFooter');
    c.innerHTML=`<div class="cart-loading"><div class="spinner"></div> Memuat...</div>`; f.style.display='none';
    try{ renderCart(await api('/api/cart')); }catch{ c.innerHTML=`<div class="cart-empty">${CEM}<p>Gagal memuat keranjang.</p></div>`; }
}
function renderCart(data){
    const c=document.getElementById('cartItems'),f=document.getElementById('cartFooter'),t=document.getElementById('cartTotal');
    if(!data.items?.length){ c.innerHTML=`<div class="cart-empty">${CEM}<p>Keranjangmu masih kosong.</p></div>`; f.style.display='none'; return; }
    c.innerHTML=data.items.map(it=>`<div class="cart-item" id="cart-item-${it.id}"><div class="cart-item-img">${it.image_url?`<img src="${it.image_url}" alt="${esc(it.title)}">`:`<div class="cart-item-img-ph">${CPH}</div>`}</div><div class="cart-item-info"><div class="cart-item-title">${esc(it.title)}</div><div class="cart-item-price">${fmt(it.final_price)}${it.has_discount?`<span style="font-size:11px;color:#9ca3af;text-decoration:line-through;margin-left:4px;">${fmt(it.original_price)}</span>`:''}</div><div class="qty-control"><button class="qty-btn" onclick="changeQty(${it.id},${it.quantity-1},${it.stock})" ${it.quantity<=1?'disabled':''}>−</button><span class="qty-value">${it.quantity}</span><button class="qty-btn" onclick="changeQty(${it.id},${it.quantity+1},${it.stock})" ${it.quantity>=it.stock?'disabled':''}>+</button></div></div><button class="cart-item-remove" onclick="removeItem(${it.id})"><svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button></div>`).join('');
    t.textContent=fmt(data.total_price); f.style.display='block';
}
async function changeQty(id,q,s){ if(q<1||q>s) return; try{ const d=await api(`/api/cart/${id}`,'PATCH',{quantity:q}); updateBadge(d.total_items); renderCart(await api('/api/cart')); }catch(e){ showToast(e.message,'error'); } }
async function removeItem(id){ const el=document.getElementById(`cart-item-${id}`); if(el){el.style.opacity='.5';el.style.transition='opacity .2s';} try{ const d=await api(`/api/cart/${id}`,'DELETE'); updateBadge(d.total_items); showToast('Produk dihapus dari keranjang.'); renderCart(await api('/api/cart')); }catch(e){ showToast(e.message,'error'); if(el) el.style.opacity='1'; } }
async function handleCheckout(){ try{ const d=await api('/api/cart'); if(!d.items?.length){showToast('Keranjang kosong.','error');return;} if(d.items.length===1){window.location.href=`/checkout/${d.items[0].product_id}`;return;} if(confirm(`Keranjang berisi ${d.items.length} produk.\nLanjut ke checkout produk pertama?`)) window.location.href=`/checkout/${d.items[0].product_id}`; }catch{ showToast('Gagal memproses checkout.','error'); } }

// ═══════════════════════════════════════════════════
// REPORT MODAL
// ═══════════════════════════════════════════════════
const REASON_LABELS = {
    spam:'Spam / Iklan Berlebihan', scam:'Penipuan / Scam',
    hate_speech:'Ujaran Kebencian', adult_content:'Konten Tidak Sesuai',
    violence:'Kekerasan / Ancaman', fake_account:'Akun Palsu',
    copyright:'Pelanggaran Hak Cipta', other:'Lainnya'
};
const STEP_SUBTITLES = ['','Pilih kategori yang paling sesuai','Tambahkan keterangan dan bukti pendukung','Periksa kembali sebelum mengirim'];

const rs = { step:1, reason:null, detail:'', files:[] }; // report state

const rOverlay  = document.getElementById('reportModalOverlay');
const rCloseBtn = document.getElementById('reportCloseBtn');
const rCancel   = document.getElementById('rCancelBtn');
const rNext1    = document.getElementById('rNext1');
const rNext2    = document.getElementById('rNext2');
const rBack2    = document.getElementById('rBack2');
const rBack3    = document.getElementById('rBack3');
const rSubmit   = document.getElementById('rSubmitBtn');
const rDetail   = document.getElementById('reportDetail');
const rCount    = document.getElementById('charCount');

function openReportModal()  { resetReport(); rOverlay.classList.add('show'); document.body.style.overflow='hidden'; }
function closeReportModal() { rOverlay.classList.remove('show'); document.body.style.overflow=''; }

function resetReport() {
    rs.step=1; rs.reason=null; rs.detail=''; rs.files=[];
    document.querySelectorAll('.category-card').forEach(c=>c.classList.remove('selected'));
    rDetail.value=''; rCount.textContent='0';
    document.getElementById('evidencePreview').innerHTML='';
    document.getElementById('compressRow').classList.remove('show');
    rNext1.disabled=true; rNext2.disabled=true;
    const s=document.getElementById('reportSuccess'); s.classList.remove('show'); s.style.display='none';
    document.getElementById('reportStepBar').style.display='';
    document.getElementById('reportHead').style.display='';
    document.querySelectorAll('.report-step-panel').forEach(p=>{ p.classList.remove('active'); p.style.display=''; });
    goToStep(1);
}

function goToStep(n) {
    rs.step=n;
    document.querySelectorAll('.report-step-panel').forEach((p,i)=>p.classList.toggle('active',i+1===n));
    ['rStep1','rStep2','rStep3'].forEach((id,i)=>{
        const el=document.getElementById(id); el.classList.remove('active','done');
        if(i+1===n) el.classList.add('active'); else if(i+1<n) el.classList.add('done');
    });
    document.getElementById('reportStepLabel').textContent=STEP_SUBTITLES[n];
    if(n===3) fillConfirm();
}

// Category click
document.querySelectorAll('.category-card').forEach(card=>{
    card.addEventListener('click',()=>{
        document.querySelectorAll('.category-card').forEach(c=>c.classList.remove('selected'));
        card.classList.add('selected');
        card.querySelector('input').checked=true;
        rs.reason=card.dataset.value;
        rNext1.disabled=false;
    });
});

// Detail textarea
rDetail.addEventListener('input',()=>{
    const l=rDetail.value.length; rCount.textContent=l; rs.detail=rDetail.value;
    rNext2.disabled=l<10;
});

// Evidence: compress then preview
async function handleEvidenceFiles(e) {
    const remaining=3-rs.files.length;
    const files=Array.from(e.target.files).slice(0,remaining);
    e.target.value='';
    if(!files.length) return;

    const row=document.getElementById('compressRow'), lbl=document.getElementById('compressLabel');
    row.classList.add('show');

    for(let i=0;i<files.length;i++){
        lbl.textContent=`Mengompresi gambar ${i+1} dari ${files.length}...`;
        const {file, preview} = await compressImage(files[i]);
        rs.files.push(file);
        addThumb(preview, rs.files.length-1, file.size);
    }
    row.classList.remove('show');
}

function addThumb(src, idx, size) {
    const p=document.getElementById('evidencePreview');
    const d=document.createElement('div'); d.className='evidence-thumb'; d.id=`evT-${idx}`;
    d.innerHTML=`<img src="${src}" alt="bukti">
        <button class="evidence-thumb-del" onclick="removeEvidence(${idx})" title="Hapus">
            <svg width="8" height="8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
        <div class="evidence-thumb-sz">${fmtBytes(size)}</div>`;
    p.appendChild(d);
}

function removeEvidence(idx) {
    rs.files.splice(idx,1);
    document.getElementById('evidencePreview').innerHTML='';
    rs.files.forEach((f,i)=>{ const r=new FileReader(); r.onload=e=>addThumb(e.target.result,i,f.size); r.readAsDataURL(f); });
}

function fillConfirm() {
    document.getElementById('confirmReason').textContent=REASON_LABELS[rs.reason]||rs.reason;
    const dr=document.getElementById('confirmDetailRow');
    if(rs.detail){ dr.style.display=''; document.getElementById('confirmDetail').textContent=rs.detail.slice(0,200)+(rs.detail.length>200?'…':''); } else dr.style.display='none';
    const er=document.getElementById('confirmEvidRow');
    if(rs.files.length){ er.style.display=''; document.getElementById('confirmEvidCount').textContent=`${rs.files.length} file terlampir`; } else er.style.display='none';
}

// Navigation
rNext1.addEventListener('click',  ()=>{ if(rs.reason) goToStep(2); });
rNext2.addEventListener('click',  ()=>{ if(rs.detail.length>=10) goToStep(3); });
rBack2.addEventListener('click',  ()=>goToStep(1));
rBack3.addEventListener('click',  ()=>goToStep(2));
rCloseBtn.addEventListener('click', closeReportModal);
rCancel.addEventListener('click',   closeReportModal);
rOverlay.addEventListener('click',  e=>{ if(e.target===rOverlay) closeReportModal(); });
document.getElementById('reportBtn').addEventListener('click', openReportModal);

// Submit
rSubmit.addEventListener('click', async()=>{
    if(!rs.reason) return;
    rSubmit.disabled=true; rSubmit.textContent='Mengirim...';
    try{
        const fd=new FormData();
        fd.append('reason',   rs.reason);
        fd.append('detail',   rs.detail);
        fd.append('page_url', window.location.href);
        rs.files.forEach((f,i)=>fd.append(`evidence[${i}]`,f));

        const res=await fetch(REPORT_ENDPOINT,{method:'POST',headers:{'X-CSRF-TOKEN':CSRF,'Accept':'application/json'},body:fd});
        const j=await res.json();
        if(!res.ok||!j.success) throw new Error(j.message||'Gagal mengirim laporan.');

        document.querySelectorAll('.report-step-panel').forEach(p=>{p.classList.remove('active');p.style.display='none';});
        document.getElementById('reportStepBar').style.display='none';
        document.getElementById('reportHead').style.display='none';
        const s=document.getElementById('reportSuccess'); s.style.display='block'; s.classList.add('show');
        document.getElementById('ticketCode').textContent=j.ticket||('RPT-'+Math.random().toString(36).substr(2,8).toUpperCase());
    } catch(e){
        showToast(e.message||'Terjadi kesalahan.','error');
        rSubmit.disabled=false; rSubmit.textContent='Kirim Laporan';
    }
});

// ═══════════════════════════════════════════════════
// PAGE INIT
// ═══════════════════════════════════════════════════
document.addEventListener('DOMContentLoaded',()=>{
    fetch(`/api/profile/{{ $user->username }}/view`,{method:'POST',headers:{'X-CSRF-TOKEN':CSRF,'Content-Type':'application/json'}}).catch(()=>{});

    const pcs=document.querySelectorAll('[data-product-id]');
    if(pcs.length){
        const ids=[...new Set([...pcs].map(el=>el.getAttribute('data-product-id')))].join(',');
        fetch(`/api/products/batch?ids=${ids}`).then(r=>r.json()).then(m=>{
            pcs.forEach(c=>{ const p=m[c.getAttribute('data-product-id')]; p?renderProductBlock(c,p):c.innerHTML=`<div style="background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:20px;text-align:center;color:#9ca3af;font-size:13px;">Produk tidak tersedia</div>`; });
        }).catch(()=>{ pcs.forEach(c=>{ const id=c.getAttribute('data-product-id'); fetch(`/api/product/${id}/data`).then(r=>r.json()).then(p=>renderProductBlock(c,p)).catch(()=>{c.innerHTML=`<div style="background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:20px;text-align:center;color:#9ca3af;font-size:13px;">Produk tidak tersedia</div>`;}); }); });
    }
    api('/api/cart').then(d=>updateBadge(d.total_items)).catch(()=>{});
});
</script>
</body>
</html>