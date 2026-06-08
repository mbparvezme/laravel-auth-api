<?php

namespace App\Features\SocialAuth\Controllers;

use App\Enums\UserStatus;
use App\Features\SocialAuth\Models\SocialAccount;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\AppLogService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends Controller
{
    use ApiResponse;

    private const VALID_PROVIDERS = ['google', 'facebook'];

    public function __construct(private AppLogService $log) {}

    public function redirect(string $provider): JsonResponse
    {
        if (!in_array($provider, self::VALID_PROVIDERS)) {
            return $this->error(__('app.SOCIAL_INVALID_PROVIDER'), [], 422);
        }

        $url = Socialite::driver($provider)->stateless()->redirect()->getTargetUrl();

        return $this->success(__('app.SOCIAL_REDIRECT'), ['url' => $url]);
    }

    public function callback(string $provider): JsonResponse
    {
        if (!in_array($provider, self::VALID_PROVIDERS)) {
            return $this->error(__('app.SOCIAL_INVALID_PROVIDER'), [], 422);
        }

        try {
            $socialUser = Socialite::driver($provider)->stateless()->user();
        } catch (\Exception) {
            return $this->error(__('app.SOCIAL_AUTH_FAILED'), [], 422);
        }

        $isNewUser = false;

        $account = SocialAccount::where('provider', $provider)
            ->where('provider_id', $socialUser->getId())
            ->with('user')
            ->first();

        if ($account) {
            $user = $account->user;
            $account->update([
                'token'         => $socialUser->token,
                'refresh_token' => $socialUser->refreshToken,
                'expires_at'    => $socialUser->expiresIn ? now()->addSeconds($socialUser->expiresIn) : null,
            ]);
        } else {
            $user = User::where('email', $socialUser->getEmail())->first();

            if ($user) {
                if (!$user->hasVerifiedEmail()) {
                    return $this->error(__('app.SOCIAL_EMAIL_UNVERIFIED'), [], 403);
                }
                $this->linkSocialAccount($user, $provider, $socialUser);
            } else {
                $isNewUser = true;
                $user = User::create([
                    'name'              => $socialUser->getName(),
                    'email'             => $socialUser->getEmail(),
                    'email_verified_at' => now(),
                ]);
                $user->profile()->create([]);
                $this->linkSocialAccount($user, $provider, $socialUser);
            }
        }

        if ($user->status === UserStatus::Banned) {
            return $this->error(__('app.USER_BANNED'), [], 403);
        }
        if ($user->status === UserStatus::Suspended) {
            return $this->error(__('app.USER_SUSPENDED'), [], 403);
        }

        $this->log->log($isNewUser ? 'SOCIAL_REGISTER' : 'SOCIAL_LOGIN', $user->id);

        $token = $user->createToken('social-auth')->plainTextToken;
        $user->load('profile');

        return $this->success(__('app.USER_LOGIN'), [
            'user'  => new UserResource($user),
            'token' => $token,
        ]);
    }

    private function linkSocialAccount(User $user, string $provider, mixed $socialUser): void
    {
        SocialAccount::create([
            'user_id'       => $user->id,
            'provider'      => $provider,
            'provider_id'   => $socialUser->getId(),
            'token'         => $socialUser->token,
            'refresh_token' => $socialUser->refreshToken,
            'expires_at'    => $socialUser->expiresIn ? now()->addSeconds($socialUser->expiresIn) : null,
        ]);
    }
}
