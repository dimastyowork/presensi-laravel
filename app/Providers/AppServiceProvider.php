<?php

namespace App\Providers;

use Illuminate\Auth\GenericUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Auth::viaRequest('sso-session', function (Request $request) {
            $sessionUser = $request->session()->get('sso_user');

            if (!$sessionUser || !is_array($sessionUser)) {
                return null;
            }

            return new GenericUser([
                'id' => $sessionUser['id'] ?? null,
                'nip' => $sessionUser['nip'] ?? null,
                'name' => $sessionUser['name'] ?? 'N/A',
                'unit_id' => $sessionUser['unit_id'] ?? null,
                'unit' => $sessionUser['unit'] ?? null,
                'email' => $sessionUser['email'] ?? null,
                'is_initial_password' => (bool) ($sessionUser['is_initial_password'] ?? false),
            ]);
        });

        Paginator::useBootstrapFive();
    }
}
