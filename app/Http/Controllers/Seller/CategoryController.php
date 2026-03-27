<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Http\Resources\CategoryResource;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * @OA\Get(
     *     path="/seller/categories",
     *     tags={"Seller - Categories"},
     *     security={{"bearer_token":{}}},
     *     summary="List all categories",
     *     @OA\Response(response=200, description="Successful operation"),
     * )
     */
    public function index()
    {
        return CategoryResource::collection(Category::all());
    }

    /**
     * @OA\Get(
     *     path="/seller/categories/{id}",
     *     tags={"Seller - Categories"},
     *     security={{"bearer_token":{}}},
     *     summary="Get category details",
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Successful operation"),
     * )
     */
    public function show(Category $category)
    {
        return new CategoryResource($category);
    }
}
