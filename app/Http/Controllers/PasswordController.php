<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PasswordController extends Controller
{
    public function changePassword()
    {
        return view('pages.auth.change-password');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|min:8|confirmed',
        ]);

        $user = auth()->user();

        if (!\Illuminate\Support\Facades\Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Password saat ini salah.']);
        }

        $user->update([
            'password' => \Illuminate\Support\Facades\Hash::make($request->password),
            'is_initial_password' => false,
        ]);

        return redirect()->route('presence.index')->with('success', 'Password berhasil diubah.');
    }
}
