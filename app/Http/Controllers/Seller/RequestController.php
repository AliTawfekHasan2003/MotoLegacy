<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\PurchaseRequest;
use App\Models\RentalRequest;
use App\Http\Resources\PurchaseRequestResource;
use App\Http\Resources\RentalRequestResource;
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
     *             @OA\Property(property="status", type="string", enum={"accepted","rejected"})
     *         )
     *     ),
     *     @OA\Response(response=200, description="Status updated"),
     *     @OA\Response(response=403, description="Unauthorized"),
     * )
     */
    public function updatePurchaseStatus(Request $request, PurchaseRequest $purchaseRequest)
    {
        $request->validate(['status' => 'required|in:accepted,rejected']);

        if ($purchaseRequest->car->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $purchaseRequest->update(['status' => $request->status]);

        return new PurchaseRequestResource($purchaseRequest->load('user', 'car'));
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
     *             @OA\Property(property="status", type="string", enum={"accepted","rejected"})
     *         )
     *     ),
     *     @OA\Response(response=200, description="Status updated"),
     *     @OA\Response(response=403, description="Unauthorized"),
     * )
     */
    public function updateRentalStatus(Request $request, RentalRequest $rentalRequest)
    {
        $request->validate(['status' => 'required|in:accepted,rejected']);

        if ($rentalRequest->car->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $rentalRequest->update(['status' => $request->status]);

        return new RentalRequestResource($rentalRequest->load('user', 'car'));
    }
}
