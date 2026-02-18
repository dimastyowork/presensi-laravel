<?php

namespace App\Http\Controllers;

use App\Models\UserAgreement;
use App\Services\SsoApiService;
use Illuminate\Http\Request;

class PasswordController extends Controller
{
    public function __construct(private readonly SsoApiService $ssoService)
    {
    }

    public function changePassword()
    {
        return view('pages.auth.change-password');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|min:8|confirmed',
            'agreement_accepted' => 'accepted',
        ]);

        $user = auth()->user();
        if (!$user) {
            return redirect()->route('login');
        }

        $loginCheck = $this->ssoService->login((string) $user->nip, (string) $request->current_password);
        if (!$loginCheck['ok']) {
            return back()->withErrors(['current_password' => 'Password saat ini salah.']);
        }

        $payload = [
            'password' => $request->password,
            'password_confirmation' => $request->password_confirmation,
            'is_initial_password' => false,
        ];

        $result = $this->ssoService->updateUser($user->id, $payload);

        if (isset($result['error'])) {
            return back()->withErrors([
                'password' => $result['message'] ?? 'Gagal mengubah password di SSO.',
            ]);
        }

        $sessionUser = session('sso_user', []);
        if (is_array($sessionUser)) {
            $sessionUser['is_initial_password'] = false;
            $sessionUser['agreement_accepted'] = true;
            session(['sso_user' => $sessionUser]);
        }

        UserAgreement::updateOrCreate(
            ['sso_user_id' => (int) $user->id],
            [
                'nip' => $user->nip ?? null,
                'name' => $user->name ?? null,
                'unit' => $user->unit ?? null,
                'agreed_at' => now(),
                'agreement_version' => 'v1',
            ]
        );

        return redirect()->route('presence.index')->with('success', 'Password berhasil diubah.');
    }
}
