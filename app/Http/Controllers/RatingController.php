<?php

namespace App\Http\Controllers;

use App\Models\Rating;
use App\Models\User;
use App\Http\Resources\RatingResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RatingController extends Controller
{
    /**
     * @OA\Get(
     *     path="/ratings/{seller_id}",
     *     tags={"User - Ratings & Reviews"},
     *     summary="List all ratings for a specific seller",
     *     @OA\Parameter(
     *         name="seller_id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(response=200, description="Successful operation"),
     * )
     */
    public function index($seller_id)
    {
        $ratings = Rating::where('seller_id', $seller_id)
            ->with(['user', 'seller'])
            ->get();

        return RatingResource::collection($ratings);
    }

    /**
     * @OA\Post(
     *     path="/ratings",
     *     tags={"User - Ratings & Reviews"},
     *     summary="Rate a seller",
     *     security={{"bearer_token":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"seller_id", "rating"},
     *                 
     *                 @OA\Property(
     *                     property="seller_id",
     *                     type="integer",
     *                     example=1,
     *                     description="ID of the seller"
     *                 ),
     *                 @OA\Property(
     *                     property="rating",
     *                     type="integer",
     *                     minimum=1,
     *                     maximum=5,
     *                     example=5,
     *                     description="Rating value from 1 to 5"
     *                 ),
     *                 @OA\Property(
     *                     property="comment",
     *                     type="string",
     *                     example="Great seller, highly recommended"
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(response=200, description="Rating submitted"),
     * )
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'seller_id' => 'required|integer|exists:users,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string',
        ]);

        $seller = User::findOrFail($validated['seller_id']);

        if (!$seller->hasRole('seller')) {
            return response()->json([
                'message' => 'You can only rate sellers'
            ], 400);
        }

        $rating = Auth::user()->ratingsGiven()->updateOrCreate(
            ['seller_id' => $validated['seller_id']],
            [
                'rating' => $validated['rating'],
                'comment' => $validated['comment'] ?? null
            ]
        );

        return new RatingResource($rating->load('user', 'seller'));
    }
}