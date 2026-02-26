@extends('layouts.fullscreen-layout')
@section('title', 'Ganti Password')

@section('content')
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}
html,body{height:100%;font-family:'Plus Jakarta Sans',sans-serif;}

body{
    min-height:100vh;
    display:flex;align-items:center;justify-content:center;
    overflow-x:hidden;overflow-y:auto;
    position:relative;
    background:#f0ede8;
    padding:1.5rem;
}
body::before{
    content:'';position:fixed;inset:0;
    background:
        radial-gradient(ellipse 55% 55% at 15% 20%, rgba(37,99,235,.10) 0%, transparent 65%),
        radial-gradient(ellipse 60% 50% at 85% 75%, rgba(100,116,139,.09) 0%, transparent 60%),
        #f0ede8;
}
body::after{
    content:'';position:fixed;inset:0;
    background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='200' height='200'%3E%3Cfilter id='g'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='.85' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='200' height='200' filter='url(%23g)' opacity='.035'/%3E%3C/svg%3E");
    pointer-events:none;
}

/* ── Page wrapper ── */
.page{
    position:relative;z-index:1;
    width:100%;max-width:920px;
    min-height:560px;
    display:grid;
    grid-template-columns:1fr 400px;
    border-radius:24px;
    overflow:hidden;
    box-shadow:
        0 0 0 1px rgba(15,23,42,.07),
        0 20px 60px rgba(15,23,42,.12),
        0 4px 16px rgba(15,23,42,.06);
    animation:fadein .5s ease both;
}
@keyframes fadein{from{opacity:0}to{opacity:1}}

/* ── Left info ── */
.info{
    background:rgba(255,255,255,.45);
    backdrop-filter:blur(20px) saturate(140%);
    -webkit-backdrop-filter:blur(20px) saturate(140%);
    transform:translateZ(0);
    border-right:1px solid rgba(15,23,42,.07);
    padding:3rem 2.75rem;
    display:flex;flex-direction:column;justify-content:space-between;
}
.info-top{}

.logo-wrap{margin-bottom:3rem;}
.logo-wrap img{height:36px;width:auto;}

