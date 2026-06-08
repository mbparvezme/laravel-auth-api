<?php

namespace App\Features\MultiDevice\Middleware;

use App\Features\MultiDevice\Models\DeviceSession;
use Closure;
use Illuminate\Http\Request;

class TrackDeviceSession
{
    public function handle(Request $request, Closure $next): mixed
    {
        $response = $next($request);

        $token = $request->user()?->currentAccessToken();

        // Skip TransientToken (used in tests) and any non-Eloquent token
        if (!$token || !method_exists($token, 'getKey')) {
            return $response;
        }

        $tokenId = $token->getKey();
        $ua      = $request->userAgent() ?? '';

        try {
            DeviceSession::updateOrCreate(
                ['token_id' => $tokenId],
                [
                    'ip_address' => $request->ip(),
                    'platform'   => $this->detectPlatform($ua),
                    'device'     => $this->detectDevice($ua),
                    'user_agent' => $ua,
                ]
            );
        } catch (\Exception) {
            // Token was revoked during this request (e.g. logout) — skip
        }

        return $response;
    }

    private function detectPlatform(string $ua): string
    {
        return match (true) {
            str_contains($ua, 'Windows')  => 'Windows',
            str_contains($ua, 'Macintosh') => 'macOS',
            str_contains($ua, 'Android')  => 'Android',
            str_contains($ua, 'iPhone') || str_contains($ua, 'iPad') => 'iOS',
            str_contains($ua, 'Linux')    => 'Linux',
            default                       => 'Unknown',
        };
    }

    private function detectDevice(string $ua): string
    {
        if (str_contains($ua, 'Tablet') || str_contains($ua, 'iPad')) {
            return 'Tablet';
        }
        if (str_contains($ua, 'Mobile') || str_contains($ua, 'iPhone') || str_contains($ua, 'Android')) {
            return 'Mobile';
        }
        return 'Desktop';
    }
}
