<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'        => $this->id,
            'name'      => $this->name,
            'email'     => $this->email,
            'profile'   => $this->profile ? [
                'profile_picture' => $this->profile->profile_picture,
                'mobile'          => $this->profile->mobile,
                'address'         => $this->profile->addresses,
                'dob'             => $this->profile->dob,
                'gender'          => $this->profile->gender,
                'bio'             => $this->profile->bio,
            ] : null,
        ];
    }
}
