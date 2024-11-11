<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
class Reservation extends Model
{
    protected $fillable = [
        'user_id',
        'room_id',
        'status',
        'semester',
        'year',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }

    public function room(): BelongsTo {
        return $this->belongsTo(Room::class);
    }

    public function inspection(): HasOne {
        return $this->hasOne(Inspection::class);
    }
}
