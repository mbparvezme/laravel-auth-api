<?php

namespace App\Features\ApiKeys\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class ApiKey extends Model
{
    protected $fillable = ['user_id', 'name', 'prefix', 'token', 'last_used_at'];

    protected function casts(): array
    {
        return [
            'last_used_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function generate(int $userId, string $name): array
    {
        $rawToken = Str::random(40);

        $key = static::create([
            'user_id' => $userId,
            'name'    => $name,
            'prefix'  => substr($rawToken, 0, 8),
            'token'   => hash('sha256', $rawToken),
        ]);

        return [$key, $rawToken];
    }

    public static function findByRawToken(string $rawToken): ?static
    {
        return static::where('token', hash('sha256', $rawToken))->first();
    }
}
