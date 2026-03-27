<?php

namespace App\Http\Controllers;

use App\Models\Car;
use App\Http\Resources\CarResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    /**
     * @OA\Get(
     *     path="/favorites",
     *     tags={"User - Favorites"},
     *     summary="List user's favorite cars",
     *     security={{"bearer_token":{}}},
     *     @OA\Response(response=200, description="Successful operation"),
     * )
     */
    public function index()
    {
        $favorites = Auth::user()
            ->favorites()
            ->with(['owner', 'category'])
            ->get();

        return CarResource::collection($favorites);
    }

    /**
     * @OA\Post(
     *     path="/favorites",
     *     tags={"User - Favorites"},
     *     summary="Toggle a car in user's favorites",
     *     security={{"bearer_token":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"car_id"},
     *                 @OA\Property(
     *                     property="car_id",
     *                     type="integer",
     *                     example=1,
     *                     description="ID of the car"
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Toggled successfully"
     *     ),
     * )
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'car_id' => 'required|integer|exists:cars,id'
        ]);

        $user = Auth::user();

        $exists = $user->favorites()
            ->where('car_id', $validated['car_id'])
            ->exists();

        if ($exists) {
            $user->favorites()->detach($validated['car_id']);

            return response()->json([
                'status' => false,
                'message' => 'Removed from favorites'
            ]);
        }

        $user->favorites()->attach($validated['car_id']);

        return response()->json([
            'status' => true,
            'message' => 'Added to favorites'
        ]);
    }
}