<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AppController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PasswordController;
use App\Http\Controllers\ProfileController;

// Public Routes
Route::group(['middleware' => ['throttle:5,1']], function () {
    /** OK */Route::post('register', [AuthController::class, 'registration']);
    /** OK */Route::get('verify-email/{id}/{hash}',  [AuthController::class, 'verify'])->middleware('signed')->name("verification.verify");
    /** OK */Route::post('login',    [AuthController::class, 'login']);

    /** OK */Route::get('verify-new-email',          [ProfileController::class, 'verifyNewEmail'])->name('new.email.verify');

    Route::post('password/forgot',          [PasswordController::class, 'requestPasswordReset'])->name('password.reset.request');
    Route::post('password/reset/{token}',   [PasswordController::class, 'resetPassword'])->name('password.update');
});

// Auth Routes, accessible without verification
Route::group(['middleware' => ['auth:sanctum', 'throttle:3,1']], function () {
    Route::post('resend-verification-email',    [AuthController::class, 'resendVerificationEmail'])->name('verification.resend');
        /** OK */Route::post('logout',                       [AuthController::class, 'logout']);
        /** OK */Route::post('logout-all',                   [AuthController::class, 'logoutAll']);

    Route::group(['middleware' => ['verified']], function () {
            /** OK */Route::get('dashboard',                     [AppController::class, 'dashboard']);
            /** OK */Route::get('active-devices',    [AuthController::class, 'activeDevices']);

        Route::prefix('account')->group(function () {
            /** OK */Route::get('/',             [ProfileController::class, 'index']);
            /** OK */Route::patch('/email',       [ProfileController::class, 'updateEmail']);
            /** OK */Route::post('password',     [PasswordController::class, 'updatePassword']);
            Route::patch('{status}',    [ProfileController::class, 'accountStatus']);
        });
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
// [OK] Change account status // active, reactive, inactive, delete, banned

// Update profile picture
// Update profile information

// Fallback route
Route::fallback(function () {
    return response()->json(['message' => 'Not Found.'], 404);
});
