<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\UpdateEmailRequest;
use App\Http\Requests\Auth\UpdateProfileRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Notifications\VerifyNewEmailNotification;
use App\Services\AppLogService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    use ApiResponse;

    public function __construct(private AppLogService $log) {}

    public function show(Request $request): \Illuminate\Http\JsonResponse
    {
        $user = $request->user()->load('profile');
        return $this->success(__('app.PROFILE_FETCHED'), new UserResource($user));
    }

    public function update(UpdateProfileRequest $request): \Illuminate\Http\JsonResponse
    {
        $request->user()->profile()->updateOrCreate(
            ['user_id' => $request->user()->id],
            $request->validated()
        );

        $this->log->log('PROFILE_UPDATED', $request->user()->id);
        return $this->success(__('app.PROFILE_UPDATED'));
    }

    public function updateEmail(UpdateEmailRequest $request): \Illuminate\Http\JsonResponse
    {
        $user = $request->user();

        if (!Hash::check($request->password, $user->password)) {
            return $this->error(__('app.INVALID_CURRENT_PASSWORD'), code: 403);
        }

        $user->notify(new VerifyNewEmailNotification($request->email));
        $this->log->log('EMAIL_UPDATE_REQUESTED', $user->id, ['new_email' => $request->email]);

        return $this->success(__('app.EMAIL_UPDATE_REQUESTED'));
    }

    public function verifyNewEmail(Request $request): \Illuminate\Http\JsonResponse
    {
        if (!$request->hasValidSignature()) {
            return $this->error(__('app.INVALID_VERIFICATION_LINK'), code: 403);
        }

        $user = User::findOrFail($request->query('id'));
        $user->update([
            'email'             => $request->query('email'),
            'email_verified_at' => now(),
        ]);

        $this->log->log('EMAIL_UPDATED', $user->id);
        return $this->success(__('app.EMAIL_UPDATED'));
    }

    public function updateStatus(Request $request, string $status): \Illuminate\Http\JsonResponse
    {
        if (!in_array($status, ['active', 'inactive'])) {
            return $this->error(__('app.INVALID_STATUS'), code: 422);
        }

        $request->user()->update(['status' => $status]);
        $this->log->log('STATUS_UPDATED', $request->user()->id, ['status' => $status]);

        return $this->success(__('app.STATUS_UPDATED'));
    }
}
