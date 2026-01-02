<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Route extends Model
{
    protected $fillable = [
        'route_number',
        'departure_city',
        'arrival_city',
        'departure_city_id',
        'arrival_city_id',
        'distance',
        'estimated_duration',
        'is_active',
    ];

    protected $casts = [
        'distance' => 'decimal:2',
        'estimated_duration' => 'integer',
        'is_active' => 'boolean',
    ];

    public function stops(): BelongsToMany
    {
        return $this->belongsToMany(Stop::class, 'route_stops')
            ->withPivot('order', 'estimated_time')
            ->withTimestamps()
            ->orderByPivot('order');
    }

    public function routeStops(): HasMany
    {
        return $this->hasMany(RouteStop::class)->orderBy('order');
    }

    public function trips(): HasMany
    {
        return $this->hasMany(Trip::class);
    }

    public function prices(): HasMany
    {
        return $this->hasMany(RouteStopPrice::class);
    }

    public function departureCity(): BelongsTo
    {
        return $this->belongsTo(Ville::class, 'departure_city_id');
    }

    public function arrivalCity(): BelongsTo
    {
        return $this->belongsTo(Ville::class, 'arrival_city_id');
    }
}
