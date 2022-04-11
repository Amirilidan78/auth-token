<?php

namespace Amirilidan78\AuthToken\Services;

use Exception;

class UnAuthorizedException extends Exception
{
    public function render($request)
    {
        return response()->json([ 'message' => 401 ] ,'un authorized' );
    }
}
