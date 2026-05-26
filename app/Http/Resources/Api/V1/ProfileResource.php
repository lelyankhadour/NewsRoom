<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Resources\Json\JsonResource;

class ProfileResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'           => $this->id,
            'name'    => $this->full_name,
            'email'        => $this->email,
            'job_title'         => $this->job_title ?? null,
            'phone_number'        => $this->phone_number,
            'department'   => $this->department,
            'bio' => $this->bio,
        ];
    }
}
