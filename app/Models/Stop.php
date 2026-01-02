<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Stop extends Model
{
    protected $fillable = [
        'name',
        'city',
        'type',
        'address',
    ];

    public function routes(): BelongsToMany
    {
        return $this->belongsToMany(Route::class, 'route_stops')
            ->withPivot('order', 'estimated_time')
            ->withTimestamps()
            ->orderByPivot('order');
    }
}
