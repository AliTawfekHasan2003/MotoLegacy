<?php

namespace App\Http\Controllers;

use App\Models\Car;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class StatsController extends Controller
{
    /**
     * @OA\Get(
     *     path="/stats",
     *     tags={"User - Stats"},
     *     summary="Live platform statistics (trust & strength signals)",
     *     description="Returns live counts that show marketplace activity and credibility.",
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="total_users", type="integer", example=150, description="Registered users on the platform"),
     *             @OA\Property(property="total_sellers", type="integer", example=42, description="Active sellers"),
     *             @OA\Property(property="available_cars", type="integer", example=85, description="Approved cars currently visible"),
     *             @OA\Property(property="cars_for_sale", type="integer", example=50, description="Visible cars listed for sale"),
     *             @OA\Property(property="cars_for_rent", type="integer", example=35, description="Visible cars listed for rent"),
     *             @OA\Property(
     *                 property="most_requested_category",
     *                 type="object",
     *                 description="Category with the most purchase + rental requests",
     *                 @OA\Property(property="name", type="string", example="SUV"),
     *                 @OA\Property(property="requests_count", type="integer", example=24)
     *             ),
     *             @OA\Property(
     *                 property="most_requested_brand",
     *                 type="object",
     *                 description="Brand with the most purchase + rental requests",
     *                 @OA\Property(property="name", type="string", example="Toyota"),
     *                 @OA\Property(property="requests_count", type="integer", example=18)
     *             ),
     *         )
     *     ),
     * )
     */
    public function index()
    {
        $visibleCars = Car::where('approval_status', 'approved')
            ->where('status', '!=', 'hidden');

        return response()->json([
            'total_users'              => User::count(),
            'total_sellers'            => User::role('seller')->count(),
            'available_cars'           => (clone $visibleCars)->count(),
            'cars_for_sale'            => (clone $visibleCars)->where('type', 'sale')->count(),
            'cars_for_rent'            => (clone $visibleCars)->where('type', 'rent')->count(),
            'most_requested_category'  => $this->mostRequestedCategory(),
            'most_requested_brand'     => $this->mostRequestedBrand(),
        ]);
    }

    private function requestCarIdsQuery()
    {
        return DB::table('purchase_requests')
            ->select('car_id')
            ->unionAll(
                DB::table('rental_requests')->select('car_id')
            );
    }

    private function mostRequestedCategory(): array
    {
        $row = DB::query()
            ->fromSub($this->requestCarIdsQuery(), 'req')
            ->join('cars', 'cars.id', '=', 'req.car_id')
            ->join('categories', 'categories.id', '=', 'cars.category_id')
            ->select(
                'categories.name',
                DB::raw('COUNT(*) as requests_count')
            )
            ->groupBy('categories.id', 'categories.name')
            ->orderByDesc('requests_count')
            ->first();

        return [
            'name' => $row->name ?? '',
            'requests_count' => (int) ($row->requests_count ?? 0),
        ];
    }

    private function mostRequestedBrand(): array
    {
        $row = DB::query()
            ->fromSub($this->requestCarIdsQuery(), 'req')
            ->join('cars', 'cars.id', '=', 'req.car_id')
            ->whereNotNull('cars.brand')
            ->where('cars.brand', '!=', '')
            ->select(
                'cars.brand as name',
                DB::raw('COUNT(*) as requests_count')
            )
            ->groupBy('cars.brand')
            ->orderByDesc('requests_count')
            ->first();

        return [
            'name' => $row->name ?? '',
            'requests_count' => (int) ($row->requests_count ?? 0),
        ];
    }
}
