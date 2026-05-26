<?php

namespace App\Http\Requests\Auth;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;
use App\Enums\UserRole;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:50'],
            'job_title'  => [ 'string', 'max:50'],

            'email'      => ['required', 'email', 'unique:users,email'],
            'password'   => ['required', 'string', 'min:6'],

       'role' => ['nullable', new Enum(UserRole::class)],

            'department'    => ['nullable','string', 'min:6'],
            'phone_number'      => ['nullable', 'string', 'max:20'],
        ];
    }
}
