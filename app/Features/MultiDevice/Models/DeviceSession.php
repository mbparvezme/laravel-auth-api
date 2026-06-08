<?php

namespace App\Features\MultiDevice\Models;

use Illuminate\Database\Eloquent\Model;

class DeviceSession extends Model
{
    protected $fillable = ['token_id', 'ip_address', 'platform', 'device', 'user_agent'];
}
