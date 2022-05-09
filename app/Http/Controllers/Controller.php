<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function result(bool $res = true, String $msg = "Something went wrong!", $data = false)
    {
      $ret['success']         = $res;
      $ret['message']         = $msg;
      if($data) $ret['data']  = $data;
      return $ret;
    }
  
    public function addLog(string $action, array $data = [], int|bool $user = FALSE){
      $log = new \App\Models\Log();
      $request = new \Illuminate\Http\Request();
      $log->user = $user??auth()->user()->id??0;
      $log->action = $action;
      $log->data =json_encode(array_merge($data, ['IP' => $request->ip(), 'MAC' => exec('getmac')]));
      $log->save();
    }
}
