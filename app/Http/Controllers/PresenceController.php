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
        $today = Carbon::today()->toDateString();
        $userId = Auth::id(); 
        
        $presence = Presence::where('user_id', $userId)
            ->where('date', $today)
            ->first();

        $selectedType = $request->query('type');

        return view('pages.presensi', compact('presence', 'selectedType'));
    }

    public function store(Request $request)
    {
        $userId = Auth::id();
        $today = Carbon::today()->toDateString();
        $now = Carbon::now()->toTimeString();
        $type = $request->input('type', 'in');

        $presence = Presence::where('user_id', $userId)
            ->where('date', $today)
            ->first();

        if ($type === 'in' && $presence && $presence->time_in) {
            return back()->with('error', 'Anda sudah absen masuk hari ini.');
        }

        if ($type === 'out' && (!$presence || !$presence->time_in)) {
            return back()->with('error', 'Anda harus absen masuk terlebih dahulu.');
        }

        if ($type === 'out' && $presence && $presence->time_out) {
            return back()->with('error', 'Anda sudah absen keluar hari ini.');
        }

        $imagePath = null;
        if ($request->image) {
            $image = $request->image;
            $image = str_replace('data:image/jpeg;base64,', '', $image);
            $image = str_replace(' ', '+', $image);
            $imageName = $type . '_' . $userId . '_' . time() . '.jpg';
            $imagePath = 'presences/' . $imageName;
            Storage::disk('public')->put($imagePath, base64_decode($image));
        }

        if ($type === 'in') {
            $user = Auth::user();
            $unit = \App\Models\Unit::where('name', $user->unit)->first();
            $status = 'Hadir';
            $shiftName = null;

            if ($unit && $unit->available_shifts && count($unit->available_shifts) > 0) {
                $currentTime = Carbon::now();
                $closestShift = null;
                $minDiff = null;

                foreach ($unit->available_shifts as $shift) {
                    if (!is_array($shift) || !isset($shift['start_time'])) {
                        continue;
                    }

                    try {
                        $startTime = Carbon::createFromFormat('H:i', $shift['start_time']);
                        
                        $absDiff = abs($currentTime->diffInMinutes($startTime));
                        if ($minDiff === null || $absDiff < $minDiff) {
                            $minDiff = $absDiff;
                            $closestShift = $shift;
                        }
                    } catch (\Exception $e) {
                        continue;
                    }
                }

                if ($closestShift) {
                    $shiftName = $closestShift['name'];
                    $startTime = Carbon::createFromFormat('H:i', $closestShift['start_time']);
                    
                    // Terlambat if check-in is after start_time
                    if ($currentTime->format('H:i:s') > $startTime->format('H:i:s')) {
                        $status = 'Terlambat';
                    }
                }
            }

            Presence::create([
                'user_id' => $userId,
                'date' => $today,
                'shift_name' => $shiftName,
                'status' => $status,
                'time_in' => $now,
                'location_in' => $request->location,
                'image_in' => $imagePath,
                'note' => $request->note,
            ]);
            $msg = 'Berhasil Absen Masuk';
        } else {
            $presence->update([
                'time_out' => $now,
                'location_out' => $request->location,
                'image_out' => $imagePath,
                'note' => $request->note,
            ]);
            $msg = 'Berhasil Absen Keluar';
        }

        return redirect()->route('presence.index')->with('success', $msg);
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
                    $query->whereNotNull('time_in')->where('status', 'Hadir');
                    break;
                case 'terlambat':
                    $query->where('status', 'Terlambat');
                    break;
                case 'tidak_hadir':
                    break;
            }
        }

        // Filter by user
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        
        $totalAttendance = (clone $query)->count();

        $userAttendanceCounts = (clone $query)
            ->select('user_id', \Illuminate\Support\Facades\DB::raw('count(*) as total'))
            ->groupBy('user_id')
            ->pluck('total', 'user_id');

        $presences = $query->orderBy('date', 'desc')
                          ->orderBy('time_in', 'desc')
                          ->paginate(10);
        
        $units = \App\Models\Unit::orderBy('name')->pluck('name');
        $users = \App\Models\User::orderBy('name')->get(['id', 'name', 'nip']);
        
        return view('pages.laporan-hrd', compact('presences', 'units', 'users', 'totalAttendance', 'userAttendanceCounts'));
    }

    public function exportExcel(Request $request)
    {
        $filters = $request->only(['start_date', 'end_date', 'unit', 'status']);
        
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
}
