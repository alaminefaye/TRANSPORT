<?php

namespace App\Http\Controllers;

use App\Models\Trip;
use App\Models\Route;
use App\Models\Bus;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class TripController extends Controller
{
    public function index(Request $request)
    {
        $query = Trip::with(['route', 'bus', 'driver']);

        // Filtre par route (trajet)
        if ($request->filled('route_id')) {
            $query->where('route_id', $request->route_id);
        }

        // Filtre par bus
        if ($request->filled('bus_id')) {
            $query->where('bus_id', $request->bus_id);
        }

        // Filtre par statut
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filtre par date de départ (de)
        if ($request->filled('date_from')) {
            $query->whereDate('departure_time', '>=', $request->date_from);
        }

        // Filtre par date de départ (à)
        if ($request->filled('date_to')) {
            $query->whereDate('departure_time', '<=', $request->date_to);
        }

        $trips = $query->latest('departure_time')->paginate(10)->withQueryString();

        // Charger les données pour les filtres
        $routes = Route::where('is_active', true)->get();
        $buses = Bus::all();
        $statuses = [
            'Scheduled' => 'Programmé',
            'In Progress' => 'En cours',
            'Completed' => 'Terminé',
            'Cancelled' => 'Annulé'
        ];

        return view('trips.index', compact('trips', 'routes', 'buses', 'statuses'));
    }

    public function create()
    {
        $routes = Route::where('is_active', true)->get();
        // Afficher tous les bus sauf ceux en panne
        // Un bus peut être utilisé pour plusieurs voyages programmés
        $buses = Bus::where('status', '!=', 'En panne')->get();
        $drivers = Schema::hasColumn('users', 'role') 
            ? User::where('role', 'chauffeur')->get()
            : collect([]);
        
        return view('trips.create', compact('routes', 'buses', 'drivers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'route_id' => 'required|exists:routes,id',
            'bus_id' => 'required|exists:buses,id',
            'departure_time' => 'required|date',
            'arrival_time' => 'nullable|date|after:departure_time',
            'driver_id' => 'nullable|exists:users,id',
            'status' => 'required|in:Scheduled,In Progress,Completed,Cancelled',
        ]);

        // Ne pas changer le statut du bus lors de la création
        // Le statut sera changé seulement quand le voyage passe à "In Progress"
        Trip::create($validated);

        return redirect()->route('trips.index')
            ->with('success', 'Voyage créé avec succès.');
    }

    public function show(Trip $trip)
    {
        $trip->load(['route.stops', 'bus', 'driver', 'tickets', 'seatSegments']);
        return view('trips.show', compact('trip'));
    }

    public function edit(Trip $trip)
    {
        $routes = Route::where('is_active', true)->get();
        // Afficher tous les bus sauf ceux en panne
        $buses = Bus::where('status', '!=', 'En panne')->get();
        $drivers = Schema::hasColumn('users', 'role') 
            ? User::where('role', 'chauffeur')->get()
            : collect([]);
        
        return view('trips.edit', compact('trip', 'routes', 'buses', 'drivers'));
    }

    public function update(Request $request, Trip $trip)
    {
        $validated = $request->validate([
            'route_id' => 'required|exists:routes,id',
            'bus_id' => 'required|exists:buses,id',
            'departure_time' => 'required|date',
            'arrival_time' => 'nullable|date|after:departure_time',
            'driver_id' => 'nullable|exists:users,id',
            'status' => 'required|in:Scheduled,In Progress,Completed,Cancelled',
        ]);

        // Gérer le statut du bus selon le statut du voyage
        $bus = Bus::find($validated['bus_id']);
        
        // Libérer le bus si le voyage est terminé ou annulé
        if ($validated['status'] === 'Completed' || $validated['status'] === 'Cancelled') {
            // Vérifier s'il n'y a pas d'autres voyages en cours pour ce bus
            $activeTrips = Trip::where('bus_id', $bus->id)
                ->where('id', '!=', $trip->id)
                ->where('status', 'In Progress')
                ->exists();
            
            if (!$activeTrips) {
                $bus->update(['status' => 'Disponible']);
            }
        } 
        // Mettre le bus en voyage seulement si le voyage passe à "In Progress"
        elseif ($validated['status'] === 'In Progress') {
            $bus->update(['status' => 'En voyage']);
        }

        $trip->update($validated);

        return redirect()->route('trips.index')
            ->with('success', 'Voyage modifié avec succès.');
    }

    public function destroy(Trip $trip)
    {
        // Vérifier si le voyage a des tickets
        if ($trip->tickets()->count() > 0) {
            return redirect()->route('trips.index')
                ->with('error', 'Impossible de supprimer ce voyage car il a des tickets associés.');
        }

        // Libérer le bus seulement s'il n'y a pas d'autres voyages actifs
        $bus = $trip->bus;
        $activeTrips = Trip::where('bus_id', $bus->id)
            ->where('id', '!=', $trip->id)
            ->where('status', 'In Progress')
            ->exists();
        
        if (!$activeTrips) {
            $bus->update(['status' => 'Disponible']);
        }
        
        $trip->delete();

        return redirect()->route('trips.index')
            ->with('success', 'Voyage supprimé avec succès.');
    }
}
