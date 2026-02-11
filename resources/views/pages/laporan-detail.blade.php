@extends('layouts.app')

@section('content')
<div x-data="{ openDetail(detail) { 
        // Optional: Modal logic if needed later
    } 
}">
    <!-- Header -->
    <div class="mb-10">
        <!-- Breadcrumb / Back Navigation -->
        <div class="mb-4">
            <a href="{{ url()->previous() == url()->current() ? route('hrd.report') : url()->previous() }}" 
               class="inline-flex items-center text-sm font-medium text-gray-500 hover:text-brand-500 dark:text-gray-400 dark:hover:text-brand-400 transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Kembali ke Laporan
            </a>
        </div>

        <div class="flex flex-col gap-1">
            <h1 class="text-4xl md:text-5xl lg:text-6xl font-black text-gray-900 dark:text-white tracking-tight leading-[1.1]">
                Detail <span class="text-brand-500">Laporan Pegawai</span>
            </h1>
            <p class="text-lg text-gray-500 dark:text-gray-400 font-medium ml-1">
                Detail informasi kehadiran pegawai
            </p>
        </div>
    </div>

    <!-- Top Section: User Info and Calendar Side-by-Side -->
    <div class="flex flex-col lg:flex-row gap-6 mb-8">
        
        <!-- User Info Card (Wide) -->
        <div class="flex-1 settings-card glass flex flex-col relative overflow-hidden min-h-[290px]">
            <!-- Decorative BG -->
            <div class="absolute top-0 right-0 w-64 h-64 bg-gradient-to-br from-blue-500/5 to-purple-500/5 rounded-bl-full -mr-10 -mt-10 pointer-events-none"></div>

            <div class="flex-1 p-8 sm:p-10 relative z-10 flex flex-col justify-center">
                <div class="space-y-6">
                    <div>
                        <h2 class="text-3xl sm:text-4xl font-black text-gray-900 dark:text-white mb-3 leading-tight tracking-tight">
                            {{ $presence->user->name }}
                        </h2>
                        <div class="flex items-center gap-2 text-lg text-gray-500 dark:text-gray-400 font-medium">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                            {{ $presence->user->unit }}
                        </div>
                    </div>

                    <div class="flex flex-wrap items-center gap-4 mt-4">
                        <div class="flex items-center gap-3 px-4 py-2.5 rounded-xl bg-gray-50 dark:bg-gray-800/80 border border-gray-200 dark:border-gray-700">
                            <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"/></svg>
                            <span class="text-base font-bold text-gray-700 dark:text-gray-300 font-mono tracking-wide">{{ $presence->user->nip }}</span>
                        </div>

                        <div class="flex items-center gap-3 px-4 py-2.5 rounded-xl bg-blue-50 dark:bg-blue-900/20 border border-blue-100 dark:border-blue-800">
                            <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span class="text-base font-bold text-blue-700 dark:text-blue-400">{{ $presence->shift_name ?? 'Regular' }}</span>
                        </div>

                        <div class="px-5 py-2.5 rounded-xl font-black text-sm uppercase tracking-wider flex items-center gap-2.5 shadow-sm
                            {{ $presence->status == 'Hadir' ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400 border border-green-200 dark:border-green-800' : 
                               ($presence->status == 'Terlambat' ? 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400 border border-yellow-200 dark:border-yellow-800' : 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400 border border-red-200 dark:border-red-800') }}">
                            <div class="w-2.5 h-2.5 rounded-full {{ $presence->status == 'Hadir' ? 'bg-green-500' : ($presence->status == 'Terlambat' ? 'bg-yellow-500' : 'bg-red-500') }}"></div>
                            {{ $presence->status }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bottom Bar: Date & Nav -->
            <div class="px-8 sm:px-10 py-6 border-t border-gray-100 dark:border-gray-700/50 bg-gray-50/50 dark:bg-gray-800/30 relative z-10 mt-auto">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="flex items-center gap-2 mb-1.5 opacity-60 text-gray-500 dark:text-gray-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            <span class="text-xs font-bold uppercase tracking-widest">Tanggal Laporan</span>
                        </div>
                        <div class="text-xl sm:text-2xl font-black text-gray-900 dark:text-white tracking-tight">
                            {{ \Carbon\Carbon::parse($presence->date)->isoFormat('dddd, D MMMM Y') }}
                        </div>
                    </div>
                    
                    <div class="flex items-center gap-3">
                        <a href="{{ isset($prevPresence) ? route('hrd.detail', $prevPresence->id) : '#' }}" 
                           class="group p-3.5 rounded-xl transition-all border border-transparent {{ !isset($prevPresence) ? 'opacity-30 cursor-not-allowed bg-gray-100 dark:bg-gray-800' : 'bg-white dark:bg-gray-700 text-gray-600 dark:text-gray-200 shadow-sm hover:border-blue-300 hover:text-blue-600 hover:shadow-md dark:hover:shadow-blue-900/20' }}">
                            <svg class="w-5 h-5 transform group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                        </a>
                        <a href="{{ isset($nextPresence) ? route('hrd.detail', $nextPresence->id) : '#' }}" 
                           class="group p-3.5 rounded-xl transition-all border border-transparent {{ !isset($nextPresence) ? 'opacity-30 cursor-not-allowed bg-gray-100 dark:bg-gray-800' : 'bg-white dark:bg-gray-700 text-gray-600 dark:text-gray-200 shadow-sm hover:border-blue-300 hover:text-blue-600 hover:shadow-md dark:hover:shadow-blue-900/20' }}">
                            <svg class="w-5 h-5 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right: Mini Calendar (Compact width) -->
        <div class="settings-card glass p-6 flex flex-col items-center justify-center shrink-0 w-auto h-fit self-start">
            <h3 class="font-bold text-gray-900 dark:text-white text-lg mb-6 text-center capitalize w-full border-b border-gray-100 dark:border-gray-700 pb-4">
                {{ \Carbon\Carbon::parse($presence->date)->isoFormat('MMMM Y') }}
            </h3>
            
            <div style="display: grid; grid-template-columns: repeat(7, 32px); gap: 4px; margin-bottom: 8px;">
                @foreach(['M','S','S','R','K','J','S'] as $d)
                <div style="width: 32px; height: 24px; display: flex; align-items: center; justify-content: center;">
                    <span class="text-[10px] font-bold text-gray-400">{{ $d }}</span>
                </div>
                @endforeach
            </div>

            <div style="display: grid; grid-template-columns: repeat(7, 32px); gap: 4px;">
                @php
                    $date = \Carbon\Carbon::parse($presence->date);
                    $daysInMonth = $date->daysInMonth;
                    $firstDayOfWeek = $date->copy()->startOfMonth()->dayOfWeek;
                @endphp

                @for($i = 0; $i < $firstDayOfWeek; $i++)
                    <div style="width: 32px; height: 32px;"></div>
                @endfor

                @for($day = 1; $day <= $daysInMonth; $day++)
                    @php
                        $currentDayDate = $date->copy()->day($day)->format('Y-m-d');
                        $hasPresence = isset($monthlyPresences[$currentDayDate]);
                        $isToday = $date->day == $day;
                        $p = $hasPresence ? $monthlyPresences[$currentDayDate] : null;
                    @endphp

                    @if($hasPresence)
                        <a href="{{ route('hrd.detail', $p->id) }}" 
                           style="width: 32px; height: 32px; display: flex; flex-direction: column; align-items: center; justify-content: center; border-radius: 8px; padding: 4px; font-size: 10px; font-weight: bold; transition: all 0.2s;"
                           class="{{ $isToday ? 'bg-blue-500 text-white ring-2 ring-blue-300' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-blue-100 dark:hover:bg-gray-600' }}">
                            <span style="line-height: 1;">{{ $day }}</span>
                            <div style="display: flex; gap: 2px; margin-top: 2px;">
                                @if($p->time_in)<div style="width: 4px; height: 4px; border-radius: 50%;" class="{{ $isToday ? 'bg-white' : 'bg-blue-500' }}"></div>@endif
                                @if($p->time_out)<div style="width: 4px; height: 4px; border-radius: 50%;" class="{{ $isToday ? 'bg-white' : 'bg-orange-500' }}"></div>@endif
                            </div>
                        </a>
                    @else
                        <div style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; border-radius: 8px; font-size: 10px;" 
                             class="text-gray-300 dark:text-gray-600">
                            {{ $day }}
                        </div>
                    @endif
                @endfor
            </div>
        </div>
    </div>

    <!-- Timeline & Photos -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        <!-- Timeline Section with Maps -->
        <div>
            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Timeline Kehadiran
            </h3>
            
            <div class="space-y-4">
                <!-- Clock In -->
                <div class="settings-card glass p-6 hover:shadow-lg transition-shadow">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="p-2.5 bg-green-100 dark:bg-green-900/30 rounded-xl text-green-600 dark:text-green-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                        </div>
                        <span class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Jam Masuk</span>
                    </div>
                    
                    <div class="text-4xl font-black text-gray-900 dark:text-white mb-4">
                        {{ $presence->time_in ? \Carbon\Carbon::parse($presence->time_in)->format('H:i') : '--:--' }}
                    </div>

                    @if($presence->location_in)
                    <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-700/50">
                        <div class="flex items-center gap-2 mb-2">
                            <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            <span class="text-xs font-bold text-gray-500">Lokasi Masuk</span>
                        </div>
                        <div class="aspect-video w-full rounded-xl overflow-hidden shadow-sm border border-gray-200 dark:border-gray-700">
                            <iframe 
                                width="100%" 
                                height="100%" 
                                src="https://maps.google.com/maps?q={{ $presence->location_in }}&z=15&output=embed"
                                frameborder="0" 
                                scrolling="no" 
                                marginheight="0" 
                                marginwidth="0">
                            </iframe>
                        </div>
                    </div>
                    @endif
                </div>

                <!-- Clock Out -->
                <div class="settings-card glass p-6 hover:shadow-lg transition-shadow">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="p-2.5 bg-red-100 dark:bg-red-900/30 rounded-xl text-red-600 dark:text-red-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                        </div>
                        <span class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Jam Keluar</span>
                    </div>
                    
                    <div class="text-4xl font-black text-gray-900 dark:text-white mb-4">
                        {{ $presence->time_out ? \Carbon\Carbon::parse($presence->time_out)->format('H:i') : '--:--' }}
                    </div>

                    @if($presence->location_out)
                    <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-700/50">
                        <div class="flex items-center gap-2 mb-2">
                            <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            <span class="text-xs font-bold text-gray-500">Lokasi Keluar</span>
                        </div>
                        <div class="aspect-video w-full rounded-xl overflow-hidden shadow-sm border border-gray-200 dark:border-gray-700">
                            <iframe 
                                width="100%" 
                                height="100%" 
                                src="https://maps.google.com/maps?q={{ $presence->location_out }}&z=15&output=embed"
                                frameborder="0" 
                                scrolling="no" 
                                marginheight="0" 
                                marginwidth="0">
                            </iframe>
                        </div>
                    </div>
                    @endif
                </div>

                <!-- Notes -->
                @if($presence->note)
                <div class="settings-card glass p-6 bg-amber-50 dark:bg-amber-900/20 border-amber-200 dark:border-amber-800">
                    <div class="flex items-center gap-2 mb-3">
                        <svg class="w-4 h-4 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/></svg>
                        <span class="text-xs font-bold text-amber-600 dark:text-amber-400 uppercase">Catatan</span>
                    </div>
                    <p class="text-sm text-amber-900 dark:text-amber-100 italic leading-relaxed">"{{ $presence->note }}"</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Photos & Actions -->
        <div>
            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                Bukti Foto & Aksi
            </h3>
            
            <div class="space-y-4">
                <!-- Photo In -->
                <div>
                    <span class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase block mb-2 flex items-center gap-1.5">
                        <div class="w-2 h-2 rounded-full bg-green-500"></div>
                        Saat Masuk
                    </span>
                    <div class="settings-card glass overflow-hidden aspect-video group cursor-pointer">
                        @if($presence->image_in)
                            <img src="{{ route('presence.image', basename($presence->image_in)) }}" 
                                 class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300"
                                 onclick="window.open('{{ route('presence.image', basename($presence->image_in)) }}', '_blank')">
                        @else
                            <div class="flex flex-col items-center justify-center h-full text-gray-400">
                                <svg class="w-12 h-12 mb-2 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                <span class="text-xs font-bold">Tidak Ada Foto</span>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Photo Out -->
                <div>
                    <span class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase block mb-2 flex items-center gap-1.5">
                        <div class="w-2 h-2 rounded-full bg-red-500"></div>
                        Saat Keluar
                    </span>
                    <div class="settings-card glass overflow-hidden aspect-video group cursor-pointer">
                        @if($presence->image_out)
                            <img src="{{ route('presence.image', basename($presence->image_out)) }}" 
                                 class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300"
                                 onclick="window.open('{{ route('presence.image', basename($presence->image_out)) }}', '_blank')">
                        @else
                            <div class="flex flex-col items-center justify-center h-full text-gray-400">
                                <svg class="w-12 h-12 mb-2 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                <span class="text-xs font-bold">Tidak Ada Foto</span>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Actions -->
                <div class="space-y-2 pt-2">
                    <a href="{{ route('hrd.report', ['user_id' => $presence->user_id]) }}" 
                       class="btn-secondary w-full justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        Riwayat Pegawai Ini
                    </a>

                    @if($presence->is_pending)
                    <form action="{{ route('hrd.approve', $presence->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn-primary w-full justify-center" 
                                onclick="return confirm('Apakah Anda yakin ingin menyetujui presensi ini?')">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Setujui Presensi
                        </button>
                    </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
