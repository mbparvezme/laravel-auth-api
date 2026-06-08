<?php

namespace App\Features\MagicLink\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class MagicLink extends Model
{
    protected $fillable = ['email', 'token', 'expires_at', 'used_at'];

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
            'used_at'    => 'datetime',
        ];
    }

    public static function generate(string $email): array
    {
        $rawToken = Str::random(64);

        $link = static::create([
            'email'      => $email,
            'token'      => hash('sha256', $rawToken),
            'expires_at' => now()->addMinutes(15),
        ]);

        return [$link, $rawToken];
    }

    public static function findValid(string $email, string $rawToken): ?static
    {
        return static::where('email', $email)
            ->where('token', hash('sha256', $rawToken))
            ->whereNull('used_at')
            ->where('expires_at', '>', now())
            ->first();
    }
}
