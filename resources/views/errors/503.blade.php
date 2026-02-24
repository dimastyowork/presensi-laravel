@extends('layouts.fullscreen-layout')

@section('title', 'Dalam Perbaikan')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display:ital@0;1&family=DM+Mono:wght@400;500&family=Syne:wght@400;600;700;800&display=swap" rel="stylesheet">

<div class="page-root">
    <div class="grain"></div>
    <div class="stripe-accent"></div>

    <!-- Decorative corner tape strips -->
    <div class="tape tape-tl" aria-hidden="true"></div>
    <div class="tape tape-br" aria-hidden="true"></div>

    <main class="content-wrapper">
        <!-- Left col -->
        <div class="left-col">
            <div class="icon-badge">
                <div class="gear-ring" aria-hidden="true">
                    <svg viewBox="0 0 60 60" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="30" cy="30" r="26" stroke="currentColor" stroke-width="1.5" stroke-dasharray="5 4"/>
                    </svg>
                </div>
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.4" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.4" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
            </div>

            <div class="status-pill">
                <span class="dot"></span>
                SEDANG DIPERBAIKI
            </div>

            <!-- Checklist card -->
            <div class="checklist-card">
                <div class="checklist-title">Yang sedang dikerjakan</div>
                <ul class="checklist">
                    <li class="done">
                        <svg viewBox="0 0 16 16" fill="none"><path d="M3 8l3.5 3.5L13 5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        Analisis masalah
                    </li>
                    <li class="done">
                        <svg viewBox="0 0 16 16" fill="none"><path d="M3 8l3.5 3.5L13 5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        Perbaikan backend
                    </li>
                    <li class="active">
                        <span class="spinner"></span>
                        Pengujian sistem
                    </li>
                    <li>
                        <svg viewBox="0 0 16 16" fill="none"><circle cx="8" cy="8" r="5.5" stroke="currentColor" stroke-width="1.5"/></svg>
                        Rilis ke produksi
                    </li>
                </ul>
            </div>
        </div>

        <!-- Right col -->
        <div class="right-col">
            <div class="eyebrow">RS ASA BUNDA</div>

            <h1 class="headline">
                Menu Ini<br>
                <em>Diperbaiki</em>
            </h1>

            <div class="divider"></div>

            <p class="body-text">
                Fitur ini sedang dalam proses perbaikan oleh tim teknis kami. 
                Mohon maaf atas ketidaknyamanannya — halaman ini akan segera kembali normal.
            </p>

            <!-- Progress -->
            <div class="progress-wrap">
                <div class="progress-label">
                    <span>Progres perbaikan</span>
                    <span class="progress-pct" id="pct-label">65%</span>
                </div>
                <div class="progress-track">
                    <div class="progress-fill"></div>
                </div>
            </div>

            <!-- ETA badge -->
            <div class="eta-badge">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Estimasi selesai dalam beberapa saat
            </div>

            <div class="actions">
                <a href="/" class="btn-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    Ke Dashboard
                </a>
                <button onclick="window.history.back()" class="btn-ghost">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Kembali
                </button>
            </div>

            <footer class="footer-meta">
                &copy; {{ date('Y') }} &nbsp;·&nbsp; IT RS Asa Bunda
            </footer>
        </div>
    </main>
</div>

