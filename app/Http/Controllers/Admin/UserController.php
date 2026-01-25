<?php

namespace App\Http\Controllers\Admin;

use App\Exports\UserExport;
use App\Http\Controllers\Controller;
use App\Http\Resources\AddressResource;
use App\Http\Resources\ProductResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

use App\Models\User;
use Spatie\Permission\Models\Role;

use App\Http\Resources\UserResource;
use App\Models\Address;
use App\Models\Favorite;
use App\Models\FCMToken;
use App\Models\Product;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
        $this->middleware('permission:users.read|users.write|users.delete')->only('index', 'show', 'export', 'get_user_favorites', 'get_user_address');
        $this->middleware('permission:users.write')->only('store', 'update', 'reset_password', 'user_status_toggle');
        $this->middleware('permission:users.delete')->only('destroy');
    }
 
    /**
     * @OA\Get(
     *   path="/admin/users",
     *   description="Get all users",
     *   @OA\Parameter(
     *     in="query",
     *     name="q",
     *     required=false,
     *     @OA\Schema(type="string"),
     *   ),
     *   @OA\Parameter(
     *     in="query",
     *     name="role_id",
     *     required=false,
     *     @OA\Schema(type="string"),
     *   ),
     * @OA\Parameter(
     *     in="query",
     *     name="status",
     *     required=false,
     *     @OA\Schema(type="integer",enum={0, 1})
     *   ),
     * @OA\Parameter(
     *     in="query",
     *     name="start_date",
     *     required=false,
     *     @OA\Schema(type="date")
     *   ),
     * @OA\Parameter(
     *     in="query",
     *     name="end_date",
     *     required=false,
     *     @OA\Schema(type="date")
     *   ),
     * @OA\Parameter(
     *     in="query",
     *     name="type",
     *     required=false,
     *     @OA\Schema(type="string",enum={"employee"})
     *   ),
     * @OA\Parameter(
     *     in="query",
     *     name="with_paginate",
     *     required=false,
     *     @OA\Schema(type="integer",enum={0, 1})
     *   ),
     *   @OA\Parameter(
     *     in="query",
     *     name="per_page",
     *     required=false,
     *     @OA\Schema(type="integer"),
     *   ),
     *   operationId="get_users",
     *   security={{"bearer_token": {} }},
     *   tags={"Admin - Users"},
     *   @OA\Response(
     *     response=200,
     *     description="Success",
     *   ),
     * )
    */
    public function index(Request $request)
    {
        $request->validate([
            'q'                   => ['string'],
            'role_id'             => ['exists:roles,id'],
            'status'              => ['integer', 'in:1,0'],
            'start_date'          => ['date_format:Y-m-d'],
            'end_date'            => ['date_format:Y-m-d'],
            'type'                => ['in:employee'],
            'with_paginate'       => ['integer', 'in:0,1'],
            'per_page'            => ['integer', 'min:1']
        ]);

        $q = User::query();

        if($request->status === '0'){
            $q->where('status', false);
        }else if ($request->status === '1') {
            $q->where('status', true);
        }

        if($request->start_date)
            $q->where('created_at','>=', $request->start_date);
        if($request->end_date)
            $q->where('created_at','<=', $request->end_date);

        if ($request->q) {
            $q->where(function ($query) use ($request) {
                $query->where('name', 'like', '%' . $request->q . '%')
                        ->orWhere('email', 'like', '%' . $request->q . '%')
                        ->orWhere('phone', 'like', '%' . $request->q . '%')
                        ->orWhere('id', $request->q);
            });
        }
        
        if($request->role_id){
            $user_ids = DB::table('model_has_roles')->where('model_type', User::class)
                            ->where('role_id', $request->role_id)->pluck('model_id');
            $q->whereIn('id', $user_ids);
        }

        if($request->type == 'employee'){
            $user_ids = DB::table('model_has_roles')->where('model_type', User::class)
                        ->whereNotIn('role_id', [2, 3])->pluck('model_id');
            $q->whereIn('id', $user_ids);
        }

        if ($request->with_paginate === '0')
            $user = $q->with('permissions')->get();
        else
            $user = $q->with('permissions')->paginate($request->per_page ?? 10);

        return UserResource::collection($user);
    }

    /**
     * @OA\Post(
     * path="/admin/users",
     * tags={"Admin - Users"},
     * security={{"bearer_token": {} }},
     * description="Create new user.",
     * operationId="CreateUser",
     *   @OA\RequestBody(
     *       required=true,
     *       @OA\MediaType(
     *           mediaType="multipart/form-data",
     *           @OA\Schema(
     *              required={"name","email","password","role_id"},
     *              @OA\Property(property="name", type="string"),
     *              @OA\Property(property="email",format="email", type="string"),
     *              @OA\Property(property="password", type="string"),
     *              @OA\Property(property="password_confirmation", type="string"),
     *              @OA\Property(property="phone_country_id", type="integer"),
     *              @OA\Property(property="phone", type="string"),
     *              @OA\Property(property="country_id", type="integer"),
     *              @OA\Property(property="summary", type="string"),
     *              @OA\Property(property="image", type="file"),
     *              @OA\Property(property="role_id", type="integer"),
     *           )
     *       )
     *   ),
     * @OA\Response(
     *     response=200,
     *     description="successful operation",
     *  ),
     *  )
    */
    public function store(Request $request)
    {
        $request->validate([
            'name'              => ['required', 'string'],
            'email'             => ['required', 'string', 'email', 'unique:users'],
            'phone_country_id'  => ['integer', 'exists:countries,id'],
            'phone'             => ['required', 'unique:users'],
            'password'          => ['required', 'string', 'min:6', 'confirmed'],
            'country_id'        => ['integer', 'exists:countries,id'],
            'summary'           => ['string'],
            'image'             => ['image'],
            'role_id'           => ['required', 'integer', 'exists:roles,id']
        ]);

        $verified = null;
        if(in_array($request->role_id, [2, 3]))
            $verified = now();

        $image = null;
        if($request->image)
            $image = upload_file($request->image, 'users', 'user');

        $user = User::create([
            'name'               => $request->name,
            'email'              => $request->email,
            'phone_country_id'   => $request->phone_country_id,
            'phone'              => $request->phone,
            'country_id'         => $request->country_id,
            'summary'            => $request->summary,
            'password'           => Hash::make($request->password),
            'email_verified_at'  => $verified,
            'image'              => $image,
        ]);
        $role_name = Role::find($request->role_id)->name;
        $user->assignRole($role_name);

        return response()->json(new UserResource($user));
    }
    
    /**
     * @OA\Get(
     *   path="/admin/users/{id}",
     *   description="Get specific user",
     *   @OA\Parameter(
     *     in="path",
     *     name="id",
     *     required=true,
     *     @OA\Schema(type="string"),
     *   ),
     *   operationId="show_user",
     *   tags={"Admin - Users"},
     *   security={{"bearer_token": {} }},
     *   @OA\Response(
     *     response=200,
     *     description="Success"
     *   ),
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
     *   @OA\Parameter(
     *     in="path",
     *     name="id",
     *     required=true,
     *     @OA\Schema(type="string"),
     *   ),
     *   tags={"Admin - Users"},
     *   operationId="edit_user",
     *   security={{"bearer_token": {} }},
     *   @OA\RequestBody(
     *     required=true,
     *     @OA\MediaType(
     *       mediaType="multipart/form-data",
     *       @OA\Schema(
     *              required={"name","email","phone","role_id"},
     *              @OA\Property(property="name", type="string"),
     *              @OA\Property(property="email",format="email", type="string"),
     *              @OA\Property(property="phone_country_id", type="integer"),
     *              @OA\Property(property="phone", type="string"),
     *              @OA\Property(property="country_id", type="integer"),
     *              @OA\Property(property="summary", type="string"),
     *              @OA\Property(property="image", type="file"),
     *              @OA\Property(property="role_id", type="integer"),
     *         @OA\Property(property="_method", type="string", format="string", example="PUT"),
     *       )
     *     )
     *   ),
     *   @OA\Response(
     *     response="200",
     *     description="Success"
     *   ),
     * )
    */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name'                  => ['required', 'string'],
            'email'                 => ['required', 'string', 'email', Rule::unique('users', 'email')->ignore($user->id)],
            'phone_country_id'      => ['integer', 'exists:countries,id'],
            'phone'                 => ['required', Rule::unique('users', 'phone')->ignore($user->id)],
            'country_id'            => ['integer', 'exists:countries,id'],
            'summary'               => ['string'],
            'image'                 => [''],
            'role_id'               => ['exists:roles,id'],
        ]);

        $role_name = $request->role_id?Role::find($request->role_id)->name:$user->role();

        $image = null;
        if($request->image){
            if($request->image == $user->image){
                $image = $user->image;
            }else{
                if(!is_file($request->image))
                    throw ValidationException::withMessages(['image' => __('Image should be a file')]);
                $image = upload_file($request->image, 'users', 'user');
            }
        }

        $user->name = $request->name;
        $user->email = $request->email;
        $user->phone_country_id = $request->phone_country_id;
        $user->phone = $request->phone;
        $user->country_id = $request->country_id;
        $user->summary = $request->summary;
        $user->image = $image;

        if($request->password){
            $user->password = Hash::make($request->password);
        }

        $user->save();

        if($request->role_id){
            $user->syncRoles($role_name);
        }

        return response()->json(new UserResource($user));
    }
    
    /**
     * @OA\Delete(
     *   path="/admin/users/{id}",
     *   description="Delete user",
     *   @OA\Parameter(
     *     in="path",
     *     name="id",
     *     required=true,
     *     @OA\Schema(type="string"),
     *   ),
     *   operationId="delete_user",
     *   tags={"Admin - Users"},
     *   security={{"bearer_token": {} }},
     *   @OA\Response(
     *     response=200,
     *     description="Success"
     *   )
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
     *   @OA\Parameter(
     *     in="path",
     *     name="id",
     *     required=true,
     *     @OA\Schema(type="string"),
     *   ),
     * tags={"Admin - Users"},
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
     * @OA\Response(
     *    response=200,
     *    description="successful operation",
     *     ),
     * )
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
     *   @OA\Parameter(
     *     in="path",
     *     name="id",
     *     required=true,
     *     @OA\Schema(type="string"),
     *   ),
     * tags={"Admin - Users"},
     * security={{"bearer_token": {} }},
     * @OA\Response(
     *    response=200,
     *    description="successful operation",
     *     ),
     * )
     * )
    */
    public function user_status_toggle(User $user)
    {
        if($user->status)
            DB::table('personal_access_tokens')->where('tokenable_id', $user->id)->delete();

        $user->update(['status' => !$user->status]);
        return response()->json(new UserResource($user), 200);
    }
}
