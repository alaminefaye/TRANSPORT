<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

class Ticket extends Model
{
    protected $fillable = [
        'ticket_number',
        'trip_id',
        'from_stop_id',
        'to_stop_id',
        'seat_number',
        'passenger_id',
        'client_id',
        'passenger_name',
        'passenger_phone',
        'price',
        'status',
        'boarding_time',
        'disembarkation_time',
        'qr_code',
        'sold_by',
    ];

    protected $casts = [
        'seat_number' => 'integer',
        'price' => 'decimal:2',
        'boarding_time' => 'datetime',
        'disembarkation_time' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($ticket) {
            if (empty($ticket->ticket_number)) {
                $ticket->ticket_number = 'TKT-' . strtoupper(Str::random(8));
            }
            if (empty($ticket->qr_code)) {
                $ticket->qr_code = 'QR-' . strtoupper(Str::random(12));
            }
        });
    }

    public function trip(): BelongsTo
    {
        return $this->belongsTo(Trip::class);
    }

    public function fromStop(): BelongsTo
    {
        return $this->belongsTo(Stop::class, 'from_stop_id');
    }

    public function toStop(): BelongsTo
    {
        return $this->belongsTo(Stop::class, 'to_stop_id');
    }

    public function passenger(): BelongsTo
    {
        return $this->belongsTo(User::class, 'passenger_id');
    }

    public function soldBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sold_by');
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }

    public function seatSegments()
    {
        return $this->hasMany(SeatSegment::class);
    }
}
