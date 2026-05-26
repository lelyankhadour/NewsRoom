<?php

namespace App\Http\Resources\Api\V2;


use App\Http\Resources\Api\V1\TagResource;
use App\Http\Resources\Api\V1\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ArticleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'title'        => $this->title,
            'slug'         => $this->slug,
            'content'      => $this->content,
            'status'       => $this->status,
            'published_at' => $this->published_at ? $this->published_at->toDateTimeString() : null,
            
         
            'author'       => new UserResource($this->whenLoaded('user')),
            
            'tags'         => TagResource::collection($this->whenLoaded('tags')),
            
            'created_at'   => $this->created_at->toDateTimeString(),
            'updated_at'   => $this->updated_at->toDateTimeString(),
        ];
    }
}