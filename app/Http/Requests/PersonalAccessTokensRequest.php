<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PersonalAccessTokensRequest extends FormRequest
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
     */
    public function rules(): array
    {
        return [
            'tokenable_type' => 'required',
            'tokenable_id' => 'required',
            'name' => 'required',
            'token' => 'required',
            'abilities' => '',
            'attributes' => '',
            'last_used_at' => '',
            'expires_at' => ''
        ];
    }
}