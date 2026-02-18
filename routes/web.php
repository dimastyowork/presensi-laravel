<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\PresenceController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\ShiftController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\GlobalSettingController;
use App\Http\Controllers\PasswordController;

Route::get('/', function () {
    return redirect()->route('presence.index');
});

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
Route::get('/persetujuan-dan-kebijakan', function () {
    return view('pages.legal.terms');
})->name('legal.terms');

Route::middleware(['auth'])->group(function () {

    Route::get('/ganti-password', [PasswordController::class, 'changePassword'])->name('password.change');
    Route::post('/ganti-password', [PasswordController::class, 'updatePassword'])->name('password.update');

    Route::get('/presensi', [PresenceController::class, 'index'])->name('presence.index');
    Route::get('/presensi/riwayat', [PresenceController::class, 'history'])->name('presence.history');
    Route::post('/presensi', [PresenceController::class, 'store'])->name('presence.store');
    Route::post('/presensi/update-shift', [PresenceController::class, 'updateShift'])->name('presence.update_shift');
    Route::put('/presensi/{presence}', [PresenceController::class, 'update'])->name('presence.update');
    Route::get('/images/presences/{filename}', [PresenceController::class, 'showImage'])->name('presence.image');
    
    Route::middleware([\App\Http\Middleware\CheckUnitAccess::class])->group(function () {
        Route::get('/laporan-hrd', [PresenceController::class, 'hrdReport'])->name('hrd.report');
        Route::get('/laporan-hrd/export-excel', [PresenceController::class, 'exportExcel'])->name('hrd.export.excel');
        Route::get('/laporan-hrd/export-pdf', [PresenceController::class, 'exportPdf'])->name('hrd.export.pdf');
        Route::get('/laporan-hrd/{presence}', [PresenceController::class, 'showDetail'])->name('hrd.detail');
        Route::post('/laporan-hrd/approve/{id}', [PresenceController::class, 'approve'])->name('hrd.approve');
        
        Route::post('/users/quick-update-shift', [UserController::class, 'quickUpdateShift'])->name('users.quick-update-shift');
        Route::resource('users', UserController::class);
        
        Route::resource('units', UnitController::class)->only(['index']);
        Route::resource('shifts', ShiftController::class);

        Route::get('/settings', [GlobalSettingController::class, 'index'])->name('settings.index');
        Route::put('/settings', [GlobalSettingController::class, 'update'])->name('settings.update');
    });
});
