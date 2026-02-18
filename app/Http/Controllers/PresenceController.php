<?php

namespace App\Http\Controllers;

use App\Models\Presence;
use App\Models\Unit as LocalUnit;
use App\Services\SsoApiService;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class PresenceController extends Controller
{
    protected $ssoService;

    public function __construct(SsoApiService $ssoService)
    {
        $this->ssoService = $ssoService;
    }
    public function index(Request $request)
    {
        $userId = Auth::id(); 
        $user = Auth::user();
        $now = Carbon::now();
        $today = Carbon::today();
        
        // Search for the most relevant presence:
        // 1. First, an unfinished session (time_out is null)
        // 2. Otherwise, the session for the calendar today
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

        $unit = $this->resolveUserUnit($user);
        
        $isWorkingDay = false;
        $dayName = $today->isoFormat('dddd');
        $isScheduleConfigured = $unit && count($unit->working_days) > 0 && count($unit->available_shifts) > 0;

        if ($isScheduleConfigured) {
            $isWorkingDay = $this->isWorkingDay($today, $unit->working_days);
        }

        // Shift proximity logic for UI
        $closestShift = null;
        $activeShiftInfo = [
            'is_expired' => false,
            'is_too_early' => false,
            'logical_date' => $today,
            'shift_name' => null,
            'start_time' => null,
            'end_time' => null
        ];

        if ($isScheduleConfigured) {
            $minDiff = null;
            foreach ($unit->available_shifts as $shift) {
                if (!isset($shift['start_time'])) continue;
                $tS = Carbon::parse($today->toDateString() . ' ' . $shift['start_time']);
                $diffs = [
                    ['time' => $tS, 'logical' => $today],
                    ['time' => (clone $tS)->subDay(), 'logical' => (clone $today)->subDay()],
                    ['time' => (clone $tS)->addDay(), 'logical' => (clone $today)->addDay()],
                ];
                foreach ($diffs as $d) {
                    $diff = abs($now->diffInMinutes($d['time']));
                    if ($minDiff === null || $diff < $minDiff) {
                        $minDiff = $diff;
                        $closestShift = $shift;
                        $activeShiftInfo['start_time'] = $d['time'];
                        $activeShiftInfo['logical_date'] = $d['logical'];
                    }
                }
            }
            if ($closestShift) {
                $activeShiftInfo['shift_name'] = $closestShift['name'];
                $activeShiftInfo['end_time'] = Carbon::parse($activeShiftInfo['logical_date']->toDateString() . ' ' . $closestShift['end_time']);
                if ($activeShiftInfo['end_time']->lt($activeShiftInfo['start_time'])) {
                    $activeShiftInfo['end_time']->addDay();
                }

                $activeShiftInfo['is_expired'] = $now->gt($activeShiftInfo['end_time']);
                $activeShiftInfo['is_too_early'] = $now->lt((clone $activeShiftInfo['start_time'])->subMinutes(60));
            }
        }
        
        $settings = \App\Models\GlobalSetting::all()->pluck('value', 'key');

        $selectedType = $request->query('type');
        return view('pages.presensi', compact('presence', 'selectedType', 'isWorkingDay', 'dayName', 'activeShiftInfo', 'settings', 'isScheduleConfigured'));
    }

    public function store(Request $request)
    {
        $userId = Auth::id();
        $user = Auth::user();
        $now = Carbon::now();
        $type = $request->input('type', 'in');
        $unit = $this->resolveUserUnit($user);

        // Untuk absen masuk, jadwal unit wajib lengkap.
        // Untuk absen pulang, tetap izinkan selama ada sesi masuk terbuka.
        if ($type === 'in') {
            if (
                !$unit ||
                count($unit->available_shifts) === 0 ||
                count($unit->working_days) === 0
            ) {
                return back()->with('error', 'Jadwal unit Anda belum diatur lengkap (hari kerja/shift). Hubungi admin.');
            }
        }

        // 1. Identify Closest Shift and Logical Work Date
        $shiftName = null;
        $status = 'Hadir';
        $logicalDate = Carbon::today();

        if ($unit && count($unit->available_shifts) > 0) {
            $closestShift = null;
            $closestShiftStartTime = null;
            $minDiff = null;

            foreach ($unit->available_shifts as $shift) {
                if (!isset($shift['start_time'])) continue;

                try {
                    $todayStart = Carbon::parse(Carbon::today()->toDateString() . ' ' . $shift['start_time']);
                    $yesterdayStart = (clone $todayStart)->subDay();
                    $tomorrowStart = (clone $todayStart)->addDay();

                    $diffs = [
                        ['diff' => abs($now->diffInMinutes($todayStart)), 'time' => $todayStart, 'logical' => Carbon::today()],
                        ['diff' => abs($now->diffInMinutes($yesterdayStart)), 'time' => $yesterdayStart, 'logical' => Carbon::yesterday()],
                        ['diff' => abs($now->diffInMinutes($tomorrowStart)), 'time' => $tomorrowStart, 'logical' => Carbon::tomorrow()],
                    ];

                    usort($diffs, fn($a, $b) => $a['diff'] <=> $b['diff']);
                    $best = $diffs[0];

                    if ($minDiff === null || $best['diff'] < $minDiff) {
                        $minDiff = $best['diff'];
                        $closestShift = $shift;
                        $closestShiftStartTime = $best['time'];
                        $logicalDate = $best['logical'];
                    }
                } catch (\Exception $e) { continue; }
            }

            if ($closestShift && $closestShiftStartTime) {
                $shiftName = $closestShift['name'];
                
                // Calculate Expected End Time
                $closestShiftEndTime = Carbon::parse($logicalDate->toDateString() . ' ' . $closestShift['end_time']);
                if ($closestShiftEndTime->lt($closestShiftStartTime)) {
                    $closestShiftEndTime->addDay();
                }

                // Block if shift has already ended
                if ($type === 'in' && $now->gt($closestShiftEndTime)) {
                    return back()->with('error', "Maaf, shift $shiftName untuk jadwal ini sudah berakhir pada pukul " . $closestShiftEndTime->format('H:i') . " (" . $closestShiftEndTime->isoFormat('D MMMM') . ").");
                }

                // Block if too early (more than 1 hour before)
                if ($type === 'in' && $now->lt((clone $closestShiftStartTime)->subMinutes(60))) {
                    return back()->with('error', "Maaf, absen untuk shift $shiftName belum dibuka. Silakan absen mulai pukul " . (clone $closestShiftStartTime)->subMinutes(60)->format('H:i') . ".");
                }

                if ($now->gt($closestShiftStartTime)) {
                    $status = 'Terlambat';
                }
            }
        }

        // 2. Handle Clock In
        if ($type === 'in') {
            // Check Working Day Restriction based on Logical Date
            if ($unit && count($unit->working_days) > 0) {
                $dayName = $logicalDate->isoFormat('dddd');
                if (!$this->isWorkingDay($logicalDate, $unit->working_days)) {
                    return back()->with('error', "Maaf, hari $dayName bukan hari kerja untuk unit Anda.");
                }
            }

            // Check for collision using Logical Date
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
        // 3. Handle Clock Out
        else {
            $presence = Presence::where('user_id', $userId)
                ->whereNull('time_out')
                ->orderBy('date', 'desc')
                ->orderBy('time_in', 'desc')
                ->first();

            if (!$presence) {
                return back()->with('error', 'Anda harus absen masuk terlebih dahulu.');
            }

            $imagePath = $this->handleImageUpload($request->image, $userId, 'out');
            
            $isFaceDetected = $request->input('is_face_detected') === 'true';

            $presence->update([
                'time_out' => $now->toTimeString(),
                'location_out' => $request->location,
                'image_out' => $imagePath,
                'note' => $request->note,
                // Only update pending status if it's clock out and face not detected. 
                // If it was already pending from IN, it stays pending? 
                // Let's assume ANY missing face makes it pending.
                'is_pending' => $presence->is_pending || !$isFaceDetected, // If already pending, stay pending. If new invalid face, become pending.
                'is_face_detected' => $presence->is_face_detected && $isFaceDetected, // Tracking overall valid session? Or strictly per action? 
                // Actually simplicity: Update 'is_pending' if current action fails face check.
            ]);
            
            // Logic: If check-in had face (valid) but check-out no face (invalid) -> becomes pending.
            // If check-in no face (pending) and check-out has face -> Should it clear pending?
            // "nanti hrd bisa accept" -> implies manual intervention. So automatic clearing might be risky.
            // Let's force Pending if THIS action fails face check.
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
        $userId = Auth::id();
        
        $presences = Presence::where('user_id', $userId)
            ->orderBy('date', 'desc')
            ->orderBy('time_in', 'desc')
            ->paginate(10)
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
        
        // Match view defaults: start of current month to today
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
        
        // Final safeguard against showing more than perPage
        $pagedItems = $filteredPresences->forPage($currentPage, $perPage)->values();
        
        $presences = new LengthAwarePaginator(
            $pagedItems,
            $filteredPresences->count(),
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

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

        // Get data for mini calendar (current month)
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

    private function resolveUserUnit($user): ?object
    {
        if (!$user) {
            return null;
        }

        $unitId = $user->unit_id ?? null;
        if (empty($unitId) || !Schema::hasColumn('units', 'sso_unit_id')) {
            Log::warning('User unit_id is missing or units.sso_unit_id column unavailable.', [
                'user_id' => $user->id ?? null,
                'user_unit_id' => $unitId,
            ]);
            return null;
        }

        $local = LocalUnit::where('sso_unit_id', (int) $unitId)->first();

        if (!$local) {
            Log::warning('Local unit schedule not found by sso_unit_id.', [
                'user_id' => $user->id ?? null,
                'user_unit_id' => $unitId,
            ]);
            return null;
        }

        $normalizedShifts = [];
        $sourceShifts = is_array($local?->available_shifts) ? $local->available_shifts : [];
        foreach ($sourceShifts as $shift) {
            if (is_array($shift)) {
                $normalizedShifts[] = $shift;
                continue;
            }
            if (is_object($shift)) {
                $normalizedShifts[] = (array) $shift;
            }
        }

        return (object) [
            'id' => (int) $unitId,
            'name' => $user->unit ?? null,
            'working_days' => is_array($local?->working_days) ? $local->working_days : [],
            'available_shifts' => $normalizedShifts,
        ];
    }

    private function isWorkingDay(Carbon $date, array $workingDays): bool
    {
        if (empty($workingDays)) {
            return true;
        }

        $workingDaysLc = collect($workingDays)
            ->filter(fn($v) => is_string($v))
            ->map(fn($v) => mb_strtolower(trim($v)))
            ->values()
            ->all();

        if (empty($workingDaysLc)) {
            return true;
        }

        $aliases = $this->getDayAliases($date);
        foreach ($aliases as $alias) {
            if (in_array($alias, $workingDaysLc, true)) {
                return true;
            }
        }

        Log::warning('Working day mismatch.', [
            'date' => $date->toDateString(),
            'aliases' => $aliases,
            'configured_days' => $workingDaysLc,
        ]);

        return false;
    }

    private function getDayAliases(Carbon $date): array
    {
        $dayMap = [
            1 => ['senin', 'monday', 'mon'],
            2 => ['selasa', 'tuesday', 'tue'],
            3 => ['rabu', 'wednesday', 'wed'],
            4 => ['kamis', 'thursday', 'thu'],
            5 => ['jumat', "jum'at", 'friday', 'fri'],
            6 => ['sabtu', 'saturday', 'sat'],
            7 => ['minggu', 'sunday', 'sun'],
        ];

        return $dayMap[$date->dayOfWeekIso] ?? [];
    }
}
