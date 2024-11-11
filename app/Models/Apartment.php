<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Apartment extends Model
{
    protected $fillable = [
        'floor', 
        'number',
        'gender',
        'apartment_type',
    ]; 

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function rooms(): HasMany {
        return $this->hasMany(Room::class);
    }

}
