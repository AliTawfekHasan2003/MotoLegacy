<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use App\Http\Resources\PermissionResource;

class PermissionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
        $this->middleware('permission:roles.read|roles.write')->only(['get_all_permissions','get_permissions']);
        $this->middleware('permission:roles.write')->only(['set_permissions']);
    }

    /**
     * @OA\Get(
     *   path="/admin/permissions",
     *   description="get all permissions",
     *   tags={"Permissions"},
     *   security={{"bearer_token": {} }},
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
     *  )
     *  )
    */
    public function get_all_permissions()
    {
        $permissions = Permission::all();

        return PermissionResource::collection($permissions);
    }

    /**
     * @OA\Get(
     *   path="/admin/permissions/me",
     *   description="Get my permissions",
     *   tags={"Permissions"},
     *   security={{"bearer_token": {} }},
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

    public function my_permissions()
    {
        $user = to_user(Auth::user());
        $permissions = $user->getPermissionsViaRoles();
        return PermissionResource::collection($permissions);
    }

    /**
     * @OA\Post(
     *   path="/admin/roles/{role}/permissions",
     *   summary="Set permissions for a role",
     *   tags={"Permissions"},
     *   security={{"bearer_token": {}}},
     *   @OA\Parameter(
     *     name="role",
     *     in="path",
     *     description="The ID of the role",
     *     required=true,
     *     @OA\Schema(
     *       type="integer"
     *     )
     *   ),
     *   @OA\RequestBody(
     *     required=true,
     *     @OA\MediaType(
     *       mediaType="multipart/form-data",
     *       @OA\Schema(
     *         @OA\Property(
     *           property="permissions[0]",
     *           type="string",
     *           default="",
     *           description="The names of the permissions"
     *         ),
     *         @OA\Property(
     *           property="permissions[1]",
     *           type="string",
     *            default="",
     *           description="The names of the permissions"
     *         ),
     *         @OA\Property(
     *           property="permissions[2]",
     *           type="string",
     *            default="",
     *           description="The names of the permissions"
     *         ),
     *       )
     *     )
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

    public function set_permissions(Request $request, Role $role)
    {
        $request->validate([
            'permissions' => ['array'],
            'permissions.*' => ['string', 'exists:permissions,name'],
        ]);

        if ($role->name == 'admin' || $role->name == 'user')
            throw new BadRequestHttpException(__('error_messages.Sorry, You can not modify this role'));


        $permissionNames = $request->input('permissions', []);
        $permissions = Permission::whereIn('name', $permissionNames)->pluck('id');

        $role->syncPermissions($permissions);

        return PermissionResource::collection($role->permissions);
    }

    /**
     * @OA\Get(
     *   path="/admin/roles/{role}/permissions",
     *   summary="Get permissions for a role",
     *   tags={"Permissions"},
     *   security={{"bearer_token": {}}},
     *   @OA\Parameter(
     *     name="role",
     *     in="path",
     *     description="The ID of the role",
     *     required=true,
     *     @OA\Schema(
     *       type="integer"
     *     )
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

    public function get_permissions(Role $role)
    {
        return PermissionResource::collection($role->permissions);
    }
}
