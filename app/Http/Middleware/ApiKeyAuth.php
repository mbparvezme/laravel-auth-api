<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\ApiKey;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiKeyAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $apiKey = $request->header('X-API-KEY');
        $apiSecret = $request->header('X-API-SECRET');

        if (!$apiKey || !$apiSecret) {
            return response()->json(['message' => 'API key and secret required'], 401);
        }

        $key = ApiKey::where('key', $apiKey)->first();

        if (!$key || !hash_equals($key->secret, hash('sha256', $apiSecret))) {
            return response()->json(['message' => 'Invalid API key or secret'], 401);
        }

        if ($key->expires_at && $key->expires_at->isPast()) {
            return response()->json(['message' => 'API key expired'], 401);
        }

        $request->merge(['api_user' => $key->user]);

        return $next($request);
    }
}
