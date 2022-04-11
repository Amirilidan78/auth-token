<?php

namespace Amirilidan78\AuthToken;

use Amirilidan78\AuthToken\Models\AuthenticationToken;

trait Tokenable
{
    public function tokens()
    {
        return $this->morphMany(AuthenticationToken::class, 'tokenable');
    }
}
