<?php

use App\Features\SocialAuth\Controllers\SocialAuthController;
use Illuminate\Support\Facades\Route;

Route::middleware('throttle:5,1')->group(function () {
    Route::get('auth/{provider}/redirect', [SocialAuthController::class, 'redirect']);
    Route::get('auth/{provider}/callback', [SocialAuthController::class, 'callback']);
});
