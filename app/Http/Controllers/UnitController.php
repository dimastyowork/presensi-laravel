<?php

namespace App\Http\Controllers;

use App\Models\Unit as LocalUnit;
use App\Services\SsoApiService;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Schema;

class UnitController extends Controller
{
    protected $ssoService;

    public function __construct(SsoApiService $ssoService)
    {
        $this->ssoService = $ssoService;
    }

    public function index(Request $request)
    {
        $perPage = (int) $request->input('per_page', 10);
        $currentPage = (int) $request->input('page', 1);

        // Fetch ALL units to handle local pagination reliably
        // IMPORTANT: We must strip 'page' from the params sent to SSO so it doesn't return an empty page 2.
        $params = array_merge($request->except(['page', 'per_page']), ['all' => true]);
        $response = $this->ssoService->getUnits($params);
        
        if (!isset($response['data'])) {
            \Illuminate\Support\Facades\Log::error('SSO API Unit Response Error', ['response' => $response]);
        }
        
        $itemsRaw = $response['data'] ?? $response ?? [];
        if (isset($itemsRaw['data']) && is_array($itemsRaw['data'])) {
            $itemsRaw = $itemsRaw['data'];
        }

        $items = collect($itemsRaw)->values()->map(function($item) {
            $obj = (object) $item;
            $obj->id = $obj->id ?? $obj->ID ?? $obj->id_unit ?? $obj->id_user ?? $obj->unit_id ?? null;
            $obj->name = $obj->name ?? $obj->nama ?? $obj->nama_unit ?? 'N/A';
            $obj->working_days = is_array($obj->working_days ?? null) ? $obj->working_days : [];
            $obj->available_shifts = is_array($obj->available_shifts ?? null) ? $obj->available_shifts : [];
            
            if (isset($obj->created_at) && is_string($obj->created_at)) {
                $obj->created_at = \Carbon\Carbon::parse($obj->created_at);
            } else if (!isset($obj->created_at)) {
                $obj->created_at = now();
            }
            return $obj;
        });

        $localUnits = LocalUnit::all();
        $localBySsoId = Schema::hasColumn('units', 'sso_unit_id')
            ? $localUnits->filter(fn(LocalUnit $u) => !is_null($u->sso_unit_id))->keyBy('sso_unit_id')
            : collect();
        $localByName = Schema::hasColumn('units', 'name')
            ? $localUnits->keyBy(fn(LocalUnit $u) => mb_strtolower(trim((string) $u->name)))
            : collect();

        $items = $items->map(function ($unit) use ($localBySsoId, $localByName) {
            $local = null;
            if (!empty($unit->id)) {
                $local = $localBySsoId->get((int) $unit->id);
            }
            if (!$local && !empty($unit->name)) {
                $local = $localByName->get(mb_strtolower(trim((string) $unit->name)));
            }
            if ($local) {
                $unit->working_days = is_array($local->working_days) ? $local->working_days : [];
                $unit->available_shifts = is_array($local->available_shifts) ? $local->available_shifts : [];
            }
            return $unit;
        });

        // Local pagination logic
        $total = $items->count();
        $pagedItems = $items->forPage($currentPage, $perPage)->values();

        $units = new LengthAwarePaginator(
            $pagedItems,
            $total,
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('pages.units.index', compact('units'));
}

    public function create()
    {
        $unitsResponse = $this->ssoService->getUnits(['all' => true]);
        $unitsRaw = $unitsResponse['data'] ?? (isset($unitsResponse[0]) ? $unitsResponse : []);
        $ssoUnits = collect($unitsRaw)
            ->filter(fn($u) => is_array($u))
            ->map(fn($u) => $this->ssoService->normalizeUnit($u))
            ->filter(fn($u) => !empty($u['id']))
            ->values();

        return view('pages.units.create', compact('ssoUnits'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'sso_unit_id' => 'nullable|integer',
            'name' => 'nullable|string|max:255',
            'available_shifts' => 'nullable|array',
            'available_shifts.*.name' => 'required|string|max:255',
            'available_shifts.*.start_time' => 'required|string',
            'available_shifts.*.end_time' => 'required|string',
            'working_days' => 'nullable|array',
        ]);

        $validated['working_days'] = $request->input('working_days', []);
        $ssoUnitId = null;

        if (!empty($validated['sso_unit_id'])) {
            $response = $this->ssoService->getUnit($validated['sso_unit_id']);
            $normalized = $this->ssoService->normalizeUnit((array) ($response['data'] ?? $response ?? []));
            if (empty($normalized['id'])) {
                return back()->withInput()->with('error', 'Unit SSO tidak memiliki ID yang valid.');
            }
            $ssoUnitId = (int) $normalized['id'];
        } else {
            if (empty($validated['name'])) {
                return back()->withInput()->with('error', 'Nama unit wajib diisi untuk membuat unit baru.');
            }

            $createResult = $this->ssoService->createUnit(['name' => $validated['name']]);
            $createdRaw = $createResult['data'] ?? $createResult ?? [];
            $created = $this->ssoService->normalizeUnit((array) $createdRaw);

            if (empty($created['id'])) {
                return back()->withInput()->with('error', $createResult['message'] ?? 'Gagal menambahkan unit ke SSO.');
            }
            $ssoUnitId = (int) $created['id'];
        }

        LocalUnit::updateOrCreate(
            ['sso_unit_id' => $ssoUnitId],
            [
                'available_shifts' => $validated['available_shifts'] ?? [],
                'working_days' => $validated['working_days'],
            ]
        );

        return redirect()->route('units.index')->with('success', 'Pengaturan jam kerja unit berhasil disimpan lokal.');
    }

    public function edit($id)
    {
        $response = $this->ssoService->getUnit($id);
        $rawUnit = (array) ($response['data'] ?? $response ?? []);
        $normalized = $this->ssoService->normalizeUnit($rawUnit);
        $local = null;
        if (!empty($normalized['id']) && Schema::hasColumn('units', 'sso_unit_id')) {
            $local = LocalUnit::where('sso_unit_id', (int) $normalized['id'])->first();
        }
        if (!$local && !empty($normalized['name']) && Schema::hasColumn('units', 'name')) {
            $local = LocalUnit::where('name', $normalized['name'])->first();
        }

        $unit = (object) [
            'id' => $normalized['id'],
            'name' => $normalized['name'],
            'working_days' => is_array($local?->working_days) ? $local->working_days : [],
            'available_shifts' => is_array($local?->available_shifts) ? $local->available_shifts : [],
        ];
        
        return view('pages.units.edit', compact('unit'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'available_shifts' => 'nullable|array',
            'available_shifts.*.name' => 'required|string|max:255',
            'available_shifts.*.start_time' => 'required|string',
            'available_shifts.*.end_time' => 'required|string',
            'working_days' => 'nullable|array',
        ]);

        $validated['working_days'] = $request->input('working_days', []);

        $response = $this->ssoService->getUnit($id);
        $rawUnit = (array) ($response['data'] ?? $response ?? []);
        $normalized = $this->ssoService->normalizeUnit($rawUnit);
        $ssoUnitId = $normalized['id'] ?? $id;

        LocalUnit::updateOrCreate(
            ['sso_unit_id' => (int) $ssoUnitId],
            [
                'available_shifts' => $validated['available_shifts'] ?? [],
                'working_days' => $validated['working_days'],
            ]
        );

        return redirect()->route('units.index')->with('success', 'Pengaturan jam kerja unit berhasil diupdate lokal.');
    }

    public function destroy($id)
    {
        $response = $this->ssoService->getUnit($id);
        $rawUnit = (array) ($response['data'] ?? $response ?? []);
        $normalized = $this->ssoService->normalizeUnit($rawUnit);
        if (!empty($normalized['id'])) {
            LocalUnit::where('sso_unit_id', (int) $normalized['id'])->delete();
        }

        return redirect()->route('units.index')->with('success', 'Pengaturan jam kerja unit berhasil dihapus dari database lokal.');
    }
}
