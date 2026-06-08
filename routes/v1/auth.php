<?php

use App\Http\Controllers\Auth\EmailVerificationController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\RegisterController;
use Illuminate\Support\Facades\Route;

Route::middleware('throttle:5,1')->group(function () {
    Route::post('register',        RegisterController::class);
    Route::post('login',           LoginController::class);
    Route::post('password/forgot', [PasswordController::class, 'forgot']);
    Route::post('password/reset',  [PasswordController::class, 'reset']);
});

Route::get('verify-email/{id}/{hash}', [EmailVerificationController::class, 'verify'])
    ->middleware('signed')
    ->name('verification.verify');

Route::middleware(['auth:sanctum', 'check.user.status', 'throttle:60,1'])->group(function () {
    Route::post('logout',              [LogoutController::class, 'logout']);
    Route::post('logout-all',          [LogoutController::class, 'logoutAll']);
    Route::post('resend-verification', [EmailVerificationController::class, 'resend']);
});
