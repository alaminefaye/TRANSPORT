<?php

namespace App\Http\Controllers;

use App\Models\Route;
use App\Models\Stop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RouteController extends Controller
{
    public function index(Request $request)
    {
        $query = Route::with('stops');

        // Filtre par numéro de route
        if ($request->has('route_number') && !empty(trim($request->route_number))) {
            $query->where('route_number', 'like', '%' . trim($request->route_number) . '%');
        }

        // Filtre par ville de départ
        if ($request->filled('departure_city_id')) {
            $query->where('departure_city_id', $request->departure_city_id);
        }

        // Filtre par ville d'arrivée
        if ($request->filled('arrival_city_id')) {
            $query->where('arrival_city_id', $request->arrival_city_id);
        }

        // Filtre par statut (actif/inactif)
        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active == '1');
        }

        $routes = $query->latest()->paginate(10)->withQueryString();

        // Charger les données pour les filtres
        try {
            $villes = \App\Models\Ville::where('is_active', true)->orderBy('name')->get();
        } catch (\Exception $e) {
            $villes = collect([]);
        }

        return view('routes.index', compact('routes', 'villes'));
    }

    public function create()
    {
        $stops = Stop::orderBy('city')->orderBy('name')->get();
        try {
            $villes = \App\Models\Ville::where('is_active', true)->orderBy('name')->get();
        } catch (\Exception $e) {
            // Si la table villes n'existe pas encore, retourner une collection vide
            // L'utilisateur devra d'abord créer des villes
            $villes = collect([]);
        }
        return view('routes.create', compact('stops', 'villes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'route_number' => 'required|integer|min:1|unique:routes,route_number',
            'departure_city_id' => 'required|exists:villes,id',
            'arrival_city_id' => 'required|exists:villes,id|different:departure_city_id',
            'distance' => 'required|numeric|min:0',
            'estimated_duration' => 'required|integer|min:0',
            'is_active' => 'boolean',
            'stops' => 'required|array|min:2',
            'stops.*.stop_id' => 'required|exists:stops,id',
            'stops.*.order' => 'required|integer|min:1',
            'stops.*.estimated_time' => 'nullable|date_format:H:i',
        ]);

        DB::transaction(function () use ($validated, $request) {
            $departureCity = \App\Models\Ville::find($validated['departure_city_id']);
            $arrivalCity = \App\Models\Ville::find($validated['arrival_city_id']);
            
            $route = Route::create([
                'route_number' => $validated['route_number'],
                'departure_city' => $departureCity->name,
                'arrival_city' => $arrivalCity->name,
                'departure_city_id' => $validated['departure_city_id'],
                'arrival_city_id' => $validated['arrival_city_id'],
                'distance' => $validated['distance'],
                'estimated_duration' => $validated['estimated_duration'],
                'is_active' => $request->has('is_active'),
            ]);

            // Attacher les arrêts avec leur ordre
            foreach ($validated['stops'] as $stopData) {
                $route->stops()->attach($stopData['stop_id'], [
                    'order' => $stopData['order'],
                    'estimated_time' => $stopData['estimated_time'] ?? null,
                ]);
            }
        });

        return redirect()->route('routes.index')
            ->with('success', 'Route créée avec succès.');
    }

    public function show(Route $route)
    {
        $route->load(['stops', 'trips.bus', 'prices']);
        return view('routes.show', compact('route'));
    }

    public function edit(Route $route)
    {
        $stops = Stop::orderBy('city')->orderBy('name')->get();
        try {
            $villes = \App\Models\Ville::where('is_active', true)->orderBy('name')->get();
        } catch (\Exception $e) {
            // Si la table villes n'existe pas encore, retourner une collection vide
            $villes = collect([]);
        }
        $route->load('stops');
        return view('routes.edit', compact('route', 'stops', 'villes'));
    }

    public function update(Request $request, Route $route)
    {
        $validated = $request->validate([
            'route_number' => 'required|integer|min:1|unique:routes,route_number,' . $route->id,
            'departure_city_id' => 'required|exists:villes,id',
            'arrival_city_id' => 'required|exists:villes,id|different:departure_city_id',
            'distance' => 'required|numeric|min:0',
            'estimated_duration' => 'required|integer|min:0',
            'is_active' => 'boolean',
            'stops' => 'required|array|min:2',
            'stops.*.stop_id' => 'required|exists:stops,id',
            'stops.*.order' => 'required|integer|min:1',
            'stops.*.estimated_time' => 'nullable|date_format:H:i',
        ]);

        DB::transaction(function () use ($validated, $request, $route) {
            $departureCity = \App\Models\Ville::find($validated['departure_city_id']);
            $arrivalCity = \App\Models\Ville::find($validated['arrival_city_id']);
            
            $route->update([
                'route_number' => $validated['route_number'],
                'departure_city' => $departureCity->name,
                'arrival_city' => $arrivalCity->name,
                'departure_city_id' => $validated['departure_city_id'],
                'arrival_city_id' => $validated['arrival_city_id'],
                'distance' => $validated['distance'],
                'estimated_duration' => $validated['estimated_duration'],
                'is_active' => $request->has('is_active'),
            ]);

            // Synchroniser les arrêts
            $syncData = [];
            foreach ($validated['stops'] as $stopData) {
                $syncData[$stopData['stop_id']] = [
                    'order' => $stopData['order'],
                    'estimated_time' => $stopData['estimated_time'] ?? null,
                ];
            }
            $route->stops()->sync($syncData);
        });

        return redirect()->route('routes.index')
            ->with('success', 'Route modifiée avec succès.');
    }

    public function destroy(Route $route)
    {
        // Vérifier si la route a des voyages
        if ($route->trips()->count() > 0) {
            return redirect()->route('routes.index')
                ->with('error', 'Impossible de supprimer cette route car elle a des voyages associés.');
        }

        $route->delete();

        return redirect()->route('routes.index')
            ->with('success', 'Route supprimée avec succès.');
    }
}
