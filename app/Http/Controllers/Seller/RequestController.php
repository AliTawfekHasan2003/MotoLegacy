<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\PurchaseRequest;
use App\Models\RentalRequest;
use App\Http\Resources\PurchaseRequestResource;
use App\Http\Resources\RentalRequestResource;
use App\Services\SseNotifier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RequestController extends Controller
{
    // ─── Purchase ──────────────────────────────────────────────

    /**
     * @OA\Get(
     *     path="/seller/incoming-purchase-requests",
     *     tags={"Seller - Requests"},
     *     summary="List purchase requests for seller's cars",
     *     security={{"bearer_token":{}}},
     *     @OA\Response(response=200, description="Successful operation"),
     * )
     */
    public function incomingPurchaseRequests()
    {
        $requests = PurchaseRequest::whereHas('car', function ($query) {
            $query->where('user_id', Auth::id());
        })->with(['user', 'car.category'])->latest()->get();

        return PurchaseRequestResource::collection($requests);
    }

    /**
     * @OA\Patch(
     *     path="/seller/purchase-requests/{id}/status",
     *     tags={"Seller - Requests"},
     *     summary="Accept or reject a purchase request",
     *     security={{"bearer_token":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"status"},
     *             @OA\Property(property="status", type="string", enum={"accepted","rejected"}),
     *             @OA\Property(property="rejection_reason", type="string", nullable=true, description="Optional. Used when status is rejected")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Status updated"),
     *     @OA\Response(response=403, description="Unauthorized"),
     * )
     */
    public function updatePurchaseStatus(Request $request, PurchaseRequest $purchaseRequest)
    {
        $request->validate([
            'status' => 'required|in:accepted,rejected',
            'rejection_reason' => 'nullable|string|max:1000',
        ]);

        if ($purchaseRequest->car->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $purchaseRequest->update([
            'status' => $request->status,
            'rejection_reason' => $request->status === 'rejected'
                ? $request->rejection_reason
                : null,
        ]);

        $purchaseRequest->load('user', 'car');

        $isRejected = $request->status === 'rejected';
        app(SseNotifier::class)->send(
            $purchaseRequest->user_id,
            'purchase_request_status_updated',
            [
                'request_id' => $purchaseRequest->id,
                'car_id' => $purchaseRequest->car_id,
                'status' => $purchaseRequest->status,
                'rejection_reason' => $purchaseRequest->rejection_reason,
            ],
            $isRejected ? 'تم رفض طلب الشراء' : 'تم قبول طلب الشراء',
            $isRejected
                ? ($purchaseRequest->rejection_reason ?: 'تم رفض طلب الشراء الخاص بك')
                : 'تم قبول طلب الشراء الخاص بك'
        );

        return new PurchaseRequestResource($purchaseRequest);
    }

    // ─── Rental ───────────────────────────────────────────────

    /**
     * @OA\Get(
     *     path="/seller/incoming-rental-requests",
     *     tags={"Seller - Requests"},
     *     summary="List rental requests for seller's cars",
     *     security={{"bearer_token":{}}},
     *     @OA\Response(response=200, description="Successful operation"),
     * )
     */
    public function incomingRentalRequests()
    {
        $requests = RentalRequest::whereHas('car', function ($query) {
            $query->where('user_id', Auth::id());
        })->with(['user', 'car.category'])->latest()->get();

        return RentalRequestResource::collection($requests);
    }

    /**
     * @OA\Patch(
     *     path="/seller/rental-requests/{id}/status",
     *     tags={"Seller - Requests"},
     *     summary="Accept or reject a rental request",
     *     security={{"bearer_token":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"status"},
     *             @OA\Property(property="status", type="string", enum={"accepted","rejected"}),
     *             @OA\Property(property="rejection_reason", type="string", nullable=true, description="Optional. Used when status is rejected")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Status updated"),
     *     @OA\Response(response=403, description="Unauthorized"),
     * )
     */
    public function updateRentalStatus(Request $request, RentalRequest $rentalRequest)
    {
        $request->validate([
            'status' => 'required|in:accepted,rejected',
            'rejection_reason' => 'nullable|string|max:1000',
        ]);

        if ($rentalRequest->car->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $rentalRequest->update([
            'status' => $request->status,
            'rejection_reason' => $request->status === 'rejected'
                ? $request->rejection_reason
                : null,
        ]);

        $rentalRequest->load('user', 'car');

        $isRejected = $request->status === 'rejected';
        app(SseNotifier::class)->send(
            $rentalRequest->user_id,
            'rental_request_status_updated',
            [
                'request_id' => $rentalRequest->id,
                'car_id' => $rentalRequest->car_id,
                'status' => $rentalRequest->status,
                'rejection_reason' => $rentalRequest->rejection_reason,
            ],
            $isRejected ? 'تم رفض طلب الإيجار' : 'تم قبول طلب الإيجار',
            $isRejected
                ? ($rentalRequest->rejection_reason ?: 'تم رفض طلب الإيجار الخاص بك')
                : 'تم قبول طلب الإيجار الخاص بك'
        );

        return new RentalRequestResource($rentalRequest);
    }
}
