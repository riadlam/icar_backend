<?php

namespace App\Http\Controllers;

use App\Models\Car;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class CarWebController extends Controller
{
    public function index()
    {
        try {
            // Use the existing API endpoint to get cars data
            $response = Http::get(url('/api/cars'));
            
            if ($response->successful()) {
                $cars = collect($response->json());
            } else {
                // Fallback to direct database query if API fails
                $cars = Car::with(['user.carProfile'])
                    ->where('enabled', true)
                    ->orderBy('created_at', 'desc')
                    ->get()
                    ->map(function($car) {
                        return [
                            'id' => $car->id,
                            'brand' => $car->brand,
                            'model' => $car->model,
                            'type' => $car->type,
                            'price' => $car->price,
                            'mileage' => $car->mileage,
                            'year' => $car->year,
                            'transmission' => $car->transmission,
                            'fuel' => $car->fuel,
                            'description' => $car->description,
                            'images' => $car->images,
                            'enabled' => $car->enabled,
                            'created_at' => $car->created_at,
                            'updated_at' => $car->updated_at,
                            'seller_name' => $car->user->carProfile->full_name ?? $car->user->name ?? 'Unknown',
                            'seller_phone' => $car->user->carProfile->mobile ?? 'N/A',
                            'seller_city' => $car->user->carProfile->city ?? 'N/A',
                        ];
                    });
            }
        } catch (\Exception $e) {
            // Fallback to direct database query if there's any error
            $cars = Car::with(['user.carProfile'])
                ->where('enabled', true)
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function($car) {
                    return [
                        'id' => $car->id,
                        'brand' => $car->brand,
                        'model' => $car->model,
                        'type' => $car->type,
                        'price' => $car->price,
                        'mileage' => $car->mileage,
                        'year' => $car->year,
                        'transmission' => $car->transmission,
                        'fuel' => $car->fuel,
                        'description' => $car->description,
                        'images' => $car->images,
                        'enabled' => $car->enabled,
                        'created_at' => $car->created_at,
                        'updated_at' => $car->updated_at,
                        'seller_name' => $car->user->carProfile->full_name ?? $car->user->name ?? 'Unknown',
                        'seller_phone' => $car->user->carProfile->mobile ?? 'N/A',
                        'seller_city' => $car->user->carProfile->city ?? 'N/A',
                    ];
                });
        }

        return view('cars.index', compact('cars'));
    }
}
