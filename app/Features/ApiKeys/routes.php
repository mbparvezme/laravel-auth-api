<?php

use App\Features\ApiKeys\Controllers\ApiKeyController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'verified', 'check.user.status', 'throttle:60,1'])->group(function () {
    Route::get('api-keys', [ApiKeyController::class, 'index']);
    Route::post('api-keys', [ApiKeyController::class, 'store']);
    Route::delete('api-keys/{id}', [ApiKeyController::class, 'destroy']);
});
