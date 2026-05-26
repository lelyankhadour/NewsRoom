<?php

namespace App\Http\Requests;

use App\Rules\CleanTextRule;
use Illuminate\Foundation\Http\FormRequest;


class StoreProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:2'],
            'job_title' => ['nullable', 'string', 'min:2'],

            // unchangable if  we want to login in other role should use another email
            'email' => ['prohibited'],
          

            'phone_number' => [ 'string', 'min:8'],
            'department' => [ 'string','min:4', 'max:20'],

            'bio' => ['sometimes', 'string', 'min:10', new CleanTextRule()],
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('bio')) {
            $this->merge([
                'bio' => trim($this->bio),
            ]);
        }

        if ($this->has('phone+_number')) {
            $this->merge([
                'phone_number' => trim($this->phone),
            ]);
        }
    }
}
