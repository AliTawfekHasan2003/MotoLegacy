<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RentalRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'car_id', 'start_date', 'end_date', 'total_price',
        'pickup_location', 'return_location', 'notes', 'status', 'id_number', 'payment_method'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function car()
    {
        return $this->belongsTo(Car::class);
    }
}
