<?php

namespace App\Http\Controllers\API;

use App\Models\CarProfile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CarProfileController extends Controller
{
    /**
     * Get the authenticated user's car profile information
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getMyCarProfile()
    {
        try {
            $user = Auth::user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }
            
            $profile = CarProfile::where('user_id', $user->id)->first();
            
            if (!$profile) {
                return response()->json([
                    'success' => false,
                    'message' => 'Car profile not found for this user'
                ], 404);
            }
            
            return response()->json([
                'success' => true,
                'data' => [
                    'fullName' => $profile->full_name,
                    'email' => $user->email,
                    'city' => $profile->city,
                    'phoneNumber' => $profile->mobile,
                    'showroomName' => $profile->showroom_name
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error fetching car profile: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve car profile',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
