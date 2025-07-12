<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Models\{CarProfile, GarageProfile, SparePartsProfile, TowTruckProfile, User};
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ProfileController extends Controller
{
    /**
     * Get all garage profiles with optional filtering (public endpoint)
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     * 
     * @queryParam city string Filter by city name. Example: Cairo
     * @queryParam services array Filter by services (comma-separated). Example: Oil Change,Brake Service
     */
    public function getAllGarageProfiles(Request $request)
    {
        try {
            Log::info('=== GARAGE PROFILES FILTERING STARTED ===');
            Log::info('Request parameters:', $request->all());
            
            $query = GarageProfile::with('user');
            
            // Filter by city if provided (case-insensitive)
            if ($request->has('city') && !empty($request->city)) {
                $city = strtolower($request->city);
                Log::info("Filtering by city (case-insensitive): {$city}");
                $query->whereRaw('LOWER(city) LIKE ?', ['%' . $city . '%']);
            }
            
            // Filter by services if provided
            if ($request->has('services') && !empty($request->services)) {
                $services = array_map('trim', explode(',', $request->services));
                Log::info('Filtering by services (exact match):', $services);
                
                $query->where(function($q) use ($services) {
                    foreach ($services as $service) {
                        $q->orWhereJsonContains('services', $service);
                    }
                });
                
                // Log the raw SQL query for debugging
                Log::info('SQL Query: ' . $query->toSql());
                Log::info('Bindings: ', $query->getBindings());
            }
            
            // Get the filtered results from database
            $garageProfiles = $query->latest()->get();
            Log::info('Initial filtered profiles count: ' . $garageProfiles->count());
            
            // Log all profiles before case-insensitive check
            if ($garageProfiles->isNotEmpty()) {
                $profileData = $garageProfiles->map(function($profile) {
                    return [
                        'id' => $profile->id,
                        'business_name' => $profile->business_name,
                        'city' => $profile->city,
                        'services' => $profile->services,
                        'services_lower' => array_map('strtolower', $profile->services ?? [])
                    ];
                });
                Log::info('Profiles before case-insensitive check:', $profileData->toArray());
            }
            
            // If services filter was applied, do a final case-insensitive check
            if ($request->has('services') && !empty($request->services)) {
                $services = array_map('strtolower', array_map('trim', explode(',', $request->services)));
                Log::info('Performing case-insensitive check for services:', $services);
                
                $garageProfiles = $garageProfiles->filter(function($profile) use ($services) {
                    if (empty($profile->services)) {
                        Log::info("Profile ID {$profile->id} has no services");
                        return false;
                    }
                    
                    $profileServices = array_map('strtolower', $profile->services);
                    $hasMatchingService = false;
                    
                    foreach ($services as $service) {
                        $serviceLower = strtolower($service);
                        if (in_array($serviceLower, $profileServices)) {
                            Log::info("Profile ID {$profile->id} matched service: {$serviceLower}");
                            $hasMatchingService = true;
                            break;
                        }
                    }
                    
                    if (!$hasMatchingService) {
                        Log::info("Profile ID {$profile->id} does not have any matching services");
                    }
                    
                    return $hasMatchingService;
                })->values();
                
                Log::info('Final filtered profiles count: ' . $garageProfiles->count());
            }

            return response()->json([
                'success' => true,
                'data' => $garageProfiles,
                'message' => 'Filtered garage profiles retrieved successfully.'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error fetching filtered garage profiles: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve garage profiles.'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    
    /**
     * Get all tow truck profiles (public endpoint)
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     * 
     * @queryParam city string Filter by city name. Example: Cairo
     */
    public function getAllTowTruckProfiles(Request $request)
    {
        try {
            $query = TowTruckProfile::query();
            
            // Filter by city if provided
            if ($request->has('city')) {
                $query->where('city', 'like', '%' . $request->city . '%');
            }
            
            $profiles = $query->latest()->get();

            return response()->json([
                'success' => true,
                'data' => $profiles,
                'message' => 'All tow truck profiles retrieved successfully.'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error fetching all tow truck profiles: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve tow truck profiles.'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    /**
     * Get all garage profiles for the authenticated user
     *
     * @return \Illuminate\Http\Response
     */
    /**
     * Update a specific garage profile
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\GarageProfile  $garageProfile
     * @return \Illuminate\Http\Response
     */
    public function updateGarageProfile(Request $request, GarageProfile $garageProfile)
    {
        try {
            $user = Auth::user();
            
            // Verify the garage profile belongs to the authenticated user
            if ($garageProfile->user_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized. You do not own this garage profile.'
                ], 403);
            }

            // Validate the request data
            $validated = $request->validate([
                'business_name' => 'sometimes|string|max:255',
                'mechanic_name' => 'sometimes|string|max:255',
                'mobile' => 'sometimes|string|max:20',
                'city' => 'sometimes|string|max:255',
                'services' => 'sometimes|array',
                'services.*' => 'string|max:255',
            ]);

            // Update the garage profile
            $garageProfile->update($validated);

            return response()->json([
                'success' => true,
                'data' => $garageProfile->fresh(),
                'message' => 'Garage profile updated successfully.'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error updating garage profile: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update garage profile.'
            ], 500);
        }
    }

    /**
     * Get all garage profiles for the authenticated user
     *
     * @return \Illuminate\Http\Response
     */
    public function getUserGarageProfiles()
    {
        try {
            Log::info('=== GET USER GARAGE PROFILES METHOD CALLED ===');
            Log::info('Request URL: ' . request()->fullUrl());
            Log::info('Request Method: ' . request()->method());
            Log::info('Request Headers: ', request()->header());
            Log::info('Request Query Params: ', request()->query());
            
            $user = Auth::user();
            Log::info('Authenticated User ID: ' . $user->id);
            
            $garageProfiles = GarageProfile::where('user_id', $user->id)
                ->latest()
                ->get();
                
            Log::info('Found ' . $garageProfiles->count() . ' garage profiles');
            Log::info('Garage Profiles Data: ', $garageProfiles->toArray());

            return response()->json([
                'success' => true,
                'data' => $garageProfiles,
                'message' => 'Garage profiles retrieved successfully.'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error in getUserGarageProfiles: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve garage profiles.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    /**
     * Create a new garage profile entry (allows multiple entries per user)
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function createNewGarageProfile(Request $request)
    {
        $user = Auth::user();
        
        // Validate the request data
        $validated = $request->validate([
            'business_name' => 'required|string|max:255',
            'mechanic_name' => 'required|string|max:255',
            'mobile' => 'required|string|max:20',
            'city' => 'required|string|max:255',
            'services' => 'nullable|array',
            'services.*' => 'string|max:255',
        ]);

        // Create a new garage profile
        $profile = GarageProfile::create([
            'user_id' => $user->id,
            'business_name' => $validated['business_name'],
            'mechanic_name' => $validated['mechanic_name'],
            'mobile' => $validated['mobile'],
            'city' => $validated['city'],
            'services' => $validated['services'] ?? [],
        ]);

        return response()->json([
            'message' => 'New garage profile created successfully',
            'data' => $profile
        ], 201);
    }
    
    /**
     * Create a new tow truck profile
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function createTowTruckProfile(Request $request)
    {
        $user = Auth::user();
        
        // Validate the request data
        $validated = $request->validate([
            'business_name' => 'required|string|max:255',
            'driver_name' => 'required|string|max:255',
            'mobile' => 'required|string|max:20',
            'city' => 'required|string|max:255',
        ]);

        // Create a new tow truck profile
        $profile = TowTruckProfile::create([
            'user_id' => $user->id,
            'business_name' => $validated['business_name'],
            'driver_name' => $validated['driver_name'],
            'mobile' => $validated['mobile'],
            'city' => $validated['city'],
        ]);

        return response()->json([
            'message' => 'New tow truck profile created successfully',
            'data' => $profile
        ], 201);
    }
    
    /**
     * Get all tow truck profiles for the authenticated user
     *
     * @return \Illuminate\Http\Response
     */
    public function getUserTowTruckProfiles()
    {
        try {
            $user = Auth::user();
            
            $profiles = TowTruckProfile::where('user_id', $user->id)
                ->latest()
                ->get();

            return response()->json([
                'success' => true,
                'data' => $profiles,
                'message' => 'Tow truck profiles retrieved successfully.'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error fetching tow truck profiles: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve tow truck profiles.'
            ], 500);
        }
    }
    
    /**
     * Update a specific tow truck profile
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\TowTruckProfile  $towTruckProfile
     * @return \Illuminate\Http\Response
     */
    public function updateTowTruckProfile(Request $request, TowTruckProfile $towTruckProfile)
    {
        try {
            $user = Auth::user();
            
            // Verify the tow truck profile belongs to the authenticated user
            if ($towTruckProfile->user_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized. You do not own this tow truck profile.'
                ], 403);
            }

            // Validate the request data
            $validated = $request->validate([
                'business_name' => 'sometimes|string|max:255',
                'driver_name' => 'sometimes|string|max:255',
                'mobile' => 'sometimes|string|max:20',
                'city' => 'sometimes|string|max:255',
            ]);

            // Update the tow truck profile
            $towTruckProfile->update($validated);

            return response()->json([
                'success' => true,
                'data' => $towTruckProfile->fresh(),
                'message' => 'Tow truck profile updated successfully.'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error updating tow truck profile: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update tow truck profile.'
            ], 500);
        }
    }
    
    /**
     * Delete a specific tow truck profile
     *
     * @param  \App\Models\TowTruckProfile  $towTruckProfile
     * @return \Illuminate\Http\Response
     */
    public function deleteTowTruckProfile(TowTruckProfile $towTruckProfile)
    {
        try {
            // Delete the tow truck profile without checking ownership
            $towTruckProfile->delete();

            return response()->json([
                'success' => true,
                'message' => 'Tow truck profile deleted successfully.'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error deleting tow truck profile: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete tow truck profile.'
            ], 500);
        }
    }

    /**
     * Store or update the user's profile based on their role
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $role = $user->role;
        $profile = null;

        // Get all input data and filter out null/empty values
        $input = array_filter($request->all(), function($value) {
            return $value !== null && $value !== '';
        });

        switch ($role) {
            case 'car_seller':
                $carProfileData = $request->only(['full_name', 'showroom_name', 'mobile', 'city']);
if (empty($carProfileData['showroom_name']) && !empty($carProfileData['full_name'])) {
    $carProfileData['showroom_name'] = $carProfileData['full_name'];
}
$profile = CarProfile::updateOrCreate(
    ['user_id' => $user->id],
    $carProfileData
);
                break;
                
            case 'spare_parts_seller':
                $profile = SparePartsProfile::updateOrCreate(
                    ['user_id' => $user->id],
                    $request->only(['store_name', 'mobile', 'city'])
                );
                break;
                
            case 'garage_owner':
                $profileData = $request->only(['business_name', 'mobile', 'city']);
                $profileData['mechanic_name'] = $request->input('mechanic_name', ''); // Default to empty string if not provided
                $profileData['services'] = $request->input('services', []); // Default to empty array if not provided
                $profile = GarageProfile::updateOrCreate(
                    ['user_id' => $user->id],
                    $profileData
                );
                break;
                
            case 'tow_truck':
            // Map business_name and driver_name from store_name if present
            $storeName = $request->input('store_name') ?? $request->input('business_name') ?? $request->input('full_name') ?? null;
            $profileData = [
                'business_name' => $storeName,
                'driver_name' => $storeName,
                'mobile' => $request->input('mobile'),
                'city' => $request->input('city'),
            ];
            $profile = TowTruckProfile::updateOrCreate(
                ['user_id' => $user->id],
                $profileData
            );
            break;
                
            default:
                return response()->json(['error' => 'Invalid role'], 400);
        }

        return response()->json([
            'message' => 'Profile ' . ($profile->wasRecentlyCreated ? 'created' : 'updated'),
            'data' => $profile
        ]);
    }

    /**
     * Get the authenticated user's profile based on their role
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show()
    {
        $user = Auth::user();
        $profile = null;

        switch ($user->role) {
            case 'car_seller':
                $profile = CarProfile::where('user_id', $user->id)->first();
                break;
            case 'spare_parts_seller':
                $profile = SparePartsProfile::where('user_id', $user->id)->first();
                break;
            case 'tow_truck':
                $profile = TowTruckProfile::where('user_id', $user->id)->first();
                break;
                
            case 'garage_owner':
                $profile = GarageProfile::where('user_id', $user->id)->first();
                break;
            default:
                return response()->json(['error' => 'Invalid role'], 400);
        }

        if (!$profile) {
            return response()->json(['message' => 'Profile not found'], 404);
        }

        return response()->json(['data' => $profile]);
    }

    /**
     * Update the authenticated user's phone number
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    /**
     * Update the authenticated user's name
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    /**
     * Update the authenticated user's name, phone, or city
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    /**
     * Get the authenticated user's name, city, and phone
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getBasicInfo()
    {
        $user = Auth::user();
        return response()->json([
            'name' => $user->name,
            'city' => $user->city,
            'phone' => $user->phone,
        ]);
    }

    public function updateName(Request $request)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'store_name' => 'sometimes|string|max:255',
            'phone' => 'sometimes|string|max:20',
            'city' => 'sometimes|string|max:255',
        ]);

        $user = Auth::user();
        $updatedFields = [];

        // Use 'store_name' as alias for 'name' if present
        if (isset($validated['store_name'])) {
            $user->name = $validated['store_name'];
            $updatedFields['name'] = $validated['store_name'];
        } elseif (isset($validated['name'])) {
            $user->name = $validated['name'];
            $updatedFields['name'] = $validated['name'];
        }
        if (isset($validated['phone'])) {
            $user->phone = $validated['phone'];
            $updatedFields['phone'] = $validated['phone'];
        }
        if (isset($validated['city'])) {
            $user->city = $validated['city'];
            $updatedFields['city'] = $validated['city'];
        }
        $user->save();

    // Also update CarProfile if user is a car_seller
    if ($user->role === 'car_seller') {
        $carProfileData = [];
        if (isset($updatedFields['name'])) {
            $carProfileData['showroom_name'] = $updatedFields['name'];
            $carProfileData['full_name'] = $updatedFields['name'];
        }
        if (isset($updatedFields['phone'])) {
            $carProfileData['mobile'] = $updatedFields['phone'];
        }
        if (isset($updatedFields['city'])) {
            $carProfileData['city'] = $updatedFields['city'];
        }
        if (!empty($carProfileData)) {
            \App\Models\CarProfile::updateOrCreate(
                ['user_id' => $user->id],
                $carProfileData
            );
        }
    }

    return response()->json([
        'message' => 'User info updated successfully',
        'updated' => $updatedFields
    ]);
    }

    public function updatePhone(Request $request)
    {
        $request->validate([
            'phone' => 'required|string|max:20',
        ]);

        $user = Auth::user();
        $user->phone = $request->phone;
        $user->save();

        return response()->json([
            'message' => 'Phone number updated successfully',
            'phone' => $user->phone
        ]);
    }

    /**
     * Delete the authenticated user from the users table
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteMe()
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'No authenticated user.'
            ], 401);
        }

        $user->delete();
        return response()->json([
            'success' => true,
            'message' => 'User account deleted successfully.'
        ]);
    }

    /**
     * Delete a garage profile
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     * 
     * @response 200 {
     *     "success": true,
     *     "message": "Garage profile deleted successfully."
     * }
     * @response 403 {
     *     "success": false,
     *     "message": "Unauthorized. You do not own this garage profile."
     * }
     * @response 404 {
     *     "success": false,
     *     "message": "Garage profile not found."
     * }
     */
    public function deleteGarageProfile($id)
    {
        try {
            $garageProfile = GarageProfile::find($id);

            if (!$garageProfile) {
                return response()->json([
                    'success' => false,
                    'message' => 'Garage profile not found.'
                ], 404);
            }

            $garageProfile->delete();

            return response()->json([
                'success' => true,
                'message' => 'Garage profile deleted successfully.'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error deleting garage profile: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete garage profile.'
            ], 500);
        }
    }
}
