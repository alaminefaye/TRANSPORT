<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Bus extends Model
{
    protected $fillable = [
        'immatriculation',
        'capacity',
        'type',
        'status',
        'notes',
        'seat_layout',
    ];

    protected $casts = [
        'capacity' => 'integer',
        'seat_layout' => 'array',
    ];

    public function trips(): HasMany
    {
        return $this->hasMany(Trip::class);
    }

    public function fuelRecords(): HasMany
    {
        return $this->hasMany(FuelRecord::class);
    }
}
