<?php

namespace Amirilidan78\AuthToken;

use Amirilidan78\AuthToken\Middleware\AuthTokenMiddleware;
use Illuminate\Support\ServiceProvider;
use Laravel\Sanctum\Sanctum;

class AuthServiceProvider extends ServiceProvider
{
    public function register()
    {
        app('router')->aliasMiddleware('authentication', AuthTokenMiddleware::class);

        if (! app()->configurationIsCached())
            $this->mergeConfigFrom(__DIR__.'/../config/auth_token.php', 'auth_token');
    }

    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        $this->publishes([
            __DIR__.'/../config/auth_token.php' => config_path('auth_token.php'),
        ]);
    }

}
