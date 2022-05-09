<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegistrationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'name'      => 'required|max:255',
            'email'     => 'required|unique:App\Models\User,email',
            'mobile'    => 'required|unique:App\Models\User,mobile',
            'password'  => ['required','confirmed', Password::defaults()],
            'country'   => 'required|max:3',
            'country_code'  => 'max:3',
        ];
    }
}
