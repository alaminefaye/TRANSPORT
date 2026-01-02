<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoyaltyPointEarning extends Model
{
    protected $fillable = [
        'client_id',
        'ticket_id',
        'from_stop_id',
        'earned_date',
        'points',
    ];

    protected $casts = [
        'earned_date' => 'date',
        'points' => 'integer',
    ];

    /**
     * Relation avec le client
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Relation avec le ticket
     */
    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }

    /**
     * Relation avec l'arrêt de montée
     */
    public function fromStop(): BelongsTo
    {
        return $this->belongsTo(Stop::class, 'from_stop_id');
    }
}
