<?php

namespace App\Http\Controllers\Admin;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


use App\Http\Controllers\Controller;
use App\Http\Resources\CarResource;
use App\Models\Car;
use App\Support\PriceValidation;
use App\Services\SseNotifier;

class CarController extends Controller
{

    /**
     * @OA\Get(
     *     path="/admin/cars",
     *     tags={"Admin - Cars"},
     *     security={{"bearer_token":{}}},
     *     summary="List cars visible to admin (hidden cars are excluded)",
     *     @OA\Parameter(name="name", in="query", @OA\Schema(type="string")),
     *     @OA\Parameter(name="brand", in="query", @OA\Schema(type="string")),
     *     @OA\Parameter(name="approval_status", in="query", @OA\Schema(type="string", enum={"pending","approved", "rejected"})),
     *     @OA\Parameter(name="type", in="query", @OA\Schema(type="string", enum={"sale","rent"})),
     *     @OA\Parameter(name="year", in="query", @OA\Schema(type="integer")),
     *     @OA\Parameter(name="min_price", in="query", @OA\Schema(type="number")),
     *     @OA\Parameter(name="max_price", in="query", @OA\Schema(type="number")),
     *     @OA\Parameter(name="category_id", in="query", @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Successful operation"),
     * )
     */
    public function index(Request $request)
    {
        $request->validate([
            'min_price' => PriceValidation::filterRules(),
            'max_price' => PriceValidation::filterRules(),
        ]);

        $query = Car::with(['owner', 'category'])->where('status', '!=', 'hidden');

        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }
        if ($request->has('brand')) {
            $query->where('brand', 'like', '%' . $request->brand . '%');
        }
        if ($request->has('type')) {
            $query->where('type', $request->type);
        }
        if ($request->has('year')) {
            $query->where('year', $request->year);
        }

        if ($request->has('approval_status')) {
            $query->where('approval_status', $request->approval_status);
        }

        if ($request->has('min_price')) {
            $query->where(function ($q) use ($request) {
                $q->where('purchase_price', '>=', $request->min_price)
                    ->orWhere('rental_price_per_day', '>=', $request->min_price);
            });
        }
        if ($request->has('max_price')) {
            $query->where(function ($q) use ($request) {
                $q->where('purchase_price', '<=', $request->max_price)
                    ->orWhere('rental_price_per_day', '<=', $request->max_price);
            });
        }
        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        return CarResource::collection($query->latest()->paginate(10));
    }

    /**
     * @OA\Get(
     *     path="/admin/cars/{id}",
     *     tags={"Admin - Cars"},
     *     security={{"bearer_token":{}}},
     *     summary="Get car details (404 if hidden)",
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Successful operation"),
     * )
     */
    public function show(Car $car)
    {
        if ($car->status === 'hidden') {
            abort(404);
        }

        return new CarResource($car->load('owner', 'category'));
    }

    /**
     * @OA\Post(
     *     path="/admin/cars/{id}/approval",
     *     tags={"Admin - Cars"},
     *     summary="Approve or reject car",
     *     description="عند رفض سيارة جديدة أرسل rejection_reason (اختياري). يُحفظ على السيارة ويُرسل للبائع في SSE car_approval_status_updated.",
     *     security={{"bearer_token":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"status"},
     *                 @OA\Property(property="status", type="string", enum={"approved","rejected"}),
     *                 @OA\Property(property="rejection_reason", type="string", nullable=true, example="المستندات غير واضحة", description="سبب رفض السيارة. يُستخدم عند status=rejected"),
     *                 @OA\Property(property="_method", type="string", example="PATCH")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=200, description="Car status updated. Response includes rejection_reason"),
     * )
     */
    public function approveCar(Request $request, Car $car)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected',
            'rejection_reason' => 'nullable|string|max:1000',
        ]);

        $car->update([
            'approval_status' => $request->status,
            'rejection_reason' => $request->status === 'rejected'
                ? $request->rejection_reason
                : null,
        ]);

        $car->load('owner', 'category');

        $isRejected = $request->status === 'rejected';
        app(SseNotifier::class)->send(
            $car->user_id,
            'car_approval_status_updated',
            [
                'car_id' => $car->id,
                'approval_status' => $car->approval_status,
                'rejection_reason' => $car->rejection_reason,
            ],
            $isRejected ? 'Car rejected' : 'Car approved',
            $isRejected
                ? ($car->rejection_reason ?: 'Your car was rejected by admin')
                : 'Your car was approved by admin'
        );

        return response()->json([
            'message' => 'Car status updated successfully',
            'data' => new CarResource($car),
        ]);
    }
}
