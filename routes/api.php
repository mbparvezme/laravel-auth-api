<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PasswordController;

// Public Routes
Route::post('create-user',      [AuthController::class, 'registration']);
Route::post('login',            [AuthController::class, 'login']);

Route::post('password-reset',           [PasswordController::class, 'requestPasswordReset'])->name('password.reset.request');
Route::get('password-reset/{token}',    [PasswordController::class, 'showResetForm'])->name('password.reset.form');
Route::post('reset-password',           [PasswordController::class, 'resetPassword'])->name('password.update');

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
