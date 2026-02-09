@extends('layouts.fullscreen-layout')
@section('title', 'Sign In')

@section('content')
    <div class="relative z-1 bg-white p-6 sm:p-0 dark:bg-gray-900">
        <div class="relative flex h-screen w-full flex-col justify-center sm:p-0 lg:flex-row dark:bg-gray-900">
            <!-- Form -->
            <div class="flex w-full flex-1 flex-col lg:w-1/2">
                <div class="mx-auto flex w-full max-w-md flex-1 flex-col justify-center px-4">
                    <div>
                        <div class="mb-8">
                            <h1 class="text-3xl font-bold text-gray-800 dark:text-white/90 mb-2">
                                Selamat Datang
                            </h1>
                            <p class="text-sm text-gray-500 dark:text-gray-400 font-medium">
                                Masukkan email dan password Anda untuk masuk ke sistem e-presensi.
                            </p>
                        </div>
                        
                        <form method="POST" action="{{ route('login') }}">
                            @csrf
                            <div class="space-y-6">
                                <div>
                                    <label class="mb-2 block text-sm font-semibold text-gray-700 dark:text-gray-400">
                                        NIP / NIK Karyawan
                                    </label>
                                    <input type="text" name="nip" value="{{ old('nip') }}" required autofocus
                                        placeholder="Contoh: 0001"
                                        class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 h-12 w-full rounded-xl border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 transition-all dark:border-gray-700 dark:text-white/90" />
                                    @error('nip')
                                        <p class="text-red-500 text-xs mt-2 font-medium">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label class="mb-2 block text-sm font-semibold text-gray-700 dark:text-gray-400">
                                        Password
                                    </label>
                                    <div x-data="{ showPassword: false }" class="relative">
                                        <input :type="showPassword ? 'text' : 'password'"
                                            name="password" required
                                            placeholder="••••••••"
                                            class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 h-12 w-full rounded-xl border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 transition-all dark:border-gray-700 dark:text-white/90" />
                                        <button type="button" @click="showPassword = !showPassword"
                                            class="absolute top-1/2 right-4 -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                                            <svg x-show="!showPassword" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            <svg x-show="showPassword" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.917 9.917 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                            </svg>
                                        </button>
                                    </div>
                                    @error('password')
                                        <p class="text-red-500 text-xs mt-2 font-medium">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="flex items-center">
                                    <input type="checkbox" id="remember" name="remember" class="h-4 w-4 rounded border-gray-300 text-brand-500 focus:ring-brand-500">
                                    <label for="remember" class="ml-2 block text-sm text-gray-600 dark:text-gray-400">
                                        Ingat saya
                                    </label>
                                </div>

                                <button type="submit"
                                    class="flex w-full items-center justify-center rounded-xl bg-brand-500 py-3.5 font-bold text-white shadow-lg shadow-brand-500/30 transition-all hover:bg-brand-600 hover:shadow-brand-500/40 active:scale-[0.98]">
                                    Masuk ke Aplikasi
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="mt-10 py-6 text-center lg:hidden">
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        &copy; {{ date('Y') }} RS Asa Bunda. All rights reserved.
                    </p>
                </div>
            </div>

            <!-- Right Side (Visual) -->
            <div class="relative hidden h-full w-1/2 items-center lg:flex bg-brand-600 dark:bg-white/5 overflow-hidden">
                <div class="absolute inset-0 z-0">
                    <x-common.common-grid-shape/>
                </div>
                <div class="relative z-10 mx-auto max-w-lg px-8 text-center text-white">
                    <div class="mb-10 flex justify-center">
                         <img class="h-24 w-auto" src="/images/logo/logo-header.svg" alt="Logo" />
                    </div>
                    <h2 class="text-3xl font-bold mb-4">E-Presensi Digital</h2>
                    <p class="text-lg text-white/80 leading-relaxed">
                        Sistem kehadiran digital mutakhir untuk efisiensi dan transparansi operasional Rumah Sakit Asa Bunda.
                    </p>
                </div>
                <!-- Light/Dark Toggle -->
                <div class="absolute bottom-6 right-6 z-20">
                    <button @click.prevent="$store.theme.toggle()" class="flex h-12 w-12 items-center justify-center rounded-full bg-white/10 text-white backdrop-blur-md transition-all hover:bg-white/20">
                        <svg x-show="$store.theme.theme === 'light'" class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                        </svg>
                        <svg x-show="$store.theme.theme === 'dark'" class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4-9H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 12.728l-.707-.707M6.343 17.657l-.707-.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection
