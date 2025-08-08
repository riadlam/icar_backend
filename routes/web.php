<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CarWebController;

Route::get('/', function () {
    return redirect('/dashboard');
});

// Dashboard Routes
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/cars', [CarWebController::class, 'index'])->name('cars.index');

// Test routes to verify cars is working
Route::get('/test-cars', function() {
    return 'Cars route is working!';
});

Route::get('/test-cars-route', function() {
    return 'Route name: ' . route('cars.index');
});
