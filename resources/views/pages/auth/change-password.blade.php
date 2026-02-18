@extends('layouts.fullscreen-layout')
@section('title', 'Ganti Password')

@section('content')
<div
    x-data="{
        agreementOpen: {{ old('agreement_accepted') ? 'false' : 'true' }},
        agreed:        {{ old('agreement_accepted') ? 'true'  : 'false' }},
        modalChecked:  {{ old('agreement_accepted') ? 'true'  : 'false' }},
        reachedEnd: false,
        onScroll(el) {
            if (el.scrollHeight - el.scrollTop - el.clientHeight < 60) {
                this.reachedEnd = true;
            }
        }
    }"
    class="relative bg-white dark:bg-gray-900"
>

    {{-- ═══════════════════════════════════════════════
         MAIN LAYOUT
    ═══════════════════════════════════════════════ --}}
    <div class="flex min-h-screen w-full flex-col lg:flex-row">

        {{-- ── Left: Form ── --}}
        <div class="flex w-full flex-1 flex-col lg:w-1/2">
            <div class="mx-auto flex w-full max-w-md flex-1 flex-col justify-center px-6 py-10">

                {{-- Badge --}}
                <div class="mb-5 inline-flex w-fit items-center gap-2 rounded-full bg-brand-50 px-3 py-1.5 text-xs font-semibold text-brand-600 dark:bg-brand-900/30 dark:text-brand-400">
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/>
                    </svg>
                    Langkah Wajib
                </div>

                {{-- Heading --}}
                <h1 class="mb-2 text-3xl font-bold text-gray-800 dark:text-white/90">Buat Password Baru</h1>
                <p class="mb-8 text-sm font-medium leading-relaxed text-gray-500 dark:text-gray-400">
                    Demi keamanan akun Anda, silakan ganti password bawaan sebelum dapat menggunakan aplikasi.
                </p>

                {{-- Warning --}}
                @if(session('warning'))
                <div class="mb-6 flex items-start gap-3 rounded-xl border border-yellow-200 bg-yellow-50 p-4 dark:border-yellow-700/40 dark:bg-yellow-900/20">
                    <svg class="mt-0.5 h-5 w-5 shrink-0 text-yellow-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>
                    </svg>
                    <p class="text-sm font-medium text-yellow-800 dark:text-yellow-300">{{ session('warning') }}</p>
                </div>
                @endif

                {{-- Form --}}
                <form method="POST" action="{{ route('password.update') }}">
                    @csrf
                    <div class="space-y-5">

                        {{-- Current Password --}}
                        <div>
                            <label class="mb-1.5 block text-sm font-semibold text-gray-700 dark:text-gray-300">
                                Password Saat Ini
                            </label>
                            <input type="password" name="current_password" required autofocus
                                placeholder="Masukkan password lama Anda"
                                class="h-12 w-full rounded-xl border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-sm placeholder:text-gray-400 transition-all focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 dark:border-gray-700 dark:bg-gray-800 dark:text-white/90 dark:placeholder:text-gray-500 @error('current_password') border-red-400 focus:border-red-400 focus:ring-red-400/20 @enderror" />
                            @error('current_password')
                            <p class="mt-1.5 flex items-center gap-1 text-xs font-medium text-red-500">
                                <svg class="h-3.5 w-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg>
                                {{ $message }}
                            </p>
                            @enderror
                        </div>

                        {{-- New Password --}}
                        <div>
                            <label class="mb-1.5 block text-sm font-semibold text-gray-700 dark:text-gray-300">
                                Password Baru
                            </label>
                            <input type="password" name="password" required
                                placeholder="Min. 8 karakter"
                                class="h-12 w-full rounded-xl border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-sm placeholder:text-gray-400 transition-all focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 dark:border-gray-700 dark:bg-gray-800 dark:text-white/90 dark:placeholder:text-gray-500 @error('password') border-red-400 focus:border-red-400 focus:ring-red-400/20 @enderror" />
                            @error('password')
                            <p class="mt-1.5 flex items-center gap-1 text-xs font-medium text-red-500">
                                <svg class="h-3.5 w-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg>
                                {{ $message }}
                            </p>
                            @enderror
                        </div>

                        {{-- Confirm Password --}}
                        <div>
                            <label class="mb-1.5 block text-sm font-semibold text-gray-700 dark:text-gray-300">
                                Konfirmasi Password Baru
                            </label>
                            <input type="password" name="password_confirmation" required
                                placeholder="Ulangi password baru Anda"
                                class="h-12 w-full rounded-xl border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-sm placeholder:text-gray-400 transition-all focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 dark:border-gray-700 dark:bg-gray-800 dark:text-white/90 dark:placeholder:text-gray-500" />
                        </div>

                        {{-- Agreement button --}}
                        <div>
                            <input type="hidden" name="agreement_accepted" :value="agreed ? 1 : ''">
                            <button type="button"
                                @click="modalChecked = agreed; reachedEnd = false; agreementOpen = true"
                                class="group w-full rounded-xl border px-4 py-3 text-left text-sm transition-all"
                                :class="agreed
                                    ? 'border-green-300 bg-green-50 dark:border-green-700/50 dark:bg-green-900/20'
                                    : 'border-gray-300 bg-white hover:border-brand-400 dark:border-gray-700 dark:bg-gray-800 dark:hover:border-brand-500'">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2.5">
                                        <span x-show="agreed" class="flex h-5 w-5 items-center justify-center rounded-full bg-green-500">
                                            <svg class="h-3 w-3 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                                        </span>
                                        <span x-show="!agreed" class="flex h-5 w-5 items-center justify-center rounded-full border-2 border-gray-300 dark:border-gray-600">
                                            <svg class="h-3 w-3 text-gray-400 dark:text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 12h-15"/></svg>
                                        </span>
                                        <span class="font-semibold"
                                            :class="agreed ? 'text-green-700 dark:text-green-400' : 'text-gray-700 dark:text-gray-200'">
                                            <span x-show="agreed">Kebijakan Telah Disetujui</span>
                                            <span x-show="!agreed">Baca & Setujui Kebijakan Penggunaan</span>
                                        </span>
                                    </div>
                                    <svg class="h-4 w-4 text-gray-400 transition-transform group-hover:translate-x-0.5 dark:text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
                                </div>
                            </button>
                            @error('agreement_accepted')
                            <p class="mt-1.5 flex items-center gap-1 text-xs font-medium text-red-500">
                                <svg class="h-3.5 w-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg>
                                {{ $message }}
                            </p>
                            @enderror
                        </div>

                        {{-- Submit --}}
                        <button type="submit"
                            :disabled="!agreed"
                            :class="!agreed ? 'cursor-not-allowed opacity-50 grayscale' : 'hover:bg-brand-600 hover:shadow-brand-500/40 active:scale-[0.98]'"
                            class="flex w-full items-center justify-center gap-2 rounded-xl bg-brand-500 py-3.5 font-bold text-white shadow-lg shadow-brand-500/30 transition-all">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/>
                            </svg>
                            Simpan & Lanjutkan
                        </button>

                    </div>
                </form>

            </div>
        </div>

        {{-- ── Right: Visual Panel ── --}}
        <div class="relative hidden h-screen w-1/2 flex-col items-center justify-center overflow-hidden bg-brand-600 lg:flex dark:bg-gray-800">
            <div class="absolute inset-0 z-0">
                <x-common.common-grid-shape/>
            </div>
            <div class="relative z-10 mx-auto max-w-sm px-8 text-center text-white">
                <div class="mb-8 flex justify-center">
                    <img class="h-20 w-auto" src="/images/logo/logo-header.svg" alt="Logo">
                </div>
                <h2 class="mb-3 text-2xl font-bold">Keamanan Akun Anda</h2>
                <p class="mb-8 text-sm leading-relaxed text-white/75">
                    Lindungi akun Anda dengan password yang kuat. Jangan bagikan password kepada siapapun.
                </p>
                <div class="space-y-2.5 text-left">
                    <div class="flex items-center gap-3 rounded-xl bg-white/10 px-4 py-2.5 backdrop-blur-sm">
                        <svg class="h-4 w-4 shrink-0 text-green-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                        <p class="text-sm font-medium text-white/90">Gunakan minimal 8 karakter</p>
                    </div>
                    <div class="flex items-center gap-3 rounded-xl bg-white/10 px-4 py-2.5 backdrop-blur-sm">
                        <svg class="h-4 w-4 shrink-0 text-green-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                        <p class="text-sm font-medium text-white/90">Kombinasikan huruf, angka & simbol</p>
                    </div>
                    <div class="flex items-center gap-3 rounded-xl bg-white/10 px-4 py-2.5 backdrop-blur-sm">
                        <svg class="h-4 w-4 shrink-0 text-green-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                        <p class="text-sm font-medium text-white/90">Hindari tanggal lahir atau nama pribadi</p>
                    </div>
                </div>
            </div>
        </div>

    </div>{{-- /main layout --}}


    {{-- ═══════════════════════════════════════════════
         AGREEMENT MODAL
         Struktur (semua fixed-height, flex column):
           [header]          ← shrink-0
           [scroll body]     ← flex-1, overflow-y-auto  ← INI yang scroll
           [scroll hint]     ← shrink-0, hilang setelah user sampai bawah
           [checkbox footer] ← shrink-0
           [action buttons]  ← shrink-0
    ═══════════════════════════════════════════════ --}}
    <div
        x-show="agreementOpen"
        x-cloak
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 flex items-center justify-center p-4"
        style="background: rgba(3,7,18,.65); backdrop-filter: blur(6px);"
        @keydown.escape.window="agreementOpen = false"
    >
        <div
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95 translate-y-3"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 scale-100 translate-y-0"
            x-transition:leave-end="opacity-0 scale-95 translate-y-3"
            @click.stop
            class="modal-card flex w-full flex-col overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-2xl dark:border-gray-700 dark:bg-gray-900"
            style="max-width: 680px; height: min(88vh, 680px);"
        >

            {{-- ── Fixed: Header ── --}}
            <div class="flex shrink-0 items-center justify-between border-b border-gray-100 bg-white px-6 py-4 dark:border-gray-800 dark:bg-gray-900">
                <div class="flex items-center gap-3">
                    <img class="h-8 w-auto dark:brightness-0 dark:invert" src="/images/logo/logo-title.svg" alt="Logo" />
                    <div>
                        <p class="text-sm font-bold text-gray-900 dark:text-white">Persetujuan Penggunaan Aplikasi</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">RS ASA BUNDA — Sistem Presensi</p>
                    </div>
                </div>
                <button @click="agreementOpen = false"
                    class="flex h-8 w-8 items-center justify-center rounded-lg text-gray-400 transition hover:bg-gray-100 hover:text-gray-600 dark:hover:bg-gray-800 dark:hover:text-gray-200">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            {{-- ── Flex-1: Scrollable body ── --}}
            <div
                class="modal-scroll min-h-0 flex-1 overflow-y-auto overscroll-contain bg-white px-6 py-5 dark:bg-gray-900"
                @scroll="onScroll($el)"
            >

                {{-- Intro notice --}}
                <div class="mb-5 rounded-xl border border-indigo-200 bg-indigo-50 p-4 dark:border-indigo-500/30 dark:bg-slate-800">
                    <div class="flex items-start gap-3">
                        <svg class="mt-0.5 h-4 w-4 shrink-0 text-indigo-500 dark:text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z"/></svg>
                        <p class="text-[13px] font-bold leading-relaxed text-indigo-800 dark:text-gray-100">
                            Harap baca seluruh kebijakan ini dengan seksama. Dengan menggunakan Sistem Presensi RS ASA BUNDA, Anda menyatakan telah membaca, memahami, dan menyetujui semua ketentuan yang berlaku.
                        </p>
                    </div>
                </div>

                {{-- ── 1. Pendahuluan ── --}}
                <div class="mb-5">
                    <h4 class="mb-2.5 flex items-center gap-2 text-[11px] font-black uppercase tracking-wider text-gray-900 dark:text-white">
                        <span class="flex h-5 w-5 shrink-0 items-center justify-center rounded-md bg-indigo-600 text-[10px] font-bold text-white">1</span>
                        Pendahuluan
                    </h4>
                    <div class="rounded-xl border border-gray-100 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-800">
                        <p class="text-[13px] font-medium leading-relaxed text-gray-700 dark:text-gray-300">
                            Selamat datang di <strong class="text-gray-900 dark:text-white">Sistem Presensi RS ASA BUNDA</strong>. Sistem ini dirancang untuk memudahkan pengelolaan kehadiran karyawan secara digital, akurat, dan transparan menggunakan teknologi GPS dan verifikasi foto wajah secara langsung (<em>live biometric</em>).
                        </p>
                        <p class="mt-2.5 text-[13px] font-medium leading-relaxed text-gray-700 dark:text-gray-300">
                            Persetujuan ini adalah perjanjian yang mengikat secara hukum antara Anda sebagai pengguna terdaftar dengan RS ASA BUNDA selaku pengelola sistem. Apabila Anda <strong class="text-gray-900 dark:text-white">tidak menyetujui</strong> ketentuan ini, harap segera menghubungi Divisi SDM untuk mendapatkan panduan lebih lanjut.
                        </p>
                    </div>
                </div>

                {{-- ── 2. Kebijakan Privasi ── --}}
                <div class="mb-5">
                    <h4 class="mb-2.5 flex items-center gap-2 text-[11px] font-black uppercase tracking-wider text-gray-900 dark:text-white">
                        <span class="flex h-5 w-5 shrink-0 items-center justify-center rounded-md bg-indigo-600 text-[10px] font-bold text-white">2</span>
                        Kebijakan Privasi & Data
                    </h4>
                    <div class="rounded-xl border border-gray-100 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-800">
                        <p class="mb-3 text-[13px] font-medium text-gray-700 dark:text-gray-300">
                            Kami hanya mengumpulkan data yang benar-benar diperlukan untuk menjalankan fungsi presensi dan administrasi kepegawaian:
                        </p>
                        <ul class="space-y-3">
                            <li class="flex items-start gap-3">
                                <svg class="mt-0.5 h-4 w-4 shrink-0 text-indigo-500 dark:text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                <span class="text-[13px] text-gray-700 dark:text-gray-300"><strong class="text-gray-900 dark:text-white">Identitas:</strong> Nama lengkap, NIK, dan jabatan resmi Anda sesuai data kepegawaian yang berlaku.</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <svg class="mt-0.5 h-4 w-4 shrink-0 text-indigo-500 dark:text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                <span class="text-[13px] text-gray-700 dark:text-gray-300"><strong class="text-gray-900 dark:text-white">Lokasi GPS:</strong> Koordinat perangkat direkam saat presensi untuk memvalidasi kehadiran dalam radius area kerja yang ditetapkan manajemen.</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <svg class="mt-0.5 h-4 w-4 shrink-0 text-indigo-500 dark:text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                <span class="text-[13px] text-gray-700 dark:text-gray-300"><strong class="text-gray-900 dark:text-white">Foto Biometrik:</strong> Foto wajah Anda diambil secara langsung (live) saat clock-in maupun clock-out sebagai bukti kehadiran fisik yang sah.</span>
                            </li>
                        </ul>
                        {{-- Note box --}}
                        <div class="mt-3 rounded-lg border border-gray-200 bg-white px-3.5 py-2.5 dark:border-gray-600 dark:bg-gray-700">
                            <p class="text-[12px] font-medium text-gray-500 dark:text-gray-300">
                                Data yang dikumpulkan <strong class="text-gray-800 dark:text-white">tidak akan dijual, disewakan, atau dibagikan</strong> kepada pihak ketiga di luar kepentingan administratif RS ASA BUNDA.
                            </p>
                        </div>
                    </div>
                </div>

                {{-- ── 3. Pengelolaan Data ── --}}
                <div class="mb-5">
                    <h4 class="mb-2.5 flex items-center gap-2 text-[11px] font-black uppercase tracking-wider text-gray-900 dark:text-white">
                        <span class="flex h-5 w-5 shrink-0 items-center justify-center rounded-md bg-indigo-600 text-[10px] font-bold text-white">3</span>
                        Pengelolaan & Penggunaan Data
                    </h4>
                    <div class="rounded-xl border border-gray-100 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-800">
                        <p class="mb-3 text-[13px] font-medium text-gray-700 dark:text-gray-300">Data Anda digunakan secara eksklusif untuk kepentingan operasional RS ASA BUNDA, meliputi:</p>
                        <div class="grid grid-cols-1 gap-2 sm:grid-cols-2">
                            <div class="rounded-lg border border-gray-200 bg-white p-3 dark:border-gray-600 dark:bg-gray-700">
                                <p class="text-[11px] font-bold uppercase tracking-wider text-indigo-600 dark:text-indigo-400">Pelaporan Kehadiran</p>
                                <p class="mt-1 text-[12px] leading-relaxed text-gray-600 dark:text-gray-300">Laporan kehadiran bulanan sebagai dasar perhitungan penggajian dan tunjangan.</p>
                            </div>
                            <div class="rounded-lg border border-gray-200 bg-white p-3 dark:border-gray-600 dark:bg-gray-700">
                                <p class="text-[11px] font-bold uppercase tracking-wider text-indigo-600 dark:text-indigo-400">Kepatuhan Jam Kerja</p>
                                <p class="mt-1 text-[12px] leading-relaxed text-gray-600 dark:text-gray-300">Memastikan pemenuhan standar jam kerja sesuai ketentuan RS ASA BUNDA.</p>
                            </div>
                            <div class="rounded-lg border border-gray-200 bg-white p-3 dark:border-gray-600 dark:bg-gray-700">
                                <p class="text-[11px] font-bold uppercase tracking-wider text-indigo-600 dark:text-indigo-400">Audit Internal</p>
                                <p class="mt-1 text-[12px] leading-relaxed text-gray-600 dark:text-gray-300">Mendukung proses audit dan verifikasi yang dilakukan manajemen atau pihak berwenang.</p>
                            </div>
                            <div class="rounded-lg border border-gray-200 bg-white p-3 dark:border-gray-600 dark:bg-gray-700">
                                <p class="text-[11px] font-bold uppercase tracking-wider text-indigo-600 dark:text-indigo-400">Retensi Data</p>
                                <p class="mt-1 text-[12px] leading-relaxed text-gray-600 dark:text-gray-300">Data disimpan selama masa hubungan kerja sesuai ketentuan perundang-undangan yang berlaku.</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ── 4. Ketentuan Presensi ── --}}
                <div class="mb-5">
                    <h4 class="mb-2.5 flex items-center gap-2 text-[11px] font-black uppercase tracking-wider text-gray-900 dark:text-white">
                        <span class="flex h-5 w-5 shrink-0 items-center justify-center rounded-md bg-indigo-600 text-[10px] font-bold text-white">4</span>
                        Ketentuan Presensi & Operasional
                    </h4>
                    <div class="rounded-xl border border-gray-100 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-800">
                        <div class="grid grid-cols-1 gap-2 sm:grid-cols-2">
                            <div class="rounded-lg border border-gray-200 bg-white p-3 dark:border-gray-600 dark:bg-gray-700">
                                <p class="text-[11px] font-bold uppercase tracking-wider text-indigo-600 dark:text-indigo-400">Hari Kerja</p>
                                <p class="mt-1 text-[12px] leading-relaxed text-gray-600 dark:text-gray-300">Presensi masuk hanya berlaku pada hari kerja unit Anda sesuai jadwal yang ditetapkan oleh Divisi SDM.</p>
                            </div>
                            <div class="rounded-lg border border-gray-200 bg-white p-3 dark:border-gray-600 dark:bg-gray-700">
                                <p class="text-[11px] font-bold uppercase tracking-wider text-indigo-600 dark:text-indigo-400">Jendela Presensi</p>
                                <p class="mt-1 text-[12px] leading-relaxed text-gray-600 dark:text-gray-300">Absen masuk aktif mulai <strong class="text-gray-800 dark:text-gray-200">60 menit sebelum shift</strong> dan ditolak otomatis setelah jam akhir shift berlalu.</p>
                            </div>
                            <div class="rounded-lg border border-gray-200 bg-white p-3 dark:border-gray-600 dark:bg-gray-700">
                                <p class="text-[11px] font-bold uppercase tracking-wider text-indigo-600 dark:text-indigo-400">Shift Malam</p>
                                <p class="mt-1 text-[12px] leading-relaxed text-gray-600 dark:text-gray-300">Data presensi shift malam dicatat pada tanggal kerja yang logis untuk mencegah konflik data antar hari.</p>
                            </div>
                            <div class="rounded-lg border border-gray-200 bg-white p-3 dark:border-gray-600 dark:bg-gray-700">
                                <p class="text-[11px] font-bold uppercase tracking-wider text-indigo-600 dark:text-indigo-400">Absen Pulang</p>
                                <p class="mt-1 text-[12px] leading-relaxed text-gray-600 dark:text-gray-300">Absen pulang aktif selama sesi masuk Anda masih terbuka, memberikan fleksibilitas penyelesaian tugas.</p>
                            </div>
                            <div class="rounded-lg border border-gray-200 bg-white p-3 dark:border-gray-600 dark:bg-gray-700">
                                <p class="text-[11px] font-bold uppercase tracking-wider text-indigo-600 dark:text-indigo-400">Verifikasi GPS & Foto</p>
                                <p class="mt-1 text-[12px] leading-relaxed text-gray-600 dark:text-gray-300">GPS aktif dan foto wajah live wajib dilakukan di lokasi kerja dalam radius yang diizinkan manajemen.</p>
                            </div>
                            <div class="rounded-lg border border-gray-200 bg-white p-3 dark:border-gray-600 dark:bg-gray-700">
                                <p class="text-[11px] font-bold uppercase tracking-wider text-indigo-600 dark:text-indigo-400">Riwayat Presensi</p>
                                <p class="mt-1 text-[12px] leading-relaxed text-gray-600 dark:text-gray-300">Seluruh aktivitas presensi tercatat dan dapat dipantau secara transparan melalui menu Riwayat di aplikasi.</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ── 5. Keamanan ── --}}
                <div class="mb-5">
                    <h4 class="mb-2.5 flex items-center gap-2 text-[11px] font-black uppercase tracking-wider text-gray-900 dark:text-white">
                        <span class="flex h-5 w-5 shrink-0 items-center justify-center rounded-md bg-indigo-600 text-[10px] font-bold text-white">5</span>
                        Keamanan & Perlindungan Data
                    </h4>
                    <div class="space-y-2.5 rounded-xl border border-gray-100 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-800">
                        <p class="text-[13px] font-medium leading-relaxed text-gray-700 dark:text-gray-300">
                            Seluruh data yang tersimpan dalam sistem kami dilindungi menggunakan enkripsi standar industri. Akses terhadap data sensitif dibatasi secara ketat hanya kepada personel berwenang, yaitu Divisi SDM dan IT RS ASA BUNDA.
                        </p>
                        <p class="text-[13px] font-medium leading-relaxed text-gray-700 dark:text-gray-300">
                            Anda bertanggung jawab penuh atas kerahasiaan <strong class="text-gray-900 dark:text-white">username dan password</strong> akun Anda. Segera laporkan kepada tim IT melalui <span class="font-semibold text-indigo-600 dark:text-indigo-400">it-support@rsasabunda.co.id</span> apabila mencurigai adanya akses tidak sah.
                        </p>
                    </div>
                </div>

                {{-- ── 6. Kewajiban Pengguna ── --}}
                <div class="mb-5">
                    <h4 class="mb-2.5 flex items-center gap-2 text-[11px] font-black uppercase tracking-wider text-gray-900 dark:text-white">
                        <span class="flex h-5 w-5 shrink-0 items-center justify-center rounded-md bg-indigo-600 text-[10px] font-bold text-white">6</span>
                        Kewajiban Pengguna
                    </h4>
                    <div class="rounded-xl border border-gray-100 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-800">
                        <ul class="space-y-2.5">
                            <li class="flex items-start gap-3">
                                <svg class="mt-0.5 h-4 w-4 shrink-0 text-indigo-500 dark:text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                <span class="text-[13px] text-gray-700 dark:text-gray-300">Melakukan presensi secara mandiri, jujur, dan sesuai kondisi kehadiran yang sebenarnya.</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <svg class="mt-0.5 h-4 w-4 shrink-0 text-indigo-500 dark:text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                <span class="text-[13px] text-gray-700 dark:text-gray-300">Menjaga kerahasiaan kredensial akun (username & password) dan tidak membagikannya kepada siapapun.</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <svg class="mt-0.5 h-4 w-4 shrink-0 text-indigo-500 dark:text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                <span class="text-[13px] text-gray-700 dark:text-gray-300">Memastikan perangkat memiliki GPS aktif dan kamera yang berfungsi baik saat melakukan presensi.</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <svg class="mt-0.5 h-4 w-4 shrink-0 text-indigo-500 dark:text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                <span class="text-[13px] text-gray-700 dark:text-gray-300">Segera melapor kepada atasan atau Divisi SDM jika mengalami kendala teknis yang menghambat presensi.</span>
                            </li>
                        </ul>
                    </div>
                </div>

                {{-- ── 7. Larangan & Sanksi ── --}}
                <div class="mb-5">
                    <h4 class="mb-2.5 flex items-center gap-2 text-[11px] font-black uppercase tracking-wider text-red-600 dark:text-red-400">
                        <span class="flex h-5 w-5 shrink-0 items-center justify-center rounded-md bg-red-600 text-[10px] font-bold text-white">7</span>
                        Larangan & Sanksi
                    </h4>
                    <div class="rounded-xl border border-gray-100 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-800">
                        <p class="mb-3 text-[14px] font-bold leading-relaxed text-red-600 dark:text-red-400">
                            Tindakan berikut dilarang keras dan dapat dikenakan sanksi disiplin serta ditindaklanjuti secara hukum sesuai UU ITE No. 11 Tahun 2008:
                        </p>
                        <ul class="space-y-3">
                            <li class="flex items-start gap-3">
                                <div class="mt-1 flex h-4 w-4 shrink-0 items-center justify-center rounded-full bg-red-600 text-[10px] font-bold text-white">!</div>
                                <span class="text-[13px] font-bold text-red-700 dark:text-red-300">Memanipulasi GPS, menggunakan VPN, atau memalsukan koordinat lokasi saat presensi.</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <div class="mt-1 flex h-4 w-4 shrink-0 items-center justify-center rounded-full bg-red-600 text-[10px] font-bold text-white">!</div>
                                <span class="text-[13px] font-bold text-red-700 dark:text-red-300">Menggunakan foto orang lain atau foto rekaman sebagai pengganti foto live saat presensi.</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <div class="mt-1 flex h-4 w-4 shrink-0 items-center justify-center rounded-full bg-red-600 text-[10px] font-bold text-white">!</div>
                                <span class="text-[13px] font-bold text-red-700 dark:text-red-300">Membagikan akun atau melakukan presensi atas nama orang lain (titip absen).</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <div class="mt-1 flex h-4 w-4 shrink-0 items-center justify-center rounded-full bg-red-600 text-[10px] font-bold text-white">!</div>
                                <span class="text-[13px] font-bold text-red-700 dark:text-red-300">Mengeksploitasi celah keamanan atau melakukan akses tidak sah ke sistem.</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <div class="mt-1 flex h-4 w-4 shrink-0 items-center justify-center rounded-full bg-red-600 text-[10px] font-bold text-white">!</div>
                                <span class="text-[13px] font-bold text-red-700 dark:text-red-300">Menyebarkan atau menyalahgunakan data presensi milik karyawan lain.</span>
                            </li>
                        </ul>
                    </div>
                </div>

            </div>{{-- /scroll body --}}

            {{-- ── Fixed: Scroll-nudge (disappears at bottom) ── --}}
            <div x-show="!reachedEnd" x-transition:leave="transition duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                class="relative shrink-0">
                <div class="pointer-events-none absolute bottom-full left-0 right-0 h-10 bg-gradient-to-t from-white to-transparent dark:from-gray-900 dark:to-transparent"></div>
                <div class="flex items-center justify-center gap-1.5 border-t border-gray-100 bg-gray-50 py-2 dark:border-gray-800 dark:bg-gray-950">
                    <svg class="h-3.5 w-3.5 animate-bounce text-gray-400 dark:text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/></svg>
                    <span class="text-[11px] font-medium text-gray-400 dark:text-gray-500">Gulir ke bawah untuk membaca seluruh kebijakan</span>
                </div>
            </div>

            {{-- ── Fixed: Checkbox ── --}}
            <div class="shrink-0 border-t border-gray-100 bg-gray-50 px-6 py-3.5 dark:border-gray-700 dark:bg-gray-800">
                <label class="flex cursor-pointer items-start gap-3 group">
                    <input type="checkbox" x-model="modalChecked"
                        class="mt-0.5 h-4 w-4 rounded border-gray-300 text-brand-500 focus:ring-brand-500 dark:border-gray-600 dark:bg-gray-700 dark:checked:bg-brand-500 transition-colors">
                    <span class="text-[13px] font-semibold leading-snug text-gray-700 transition-colors group-hover:text-brand-600 dark:text-gray-300 dark:group-hover:text-brand-400">
                        Saya telah membaca, memahami, dan menyetujui seluruh
                        <span class="underline decoration-brand-500/40 underline-offset-2">Syarat, Kebijakan, dan Ketentuan</span>
                        penggunaan Sistem Presensi RS ASA BUNDA.
                    </span>
                </label>
            </div>

            {{-- ── Fixed: Action buttons ── --}}
            <div class="flex shrink-0 items-center justify-end gap-3 border-t border-gray-100 bg-white px-6 py-3.5 dark:border-gray-700 dark:bg-gray-900">
                <button type="button" @click="agreementOpen = false"
                    class="rounded-lg px-4 py-2 text-xs font-bold uppercase tracking-wider text-gray-500 transition hover:bg-gray-100 hover:text-gray-700 dark:text-gray-400 dark:hover:bg-gray-800 dark:hover:text-gray-200">
                    Tutup
                </button>
                <button
                    type="button"
                    :disabled="!modalChecked"
                    :class="!modalChecked ? 'cursor-not-allowed opacity-40 grayscale' : 'hover:bg-brand-600 active:scale-95 shadow-lg shadow-brand-500/30'"
                    @click="agreed = true; agreementOpen = false"
                    class="rounded-xl bg-brand-500 px-7 py-2.5 text-xs font-black uppercase tracking-widest text-white transition-all">
                    Saya Setuju
                </button>
            </div>

        </div>{{-- /modal card --}}
    </div>{{-- /backdrop --}}

    <style>
        /* ── Scrollbar styling ── */
        .modal-scroll::-webkit-scrollbar          { width: 6px; }
        .modal-scroll::-webkit-scrollbar-track    { background: transparent; }
        .modal-scroll::-webkit-scrollbar-thumb    { background: #d1d5db; border-radius: 999px; }
        .modal-scroll::-webkit-scrollbar-thumb:hover { background: #9ca3af; }
        /* dark */
        .dark .modal-scroll::-webkit-scrollbar-thumb       { background: #4b5563; }
        .dark .modal-scroll::-webkit-scrollbar-thumb:hover { background: #6b7280; }
        /* Firefox */
        .modal-scroll { scrollbar-width: thin; scrollbar-color: #d1d5db transparent; }
        .dark .modal-scroll { scrollbar-color: #4b5563 transparent; }
    </style>

@endsection