<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\Trip;
use App\Models\Bus;
use App\Models\Route;
use App\Models\Parcel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // Statistiques générales
        $stats = [
            'total_tickets' => Ticket::where('status', '!=', 'Annulé')->count(),
            'today_tickets' => Ticket::where('status', '!=', 'Annulé')
                ->whereDate('created_at', today())
                ->count(),
            // Recettes calculées à partir des tickets (prix des tickets non annulés)
            'total_revenue' => Ticket::where('status', '!=', 'Annulé')->sum('price'),
            'today_revenue' => Ticket::where('status', '!=', 'Annulé')
                ->whereDate('created_at', today())
                ->sum('price'),
            // Statistiques des colis
            'parcels_created' => Parcel::count(), // Tous les colis créés
            'parcels_retrieved' => Parcel::where('status', 'Récupéré')->count(),
            'parcels_amount' => Parcel::sum('amount'),
        ];

        // Voyages du jour avec taux de remplissage
        $todayTrips = Trip::with(['route', 'bus'])
            ->withCount(['tickets' => function ($query) {
                $query->where('status', '!=', 'Annulé');
            }])
            ->whereDate('departure_time', today())
            ->orderBy('departure_time')
            ->get()
            ->map(function ($trip) {
                $soldTickets = $trip->tickets_count;
                $capacity = $trip->bus->capacity ?? 0;
                $trip->fill_rate = $capacity > 0 ? round(($soldTickets / $capacity) * 100, 1) : 0;
                $trip->sold_tickets = $soldTickets;
                return $trip;
            });

        // Statistiques par trajet (derniers 30 jours)
        $routeStats = Route::withCount(['trips' => function ($query) {
                $query->where('created_at', '>=', Carbon::now()->subDays(30));
            }])
            ->with(['trips' => function ($query) {
                $query->where('created_at', '>=', Carbon::now()->subDays(30));
            }])
            ->having('trips_count', '>', 0)
            ->orderBy('trips_count', 'desc')
            ->limit(5)
            ->get();

        // Chiffre d'affaires mensuel des 12 derniers mois (tickets uniquement)
        $monthlyRevenue = [];
        $monthlyLabels = [];
        
        for ($i = 11; $i >= 0; $i--) {
            $monthStart = Carbon::now()->subMonths($i)->startOfMonth();
            $monthEnd = Carbon::now()->subMonths($i)->endOfMonth();
            
            $revenue = Ticket::where('status', '!=', 'Annulé')
                ->whereBetween('created_at', [$monthStart, $monthEnd])
                ->sum('price');
            
            $monthlyRevenue[] = (float) $revenue;
            $monthlyLabels[] = $monthStart->format('M Y');
        }

        return view('dashboard', compact('stats', 'todayTrips', 'routeStats', 'monthlyRevenue', 'monthlyLabels'));
    }

}
