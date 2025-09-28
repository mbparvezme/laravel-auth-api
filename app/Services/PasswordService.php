<?php

namespace App\Services;

use App\Models\User;
use App\Traits\AppTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Jenssegers\Agent\Agent;
// ====
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Auth\Events\PasswordReset;

class PasswordService{

    use AppTrait;

    public function requestPasswordReset(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        // // Actual code
        // $status = Password::sendResetLink($request->only('email'));

        // if ($status === Password::RESET_LINK_SENT) {
        //   $this->addLog(action: 'FORGOT_PASSWORD', data: ['email' => $request->email]);
        //   return $this->apiResponse(true, __($status));
        // }

        // $this->addLog(action: 'FORGOT_PASSWORD_FAILED', data: ['email' => $request->email]);
        // return $this->apiResponse(false, __($status), null, [], 400);

        // ======================================
        // Test code
        $user = User::where('email', $request->email)->first();
        // Generate reset token
        $token = Password::createToken($user);

        // Normally this goes to email — but for debugging we return the link
        $resetUrl = url(route('password.reset.form', [
          'token' => $token,
          'email' => $user->email,
        ], false));

        return $this->apiResponse(true, 'Password reset link generated', [
          'reset_url' => $resetUrl
        ]);
    }


    public function resetPassword(Request $request)
    {
        $request->validate([
          'token'    => 'required',
          'email'    => 'required|email',
          'password' => 'required|string|min:8|confirmed',
        ]);

        $status = Password::reset(
          $request->only('email', 'password', 'password_confirmation', 'token'),
          function (User $user, string $password) {
            $user->forceFill([
              'password' => Hash::make($password),
              'remember_token' => Str::random(60),
            ])->save();

            event(new PasswordReset($user));
          }
        );

        if ($status === Password::PASSWORD_RESET) {
          $this->addLog(action: 'PASSWORD_RESET', data: ['email' => $request->email]);
          return $this->apiResponse(true, __($status));
        }

        $this->addLog(action: 'PASSWORD_RESET_FAILED', data: ['email' => $request->email]);
        return $this->apiResponse(false, __($status), null, [], 400);
    }


    public function updatePassword(Request $request)
    {
      $request->validate([
        'current_password' => 'required|string',
        'new_password'     => 'required|string|min:8|confirmed',
      ]);

      $user = Auth::user();

      if (!$user) {
        return $this->apiResponse(false, 'Unauthenticated', null, [], 401);
      }

      if (!Hash::check($request->current_password, $user->password)) {
        $this->addLog(action: 'PASSWORD_UPDATE_FAILED', user: $user->id);
        return $this->apiResponse(false, __('app.PASSWORD_ERROR'));
      }

      $user->password = Hash::make($request->new_password);
      $user->save();

      $this->addLog(action: 'PASSWORD_UPDATED', user: $user->id);
      return $this->apiResponse(true, __('app.PASSWORD_UPDATED'));
    }


}