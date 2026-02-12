@extends('layouts.app')
@section('title', 'Presensi')

@section('content')
<div x-data="{ 
    step: 'choice', 
    type: 'in',
    hasIn: {{ ($presence && $presence->time_in) ? 'true' : 'false' }},
    hasOut: {{ ($presence && $presence->time_out) ? 'true' : 'false' }},
}" class="presence-container">

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
                <div @click="if(!hasIn && {{ $isWorkingDay ? 'true' : 'false' }} && !{{ $activeShiftInfo['is_expired'] ? 'true' : 'false' }} && !{{ $activeShiftInfo['is_too_early'] ? 'true' : 'false' }}) { type = 'in'; step = 'form'; $nextTick(() => { startCamera(); getLocation(); }) }" 
                    :class="(hasIn || !{{ $isWorkingDay ? 'true' : 'false' }} || {{ $activeShiftInfo['is_expired'] ? 'true' : 'false' }} || {{ $activeShiftInfo['is_too_early'] ? 'true' : 'false' }}) ? 'card-disabled' : 'card-entry card-blue'"
                    class="presence-card">
                    
                    <div class="card-icon-wrapper">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 16l-4-4m0 0l4-4m-4 4h14" />
                        </svg>
                    </div>
                    <h3 class="card-title">Absen Masuk {{ $activeShiftInfo['shift_name'] ? '- ' . $activeShiftInfo['shift_name'] : '' }}</h3>
                    <p class="card-status" :class="hasIn ? 'status-active' : ''">
                        <template x-if="hasIn">
                            <span>Sudah Terdaftar • {{ $presence && $presence->time_in ? \Carbon\Carbon::parse($presence->time_in)->format('H:i') : '' }}</span>
                        </template>
                        <template x-if="!hasIn">
                            <span>
                                @if(!$isWorkingDay)
                                    Bukan hari kerja
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

                <div @click="if(hasIn && !hasOut) { type = 'out'; step = 'form'; $nextTick(() => { startCamera(); getLocation(); }) }" 
                    :class="(hasOut || !hasIn) ? 'card-disabled' : 'card-entry card-orange'"
                    class="presence-card">
                    
                    <div class="card-icon-wrapper">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 16l4-4m0 0l-4-4m4 4H7" />
                        </svg>
                    </div>
                    <h3 class="card-title">Absen Pulang</h3>
                    <p class="card-status" :class="hasOut ? 'status-orange' : ''">
                        <template x-if="hasOut">
                            <span>Sudah Terdaftar • {{ $presence && $presence->time_out ? \Carbon\Carbon::parse($presence->time_out)->format('H:i') : '' }}</span>
                        </template>
                        <template x-if="!hasOut">
                            <span x-text="!hasIn ? 'Masuk dahulu' : 'Selesaikan tugas'"></span>
                        </template>
                    </p>

                    <template x-if="hasOut">
                        <div class="status-badge badge-orange">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7" stroke-width="3"/></svg>
                        </div>
                    </template>
                </div>
            </div>



            @if($presence)
            <div class="summary-container animate-slide-up">
                <div class="summary-divider">
                    <span class="divider-text">Ringkasan Kehadiran</span>
                </div>
                <div class="summary-grid">
                    <div class="summary-pill glass">
                        <p class="summary-label text-emerald">Check In</p>
                        <p class="summary-time">{{ $presence->time_in ? \Carbon\Carbon::parse($presence->time_in)->format('H:i') : '--:--' }}</p>
                    </div>
                    <div class="summary-pill glass">
                        <p class="summary-label text-orange">Check Out</p>
                        <p class="summary-time">{{ $presence->time_out ? \Carbon\Carbon::parse($presence->time_out)->format('H:i') : '--:--' }}</p>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </template>

    <template x-if="step === 'form'">
        <div class="form-screen animate-slide-right">
            <div class="form-header">
                <button @click="step = 'choice'" class="btn-back">
                    <div class="icon-circle glass">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7" stroke-width="3"/></svg>
                    </div>
                    <span>Kembali</span>
                </button>
                <div :class="type === 'in' ? 'badge-primary' : 'badge-warning'" class="type-indicator">
                    Konfirmasi <span x-text="type === 'in' ? 'Masuk' : 'Pulang'"></span>
                </div>
            </div>

            <form action="{{ route('presence.store') }}" method="POST" id="attendance-form" class="main-form-grid">
                @csrf
                <input type="hidden" name="type" :value="type">
                
                <div class="media-column">
                    <div class="content-card glass">
                        <div class="card-header">
                            <h3 class="card-header-title">Verifikasi Biometrik</h3>
                            <div class="card-actions">
                                <button type="button" @click="toggleCamera()" class="btn-action">
                                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" stroke-width="2.5"/></svg>
                                </button>
                                <button type="button" id="retake-photo" class="btn-action btn-danger hidden">
                                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-width="2.5"/></svg>
                                </button>
                            </div>
                        </div>

                        <div class="camera-viewport">
                            <video id="webcam" autoplay playsinline class="video-preview"></video>
                            <canvas id="overlay" class="overlay-canvas"></canvas>
                            <img id="photo-preview" class="image-preview hidden">
                            <canvas id="canvas" class="hidden"></canvas>
                            
                            <div id="camera-status" class="overlay-loading">
                                <div class="spinner"></div>
                                <p class="loading-text" id="loading-text-content">Inisialisasi Sensor...</p>
                            </div>

                            <div id="snap-overlay" class="snap-action-wrapper">
                                <button type="button" id="snap-btn" class="btn-snap">
                                    <div class="inner-snap-circle"></div>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="content-card glass">
                        <div class="card-header">
                            <h3 class="card-header-title">Kordinat GPS</h3>
                            <button type="button" id="refresh-location" class="btn-action">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" stroke-width="2.5"/><circle cx="12" cy="11" r="3" stroke-width="2.5"/></svg>
                            </button>
                        </div>
                        <div id="map" class="map-container"></div>
                        <div class="location-status-bar glass">
                            <div id="loc-dot" class="dot-orange"></div>
                            <span id="loc-text" class="status-msg">Sinkronisasi Satelit...</span>
                        </div>
                    </div>
                </div>

                <div class="details-column">
                    <div class="clock-card">
                        <h2 class="clock-time" id="live-clock">00:00:00</h2>
                        <p class="clock-date">
                            {{ \Carbon\Carbon::now()->isoFormat('dddd, D MMMM YYYY') }}
                        </p>
                    </div>

                    <div class="content-card glass">
                        <div class="form-fields">
                            <div class="field-group">
                                <label class="field-label">Catatan Tugas (Opsional)</label>
                                <textarea name="note" rows="5" class="glass-textarea" placeholder="Tuliskan keterangan jika ada..."></textarea>
                            </div>
                            
                            <input type="hidden" name="location" id="location-input">
                            <input type="hidden" name="image" id="image-input">
                            <input type="hidden" name="is_face_detected" id="face-detected-input" value="false">

                            <button type="submit" id="submit-btn" disabled class="btn-submit">
                                <span id="submit-text">Ambil Foto Verifikasi</span>
                            </button>
                        </div>
                    </div>

                    <div class="policy-card glass-green">
                        <h4 class="policy-title">
                             <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M2.166 4.9L10 1.55l7.834 3.35a1 1 0 01.666.941v6.275c0 3.33-1.842 6.427-4.834 8.16L10 21.45l-3.666-2.174c-2.992-1.733-4.834-4.83-4.834-8.16V5.841a1 1 0 01.666-.941zM10 11.5a1.5 1.5 0 100-3 1.5 1.5 0 000 3z" clip-rule="evenodd"/></svg>
                             Secure Access
                        </h4>
                        <p class="policy-text text-emerald">Sistem ini memverifikasi lokasi & biometrik secara otomatis untuk keamanan data rumah sakit.</p>
                    </div>
                </div>
            </form>
        </div>
    </template>
