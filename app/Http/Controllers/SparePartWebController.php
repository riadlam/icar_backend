<?php

namespace App\Http\Controllers;

use App\Models\SparePart;
use App\Models\SparePartPost;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class SparePartWebController extends Controller
{
    public function index()
    {
        try {
            // Use the existing API endpoint to get spare parts data
            $response = Http::get(url('/api/spare-parts'));
            
            if ($response->successful()) {
                $spareParts = collect($response->json());
            } else {
                // Fallback to direct database query if API fails
                $spareParts = SparePartPost::with(['user.sparePartsProfile'])
                    ->where('is_available', true)
                    ->orderBy('created_at', 'desc')
                    ->get()
                    ->map(function($sparePart) {
                        // Get seller info from spare_parts_profiles table
                        $profile = $sparePart->user->sparePartsProfile;
                        
                        return [
                            'id' => $sparePart->id,
                            'title' => $sparePart->spare_parts_category . ' - ' . $sparePart->spare_parts_subcategory,
                            'description' => $sparePart->brand . ' ' . $sparePart->model,
                            'price' => 0, // No price field in current structure
                            'condition' => 'new', // Default condition
                            'brand' => $sparePart->brand,
                            'model' => $sparePart->model,
                            'year' => 'N/A', // No year field
                            'images' => [], // No images field
                            'is_available' => $sparePart->is_available,
                            'created_at' => $sparePart->created_at,
                            'updated_at' => $sparePart->updated_at,
                            'store_name' => $profile->store_name ?? 'Unknown Store',
                            'mobile' => $profile->mobile ?? 'N/A',
                            'city' => $profile->city ?? 'N/A',
                        ];
                    });
            }
        } catch (\Exception $e) {
            // Fallback to direct database query if there's any error
            $spareParts = SparePartPost::with(['user.sparePartsProfile'])
                ->where('is_available', true)
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function($sparePart) {
                    return [
                        'id' => $sparePart->id,
                        'title' => $sparePart->title,
                        'description' => $sparePart->description,
                        'price' => $sparePart->price,
                        'condition' => $sparePart->condition,
                        'brand' => $sparePart->brand,
                        'model' => $sparePart->model,
                        'year' => $sparePart->year,
                        'images' => $sparePart->images,
                        'is_available' => $sparePart->is_available,
                        'created_at' => $sparePart->created_at,
                        'updated_at' => $sparePart->updated_at,
                        'full_name' => $sparePart->user->sparePartsProfile->full_name ?? $sparePart->user->name ?? 'Unknown',
                        'mobile' => $sparePart->user->sparePartsProfile->mobile ?? 'N/A',
                        'city' => $sparePart->user->sparePartsProfile->city ?? 'N/A',
                    ];
                });
        }

        return view('spare-parts.index', compact('spareParts'));
    }

    public function destroy($id)
    {
        try {
            $sparePart = SparePartPost::find($id);
            
            if (!$sparePart) {
                return response()->json([
                    'success' => false,
                    'message' => 'Spare part not found.'
                ], 404);
            }

            $sparePart->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Spare part deleted successfully.'
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error deleting spare part: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete spare part.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
