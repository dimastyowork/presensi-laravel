<?php

namespace App\Http\Middleware;

use App\Models\UserAgreement;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\Response;

class ForceChangePassword
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        if (
            $user &&
            !$request->is('ganti-password') &&
            !$request->is('logout')
        ) {
            $mustChangePassword = (bool) ($user->is_initial_password ?? false);
            $hasAgreement = false;

            if (Schema::hasTable('user_agreements')) {
                $hasAgreement = UserAgreement::where('sso_user_id', (int) ($user->id ?? 0))->exists();
            }

            if ($mustChangePassword || !$hasAgreement) {
                return redirect()->route('password.change')->with(
                    'warning',
                    'Sebelum melanjutkan, Anda wajib mengganti password awal dan menyetujui kebijakan penggunaan aplikasi.'
                );
            }
        }

        return $next($request);
    }
}
