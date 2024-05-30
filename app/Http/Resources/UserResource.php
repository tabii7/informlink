<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'fullname' => $this->name,
            'email' => $this->email,
            'user_name' => $this->user_name,
            'phone' => $this->profile_photo_path,
            'token' => $this->when($this->token, $this->token),
          
        ];
    }
}
