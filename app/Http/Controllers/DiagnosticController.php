<?php

namespace App\Http\Controllers;

use App\Models\Route;
use App\Models\RouteStop;
use App\Models\RouteStopPrice;
use App\Models\Trip;
use App\Models\Stop;
use Illuminate\Http\Request;

class DiagnosticController extends Controller
{
    /**
     * Affiche les informations de diagnostic pour une route
     */
    public function showRouteDiagnostic(Request $request)
    {
        $routes = Route::where('is_active', true)->get();
        
        $routeId = $request->input('route_id');
        $diagnostic = null;
        
        if ($routeId) {
            $route = Route::with(['routeStops.stop', 'prices.fromStop', 'prices.toStop'])
                ->findOrFail($routeId);
            
            // Récupérer les arrêts de la route dans l'ordre
            $routeStops = $route->routeStops()->orderBy('order')->get();
            
            // Récupérer tous les prix définis pour cette route
            $prices = $route->prices()->where('is_active', true)->get();
            
            // Vérifier les segments consécutifs manquants
            $missingSegments = [];
            for ($i = 0; $i < $routeStops->count() - 1; $i++) {
                $currentStop = $routeStops[$i];
                $nextStop = $routeStops[$i + 1];
                
                $priceExists = RouteStopPrice::where('route_id', $route->id)
                    ->where('from_stop_id', $currentStop->stop_id)
                    ->where('to_stop_id', $nextStop->stop_id)
                    ->where('is_active', true)
                    ->exists();
                
                if (!$priceExists) {
                    $missingSegments[] = [
                        'from_stop_id' => $currentStop->stop_id,
                        'from_stop_name' => $currentStop->stop->name ?? 'N/A',
                        'from_stop_city' => $currentStop->stop->city ?? 'N/A',
                        'to_stop_id' => $nextStop->stop_id,
                        'to_stop_name' => $nextStop->stop->name ?? 'N/A',
                        'to_stop_city' => $nextStop->stop->city ?? 'N/A',
                        'order' => ($i + 1) . ' → ' . ($i + 2)
                    ];
                }
            }
            
            $diagnostic = [
                'route' => $route,
                'route_stops' => $routeStops,
                'prices' => $prices,
                'missing_segments' => $missingSegments,
                'total_stops' => $routeStops->count(),
                'total_prices' => $prices->count(),
                'expected_segments' => $routeStops->count() - 1
            ];
        }
        
        return view('diagnostic.route-prices', compact('routes', 'diagnostic', 'routeId'));
    }
}


