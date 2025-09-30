<?php

namespace App\Traits;
use App\Models\AppLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Jenssegers\Agent\Agent; 

trait AppTrait
{

    public function addLog(string $action, array $data = [], int|bool $user = FALSE)
    {
        $request = request();
        $userid = $user ?? auth()->user()->id ?? 0;

        AppLog::create([
            'user'      => $userid,
            'action'    => $action,
            'data'      => json_encode((array) array_merge($data, $this->userMeta($request))),
        ]);
    }

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

    public function userMeta($request)
    {
        $agent = new Agent();
        $agent->setUserAgent($request->header('User-Agent'));

        return [
            'device_name'   => $this->getDevice($agent),
            'ip_address'    => $request->ip(),
            'browser'       => $agent->browser(),
            'platform'      => $agent->platform(),
            'mac'           => $this->getMAC(),
        ];
    }

    protected function getMAC()
    {
        $mac = null;
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            try {
                $mac = exec('getmac');
            } catch (\Throwable $e) {
                $mac = null;
            }
        }
        return $mac;
    }

    protected function getDevice($agent)
    {
        $deviceName = 'Unknown';
        if ($agent->isDesktop()) {
            $deviceName = 'Desktop';
        } elseif ($agent->isTablet()) {
            $deviceName = $agent->device();
        } elseif ($agent->isMobile()) {
            $deviceName = $agent->device();
        }
        return $deviceName;
    }

}
