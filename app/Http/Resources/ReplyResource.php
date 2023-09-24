<?php

namespace App\Http\Resources;

use App\Models\Comment;
use App\Models\Favorite;
use App\Models\FavoriteUser;
use App\Models\Package;
use App\Models\Reviews;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class ReplyResource extends JsonResource
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
            'image' => @$this->user->image,
            'name' =>$this->user->name,
            'user_uuid' =>$this->user->uuid,
            'comment' =>$this->comment,
            'favorite_count' =>$this->favorites_count,

        ];

    }
}
