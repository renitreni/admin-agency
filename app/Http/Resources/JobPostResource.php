<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class JobPostResource extends JsonResource
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
            'description_excerpt' => \Illuminate\Support\Str::limit(trim(preg_replace('/\s+/u', ' ', html_entity_decode(strip_tags($this->description), ENT_QUOTES | ENT_HTML5, 'UTF-8'))), 180),
            'posted_by' => $this->posted_by,
            'title' => $this->title,
            'country' => $this->country,
            'is_published' => $this->is_published,
            'created_at' => $this->created_at->format('F j, Y'),
        ];
    }
}
