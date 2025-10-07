<?php

namespace App\Models;

use App\BaseModel;

class Profile extends BaseModel
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