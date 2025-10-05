<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{

    protected $fillable = [
        'user_id',
        'profile_picture',
        'mobile',
        'pending_email',
        'address',
        'dob',
        'gender',
        'bio',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}