@extends('layouts.fullscreen-layout')

@section('title', 'Persetujuan & Kebijakan')

@section('content')
<div class="min-h-screen bg-slate-50 dark:bg-gray-950 transition-colors duration-300">
    <!-- Top Navigation -->
    <nav class="fixed top-0 z-50 w-full border-b border-slate-200/60 bg-white/70 backdrop-blur-xl dark:border-white/5 dark:bg-gray-900/70">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex h-16 items-center justify-between">
                <div class="flex items-center gap-3">
                    <img src="{{ asset('images/logo/logo-title.svg') }}" alt="Logo" class="h-8 w-auto">
                    <span class="text-lg font-bold tracking-tight text-slate-900 dark:text-white">RS ASA BUNDA</span>
                </div>
                <div class="flex items-center gap-4">
                    <button @click="$store.theme.toggle()" class="flex h-10 w-10 items-center justify-center rounded-xl bg-white shadow-sm ring-1 ring-slate-200 transition-all hover:bg-slate-50 dark:bg-gray-800 dark:ring-white/10 dark:hover:bg-gray-700">
                        <template x-if="$store.theme.theme === 'dark'">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-amber-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="4"/><path d="M12 2v2"/><path d="M12 20v2"/><path d="m4.93 4.93 1.41 1.41"/><path d="m17.66 17.66 1.41 1.41"/><path d="M2 12h2"/><path d="M20 12h2"/><path d="m6.34 17.66-1.41 1.41"/><path d="m19.07 4.93-1.41 1.41"/></svg>
                        </template>
                        <template x-if="$store.theme.theme === 'light'">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-indigo-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3a6 6 0 0 0 9 9 9 9 0 1 1-9-9Z"/></svg>
                        </template>
                    </button>
                    <a href="{{ url('/') }}" class="hidden rounded-full bg-indigo-600 px-5 py-2 text-sm font-semibold text-white transition-all hover:bg-indigo-700 hover:shadow-lg hover:shadow-indigo-500/30 sm:block">
                        Kembali
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <div class="relative overflow-hidden pt-32 pb-16 sm:pt-48 sm:pb-24">
        <div class="absolute inset-0 -z-10 bg-[radial-gradient(45rem_50rem_at_top,theme(colors.indigo.100),theme(colors.slate.50))] opacity-40 dark:bg-[radial-gradient(45rem_50rem_at_top,theme(colors.indigo.900),theme(colors.gray.950))]"></div>
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <div class="mx-auto max-w-2xl text-center">
                <h1 class="text-4xl font-bold tracking-tight text-slate-900 dark:text-white sm:text-6xl">
                    Persetujuan & Kebijakan Layanan
                </h1>
                <p class="mt-6 text-lg leading-8 text-slate-600 dark:text-gray-400">
                    Terakhir diperbarui: {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}
                </p>
            </div>
        </div>
    </div>

    <!-- Content Section -->
    <div class="mx-auto max-w-7xl px-6 pb-24 lg:px-8">
        <div class="flex flex-col gap-12 lg:flex-row">
            <!-- Table of Contents (Sticky) -->
            <aside class="lg:w-1/4 lg:shrink-0">
                <nav class="sticky top-24 space-y-1">
                    <p class="mb-4 text-xs font-bold uppercase tracking-widest text-slate-400 dark:text-gray-500">Daftar Isi</p>
                    <a href="#pendahuluan" class="group flex items-center rounded-lg px-3 py-2 text-sm font-medium text-slate-600 transition-all hover:bg-white hover:text-indigo-600 dark:text-gray-400 dark:hover:bg-white/5 dark:hover:text-indigo-400">
                        <span class="mr-3 h-1.5 w-1.5 rounded-full bg-slate-300 transition-colors group-hover:bg-indigo-500"></span>
                        Pendahuluan
                    </a>
                    <a href="#kebijakan-privasi" class="group flex items-center rounded-lg px-3 py-2 text-sm font-medium text-slate-600 transition-all hover:bg-white hover:text-indigo-600 dark:text-gray-400 dark:hover:bg-white/5 dark:hover:text-indigo-400">
                        <span class="mr-3 h-1.5 w-1.5 rounded-full bg-slate-300 transition-colors group-hover:bg-indigo-500"></span>
                        Kebijakan Privasi
                    </a>
                    <a href="#data-pengguna" class="group flex items-center rounded-lg px-3 py-2 text-sm font-medium text-slate-600 transition-all hover:bg-white hover:text-indigo-600 dark:text-gray-400 dark:hover:bg-white/5 dark:hover:text-indigo-400">
                        <span class="mr-3 h-1.5 w-1.5 rounded-full bg-slate-300 transition-colors group-hover:bg-indigo-500"></span>
                        Pengelolaan Data
                    </a>
                    <a href="#ketentuan-presensi" class="group flex items-center rounded-lg px-3 py-2 text-sm font-medium text-slate-600 transition-all hover:bg-white hover:text-indigo-600 dark:text-gray-400 dark:hover:bg-white/5 dark:hover:text-indigo-400">
                        <span class="mr-3 h-1.5 w-1.5 rounded-full bg-slate-300 transition-colors group-hover:bg-indigo-500"></span>
                        Ketentuan Presensi
                    </a>
                    <a href="#keamanan" class="group flex items-center rounded-lg px-3 py-2 text-sm font-medium text-slate-600 transition-all hover:bg-white hover:text-indigo-600 dark:text-gray-400 dark:hover:bg-white/5 dark:hover:text-indigo-400">
                        <span class="mr-3 h-1.5 w-1.5 rounded-full bg-slate-300 transition-colors group-hover:bg-indigo-500"></span>
                        Keamanan Informasi
                    </a>
                    <a href="#larangan" class="group flex items-center rounded-lg px-3 py-2 text-sm font-medium text-slate-600 transition-all hover:bg-white hover:text-indigo-600 dark:text-gray-400 dark:hover:bg-white/5 dark:hover:text-indigo-400">
                        <span class="mr-3 h-1.5 w-1.5 rounded-full bg-slate-300 transition-colors group-hover:bg-indigo-500"></span>
                        Larangan & Sanksi
                    </a>
                    <a href="#kontak" class="group flex items-center rounded-lg px-3 py-2 text-sm font-medium text-slate-600 transition-all hover:bg-white hover:text-indigo-600 dark:text-gray-400 dark:hover:bg-white/5 dark:hover:text-indigo-400">
                        <span class="mr-3 h-1.5 w-1.5 rounded-full bg-slate-300 transition-colors group-hover:bg-indigo-500"></span>
                        Hubungi Kami
                    </a>
                </nav>
            </aside>

            <!-- Main Text Content -->
            <div class="lg:flex-1">
                <div class="prose prose-slate max-w-none dark:prose-invert">
                    <section id="pendahuluan" class="mb-16 scroll-mt-28">
                        <h2 class="text-2xl font-bold text-slate-900 dark:text-white">1. Pendahuluan</h2>
                        <div class="mt-4 rounded-2xl bg-white p-8 shadow-sm ring-1 ring-slate-200 dark:bg-gray-900 dark:ring-white/5">
                            <p class="leading-relaxed">
                                Selamat datang di Sistem Presensi RS ASA BUNDA. Kami sangat menghargai privasi Anda dan berkomitmen untuk melindungi data pribadi yang Anda bagikan kepada kami. Persetujuan ini mengatur penggunaan layanan kami dan bagaimana kami menangani informasi Anda.
                            </p>
                            <p class="mt-4 leading-relaxed">
                                Dengan menggunakan aplikasi ini, Anda setuju untuk terikat oleh syarat dan ketentuan yang tercantum dalam dokumen ini. Jika Anda tidak setuju dengan bagian mana pun dari ketentuan ini, Anda disarankan untuk berhenti menggunakan layanan kami.
                            </p>
                        </div>
                    </section>

                    <section id="kebijakan-privasi" class="mb-16 scroll-mt-28">
                        <h2 class="text-2xl font-bold text-slate-900 dark:text-white">2. Kebijakan Privasi</h2>
                        <div class="mt-4 rounded-2xl bg-white p-8 shadow-sm ring-1 ring-slate-200 dark:bg-gray-900 dark:ring-white/5">
                            <p>Kami mengumpulkan informasi tertentu untuk menyediakan layanan presensi yang akurat:</p>
                            <ul class="mt-4 space-y-4">
                                <li class="flex gap-4">
                                    <div class="flex h-6 w-6 shrink-0 items-center justify-center rounded-md bg-indigo-100 text-indigo-600 dark:bg-indigo-900/40 dark:text-white">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor font-bold"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                                    </div>
                                    <span><strong>Identitas Pribadi:</strong> Nama lengkap, NIK, dan jabatan sesuai dengan data kepegawaian.</span>
                                </li>
                                <li class="flex gap-4">
                                    <div class="flex h-6 w-6 shrink-0 items-center justify-center rounded-md bg-indigo-100 text-indigo-600 dark:bg-indigo-900/40 dark:text-white">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                                    </div>
                                    <span><strong>Data Lokasi (GPS):</strong> Koordinat GPS saat melakukan presensi untuk memastikan kehadiran sesuai radius yang ditentukan.</span>
                                </li>
                                <li class="flex gap-4">
                                    <div class="flex h-6 w-6 shrink-0 items-center justify-center rounded-md bg-indigo-100 text-indigo-600 dark:bg-indigo-900/40 dark:text-white">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                                    </div>
                                    <span><strong>Foto Presensi:</strong> Foto wajah yang diambil secara langsung saat melakukan clock-in atau clock-out sebagai bukti kehadiran fisik.</span>
                                </li>
                            </ul>
                        </div>
                    </section>

                    <section id="data-pengguna" class="mb-16 scroll-mt-28">
                        <h2 class="text-2xl font-bold text-slate-900 dark:text-white">3. Pengelolaan Data</h2>
                        <div class="mt-4 rounded-2xl bg-white p-8 shadow-sm ring-1 ring-slate-200 dark:bg-gray-900 dark:ring-white/5">
                            <p class="dark:text-white">Data Anda digunakan secara eksklusif untuk kepentingan administratif RS ASA BUNDA:</p>
                            <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2">
                                <div class="rounded-xl border border-slate-100 bg-slate-50/50 p-4 dark:border-white/5 dark:bg-slate-950">
                                    <h4 class="font-bold text-slate-900 dark:!text-white">Pelaporan Kehadiran</h4>
                                    <p class="mt-2 text-sm text-slate-600 dark:text-gray-300">Penyusunan laporan kehadiran bulanan sebagai dasar penggajian.</p>
                                </div>
                                <div class="rounded-xl border border-slate-100 bg-slate-50/50 p-4 dark:border-white/5 dark:bg-slate-950">
                                    <h4 class="font-bold text-slate-900 dark:!text-white">Kepatuhan Jam Kerja</h4>
                                    <p class="mt-2 text-sm text-slate-600 dark:text-gray-300">Memastikan kepatuhan terhadap standar jam kerja rumah sakit.</p>
                                </div>
                                <div class="rounded-xl border border-slate-100 bg-slate-50/50 p-4 dark:border-white/5 dark:bg-slate-950">
                                    <h4 class="font-bold text-slate-900 dark:!text-white">Audit Internal</h4>
                                    <p class="mt-2 text-sm text-slate-600 dark:text-gray-300">Mendukung proses audit dan verifikasi yang dilakukan manajemen atau pihak berwenang.</p>
                                </div>
                                <div class="rounded-xl border border-slate-100 bg-slate-50/50 p-4 dark:border-white/5 dark:bg-slate-950">
                                    <h4 class="font-bold text-slate-900 dark:!text-white">Retensi Data</h4>
                                    <p class="mt-2 text-sm text-slate-600 dark:text-gray-300">Data disimpan selama masa hubungan kerja sesuai ketentuan perundang-undangan.</p>
                                </div>
                            </div>
                        </div>
                    </section>

                    <section id="ketentuan-presensi" class="mb-16 scroll-mt-28">
                        <h2 class="text-2xl font-bold text-slate-900 dark:text-white">4. Ketentuan Presensi & Operasional</h2>
                        <div class="mt-4 rounded-2xl bg-white p-8 shadow-sm ring-1 ring-slate-200 dark:bg-gray-900 dark:ring-white/5">
                            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 md:grid-cols-2">
                                <div class="rounded-xl border border-slate-100 bg-slate-50/50 p-4 dark:border-white/5 dark:bg-slate-950">
                                    <h4 class="font-bold text-slate-900 dark:!text-white">Hari Kerja</h4>
                                    <p class="mt-2 text-sm text-slate-600 dark:text-gray-300">Presensi masuk hanya berlaku pada hari kerja unit Anda sesuai jadwal yang ditetapkan oleh Divisi SDM.</p>
                                </div>
                                <div class="rounded-xl border border-slate-100 bg-slate-50/50 p-4 dark:border-white/5 dark:bg-slate-950">
                                    <h4 class="font-bold text-slate-900 dark:!text-white">Jendela Presensi</h4>
                                    <p class="mt-2 text-sm text-slate-600 dark:text-gray-300">Absen masuk aktif mulai 60 menit sebelum shift dan ditolak otomatis setelah jam akhir shift berlalu.</p>
                                </div>
                                <div class="rounded-xl border border-slate-100 bg-slate-50/50 p-4 dark:border-white/5 dark:bg-slate-950">
                                    <h4 class="font-bold text-slate-900 dark:!text-white">Shift Malam</h4>
                                    <p class="mt-2 text-sm text-slate-600 dark:text-gray-300">Data presensi shift malam dicatat pada tanggal kerja yang logis untuk mencegah konflik data antar hari.</p>
                                </div>
                                <div class="rounded-xl border border-slate-100 bg-slate-50/50 p-4 dark:border-white/5 dark:bg-slate-950">
                                    <h4 class="font-bold text-slate-900 dark:!text-white">Absen Pulang</h4>
                                    <p class="mt-2 text-sm text-slate-600 dark:text-gray-300">Absen pulang aktif selama sesi masuk Anda masih terbuka, memberikan fleksibilitas penyelesaian tugas.</p>
                                </div>
                                <div class="rounded-xl border border-slate-100 bg-slate-50/50 p-4 dark:border-white/5 dark:bg-slate-950">
                                    <h4 class="font-bold text-slate-900 dark:!text-white">Verifikasi GPS & Foto</h4>
                                    <p class="mt-2 text-sm text-slate-600 dark:text-gray-300">GPS aktif dan foto wajah live wajib dilakukan di lokasi kerja dalam radius yang diizinkan manajemen.</p>
                                </div>
                                <div class="rounded-xl border border-slate-100 bg-slate-50/50 p-4 dark:border-white/5 dark:bg-slate-950">
                                    <h4 class="font-bold text-slate-900 dark:!text-white">Riwayat Presensi</h4>
                                    <p class="mt-2 text-sm text-slate-600 dark:text-gray-300">Seluruh aktivitas presensi tercatat dan dapat dipantau secara transparan melalui menu Riwayat di aplikasi.</p>
                                </div>
                            </div>
                        </div>
                    </section>

                    <section id="keamanan" class="mb-16 scroll-mt-28">
                        <h2 class="text-2xl font-bold text-slate-900 dark:text-white">5. Keamanan Informasi</h2>
                        <div class="mt-4 rounded-2xl bg-indigo-600 p-8 text-white shadow-xl shadow-indigo-500/20">
                            <div class="flex items-start gap-4">
                                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-white/20 backdrop-blur-sm">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.040L3 14.535a9.235 9.235 0 00.174 2.143c.483 2.502 3.12 4.581 5.842 5.257a.933.933 0 00.568 0c2.722-.676 5.359-2.755 5.842-5.257a9.235 9.235 0 00.174-2.143l-1.382-8.551z" /></svg>
                                </div>
                                <div>
                                    <p class="text-lg font-semibold">Kami Menjamin Keamanan Data Anda</p>
                                    <p class="mt-2 text-indigo-100 opacity-90">
                                        Kami menerapkan enkripsi standar industri untuk melindungi transmisi data antara perangkat Anda dan server kami. Akses terhadap data pribadi dibatasi secara ketat hanya kepada personil berwenang di bagian SDM dan IT.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </section>

                    <section id="larangan" class="mb-16 scroll-mt-28">
                        <h2 class="text-2xl font-bold text-slate-900 dark:text-white">6. Larangan & Sanksi</h2>
                        <div class="mt-4 rounded-2xl bg-white p-8 shadow-sm ring-1 ring-slate-200 dark:bg-gray-900 dark:ring-white/5">
                            <p class="text-lg font-bold text-red-600 dark:text-red-400">Tindakan berikut dilarang keras dan dapat dikenakan sanksi disiplin serta ditindaklanjuti secara hukum sesuai UU ITE No. 11 Tahun 2008:</p>
                            <ul class="mt-6 space-y-4">
                                <li class="flex gap-4">
                                    <div class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-red-600 text-[12px] font-bold text-white">!</div>
                                    <span class="font-bold text-red-800 dark:text-red-300">Memanipulasi GPS, menggunakan VPN, atau memalsukan koordinat lokasi saat presensi.</span>
                                </li>
                                <li class="flex gap-4">
                                    <div class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-red-600 text-[12px] font-bold text-white">!</div>
                                    <span class="font-bold text-red-800 dark:text-red-300">Menggunakan foto orang lain atau foto rekaman sebagai pengganti foto live saat presensi.</span>
                                </li>
                                <li class="flex gap-4">
                                    <div class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-red-600 text-[12px] font-bold text-white">!</div>
                                    <span class="font-bold text-red-800 dark:text-red-300">Membagikan akun atau melakukan presensi atas nama orang lain (titip absen).</span>
                                </li>
                                <li class="flex gap-4">
                                    <div class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-red-600 text-[12px] font-bold text-white">!</div>
                                    <span class="font-bold text-red-800 dark:text-red-300">Mengeksploitasi celah keamanan atau melakukan akses tidak sah ke sistem.</span>
                                </li>
                                <li class="flex gap-4">
                                    <div class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-red-600 text-[12px] font-bold text-white">!</div>
                                    <span class="font-bold text-red-800 dark:text-red-300">Menyebarkan atau menyalahgunakan data presensi milik karyawan lain.</span>
                                </li>
                            </ul>
                        </div>
                    </section>

                    <section id="kontak" class="mb-16 scroll-mt-28">
                        <h2 class="text-2xl font-bold text-slate-900 dark:text-white">7. Hubungi Kami</h2>
                        <div class="mt-4 rounded-2xl bg-white p-8 shadow-sm ring-1 ring-slate-200 dark:bg-gray-900 dark:ring-white/5">
                            <p>Jika Anda memiliki pertanyaan mengenai kebijakan ini atau penggunaan data Anda, silakan hubungi:</p>
                            <div class="mt-6 space-y-4">
                                <div class="flex items-center gap-4">
                                    <div class="flex h-10 w-10 items-center justify-center rounded-full bg-slate-100 text-slate-600 dark:bg-white/5 dark:text-gray-400">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                                    </div>
                                    <span class="font-medium text-slate-700 dark:text-gray-300">it-support@rsasabunda.co.id</span>
                                </div>
                                <div class="flex items-center gap-4">
                                    <div class="flex h-10 w-10 items-center justify-center rounded-full bg-slate-100 text-slate-600 dark:bg-white/5 dark:text-gray-400">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                                    </div>
                                    <span class="font-medium text-slate-700 dark:text-gray-300">Jl. Pramuka No. 249, Purwakarta</span>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>

                <!-- Footer Action -->
                <div class="mt-16 border-t border-slate-200 pt-8 dark:border-white/10">
                    <p class="text-center text-sm text-slate-500 dark:text-gray-500">
                        &copy; {{ date('Y') }} RS ASA BUNDA. Seluruh hak cipta dilindungi.
                    </p>
                </div>
            </div>
        </div>
    </div>
    <!-- Back to Top Button -->
    <div x-data="{ show: false }" x-init="window.addEventListener('scroll', () => { show = window.scrollY > 500 })" class="fixed bottom-8 right-8 z-50">
        <button x-show="show" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 translate-y-4" @click="window.scrollTo({ top: 0, behavior: 'smooth' })" class="flex h-12 w-12 items-center justify-center rounded-full bg-white shadow-xl ring-1 ring-slate-200 transition-all hover:bg-slate-50 dark:bg-gray-800 dark:ring-white/10 dark:hover:bg-gray-700">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-indigo-600 dark:text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor font-bold"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4.5 15.75l7.5-7.5 7.5 7.5" /></svg>
        </button>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Smooth scroll for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                document.querySelector(this.getAttribute('href')).scrollIntoView({
                    behavior: 'smooth'
                });
            });
        });
    });
</script>
@endpush
@endsection
