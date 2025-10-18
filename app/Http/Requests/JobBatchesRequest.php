<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class JobBatchesRequest extends FormRequest
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
            'name' => 'required',
            'total_jobs' => 'required',
            'pending_jobs' => 'required',
            'failed_jobs' => 'required',
            'failed_job_ids' => 'required',
            'options' => '',
            'cancelled_at' => '',
            'finished_at' => ''
        ];
    }
}