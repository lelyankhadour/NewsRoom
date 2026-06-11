<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCommentRequest; 
use App\Http\Resources\Api\V1\CommentResource;
use App\Models\Article;
use App\Services\CommentService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

class CommentController extends Controller
{
    use ApiResponse;

    protected CommentService $service;

    public function __construct(CommentService $service)
    {
        $this->service = $service;
    }

    public function store(StoreCommentRequest $request, Article $article): JsonResponse
    {
        // try {
            $comment = $this->service->createComment($request->validated(), $article, $request->user());

            return $this->successResponse(
                new CommentResource($comment->load('user')),
                "Comment posted successfully.",
                201
            );
        // } catch (\Throwable $exception) {
        //     return $this->errorResponse("Failed to process the comment.", 500);
        // }
    }
}