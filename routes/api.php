<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PasswordController;

// Public Routes
Route::post('register', [AuthController::class, 'registration'])->middleware('throttle:2,1');
Route::post('login',    [AuthController::class, 'login'])->middleware('throttle:2,1');

Route::post('forgot-password',          [PasswordController::class, 'requestPasswordReset'])->middleware('throttle:3,1')->name('password.reset.request');
Route::post('password-reset/{token}',   [PasswordController::class, 'resetPassword'])->name('password.update');

Route::get('verify-email/{id}/{hash}',  [AuthController::class, 'verify'])->middleware('signed')->name("verification.verify");

// Auth Routes, accessible without verification
Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::post('resend-verification-email',    [AuthController::class, 'resendVerificationEmail'])->middleware(['throttle:6,1'])->name('verification.resend');
    Route::post('logout',                       [AuthController::class, 'logout']);
    Route::post('logout-all',                   [AuthController::class, 'logoutAll']);

    Route::get('dashboard',                     [Application::class, 'dashboard']);

    Route::group(['middleware' => ['verified']], function () {
        Route::get('active-devices',    [AuthController::class, 'activeDevices']);
        Route::get('profile',           [ProfileController::class, 'profile']);
        Route::put('update-password',   [ProfileController::class, 'updatePassword']);
    });
});



// [OK] Registration
// [OK] Verification email
// [OK] Verify
// [OK] Resend Verification email
// [OK] Login
// [OK] Logout (single device)
// [OK] Logout (All devices)
// [OK] Forgot password (request)
// [OK] Reset password

// Change password
// Update email
// Update profile picture
// Update profile information
// Delete account
// Profile



// Fallback route
Route::fallback(function () {
    return response()->json(['message' => 'Not Found.'], 404);
});
