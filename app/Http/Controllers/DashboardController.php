<?php

namespace App\Http\Controllers;

use App\Models\Car;
use App\Models\SparePartPost;
use App\Models\GarageProfile;
use App\Models\TowTruckProfile;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Get real counts from all management sections
        $stats = [
            'total_cars' => Car::count(),
            'total_spare_parts' => SparePartPost::count(),
            'total_garages' => GarageProfile::count(),
            'total_tow_trucks' => TowTruckProfile::count(),
            'total_users' => User::count(),
            'available_spare_parts' => SparePartPost::where('is_available', true)->count(),
            'active_cities' => collect([
                Car::distinct('city')->pluck('city'),
                SparePartPost::join('users', 'spare_part_posts.user_id', '=', 'users.id')
                    ->join('spare_parts_profiles', 'users.id', '=', 'spare_parts_profiles.user_id')
                    ->distinct('spare_parts_profiles.city')->pluck('spare_parts_profiles.city'),
                GarageProfile::distinct('city')->pluck('city'),
                TowTruckProfile::distinct('city')->pluck('city')
            ])->flatten()->unique()->count(),
        ];

        return view('dashboard.index', compact('stats'));
    }
}
