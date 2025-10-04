<?php

namespace App\Services;

use App\Http\Requests\RegistrationRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Traits\AppTrait;

use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuthService{

    use AppTrait;
    protected $user;

    private static array $logKey = [
        'registration_success' => 'USER_REGISTRATION',
        'id_err' => 'ERR_USER_ID',
        'pass_err' => 'ERR_LOGIN_PASS',
        'login_ok' => 'LOGIN_OK',
        'logout' => 'LOGOUT',
        'logout_all' => 'LOGOUT_ALL',
        'verification_resend' => 'VERIFICATION_RESENT',
        'verified' => 'VERIFIED',
    ];

    public function registration(RegistrationRequest $request)
    {
        try {
            $validated = $request->validated();

            $this->user = User::create($validated);
            
            $token = $this->user->createToken('authToken')->plainTextToken;
            $this->updateTokenAttributes($request);
            $this->user->sendEmailVerificationNotification();
            
            $this->addLog(action: self::$logKey['registration_success'], user: $this->user->id);
            return $this->apiResponse(success: true, message: __('app.ACCOUNT_CREATED'),
                data: [
                    'user'  => new UserResource($this->user),
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

    public function login(Request $request)
    {
        $request->validate(['userid' => 'required|string', 'password' => 'required|string']);
        $this->user = User::where('email', $request->userid)->first();

        if (!$this->user) {
            $this->addLog(action: self::$logKey['id_err'], data: ['userid' => $request->userid]);
            return $this->apiResponse(success: false, message: __('app.INVALID_LOGIN'));
        }

        if (!Hash::check($request->password, $this->user->password)) {
            $this->addLog(action: self::$logKey['pass_err'], data: ['user' => $request->userid]);
            return $this->apiResponse(success: false, message: __('app.INVALID_LOGIN'));
        }

        $token = $this->user->createToken('authToken')->plainTextToken;
        $this->updateTokenAttributes($request);

        $this->addLog(action: self::$logKey['login_ok'], user: $this->user->id);
        return $this->apiResponse(success: true, message: __('app.USER_LOGIN'),
            data: [
                'user' => new UserResource($this-> user),
                'token' => $token,
            ]
        );
    }

    protected function updateTokenAttributes($request): void{
        $accessToken = $this->user->tokens()->latest()->first();
        if ($accessToken) {
            DB::table('personal_access_tokens')->where('id', $accessToken->id)
                ->update(['attributes' => json_encode($this->userMeta($request))]);
        }
    }

    public function logout(Request $request)
    {
        $this->addLog(action: self::$logKey['logout'], user: Auth::id());
        $request->user()->currentAccessToken()->delete();
        return $this->apiResponse(success: true, message: __('auth.LOGGED_OUT'), code: 201);
    }

    public function logoutAll(Request $request)
    {
        $this->addLog(action: self::$logKey['logout_all'], user: Auth::id());
        $request->user()->tokens()->delete();
        return $this->apiResponse(success: true, message: __('auth.LOGGED_OUT_ALL'), code: 201);
    }

    public function activeDevices(Request $request)
    {
        $tokens = $request->user()->tokens()->get(['id', 'name', 'abilities', 'last_used_at', 'created_at']);
        return $this->apiResponse(success: true, data: $tokens, code: 201);
    }

    // Send verification email
    public function resendVerificationEmail(Request $request)
    {
        $user = $request->user();

        if ($user->hasVerifiedEmail()) {
            return $this->apiResponse(success: true, message: __('app.ALREADY_VERIFIED'));
        }

        $request->user()->sendEmailVerificationNotification();

        $this->addLog(action: self::$logKey['verification_resend'], user: Auth::id());
        return $this->apiResponse(success: true, message: __('app.VERIFICATION_EMAIL_SEND'));
    }

    // Verify email
    public function verify($id, $hash)
    {
        $user = User::findOrFail($id);

        if (!hash_equals((string) $hash, sha1($user->email))) {
            return response()->json(['success' => false, 'message' => __('app.INVALID_VERIFICATION_LINK')], 400);
        }

        if ($user->hasVerifiedEmail()) {
            return response()->json(['success' => true, 'message' => __('app.ALREADY_VERIFIED')]);
        }

        $user->markEmailAsVerified();
        event(new Verified($user));

        $this->addLog(action: self::$logKey['verified'], user: Auth::id());
        return response()->json(['success' => true, 'message' => __('app.EMAIL_VERIFIED')]);
    }

}