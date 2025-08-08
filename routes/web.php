<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CarWebController;
use App\Http\Controllers\SparePartWebController;
use App\Http\Controllers\GarageWebController;

Route::get('/', function () {
    return redirect('/dashboard');
});

// Dashboard Routes
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/cars', [CarWebController::class, 'index'])->name('cars.index');
Route::delete('/cars/{id}', [CarWebController::class, 'destroy'])->name('cars.destroy');

Route::get('/spare-parts', [SparePartWebController::class, 'index'])->name('spare-parts.index');
Route::delete('/spare-parts/{id}', [SparePartWebController::class, 'destroy'])->name('spare-parts.destroy');

Route::get('/garages', [GarageWebController::class, 'index'])->name('garages.index');
Route::delete('/garages/{id}', [GarageWebController::class, 'destroy'])->name('garages.destroy');

// Test routes to verify cars is working
Route::get('/test-cars', function() {
    return 'Cars route is working!';
});

Route::get('/test-cars-route', function() {
    return 'Route name: ' . route('cars.index');
});
