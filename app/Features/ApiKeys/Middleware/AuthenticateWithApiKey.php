<?php

namespace App\Features\ApiKeys\Middleware;

use App\Features\ApiKeys\Models\ApiKey;
use App\Traits\ApiResponse;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthenticateWithApiKey
{
    use ApiResponse;

    public function handle(Request $request, Closure $next): mixed
    {
        $rawToken = $request->header('X-API-Key');

        if (!$rawToken) {
            return $this->error(__('app.UNAUTHENTICATED'), [], 401);
        }

        $apiKey = ApiKey::findByRawToken($rawToken);

        if (!$apiKey) {
            return $this->error(__('app.UNAUTHENTICATED'), [], 401);
        }

        $apiKey->update(['last_used_at' => now()]);
        Auth::setUser($apiKey->user);

        return $next($request);
    }
}
