<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\BusController;
use App\Http\Controllers\RouteController;
use App\Http\Controllers\StopController;
use App\Http\Controllers\TripController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\RouteStopPriceController;
use App\Http\Controllers\VilleController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\ParcelController;
use App\Http\Controllers\DestinationController;
use App\Http\Controllers\ReceptionAgencyController;
use App\Http\Controllers\FuelRecordController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\DiagnosticController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\SettingController;

// Authentication Routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Protected Routes
Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Buses
    Route::resource('buses', BusController::class);

    // Fuel Records
    Route::resource('fuel-records', FuelRecordController::class);

    // Routes
    Route::resource('routes', RouteController::class);

    // Stops - Custom API route must come before resource route
    Route::get('/stops/api', [StopController::class, 'api'])->name('stops.api');
    Route::resource('stops', StopController::class);

    // Villes
    Route::resource('villes', VilleController::class);

    // Trips
    Route::resource('trips', TripController::class);

    // Tickets - Custom routes must come before resource routes
    Route::get('/tickets/calculate-price', [TicketController::class, 'calculatePrice'])->name('tickets.calculate-price');
    Route::get('/tickets/available-seats', [TicketController::class, 'getAvailableSeats'])->name('tickets.available-seats');
    Route::match(['get', 'post'], '/tickets/search/qr', [TicketController::class, 'searchByQrCode'])->name('tickets.search.qr');
    Route::get('/tickets/retrieve', [TicketController::class, 'retrieve'])->name('tickets.retrieve');
    Route::post('/tickets/{ticket}/board', [TicketController::class, 'board'])->name('tickets.board');
    Route::post('/tickets/{ticket}/disembark', [TicketController::class, 'disembark'])->name('tickets.disembark');
    Route::post('/tickets/{ticket}/cancel', [TicketController::class, 'cancel'])->name('tickets.cancel');
    Route::resource('tickets', TicketController::class);

    // Route Stop Prices
    Route::resource('route-stop-prices', RouteStopPriceController::class);

    // Clients
    Route::get('/clients/search-by-phone', [ClientController::class, 'searchByPhone'])->name('clients.search-by-phone');
    Route::resource('clients', ClientController::class)->only(['index', 'show']);

    // Employees
    Route::resource('employees', EmployeeController::class);

    // Parcels (Colis)
    Route::get('/parcels/retrieved', [ParcelController::class, 'retrieved'])->name('parcels.retrieved');
    Route::post('/parcels/{parcel}/mark-retrieved', [ParcelController::class, 'markAsRetrieved'])->name('parcels.mark-retrieved');
    Route::resource('parcels', ParcelController::class);

    // Destinations
    Route::resource('destinations', DestinationController::class);

    // Reception Agencies
    Route::resource('reception-agencies', ReceptionAgencyController::class);

    // Expenses
    Route::resource('expenses', ExpenseController::class);
    
    // Diagnostic
    Route::get('/diagnostic/route-prices', [DiagnosticController::class, 'showRouteDiagnostic'])->name('diagnostic.route-prices');
    
    // Test permissions
    Route::get('/test-permissions', function () {
        return view('test-permissions');
    })->name('test.permissions');
    
    // Debug permissions
    Route::get('/debug-permissions', [\App\Http\Controllers\DebugController::class, 'permissions'])->name('debug.permissions');
    
    // User Management
    Route::resource('users', UserController::class);
    
    // Role Management
    Route::resource('roles', RoleController::class);
    
    // Permission Management
    Route::resource('permissions', PermissionController::class)->only(['index', 'show']);
    
    // Settings
    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
    Route::put('/settings', [SettingController::class, 'update'])->name('settings.update');
});
