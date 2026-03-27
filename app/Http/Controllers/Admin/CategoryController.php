<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Http\Resources\CategoryResource;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * @OA\Post(
     *     path="/admin/categories",
     *     tags={"Admin Categories"},
     *     summary="Create a new category (Admin only)",
     *     security={{"bearer_token":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *             required={"name"},
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="description", type="string")
     *         )
     * )
     *     ),
     *     @OA\Response(response=201, description="Category created"),
     * )
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $category = Category::create($request->all());

        return new CategoryResource($category);
    }

    /**
     * @OA\Post(
     *     path="/admin/categories/{id}",
     *     tags={"Admin Categories"},
     *     summary="Update a category (Admin only)",
     *     security={{"bearer_token":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *             required={"name"},
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="description", type="string"),
     *                 @OA\Property(property="_method", type="string", example="PATCH"),
     *         )
     *         )
     *     ),
     *     @OA\Response(response=200, description="Category updated"),
     * )
     */
    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $category->update($request->all());

        return new CategoryResource($category);
    }

    /**
     * @OA\Delete(
     *     path="/admin/categories/{id}",
     *     tags={"Admin Categories"},
     *     summary="Delete a category (Admin only)",
     *     security={{"bearer_token":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=204, description="Category deleted"),
     * )
     */
    public function destroy(Category $category)
    {
        $category->delete();

        return response()->json(null, 204);
    }

    /**
     * @OA\Get(
     *     path="/admin/categories",
     *     tags={"Admin Categories"},
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
     *     path="/admin/categories/{id}",
     *     tags={"Admin Categories"},
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
