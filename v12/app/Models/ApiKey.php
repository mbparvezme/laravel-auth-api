<?php

namespace App\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class ApiKey extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['user_id', 'key', 'name', 'secret', 'abilities', 'expires_at'];

    protected $casts = [
        'abilities' => 'array',
        'expires_at' => 'datetime',
    ];

    public static function generateForUser($userId, $name, $abilities = [], $expiresAt = null)
    {
        $plainSecret = Str::random(64);
        $apiKey = self::create([
            'user_id' => $userId,
            'name' => $name,
            'key' => Str::random(32),
            'secret' => hash('sha256', $plainSecret),
            'abilities' => $abilities,
            'expires_at' => $expiresAt,
        ]);
        $apiKey->plain_secret = $plainSecret;
        return $apiKey;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
