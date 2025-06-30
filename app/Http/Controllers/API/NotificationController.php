<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\CarProfile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class NotificationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        // Enable query logging
        \DB::enableQueryLog();
        
        $userId = Auth::id();
        Log::info('Fetching notifications for user_id: ' . $userId);
        
        // Get notifications as array to avoid model modification issues
        $notifications = Notification::where('user_id', $userId)
                                 ->orderBy('created_at', 'desc')
                                 ->get()
                                 ->map(function ($notification) {
                                     // Start building the notification data
                                     $notificationData = [
                                         'id' => $notification->id,
                                         'message' => $notification->message,
                                         'read_at' => $notification->read_at,
                                         'created_at' => $notification->created_at,
                                         'updated_at' => $notification->updated_at,
                                     ];

                                     // Process the data field
                                     $data = $notification->data;
                                     if (is_string($data)) {
                                         $data = json_decode($data, true) ?: [];
                                     } elseif (!is_array($data)) {
                                         $data = [];
                                     }

                                     // If we have a user_id in the data, try to get the car owner profile
                                     if (isset($data['user_id'])) {
                                         $ownerId = $data['user_id'];
                                         Log::info('Looking up car owner profile', ['user_id' => $ownerId]);
                                         
                                         try {
                                             // First check if user exists
                                             $userExists = User::where('id', $ownerId)->exists();
                                             Log::info('User exists check:', ['user_id' => $ownerId, 'exists' => $userExists]);
                                             
                                             if ($userExists) {
                                                 $profile = CarProfile::where('user_id', $ownerId)
                                                                   ->select('full_name', 'showroom_name', 'mobile', 'city')
                                                                   ->first();
                                                 
                                                 if ($profile) {
                                                     // Merge profile fields directly into data
                                                     $profileData = $profile->toArray();
                                                     foreach ($profileData as $key => $value) {
                                                         $data[$key] = $value;
                                                     }
                                                     Log::info('Added owner profile to notification', [
                                                         'user_id' => $ownerId,
                                                         'profile' => $profileData
                                                     ]);
                                                 } else {
                                                     Log::info('No car profile found for user', ['user_id' => $ownerId]);
                                                 }
                                             } else {
                                                 Log::warning('User does not exist', ['user_id' => $ownerId]);
                                                 $data['owner_profile'] = null;
                                             }
                                         } catch (\Exception $e) {
                                             Log::error('Error fetching car owner profile: ' . $e->getMessage(), [
                                                 'user_id' => $ownerId,
                                                 'exception' => $e
                                             ]);
                                             $data['owner_profile'] = null;
                                         }
                                     } else {
                                         Log::info('No user_id found in notification data');
                                     }

                                     // Add the processed data to the notification
                                     $notificationData['data'] = $data;

                                     return $notificationData;
                                 });

        // Log all database queries that were executed
        $queries = \DB::getQueryLog();
        Log::debug('Database queries executed:', ['queries' => $queries]);

        return response()->json([
            'success' => true, 
            'data' => $notifications->values()->all(),
            'debug' => [
                'queries' => $queries
            ]
        ]);
    }

    /**
     * Mark a specific notification as read.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $notificationId
     * @return \Illuminate\Http\JsonResponse
     */
    public function markAsRead(Request $request, $notificationId)
    {
        $userId = Auth::id();
        $notification = Notification::where('id', $notificationId)
                                    ->where('user_id', $userId)
                                    ->first();

        if (!$notification) {
            return response()->json(['success' => false, 'message' => 'Notification not found.'], 404);
        }

        $wasRead = (bool) $notification->read_at;
        $notification->read_at = now();
        $notification->save();
        
        if ($wasRead) {
            return response()->json(['success' => true, 'message' => 'Notification read timestamp updated.', 'data' => $notification]);
        }

        return response()->json(['success' => true, 'message' => 'Notification marked as read.', 'data' => $notification]);
    }

    /**
     * Mark all unread notifications for the authenticated user as read.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function markAllAsRead(Request $request)
    {
        $userId = Auth::id();
        $updatedCount = Notification::where('user_id', $userId)
                                ->whereNull('read_at')
                                ->update(['read_at' => now()]);

        return response()->json(['success' => true, 'message' => $updatedCount . ' notifications marked as read.']);
    }

    /**
     * Get the count of unread notifications for the authenticated user
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function unreadCount(Request $request)
    {
        $userId = Auth::id();
        $count = Notification::where('user_id', $userId)
                           ->whereNull('read_at')
                           ->count();

        return response()->json([
            'success' => true,
            'unread_count' => $count
        ]);
    }
}
