<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta id="theme-color-meta" name="theme-color" content="#ffffff">

    <title>{{ strtoupper($__env->yieldContent('title', $title ?? 'DASHBOARD')) }} | {{ strtoupper(config('app.name')) }}</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/logo/logo-title.svg') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/logo/logo-title.svg') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script> --}}

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" type="text/css" href="https://npmcdn.com/flatpickr/dist/themes/dark.css" id="flatpickr-dark-theme" disabled>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/id.js"></script>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.store('theme', {
                init() {
                    const savedTheme = localStorage.getItem('theme');
                    this.theme = savedTheme || 'light';
                    this.updateTheme();
                },
                theme: 'light',
                toggle() {
                    this.theme = this.theme === 'light' ? 'dark' : 'light';
                    localStorage.setItem('theme', this.theme);
                    this.updateTheme();
                },
                updateTheme() {
                    const html = document.documentElement;
                    const body = document.body;
                    const metaThemeColor = document.getElementById('theme-color-meta');

                    if (this.theme === 'dark') {
                        html.classList.add('dark');
                        body.classList.add('dark', 'bg-gray-900');
                        if (metaThemeColor) metaThemeColor.setAttribute('content', '#111827');
                    } else {
                        html.classList.remove('dark');
                        body.classList.remove('dark', 'bg-gray-900');
                        if (metaThemeColor) metaThemeColor.setAttribute('content', '#ffffff');
                    }
                }
            });

            Alpine.store('sidebar', {
                isExpanded: window.innerWidth >= 1280,
                isMobileOpen: false,
                isHovered: false,

                toggleExpanded() {
                    this.isExpanded = !this.isExpanded;
                    this.isMobileOpen = false;
                },

                toggleMobileOpen() {
                    this.isMobileOpen = !this.isMobileOpen;
                },

                setMobileOpen(val) {
                    this.isMobileOpen = val;
                },

                setHovered(val) {
                    if (window.innerWidth >= 1280 && !this.isExpanded) {
                        this.isHovered = val;
                    }
                }
            });
        });
    </script>

    <script>
        (function() {
            const savedTheme = localStorage.getItem('theme');
            const theme = savedTheme || 'light';
            const metaThemeColor = document.getElementById('theme-color-meta');
            if (theme === 'dark') {
                document.documentElement.classList.add('dark');
                if (metaThemeColor) metaThemeColor.setAttribute('content', '#111827');
            } else {
                document.documentElement.classList.remove('dark');
                if (metaThemeColor) metaThemeColor.setAttribute('content', '#ffffff');
            }
        })();
    </script>
    
</head>

<body
    x-data="{ 'loaded': true}"
    x-init="$store.sidebar.isExpanded = window.innerWidth >= 1280;
    const checkMobile = () => {
        if (window.innerWidth < 1280) {
            $store.sidebar.setMobileOpen(false);
            $store.sidebar.isExpanded = false;
        } else {
            $store.sidebar.isMobileOpen = false;
            $store.sidebar.isExpanded = true;
        }
    };
    window.addEventListener('resize', checkMobile);">

    {{-- preloader --}}
    <x-common.preloader/>
    {{-- preloader end --}}

    <div class="min-h-screen xl:flex">
        @include('layouts.backdrop')
        @include('layouts.sidebar')

        <div class="flex-1 transition-all duration-300 ease-in-out"
            :class="{
                'xl:ml-[290px]': $store.sidebar.isExpanded || $store.sidebar.isHovered,
                'xl:ml-[90px]': !$store.sidebar.isExpanded && !$store.sidebar.isHovered,
                'ml-0': $store.sidebar.isMobileOpen
            }">
            @include('layouts.app-header')
            <div class="p-1 mx-auto max-w-(--breakpoint-2xl) md:p-6">
                @yield('content')
                <div class="xl:hidden" style="height: 60px;"></div>
            </div>
        </div>

        @include('layouts.bottom-nav')
    </div>

</body>

