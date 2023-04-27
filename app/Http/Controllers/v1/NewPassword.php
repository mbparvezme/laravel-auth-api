<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Otp;
use App\Helper\OTPHelper;
use App\Models\User as UserModel;

class NewPassword extends Controller
{

    public function requestPasswordReset(Request $request){
        try{
            if(!$this->isValidUser($request)){
                return response()->json($this->result(res: FALSE, msg: __('auth.EMPTY_USER_ID')), 401);
            }

            $userIDType = $this->getUserIdType($request->userID);
            $user = UserModel::where($userIDType, $request->userID)->first();

            if(!$user) {
                $this->addLog(action:'RESET_USER_ID_ERR', data: ['user' => $request->userID]);
                return response()->json($this->result(res: FALSE, msg: __('auth.INVALID_USER_ID')),401);
            }

            // Count recovery request
            $passwordRequestLimit = $this->checkPasswordRequestLimit($user->id);
            if($passwordRequestLimit !== true){
                return response()->json($this->result(res: FALSE, msg: $passwordRequestLimit), 200);
            }

            $data = (new OTPHelper)->otp()->send($user, $request->userID);

            if($data !== FALSE){
                $data = env('APP_ENV') == 'local' ? ['token' => $data['token'], 'otp' => $data['otp']] : [];
                return response()->json($this->result(msg: 'Please check your ' . ucfirst($userIDType). ' and use to code to reset password.', data: $data), 200);
            }
            return response()->json([$data], 200);

      } catch (\Throwable $th) {
          return $this->throwError($th);
      }
    }

    public function resetPassword(Request $request){
        try {
            $this->validate($request, ['otp' => 'required', 'password' => 'required', 'password_confirmation' => 'required|same:password', 'token' => 'required']);

            $data = (new OTPHelper)->check($request->otp, $request->token);
            if($data['result'] === FALSE){
                return response()->json($this->result(res: FALSE, msg: $data['message']), 200);
            }

            $user = UserModel::find($data['user']);
            $user->password = $request->password;
            $user->save();

            $data = (new OTPHelper)->markUsed($data['user'], $request->otp);
            return response()->json($this->result(msg: __('auth.PASSWORD_UPDATED')), 200);

        } catch (\Throwable $th) {
            return $this->throwError($th);
        }
    }

    protected function isValidUser(Request $request): bool{
        if($request->userID == ""){
            $this->addLog(action:'NULL_USER_ID_ERR', data: ['user'=>$request->userID??""]);
            return FALSE;
        }
        return TRUE;
    }

    private function checkPasswordRequestLimit($userId){
      try {
          $filter = ['user' => $userId, 'action' => 'PASSWORD_RESET'];

          $thirty_days_ago = date('Y-m-d', strtotime("-31 days"));
          $monthlyOTPs =  Otp::where($filter)->whereDate('created_at', ">=", $thirty_days_ago)->count();
          if($monthlyOTPs >= env('MONTHLY_PASSWORD_RESET_LIMIT')){
              return __('auth.MONTHLY_PASSWORD_RESET_EXCEED');
          }

          $seven_days_ago = date('Y-m-d', strtotime("-8 days"));
          $weeklyOTPs  =  Otp::where($filter)->whereDate('created_at', ">=", $seven_days_ago)->count();
          if($weeklyOTPs >= env('WEEKLY_PASSWORD_RESET_LIMIT')){
              return __('auth.WEEKLY_PASSWORD_RESET_EXCEED');
          }

          $dailyOTPs  = Otp::where($filter)->whereDate('created_at', "=", date('Y-m-d'))->count();
          if($dailyOTPs >= env('DAILY_PASSWORD_RESET_LIMIT')){
              return __('auth.DAILY_PASSWORD_RESET_EXCEED');
          }

          return true;
      } catch (\Throwable $th) {
          return $this->throwError($th);
      }
    }


}
