<?php

namespace App\Http\Requests;

use App\Enums\ArticleStatus;
use App\Enums\UserRole;
use App\Rules\CleanTextRule;
use App\Rules\ValidateArticleTagsRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreArticleRequest extends FormRequest
{
    
public function authorize(): bool
{
    $user = $this->user();

    return $user && in_array($user->role, [UserRole::WRITER, UserRole::ADMIN], true);
}
   
    protected function prepareForValidation(): void
    {
        $this->merge([
            // Strip multiple spaces and convert casing errors automatically to standard lowercase before validation
            'title' => $this->title ? strtolower(preg_replace('/\s+/', ' ', trim($this->title))) : null,
            'content' => $this->content ? trim($this->content) : null,
        ]);
    }

    public function rules(): array
    {
        return [
            'title' => [
                'required',
                'string',
                'min:10',
                Rule::unique('articles', 'title'),
                new CleanTextRule(),
            ],
            'content' => [
                'required',
                'string',
                'min:100',
            ],
            'status' => [
                'required',
                Rule::enum(ArticleStatus::class), 
            ],
            'tags' => [
                'nullable',
                'array',
            ],
            'tags.*' => [
                'integer',
              new ValidateArticleTagsRule(),
            ],
        ];
    }
}