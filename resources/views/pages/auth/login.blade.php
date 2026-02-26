@extends('layouts.fullscreen-layout')
@section('title', 'Masuk')

@section('content')
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}

:root{
    --teal:  #2563eb;
    --teal2: #1d4ed8;
    --slate: #1e293b;
    --muted: #64748b;
}

html,body{
    height:100%;
    overflow:hidden;
}

body{
    font-family:'Instrument Sans',sans-serif;
    display:flex;
    align-items:center;
    justify-content:center;
    background:#f0ede8;
    position:relative;
    padding:16px;
}

/* Soft background */
body::before{
    content:'';
    position:fixed;inset:0;
    background:
        radial-gradient(ellipse 55% 55% at 15% 20%, rgba(37,99,235,.11) 0%, transparent 65%),
        radial-gradient(ellipse 60% 50% at 85% 75%, rgba(100,116,139,.10) 0%, transparent 60%),
        #f0ede8;
    pointer-events:none;
}

/* Main card */
.login-card{
    width:100%;
    max-width:400px;
    background:rgba(255,255,255,.75);
    backdrop-filter:blur(20px) saturate(160%);
    -webkit-backdrop-filter:blur(20px) saturate(160%);
    border-radius:32px;
    padding:2.25rem 2rem;
    box-shadow:
        0 0 0 1px rgba(15,23,42,.06),
        0 20px 40px -12px rgba(15,23,42,.25),
        0 4px 12px rgba(15,23,42,.05);
    transform:translateZ(0);
}

/* Logo di tengah - tanpa bulatan */
.logo-area{
    display:flex;
    flex-direction:column;
    align-items:center;
    justify-content:center;
    text-align:center;
    margin-bottom:2rem;
}

.logo-area img{
    height:55px;
    width:auto;
    margin-bottom:.75rem;
}

.logo-area h2{
    font-size:1.5rem;
    font-weight:600;
    color:var(--slate);
    letter-spacing:-.02em;
    margin-bottom:.15rem;
}

.logo-area .badge{
    font-size:.7rem;
    color:var(--teal);
    font-weight:600;
    text-transform:uppercase;
    letter-spacing:.08em;
    background:rgba(37,99,235,.1);
    padding:.25rem .75rem;
    border-radius:30px;
    display:inline-block;
}

/* Welcome text */
.welcome-text{
    text-align:center;
    margin-bottom:1.75rem;
}

.welcome-text p{
    font-size:.9rem;
    color:var(--muted);
    line-height:1.5;
}

/* Form elements */
.field{
    margin-bottom:1rem;
}

.field label{
    display:block;
    font-size:.7rem;
    font-weight:600;
    color:rgba(30,41,59,.5);
    text-transform:uppercase;
    letter-spacing:.04em;
    margin-bottom:.35rem;
}

.input-shell{
    position:relative;
}

.input-shell .ico{
    position:absolute;
    left:14px;
    top:50%;
    transform:translateY(-50%);
    color:rgba(30,41,59,.3);
    display:flex;
    pointer-events:none;
}

.input-shell input{
    width:100%;
    height:50px;
    background:rgba(255,255,255,.8);
    border:1px solid rgba(15,23,42,.12);
    border-radius:18px;
    font-size:.95rem;
    color:var(--slate);
    padding:0 44px;
    outline:none;
    transition:all .15s;
}

.input-shell input:focus{
    border-color:rgba(37,99,235,.5);
    background:#fff;
    box-shadow:0 0 0 4px rgba(37,99,235,.08);
}

.input-shell input::placeholder{
    color:rgba(30,41,59,.25);
}

.eye-btn{
    position:absolute;
    right:14px;
    top:50%;
    transform:translateY(-50%);
    background:none;
    border:none;
    color:rgba(30,41,59,.3);
    cursor:pointer;
    display:flex;
    padding:6px;
    border-radius:50%;
    transition:all .15s;
}

.eye-btn:hover{
    background:rgba(30,41,59,.05);
    color:rgba(30,41,59,.6);
}

/* Checkbox row */
.row-check{
    display:flex;
    align-items:center;
    justify-content:space-between;
    margin:1.25rem 0 1.5rem;
}

.check-label{
    display:flex;
    align-items:center;
    gap:8px;
    cursor:pointer;
}

.check-label input[type=checkbox]{
    width:18px;
    height:18px;
    border-radius:6px;
    border:1.5px solid rgba(15,23,42,.2);
    background:rgba(255,255,255,.8);
    appearance:none;
    cursor:pointer;
    transition:all .12s;
}

.check-label input[type=checkbox]:checked{
    background:var(--teal);
    border-color:var(--teal);
    background-image:url("data:image/svg+xml,%3Csvg viewBox='0 0 12 12' fill='none' stroke='white' stroke-width='2.5' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M2 6l3 3 5-5'/%3E%3C/svg%3E");
    background-size:10px;
    background-position:center;
    background-repeat:no-repeat;
}

.check-label span{
    font-size:.85rem;
    color:var(--muted);
    font-weight:500;
}

