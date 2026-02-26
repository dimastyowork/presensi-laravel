@extends('layouts.app')
@section('title', 'Presensi')

@section('content')
@php \Carbon\Carbon::setLocale('id'); @endphp
<div x-data="{ 
    step: 'choice', 
    type: 'in',
    isLocReady: false,
    locStatusText: 'Mendeteksi GPS...',
    locStatusType: 'loading', // 'loading', 'success', 'warning'
    locDistance: null,
    hasIn: {{ ($todayPresence && $todayPresence->time_in) ? 'true' : 'false' }},
    hasOut: {{ ($todayPresence && $todayPresence->time_out) ? 'true' : 'false' }},

    init() {
        window.presenceApp = this;
    },

    async startTracking() {
        this.isLocReady = false;
        this.locStatusText = 'Mendeteksi GPS...';
        this.locStatusType = 'loading';
        this.locDistance = null;
        
        if (typeof window.startLocationTracking === 'function') {
            window.startLocationTracking();
        }
    },

    proceed() {
        if (!this.isLocReady) return;
        
        // 1. Stop tracking via pure JS
        if (typeof window.stopLocationTracking === 'function') {
            window.stopLocationTracking();
        }
        
        // 2. Change step
        this.step = 'form';
        
        // 3. Start camera
        setTimeout(() => {
            if (typeof window.startCamera === 'function') window.startCamera();
        }, 150);
    }
}" 
class="presence-container">

    <template x-if="step === 'choice'">
        <div class="choice-screen animate-fade-in">
            
            @if(session('success'))
                <div class="alert alert-success animate-bounce">
                    <div class="alert-icon">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7" stroke-width="3"/></svg>
                    </div>
                    <span class="alert-text">{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger animate-bounce">
                    <div class="alert-icon">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="3"/></svg>
                    </div>
                    <span class="alert-text">{{ session('error') }}</span>
                </div>
            @endif

            @if(!$isShiftSelected)
                <div class="alert alert-warning animate-pulse">
                    <div class="alert-icon">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" stroke-width="2.5"/></svg>
                    </div>
                    <span class="alert-text">Shift kerja Anda belum ditetapkan. Silakan hubungi admin IT/HRD.</span>
                </div>
            @endif

            @if(!$isWorkingDay)
                <div class="alert alert-warning animate-pulse">
                    <div class="alert-icon">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" stroke-width="2.5"/></svg>
                    </div>
                    <span class="alert-text">Bukan Hari Kerja: {{ $dayName }} tidak terdaftar sebagai hari kerja untuk unit Anda.</span>
                </div>
            @endif

            @if(isset($settings['attendance_message']) && $settings['attendance_message'])
                <div class="alert alert-info animate-fade-in">
                    <div class="alert-icon">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" stroke-width="2.5"/></svg>
                    </div>
                    <span class="alert-text">{{ $settings['attendance_message'] }}</span>
                </div>
            @endif

            <div class="welcome-header">
                <h1 class="welcome-title">
                    Selamat {{ \Carbon\Carbon::now()->hour < 11 ? 'Pagi' : (\Carbon\Carbon::now()->hour < 15 ? 'Siang' : (\Carbon\Carbon::now()->hour < 18 ? 'Sore' : 'Malam')) }}, 
                    <span class="text-brand">{{ \App\Helpers\NameHelper::getFirstName(Auth::user()->name) }}</span>!
                </h1>
                <p class="welcome-subtitle">Silakan pilih aktivitas presensi Anda hari ini</p>
            </div>

            @php
                $externalLinks = isset($settings['external_links']) ? json_decode($settings['external_links'], true) : [];
                // Fallback for old single link style if JSON is empty but old keys exist (though we deleted them, just in case)
                if (empty($externalLinks) && isset($settings['external_link_url']) && !empty($settings['external_link_url'])) {
                    $externalLinks[] = [
                        'label' => $settings['external_link_label'] ?? 'Link Eksternal',
                        'url' => $settings['external_link_url']
                    ];
                }
            @endphp

            @if(!empty($externalLinks))
                <div class="external-links-container animate-fade-in mb-10 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3 w-full">
                    @foreach($externalLinks as $link)
                        @if(!empty($link['url']))
                            <a href="{{ $link['url'] }}" target="_blank" class="btn-external w-full justify-between glass hover:bg-blue-50 dark:hover:bg-gray-800">
                                <div class="flex items-center gap-3">
                                    <div class="p-2 rounded-full bg-blue-100/50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" /></svg>
                                    </div>
                                    <span class="text-sm font-semibold external-link-label">{{ $link['label'] ?? 'Link Eksternal' }}</span>
                                </div>
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </a>
                        @endif
                    @endforeach
                </div>
            @endif

            <div class="choice-cards">
                <div @click="if(!hasIn && {{ $isShiftSelected ? 'true' : 'false' }} && {{ $isWorkingDay ? 'true' : 'false' }} && !{{ $activeShiftInfo['is_expired'] ? 'true' : 'false' }} && !{{ $activeShiftInfo['is_too_early'] ? 'true' : 'false' }}) { type = 'in'; step = 'location'; $nextTick(() => { startTracking(); }) }" 
                    :class="(hasIn || !{{ $isShiftSelected ? 'true' : 'false' }} || !{{ $isWorkingDay ? 'true' : 'false' }} || {{ $activeShiftInfo['is_expired'] ? 'true' : 'false' }} || {{ $activeShiftInfo['is_too_early'] ? 'true' : 'false' }}) ? 'card-disabled' : 'card-entry card-blue'"
                    class="presence-card">
                    
                    <div class="card-icon-wrapper">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 16l-4-4m0 0l4-4m-4 4h14" />
                        </svg>
                    </div>
                    <h3 class="card-title">Absen Masuk {{ $activeShiftInfo['shift_name'] ? '- ' . $activeShiftInfo['shift_name'] : '' }}</h3>
                    <p class="card-status" :class="hasIn ? 'status-active' : ''">
                        <template x-if="hasIn">
                            <span>Sudah Terdaftar • {{ $todayPresence && $todayPresence->time_in ? \Carbon\Carbon::parse($todayPresence->time_in)->format('H:i') : '' }}</span>
                        </template>
                        <template x-if="!hasIn">
                            <span>
                                @if(!$isWorkingDay)
                                    Bukan hari kerja
                                @elseif(!$isShiftSelected)
                                    Shift ditetapkan admin
                                @elseif($activeShiftInfo['is_expired'])
                                    Shift sudah berakhir ({{ $activeShiftInfo['end_time'] ? $activeShiftInfo['end_time']->format('H:i') : '' }})
                                @elseif($activeShiftInfo['is_too_early'])
                                    Belum dibuka (Buka {{ $activeShiftInfo['start_time'] ? (clone $activeShiftInfo['start_time'])->subMinutes(60)->format('H:i') : '' }})
                                @else
                                    Mulai tugas hari ini
                                @endif
                            </span>
                        </template>
                    </p>
                    
                    <template x-if="hasIn">
                        <div class="status-badge badge-green">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7" stroke-width="3"/></svg>
                        </div>
                    </template>
                </div>

                <div @click="if(hasIn && !hasOut && !{{ $isTodayStaleOut ? 'true' : 'false' }}) { type = 'out'; step = 'location'; $nextTick(() => { startTracking(); }) }" 
                    :class="(hasOut || !hasIn || {{ $isTodayStaleOut ? 'true' : 'false' }}) ? 'card-disabled' : 'card-entry card-orange'"
                    class="presence-card">
                    
                    <div class="card-icon-wrapper">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 16l4-4m0 0l-4-4m4 4H7" />
                        </svg>
                    </div>
                    <h3 class="card-title">Absen Pulang</h3>
                    <p class="card-status" :class="hasOut ? 'status-orange' : ''">
                        <template x-if="hasOut">
                            <span>Sudah Terdaftar • {{ $todayPresence && $todayPresence->time_out ? \Carbon\Carbon::parse($todayPresence->time_out)->format('H:i') : '' }}</span>
                        </template>
                        <template x-if="!hasOut">
                            <span>
                                @if(!($todayPresence && $todayPresence->time_in))
                                    Masuk dahulu
                                @elseif($isTodayStaleOut)
                                    Batas waktu pulang berakhir (8 Jam)
                                @else
                                    Selesaikan tugas
                                @endif
                            </span>
                        </template>
                    </p>

                    <template x-if="hasOut">
                        <div class="status-badge badge-orange">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7" stroke-width="3"/></svg>
                        </div>
                    </template>
                </div>
            </div>

            @if($presenceForDisplay)
            <div class="summary-container animate-slide-up">
                <div class="summary-divider">
                    <span class="divider-text">Ringkasan Kehadiran</span>
                </div>
                <div class="summary-grid">
                    <div class="summary-pill glass">
                        <p class="summary-label text-emerald">Check In</p>
                        <p class="summary-time">{{ $presenceForDisplay->time_in ? \Carbon\Carbon::parse($presenceForDisplay->time_in)->format('H:i') : '--:--' }}</p>
                    </div>
                    <div class="summary-pill glass">
                        <p class="summary-label text-orange">Check Out</p>
                        <p class="summary-time">{{ $presenceForDisplay->time_out ? \Carbon\Carbon::parse($presenceForDisplay->time_out)->format('H:i') : '--:--' }}</p>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </template>

    {{-- ===== STEP: LOKASI ===== --}}
    <template x-if="step === 'location'">
        <div class="form-screen animate-slide-right">
            {{-- Header compact --}}
            <div class="form-header-compact">
                <button @click="step = 'choice'; document.dispatchEvent(new CustomEvent('go-to-choice'))" class="btn-back-compact" title="Kembali">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7" stroke-width="3"/></svg>
                </button>
                <div class="compact-clock-inline">
                    <span class="clock-inline-time" id="loc-live-clock">{{ \Carbon\Carbon::now()->format('H:i:s') }}</span>
                    <span class="clock-inline-separator">·</span>
                    <span class="clock-inline-date">{{ \Carbon\Carbon::now()->isoFormat('dddd, D MMM') }}</span>
                </div>
                <div :class="type === 'in' ? 'badge-primary' : 'badge-warning'" class="type-indicator-compact flex items-center gap-1.5">
                    <template x-if="type === 'in'">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M11 16l-4-4m0 0l4-4m-4 4h14" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </template>
                    <template x-if="type === 'out'">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M17 16l4-4m0 0l-4-4m4 4H7" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </template>
                    <span x-text="type === 'in' ? 'Masuk' : 'Pulang'"></span>
                </div>
            </div>

            {{-- Instruksi --}}
            <div class="loc-screen-info glass" style="border-radius:16px; padding:6px 16px; margin-bottom:10px;">
                <p class="flex items-center gap-2" style="font-size:0.75rem; color:var(--text-secondary); font-weight:700; margin:0;">
                    <svg class="w-4 h-4 text-brand-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" stroke-width="2"/><path d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" stroke-width="2"/></svg>
                    <span>Pastikan Anda berada <strong>di dalam area kantor</strong>, lalu tekan "Lanjutkan ke Foto"</span>
                </p>
            </div>

            {{-- Map --}}
            <div class="glass" style="border-radius:20px; overflow:hidden; margin-bottom:10px;">
                <div id="map" style="width:100%; height:260px; background:#0f172a;"></div>
            </div>

            {{-- GPS Status + Jarak --}}
            <div class="gps-strip glass" style="margin-bottom:10px;">
                <div id="loc-dot" :class="isLocReady ? 'dot-green' : 'dot-orange animate-pulse'"></div>
                <div style="flex:1; overflow:hidden; display:flex; flex-direction:column; gap:1px;">
                    <div class="flex items-center gap-2">
                        <template x-if="locStatusType === 'loading'">
                            <svg class="w-3.5 h-3.5 animate-spin text-blue-500" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        </template>
                        <template x-if="locStatusType === 'success'">
                            <svg class="w-3.5 h-3.5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" stroke-width="2.5"/></svg>
                        </template>
                        <template x-if="locStatusType === 'warning'">
                            <svg class="w-3.5 h-3.5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" stroke-width="2.5"/><path d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" stroke-width="2.5"/></svg>
                        </template>
                        <span id="loc-text" class="gps-status-text" x-text="locStatusText">Sinkronisasi Satelit...</span>
                    </div>
                    <div id="loc-distance-display" x-show="locDistance !== null" style="font-size:0.7rem; font-weight:700; margin-left: 22px;" :style="isLocReady ? 'color:#10b981' : 'color:#f59e0b'">
                        <span x-text="isLocReady ? 'Sudah dalam jangkauan kantor!' : 'Mendekat ' + locDistance + 'm lagi (maks: {{ $settings['office_radius'] ?? 100 }}m)'"></span>
                    </div>
                </div>
                <button type="button" @click="startTracking()" class="gps-refresh-btn" title="Refresh">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" stroke-width="2.5"/></svg>
                </button>
            </div>

            {{-- Progress bar jarak --}}
            <div id="loc-progress-wrap" x-show="locDistance !== null" style="background:var(--glass-bg); border:1px solid var(--glass-border); border-radius:12px; padding:10px 14px; margin-bottom:10px;">
                <div style="display:flex; justify-content:space-between; font-size:0.65rem; font-weight:700; color:var(--text-tertiary); margin-bottom:6px;">
                    <span>Jarak ke Kantor</span>
                    <span x-text="locDistance + 'm'">-</span>
                </div>
                <div style="height:6px; background:var(--hover-bg); border-radius:99px; overflow:hidden;">
                    <div id="loc-progress-bar" 
                        :style="{ 
                            width: Math.max(0, Math.min(100, 100 - (locDistance / ({{ $settings['office_radius'] ?? 100 }} * 5) * 100))) + '%',
                            background: isLocReady ? '#10b981' : (locDistance < {{ ($settings['office_radius'] ?? 100) * 2 }} ? '#f59e0b' : '#ef4444')
                        }"
                        style="height:100%; border-radius:99px; transition:width 0.5s, background 0.5s;"></div>
                </div>
            </div>

            {{-- Tombol Lanjutkan --}}
            <button id="btn-go-to-camera" 
                type="button"
                @click="proceed()"
                class="btn-submit-compact"
                :class="{ 'active': isLocReady }"
                :style="isLocReady ? '' : 'cursor:not-allowed; opacity:0.6; pointer-events: ' + (isLocReady ? 'auto' : 'none')"
                style="margin-top:4px;">
                <div class="flex items-center justify-center gap-2">
                    <template x-if="isLocReady">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" stroke-width="2.5"/></svg>
                    </template>
                    <template x-if="!isLocReady">
                        <svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    </template>
                    <span id="btn-go-text" x-text="isLocReady ? 'Lanjutkan ke Verifikasi Foto' : 'Menunggu Lokasi Valid...'">Menunggu Lokasi Valid...</span>
                    <template x-if="isLocReady">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M14 5l7 7m0 0l-7 7m7-7H3" stroke-width="3"/></svg>
                    </template>
                </div>
            </button>
        </div>
    </template>

    <template x-if="step === 'form'">
        <div class="form-screen animate-slide-right">
            {{-- Header compact --}}
            <div class="form-header-compact">
                <button @click="step = 'choice'; document.dispatchEvent(new CustomEvent('go-to-choice'))" class="btn-back-compact" title="Kembali">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7" stroke-width="3"/></svg>
                </button>
                <div class="compact-clock-inline">
                    <span id="live-clock" class="clock-inline-time">00:00:00</span>
                    <span class="clock-inline-separator">·</span>
                    <span class="clock-inline-date">{{ \Carbon\Carbon::now()->isoFormat('dddd, D MMM') }}</span>
                </div>
                <div :class="type === 'in' ? 'badge-primary' : 'badge-warning'" class="type-indicator-compact flex items-center gap-1.5">
                    <template x-if="type === 'in'">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M11 16l-4-4m0 0l4-4m-4 4h14" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </template>
                    <template x-if="type === 'out'">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M17 16l4-4m0 0l-4-4m4 4H7" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </template>
                    <span x-text="type === 'in' ? 'Masuk' : 'Pulang'"></span>
                </div>
            </div>

            <form action="{{ route('presence.store') }}" method="POST" id="attendance-form" class="form-compact-grid">
                @csrf
                <input type="hidden" name="type" :value="type">

                {{-- Kamera --}}
                <div class="cam-card glass">
                    <div class="cam-card-topbar">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-brand-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" stroke-width="2"/><path d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" stroke-width="2"/></svg>
                            <span class="cam-label">Verifikasi Wajah</span>
                        </div>
                        <div class="cam-actions">
                            <button type="button" @click="toggleCamera()" class="btn-cam-action" title="Flip kamera">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" stroke-width="2.5"/></svg>
                            </button>
                            <button type="button" id="retake-photo" class="btn-cam-action btn-cam-danger hidden" title="Ulangi foto">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-width="2.5"/></svg>
                            </button>
                        </div>
                    </div>
                    <div class="camera-viewport-compact">
                        <video id="webcam" autoplay playsinline class="video-preview"></video>
                        <canvas id="overlay" class="overlay-canvas"></canvas>
                        <img id="photo-preview" class="image-preview hidden">
                        <canvas id="canvas" class="hidden"></canvas>

                        <div id="liveness-instruction" class="liveness-instruction-container hidden">
                            <div class="instruction-pill">
                                <div class="instruction-dot pulse"></div>
                                <span id="instruction-text">Mendeteksi Wajah...</span>
                            </div>
                            <div class="instruction-steps">
                                <div id="step-look" class="step-dot active"></div>
                                <div id="step-blink" class="step-dot"></div>
                            </div>
                        </div>

                        {{-- Overlay loading kamera --}}
                        <div id="camera-status" class="overlay-loading">
                            <div class="spinner"></div>
                            <p class="loading-text" id="loading-text-content">Inisialisasi Sensor...</p>
                        </div>

                        <div id="snap-overlay" class="snap-action-wrapper-compact">
                            <button type="button" id="snap-btn" class="btn-snap-compact">
                                <div class="inner-snap-circle"></div>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Catatan + Submit --}}
                <div class="bottom-panel glass">
                    <!-- <div class="note-row">
                        <div class="flex items-center gap-2 mb-2">
                            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" stroke-width="2"/></svg>
                            <label class="note-label">Catatan</label>
                        </div>
                        <textarea name="note" rows="2" class="glass-textarea-compact" placeholder="Keterangan (opsional)..."></textarea>
                    </div> -->
                    <input type="hidden" name="location" id="location-input">
                    <input type="hidden" name="image" id="image-input">
                    <input type="hidden" name="is_face_detected" id="face-detected-input" value="false">
                    <button type="submit" id="submit-btn" disabled class="btn-submit-compact">
                        <span id="submit-text">Ambil Foto Verifikasi</span>
                    </button>
                </div>
            </form>
        </div>
    </template>
