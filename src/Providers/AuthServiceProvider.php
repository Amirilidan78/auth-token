<?php

namespace Amirilidan78\AuthToken\Providers;

use Amirilidan78\AuthToken\Middleware\AuthTokenMiddleware;
use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    public function register()
    {
        app('router')->aliasMiddleware('authentication', AuthTokenMiddleware::class);
    }

    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__.'/../Migrations');

        $this->publishes([
            __DIR__.'/../Configs/token_auth.php' => config_path('auth_token.php'),
        ]);
    }

}
