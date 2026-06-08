<?php

use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(base_path('routes/v1/auth.php'));
Route::prefix('v1')->group(base_path('routes/v1/profile.php'));

Route::fallback(fn () => response()->json([
    'success' => false,
    'message' => __('app.ROUTE_NOT_FOUND'),
], 404));
