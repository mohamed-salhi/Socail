<?php

namespace App\Http\Resources;

use App\Models\Favorite;
use App\Models\FavoriteUser;
use App\Models\Package;
use App\Models\Reviews;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class ProfileResource extends JsonResource
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
            'image' => $this->image,
            'name' =>$this->name,
            'user' =>$this->user,
            'biography' =>$this->biography,
            'followers' => $this->followers_count,
            'following' => $this->following_count,


        ];

    }
}
