<?php

namespace App\Http\Controllers;

use App\Services\PasswordService;
use Illuminate\Http\Request;

class PasswordController extends Controller
{
    protected PasswordService $passwordService;

    public function __construct(PasswordService $passwordService)
    {
        $this->passwordService = $passwordService;
    }

    public function requestPasswordReset(Request $request){
        return $this->passwordService->requestPasswordReset($request);
    }

    public function resetPassword(Request $request){
        return $this->passwordService->resetPassword($request);
    }

    public function updatePassword(Request $request)
    {
        return $this->passwordService->updatePassword($request);
    }

}
