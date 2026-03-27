<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\CarController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\MessageController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin Routes  (prefix: api/admin)
|--------------------------------------------------------------------------
*/

// ─── Auth (public) ────────────────────────────────────────────────────────
Route::post('login', [AdminAuthController::class, 'login']);

// ─── Authenticated admin routes ───────────────────────────────────────────
Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {

    // Profile / Auth
    Route::post('logout', [AdminAuthController::class, 'logout']);
    Route::get('profile', [AdminAuthController::class, 'get_profile']);

    // Dashboard
    Route::get('stats', [AdminController::class, 'stats']);

    // Categories
    Route::apiResource('categories', CategoryController::class);

    // Messages
    Route::get('messages', [MessageController::class, 'index']);

    // Requests
    Route::get('purchase-requests', [AdminController::class, 'purchaseRequests']);
    Route::get('rental-requests',   [AdminController::class, 'rentalRequests']);

    // Roles
    Route::apiResource('roles', RoleController::class);

    Route::get('cars',                      [CarController::class, 'index']);
    Route::get('cars/{car}',                [CarController::class, 'show']);
    Route::patch('cars/{car}/approval',                [CarController::class, 'approveCar']);


    // Users
    Route::apiResource('users', UserController::class);
    Route::post('users/{user}/reset_password', [UserController::class, 'reset_password']);
    Route::post('users/{user}/activate',       [UserController::class, 'user_status_toggle']);
});