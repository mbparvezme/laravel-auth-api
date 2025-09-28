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
        // Send password reset instructions
        return $this->passwordService->requestPasswordReset($request);
    }

    public function resetPassword(Request $request){
        // Reset the password
        return $this->passwordService->resetPassword($request);
    }

    public function changePasswordByUser(Request $request){
        // Change password for authenticated user
        return $this->passwordService->updatePassword($request);
    }

}
