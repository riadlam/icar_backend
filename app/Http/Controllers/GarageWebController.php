<?php

namespace App\Http\Controllers;

use App\Models\GarageProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class GarageWebController extends Controller
{
    public function index()
    {
        try {
            // Use the existing API endpoint to get garage profiles data
            $response = Http::get(url('/api/garage-profiles/all'));
            
            if ($response->successful()) {
                $garages = collect($response->json()['data']);
            } else {
                // Fallback to direct database query if API fails
                $garages = GarageProfile::with('user')
                    ->orderBy('created_at', 'desc')
                    ->get()
                    ->map(function($garage) {
                        return [
                            'id' => $garage->id,
                            'business_name' => $garage->business_name,
                            'mechanic_name' => $garage->mechanic_name,
                            'mobile' => $garage->mobile,
                            'city' => $garage->city,
                            'services' => $garage->services,
                            'created_at' => $garage->created_at,
                            'updated_at' => $garage->updated_at,
                        ];
                    });
            }
        } catch (\Exception $e) {
            // Fallback to direct database query if there's any error
            $garages = GarageProfile::with('user')
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function($garage) {
                    return [
                        'id' => $garage->id,
                        'business_name' => $garage->business_name,
                        'mechanic_name' => $garage->mechanic_name,
                        'mobile' => $garage->mobile,
                        'city' => $garage->city,
                        'services' => $garage->services,
                        'created_at' => $garage->created_at,
                        'updated_at' => $garage->updated_at,
                    ];
                });
        }

        return view('garages.index', compact('garages'));
    }

    public function destroy($id)
    {
        try {
            $garage = GarageProfile::find($id);
            
            if (!$garage) {
                return response()->json([
                    'success' => false,
                    'message' => 'Garage profile not found.'
                ], 404);
            }

            $garage->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Garage profile deleted successfully.'
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error deleting garage profile: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete garage profile.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
