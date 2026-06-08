<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\AppLogService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class LogoutController extends Controller
{
    use ApiResponse;

    public function __construct(private AppLogService $log) {}

    public function logout(Request $request): \Illuminate\Http\JsonResponse
    {
        $this->log->log('LOGOUT', $request->user()->id);
        $request->user()->currentAccessToken()->delete();
        return $this->success(__('app.LOGGED_OUT'));
    }

    public function logoutAll(Request $request): \Illuminate\Http\JsonResponse
    {
        $this->log->log('LOGOUT_ALL', $request->user()->id);
        $request->user()->tokens()->delete();
        return $this->success(__('app.LOGGED_OUT_ALL'));
    }
}
