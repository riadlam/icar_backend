<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SubscriptionController extends Controller
{
    /**
     * Subscribe the authenticated user to another user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $userIdToFollow
     * @return \Illuminate\Http\JsonResponse
     */
    public function subscribe(Request $request, $userIdToFollow)
    {
        $subscriberId = Auth::id();

        if ($subscriberId == $userIdToFollow) {
            return response()->json(['success' => false, 'message' => 'You cannot subscribe to yourself.'], 400);
        }

        // Check if the user to follow exists
        $userToFollow = User::find($userIdToFollow);
        if (!$userToFollow) {
            return response()->json(['success' => false, 'message' => 'User to follow not found.'], 404);
        }

        try {
            // Check if a subscription already exists
            $existingSubscription = Subscription::where('subscriber_id', $subscriberId)
                                                ->where('subscribed_to_id', $userIdToFollow)
                                                ->first();

            if ($existingSubscription) {
                // If exists, delete it (unsubscribe)
                $existingSubscription->delete();
                return response()->json([
                    'success' => true,
                    'message' => 'Successfully unsubscribed from ' . $userToFollow->name,
                    'action' => 'unsubscribed'
                ], 200);
            } else {
                // If not exists, create it (subscribe)
                $subscription = Subscription::create([
                    'subscriber_id' => $subscriberId,
                    'subscribed_to_id' => $userIdToFollow,
                ]);
                return response()->json([
                    'success' => true,
                    'message' => 'Successfully subscribed to ' . $userToFollow->name,
                    'data' => $subscription,
                    'action' => 'subscribed'
                ], 201);
            }

        } catch (\Exception $e) {
            Log::error('Error subscribing user: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to subscribe.', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Unsubscribe the authenticated user from another user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $userIdToUnfollow
     * @return \Illuminate\Http\JsonResponse
     */
    public function unsubscribe(Request $request, $userIdToUnfollow)
    {
        $subscriberId = Auth::id();

        try {
            $subscription = Subscription::where('subscriber_id', $subscriberId)
                                        ->where('subscribed_to_id', $userIdToUnfollow)
                                        ->first();

            if (!$subscription) {
                return response()->json(['success' => false, 'message' => 'Subscription not found.'], 404);
            }

            $subscription->delete();

            $userUnfollowed = User::find($userIdToUnfollow);
            $userName = $userUnfollowed ? $userUnfollowed->name : 'the user';

            return response()->json(['success' => true, 'message' => 'Successfully unsubscribed from ' . $userName . '.']);

        } catch (\Exception $e) {
            Log::error('Error unsubscribing user: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to unsubscribe.', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Check if the authenticated user is subscribed to a specific user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $userIdToCheck
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkSubscription(Request $request, $userIdToCheck)
    {
        $subscriberId = Auth::id();

        // Check if the user to check exists (optional, but good practice)
        $userToCheck = User::find($userIdToCheck);
        if (!$userToCheck) {
            return response()->json(['success' => false, 'message' => 'User to check not found.'], 404);
        }

        if ($subscriberId == $userIdToCheck) {
             // A user cannot be 'subscribed' to themselves in this context, so return false.
             // Or, you could return a specific message if preferred.
            return response()->json(['success' => true, 'is_subscribed' => false, 'message' => 'Cannot check subscription status for oneself.'], 200);
        }

        $isSubscribed = Subscription::where('subscriber_id', $subscriberId)
                                    ->where('subscribed_to_id', $userIdToCheck)
                                    ->exists();

        return response()->json(['success' => true, 'is_subscribed' => $isSubscribed]);
    }
}
