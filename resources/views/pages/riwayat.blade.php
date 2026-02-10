@extends('layouts.app')

@section('content')
<script>
    window.presenceData = @json($presenceStats);
</script>

<div class="history-container animate-fade-in" x-data="{ 
    showModal: false, 
    selectedData: null,
    currentDate: new Date(),
    months: ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'],
    presenceData: window.presenceData || {},
    
    formatTime(time) {
        return time ? time.substring(0, 5) : '--:--';
    },

    get currentMonthName() {
        return this.months[this.currentDate.getMonth()];
    },

    get currentYear() {
        return this.currentDate.getFullYear();
    },

    get calendarDays() {
        const year = this.currentDate.getFullYear();
        const month = this.currentDate.getMonth();
        const firstDay = new Date(year, month, 1);
        const lastDay = new Date(year, month + 1, 0);
        
        const days = [];
        
        // Padding for the start of the month (Monday start)
        let firstDayOfWeek = firstDay.getDay(); 
        let padding = firstDayOfWeek === 0 ? 6 : firstDayOfWeek - 1;
        
        for (let i = 0; i < padding; i++) {
            days.push({ day: null, date: null });
        }
        
        for (let i = 1; i <= lastDay.getDate(); i++) {
            const dateObj = new Date(year, month, i);
            // Manual ISO Date string to avoid localization issues (YYYY-MM-DD)
            const y = dateObj.getFullYear();
            const m = String(dateObj.getMonth() + 1).padStart(2, '0');
            const d = String(dateObj.getDate()).padStart(2, '0');
            const dateStr = `${y}-${m}-${d}`;
            
            days.push({
                day: i,
                date: dateStr,
                presence: this.presenceData ? this.presenceData[dateStr] : null
            });
        }
        
        return days;
    },

    prevMonth() {
        this.currentDate = new Date(this.currentDate.getFullYear(), this.currentDate.getMonth() - 1, 1);
    },

    nextMonth() {
        this.currentDate = new Date(this.currentDate.getFullYear(), this.currentDate.getMonth() + 1, 1);
    },

    openDetail(data) {
        if (data) {
            this.selectedData = data;
            this.showModal = true;
        }
    }
}">
    <!-- Header Content -->
    <div class="history-header">
        <div class="header-main">
            <h1 class="header-title">Riwayat <span class="text-brand">Aktivitas</span></h1>
            <p class="header-subtitle">Sentuh kalender untuk melihat detail kehadiran spesifik.</p>
        </div>
    </div>

    <!-- Quick Stats Tiles -->
    <div class="stats-grid">
        <!-- Total Kehadiran -->
        <div class="stat-card card-gradient shadow-premium">
            <div class="card-blur-circle"></div>
            <p class="stat-label">Total Kehadiran</p>
            <div class="stat-main">
                 <h3 class="stat-value">{{ $presences->total() }}</h3>
                 <span class="stat-unit">hari hadir</span>
            </div>
            <div class="stat-badge-pill">Periode: {{ date('F Y') }}</div>
        </div>

        <!-- Detail Stats -->
        <div class="stat-group">
            <div class="stat-card glass shadow-premium">
                <p class="stat-label text-dim">Log Terakhir</p>
                <div class="stat-item-row">
                    <div class="icon-box orange shadow-orange-light">
                        <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 8v4l3 3" stroke-width="2.5" stroke-linecap="round"/></svg>
                    </div>
                    <div>
                        <h4 class="stat-time text-main">
                            {{ $presences->first() && $presences->first()->time_in ? \Carbon\Carbon::parse($presences->first()->time_in)->format('H:i') : '--:--' }}
                        </h4>
                        <p class="stat-date text-dim">{{ $presences->first() ? \Carbon\Carbon::parse($presences->first()->date)->isoFormat('dddd, D MMM') : 'No Data' }}</p>
                    </div>
                </div>
            </div>

            <div class="stat-card glass shadow-premium">
                <p class="stat-label text-dim">Efisiensi Kerja</p>
                <div class="stat-item-row">
                    <div class="icon-box emerald shadow-emerald-light">
                        <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" stroke-width="2.5" stroke-linecap="round"/></svg>
                    </div>
                    <div>
                        @php
                            // Calculate Stats from the full dataset (presenceStats is a collection)
                            $totalAttendance = $presenceStats->count();
                            $lateCount = $presenceStats->filter(function($item) {
                                // presenceStats in view is a Keyed Collection of Arrays, not Models
                                return isset($item['status']) && $item['status'] === 'Terlambat';
                            })->count();
                            
                            $onTimeCount = $totalAttendance - $lateCount;
                            $percentage = $totalAttendance > 0 ? round(($onTimeCount / $totalAttendance) * 100) : 0;
                            
                            $colorClass = 'text-emerald';
                            $statusText = 'Sangat Baik';
                            
                            if($percentage < 95) { $colorClass = 'text-blue'; $statusText = 'Baik'; }
                            if($percentage < 85) { $colorClass = 'text-orange'; $statusText = 'Cukup'; }
                            if($percentage < 70) { $colorClass = 'text-red-500'; $statusText = 'Perlu Perbaikan'; }
                        @endphp
                        
                        <h4 class="stat-time text-main">{{ $percentage }}%</h4>
                        <p class="stat-status {{ $colorClass }}">{{ $statusText }} ({{ $lateCount }} Terlambat)</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Calendar Hub Section -->
    <div class="calendar-section animate-fade-in">
        <div class="data-hub glass shadow-premium" style="padding: 40px;">
            <div class="hub-header-custom">
                <div class="month-selector">
                    <button @click="prevMonth()" class="nav-btn">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7" stroke-width="3"/></svg>
                    </button>
                    <h2 class="current-month-label text-main" x-text="currentMonthName + ' ' + currentYear"></h2>
                    <button @click="nextMonth()" class="nav-btn">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7" stroke-width="3"/></svg>
                    </button>
                </div>
                <div class="hub-dots">
                    <div class="dot blue shadow-blue"></div>
                    <span class="text-dim label-small">IN</span>
                    <div class="dot orange shadow-orange" style="margin-left: 10px;"></div>
                    <span class="text-dim label-small">OUT</span>
                </div>
            </div>
            
            <div class="calendar-grid-wrapper">
                <div class="calendar-weekdays">
                    <span>Sen</span><span>Sel</span><span>Rab</span><span>Kam</span><span>Jum</span><span>Sab</span><span>Min</span>
                </div>
                <div class="calendar-days-grid">
                    <template x-for="(day, index) in calendarDays" :key="index">
                        <div class="day-cell" :class="day.day ? 'active-day' : 'empty-day'" @click="day.presence ? openDetail(day.presence) : null">
                            <span class="day-number" x-text="day.day" :class="day.presence ? 'text-brand font-black' : 'text-dim'"></span>
                            
                            <template x-if="day.presence">
                                <div class="presence-indicators">
                                    <div class="indicator-dot bg-blue" x-show="day.presence.time_in"></div>
                                    <div class="indicator-dot bg-orange" x-show="day.presence.time_out"></div>
                                </div>
                            </template>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>

    <!-- Detail Modal (Alpine.js) -->
    <div x-show="showModal" 
         class="modal-overlay" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         x-cloak
         @click="showModal = false">
        
        <div class="modal-content glass shadow-high" @click.stop>
            <template x-if="selectedData">
                <div>
                    <div class="modal-header">
                        <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                            <div>
                                <h2 class="text-main" x-text="selectedData.dayName"></h2>
                                <p class="text-dim uppercase tracking-widest font-black" style="font-size: 10px;" x-text="selectedData.dateFormatted"></p>
                            </div>
                            <template x-if="selectedData.status">
                                <span :class="selectedData.status === 'Terlambat' ? 'badge-warning' : 'badge-primary'" 
                                      class="status-badge-mini" 
                                      x-text="selectedData.status"></span>
                            </template>
                        </div>
                        <template x-if="selectedData.shift_name">
                            <p class="text-brand font-bold text-xs mt-2" x-text="'Shift: ' + selectedData.shift_name"></p>
                        </template>
                    </div>

                    <div class="modal-body">
                        <div class="time-grid">
                            <div class="time-card glass blue-border">
                                <span class="text-dim label-tiny">Masuk</span>
                                <h3 class="text-main text-2xl font-black" x-text="formatTime(selectedData.time_in)"></h3>
                            </div>
                            <div class="time-card glass orange-border">
                                <span class="text-dim label-tiny">Pulang</span>
                                <h3 class="text-main text-2xl font-black" x-text="formatTime(selectedData.time_out)"></h3>
                            </div>
                        </div>

                        <div class="photo-grid mt-6" x-show="selectedData.image_in || selectedData.image_out" style="display: flex; gap: 15px; margin-top: 20px;">
                            <template x-if="selectedData.image_in">
                                <div class="photo-box">
                                    <img :src="'/images/' + selectedData.image_in" class="photo-img shadow-premium">
                                    <span class="photo-label bg-blue shadow-blue">IN</span>
                                </div>
                            </template>
                            <template x-if="selectedData.image_out">
                                <div class="photo-box">
                                    <img :src="'/images/' + selectedData.image_out" class="photo-img shadow-premium">
                                    <span class="photo-label bg-orange shadow-orange">OUT</span>
                                </div>
                            </template>
                        </div>

                        <div class="note-box glass mt-6" x-show="selectedData.note" style="margin-top: 20px; padding: 20px; border-radius: 20px;">
                            <span class="text-dim label-tiny">Catatan</span>
                            <p class="text-main text-sm italic mt-1" x-text="selectedData.note"></p>
                        </div>
                    </div>
                </div>
            </template>

            <button class="btn-close glass" @click="showModal = false">Tutup Detail</button>
        </div>
    </div>
