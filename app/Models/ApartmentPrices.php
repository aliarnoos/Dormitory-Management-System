<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApartmentPrices extends Model
{
    
    protected $fillable = [
        'apartment_type',
        'ug_semester_price',
        'app_semester_price',
        'winter_price',
        'summer_price',
    ];
}
