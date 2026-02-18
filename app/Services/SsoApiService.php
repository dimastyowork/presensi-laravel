<?php

namespace App\Services;

use Closure;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SsoApiService
{
    protected $baseUrl;

    public function __construct()
    {
        $this->baseUrl = config('services.sso.url', 'https://auth.rsasabunda.com');
    }

    protected function token(): ?string
    {
        return session('sso_token') ?? config('services.sso.token');
    }

    protected function client()
    {
        $token = $this->token();

        if (empty($token)) {
            \Illuminate\Support\Facades\Log::warning('SSO token is empty in SsoApiService::client().');
        }

        return Http::withToken($token)
            ->withHeaders([
                'X-API-TOKEN' => $token,
                'Accept' => 'application/json',
            ])
            ->baseUrl($this->baseUrl)
            ->withoutVerifying()
            ->withQueryParameters(['api_token' => $token])
            ->acceptJson();
    }

    protected function safeJson(Closure $callback): array
    {
        try {
            $response = $callback();
            if (!$response) {
                return ['error' => true, 'message' => 'SSO response is empty.'];
            }
            return $response->json() ?? [];
        } catch (\Throwable $e) {
            Log::error('SSO request failed.', [
                'message' => $e->getMessage(),
                'base_url' => $this->baseUrl,
            ]);

            return [
                'error' => true,
                'message' => 'SSO service unavailable',
                'detail' => $e->getMessage(),
            ];
        }
    }

    public function login(string $nip, string $password): array
    {
        $identifier = trim($nip);

        $response = Http::timeout(15)
            ->withoutVerifying()
            ->withOptions([
                'curl' => [
                    CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4,
                ],
            ])
            ->post($this->baseUrl . '/api/login', [
                // Send common identifier aliases for broader SSO compatibility.
                'nip' => $identifier,
                'username' => $identifier,
                'nik' => $identifier,
                'password' => $password,
            ]);

        return [
            'ok' => $response->successful(),
            'status' => $response->status(),
            'data' => $response->json() ?? [],
            'body' => $response->body(),
        ];
    }

    public function normalizeUser(array $rawUser): array
    {
        $id = $rawUser['id']
            ?? $rawUser['ID']
            ?? $rawUser['user_id']
            ?? $rawUser['id_user']
            ?? $rawUser['nip']
            ?? null;

        return [
            'id' => $id,
            'nip' => $rawUser['nip'] ?? $rawUser['username'] ?? $rawUser['nik'] ?? null,
            'name' => $rawUser['name'] ?? $rawUser['nama'] ?? $rawUser['name_user'] ?? 'N/A',
            'unit_id' => $rawUser['unit_id'] ?? $rawUser['id_unit'] ?? $rawUser['unitId'] ?? null,
            'unit' => $rawUser['unit'] ?? $rawUser['nama_unit'] ?? $rawUser['unit_name'] ?? null,
            'email' => $rawUser['email'] ?? null,
            'is_initial_password' => (bool) ($rawUser['is_initial_password'] ?? false),
            'raw' => $rawUser,
        ];
    }

    public function normalizeUnit(array $rawUnit): array
    {
        return [
            'id' => $rawUnit['id'] ?? $rawUnit['ID'] ?? $rawUnit['id_unit'] ?? $rawUnit['unit_id'] ?? null,
            'name' => $rawUnit['name'] ?? $rawUnit['nama'] ?? $rawUnit['nama_unit'] ?? 'N/A',
            'working_days' => $rawUnit['working_days'] ?? [],
            'available_shifts' => $rawUnit['available_shifts'] ?? [],
            'raw' => $rawUnit,
        ];
    }

    public function getUsersMap(array $params = []): array
    {
        $response = $this->getUsers(array_merge(['all' => true], $params));
        $usersRaw = $response['data'] ?? (isset($response[0]) ? $response : []);

        $map = [];
        foreach ($usersRaw as $userRaw) {
            if (!is_array($userRaw)) {
                continue;
            }

            $user = $this->normalizeUser($userRaw);
            $id = $user['id'];
            if ($id !== null) {
                $map[(string) $id] = $user;
            }
        }

        return $map;
    }

    public function getUnits($params = [])
    {
        return $this->safeJson(fn() => $this->client()->get('/api/units', $params));
    }

    public function getUnit($id)
    {
        return $this->safeJson(fn() => $this->client()->get("/api/units/{$id}"));
    }

    public function createUnit($data)
    {
        return $this->safeJson(fn() => $this->client()->post('/api/units', $data));
    }

    public function updateUnit($id, $data)
    {
        return $this->safeJson(fn() => $this->client()->put("/api/units/{$id}", $data));
    }

    public function deleteUnit($id)
    {
        return $this->safeJson(fn() => $this->client()->delete("/api/units/{$id}"));
    }

    // === USERS ===

    public function getUsers($params = [])
    {
        $response = null;
        $payload = $this->safeJson(function () use ($params, &$response) {
            $response = $this->client()->get('/api/users', $params);
            return $response;
        });

        if (!empty($payload['error'])) {
            return $payload;
        }

        if (!$response->successful()) {
            $token = $this->token();
            Log::error('SSO API Error Details:', [
                'status' => $response->status(),
                'url' => $this->baseUrl . '/api/users',
                'token_used' => $token ? substr($token, 0, 5) . '...' : null,
                'body' => $response->body()
            ]);
        }
        return $payload;
    }

    public function getUser($id)
    {
        return $this->safeJson(fn() => $this->client()->get("/api/users/{$id}"));
    }

    public function createUser($data)
    {
        return $this->safeJson(fn() => $this->client()->post('/api/users', $data));
    }

    public function updateUser($id, $data)
    {
        return $this->safeJson(fn() => $this->client()->put("/api/users/{$id}", $data));
    }

    public function deleteUser($id)
    {
        return $this->safeJson(fn() => $this->client()->delete("/api/users/{$id}"));
    }
}
