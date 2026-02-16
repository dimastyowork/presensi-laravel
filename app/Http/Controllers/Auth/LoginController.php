<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('pages.auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'nip' => ['required', 'string'],
            'password' => ['required'],
        ]);

        try {
            $ssoBaseUrl = config('services.sso.url', 'https://auth.rsasabunda.com');
            $response = Http::timeout(15)
                ->withoutVerifying()
                ->withOptions([
                    'curl' => [
                        CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4, // Paksa IPv4 untuk menghindari DNS timeout
                    ]
                ])
                ->post($ssoBaseUrl . '/api/login', [
                    'nip' => $credentials['nip'],
                    'password' => $credentials['password'],
                ]);

            if ($response->successful()) {
                $data = $response->json();
                Log::info('SSO Login Success Response:', ['data' => $data]);
                
                // Mendukung beberapa struktur response yang umum
                $ssoUser = $data['user'] ?? $data['data']['user'] ?? $data['data'] ?? null;

                if ($ssoUser) {
                    $nip = $ssoUser['nip'] ?? $ssoUser['username'] ?? $ssoUser['nik'] ?? null;
                    
                    if ($nip) {
                        $user = User::updateOrCreate(
                            ['nip' => $nip],
                            [
                                'name' => $ssoUser['name'] ?? $ssoUser['nama'] ?? $ssoUser['name_user'] ?? $nip,
                                'unit' => $ssoUser['unit'] ?? $ssoUser['nama_unit'] ?? $ssoUser['unit_name'] ?? null,
                                'password' => Hash::make($credentials['password']),
                            ]
                        );

                        Auth::login($user, $request->boolean('remember'));
                        $request->session()->regenerate();

                        Log::info('SSO User logged in successfully:', ['nip' => $nip]);
                        return redirect()->intended('/presensi');
                    } else {
                        Log::warning('SSO Login: User data found but "nip/username" is missing.', ['response' => $data]);
                    }
                } else {
                    Log::warning('SSO Login: Response successful but user data is missing.', ['response' => $data]);
                }
            } else {
                Log::warning('SSO Login failed with status: ' . $response->status(), ['response' => $response->body()]);
            }
        } catch (\Exception $e) {
            Log::error('SSO Login Exception: ' . $e->getMessage());
        }

        // Fallback login lokal jika SSO gagal atau tidak merespon record yang cocok
        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            return redirect()->intended('/presensi');
        }

        throw ValidationException::withMessages([
            'nip' => __('auth.failed'),
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
