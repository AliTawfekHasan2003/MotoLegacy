<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Mosab\Translation\Database\TranslatableModel;

class Country extends TranslatableModel
{
    use HasFactory;

    protected $fillable = [
        'country_code',
        'phone_code',
    ];

    protected $translatable = [
        'name',
        'nationality',
    ];
}
