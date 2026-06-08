<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateEmailRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'email'    => ['required', 'email', Rule::unique('users', 'email')->ignore($this->user()->id)],
            'password' => ['required', 'string'],
        ];
    }
}