.info-eyebrow{
    font-size:.7rem;font-weight:600;
    color:#2563eb;letter-spacing:.12em;text-transform:uppercase;
    margin-bottom:.75rem;
}
.info h1{
    font-size:1.75rem;font-weight:800;
    color:#1e293b;line-height:1.2;
    letter-spacing:-.025em;margin-bottom:.65rem;
}
.info h1 em{font-style:normal;color:#2563eb;}
.info-desc{
    font-size:.875rem;color:#64748b;
    line-height:1.75;font-weight:300;
    margin-bottom:2rem;
}

.tip-list{display:flex;flex-direction:column;gap:.625rem;}
.tip-item{
    display:flex;align-items:flex-start;gap:10px;
    padding:.75rem 1rem;
    background:rgba(255,255,255,.55);
    border:1px solid rgba(15,23,42,.07);
    border-radius:12px;
}
.tip-icon{
    width:22px;height:22px;border-radius:6px;
    background:#2563eb;flex-shrink:0;
    display:flex;align-items:center;justify-content:center;
    margin-top:1px;
}
.tip-icon svg{color:#fff;}
.tip-text{font-size:.8rem;color:#475569;line-height:1.5;font-weight:400;}

.info-foot{font-size:.7rem;color:rgba(30,41,59,.25);letter-spacing:.04em;}

/* ── Right form ── */
.form-panel{
    background:rgba(255,255,255,.70);
    backdrop-filter:blur(28px) saturate(160%);
    -webkit-backdrop-filter:blur(28px) saturate(160%);
    transform:translateZ(0);
    padding:2.75rem 2.5rem;
    display:flex;align-items:center;justify-content:center;
}
.form-inner{width:100%;max-width:340px;}

.form-head{margin-bottom:1.75rem;}
.form-head h2{
    font-size:1.25rem;font-weight:800;
    color:#1e293b;letter-spacing:-.02em;margin-bottom:.3rem;
}
.form-head p{font-size:.8rem;color:#94a3b8;line-height:1.6;}

/* Warning */
.form-warn{
    display:flex;align-items:flex-start;gap:9px;
    padding:.625rem .875rem;border-radius:11px;
    background:rgba(245,158,11,.08);
    border:1px solid rgba(245,158,11,.22);
    margin-bottom:1.125rem;
    font-size:.78rem;color:#92400e;line-height:1.5;
}
.form-warn svg{flex-shrink:0;margin-top:1px;color:#f59e0b;}

/* Fields */
.field{margin-bottom:1.0rem;}
.field label{
    display:block;font-size:.72rem;font-weight:600;
    color:rgba(30,41,59,.55);letter-spacing:.06em;text-transform:uppercase;
    margin-bottom:.4rem;
}
.field input{
    width:100%;height:44px;
    background:rgba(255,255,255,.75);
    border:1px solid rgba(15,23,42,.12);
    border-radius:10px;
    font-family:'Plus Jakarta Sans',sans-serif;
    font-size:.88rem;color:#1e293b;
    padding:0 14px;
    outline:none;
    transition:border-color .18s, box-shadow .18s, background .18s;
}
.field input::placeholder{color:rgba(30,41,59,.25);}
.field input:focus{
    border-color:rgba(37,99,235,.45);
    background:rgba(255,255,255,.95);
    box-shadow:0 0 0 3px rgba(37,99,235,.10);
}
.field input.is-error{border-color:rgba(239,68,68,.45);}
.field input.is-error:focus{box-shadow:0 0 0 3px rgba(239,68,68,.10);}
.field-error{
    font-size:.72rem;color:#dc2626;margin-top:.35rem;font-weight:500;
    display:flex;align-items:center;gap:4px;
}

/* Agreement button */
.agreement-btn{
    width:100%;padding:.75rem 1rem;
    border-radius:11px;cursor:pointer;
    display:flex;align-items:center;justify-content:space-between;gap:10px;
    transition:all .18s;margin-bottom:1rem;
    font-family:'Plus Jakarta Sans',sans-serif;
}
.agreement-btn--pending{
    background:rgba(255,255,255,.75);
    border:1px solid rgba(15,23,42,.12);
}
.agreement-btn--pending:hover{border-color:#2563eb;background:#fff;}
.agreement-btn--done{
    background:rgba(22,163,74,.06);
    border:1px solid rgba(22,163,74,.22);
}
.agreement-btn__left{display:flex;align-items:center;gap:10px;}
.agreement-btn__check{
    width:20px;height:20px;border-radius:50%;flex-shrink:0;
    display:flex;align-items:center;justify-content:center;
}
.agreement-btn__check--done{background:#16a34a;}
.agreement-btn__check--empty{
    border:1.5px solid rgba(15,23,42,.20);background:transparent;
}
.agreement-btn__label{font-size:.8rem;font-weight:600;text-align:left;}
.agreement-btn__label--pending{color:#1e293b;}
.agreement-btn__label--done{color:#15803d;}
.agreement-btn__arrow{color:#cbd5e1;flex-shrink:0;}

/* Submit */
.btn-submit{
    width:100%;height:44px;border-radius:10px;border:none;
    font-family:'Plus Jakarta Sans',sans-serif;
    font-size:.88rem;font-weight:700;color:#fff;
    cursor:pointer;letter-spacing:.01em;
    transition:background .18s,box-shadow .18s,opacity .18s;
    display:flex;align-items:center;justify-content:center;gap:8px;
}
.btn-submit--active{
    background:#2563eb;
    box-shadow:0 3px 14px rgba(37,99,235,.30);
}
.btn-submit--active:hover{background:#1d4ed8;box-shadow:0 5px 18px rgba(37,99,235,.40);}
.btn-submit--inactive{background:#94a3b8;cursor:not-allowed;opacity:.7;}

/* Responsive */
@media(max-width:760px){
    body{padding:.875rem;align-items:flex-start;}
    .page{
        grid-template-columns:1fr;
        border-radius:18px;min-height:auto;
    }
    .info{
        padding:1.5rem 1.25rem 1.125rem;
        border-right:none;border-bottom:1px solid rgba(15,23,42,.07);
    }
    .logo-wrap{margin-bottom:1rem;}
    .info h1{font-size:1.1rem;margin-bottom:0;}
    .info-desc,.tip-list,.info-foot{display:none;}
    .form-panel{padding:1.75rem 1.25rem 2rem;align-items:flex-start;}
    .form-inner{max-width:100%;}
}

/* ════════════════════════════════
   MODAL
════════════════════════════════ */
.modal-backdrop{
    position:fixed;inset:0;z-index:50;
    display:flex;align-items:center;justify-content:center;
    padding:1rem;
    background:rgba(3,7,18,.55);
    backdrop-filter:blur(6px);
}
.modal-card{
    width:100%;max-width:660px;
    height:min(88vh,660px);
    border-radius:20px;
    overflow:hidden;
    display:flex;flex-direction:column;
    background:#fafaf9;
    box-shadow:0 32px 64px rgba(0,0,0,.20),0 0 0 1px rgba(15,23,42,.07);
}
.modal-header{
    display:flex;align-items:center;justify-content:space-between;
    padding:.875rem 1.25rem;
    border-bottom:1px solid rgba(15,23,42,.07);
    background:#fff;flex-shrink:0;
}
.modal-header__left{display:flex;align-items:center;gap:11px;}
.modal-header__left img{height:28px;width:auto;}
.modal-header__title{font-size:.8rem;font-weight:700;color:#1e293b;}
.modal-header__sub{font-size:.68rem;color:#94a3b8;margin-top:1px;}
.modal-close{
    width:30px;height:30px;border-radius:8px;
    background:none;border:1px solid rgba(15,23,42,.09);
    color:#94a3b8;display:flex;align-items:center;justify-content:center;
    cursor:pointer;transition:all .15s;
}
.modal-close:hover{background:rgba(15,23,42,.05);color:#1e293b;}

/* Scrollable body */
.modal-body{
    flex:1;min-height:0;
    overflow-y:auto;overscroll-behavior:contain;
    padding:1.25rem 1.5rem;
    background:#fafaf9;
}
.modal-body::-webkit-scrollbar{width:5px;}
.modal-body::-webkit-scrollbar-track{background:transparent;}
.modal-body::-webkit-scrollbar-thumb{background:#d1d5db;border-radius:99px;}
.modal-body::-webkit-scrollbar-thumb:hover{background:#9ca3af;}

/* Scroll nudge */
.modal-nudge{
    flex-shrink:0;position:relative;
}
.modal-nudge__fade{
    position:absolute;bottom:100%;left:0;right:0;height:32px;
    background:linear-gradient(to top, #fafaf9, transparent);
    pointer-events:none;
}
.modal-nudge__bar{
    display:flex;align-items:center;justify-content:center;gap:6px;
    padding:.4rem;font-size:.68rem;color:#94a3b8;font-weight:500;
    border-top:1px solid rgba(15,23,42,.06);background:#f8f9fa;
}
.nudge-bounce{animation:nbounce 1.4s ease-in-out infinite;}
@keyframes nbounce{0%,100%{transform:translateY(0)}50%{transform:translateY(3px)}}

/* Checkbox row */
.modal-check{
    flex-shrink:0;padding:.875rem 1.5rem;
    border-top:1px solid rgba(15,23,42,.07);
    background:#fff;
}
.check-label{
    display:flex;align-items:flex-start;gap:10px;cursor:pointer;
}
.check-label input[type=checkbox]{
    width:16px;height:16px;border-radius:4px;margin-top:1px;
    border:1.5px solid rgba(15,23,42,.18);
    background:rgba(255,255,255,.8);
    appearance:none;-webkit-appearance:none;
    cursor:pointer;transition:all .15s;flex-shrink:0;
}
.check-label input[type=checkbox]:checked{
    background:#2563eb;border-color:#2563eb;
    background-image:url("data:image/svg+xml,%3Csvg viewBox='0 0 12 12' fill='none' stroke='white' stroke-width='2.2' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M2 6l3 3 5-5'/%3E%3C/svg%3E");
    background-size:10px;background-position:center;background-repeat:no-repeat;
}
.check-label span{font-size:.8rem;font-weight:500;color:#475569;line-height:1.55;}
.check-label span strong{color:#1e293b;font-weight:700;}

/* Modal footer */
.modal-footer{
    flex-shrink:0;
    display:flex;align-items:center;justify-content:flex-end;gap:10px;
    padding:.75rem 1.5rem;
    border-top:1px solid rgba(15,23,42,.07);
    background:#fff;
}
.btn-modal-cancel{
    padding:.45rem 1rem;border-radius:8px;border:1px solid rgba(15,23,42,.10);
    background:none;font-family:'Plus Jakarta Sans',sans-serif;
    font-size:.78rem;font-weight:600;color:#64748b;cursor:pointer;
    transition:all .15s;
}
.btn-modal-cancel:hover{background:rgba(15,23,42,.04);color:#1e293b;}
.btn-modal-agree{
    padding:.45rem 1.25rem;border-radius:9px;border:none;
    font-family:'Plus Jakarta Sans',sans-serif;
    font-size:.78rem;font-weight:700;color:#fff;cursor:pointer;
    transition:all .15s;
}
.btn-modal-agree--active{background:#2563eb;box-shadow:0 3px 10px rgba(37,99,235,.28);}
.btn-modal-agree--active:hover{background:#1d4ed8;}
.btn-modal-agree--inactive{background:#94a3b8;cursor:not-allowed;opacity:.6;}

/* ── Content blocks inside modal ── */
.modal-intro{
    display:flex;align-items:flex-start;gap:9px;
    padding:.875rem 1rem;border-radius:12px;
    background:rgba(37,99,235,.05);
    border:1px solid rgba(37,99,235,.14);
    margin-bottom:1.25rem;
    font-size:.8rem;color:#1e3a8a;line-height:1.6;font-weight:500;
}
.modal-intro svg{flex-shrink:0;margin-top:2px;color:#2563eb;}

.section{margin-bottom:1.25rem;}
.section-head{
    display:flex;align-items:center;gap:8px;
    font-size:.68rem;font-weight:800;text-transform:uppercase;
    letter-spacing:.08em;color:#1e293b;margin-bottom:.625rem;
}
.section-num{
    width:20px;height:20px;border-radius:6px;
    background:#2563eb;flex-shrink:0;
    display:flex;align-items:center;justify-content:center;
    font-size:.6rem;font-weight:800;color:#fff;
}
.section-num--red{background:#dc2626;}
.section-head--red{color:#dc2626;}

.section-body{
    background:#fff;border:1px solid rgba(15,23,42,.08);
    border-radius:12px;padding:.875rem 1rem;
    font-size:.8rem;color:#475569;line-height:1.7;
}
.section-body p+p{margin-top:.625rem;}

.section-note{
    margin-top:.625rem;padding:.5rem .75rem;
    border-radius:8px;background:rgba(15,23,42,.03);
    border:1px solid rgba(15,23,42,.07);
    font-size:.73rem;color:#64748b;
}

.check-list{display:flex;flex-direction:column;gap:.5rem;margin-top:.625rem;}
.check-item{display:flex;align-items:flex-start;gap:8px;font-size:.8rem;color:#475569;}
.check-item svg{flex-shrink:0;margin-top:2px;color:#2563eb;}

.warn-list{display:flex;flex-direction:column;gap:.5rem;}
.warn-item{
    display:flex;align-items:flex-start;gap:8px;
    font-size:.78rem;color:#b91c1c;font-weight:600;
}
.warn-badge{
    width:16px;height:16px;border-radius:50%;
    background:#dc2626;flex-shrink:0;margin-top:2px;
    display:flex;align-items:center;justify-content:center;
    font-size:.6rem;font-weight:800;color:#fff;
}

.mini-grid{
    display:grid;grid-template-columns:1fr 1fr;gap:.5rem;
    margin-top:.625rem;
}
@media(max-width:500px){.mini-grid{grid-template-columns:1fr;}}
.mini-card{
    padding:.625rem .75rem;
    background:rgba(15,23,42,.025);
    border:1px solid rgba(15,23,42,.07);
    border-radius:9px;
}
.mini-card__title{
    font-size:.63rem;font-weight:700;text-transform:uppercase;
    letter-spacing:.07em;color:#2563eb;margin-bottom:.25rem;
}
.mini-card__desc{font-size:.73rem;color:#64748b;line-height:1.5;}

.warn-intro{
    font-size:.8rem;font-weight:700;color:#dc2626;
    margin-bottom:.625rem;line-height:1.5;
}
</style>
<div
    x-data="{
        agreementOpen: {{ old('agreement_accepted') ? 'false' : 'true' }},
        agreed: {{ old('agreement_accepted') ? 'true' : 'false' }},
        modalChecked: {{ old('agreement_accepted') ? 'true' : 'false' }},
        reachedEnd: false,
        onScroll(el) {
            if (el.scrollHeight - el.scrollTop - el.clientHeight < 60) this.reachedEnd = true;
        },
        confirmSubmit() {
            Swal.fire({
                title: 'Simpan Password?',
                text: 'Pastikan Anda telah mencatat password baru ini. Lanjutkan?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#2563eb',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Ya, Simpan',
                cancelButtonText: 'Batal',
                background: document.documentElement.classList.contains('dark') ? '#1f2937' : '#ffffff',
                color: document.documentElement.classList.contains('dark') ? '#ffffff' : '#000000',
            }).then((result) => {
                if (result.isConfirmed) {
                    this.$refs.passwordForm.submit();
                }
            });
        }
    }"
>

{{-- ══════ MAIN PAGE ══════ --}}
<div class="page">

    {{-- Left info --}}
    <div class="info">
        <div class="info-top">
            <div class="logo-wrap">
                <img src="/images/logo/logo-header.svg" alt="RS Asa Bunda"/>
            </div>
            <p class="info-eyebrow">Langkah Wajib</p>
            <h1>Buat Password<br/>Baru yang <em>Aman</em></h1>
            <p class="info-desc">Demi keamanan akun Anda, ganti password bawaan sebelum dapat menggunakan aplikasi sepenuhnya.</p>
            <div class="tip-list">
                <div class="tip-item">
                    <div class="tip-icon"><svg width="11" height="11" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7" stroke-width="3" stroke-linecap="round"/></svg></div>
                    <p class="tip-text">Gunakan minimal 8 karakter</p>
                </div>
                <div class="tip-item">
                    <div class="tip-icon"><svg width="11" height="11" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7" stroke-width="3" stroke-linecap="round"/></svg></div>
                    <p class="tip-text">Kombinasikan huruf, angka & simbol</p>
                </div>
                <div class="tip-item">
                    <div class="tip-icon"><svg width="11" height="11" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7" stroke-width="3" stroke-linecap="round"/></svg></div>
                    <p class="tip-text">Hindari tanggal lahir atau nama pribadi</p>
                </div>
                <div class="tip-item">
                    <div class="tip-icon"><svg width="11" height="11" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7" stroke-width="3" stroke-linecap="round"/></svg></div>
                    <p class="tip-text">Jangan bagikan password kepada siapapun</p>
                </div>
            </div>
        </div>
        <div class="info-foot">&copy; {{ date('Y') }} RS Asa Bunda</div>
    </div>

    {{-- Right form --}}
    <div class="form-panel">
        <div class="form-inner">

            <div class="form-head">
                <h2>Halo, {{ \App\Helpers\NameHelper::getFirstName(Auth::user()->name) }} 👋</h2>
                <p>Buat password baru untuk mengamankan akun Anda.</p>
            </div>

            @if(session('warning'))
            <div class="form-warn">
                <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" stroke-width="2" stroke-linecap="round"/></svg>
                <span>{{ session('warning') }}</span>
            </div>
            @endif

            <form method="POST" action="{{ route('password.update') }}" x-ref="passwordForm" @submit.prevent="confirmSubmit()">
                @csrf

                <div class="field">
                    <label>Password Saat Ini</label>
                    <input type="password" name="current_password" required autofocus
                        placeholder="Masukkan password lama"
                        class="{{ $errors->has('current_password') ? 'is-error' : '' }}"/>
                    @error('current_password')
                    <div class="field-error">
                        <svg width="11" height="11" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>
                        {{ $message }}
                    </div>
                    @enderror
                </div>

                <div class="field">
                    <label>Password Baru</label>
                    <input type="password" name="password" required
                        placeholder="Min. 8 karakter"
                        class="{{ $errors->has('password') ? 'is-error' : '' }}"/>
                    @error('password')
                    <div class="field-error">
                        <svg width="11" height="11" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>
                        {{ $message }}
                    </div>
                    @enderror
                </div>

                <div class="field">
                    <label>Konfirmasi Password Baru</label>
                    <input type="password" name="password_confirmation" required placeholder="Ulangi password baru"/>
                </div>

                <input type="hidden" name="agreement_accepted" :value="agreed ? 1 : ''"/>

                {{-- Agreement trigger --}}
                <button type="button"
                    @click="modalChecked = agreed; reachedEnd = false; agreementOpen = true"
                    class="agreement-btn"
                    :class="agreed ? 'agreement-btn--done' : 'agreement-btn--pending'">
                    <div class="agreement-btn__left">
                        <span class="agreement-btn__check" :class="agreed ? 'agreement-btn__check--done' : 'agreement-btn__check--empty'">
                            <template x-if="agreed">
                                <svg width="10" height="10" fill="none" stroke="white" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7" stroke-width="3" stroke-linecap="round"/></svg>
                            </template>
                        </span>
                        <span class="agreement-btn__label" :class="agreed ? 'agreement-btn__label--done' : 'agreement-btn__label--pending'">
                            <span x-show="agreed">Kebijakan telah disetujui</span>
                            <span x-show="!agreed">Baca & Setujui Kebijakan Penggunaan</span>
                        </span>
                    </div>
                    <svg class="agreement-btn__arrow" width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7" stroke-width="2.5" stroke-linecap="round"/></svg>
                </button>

                @error('agreement_accepted')
                <div class="field-error" style="margin-top:-.5rem;margin-bottom:.75rem;">
                    <svg width="11" height="11" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>
                    {{ $message }}
                </div>
                @enderror

                <button type="submit"
                    :disabled="!agreed"
                    class="btn-submit"
                    :class="agreed ? 'btn-submit--active' : 'btn-submit--inactive'">
                    <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" stroke-width="2" stroke-linecap="round"/></svg>
                    Simpan & Lanjutkan
                </button>

            </form>
        </div>
    </div>

</div>{{-- /page --}}


{{-- ══════ MODAL ══════ --}}
<div x-show="agreementOpen" x-cloak
    class="modal-backdrop"
    @keydown.escape.window="agreementOpen = false"
    x-transition:enter="transition ease-out duration-180"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-140"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0">

    <div class="modal-card"
        @click.stop
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95 translate-y-2"
        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95">

        {{-- Header --}}
        <div class="modal-header">
            <div class="modal-header__left">
                <img src="/images/logo/logo-header.svg" alt="Logo"/>
                <div>
                    <div class="modal-header__title">Persetujuan Penggunaan Aplikasi</div>
                    <div class="modal-header__sub">RS Asa Bunda — Sistem Presensi</div>
                </div>
            </div>
            <button @click="agreementOpen = false" class="modal-close">
                <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="2.5" stroke-linecap="round"/></svg>
            </button>
        </div>

        {{-- Scrollable body --}}
        <div class="modal-body" @scroll="onScroll($el)">

            <div class="modal-intro">
                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" stroke-width="2"/></svg>
                Harap baca seluruh kebijakan ini dengan seksama. Dengan menggunakan sistem presensi RS Asa Bunda, Anda menyatakan telah membaca, memahami, dan menyetujui semua ketentuan yang berlaku.
            </div>

            {{-- 1 --}}
            <div class="section">
                <div class="section-head"><span class="section-num">1</span> Pendahuluan</div>
                <div class="section-body">
                    <p>Selamat datang di <strong>Sistem Presensi RS Asa Bunda</strong>. Sistem ini dirancang untuk memudahkan pengelolaan kehadiran karyawan secara digital, akurat, dan transparan menggunakan teknologi GPS dan verifikasi foto wajah secara langsung.</p>
                    <p>Persetujuan ini adalah perjanjian yang mengikat antara Anda sebagai pengguna terdaftar dengan RS Asa Bunda. Apabila Anda tidak menyetujui ketentuan ini, harap segera menghubungi unit SDM.</p>
                </div>
            </div>

            {{-- 2 --}}
            <div class="section">
                <div class="section-head"><span class="section-num">2</span> Kebijakan Privasi & Data</div>
                <div class="section-body">
                    <p>Kami hanya mengumpulkan data yang diperlukan untuk menjalankan fungsi presensi:</p>
                    <div class="check-list">
                        <div class="check-item"><svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7" stroke-width="2.5" stroke-linecap="round"/></svg><span><strong>Identitas</strong> — Nama lengkap, NIP, dan jabatan sesuai data kepegawaian.</span></div>
                        <div class="check-item"><svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7" stroke-width="2.5" stroke-linecap="round"/></svg><span><strong>Lokasi GPS</strong> — Koordinat direkam saat presensi untuk memvalidasi kehadiran dalam radius area kerja.</span></div>
                        <div class="check-item"><svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7" stroke-width="2.5" stroke-linecap="round"/></svg><span><strong>Foto Biometrik</strong> — Foto wajah diambil secara live saat clock-in dan clock-out sebagai bukti kehadiran.</span></div>
                    </div>
                    <div class="section-note">Data yang dikumpulkan <strong>tidak akan dijual, disewakan, atau dibagikan</strong> kepada pihak ketiga di luar kepentingan administratif RS Asa Bunda.</div>
                </div>
            </div>

            {{-- 3 --}}
            <div class="section">
                <div class="section-head"><span class="section-num">3</span> Pengelolaan & Penggunaan Data</div>
                <div class="section-body">
                    <p>Data Anda digunakan secara eksklusif untuk kepentingan operasional:</p>
                    <div class="mini-grid" style="margin-top:.625rem;">
                        <div class="mini-card"><div class="mini-card__title">Pelaporan Kehadiran</div><div class="mini-card__desc">Dasar perhitungan penggajian dan tunjangan bulanan.</div></div>
                        <div class="mini-card"><div class="mini-card__title">Kepatuhan Jam Kerja</div><div class="mini-card__desc">Memastikan pemenuhan standar jam kerja yang ditetapkan.</div></div>
                        <div class="mini-card"><div class="mini-card__title">Audit Internal</div><div class="mini-card__desc">Mendukung proses audit dan verifikasi manajemen.</div></div>
                        <div class="mini-card"><div class="mini-card__title">Retensi Data</div><div class="mini-card__desc">Disimpan selama masa hubungan kerja sesuai perundangan.</div></div>
                    </div>
                </div>
            </div>

            {{-- 4 --}}
            <div class="section">
                <div class="section-head"><span class="section-num">4</span> Ketentuan Presensi & Operasional</div>
                <div class="section-body">
                    <div class="mini-grid">
                        <div class="mini-card"><div class="mini-card__title">Hari Kerja</div><div class="mini-card__desc">Presensi hanya berlaku pada hari kerja unit Anda sesuai jadwal SDM.</div></div>
                        <div class="mini-card"><div class="mini-card__title">Jendela Presensi</div><div class="mini-card__desc">Aktif mulai 60 menit sebelum shift, ditolak otomatis setelah jam akhir shift.</div></div>
                        <div class="mini-card"><div class="mini-card__title">Absen Pulang</div><div class="mini-card__desc">Aktif selama 8 jam setelah sesi masuk, memberikan fleksibilitas tugas.</div></div>
                        <div class="mini-card"><div class="mini-card__title">Verifikasi GPS & Foto</div><div class="mini-card__desc">GPS aktif dan foto live wajib dilakukan di lokasi kerja dalam radius yang diizinkan.</div></div>
                    </div>
                </div>
            </div>

            {{-- 5 --}}
            <div class="section">
                <div class="section-head"><span class="section-num">5</span> Keamanan & Perlindungan Data</div>
                <div class="section-body">
                    <p>Seluruh data dilindungi menggunakan enkripsi standar industri. Akses dibatasi hanya kepada personel berwenang unit SDM dan IT RS Asa Bunda.</p>
                    <p style="margin-top:.625rem;">Anda bertanggung jawab atas kerahasiaan username dan password. Segera laporkan ke <strong>it.rsasabunda@gmail.com</strong> bila mencurigai akses tidak sah.</p>
                </div>
            </div>

            {{-- 6 --}}
            <div class="section">
                <div class="section-head"><span class="section-num">6</span> Kewajiban Pengguna</div>
                <div class="section-body">
                    <div class="check-list">
                        <div class="check-item"><svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7" stroke-width="2.5" stroke-linecap="round"/></svg><span>Melakukan presensi secara mandiri, jujur, dan sesuai kondisi kehadiran yang sebenarnya.</span></div>
                        <div class="check-item"><svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7" stroke-width="2.5" stroke-linecap="round"/></svg><span>Menjaga kerahasiaan kredensial akun dan tidak membagikannya kepada siapapun.</span></div>
                        <div class="check-item"><svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7" stroke-width="2.5" stroke-linecap="round"/></svg><span>Memastikan perangkat memiliki GPS aktif dan kamera yang berfungsi saat presensi.</span></div>
                        <div class="check-item"><svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7" stroke-width="2.5" stroke-linecap="round"/></svg><span>Segera melapor ke atasan atau SDM jika mengalami kendala teknis yang menghambat presensi.</span></div>
                    </div>
                </div>
            </div>

            {{-- 7 --}}
            <div class="section">
                <div class="section-head section-head--red"><span class="section-num section-num--red">7</span> Larangan & Sanksi</div>
                <div class="section-body">
                    <p class="warn-intro">Tindakan berikut dilarang keras dan dapat dikenakan sanksi disiplin serta ditindaklanjuti sesuai UU ITE No. 11 Tahun 2008:</p>
                    <div class="warn-list">
                        <div class="warn-item"><span class="warn-badge">!</span><span>Memanipulasi GPS, menggunakan VPN, atau memalsukan koordinat lokasi saat presensi.</span></div>
                        <div class="warn-item"><span class="warn-badge">!</span><span>Menggunakan foto orang lain atau foto rekaman sebagai pengganti foto live.</span></div>
                        <div class="warn-item"><span class="warn-badge">!</span><span>Membagikan akun atau melakukan presensi atas nama orang lain (titip absen).</span></div>
                        <div class="warn-item"><span class="warn-badge">!</span><span>Mengeksploitasi celah keamanan atau melakukan akses tidak sah ke sistem.</span></div>
                        <div class="warn-item"><span class="warn-badge">!</span><span>Menyebarkan atau menyalahgunakan data presensi milik karyawan lain.</span></div>
                    </div>
                </div>
            </div>

        </div>{{-- /modal-body --}}

        {{-- Scroll nudge --}}
        <div class="modal-nudge" x-show="!reachedEnd"
            x-transition:leave="transition duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0">
            <div class="modal-nudge__fade"></div>
            <div class="modal-nudge__bar">
                <svg class="nudge-bounce" width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19.5 8.25l-7.5 7.5-7.5-7.5" stroke-width="2.5" stroke-linecap="round"/></svg>
                Gulir ke bawah untuk membaca seluruh kebijakan
            </div>
        </div>

        {{-- Checkbox --}}
        <div class="modal-check">
            <label class="check-label">
                <input type="checkbox" x-model="modalChecked"/>
                <span>Saya telah membaca, memahami, dan menyetujui seluruh <strong>Syarat, Kebijakan, dan Ketentuan</strong> penggunaan Sistem Presensi RS Asa Bunda.</span>
            </label>
        </div>

        {{-- Footer --}}
        <div class="modal-footer">
            <button type="button" @click="agreementOpen = false" class="btn-modal-cancel">Tutup</button>
            <button type="button"
                :disabled="!modalChecked"
                @click="agreed = true; agreementOpen = false"
                class="btn-modal-agree"
                :class="modalChecked ? 'btn-modal-agree--active' : 'btn-modal-agree--inactive'">
                Saya Setuju
            </button>
        </div>

    </div>{{-- /modal-card --}}
</div>{{-- /modal-backdrop --}}

</div>{{-- /x-data --}}
@endsection