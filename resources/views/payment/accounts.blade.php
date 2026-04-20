@extends('layouts.dashboard')

@section('title', 'Rekening Pembayaran | Mobay.id')

@section('content')
<style>
/* ================================================================
   SCOPED — semua class diawali "pa-" agar tidak bentrok layout
================================================================ */
.pa-wrap {
    max-width: 100%;
    width: 100%;
    font-family: 'Plus Jakarta Sans', sans-serif;
    padding-right: 0;
}

/* ── Header ─────────────────────────────────────────────── */
.pa-header {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 22px;
}

.pa-header-icon {
    width: 42px; height: 42px;
    background: linear-gradient(135deg, #2356e8, #1a44c4);
    border-radius: 11px;
    display: flex; align-items: center; justify-content: center;
    color: #fff;
    font-size: 17px;
    box-shadow: 0 4px 14px rgba(35,86,232,.25);
    flex-shrink: 0;
}

.pa-header-title { margin: 0; font-size: 20px; font-weight: 700; color: #111827; letter-spacing: -.3px; }
.pa-header-sub   { margin: 2px 0 0; font-size: 13px; color: #6b7c93; }

/* ── Security Badge ─────────────────────────────────────── */
.pa-sec-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: #f0fdf4;
    border: 1px solid #bbf7d0;
    border-radius: 99px;
    padding: 6px 14px;
    font-size: 12px;
    font-weight: 600;
    color: #16a34a;
    margin-bottom: 20px;
}

.pa-sec-badge .dot {
    width: 7px; height: 7px;
    background: #22c55e;
    border-radius: 50%;
    animation: paDot 2s infinite;
}

@keyframes paDot {
    0%,100% { transform: scale(1); opacity: 1; }
    50%      { transform: scale(.7); opacity: .5; }
}

/* ── Stats Strip ────────────────────────────────────────── */
.pa-stats {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    background: #ffffff;
    border: 1px solid #e8edf5;
    border-radius: 12px;
    overflow: hidden;
    margin-bottom: 22px;
    box-shadow: 0 1px 4px rgba(0,0,0,.04);
}

.pa-stat {
    padding: 14px 16px;
    text-align: center;
    border-right: 1px solid #f1f5f9;
}
.pa-stat:last-child { border-right: none; }

.pa-stat-val {
    font-size: 22px;
    font-weight: 800;
    color: #2356e8;
    font-variant-numeric: tabular-nums;
    line-height: 1;
}

.pa-stat-label {
    font-size: 11px;
    color: #9aaabb;
    margin-top: 4px;
    font-weight: 500;
}

/* ── Section Label ──────────────────────────────────────── */
.pa-section-label {
    font-size: 10.5px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .1em;
    color: #9aaabb;
    margin-bottom: 10px;
}

/* ── Account Cards ──────────────────────────────────────── */
.pa-accounts { display: flex; flex-direction: column; gap: 8px; margin-bottom: 22px; }

.pa-card {
    background: #ffffff;
    border: 1.5px solid #e8edf5;
    border-radius: 12px;
    padding: 14px 16px;
    display: flex;
    align-items: center;
    gap: 13px;
    transition: border-color .18s, box-shadow .18s, transform .15s;
    box-shadow: 0 1px 3px rgba(0,0,0,.04);
    position: relative;
}

.pa-card:hover {
    border-color: #c7d8ff;
    box-shadow: 0 4px 14px rgba(35,86,232,.08);
    transform: translateY(-1px);
}

.pa-card.is-default {
    border-color: #2356e8;
    background: #f5f8ff;
    box-shadow: 0 0 0 3px rgba(35,86,232,.07), 0 1px 3px rgba(0,0,0,.04);
}

/* Bank badge */
.pa-bank {
    width: 44px; height: 44px;
    border-radius: 9px;
    display: flex; align-items: center; justify-content: center;
    font-size: 10px; font-weight: 800;
    font-family: 'DM Mono', monospace;
    letter-spacing: .4px;
    flex-shrink: 0;
}

.pa-bank.BCA     { background: #e3eeff; color: #003d82; }
.pa-bank.BRI     { background: #e3e8f5; color: #003087; }
.pa-bank.BNI     { background: #fff0e8; color: #f26722; }
.pa-bank.MANDIRI { background: #e3eeff; color: #00529b; }
.pa-bank.BSI     { background: #e8f5e9; color: #1b5e20; }
.pa-bank.CIMB    { background: #fce4ec; color: #b71c1c; }
.pa-bank.GOPAY   { background: #e8f5e9; color: #00880a; }
.pa-bank.OVO     { background: #f3e5f5; color: #6a1b9a; }
.pa-bank.DANA    { background: #e3f2fd; color: #1565c0; }
.pa-bank.DEFAULT { background: #f1f5f9; color: #64748b; }

.pa-card-info { flex: 1; min-width: 0; }

.pa-card-name {
    font-size: 13.5px; font-weight: 700; color: #111827;
    margin-bottom: 3px;
    white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
}

.pa-card-num {
    font-size: 12px;
    color: #9aaabb;
    letter-spacing: .3px;
}

/* Tags */
.pa-tag {
    display: inline-flex; align-items: center; gap: 4px;
    font-size: 10.5px; font-weight: 700;
    padding: 3px 9px; border-radius: 99px; flex-shrink: 0;
}

.pa-tag-default { background: #e8f0fe; color: #2356e8; border: 1px solid #c7d8ff; }
.pa-tag-label   { background: #f1f5f9; color: #64748b; border: 1px solid #e2e8f0; }

/* Card action buttons */
.pa-card-actions { display: flex; gap: 5px; flex-shrink: 0; }

.pa-btn-icon {
    width: 30px; height: 30px;
    border-radius: 7px;
    border: 1px solid #e8edf5;
    background: #ffffff;
    color: #9aaabb;
    cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    font-size: 12px;
    transition: all .18s;
}

.pa-btn-icon:hover             { background: #f1f5f9; color: #475569; border-color: #d1d9e6; }
.pa-btn-icon.set-default:hover { background: #e8f0fe; color: #2356e8; border-color: #c7d8ff; }
.pa-btn-icon.danger:hover      { background: #fff1f2; color: #e53e3e; border-color: #fecdd3; }

/* Default checkmark */
.pa-check {
    width: 24px; height: 24px;
    background: #2356e8; border-radius: 50%;
    display: none; align-items: center; justify-content: center;
    color: #fff; font-size: 9px; flex-shrink: 0;
}

.pa-card.is-default .pa-check { display: flex; }

/* ── Empty State ────────────────────────────────────────── */
.pa-empty {
    text-align: center;
    padding: 36px 20px;
    background: #ffffff;
    border: 1.5px dashed #dde5f0;
    border-radius: 12px;
}

.pa-empty-icon {
    width: 52px; height: 52px;
    background: #e8f0fe; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    margin: 0 auto 12px;
    color: #2356e8; font-size: 20px;
}

.pa-empty-title { font-size: 14.5px; font-weight: 700; color: #334155; margin-bottom: 4px; }
.pa-empty-sub   { font-size: 12.5px; color: #9aaabb; }

/* ── Add Form Accordion ─────────────────────────────────── */
.pa-accordion {
    background: #ffffff;
    border: 1.5px solid #e8edf5;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 1px 3px rgba(0,0,0,.04);
}

.pa-accordion-trigger {
    padding: 15px 18px;
    display: flex; align-items: center; justify-content: space-between;
    cursor: pointer; user-select: none;
    transition: background .15s;
    border-bottom: 1.5px solid transparent;
}

.pa-accordion.is-open .pa-accordion-trigger { border-bottom-color: #f1f5f9; }
.pa-accordion-trigger:hover { background: #f8fafd; }

.pa-trigger-left { display: flex; align-items: center; gap: 11px; }

.pa-trigger-icon {
    width: 32px; height: 32px;
    background: #e8f0fe; border-radius: 8px;
    display: flex; align-items: center; justify-content: center;
    color: #2356e8; font-size: 13px;
    border: 1px solid #c7d8ff;
}

.pa-trigger-title { font-size: 13.5px; font-weight: 700; color: #334155; }
.pa-trigger-sub   { font-size: 11.5px; color: #9aaabb; margin-top: 1px; }

.pa-chevron {
    color: #9aaabb; font-size: 12px;
    transition: transform .3s cubic-bezier(.4,0,.2,1);
}

.pa-accordion.is-open .pa-chevron { transform: rotate(180deg); }

.pa-accordion-body {
    max-height: 0; overflow: hidden;
    transition: max-height .4s cubic-bezier(.4,0,.2,1);
}

.pa-accordion.is-open .pa-accordion-body { max-height: 900px; }

.pa-form { padding: 22px; display: flex; flex-direction: column; gap: 16px; }

/* ── Form Fields ────────────────────────────────────────── */
.pa-field { display: flex; flex-direction: column; gap: 6px; }

.pa-label {
    font-size: 11.5px; font-weight: 700;
    color: #556070;
    display: flex; align-items: center; gap: 5px;
    text-transform: uppercase; letter-spacing: .05em;
}

.pa-label .req { color: #e53e3e; }
.pa-label i    { color: #2356e8; font-size: 11px; }

.pa-input, .pa-select {
    background: #f8fafd;
    border: 1.5px solid #e2e8f0;
    border-radius: 9px;
    padding: 10px 13px;
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-size: 13.5px;
    color: #111827;
    outline: none;
    transition: border-color .18s, box-shadow .18s, background .18s;
    width: 100%;
}

.pa-input::placeholder { color: #c4cdd9; }

.pa-input:focus, .pa-select:focus {
    border-color: #2356e8;
    background: #ffffff;
    box-shadow: 0 0 0 3px rgba(35,86,232,.1);
}

.pa-input.is-error   { border-color: #e53e3e; }
.pa-input.is-success { border-color: #22c55e; }

.pa-select {
    cursor: pointer; appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='7' fill='none' viewBox='0 0 12 7'%3E%3Cpath d='M1 1l5 5 5-5' stroke='%239aaabb' stroke-width='1.5' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 13px center;
    padding-right: 36px;
    background-color: #f8fafd;
}

.pa-select option { background: #fff; }

.pa-hint {
    font-size: 11.5px; color: #9aaabb;
    display: flex; align-items: center; gap: 5px;
}

.pa-hint.is-error   { color: #e53e3e; }
.pa-hint.is-success { color: #16a34a; }

/* Input + verify row */
.pa-input-row { display: flex; gap: 8px; }
.pa-input-row .pa-input { flex: 1; }

.pa-btn-verify {
    padding: 0 15px; height: 42px;
    border-radius: 9px;
    border: 1.5px solid #c7d8ff;
    background: #e8f0fe;
    color: #2356e8;
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-size: 12.5px; font-weight: 700;
    cursor: pointer; white-space: nowrap; flex-shrink: 0;
    transition: all .18s;
    display: flex; align-items: center; gap: 6px;
}

.pa-btn-verify:hover:not(:disabled) { background: #dbeafe; border-color: #2356e8; }
.pa-btn-verify:disabled { opacity: .45; cursor: not-allowed; }
.pa-btn-verify.is-verified { border-color: #bbf7d0; background: #f0fdf4; color: #16a34a; }

/* Verified callout */
.pa-verified {
    display: none; align-items: center; gap: 10px;
    background: #f0fdf4; border: 1.5px solid #bbf7d0;
    border-radius: 9px; padding: 10px 13px;
}

.pa-verified.show { display: flex; }

.pa-verified-icon {
    width: 26px; height: 26px; background: #22c55e;
    border-radius: 50%; display: flex; align-items: center;
    justify-content: center; color: #fff; font-size: 10px; flex-shrink: 0;
}

.pa-verified-name { font-size: 13px; font-weight: 700; color: #111827; }
.pa-verified-sub  { font-size: 11.5px; color: #16a34a; margin-top: 1px; }

/* 2-col row */
.pa-field-row { display: grid; grid-template-columns: 1fr 1fr; gap: 13px; }

/* Toggle */
.pa-toggle-row {
    display: flex; align-items: center; justify-content: space-between;
    padding: 11px 14px;
    background: #f8fafd; border-radius: 9px;
    border: 1.5px solid #e2e8f0; cursor: pointer;
    transition: border-color .15s;
}

.pa-toggle-row:hover { border-color: #c7d8ff; }

.pa-toggle-label {
    display: flex; align-items: center; gap: 9px;
    font-size: 13px; font-weight: 600; color: #334155;
}

.pa-toggle-label i { color: #2356e8; font-size: 13px; }

.pa-switch { position: relative; width: 38px; height: 21px; }
.pa-switch input { opacity: 0; width: 0; height: 0; }

.pa-slider {
    position: absolute; cursor: pointer; inset: 0;
    background: #cbd5e1; border-radius: 99px; transition: background .2s;
}

.pa-slider::before {
    content: ''; position: absolute;
    width: 15px; height: 15px; background: #fff;
    border-radius: 50%; top: 3px; left: 3px;
    transition: transform .2s;
    box-shadow: 0 1px 3px rgba(0,0,0,.12);
}

.pa-switch input:checked + .pa-slider { background: #2356e8; }
.pa-switch input:checked + .pa-slider::before { transform: translateX(17px); }

/* Divider */
.pa-divider { height: 1px; background: #f1f5f9; }

/* Notice */
.pa-notice {
    display: flex; gap: 10px;
    border-radius: 9px; padding: 11px 14px;
    font-size: 12.5px; line-height: 1.6;
}

.pa-notice i { font-size: 13px; flex-shrink: 0; margin-top: 1px; }
.pa-notice p { margin: 0; }
.pa-notice.warning { background: #fffbeb; border: 1px solid #fde68a; color: #92400e; }
.pa-notice.warning i { color: #d97706; }

/* PIN inputs */
.pa-pin-row {
    display: grid;
    grid-template-columns: repeat(6, 1fr);
    gap: 8px;
    width: 100%;
}

.pa-pin {
    width: 100%;
    height: 48px;
    background: #f8fafd;
    border: 1.5px solid #e2e8f0;
    border-radius: 9px;
    text-align: center;
    font-size: 20px;
    font-weight: 700;
    color: #111827;
    outline: none;
    transition: border-color .18s, box-shadow .18s;
    aspect-ratio: 1/1;
    min-width: 40px;
    max-width: 60px;
    margin: 0 auto;
}

.pa-pin:focus { border-color: #2356e8; box-shadow: 0 0 0 3px rgba(35,86,232,.1); background: #fff; }

/* Security progress */
.pa-progress { display: none; background: #f8fafd; border: 1px solid #e2e8f0; border-radius: 9px; padding: 12px 14px; }
.pa-progress.show { display: block; }
.pa-progress-top  { display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px; }
.pa-progress-label{ font-size: 11.5px; font-weight: 700; color: #9aaabb; display: flex; align-items: center; gap: 6px; }
.pa-progress-score{ font-size: 11.5px; font-weight: 700; }
.pa-progress-bar  { height: 5px; background: #e2e8f0; border-radius: 99px; overflow: hidden; }
.pa-progress-fill { height: 100%; border-radius: 99px; width: 0; transition: width .6s ease, background .4s; }

/* Primary button */
.pa-btn-primary {
    width: 100%; padding: 12px 18px;
    background: #2356e8; border: none;
    border-radius: 9px; color: #fff;
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-size: 13.5px; font-weight: 700;
    cursor: pointer;
    transition: all .2s;
    display: flex; align-items: center; justify-content: center; gap: 8px;
    box-shadow: 0 4px 14px rgba(35,86,232,.25);
}

.pa-btn-primary:hover:not(:disabled)  { background: #1a44c4; transform: translateY(-1px); box-shadow: 0 6px 18px rgba(35,86,232,.3); }
.pa-btn-primary:active:not(:disabled) { transform: translateY(0); }
.pa-btn-primary:disabled              { opacity: .6; cursor: not-allowed; }

.pa-spinner {
    width: 15px; height: 15px;
    border: 2px solid rgba(255,255,255,.35);
    border-top-color: #fff; border-radius: 50%;
    animation: paSpin .65s linear infinite; display: none;
}

@keyframes paSpin { to { transform: rotate(360deg); } }

/* ── Modal ──────────────────────────────────────────────── */
.pa-modal-bg {
    position: fixed; inset: 0;
    background: rgba(8,18,48,.45);
    backdrop-filter: blur(3px);
    z-index: 500;
    display: flex; align-items: center; justify-content: center;
    padding: 20px;
    opacity: 0; pointer-events: none;
    transition: opacity .22s;
}

.pa-modal-bg.show { opacity: 1; pointer-events: all; }

.pa-modal {
    background: #fff;
    border-radius: 16px; padding: 26px;
    width: 100%; max-width: 390px;
    transform: scale(.96) translateY(8px);
    transition: transform .22s;
    box-shadow: 0 20px 60px rgba(0,0,0,.14);
}

.pa-modal-bg.show .pa-modal { transform: scale(1) translateY(0); }

.pa-modal-head  { display: flex; align-items: flex-start; gap: 13px; margin-bottom: 18px; }

.pa-modal-icon {
    width: 42px; height: 42px;
    background: #fff1f2; border: 1px solid #fecdd3;
    border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    color: #e53e3e; font-size: 17px; flex-shrink: 0;
}

.pa-modal-title { font-size: 16px; font-weight: 800; color: #111827; margin-bottom: 4px; }
.pa-modal-sub   { font-size: 12.5px; color: #64748b; line-height: 1.55; }

.pa-modal-footer { display: flex; gap: 9px; margin-top: 20px; }

.pa-btn-ghost {
    flex: 1; padding: 11px;
    background: #fff; border: 1.5px solid #e2e8f0;
    border-radius: 9px; color: #556070;
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-size: 13px; font-weight: 600; cursor: pointer;
    transition: all .18s;
}

.pa-btn-ghost:hover { background: #f1f5f9; border-color: #c4cdd9; }

.pa-btn-danger {
    flex: 1; padding: 11px;
    background: #e53e3e; border: none;
    border-radius: 9px; color: #fff;
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-size: 13px; font-weight: 700; cursor: pointer;
    transition: background .18s;
}

.pa-btn-danger:hover { background: #c53030; }

/* ── Toast ──────────────────────────────────────────────── */
.pa-toasts {
    position: fixed;
    bottom: 22px;
    right: 22px;
    z-index: 10000;
    display: flex;
    flex-direction: column;
    gap: 10px;
    pointer-events: none;
    max-width: 350px;
}

.pa-toast {
    background: white;
    border-radius: 12px;
    padding: 14px 18px;
    display: flex;
    align-items: center;
    gap: 12px;
    font-size: 14px;
    font-weight: 500;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1), 0 2px 5px rgba(0, 0, 0, 0.05);
    border-left: 4px solid;
    animation: slideInRight 0.3s ease forwards;
    pointer-events: auto;
    width: 100%;
}

.pa-toast i {
    font-size: 18px;
    flex-shrink: 0;
}

.pa-toast span {
    flex: 1;
    color: #1e293b;
}

.pa-toast.success {
    border-left-color: #10b981;
    background: #f0fdf4;
}

.pa-toast.success i {
    color: #10b981;
}

.pa-toast.warning {
    border-left-color: #f59e0b;
    background: #fffbeb;
}

.pa-toast.warning i {
    color: #f59e0b;
}

.pa-toast.error {
    border-left-color: #ef4444;
    background: #fef2f2;
}

.pa-toast.error i {
    color: #ef4444;
}

.pa-toast.info {
    border-left-color: #3b82f6;
    background: #eff6ff;
}

.pa-toast.info i {
    color: #3b82f6;
}

/* MODAL ALERT - Pop up di tengah */
.pa-modal-alert {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    backdrop-filter: blur(4px);
    z-index: 999999;
    display: none;
    align-items: center;
    justify-content: center;
}

.pa-modal-alert.show {
    display: flex;
}

.pa-modal-alert-content {
    background: white;
    border-radius: 20px;
    padding: 30px 25px;
    max-width: 380px;
    width: 90%;
    text-align: center;
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
    animation: modalPop 0.3s ease;
}

@keyframes modalPop {
    0% {
        opacity: 0;
        transform: scale(0.9) translateY(10px);
    }
    100% {
        opacity: 1;
        transform: scale(1) translateY(0);
    }
}

.pa-modal-alert-icon {
    width: 70px;
    height: 70px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 20px;
    font-size: 30px;
}

.pa-modal-alert-icon.warning {
    background: #fef3c7;
    color: #d97706;
}

.pa-modal-alert-icon.success {
    background: #d1fae5;
    color: #059669;
}

.pa-modal-alert-icon.error {
    background: #fee2e2;
    color: #dc2626;
}

.pa-modal-alert-icon.info {
    background: #dbeafe;
    color: #2563eb;
}

.pa-modal-alert-title {
    font-size: 20px;
    font-weight: 700;
    color: #111827;
    margin-bottom: 10px;
}

.pa-modal-alert-message {
    font-size: 15px;
    color: #6b7280;
    line-height: 1.5;
    margin-bottom: 25px;
}

.pa-modal-alert-btn {
    background: #2563eb;
    color: white;
    border: none;
    padding: 12px 30px;
    border-radius: 12px;
    font-weight: 600;
    font-size: 15px;
    cursor: pointer;
    transition: all 0.2s;
    min-width: 140px;
}

.pa-modal-alert-btn:hover {
    background: #1d4ed8;
    transform: translateY(-2px);
    box-shadow: 0 10px 25px -5px rgba(37, 99, 235, 0.4);
}

.pa-modal-alert-btn.warning {
    background: #d97706;
}

.pa-modal-alert-btn.warning:hover {
    background: #b45309;
}

/* Animations */
@keyframes slideInRight {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

@keyframes fadeOut {
    from {
        opacity: 1;
        transform: translateX(0);
    }
    to {
        opacity: 0;
        transform: translateX(20px);
    }
}

/* Untuk memastikan toast muncul di atas elemen lain */
.pa-toasts {
    position: fixed;
    bottom: 30px;
    right: 30px;
    z-index: 999999;
}

@keyframes paToastIn {
    from { transform: translateX(18px); opacity: 0; }
    to   { transform: translateX(0); opacity: 1; }
}

@keyframes paFadeUp {
    from { opacity: 0; transform: translateY(10px); }
    to   { opacity: 1; transform: translateY(0); }
}

.pa-wrap > * { animation: paFadeUp .35s ease both; }
.pa-header    { animation-delay: .00s; }
.pa-sec-badge { animation-delay: .04s; }
.pa-stats     { animation-delay: .08s; }
.pa-accounts  { animation-delay: .12s; }
.pa-accordion { animation-delay: .16s; }

/* Responsive */
@media (max-width: 768px) {
    .pa-pin-row {
        gap: 6px;
    }
    
    .pa-pin {
        height: 44px;
        font-size: 18px;
    }
}

@media (max-width: 576px) {
    .pa-pin-row {
        gap: 4px;
    }
    
    .pa-pin {
        height: 40px;
        font-size: 16px;
    }
}

@media (max-width: 520px) {
    .pa-field-row  { grid-template-columns: 1fr; }
    .pa-header-title { font-size: 17px; }
}
</style>

<div class="pa-wrap">
    {{-- Header --}}
    <div style="margin-bottom: 24px;">
        <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 8px;">
            {{-- <a href="{{ route('dashboard') }}" style="width: 36px; height: 36px; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 8px; display: flex; align-items: center; justify-content: center; text-decoration: none; transition: all 0.2s;">
                <i class="fas fa-arrow-left" style="font-size: 14px; color: #475569;"></i>
            </a> --}}
            <div>
                <h1 style="margin: 0; font-size: 24px; font-weight: 600; color: #000000;">Rekening Pembayaran</h1>
                <p style="margin: 0; font-size: 14px; color: #797979;">Kelola rekening bank tersimpan untuk penarikan saldo</p>
            </div>
        </div>
        <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;">
            <span style="display:inline-flex;align-items:center;gap:6px;padding:7px 12px;border-radius:999px;background:{{ $isProUser ? '#eff6ff' : '#f8fafc' }};border:1px solid {{ $isProUser ? '#bfdbfe' : '#e2e8f0' }};font-size:12px;font-weight:700;color:{{ $isProUser ? '#1d4ed8' : '#475569' }};">
                <i class="fas {{ $isProUser ? 'fa-crown' : 'fa-wallet' }}"></i>
                {{ $isProUser ? 'Pro: hingga 5 rekening' : 'Free: hingga 2 rekening' }}
            </span>
            @unless($isProUser)
                <span style="font-size:12px;color:#64748b;">Upgrade ke Pro untuk menambah sampai 5 rekening bank.</span>
            @endunless
        </div>
    </div>

    {{-- Security badge --}}
    <div class="pa-sec-badge">
        <div class="dot"></div>
        <i class="fas fa-lock"></i>
        Koneksi aman &mdash; AES-256 Encrypted &mdash; Dilindungi konfirmasi PIN
    </div>

    {{-- Stats --}}
    <div class="pa-stats">
        <div class="pa-stat">
            <div class="pa-stat-val">{{ $maxAccounts }}</div>
            <div class="pa-stat-label">Maks. Rekening</div>
        </div>
        <div class="pa-stat">
            <div class="pa-stat-val" id="statSaved">{{ $accounts->count() }}</div>
            <div class="pa-stat-label">Tersimpan</div>
        </div>
        <div class="pa-stat">
            <div class="pa-stat-val" id="statSlots">{{ $remainingSlots }}</div>
            <div class="pa-stat-label">Slot Tersisa</div>
        </div>
    </div>

    {{-- Saved accounts --}}
    <div class="pa-section-label">Rekening Tersimpan</div>
    <div class="pa-accounts" id="accountsList">
        @forelse($accounts as $acc)
            <div class="pa-card {{ $acc['is_default'] ? 'is-default' : '' }}"
                 data-id="{{ $acc['id'] }}"
                 data-bank="{{ $acc['bank_code'] }}">

                <div class="pa-bank {{ $acc['bank_code'] }}">
                    {{ Str::limit($acc['bank_code'], 4, '') }}
                </div>

                <div class="pa-card-info">
                    <div class="pa-card-name">{{ $acc['holder_name'] }}</div>
                    <div class="pa-card-num">{{ $acc['masked_number'] }} &middot; {{ $acc['bank_name'] }}</div>
                </div>

                @if($acc['is_default'])
                    <span class="pa-tag pa-tag-default">
                        <i class="fas fa-star" style="font-size:8px"></i> Utama
                    </span>
                @elseif($acc['label'])
                    <span class="pa-tag pa-tag-label">{{ $acc['label'] }}</span>
                @endif

                <div class="pa-card-actions">
                    @if(!$acc['is_default'])
                        <button class="pa-btn-icon set-default" title="Jadikan utama"
                                onclick="setDefault(event, {{ $acc['id'] }})">
                            <i class="far fa-star"></i>
                        </button>
                    @endif
                    <button class="pa-btn-icon danger" title="Hapus rekening"
                            onclick="openDeleteModal(event, {{ $acc['id'] }})">
                        <i class="far fa-trash-can"></i>
                    </button>
                </div>

                <div class="pa-check">
                    <i class="fas fa-check" style="font-size:9px"></i>
                </div>
            </div>
        @empty
            <div class="pa-empty" id="emptyState">
                <div class="pa-empty-icon"><i class="far fa-credit-card"></i></div>
                <div class="pa-empty-title">Belum ada rekening tersimpan</div>
                <div class="pa-empty-sub">Tambahkan rekening bank untuk memudahkan penarikan saldo</div>
            </div>
        @endforelse
    </div>

    {{-- Add account accordion --}}
    <div class="pa-section-label">Tambah Rekening Baru</div>
    <div class="pa-accordion" id="addAccordion">
        <div class="pa-accordion-trigger" onclick="toggleForm()">
            <div class="pa-trigger-left">
                <div class="pa-trigger-icon"><i class="fas fa-plus"></i></div>
                <div>
                    <div class="pa-trigger-title">Tambah Rekening Bank</div>
                    <div class="pa-trigger-sub">Simpan rekening untuk penarikan yang lebih cepat</div>
                </div>
            </div>
            <i class="fas fa-chevron-down pa-chevron"></i>
        </div>

        <div class="pa-accordion-body">
            <div class="pa-form">
                {{-- Bank --}}
                <div class="pa-field">
                    <label class="pa-label"><i class="fas fa-landmark"></i> Bank <span class="req">*</span></label>
                    <select class="pa-select" id="fBank" onchange="onBankChange()">
                        <option value="">-- Pilih Bank --</option>
                        @foreach($bankList as $code => $name)
                            <option value="{{ $code }}">{{ $name }} ({{ $code }})</option>
                        @endforeach
                    </select>
                </div>

                {{-- Account number --}}
                <div class="pa-field">
                    <label class="pa-label"><i class="fas fa-hashtag"></i> Nomor Rekening <span class="req">*</span></label>
                    <div class="pa-input-row">
                        <input type="text" class="pa-input" id="fAccountNum"
                               placeholder="Masukkan nomor rekening"
                               maxlength="16" inputmode="numeric" autocomplete="off"
                               oninput="onAccountNumInput()" />
                        <button class="pa-btn-verify" id="verifyBtn" onclick="verifyAccount()" disabled>
                            <i class="fas fa-circle-check"></i> Verifikasi
                        </button>
                    </div>
                    <div class="pa-hint" id="numHint">
                        <i class="fas fa-circle-info"></i> Nomor rekening 10–16 digit
                    </div>
                </div>

                {{-- Verified name --}}
                <div class="pa-verified" id="verifiedCallout">
                    <div class="pa-verified-icon">
                        <i class="fas fa-check" style="font-size:10px"></i>
                    </div>
                    <div>
                        <div class="pa-verified-name" id="verifiedName">—</div>
                        <div class="pa-verified-sub">
                            <i class="fas fa-shield-halved" style="font-size:10px"></i>
                            Nama terverifikasi sesuai data bank
                        </div>
                    </div>
                </div>

                {{-- Holder name --}}
                <div class="pa-field">
                    <label class="pa-label"><i class="fas fa-user"></i> Nama Pemilik Rekening <span class="req">*</span></label>
                    <input type="text" class="pa-input" id="fHolder"
                           placeholder="Nama sesuai buku tabungan"
                           maxlength="60" oninput="onHolderInput()" />
                    <div class="pa-hint" id="holderHint">
                        <i class="fas fa-circle-info"></i> Sesuaikan dengan nama di rekening bank
                    </div>
                </div>

                {{-- Label + Default --}}
                <div class="pa-field-row">
                    <div class="pa-field">
                        <label class="pa-label"><i class="fas fa-tag"></i> Label <span style="color:#c4cdd9;font-weight:400;text-transform:none">(opsional)</span></label>
                        <input type="text" class="pa-input" id="fLabel" placeholder="Contoh: Tabungan Bisnis" maxlength="30" />
                    </div>
                    <div class="pa-field" style="justify-content:flex-end">
                        <label class="pa-label" style="opacity:0;user-select:none">-</label>
                        <label class="pa-toggle-row" for="fDefault">
                            <div class="pa-toggle-label">
                                <i class="fas fa-star"></i> Jadikan Utama
                            </div>
                            <div class="pa-switch">
                                <input type="checkbox" id="fDefault" />
                                <span class="pa-slider"></span>
                            </div>
                        </label>
                    </div>
                </div>

                <div class="pa-divider"></div>

                {{-- Notice --}}
                <div class="pa-notice warning">
                    <i class="fas fa-triangle-exclamation"></i>
                    <p><strong>Keamanan data Anda:</strong> Nomor rekening dienkripsi sebelum disimpan. Konfirmasi PIN diperlukan untuk setiap penambahan rekening baru.</p>
                </div>

                {{-- PIN --}}
                <div class="pa-field">
                    <label class="pa-label"><i class="fas fa-key"></i> Konfirmasi PIN Akun <span class="req">*</span></label>
                    <div class="pa-pin-row" id="pinInputs">
                        <input type="password" class="pa-pin" maxlength="1" inputmode="numeric" />
                        <input type="password" class="pa-pin" maxlength="1" inputmode="numeric" />
                        <input type="password" class="pa-pin" maxlength="1" inputmode="numeric" />
                        <input type="password" class="pa-pin" maxlength="1" inputmode="numeric" />
                        <input type="password" class="pa-pin" maxlength="1" inputmode="numeric" />
                        <input type="password" class="pa-pin" maxlength="1" inputmode="numeric" />
                    </div>
                    <div class="pa-hint"><i class="fas fa-circle-info"></i> Masukkan 6 digit PIN akun Anda</div>
                </div>

                {{-- Progress --}}
                <div class="pa-progress" id="securityProgress">
                    <div class="pa-progress-top">
                        <div class="pa-progress-label"><i class="fas fa-shield-halved"></i> Kelengkapan Data</div>
                        <span class="pa-progress-score" id="spText" style="color:#9aaabb">—</span>
                    </div>
                    <div class="pa-progress-bar">
                        <div class="pa-progress-fill" id="spFill"></div>
                    </div>
                </div>

                {{-- Submit --}}
                <button class="pa-btn-primary" id="saveBtn" onclick="saveAccount()">
                    <i class="fas fa-floppy-disk" id="saveBtnIcon"></i>
                    <span id="saveBtnText">Simpan Rekening</span>
                    <div class="pa-spinner" id="saveSpinner"></div>
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Delete Modal --}}
<div class="pa-modal-bg" id="deleteModal">
    <div class="pa-modal">
        <div class="pa-modal-head">
            <div class="pa-modal-icon"><i class="fas fa-trash-can"></i></div>
            <div>
                <div class="pa-modal-title">Hapus Rekening?</div>
                <div class="pa-modal-sub">Rekening yang dihapus tidak dapat dipulihkan. Pastikan tidak ada penarikan tertunda menggunakan rekening ini.</div>
            </div>
        </div>
        <div class="pa-field" style="margin-top:4px">
            <label class="pa-label"><i class="fas fa-key"></i> Konfirmasi PIN <span class="req">*</span></label>
            <div class="pa-pin-row" id="deletePinInputs">
                <input type="password" class="pa-pin" maxlength="1" inputmode="numeric" />
                <input type="password" class="pa-pin" maxlength="1" inputmode="numeric" />
                <input type="password" class="pa-pin" maxlength="1" inputmode="numeric" />
                <input type="password" class="pa-pin" maxlength="1" inputmode="numeric" />
                <input type="password" class="pa-pin" maxlength="1" inputmode="numeric" />
                <input type="password" class="pa-pin" maxlength="1" inputmode="numeric" />
            </div>
        </div>
        <div class="pa-modal-footer">
            <button class="pa-btn-ghost" onclick="closeDeleteModal()"><i class="fas fa-xmark"></i> Batal</button>
            <button class="pa-btn-danger" onclick="confirmDelete()"><i class="fas fa-trash-can"></i> Hapus Rekening</button>
        </div>
    </div>
</div>

{{-- Modal Alert --}}
<div class="pa-modal-alert" id="alertModal">
    <div class="pa-modal-alert-content">
        <div class="pa-modal-alert-icon warning" id="alertIcon">
            <i class="fas fa-exclamation-triangle"></i>
        </div>
        <div class="pa-modal-alert-title" id="alertTitle">Peringatan</div>
        <div class="pa-modal-alert-message" id="alertMessage">Pesan akan tampil di sini</div>
        <button class="pa-modal-alert-btn" onclick="closeAlertModal()" id="alertButton">OK</button>
    </div>
</div>

{{-- Toast Container --}}
<div class="pa-toasts" id="toastStack"></div>

<script>
// Fungsi untuk menampilkan modal alert
function showAlert(type, message) {
    const modal = document.getElementById('alertModal');
    const icon = document.getElementById('alertIcon');
    const title = document.getElementById('alertTitle');
    const msg = document.getElementById('alertMessage');
    const btn = document.getElementById('alertButton');
    
    // Set icon dan title berdasarkan type
    icon.className = 'pa-modal-alert-icon ' + type;
    btn.className = 'pa-modal-alert-btn ' + type;
    
    switch(type) {
        case 'warning':
            icon.innerHTML = '<i class="fas fa-exclamation-triangle"></i>';
            title.textContent = 'Peringatan';
            btn.textContent = 'Mengerti';
            break;
        case 'success':
            icon.innerHTML = '<i class="fas fa-check-circle"></i>';
            title.textContent = 'Berhasil';
            btn.textContent = 'OK';
            break;
        case 'error':
            icon.innerHTML = '<i class="fas fa-times-circle"></i>';
            title.textContent = 'Gagal';
            btn.textContent = 'Tutup';
            break;
        default:
            icon.innerHTML = '<i class="fas fa-info-circle"></i>';
            title.textContent = 'Informasi';
            btn.textContent = 'OK';
    }
    
    msg.textContent = message;
    modal.classList.add('show');
}

function closeAlertModal() {
    document.getElementById('alertModal').classList.remove('show');
}

// Override showToast untuk menggunakan modal alert
function showToast(type, icon, message) {
    showAlert(type, message);
}

const CSRF   = document.querySelector('meta[name="csrf-token"]')?.content || '';
const ROUTES = {
    store:   "{{ route('payment.accounts.store') }}",
    verify:  "{{ route('payment.accounts.verify') }}",
    destroy: id => `/payment/accounts/${id}`,
    default: id => `/payment/accounts/${id}/default`,
};
const MAX_ACCOUNTS = {{ $maxAccounts }};
let pendingDeleteId = null;
let isVerified      = false;

function toggleForm() {
    document.getElementById('addAccordion').classList.toggle('is-open');
}

function onAccountNumInput() {
    const el   = document.getElementById('fAccountNum');
    const btn  = document.getElementById('verifyBtn');
    const hint = document.getElementById('numHint');
    el.value   = el.value.replace(/\D/g, '').slice(0, 16);
    const len  = el.value.length;

    if (len >= 10) {
        btn.disabled = false;
        hint.className = 'pa-hint is-success';
        hint.innerHTML = '<i class="fas fa-check"></i> Format nomor valid';
        el.classList.remove('is-error'); el.classList.add('is-success');
    } else if (len > 0) {
        btn.disabled = true;
        hint.className = 'pa-hint is-error';
        hint.innerHTML = '<i class="fas fa-triangle-exclamation"></i> Nomor terlalu pendek (min. 10 digit)';
        el.classList.add('is-error'); el.classList.remove('is-success');
    } else {
        btn.disabled = true;
        hint.className = 'pa-hint';
        hint.innerHTML = '<i class="fas fa-circle-info"></i> Nomor rekening 10–16 digit';
        el.classList.remove('is-error', 'is-success');
    }

    if (isVerified) {
        isVerified = false;
        document.getElementById('verifiedCallout').classList.remove('show');
        btn.innerHTML = '<i class="fas fa-circle-check"></i> Verifikasi';
        btn.className = 'pa-btn-verify';
        document.getElementById('fHolder').value = '';
    }
    updateProgress();
}

function onBankChange() {
    isVerified = false;
    document.getElementById('verifiedCallout').classList.remove('show');
    updateProgress();
}

function onHolderInput() {
    const val  = document.getElementById('fHolder').value.trim();
    const hint = document.getElementById('holderHint');
    if (val.length >= 3) {
        hint.className = 'pa-hint is-success';
        hint.innerHTML = '<i class="fas fa-check"></i> Nama terisi';
    } else {
        hint.className = 'pa-hint';
        hint.innerHTML = '<i class="fas fa-circle-info"></i> Sesuaikan dengan nama di rekening bank';
    }
    updateProgress();
}

async function verifyAccount() {
    const bank = document.getElementById('fBank').value;
    const num  = document.getElementById('fAccountNum').value;
    if (!bank) { showAlert('warning', 'Pilih bank terlebih dahulu.'); return; }
    if (num.length < 10) { showAlert('warning', 'Nomor rekening tidak valid.'); return; }

    const btn = document.getElementById('verifyBtn');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-circle-notch fa-spin"></i> Memverifikasi...';

    try {
        const res = await apiFetch(ROUTES.verify, { bank_code: bank, account_number: num });
        if (res.success) {
            isVerified = true;
            document.getElementById('fHolder').value = res.account_name;
            document.getElementById('verifiedName').textContent = res.account_name;
            document.getElementById('verifiedCallout').classList.add('show');
            document.getElementById('fAccountNum').classList.add('is-success');
            btn.innerHTML = '<i class="fas fa-circle-check"></i> Terverifikasi';
            btn.classList.add('is-verified'); btn.disabled = true;
            showAlert('success', 'Rekening berhasil diverifikasi.');
            updateProgress();
        } else {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-circle-check"></i> Coba Lagi';
            showAlert('error', res.message || 'Verifikasi gagal.');
        }
    } catch {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-circle-check"></i> Verifikasi';
        showAlert('error', 'Gagal menghubungi server.');
    }
}

function initPinNav(containerId) {
    const cells = document.querySelectorAll(`#${containerId} .pa-pin`);
    cells.forEach((cell, idx) => {
        cell.addEventListener('input', () => {
            cell.value = cell.value.replace(/\D/g,'').slice(-1);
            if (cell.value && idx < cells.length - 1) cells[idx+1].focus();
            if (containerId === 'pinInputs') updateProgress();
        });
        cell.addEventListener('keydown', e => {
            if (e.key === 'Backspace' && !cell.value && idx > 0) cells[idx-1].focus();
        });
        cell.addEventListener('paste', e => {
            e.preventDefault();
            const p = (e.clipboardData||window.clipboardData).getData('text').replace(/\D/g,'');
            cells.forEach((c,i) => c.value = p[i]||'');
            if (p.length) cells[Math.min(p.length, cells.length-1)].focus();
            if (containerId === 'pinInputs') updateProgress();
        });
    });
}

initPinNav('pinInputs');
initPinNav('deletePinInputs');

function getPinValue(cid) {
    return Array.from(document.querySelectorAll(`#${cid} .pa-pin`)).map(c=>c.value).join('');
}

function updateProgress() {
    const prog  = document.getElementById('securityProgress');
    const fill  = document.getElementById('spFill');
    const txt   = document.getElementById('spText');
    const bank  = document.getElementById('fBank').value;
    const num   = document.getElementById('fAccountNum').value;
    const holder= document.getElementById('fHolder').value.trim();
    const pin   = getPinValue('pinInputs');

    let pts = 0;
    if (bank)           pts += 20;
    if (num.length >= 10) pts += 20;
    if (isVerified)     pts += 25;
    if (holder.length >= 3) pts += 15;
    if (pin.length === 6)   pts += 20;

    if (pts === 0) { prog.classList.remove('show'); return; }
    prog.classList.add('show');
    fill.style.width = pts + '%';

    if (pts < 40) {
        fill.style.background = '#e53e3e'; txt.textContent = 'Kurang lengkap'; txt.style.color = '#e53e3e';
    } else if (pts < 80) {
        fill.style.background = '#f59e0b'; txt.textContent = 'Hampir selesai'; txt.style.color = '#d97706';
    } else {
        fill.style.background = '#22c55e'; txt.textContent = 'Siap disimpan'; txt.style.color = '#16a34a';
    }
}

async function saveAccount() {
    const bank   = document.getElementById('fBank').value;
    const num    = document.getElementById('fAccountNum').value;
    const holder = document.getElementById('fHolder').value.trim();
    const label  = document.getElementById('fLabel').value.trim();
    const isDef  = document.getElementById('fDefault').checked;
    const pin    = getPinValue('pinInputs');

    if (!bank)            { showAlert('warning', 'Pilih bank terlebih dahulu.'); return; }
    if (num.length < 10)  { showAlert('warning', 'Nomor rekening tidak valid.'); return; }
    if (!isVerified)      { showAlert('warning', 'Verifikasi nomor rekening terlebih dahulu.'); return; }
    if (holder.length < 3){ showAlert('warning', 'Masukkan nama pemilik rekening.'); return; }
    if (pin.length < 6)   { showAlert('warning', 'Masukkan 6 digit PIN Anda.'); return; }

    setBtnLoading(true);

    try {
        const res = await apiFetch(ROUTES.store, {
            bank_code: bank, account_number: num, account_holder: holder,
            label, is_default: isDef ? 1 : 0, pin,
        });
        if (res.success) {
            appendAccountCard(res.account);
            resetForm();
            document.getElementById('addAccordion').classList.remove('is-open');
            updateSlotStats();
            showAlert('success', 'Rekening berhasil disimpan.');
        } else {
            showAlert('error', res.message || 'Gagal menyimpan rekening.');
        }
    } catch {
        showAlert('error', 'Terjadi kesalahan. Silakan coba lagi.');
    }

    setBtnLoading(false);
}

function setBtnLoading(loading) {
    document.getElementById('saveBtn').disabled = loading;
    document.getElementById('saveBtnIcon').style.display  = loading ? 'none' : '';
    document.getElementById('saveSpinner').style.display  = loading ? 'block' : 'none';
    document.getElementById('saveBtnText').textContent    = loading ? 'Menyimpan...' : 'Simpan Rekening';
}

async function setDefault(e, id) {
    e.stopPropagation();
    try {
        const res = await fetch(ROUTES.default(id), {
            method: 'PATCH',
            headers: { 'X-CSRF-TOKEN': CSRF, 'Content-Type': 'application/json', 'Accept': 'application/json' },
        }).then(r => r.json());
        if (res.success) {
            document.querySelectorAll('.pa-card').forEach(c => {
                c.classList.remove('is-default');
                const chk = c.querySelector('.pa-check');
                if (chk) chk.style.display = 'none';
                if (c.dataset.id == id) {
                    c.classList.add('is-default');
                    if (chk) chk.style.display = 'flex';
                }
            });
            showAlert('success', 'Rekening utama diperbarui.');
        }
    } catch {
        showAlert('error', 'Gagal memperbarui rekening utama.');
    }
}

function openDeleteModal(e, id) {
    e.stopPropagation();
    pendingDeleteId = id;
    document.querySelectorAll('#deletePinInputs .pa-pin').forEach(c => c.value = '');
    document.getElementById('deleteModal').classList.add('show');
    document.querySelector('#deletePinInputs .pa-pin').focus();
}

function closeDeleteModal() {
    pendingDeleteId = null;
    document.getElementById('deleteModal').classList.remove('show');
}

async function confirmDelete() {
    const pin = getPinValue('deletePinInputs');
    if (pin.length < 6) { showAlert('warning', 'Masukkan 6 digit PIN.'); return; }

    try {
        const res = await fetch(ROUTES.destroy(pendingDeleteId), {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': CSRF, 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body: JSON.stringify({ pin }),
        }).then(r => r.json());

        if (res.success) {
            const card = document.querySelector(`.pa-card[data-id="${pendingDeleteId}"]`);
            if (card) {
                card.style.transition = 'opacity .3s, transform .3s';
                card.style.opacity = '0'; card.style.transform = 'translateX(18px)';
                setTimeout(() => { card.remove(); updateSlotStats(); checkEmptyState(); }, 300);
            }
            closeDeleteModal();
            showAlert('info', 'Rekening berhasil dihapus.');
        } else {
            showAlert('error', res.message || 'Hapus gagal.');
        }
    } catch {
        showAlert('error', 'Terjadi kesalahan server.');
    }
}

document.getElementById('deleteModal').addEventListener('click', function(e) {
    if (e.target === this) closeDeleteModal();
});

function appendAccountCard(acc) {
    const empty = document.getElementById('emptyState');
    if (empty) empty.remove();
    if (acc.is_default) document.querySelectorAll('.pa-card.is-default').forEach(c => c.classList.remove('is-default'));

    const card = document.createElement('div');
    card.className = `pa-card${acc.is_default ? ' is-default' : ''}`;
    card.dataset.id = acc.id; card.dataset.bank = acc.bank_code;
    card.style.animation = 'paFadeUp .35s ease both';
    card.innerHTML = `
        <div class="pa-bank ${acc.bank_code}">${acc.bank_code.slice(0,4)}</div>
        <div class="pa-card-info">
            <div class="pa-card-name">${escapeHtml(acc.holder_name)}</div>
            <div class="pa-card-num">${escapeHtml(acc.masked_number)} &middot; ${escapeHtml(acc.bank_name)}</div>
        </div>
        ${acc.is_default ? '<span class="pa-tag pa-tag-default"><i class="fas fa-star" style="font-size:8px"></i> Utama</span>' : (acc.label ? `<span class="pa-tag pa-tag-label">${escapeHtml(acc.label)}</span>` : '')}
        <div class="pa-card-actions">
            ${!acc.is_default ? `<button class="pa-btn-icon set-default" title="Jadikan utama" onclick="setDefault(event,${acc.id})"><i class="far fa-star"></i></button>` : ''}
            <button class="pa-btn-icon danger" title="Hapus rekening" onclick="openDeleteModal(event,${acc.id})"><i class="far fa-trash-can"></i></button>
        </div>
        <div class="pa-check" style="${acc.is_default?'':'display:none'}"><i class="fas fa-check" style="font-size:9px"></i></div>
    `;
    document.getElementById('accountsList').appendChild(card);
}

function checkEmptyState() {
    const list = document.getElementById('accountsList');
    if (!list.querySelector('.pa-card')) {
        list.innerHTML = `
            <div class="pa-empty" id="emptyState">
                <div class="pa-empty-icon"><i class="far fa-credit-card"></i></div>
                <div class="pa-empty-title">Belum ada rekening tersimpan</div>
                <div class="pa-empty-sub">Tambahkan rekening bank untuk memudahkan penarikan saldo</div>
            </div>`;
    }
}

function updateSlotStats() {
    const count = document.querySelectorAll('.pa-card').length;
    document.getElementById('statSaved').textContent = count;
    document.getElementById('statSlots').textContent = MAX_ACCOUNTS - count;
}

function resetForm() {
    ['fBank','fAccountNum','fHolder','fLabel'].forEach(id => {
        const el = document.getElementById(id);
        if (el) {
            el.value = ''; 
            el.className = id === 'fBank' ? 'pa-select' : 'pa-input';
        }
    });
    document.getElementById('fDefault').checked = false;
    document.getElementById('verifiedCallout').classList.remove('show');
    document.getElementById('securityProgress').classList.remove('show');
    const btn = document.getElementById('verifyBtn');
    if (btn) {
        btn.disabled = true; 
        btn.innerHTML = '<i class="fas fa-circle-check"></i> Verifikasi'; 
        btn.className = 'pa-btn-verify';
    }
    document.querySelectorAll('#pinInputs .pa-pin').forEach(c => c.value = '');
    document.getElementById('numHint').className = 'pa-hint';
    document.getElementById('numHint').innerHTML = '<i class="fas fa-circle-info"></i> Nomor rekening 10–16 digit';
    document.getElementById('holderHint').className = 'pa-hint';
    document.getElementById('holderHint').innerHTML = '<i class="fas fa-circle-info"></i> Sesuaikan dengan nama di rekening bank';
    isVerified = false;
}

async function apiFetch(url, data) {
    const res = await fetch(url, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF },
        body: JSON.stringify(data),
    });
    return res.json();
}

function escapeHtml(str) {
    const d = document.createElement('div');
    d.appendChild(document.createTextNode(String(str)));
    return d.innerHTML;
}

// Tutup modal alert jika klik di luar
document.getElementById('alertModal').addEventListener('click', function(e) {
    if (e.target === this) closeAlertModal();
});
</script>
@endsection
