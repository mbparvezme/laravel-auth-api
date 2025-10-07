<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AppController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PasswordController;
use App\Http\Controllers\ProfileController;

// Public Routes
Route::group(['middleware' => ['throttle:5,1']], function () {
    Route::post('register', [AuthController::class, 'registration']);
    Route::post('login',    [AuthController::class, 'login']);
    
    Route::get('verify-email/{id}/{hash}',  [AuthController::class, 'verify'])->middleware('signed')->name("verification.verify");
    Route::get('verify-new-email',          [ProfileController::class, 'verifyNewEmail'])->name('new.email.verify');

    Route::post('password/forgot',          [PasswordController::class, 'requestPasswordReset'])->name('password.reset.request');
    Route::post('password/reset/{token}',   [PasswordController::class, 'resetPassword'])->name('password.reset');
});

// Auth Routes, accessible without verification
Route::group(['middleware' => ['auth:sanctum', 'throttle:3,1']], function () {
    Route::post('resend-verification-email',    [AuthController::class, 'resendVerificationEmail'])->name('verification.resend');
    Route::post('logout',                       [AuthController::class, 'logout']);
    Route::post('logout-all',                   [AuthController::class, 'logoutAll']);

    Route::group(['middleware' => ['verified']], function () {
        Route::get('dashboard',         [AppController::class, 'dashboard']);
        Route::get('active-devices',    [AuthController::class, 'activeDevices']);
        Route::get('api-token',         [AuthController::class, 'activeDevices']);

        Route::prefix('account')->group(function () {
            Route::get('/',             [ProfileController::class, 'index']);
            Route::patch('/email',      [ProfileController::class, 'updateEmail']);
            Route::post('password',     [PasswordController::class, 'updatePassword']);
            Route::patch('{status}',    [ProfileController::class, 'accountStatus']);
        });
    });
});

Route::group(['middleware' => ['throttle:500,1', 'apikey.auth']], function () {

});

// Fallback route
Route::fallback(function () {
    return response()->json(['message' => 'Not Found.'], 404);
});
