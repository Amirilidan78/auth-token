<?php

namespace Amirilidan78\AuthToken;

use Amirilidan78\AuthToken\Middleware\AuthTokenMiddleware;
use Illuminate\Support\ServiceProvider;
use Laravel\Sanctum\Sanctum;

class AuthTokenServiceProvider extends ServiceProvider
{
    public function register()
    {
        app('router')->aliasMiddleware('authentication', AuthTokenMiddleware::class);
    }

    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        $this->publishes([
            __DIR__.'/../database/migrations' => database_path('migrations'),
        ], 'auth-token-migration');

        $this->publishes([
            __DIR__.'/../config/auth_token.php' => config_path('auth_token.php'),
        ],'auth-token-config');
    }

}
