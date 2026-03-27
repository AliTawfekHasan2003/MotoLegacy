<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

use App\Models\User;
use Spatie\Permission\Models\Role;

use App\Http\Resources\UserResource;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
        $this->middleware('permission:users.read|users.write|users.delete')->only('index', 'show');
        $this->middleware('permission:users.write')->only('store', 'update', 'reset_password', 'user_status_toggle');
        $this->middleware('permission:users.delete')->only('destroy');
    }

    /**
     * @OA\Get(
     *   path="/admin/users",
     *   description="Get all users",
     *   @OA\Parameter(in="query", name="search", required=false, @OA\Schema(type="string")),
     *   @OA\Parameter(in="query", name="role_id", required=false, @OA\Schema(type="integer")),
     *   @OA\Parameter(in="query", name="is_active", required=false, @OA\Schema(type="integer",enum={0, 1})),
     *   @OA\Parameter(in="query", name="start_date", required=false, @OA\Schema(type="date")),
     *   @OA\Parameter(in="query", name="end_date", required=false, @OA\Schema(type="date")),
     *   @OA\Parameter(in="query", name="with_paginate", required=false, @OA\Schema(type="integer",enum={0, 1})),
     *   @OA\Parameter(in="query", name="per_page", required=false, @OA\Schema(type="integer")),
     *   operationId="get_users",
     *   security={{"bearer_token": {} }},
     *   tags={"Admin Users"},
     *   @OA\Response(response=200, description="Success"),
     * )
    */
    public function index(Request $request)
    {
        $request->validate([
            'search'              => ['nullable', 'string'],
            'role_id'             => ['nullable', 'exists:roles,id'],
            'is_active'           => ['nullable', 'integer', 'in:1,0'],
            'start_date'          => ['nullable', 'date_format:Y-m-d'],
            'end_date'            => ['nullable', 'date_format:Y-m-d'],
            'with_paginate'       => ['nullable', 'integer', 'in:0,1'],
            'per_page'            => ['nullable', 'integer', 'min:1']
        ]);

        $q = User::query();

        if($request->has('is_active')){
            $q->where('is_active', $request->is_active);
        }

        if($request->start_date)
            $q->where('created_at','>=', $request->start_date);
        if($request->end_date)
            $q->where('created_at','<=', $request->end_date);

        if ($request->search) {
            $q->where(function ($query) use ($request) {
                $query->where('name', 'like', '%' . $request->search . '%')
                        ->orWhere('email', 'like', '%' . $request->search . '%')
                        ->orWhere('phone', 'like', '%' . $request->search . '%')
                        ->orWhere('id', $request->search);
            });
        }
        
        if($request->role_id){
            $q->where('role_id', $request->role_id);
        }

        $user = ($request->with_paginate === '0')
            ? $q->with('roleModel')->get()
            : $q->with('roleModel')->paginate($request->per_page ?? 10);

        return UserResource::collection($user);
    }

    /**
     * @OA\Post(
     * path="/admin/users",
     * tags={"Admin Users"},
     * security={{"bearer_token": {} }},
     * description="Create new user.",
     * operationId="CreateUser",
     *   @OA\RequestBody(
     *       required=true,
     *       @OA\MediaType(
     *           mediaType="multipart/form-data",
     *           @OA\Schema(
     *              required={"name","email","password","phone","role_id"},
     *              @OA\Property(property="name", type="string"),
     *              @OA\Property(property="email",format="email", type="string"),
     *              @OA\Property(property="password", type="string"),
     *              @OA\Property(property="password_confirmation", type="string"),
     *              @OA\Property(property="phone", type="string"),
     *              @OA\Property(property="birth_date", type="string", format="date"),
     *              @OA\Property(property="address", type="string"),
     *              @OA\Property(property="license_number", type="string"),
     *              @OA\Property(property="license_expiry_date", type="string", format="date"),
     *              @OA\Property(property="business_type", type="string"),
     *              @OA\Property(property="role_id", type="integer"),
     *           )
     *       )
     *   ),
     * @OA\Response(response=200, description="successful operation"),
     * )
    */
    public function store(Request $request)
    {
        $request->validate([
            'name'              => ['required', 'string'],
            'email'             => ['required', 'string', 'email', 'unique:users'],
            'password'          => ['required', 'string', 'min:6', 'confirmed'],
            'phone'             => ['required', 'unique:users'],
            'role_id'           => ['required', 'integer', 'exists:roles,id'],
            'birth_date'        => ['nullable', 'date'],
            'address'           => ['nullable', 'string'],
            'license_number'    => ['nullable', 'string'],
            'license_expiry_date' => ['nullable', 'date'],
            'business_type'     => ['nullable', 'string'],
        ]);

        $user = User::create([
            'name'               => $request->name,
            'email'              => $request->email,
            'phone'              => $request->phone,
            'password'           => Hash::make($request->password),
            'birth_date'         => $request->birth_date,
            'address'            => $request->address,
            'license_number'     => $request->license_number,
            'license_expiry_date' => $request->license_expiry_date,
            'business_type'      => $request->business_type,
            'role_id'            => $request->role_id,
            'is_active'          => true,
            'email_verified_at'  => now(),
        ]);
        
        $role_name = \Spatie\Permission\Models\Role::find($request->role_id)->name;
        $user->assignRole($role_name);

        return response()->json(new UserResource($user));
    }
    
    /**
     * @OA\Get(
     *   path="/admin/users/{id}",
     *   description="Get specific user",
     *   @OA\Parameter(in="path", name="id", required=true, @OA\Schema(type="string")),
     *   operationId="show_user",
     *   tags={"Admin Users"},
     *   security={{"bearer_token": {} }},
     *   @OA\Response(response=200, description="Success"),
     * )
    */
    public function show(User $user)
    {
        return response()->json(new UserResource($user));
    }

    /**
     * @OA\Post(
     *   path="/admin/users/{id}",
     *   description="Edit user",
     *   @OA\Parameter(in="path", name="id", required=true, @OA\Schema(type="string")),
     *   tags={"Admin Users"},
     *   operationId="edit_user",
     *   security={{"bearer_token": {} }},
     *   @OA\RequestBody(
     *     required=true,
     *     @OA\MediaType(
     *       mediaType="multipart/form-data",
     *       @OA\Schema(
     *              @OA\Property(property="name", type="string"),
     *              @OA\Property(property="email",format="email", type="string"),
     *              @OA\Property(property="phone", type="string"),
     *              @OA\Property(property="birth_date", type="string", format="date"),
     *              @OA\Property(property="address", type="string"),
     *              @OA\Property(property="license_number", type="string"),
     *              @OA\Property(property="license_expiry_date", type="string", format="date"),
     *              @OA\Property(property="business_type", type="string"),
     *              @OA\Property(property="role_id", type="integer"),
     *              @OA\Property(property="_method", type="string", format="string", example="PUT"),
     *       )
     *     )
     *   ),
     *   @OA\Response(response="200", description="Success"),
     * )
    */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name'                  => ['required', 'string'],
            'email'                 => ['required', 'string', 'email', Rule::unique('users', 'email')->ignore($user->id)],
            'phone'                 => ['required', Rule::unique('users', 'phone')->ignore($user->id)],
            'role_id'               => ['exists:roles,id'],
            'birth_date'            => ['nullable', 'date'],
            'address'               => ['nullable', 'string'],
            'license_number'        => ['nullable', 'string'],
            'license_expiry_date'   => ['nullable', 'date'],
            'business_type'         => ['nullable', 'string'],
        ]);

        $user->update($request->only([
            'name', 'email', 'phone', 'role_id', 'birth_date', 'address', 'license_number', 'license_expiry_date', 'business_type'
        ]));

        if($request->password){
            $user->password = Hash::make($request->password);
            $user->save();
        }

        if($request->role_id){
            $role_name = \Spatie\Permission\Models\Role::find($request->role_id)->name;
            $user->syncRoles($role_name);
        }

        return response()->json(new UserResource($user));
    }
    
    /**
     * @OA\Delete(
     *   path="/admin/users/{id}",
     *   description="Delete user",
     *   @OA\Parameter(in="path", name="id", required=true, @OA\Schema(type="string")),
     *   operationId="delete_user",
     *   tags={"Admin Users"},
     *   security={{"bearer_token": {} }},
     *   @OA\Response(response=200, description="Success")
     * )
    */
    public function destroy(User $user)
    {
        $user->delete();

        return response()->json(null,204);
    }

    /**
     * @OA\Post(
     * path="/admin/users/{id}/reset_password",
     * description="reset user password.",
     *   @OA\Parameter(in="path", name="id", required=true, @OA\Schema(type="string")),
     * tags={"Admin Users"},
     * security={{"bearer_token": {} }},
     *   @OA\RequestBody(
     *     required=true,
     *     @OA\MediaType(
     *       mediaType="multipart/form-data",
     *       @OA\Schema(
     *              required={"password","password_confirmation"},
     *              @OA\Property(property="password", type="string"),
     *              @OA\Property(property="password_confirmation", type="string"),
     *       )
     *     )
     *   ),
     * @OA\Response(response=200, description="successful operation"),
     * )
    */
    public function reset_password(Request $request, User $user)
    {
        $request->validate([
            'password'         => ['required', 'string', 'min:6', 'confirmed'],
        ]);
        $user->update(['password'  => Hash::make($request->password),]);
        return response()->json(new UserResource($user), 200);
    }

    /**
     * @OA\Post(
     * path="/admin/users/{id}/activate",
     * description="activate the user.",
     *   @OA\Parameter(in="path", name="id", required=true, @OA\Schema(type="string")),
     * tags={"Admin Users"},
     * security={{"bearer_token": {} }},
     * @OA\Response(response=200, description="successful operation"),
     * )
    */
    public function user_status_toggle(User $user)
    {
        if($user->is_active)
            DB::table('personal_access_tokens')->where('tokenable_id', $user->id)->delete();

        $user->update(['is_active' => !$user->is_active]);
        return response()->json(new UserResource($user), 200);
    }
}
