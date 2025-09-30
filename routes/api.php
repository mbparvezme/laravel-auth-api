<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AppController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PasswordController;
use App\Http\Controllers\ProfileController;

// Public Routes
Route::post('register', [AuthController::class, 'registration'])->middleware('throttle:2,1');
Route::post('login',    [AuthController::class, 'login'])->middleware('throttle:2,1');

Route::post('forgot-password',          [PasswordController::class, 'requestPasswordReset'])->middleware('throttle:3,1')->name('password.reset.request');
Route::post('password-reset/{token}',   [PasswordController::class, 'resetPassword'])->name('password.update');

Route::get('verify-email/{id}/{hash}',  [AuthController::class, 'verify'])->middleware('signed')->name("verification.verify");
Route::get('verify-new-email',          [ProfileController::class, 'verifyNewEmail'])->name('new.email.verify');

// Auth Routes, accessible without verification
Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::post('resend-verification-email',    [AuthController::class, 'resendVerificationEmail'])->middleware(['throttle:6,1'])->name('verification.resend');
    Route::post('logout',                       [AuthController::class, 'logout']);
    Route::post('logout-all',                   [AuthController::class, 'logoutAll']);

    Route::get('dashboard',                     [AppController::class, 'dashboard']);

    Route::group(['middleware' => ['verified']], function () {
        Route::get('active-devices',    [AuthController::class, 'activeDevices']);

        Route::get('profile',           [ProfileController::class, 'index']);
        Route::patch('email',           [ProfileController::class, 'updateEmail']);
        Route::post('password',         [ProfileController::class, 'updatePassword']);
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
// [OK] Change password
// [OK] Profile
// [OK] Update new email
// [OK] New email verify

// Update profile picture
// Update profile information
// Delete account

// Fallback route
Route::fallback(function () {
    return response()->json(['message' => 'Not Found.'], 404);
});
