@extends('layouts.dashboard')

@section('title', 'Tampilan')

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
* { box-sizing: border-box; }

/* ═══════════════════════════════════════════
   LAYOUT UTAMA
═══════════════════════════════════════════ */
.ap-layout {
    display: grid;
    grid-template-columns: 1fr 360px;
    min-height: calc(100vh - 64px);
    align-items: start;
    font-family: 'Plus Jakarta Sans', sans-serif;
}
.ap-editor {
    padding: 32px 36px 120px;
    max-width: 780px;
}
.ap-editor h1 { font-size: 22px; font-weight: 700; color: #111827; margin: 0 0 4px; }
.ap-subtitle  { font-size: 13.5px; color: #9ca3af; margin: 0 0 28px; }

/* ─── Preview Panel ─── */
.ap-preview {
    position: sticky; top: 0; height: 100vh;
    background: #f8fafc; border-left: 1px solid #e5e7eb;
    display: flex; flex-direction: column; align-items: center;
    justify-content: center; padding: 24px 16px; gap: 14px;
}
.ap-preview-label { font-size: 11px; font-weight: 700; color: #94a3b8; letter-spacing: 1px; text-transform: uppercase; }
/* ══════════════════════════════════════════
   PHONE MOCKUP — sama persis dengan Link Saya
   ══════════════════════════════════════════ */
.phone-outer {
    position: relative;
    flex-shrink: 0;
    width: 285px;
}
/* Tombol kanan (power + volume) */
.phone-outer::after {
    content: '';
    position: absolute;
    right: -5px;
    top: 100px;
    width: 4px;
    height: 60px;
    background: #d1d5db;
    border-radius: 0 3px 3px 0;
    box-shadow: 0 -44px 0 0 #d1d5db, 0 70px 0 0 #d1d5db;
}
/* Tombol kiri (volume up/down/silent) */
.phone-outer::before {
    content: '';
    position: absolute;
    left: -5px;
    top: 120px;
    width: 4px;
    height: 40px;
    background: #d1d5db;
    border-radius: 3px 0 0 3px;
    box-shadow: 0 -52px 0 0 #d1d5db, 0 52px 0 0 #d1d5db;
}
.phone-wrap {
    width: 285px;
    background: #1a1a1a;
    border-radius: 44px;
    padding: 10px;
    box-shadow:
        0 0 0 1px #111,
        inset 0 0 0 1px #333,
        0 30px 80px rgba(0,0,0,0.4);
    position: relative;
    overflow: hidden;
}
/* Layar dalam */
.phone-inner {
    background: #fff;
    border-radius: 36px;
    overflow: hidden;
    position: relative;
}
/* Status bar putih */
.phone-status-bar {
    position: relative;
    height: 44px;
    background: #fff;
    display: flex;
    align-items: flex-end;
    justify-content: space-between;
    padding: 0 22px 8px;
    z-index: 10;
    border-radius: 36px 36px 0 0;
}
/* Dynamic island notch */
.phone-notch {
    position: absolute;
    top: 10px; left: 50%; transform: translateX(-50%);
    width: 100px; height: 26px;
    background: #111;
    border-radius: 20px;
    z-index: 11;
}
.phone-status-time {
    font-size: 12px; font-weight: 700; color: #111; letter-spacing: -0.3px;
}
.phone-status-icons {
    display: flex; align-items: center; gap: 4px;
}
.phone-screen {
    width: 100%;
    height: 540px;
    overflow: hidden;
    background: #fff;
    position: relative;
}
.phone-screen iframe {
    width: 375px; height: 700px; border: none;
    transform: scale(0.76); transform-origin: top left;
    pointer-events: auto;
}
/* Home indicator */
.phone-home-bar {
    height: 28px;
    background: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 0 0 36px 36px;
}
.phone-home-bar::after {
    content: '';
    width: 110px; height: 5px;
    background: #111;
    border-radius: 3px;
}
.ap-preview-url {
    font-size: 12px; color: #9ca3af; background: #fff; border: 1px solid #e5e7eb;
    border-radius: 20px; padding: 6px 14px; display: flex; align-items: center; gap: 5px;
}
.ap-preview-url strong { color: #374151; font-weight: 600; }
.btn-refresh-preview {
    font-size: 11.5px; color: #9ca3af; background: none; border: none; cursor: pointer;
    display: flex; align-items: center; gap: 4px; padding: 5px 10px; border-radius: 6px;
    transition: all 0.15s; font-family: 'Plus Jakarta Sans', sans-serif;
}
.btn-refresh-preview:hover { background: #f1f5f9; color: #475569; }

/* ═══════════════════════════════════════════
   SECTION CARDS
═══════════════════════════════════════════ */
.sec-card { background: #fff; border: 1px solid #e5e7eb; border-radius: 16px; padding: 22px 24px; margin-bottom: 16px; }
.sec-header { display: flex; align-items: center; gap: 10px; margin-bottom: 18px; }
.sec-icon { width: 34px; height: 34px; background: #eff6ff; border-radius: 9px; display: flex; align-items: center; justify-content: center; color: #3b82f6; flex-shrink: 0; }
.sec-title { font-size: 14px; font-weight: 700; color: #111827; margin: 0; }
.sec-desc  { font-size: 12px; color: #9ca3af; margin: 1px 0 0; }

/* ═══════════════════════════════════════════
   PROFIL CARD
═══════════════════════════════════════════ */
.pc-banner-wrap {
    position: relative; width: 100%; height: 130px;
    background: #f3f4f6; border-radius: 12px; overflow: hidden;
    border: 2px dashed #e5e7eb; cursor: pointer; transition: all 0.18s;
    display: flex; align-items: center; justify-content: center; flex-direction: column; gap: 6px;
}
.pc-banner-wrap:hover { border-color: #93c5fd; background: #f0f9ff; }
.pc-banner-wrap.has-img { border-style: solid; border-color: #e5e7eb; }
.pc-banner-img { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; display: none; }
.pc-banner-hint { font-size: 12px; color: #9ca3af; font-weight: 500; position: relative; z-index: 1; }
.pc-banner-hint svg { display: block; margin: 0 auto 5px; }
.pc-banner-overlay {
    position: absolute; inset: 0; background: rgba(0,0,0,0.35);
    display: none; align-items: center; justify-content: center;
    color: #fff; font-size: 12px; font-weight: 600; gap: 5px; z-index: 2;
}
.pc-banner-wrap.has-img .pc-banner-hint { display: none; }
.pc-banner-wrap.has-img:hover .pc-banner-overlay { display: flex; }
.pc-banner-size { font-size: 11px; color: #9ca3af; margin-top: 6px; display: block; text-align: center; }

.pc-about-wrap { position: relative; }
.pc-about-wrap textarea {
    width: 100%; min-height: 90px; padding: 10px 40px 10px 12px;
    border: 1.5px solid #e5e7eb; border-radius: 10px;
    font-size: 13.5px; color: #374151; resize: vertical; outline: none;
    font-family: 'Plus Jakarta Sans', sans-serif; line-height: 1.5;
    transition: border-color 0.15s;
}
.pc-about-wrap textarea:focus { border-color: #3b82f6; }
.pc-emoji-btn {
    position: absolute; right: 9px; top: 9px;
    background: none; border: none; font-size: 20px; cursor: pointer; padding: 2px;
    border-radius: 6px; transition: background 0.15s;
}
.pc-emoji-btn:hover { background: #f3f4f6; }

.pc-color-row { display: flex; align-items: center; gap: 12px; margin-top: 2px; }
.pc-color-row label { font-size: 13px; font-weight: 600; color: #374151; white-space: nowrap; }
.pc-color-inner {
    display: flex; align-items: center; gap: 8px;
    border: 1.5px solid #e5e7eb; border-radius: 9px; padding: 6px 10px;
    flex: 1; max-width: 180px; transition: border-color 0.15s;
}
.pc-color-inner:focus-within { border-color: #3b82f6; }
.pc-color-dot { width: 26px; height: 26px; border-radius: 6px; border: 1.5px solid #e5e7eb; overflow: hidden; position: relative; cursor: pointer; flex-shrink: 0; }
.pc-color-dot input[type="color"] { position: absolute; inset: -5px; width: calc(100% + 10px); height: calc(100% + 10px); border: none; padding: 0; cursor: pointer; opacity: 0; }
.pc-color-text { flex: 1; border: none; outline: none; font-size: 12px; font-family: 'Courier New', monospace; color: #374151; background: transparent; }
.pc-color-presets { display: flex; gap: 5px; margin-top: 10px; flex-wrap: wrap; }
.pc-color-preset { width: 24px; height: 24px; border-radius: 50%; cursor: pointer; border: 2px solid transparent; transition: all 0.15s; flex-shrink: 0; }
.pc-color-preset:hover { transform: scale(1.15); }
.pc-color-preset.active { border-color: #111; box-shadow: 0 0 0 2px #fff, 0 0 0 4px #111; }

.sl-chips { display: flex; flex-wrap: wrap; gap: 7px; margin-bottom: 14px; }
.sl-chip {
    padding: 6px 14px; border: 1.5px solid #e5e7eb; border-radius: 50px;
    font-size: 12px; font-weight: 600; color: #374151; cursor: pointer;
    transition: all 0.18s; background: #fff; display: flex; align-items: center; gap: 5px;
    font-family: 'Plus Jakarta Sans', sans-serif;
}
.sl-chip:hover  { border-color: #10b981; color: #10b981; }
.sl-chip.active { border-color: #10b981; background: #10b981; color: #fff; }
.sl-chip .sl-icon { font-size: 13px; }

.sl-inputs { display: flex; flex-direction: column; gap: 8px; }
.sl-input-row {
    display: flex; align-items: center; gap: 8px;
    border: 1.5px solid #e5e7eb; border-radius: 10px; padding: 9px 12px;
    background: #fff; animation: slIn 0.2s ease;
}
@keyframes slIn { from { opacity:0; transform:translateY(-6px); } to { opacity:1; transform:translateY(0); } }
.sl-input-row:focus-within { border-color: #3b82f6; }
.sl-input-label { font-size: 12px; font-weight: 700; color: #10b981; white-space: nowrap; min-width: 70px; }
.sl-input-field { flex: 1; border: none; outline: none; font-size: 13px; color: #374151; background: transparent; font-family: 'Plus Jakarta Sans', sans-serif; }
.sl-input-remove { background: none; border: none; color: #d1d5db; cursor: pointer; padding: 2px; border-radius: 4px; transition: color 0.15s; font-size: 14px; display: flex; }
.sl-input-remove:hover { color: #ef4444; }

/* ═══════════════════════════════════════════
   BG TABS
═══════════════════════════════════════════ */
.bg-tabs { display: flex; background: #f3f4f6; border-radius: 10px; padding: 3px; gap: 2px; margin-bottom: 18px; }
.bg-tab {
    flex: 1; padding: 8px 10px; background: none; border: none; border-radius: 8px;
    font-size: 12.5px; font-weight: 600; color: #6b7280; cursor: pointer; transition: all 0.18s;
    font-family: 'Plus Jakarta Sans', sans-serif; text-align: center;
    display: flex; align-items: center; justify-content: center; gap: 5px;
}
.bg-tab.active { background: #fff; color: #111827; box-shadow: 0 1px 4px rgba(0,0,0,0.1); }
.bg-panel        { display: none; }
.bg-panel.active { display: block; }

.color-row { display: flex; align-items: center; gap: 10px; margin-bottom: 14px; }
.color-row label { font-size: 13px; font-weight: 600; color: #374151; min-width: 68px; }
.color-swatch-wrap { display: flex; align-items: center; gap: 8px; flex: 1; }
.color-swatch { width: 34px; height: 34px; border-radius: 8px; border: 2px solid #e5e7eb; cursor: pointer; flex-shrink: 0; overflow: hidden; position: relative; }
.color-swatch input[type="color"] { position: absolute; inset: -6px; width: calc(100% + 12px); height: calc(100% + 12px); border: none; padding: 0; cursor: pointer; opacity: 0; }
.color-input { flex: 1; padding: 8px 11px; border: 1.5px solid #e5e7eb; border-radius: 8px; font-size: 12.5px; font-family: 'Courier New', monospace; color: #374151; outline: none; max-width: 105px; transition: border-color 0.15s; }
.color-input:focus { border-color: #3b82f6; }
.preset-row { display: flex; flex-wrap: wrap; gap: 7px; margin-top: 14px; }
.preset-dot { width: 28px; height: 28px; border-radius: 7px; cursor: pointer; border: 2px solid transparent; transition: all 0.15s; flex-shrink: 0; }
.preset-dot:hover { transform: scale(1.12); }
.preset-dot.active { border-color: #111827; box-shadow: 0 0 0 2px #fff, 0 0 0 4px #111827; }
.dir-label { font-size: 12.5px; font-weight: 600; color: #374151; margin-bottom: 8px; }
.dir-grid  { display: grid; grid-template-columns: repeat(4, 1fr); gap: 7px; }
.dir-btn   { height: 38px; border: 1.5px solid #e5e7eb; border-radius: 8px; background: #f9fafb; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 17px; transition: all 0.15s; color: #6b7280; font-family: 'Plus Jakarta Sans', sans-serif; }
.dir-btn:hover  { border-color: #93c5fd; background: #eff6ff; color: #3b82f6; }
.dir-btn.active { border-color: #3b82f6; background: #eff6ff; color: #3b82f6; }

.bg-upload { border: 2px dashed #e5e7eb; border-radius: 12px; padding: 22px 16px; text-align: center; cursor: pointer; transition: all 0.18s; position: relative; background: #fafafa; }
.bg-upload:hover { border-color: #93c5fd; background: #f0f9ff; }
.bg-upload-icon { width: 40px; height: 40px; background: #eff6ff; border-radius: 11px; display: flex; align-items: center; justify-content: center; margin: 0 auto 8px; color: #3b82f6; }
.bg-upload p    { font-size: 13px; color: #374151; font-weight: 600; margin: 0 0 3px; }
.bg-upload span { font-size: 11.5px; color: #9ca3af; }
.bg-upload-preview { width: 100%; height: 90px; border-radius: 10px; object-fit: cover; margin-top: 10px; border: 1.5px solid #e5e7eb; display: none; }

/* ═══════════════════════════════════════════
   WALLPAPER GALLERY
═══════════════════════════════════════════ */
.wg-divider { display: flex; align-items: center; gap: 10px; margin: 16px 0 12px; }
.wg-divider-line  { flex: 1; height: 1px; background: #e5e7eb; }
.wg-divider-label { font-size: 11px; font-weight: 700; color: #9ca3af; letter-spacing: 0.6px; text-transform: uppercase; white-space: nowrap; }

.wg-cats { display: flex; gap: 6px; flex-wrap: wrap; margin-bottom: 12px; }
.wg-cat {
    padding: 4px 12px; border: 1.5px solid #e5e7eb; border-radius: 20px;
    background: #fff; font-size: 11.5px; font-weight: 600; color: #6b7280;
    cursor: pointer; transition: all 0.15s; font-family: 'Plus Jakarta Sans', sans-serif;
}
.wg-cat:hover  { border-color: #93c5fd; color: #3b82f6; }
.wg-cat.active { border-color: #3b82f6; background: #eff6ff; color: #3b82f6; }

.wg-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 7px; }
.wg-item {
    border-radius: 10px; overflow: hidden; cursor: pointer;
    border: 2.5px solid transparent; transition: all 0.18s;
    position: relative; aspect-ratio: 9/16;
}
.wg-item:hover  { border-color: #93c5fd; transform: translateY(-2px); box-shadow: 0 6px 16px rgba(59,130,246,0.15); }
.wg-item.active { border-color: #3b82f6; box-shadow: 0 0 0 3px rgba(59,130,246,0.2); }
.wg-item.active::after {
    content: '✓'; position: absolute; top: 5px; right: 5px;
    width: 18px; height: 18px; background: #3b82f6; color: #fff;
    border-radius: 50%; font-size: 9px; font-weight: 800;
    display: flex; align-items: center; justify-content: center; line-height: 1;
}
.wg-thumb { width: 100%; height: 100%; display: block; pointer-events: none; }
.wg-label { position: absolute; bottom: 0; left: 0; right: 0; background: rgba(0,0,0,0.45); color: #fff; font-size: 9px; font-weight: 700; text-align: center; padding: 3px 2px; }

/* ═══════════════════════════════════════════
   BUTTONS SECTION
═══════════════════════════════════════════ */
.btn-style-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px; margin-bottom: 18px; }
.btn-style-item { border: 2px solid #e5e7eb; border-radius: 10px; padding: 14px 10px; cursor: pointer; transition: all 0.18s; text-align: center; background: #fafafa; }
.btn-style-item:hover  { border-color: #93c5fd; background: #f0f9ff; }
.btn-style-item.active { border-color: #3b82f6; background: #eff6ff; }
.btn-style-preview { height: 30px; border-radius: 6px; display: flex; align-items: center; justify-content: center; font-size: 11px; font-weight: 600; margin-bottom: 7px; color: #374151; font-family: 'Plus Jakarta Sans', sans-serif; transition: all 0.18s; }
.btn-style-name    { font-size: 11px; font-weight: 600; color: #6b7280; }

.btn-shape-group { display: flex; gap: 8px; margin-bottom: 18px; }
.btn-shape-item  { flex: 1; border: 2px solid #e5e7eb; border-radius: 10px; padding: 12px 8px; cursor: pointer; transition: all 0.18s; text-align: center; background: #fafafa; }
.btn-shape-item:hover  { border-color: #93c5fd; }
.btn-shape-item.active { border-color: #3b82f6; background: #eff6ff; }
.btn-shape-preview { height: 32px; border: 2px solid #374151; background: transparent; margin: 0 6px 8px; display: flex; align-items: center; justify-content: center; }
.btn-shape-name    { font-size: 11px; font-weight: 600; color: #6b7280; }

.color-row-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-top: 4px; }
.color-field label { display: block; font-size: 12px; font-weight: 600; color: #374151; margin-bottom: 7px; }
.color-field-inner { display: flex; align-items: center; gap: 8px; border: 1.5px solid #e5e7eb; border-radius: 9px; padding: 6px 10px; transition: border-color 0.15s; }
.color-field-inner:focus-within { border-color: #3b82f6; }
.color-dot { width: 26px; height: 26px; border-radius: 6px; flex-shrink: 0; border: 1.5px solid #e5e7eb; overflow: hidden; position: relative; cursor: pointer; }
.color-dot input[type="color"] { position: absolute; inset: -5px; width: calc(100% + 10px); height: calc(100% + 10px); border: none; padding: 0; cursor: pointer; opacity: 0; }
.color-field-input { flex: 1; border: none; outline: none; font-size: 12px; font-family: 'Courier New', monospace; color: #374151; background: transparent; }

/* ═══════════════════════════════════════════
   FONTS
═══════════════════════════════════════════ */
.font-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 10px; }
.font-item  { border: 2px solid #e5e7eb; border-radius: 11px; padding: 14px 14px 12px; cursor: pointer; transition: all 0.18s; background: #fafafa; }
.font-item:hover  { border-color: #93c5fd; background: #f0f9ff; }
.font-item.active { border-color: #3b82f6; background: #eff6ff; }
.font-sample { font-size: 22px; font-weight: 700; color: #111827; margin-bottom: 4px; line-height: 1.2; }
.font-name   { font-size: 11px; font-weight: 600; color: #9ca3af; }

/* ═══════════════════════════════════════════
   SAVE BAR
═══════════════════════════════════════════ */
.ap-save-bar {
    position: sticky; bottom: 0;
    background: rgba(255,255,255,0.96); backdrop-filter: blur(14px);
    border-top: 1px solid #e5e7eb; padding: 14px 36px;
    display: flex; align-items: center; justify-content: space-between; gap: 12px;
    z-index: 50; margin: 0 -36px;
}
.save-bar-hint .dot { color: #f59e0b; font-weight: 700; }

.save-bar-actions   { display: flex; gap: 9px; }
.btn-reset-def { padding: 9px 18px; border: 1.5px solid #e5e7eb; border-radius: 9px; background: #fff; font-size: 13px; font-weight: 600; color: #6b7280; cursor: pointer; transition: all 0.18s; font-family: 'Plus Jakarta Sans', sans-serif; }
.btn-reset-def:hover { border-color: #ef4444; color: #ef4444; }
.btn-save-ap { padding: 9px 22px; background: #3b82f6; border: none; border-radius: 9px; color: #fff; font-size: 13px; font-weight: 700; cursor: pointer; transition: all 0.18s; display: flex; align-items: center; gap: 7px; font-family: 'Plus Jakarta Sans', sans-serif; min-width: 120px; justify-content: center; }
.btn-save-ap:hover    { background: #2563eb; }
.btn-save-ap:disabled { opacity: 0.55; cursor: not-allowed; }
.btn-save-ap .spin    { width: 15px; height: 15px; border: 2px solid rgba(255,255,255,0.35); border-top-color: #fff; border-radius: 50%; animation: sp 0.6s linear infinite; display: none; }
.btn-save-ap.loading .spin    { display: block; }
.btn-save-ap.loading .save-lbl{ display: none; }
@keyframes sp { to { transform: rotate(360deg); } }

/* Toast */
.ap-toast { position: fixed; bottom: 90px; left: 50%; transform: translateX(-50%) translateY(16px); background: #18181b; color: #fff; padding: 10px 20px; border-radius: 50px; font-size: 13px; font-weight: 500; z-index: 9999; opacity: 0; transition: all 0.28s; white-space: nowrap; pointer-events: none; font-family: 'Plus Jakarta Sans', sans-serif; }
.ap-toast.show    { opacity: 1; transform: translateX(-50%) translateY(0); }
.ap-toast.success { background: #16a34a; }
.ap-toast.error   { background: #dc2626; }

.ap-divider { height: 1px; background: #f1f5f9; margin: 16px 0; }

@media (max-width: 1080px) {
    .ap-layout { grid-template-columns: 1fr; }
    .ap-preview { display: none; }
    .ap-save-bar { margin: 0 -16px; padding: 14px 20px; }
    .ap-editor   { padding: 20px 16px 100px; }
}
</style>
@endpush

@section('content')
<div class="ap-toast" id="apToast"></div>

<div class="ap-layout">

    {{-- ═══════════ EDITOR ═══════════ --}}
    <div class="ap-editor">
        <h1>Tampilan</h1>
        <p class="ap-subtitle">Kustomisasi tampilan halaman profil publik kamu</p>

        {{-- ══════════ 1. PROFIL CARD ══════════ --}}
        <div class="sec-card">
            <div class="sec-header">
                <div class="sec-icon">
                    <svg width="17" height="17" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                </div>
                <div>
                    <p class="sec-title">Profil Card</p>
                    <p class="sec-desc">Kustomisasi banner, bio, warna teks, dan social link</p>
                </div>
            </div>

            {{-- Banner --}}
            <p style="font-size:12.5px;font-weight:700;color:#374151;margin-bottom:8px;">Banner</p>
            <div class="pc-banner-wrap {{ $profile->banner_image ? 'has-img' : '' }}"
                 id="pcBannerWrap"
                 onclick="document.getElementById('pcBannerInput').click()">
                <img id="pcBannerImg" class="pc-banner-img"
                     src="{{ $profile->banner_image ? asset('storage/'.$profile->banner_image) : '' }}"
                     style="{{ $profile->banner_image ? 'display:block' : '' }}">
                <div class="pc-banner-overlay">
                    <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><circle cx="12" cy="13" r="3" stroke-width="2"/></svg>
                    Ganti Banner
                </div>
                <div class="pc-banner-hint">
                    <svg width="28" height="28" fill="none" stroke="#9ca3af" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    Optimize banner size 1200 x 628 px
                </div>
                <input type="file" id="pcBannerInput" accept="image/*" style="display:none" onchange="handleBannerUpload(this)">
            </div>
            <span class="pc-banner-size">PNG, JPG, WEBP — maks 3MB</span>

            <div class="ap-divider"></div>

            {{-- About --}}
            <p style="font-size:12.5px;font-weight:700;color:#374151;margin-bottom:8px;">About</p>
            <div class="pc-about-wrap">
                <textarea id="pcAbout"
                          placeholder="Tulis bio singkat tentang dirimu..."
                          oninput="onAboutIn(this.value)"
                >{{ $profile->about ?? '' }}</textarea>
                <button class="pc-emoji-btn" type="button" title="Emoji">😊</button>
            </div>

            <div class="ap-divider"></div>

            {{-- Text Color --}}
            <p style="font-size:12.5px;font-weight:700;color:#374151;margin-bottom:10px;">Warna Teks Profil</p>
            <div class="pc-color-row">
                <label>Warna</label>
                <div class="pc-color-inner">
                    <div class="pc-color-dot" id="pcTxtColorDot" style="background:{{ $profile->text_color ?? '#111827' }}">
                        <input type="color" id="pcTxtColorPicker"
                               value="{{ $profile->text_color ?? '#111827' }}"
                               oninput="onTxtColorIn(this.value)">
                    </div>
                    <input type="text" class="pc-color-text" id="pcTxtColorText"
                           value="{{ $profile->text_color ?? '#111827' }}"
                           oninput="onTxtColorTextIn(this.value)" placeholder="#111827">
                </div>
            </div>
            @php
            $txtPresets = ['#111827','#ffffff','#374151','#6b7280','#3b82f6','#10b981','#f59e0b','#ef4444','#8b5cf6','#ec4899','#0ea5e9','#f97316'];
            @endphp
            <div class="pc-color-presets">
                @foreach($txtPresets as $c)
                <div class="pc-color-preset {{ ($profile->text_color ?? '#111827') === $c ? 'active' : '' }}"
                     style="background:{{ $c }};{{ in_array($c,['#ffffff']) ? 'border-color:#d1d5db;' : '' }}"
                     onclick="selectTxtColorPreset('{{ $c }}',this)" title="{{ $c }}"></div>
                @endforeach
            </div>

            <div class="ap-divider"></div>

            {{-- Social Links --}}
            <p style="font-size:12.5px;font-weight:700;color:#374151;margin-bottom:10px;">Social Links</p>
            <div class="sl-chips" id="slChips"></div>
            <div class="sl-inputs" id="slInputs"></div>
        </div>

        {{-- ══════════ 2. BACKGROUND ══════════ --}}
        <div class="sec-card">
            <div class="sec-header">
                <div class="sec-icon">
                    <svg width="17" height="17" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/></svg>
                </div>
                <div>
                    <p class="sec-title">Latar Belakang</p>
                    <p class="sec-desc">Atur warna atau gambar latar halaman profilmu</p>
                </div>
            </div>

            <div class="bg-tabs">
                <button class="bg-tab {{ ($profile->bg_type ?? 'color') === 'color' ? 'active' : '' }}" data-bg="color" onclick="switchBgTab(this,'color')">
                    <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke-width="2"/></svg> Warna
                </button>
                <button class="bg-tab {{ ($profile->bg_type ?? 'color') === 'gradient' ? 'active' : '' }}" data-bg="gradient" onclick="switchBgTab(this,'gradient')">
                    <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4l16 16M4 20h16V4"/></svg> Gradien
                </button>
                <button class="bg-tab {{ ($profile->bg_type ?? 'color') === 'image' ? 'active' : '' }}" data-bg="image" onclick="switchBgTab(this,'image')">
                    <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg> Gambar
                </button>
            </div>

            {{-- Panel: Color --}}
            <div class="bg-panel {{ ($profile->bg_type ?? 'color') === 'color' ? 'active' : '' }}" id="panel-color">
                <div class="color-row">
                    <label>Warna</label>
                    <div class="color-swatch-wrap">
                        <div class="color-swatch" id="bgColorSwatch" style="background: {{ $profile->background_color ?? '#ffffff' }}">
                            <input type="color" id="bgColorPicker" value="{{ $profile->background_color ?? '#ffffff' }}" oninput="onBgColorIn(this.value)">
                        </div>
                        <input type="text" class="color-input" id="bgColorText" value="{{ $profile->background_color ?? '#ffffff' }}" oninput="onBgColorTextIn(this.value)" placeholder="#ffffff">
                    </div>
                </div>
                @php $presets = ['#ffffff','#f8fafc','#111827','#1e293b','#fef9c3','#ecfdf5','#eff6ff','#fdf2f8','#fff7ed','#f0fdf4','#e0f2fe','#fce7f3','#f1f5f9','#fafafa','#18181b','#0f172a']; @endphp
                <div class="preset-row">
                    @foreach($presets as $c)
                    <div class="preset-dot {{ ($profile->background_color ?? '#ffffff') === $c ? 'active' : '' }}"
                         style="background:{{ $c }};{{ in_array($c,['#ffffff','#fafafa']) ? 'border-color:#d1d5db;' : '' }}"
                         onclick="selectPreset('{{ $c }}',this)" title="{{ $c }}"></div>
                    @endforeach
                </div>
            </div>

            {{-- Panel: Gradient --}}
            <div class="bg-panel {{ ($profile->bg_type ?? 'color') === 'gradient' ? 'active' : '' }}" id="panel-gradient">
                <div class="color-row">
                    <label>Warna 1</label>
                    <div class="color-swatch-wrap">
                        <div class="color-swatch" id="g1Swatch" style="background:{{ $profile->bg_gradient_start ?? '#2563eb' }}">
                            <input type="color" id="g1Picker" value="{{ $profile->bg_gradient_start ?? '#2563eb' }}" oninput="onGradIn()">
                        </div>
                        <input type="text" class="color-input" id="g1Text" value="{{ $profile->bg_gradient_start ?? '#2563eb' }}" oninput="syncTxt('g1Text','g1Picker','g1Swatch');onGradIn()" placeholder="#2563eb">
                    </div>
                </div>
                <div class="color-row">
                    <label>Warna 2</label>
                    <div class="color-swatch-wrap">
                        <div class="color-swatch" id="g2Swatch" style="background:{{ $profile->bg_gradient_end ?? '#8b5cf6' }}">
                            <input type="color" id="g2Picker" value="{{ $profile->bg_gradient_end ?? '#8b5cf6' }}" oninput="onGradIn()">
                        </div>
                        <input type="text" class="color-input" id="g2Text" value="{{ $profile->bg_gradient_end ?? '#8b5cf6' }}" oninput="syncTxt('g2Text','g2Picker','g2Swatch');onGradIn()" placeholder="#8b5cf6">
                    </div>
                </div>
                <p class="dir-label">Arah Gradien</p>
                @php
                $dirs = [['v'=>'to top','i'=>'↑'],['v'=>'to bottom','i'=>'↓'],['v'=>'to left','i'=>'←'],['v'=>'to right','i'=>'→'],['v'=>'to top left','i'=>'↖'],['v'=>'to top right','i'=>'↗'],['v'=>'to bottom left','i'=>'↙'],['v'=>'to bottom right','i'=>'↘']];
                $curDir = $profile->bg_gradient_direction ?? 'to bottom';
                @endphp
                <div class="dir-grid">
                    @foreach($dirs as $d)
                    <button class="dir-btn {{ $curDir === $d['v'] ? 'active' : '' }}" data-dir="{{ $d['v'] }}" onclick="selectDir(this,'{{ $d['v'] }}')">{{ $d['i'] }}</button>
                    @endforeach
                </div>
            </div>

            {{-- Panel: Image --}}
            <div class="bg-panel {{ ($profile->bg_type ?? 'color') === 'image' ? 'active' : '' }}" id="panel-image">
                <div class="bg-upload" onclick="document.getElementById('bgImgInput').click()">
                    <div class="bg-upload-icon">
                        <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </div>
                    <p>Upload gambar sendiri</p>
                    <span>PNG, JPG, WEBP — maks 2MB</span>
                    <input type="file" id="bgImgInput" accept="image/*" style="display:none" onchange="handleBgImg(this)">
                </div>
                @if($profile->bg_image && !str_starts_with($profile->bg_image ?? '', 'wg_'))
                <img src="{{ asset('storage/'.$profile->bg_image) }}" id="bgImgPreview" class="bg-upload-preview" style="display:block">
                @else
                <img src="" id="bgImgPreview" class="bg-upload-preview">
                @endif

                <div class="wg-divider">
                    <div class="wg-divider-line"></div>
                    <span class="wg-divider-label">✦ Galeri Wallpaper Payou</span>
                    <div class="wg-divider-line"></div>
                </div>

                <div class="wg-cats" id="wgCats">
                    <button class="wg-cat active" onclick="filterWg(this,'all')">Semua</button>
                    <button class="wg-cat" onclick="filterWg(this,'gradient')">Gradien</button>
                    <button class="wg-cat" onclick="filterWg(this,'pattern')">Pattern</button>
                    <button class="wg-cat" onclick="filterWg(this,'minimal')">Minimal</button>
                    <button class="wg-cat" onclick="filterWg(this,'dark')">Gelap</button>
                </div>

                <div class="wg-grid" id="wgGrid"></div>
            </div>
        </div>

        {{-- ══════════ 3. BUTTONS ══════════ --}}
        <div class="sec-card">
            <div class="sec-header">
                <div class="sec-icon" style="background:#f0fdf4; color:#16a34a;">
                    <svg width="17" height="17" fill="none" stroke="currentColor" viewBox="0 0 24 24"><rect x="3" y="8" width="18" height="8" rx="4" stroke-width="2"/></svg>
                </div>
                <div>
                    <p class="sec-title">Tombol</p>
                    <p class="sec-desc">Gaya tombol untuk link dan produkmu</p>
                </div>
            </div>

            <p style="font-size:12.5px;font-weight:700;color:#374151;margin-bottom:10px;">Gaya Tombol</p>
            @php
            $btnStyles = [
                ['id'=>'fill',        'name'=>'Fill',        'css'=>'background:#3b82f6;color:#fff;border:2px solid #3b82f6;'],
                ['id'=>'outline',     'name'=>'Outline',     'css'=>'background:transparent;color:#3b82f6;border:2px solid #3b82f6;'],
                ['id'=>'hard_shadow', 'name'=>'Hard Shadow', 'css'=>'background:#fff;color:#111;border:2px solid #111;box-shadow:3px 3px 0 #111;'],
                ['id'=>'soft_shadow', 'name'=>'Soft Shadow', 'css'=>'background:#fff;color:#374151;border:1.5px solid #e5e7eb;box-shadow:0 4px 12px rgba(0,0,0,0.12);'],
                ['id'=>'ghost',       'name'=>'Ghost',       'css'=>'background:rgba(255,255,255,0.12);color:#fff;border:1.5px solid rgba(255,255,255,0.3);backdrop-filter:blur(8px);'],
                ['id'=>'minimal',     'name'=>'Minimal',     'css'=>'background:transparent;color:#111;border:none;border-bottom:2px solid #111;border-radius:0!important;'],
            ];
            $curBtnStyle = $profile->btn_style ?? 'fill';
            @endphp
            <div class="btn-style-grid">
                @foreach($btnStyles as $bs)
                <div class="btn-style-item {{ $curBtnStyle === $bs['id'] ? 'active' : '' }}"
                     data-bstyle="{{ $bs['id'] }}" onclick="selectBtnStyle(this,'{{ $bs['id'] }}')">
                    <div class="btn-style-preview" style="{{ $bs['css'] }}">Link Kamu</div>
                    <div class="btn-style-name">{{ $bs['name'] }}</div>
                </div>
                @endforeach
            </div>

            <div class="ap-divider"></div>

            <p style="font-size:12.5px;font-weight:700;color:#374151;margin-bottom:10px;">Bentuk Tombol</p>
            @php
            $shapes = [
                ['id'=>'square',  'name'=>'Square',  'r'=>'border-radius:4px;'],
                ['id'=>'rounded', 'name'=>'Rounded', 'r'=>'border-radius:10px;'],
                ['id'=>'pill',    'name'=>'Pill',    'r'=>'border-radius:50px;'],
            ];
            $curShape = $profile->btn_shape ?? 'rounded';
            @endphp
            <div class="btn-shape-group">
                @foreach($shapes as $sh)
                <div class="btn-shape-item {{ $curShape === $sh['id'] ? 'active' : '' }}"
                     data-shape="{{ $sh['id'] }}" onclick="selectBtnShape(this,'{{ $sh['id'] }}')">
                    <div class="btn-shape-preview" style="{{ $sh['r'] }}"></div>
                    <div class="btn-shape-name">{{ $sh['name'] }}</div>
                </div>
                @endforeach
            </div>

            <div class="ap-divider"></div>

            <p style="font-size:12.5px;font-weight:700;color:#374151;margin-bottom:10px;">Warna Tombol</p>
            <div class="color-row-2">
                <div class="color-field">
                    <label>Warna Tombol</label>
                    <div class="color-field-inner">
                        <div class="color-dot" id="btnColorDot" style="background:{{ $profile->btn_color ?? '#3b82f6' }}">
                            <input type="color" id="btnColorPicker" value="{{ $profile->btn_color ?? '#3b82f6' }}" oninput="onBtnColorIn(this.value,'btn')">
                        </div>
                        <input type="text" class="color-field-input" id="btnColorText" value="{{ $profile->btn_color ?? '#3b82f6' }}" oninput="onBtnColorTextIn(this.value,'btn')" placeholder="#3b82f6">
                    </div>
                </div>
                <div class="color-field">
                    <label>Warna Teks</label>
                    <div class="color-field-inner">
                        <div class="color-dot" id="btnTxtColorDot" style="background:{{ $profile->btn_text_color ?? '#ffffff' }}">
                            <input type="color" id="btnTxtColorPicker" value="{{ $profile->btn_text_color ?? '#ffffff' }}" oninput="onBtnColorIn(this.value,'txt')">
                        </div>
                        <input type="text" class="color-field-input" id="btnTxtColorText" value="{{ $profile->btn_text_color ?? '#ffffff' }}" oninput="onBtnColorTextIn(this.value,'txt')" placeholder="#ffffff">
                    </div>
                </div>
            </div>
        </div>

        {{-- ══════════ 4. FONT ══════════ --}}
        <div class="sec-card">
            <div class="sec-header">
                <div class="sec-icon" style="background:#fef3c7; color:#d97706;">
                    <svg width="17" height="17" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                </div>
                <div>
                    <p class="sec-title">Font</p>
                    <p class="sec-desc">Pilih tipografi untuk halaman profilmu</p>
                </div>
            </div>
            @php
            $fonts = [
                ['id'=>'Plus Jakarta Sans','sample'=>'Aa','family'=>"'Plus Jakarta Sans', sans-serif"],
                ['id'=>'Inter',            'sample'=>'Aa','family'=>"'Inter', sans-serif"],
                ['id'=>'Poppins',          'sample'=>'Aa','family'=>"'Poppins', sans-serif"],
                ['id'=>'Lato',             'sample'=>'Aa','family'=>"'Lato', sans-serif"],
                ['id'=>'Merriweather',     'sample'=>'Aa','family'=>"'Merriweather', serif"],
                ['id'=>'Space Grotesk',    'sample'=>'Aa','family'=>"'Space Grotesk', sans-serif"],
                ['id'=>'Nunito',           'sample'=>'Aa','family'=>"'Nunito', sans-serif"],
                ['id'=>'DM Sans',          'sample'=>'Aa','family'=>"'DM Sans', sans-serif"],
            ];
            $curFont = $profile->font_family ?? 'Plus Jakarta Sans';
            @endphp
            <div class="font-grid">
                @foreach($fonts as $f)
                <div class="font-item {{ $curFont === $f['id'] ? 'active' : '' }}"
                     data-font="{{ $f['id'] }}" onclick="selectFont(this,'{{ $f['id'] }}')">
                    <div class="font-sample" style="font-family: {{ $f['family'] }}">{{ $f['sample'] }}</div>
                    <div class="font-name">{{ $f['id'] }}</div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- ══════════ SAVE BAR ══════════ --}}
        <div class="ap-save-bar">
            <div class="save-bar-actions" style="display:flex;gap:9px;">
                <button class="btn-reset-def" onclick="resetAppearance()">Reset Default</button>
                <button class="btn-save-ap" id="btnSave" onclick="saveAppearance()">
                    <div class="spin"></div>
                    <span class="save-lbl">Simpan Tampilan</span>
                </button>
            </div>
        </div>
    </div>

    {{-- ═══════════ PREVIEW ═══════════ --}}
    <div class="ap-preview">
        <div class="ap-preview-label">Preview Langsung</div>
        <div class="phone-outer">
            <div class="phone-wrap">
                <div class="phone-inner">
                    <div class="phone-status-bar">
                        <div class="phone-notch"></div>
                        <span class="phone-status-time">9:41</span>
                        <div class="phone-status-icons">
                            <svg width="15" height="11" viewBox="0 0 15 11" fill="#111"><rect x="0" y="3" width="3" height="8" rx="1"/><rect x="4" y="2" width="3" height="9" rx="1"/><rect x="8" y="0.5" width="3" height="10.5" rx="1"/><rect x="12" y="0" width="2.5" height="11" rx="1" opacity=".3"/></svg>
                            <svg width="15" height="11" viewBox="0 0 24 24" fill="none" stroke="#111" stroke-width="2" stroke-linecap="round"><path d="M1.5 8.5C5.5 4.5 18.5 4.5 22.5 8.5M5 12C8 9 16 9 19 12M9 15.5c1.5-1.5 5.5-1.5 7 0M13 19h.01"/></svg>
                            <svg width="25" height="12" viewBox="0 0 25 12" fill="none"><rect x="0.5" y="0.5" width="21" height="11" rx="3.5" stroke="#111" stroke-opacity="0.35"/><rect x="2" y="2" width="17" height="8" rx="2" fill="#111"/><path d="M23 4.5V7.5C23.8 7.2 24.5 6.4 24.5 6C24.5 5.6 23.8 4.8 23 4.5Z" fill="#111" fill-opacity="0.4"/></svg>
                        </div>
                    </div>
                    <div class="phone-screen">
                        <iframe id="previewFrame" src="{{ route('dashboard.appearance.preview') }}" title="Preview profil"></iframe>
                    </div>
                    <div class="phone-home-bar"></div>
                </div>
            </div>
        </div>
        <div class="ap-preview-url">
            <svg width="11" height="11" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
            payou.id/<strong>{{ $user->username }}</strong>
        </div>
        <button class="btn-refresh-preview" onclick="reloadPreview()">
            <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
            Refresh Preview
        </button>
    </div>

</div>
@endsection

@push('scripts')
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&family=Poppins:wght@400;700&family=Lato:wght@400;700&family=Merriweather:wght@400;700&family=Space+Grotesk:wght@400;700&family=Nunito:wght@400;700&family=DM+Sans:wght@400;700&display=swap" rel="stylesheet">

<script>
const CSRF = document.querySelector('meta[name="csrf-token"]')?.content;
const profileData = @json($profile);

// ─── STATE ───
let st = {
    banner_image:          profileData.banner_image          ?? null,
    about:                 profileData.about                 ?? '',
    text_color:            profileData.text_color            ?? '#111827',
    social_links:          (function() {
                               let sl = profileData.social_links;
                               if (!sl) return {};
                               if (typeof sl === 'string') sl = JSON.parse(sl);
                               // Kalau array (bukan object), kembalikan object kosong
                               if (Array.isArray(sl)) return {};
                               return sl;
                           })(),
    bg_type:               profileData.bg_type               ?? 'color',
    background_color:      profileData.background_color      ?? '#ffffff',
    bg_gradient_start:     profileData.bg_gradient_start     ?? '#2563eb',
    bg_gradient_end:       profileData.bg_gradient_end       ?? '#8b5cf6',
    bg_gradient_direction: profileData.bg_gradient_direction ?? 'to bottom',
    bg_image:              profileData.bg_image              ?? null,
    btn_style:             profileData.btn_style             ?? 'fill',
    btn_shape:             profileData.btn_shape             ?? 'rounded',
    btn_color:             profileData.btn_color             ?? '#3b82f6',
    btn_text_color:        profileData.btn_text_color        ?? '#ffffff',
    font_family:           profileData.font_family           ?? 'Plus Jakarta Sans',
};

let isDirty = false;
let toastTmr;

function markDirty() {
    isDirty = true;
    updatePreview();
}

function showSaveStatus(state) {
    const el = document.getElementById('saveStatus');
    if (!el) return;
    if (state === 'typing')       el.innerHTML = '<span style="color:#f59e0b">● Belum tersimpan</span>';
    else if (state === 'saving')  el.innerHTML = '<span style="color:#6b7280">⏳ Menyimpan...</span>';
    else if (state === 'saved')   el.innerHTML = '<span style="color:#10b981">✓ Tersimpan</span>';
    else                          el.innerHTML = '';
}

// ═══════════════════════════════════════════
// WALLPAPER GALLERY
// ═══════════════════════════════════════════
const WALLPAPERS = [
    { id:'wg_aurora',    cat:'gradient', label:'Aurora',     cssValue:'linear-gradient(135deg,#667eea 0%,#764ba2 50%,#f093fb 100%)' },
    { id:'wg_peach',     cat:'gradient', label:'Peach',      cssValue:'linear-gradient(135deg,#f6d365,#fda085)' },
    { id:'wg_ocean',     cat:'gradient', label:'Ocean',      cssValue:'linear-gradient(135deg,#2193b0,#6dd5ed)' },
    { id:'wg_forest',    cat:'gradient', label:'Forest',     cssValue:'linear-gradient(135deg,#11998e,#38ef7d)' },
    { id:'wg_candy',     cat:'gradient', label:'Candy',      cssValue:'linear-gradient(135deg,#f953c6,#b91d73)' },
    { id:'wg_golden',    cat:'gradient', label:'Golden',     cssValue:'linear-gradient(135deg,#f7971e,#ffd200)' },
    { id:'wg_royal',     cat:'gradient', label:'Royal',      cssValue:'linear-gradient(135deg,#141e30,#243b55)' },
    { id:'wg_rose',      cat:'gradient', label:'Rose',       cssValue:'linear-gradient(135deg,#ff6a88,#ff99ac)' },
    { id:'wg_nordic',    cat:'gradient', label:'Nordic',     cssValue:'linear-gradient(135deg,#a8edea,#fed6e3)' },
    { id:'wg_twilight',  cat:'gradient', label:'Twilight',   cssValue:'linear-gradient(135deg,#0f0c29,#302b63,#24243e)' },
    { id:'wg_spring',    cat:'gradient', label:'Spring',     cssValue:'linear-gradient(135deg,#96fbc4,#f9f586)' },
    { id:'wg_dusk',      cat:'gradient', label:'Dusk',       cssValue:'linear-gradient(135deg,#2c3e50,#fd746c)' },
    { id:'wg_dots',      cat:'pattern',  label:'Dots',       cssValue:'radial-gradient(circle,#cbd5e1 1.5px,transparent 1.5px)', bgSize:'24px 24px', bgColor:'#f8fafc' },
    { id:'wg_grid',      cat:'pattern',  label:'Grid',       cssValue:'linear-gradient(#e2e8f0 1px,transparent 1px),linear-gradient(90deg,#e2e8f0 1px,transparent 1px)', bgSize:'24px 24px', bgColor:'#f8fafc' },
    { id:'wg_diagonal',  cat:'pattern',  label:'Lines',      cssValue:'repeating-linear-gradient(45deg,#cbd5e1,#cbd5e1 1px,transparent 1px,transparent 12px)', bgColor:'#f1f5f9' },
    { id:'wg_checker',   cat:'pattern',  label:'Check',      cssValue:'conic-gradient(#e2e8f0 90deg,#f8fafc 90deg 180deg,#e2e8f0 180deg 270deg,#f8fafc 270deg)', bgSize:'20px 20px', bgColor:'#f8fafc' },
    { id:'wg_dotsdark',  cat:'pattern',  label:'Dots Dark',  cssValue:'radial-gradient(circle,#475569 1.5px,transparent 1.5px)', bgSize:'24px 24px', bgColor:'#1e293b' },
    { id:'wg_griddark',  cat:'pattern',  label:'Grid Dark',  cssValue:'linear-gradient(#334155 1px,transparent 1px),linear-gradient(90deg,#334155 1px,transparent 1px)', bgSize:'24px 24px', bgColor:'#0f172a' },
    { id:'wg_wave',      cat:'pattern',  label:'Wave',       cssValue:'repeating-radial-gradient(circle at 0 0,transparent 0,#e0f2fe 8px),repeating-linear-gradient(#bae6fd55,#bae6fd)' },
    { id:'wg_mesh',      cat:'pattern',  label:'Mesh',       cssValue:'radial-gradient(at 40% 20%,#fde68a 0,transparent 50%),radial-gradient(at 80% 0,#c7d2fe 0,transparent 50%),radial-gradient(at 0 50%,#fecdd3 0,transparent 50%)', bgColor:'#fff7ed' },
    { id:'wg_white',     cat:'minimal',  label:'White',      cssValue:'#ffffff',   solid:true },
    { id:'wg_cream',     cat:'minimal',  label:'Cream',      cssValue:'#fef9f0',   solid:true },
    { id:'wg_blush',     cat:'minimal',  label:'Blush',      cssValue:'#fdf2f8',   solid:true },
    { id:'wg_mint',      cat:'minimal',  label:'Mint',       cssValue:'#f0fdf4',   solid:true },
    { id:'wg_sky',       cat:'minimal',  label:'Sky',        cssValue:'#f0f9ff',   solid:true },
    { id:'wg_gray',      cat:'minimal',  label:'Gray',       cssValue:'#f1f5f9',   solid:true },
    { id:'wg_warmgray',  cat:'minimal',  label:'Warm',       cssValue:'#fafaf9',   solid:true },
    { id:'wg_sand',      cat:'minimal',  label:'Sand',       cssValue:'#fef3c7',   solid:true },
    { id:'wg_obsidian',  cat:'dark',     label:'Obsidian',   cssValue:'#0a0a0a',   solid:true },
    { id:'wg_night',     cat:'dark',     label:'Night',      cssValue:'#0f172a',   solid:true },
    { id:'wg_smoke',     cat:'dark',     label:'Smoke',      cssValue:'#1c1c1e',   solid:true },
    { id:'wg_deep',      cat:'dark',     label:'Deep',       cssValue:'#111827',   solid:true },
    { id:'wg_void',      cat:'dark',     label:'Void',       cssValue:'linear-gradient(135deg,#0f0c29,#302b63,#24243e)' },
    { id:'wg_abyss',     cat:'dark',     label:'Abyss',      cssValue:'linear-gradient(135deg,#000000,#434343)' },
    { id:'wg_cosmos',    cat:'dark',     label:'Cosmos',     cssValue:'linear-gradient(135deg,#0d0d0d,#1a1a2e,#16213e)' },
    { id:'wg_eclipse',   cat:'dark',     label:'Eclipse',    cssValue:'linear-gradient(135deg,#1a1a2e,#16213e,#0f3460)' },
];

let activeWgCat = 'all';
let activeWgId  = @json(
    isset($profile->bg_image) && str_starts_with($profile->bg_image ?? '', 'wg_') ? $profile->bg_image : null
);

function wgThumbStyle(wg) {
    if (wg.solid) return `background:${wg.cssValue}`;
    let s = `background:${wg.cssValue}`;
    if (wg.bgColor) s += `;background-color:${wg.bgColor}`;
    if (wg.bgSize)  s += `;background-size:${wg.bgSize}`;
    return s;
}
function renderWgGrid(cat) {
    const grid = document.getElementById('wgGrid');
    const list = cat === 'all' ? WALLPAPERS : WALLPAPERS.filter(w => w.cat === cat);
    grid.innerHTML = list.map(wg => `
        <div class="wg-item ${activeWgId === wg.id ? 'active' : ''}"
             id="wgi-${wg.id}" title="${wg.label}"
             onclick="selectWallpaper('${wg.id}')">
            <div class="wg-thumb" style="${wgThumbStyle(wg)}"></div>
            <div class="wg-label">${wg.label}</div>
        </div>
    `).join('');
}
function filterWg(btn, cat) {
    document.querySelectorAll('.wg-cat').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    activeWgCat = cat;
    renderWgGrid(cat);
}
function selectWallpaper(wgId) {
    const wg = WALLPAPERS.find(w => w.id === wgId);
    if (!wg) return;
    document.querySelectorAll('.wg-item').forEach(el => el.classList.remove('active'));
    document.getElementById(`wgi-${wgId}`)?.classList.add('active');
    activeWgId = wgId;
    document.getElementById('bgImgPreview').style.display = 'none';
    st.bg_image = wgId;
    st.bg_type  = 'image';
    markDirty();
}
renderWgGrid('all');

// ═══════════════════════════════════════════
// PROFILE CARD
// ═══════════════════════════════════════════
async function handleBannerUpload(input) {
    const file = input.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = e => {
        const img  = document.getElementById('pcBannerImg');
        const wrap = document.getElementById('pcBannerWrap');
        img.src = e.target.result;
        img.style.display = 'block';
        wrap.classList.add('has-img');
    };
    reader.readAsDataURL(file);
    const fd = new FormData();
    fd.append('image', file);
    fd.append('_token', CSRF);
    try {
        const res  = await fetch('{{ route("dashboard.appearance.uploadBanner") }}', { method:'POST', body:fd });
        const data = await res.json();
        if (data.success) {
            st.banner_image = data.path;
            markDirty();
            showToast('Banner berhasil diupload! 🖼️', 'success');
        } else {
            showToast(data.message ?? 'Gagal upload banner.', 'error');
        }
    } catch(e) { showToast('Gagal upload banner.', 'error'); }
}

function onAboutIn(v) { st.about = v; markDirty(); }

function onTxtColorIn(v) {
    st.text_color = v;
    document.getElementById('pcTxtColorText').value = v;
    document.getElementById('pcTxtColorDot').style.background = v;
    document.querySelectorAll('.pc-color-preset').forEach(p => p.classList.toggle('active', p.title === v));
    markDirty();
}
function onTxtColorTextIn(v) {
    if (/^#[0-9a-fA-F]{6}$/.test(v)) {
        st.text_color = v;
        document.getElementById('pcTxtColorPicker').value = v;
        document.getElementById('pcTxtColorDot').style.background = v;
        markDirty();
    }
}
function selectTxtColorPreset(c, el) {
    st.text_color = c;
    document.getElementById('pcTxtColorPicker').value = c;
    document.getElementById('pcTxtColorText').value   = c;
    document.getElementById('pcTxtColorDot').style.background = c;
    document.querySelectorAll('.pc-color-preset').forEach(p => p.classList.remove('active'));
    el.classList.add('active');
    markDirty();
}

// ═══════════════════════════════════════════
// SOCIAL LINKS
// ═══════════════════════════════════════════
const SL_LIST = [
    { id:'telegram',   label:'Telegram',   placeholder:'username',        prefix:'https://t.me/',
      icon:`<svg viewBox="0 0 24 24" fill="currentColor" width="14" height="14"><path d="M12 2C6.477 2 2 6.477 2 12s4.477 10 10 10 10-4.477 10-10S17.523 2 12 2zm4.93 6.686l-1.683 7.927c-.127.567-.46.707-.931.44l-2.57-1.894-1.24 1.193c-.137.137-.252.252-.517.252l.185-2.621 4.768-4.307c.207-.185-.045-.287-.322-.102L7.89 14.214l-2.522-.788c-.548-.171-.558-.548.115-.812l9.867-3.805c.456-.166.856.112.58.877z"/></svg>` },
    { id:'website',    label:'Website',    placeholder:'yourwebsite.com', prefix:'https://',
      icon:`<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><circle cx="12" cy="12" r="10"/><path d="M2 12h20M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>` },
    { id:'email',      label:'Email',      placeholder:'you@email.com',   prefix:'mailto:',
      icon:`<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>` },
    { id:'discord',    label:'Discord',    placeholder:'invite code',     prefix:'https://discord.gg/',
      icon:`<svg viewBox="0 0 24 24" fill="currentColor" width="14" height="14"><path d="M20.317 4.37a19.791 19.791 0 0 0-4.885-1.515.074.074 0 0 0-.079.037c-.21.375-.444.864-.608 1.25a18.27 18.27 0 0 0-5.487 0 12.64 12.64 0 0 0-.617-1.25.077.077 0 0 0-.079-.037A19.736 19.736 0 0 0 3.677 4.37a.07.07 0 0 0-.032.027C.533 9.046-.32 13.58.099 18.057a.082.082 0 0 0 .031.057 19.9 19.9 0 0 0 5.993 3.03.078.078 0 0 0 .084-.028c.462-.63.874-1.295 1.226-1.994a.076.076 0 0 0-.041-.106 13.107 13.107 0 0 1-1.872-.892.077.077 0 0 1-.008-.128 10.2 10.2 0 0 0 .372-.292.074.074 0 0 1 .077-.01c3.928 1.793 8.18 1.793 12.062 0a.074.074 0 0 1 .078.01c.12.098.246.198.373.292a.077.077 0 0 1-.006.127 12.299 12.299 0 0 1-1.873.892.077.077 0 0 0-.041.107c.36.698.772 1.362 1.225 1.993a.076.076 0 0 0 .084.028 19.839 19.839 0 0 0 6.002-3.03.077.077 0 0 0 .032-.054c.5-5.177-.838-9.674-3.549-13.66a.061.061 0 0 0-.031-.03z"/></svg>` },
    { id:'tiktok',     label:'TikTok',     placeholder:'username',        prefix:'https://tiktok.com/@',
      icon:`<svg viewBox="0 0 24 24" fill="currentColor" width="14" height="14"><path d="M19.59 6.69a4.83 4.83 0 0 1-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 0 1-2.88 2.5 2.89 2.89 0 0 1-2.89-2.89 2.89 2.89 0 0 1 2.89-2.89c.28 0 .54.04.79.1V9.01a6.33 6.33 0 0 0-.79-.05 6.34 6.34 0 0 0-6.34 6.34 6.34 6.34 0 0 0 6.34 6.34 6.34 6.34 0 0 0 6.33-6.34V8.69a8.27 8.27 0 0 0 4.84 1.55V6.79a4.85 4.85 0 0 1-1.07-.1z"/></svg>` },
    { id:'instagram',  label:'Instagram',  placeholder:'username',        prefix:'https://instagram.com/',
      icon:`<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/></svg>` },
    { id:'youtube',    label:'Youtube',    placeholder:'nama channel',    prefix:'https://youtube.com/@',
      icon:`<svg viewBox="0 0 24 24" fill="currentColor" width="14" height="14"><path d="M23.495 6.205a3.007 3.007 0 0 0-2.088-2.088c-1.87-.501-9.396-.501-9.396-.501s-7.507-.01-9.396.501A3.007 3.007 0 0 0 .527 6.205a31.247 31.247 0 0 0-.522 5.805 31.247 31.247 0 0 0 .522 5.783 3.007 3.007 0 0 0 2.088 2.088c1.868.502 9.396.502 9.396.502s7.506 0 9.396-.502a3.007 3.007 0 0 0 2.088-2.088 31.247 31.247 0 0 0 .5-5.783 31.247 31.247 0 0 0-.5-5.805zM9.609 15.601V8.408l6.264 3.602z"/></svg>` },
    { id:'twitch',     label:'Twitch',     placeholder:'username',        prefix:'https://twitch.tv/',
      icon:`<svg viewBox="0 0 24 24" fill="currentColor" width="14" height="14"><path d="M11.571 4.714h1.715v5.143H11.57zm4.715 0H18v5.143h-1.714zM6 0L1.714 4.286v15.428h5.143V24l4.286-4.286h3.428L22.286 12V0zm14.571 11.143l-3.428 3.428h-3.429l-3 3v-3H6.857V1.714h13.714z"/></svg>` },
    { id:'linkedin',   label:'LinkedIn',   placeholder:'username',        prefix:'https://linkedin.com/in/',
      icon:`<svg viewBox="0 0 24 24" fill="currentColor" width="14" height="14"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433a2.062 2.062 0 0 1-2.063-2.065 2.064 2.064 0 1 1 2.063 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>` },
    { id:'x',          label:'X',          placeholder:'username',        prefix:'https://x.com/',
      icon:`<svg viewBox="0 0 24 24" fill="currentColor" width="14" height="14"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-4.714-6.231-5.401 6.231H2.742l7.732-8.858L1.254 2.25H8.08l4.253 5.622zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>` },
    { id:'facebook',   label:'Facebook',   placeholder:'username',        prefix:'https://facebook.com/',
      icon:`<svg viewBox="0 0 24 24" fill="currentColor" width="14" height="14"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>` },
    { id:'behance',    label:'Behance',    placeholder:'username',        prefix:'https://behance.net/',
      icon:`<svg viewBox="0 0 24 24" fill="currentColor" width="14" height="14"><path d="M22 7h-7V5h7v2zm1.726 10c-.442 1.297-2.029 3-5.101 3-3.074 0-5.564-1.729-5.564-5.675 0-3.91 2.325-5.92 5.466-5.92 3.082 0 4.964 1.782 5.375 4.426.078.506.109 1.188.095 2.14H15.97c.13 3.211 3.483 3.312 4.588 2.029H23.726zm-7.726-3h3.457c-.073-1.580-1.002-2.18-1.712-2.18-.747 0-1.633.572-1.745 2.18zM7.17 9.025c.395 0 2.353.105 2.353 1.734 0 .97-.771 1.463-1.55 1.546v.047c.99.078 1.968.609 1.968 1.873 0 2.006-2.006 2.072-2.637 2.072H1V9.025h6.17zm-3.07 5.52h2.167c.588 0 1.14-.228 1.14-.91 0-.773-.693-.9-1.244-.9H4.1v1.81zm0-3.31h1.937c.5 0 1.057-.162 1.057-.836 0-.73-.625-.836-1.14-.836H4.1v1.672z"/></svg>` },
    { id:'dribbble',   label:'Dribbble',   placeholder:'username',        prefix:'https://dribbble.com/',
      icon:`<svg viewBox="0 0 24 24" fill="currentColor" width="14" height="14"><path d="M12 24C5.385 24 0 18.615 0 12S5.385 0 12 0s12 5.385 12 12-5.385 12-12 12zm10.12-10.358c-.35-.11-3.17-.953-6.384-.438 1.34 3.684 1.887 6.684 1.992 7.308 2.3-1.555 3.936-4.02 4.395-6.87zm-6.115 7.808c-.153-.9-.75-4.032-2.19-7.77l-.066.02c-5.79 2.015-7.86 6.017-8.04 6.39 1.73 1.35 3.92 2.166 6.29 2.166 1.42 0 2.77-.29 4.01-.806zm-9.86-3.28c.24-.38 3.28-5.21 8.536-6.89.016-.064.033-.128.05-.192-1.52-.547-4.73-1.07-8.52-1.07-.284 0-.568.004-.85.012-.04.166-.065.334-.065.504 0 3.126 1.19 5.99 3.14 8.13zm7.715-10.27c-.47-1.353-1.31-3.373-2.38-5.13-1.34.09-2.63.41-3.79.94 1.46 1.764 2.546 3.764 2.77 4.43.67-.12 1.39-.2 2.16-.2.42 0 .83.02 1.24.06zm.36-.09c.46.03.92.09 1.37.17.01-.04.01-.09.01-.13 0-1.72-.468-3.335-1.286-4.72-.29.75-.784 2.52-1.094 4.68zm3.327.55c-.34-.066-.69-.12-1.043-.157.16-1.766.566-3.457 1.05-4.656.66.39 1.25.87 1.78 1.404-.676.952-1.452 2.307-1.787 3.41z"/></svg>` },
    { id:'whatsapp',   label:'WhatsApp',   placeholder:'628xxx (no. HP)',  prefix:'https://wa.me/',
      icon:`<svg viewBox="0 0 24 24" fill="currentColor" width="14" height="14"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413z"/></svg>` },
    { id:'spotify',    label:'Spotify',    placeholder:'username',        prefix:'https://open.spotify.com/user/',
      icon:`<svg viewBox="0 0 24 24" fill="currentColor" width="14" height="14"><path d="M12 0C5.4 0 0 5.4 0 12s5.4 12 12 12 12-5.4 12-12S18.66 0 12 0zm5.521 17.34c-.24.359-.66.48-1.021.24-2.82-1.74-6.36-2.101-10.561-1.141-.418.122-.779-.179-.899-.539-.12-.421.18-.78.54-.9 4.56-1.021 8.52-.6 11.64 1.32.42.18.479.659.301 1.02zm1.44-3.3c-.301.42-.841.6-1.262.3-3.239-1.98-8.159-2.58-11.939-1.38-.479.12-1.02-.12-1.14-.6-.12-.48.12-1.021.6-1.141C9.6 9.9 15 10.561 18.72 12.84c.361.181.54.78.241 1.2zm.12-3.36C15.24 8.4 8.82 8.16 5.16 9.301c-.6.179-1.2-.181-1.38-.721-.18-.601.18-1.2.72-1.381 4.26-1.26 11.28-1.02 15.721 1.621.539.3.719 1.02.419 1.56-.299.421-1.02.599-1.559.3z"/></svg>` },
    { id:'threads',    label:'Threads',    placeholder:'username',        prefix:'https://threads.net/@',
      icon:`<svg viewBox="0 0 24 24" fill="currentColor" width="14" height="14"><path d="M12.186 24h-.007c-3.581-.024-6.334-1.205-8.184-3.509C2.35 18.44 1.5 15.586 1.472 12.01v-.017c.03-3.579.879-6.43 2.525-8.482C5.845 1.205 8.6.024 12.18 0h.014c2.746.02 5.043.725 6.826 2.098 1.677 1.29 2.858 3.13 3.509 5.467l-2.04.569c-1.104-3.96-3.898-5.984-8.304-6.015-2.91.022-5.11.936-6.54 2.717C4.307 6.504 3.616 8.914 3.589 12c.027 3.086.718 5.496 2.057 7.164 1.43 1.783 3.631 2.698 6.54 2.717 2.623-.02 4.358-.631 5.689-2.046 1.367-1.455 2.041-3.534 2.075-6.154H12.79v-2.113h9.23c.16 3.404-.499 6.094-1.97 8.009-1.855 2.364-4.797 3.6-8.868 3.623z"/></svg>` },
];
function renderSlChips() {
    document.getElementById('slChips').innerHTML = SL_LIST.map(s => `
        <div class="sl-chip ${st.social_links[s.id] !== undefined ? 'active' : ''}"
             id="slchip-${s.id}" onclick="toggleSlChip('${s.id}')">
            ${s.icon} ${s.label}
        </div>
    `).join('');
}
function getDisplayValue(id, fullUrl) {
    const s = SL_LIST.find(x => x.id === id);
    if (!s || !fullUrl) return fullUrl || '';
    // Kalau sudah pakai prefix, strip prefix untuk tampilan
    if (s.prefix && fullUrl.startsWith(s.prefix)) return fullUrl.slice(s.prefix.length);
    return fullUrl;
}
function buildFullUrl(id, input) {
    const s = SL_LIST.find(x => x.id === id);
    if (!s || !input) return input;
    // Kalau user sudah ketik URL lengkap (http/https/mailto), pakai apa adanya
    if (/^(https?:\/\/|mailto:)/.test(input)) return input;
    return s.prefix + input;
}
function renderSlInputs() {
    const el = document.getElementById('slInputs');
    const active = SL_LIST.filter(s => st.social_links[s.id] !== undefined);
    if (!active.length) {
        el.innerHTML = `<p style="font-size:12.5px;color:#9ca3af;text-align:center;padding:10px 0;">Pilih platform di atas untuk menambahkan link</p>`;
        return;
    }
    el.innerHTML = active.map(s => {
        const displayVal = getDisplayValue(s.id, st.social_links[s.id] ?? '');
        return `
        <div class="sl-input-row" id="slrow-${s.id}">
            <span class="sl-input-label" style="display:flex;align-items:center;gap:5px;">${s.icon} ${s.label}</span>
            <input type="text" class="sl-input-field"
                   placeholder="${s.placeholder}"
                   value="${escHtml(displayVal)}"
                   oninput="onSlInput('${s.id}', this.value)">
            <button class="sl-input-remove" onclick="toggleSlChip('${s.id}')" title="Hapus">
                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
    `}).join('');
}
function toggleSlChip(id) {
    if (st.social_links[id] !== undefined) delete st.social_links[id];
    else st.social_links[id] = '';
    renderSlChips(); renderSlInputs(); markDirty();
}
function onSlInput(id, val) {
    // Simpan URL lengkap ke state, tapi tampilkan hanya username
    st.social_links[id] = buildFullUrl(id, val);
    markDirty();
}
function escHtml(s) {
    return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}
renderSlChips(); renderSlInputs();

// ═══════════════════════════════════════════
// BACKGROUND
// ═══════════════════════════════════════════
function switchBgTab(btn, type) {
    document.querySelectorAll('.bg-tab').forEach(t => t.classList.remove('active'));
    btn.classList.add('active');
    document.querySelectorAll('.bg-panel').forEach(p => p.classList.remove('active'));
    document.getElementById(`panel-${type}`)?.classList.add('active');
    st.bg_type = type;
    markDirty();
}
function onBgColorIn(v) {
    st.background_color = v;
    document.getElementById('bgColorText').value = v;
    document.getElementById('bgColorSwatch').style.background = v;
    document.querySelectorAll('.preset-dot').forEach(p => p.classList.remove('active'));
    markDirty();
}
function onBgColorTextIn(v) {
    if (/^#[0-9a-fA-F]{6}$/.test(v)) {
        st.background_color = v;
        document.getElementById('bgColorPicker').value = v;
        document.getElementById('bgColorSwatch').style.background = v;
        markDirty();
    }
}
function selectPreset(c, el) {
    st.background_color = c;
    document.getElementById('bgColorPicker').value = c;
    document.getElementById('bgColorText').value   = c;
    document.getElementById('bgColorSwatch').style.background = c;
    document.querySelectorAll('.preset-dot').forEach(p => p.classList.remove('active'));
    el.classList.add('active');
    markDirty();
}
function onGradIn() {
    const c1 = document.getElementById('g1Picker').value;
    const c2 = document.getElementById('g2Picker').value;
    document.getElementById('g1Swatch').style.background = c1;
    document.getElementById('g2Swatch').style.background = c2;
    document.getElementById('g1Text').value = c1;
    document.getElementById('g2Text').value = c2;
    st.bg_gradient_start = c1; st.bg_gradient_end = c2;
    markDirty();
}
function syncTxt(tId, pId, sId) {
    const v = document.getElementById(tId).value;
    if (/^#[0-9a-fA-F]{6}$/.test(v)) {
        document.getElementById(pId).value = v;
        document.getElementById(sId).style.background = v;
    }
}
function selectDir(btn, dir) {
    document.querySelectorAll('.dir-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    st.bg_gradient_direction = dir;
    markDirty();
}
async function handleBgImg(input) {
    const file = input.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = e => {
        const img = document.getElementById('bgImgPreview');
        img.src = e.target.result; img.style.display = 'block';
    };
    reader.readAsDataURL(file);
    document.querySelectorAll('.wg-item').forEach(el => el.classList.remove('active'));
    activeWgId = null;
    const fd = new FormData();
    fd.append('image', file); fd.append('_token', CSRF);
    try {
        const res  = await fetch('{{ route("dashboard.appearance.uploadBg") }}', { method:'POST', body:fd });
        const data = await res.json();
        if (data.success) {
            st.bg_image = data.path; st.bg_type = 'image';
            markDirty();
            showToast('Gambar berhasil diupload! 🖼️', 'success');
        } else {
            showToast(data.message ?? 'Gagal upload.', 'error');
        }
    } catch(e) { showToast('Gagal upload gambar.', 'error'); }
}

// ═══════════════════════════════════════════
// BUTTONS
// ═══════════════════════════════════════════
function selectBtnStyle(el, style) {
    document.querySelectorAll('.btn-style-item').forEach(i => i.classList.remove('active'));
    el.classList.add('active'); st.btn_style = style; markDirty();
}
function selectBtnShape(el, shape) {
    document.querySelectorAll('.btn-shape-item').forEach(i => i.classList.remove('active'));
    el.classList.add('active'); st.btn_shape = shape; markDirty();
}
function onBtnColorIn(v, which) {
    if (which === 'btn') { st.btn_color = v; document.getElementById('btnColorText').value = v; document.getElementById('btnColorDot').style.background = v; }
    else { st.btn_text_color = v; document.getElementById('btnTxtColorText').value = v; document.getElementById('btnTxtColorDot').style.background = v; }
    markDirty();
}
function onBtnColorTextIn(v, which) {
    if (!/^#[0-9a-fA-F]{6}$/.test(v)) return;
    if (which === 'btn') { st.btn_color = v; document.getElementById('btnColorPicker').value = v; document.getElementById('btnColorDot').style.background = v; }
    else { st.btn_text_color = v; document.getElementById('btnTxtColorPicker').value = v; document.getElementById('btnTxtColorDot').style.background = v; }
    markDirty();
}

// ═══════════════════════════════════════════
// FONT
// ═══════════════════════════════════════════
function selectFont(el, fontId) {
    document.querySelectorAll('.font-item').forEach(i => i.classList.remove('active'));
    el.classList.add('active'); st.font_family = fontId; markDirty();
}

// ═══════════════════════════════════════════
// BUILD BTN CSS
// ═══════════════════════════════════════════
function buildBtnCss() {
    switch(st.btn_style) {
        case 'fill':        return `background:${st.btn_color};color:${st.btn_text_color};border:2px solid ${st.btn_color};`;
        case 'outline':     return `background:transparent;color:${st.btn_color};border:2px solid ${st.btn_color};`;
        case 'hard_shadow': return `background:${st.btn_color};color:${st.btn_text_color};border:2px solid #111;box-shadow:3px 3px 0 #111;`;
        case 'soft_shadow': return `background:${st.btn_color};color:${st.btn_text_color};border:none;box-shadow:0 4px 16px rgba(0,0,0,0.15);`;
        case 'ghost':       return `background:rgba(255,255,255,0.15);color:${st.btn_text_color};border:1.5px solid rgba(255,255,255,0.3);backdrop-filter:blur(8px);`;
        case 'minimal':     return `background:transparent;color:${st.btn_color};border:none;border-bottom:2px solid ${st.btn_color};border-radius:0!important;`;
        default:            return `background:${st.btn_color};color:${st.btn_text_color};border:2px solid ${st.btn_color};`;
    }
}

// ═══════════════════════════════════════════
// LIVE PREVIEW
// ═══════════════════════════════════════════
function updatePreview() {
    const frame = document.getElementById('previewFrame');
    if (!frame?.contentWindow) return;
    let bgCss, bgColor = null, bgSize = null;
    if (st.bg_type === 'image' && st.bg_image && st.bg_image.startsWith('wg_')) {
        const wg = WALLPAPERS.find(w => w.id === st.bg_image);
        if (wg) { bgCss = wg.cssValue; bgColor = wg.bgColor ?? null; bgSize = wg.bgSize ?? null; }
        else { bgCss = st.background_color; }
    } else if (st.bg_type === 'gradient') {
        bgCss = `linear-gradient(${st.bg_gradient_direction}, ${st.bg_gradient_start}, ${st.bg_gradient_end})`;
    } else if (st.bg_type === 'image' && st.bg_image) {
        bgCss = `url('/storage/${st.bg_image}') center/cover no-repeat`;
    } else {
        bgCss = st.background_color;
    }
    const shapeMap = { pill:'50px', rounded:'12px', square:'4px' };
    frame.contentWindow.postMessage({
        type: 'payou_appearance_update',
        payload: {
            bgCss, bgColor, bgSize,
            fontFamily:     st.font_family,
            textColor:      st.text_color,
            btnCss:         buildBtnCss(),
            btnRadius:      shapeMap[st.btn_shape] ?? '12px',
            btn_style:      st.btn_style,
            btn_shape:      st.btn_shape,
            btn_color:      st.btn_color,
            btn_text_color: st.btn_text_color,
        }
    }, '*');
}
function reloadPreview() {
    const f = document.getElementById('previewFrame');
    if (f) f.src = f.src;
}

// ═══════════════════════════════════════════
// SAVE & RESET
// ═══════════════════════════════════════════
async function saveAppearance() {
    const btn = document.getElementById('btnSave');
    if (btn) { btn.disabled = true; btn.classList.add('loading'); }
    try {
        const res = await fetch('{{ route("dashboard.appearance.save") }}', {
            method:  'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CSRF,
                'Accept':       'application/json',
            },
            body: JSON.stringify(st),
        });
        const rawText = await res.text();
        let data;
        try {
            // Strip Laravel Debugbar / HTML yang mungkin inject sebelum/sesudah JSON
            const start = rawText.indexOf('{');
            const end   = rawText.lastIndexOf('}');
            const clean = (start !== -1 && end !== -1) ? rawText.slice(start, end + 1) : rawText;
            data = JSON.parse(clean);
        } catch { throw new Error('Response bukan JSON: ' + rawText.substring(0, 200)); }

        if (res.ok && data.success) {
            isDirty = false;
            showToast('Tampilan berhasil disimpan! ✓', 'success');
            setTimeout(reloadPreview, 400);
            // Kasih tau tab profil publik supaya reload
            localStorage.setItem('payou_saved', Date.now());
        } else {
            const errMsg = data.message
                ?? (data.errors ? Object.values(data.errors).flat().join(', ') : null)
                ?? `Error ${res.status}`;
            showToast(errMsg, 'error');
        }
    } catch(e) {
        showToast('Gagal menyimpan: ' + e.message, 'error');
        console.error('Save exception:', e);
    } finally {
        if (btn) { btn.disabled = false; btn.classList.remove('loading'); }
    }
}

async function resetAppearance() {
    if (!confirm('Reset tampilan ke default?')) return;
    try {
        const res  = await fetch('{{ route("dashboard.appearance.reset") }}', {
            method:  'POST',
            headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' }
        });
        const data = await res.json();
        if (data.success) {
            showToast('Tampilan direset ke default.', 'success');
            setTimeout(() => location.reload(), 800);
        } else {
            showToast(data.message ?? 'Gagal reset.', 'error');
        }
    } catch(e) { showToast('Gagal reset.', 'error'); }
}

// ─── Toast ───
function showToast(msg, type = 'default') {
    const t = document.getElementById('apToast');
    t.textContent = msg; t.className = `ap-toast ${type} show`;
    clearTimeout(toastTmr);
    toastTmr = setTimeout(() => t.classList.remove('show'), 3500);
}

// ─── Init ───
document.getElementById('previewFrame').addEventListener('load', () => setTimeout(updatePreview, 300));
// auto-save aktif — tidak perlu beforeunload warning
</script>
@endpush