<?php

namespace App\Http\Controllers\Auth;

use App\Enums\UserStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\AppLogService;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    use ApiResponse;

    public function __construct(private AppLogService $log) {}

    public function __invoke(LoginRequest $request): \Illuminate\Http\JsonResponse
    {
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            $this->log->log('ERR_LOGIN_PASS', $user?->id, ['email' => $request->email]);
            return $this->error(__('app.INVALID_LOGIN'), code: 401);
        }

        if ($user->status === UserStatus::Banned) {
            $this->log->log('ERR_USER_BANNED', $user->id);
            return $this->error(__('app.USER_BANNED'), code: 403);
        }

        if ($user->status === UserStatus::Suspended) {
            $this->log->log('ERR_USER_SUSPENDED', $user->id);
            return $this->error(__('app.USER_SUSPENDED'), code: 403);
        }

        $token = $user->createToken('auth')->plainTextToken;
        $user->load('profile');
        $this->log->log('LOGIN_OK', $user->id);

        return $this->success(__('app.USER_LOGIN'), [
            'user'  => new UserResource($user),
            'token' => $token,
        ]);
    }
}
