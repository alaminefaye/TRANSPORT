<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Client extends Model
{
    protected $fillable = [
        'name',
        'phone',
        'email',
        'loyalty_points',
    ];

    /**
     * Relation avec les tickets
     */
    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }

    /**
     * Relation avec les colis en tant qu'expéditeur
     */
    public function sentParcels(): HasMany
    {
        return $this->hasMany(Parcel::class, 'sender_client_id');
    }

    /**
     * Relation avec les colis en tant que bénéficiaire
     */
    public function receivedParcels(): HasMany
    {
        return $this->hasMany(Parcel::class, 'recipient_client_id');
    }

    /**
     * Relation avec les points de fidélité gagnés
     */
    public function loyaltyPointEarnings(): HasMany
    {
        return $this->hasMany(LoyaltyPointEarning::class);
    }
    
    /**
     * Vérifie si le client peut utiliser un voyage gratuit
     */
    public function canUseFreeTicket(): bool
    {
        return ($this->loyalty_points ?? 0) >= 10;
    }
}
