<?php

namespace App\Http\Controllers;

use App\Models\RouteStopPrice;
use App\Models\Route;
use Illuminate\Http\Request;

class RouteStopPriceController extends Controller
{
    public function index(Request $request)
    {
        $query = RouteStopPrice::with(['route', 'fromStop', 'toStop']);

        // Filtre par route
        if ($request->filled('route_id')) {
            $query->where('route_id', $request->route_id);
        }

        // Filtre par arrêt de départ
        if ($request->filled('from_stop_id')) {
            $query->where('from_stop_id', $request->from_stop_id);
        }

        // Filtre par arrêt d'arrivée
        if ($request->filled('to_stop_id')) {
            $query->where('to_stop_id', $request->to_stop_id);
        }

        // Filtre par statut (actif/inactif)
        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active == '1');
        }

        $prices = $query->latest()->paginate(10)->withQueryString();

        // Charger les données pour les filtres
        $routes = Route::where('is_active', true)->get();
        $stops = \App\Models\Stop::all();

        return view('route-stop-prices.index', compact('prices', 'routes', 'stops'));
    }

    public function create()
    {
        $routes = Route::where('is_active', true)->with('stops')->get();
        return view('route-stop-prices.create', compact('routes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'route_id' => 'nullable|exists:routes,id',
            'from_stop_id' => 'required|exists:stops,id',
            'to_stop_id' => 'required|exists:stops,id|different:from_stop_id',
            'price' => 'required|numeric|min:0',
            'is_active' => 'boolean',
            'is_global' => 'boolean',
        ]);

        // Si c'est un prix global, route_id doit être null
        $routeId = $request->has('is_global') && $request->is_global ? null : $validated['route_id'];

        // Vérifier l'unicité
        $query = RouteStopPrice::where('from_stop_id', $validated['from_stop_id'])
            ->where('to_stop_id', $validated['to_stop_id']);
        
        if ($routeId) {
            $query->where('route_id', $routeId);
        } else {
            $query->whereNull('route_id');
        }
        
        if ($query->exists()) {
            return back()->withInput()->with('error', 'Un tarif existe déjà pour ce segment.');
        }

        RouteStopPrice::create([
            'route_id' => $routeId,
            'from_stop_id' => $validated['from_stop_id'],
            'to_stop_id' => $validated['to_stop_id'],
            'price' => $validated['price'],
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('route-stop-prices.index')
            ->with('success', 'Tarif créé avec succès.');
    }

    public function edit(RouteStopPrice $routeStopPrice)
    {
        $routeStopPrice->load(['route.stops', 'fromStop', 'toStop']);
        $routes = Route::where('is_active', true)->with('stops')->get();
        return view('route-stop-prices.edit', compact('routeStopPrice', 'routes'));
    }

    public function update(Request $request, RouteStopPrice $routeStopPrice)
    {
        $validated = $request->validate([
            'route_id' => 'nullable|exists:routes,id',
            'from_stop_id' => 'required|exists:stops,id',
            'to_stop_id' => 'required|exists:stops,id|different:from_stop_id',
            'price' => 'required|numeric|min:0',
            'is_active' => 'boolean',
            'is_global' => 'boolean',
        ]);

        // Si c'est un prix global, route_id doit être null
        $routeId = $request->has('is_global') && $request->is_global ? null : $validated['route_id'];

        // Vérifier l'unicité (sauf pour l'enregistrement actuel)
        $query = RouteStopPrice::where('from_stop_id', $validated['from_stop_id'])
            ->where('to_stop_id', $validated['to_stop_id'])
            ->where('id', '!=', $routeStopPrice->id);
        
        if ($routeId) {
            $query->where('route_id', $routeId);
        } else {
            $query->whereNull('route_id');
        }
        
        if ($query->exists()) {
            return back()->withInput()->with('error', 'Un tarif existe déjà pour ce segment.');
        }

        $routeStopPrice->update([
            'route_id' => $routeId,
            'from_stop_id' => $validated['from_stop_id'],
            'to_stop_id' => $validated['to_stop_id'],
            'price' => $validated['price'],
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('route-stop-prices.index')
            ->with('success', 'Tarif modifié avec succès.');
    }

    public function destroy(RouteStopPrice $routeStopPrice)
    {
        $routeStopPrice->delete();

        return redirect()->route('route-stop-prices.index')
            ->with('success', 'Tarif supprimé avec succès.');
    }
}
