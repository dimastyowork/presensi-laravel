<?php

namespace App\Http\Controllers;

use App\Models\Overtime;
use App\Services\SsoApiService;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class OvertimeController extends Controller
{
    protected $ssoService;

    public function __construct(SsoApiService $ssoService)
    {
        $this->ssoService = $ssoService;
    }

    /**
     * User: List own overtime requests
     */
    public function index(Request $request)
    {
        if (config('maintenance.overtime')) abort(503);

        $userId = Auth::id();
        $baseQuery = Overtime::where('user_id', $userId);

        $query = (clone $baseQuery);
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('start_date')) {
            $query->whereDate('date', '>=', $request->start_date);
        }
        
        if ($request->filled('end_date')) {
            $query->whereDate('date', '<=', $request->end_date);
        }
        
        $perPage = $request->get('per_page', 10);
        $overtimes = $query
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage)
            ->onEachSide(1)
            ->withQueryString();

        $stats = [
            'total' => (clone $baseQuery)->count(),
            'approved' => (clone $baseQuery)->where('status', 'approved')->count(),
            'pending' => (clone $baseQuery)->where('status', 'pending')->count(),
        ];

        return view('pages.overtime.index', compact('overtimes', 'stats'));
    }

    /**
     * User: Show request form
     */
    public function create()
    {
        if (config('maintenance.overtime')) abort(503);

        return view('pages.overtime.create');
    }

    /**
     * User: Store request
     */
    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required',
            'reason' => 'required|string|max:500',
        ]);

        Overtime::create([
            'user_id' => Auth::id(),
            'date' => $request->date,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'reason' => $request->reason,
            'status' => 'pending',
        ]);

        return redirect()->route('overtime.index')->with('success', 'Pengajuan lembur berhasil dikirim.');
    }

    /**
     * Admin: List all requests
     */
    public function adminIndex(Request $request)
    {
        $query = Overtime::query();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('start_date')) {
            $query->where('date', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->where('date', '<=', $request->end_date);
        }

        $allOvertimes = $query->orderBy('status', 'asc') // Show pending first
            ->orderBy('date', 'desc')
            ->get();

        $usersMap = $this->ssoService->getUsersMap();
        
        $items = $allOvertimes->map(function($ot) use ($usersMap) {
            $user = $usersMap[(string) $ot->user_id] ?? null;
            $ot->user_name = $user['name'] ?? 'N/A';
            $ot->user_nip = $user['nip'] ?? '-';
            $ot->user_unit = $user['unit'] ?? '-';
            return $ot;
        });

        if ($request->filled('search')) {
            $search = strtolower($request->search);
            $items = $items->filter(function($item) use ($search) {
                return str_contains(strtolower($item->user_name), $search) || 
                       str_contains(strtolower($item->user_nip), $search);
            });
        }

        if ($request->filled('unit')) {
            $items = $items->filter(fn($item) => $item->user_unit === $request->unit);
        }

        $perPage = $request->get('per_page', 10);
        $currentPage = \Illuminate\Pagination\LengthAwarePaginator::resolveCurrentPage();
        $pagedItems = $items->forPage($currentPage, $perPage)->values();
        
        $overtimes = (new \Illuminate\Pagination\LengthAwarePaginator(
            $pagedItems,
            $items->count(),
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        ))->onEachSide(1);

        $unitsResponse = $this->ssoService->getUnits(['all' => true]);
        $unitsRaw = $unitsResponse['data'] ?? (isset($unitsResponse[0]) ? $unitsResponse : []);
        $units = collect($unitsRaw)->map(fn($u) => $this->ssoService->normalizeUnit($u)['name'])->unique()->filter()->values();

        $stats = [
            'pending' => Overtime::where('status', 'pending')->count(),
            'total_month' => Overtime::whereMonth('date', Carbon::now()->month)->count(),
            'approved_today' => Overtime::where('status', 'approved')->whereDate('updated_at', Carbon::today())->count(),
        ];

        return view('pages.overtime.admin-index', compact('overtimes', 'units', 'stats'));
    }

    /**
     * Admin: Approve request
     */
    public function approve(Request $request, $id)
    {
        $overtime = Overtime::findOrFail($id);
        $overtime->update([
            'status' => 'approved',
            'approved_by' => Auth::user()->name,
            'admin_note' => $request->admin_note,
        ]);

        return back()->with('success', 'Pengajuan lembur disetujui.');
    }

    /**
     * Admin: Reject request
     */
    public function reject(Request $request, $id)
    {
        $overtime = Overtime::findOrFail($id);
        $overtime->update([
            'status' => 'rejected',
            'approved_by' => Auth::user()->name,
            'admin_note' => $request->admin_note,
        ]);

        return back()->with('success', 'Pengajuan lembur ditolak.');
    }
}
