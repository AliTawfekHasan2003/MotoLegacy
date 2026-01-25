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
            'id'                => $this->id, 
            'nationality'       => new CountryResource($this->whenLoaded('nationality')), 
            'name'              => $this->name, 
            'email'             => $this->email, 
            'phone_country'     => new CountryResource($this->whenLoaded('phone_country')), 
            'phone'             => $this->phone, 
            'image'             => $this->image, 
            'summary'           => $this->summary, 
            'status'            => $this->status,
            'role'                  => $this->roleModel(),
        ];
    }
}
