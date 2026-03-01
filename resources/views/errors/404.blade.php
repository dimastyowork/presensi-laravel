@extends(auth()->check() ? 'layouts.app' : 'layouts.fullscreen-layout')

@section('title', '404 - Halaman Tidak Ditemukan')

@section('content')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@600;800&display=swap');

    /* Hilangkan card/wrapper dari parent layout */
    .content-area,
    [class*="content"] > .rounded-2xl,
    [class*="content"] > .rounded-3xl,
    [class*="content"] > [class*="shadow"],
    [class*="content"] > .bg-white,
    [class*="main"] > .rounded-2xl,
    [class*="main"] > .rounded-3xl,
    [class*="main"] > [class*="shadow"],
    [class*="main"] > .bg-white {
        background: transparent !important;
        box-shadow: none !important;
        border: none !important;
        padding: 0 !important;
    }

    .err-page {
        font-family: 'Plus Jakarta Sans', sans-serif;
        background-color: {{ auth()->check() ? 'transparent' : '#ffffff' }};
        min-height: {{ auth()->check() ? 'calc(100vh - 200px)' : '100vh' }};
        display: flex;
        flex-direction: column;
        position: relative;
        overflow: hidden;
    }
    .dark .err-page { background-color: {{ auth()->check() ? 'transparent' : '#030712' }}; }

    /* Sparks */
    .deco { position: absolute; pointer-events: none; animation: decoF 3.5s ease-in-out infinite; }
    .deco:nth-child(2){ animation-delay:.7s }
    .deco:nth-child(3){ animation-delay:1.4s }
    .deco:nth-child(4){ animation-delay:.4s }
    .deco:nth-child(5){ animation-delay:1.9s }
    @keyframes decoF { 0%,100%{transform:scale(1) rotate(0deg)} 50%{transform:scale(1.15) rotate(12deg)} }

    /* Main hero */
    .err-hero {
        flex: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 48px 24px 24px;
        text-align: center;
        position: relative;
        z-index: 10;
    }

    /* 404 number row */
    .err-numrow {
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 2.5rem;
    }

    .err-digit {
        font-size: clamp(130px, 25vw, 250px);
        font-weight: 800;
        line-height: 1;
        letter-spacing: -0.06em;
        color: #0f172a;
        user-select: none;
    }
    .dark .err-digit { color: #f8fafc; }

    /* Portal ring = the 0 */
    .err-ring {
        width: clamp(110px, 20vw, 200px);
        height: clamp(140px, 26vw, 260px);
        border: clamp(18px, 3vw, 30px) solid #3b82f6;
        border-radius: 500px;
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 -15px;
        flex-shrink: 0;
        z-index: 5;
    }

    /* Logo floats inside + overflows ring */
    .err-logo {
        width: 158%;
        position: absolute;
        z-index: 20;
        filter: drop-shadow(0 22px 40px rgba(0,0,0,0.16));
        animation: logoF 4s ease-in-out infinite;
    }
    @keyframes logoF {
        0%,100% { transform: translateY(0); }
        50%      { transform: translateY(-16px); }
    }

    /* OOPS pill */
    .err-oops {
        position: absolute;
        top: 10%; right: -28px;
        background: #3b82f6;
        color: #fff;
        padding: 5px 14px;
        border-radius: 999px;
        font-size: clamp(11px, 1.8vw, 15px);
        font-weight: 800;
        transform: rotate(12deg);
        z-index: 30;
        box-shadow: 0 6px 16px rgba(59,130,246,0.35);
        white-space: nowrap;
    }

    /* ── Bottom bar ── */
    .err-bar {
        position: relative;
        z-index: 10;
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 18px 40px 28px;
        gap: 12px;
    }
    @media(max-width:580px){
        .err-bar {
            flex-direction: column;
            align-items: center;
            gap: 16px;
            padding: 16px 24px 28px;
        }
        .err-bar-center { order: 3; }
    }

    /* Pill button: circle icon + label */
    .pill {
        display: inline-flex;
        align-items: center;
        gap: 14px;
        font-size: 14px;
        font-weight: 700;
        color: #0f172a;
        text-decoration: none;
        background: none;
        border: none;
        cursor: pointer;
        font-family: 'Plus Jakarta Sans', sans-serif;
        transition: opacity .2s, transform .2s;
        white-space: nowrap;
    }
    .dark .pill { color: #f1f5f9; }
    .pill:hover { opacity: .8; transform: translateY(-2px); }

    .pill-icon {
        width: 44px; height: 44px;
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
        transition: transform .2s;
    }
    .pill:hover .pill-icon { transform: scale(1.08); }
    .pill-icon-orange { background: #f97316; color: #fff; }
    .pill-icon-green  { background: #22c55e; color: #fff; }

    /* center copyright */
    .err-bar-center {
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 0.18em;
        text-transform: uppercase;
        color: #94a3b8;
    }
    .dark .err-bar-center { color: #475569; }

    /* Entrance animations */
    @keyframes fadeUp { from{opacity:0;transform:translateY(18px)} to{opacity:1;transform:translateY(0)} }
    .a1 { animation: fadeUp .7s cubic-bezier(.22,1,.36,1) both; }
    .a2 { animation: fadeUp .65s .14s cubic-bezier(.22,1,.36,1) both; }
    .a3 { animation: fadeUp .6s .26s cubic-bezier(.22,1,.36,1) both; }
</style>

<div class="err-page">

    {{-- Decorative sparks --}}
    <div class="deco" style="top:8%;left:7%">
        <svg width="38" height="38" viewBox="0 0 24 24"><path d="M12 0l2.5 9.5L24 12l-9.5 2.5L12 24l-2.5-9.5L0 12l9.5-2.5z" fill="#3b82f6" opacity=".65"/></svg>
    </div>
    <div class="deco" style="top:13%;right:8%">
        <svg width="26" height="26" viewBox="0 0 24 24"><path d="M12 0l2 9.5L24 12l-10 2L12 24l-2-10L0 12l10-2z" fill="#22c55e" opacity=".55"/></svg>
    </div>
    <div class="deco" style="bottom:18%;right:9%">
        <svg width="32" height="32" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" fill="#60a5fa" opacity=".45"/></svg>
    </div>
    <div class="deco" style="top:52%;left:4%">
        <svg width="16" height="16" viewBox="0 0 16 16"><circle cx="8" cy="8" r="8" fill="#3b82f6" opacity=".5"/></svg>
    </div>
    <div class="deco" style="bottom:28%;left:10%">
        <svg width="20" height="20" viewBox="0 0 24 24"><path d="M12 0l2 9.5L24 12l-10 2L12 24l-2-10L0 12l10-2z" fill="#f97316" opacity=".5"/></svg>
    </div>

    {{-- Hero --}}
    <main class="err-hero">

        {{-- 404 --}}
        <div class="err-numrow a1">
            <span class="err-digit">4</span>

            <div class="err-ring">
                <img src="{{ asset('images/logo/logo-title.svg') }}"
                     alt="{{ config('app.name') }}"
                     class="err-logo">
                <div class="err-oops">LOST!</div>
            </div>

            <span class="err-digit">4</span>
        </div>

        {{-- Text --}}
        <div class="a2" style="max-width:560px;">
            <h1 class="text-3xl md:text-5xl font-extrabold text-gray-900 dark:text-white tracking-tight mb-4" style="letter-spacing:-0.02em;">
                Halaman Tidak<br>Ditemukan
            </h1>
            <p class="text-gray-500 dark:text-gray-400 text-sm md:text-base leading-relaxed max-w-md mx-auto">
                Sepertinya Anda tersesat. Halaman yang Anda cari tidak tersedia atau mungkin telah berpindah ke alamat lain.
            </p>
        </div>

    </main>

    {{-- Bottom bar --}}
    <div class="err-bar a3">

        {{-- Left: back to home --}}
        <a href="{{ url('/') }}" class="pill">
            <span class="pill-icon pill-icon-orange">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <polyline points="15 18 9 12 15 6"/>
                </svg>
            </span>
            Ke Beranda Utama
        </a>

        {{-- Center: copyright --}}
        <span class="err-bar-center">
            &copy; {{ date('Y') }} Unit IT RS Asa Bunda
        </span>

        {{-- Right: back --}}
        <button type="button" onclick="window.history.back()" class="pill">
            Kembali
            <span class="pill-icon pill-icon-green">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <polyline points="11 17 6 12 11 7"/>
                    <path d="M6 12h12"/>
                </svg>
            </span>
        </button>

    </div>

</div>
@endsection
