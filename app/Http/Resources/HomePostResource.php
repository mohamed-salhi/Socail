<?php

namespace App\Http\Resources;

use App\Models\Favorite;
use App\Models\FavoriteUser;
use App\Models\Like;
use App\Models\Package;
use App\Models\Reviews;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class HomePostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'uuid' => $this->posts->uuid,
            'image' => $this->user->image,
            'name' =>$this->user->name,
            'user_uuid' =>$this->user->uuid,
            'attachments' =>$this->attachments,
            'count_like' =>$this->likes_count,
            'count_comment' =>$this->comments_count,
            'is_favorite' =>Favorite::query()->where('content_uuid', $this->uuid)->where('user_uuid', Auth::guard('sanctum')->user()->uuid)->exists(),
            'is_like' =>Like::query()->where('content_uuid', $this->uuid)->where('user_uuid', Auth::guard('sanctum')->user()->uuid)->exists(),

        ];

    }
}
