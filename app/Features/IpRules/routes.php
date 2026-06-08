<?php

use App\Features\IpRules\Controllers\IpRuleController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'verified', 'check.user.status', 'throttle:60,1'])->group(function () {
    Route::get('ip-rules', [IpRuleController::class, 'index']);
    Route::post('ip-rules', [IpRuleController::class, 'store']);
    Route::delete('ip-rules/{id}', [IpRuleController::class, 'destroy']);
});
