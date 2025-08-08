<?php

namespace App\Http\Controllers\API;

use App\Models\Car;
use App\Models\CarProfile;
use App\Models\AdditionalPhone;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class CarController extends Controller
{
    /**
     * Delete a car by ID (only if owned by authenticated user)
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $user = Auth::user();
        $car = \App\Models\Car::where('id', $id)->where('user_id', $user->id)->first();
        if (!$car) {
            return response()->json([
                'success' => false,
                'message' => 'Car not found or you do not have permission to delete it.'
            ], 404);
        }
        try {
            $car->delete();
            return response()->json([
                'success' => true,
                'message' => 'Car deleted successfully.'
            ]);
        } catch (\Exception $e) {
            \Log::error('Error deleting car: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete car.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    /**
     * Filter cars by various parameters with priority-based matching
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function filter(Request $request)
    {
        // Get all request data
        $filters = $request->all();
        
        // Start with base query and join with car_profiles
        $query = Car::query()
            ->select('cars.*', 'car_profiles.full_name')
            ->leftJoin('car_profiles', 'cars.user_id', '=', 'car_profiles.user_id')
            ->where('cars.enabled', true);

        // Check if we have either brand or model
        $hasBrand = !empty(trim($filters['brand'] ?? ''));
        $hasModel = !empty(trim($filters['model'] ?? ''));
        
        if ($hasBrand && $hasModel) {
            $term1 = strtolower(trim($filters['brand']));
            $term2 = strtolower(trim($filters['model']));
            
            // Check both possible combinations
            $query->where(function($q) use ($term1, $term2) {
                $q->where(function($q) use ($term1, $term2) {
                    $q->whereRaw('LOWER(brand) = ?', [$term1])
                      ->whereRaw('LOWER(model) = ?', [$term2]);
                })->orWhere(function($q) use ($term1, $term2) {
                    $q->whereRaw('LOWER(brand) = ?', [$term2])
                      ->whereRaw('LOWER(model) = ?', [$term1]);
                });
            });
        } 
        // If only brand is provided
        elseif ($hasBrand) {
            $term = strtolower(trim($filters['brand']));
            $query->where(function($q) use ($term) {
                $q->whereRaw('LOWER(brand) = ?', [$term])
                  ->orWhereRaw('LOWER(model) = ?', [$term]);
            });
        }
        // If only model is provided
        elseif ($hasModel) {
            $term = strtolower(trim($filters['model']));
            $query->where(function($q) use ($term) {
                $q->whereRaw('LOWER(model) = ?', [$term])
                  ->orWhereRaw('LOWER(brand) = ?', [$term]);
            });
        }
        
        // Apply other filters (price, year, etc.)
        if (isset($filters['price_min']) && is_numeric($filters['price_min'])) {
            $query->where('price', '>=', (float)$filters['price_min']);
        }
        
        if (isset($filters['price_max']) && is_numeric($filters['price_max'])) {
            $query->where('price', '<=', (float)$filters['price_max']);
        }
        
        if (isset($filters['type'])) {
            $type = strtolower($filters['type']);
            if ($type === 'sell') {
                $type = 'sale';
            }
            if (in_array($type, ['sale', 'rent'])) {
                $query->where('type', $type);
            }
        }
        
        if (isset($filters['transmission'])) {
            $transmission = strtolower($filters['transmission']);
            if (in_array($transmission, ['automatic', 'manual'])) {
                $query->where('transmission', $transmission);
            } else if ($transmission !== 'all') {
                // If transmission is specified but not 'all', 'automatic', or 'manual', return no results
                return response()->json([
                    'success' => true,
                    'data' => []
                ]);
            }
            // If transmission is 'all', don't apply any transmission filter
        }
        
        // Apply fuel type filter
        if (isset($filters['fuel_type'])) {
            $fuelType = strtolower($filters['fuel_type']);
            $validFuelTypes = ['gasoline', 'diesel', 'electric', 'hybrid', 'plug_in_hybrid'];
            
            if (in_array($fuelType, $validFuelTypes)) {
                $query->where('fuel', $fuelType);
            } else if ($fuelType !== 'all') {
                // If fuel_type is specified but not 'all' or a valid type, return no results
                return response()->json([
                    'success' => true,
                    'data' => []
                ]);
            }
            // If fuel_type is 'all', don't apply any fuel type filter
        }
        
        // Apply mileage filter (show cars with same or higher mileage)
        if (isset($filters['mileage']) && is_numeric($filters['mileage'])) {
            $mileage = (int)$filters['mileage'];
            $query->where('mileage', '>=', $mileage);
        }
        
        // Check if year filter is provided
        $targetYear = isset($filters['year']) ? (int)$filters['year'] : null;
        
        // Get all cars matching brand and type first
        $baseQuery = clone $query;
        
        if ($targetYear) {
            // Get cars with exactly the selected year
            $filteredCars = (clone $baseQuery)
                ->where('year', '=', $targetYear)
                ->orderBy('year', 'desc')
                ->get();
        } else {
            // If no year filter, just get all matching cars
            $filteredCars = $baseQuery->orderBy('year', 'desc')->get();
        }
        
        // If no cars match the brand and type, return empty result
        if ($filteredCars->isEmpty()) {
            return response()->json([
                'success' => true,
                'data' => []
            ]);
        }

        // Define filter priorities and their weights
        // Note: brand, model, type, year, transmission, fuel_type, and mileage are already filtered out above
        $filterWeights = [
            'model' => 3,
            'price' => 2,
        ];

        // Calculate relevance score for each car
        $scoredCars = $filteredCars->map(function($car) use ($filters, $filterWeights) {
            $score = 0;
            $matchedFilters = [];
            
            // Check each filter and add to score if matched
            foreach ($filters as $key => $value) {
                // Skip brand, model, type, year, transmission, fuel_type, mileage, and price filters as they're already filtered
                if (in_array($key, ['brand', 'model', 'type', 'year', 'transmission', 'fuel_type', 'mileage', 'price_min', 'price_max'])) continue;
                
                if (isset($car->$key)) {
                    if (in_array($key, ['min_price', 'max_price', 'max_mileage'])) {
                        // Handle range filters
                        if (str_starts_with($key, 'min_')) {
                            $field = substr($key, 4);
                            if ($car->$field >= $value) {
                                $score += $filterWeights[$field] ?? 1;
                                $matchedFilters[] = $key;
                            }
                        } elseif (str_starts_with($key, 'max_')) {
                            $field = substr($key, 4);
                            if ($car->$field <= $value) {
                                $score += $filterWeights[$field] ?? 1;
                                $matchedFilters[] = $key;
                            }
                        }
                    } else {
                        // Handle exact match filters
                        if (strtolower($car->$key) === strtolower($value)) {
                            $score += $filterWeights[$key] ?? 1;
                            $matchedFilters[] = $key;
                        } elseif ($key === 'model' && stripos(strtolower($car->model), strtolower($value)) !== false) {
                            // Partial match for model
                            $score += ($filterWeights[$key] ?? 1) * 0.7;
                            $matchedFilters[] = $key . ' (partial)';
                        }
                    }
                }
            }
            
            // Calculate percentage match
            $totalPossibleScore = array_sum(array_intersect_key($filterWeights, $filters));
            $matchPercentage = $totalPossibleScore > 0 ? ($score / $totalPossibleScore) * 100 : 0;
            
            return [
                'car' => $car,
                'score' => $score,
                'match_percentage' => round($matchPercentage, 2),
                'matched_filters' => $matchedFilters
            ];
        });

        // Sort by score (descending) and then by creation date (newest first)
        $sortedCars = $scoredCars->sortByDesc(function($item) {
            return [$item['score'], $item['car']->created_at];
        });

        // Transform results to include only necessary car data
        $resultData = $sortedCars->map(function($item) {
            $car = $item['car'];
            return [
                'id' => $car->id,
                'user_id' => $car->user_id,
                'full_name' => $car->full_name,
                'brand' => $car->brand,
                'model' => $car->model,
                'year' => $car->year,
                'price' => $car->price,
                'mileage' => $car->mileage,
                'transmission' => $car->transmission,
                'fuel' => $car->fuel,
                'type' => $car->type,
                'images' => $car->images ?? [],
                'created_at' => $car->created_at->toDateTimeString(),
                'updated_at' => $car->updated_at->toDateTimeString()
            ];
        })->values();

        return response()->json([
            'success' => true,
            'data' => $resultData
        ], 200, [], JSON_UNESCAPED_SLASHES);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:rent,sale',
            'brand' => 'required',
            'model' => 'required',
            'price' => 'required|numeric',
            'mileage' => 'required|integer',
            'year' => 'required|integer',
            'transmission' => 'required',
            'fuel' => 'required',
            'description' => 'nullable|string',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
        ]);

        $validated['user_id'] = Auth::id();
        
        // Handle image upload
        $imagePaths = [];
        if ($request->hasFile('images')) {
            $userId = $validated['user_id'];
            $publicPath = public_path('cars/' . $userId);
            
            // Create directory if it doesn't exist
            if (!file_exists($publicPath)) {
                mkdir($publicPath, 0755, true);
            }
            
            foreach ($request->file('images') as $image) {
                $filename = uniqid() . '.' . $image->getClientOriginalExtension();
                $image->move($publicPath, $filename);
                $imagePaths[] = url('cars/' . $userId . '/' . $filename);
            }
            $validated['images'] = $imagePaths;
        }

        $car = Car::create($validated);

        // Notify all subscribers
        $subscribers = DB::table('subscriptions')
            ->where('subscribed_to_id', auth()->id())
            ->pluck('subscriber_id');

        foreach ($subscribers as $subscriberId) {
            $userName = auth()->user() ? auth()->user()->name : 'A user'; // Fallback if user name is not available
            DB::table('notifications')->insert([
                'user_id' => $subscriberId,
                'message' => $userName . ' has listed a new car: ' . $car->brand . ' ' . $car->model,
                'data' => json_encode($car->toArray()),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return response()->json([
            'message' => 'Car created successfully',
            'data' => $car
        ], 201, [], JSON_UNESCAPED_SLASHES);
    }

    public function index()
    {
        $cars = Car::with(['user.carProfile' => function($query) {
            $query->select('id', 'user_id', 'full_name', 'mobile', 'city');
        }])
        ->where('enabled', true)
        ->get()
        ->map(function($car) {
            $carData = $car->toArray();
            $carData['full_name'] = $car->user->carProfile->full_name ?? null;
            $carData['mobile'] = $car->user->carProfile->mobile ?? null;
            $carData['city'] = $car->user->carProfile->city ?? null;
            unset($carData['user']);
            return $carData;
        });
        
        return response()->json($cars, 200, [], JSON_UNESCAPED_SLASHES);
    }

    public function myCars()
    {
        $userId = Auth::id();
        $cars = Car::where('user_id', $userId)
                  ->orderBy('created_at', 'desc')
                  ->get()
                  ->map(function($car) {
                      return array_merge($car->toArray(), [
                          'images' => $car->images ?? []
                      ]);
                  });
                  
        return response()->json([
            'success' => true,
            'data' => $cars
        ], 200, [], JSON_UNESCAPED_SLASHES);
    }

    /**
     * Get the mobile number from car profile for a specific user
     *
     * @param int $userId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCarProfileMobile($userId)
    {
        try {
            $carProfile = CarProfile::where('user_id', $userId)->first(['id', 'mobile']);

            if (!$carProfile) {
                return response()->json([
                    'success' => false,
                    'message' => 'Car profile not found for this user'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'mobile' => $carProfile->mobile
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting car profile mobile: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to get mobile number',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getCarsByUser(Request $request)
    {
        $userId = $request->header('user_id');
        
        if (!$userId) {
            return response()->json([
                'success' => false,
                'message' => 'User ID is required in the header'
            ], 400);
        }

        $cars = Car::with(['user.carProfile' => function($query) {
                $query->select('id', 'user_id', 'full_name', 'mobile', 'city');
            }])
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($car) {
                $carData = $car->toArray();
                $carData['full_name'] = $car->user->carProfile->full_name ?? null;
                $carData['mobile'] = $car->user->carProfile->mobile ?? null;
                $carData['city'] = $car->user->carProfile->city ?? null;
                unset($carData['user']);
                return $carData;
            });
            
        return response()->json([
            'success' => true,
            'data' => $cars
        ], 200, [], JSON_UNESCAPED_SLASHES);
    }

    public function update(Request $request, $id)
    {
        \Log::info('Update request received', ['car_id' => $id, 'request_data' => $request->except('images')]);
        
        $car = Car::where('id', $id)
                ->where('user_id', Auth::id())
                ->first();

        if (!$car) {
            \Log::error('Car not found or unauthorized', ['car_id' => $id, 'user_id' => Auth::id()]);
            return response()->json([
                'success' => false,
                'message' => 'Car not found or you do not have permission to update it'
            ], 404);
        }

        \Log::info('Current car data', ['car' => $car->toArray()]);

        $validated = $request->validate([
            'type' => 'sometimes|in:rent,sale',
            'brand' => 'sometimes|string',
            'model' => 'sometimes|string',
            'price' => 'sometimes|numeric',
            'mileage' => 'sometimes|integer',
            'year' => 'sometimes|integer',
            'transmission' => 'sometimes|string',
            'fuel' => 'sometimes|string',
            'description' => 'nullable|string',
            'enabled' => 'sometimes|boolean',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
        ]);

        \Log::debug('Validated data', $validated);

        // Handle image upload if new images are provided
        if ($request->hasFile('images')) {
            \Log::info('Processing new image upload');
            $userId = Auth::id();
            $publicPath = public_path('cars/' . $userId);

            // Delete old images if they exist
            if (!empty($car->images)) {
                \Log::info('Deleting old images', ['images' => $car->images]);
                foreach ($car->images as $oldImage) {
                    $parsed = parse_url($oldImage);
                    if (isset($parsed['path'])) {
                        $oldFilePath = public_path($parsed['path']);
                        if (file_exists($oldFilePath)) {
                            unlink($oldFilePath);
                            \Log::info('Deleted old image', ['path' => $oldFilePath]);
                        } else {
                            \Log::warning('Old image not found in public folder', ['path' => $oldFilePath]);
                        }
                    }
                }
            } else {
                \Log::info('No old images to delete');
            }

            // Create directory if it doesn't exist
            if (!file_exists($publicPath)) {
                mkdir($publicPath, 0755, true);
            }

            // Upload new images
            $imagePaths = [];
            foreach ($request->file('images') as $image) {
                $filename = uniqid() . '.' . $image->getClientOriginalExtension();
                $image->move($publicPath, $filename);
                $imagePaths[] = url('cars/' . $userId . '/' . $filename);
                \Log::info('Uploaded new image', [
                    'original_name' => $image->getClientOriginalName(),
                    'stored_path' => 'cars/' . $userId . '/' . $filename,
                    'full_url' => url('cars/' . $userId . '/' . $filename)
                ]);
            }

            // Update the images in the validated data
            $validated['images'] = $imagePaths;
            \Log::info('Updated image paths', ['new_images' => $imagePaths]);
        } else {
            \Log::info('No new images provided in request');
        }

        $car->update($validated);
        
        // Refresh the model to get the latest data
        $car = $car->fresh();
        \Log::info('Car updated successfully', ['updated_car' => $car->toArray()]);

        return response()->json([
            'success' => true,
            'message' => 'Car updated successfully',
            'data' => $car
        ], 200, [], JSON_UNESCAPED_SLASHES);
    }

    /**
     * Add an additional phone number
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $userId
     * @return \Illuminate\Http\JsonResponse
     */
    public function addAdditionalPhone(Request $request, $userId)
    {
        $request->validate([
            'phone_number' => 'required|string|max:20',
        ]);

        try {
            // Check if user already has 3 phone numbers
            $phoneCount = AdditionalPhone::where('user_id', $userId)->count();
            if ($phoneCount >= 3) {
                return response()->json([
                    'success' => false,
                    'message' => 'Maximum of 3 additional phone numbers allowed'
                ], 422);
            }

            // Check if phone number already exists for this user
            $existingPhone = AdditionalPhone::where('user_id', $userId)
                ->where('phone_number', $request->phone_number)
                ->first();

            if ($existingPhone) {
                return response()->json([
                    'success' => false,
                    'message' => 'Phone number already exists in your additional numbers'
                ], 422);
            }

            // Create new phone number
            $phone = AdditionalPhone::create([
                'user_id' => $userId,
                'phone_number' => $request->phone_number
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Phone number added successfully',
                'data' => [
                    'phone' => $phone,
                    'total_phones' => $phoneCount + 1
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error adding additional phone: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to add phone number',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all additional phone numbers for a user
     *
     * @param int $userId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAdditionalPhones($userId)
    {
        try {
            $phones = AdditionalPhone::where('user_id', $userId)
                ->orderBy('created_at', 'asc')
                ->get(['id', 'phone_number']);

            return response()->json([
                'success' => true,
                'data' => [
                    'phones' => $phones,
                    'count' => $phones->count()
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting additional phones: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to get phone numbers',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete an additional phone number
     *
     * @param int $userId
     * @param int $phoneId
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteAdditionalPhone($userId, $phoneId)
    {
        try {
            $phone = AdditionalPhone::where('id', $phoneId)
                ->where('user_id', $userId)
                ->first();

            if (!$phone) {
                return response()->json([
                    'success' => false,
                    'message' => 'Phone number not found or you do not have permission to delete it'
                ], 404);
            }

            $phone->delete();

            $remainingPhones = AdditionalPhone::where('user_id', $userId)->count();

            return response()->json([
                'success' => true,
                'message' => 'Phone number deleted successfully',
                'data' => [
                    'remaining_phones' => $remainingPhones
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error deleting additional phone: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete phone number',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Search cars by brand or model.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function searchCars(Request $request)
    {
        $searchTerm = $request->query('q');

        if (empty(trim($searchTerm))) {
            return response()->json([
                'success' => true,
                'data' => [],
                'message' => 'Search term cannot be empty.'
            ]);
        }

        try {
            $cars = Car::query()
                ->where('enabled', true)
                ->where(function ($query) use ($searchTerm) {
                    $query->where('brand', 'LIKE', '%' . $searchTerm . '%')
                          ->orWhere('model', 'LIKE', '%' . $searchTerm . '%');
                })
                ->select('cars.*', 'car_profiles.full_name') // Assuming you want to keep joining with car_profiles
                ->leftJoin('car_profiles', 'cars.user_id', '=', 'car_profiles.user_id')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $cars
            ]);
        } catch (\Exception $e) {
            Log::error('Error searching cars: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to search cars',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
