<?php

namespace App\Http\Controllers;

use App\Models\Car;
use App\Models\PurchaseRequest;
use App\Models\RentalRequest;
use App\Http\Resources\PurchaseRequestResource;
use App\Http\Resources\RentalRequestResource;
use App\Services\SseNotifier;
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
     *     description="Notifies the car seller via SSE type purchase_request_created.",
     *     security={{"bearer_token":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"car_id"},
     *                 @OA\Property(property="car_id", type="integer"),
     *                 @OA\Property(property="offered_price", type="number", nullable=true, description="Optional offer price from the buyer"),
     *                 @OA\Property(property="meeting_location", type="string", nullable=true),
     *                 @OA\Property(property="meeting_date", type="string", format="date", nullable=true),
     *                 @OA\Property(property="notes", type="string", nullable=true),
     *                 @OA\Property(property="id_number", type="string", nullable=true),
     *                 @OA\Property(property="payment_method", type="string", nullable=true),
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
            'offered_price' => 'nullable|numeric|min:0',
            'meeting_date' => 'nullable|date',
            'notes' => 'nullable|string',
            'id_number' => 'nullable|string',
            'payment_method' => 'nullable|string',
        ]);

        $car = Car::findOrFail($request->car_id);

        if ($car->status === 'hidden' || $car->approval_status !== 'approved') {
            return response()->json(['message' => 'This car is not available'], 400);
        }

        if ($car->type !== 'sale') {
            return response()->json(['message' => 'This car is not for sale'], 400);
        }

        $purchaseRequest = Auth::user()->purchaseRequests()->create([
            'car_id' => $car->id,
            'offered_price' => $request->offered_price,
            'meeting_location' => $request->meeting_location ?? null,
            'meeting_date' => $request->meeting_date ?? null,
            'notes' => $request->notes ?? null,
            'id_number' => $request->id_number ?? null,
            'payment_method' => $request->payment_method ?? null,
        ]);

        $purchaseRequest->load('user', 'car');

        app(SseNotifier::class)->send(
            (int) $car->user_id,
            'purchase_request_created',
            [
                'request_id' => $purchaseRequest->id,
                'car_id' => $car->id,
                'buyer_id' => $purchaseRequest->user_id,
                'offered_price' => $purchaseRequest->offered_price,
                'status' => $purchaseRequest->status,
            ],
            'طلب شراء جديد',
            'وصلك طلب شراء جديد على سيارة ' . ($car->name ?: '#' . $car->id)
        );

        return new PurchaseRequestResource($purchaseRequest);
    }
    /**
     * @OA\Get(
     *     path="/my-purchase-requests",
     *     tags={"User - Requests - Rental"},
     *     summary="List purchase requests made by user (includes rejection_reason when rejected)",
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
     *     description="Notifies the car seller via SSE type rental_request_created.",
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
     *                 @OA\Property(property="pickup_location", type="string", nullable=true),
     *                 @OA\Property(property="return_location", type="string", nullable=true),
     *                 @OA\Property(property="notes", type="string", nullable=true),
     *                 @OA\Property(property="id_number", type="string", nullable=true),
     *                 @OA\Property(property="payment_method", type="string", nullable=true)
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
            'notes' => 'nullable|string',
            'id_number' => 'nullable|string',
            'payment_method' => 'nullable|string',
        ]);

        $car = Car::findOrFail($request->car_id);

        if ($car->status === 'hidden' || $car->approval_status !== 'approved') {
            return response()->json(['message' => 'This car is not available'], 400);
        }

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

        $rentalRequest->load('user', 'car');

        app(SseNotifier::class)->send(
            (int) $car->user_id,
            'rental_request_created',
            [
                'request_id' => $rentalRequest->id,
                'car_id' => $car->id,
                'renter_id' => $rentalRequest->user_id,
                'start_date' => $rentalRequest->start_date,
                'end_date' => $rentalRequest->end_date,
                'total_price' => $rentalRequest->total_price,
                'status' => $rentalRequest->status,
            ],
            'طلب إيجار جديد',
            'وصلك طلب إيجار جديد على سيارة ' . ($car->name ?: '#' . $car->id)
        );

        return new RentalRequestResource($rentalRequest);
    }

    /**
     * @OA\Get(
     *     path="/my-rental-requests",
     *     tags={"User - Requests - Rental"},
     *     summary="List rental requests made by user (includes rejection_reason when rejected)",
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
