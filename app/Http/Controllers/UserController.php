<?php

namespace App\Http\Controllers;

use App\Services\SsoApiService;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class UserController extends Controller
{
    protected $ssoService;

    public function __construct(SsoApiService $ssoService)
    {
        $this->ssoService = $ssoService;
    }

    public function index(Request $request)
    {
        $perPage = (int) $request->input('per_page', 10);
        $request->merge(['per_page' => $perPage]);
        $response = $this->ssoService->getUsers($request->all());
        
        if (!isset($response['data'])) {
            \Illuminate\Support\Facades\Log::error('SSO API User Response Error', ['response' => $response]);
        }
        // Detect pagination more robustly (support top-level or meta-nested)
        $paginatedData = null;
        if (isset($response['data']) && (isset($response['current_page']) || isset($response['meta']['current_page']))) {
            $paginatedData = $response;
            $itemsRaw = $response['data'];
        } else if (isset($response['data']) && is_array($response['data']) && !isset($response['data'][0])) {
            // Case where data is an object with current_page inside it (unlikely but possible)
            $paginatedData = $response['data'];
            $itemsRaw = $response['data']['data'] ?? [];
        } else {
            $itemsRaw = $response['data'] ?? $response ?? [];
        }

        $items = collect($itemsRaw)->values()->map(function($item) {
            $obj = (object) $item;
            // Robust ID normalization
            $obj->id = $obj->id ?? $obj->ID ?? $obj->id_user ?? $obj->user_id ?? $obj->nip ?? null;
            
            // Property normalization for view compatibility
            $obj->name = $obj->name ?? $obj->nama ?? $obj->name_user ?? $obj->nip ?? 'N/A';
            $obj->nip = $obj->nip ?? $obj->username ?? '-';
            $obj->unit = $obj->unit ?? $obj->nama_unit ?? $obj->unit_name ?? '-';

            if (isset($obj->created_at) && is_string($obj->created_at)) {
                $obj->created_at = \Carbon\Carbon::parse($obj->created_at);
            } else if (!isset($obj->created_at)) {
                $obj->created_at = now();
            }
            return $obj;
        })->sortBy('nip')->values();

        if ($paginatedData) {
            $total = (int) ($paginatedData['total'] ?? $paginatedData['meta']['total'] ?? $items->count());
            $perPage = (int) ($request->input('per_page', $paginatedData['per_page'] ?? $paginatedData['meta']['per_page'] ?? 15));
            $currentPage = (int) ($paginatedData['current_page'] ?? $paginatedData['meta']['current_page'] ?? 1);
            
            $users = new LengthAwarePaginator(
                $items,
                $total,
                $perPage,
                $currentPage,
                ['path' => $request->url(), 'query' => $request->query()]
            );
        } else {
            $users = new LengthAwarePaginator($items->forPage(1, $perPage), $items->count(), $perPage, 1);
        }

        return view('pages.users.index', compact('users'));
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

        return view('pages.users.edit', compact('user', 'units'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'nip' => 'required|string',
            'name' => 'required|string|max:255',
            'unit' => 'nullable|string|max:255',
            'password' => 'nullable|string|min:6|confirmed',
        ]);

        $result = $this->ssoService->updateUser($id, $validated);

        if (isset($result['error']) || (isset($result['message']) && !isset($result['id']) && !isset($result['data']))) {
            return back()->withInput()->with('error', $result['message'] ?? 'Gagal mengupdate user di SSO');
        }

        return redirect()->route('users.index')->with('success', 'User berhasil diupdate di SSO!');
    }

    public function destroy($id)
    {
        $this->ssoService->deleteUser($id);
        return redirect()->route('users.index')->with('success', 'User berhasil dihapus dari SSO!');
    }
}
