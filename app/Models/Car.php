<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Car extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'category_id', 'type', 'name', 'description', 'year', 'color',
        'fuel_type', 'transmission', 'doors', 'seats', 'previous_owners_count',
        'brand', 'registration_country', 'engine_year', 'cylinders_count',
        'drive_system', 'plate_number', 'fuel_consumption', 'warranty',
        'warranty_duration', 'status', 'purchase_price', 'rental_price_per_day',
        'air_conditioning', 'airbags', 'rear_camera', 'bluetooth', 'sunroof', 'image', 'approval_status'
    ];

    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function purchaseRequests()
    {
        return $this->hasMany(PurchaseRequest::class);
    }

    public function rentalRequests()
    {
        return $this->hasMany(RentalRequest::class);
    }

    public function favoritedBy()
    {
        return $this->belongsToMany(User::class, 'favorites');
    }
}
