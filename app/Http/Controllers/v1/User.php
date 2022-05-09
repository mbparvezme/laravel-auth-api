<?php

namespace App\Http\Controllers\v1;

use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\RegistrationRequest;
use App\Models\User as UserModel;
use App\Models\Otp;
use App\Helper\OTPHelper;

class User extends Controller
{

  public function create(RegistrationRequest $request){
    $user = UserModel::create($request->validated());
    $token = $user->createToken('authToken')->plainTextToken;
    // event(new Registered($user));
    $this->addLog(action: 'USER_REGISTRATION', user: $user->id);
    return response()->json(['user'=>$user,'token'=>$token,'success'=>true,'message'=>'Account created successfully' ], 201);
  }

  public function confirmEmail(string $token){
    
    // event(new Registered($user));
    $this->addLog(action: 'USER_REGISTRATION', user: $user->id);
    return response()->json(['user'=>$user,'token'=>$token,'success'=>true,'message'=>'Account created successfully' ], 201);
  }

  public function login(Request $request){
    if($request->userID == "" || $request->password == ""){
      $this->addLog(action:'AUTH_VALIDATION_ERR', data: ['user'=>$request->userID??""]);
      return response()->json($this->result(res: FALSE, msg: 'Invalid login details'), 401);
    }

    $user = UserModel::where( $this->getUserIdType($request->userID), $request->userID)->first();

    if(!$user || !Hash::check($request->password, $user->password)) {
      $action = !$user ? '_ID_ERR' : '_PASS_ERR';
      $this->addLog(action:'USER_AUTH'.$action, data: ['user'=>$request->userID]);
      return response()->json($this->result(res: FALSE, msg: 'Invalid user ID or password'),401);
    }

    $this->addLog(action:'USER_AUTH', user: $user->id);
    $token = $user->createToken('authToken')->plainTextToken;

    return response()->json(['user'=>$user,'token'=>$token,'success'=>true,'error'=>false], 201);
  }

  public function logout(Request $request){
    $this->addLog(action:'USER_AUTH_END', user: auth()->user()->id);
    $request->user()->tokens()->delete();
    return response()->json($this->result(msg: 'Logged out successfully!'), 200);
  }

  public function requestPasswordReset(Request $request){
    if($request->userID == ""){
      $this->addLog(action:'NULL_USER_ID_ERR', data: ['user'=>$request->userID??""]);
      return response()->json($this->result(res: FALSE, msg: 'Enter user ID!'), 401);
    }

    $userIDType = $this->getUserIdType($request->userID);
    $user = UserModel::where($userIDType, $request->userID)->first();
    if(!$user) {
      $this->addLog(action:'RESET_USER_ID_ERR', data: ['user'=>$request->userID]);
      return response()->json($this->result(res: FALSE, msg: 'Invalid user ID!'),401);
    }

    // Count recovery request
    $passwordRequestLimit = $this->checkPasswordRequestLimit($user->id);
    if($passwordRequestLimit !== "OK"){
      return response()->json($this->result(res: FALSE, msg: $passwordRequestLimit),401);
    }

    $data = (new OTPHelper)->otp()->send($user, $request->userID);
    if($data['result'] !== FALSE){
      return response()->json($this->result(msg: 'Please check your ' . ucfirst($userIDType). ' and use to code to reset password.', data: ['token' => $data['token']]), 200);
    }else{
      return response()->json($this->result(res: FALSE), 200);
    }
  }

  public function resetPassword(Request $request){
    $this->validate($request, [
      'otp' => 'required',
      'password' => 'required',
      'password_confirmation' => 'required|same:password',
      'token' => 'required',
    ]);

    $data = (new OTPHelper)->check($request->otp, $request->token);
    if($data['result'] === FALSE){
      return response()->json($this->result(res: FALSE, msg: $data['message']), 200);
    }

    $user = UserModel::find($data['user']);
    $user->password = $request->password;
    $user->save();

    $data = (new OTPHelper)->markUsed($data['user'], $request->otp);
    return response()->json($this->result(msg: 'Password updated successfully! Login to your account'), 200);
  }

  private function getUserIdType($userId){
    return is_numeric($userId) ? 'mobile' : 'email';
  }

  private function checkPasswordRequestLimit($userId){
    $filter = [
      'user' => $userId,
      'action' => 'PASSWORD_RESET'
    ];

    $thirty_days_ago = date('Y-m-d', strtotime("-31 days"));
    $monthlyOTPs =  Otp::where($filter)->whereDate('created_at', ">=", $thirty_days_ago)->count();
    if($monthlyOTPs >= 15){
      return "Too many password reset requests. Try again after two or three days!";
    }

    $seven_days_ago = date('Y-m-d', strtotime("-8 days"));
    $weeklyOTPs  =  Otp::where($filter)->whereDate('created_at', ">=", $seven_days_ago)->count();
    if($weeklyOTPs >= 6){
      return "Too many password reset requests. Try again tomorrow or after tomorrow!";
    }

    $dailyOTPs  = Otp::where($filter)->whereDate('created_at', "=", date('Y-m-d'))->count();
    if($dailyOTPs >= 3){
      return "Too many password reset requests. Try again tomorrow!";
    }

    return "OK";

  }

}