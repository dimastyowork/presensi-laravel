<?php

namespace App\Http\Controllers;

use App\Models\Shift;
use App\Models\UserShift;
use App\Services\SsoApiService;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    protected $ssoService;

    public function __construct(SsoApiService $ssoService)
    {
        $this->ssoService = $ssoService;
    }

    public function index(Request $request)
    {
        $allShifts = Shift::all();
        $perPage = (int) $request->input('per_page', 10);
        $currentPage = (int) $request->input('page', 1);
        
        $params = array_merge($request->except(['page', 'per_page']), ['all' => true]);
        $response = $this->ssoService->getUsers($params);
        
        if (!isset($response['data'])) {
            \Illuminate\Support\Facades\Log::error('SSO API User Response Error', ['response' => $response]);
        }

        $itemsRaw = $response['data'] ?? $response ?? [];
        if (isset($itemsRaw['data']) && is_array($itemsRaw['data'])) {
            $itemsRaw = $itemsRaw['data'];
        }

        $pagedItemsRaw = collect($itemsRaw)->values();
        $userIds = $pagedItemsRaw->map(fn($item) => $item['id'] ?? $item['ID'] ?? $item['id_user'] ?? $item['user_id'] ?? null)->filter()->toArray();
        $userShifts = UserShift::with('shift')
            ->whereIn('user_id', $userIds)
            ->orderByDesc('is_active')
            ->get()
            ->groupBy('user_id');

        $items = $pagedItemsRaw->map(function($item) use ($userShifts) {
            $obj = (object) $item;
            $obj->id = $obj->id ?? $obj->ID ?? $obj->id_user ?? $obj->user_id ?? $obj->nip ?? null;
            $obj->name = $obj->name ?? $obj->nama ?? $obj->name_user ?? $obj->nip ?? 'N/A';
            $obj->nip = $obj->nip ?? $obj->username ?? '-';
            $obj->unit = $obj->unit ?? $obj->nama_unit ?? $obj->unit_name ?? '-';
            
            $shiftRecords = $userShifts->get($obj->id, collect());
            $shiftNames = $shiftRecords->pluck('shift.name')->filter()->values();
            $obj->shift = $shiftNames->isNotEmpty() ? $shiftNames->implode(', ') : '-';
            $obj->shift_ids = $shiftRecords->pluck('shift_id')->map(fn($id) => (int) $id)->values()->all();
            $obj->active_shift_id = $shiftRecords->firstWhere('is_active', true)->shift_id ?? ($shiftRecords->first()->shift_id ?? null);

            if (isset($obj->created_at) && is_string($obj->created_at)) {
                $obj->created_at = \Carbon\Carbon::parse($obj->created_at);
            } else if (!isset($obj->created_at)) {
                $obj->created_at = now();
            }
            return $obj;
        })->sortBy('nip')->values();

        $total = $items->count();
        $pagedItems = $items->forPage($currentPage, $perPage)->values();

        $users = new LengthAwarePaginator(
            $pagedItems,
            $total,
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('pages.users.index', compact('users', 'allShifts'));
    }

    public function quickUpdateShift(Request $request)
    {
        $request->validate([
            'user_id' => 'required',
            'shift_ids' => 'nullable|array',
            'shift_ids.*' => 'exists:shifts,id',
        ]);

        $shiftIds = collect($request->input('shift_ids', []))
            ->map(fn($id) => (int) $id)
            ->unique()
            ->values();

        DB::transaction(function () use ($request, $shiftIds) {
            UserShift::where('user_id', $request->user_id)->delete();

            if ($shiftIds->isEmpty()) {
                return;
            }

            $rows = $shiftIds->values()->map(function ($shiftId, $index) use ($request) {
                return [
                    'user_id' => (int) $request->user_id,
                    'shift_id' => $shiftId,
                    'is_active' => $index === 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            })->all();

            UserShift::insert($rows);
        });

        return response()->json(['success' => true, 'message' => 'Shift updated successfully']);
    }


    public function create()
    {
        $unitsResponse = $this->ssoService->getUnits(['all' => true]);
        $unitsRaw = $unitsResponse['data'] ?? (isset($unitsResponse[0]) ? $unitsResponse : []);
        $units = collect($unitsRaw)
            ->filter(fn($u) => is_array($u) || is_object($u))
            ->map(function ($u) {
                $arr = (array) $u;
                return (object) [
                    'id' => $arr['id'] ?? $arr['ID'] ?? $arr['id_unit'] ?? $arr['unit_id'] ?? null,
                    'name' => $arr['name'] ?? $arr['nama'] ?? $arr['nama_unit'] ?? null,
                ];
            })
            ->filter(fn($u) => !empty($u->name))
            ->values();
        return view('pages.users.create', compact('units'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nip' => 'required|string',
            'name' => 'required|string|max:255',
            'unit' => 'nullable|string|max:255',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $result = $this->ssoService->createUser($validated);

        if (isset($result['error']) || (isset($result['message']) && !isset($result['id']) && !isset($result['data']))) {
            return back()->withInput()->with('error', $result['message'] ?? 'Gagal menambahkan user ke SSO');
        }

        return redirect()->route('users.index')->with('success', 'User berhasil ditambahkan ke SSO!');
    }

    public function edit($id)
    {
        $userResponse = $this->ssoService->getUser($id);
        $user = (object) ($userResponse['data'] ?? $userResponse);
        
        $unitsResponse = $this->ssoService->getUnits(['all' => true]);
        $unitsRaw = $unitsResponse['data'] ?? (isset($unitsResponse[0]) ? $unitsResponse : []);
        $units = collect($unitsRaw)
            ->filter(fn($u) => is_array($u) || is_object($u))
            ->map(function ($u) {
                $arr = (array) $u;
                return (object) [
                    'id' => $arr['id'] ?? $arr['ID'] ?? $arr['id_unit'] ?? $arr['unit_id'] ?? null,
                    'name' => $arr['name'] ?? $arr['nama'] ?? $arr['nama_unit'] ?? null,
                ];
            })
            ->filter(fn($u) => !empty($u->name))
            ->values();

        $shifts = Shift::all();
        $user->shift_ids = UserShift::where('user_id', $id)->pluck('shift_id')->map(fn($id) => (int) $id)->all();

        return view('pages.users.edit', compact('user', 'units', 'shifts'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'nip' => 'required|string',
            'name' => 'required|string|max:255',
            'unit' => 'nullable|string|max:255',
            'password' => 'nullable|string|min:6|confirmed',
            'shift_ids' => 'nullable|array',
            'shift_ids.*' => 'exists:shifts,id',
        ]);

        $result = $this->ssoService->updateUser($id, $validated);

        if (isset($result['error']) || (isset($result['message']) && !isset($result['id']) && !isset($result['data']))) {
            return back()->withInput()->with('error', $result['message'] ?? 'Gagal mengupdate user di SSO');
        }

        $this->syncUserShifts((int) $id, $request->input('shift_ids', []));

        return redirect()->route('users.index')->with('success', 'User berhasil diupdate!');
    }

    public function destroy($id)
    {
        $this->ssoService->deleteUser($id);
        return redirect()->route('users.index')->with('success', 'User berhasil dihapus dari SSO!');
    }

    private function syncUserShifts(int $userId, array $shiftIds): void
    {
        $normalized = collect($shiftIds)
            ->map(fn($id) => (int) $id)
            ->filter(fn($id) => $id > 0)
            ->unique()
            ->values();

        DB::transaction(function () use ($userId, $normalized) {
            UserShift::where('user_id', $userId)->delete();

            if ($normalized->isEmpty()) {
                return;
            }

            $rows = $normalized->map(function ($shiftId, $index) use ($userId) {
                return [
                    'user_id' => $userId,
                    'shift_id' => $shiftId,
                    'is_active' => $index === 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            })->all();

            UserShift::insert($rows);
        });
    }
}
