<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $perPage = $request->input('per_page', 10);

        $users = User::when($search, function ($query, $search) {
            return $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('nip', 'like', "%{$search}%")
                  ->orWhere('unit', 'like', "%{$search}%");
            });
        })
        ->orderBy('nip', 'asc')
        ->paginate($perPage)
        ->withQueryString();

        return view('pages.users.index', compact('users'));
    }

    public function create()
    {
        $units = \App\Models\Unit::orderBy('name')->get();
        return view('pages.users.create', compact('units'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nip' => 'required|string|unique:users,nip',
            'name' => 'required|string|max:255',
            'unit' => 'nullable|string|max:255',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $validated['password'] = Hash::make($validated['password']);

        User::create($validated);

        return redirect()->route('users.index')->with('success', 'User berhasil ditambahkan!');
    }

    public function edit(User $user)
    {
        $units = \App\Models\Unit::orderBy('name')->get();
        return view('pages.users.edit', compact('user', 'units'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'nip' => 'required|string|unique:users,nip,' . $user->id,
            'name' => 'required|string|max:255',
            'unit' => 'nullable|string|max:255',
            'password' => 'nullable|string|min:6|confirmed',
        ]);

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        return redirect()->route('users.index')->with('success', 'User berhasil diupdate!');
    }

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('users.index')->with('success', 'User berhasil dihapus!');
    }
}
