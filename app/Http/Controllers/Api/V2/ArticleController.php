<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use App\Contracts\ArticleRepositoryInterface;
use App\Http\Resources\Api\V2\ArticleV2Resource;
use App\Services\ArticleService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

class ArticleController extends Controller
{
    use ApiResponse;

    protected ArticleRepositoryInterface $articleRepository;
protected ArticleService $article;
    public function __construct(ArticleService $article)
    {
        $this->article = $article;
    }
      public function index(): JsonResponse
    {
        try {

            $articles = $this->article->getPublishedArticles(15);
            $transformed = ArticleV2Resource::collection($articles)->response()->getData(true);

            return $this->successResponse($transformed, "Articles for V2 (Mobile App) retrieved successfully.");
        } catch (\Throwable $exception) {
            return $this->errorResponse("Failed to retrieve V2 payload.", 500);
        }
    }
}