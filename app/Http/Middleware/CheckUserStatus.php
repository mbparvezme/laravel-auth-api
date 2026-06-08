<?php

namespace App\Http\Middleware;

use App\Enums\UserStatus;
use App\Traits\ApiResponse;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckUserStatus
{
    use ApiResponse;

    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user?->status === UserStatus::Banned) {
            return $this->error(__('app.USER_BANNED'), code: 403);
        }

        if ($user?->status === UserStatus::Suspended) {
            return $this->error(__('app.USER_SUSPENDED'), code: 403);
        }

        return $next($request);
    }
}
