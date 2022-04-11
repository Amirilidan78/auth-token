# auth-token
simple laravel authentication package


Install
```
composer require amirilidan78/auth-token:dev-main
```


Publish migrations
```
php artisan vendor:publish --tag=auth-token-migration
```


Publish config file
```
php artisan vendor:publish --tag=auth-token-config
```


Docs 

import AuthService from `Amirilidan78\AuthToken\Services`

### `AuthService::create_auth_token`
Use this function for creating token
<br>
Function returns token : string
<br>
Acceptable request header `Auth {$token}`

### `AuthService::get_auth_token_from_request`
Function extract authentication token from user request
<br>
Function returns authentication token : string|null

### `AuthService::validate_token`
Function validate request auth token you should specify guard for this function, guard is string like `user`, `admin`, ...
<br>
Function returns authentication token : string|null

### `AuthService::logout`
Function delete authentication token from database
<br>
Function returns authentication token : string|null
 
### `AuthService::remove_expired_auth_tokens`
Function removes all expired tokens, expire token config key is `token_auth.token_expire_minutes`
<br>
Function returns authentication token : string|null


 
### `Middleware::authentication`
Use middleware authentication with guard for secure routes
<br>
Example `->middleware(['authentication:user'])`

