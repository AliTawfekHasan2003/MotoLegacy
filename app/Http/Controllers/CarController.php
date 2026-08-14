<?php

namespace App\Http\Controllers;

use App\Models\Car;
use App\Http\Resources\CarResource;
use Illuminate\Http\Request;

/**
 * Public car browsing - available to everyone (no auth required).
 * Seller-specific actions (store, update, destroy, toggle-visibility) are in Seller\CarController.
 */
class CarController extends Controller
{
    /**
     * @OA\Get(
     *     path="/cars",
     *     tags={"User - Cars"},
     *     summary="List approved visible cars (hidden/sold/rented cars are excluded)",
     *     @OA\Parameter(name="name", in="query", @OA\Schema(type="string")),
     *     @OA\Parameter(name="brand", in="query", @OA\Schema(type="string")),
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
        $query = Car::where('approval_status', 'approved')->with(['owner', 'category'])->where('status', '!=', 'hidden');

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
     *     path="/cars/{id}",
     *     tags={"User - Cars"},
     *     summary="Get car details (404 if hidden or not approved)",
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Successful operation"),
     * )
     */
    public function show(Car $car)
    {
        if ($car->status === 'hidden' || $car->approval_status !== 'approved') {
            abort(404);
        }

        return new CarResource($car->load('owner', 'category'));
    }
}
