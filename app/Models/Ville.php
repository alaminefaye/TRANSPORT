<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ville extends Model
{
    protected $fillable = [
        'name',
        'code',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function departureRoutes(): HasMany
    {
        return $this->hasMany(Route::class, 'departure_city_id');
    }

    public function arrivalRoutes(): HasMany
    {
        return $this->hasMany(Route::class, 'arrival_city_id');
    }
}
