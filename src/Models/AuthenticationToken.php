<?php

namespace Amirilidan78\AuthToken\Models;

use Illuminate\Database\Eloquent\Casts\AsCollection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuthenticationToken extends Model
{
    use HasFactory ;

    protected $guarded = [] ;

    protected $casts = [
        'authorized_at' => 'date' ,
        'last_access_at' => 'date' ,
        'expires_at' => 'date' ,
        'payload' => AsCollection::class ,
    ] ;

    public function tokenable()
    {
        return $this->morphTo('tokenable');
    }

}
