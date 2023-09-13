<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SingleJobPostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'uuid' => $this->uuid,
            'posted_by' => $this->posted_by,
            'title' => $this->title,
            'country' => $this->country,
            'is_published' => $this->is_published,
            'created_at' => $this->created_at->format('F j, Y'),
            'desription' => $this->description,
        ];
    }
}
