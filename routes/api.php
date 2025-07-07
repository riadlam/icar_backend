<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\ProfileController;
use App\Http\Controllers\API\CarController;
use App\Http\Controllers\API\SparePartController;
use App\Http\Controllers\API\FavoriteSellerController;
use App\Http\Controllers\API\SubscriptionController;
use App\Http\Controllers\API\NotificationController;
use App\Http\Controllers\API\CarProfileController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::get('/garage-profiles/all', [ProfileController::class, 'getAllGarageProfiles']);
Route::get('/tow-truck-profiles/all', [ProfileController::class, 'getAllTowTruckProfiles']);
Route::get('/auth/google', [AuthController::class, 'redirectToGoogle']);
Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback']);
Route::post('/login/google', [AuthController::class, 'googleLogin']);
Route::get('/cars', [CarController::class, 'index']);
Route::post('/cars/filter', [CarController::class, 'filter']);
Route::get('/user/cars/list', [CarController::class, 'getCarsByUser']);
Route::get('/cars/search', [CarController::class, 'searchCars']);
Route::post('/spare-parts/search', [SparePartController::class, 'searchSpareParts']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user/role', [AuthController::class, 'getRole']);
    Route::post('/update-role', [AuthController::class, 'updateRole']);
    Route::post('/profile', [ProfileController::class, 'store']);
    Route::post('/garage-profiles/create-new', [ProfileController::class, 'createNewGarageProfile']);
    Route::get('/garage-profiles', [ProfileController::class, 'getUserGarageProfiles']);
    Route::put('/garage-profiles/{garageProfile}', [ProfileController::class, 'updateGarageProfile']);
    
    Route::get('/tow-truck-profiles', [ProfileController::class, 'getUserTowTruckProfiles']);
    Route::post('/tow-truck-profiles/create', [ProfileController::class, 'createTowTruckProfile']);
    Route::put('/tow-truck-profiles/{towTruckProfile}', [ProfileController::class, 'updateTowTruckProfile']);
    Route::delete('/tow-truck-profiles/{towTruckProfile}', [ProfileController::class, 'deleteTowTruckProfile']);
    Route::get('/profile', [ProfileController::class, 'show']);
    Route::post('/cars', [CarController::class, 'store']);
    Route::put('/cars/{car}', [CarController::class, 'update']);
    Route::delete('/cars/{car}', [CarController::class, 'destroy']);
    Route::get('/user/cars', [CarController::class, 'myCars']);
    Route::post('/spare-parts', [SparePartController::class, 'store']);
    Route::get('/spare-parts', [SparePartController::class, 'index']);
    Route::get('/spare-parts/my-profile', [SparePartController::class, 'getMySparePartsProfile']);
    Route::put('/spare-parts/profile', [SparePartController::class, 'updateSparePartsProfile']);
    Route::get('/spare-parts/my-posts', [SparePartController::class, 'getMySparePartsPosts']);
    Route::post('/spare-parts/posts', [SparePartController::class, 'createSparePartsPost']);
    Route::delete('/spare-parts/posts/{id}', [SparePartController::class, 'deleteSparePartsPost']);
    Route::put('/spare-parts/posts/{id}', [SparePartController::class, 'updateSparePartsPost']);
    Route::post('/profile/phone', [ProfileController::class, 'updatePhone']);
    Route::post('/profile/update-name', [ProfileController::class, 'updateName']);
    Route::get('/profile/basic-info', [ProfileController::class, 'getBasicInfo']);
    Route::delete('/profile/me', [ProfileController::class, 'deleteMe']);
    // Additional phone numbers management
    Route::post('/users/{userId}/additional-phones', [CarController::class, 'addAdditionalPhone']);
    Route::get('/users/{userId}/additional-phones', [CarController::class, 'getAdditionalPhones']);
    Route::delete('/users/{userId}/additional-phones/{phoneId}', [CarController::class, 'deleteAdditionalPhone']);
    
    // Car profile mobile number
    Route::get('/users/{userId}/car-profile/mobile', [CarController::class, 'getCarProfileMobile']);
    
    // Favorite Sellers
    Route::post('/favorite-sellers', [FavoriteSellerController::class, 'store']);
    Route::get('/favorite-sellers', [FavoriteSellerController::class, 'show']);
    Route::post('/favorite-sellers/check', [FavoriteSellerController::class, 'check']);

    // Car profile routes
    Route::get('/my-car-profile', [CarProfileController::class, 'getMyCarProfile']);
    
    // Subscription routes
    Route::post('/users/{user}/subscribe', [SubscriptionController::class, 'subscribe'])->where('user', '[0-9]+');
    Route::delete('/users/{user}/unsubscribe', [SubscriptionController::class, 'unsubscribe'])->where('user', '[0-9]+');
    Route::get('/users/{user}/subscription-status', [SubscriptionController::class, 'checkSubscription'])->where('user', '[0-9]+');

    // Notification routes
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::get('/notifications/unread-count', [NotificationController::class, 'unreadCount']);
    Route::post('/notifications/{notification}/read', [NotificationController::class, 'markAsRead'])->where('notification', '[0-9]+');
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead']);
});
