<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DetailsShahidResource extends JsonResource
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
            'title' => $this->title,
            'views' => $this->views,
            'likes' => $this->like_count ?? 0,
            'video' => $this->video,
            'image' => $this->image,
            'is_like' => $this->is_like,
            'created_at' => $this->created_at->format('d/m/Y'),

        ];


    }
}
