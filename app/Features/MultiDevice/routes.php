<?php

use App\Features\MultiDevice\Controllers\DeviceController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'verified', 'check.user.status', 'throttle:60,1'])->group(function () {
    Route::get('devices', [DeviceController::class, 'index']);
    Route::delete('devices/logout-others', [DeviceController::class, 'logoutOthers']);
    Route::delete('devices/{id}', [DeviceController::class, 'destroy']);
});