.forgot-link{
    font-size:.8rem;
    color:var(--teal);
    text-decoration:none;
    font-weight:600;
    padding:.3rem 0;
}

.forgot-link:hover{
    text-decoration:underline;
}

/* Submit button */
.btn-submit{
    width:100%;
    height:50px;
    background:var(--teal);
    border:none;
    border-radius:18px;
    color:white;
    font-size:.95rem;
    font-weight:600;
    cursor:pointer;
    box-shadow:0 6px 14px rgba(37,99,235,.35);
    transition:all .2s;
    margin-bottom:1.5rem;
    letter-spacing:.3px;
}

.btn-submit:hover{
    background:var(--teal2);
    transform:translateY(-1px);
    box-shadow:0 8px 18px rgba(37,99,235,.45);
}

.btn-submit:active{
    transform:translateY(0);
    box-shadow:0 4px 10px rgba(37,99,235,.3);
}

/* Footer */
.form-footer{
    text-align:center;
    font-size:.75rem;
    color:rgba(30,41,59,.4);
    display:flex;
    align-items:center;
    justify-content:center;
    gap:6px;
}

.form-footer .dot{
    width:3px;
    height:3px;
    background:rgba(30,41,59,.2);
    border-radius:50%;
    display:inline-block;
}

.form-footer a{
    color:var(--teal);
    text-decoration:none;
    font-weight:500;
}

.form-footer a:hover{
    text-decoration:underline;
}

/* Error message */
.field-error{
    font-size:.7rem;
    color:#ef4444;
    margin-top:.25rem;
    font-weight:500;
    margin-left:6px;
}

/* Mobile */
@media(max-width:480px){
    .login-card{
        padding:1.75rem 1.5rem;
        border-radius:28px;
    }
    
    .logo-area img{
        height:48px;
    }
    
    .logo-area h2{
        font-size:1.4rem;
    }
    
    .welcome-text{
        margin-bottom:1.5rem;
    }
    
    .input-shell input{
        height:48px;
    }
    
    .btn-submit{
        height:48px;
    }
}

/* Very small phones */
@media(max-width:360px){
    .login-card{
        padding:1.5rem 1.25rem;
    }
    
    .logo-area img{
        height:44px;
    }
    
    .logo-area h2{
        font-size:1.3rem;
    }
    
    .row-check{
        flex-direction:column;
        align-items:flex-start;
        gap:10px;
    }
}
</style>

<div class="login-card">
    
    <div class="logo-area">
        <img src="/images/logo/logo-header.svg" alt="RS Asa Bunda"/>
        <span class="badge">E-Presensi</span>
    </div>
    
    <div class="welcome-text">
        <p>Masukkan NIP dan password Anda</p>
    </div>
    
    <form method="POST" action="{{ route('login') }}">
        @csrf
        
        @if(session('error'))
            <div style="background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.2); color: #ef4444; padding: 12px 16px; border-radius: 14px; font-size: 0.85rem; margin-bottom: 1.5rem; line-height: 1.4; text-align: center;">
                {{ session('error') }}
            </div>
        @endif

        <div class="field">
            <label for="nip">NIP / NIK</label>
            <div class="input-shell">
                <span class="ico">
                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </span>
                <input type="text" id="nip" name="nip" value="{{ old('nip') }}" placeholder="Contoh: 0001" required autofocus/>
            </div>
            @error('nip')
                <div class="field-error">{{ $message }}</div>
            @enderror
        </div>
        
        <div class="field">
            <label for="password">Password</label>
            <div class="input-shell" x-data="{ show: false }">
                <span class="ico">
                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                </span>
                <input :type="show ? 'text' : 'password'" id="password" name="password" placeholder="••••••••" required/>
                <button type="button" class="eye-btn" @click="show = !show" tabindex="-1">
                    <svg x-show="!show" width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    <svg x-show="show" width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display:none">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.917 9.917 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                    </svg>
                </button>
            </div>
            @error('password')
                <div class="field-error">{{ $message }}</div>
            @enderror
        </div>
        
        <div class="row-check">
            <label class="check-label">
                <input type="checkbox" name="remember" id="remember"/>
                <span>Ingat saya</span>
            </label>
            <!-- <a href="#" class="forgot-link">Lupa password?</a> -->
        </div>
        
        <button type="submit" class="btn-submit">
            Masuk ke Aplikasi
        </button>
        
        <div class="form-footer">
            <span>&copy; {{ date('Y') }}</span>
            <span class="dot"></span>
            <span>RS Asa Bunda</span>
            <span class="dot"></span>
            <a href="{{ route('legal.terms') }}">Kebijakan</a>
        </div>
    </form>
</div>

<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
<script>
    setInterval(async () => {
        try {
            const response = await fetch('{{ url("/csrf-token") }}');
            if (response.ok) {
                const data = await response.json();
                if (data.token) {
                    document.querySelectorAll('input[name="_token"]').forEach(input => {
                        input.value = data.token;
                    });
                }
                console.log('Session heartbeat success');
            }
        } catch (error) {
            console.error('Heartbeat failed', error);
        }
    }, 5 * 60 * 1000);
</script>
@endsection