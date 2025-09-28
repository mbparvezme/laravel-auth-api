<?php

namespace App\Traits;
use App\Models\AppLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

trait AppTrait
{
    // Log
    public function addLog(string $action, array $data = [], int|bool $user = FALSE)
    {
        $request = request();
        $userid = $user ?? auth()->user()->id ?? 0;

        // safely checking to get MAC (exec may fail on some servers)
        $mac = null;
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            try {
                $mac = exec('getmac');
            } catch (\Throwable $e) {
                $mac = null;
            }
        }

        AppLog::create([
            'user'      => $userid,
            'action'    => $action,
            'data'      => json_encode((array) array_merge($data, ['IP' => $request->ip(), 'MAC' => $mac])),
        ]);
    }

    // Response
    public function apiResponse(
        bool $success = false,
        $message = null,
        $data = null,
        array $errors = [],
        int $code = 200
    ){
        $m = ($message ? ($success ? __('app.SUCCESS_COMMON') : __('app.ERROR_COMMON')) : $message);
        return response()->json([
            'success' => $success,
            'message' => $m,
            'data'    => $data,
            'errors'  => $errors,
        ], $code);
    }

}
