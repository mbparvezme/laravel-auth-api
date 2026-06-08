<?php

namespace App\Features\ApiKeys;

use App\Features\ApiKeys\Middleware\AuthenticateWithApiKey;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class ApiKeyServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/migrations');
    }

    public function boot(): void
    {
        app('router')->aliasMiddleware('api.key.auth', AuthenticateWithApiKey::class);

        Route::middleware('api')
            ->prefix('api/v1')
            ->group(__DIR__ . '/routes.php');
    }
}
