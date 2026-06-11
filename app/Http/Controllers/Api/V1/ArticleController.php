<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreArticleRequest;
use App\Http\Requests\UpdateArticleRequest;
use App\Http\Resources\Api\V1\ArticleV1Resource;

use App\Models\Article;
use App\Services\ArticleService;
use App\Traits\ApiResponse;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ArticleController extends Controller
{
    use ApiResponse,AuthorizesRequests;

    protected ArticleService $service;

    public function __construct(ArticleService $service)
    {
        $this->service = $service;
    }
    public function index(): JsonResponse
{
    try {
        $articles = $this->service->getPublishedArticles(15);
        
        return $this->successResponse(
            ArticleV1Resource::collection($articles)->response()->getData(true), 
            "Articles collection compiled successfully."
        );
    } catch (\Throwable $exception) {
        return $this->errorResponse("Service mapping execution error encountered.", 500);
    }
}

    public function store(StoreArticleRequest $request): JsonResponse
    {
        try {
            $article = $this->service->createArticle($request->validated(), $request->user()->id);
      
 
            return $this->successResponse(
                new ArticleV1Resource($article->load(['user', 'tags'])),
                "Article resource initialized successfully.",
                201
            );
        } catch (\Throwable $exception) {
            return $this->errorResponse("Failed to register and process the requested article.", 500);
        }
    }

    /**
     * Route API Endpoint for picking explicit article profile.
     */
    public function show(Article $article): JsonResponse
    {
        try {
            $this->authorize("view",$article);
            return $this->successResponse(
                new ArticleV1Resource($article->load(['user', 'tags','attachments'])),
                "Article details fetched successfully."
            );
        } catch (\Throwable $exception) {
            return $this->errorResponse("Requested element allocation fault.", 500);
        }
    }

public function update(UpdateArticleRequest $request, Article $article): JsonResponse
{ 
    try{
        $this->authorize('update', $article);
   
    $updatedArticle = $this->service->updateArticle($article, $request->all());
// dd( $updatedArticle);

    return $this->successResponse(
        new ArticleV1Resource($updatedArticle->load(['user', 'tags','attachments'])),
        "Article persistence layout modified successfully."
    );
     } catch (\Throwable $exception) {
            return $this->errorResponse("Entity state tracking mismatch triggered a failure.", 500);
        }
}
   
public function destroy($id): JsonResponse
{

    $article = Article::find($id);

    if (!$article) {
        return response()->json(['message' => 'Article not found'], 404);
    }


    $this->authorize('delete', $article);

  
    $this->service->deleteArticle($article);

    return response()->json(['message' => 'Article deleted successfully'], 200);
}
}