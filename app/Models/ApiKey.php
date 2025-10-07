<?php

namespace App\Models;

use App\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ApiKey extends BaseModel
{
    use HasFactory;

    protected $fillable = ['user_id', 'key', 'secret', 'abilities', 'expires_at'];

    protected $casts = [
        'abilities' => 'array',
        'expires_at' => 'datetime',
    ];

    // Generate key & secret
    public static function generateForUser($userId, $abilities = null, $expiresAt = null)
    {
        return self::create([
            'user_id' => $userId,
            'key' => Str::random(32),
            'secret' => hash('sha256', Str::random(64)),
            'abilities' => $abilities,
            'expires_at' => $expiresAt,
        ]);
    }
}
