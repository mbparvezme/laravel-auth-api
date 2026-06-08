<?php

namespace App\Services;

use App\Models\AppLog;
use Illuminate\Http\Request;

class AppLogService
{
    private const USER_VISIBLE_ACTIONS = [
        'REGISTER', 'LOGIN_OK', 'LOGOUT', 'LOGOUT_ALL',
        'EMAIL_VERIFIED', 'PASSWORD_RESET_OK', 'PASSWORD_UPDATED',
        'PROFILE_UPDATED', 'EMAIL_UPDATE_REQUESTED', 'EMAIL_UPDATED',
        'STATUS_UPDATED', 'SOCIAL_LOGIN', 'SOCIAL_REGISTER',
        'API_KEY_CREATED', 'API_KEY_REVOKED',
        'MAGIC_LINK_LOGIN',
    ];

    public function log(string $action, ?int $userId = null, array $extra = []): void
    {
        AppLog::create([
            'user_id'      => $userId,
            'action'       => $action,
            'data'         => array_merge($this->deviceInfo(request()), $extra),
            'user_visible' => in_array($action, self::USER_VISIBLE_ACTIONS),
        ]);
    }

    private function deviceInfo(Request $request): array
    {
        $ua = $request->header('User-Agent', '');

        return [
            'ip_address' => $request->ip(),
            'platform'   => $this->detectPlatform($ua),
            'device'     => $this->detectDevice($ua),
        ];
    }

    private function detectPlatform(string $ua): string
    {
        return match (true) {
            str_contains($ua, 'Windows') => 'Windows',
            str_contains($ua, 'Mac')     => 'macOS',
            str_contains($ua, 'Android') => 'Android',
            str_contains($ua, 'iPhone')  => 'iOS',
            str_contains($ua, 'Linux')   => 'Linux',
            default                      => 'Unknown',
        };
    }

    private function detectDevice(string $ua): string
    {
        return match (true) {
            str_contains($ua, 'Mobile') || str_contains($ua, 'Android') => 'Mobile',
            str_contains($ua, 'Tablet') || str_contains($ua, 'iPad')    => 'Tablet',
            default                                                       => 'Desktop',
        };
    }
}
