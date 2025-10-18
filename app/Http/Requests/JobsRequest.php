<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class JobsRequest extends FormRequest
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
            'queue' => 'required',
            'payload' => 'required',
            'attempts' => 'required',
            'reserved_at' => '',
            'available_at' => 'required'
        ];
    }
}