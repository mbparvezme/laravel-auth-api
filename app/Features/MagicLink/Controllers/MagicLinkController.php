<?php

namespace App\Features\MagicLink\Controllers;

use App\Enums\UserStatus;
use App\Features\MagicLink\Models\MagicLink;
use App\Features\MagicLink\Notifications\MagicLinkNotification;
use App\Features\MagicLink\Requests\RequestMagicLinkRequest;
use App\Features\MagicLink\Requests\VerifyMagicLinkRequest;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\AppLogService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

class MagicLinkController extends Controller
{
    use ApiResponse;

    public function __construct(private AppLogService $log) {}

    public function request(RequestMagicLinkRequest $request): JsonResponse
    {
        $user = User::where('email', $request->email)->first();

        if ($user && !in_array($user->status, [UserStatus::Banned, UserStatus::Suspended])) {
            [$link, $rawToken] = MagicLink::generate($request->email);
            $user->notify(new MagicLinkNotification($rawToken, $request->email));
        }

        return $this->success(__('app.MAGIC_LINK_SENT'));
    }

    public function verify(VerifyMagicLinkRequest $request): JsonResponse
    {
        $link = MagicLink::findValid($request->email, $request->token);

        if (!$link) {
            return $this->error(__('app.MAGIC_LINK_INVALID'), [], 422);
        }

        $user = User::where('email', $link->email)->first();

        if (!$user) {
            return $this->error(__('app.MAGIC_LINK_INVALID'), [], 422);
        }

        if ($user->status === UserStatus::Banned) {
            return $this->error(__('app.USER_BANNED'), [], 403);
        }

        if ($user->status === UserStatus::Suspended) {
            return $this->error(__('app.USER_SUSPENDED'), [], 403);
        }

        $link->update(['used_at' => now()]);

        $token = $user->createToken('magic-link')->plainTextToken;
        $user->load('profile');
        $this->log->log('MAGIC_LINK_LOGIN', $user->id);

        return $this->success(__('app.MAGIC_LINK_VERIFIED'), [
            'user'  => new UserResource($user),
            'token' => $token,
        ]);
    }
}
