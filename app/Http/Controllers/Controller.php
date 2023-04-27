<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function result(bool $res = true, String $msg = "", $data = false)
    {
        $result['success']         = $res;
        $result['message']         = ($msg == "" ? ($res ? __('customMessage.SUCCESS_COMMON') : __('customMessage.ERROR_COMMON')) : $msg);
        if($data) $result['data']  = $data;
        return $result;
    }

    public function addLog(string $action, array $data = [], int|bool $user = FALSE)
    {
        $log            = new \App\Models\Log();
        $request        = new \Illuminate\Http\Request();
        $log->user      = $user ?? auth()->user()->id ?? 0;
        $log->action    = $action;
        $log->data      = json_encode(array_merge($data, ['IP' => $request->ip(), 'MAC' => exec('getmac')]));
        $log->save();
    }

    public function throwError(\Throwable $th){
        return response()->json([
            'status' => false,
            'message' => $th->getMessage()
        ], 500);
    }

    public function getUserIdType($userId){
        return is_numeric($userId) ? 'mobile' : 'email';
    }

}
