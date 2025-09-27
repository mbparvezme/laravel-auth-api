<?php

namespace App\Services;

use App\Http\Requests\RegistrationRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Traits\AppTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Jenssegers\Agent\Agent;

class AuthService{

    use AppTrait;
    protected $user;

    public function login(Request $request)
    {

        $request->validate(['userid' => 'required|string', 'password' => 'required|string']);
        $this->user = User::where('email', $request->userid)->first();

        if (!$this->user) {
            $this->addLog(action:'ERR_LOGIN_ID', data: ['userid' => $request->userid]);
            return $this->apiResponse(success: false, message: __('app.INVALID_LOGIN'));
        }

        if (!Hash::check($request->password, $this->user->password)) {
            $this->addLog(action:'ERR_LOGIN_PASS', data: ['user' => $request->userid]);
            return $this->apiResponse(success: false, message: __('app.INVALID_LOGIN'));
        }

        $this->addLog(action:'USER_LOGIN', user: $this->user->id);
        $token = $this->user->createToken('authToken')->plainTextToken;
        $this->updateTokenAttributes($request);

        return $this->apiResponse(success: true, message: __('app.INVALID_LOGIN'), data: [
            'user' => new UserResource($this->user),
            'token' => $token,
        ]);
    }

    public function register(RegistrationRequest $request)
    {
        try {
            $validated = $request->validated();

            $user = User::create($validated);
            $this->addLog(action: 'USER_REGISTRATION', user: $user->id);

            $token = $user->createToken('authToken')->plainTextToken;
            $this->updateTokenAttributes($request);

            // Send verification email (optional, implement in service/job)
            // Mail::to($user->email)->send(new VerifyEmail($user));

            return $this->apiResponse(success: true, message: __('app.ACCOUNT_CREATED'), 
                data: [
                    'user'  => new UserResource($user),
                    'token' => $token,
                ],
                code: 201
            );
        } catch (\Throwable $th) {
            return $this->apiResponse(
                false,
                __('app.ERROR_COMMON'),
                null,
                ['exception' => $th->getMessage()],
                500
            );
        }
    }


    private function updateTokenAttributes($request): void{
        $accessToken = $this->user->tokens()->latest()->first();

        if ($accessToken) {
            $agent = new Agent();
            $accessToken->update([
                'attributes' => [
                    'device_name' => $request->header('User-Agent'),
                    'ip_address'  => $request->ip(),
                    'browser'     => $agent->browser(),
                    'platform'    => $agent->platform(),
                ]
            ]);
        }
    }

    public function logout(Request $request){
        try{
            $this->addLog(action:'USER_AUTH_END', user: auth()->user()->id);
            $request->user()->tokens()->delete();
            return response()->json($this->result(msg: __('auth.LOGGED_OUT')), 200);
        } catch (\Throwable $th) {
            return $this->throwError($th);
        }
    }

}

