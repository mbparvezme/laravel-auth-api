<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\RegistrationRequest;
use Illuminate\Auth\Events\Registered;
use App\Models\User as UserModel;

class UserController extends Controller
{

    public function index()
    {

    }

    public function store(RegistrationRequest $request)
    {
        try{
            $validated = $request->validated();
            $user = UserModel::create($validated);
            $token = $user->createToken('authToken')->plainTextToken;

            $this->addLog(action: 'USER_REGISTRATION', user: $user->id);

            // // VERIFICATION
            // $verification_process = env('VERIFY_USER_BY', 'email');

            // if($verification_process == "email"){
            //     event(new Registered($user));
            //     // 1. Send email verification otp
            //     // 2. Show verification window
            // }
            // if($verification_process == "mobile"){
            //     // 1. Send mobile number verification OTP
            //     // 2. Show verification window
            // }

            return response()->json(['user'=>$user,'token'=>$token,'success'=>true,'message'=> __('auth.ACCOUNT_CREATED') ], 201);
        } catch (\Throwable $th) {
            return $this->throwError($th);
        }
    }

    public function show(string $id)
    {

    }

    public function update(Request $request, string $id)
    {

    }

    public function destroy(string $id)
    {

    }
}
