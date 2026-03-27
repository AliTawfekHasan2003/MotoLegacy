<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'birth_date',
        'address',
        'license_number',
        'license_expiry_date',
        'business_type',
        'role_id',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'birth_date' => 'date',
        'license_expiry_date' => 'date',
        'is_active' => 'boolean',
    ];

    public function roleModel()
    {
        return $this->belongsTo(\Spatie\Permission\Models\Role::class, 'role_id');
    }

    public function cars()
    {
        return $this->hasMany(Car::class);
    }

    public function purchaseRequests()
    {
        return $this->hasMany(PurchaseRequest::class);
    }

    public function rentalRequests()
    {
        return $this->hasMany(RentalRequest::class);
    }

    public function favorites()
    {
        return $this->belongsToMany(Car::class, 'favorites');
    }

    public function ratingsReceived()
    {
        return $this->hasMany(Rating::class, 'seller_id');
    }

    public function ratingsGiven()
    {
        return $this->hasMany(Rating::class, 'user_id');
    }
}
