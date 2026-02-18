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
        $currentPage = (int) $request->input('page', 1);
        
        // Fetch ALL users to handle local pagination reliably, 
        // since SSO API might have a different default per_page (e.g. 15) 
        // or ignore our per_page parameter. 
        // IMPORTANT: We must strip 'page' from the params sent to SSO so it doesn't return an empty page 2.
        $params = array_merge($request->except(['page', 'per_page']), ['all' => true]);
        $response = $this->ssoService->getUsers($params);
        
        if (!isset($response['data'])) {
            \Illuminate\Support\Facades\Log::error('SSO API User Response Error', ['response' => $response]);
        }

        // The API when called with 'all' => true usually returns the flat array in 'data' 
        // or as the root response.
        $itemsRaw = $response['data'] ?? $response ?? [];
        if (isset($itemsRaw['data']) && is_array($itemsRaw['data'])) {
            $itemsRaw = $itemsRaw['data'];
        }

        $items = collect($itemsRaw)->values()->map(function($item) {
            $obj = (object) $item;
            $obj->id = $obj->id ?? $obj->ID ?? $obj->id_user ?? $obj->user_id ?? $obj->nip ?? null;
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

        // Local pagination logic
        $total = $items->count();
        $pagedItems = $items->forPage($currentPage, $perPage)->values();

        $users = new LengthAwarePaginator(
            $pagedItems,
            $total,
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

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
