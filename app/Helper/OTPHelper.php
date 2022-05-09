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

  private $SMS = [
    'REGISTER' => ' account verification code is: ',
    'PASSWORD_RESET' => ' password reset code is: ',
    'MOBILE_VERIFICATION' => ' mobile verification code is: ',
  ];

  public function otp(int $len = 6, string $action = "PASSWORD_RESET"){
    if($len < 4){
      return FALSE;
    }

    $this->OTP = str_pad(mt_rand(1000, 999999), $len, '0', STR_PAD_LEFT);
    $this->action = $action;
    $this->token = (string) Str::uuid();
    return $this;
  }

  public function send(User $user, string $id){
    $this->user = $user;
    $this->userID = $id;
    if($this->saveOTP()){
      if(is_numeric($this->userID)){
        return $this->sendSMS();
      }else{
        return $this->sendEmail();
      }
    }
    return ['result' => FALSE, 'message' => 'Something went wrong! Please try again.'];
  }

  public function check(int $otp, string $token){
    $otp = \App\Models\Otp::where('otp', $otp)->first();

    if(!$otp){
      return ['result' => FALSE, 'message' => 'Invalid OTP code!'];
   	}

    if($otp->token != $token){
      return ['result' => FALSE, 'message' => 'Invalid request!'];
   	}

    if(time() - strtotime($otp->expired_at) > 1800){
      return ['result' => FALSE, 'message' => 'OTP code has expired!'];
    }

    return ['result' => TRUE, 'user' => $otp->user];
  }

  public function markUsed(int $user, int $otp){
    $otp = \App\Models\Otp::where(['user' => $user, 'otp' => $otp])->first();
    $otp->token = NULL;
    $otp->save();
  }

  protected function sendSMS(){
    $text = env('APP_NAME') . $this->SMS[$this->action] . $this->OTP;
    try{
      (new SMSHelper)->sms($text)->to($this->user->country_code.$this->userID)->send();
      return ['result' => TRUE, 'token' => $this->token];
    }catch(\Exception $e){
      return FALSE;
    }
  }

  protected function sendEmail(){
    $mailData = ["name" => $this->user->name, "otp" => $this->OTP];
    if($this->action == "PASSWORD_RESET"){
      $mailData["expired_at"] = date('Y-m-d H:i:s', strtotime("+30 minutes"));
      $emailFormat = new PasswordResetOTP($mailData);
    }
    try{
      Mail::to($this->userID)->send($emailFormat);
      return ['result' => TRUE, 'token' => $this->token];
    }catch(\Exception $e){
      return FALSE;
    }
  }

  protected function saveOTP(){
    return \App\Models\Otp::create([
      'user' => $this->user->id,
      'action' => $this->action,
      'sent_to' => $this->userID,
      'otp' => $this->OTP,
      'expired_at' => date('Y-m-d H:i:s', strtotime("+30 minutes")),
      'token' => $this->token,
    ]);
  }

}
