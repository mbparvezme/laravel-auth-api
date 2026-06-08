<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AppLog extends Model
{
    const UPDATED_AT = null;

    protected $fillable = ['user_id', 'action', 'data', 'user_visible'];

    protected function casts(): array
    {
        return [
            'data'         => 'array',
            'user_visible' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
