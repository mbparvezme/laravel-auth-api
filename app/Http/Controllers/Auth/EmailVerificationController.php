<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\AppLogService;
use App\Traits\ApiResponse;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;

class EmailVerificationController extends Controller
{
    use ApiResponse;

    public function __construct(private AppLogService $log) {}

    public function verify(Request $request, string $id, string $hash): \Illuminate\Http\JsonResponse
    {
        $user = User::findOrFail($id);

        if (!$request->hasValidSignature() || !hash_equals($hash, sha1($user->email))) {
            return $this->error(__('app.INVALID_VERIFICATION_LINK'), code: 403);
        }

        if ($user->hasVerifiedEmail()) {
            return $this->success(__('app.ALREADY_VERIFIED'));
        }

        $user->markEmailAsVerified();
        event(new Verified($user));
        $this->log->log('EMAIL_VERIFIED', $user->id);

        return $this->success(__('app.EMAIL_VERIFIED'));
    }

    public function resend(Request $request): \Illuminate\Http\JsonResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return $this->success(__('app.ALREADY_VERIFIED'));
        }

        $request->user()->sendEmailVerificationNotification();
        $this->log->log('VERIFICATION_RESENT', $request->user()->id);

        return $this->success(__('app.VERIFICATION_EMAIL_SENT'));
    }
}
