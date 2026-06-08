<?php

namespace App\Http\Controllers;

use App\Models\AppLog;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class AppLogController extends Controller
{
    use ApiResponse;

    public function index(Request $request): \Illuminate\Http\JsonResponse
    {
        $logs = AppLog::where('user_id', $request->user()->id)
            ->where('user_visible', true)
            ->latest()
            ->paginate(20);

        return $this->success(__('app.LOGS_FETCHED'), $logs);
    }
}
