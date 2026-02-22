<?php

namespace App\Http\Controllers;

use App\Models\Presence;
use App\Models\Shift;
use App\Models\UserShift;
use App\Services\SsoApiService;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class PresenceController extends Controller
{
    protected $ssoService;

    public function __construct(SsoApiService $ssoService)
    {
        $this->ssoService = $ssoService;
        Carbon::setLocale('id');
    }
    public function index(Request $request)
    {
        Carbon::setLocale('id');
        $userId = Auth::id(); 
        $now = Carbon::now();
        $today = Carbon::today();
        
        // Cari presence yang masih open (belum absen pulang)
        $presence = Presence::where('user_id', $userId)
            ->whereNull('time_out')
            ->orderBy('date', 'desc')
            ->orderBy('time_in', 'desc')
            ->first();

        if (!$presence) {
            $presence = Presence::where('user_id', $userId)
                ->where('date', $today->toDateString())
                ->first();
        }

        $userShifts = UserShift::with('shift')
            ->where('user_id', $userId)
            ->orderByDesc('is_active')
            ->get();
        $activeUserShift = $userShifts->firstWhere('is_active', true) ?? $userShifts->first();
        $isShiftSelected = ($activeUserShift && $activeUserShift->shift);

        $isWorkingDay = true;
        $dayName = $today->isoFormat('dddd');
        if ($isShiftSelected) {
            $shiftWorkingDays = is_array($activeUserShift->shift->working_days ?? null) ? $activeUserShift->shift->working_days : [];
            $isWorkingDay = $this->isWorkingDay($today, $shiftWorkingDays);
        }

        $activeShiftInfo = [
            'is_expired' => false,
            'is_too_early' => false,
            'logical_date' => $today,
            'shift_name' => null,
            'start_time' => null,
            'end_time' => null
        ];

        if ($isShiftSelected) {
            $shift = $activeUserShift->shift;
            $possibleWindows = [
                ['logical' => (clone $today)->subDay()],
                ['logical' => (clone $today)],
                ['logical' => (clone $today)->addDay()],
            ];

            $bestMatch = null;
            $minDiff = null;

            foreach ($possibleWindows as $window) {
                $logicalDate = $window['logical'];
                $startTime = Carbon::parse($logicalDate->toDateString() . ' ' . $shift->start_time);
                $endTime = Carbon::parse($logicalDate->toDateString() . ' ' . $shift->end_time);
                
                if ($endTime->lt($startTime)) {
                    $endTime->addDay();
                }

                $bufferStart = (clone $startTime)->subMinutes(60);

                if ($now->between($bufferStart, $endTime)) {
                    $bestMatch = [
                        'time' => $startTime,
                        'logical' => $logicalDate,
                        'end' => $endTime
                    ];
                    break; 
                }

                $diff = abs($now->diffInMinutes($startTime));
                if ($minDiff === null || $diff < $minDiff) {
                    $minDiff = $diff;
                    $bestMatch = [
                        'time' => $startTime,
                        'logical' => $logicalDate,
                        'end' => $endTime
                    ];
                }
            }

            if ($bestMatch) {
                $activeShiftInfo['shift_name'] = $shift->name;
                $activeShiftInfo['start_time'] = $bestMatch['time'];
                $activeShiftInfo['logical_date'] = $bestMatch['logical'];
                $activeShiftInfo['end_time'] = $bestMatch['end'];
                
                $activeShiftInfo['is_expired'] = $now->gt($activeShiftInfo['end_time']);
                $activeShiftInfo['is_too_early'] = $now->lt((clone $activeShiftInfo['start_time'])->subMinutes(60));
            }
        }
        
        $settings = \App\Models\GlobalSetting::all()->pluck('value', 'key');

        $selectedType = $request->query('type');
        
        // Cek apakah presence yang open sudah stale (window absen pulang habis)
        $isStaleOut = false;
        if ($presence && !$presence->time_out) {
            $shift = Shift::where('name', $presence->shift_name)->first();
            if ($shift) {
                $shiftStartTime = Carbon::parse($presence->date . ' ' . $shift->start_time);
                $shiftEndTime = Carbon::parse($presence->date . ' ' . $shift->end_time);
                if ($shiftEndTime->lt($shiftStartTime)) {
                    $shiftEndTime->addDay();
                }
                $isStaleOut = $now->gt($shiftEndTime->addHours(8));
            }
        }

        // Cari presence HARI INI (logical date) untuk menentukan hasIn/hasOut
        // Jika presence lama sudah stale, jangan blokir absen masuk hari ini
        $logicalDateStr = $activeShiftInfo['logical_date']->toDateString();
        $shiftNameForToday = $activeShiftInfo['shift_name'];

        $todayPresence = null;
        if ($shiftNameForToday) {
            $todayPresence = Presence::where('user_id', $userId)
                ->where('date', $logicalDateStr)
                ->where('shift_name', $shiftNameForToday)
                ->first();
        }

        // Cek apakah today's presence window absen pulang sudah habis
        $isTodayStaleOut = false;
        if ($todayPresence && !$todayPresence->time_out) {
            $shiftForToday = Shift::where('name', $todayPresence->shift_name)->first();
            if ($shiftForToday) {
                $todayShiftEnd = Carbon::parse($todayPresence->date . ' ' . $shiftForToday->end_time);
                $todayShiftStart = Carbon::parse($todayPresence->date . ' ' . $shiftForToday->start_time);
                if ($todayShiftEnd->lt($todayShiftStart)) $todayShiftEnd->addDay();
                $isTodayStaleOut = $now->gt((clone $todayShiftEnd)->addHours(8));
            }
        }

        // presence untuk display summary (bisa dari hari ini atau kemarin jika stale)
        $presenceForDisplay = $todayPresence ?? $presence;

        return view('pages.presensi', compact(
            'presenceForDisplay', 'selectedType', 'isWorkingDay', 'dayName',
            'activeShiftInfo', 'settings', 'isShiftSelected',
            'todayPresence', 'isTodayStaleOut'
        ));

    }

    public function updateShift(Request $request)
    {
        return back()->with('error', 'Shift kerja ditetapkan oleh admin IT/HRD. Pengguna tidak dapat mengganti shift sendiri.');
    }

    public function store(Request $request)
    {
        Carbon::setLocale('id');
        $userId = Auth::id();
        $now = Carbon::now();
        $type = $request->input('type', 'in');

        // Fetch user shift
        $userShifts = UserShift::with('shift')
            ->where('user_id', $userId)
            ->orderByDesc('is_active')
            ->get();
        $userShift = $userShifts->firstWhere('is_active', true) ?? $userShifts->first();
        
        if ($type === 'in' && (!$userShift || !$userShift->shift)) {
            return back()->with('error', 'Anda belum memilih shift kerja. Silakan pilih shift terlebih dahulu.');
        }

        $shiftName = null;
        $status = 'Hadir';
        $logicalDate = Carbon::today();
        $shiftWorkingDays = [];
        
        if ($userShift && $userShift->shift) {
            $shift = $userShift->shift;
            $shiftName = $shift->name;
            $shiftWorkingDays = is_array($shift->working_days ?? null) ? $shift->working_days : [];
            
            // Check windows: Yesterday, Today, Tomorrow
            $possibleWindows = [
                ['logical' => Carbon::yesterday()],
                ['logical' => Carbon::today()],
                ['logical' => Carbon::tomorrow()],
            ];

            $best = null;
            $minDiff = null;

            foreach ($possibleWindows as $window) {
                $lDate = $window['logical'];
                $sTime = Carbon::parse($lDate->toDateString() . ' ' . $shift->start_time);
                $eTime = Carbon::parse($lDate->toDateString() . ' ' . $shift->end_time);
                
                if ($eTime->lt($sTime)) {
                    $eTime->addDay();
                }

                $bStart = (clone $sTime)->subMinutes(60);

                if ($now->between($bStart, $eTime)) {
                    $best = [
                        'time' => $sTime,
                        'logical' => $lDate,
                        'end' => $eTime
                    ];
                    break;
                }

                $diff = abs($now->diffInMinutes($sTime));
                if ($minDiff === null || $diff < $minDiff) {
                    $minDiff = $diff;
                    $best = [
                        'time' => $sTime,
                        'logical' => $lDate,
                        'end' => $eTime
                    ];
                }
            }
            
            $shiftStartTime = $best['time'];
            $logicalDate = $best['logical'];
            $shiftEndTime = $best['end'];

            if ($type === 'in' && $now->gt($shiftEndTime)) {
                return back()->with('error', "Maaf, shift $shiftName untuk jadwal ini sudah berakhir pada pukul " . $shiftEndTime->format('H:i') . " (" . $shiftEndTime->isoFormat('D MMMM') . ").");
            }

            if ($type === 'in' && $now->lt((clone $shiftStartTime)->subMinutes(60))) {
                return back()->with('error', "Maaf, absen untuk shift $shiftName belum dibuka. Silakan absen mulai pukul " . (clone $shiftStartTime)->subMinutes(60)->format('H:i') . ".");
            }

            if ($now->gt($shiftStartTime)) {
                $status = 'Terlambat';
            }
        }

        if ($type === 'in') {
            if (count($shiftWorkingDays) > 0) {
                $dayName = $logicalDate->isoFormat('dddd');
                if (!$this->isWorkingDay($logicalDate, $shiftWorkingDays)) {
                    return back()->with('error', "Maaf, hari $dayName tidak termasuk hari kerja untuk shift Anda.");
                }
            }

            $existing = Presence::where('user_id', $userId)
                ->where('date', $logicalDate->toDateString())
                ->where('shift_name', $shiftName)
                ->first();

            if ($existing && $existing->time_in) {
                return back()->with('error', "Anda sudah absen masuk untuk shift $shiftName di tanggal " . $logicalDate->isoFormat('D MMMM Y'));
            }

            $imagePath = $this->handleImageUpload($request->image, $userId, 'in');
            
            $isFaceDetected = $request->input('is_face_detected') === 'true';

            Presence::create([
                'user_id' => $userId,
                'date' => $logicalDate->toDateString(),
                'shift_name' => $shiftName,
                'status' => $status,
                'time_in' => $now->toTimeString(),
                'location_in' => $request->location,
                'image_in' => $imagePath,
                'note' => $request->note,
                'is_face_detected' => $isFaceDetected,
                'is_pending' => !$isFaceDetected,
            ]);
            
            $msg = $isFaceDetected ? 'Berhasil Absen Masuk' : 'Absen Masuk Berhasil (Menunggu Verifikasi Wajah)';
        }
        else {
            $presence = Presence::where('user_id', $userId)
                ->whereNull('time_out')
                ->orderBy('date', 'desc')
                ->orderBy('time_in', 'desc')
                ->first();

            if (!$presence) {
                return back()->with('error', 'Anda harus absen masuk terlebih dahulu.');
            }

            $shift = Shift::where('name', $presence->shift_name)->first();
            if ($shift) {
                $shiftStartTime = Carbon::parse($presence->date . ' ' . $shift->start_time);
                $shiftEndTime = Carbon::parse($presence->date . ' ' . $shift->end_time);
                if ($shiftEndTime->lt($shiftStartTime)) {
                    $shiftEndTime->addDay();
                }

                $maxOutTime = (clone $shiftEndTime)->addHours(8);
                if ($now->gt($maxOutTime)) {
                    return back()->with('error', "Batas waktu absen keluar untuk shift {$presence->shift_name} telah berakhir (Maksimal 8 jam setelah jadwal shift berakhir pada jam " . $shiftEndTime->format('H:i') . ").");
                }
            }

            $imagePath = $this->handleImageUpload($request->image, $userId, 'out');
            
            $isFaceDetected = $request->input('is_face_detected') === 'true';

            $presence->update([
                'time_out' => $now->toTimeString(),
                'location_out' => $request->location,
                'image_out' => $imagePath,
                'note' => $request->note,
                'is_pending' => $presence->is_pending || !$isFaceDetected,
                'is_face_detected' => $presence->is_face_detected && $isFaceDetected,
            ]);
            
            if (!$isFaceDetected) {
                $presence->update(['is_pending' => true]);
            }

            $msg = $isFaceDetected ? 'Berhasil Absen Keluar' : 'Absen Keluar Berhasil (Menunggu Verifikasi Wajah)';
            $msg = 'Berhasil Absen Keluar';
        }

        return redirect()->route('presence.index')->with('success', $msg);
    }

    private function handleImageUpload($imageData, $userId, $type)
    {
        if (!$imageData) return null;
        
        $image = str_replace(['data:image/jpeg;base64,', ' '], ['', '+'], $imageData);
        $imageName = $type . '_' . $userId . '_' . time() . '.jpg';
        $imagePath = 'presences/' . $imageName;
        Storage::disk('local')->put($imagePath, base64_decode($image));
        return $imagePath;
    }

    public function showImage($filename)
    {
        $path = 'presences/' . $filename;
        if (!Storage::disk('local')->exists($path)) {
            abort(404);
        }
        
        $file = Storage::disk('local')->get($path);
        $type = Storage::disk('local')->mimeType($path);
        
        return response($file, 200)->header("Content-Type", $type);
    }

    public function update(Request $request, Presence $presence)
    {
        return $this->store($request);
    }

    public function history()
    {
        Carbon::setLocale('id');
        $userId = Auth::id();
        
        $presences = Presence::where('user_id', $userId)
            ->orderBy('date', 'desc')
            ->orderBy('time_in', 'desc')
            ->paginate(10)
            ->onEachSide(1)
            ->withQueryString();

        $presenceStats = Presence::where('user_id', $userId)
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->date => [
                    'date' => $item->date,
                    'time_in' => $item->time_in,
                    'time_out' => $item->time_out,
                    'image_in' => $item->image_in,
                    'image_out' => $item->image_out,
                    'note' => $item->note,
                    'shift_name' => $item->shift_name,
                    'status' => $item->status,
                    'dayName' => \Carbon\Carbon::parse($item->date)->isoFormat('dddd'),
                    'dateFormatted' => \Carbon\Carbon::parse($item->date)->isoFormat('D MMMM YYYY')
                ]];
            });

        return view('pages.riwayat', compact('presences', 'presenceStats'));
    }

    public function hrdReport(Request $request)
    {
        $query = Presence::query();
        
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', Carbon::now()->toDateString());

        if ($request->filled('start_date')) {
            $query->where('date', '>=', $request->start_date);
        } else {
            $query->where('date', '>=', $startDate);
        }

        if ($request->filled('end_date')) {
            $query->where('date', '<=', $request->end_date);
        } else {
            $query->where('date', '<=', $endDate);
        }
        
        if ($request->filled('status')) {
            switch ($request->status) {
                case 'hadir':
                    $query->whereNotNull('time_in')->where('status', 'Hadir')->where('is_pending', false);
                    break;
                case 'terlambat':
                    $query->where('status', 'Terlambat')->where('is_pending', false);
                    break;
                case 'pending':
                    $query->where('is_pending', true);
                    break;
                case 'tidak_hadir':
                    break;
            }
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        $allPresences = $query->orderBy('date', 'desc')
            ->orderBy('time_in', 'desc')
            ->get();

        $usersMap = $this->ssoService->getUsersMap();
        $filteredPresences = $this->filterPresencesBySsoUser($allPresences, $usersMap, $request);
        $this->attachSsoUserRelation($filteredPresences, $usersMap);

        $totalAttendance = $filteredPresences->count();

        $userAttendanceCounts = $filteredPresences
            ->groupBy('user_id')
            ->map(fn(Collection $items) => $items->count());

        $userLatenessCounts = $filteredPresences
            ->where('status', 'Terlambat')
            ->groupBy('user_id')
            ->map(fn(Collection $items) => $items->count());

        $perPage = (int) $request->input('per_page', 10);
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        
        $pagedItems = $filteredPresences->forPage($currentPage, $perPage)->values();
        
        $presences = (new LengthAwarePaginator(
            $pagedItems,
            $filteredPresences->count(),
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        ))->onEachSide(1);

        $unitsResponse = $this->ssoService->getUnits(['all' => true]);
        $unitsRaw = $unitsResponse['data'] ?? (isset($unitsResponse[0]) ? $unitsResponse : []);
        $units = collect($unitsRaw)
            ->filter(fn($u) => is_array($u))
            ->map(fn($u) => $this->ssoService->normalizeUnit($u))
            ->pluck('name')
            ->filter()
            ->unique()
            ->values();

        $users = collect($usersMap)
            ->map(fn($user) => (object) $user)
            ->values();
        
        $globalPendingCount = Presence::where('is_pending', true)->count();
        
        return view('pages.laporan-hrd', compact('presences', 'units', 'users', 'totalAttendance', 'userAttendanceCounts', 'userLatenessCounts', 'globalPendingCount'));
    }

    public function exportExcel(Request $request)
    {
        $filters = $request->only(['start_date', 'end_date', 'unit', 'status', 'user_id', 'search']);
        
        $filename = 'Laporan_Kehadiran_' . now()->format('Y-m-d_His') . '.xlsx';
        
        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\PresencesExport($filters),
            $filename
        );
    }

    public function exportPdf(Request $request)
    {
        return back()->with('error', 'Export PDF belum diimplementasikan');
    }

    public function approve($id)
    {
        $presence = Presence::findOrFail($id);
        $presence->update(['is_pending' => false]);
        return back()->with('success', 'Presensi berhasil disetujui.');
    }

    public function showDetail(Presence $presence)
    {
        $usersMap = $this->ssoService->getUsersMap();
        $this->attachSsoUserRelation(collect([$presence]), $usersMap);

        $prevPresence = Presence::where('user_id', $presence->user_id)
            ->where('date', '<', $presence->date)
            ->orderBy('date', 'desc')
            ->first();

        $nextPresence = Presence::where('user_id', $presence->user_id)
            ->where('date', '>', $presence->date)
            ->orderBy('date', 'asc')
            ->first();

        $currentDate = Carbon::parse($presence->date);
        $monthlyPresences = Presence::where('user_id', $presence->user_id)
            ->whereYear('date', $currentDate->year)
            ->whereMonth('date', $currentDate->month)
            ->get(['id', 'date', 'status', 'time_in', 'time_out'])
            ->keyBy('date');

        return view('pages.laporan-detail', compact('presence', 'prevPresence', 'nextPresence', 'monthlyPresences'));
    }

    private function filterPresencesBySsoUser(Collection $presences, array $usersMap, Request $request): Collection
    {
        return $presences->filter(function (Presence $presence) use ($usersMap, $request) {
            $user = $usersMap[(string) $presence->user_id] ?? null;

            if ($request->filled('unit')) {
                $unit = $user['unit'] ?? null;
                if ($unit !== $request->unit) {
                    return false;
                }
            }

            if ($request->filled('search')) {
                $needle = strtolower((string) $request->search);
                $name = strtolower((string) ($user['name'] ?? ''));
                $nip = strtolower((string) ($user['nip'] ?? ''));
                if (!str_contains($name, $needle) && !str_contains($nip, $needle)) {
                    return false;
                }
            }

            return true;
        })->values();
    }

    private function attachSsoUserRelation(Collection $presences, array $usersMap): void
    {
        foreach ($presences as $presence) {
            $user = $usersMap[(string) $presence->user_id] ?? null;
            $presence->setRelation('user', (object) [
                'id' => $presence->user_id,
                'name' => $user['name'] ?? 'N/A',
                'nip' => $user['nip'] ?? '-',
                'unit' => $user['unit'] ?? '-',
            ]);
        }
    }

    private function isWorkingDay(Carbon $date, array $workingDays): bool
    {
        if (empty($workingDays)) {
            return true;
        }

        $workingDaysLc = collect($workingDays)
            ->map(fn($v) => mb_strtolower(trim((string)$v)))
            ->filter()
            ->values()
            ->all();

        if (empty($workingDaysLc)) {
            return true;
        }

        $dayMap = [
            0 => ['minggu', 'sunday', 'sun'],
            1 => ['senin', 'monday', 'mon'],
            2 => ['selasa', 'tuesday', 'tue'],
            3 => ['rabu', 'wednesday', 'wed'],
            4 => ['kamis', 'thursday', 'thu'],
            5 => ['jumat', "jum'at", 'friday', 'fri'],
            6 => ['sabtu', 'saturday', 'sat'],
            7 => ['minggu', 'sunday', 'sun'],
        ];

        // Combine aliases from dayOfWeek (0-6) and dayOfWeekIso (1-7)
        $dayIndex = $date->dayOfWeek;
        $dayIndexIso = $date->dayOfWeekIso;
        
        $aliases = array_merge(
            $dayMap[$dayIndex] ?? [],
            $dayMap[$dayIndexIso] ?? []
        );

        // Add localized day names from Carbon to be extra safe
        $aliases[] = mb_strtolower($date->isoFormat('dddd'));
        $aliases[] = mb_strtolower($date->format('l'));
        $aliases[] = mb_strtolower($date->format('D'));
        
        // Remove duplicates and trim aliases
        $aliases = array_unique(array_map('trim', $aliases));

        foreach ($aliases as $alias) {
            if (in_array($alias, $workingDaysLc, true)) {
                return true;
            }
        }

        Log::warning('Working day mismatch.', [
            'date' => $date->toDateString(),
            'logical_day_name' => $date->isoFormat('dddd'),
            'day_index' => $dayIndex,
            'day_index_iso' => $dayIndexIso,
            'matched_aliases' => $aliases,
            'configured_working_days' => $workingDaysLc,
        ]);

        return false;
    }
}
