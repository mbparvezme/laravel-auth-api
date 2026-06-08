<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\AppLogService;
use App\Traits\ApiResponse;

class RegisterController extends Controller
{
    use ApiResponse;

    public function __construct(private AppLogService $log) {}

    public function __invoke(RegisterRequest $request): \Illuminate\Http\JsonResponse
    {
        $user  = User::create($request->validated());
        $user->profile()->create([]);
        $user->sendEmailVerificationNotification();
        $token = $user->createToken('auth')->plainTextToken;

        $this->log->log('REGISTER', $user->id);

        return $this->success(__('app.ACCOUNT_CREATED'), [
            'user'  => new UserResource($user),
            'token' => $token,
        ], 201);
    }
}
