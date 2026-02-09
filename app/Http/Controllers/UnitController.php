<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use Illuminate\Http\Request;

class UnitController extends Controller
{
    public function index()
    {
        $units = Unit::orderBy('name')->paginate(10);
        return view('pages.units.index', compact('units'));
    }

    public function create()
    {
        return view('pages.units.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:units,name|max:255',
            'available_shifts' => 'nullable|array',
            'available_shifts.*.name' => 'required|string|max:255',
            'available_shifts.*.start_time' => 'required|string',
            'available_shifts.*.end_time' => 'required|string',
        ]);

        Unit::create($validated);

        return redirect()->route('units.index')->with('success', 'Unit berhasil ditambahkan!');
    }

    public function edit(Unit $unit)
    {
        return view('pages.units.edit', compact('unit'));
    }

    public function update(Request $request, Unit $unit)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:units,name,' . $unit->id,
            'available_shifts' => 'nullable|array',
            'available_shifts.*.name' => 'required|string|max:255',
            'available_shifts.*.start_time' => 'required|string',
            'available_shifts.*.end_time' => 'required|string',
        ]);

        $unit->update($validated);

        return redirect()->route('units.index')->with('success', 'Unit berhasil diupdate!');
    }

    public function destroy(Unit $unit)
    {
        $unit->delete();
        return redirect()->route('units.index')->with('success', 'Unit berhasil dihapus!');
    }
}
