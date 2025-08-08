<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CarWebController;
use App\Http\Controllers\SparePartWebController;
use App\Http\Controllers\GarageWebController;
use App\Http\Controllers\TowTruckWebController;
use App\Http\Controllers\AuthController;

// Authentication routes
Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Protected Dashboard Routes
Route::middleware('admin.auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/cars', [CarWebController::class, 'index'])->name('cars.index');
    Route::delete('/cars/{id}', [CarWebController::class, 'destroy'])->name('cars.destroy');

    Route::get('/spare-parts', [SparePartWebController::class, 'index'])->name('spare-parts.index');
    Route::delete('/spare-parts/{id}', [SparePartWebController::class, 'destroy'])->name('spare-parts.destroy');

    Route::get('/garages', [GarageWebController::class, 'index'])->name('garages.index');
    Route::delete('/garages/{id}', [GarageWebController::class, 'destroy'])->name('garages.destroy');

    Route::get('/tow-trucks', [TowTruckWebController::class, 'index'])->name('tow-trucks.index');
    Route::delete('/tow-trucks/{id}', [TowTruckWebController::class, 'destroy'])->name('tow-trucks.destroy');
});

// Test routes to verify cars is working
Route::get('/test-cars', function() {
    return 'Cars route is working!';
});

Route::get('/test-cars-route', function() {
    return 'Route name: ' . route('cars.index');
});
