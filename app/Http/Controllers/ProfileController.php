<?php

namespace App\Http\Controllers;

use App\Services\PasswordService;
use App\Models\User;
use App\Traits\AppTrait;
use App\Http\Resources\UserResource;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    use AppTrait;
    protected PasswordService $passwordService;

    private static array $logKey = [
        'email_update_pass_err' => 'EMAIL_UPDATE_PASS_ERR',
        'email_update_verification_link_sent' => 'NEW_EMAIL_VERIFICATION_SENT',
        'email_update_invalid_token' => 'NEW_EMAIL_INVALID_TOKEN',
        'email_update_invalid_user' => 'EMAIL_UPDATE_INVALID_USER',
        'email_update_not_pending' => 'EMAIL_UPDATE_NOT_PENDING',
        'new_email_verified' => 'NEW_EMAIL_VERIFIED',
        'account_status' => 'ACCOUNT_STATUS',
    ];

    private static array $statuses = [
        "active" => 1,
        "reactive" => 1,
        "inactive" => 0,
        "delete" => -1,
        "block" => -2,
    ];

    public function index(){
        return $this->apiResponse(success: true, message: __('app.USER_INFO'), data: 
            new UserResource(auth()->user()->load('profile'))
        );
    }

    public function updateEmail(Request $request)
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'email'    => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'password' => ['required'],
        ]);

        if ($validator->fails()) {
            return $this->apiResponse(success: false, message: $validator->errors(), code: 422);
        }

        if (!Hash::check($request->password, $user->password)) {
            $this->addLog(action: self::$logKey['email_update_pass_err'], data: $request->all(), user: $user->id);
            return $this->apiResponse(success: false, message: __('app.EMAIL_UPDATE_PASS_ERR'), code: 400);
        }

        $user->profile->pending_email = $request->email;
        $user->profile->save();

        $verificationUrl = URL::temporarySignedRoute(
            'new.email.verify', Carbon::now()->addMinutes(60), ['user' => $user->id]
        );

        Mail::raw("Click to verify your new email: $verificationUrl", function ($m) use ($request) {
            $m->to($request->email)->subject('Verify your new email');
        });

        $this->addLog(action: self::$logKey['email_update_verification_link_sent'], data: ['old' => $user->email, 'new' => $request->email], user: $user->id);
        return $this->apiResponse(success: true, message: __('app.EMAIL_UPDATE_VERIFICATION_EMAIL_SENT'));
    }

    public function verifyNewEmail(Request $request)
    {
        if (!$request->hasValidSignature()) {
            $this->addLog(action: self::$logKey['email_update_invalid_token'], data: $request->all());
            return $this->apiResponse(success: false, message: __('app.EMAIL_UPDATE_INVALID_TOKEN'), code: 403);
        }

        $user = User::findOrFail($request->user);

        if (!$user?->profile) {
            $this->addLog(action: self::$logKey['email_update_invalid_user'], data: $request->all());
            return $this->apiResponse(success: false, message: __('app.EMAIL_UPDATE_INVALID_USER'), code: 404);
        }

        if (!$user->profile->pending_email) {
            $this->addLog(action: self::$logKey['email_update_not_pending'], data: $request->all());
            return $this->apiResponse(success: false, message: __('app.EMAIL_UPDATE_INVALID_PENDING'), code: 404);
        }

        DB::transaction(function() use ($user) {
            $user->email = $user->profile->pending_email;
            $user->email_verified_at = now();
            $user->save();

            $user->profile->pending_email = null;
            $user->profile->save();
        });

        $this->addLog(action: self::$logKey['new_email_verified'], data: $request->all());
        return $this->apiResponse(success: true, message: __('app.NEW_EMAIL_VERIFIED'), code: 200);
    }

    public function accountStatus($status){
        if (!array_key_exists($status, self::$statuses)) {
            return $this->apiResponse(success: false, code: 404);
        }

        $user = auth()->user();
        $data = [
            'previous' => $key = array_search($user->status, self::$statuses),
            'new' => $status ?? "UNKNOWN",
        ];

        $user->status = (string) self::$statuses[$status] ?? $user->status;
        $user->save();

        $this->addLog(action: self::$logKey['account_status'], data: $data, user: $user->id);
        return $this->apiResponse(success: true, message: __('app.'. strtoupper($status)), code: 200);

    }

}
