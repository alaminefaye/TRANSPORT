<?php

namespace App\Services;

use App\Models\Client;
use App\Models\Ticket;
use App\Models\LoyaltyPointEarning;
use Carbon\Carbon;

class LoyaltyService
{
    /**
     * Attribue des points de fidélité à un client lors de l'achat d'un ticket
     * Règles :
     * - 1 point par jour maximum par arrêt de montée
     * - Si l'arrêt de montée est différent, on peut gagner un autre point le même jour
     */
    public function awardPointsForTicket(Client $client, Ticket $ticket): bool
    {
        $today = Carbon::today();
        $fromStopId = $ticket->from_stop_id;
        
        // Vérifier si le client a déjà gagné un point aujourd'hui pour cet arrêt de montée
        $existingEarningForThisStop = LoyaltyPointEarning::where('client_id', $client->id)
            ->whereDate('earned_date', $today)
            ->where('from_stop_id', $fromStopId)
            ->first();
        
        // Si le client a déjà gagné un point aujourd'hui pour cet arrêt, ne pas en donner un autre
        if ($existingEarningForThisStop) {
            return false;
        }
        
        // Si c'est un arrêt différent de ceux déjà utilisés aujourd'hui, on peut donner un point
        // Vérifier s'il y a déjà un point gagné aujourd'hui pour un autre arrêt
        $existingEarningForOtherStop = LoyaltyPointEarning::where('client_id', $client->id)
            ->whereDate('earned_date', $today)
            ->where('from_stop_id', '!=', $fromStopId)
            ->first();
        
        // Si c'est un arrêt différent, on peut donner un point
        // (même s'il y a déjà un point pour un autre arrêt)
        if ($existingEarningForOtherStop || !$existingEarningForThisStop) {
            // Donner le point pour ce nouvel arrêt
            $this->createPointEarning($client, $ticket, $fromStopId);
            return true;
        }
        
        return false;
    }
    
    /**
     * Crée un enregistrement de point gagné
     */
    private function createPointEarning(Client $client, Ticket $ticket, int $fromStopId): void
    {
        LoyaltyPointEarning::create([
            'client_id' => $client->id,
            'ticket_id' => $ticket->id,
            'from_stop_id' => $fromStopId,
            'earned_date' => Carbon::today(),
            'points' => 1,
        ]);
        
        // Incrémenter les points du client
        $client->increment('loyalty_points');
    }
    
    /**
     * Vérifie si un client peut utiliser ses points pour un voyage gratuit
     */
    public function canUseFreeTicket(Client $client): bool
    {
        return $client->loyalty_points >= 10;
    }
    
    /**
     * Utilise 10 points pour un voyage gratuit
     */
    public function useFreeTicket(Client $client): bool
    {
        if (!$this->canUseFreeTicket($client)) {
            return false;
        }
        
        $client->decrement('loyalty_points', 10);
        return true;
    }
    
    /**
     * Récupère le nombre de points d'un client
     */
    public function getClientPoints(Client $client): int
    {
        return $client->loyalty_points ?? 0;
    }
}

