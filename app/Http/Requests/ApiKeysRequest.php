<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ApiKeysRequest extends FormRequest
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
            'user_id' => 'required',
            'name' => 'required',
            'key' => 'required',
            'secret' => 'required',
            'abilities' => '',
            'expires_at' => ''
        ];
    }
}