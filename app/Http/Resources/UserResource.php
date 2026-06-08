<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'      => $this->id,
            'name'    => $this->name,
            'email'   => $this->email,
            'status'  => $this->status->value,
            'profile' => $this->whenLoaded('profile', fn () => [
                'profile_picture' => $this->profile->profile_picture,
                'mobile'          => $this->profile->mobile,
                'address'         => $this->profile->address,
                'dob'             => $this->profile->dob?->format('Y-m-d'),
                'gender'          => $this->profile->gender,
                'bio'             => $this->profile->bio,
            ]),
        ];
    }
}
