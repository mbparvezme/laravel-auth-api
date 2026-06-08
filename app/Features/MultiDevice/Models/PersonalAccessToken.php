<?php

namespace App\Features\MultiDevice\Models;

use Illuminate\Database\Eloquent\Relations\HasOne;
use Laravel\Sanctum\PersonalAccessToken as SanctumToken;

class PersonalAccessToken extends SanctumToken
{
    public function deviceSession(): HasOne
    {
        return $this->hasOne(DeviceSession::class, 'token_id');
    }
}
