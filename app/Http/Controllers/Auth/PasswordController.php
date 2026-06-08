<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Http\Requests\Auth\UpdatePasswordRequest;
use App\Services\AppLogService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

class PasswordController extends Controller
{
    use ApiResponse;

    public function __construct(private AppLogService $log) {}

    public function forgot(ForgotPasswordRequest $request): \Illuminate\Http\JsonResponse
    {
        Password::sendResetLink($request->only('email'));
        return $this->success(__('app.PASSWORD_RESET_SENT'));
    }

    public function reset(ResetPasswordRequest $request): \Illuminate\Http\JsonResponse
    {
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill(['password' => $password])->save();
                $user->tokens()->delete();
                $this->log->log('PASSWORD_RESET_OK', $user->id);
            }
        );

        if ($status !== Password::PASSWORD_RESET) {
            return $this->error(__('app.PASSWORD_RESET_FAILED'), code: 422);
        }

        return $this->success(__('app.PASSWORD_RESET_OK'));
    }

    public function update(UpdatePasswordRequest $request): \Illuminate\Http\JsonResponse
    {
        $user = $request->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return $this->error(__('app.INVALID_CURRENT_PASSWORD'), code: 403);
        }

        $user->update(['password' => $request->password]);
        $this->log->log('PASSWORD_UPDATED', $user->id);

        return $this->success(__('app.PASSWORD_UPDATED'));
    }
}
