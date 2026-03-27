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
            'name'              => $this->name, 
            'email'             => $this->email, 
            'phone'             => $this->phone, 
            'is_active'         => $this->is_active,
            'birth_date'        => $this->birth_date,
            'address'           => $this->address,
            'license_number'    => $this->license_number,
            'license_expiry_date' => $this->license_expiry_date,
            'business_type'     => $this->business_type,
            'role_id'           => $this->role_id,
            'role_name'         => $this->roleModel?->name,
            'created_at'        => $this->created_at,
            'updated_at'        => $this->updated_at,
        ];
    }
}
