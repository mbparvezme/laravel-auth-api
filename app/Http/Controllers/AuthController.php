<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegistrationRequest;
use App\Models\User;
use App\Services\AuthService;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    protected AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function registration(RegistrationRequest $request)
    {
        return $this->authService->registration($request);
    }

    public function verify(Request $request, $id, $hash)
    {
        $user = User::findOrFail($id);
        return $this->authService->verify($request, $user);
    }

    public function login(Request $request)
    {
        return $this->authService->login($request);
    }

    public function logout(Request $request)
    {
        return $this->authService->logout($request);
    }

    public function logoutAll(Request $request)
    {
        return $this->authService->logoutAll($request);
    }

    public function resendVerificationEmail(Request $request)
    {
        return $this->authService->resendVerificationEmail($request);
    }

    public function activeDevices(Request $request){
        return $this->authService->activeDevices($request);
    }
}