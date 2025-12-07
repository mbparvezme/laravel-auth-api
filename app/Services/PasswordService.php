<?php

namespace App\Services;

use App\Models\User;
use App\Traits\AppTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
// ====
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Auth\Events\PasswordReset;

class PasswordService{

    use AppTrait;

    private static array $logKey = [
        'reset_user_err' => 'PASS_RESET_USER_ERR',
        'reset_link_sent' => 'PASS_RESET_LINK_SENT',
        'reset_link_err' => 'RESET_LINK_ERR',
        'pass_reset_ok' => 'PASS_RESET_OK',
        'pass_reset_err' => 'PASS_RESET_ERR',
        'pass_update_err' => 'PASS_UPDATE_ERR',
        'pass_update_ok' => 'PASS_UPDATE_OK',
        'tempered_link' => 'TEMPERED_PASS_RESET_REQUEST',
    ];

    public function requestPasswordReset(Request $request)
    {
        $request->validate(['email' => 'required|email']);
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            $this->addLog(action: self::$logKey['reset_user_err'], data: ['email' => $request->email]);
            return $this->apiResponse(success: false, message: __('app.PASS_RESET_MSG'), code: 404);
        }

        if ($user->status == -1) {
          return $this->blockedUser();
        }

        $status = Password::sendResetLink($request->only('email'));

        if ($status === Password::RESET_LINK_SENT) {
          $this->addLog(action: self::$logKey['reset_link_sent'], data: ['email' => $request->email]);
          return $this->apiResponse(success: true, message: __('app.PASS_RESET_MSG'));
        }

        $this->addLog(action: self::$logKey['reset_link_err'], data: ['email' => $request->email]);
        return $this->apiResponse(success: false, message: __('app.PASS_RESET_MSG'), code: 400);
        // ======================================
        // Test code
        // $user = User::where('email', $request->email)->first();
        // $token = Password::createToken($user);
        // $resetUrl = env('FRONTEND_URL', 'http://localhost:3000'). '/reset-password/' . $token . '?email=' . urlencode($user->email);
        // return $this->apiResponse(true, 'Password reset link generated', ['reset_url' => $resetUrl]);
    }


    public function resetPassword(Request $request, $token)
    {
        $request->validate([
          'token'    => 'required',
          'email'    => 'required|email',
          'password' => 'required|string|min:8|confirmed',
        ]);

        if(strtok($token, '&') !== $request->token){
            $this->addLog(action: self::$logKey['tempered_link'], data: ['email' => $request->email]);
            return $this->apiResponse(success: false, message: __('app.RESET_PASS_ERR'), code:  400);
        }

        $status = Password::reset(
          $request->only('email', 'password', 'password_confirmation', 'token'),
          function (User $user, string $password) {
            $user->forceFill(['password' => Hash::make($password), 'remember_token' => Str::random(60)])->save();
            event(new PasswordReset($user));
          }
        );

        if ($status === Password::PASSWORD_RESET) {
          $user = User::where('email', $request->email)->first();
          $user->tokens()->delete();
          $this->addLog(action: self::$logKey['pass_reset_ok'], data: ['email' => $request->email]);
          return $this->apiResponse(success: true, message: __('app.RESET_PASS_OK'));
        }

        $this->addLog(action: self::$logKey['pass_reset_err'], data: ['email' => $request->email]);
        return $this->apiResponse(success: false, message: __('app.RESET_PASS_ERR'), code:  400);
    }


    public function updatePassword(Request $request)
    {
      $request->validate(['current_password' => 'required|string', 'new_password' => 'required|string|min:8|confirmed']);
      $user = Auth::user();

      if (!$user) {
        return $this->apiResponse(success: false, message: __('app.UNAUTH_PASS_UPDATE'), code: 401);
      }

      if (!Hash::check($request->current_password, $user->password)) {
        $this->addLog(action: self::$logKey['pass_update_err'], user: $user->id);
        return $this->apiResponse(success: false, message: __('app.PASS_UPDATE_CURRENT_PASS_ERR'), code: 401);
      }

      $user->password = Hash::make($request->new_password);
      $user->save();
      $user->tokens()->delete();

      $this->addLog(action: self::$logKey['pass_update_ok'], user: $user->id);
      return $this->apiResponse(success: true, message: __('app.PASS_UPDATE'));
    }

}