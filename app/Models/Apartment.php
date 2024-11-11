<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Apartment extends Model
{

    protected static function booted()
    {
        static::created(function ($apartment) {
            $numberOfRooms = match($apartment->apartment_type) {
                'standard' => 2,
                'economy' => 3,
                'private' => 1,
                default => 0,
            };

            $roomLetters = range('A', 'Z');

            for ($i = 0; $i < $numberOfRooms; $i++) {
                $apartment->rooms()->create([
                    'room_number' => $roomLetters[$i],
                    'is_available' => true,
                ]);
            }
        });
    }

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
