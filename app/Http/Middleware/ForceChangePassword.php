<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
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

        if ($user && $user->is_initial_password && 
            !$request->is('ganti-password') && 
            !$request->is('logout')) {
            return redirect()->route('password.change')->with('warning', 'Harap ganti password Anda untuk keamanan.');
        }

        return $next($request);
    }
}
