<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\PresenceController;

Route::get('/', function () {
    return redirect()->route('presence.index');
});

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::middleware(['auth'])->group(function () {

    Route::get('/ganti-password', [App\Http\Controllers\PasswordController::class, 'changePassword'])->name('password.change');
    Route::post('/ganti-password', [App\Http\Controllers\PasswordController::class, 'updatePassword'])->name('password.update');

    Route::get('/presensi', [PresenceController::class, 'index'])->name('presence.index');
    Route::get('/presensi/riwayat', [PresenceController::class, 'history'])->name('presence.history');
    Route::post('/presensi', [PresenceController::class, 'store'])->name('presence.store');
    Route::put('/presensi/{presence}', [PresenceController::class, 'update'])->name('presence.update');
    Route::get('/images/presences/{filename}', [PresenceController::class, 'showImage'])->name('presence.image');
    
    Route::middleware([\App\Http\Middleware\CheckUnitAccess::class])->group(function () {
        Route::get('/laporan-hrd', [PresenceController::class, 'hrdReport'])->name('hrd.report');
        Route::get('/laporan-hrd/export-excel', [PresenceController::class, 'exportExcel'])->name('hrd.export.excel');
        Route::get('/laporan-hrd/export-pdf', [PresenceController::class, 'exportPdf'])->name('hrd.export.pdf');
        Route::post('/laporan-hrd/approve/{id}', [PresenceController::class, 'approve'])->name('hrd.approve');
        
        Route::resource('users', App\Http\Controllers\UserController::class);
        
        Route::resource('units', App\Http\Controllers\UnitController::class);

        Route::get('/settings', [App\Http\Controllers\GlobalSettingController::class, 'index'])->name('settings.index');
        Route::put('/settings', [App\Http\Controllers\GlobalSettingController::class, 'update'])->name('settings.update');
    });
});
