<?php

namespace App\Http\Controllers;

use App\Models\Shift;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class ShiftController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Shift::query();

        if ($request->filled('search')) {
            $search = mb_strtolower($request->input('search'));
            $query->whereRaw('LOWER(name) LIKE ?', ["%{$search}%"]);
        }

        $perPage = (int) $request->input('per_page', 10);
        $shifts = $query->paginate($perPage)->withQueryString();

        return view('pages.shifts.index', compact('shifts'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('pages.shifts.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'start_time' => 'required',
            'end_time' => 'required',
            'working_days' => 'nullable|array',
        ]);
        $validated['working_days'] = $request->input('working_days', []);

        Shift::create($validated);

        return redirect()->route('shifts.index')->with('success', 'Shift berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     */
    public function show(Shift $shift)
    {
        return view('pages.shifts.show', compact('shift'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Shift $shift)
    {
        return view('pages.shifts.edit', compact('shift'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Shift $shift)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'start_time' => 'required',
            'end_time' => 'required',
            'working_days' => 'nullable|array',
        ]);
        $validated['working_days'] = $request->input('working_days', []);

        $shift->update($validated);

        return redirect()->route('shifts.index')->with('success', 'Shift berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Shift $shift)
    {
        $shift->delete();

        return redirect()->route('shifts.index')->with('success', 'Shift berhasil dihapus');
    }
}
