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
        $apiKey = null;
        $apiSecret = null;

        // Try to read from Authorization header: "Authorization: ApiKey key:secret"
        if ($authHeader = $request->header('Authorization')) {
            if (preg_match('/^api\s+(\S+):(\S+)$/', $authHeader, $matches)) {
                $apiKey = $matches[1];
                $apiSecret = $matches[2];
            }
        }

        // Fallback to X-API headers
        if (!$apiKey || !$apiSecret) {
            $apiKey = $apiKey ?? $request->header('X-API-KEY');
            $apiSecret = $apiSecret ?? $request->header('X-API-SECRET');
        }

        // Validate
        $keyRecord = ApiKey::where('key', $apiKey)->first();

        if (
            !$keyRecord ||
            !hash_equals($keyRecord->secret, hash('sha256', $apiSecret)) ||
            ($keyRecord->expires_at && now()->greaterThan($keyRecord->expires_at))
        ) {
            return response()->json(['success' => false, 'message' => __('app.API_INVALID_CREDENTIAL')], 401);
        }

        // Optionally attach user
        $request->setUserResolver(fn() => $keyRecord->user);
        return $next($request);
    }
}
