<?php

namespace App\Http\Controllers;

use App\Services\SsoApiService;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

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
            
            if (isset($obj->created_at) && is_string($obj->created_at)) {
                $obj->created_at = \Carbon\Carbon::parse($obj->created_at);
            } else if (!isset($obj->created_at)) {
                $obj->created_at = now();
            }
            return $obj;
        });

        $total = $items->count();
        $pagedItems = $items->forPage($currentPage, $perPage)->values();

        $units = (new LengthAwarePaginator(
            $pagedItems,
            $total,
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        ))->onEachSide(1);

        return view('pages.units.index', compact('units'));
}

    public function create()
    {
        return redirect()->route('units.index')->with('error', 'Manajemen unit dikelola dari Auth/SSO.');
    }

    public function store(Request $request)
    {
        return redirect()->route('units.index')->with('error', 'Manajemen unit dikelola dari Auth/SSO.');
    }

    public function edit($id)
    {
        return redirect()->route('units.index')->with('error', 'Manajemen unit dikelola dari Auth/SSO.');
    }

    public function update(Request $request, $id)
    {
        return redirect()->route('units.index')->with('error', 'Manajemen unit dikelola dari Auth/SSO.');
    }

    public function destroy($id)
    {
        return redirect()->route('units.index')->with('error', 'Manajemen unit dikelola dari Auth/SSO.');
    }
}
