<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegistrationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        $mob_rules = env('MOBILE_NUMBER_REQUIRED') == true ? 'required|unique:App\Models\User,mobile' : 'unique:App\Models\User,mobile';

        return [
            'name'      => 'required|max:255',
            'email'     => 'required|unique:App\Models\User,email',
            'mobile'    => $mob_rules,
            'password'  => ['required','confirmed', Password::defaults()],
            'country'   => 'required|max:3',
            'country_code'  => 'max:3',
        ];
    }
}
