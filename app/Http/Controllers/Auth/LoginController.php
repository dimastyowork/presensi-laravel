<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\SsoApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function __construct(private readonly SsoApiService $ssoService)
    {
    }

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
            $loginResult = $this->ssoService->login($credentials['nip'], $credentials['password']);

            if ($loginResult['ok']) {
                $data = $loginResult['data'];
                $ssoUserRaw = $data['user'] ?? $data['data']['user'] ?? $data['data'] ?? null;
                $token = $data['token'] ?? $data['access_token'] ?? $data['data']['token'] ?? $data['data']['access_token'] ?? null;

                if (is_array($ssoUserRaw)) {
                    $normalizedUser = $this->ssoService->normalizeUser($ssoUserRaw);
                    if (!empty($normalizedUser['id']) || !empty($normalizedUser['nip'])) {
                        $request->session()->regenerate();
                        if (!empty($token)) {
                            $request->session()->put('sso_token', $token);
                        }

                        // Some login responses do not include full flags (e.g. is_initial_password),
                        // so fetch detail profile when possible.
                        if (!empty($normalizedUser['id']) && (!array_key_exists('is_initial_password', $ssoUserRaw) || empty($normalizedUser['unit_id']))) {
                            $detailResponse = $this->ssoService->getUser($normalizedUser['id']);
                            $detailRaw = $detailResponse['data'] ?? $detailResponse ?? null;
                            if (is_array($detailRaw) && !empty($detailRaw)) {
                                $normalizedUser = array_merge(
                                    $normalizedUser,
                                    $this->ssoService->normalizeUser($detailRaw)
                                );
                            }
                        }

                        // If SSO user payload still does not include unit_id,
                        // resolve once from unit name to keep runtime checks strict by ID.
                        if (empty($normalizedUser['unit_id']) && !empty($normalizedUser['unit'])) {
                            $unitsResponse = $this->ssoService->getUnits(['all' => true]);
                            $unitsRaw = $unitsResponse['data'] ?? (isset($unitsResponse[0]) ? $unitsResponse : []);
                            foreach ($unitsRaw as $unitRaw) {
                                if (!is_array($unitRaw)) {
                                    continue;
                                }
                                $unit = $this->ssoService->normalizeUnit($unitRaw);
                                if (!empty($unit['id']) && strcasecmp((string) $unit['name'], (string) $normalizedUser['unit']) === 0) {
                                    $normalizedUser['unit_id'] = (int) $unit['id'];
                                    break;
                                }
                            }
                        }

                        $request->session()->put('sso_user', $normalizedUser);

                        Log::info('SSO login success.', [
                            'user_id' => $normalizedUser['id'],
                            'nip' => $normalizedUser['nip'],
                            'unit_id' => $normalizedUser['unit_id'] ?? null,
                            'is_initial_password' => $normalizedUser['is_initial_password'] ?? null,
                        ]);

                        return redirect()->intended('/presensi');
                    }
                }

                Log::warning('SSO login response did not contain a usable user payload.', [
                    'keys' => array_keys($data),
                ]);
            } else {
                $ssoMessage = data_get($loginResult, 'data.message');
                Log::warning('SSO login failed.', [
                    'status' => $loginResult['status'],
                    'body' => $loginResult['body'],
                    'message' => $ssoMessage,
                ]);

                throw ValidationException::withMessages([
                    'nip' => $ssoMessage ?: 'Login ditolak oleh server SSO.',
                ]);
            }
        } catch (\Exception $e) {
            if ($e instanceof ValidationException) {
                throw $e;
            }
            Log::error('SSO Login Exception: ' . $e->getMessage());
        }

        throw ValidationException::withMessages([
            'nip' => 'Login gagal. Pastikan NIP dan password SSO benar.',
        ]);
    }

    public function logout(Request $request)
    {
        $request->session()->forget(['sso_user', 'sso_token']);
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
