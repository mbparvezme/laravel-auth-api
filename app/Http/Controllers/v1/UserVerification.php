<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

class UserVerification extends Controller
{

    public function sendVerificationEmail(Request $request){
        if ($request->user()->hasVerifiedEmail()) {
            return response()->json(['success'=>true,'message'=> __('auth.ALREADY_VERIFIED') ], 201);
        }
        $request->user()->sendEmailVerificationNotification();
        $this->addLog(action: 'VERIFICATION_REQUEST_SENT', user: auth()->user()->id);
        return response()->json(['success'=>true,'message'=> __('auth.VERIFICATION_EMAIL_SEND') ], 201);
    }

    public function verify(EmailVerificationRequest $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return response()->json(['success'=>true,'message'=> __('auth.ALREADY_VERIFIED') ], 201);
        }
        if ($request->user()->markEmailAsVerified()) {
            event(new Verified($request->user()));
        }

        return response()->json(['success'=>true,'message'=> __('auth.VERIFICATION_DONE') ], 201);
    }


}
