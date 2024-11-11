<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Room extends Model
{
    protected $fillables = [
        'apartment_id',
        'room_number',
        'is_available',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function apartment(): BelongsTo {
        return $this->belongsTo(Apartment::class);
    }

    public function reservations(): HasMany {
        return $this->hasMany(Reservation::class);
    }
}
