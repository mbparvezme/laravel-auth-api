<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\v1\UserController;
use App\Http\Controllers\v1\Auth;
use App\Http\Controllers\v1\UserVerification;
use App\Http\Controllers\v1\Admin\Application;
use App\Http\Controllers\v1\NewPassword;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/


// Route::prefix('v1')->group(function(){
Route::prefix('v1')->group(function(){

    // Public Routes
    Route::post('/registration',        [UserController::class, 'store']);
    Route::post('/login',               [Auth::class, 'login']);
    Route::post('/password-reset',      [NewPassword::class, 'requestPasswordReset']);
    Route::put('/reset-password',       [NewPassword::class, 'resetPassword']);

    // Auth Routes, accessible without verification
    Route::group(['middleware' => ['auth:sanctum']], function () {
        Route::post('email-verification',       [UserVerification::class, 'sendVerificationEmail']);
        Route::get('verify-email/{id}/{hash}',  [UserVerification::class, 'verify'])->name("verification.verify");
        Route::get('/dashboard',        [Application::class, 'dashboard']);
        Route::get('/logout',           [Auth::class, 'logout']);
    });

    // Auth Routes, required email verification
    Route::group(['middleware' => ['auth:sanctum', 'verified']], function () {
        Route::get('/profile',        [Application::class, 'profile']);
    });

    // Fallback route
    Route::fallback(function(){
        return response()->json('Page Not Found. If error persists, contact info@website.com', 404);
    });
});
