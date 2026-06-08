<?php

namespace App\Features\MultiDevice;

use App\Features\MultiDevice\Middleware\TrackDeviceSession;
use App\Features\MultiDevice\Models\PersonalAccessToken;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Laravel\Sanctum\Sanctum;

class MultiDeviceServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/migrations');
    }

    public function boot(): void
    {
        Sanctum::usePersonalAccessTokenModel(PersonalAccessToken::class);

        app('router')->pushMiddlewareToGroup('api', TrackDeviceSession::class);

        Route::middleware('api')
            ->prefix('api/v1')
            ->group(__DIR__ . '/routes.php');
    }
}
