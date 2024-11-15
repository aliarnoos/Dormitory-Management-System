<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Deposit extends Model
{

    protected static function booted()
    {
        static::created(function ($deposit) {
            // Update the `has_deposit` column to true for the associated user
            $deposit->user()->update(['has_deposit' => true]);
        });
    }
    
    protected $fillable = [
        'user_id',
        'amount',
        'date',
        'status',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
