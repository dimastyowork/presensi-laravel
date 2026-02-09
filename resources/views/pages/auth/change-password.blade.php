@extends('layouts.fullscreen-layout')
@section('title', 'Ganti Password')

@section('content')
    <div class="relative z-1 bg-white p-6 sm:p-0 dark:bg-gray-900">
        <div class="relative flex h-screen w-full flex-col justify-center sm:p-0 lg:flex-row dark:bg-gray-900">
            <!-- Form -->
            <div class="flex w-full flex-1 flex-col lg:w-1/2">
                <div class="mx-auto flex w-full max-w-md flex-1 flex-col justify-center px-4">
                    <div>
                        <div class="mb-8">
                            <h1 class="text-3xl font-bold text-gray-800 dark:text-white/90 mb-2">
                                Ganti Password
                            </h1>
                            <p class="text-sm text-gray-500 dark:text-gray-400 font-medium">
                                Demi keamanan, Anda diwajibkan mengganti password bawaan sebelum melanjutkan.
                            </p>
                        </div>
                        
                        @if(session('warning'))
                        <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 mb-6" role="alert">
                            <p>{{ session('warning') }}</p>
                        </div>
                        @endif

                        <form method="POST" action="{{ route('password.update') }}">
                            @csrf
                            <div class="space-y-6">
                                <div>
                                    <label class="mb-2 block text-sm font-semibold text-gray-700 dark:text-gray-400">
                                        Password Saat Ini
                                    </label>
                                    <input type="password" name="current_password" required autofocus
                                        class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 h-12 w-full rounded-xl border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 transition-all dark:border-gray-700 dark:text-white/90" />
                                    @error('current_password')
                                        <p class="text-red-500 text-xs mt-2 font-medium">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label class="mb-2 block text-sm font-semibold text-gray-700 dark:text-gray-400">
                                        Password Baru (Min. 8 karakter)
                                    </label>
                                    <input type="password" name="password" required
                                        class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 h-12 w-full rounded-xl border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 transition-all dark:border-gray-700 dark:text-white/90" />
                                    @error('password')
                                        <p class="text-red-500 text-xs mt-2 font-medium">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label class="mb-2 block text-sm font-semibold text-gray-700 dark:text-gray-400">
                                        Konfirmasi Password Baru
                                    </label>
                                    <input type="password" name="password_confirmation" required
                                        class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 h-12 w-full rounded-xl border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 transition-all dark:border-gray-700 dark:text-white/90" />
                                </div>

                                <button type="submit"
                                    class="flex w-full items-center justify-center rounded-xl bg-brand-500 py-3.5 font-bold text-white shadow-lg shadow-brand-500/30 transition-all hover:bg-brand-600 hover:shadow-brand-500/40 active:scale-[0.98]">
                                    Simpan & Lanjutkan
                                </button>
                            </div>
                        </form>
                    </div>
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
                    <h2 class="text-3xl font-bold mb-4">Keamanan Akun</h2>
                    <p class="text-lg text-white/80 leading-relaxed">
                        Lindungi akun Anda dengan mengubah password secara berkala.
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection
