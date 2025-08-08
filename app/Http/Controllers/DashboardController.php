<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class DashboardController extends Controller
{
    public function index()
    {
        // Get real counts from existing APIs
        $stats = [
            'total_cars' => 0,
            'total_spare_parts' => 0,
            'total_garages' => 0,
            'total_tow_trucks' => 0,
            'total_users' => 0,
            'available_spare_parts' => 0,
            'active_cities' => 0,
        ];

        try {
            // Get cars count from API
            $carsResponse = Http::get(url('/api/cars'));
            if ($carsResponse->successful()) {
                $stats['total_cars'] = count($carsResponse->json()['data'] ?? []);
            }
        } catch (\Exception $e) {
            // Fallback to 0 if API fails
        }

        try {
            // Get spare parts count from API
            $sparePartsResponse = Http::get(url('/api/spare-parts'));
            if ($sparePartsResponse->successful()) {
                $sparePartsData = $sparePartsResponse->json()['data'] ?? [];
                $stats['total_spare_parts'] = count($sparePartsData);
                $stats['available_spare_parts'] = collect($sparePartsData)->where('is_available', true)->count();
            }
        } catch (\Exception $e) {
            // Fallback to 0 if API fails
        }

        try {
            // Get garages count from API
            $garagesResponse = Http::get(url('/api/garage-profiles/all'));
            if ($garagesResponse->successful()) {
                $stats['total_garages'] = count($garagesResponse->json()['data'] ?? []);
            }
        } catch (\Exception $e) {
            // Fallback to 0 if API fails
        }

        try {
            // Get tow trucks count from API
            $towTrucksResponse = Http::get(url('/api/tow-truck-profiles/all'));
            if ($towTrucksResponse->successful()) {
                $stats['total_tow_trucks'] = count($towTrucksResponse->json()['data'] ?? []);
            }
        } catch (\Exception $e) {
            // Fallback to 0 if API fails
        }

        // For users and cities, we'll use simple counts without complex queries
        try {
            // Simple user count - we'll use a basic approach
            $stats['total_users'] = \App\Models\User::count();
        } catch (\Exception $e) {
            $stats['total_users'] = 0;
        }

        // For active cities, we'll collect from all API responses
        $allCities = collect();
        
        try {
            if (isset($carsResponse) && $carsResponse->successful()) {
                $carsData = $carsResponse->json()['data'] ?? [];
                $allCities = $allCities->merge(collect($carsData)->pluck('city')->filter());
            }
        } catch (\Exception $e) {
            // Skip if fails
        }

        try {
            if (isset($sparePartsResponse) && $sparePartsResponse->successful()) {
                $sparePartsData = $sparePartsResponse->json()['data'] ?? [];
                $allCities = $allCities->merge(collect($sparePartsData)->pluck('city')->filter());
            }
        } catch (\Exception $e) {
            // Skip if fails
        }

        try {
            if (isset($garagesResponse) && $garagesResponse->successful()) {
                $garagesData = $garagesResponse->json()['data'] ?? [];
                $allCities = $allCities->merge(collect($garagesData)->pluck('city')->filter());
            }
        } catch (\Exception $e) {
            // Skip if fails
        }

        try {
            if (isset($towTrucksResponse) && $towTrucksResponse->successful()) {
                $towTrucksData = $towTrucksResponse->json()['data'] ?? [];
                $allCities = $allCities->merge(collect($towTrucksData)->pluck('city')->filter());
            }
        } catch (\Exception $e) {
            // Skip if fails
        }

        $stats['active_cities'] = $allCities->unique()->count();

        return view('dashboard.index', compact('stats'));
    }
}
