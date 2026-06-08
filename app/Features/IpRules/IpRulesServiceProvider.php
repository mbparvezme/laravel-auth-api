<?php

namespace App\Features\IpRules;

use App\Features\IpRules\Middleware\CheckIpRules;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class IpRulesServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/migrations');
    }

    public function boot(Kernel $kernel): void
    {
        // Prepend globally so IP check runs before any auth middleware
        $kernel->prependMiddleware(CheckIpRules::class);

        Route::middleware('api')
            ->prefix('api/v1')
            ->group(__DIR__ . '/routes.php');
    }
}
