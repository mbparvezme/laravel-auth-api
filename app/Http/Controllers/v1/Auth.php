<?php

namespace App\Http\Controllers\v1;

use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User as UserModel;

class Auth extends Controller
{

  public function login(Request $request){
    try{
        if(!$this->isValidRequest($request)){
            return response()->json($this->result(res: FALSE, msg: __('auth.INVALID_LOGIN')), 401);
        }

        $user = UserModel::where( $this->getUserIdType($request->userID), $request->userID)->first();

        if(!$user) {
            $this->addLog(action:'USER_LOGIN_ERR_ID', data: ['user'=>$request->userID]);
            return response()->json($this->result(res: FALSE, msg: __('auth.INVALID_LOGIN')),401);
        }

        if(!$this->isPasswordValid($request, $user->password)) {
            return response()->json($this->result(res: FALSE, msg: __('auth.PASSWORD_ERROR')),401);
        }

        $this->addLog(action:'USER_LOGIN', user: $user->id);
        $token = $user->createToken('authToken')->plainTextToken;

        return response()->json(['user'=>$user,'token'=>$token,'success'=>true,'error'=>false], 201);
    } catch (\Throwable $th) {
        return $this->throwError($th);
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

    private function isValidRequest(Request $request) : bool{
        if($request->userID == "" || $request->password == ""){
            $this->addLog(action:'AUTH_VALIDATION_ERR', data: ['user'=>$request->userID??""]);
            return FALSE;
        }
        return TRUE;
    }

    private function isPasswordValid(Request $request, String $password): bool{
        if(!Hash::check($request->password, $password)) {
            $this->addLog(action:'USER_LOGIN_ERR_PASSWORD', data: ['user'=>$request->userID]);
            return FALSE;
        }
        return TRUE;
    }

}
