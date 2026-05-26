<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name'         => 'sometimes|required|string|max:255',
            'job_title'    => 'sometimes|nullable|string|max:255',
            'department'   => 'sometimes|nullable|string|max:255',
            'bio'          => 'sometimes|nullable|string|max:1000',
            'phone_number' => 'sometimes|nullable|string|max:20',
            'email'        => ['prohibited'],
            'role'         => ['prohibited'],
        ];
    }
}