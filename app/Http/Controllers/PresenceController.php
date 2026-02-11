<?php

namespace App\Http\Controllers;

use App\Models\Presence;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PresenceController extends Controller
{
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

        $unit = \App\Models\Unit::where('name', $user->unit)->first();
        $isWorkingDay = true;
        $dayName = $today->isoFormat('dddd');

        if ($unit && $unit->working_days && count($unit->working_days) > 0) {
            $isWorkingDay = in_array($dayName, $unit->working_days);
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

        if ($unit && $unit->available_shifts && count($unit->available_shifts) > 0) {
            $minDiff = null;
            foreach ($unit->available_shifts as $shift) {
                if (!is_array($shift) || !isset($shift['start_time'])) continue;
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
        return view('pages.presensi', compact('presence', 'selectedType', 'isWorkingDay', 'dayName', 'activeShiftInfo', 'settings'));
    }

    public function store(Request $request)
    {
        $userId = Auth::id();
        $user = Auth::user();
        $now = Carbon::now();
        $type = $request->input('type', 'in');
        $unit = \App\Models\Unit::where('name', $user->unit)->first();

        // 1. Identify Closest Shift and Logical Work Date
        $shiftName = null;
        $status = 'Hadir';
        $logicalDate = Carbon::today();

        if ($unit && $unit->available_shifts && count($unit->available_shifts) > 0) {
            $closestShift = null;
            $closestShiftStartTime = null;
            $minDiff = null;

            foreach ($unit->available_shifts as $shift) {
                if (!is_array($shift) || !isset($shift['start_time'])) continue;

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
            if ($unit && $unit->working_days && count($unit->working_days) > 0) {
                $dayName = $logicalDate->isoFormat('dddd');
                if (!in_array($dayName, $unit->working_days)) {
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
            ->paginate(10);

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
        $query = Presence::with('user');
        
        if ($request->filled('start_date')) {
            $query->where('date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->where('date', '<=', $request->end_date);
        }
        
        if ($request->filled('unit')) {
            $query->whereHas('user', function($q) use ($request) {
                $q->where('unit', $request->unit);
            });
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

        // Filter by user selection (Dropdown)
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter by user search (Text input)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('nip', 'like', "%{$search}%");
            });
        }
        
        $totalAttendance = (clone $query)->count();

        $userAttendanceCounts = (clone $query)
            ->select('user_id', \Illuminate\Support\Facades\DB::raw('count(*) as total'))
            ->groupBy('user_id')
            ->pluck('total', 'user_id');

        $perPage = $request->input('per_page', 10);

        $presences = $query->orderBy('date', 'desc')
                          ->orderBy('time_in', 'desc')
                          ->paginate($perPage)
                          ->withQueryString();
        
        $units = \App\Models\Unit::orderBy('name')->pluck('name');
        $users = \App\Models\User::orderBy('name')->get(['id', 'name', 'nip']);
        
        $globalPendingCount = Presence::where('is_pending', true)->count();
        
        return view('pages.laporan-hrd', compact('presences', 'units', 'users', 'totalAttendance', 'userAttendanceCounts', 'globalPendingCount'));
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
}
