<?php

namespace App\Http\Controllers;

use App\Models\Car;
use App\Models\PurchaseRequest;
use App\Models\RentalRequest;
use App\Http\Resources\PurchaseRequestResource;
use App\Http\Resources\RentalRequestResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class RequestController extends Controller
{
    /**
     * @OA\Post(
     *     path="/purchase-requests",
     *     tags={"User - Requests - Rental"},
     *     summary="Submit a purchase request",
     *     security={{"bearer_token":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"car_id"},
     *                 @OA\Property(property="car_id", type="integer"),
     *                 @OA\Property(property="meeting_location", type="string"),
     *                 @OA\Property(property="meeting_date", type="string", format="date"),
     *                 @OA\Property(property="notes", type="string"),
     *                 @OA\Property(property="id_number", type="string"),
     *                 @OA\Property(property="payment_method", type="string"),
     *             )
     *         )
     *     ),
     *     @OA\Response(response=201, description="Request submitted"),
     * )
     */
    public function storePurchase(Request $request)
    {
        $request->validate([
            'car_id' => 'required|exists:cars,id',
            'meeting_location' => 'nullable|string',
            'meeting_date' => 'nullable|date',
            'notes' => 'nullable|string',
            'id_number' => 'nullable|string',
            'payment_method' => 'nullable|string',
        ]);

        $car = Car::findOrFail($request->car_id);

        if ($car->type !== 'sale') {
            return response()->json(['message' => 'This car is not for sale'], 400);
        }

        $purchasePrice = $car->purchase_price;

        $purchaseRequest = Auth::user()->purchaseRequests()->create([
            'car_id' => $car->id,
            'offered_price' => $purchasePrice, 
            'meeting_location' => $request->meeting_location ?? null,
            'meeting_date' => $request->meeting_date ?? null,
            'notes' => $request->notes ?? null,
            'id_number' => $request->id_number ?? null,
            'payment_method' => $request->payment_method ?? null,
        ]);

        return new PurchaseRequestResource($purchaseRequest->load('user', 'car'));
    }
    /**
     * @OA\Get(
     *     path="/my-purchase-requests",
     *     tags={"User - Requests - Rental"},
     *     summary="List purchase requests made by user",
     *     security={{"bearer_token":{}}},
     *     @OA\Response(response=200, description="Successful operation"),
     * )
     */
    public function myPurchaseRequests()
    {
        $requests = Auth::user()
            ->purchaseRequests()
            ->with(['car.owner', 'car.category'])
            ->latest()
            ->get();

        return PurchaseRequestResource::collection($requests);
    }

    /**
     * @OA\Post(
     *     path="/rental-requests",
     *     tags={"User - Requests - Rental"},
     *     summary="Submit a rental request",
     *     security={{"bearer_token":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"car_id", "start_date", "end_date"},
     *                 @OA\Property(property="car_id", type="integer"),
     *                 @OA\Property(property="start_date", type="string", format="date"),
     *                 @OA\Property(property="end_date", type="string", format="date"),
     *                 @OA\Property(property="pickup_location", type="string"),
     *                 @OA\Property(property="return_location", type="string"),
     *                 @OA\Property(property="notes", type="string"),
     *                 @OA\Property(property="id_number", type="string"),
     *                 @OA\Property(property="payment_method", type="string")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=201, description="Request submitted"),
     * )
     */
    public function storeRental(Request $request)
    {
        $request->validate([
            'car_id' => 'required|exists:cars,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'pickup_location' => 'nullable|string',
            'return_location' => 'nullable|string',
            'notes' => 'nullable|string',
            'id_number' => 'nullable|string',
            'payment_method' => 'nullable|string',
        ]);

        $car = Car::findOrFail($request->car_id);

        if ($car->type !== 'rent') {
            return response()->json(['message' => 'This car is not for rent'], 400);
        }

        $start = Carbon::parse($request->start_date);
        $end = Carbon::parse($request->end_date);
        $days = $start->diffInDays($end) + 1;
        $totalPrice = $days * $car->rental_price_per_day;

        $rentalRequest = Auth::user()->rentalRequests()->create([
            'car_id' => $car->id,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'pickup_location' => $request->pickup_location ?? null,
            'return_location' => $request->return_location ?? null,
            'notes' => $request->notes ?? null,
            'id_number' => $request->id_number ?? null,
            'payment_method' => $request->payment_method ?? null,
            'total_price' => $totalPrice,
        ]);

        return new RentalRequestResource($rentalRequest->load('user', 'car'));
    }

    /**
     * @OA\Get(
     *     path="/my-rental-requests",
     *     tags={"User - Requests - Rental"},
     *     summary="List rental requests made by user",
     *     security={{"bearer_token":{}}},
     *     @OA\Response(response=200, description="Successful operation"),
     * )
     */
    public function myRentalRequests()
    {
        $requests = Auth::user()
            ->rentalRequests()
            ->with(['car.owner', 'car.category'])
            ->latest()
            ->get();

        return RentalRequestResource::collection($requests);
    }
}
