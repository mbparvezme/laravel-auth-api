<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

// Public Routes
Route::post('registration',     [AuthController::class, 'store']);
Route::post('login',             [AuthController::class, 'login']);
Route::post('password-reset',   [AuthController::class, 'requestPasswordReset']);
Route::put('reset-password',    [AuthController::class, 'resetPassword']);

// Auth Routes, accessible without verification
Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::get('logout',                    [AuthController::class, 'logout']);
    Route::post('email-verification',       [AuthController::class, 'sendVerificationEmail']);
    Route::get('verify-email/{id}/{hash}',  [AuthController::class, 'verify'])->name("verification.verify");
    Route::get('dashboard',                 [Application::class, 'dashboard']);
});

// Auth Routes, required email verification
Route::group(['middleware' => ['auth:sanctum', 'verified']], function () {
    Route::get('profile',           [ProfileController::class, 'profile']);
    Route::put('update-password',   [ProfileController::class, 'updatePassword']);
});

// Fallback route
Route::fallback(function () {
    return response()->json('Page Not Found. If error persists, contact info@website.com', 404);
});
