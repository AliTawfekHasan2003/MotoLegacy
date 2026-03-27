<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CarResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'name'        => $this->name,
            'description' => $this->description,
            'image'       => $this->image,
            'type'        => $this->type,
            'year' => $this->year,
            'color' => $this->color,
            'fuel_type' => $this->fuel_type,
            'transmission' => $this->transmission,
            'doors' => $this->doors,
            'seats' => $this->seats,
            'previous_owners_count' => $this->previous_owners_count,
            'brand' => $this->brand,
            'registration_country' => $this->registration_country,
            'engine_year' => $this->engine_year,
            'cylinders_count' => $this->cylinders_count,
            'drive_system' => $this->drive_system,
            'plate_number' => $this->plate_number,
            'fuel_consumption' => $this->fuel_consumption,
            'warranty' => $this->warranty,
            'warranty_duration' => $this->warranty_duration,
            'status' => $this->status,
            'purchase_price' => $this->purchase_price,
            'rental_price_per_day' => $this->rental_price_per_day,
            'air_conditioning' => $this->air_conditioning,
            'airbags' => $this->airbags,
            'rear_camera' => $this->rear_camera,
            'bluetooth' => $this->bluetooth,
            'sunroof' => $this->sunroof,
            'approval_status' => $this->approval_status,
            'owner' => new UserResource($this->whenLoaded('owner')),
            'category' => new CategoryResource($this->whenLoaded('category')),
            'created_at' => $this->created_at,
        ];
    }
}
