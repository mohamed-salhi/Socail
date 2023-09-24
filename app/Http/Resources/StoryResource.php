<?php

namespace App\Http\Resources;

use App\Models\Comment;
use App\Models\Favorite;
use App\Models\FavoriteUser;
use App\Models\Like;
use App\Models\Package;
use App\Models\Reviews;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class StoryResource extends JsonResource
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
            'file' => @$this->image,
            'type' => @$this->imagesStory->type_attachment,

            'is_favorite' =>Like::query()->where('content_uuid', $this->uuid)->where('user_uuid', Auth::guard('sanctum')->user()->uuid)->exists(),



        ];

    }
}