<style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    :root {
        --ink:       #0e0c0a;
        --paper:     #f5f0e8;
        --cream:     #ede8dd;
        --amber:     #d97706;
        --amber-lt:  #fef3c7;
        --amber-dim: rgba(217, 119, 6, 0.10);
        --border:    rgba(14, 12, 10, 0.14);
        --mono:      'DM Mono', monospace;
        --serif:     'DM Serif Display', serif;
        --sans:      'Syne', sans-serif;
    }

    body { background: var(--paper); color: var(--ink); font-family: var(--sans); }

    .page-root {
        position: relative;
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        padding: 2rem;
        background: var(--paper);
    }

    /* ── Grain ── */
    .grain {
        position: fixed; inset: 0; z-index: 0; pointer-events: none;
        background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noise'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noise)' opacity='0.04'/%3E%3C/svg%3E");
        background-repeat: repeat;
        opacity: 0.55;
        mix-blend-mode: multiply;
    }

    /* ── Diagonal stripe ── */
    .stripe-accent {
        position: fixed;
        top: 0; left: -20%;
        width: 38%; height: 100%;
        background: linear-gradient(to bottom, var(--amber-dim), transparent 70%);
        transform: skewX(-8deg);
        pointer-events: none;
        z-index: 0;
    }

    /* ── Tape corner decoration ── */
    .tape {
        position: fixed;
        width: 180px; height: 28px;
        background: repeating-linear-gradient(
            -45deg,
            var(--amber) 0px,
            var(--amber) 10px,
            #0e0c0a 10px,
            #0e0c0a 20px
        );
        opacity: 0.18;
        pointer-events: none;
        z-index: 0;
    }
    .tape-tl { top: 0; left: 0; transform: rotate(-45deg) translate(-50px, -10px); }
    .tape-br { bottom: 0; right: 0; transform: rotate(-45deg) translate(50px, 10px); }

    /* ── Layout ── */
    .content-wrapper {
        position: relative; z-index: 2;
        display: flex;
        gap: clamp(3rem, 8vw, 8rem);
        align-items: flex-start;
        max-width: 960px;
        width: 100%;
        animation: fadeUp 0.7s cubic-bezier(0.16, 1, 0.3, 1) both;
    }
    @keyframes fadeUp {
        from { opacity: 0; transform: translateY(28px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    /* ── Left ── */
    .left-col {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        gap: 1.5rem;
        padding-top: 0.5rem;
        flex-shrink: 0;
        width: 200px;
    }

    .icon-badge {
        position: relative;
        width: 72px; height: 72px;
        background: var(--ink);
        color: var(--paper);
        border-radius: 18px;
        display: flex; align-items: center; justify-content: center;
        box-shadow: 6px 6px 0 var(--amber);
        transition: box-shadow 0.25s, transform 0.25s;
        overflow: visible;
    }
    .icon-badge:hover { box-shadow: 8px 8px 0 var(--amber); transform: translate(-2px,-2px); }
    .icon-badge > svg { width: 32px; height: 32px; position: relative; z-index: 1; }

    .gear-ring {
        position: absolute;
        inset: -12px;
        animation: spin 10s linear infinite;
        color: var(--amber);
        opacity: 0.5;
    }
    .gear-ring svg { width: 100%; height: 100%; }
    @keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }

    .status-pill {
        display: flex; align-items: center; gap: 8px;
        font-family: var(--mono);
        font-size: 0.62rem;
        letter-spacing: 0.12em;
        text-transform: uppercase;
        color: var(--ink);
        border: 1.5px solid var(--border);
        border-radius: 100px;
        padding: 6px 14px;
        background: rgba(255,255,255,0.55);
        white-space: nowrap;
    }
    .dot {
        width: 7px; height: 7px;
        background: var(--amber);
        border-radius: 50%;
        animation: blink 1.4s ease-in-out infinite;
    }
    @keyframes blink { 0%,100% { opacity: 1; } 50% { opacity: 0.15; } }

    /* ── Checklist card ── */
    .checklist-card {
        width: 100%;
        background: rgba(255,255,255,0.6);
        border: 1.5px solid var(--border);
        border-radius: 16px;
        padding: 16px 18px;
        backdrop-filter: blur(8px);
    }
    .checklist-title {
        font-family: var(--mono);
        font-size: 0.6rem;
        letter-spacing: 0.14em;
        text-transform: uppercase;
        color: rgba(14,12,10,0.4);
        margin-bottom: 12px;
    }
    .checklist {
        list-style: none;
        display: flex;
        flex-direction: column;
        gap: 10px;
    }
    .checklist li {
        display: flex;
        align-items: center;
        gap: 9px;
        font-family: var(--sans);
        font-size: 0.78rem;
        font-weight: 600;
        color: rgba(14,12,10,0.4);
    }
    .checklist li svg { width: 16px; height: 16px; flex-shrink: 0; color: rgba(14,12,10,0.25); }
    .checklist li.done { color: rgba(14,12,10,0.65); }
    .checklist li.done svg { color: #16a34a; }
    .checklist li.active { color: var(--amber); font-weight: 700; }

    /* Mini spinner for active item */
    .spinner {
        width: 14px; height: 14px;
        border: 2px solid var(--amber-lt);
        border-top-color: var(--amber);
        border-radius: 50%;
        animation: spin 0.8s linear infinite;
        flex-shrink: 0;
    }

    /* ── Right ── */
    .right-col {
        flex: 1;
        display: flex;
        flex-direction: column;
        align-items: flex-start;
    }

    .eyebrow {
        font-family: var(--mono);
        font-size: 0.65rem;
        letter-spacing: 0.2em;
        text-transform: uppercase;
        color: var(--amber);
        margin-bottom: 1rem;
        font-weight: 500;
    }

    .headline {
        font-family: var(--serif);
        font-size: clamp(3.2rem, 8vw, 6rem);
        line-height: 0.95;
        letter-spacing: -0.02em;
        color: var(--ink);
        margin-bottom: 1.75rem;
    }
    .headline em { font-style: italic; color: var(--amber); }

    .divider { width: 56px; height: 3px; background: var(--ink); margin-bottom: 1.5rem; }

    .body-text {
        font-family: var(--sans);
        font-size: 1rem;
        line-height: 1.7;
        color: rgba(14,12,10,0.65);
        max-width: 400px;
        font-weight: 400;
        margin-bottom: 2rem;
    }

    /* ── Progress ── */
    .progress-wrap { width: 100%; max-width: 400px; margin-bottom: 1.25rem; }
    .progress-label {
        display: flex;
        justify-content: space-between;
        font-family: var(--mono);
        font-size: 0.65rem;
        letter-spacing: 0.1em;
        text-transform: uppercase;
        color: rgba(14,12,10,0.4);
        margin-bottom: 8px;
    }
    .progress-pct { color: var(--amber); font-weight: 500; }
    .progress-track {
        width: 100%; height: 6px;
        background: rgba(14,12,10,0.08);
        border-radius: 100px;
        overflow: hidden;
        position: relative;
    }
    .progress-fill {
        position: absolute; top: 0; left: 0;
        height: 100%; width: 65%;
        background: var(--amber);
        border-radius: 100px;
        animation: breathe 2.5s ease-in-out infinite;
    }
    .progress-fill::after {
        content: '';
        position: absolute; top: 0; right: 0;
        width: 50px; height: 100%;
        background: linear-gradient(to right, transparent, rgba(255,255,255,0.65));
        animation: sweep 1.8s ease-in-out infinite;
    }
    @keyframes breathe { 0%,100% { opacity: 1; } 50% { opacity: 0.72; } }
    @keyframes sweep { 0% { opacity: 0; transform: translateX(-20px); } 50% { opacity: 1; } 100% { opacity: 0; transform: translateX(0); } }

    /* ── ETA badge ── */
    .eta-badge {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        font-family: var(--mono);
        font-size: 0.65rem;
        letter-spacing: 0.08em;
        color: rgba(14,12,10,0.5);
        margin-bottom: 2.5rem;
        padding: 8px 14px;
        background: var(--amber-lt);
        border: 1.5px solid rgba(217,119,6,0.25);
        border-radius: 10px;
    }
    .eta-badge svg { width: 14px; height: 14px; color: var(--amber); flex-shrink: 0; }

    /* ── Buttons ── */
    .actions { display: flex; gap: 12px; flex-wrap: wrap; margin-bottom: 3rem; }

    .btn-primary, .btn-ghost {
        display: inline-flex; align-items: center; gap: 8px;
        height: 50px; padding: 0 24px;
        font-family: var(--sans);
        font-size: 0.875rem;
        font-weight: 700;
        letter-spacing: 0.04em;
        border-radius: 10px;
        cursor: pointer;
        border: none;
        text-decoration: none;
        transition: transform 0.2s, box-shadow 0.2s, background 0.2s;
    }
    .btn-primary { background: var(--ink); color: var(--paper); box-shadow: 4px 4px 0 var(--amber); }
    .btn-primary:hover { transform: translate(-2px,-2px); box-shadow: 6px 6px 0 var(--amber); }
    .btn-primary svg { width: 17px; height: 17px; }

    .btn-ghost { background: transparent; color: var(--ink); border: 1.5px solid var(--border); }
    .btn-ghost:hover { background: var(--cream); transform: translate(-1px,-1px); }
    .btn-ghost svg { width: 17px; height: 17px; }

    /* ── Footer ── */
    .footer-meta {
        font-family: var(--mono);
        font-size: 0.65rem;
        letter-spacing: 0.12em;
        text-transform: uppercase;
        color: rgba(14,12,10,0.35);
    }

    /* ── Responsive ── */
    @media (max-width: 640px) {
        .content-wrapper { flex-direction: column; gap: 2rem; }
        .left-col { width: 100%; flex-direction: row; align-items: center; flex-wrap: wrap; }
        .checklist-card { width: 100%; }
        .headline { font-size: clamp(3rem, 14vw, 5rem); }
    }
</style>
@endsection