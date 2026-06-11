<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Resources\Json\JsonResource;

class CommentResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'content' => $this->content,
            'user' => new UserResource($this->whenLoaded('user')),
            'created_at' => $this->created_at->toDateTimeString(),
        ];
    }
}