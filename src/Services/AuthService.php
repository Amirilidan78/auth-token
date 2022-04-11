<?php

namespace Amirilidan78\AuthToken\Services;

use Amirilidan78\AuthToken\Models\AuthenticationToken;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;

class AuthService
{
    private static function get_cache_prefix()
    {
        return Config::get('token_auth.redis_cache_prefix') ;
    }

    private static function get_header_key()
    {
        return Config::get('token_auth.token_header_key') ;
    }

    private static function get_random_string_length()
    {
        return Config::get('token_auth.token_random_string_length') ;
    }

    private static function get_token_expires_in_minute()
    {
        return Config::get('token_auth.token_expire_minutes') ;
    }

    // ======================================== //

    public static function redis_auth_key( string|int $model_id ,string $token ) : string
    {
        return self::get_cache_prefix() . "_{$model_id}_{$token}" ;
    }

    public static function generate_auth_token( string|int $model_id ,string $token ) : string
    {
        return encrypt($model_id . '|' . $token) ;
    }

    public static function extract_user_and_token_from_header( string $auth_token ) : array
    {
        $plain_text = decrypt($auth_token) ;

        $arr = explode('|',$plain_text) ;

        return [
            $arr[0] , // user _id
            $arr[1] , // token
        ] ;
    }

    public static function get_auth_token_from_request( Request $request ) : string|null
    {
        return $request->header(self::get_header_key()) ?? null ;
    }

    public static function validate_token( string|null $auth_token ,string $guard ) : bool
    {
        if( !$auth_token )
            return false ;

        [ $model_id ,$token ] = self::extract_user_and_token_from_header( $auth_token ) ;

        $redis_key = self::redis_auth_key( $model_id ,$token ) ;

        // if token authorized less than 60 seconds ago
        if( Cache::has($redis_key) )
            return true ;

        $authentication_token_model = AuthenticationToken::query()->where('guard',$guard)->where('token',$token)->with('tokenable')->first() ;

        if( ! $authentication_token_model )
            return false ;

        if( ! $authentication_token_model['tokenable'] )
            return false ;

        // update token details
        $authentication_token_model->update([
            'expires_at' => now()->addHour()->timestamp ,
            'payload' => [ 'ip' => request()->ip() ] ,
        ]) ;

        // remember token is valid for 60 seconds
        Cache::remember($redis_key ,60,fn() => true ) ;

        return true ;
    }

    public static function create_auth_token( Authenticatable $authenticatable ,string|null $guard = null ) : string
    {
        if( !$guard )
            $guard = Config::get('token_auth.default_guard') ;

        $token = Str::random( self::get_random_string_length() ) ;

        AuthenticationToken::query()->create([
            'tokenable_type' => get_class($authenticatable) ,
            'tokenable_id' => $authenticatable['id'] ,
            'guard' => $guard ,
            'token' => $token ,
            'authorized_at' => now() ,
            'last_access_at' => now() ,
            'expires_at' => now()->addMinutes( self::get_token_expires_in_minute() ) ,
            'payload' => [
                'ip' => request()->ip()
            ]
        ]);

        return self::generate_auth_token( $authenticatable['id'], $token ) ;
    }

    public static function logout( string $auth_token ) : bool
    {
        [ $model_id ,$token ] = self::extract_user_and_token_from_header( $auth_token ) ;

        $authentication_token_model = AuthenticationToken::query()->where('token',$token)->first() ;

        if( !$authentication_token_model )
            return false ;

        $redis_key = self::redis_auth_key( $model_id ,$token ) ;

        Cache::forget($redis_key) ;

        $authentication_token_model->delete() ;

        return true ;
    }

    public static function remove_expired_auth_tokens() : int
    {
        $now = now()->timestamp ;

        return AuthenticationToken::query()->where('expires_at',"<",$now)->delete() ;
    }

    public static function user() : Authenticatable|null
    {
        $auth_token = self::get_auth_token_from_request( \request() ) ;

        if( !$auth_token )
            return null ;

        [ $model_id ,$token ] = self::extract_user_and_token_from_header( $auth_token ) ;

        $authentication_token_model = AuthenticationToken::query()->where("token",$token)->with('tokenable')->first() ;

        if( !$authentication_token_model )
            return null ;

        return $authentication_token_model['tokenable'] ;
    }

}