</div>

@push('scripts')
<style>
    [x-cloak] { display: none !important; }

    :root {
        --brand-blue: #3b82f6;
        --brand-blue-dark: #2563eb;
        --brand-blue-transparent: rgba(59, 130, 246, 0.1);
        --brand-orange: #f97316;
        --brand-orange-transparent: rgba(249, 115, 22, 0.1);
        --brand-emerald: #10b981;
        --brand-emerald-transparent: rgba(16, 185, 129, 0.1);
        
        /* Light mode colors */
        --text-main: #1e293b;
        --text-dim: #94a3b8;
        --text-label: #64748b;
        --card-bg: #ffffff;
        --card-border: rgba(0, 0, 0, 0.05);
        --hub-bg: rgba(255, 255, 255, 0.7);
        --hover-bg: rgba(0, 0, 0, 0.02);
        --shadow-color: rgba(0, 0, 0, 0.05);
        --shadow-hover: rgba(0, 0, 0, 0.1);
    }

    .dark {
        /* Dark mode colors */
        --text-main: #f8fafc;
        --text-dim: #64748b;
        --text-label: #94a3b8;
        --card-bg: #1f2937;
        --card-border: rgba(255, 255, 255, 0.08);
        --hub-bg: rgba(31, 41, 55, 0.8);
        --hover-bg: rgba(255, 255, 255, 0.05);
        --shadow-color: rgba(0, 0, 0, 0.3);
        --shadow-hover: rgba(0, 0, 0, 0.5);
    }

    .text-main { color: var(--text-main); transition: color 0.3s ease; }
    .text-dim { color: var(--text-dim); transition: color 0.3s ease; }
    
    .history-container { 
        max-width: 1200px; 
        margin: 0 auto; 
        padding: 60px 20px 40px; 
        font-family: 'Outfit', sans-serif; 
    }
    
    .history-header { display: flex; justify-content: space-between; align-items: flex-end; gap: 30px; margin-bottom: 60px; flex-wrap: wrap; }
    .header-title { font-size: clamp(2rem, 5vw, 3.5rem); font-weight: 900; letter-spacing: -2px; line-height: 1; color: var(--text-main); }
    .text-brand { color: var(--brand-blue); }
    .header-subtitle { color: var(--text-dim); font-size: 1rem; margin-top: 10px; }
    .hub-badge { display: inline-flex; align-items: center; gap: 10px; padding: 6px 16px; background: var(--brand-blue-transparent); border: 1px solid rgba(59, 130, 246, 0.3); border-radius: 50px; color: var(--brand-blue); font-size: 10px; font-weight: 900; text-transform: uppercase; margin-bottom: 15px; }

    .status-badge-mini {
        padding: 4px 12px;
        border-radius: 10px;
        font-size: 10px;
        font-weight: 900;
        text-transform: uppercase;
        color: white;
    }
    .status-badge-mini.badge-primary { background: var(--brand-blue); }
    .status-badge-mini.badge-warning { background: var(--brand-orange); }

    .stats-grid { display: grid; grid-template-columns: 1.2fr 1.8fr; gap: 30px; margin-bottom: 60px; }
    .stat-card { border-radius: 40px; padding: 40px; position: relative; overflow: hidden; background: var(--card-bg); border: 1px solid var(--card-border); transition: all 0.3s ease; }
    .card-gradient { background: linear-gradient(135deg, var(--brand-blue), var(--brand-blue-dark)); color: white; border: none; }
    .stat-value { font-size: 6rem; font-weight: 900; letter-spacing: -5px; line-height: 1; margin: 0; color: inherit; }
    .stat-unit { font-size: 1.5rem; font-weight: 700; opacity: 0.5; font-style: italic; }
    .stat-label { color: var(--text-dim); font-size: 0.875rem; font-weight: 600; text-transform: uppercase; letter-spacing: 1px; }
    .stat-group { display: grid; grid-template-columns: 1fr 1fr; gap: 30px; }
    .stat-item-row { display: flex; align-items: center; gap: 15px; }
    .icon-box { width: 70px; height: 70px; border-radius: 25px; display: flex; align-items: center; justify-content: center; transition: all 0.3s ease; }
    .icon-box.orange { background: var(--brand-orange-transparent); color: var(--brand-orange); }
    .icon-box.emerald { background: var(--brand-emerald-transparent); color: var(--brand-emerald); }
    .stat-time { font-size: 2.5rem; font-weight: 900; letter-spacing: -1.5px; color: var(--text-main); }
    .stat-date { color: var(--text-dim); font-size: 0.875rem; margin-top: 4px; }
    .stat-status { font-weight: 600; font-size: 0.875rem; }
    .text-emerald { color: var(--brand-emerald); }
    .stat-badge-pill { display: inline-block; padding: 4px 12px; background: var(--hover-bg); border-radius: 20px; font-size: 0.75rem; font-weight: 600; color: var(--text-label); margin-top: 10px; }

    /* Custom Calendar Hub */
    .data-hub { background: var(--hub-bg); backdrop-blur: 20px; border: 1px solid var(--card-border); border-radius: 40px; transition: all 0.3s ease; }
    .hub-header-custom { display: flex; justify-content: space-between; align-items: center; margin-bottom: 40px; flex-wrap: wrap; gap: 20px; }
    .month-selector { display: flex; align-items: center; gap: 20px; }
    .nav-btn { width: 50px; height: 50px; border-radius: 15px; display: flex; align-items: center; justify-content: center; background: var(--hover-bg); border: 1px solid var(--card-border); color: var(--text-dim); cursor: pointer; transition: all 0.2s; }
    .nav-btn:hover { background: var(--brand-blue); color: white; border-color: var(--brand-blue); transform: scale(1.1); }
    .current-month-label { font-size: 2rem; font-weight: 900; letter-spacing: -1px; color: var(--text-main); }
    .hub-dots { display: flex; align-items: center; gap: 8px; }
    .dot { width: 10px; height: 10px; border-radius: 50%; }
    .dot.blue { background: var(--brand-blue); }
    .dot.orange { background: var(--brand-orange); }
    .label-small { font-size: 0.75rem; font-weight: 700; color: var(--text-dim); text-transform: uppercase; }
    .shadow-blue { box-shadow: 0 0 8px var(--brand-blue); }
    .shadow-orange { box-shadow: 0 0 8px var(--brand-orange); }

    .calendar-weekdays { display: grid; grid-template-columns: repeat(7, 1fr); margin-bottom: 20px; border-bottom: 1px solid var(--card-border); padding-bottom: 15px; }
    .calendar-weekdays span { text-align: center; font-size: 10px; font-weight: 900; text-transform: uppercase; letter-spacing: 2px; color: var(--text-dim); }

    .calendar-grid-wrapper { width: 100%; overflow: hidden; }
    .calendar-days-grid { display: grid; grid-template-columns: repeat(7, 1fr); gap: 8px; }
    .day-cell { 
        aspect-ratio: 1; 
        border-radius: 20px; 
        padding: 10px; 
        display: flex; 
        flex-direction: column; 
        justify-content: space-between; 
        align-items: center;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1); 
        position: relative; 
        border: 1px solid transparent;
        min-height: 60px;
        overflow: hidden;
    }
    .empty-day { opacity: 0.3; }
    .active-day { background: var(--hover-bg); cursor: pointer; border: 1px solid var(--card-border); }
    .active-day:hover { background: var(--card-bg); box-shadow: 0 20px 40px var(--shadow-hover); transform: translateY(-5px); border-color: var(--brand-blue); }
    .day-number { 
        font-size: clamp(0.9rem, 2vw, 1.2rem); 
        font-weight: 700; 
        transition: color 0.3s ease; 
        color: var(--text-main);
        line-height: 1;
    }
    .day-number.text-dim { color: var(--text-dim); }
    .day-number.font-black { font-weight: 900; }
    
    .presence-indicators { display: flex; gap: 4px; margin-top: 4px; }
    .indicator-dot { width: 8px; height: 8px; border-radius: 50%; box-shadow: 0 0 10px transparent; transition: all 0.3s ease; }
    .bg-blue { background: var(--brand-blue); }
    .bg-orange { background: var(--brand-orange); }
    .day-cell:hover .indicator-dot.bg-blue { box-shadow: 0 0 12px var(--brand-blue); }
    .day-cell:hover .indicator-dot.bg-orange { box-shadow: 0 0 12px var(--brand-orange); }

    .modal-overlay { 
        position: fixed; 
        inset: 0; 
        background: rgba(0,0,0,0.85); 
        backdrop-filter: blur(20px); 
        display: flex; 
        align-items: center; 
        justify-content: center; 
        z-index: 99999; 
        padding: 20px; 
    }
    .modal-content { 
        background: var(--card-bg); 
        width: 100%; 
        max-width: 500px; 
        border-radius: 40px; 
        padding: 40px; 
        border: 1px solid var(--card-border); 
        overflow: hidden; 
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.7);
        position: relative;
        z-index: 100000;
    }
    
    .modal-header { margin-bottom: 20px; }
    .modal-header h2 { font-size: 1.75rem; font-weight: 900; color: var(--text-main); margin: 0; }
    .modal-header p { color: var(--text-dim); margin-top: 4px; }
    
    .time-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-top: 30px; }
    .time-card { padding: 20px; border-radius: 25px; text-align: center; background: var(--hover-bg); border: 1px solid var(--card-border); transition: all 0.3s ease; }
    .blue-border { border-left: 5px solid var(--brand-blue); }
    .orange-border { border-left: 5px solid var(--brand-orange); }
    .label-tiny { font-size: 0.625rem; font-weight: 900; text-transform: uppercase; letter-spacing: 1px; color: var(--text-dim); display: block; margin-bottom: 8px; }
    
    .photo-img { width: 100%; height: 200px; object-fit: cover; border-radius: 25px; transition: transform 0.5s ease; }
    .photo-box { flex: 1; position: relative; border-radius: 25px; overflow: hidden; }
    .photo-box:hover .photo-img { transform: scale(1.1); }
    .photo-label { position: absolute; top: 15px; right: 15px; padding: 6px 14px; border-radius: 12px; font-weight: 900; font-size: 11px; color: white; backdrop-blur: 10px; }
    
    .btn-close { width: 100%; margin-top: 30px; padding: 18px; border-radius: 20px; border: 1px solid var(--card-border); font-weight: 900; text-transform: uppercase; letter-spacing: 2px; color: var(--brand-blue); cursor: pointer; transition: all 0.3s ease; background: var(--hover-bg); }
    .btn-close:hover { background: var(--brand-blue); color: white; transform: translateY(-2px); box-shadow: 0 10px 20px rgba(59, 130, 246, 0.3); }

    .glass { background: var(--hub-bg); backdrop-blur: 20px; border: 1px solid var(--card-border); }
    .shadow-premium { box-shadow: 0 10px 30px var(--shadow-color); }
    .shadow-high { box-shadow: 0 25px 50px var(--shadow-hover); }
    .shadow-orange-light { box-shadow: 0 4px 12px rgba(249, 115, 22, 0.2); }
    .shadow-emerald-light { box-shadow: 0 4px 12px rgba(16, 185, 129, 0.2); }

    @media (max-width: 900px) {
        .history-header { flex-direction: column; align-items: flex-start; margin-bottom: 40px; gap: 20px; }
        .stats-grid { grid-template-columns: 1fr; }
        .stat-value { font-size: 4.5rem; }
        .stat-group { grid-template-columns: 1fr; }
        .calendar-section { padding: 0; }
        .data-hub { padding: 20px !important; border-radius: 30px; }
        .current-month-label { font-size: 1.5rem; }
        .calendar-days-grid { gap: 5px; }
        .day-cell { padding: 6px; border-radius: 10px; min-height: 45px; }
        .day-number { font-size: 0.85rem; }
        .indicator-dot { width: 5px; height: 5px; }
        .presence-indicators { gap: 2px; margin-top: 2px; }
        .time-grid { grid-template-columns: 1fr; }
        .photo-grid { flex-direction: column; }
    }

    /* Pagination Customization */
    .pagination-wrapper {
        padding: 20px;
        display: flex;
        justify-content: center;
        width: 100%;
    }
    
    .pagination {
        display: flex;
        justify-content: center;
        gap: 4px;
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .page-item .page-link {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 36px;
        height: 36px;
        border-radius: 8px;
        color: var(--text-secondary);
        font-weight: 600;
        font-size: 0.875rem;
        background: var(--hover-bg);
        border: 1px solid var(--card-border);
        transition: all 0.2s;
        text-decoration: none;
    }

    .page-item.active .page-link {
        background: var(--brand-blue);
        color: white;
        border-color: var(--brand-blue);
        box-shadow: 0 4px 6px rgba(59, 130, 246, 0.3);
    }

    .page-item.disabled .page-link {
        opacity: 0.5;
        cursor: not-allowed;
    }

    @media (max-width: 480px) {
        .data-hub { padding: 15px !important; border-radius: 25px; }
        .calendar-days-grid { gap: 3px; }
        .day-cell { padding: 4px; border-radius: 8px; min-height: 38px; }
        .day-number { font-size: 0.75rem; }
        .indicator-dot { width: 4px; height: 4px; }
        .presence-indicators { gap: 2px; }
        .calendar-weekdays span { font-size: 7px; letter-spacing: 0.5px; }
        .nav-btn { width: 40px; height: 40px; }
        .current-month-label { font-size: 1.2rem; }
    }
</style>
@endpush
@endsection
