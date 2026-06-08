<?php

use App\Features\MagicLink\Controllers\MagicLinkController;
use Illuminate\Support\Facades\Route;

Route::middleware('throttle:3,1')->group(function () {
    Route::post('magic-link/request', [MagicLinkController::class, 'request']);
});

Route::middleware('throttle:10,1')->group(function () {
    Route::post('magic-link/verify', [MagicLinkController::class, 'verify']);
});