</div>

@push('scripts')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
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
        padding: 40px 20px;
        font-family: 'Outfit', sans-serif;
        position: relative;
    }

    .choice-screen {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        min-height: 60vh;
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
        border-radius: 40px;
        padding: 50px 30px;
        text-align: center;
        position: relative;
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        cursor: pointer;
        backdrop-filter: blur(15px);
    }
    
    .dark .presence-card {
        background: rgba(30, 41, 59, 0.5);
        border-color: rgba(255, 255, 255, 0.05);
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
        width: 90px;
        height: 90px;
        border-radius: 30px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 30px;
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
        font-size: 1.8rem; 
        font-weight: 800; 
        color: var(--text-main); 
        margin-bottom: 10px; 
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
        .media-column > .content-card:first-child { order: 2; } /* Camera card is first child of media-column */
        .media-column > .content-card:last-child { order: 3; } /* GPS card is last child of media-column */
        .details-column > .content-card { order: 4; } /* Form fields */
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
    .video-preview, .image-preview { 
        width: 100%; 
        height: 100%; 
        object-fit: cover; 
    }
    .overlay-canvas {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
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

    @media (max-width: 900px) {
        .presence-container { padding: 30px 15px; }
        
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

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    const OFFICE_LOCATION = "{{ $settings['office_location'] ?? '' }}"; // Format: "-7.123, 108.123"
    const OFFICE_RADIUS = {{ $settings['office_radius'] ?? 500 }}; // Meters
    let isLocationInRange = false;

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
            
            // Attempt to load TinyFaceDetector first (Critical)
            await faceapi.nets.tinyFaceDetector.loadFromUri(MODEL_URL);
            
            // Attempt to load Landmark (Optional for basic, required for strict)
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

            currentStream = await navigator.mediaDevices.getUserMedia({
                video: { facingMode: useFrontCamera ? "user" : "environment", width: { ideal: 640 }, height: { ideal: 480 } }
            });
            webcam.srcObject = currentStream;
            
            webcam.onloadedmetadata = () => {
                camStatus.style.display = 'none';
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

        const displaySize = { width: video.videoWidth, height: video.videoHeight };
        faceapi.matchDimensions(canvas, displaySize);

        if(detectionInterval) clearInterval(detectionInterval);

        detectionInterval = setInterval(async () => {
            if (video.paused || video.ended) return;

            let detection = null;
            
            if (faceapi.nets.faceLandmark68Net.isLoaded) {
                try {
                    detection = await faceapi.detectSingleFace(video, new faceapi.TinyFaceDetectorOptions()).withFaceLandmarks();
                } catch (e) {
                    console.warn("Landmark detection error, falling back to simple detection", e);
                }
            }
            
            if (!detection) {
                 const simpleDetections = await faceapi.detectAllFaces(video, new faceapi.TinyFaceDetectorOptions());
                 if(simpleDetections.length > 0) {
                     detection = { detection: simpleDetections[0], landmarks: null };
                 }
            }
            
            const ctx = canvas.getContext('2d');
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            
            if (useFrontCamera) {
                // ctx.save();
                // ctx.scale(-1, 1);
                // ctx.translate(-canvas.width, 0);
            }
            
            if (useFrontCamera) {
                // ctx.restore();
            }
            const faceDetectedInput = document.getElementById('face-detected-input');
            
            if (detection) {
                const resizedDetections = faceapi.resizeResults(detection, displaySize);
                faceapi.draw.drawDetections(canvas, resizedDetections);
                
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
                    
                    const avgEAR = (getEAR(leftEye) + getEAR(rightEye)) / 2;
                    
                    if(!window.livenessState) window.livenessState = 0;
                    
                    let instruction = "";
                    let color = "white";
                    
                    switch(window.livenessState) {
                        case 0:
                            instruction = "Lihat Lurus ke Kamera";
                            if (noseRelX > 0.4 && noseRelX < 0.6) {
                                setTimeout(() => { if(window.livenessState === 0) window.livenessState = 1; }, 1000);
                            }
                            break;
                        case 1:
                            instruction = "KEDIPKAN Mata Anda";
                            if (avgEAR < 0.25) window.livenessState = 2; 
                            break;
                        case 2:
                            instruction = "VERIFIKASI BERHASIL!";
                            color = "#10b981";
                            if(faceDetectedInput) faceDetectedInput.value = "true";
                            break;
                    }
                    
                    if (useFrontCamera) ctx.restore(); 
                    
                    ctx.font = "900 24px 'Outfit', sans-serif";
                    ctx.textAlign = "center";
                    ctx.lineWidth = 4;
                    ctx.strokeStyle = 'black';
                    ctx.strokeText(instruction, canvas.width/2, 50);
                    ctx.fillStyle = color;
                    ctx.fillText(instruction, canvas.width/2, 50);

                    if (window.livenessState === 2) {
                        snapBtn.style.borderColor = "#10b981"; 
                        snapBtn.style.boxShadow = "0 0 20px rgba(16, 185, 129, 0.5)";
                        snapBtn.disabled = false;
                    } else {
                         snapBtn.style.borderColor = "#f59e0b";
                         snapBtn.style.boxShadow = "none";
                         if(faceDetectedInput) faceDetectedInput.value = "false";
                    }
                    
                } else {
                    if (useFrontCamera) ctx.restore();
                    
                    ctx.font = "bold 16px Arial";
                    ctx.fillStyle = "yellow";
                    ctx.textAlign = "center";
                    ctx.fillText("Model Liveness Tidak Lengkap", canvas.width/2, 30);
                    
                    snapBtn.style.borderColor = "#10b981";
                    snapBtn.style.boxShadow = "0 0 20px rgba(16, 185, 129, 0.5)";
                    snapBtn.disabled = false;
                    if(faceDetectedInput) faceDetectedInput.value = "true";
                }

            } else {
                if (useFrontCamera) ctx.restore();
                
                if(faceDetectedInput) faceDetectedInput.value = "false";
                window.livenessState = 0;
                snapBtn.style.borderColor = "#f59e0b";
                snapBtn.style.boxShadow = "none";
            }

        }, 100);
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

    window.getLocation = () => {
        const dot = document.getElementById('loc-dot');
        const text = document.getElementById('loc-text');
        if(!dot) return;

        dot.className = 'dot-orange animate-pulse';
        text.textContent = "Sinkronisasi Satelit...";

        navigator.geolocation.getCurrentPosition(pos => {
            const { latitude, longitude, accuracy } = pos.coords;
            document.getElementById('location-input').value = `${latitude}, ${longitude}`;
            
            let distance = 0;
            let statusText = "";
            let inRange = false;
            
            if(OFFICE_LOCATION) {
                try {
                    const parts = OFFICE_LOCATION.split(',').map(n => parseFloat(n.trim()));
                    const offLat = parts[0];
                    const offLng = parts[1];
                    
                    if(!isNaN(offLat) && !isNaN(offLng)) {
                        distance = calculateDistance(latitude, longitude, offLat, offLng);
                        
                        if(distance <= OFFICE_RADIUS) {
                            inRange = true;
                            statusText = `DALAM JANGKAUAN (${distance.toFixed(0)}m)`;
                        } else {
                            inRange = false;
                            statusText = `DI LUAR JANGKAUAN (${distance.toFixed(0)}m)`;
                        }
                    } else {
                        inRange = true; 
                        statusText = "LOKASI KANTOR BELUM DISET";
                    }
                } catch(e) {
                    console.error(e);
                    inRange = true; 
                    statusText = "ERROR KONFIGURASI LOKASI";
                }
            } else {
                inRange = true;
                statusText = "SKIP LOKASI (DEV MOOE)";
            }
            
            isLocationInRange = inRange;
            text.textContent = statusText;

            if(inRange) {
                dot.className = 'dot-green';
            } else {
                dot.className = 'dot-red animate-pulse';
            }
            
            if(!map) {
                map = L.map('map', { zoomControl: false }).setView([latitude, longitude], 17);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
                marker = L.marker([latitude, longitude]).addTo(map);
                
                if(OFFICE_LOCATION) {
                     const parts = OFFICE_LOCATION.split(',').map(n => parseFloat(n.trim()));
                     if(parts.length === 2 && !isNaN(parts[0])) {
                         L.circle(parts, {
                            color: '#3b82f6',
                            fillColor: '#3b82f6',
                            fillOpacity: 0.1,
                            radius: OFFICE_RADIUS
                        }).addTo(map);
                     }
                }

                setTimeout(() => map.invalidateSize(), 800);
            } else {
                map.setView([latitude, longitude]);
                marker.setLatLng([latitude, longitude]);
                map.invalidateSize();
            }
        }, err => {
            text.textContent = "GPS GAGAL: " + err.message;
            dot.className = 'dot-orange';
            isLocationInRange = false;
        }, { enableHighAccuracy: true });
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

        
            const locText = document.getElementById('loc-text').textContent;
            
            if (!isLocationInRange) {
                Swal.fire({
                    icon: 'error',
                    title: 'Lokasi Tidak Valid',
                    text: 'Anda berada di luar jangkauan kantor! ' + locText,
                    confirmButtonColor: '#ef4444'
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

            canvas.width = webcam.videoWidth;
            canvas.height = webcam.videoHeight;
            const ctx = canvas.getContext('2d');
            
            ctx.translate(canvas.width, 0);
            ctx.scale(-1, 1);
            
            ctx.drawImage(webcam, 0, 0, canvas.width, canvas.height);
            
            const data = canvas.toDataURL('image/jpeg', 0.85);

            if(window.detectionInterval) clearInterval(window.detectionInterval);
            
            preview.src = data;
            preview.classList.remove('hidden');
            
            webcam.classList.add('hidden');
            document.getElementById('overlay').classList.add('hidden'); 
            snapOverlay.classList.add('hidden');
            
            retakeBtn.classList.remove('hidden');
            imageInput.value = data;
            
            submitBtn.disabled = false;
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
            snapOverlay.classList.remove('hidden');
            retakeBtn.classList.add('hidden');
            
            if(typeof startFaceDetection === 'function') startFaceDetection();
            
            document.getElementById('submit-btn').disabled = true;
            document.getElementById('submit-text').textContent = 'Ambil Foto Verifikasi';
        }

        if(e.target.closest('#refresh-location')) {
            if(typeof getLocation === 'function') getLocation();
        }
    });

    setInterval(() => {
        const el = document.getElementById('live-clock');
        if(el) {
            const now = new Date();
            el.textContent = now.toLocaleTimeString('en-GB');
        }
    }, 1000);
</script>
@endpush
@endsection
