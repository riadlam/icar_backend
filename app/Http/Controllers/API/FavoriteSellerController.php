<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\FavoriteSeller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class FavoriteSellerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    /**
     * Add a seller to favorites
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'user_id' => 'required|exists:users,id',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }

            $customerId = Auth::id();
            $sellerId = $request->user_id;

            // Check if already favorited
            $favorite = FavoriteSeller::where('customer_user_id', $customerId)
                ->where('user_id', $sellerId)
                ->first();

            if ($favorite) {
                // If exists, remove from favorites
                $favorite->delete();
                
                return response()->json([
                    'success' => true,
                    'message' => 'Seller removed from favorites successfully',
                    'action' => 'removed'
                ]);
            } else {
                // If not exists, add to favorites
                $favorite = FavoriteSeller::create([
                    'customer_user_id' => $customerId,
                    'user_id' => $sellerId
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Seller added to favorites successfully',
                    'action' => 'added',
                    'data' => $favorite
                ], 201);
            }

        } catch (\Exception $e) {
            Log::error('Error adding favorite seller: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to add favorite seller',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all favorite sellers for the authenticated user with their profile information
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show()
    {
        try {
            $customerId = Auth::id();
            
            // Get all favorite sellers for the current user with their profile information
            $favorites = FavoriteSeller::with(['seller.carProfile' => function($query) {
                    $query->select('id', 'user_id', 'full_name');
                }])
                ->where('customer_user_id', $customerId)
                ->get()
                ->map(function($favorite) {
                    return [
                        'id' => $favorite->id,
                        'seller_id' => $favorite->user_id,
                        'full_name' => $favorite->seller->carProfile->full_name ?? 'N/A',
                        'created_at' => $favorite->created_at->toDateTimeString(),
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $favorites
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching favorite sellers: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch favorite sellers',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Check if a seller is favorited by the authenticated user
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function check(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'user_id' => 'required|exists:users,id',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }

            $customerId = Auth::id();
            $sellerId = $request->user_id;

            $isFavorite = FavoriteSeller::where('customer_user_id', $customerId)
                ->where('user_id', $sellerId)
                ->exists();

            return response()->json([
                'success' => true,
                'is_favorite' => $isFavorite
            ]);

        } catch (\Exception $e) {
            Log::error('Error checking favorite status: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to check favorite status',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
