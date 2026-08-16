<?php

namespace App\Http\Controllers;

use App\Http\Resources\NotificationResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * @OA\Get(
     *     path="/notifications",
     *     tags={"User - Notifications"},
     *     summary="List notifications for the authenticated buyer/renter",
     *     security={{"bearer_token":{}}},
     *     @OA\Parameter(name="per_page", in="query", @OA\Schema(type="integer", default=20)),
     *     @OA\Response(response=200, description="Paginated notifications"),
     *     @OA\Response(response=401, description="Unauthenticated")
     * )
     *
     * @OA\Get(
     *     path="/seller/notifications",
     *     tags={"Seller - Notifications"},
     *     summary="List notifications for the authenticated seller",
     *     security={{"bearer_token":{}}},
     *     @OA\Parameter(name="per_page", in="query", @OA\Schema(type="integer", default=20)),
     *     @OA\Response(response=200, description="Paginated notifications"),
     *     @OA\Response(response=401, description="Unauthenticated")
     * )
     *
     * @OA\Get(
     *     path="/admin/notifications",
     *     tags={"Admin - Notifications"},
     *     summary="List notifications for the authenticated admin",
     *     security={{"bearer_token":{}}},
     *     @OA\Parameter(name="per_page", in="query", @OA\Schema(type="integer", default=20)),
     *     @OA\Response(response=200, description="Paginated notifications"),
     *     @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function index(Request $request)
    {
        $notifications = Auth::user()
            ->appNotifications()
            ->latest()
            ->paginate($request->integer('per_page', 20));

        return NotificationResource::collection($notifications);
    }
}
