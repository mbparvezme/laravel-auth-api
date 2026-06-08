<?php

namespace App\Features\IpRules\Middleware;

use App\Features\IpRules\Models\IpRule;
use App\Traits\ApiResponse;
use Closure;
use Illuminate\Http\Request;

class CheckIpRules
{
    use ApiResponse;

    public function handle(Request $request, Closure $next): mixed
    {
        if ($request->is('api/*') && IpRule::isBlocked($request->ip())) {
            return $this->error(__('app.IP_BLOCKED'), [], 403);
        }

        return $next($request);
    }
}
