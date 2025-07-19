<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Models\SparePart;
use App\Models\SparePartsProfile;
use App\Models\SparePartPost;
use Illuminate\Support\Facades\Auth;

class SparePartController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'supported_brands' => 'nullable|array',
            'image' => 'nullable|string',
            'description' => 'nullable|string',
        ]);

        $validated['user_id'] = Auth::id();
        $part = SparePart::create($validated);

        return response()->json(['message' => 'Spare part created', 'data' => $part]);
    }

    public function index()
    {
        return SparePart::all();
    }

    /**
     * Get the authenticated user's spare parts profile
     *
     * @return \Illuminate\Http\Response
     */
    public function getMySparePartsProfile()
    {
        $profile = SparePartsProfile::where('user_id', Auth::id())->first();
        
        if (!$profile) {
            return response()->json([
                'message' => 'Spare parts profile not found',
                'exists' => false
            ], 404);
        }

        return response()->json([
            'message' => 'Spare parts profile retrieved successfully',
            'exists' => true,
            'data' => $profile
        ]);
    }

    /**
     * Get all spare parts posts for the authenticated user
     *
     * @return \Illuminate\Http\Response
     */
    public function getMySparePartsPosts()
    {
        $posts = SparePartPost::where('user_id', Auth::id())
            ->select('*', 'is_available')
            ->get();
        
        return response()->json([
            'message' => 'Spare parts posts retrieved successfully',
            'data' => $posts
        ]);
    }

    /**
     * Create new spare parts posts
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    /**
     * Update the authenticated user's spare parts profile
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateSparePartsProfile(Request $request)
    {
        $validated = $request->validate([
            'store_name' => 'required|string|max:255',
            'mobile' => 'required|string|max:20',
            'city' => 'required|string|max:255',
        ]);

        $profile = SparePartsProfile::updateOrCreate(
            ['user_id' => Auth::id()],
            $validated
        );

        return response()->json([
            'message' => 'Spare parts profile updated successfully',
            'data' => $profile
        ]);
    }

    /**
     * Create new spare parts posts
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function createSparePartsPost(Request $request)
    {
        $validated = $request->validate([
            'brand' => 'required|string|max:255',
            'model' => 'required|string|max:255',
            'spare_parts_category' => 'required|string|max:255',
            'spare_parts_subcategories' => 'required|array|min:1',
            'spare_parts_subcategories.*' => 'string|max:255',
        ]);

        $userId = Auth::id();
        $posts = [];
        
        // Create a post for each subcategory
        foreach ($validated['spare_parts_subcategories'] as $subcategory) {
            $post = SparePartPost::create([
                'user_id' => $userId,
                'brand' => $validated['brand'],
                'model' => $validated['model'],
                'spare_parts_category' => $validated['spare_parts_category'],
                'spare_parts_subcategory' => $subcategory,
                'is_available' => 1, // Set as available by default
            ]);
            
            $posts[] = $post;
        }

        return response()->json([
            'message' => count($posts) . ' spare parts posts created successfully',
            'data' => $posts
        ], 201);
    }

    /**
     * Search for spare parts and return matching users' profiles
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function searchSpareParts(Request $request)
    {
        $validated = $request->validate([
            'brand' => 'required|string|max:255',
            'model' => 'required|string|max:255',
            'spare_parts_category' => 'required|string|max:255',
            'spare_parts_subcategory' => 'required|string|max:255',
            'city' => 'nullable|string|max:255',
        ]);

        // Find all available posts that match the criteria
        $query = SparePartPost::where('brand', $validated['brand'])
            ->where('model', $validated['model'])
            ->where('spare_parts_category', $validated['spare_parts_category'])
            ->where('spare_parts_subcategory', $validated['spare_parts_subcategory'])
            ->where('is_available', 1);

        // Get the user IDs from the matching posts
        $matchingUserIds = (clone $query)->pluck('user_id');

        // Build the profile query
        $profileQuery = SparePartsProfile::whereIn('user_id', $matchingUserIds);

        // Apply city filter if provided
        if (!empty($validated['city'])) {
            $profileQuery->where('city', 'like', '%' . $validated['city'] . '%');
        }

        // Get the filtered user IDs
        $filteredUserIds = $profileQuery->pluck('user_id');

        // Get the final list of posts that match both criteria
        $filteredPosts = (clone $query)
            ->whereIn('user_id', $filteredUserIds)
            ->with('user.sparePartsProfile')
            ->get();

        // Get unique user IDs from the final filtered posts
        $finalUserIds = $filteredPosts->pluck('user_id')->unique();

        // Get the spare parts profiles for these users
        $profiles = SparePartsProfile::whereIn('user_id', $finalUserIds)
            ->with('user')
            ->get()
            ->map(function($profile) {
                return [
                    'store_name' => $profile->store_name,
                    'mobile' => $profile->mobile,
                    'city' => $profile->city,
                    'user_id' => $profile->user_id,
                ];
            });

        return response()->json([
            'message' => 'Matching profiles retrieved successfully',
            'data' => $profiles
        ]);
    }

    /**
     * Delete a spare parts post
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function deleteSparePartsPost($id)
    {
        $post = SparePartPost::where('id', $id)
            ->where('user_id', Auth::id())
            ->first();

        if (!$post) {
            return response()->json([
                'message' => 'Post not found or you do not have permission to delete it',
            ], 404);
        }

        $post->delete();

        return response()->json([
            'message' => 'Spare parts post deleted successfully'
        ]);
    }

    /**
     * Update a spare parts post
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateSparePartsPost(Request $request, $id)
    {
        $validated = $request->validate([
            'brand' => 'sometimes|required|string|max:255',
            'model' => 'sometimes|required|string|max:255',
            'spare_parts_category' => 'sometimes|required|string|max:255',
            'spare_parts_subcategory' => 'sometimes|required|string|max:255',
            'is_available' => 'boolean',
        ]);

        $post = SparePartPost::where('id', $id)
            ->where('user_id', Auth::id())
            ->first();

        if (!$post) {
            return response()->json([
                'message' => 'Post not found or you do not have permission to update it',
            ], 404);
        }

        // Update the post with the validated data
        $post->fill($validated);
        
        // Explicitly set is_available if it's in the request
        if ($request->has('is_available')) {
            $post->is_available = (bool)$request->is_available;
        }
        
        $post->save();

        // Refresh the model to get the updated data
        $post->refresh();

        return response()->json([
            'message' => 'Spare parts post updated successfully',
            'data' => $post
        ]);
    }

    // ... existing code ...
}
