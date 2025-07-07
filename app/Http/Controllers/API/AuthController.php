<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Models\User;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function redirectToGoogle()
    {
        $redirectUrl = Socialite::driver('google')->stateless()->redirect()->getTargetUrl();
        Log::info('Redirecting to Google with URL: ' . $redirectUrl);

        return response()->json(['url' => $redirectUrl]);
    }

    public function handleGoogleCallback(Request $request)
    {
        try {
            Log::info('Handling Google OAuth callback');

            $googleUser = Socialite::driver('google')->stateless()->user();
            Log::info('Google user retrieved', ['email' => $googleUser->getEmail()]);

            $isNewUser = false;
            $user = User::firstOrNew(['email' => $googleUser->getEmail()]);
            
            if (!$user->exists) {
                $isNewUser = true;
                $user->fill([
                    'google_id' => $googleUser->getId(),
                    'name' => $googleUser->getName(),
                    'password' => bcrypt(Str::random(24)),
                    'email_verified_at' => now(),
                ]);
                $user->save();
            } else if ($user->google_id !== $googleUser->getId()) {
                $user->google_id = $googleUser->getId();
                $user->save();
            }

            Log::info($isNewUser ? 'New user created' : 'Existing user logged in', ['user_id' => $user->id]);

            $token = $user->createToken('api-token')->plainTextToken;

            return response()->json([
                'user' => $user,
                'token' => $token,
                'token_type' => 'Bearer',
                'is_new_user' => $isNewUser
            ]);

        } catch (\Exception $e) {
            Log::error('Error in handleGoogleCallback', ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json([
                'error' => 'Failed to authenticate with Google',
                'message' => $e->getMessage(),
            ], 401);
        }
    }

    public function googleLogin(Request $request)
    {
        Log::info('Starting googleLogin method');

        $request->validate([
            'access_token' => 'required|string',
        ]);

        Log::info('Access token received', ['token' => $request->access_token]);

        try {
            $googleUser = Socialite::driver('google')
                ->stateless()
                ->userFromToken($request->access_token);

            Log::info('Google user from token', ['email' => $googleUser->getEmail()]);

            $isNewUser = false;
            $user = User::firstOrNew(['email' => $googleUser->getEmail()]);
            
            if (!$user->exists) {
                $isNewUser = true;
                $user->fill([
                    'google_id' => $googleUser->getId(),
                    'name' => $googleUser->getName(),
                    'password' => bcrypt(Str::random(24)),
                    'email_verified_at' => now(),
                ]);
                $user->save();
            } else if ($user->google_id !== $googleUser->getId()) {
                $user->google_id = $googleUser->getId();
                $user->save();
            }

            Log::info($isNewUser ? 'New user logged in via token' : 'Existing user logged in via token', 
                     ['user_id' => $user->id]);

            $token = $user->createToken('api-token')->plainTextToken;

            return response()->json([
                'user' => $user,
                'token' => $token,
                'token_type' => 'Bearer',
                'is_new_user' => $isNewUser
            ]);

        } catch (\Exception $e) {
            Log::error('Error in googleLogin', [
                'message' => $e->getMessage(),
                'token' => $request->access_token,
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'error' => 'Invalid token provided',
                'message' => $e->getMessage(),
            ], 401);
        }
    }

    /**
     * Update the authenticated user's role
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateRole(Request $request)
    {
        try {
            $request->validate([
                'role_index' => 'required|integer|min:0|max:3',
            ]);

            $roles = ['spare_parts_seller', 'car_seller', 'tow_truck', 'garage_owner'];
            
            if (!isset($roles[$request->role_index])) {
                return response()->json(['error' => 'Invalid role index'], 400);
            }

            $user = Auth::user();
            $user->role = $roles[$request->role_index];
            $user->save();

            return response()->json([
                'message' => 'Role updated successfully',
                'user' => $user->only(['id', 'name', 'email', 'role'])
            ]);

        } catch (\Exception $e) {
            Log::error('Error updating user role: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to update role',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get the authenticated user's role
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getRole()
    {
        try {
            $user = Auth::user();
            
            if (!$user) {
                return response()->json([
                    'error' => 'User not authenticated'
                ], 401);
            }

            return response()->json([
                'role' => $user->role
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error retrieving user role: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to retrieve role',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
