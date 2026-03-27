<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PurchaseRequestResource extends JsonResource
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
            'offered_price' => $this->offered_price,
            'meeting_location' => $this->meeting_location,
            'meeting_date' => $this->meeting_date,
            'notes' => $this->notes,
            'status' => $this->status,
            'id_number' => $this->id_number,
            'payment_method' => $this->payment_method,
            'created_at' => $this->created_at,
        ];
    }
}
