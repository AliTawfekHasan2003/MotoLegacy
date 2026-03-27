<?php

use App\Http\Controllers\Seller\AuthController as SellerAuthController;
use App\Http\Controllers\Seller\CarController as SellerCarController;
use App\Http\Controllers\Seller\CategoryController;
use App\Http\Controllers\Seller\RequestController as SellerRequestController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Seller Routes  (prefix: api/seller)
|--------------------------------------------------------------------------
*/

// ─── Auth (public – no sanctum needed) ───────────────────────────────────
Route::post('register', [SellerAuthController::class, 'register']);
Route::post('login',    [SellerAuthController::class, 'login']);


// ─── Authenticated seller routes ─────────────────────────────────────────
Route::middleware(['auth:sanctum', 'role:seller'])->group(function () {

    // Profile
    Route::get('profile',    [SellerAuthController::class, 'get_profile']);
    Route::put('profile',    [SellerAuthController::class, 'edit_profile']);
    Route::post('logout',    [SellerAuthController::class, 'logout']);
    Route::post('change-password',    [SellerAuthController::class, 'changePassword']);
    Route::delete('account', [SellerAuthController::class, 'delete_account']);

    // Cars
    Route::apiResource('cars', SellerCarController::class);
    Route::post('cars/{car}/toggle-visibility',  [SellerCarController::class, 'toggle_visibility']);

    // Purchase Requests
    Route::get('incoming-purchase-requests',                       [SellerRequestController::class, 'incomingPurchaseRequests']);
    Route::patch('purchase-requests/{purchaseRequest}/status',     [SellerRequestController::class, 'updatePurchaseStatus']);

    // Rental Requests
    Route::get('incoming-rental-requests',                         [SellerRequestController::class, 'incomingRentalRequests']);
    Route::patch('rental-requests/{rentalRequest}/status',         [SellerRequestController::class, 'updateRentalStatus']);

    Route::get('categories',                [CategoryController::class, 'index']);
    Route::get('categories/{category}',     [CategoryController::class, 'show']);
});
