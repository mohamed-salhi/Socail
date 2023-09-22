<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EditProfileResource extends JsonResource
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
            'image'=> $this->image,
            'name'=> $this->name,
            'biography'=> $this->biography,

            'user'=> $this->user,
            'email' => $this->email,
            'mobile' => $this->mobile,
            'mobile_without_country_code' => @explode( '-', $this->mobile)[1],
            'mobile_country_code' => @explode( '-', $this->mobile)[0],
            'gender' => $this->gender,
            'dateOfBirth' =>$this->dateOfBirth,
            ];
    }
}
