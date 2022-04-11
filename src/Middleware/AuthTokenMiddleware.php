<?php

namespace Amirilidan78\AuthToken\Middleware;

use Amirilidan78\AuthToken\Services\AuthService;
use Amirilidan78\AuthToken\Services\UnAuthorizedException;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

class AuthTokenMiddleware
{
    public function handle(Request $request, Closure $next, string|null $guard = null )
    {
        if( !$guard )
            $guard = Config::get('token_auth.default_guard') ;

        if( AuthService::validate_token( AuthService::get_auth_token_from_request($request) ,$guard ) )
            return $next($request);

        throw new UnAuthorizedException() ;
    }
}
