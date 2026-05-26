<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class CleanTextRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Check if the sanitized title contains any character outside alphanumeric, spaces, hyphens, or underscores
        if (preg_match('/[^a-zA-Z0-9\s\-_]/', $value)) {
            $fail('The ' . $attribute . ' contains invalid characters after sanitization.');
        }
    }
}