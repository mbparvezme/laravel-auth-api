<?php

use App\Features\ApiKeys\ApiKeyServiceProvider;
use App\Features\SocialAuth\SocialAuthServiceProvider;
use App\Providers\AppServiceProvider;

return [
    AppServiceProvider::class,
    SocialAuthServiceProvider::class,
    ApiKeyServiceProvider::class,
];