</div>

@push('scripts')
<link rel="stylesheet" href="{{ asset('leaflet/leaflet.css') }}" />
<script src="{{ asset('leaflet/leaflet.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.min.js"></script>

<style>
    :root {
        --brand-blue: #3b82f6;
        --brand-blue-hover: #2563eb;
        --brand-orange: #f97316;
        --brand-emerald: #10b981;
        
        --text-main: #1e293b;
        --text-secondary: #64748b;
        --text-tertiary: #94a3b8;
        --bg-primary: #ffffff;
        --bg-secondary: #f8fafc;
        --card-bg: rgba(255, 255, 255, 0.9);
        --glass-bg: rgba(255, 255, 255, 0.7);
        --glass-border: rgba(0, 0, 0, 0.08);
        --hover-bg: rgba(0, 0, 0, 0.03);
        --shadow-color: rgba(0, 0, 0, 0.1);
        --overlay-bg: rgba(0, 0, 0, 0.5);
    }

    .dark {
        --text-main: #f8fafc;
        --text-secondary: #94a3b8;
        --text-tertiary: #64748b;
        --bg-primary: #0c111d;
        --bg-secondary: #1a1f2e;
        --card-bg: rgba(31, 41, 55, 0.8);
        --glass-bg: rgba(255, 255, 255, 0.03);
        --glass-border: rgba(255, 255, 255, 0.08);
        --hover-bg: rgba(255, 255, 255, 0.06);
        --shadow-color: rgba(0, 0, 0, 0.3);
        --overlay-bg: rgba(0, 0, 0, 0.8);
    }

    .presence-container {
        max-width: 1100px;
        margin: 0 auto;
        padding: 20px 20px;
        font-family: 'Outfit', sans-serif;
        position: relative;
    }
    @media (max-width: 767px) {
        .presence-container { padding: 12px 10px 16px; }
    }

    .choice-screen {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        min-height: 60vh;
    }

    .shift-selection-screen {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        min-height: 60vh;
        width: 100%;
    }

    .shift-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 20px;
        width: 100%;
        max-width: 900px;
        margin-top: 40px;
    }

    .shift-card-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 25px 35px;
        border-radius: 25px;
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        border: 1px solid var(--glass-border);
    }

    .shift-card-item:hover {
        transform: translateY(-5px);
        background: var(--hover-bg);
        border-color: var(--brand-blue);
        box-shadow: 0 15px 30px var(--shadow-color);
    }

    .shift-name {
        font-size: 1.5rem;
        font-weight: 800;
        color: var(--text-main);
        margin-bottom: 5px;
    }

    .shift-time {
        display: flex;
        align-items: center;
        gap: 8px;
        color: var(--text-secondary);
        font-weight: 600;
        font-size: 0.95rem;
    }

    .shift-arrow {
        color: var(--text-tertiary);
        transition: transform 0.3s;
    }

    .shift-card-item:hover .shift-arrow {
        color: var(--brand-blue);
        transform: translateX(5px);
    }

    .alert {
        display: flex;
        align-items: center;
        gap: 15px;
        backdrop-filter: blur(10px);
    }

    .alert-success {
        background: rgba(16, 185, 129, 0.1);
        border-color: rgba(16, 185, 129, 0.3);
        color: var(--brand-emerald);
    }

    .alert-danger {
        background: rgba(239, 68, 68, 0.1);
        border-color: rgba(239, 68, 68, 0.3);
        color: #ef4444;
    }

    .alert-warning {
        background: rgba(245, 158, 11, 0.1);
        border-color: rgba(245, 158, 11, 0.3);
        color: #f59e0b;
    }

    .welcome-header { text-align: center; margin-bottom: 60px; }
    .welcome-title { 
        font-size: clamp(2rem, 5vw, 3rem); 
        font-weight: 900; 
        letter-spacing: -2px; 
        margin-bottom: 10px;
        color: var(--text-main);
    }
    .text-brand { color: var(--brand-blue); }
    .welcome-subtitle { 
        color: var(--text-secondary); 
        font-weight: 500; 
        letter-spacing: 1px; 
    }

    .choice-cards {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 30px;
        width: 100%;
        max-width: 900px;
    }

    .presence-card {
        background: var(--glass-bg);
        border: 2px solid var(--glass-border);
        border-radius: 32px;
        padding: 40px 24px;
        text-align: center;
        position: relative;
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        cursor: pointer;
        backdrop-filter: blur(15px);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }
    
    .dark .presence-card {
        background: rgba(30, 41, 59, 0.5);
        border-color: rgba(255, 255, 255, 0.1);
    }

    .card-entry:hover {
        transform: translateY(-10px) scale(1.02);
        background: var(--hover-bg);
    }
    
    .dark .card-entry:hover {
        background: rgba(255, 255, 255, 0.05);
    }

    .card-blue:hover { 
        border-color: var(--brand-blue); 
        box-shadow: 0 20px 40px rgba(59, 130, 246, 0.2); 
    }
    .dark .card-blue:hover {
        border-color: #60a5fa;
        box-shadow: 0 20px 40px rgba(96, 165, 250, 0.15);
    }

    .card-orange:hover { 
        border-color: var(--brand-orange); 
        box-shadow: 0 20px 40px rgba(249, 115, 22, 0.2); 
    }
    .dark .card-orange:hover {
        border-color: #fb923c;
        box-shadow: 0 20px 40px rgba(251, 146, 60, 0.15);
    }

    .card-icon-wrapper {
        width: 70px;
        height: 70px;
        border-radius: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 24px;
        color: white;
        transition: transform 0.3s;
    }
    .card-blue .card-icon-wrapper { 
        background: var(--brand-blue); 
        box-shadow: 0 10px 20px rgba(59, 130, 246, 0.3); 
    }
    .card-orange .card-icon-wrapper { 
        background: var(--brand-orange); 
        box-shadow: 0 10px 20px rgba(249, 115, 22, 0.3); 
    }
    .presence-card:hover .card-icon-wrapper { transform: scale(1.1); }

    .card-title { 
        font-size: 1.5rem; 
        font-weight: 800; 
        color: var(--text-main); 
        margin-bottom: 8px; 
    }
    .card-status { 
        font-size: 0.9rem; 
        font-weight: 600; 
        color: var(--text-tertiary); 
    }
    .status-active { color: var(--brand-emerald); }
    .status-orange { color: var(--brand-orange); }

    .card-disabled { 
        opacity: 0.4; 
        filter: grayscale(100%); 
        cursor: default; 
    }
    
    .status-badge {
        position: absolute;
        top: 25px;
        right: 25px;
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        box-shadow: 0 4px 10px rgba(0,0,0,0.3);
    }
    .badge-green { background: var(--brand-emerald); }
    .badge-orange { background: var(--brand-orange); }

    .btn-change-shift {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 12px 24px;
        border-radius: 12px;
        color: var(--text-secondary);
        font-weight: 700;
        font-size: 0.875rem;
        cursor: pointer;
        transition: all 0.3s;
        border: 1px solid var(--glass-border);
    }

    .btn-change-shift:hover {
        background: var(--hover-bg);
        color: var(--brand-blue);
        border-color: var(--brand-blue);
    }

    .summary-container { margin-top: 80px; width: 100%; max-width: 600px; }
    .summary-divider { 
        display: flex; 
        align-items: center; 
        gap: 20px; 
        margin-bottom: 25px; 
        opacity: 0.3;
    }
    .summary-divider::before, .summary-divider::after { 
        content: ''; 
        flex: 1; 
        height: 1px; 
        background: var(--text-tertiary); 
    }
    .divider-text { 
        font-size: 10px; 
        font-weight: 900; 
        color: var(--text-secondary); 
        text-transform: uppercase; 
        letter-spacing: 4px; 
        white-space: nowrap; 
    }

    .summary-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
    .summary-pill { 
        padding: 25px; 
        border-radius: 30px; 
        text-align: center;
        background: var(--glass-bg);
        border: 1px solid var(--glass-border);
        backdrop-filter: blur(20px);
    }
    .summary-label { 
        font-size: 9px; 
        font-weight: 900; 
        text-transform: uppercase; 
        letter-spacing: 2px; 
        margin-bottom: 5px; 
    }
    .summary-time { 
        font-size: 2rem; 
        font-weight: 900;
        color: var(--text-main);
    }

    .form-screen { width: 100%; }
    .form-header { 
        display: flex; 
        justify-content: space-between; 
        align-items: center; 
        margin-bottom: 40px;
        flex-wrap: wrap;
        gap: 20px;
    }
    .btn-back { 
        display: flex; 
        align-items: center; 
        gap: 15px; 
        background: none; 
        border: none; 
        cursor: pointer;
        padding: 0;
    }
    .icon-circle { 
        width: 50px; height: 50px; 
        border-radius: 18px; 
        display: flex; 
        align-items: center; 
        justify-content: center;
        color: var(--text-main);
        background: var(--glass-bg);
        border: 1px solid var(--glass-border);
        transition: transform 0.2s;
    }
    .btn-back:hover .icon-circle { transform: translateX(-5px); }
    .btn-back span { color: var(--text-main); font-weight: 700; }

    .type-indicator {
        padding: 12px 30px;
        border-radius: 18px;
        color: white;
        font-weight: 900;
        text-transform: uppercase;
        font-size: 12px;
        letter-spacing: 2px;
        box-shadow: 0 10px 20px rgba(0,0,0,0.2);
    }
    .badge-primary { background: var(--brand-blue); }
    .badge-warning { background: var(--brand-orange); }

    /* ====== COMPACT FORM LAYOUT (new mobile-first) ====== */

    /* Header: Kembali | Jam | Badge */
    .form-header-compact {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
        margin-bottom: 16px;
        flex-wrap: nowrap;
        width: 100%;
    }
    .btn-back-compact {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 38px;
        height: 38px;
        min-width: 38px;
        background: var(--glass-bg);
        border: 1px solid var(--glass-border);
        cursor: pointer;
        color: var(--text-secondary);
        border-radius: 12px;
        backdrop-filter: blur(10px);
        transition: all 0.2s;
    }
    .btn-back-compact:active { transform: scale(0.95); }
    .btn-back-compact:hover { color: var(--brand-blue); border-color: var(--brand-blue); }
    .compact-clock-inline {
        display: flex;
        align-items: center;
        gap: 6px;
        flex: 1;
        justify-content: center;
    }
    .clock-inline-time {
        font-size: 0.95rem;
        font-weight: 900;
        letter-spacing: -0.5px;
        color: var(--text-main);
    }
    .clock-inline-separator { color: var(--text-tertiary); font-weight: 300; font-size: 0.8rem; margin: 0 1px; }
    .clock-inline-date {
        font-size: 0.6rem;
        font-weight: 700;
        color: var(--text-tertiary);
        text-transform: uppercase;
        letter-spacing: 0.2px;
        white-space: nowrap;
    }
    .type-indicator-compact {
        padding: 6px 14px;
        border-radius: 10px;
        color: white;
        font-weight: 900;
        font-size: 0.7rem;
        letter-spacing: 1px;
        white-space: nowrap;
    }

    /* Grid: single column compact */
    .form-compact-grid {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    /* Camera Card */
    .cam-card {
        border-radius: 24px;
        overflow: hidden;
    }
    .cam-card-topbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 10px 14px 8px;
    }
    .cam-label {
        font-size: 0.75rem;
        font-weight: 800;
        color: var(--text-secondary);
        letter-spacing: 0.5px;
    }
    .cam-actions { display: flex; gap: 8px; }
    .btn-cam-action {
        width: 34px; height: 34px;
        border-radius: 10px;
        background: var(--hover-bg);
        border: 1px solid var(--glass-border);
        color: var(--text-tertiary);
        display: flex; align-items: center; justify-content: center;
        cursor: pointer; transition: all 0.2s;
    }
    .btn-cam-action:hover { background: var(--glass-bg); color: var(--text-main); border-color: var(--brand-blue); }
    .btn-cam-danger:hover { background: #ef4444; color: white; border-color: #ef4444; }

    /* Camera viewport compact */
    .camera-viewport-compact {
        position: relative;
        width: 100%;
        background: #000;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 200px;
    }
    @media (min-width: 768px) {
        .camera-viewport-compact { aspect-ratio: 16/9; max-height: 400px; }
    }

    /* Snap button compact */
    .snap-action-wrapper-compact {
        position: absolute; bottom: 16px; left: 0; right: 0;
        display: flex; justify-content: center; z-index: 5;
    }
    .btn-snap-compact {
        width: 66px; height: 66px;
        border-radius: 50%;
        background: rgba(255,255,255,0.12);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255,255,255,0.25);
        padding: 6px;
        cursor: pointer;
        transition: transform 0.2s;
    }
    .btn-snap-compact:active { transform: scale(0.88); }
    .btn-snap-compact .inner-snap-circle {
        width: 100%; height: 100%;
        border-radius: 50%;
        background: var(--brand-blue);
        border: 5px solid #fff;
        box-shadow: 0 4px 14px rgba(59, 130, 246, 0.45);
    }

    /* GPS Strip */
    .gps-strip {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 10px 14px;
        border-radius: 16px;
    }
    .gps-refresh-btn {
        width: 30px; height: 30px; min-width: 30px;
        border-radius: 8px;
        background: var(--hover-bg);
        border: 1px solid var(--glass-border);
        color: var(--text-tertiary);
        display: flex; align-items: center; justify-content: center;
        cursor: pointer;
    }
    .gps-status-text {
        flex: 1;
        font-size: 0.65rem;
        font-weight: 800;
        color: var(--text-secondary);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    .gps-map-toggle {
        width: 30px; height: 30px; min-width: 30px;
        border-radius: 8px;
        background: var(--hover-bg);
        border: 1px solid var(--glass-border);
        color: var(--text-tertiary);
        display: flex; align-items: center; justify-content: center;
        cursor: pointer;
        transition: all 0.2s;
    }
    .gps-map-toggle:hover { color: var(--brand-blue); border-color: var(--brand-blue); }

    /* Map panel collapsible */
    .map-panel-compact {
        border-radius: 18px;
        overflow: hidden;
        padding: 0;
    }
    .map-container-compact {
        width: 100%;
        height: 200px;
        background: #000;
    }

    /* Bottom panel: catatan + submit */
    .bottom-panel {
        border-radius: 24px;
        padding: 14px;
        display: flex;
        flex-direction: column;
        gap: 10px;
    }
    .note-row { display: flex; flex-direction: column; gap: 6px; }
    .note-label {
        font-size: 0.7rem;
        font-weight: 800;
        color: var(--text-tertiary);
        text-transform: uppercase;
        letter-spacing: 1px;
    }
    .glass-textarea-compact {
        width: 100%;
        background: var(--hover-bg);
        border: 1px solid var(--glass-border);
        border-radius: 14px;
        padding: 10px 14px;
        color: var(--text-main);
        font-family: inherit;
        font-size: 0.875rem;
        outline: none;
        resize: none;
        transition: border-color 0.3s;
    }
    .glass-textarea-compact::placeholder { color: var(--text-tertiary); }
    .glass-textarea-compact:focus { border-color: var(--brand-blue); background: var(--glass-bg); }

    .btn-submit-compact {
        width: 100%;
        padding: 14px;
        border-radius: 16px;
        background: var(--hover-bg);
        border: 1px solid var(--glass-border);
        color: var(--text-tertiary);
        font-size: 0.7rem;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: 1.5px;
        cursor: not-allowed;
        transition: all 0.4s;
    }
    .btn-submit-compact.active {
        background: var(--brand-blue);
        border-color: var(--brand-blue);
        color: white;
        cursor: pointer;
        box-shadow: 0 8px 20px rgba(59, 130, 246, 0.35);
    }

    @media (max-width: 380px) {
        .btn-back-compact span { display: none; }
        .clock-inline-date { font-size: 0.6rem; letter-spacing: 0; }
        .btn-submit-compact { letter-spacing: 0.5px; font-size: 0.65rem; }
    }
    /* ====== END COMPACT FORM ====== */

    .main-form-grid {
        display: grid;
        grid-template-columns: 1.4fr 1fr;
        gap: 40px;
    }
    
    @media (max-width: 1023px) {
        .main-form-grid {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }
        
        .media-column, .details-column {
            display: contents;
        }

        /* Order: 1. Jam, 2. Foto, 3. GPS, 4. Catatan */
        .clock-card { order: 1; }
        .media-column > .content-card:first-child { order: 2; }
        .media-column > .content-card:last-child { order: 3; }
        .details-column > .content-card { order: 4; }
        .policy-card { order: 5; }
    }


    .content-card {
        border-radius: 40px;
        padding: 30px;
        margin-bottom: 30px;
        display: flex;
        flex-direction: column;
    }
    @media (max-width: 1023px) {
        .content-card { padding: 15px; margin-bottom: 20px; border-radius: 30px; }
    }
    .glass { 
        background: var(--glass-bg); 
        border: 1px solid var(--glass-border); 
        backdrop-filter: blur(20px); 
    }

    .card-header { 
        display: flex; 
        justify-content: space-between; 
        align-items: center; 
        margin-bottom: 25px; 
    }
    .card-header-title { 
        font-size: 1.3rem; 
        font-weight: 900; 
        color: var(--text-main); 
        letter-spacing: -0.5px; 
    }
    
    .card-actions { display: flex; gap: 10px; }
    .btn-action {
        width: 50px; height: 50px;
        border-radius: 18px;
        background: var(--hover-bg);
        border: 1px solid var(--glass-border);
        color: var(--text-tertiary);
        display: flex; align-items: center; justify-content: center;
        cursor: pointer; transition: all 0.2s;
    }
    .btn-action:hover { 
        background: var(--glass-bg); 
        color: var(--text-main); 
        border-color: var(--brand-blue);
    }
    .btn-danger:hover { 
        background: #ef4444; 
        color: white; 
        border-color: #ef4444; 
    }

    .camera-viewport {
        position: relative;
        width: 100%;
        aspect-ratio: 9/16;
        background: #000;
        border-radius: 30px;
        overflow: hidden;
        border: 1px solid var(--glass-border);
    }
    @media (min-width: 1024px) {
        .camera-viewport { aspect-ratio: 4/3; }
    }
    .video-preview { 
        width: 100%; 
        height: auto; 
        object-fit: contain;
        transform: translateZ(0);
        -webkit-transform: translateZ(0);
    }
    .image-preview { 
        width: 100%; 
        height: 100%; 
        object-fit: cover; 
    }
    .overlay-canvas {
        position: absolute;
        top: 0;
        left: 0;
        z-index: 2;
        pointer-events: none;
    }
    
    .overlay-loading {
        position: absolute; inset: 0;
        background: var(--overlay-bg);
        display: flex; flex-direction: column; align-items: center; justify-content: center;
        z-index: 10;
    }
    .spinner {
        width: 50px; height: 50px;
        border: 4px solid rgba(255,255,255,0.1);
        border-top-color: var(--brand-blue);
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }
    .loading-text { 
        margin-top: 20px; 
        font-size: 10px; 
        font-weight: 900; 
        color: var(--text-tertiary); 
        text-transform: uppercase; 
        letter-spacing: 3px; 
    }

    .snap-action-wrapper {
        position: absolute; bottom: 30px; left: 0; right: 0;
        display: flex; justify-content: center; z-index: 5;
    }
    .btn-snap {
        width: 90px; height: 90px;
        border-radius: 50%;
        background: rgba(255,255,255,0.1);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255,255,255,0.2);
        padding: 8px;
        cursor: pointer;
        transition: transform 0.2s;
    }
    .btn-snap:active { transform: scale(0.9); }
    .inner-snap-circle {
        width: 100%; height: 100%;
        border-radius: 50%;
        background: var(--brand-blue);
        border: 6px solid #fff;
        box-shadow: 0 5px 15px rgba(59, 130, 246, 0.4);
    }

    .map-container { 
        width: 100%; 
        height: 320px; 
        border-radius: 30px; 
        overflow: hidden; 
        background: #000; 
    }
    .location-status-bar {
        margin-top: 25px;
        padding: 20px 30px;
        border-radius: 25px;
        display: flex; align-items: center; gap: 15px;
    }
    .dot-orange { 
        width: 15px; 
        height: 15px; 
        border-radius: 50%; 
        background: var(--brand-orange); 
    }
    .dot-green { 
        width: 15px; 
        height: 15px; 
        border-radius: 50%; 
        background: var(--brand-emerald); 
        box-shadow: 0 0 10px var(--brand-emerald); 
    }
    .status-msg { 
        font-size: 11px; 
        font-weight: 900; 
        color: var(--text-secondary); 
        text-transform: uppercase; 
        letter-spacing: 1px; 
    }

    .clock-card {
        background: linear-gradient(135deg, var(--brand-blue), #1e40af);
        border-radius: 40px;
        padding: 50px 30px;
        text-align: center;
        margin-bottom: 30px;
        box-shadow: 0 20px 40px var(--shadow-color);
        position: relative;
        overflow: hidden;
    }
    .clock-time { 
        font-size: clamp(3rem, 8vw, 5rem); 
        font-weight: 900; 
        color: white; 
        letter-spacing: -4px; 
        margin-bottom: 5px; 
    }
    .clock-date { 
        font-size: 11px; 
        font-weight: 800; 
        color: rgba(255,255,255,0.5); 
        text-transform: uppercase; 
        letter-spacing: 5px; 
    }

    .field-group { margin-bottom: 30px; }
    .field-label { 
        display: block; 
        font-size: 9px; 
        font-weight: 900; 
        text-transform: uppercase; 
        letter-spacing: 3px; 
        color: var(--text-tertiary); 
        margin-bottom: 20px; 
        margin-left: 10px; 
    }
    .glass-textarea {
        width: 100%;
        background: var(--hover-bg);
        border: 1px solid var(--glass-border);
        border-radius: 30px;
        padding: 25px;
        color: var(--text-main);
        font-family: inherit;
        font-size: 1rem;
        outline: none;
        transition: border-color 0.3s;
        resize: vertical;
    }
    .glass-textarea::placeholder {
        color: var(--text-tertiary);
    }
    .glass-textarea:focus { 
        border-color: var(--brand-blue); 
        background: var(--glass-bg);
    }

    .btn-submit {
        width: 100%;
        padding: 25px;
        border-radius: 30px;
        background: var(--hover-bg);
        border: 1px solid var(--glass-border);
        color: var(--text-tertiary);
        font-size: 12px;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: 4px;
        cursor: not-allowed;
        transition: all 0.4s;
    }
    .dark .btn-submit:disabled {
        background: #334155;
        color: #64748b;
        border-color: #475569;
    }
    
    .btn-submit:not(:disabled) {
        background: var(--brand-blue);
        color: white;
        cursor: pointer;
        box-shadow: 0 15px 30px rgba(59, 130, 246, 0.3);
        border-color: var(--brand-blue);
    }
    .btn-submit:not(:disabled):hover { 
        transform: scale(1.02); 
        background: var(--brand-blue-hover); 
    }

    .glass-green { 
        background: rgba(16, 185, 129, 0.05); 
        border: 1px solid rgba(16, 185, 129, 0.2); 
    }
    .policy-card { padding: 30px; border-radius: 30px; }
    .policy-title { 
        font-size: 11px; 
        font-weight: 900; 
        text-transform: uppercase; 
        letter-spacing: 2px; 
        color: var(--brand-emerald); 
        display: flex; 
        align-items: center; 
        gap: 10px; 
        margin-bottom: 10px; 
    }
    .policy-text { 
        font-size: 10px; 
        font-weight: 500; 
        line-height: 1.6; 
        opacity: 0.8; 
    }

    .text-emerald { color: var(--brand-emerald); }
    .text-orange { color: var(--brand-orange); }
    .text-white { color: white; }

    @keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
    @keyframes fade-in { from { opacity: 0; transform: scale(0.95); } to { opacity: 1; transform: scale(1); } }
    @keyframes slide-right { from { opacity: 0; transform: translateX(20px); } to { opacity: 1; transform: translateX(0); } }
    @keyframes slide-up { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
    @keyframes bounce { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-10px); } }
    @keyframes pulse { 0%, 100% { opacity: 1; } 50% { opacity: 0.5; } }

    .animate-fade-in { animation: fade-in 0.5s ease-out; }
    .animate-slide-right { animation: slide-right 0.5s ease-out; }
    .animate-slide-up { animation: slide-up 0.5s ease-out; }
    .animate-bounce { animation: bounce 0.5s ease-out; }
    .animate-pulse { animation: pulse 2s ease-in-out infinite; }
    
    @keyframes map-pulse {
        0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(59, 130, 246, 0.7); }
        70% { transform: scale(1); box-shadow: 0 0 0 15px rgba(59, 130, 246, 0); }
        100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(59, 130, 246, 0); }
    }
    .map-marker-pulse {
        width: 20px;
        height: 20px;
        background: rgba(59, 130, 246, 0.4);
        border-radius: 50%;
        position: absolute;
        animation: map-pulse 2s infinite;
        z-index: 1;
    }

    .liveness-instruction-container {
        position: absolute;
        top: 20px;
        left: 0;
        right: 0;
        z-index: 20;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 10px;
        pointer-events: none;
    }

    .instruction-pill {
        background: rgba(0, 0, 0, 0.7);
        backdrop-filter: blur(10px);
        padding: 10px 20px;
        border-radius: 100px;
        border: 1px solid rgba(255, 255, 255, 0.2);
        display: flex;
        align-items: center;
        gap: 10px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        transform: translateY(0);
        transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }

    .instruction-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: #3b82f6;
    }

    .instruction-dot.pulse {
        animation: dot-pulse 1.5s infinite;
    }

    @keyframes dot-pulse {
        0% { transform: scale(1); box-shadow: 0 0 0 0 rgba(59, 130, 246, 0.7); }
        70% { transform: scale(1.2); box-shadow: 0 0 0 10px rgba(59, 130, 246, 0); }
        100% { transform: scale(1); box-shadow: 0 0 0 0 rgba(59, 130, 246, 0); }
    }

    #instruction-text {
        color: white;
        font-size: 13px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .instruction-steps {
        display: flex;
        gap: 6px;
    }

    .step-dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.3);
        transition: all 0.3s;
    }

    .step-dot.active {
        background: #3b82f6;
        width: 15px;
        border-radius: 10px;
    }

    .step-dot.success {
        background: #10b981;
    }

    @media (max-width: 900px) {
        .presence-container { padding: 18px 15px; }
        
        .choice-cards { 
            grid-template-columns: 1fr; 
            gap: 20px;
            max-width: 100%;
        }
        
        .presence-card { 
            padding: 40px 25px; 
            border-radius: 30px;
        }
        
        .card-icon-wrapper {
            width: 70px;
            height: 70px;
            border-radius: 20px;
            margin-bottom: 20px;
        }
        
        .card-title { font-size: 1.5rem; }
        
        .summary-container { margin-top: 50px; }
        .summary-grid { gap: 10px; }
        .summary-pill { padding: 20px; }
        .summary-time { font-size: 1.5rem; }
        
        .main-form-grid { 
            grid-template-columns: 1fr; 
            gap: 25px;
        }
        
        .form-header {
            margin-bottom: 30px;
        }
        
        .clock-card { 
            padding: 40px 25px; 
            margin-bottom: 25px;
        }
        
        .camera-viewport {
            aspect-ratio: 3/4;
            border-radius: 25px;
        }
        
        .map-container { 
            height: 250px; 
            border-radius: 25px;
        }
        
        .content-card {
            padding: 25px;
            margin-bottom: 20px;
            border-radius: 30px;
        }
        
        .btn-snap {
            width: 70px;
            height: 70px;
        }
        
        .snap-action-wrapper {
            bottom: 20px;
        }
        
        .field-group { margin-bottom: 20px; }
        
        .glass-textarea {
            padding: 20px;
            border-radius: 25px;
        }
        
        .btn-submit {
            padding: 20px;
            border-radius: 25px;
            font-size: 11px;
        }
        
        .policy-card {
            padding: 25px;
            border-radius: 25px;
        }
    }

    @media (max-width: 480px) {
        .welcome-header { margin-bottom: 40px; }
        .welcome-title { font-size: 1.75rem; }
        
        .choice-cards { gap: 15px; }
        .presence-card { padding: 35px 20px; }
        
        .card-icon-wrapper {
            width: 60px;
            height: 60px;
        }
        
        .card-title { font-size: 1.3rem; }
        .card-status { font-size: 0.8rem; }
        
        .status-badge {
            width: 28px;
            height: 28px;
            top: 20px;
            right: 20px;
        }
        
        .clock-time { font-size: 2.5rem; }
        .clock-date { font-size: 9px; letter-spacing: 3px; }
        
        .btn-action {
            width: 45px;
            height: 45px;
        }
        
        .icon-circle {
            width: 45px;
            height: 45px;
        }
        
        .type-indicator {
            padding: 10px 20px;
            font-size: 10px;
        }
    }
    .btn-emergency {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        padding: 12px 24px;
        background: rgba(239, 68, 68, 0.1);
        color: var(--brand-red);
        border: 1px solid rgba(239, 68, 68, 0.3);
        border-radius: 12px;
        font-weight: 700;
        text-decoration: none;
        transition: all 0.3s;
    }

    .btn-emergency:hover {
        background: var(--brand-red);
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
    }

    .btn-external {
        display: inline-flex;
        align-items: center;
        padding: 12px 16px;
        margin-bottom: 8px;
        border-radius: 16px;
        background: var(--card-bg);
        border: 1px solid var(--glass-border);
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        text-decoration: none;
        transition: all 0.3s ease;
        backdrop-filter: blur(10px);
    }

    .dark .btn-external {
        background: rgba(30, 41, 59, 0.7);
        border-color: rgba(255, 255, 255, 0.05);
    }

    .btn-external:hover {
        transform: translateY(-2px);
        background: rgba(255, 255, 255, 0.95);
        box-shadow: 0 8px 16px rgba(0,0,0,0.08);
        border-color: var(--brand-blue);
    }
    
    .dark .btn-external:hover {
        background: rgba(30, 41, 59, 0.9);
        border-color: #60a5fa;
    }
    
    .external-link-label {
        color: var(--text-main);
    }

    .alert-info {
        background: rgba(59, 130, 246, 0.1);
        border-left: 4px solid #3b82f6;
    }
    
    .alert-info .alert-icon {
        color: #3b82f6;
    }
    
    .dark .alert-info {
        background: rgba(59, 130, 246, 0.15);
        border-left: 4px solid #60a5fa;
        border-right: 1px solid rgba(59, 130, 246, 0.2);
        border-top: 1px solid rgba(59, 130, 246, 0.2);
        border-bottom: 1px solid rgba(59, 130, 246, 0.2);
    }
    
    .dark .alert-info .alert-icon {
        color: #60a5fa;
    }
    
    .dark .alert-info .alert-text {
        color: #e2e8f0;
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    const OFFICE_LOCATION = "{{ $settings['office_location'] ?? '' }}";
    const OFFICE_RADIUS = {{ $settings['office_radius'] ?? 500 }};
    let isLocationInRange = null;

    let currentStream = null;
    let map = null, marker = null;
    let useFrontCamera = true;
    let isModelLoaded = false;
    let detectionInterval = null;

    const loadModels = async () => {
        const MODEL_URL = '{{ asset("models") }}';
        try {
            const loadingEl = document.getElementById('loading-text-content');
            if(loadingEl) loadingEl.textContent = "Memuat AI Wajah...";
            
            await faceapi.nets.tinyFaceDetector.loadFromUri(MODEL_URL);
            
            try {
                await faceapi.nets.faceLandmark68Net.loadFromUri(MODEL_URL);
            } catch (landmarkError) {
                console.warn("Landmark model missing, liveness check will be disabled.");
            }

            isModelLoaded = true;
            console.log("Models loaded successfully");
        } catch (error) {
            console.error("Error loading critical models:", error);
            const loadingEl = document.getElementById('loading-text-content');
            if(loadingEl) loadingEl.textContent = "Offline Mode (Tanpa AI)";
        }
    };

    document.addEventListener('DOMContentLoaded', () => {
        loadModels();
    });

    window.startCamera = async () => {
        const webcam = document.getElementById('webcam');
        const camStatus = document.getElementById('camera-status');
        const loadingText = document.getElementById('loading-text-content');
        if(!webcam) return;

        if (currentStream) {
            currentStream.getTracks().forEach(t => t.stop());
            if(detectionInterval) clearInterval(detectionInterval);
        }

        try {
            camStatus.style.display = 'flex';
            loadingText.textContent = "Mengakses Kamera...";
            
            if (!isModelLoaded) {
                 loadingText.textContent = "Menunggu AI...";
                 await new Promise(resolve => {
                     const check = setInterval(() => {
                         if(isModelLoaded) { clearInterval(check); resolve(); }
                     }, 100);
                 });
            }

            const isIOS = /iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream;
            const videoConstraints = isIOS
                ? { 
                    facingMode: useFrontCamera ? "user" : "environment"
                }
                : { 
                    facingMode: useFrontCamera ? "user" : "environment", 
                    width: { ideal: 640 }, 
                    height: { ideal: 480 }
                };
            
            currentStream = await navigator.mediaDevices.getUserMedia({ video: videoConstraints });
            webcam.srcObject = currentStream;
            
            webcam.onloadedmetadata = () => {
                camStatus.style.display = 'none';
                document.getElementById('liveness-instruction').classList.remove('hidden');
                window.livenessState = 0;
                window.startLookTime = null;
                startFaceDetection();
            };
        } catch (e) {
            console.error(e);
            camStatus.innerHTML = '<div style="text-align: center; color: #f44;"><p>AKSES KAMERA DITOLAK</p></div>';
        }
    };

    const startFaceDetection = () => {
        const video = document.getElementById('webcam');
        const canvas = document.getElementById('overlay');
        const submitBtn = document.getElementById('submit-btn');

        if (!video || !canvas) return;

        const displaySize = { width: video.offsetWidth, height: video.offsetHeight };
        faceapi.matchDimensions(canvas, displaySize);

        const faceOptions = new faceapi.TinyFaceDetectorOptions({ inputSize: 224, scoreThreshold: 0.5 });

        if(detectionInterval) clearInterval(detectionInterval);

        detectionInterval = setInterval(async () => {
            if (video.paused || video.ended) return;

            let detection = null;
            
            if (faceapi.nets.faceLandmark68Net.isLoaded) {
                try {
                    detection = await faceapi.detectSingleFace(video, faceOptions).withFaceLandmarks();
                } catch (e) {
                    console.warn("Landmark detection error, falling back to simple detection", e);
                }
            }
            
            if (!detection) {
                 const simpleDetections = await faceapi.detectAllFaces(video, faceOptions);
                 if(simpleDetections.length > 0) {
                     detection = { detection: simpleDetections[0], landmarks: null };
                 }
            }
            
            const ctx = canvas.getContext('2d');
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            
            ctx.save();
            if (useFrontCamera) {
                ctx.translate(canvas.width, 0);
                ctx.scale(-1, 1);
            }
            
            const faceDetectedInput = document.getElementById('face-detected-input');
            
            if (detection) {
                const resizedDetections = faceapi.resizeResults(detection, displaySize);
                faceapi.draw.drawDetections(canvas, resizedDetections);

                let instruction = "";
                let color = "white";

                if (resizedDetections.landmarks) {
                    const landmarks = resizedDetections.landmarks;
                    const nose = landmarks.getNose()[0];
                    const leftEye = landmarks.getLeftEye();
                    const rightEye = landmarks.getRightEye();
                    const jaw = landmarks.getJawOutline();
                    
                    const faceWidth = jaw[16].x - jaw[0].x;
                    const noseRelX = (nose.x - jaw[0].x) / faceWidth;
                    
                    const getEAR = (eye) => {
                        const v1 = Math.abs(eye[1].y - eye[5].y);
                        const v2 = Math.abs(eye[2].y - eye[4].y);
                        const h = Math.abs(eye[0].x - eye[3].x);
                        return (v1 + v2) / (2.0 * h);
                    };
                    
                    const leftEAR = getEAR(leftEye);
                    const rightEAR = getEAR(rightEye);
                    const minEAR = Math.min(leftEAR, rightEAR);
                    
                    const instrEl = document.getElementById('instruction-text');
                    const stepLook = document.getElementById('step-look');
                    const stepBlink = document.getElementById('step-blink');

                    switch(window.livenessState) {
                        case 0:
                            instruction = "Lihat Lurus ke Kamera";
                            if (instrEl) instrEl.textContent = instruction;
                            if (stepLook) stepLook.className = 'step-dot active';
                            if (stepBlink) stepBlink.className = 'step-dot';

                            if (noseRelX > 0.35 && noseRelX < 0.65) {
                                if(!window.startLookTime) window.startLookTime = Date.now();
                                if(Date.now() - window.startLookTime > 800) {
                                    window.livenessState = 1;
                                    window.startLookTime = null;
                                }
                            } else {
                                window.startLookTime = null;
                            }
                            window.startBlinkStateTime = null;
                            break;
                        case 1:
                            instruction = "KEDIPKAN Mata Anda";
                            if (instrEl) instrEl.textContent = instruction;
                            if (stepLook) stepLook.className = 'step-dot success';
                            if (stepBlink) stepBlink.className = 'step-dot active';

                            if (minEAR < 0.33) {
                                window.livenessState = 2; 
                            }
                            
                            if (!window.startBlinkStateTime) window.startBlinkStateTime = Date.now();
                            if (Date.now() - window.startBlinkStateTime > 4000) {
                                window.livenessState = 2;
                                console.log("Blink detection timeout, bypassing...");
                            }
                            break;
                        case 2:
                            instruction = "VERIFIKASI BERHASIL!";
                            if (instrEl) instrEl.textContent = instruction;
                            if (stepLook) stepLook.className = 'step-dot success';
                            if (stepBlink) stepBlink.className = 'step-dot success';

                            color = "#10b981";
                            if(faceDetectedInput) faceDetectedInput.value = "true";
                            break;
                    }
                } else {
                    instruction = "Mencari Titik Wajah...";
                    const instrEl = document.getElementById('instruction-text');
                    if(instrEl) instrEl.textContent = instruction;
                    color = "#10b981";
                    if(faceDetectedInput) faceDetectedInput.value = "true";
                }

                ctx.restore();
                
                if (window.livenessState === 2 || (!resizedDetections.landmarks && detection)) {
                    snapBtn.style.borderColor = "#10b981"; 
                    snapBtn.style.boxShadow = "0 0 20px rgba(16, 185, 129, 0.5)";
                    snapBtn.disabled = false;
                } else {
                     snapBtn.style.borderColor = "#f59e0b";
                     snapBtn.style.boxShadow = "none";
                     if(faceDetectedInput) faceDetectedInput.value = "false";
                }
                
            } else {
                ctx.restore();
                const instrEl = document.getElementById('instruction-text');
                if(instrEl) instrEl.textContent = "Wajah Tidak Terdeteksi";
                
                if(faceDetectedInput) faceDetectedInput.value = "false";
                window.livenessState = 0;
                snapBtn.style.borderColor = "#f59e0b";
                snapBtn.style.boxShadow = "none";
            }

        }, 80);
    };

    window.toggleCamera = () => {
        useFrontCamera = !useFrontCamera;
        window.startCamera();
    };

    function calculateDistance(lat1, lon1, lat2, lon2) {
        const R = 6371e3;
        const φ1 = lat1 * Math.PI/180;
        const φ2 = lat2 * Math.PI/180;
        const Δφ = (lat2-lat1) * Math.PI/180;
        const Δλ = (lon2-lon1) * Math.PI/180;

        const a = Math.sin(Δφ/2) * Math.sin(Δφ/2) +
                  Math.cos(φ1) * Math.cos(φ2) *
                  Math.sin(Δλ/2) * Math.sin(Δλ/2);
        const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));

        return R * c;
    }

    let locationWatcherId = null;
    let cameraStarted = false;

    const personIcon = L.divIcon({
        html: `<div style="position:relative; display:flex; align-items:center; justify-content:center;">
                <div class="map-marker-pulse"></div>
                <img src="{{ asset('images/user/man.svg') }}" style="width:45px; height:45px; position:relative; z-index:10; filter:drop-shadow(0 4px 6px rgba(0,0,0,0.3));">
              </div>`,
        className: 'custom-person-marker-container',
        iconSize: [45, 45],
        iconAnchor: [22, 22]
    });

    function updateMapPosition(latitude, longitude) {
        if (!map) {
            map = L.map('map', { zoomControl: false }).setView([latitude, longitude], 17);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
            marker = L.marker([latitude, longitude], { icon: personIcon }).addTo(map);
            if (OFFICE_LOCATION) {
                const parts = OFFICE_LOCATION.split(',').map(n => parseFloat(n.trim()));
                if (parts.length === 2 && !isNaN(parts[0])) {
                    L.circle(parts, {
                        color: '#3b82f6', fillColor: '#3b82f6',
                        fillOpacity: 0.15, radius: OFFICE_RADIUS
                    }).addTo(map);
                }
            }
            setTimeout(() => map.invalidateSize(), 500);
        } else {
            map.setView([latitude, longitude], map.getZoom());
            marker.setLatLng([latitude, longitude]);
        }
    }

    window.startLocationTracking = () => {
        window._hasShownLocWarning = false;
        if (!navigator.geolocation) {
            if (window.presenceApp) {
                window.presenceApp.locStatusText = 'GPS tidak tersedia di browser ini';
                window.presenceApp.locStatusType = 'warning';
                window.presenceApp.isLocReady = false;
                window.presenceApp.locDistance = null;
            }
            return;
        }

        // Geolocation requires Secure Context (HTTPS or localhost)
        if (!window.isSecureContext && window.location.hostname !== 'localhost') {
            if (window.presenceApp) {
                window.presenceApp.locStatusText = 'Akses Lokasi diblokir (Butuh HTTPS)';
                window.presenceApp.locStatusType = 'warning';
                window.presenceApp.isLocReady = false;
                window.presenceApp.locDistance = null;
            }
            console.warn("Geolocation requires a secure context (HTTPS or localhost).");
            return;
        }

        if (locationWatcherId !== null) {
            navigator.geolocation.clearWatch(locationWatcherId);
            locationWatcherId = null;
        }

        locationWatcherId = navigator.geolocation.watchPosition(pos => {
            const { latitude, longitude } = pos.coords;
            window._lastLat = latitude;
            window._lastLng = longitude;

            updateMapPosition(latitude, longitude);

            let inRange = false, statusText = '', distance = 0, statusType = 'warning';
            if (OFFICE_LOCATION) {
                try {
                    const parts = OFFICE_LOCATION.split(',').map(n => parseFloat(n.trim()));
                    const offLat = parts[0], offLng = parts[1];
                    if (!isNaN(offLat) && !isNaN(offLng)) {
                        distance = calculateDistance(latitude, longitude, offLat, offLng);
                        
                        if (distance > 1000000) { 
                            inRange = false;
                            statusType = 'warning';
                            statusText = 'Lokasi Tidak Akurat Pastikan GPS Aktif';
                            
                            if (!window._hasShownLocWarning) {
                                Swal.fire({
                                    icon: 'warning',
                                    title: 'Lokasi Tidak Akurat',
                                    html: 'GPS terdeteksi jauh.<br><br>' +
                                          '1. Pastikan GPS aktif.<br>' +
                                          '2. Gunakan Browser Chrome/Safari.<br>' +
                                          '3. Hindari dalam ruangan beton tebal.',
                                    confirmButtonColor: '#3b82f6'
                                });
                                window._hasShownLocWarning = true;
                            }
                        } else {
                            inRange  = distance <= OFFICE_RADIUS;
                            statusType = inRange ? 'success' : 'warning';
                            statusText = inRange
                                ? `Dalam area kantor (${distance.toFixed(0)}m)`
                                : `${distance.toFixed(0)}m dari kantor`;
                        }
                    } else {
                        inRange = true; statusType = 'success'; statusText = 'Lokasi kantor belum diset';
                    }
                } catch(e) { inRange = true; statusType = 'success'; statusText = 'Skip (error config)'; }
            } else {
                inRange = true; statusType = 'success'; statusText = 'Mode dev — lokasi dilewati';
            }

            if (window.presenceApp) {
                window.presenceApp.isLocReady = inRange;
                window.presenceApp.locStatusText = statusText;
                window.presenceApp.locStatusType = statusType;
                window.presenceApp.locDistance = distance >= 0 ? Math.round(distance) : null;
            }

        }, err => {
            if (window.presenceApp) {
                let msg = 'GPS Gagal: ' + err.message;
                if (err.code === 1) {
                    msg = 'Akses Lokasi Ditolak. Harap izinkan GPS di browser/setelan HP Anda.';
                } else if (err.code === 2) {
                    msg = 'Lokasi tidak ditemukan. Pastikan GPS aktif dan sinyal kuat.';
                } else if (err.code === 3) {
                    msg = 'Waktu deteksi GPS habis. Silakan coba lagi.';
                }
                
                window.presenceApp.locStatusText = msg;
                window.presenceApp.locStatusType = 'warning';
                window.presenceApp.isLocReady = false;
                window.presenceApp.locDistance = null;
            }
        }, {
            enableHighAccuracy: true,
            maximumAge: 10000,
            timeout: 10000
        });
    };

    window.stopLocationTracking = () => {
        if (locationWatcherId !== null) {
            navigator.geolocation.clearWatch(locationWatcherId);
            locationWatcherId = null;
        }
        const locInput = document.getElementById('location-input');
        if (locInput && window._lastLat !== undefined) {
            locInput.value = `${window._lastLat}, ${window._lastLng}`;
        }
    };



    document.addEventListener('click', (e) => {
        const snapBtn = e.target.closest('#snap-btn');
        if(snapBtn) {
            const faceDetectedInput = document.getElementById('face-detected-input');
            const isFaceDetected = faceDetectedInput && faceDetectedInput.value === "true";
            
            if (!isFaceDetected) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Wajah Tidak Terdeteksi',
                    text: 'Pastikan wajah Anda terlihat jelas dalam kamera!',
                    confirmButtonColor: '#3b82f6'
                });
                return;
            }

            const webcam = document.getElementById('webcam');
            const canvas = document.getElementById('canvas');
            const preview = document.getElementById('photo-preview');
            const snapOverlay = document.getElementById('snap-overlay');
            const retakeBtn = document.getElementById('retake-photo');
            const submitBtn = document.getElementById('submit-btn');
            const imageInput = document.getElementById('image-input');

            canvas.width = webcam.videoWidth || webcam.offsetWidth;
            canvas.height = webcam.videoHeight || webcam.offsetHeight;
            const ctx = canvas.getContext('2d');
            
            if (useFrontCamera) {
                ctx.translate(canvas.width, 0);
                ctx.scale(-1, 1);
            }
            
            ctx.drawImage(webcam, 0, 0, canvas.width, canvas.height);
            
            const data = canvas.toDataURL('image/jpeg', 0.85);

            if(window.detectionInterval) clearInterval(window.detectionInterval);
            
            preview.src = data;
            preview.classList.remove('hidden');
            
            webcam.classList.add('hidden');
            document.getElementById('overlay').classList.add('hidden'); 
            document.getElementById('liveness-instruction').classList.add('hidden');
            snapOverlay.classList.add('hidden');
            
            retakeBtn.classList.remove('hidden');
            imageInput.value = data;
            
            submitBtn.disabled = false;
            submitBtn.classList.add('active');
            document.getElementById('submit-text').textContent = 'Kirim Presensi Sekarang';
        }

        if(e.target.closest('#retake-photo')) {
            const preview = document.getElementById('photo-preview');
            const webcam = document.getElementById('webcam');
            const overlay = document.getElementById('overlay');
            const snapOverlay = document.getElementById('snap-overlay');
            const retakeBtn = document.getElementById('retake-photo');
            
            preview.classList.add('hidden');
            webcam.classList.remove('hidden');
            overlay.classList.remove('hidden');
            document.getElementById('liveness-instruction').classList.remove('hidden');
            snapOverlay.classList.remove('hidden');
            retakeBtn.classList.add('hidden');
            
            if(typeof startFaceDetection === 'function') startFaceDetection();
            
            const sb = document.getElementById('submit-btn');
            if(sb) { sb.disabled = true; sb.classList.remove('active'); }
            document.getElementById('submit-text').textContent = 'Ambil Foto Verifikasi';
        }

        if(e.target.closest('#refresh-location')) {
            if(typeof startLocationTracking === 'function') startLocationTracking();
        }
    });

    document.addEventListener('go-to-form', () => {
        const presenceContainer = document.querySelector('[x-data]');
        if (presenceContainer && presenceContainer._x_dataStack) {
            const alpineData = presenceContainer._x_dataStack[0];
            if (alpineData) {
                alpineData.step = 'form';
                setTimeout(() => window.startCamera(), 150);
            }
        }
    });

    window.toggleMapPanel = () => {
        const panel = document.getElementById('map-panel');
        if (!panel) return;
        const isVisible = panel.style.display !== 'none';
        panel.style.display = isVisible ? 'none' : 'block';
        if (!isVisible && map) {
            setTimeout(() => map.invalidateSize(), 100);
        }
    };


    setInterval(() => {
        const now = new Date();
        const timeStr = now.toLocaleTimeString('en-GB');
        const el = document.getElementById('live-clock');
        if(el) el.textContent = timeStr;
        const el2 = document.getElementById('loc-live-clock');
        if(el2) el2.textContent = timeStr;
    }, 1000);

    document.addEventListener('go-to-choice', () => {
        if (locationWatcherId !== null) {
            navigator.geolocation.clearWatch(locationWatcherId);
            locationWatcherId = null;
        }
        cameraStarted = false;

        if (currentStream) {
            currentStream.getTracks().forEach(t => t.stop());
            currentStream = null;
        }
        if (detectionInterval) {
            clearInterval(detectionInterval);
            detectionInterval = null;
        }
        map = null;
        marker = null;
        isLocationInRange = null;

        if (window.presenceApp) {
            window.presenceApp.step = 'choice';
            window.presenceApp.isLocReady = false;
        } else {
            window.dispatchEvent(new CustomEvent('alpine:go-choice'));
        }
    });


</script>
@endpush
@endsection
