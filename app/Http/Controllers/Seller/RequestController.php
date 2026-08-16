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
     *     summary="List purchase requests for seller's cars (includes rejection_reason)",
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
     *     description="عند الرفض أرسل rejection_reason (اختياري). يُحفظ على الطلب ويُرسل للمشتري في SSE purchase_request_status_updated.",
     *     security={{"bearer_token":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"status"},
     *                 @OA\Property(property="status", type="string", enum={"accepted","rejected"}),
     *                 @OA\Property(property="rejection_reason", type="string", nullable=true, example="السعر غير مناسب", description="سبب الرفض. يُستخدم عند status=rejected"),
     *                 @OA\Property(property="_method", type="string", example="PATCH")
     *             )
     *         ),
     *         @OA\JsonContent(
     *             required={"status"},
     *             @OA\Property(property="status", type="string", enum={"accepted","rejected"}),
     *             @OA\Property(property="rejection_reason", type="string", nullable=true, example="السعر غير مناسب", description="سبب الرفض. يُستخدم عند status=rejected")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Status updated. Response includes rejection_reason", @OA\JsonContent(ref="#/components/schemas/PurchaseRequest")),
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
            $isRejected ? 'Purchase request rejected' : 'Purchase request accepted',
            $isRejected
                ? ($purchaseRequest->rejection_reason ?: 'Your purchase request was rejected')
                : 'Your purchase request was accepted'
        );

        return new PurchaseRequestResource($purchaseRequest);
    }

    // ─── Rental ───────────────────────────────────────────────

    /**
     * @OA\Get(
     *     path="/seller/incoming-rental-requests",
     *     tags={"Seller - Requests"},
     *     summary="List rental requests for seller's cars (includes rejection_reason)",
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
     *     description="عند الرفض أرسل rejection_reason (اختياري). يُحفظ على الطلب ويُرسل للمستأجر في SSE rental_request_status_updated.",
     *     security={{"bearer_token":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"status"},
     *                 @OA\Property(property="status", type="string", enum={"accepted","rejected"}),
     *                 @OA\Property(property="rejection_reason", type="string", nullable=true, example="التواريخ غير متاحة", description="سبب الرفض. يُستخدم عند status=rejected"),
     *                 @OA\Property(property="_method", type="string", example="PATCH")
     *             )
     *         ),
     *         @OA\JsonContent(
     *             required={"status"},
     *             @OA\Property(property="status", type="string", enum={"accepted","rejected"}),
     *             @OA\Property(property="rejection_reason", type="string", nullable=true, example="التواريخ غير متاحة", description="سبب الرفض. يُستخدم عند status=rejected")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Status updated. Response includes rejection_reason", @OA\JsonContent(ref="#/components/schemas/RentalRequest")),
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
            $isRejected ? 'Rental request rejected' : 'Rental request accepted',
            $isRejected
                ? ($rentalRequest->rejection_reason ?: 'Your rental request was rejected')
                : 'Your rental request was accepted'
        );

        return new RentalRequestResource($rentalRequest);
    }
}
