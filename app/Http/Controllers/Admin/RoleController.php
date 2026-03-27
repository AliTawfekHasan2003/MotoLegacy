<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\RoleResource;

class RoleController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
        $this->middleware('permission:roles.read|roles.write|roles.delete|users.read|users.write')->only(['index', 'show']);
        $this->middleware('permission:roles.write')->only(['store', 'update']);
        $this->middleware('permission:roles.delete')->only(['destroy']);
    }

    /**
     * @OA\Get(
     *   path="/admin/roles",
     *   description="get all roles",
     *   tags={"Admin Roles"},
     *   security={{"bearer_token": {} }},
     *   @OA\Parameter(
     *     name="with_paginate",
     *     in="query",
     *     description="Enable pagination (0 = false, 1 = true)",
     *     required=false,
     *     @OA\Schema(
     *       type="integer",
     *       enum={0, 1}
     *     )
     *   ),
     *   @OA\Parameter(
     *     in="query",
     *     name="per_page",
     *     required=false,
     *     @OA\Schema(type="integer"),
     *   ),
     *   @OA\Parameter(
     *     in="query",
     *     name="search",
     *     required=false,
     *     @OA\Schema(type="string"),
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Success",
     *   ),
     *   @OA\Response(
     *     response=401,
     *     description="Unauthenticated",
     *   ),
     *   @OA\Response(
     *     response=403,
     *     description="Forbidden. This action is unauthorized.",
     *   )
     * )
     */
    public function index(Request $request)
    {
        $request->validate([
            'with_paginate' => ['integer', 'in:0,1'],
            'per_page'      => ['integer', 'min:1'],
            'search'        => ['string'],
        ]);

        $searchTerm = $request->query('search');
        $query = Role::query();

        if ($searchTerm) {
            $query->where(function($query) use ($searchTerm) {
                $query->where('name', 'like', '%' . $searchTerm . '%');
            });
        }

        if ($request->with_paginate === '0')
            $roles = $query->with('permissions')->get();
        else
            $roles = $query->with('permissions')->paginate($request->per_page ?? 10);

        return RoleResource::collection($roles);
    }
}
