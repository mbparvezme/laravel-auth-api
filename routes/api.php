<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\v1\Site;
use App\Http\Controllers\v1\User;

Route::prefix('v1')->group(function(){
  Route::get('/home',                   [Site::class, 'index']);

  Route::post('/register',              [User::class, 'create']);
  Route::post('/login',                 [User::class, 'login']);
  Route::post('/password-reset',        [User::class, 'requestPasswordReset']);
  Route::post('/reset-password',        [User::class, 'resetPassword']);

  Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::get('/dashboard',  ['App\Http\Controllers\v1\Admin\Application', 'dashboard']);
    Route::get('/logout',               [User::class, 'logout']);
  });
});
