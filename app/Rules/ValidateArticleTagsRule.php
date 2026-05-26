<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\DB;
use Throwable;

class ValidateArticleTagsRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        try {
            // Verify if the incoming tag ID exists inside the database tags table
            $exists = DB::table('tags')->where('id', $value)->exists();

            if (!$exists) {
                $fail("The selected tag ID {$value} is completely invalid or does not exist inside platform records.");
            }
        } catch (Throwable $exception) {
               $fail("Validation service error occurred while processing system tags.");
        }
    }
}