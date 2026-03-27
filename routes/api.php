<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CarController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\RatingController;
use App\Http\Controllers\RequestController;

/*
|--------------------------------------------------------------------------
| User Routes  (prefix: api/)
|--------------------------------------------------------------------------
*/

// ─── Auth (public) ────────────────────────────────────────────────────────
Route::post('login',          [AuthController::class, 'login']);
Route::post('register',       [AuthController::class, 'register']);

// ─── Authenticated user routes ────────────────────────────────────────────
Route::middleware('auth:sanctum')->group(function () {
    Route::post('logout',                [AuthController::class, 'logout']);
    Route::get('user',                   [AuthController::class, 'get_profile']);
    Route::put('user',                   [AuthController::class, 'edit_profile']);
    Route::post('user/change-password', [AuthController::class, 'changePassword']);
    Route::delete('user/delete_account', [AuthController::class, 'delete_user']);

    // Favorites
    Route::get('favorites',  [FavoriteController::class, 'index']);
    Route::post('favorites', [FavoriteController::class, 'store']);

    // Purchase Requests (buyer side)
    Route::post('purchase-requests',           [RequestController::class, 'storePurchase']);
    Route::get('my-purchase-requests',         [RequestController::class, 'myPurchaseRequests']);

    // Rental Requests (renter side)
    Route::post('rental-requests',             [RequestController::class, 'storeRental']);
    Route::get('my-rental-requests',           [RequestController::class, 'myRentalRequests']);

    // Ratings (authenticated)
    Route::post('ratings', [RatingController::class, 'store']);
});

// ─── Public routes ────────────────────────────────────────────────────────
Route::post('messages',                 [MessageController::class, 'store']);
Route::get('ratings/{seller_id}',       [RatingController::class, 'index']);
Route::get('categories',                [CategoryController::class, 'index']);
Route::get('categories/{category}',     [CategoryController::class, 'show']);
Route::get('cars',                      [CarController::class, 'index']);
Route::get('cars/{car}',                [CarController::class, 'show']);
