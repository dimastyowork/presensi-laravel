@extends('layouts.fullscreen-layout')

@section('title', '403 - Akses Dibatasi')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display:ital@0;1&family=DM+Mono:wght@400;500&family=Syne:wght@400;600;700;800&display=swap" rel="stylesheet">

<div class="page-root">
    <!-- Grain texture overlay -->
    <div class="grain"></div>

    <!-- Background large 403 text -->
    <div class="bg-number" aria-hidden="true">403</div>

    <!-- Diagonal stripe accent -->
    <div class="stripe-accent"></div>

    <main class="content-wrapper">
        <!-- Left column: icon + label -->
        <div class="left-col">
            <div class="icon-badge">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.4" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
            </div>
            <div class="status-pill">
                <span class="dot"></span>
                HTTP 403 — FORBIDDEN
            </div>
        </div>

        <!-- Right column: main copy + actions -->
        <div class="right-col">
            <div class="eyebrow">RS ASA BUNDA</div>

            <h1 class="headline">
                Area<br>
                <em>Terlarang</em>
            </h1>

            <div class="divider"></div>

            <p class="body-text">
                Anda tidak memiliki otoritas untuk mengakses halaman ini. 
                Jika Anda yakin ini adalah kesalahan, silakan hubungi administrator sistem.
            </p>

            <div class="actions">
                <a href="/" class="btn-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    Dashboard
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
        --ink:     #0e0c0a;
        --paper:   #f5f0e8;
        --cream:   #ede8dd;
        --red:     #c0392b;
        --red-dim: rgba(192, 57, 43, 0.12);
        --border:  rgba(14, 12, 10, 0.14);
        --mono:    'DM Mono', monospace;
        --serif:   'DM Serif Display', serif;
        --sans:    'Syne', sans-serif;
    }

    body { background: var(--paper); color: var(--ink); font-family: var(--sans); }

    /* ── Root ── */
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

    /* ── Background 403 ── */
    .bg-number {
        position: fixed;
        bottom: -0.15em;
        right: -0.05em;
        font-family: var(--serif);
        font-size: clamp(18rem, 35vw, 32rem);
        color: transparent;
        -webkit-text-stroke: 2px var(--border);
        pointer-events: none;
        user-select: none;
        line-height: 1;
        z-index: 0;
        letter-spacing: -0.04em;
    }

    /* ── Diagonal accent ── */
    .stripe-accent {
        position: fixed;
        top: 0; left: -20%;
        width: 38%; height: 100%;
        background: linear-gradient(to bottom, var(--red-dim), transparent 70%);
        transform: skewX(-8deg);
        pointer-events: none;
        z-index: 0;
    }

    /* ── Layout ── */
    .content-wrapper {
        position: relative; z-index: 2;
        display: flex;
        gap: clamp(3rem, 8vw, 8rem);
        align-items: flex-start;
        max-width: 900px;
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
    }

    .icon-badge {
        width: 72px; height: 72px;
        background: var(--ink);
        color: var(--paper);
        border-radius: 18px;
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
        box-shadow: 6px 6px 0 var(--red);
        transition: box-shadow 0.25s, transform 0.25s;
    }
    .icon-badge:hover { box-shadow: 8px 8px 0 var(--red); transform: translate(-2px,-2px); }
    .icon-badge svg { width: 32px; height: 32px; }

    .status-pill {
        display: flex; align-items: center; gap: 8px;
        font-family: var(--mono);
        font-size: 0.65rem;
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
        background: var(--red);
        border-radius: 50%;
        animation: blink 1.8s ease-in-out infinite;
    }
    @keyframes blink {
        0%,100% { opacity: 1; }
        50% { opacity: 0.2; }
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
        color: var(--red);
        margin-bottom: 1rem;
        font-weight: 500;
    }

    .headline {
        font-family: var(--serif);
        font-size: clamp(3.5rem, 9vw, 6.5rem);
        line-height: 0.95;
        letter-spacing: -0.02em;
        color: var(--ink);
        margin-bottom: 1.75rem;
    }
    .headline em {
        font-style: italic;
        color: var(--red);
    }

    .divider {
        width: 56px; height: 3px;
        background: var(--ink);
        margin-bottom: 1.5rem;
    }

    .body-text {
        font-family: var(--sans);
        font-size: 1rem;
        line-height: 1.7;
        color: rgba(14,12,10,0.65);
        max-width: 400px;
        font-weight: 400;
        margin-bottom: 2.5rem;
    }

    /* ── Buttons ── */
    .actions {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
        margin-bottom: 3rem;
    }

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

    .btn-primary {
        background: var(--ink);
        color: var(--paper);
        box-shadow: 4px 4px 0 var(--red);
    }
    .btn-primary:hover {
        transform: translate(-2px, -2px);
        box-shadow: 6px 6px 0 var(--red);
    }
    .btn-primary svg { width: 17px; height: 17px; }

    .btn-ghost {
        background: transparent;
        color: var(--ink);
        border: 1.5px solid var(--border);
    }
    .btn-ghost:hover {
        background: var(--cream);
        transform: translate(-1px, -1px);
    }
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
        .bg-number { font-size: 40vw; right: -0.1em; bottom: 0; }
        .left-col { flex-direction: row; align-items: center; }
        .headline { font-size: clamp(3rem, 14vw, 5rem); }
    }
</style>
@endsection