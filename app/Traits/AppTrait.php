<?php

namespace App\Traits;
use App\Models\AppLog;
use App\Models\User;
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
        bool $success = true,
        $message = null,
        $data = null,
        array $errors = [],
        int $code = 200
    ){
        $m = $message ?? ($success ? __('app.SUCCESS_COMMON') : __('app.ERROR_COMMON'));
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

    protected function checkUserStatus(?User $user)
    {
        if (!$user) {
            return null;
        }

        if ($user->status == -1) {
            return $this->apiResponse(success: false, message: __('app.USER_BLOCKED'), code: 403);
        }

        return null;
    }

}
