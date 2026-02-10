<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class GlobalSettingController extends Controller
{
    public function index()
    {
        $settings = \App\Models\GlobalSetting::all();
        return view('pages.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $data = $request->except('_token');
        
        foreach ($data as $key => $value) {
            \App\Models\GlobalSetting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        // Specific cleanups if any


        return back()->with('success', 'Pengaturan berhasil diperbarui.');
    }
}
