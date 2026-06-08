<?php

use App\Http\Controllers\AppLogController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('account/email/verify', [ProfileController::class, 'verifyNewEmail'])
    ->middleware('signed')
    ->name('account.email.verify');

Route::middleware(['auth:sanctum', 'verified', 'check.user.status', 'throttle:60,1'])->group(function () {
    Route::get('profile',                   [ProfileController::class, 'show']);
    Route::patch('profile',                 [ProfileController::class, 'update']);
    Route::patch('account/email',           [ProfileController::class, 'updateEmail']);
    Route::post('account/password',         [PasswordController::class, 'update']);
    Route::patch('account/status/{status}', [ProfileController::class, 'updateStatus']);
    Route::get('logs',                      [AppLogController::class, 'index']);
});
