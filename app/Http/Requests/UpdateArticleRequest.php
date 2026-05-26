<?php

namespace App\Http\Requests;

use App\Enums\ArticleStatus;
use App\Rules\ValidateArticleTagsRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class UpdateArticleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'title'   => 'sometimes|required|string|max:255',
            'content' => 'sometimes|required|string',
            'status'  => ['sometimes', 'required', new Enum(ArticleStatus::class)],
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