<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RentalRequestResource extends JsonResource
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
            'user' => new UserResource($this->whenLoaded('user')),
            'car' => new CarResource($this->whenLoaded('car')),
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'total_price' => $this->total_price,
            'pickup_location' => $this->pickup_location,
            'return_location' => $this->return_location,
            'notes' => $this->notes,
            'status' => $this->status,
            'id_number' => $this->id_number,
            'payment_method' => $this->payment_method,
            'created_at' => $this->created_at,
        ];
    }
}
