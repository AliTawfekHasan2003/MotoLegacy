<?php

namespace App\Http\Controllers\Admin;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


use App\Http\Controllers\Controller;
use App\Http\Resources\CarResource;
use App\Models\Car;

class CarController extends Controller
{

    /**
     * @OA\Get(
     *     path="/admin/cars",
     *     tags={"Admin - Cars"},
     *     security={{"bearer_token":{}}},
     *     summary="List all visible cars with optional filters",
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
        $user = to_user(Auth::user());

        $query = Car::where('user_id', $user->id)->with(['owner', 'category'])->where('status', '!=', 'hidden');

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
     *     path="/admin/cars/{id}",
     *     tags={"Admin - Cars"},
     *     security={{"bearer_token":{}}},
     *     summary="Get car details",
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Successful operation"),
     * )
     */
    public function show(Car $car)
    {
        return new CarResource($car->load('owner', 'category'));
    }

    /**
     * @OA\Post(
     *     path="/admin/cars/{id}/approval",
     *     tags={"Admin - Cars"},
     *     summary="Approve or reject car",
     *     security={{"bearer_token":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *             required={"status"},
     *             @OA\Property(property="status", type="string", enum={"approved","rejected"}),
     *                 @OA\Property(property="_method", type="string", example="PATCH"),
     *         )
     * 
     *       )
     *     ),
     *     @OA\Response(response=200, description="Car status updated")
     * )
     */
    public function approveCar(Request $request, Car $car)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected'
        ]);

        $car->update([
            'approval_status' => $request->status
        ]);

        return response()->json([
            'message' => 'Car status updated successfully',
            'data' => $car
        ]);
    }
}
