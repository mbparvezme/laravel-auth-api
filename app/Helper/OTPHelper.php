<?php

namespace App\Helper;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Models\User;
use App\Mail\PasswordResetOTP;
use App\Helper\SMSHelper;

class OTPHelper
{
  protected $OTP;
  protected $action;
  protected $user;
  protected $userID;
  protected $token;
  protected $otpExpired;

  public function otp(string $action = "PASSWORD_RESET", int $len = null){
    $otpLen = ($len == null || $len < 4 || $len > 8) ? env('OTP_NUMBER_LENGTH', 6) : $len;

    $this->OTP = $this->genOtp($otpLen);
    $this->action = $action;
    $this->token = (string) Str::uuid();
    $this->otpExpired = '+'.env('OTP_EXPIRE_TIME', 300).' seconds';
    return $this;
  }

  protected function genOtp(int $len, bool $alpha = false){
    $n = (int) str_repeat("9", $len);
    if(!$alpha){
        return str_pad(mt_rand(0000, $n), $len, '0', STR_PAD_LEFT);
    }
  }

  public function send(User $user, string $login_id){
    $this->user = $user;
    $this->userID = $login_id;
    if($this->saveOTP() !== FALSE){
        return is_numeric($this->userID) ? $this->sendSMS() : $this->sendEmail();
    }
    return ['result' => FALSE, 'message' => __('customMessage.ERROR_COMMON'), 'otp' => $this->OTP];
  }

  protected function saveOTP(){
    return \App\Models\Otp::create([
      'user' => $this->user->id,
      'action' => $this->action,
      'sent_to' => $this->userID,
      'otp' => $this->OTP,
      'expired_at' => date('Y-m-d H:i:s', strtotime($this->otpExpired)),
      'token' => $this->token,
    ]);
  }

  protected function sendSMS(){
    $SMS = [
        'REGISTER' => __('OTPHelper.REGISTER_COMMON'),
        'PASSWORD_RESET' => __('OTPHelper.PASSWORD_RESET_COMMON'),
        'MOBILE_VERIFICATION' => __('OTPHelper.MOBILE_VERIFICATION_COMMON'),
    ];

      $text = env('APP_NAME') . $SMS[$this->action] . $this->OTP;
    try{
      //   (new SMSHelper)->sms($text)->to($this->user->country_code.$this->userID)->send();
      //   return ['result' => TRUE, 'token' => $this->token];
      return ['result' => TRUE, 'token' => $this->token, 'otp' => $this->OTP];
    }catch(\Exception $e){
      return FALSE;
    }
  }

  protected function sendEmail(){
    $mailData = ["name" => $this->user->name, "otp" => $this->OTP];
    if($this->action == "PASSWORD_RESET"){
        $mailData["expired_at"] = date('Y-m-d H:i:s', strtotime($this->otpExpired));
        $emailFormat = new PasswordResetOTP($mailData);
    }
    try{
        //   Mail::to($this->userID)->send($emailFormat);
        return ['result' => TRUE, 'token' => $this->token, 'otp' => $this->OTP];
    }catch(\Exception $e){
        return FALSE;
    }
  }

  public function check(int $otp, string $token){
    $otp = \App\Models\Otp::where('otp', $otp)->first();

    if(!$otp){
      return ['result' => FALSE, 'message' => __('OTPHelper.OTP_INVALID')];
   	}

    if($otp->token != $token){
      return ['result' => FALSE, 'message' => __('OTPHelper.OTP_INVALID_REQUEST')];
   	}

    if(time() - strtotime($otp->expired_at) > env('OTP_EXPIRE_TIME', 300)){
      return ['result' => FALSE, 'message' => __('OTPHelper.OTP_EXPIRED')];
    }

    return ['result' => TRUE, 'user' => $otp->user];
  }

  public function markUsed(int $user, int $otp){
    $otp = \App\Models\Otp::where(['user' => $user, 'otp' => $otp])->first();
    $otp->token = NULL;
    $otp->save();
  }

}
