<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CountryResource extends JsonResource
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
            'country_code'      => $this->country_code,
            'phone_code'        => $this->phone_code,
            'name'              => $this->name,
            'translations'      => $this->translations,
        ];
    }
}
