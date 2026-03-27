<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Car;
use App\Models\PurchaseRequest;
use App\Models\RentalRequest;
use App\Http\Resources\UserResource;
use App\Http\Resources\PurchaseRequestResource;
use App\Http\Resources\RentalRequestResource;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    /**
     * @OA\Get(
     *     path="/admin/stats",
     *     tags={"Admin - Stats"},
     *     summary="Get system wide statistics",
     *     security={{"bearer_token":{}}},
     *     @OA\Response(response=200, description="Successful operation"),
     * )
     */
    public function stats()
    {
        return response()->json([
            'total_users' => User::count(),
            'total_cars' => Car::count(),
            'total_purchase_requests' => PurchaseRequest::count(),
            'total_rental_requests' => RentalRequest::count(),
            'active_rentals' => RentalRequest::where('status', 'accepted')->count(),
            'sold_cars' => PurchaseRequest::where('status', 'accepted')->count(),
        ]);
    }


    /**
     * @OA\Get(
     *     path="/admin/purchase-requests",
     *     tags={"Admin - Purchase - Requests"},
     *     summary="List all purchase requests in system",
     *     security={{"bearer_token":{}}},
     *     @OA\Response(response=200, description="Successful operation"),
     * )
     */
    public function purchaseRequests()
    {
        $requests = PurchaseRequest::with(['user', 'car.category', 'car.owner'])->latest()->paginate(20);
        return PurchaseRequestResource::collection($requests);
    }

    /**
     * @OA\Get(
     *     path="/admin/rental-requests",
     *     tags={"Admin - Rental - Requests"},
     *     summary="List all rental requests in system",
     *     security={{"bearer_token":{}}},
     *     @OA\Response(response=200, description="Successful operation"),
     * )
     */
    public function rentalRequests()
    {
        $requests = RentalRequest::with(['user', 'car.category', 'car.owner'])->latest()->paginate(20);
        return RentalRequestResource::collection($requests);
    }
}