@stack('scripts')

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        @if (session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: "{{ session('success') }}",
                showConfirmButton: false,
                timer: 2000,
                timerProgressBar: true,
                background: document.documentElement.classList.contains('dark') ? '#1f2937' : '#ffffff',
                color: document.documentElement.classList.contains('dark') ? '#ffffff' : '#000000',
            });
        @endif

        @if (session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: "{{ session('error') }}",
                background: document.documentElement.classList.contains('dark') ? '#1f2937' : '#ffffff',
                color: document.documentElement.classList.contains('dark') ? '#ffffff' : '#000000',
            });
        @endif

        window.confirmDelete = function(formId) {
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Data yang dihapus tidak dapat dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal',
                background: document.documentElement.classList.contains('dark') ? '#1f2937' : '#ffffff',
                color: document.documentElement.classList.contains('dark') ? '#ffffff' : '#000000',
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById(formId).submit();
                }
            });
        }
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const initFlatpickr = () => {
            const isDark = document.documentElement.classList.contains('dark');
            const darkTheme = document.getElementById('flatpickr-dark-theme');
            if (darkTheme) darkTheme.disabled = !isDark;

            flatpickr('input[type="date"]', {
                locale: 'id',
                altInput: true,
                altFormat: "d F Y",
                dateFormat: "Y-m-d",
                allowInput: true,
                disableMobile: "true",
                onChange: function(selectedDates, dateStr, instance) {
                    instance.element.dispatchEvent(new Event('input', { bubbles: true }));
                    instance.element.dispatchEvent(new Event('change', { bubbles: true }));
                }
            });

            flatpickr('input[type="time"]', {
                locale: 'id',
                enableTime: true,
                noCalendar: true,
                dateFormat: "H:i",
                time_24hr: true,
                allowInput: true,
                disableMobile: "true",
                onChange: function(selectedDates, dateStr, instance) {
                    instance.element.dispatchEvent(new Event('input', { bubbles: true }));
                    instance.element.dispatchEvent(new Event('change', { bubbles: true }));
                }
            });
        };

        initFlatpickr();

        window.addEventListener('storage', (e) => {
            if (e.key === 'theme') {
                const darkTheme = document.getElementById('flatpickr-dark-theme');
                if (darkTheme) darkTheme.disabled = e.newValue !== 'dark';
            }
        });
        
        const observer = new MutationObserver((mutations) => {
            mutations.forEach((mutation) => {
                if (mutation.attributeName === 'class') {
                    const darkTheme = document.getElementById('flatpickr-dark-theme');
                    if (darkTheme) darkTheme.disabled = !document.documentElement.classList.contains('dark');
                }
            });
        });
        observer.observe(document.documentElement, { attributes: true });
    });
</script>

<style>
    .flatpickr-input[readonly] {
        background-color: transparent !important;
    }
    
    .flatpickr-mobile {
        display: none !important;
    }

    input.flatpickr-input.form-input,
    input.flatpickr-input.filter-input {
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%236b7280'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'%3E%3C/path%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 1rem center;
        background-size: 1.25rem;
        padding-right: 3rem !important;
    }

    .dark input.flatpickr-input.form-input,
    .dark input.flatpickr-input.filter-input {
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%2394a3b8'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'%3E%3C/path%3E%3C/svg%3E");
    }

    input[name*="time"].flatpickr-input {
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%236b7280'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'%3E%3C/path%3E%3C/svg%3E") !important;
    }

    .flatpickr-calendar {
        border-radius: 20px !important;
        box-shadow: 0 10px 40px rgba(0,0,0,0.1) !important;
        border: 1px solid rgba(0,0,0,0.05) !important;
        font-family: 'Outfit', sans-serif !important;
        padding: 10px !important;
    }

    .dark .flatpickr-calendar {
        background: #1f2937 !important;
        border-color: rgba(255,255,255,0.1) !important;
        box-shadow: 0 10px 40px rgba(0,0,0,0.4) !important;
        color: #fff !important;
    }

    .flatpickr-day.selected {
        background: #3b82f6 !important;
        border-color: #3b82f6 !important;
        border-radius: 12px !important;
    }

    .flatpickr-day:hover {
        border-radius: 12px !important;
    }
    
    .flatpickr-months .flatpickr-month {
        color: var(--text-main) !important;
        fill: var(--text-main) !important;
    }
    
    .flatpickr-current-month .flatpickr-monthDropdown-months {
        font-weight: 700 !important;
    }

    .flatpickr-day {
        color: var(--text-main) !important;
    }

    .dark .flatpickr-time {
        border-top: 1px solid rgba(255,255,255,0.1) !important;
    }
    
    .dark .flatpickr-time input {
        background: #111827 !important;
        color: #fff !important;
        border-radius: 8px !important;
    }
    
    .dark .flatpickr-time .flatpickr-am-pm,
    .dark .flatpickr-time .flatpickr-time-separator {
        color: #fff !important;
    }
    
    .dark .flatpickr-calendar.hasTime .flatpickr-time {
        border-color: rgba(255,255,255,0.1) !important;
    }

    .dark .flatpickr-months .flatpickr-prev-month, 
    .dark .flatpickr-months .flatpickr-next-month {
        color: #fff !important;
        fill: #fff !important;
    }
</style>

</html>
