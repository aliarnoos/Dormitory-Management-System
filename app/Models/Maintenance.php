<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Maintenance extends Model
{
    protected $fillable = [
        'reservation_id',
        'type',
        'description',
        'status'
    ];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];
    public function reservation(): BelongsTo {
        return $this->belongsTo(Reservation::class);
    }
}
