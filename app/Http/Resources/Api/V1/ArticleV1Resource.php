<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ArticleV1Resource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'title'        => $this->title,
            'content'      => $this->content,
            'name'  => $this->user?->name ?? 'Unknown', 
            'published_at' => $this->created_at?->toDateTimeString(), 
              'attachments' => AttachmentResource::collection($this->whenLoaded('attachments')),
        ];
    }
}