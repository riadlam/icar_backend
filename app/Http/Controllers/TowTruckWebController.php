<?php

namespace App\Http\Controllers;

use App\Models\TowTruckProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class TowTruckWebController extends Controller
{
    public function index()
    {
        try {
            // Use the existing API endpoint to get tow truck profiles data
            $response = Http::get(url('/api/tow-truck-profiles/all'));
            
            if ($response->successful()) {
                $towTrucks = collect($response->json()['data']);
            } else {
                // Fallback to direct database query if API fails
                $towTrucks = TowTruckProfile::with('user')
                    ->orderBy('created_at', 'desc')
                    ->get()
                    ->map(function($towTruck) {
                        return [
                            'id' => $towTruck->id,
                            'business_name' => $towTruck->business_name,
                            'driver_name' => $towTruck->driver_name,
                            'mobile' => $towTruck->mobile,
                            'city' => $towTruck->city,
                            'created_at' => $towTruck->created_at,
                            'updated_at' => $towTruck->updated_at,
                        ];
                    });
            }
        } catch (\Exception $e) {
            // Fallback to direct database query if there's any error
            $towTrucks = TowTruckProfile::with('user')
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function($towTruck) {
                    return [
                        'id' => $towTruck->id,
                        'business_name' => $towTruck->business_name,
                        'driver_name' => $towTruck->driver_name,
                        'mobile' => $towTruck->mobile,
                        'city' => $towTruck->city,
                        'created_at' => $towTruck->created_at,
                        'updated_at' => $towTruck->updated_at,
                    ];
                });
        }

        return view('tow-trucks.index', compact('towTrucks'));
    }

    public function destroy($id)
    {
        try {
            $towTruck = TowTruckProfile::find($id);
            
            if (!$towTruck) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tow truck profile not found.'
                ], 404);
            }

            $towTruck->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Tow truck profile deleted successfully.'
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error deleting tow truck profile: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete tow truck profile.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
