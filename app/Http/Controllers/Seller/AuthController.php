<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AuthController extends Controller
{
    /**
     * @OA\Post(
     *     path="/seller/register",
     *     description="Register a new seller account",
     *     operationId="sellerRegister",
     *     tags={"Seller - Auth"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"name","email","password","password_confirmation","phone","license_number","business_type"},
     *                 @OA\Property(property="name", type="string"),
     *                 @OA\Property(property="email", format="email", type="string"),
     *                 @OA\Property(property="password", type="string"),
     *                 @OA\Property(property="password_confirmation", type="string"),
     *                 @OA\Property(property="phone", type="string"),
     *                 @OA\Property(property="birth_date", type="string", format="date"),
     *                 @OA\Property(property="address", type="string"),
     *                 @OA\Property(property="license_number", type="string"),
     *                 @OA\Property(property="license_expiry_date", type="string", format="date"),
     *                 @OA\Property(property="business_type", type="string"),
     *             )
     *         )
     *     ),
     *     @OA\Response(response=200, description="Seller registered successfully"),
     *     @OA\Response(response=422, description="Validation error"),
     * )
     */
    public function register(Request $request)
    {
        $request->validate([
            'name'                => ['required', 'string'],
            'email'               => ['required', 'string', 'email', 'unique:users'],
            'password'            => ['required', 'string', 'min:6', 'confirmed'],
            'phone'               => ['required', 'string'],
            'birth_date'          => ['nullable', 'date'],
            'address'             => ['nullable', 'string'],
            'license_number'      => ['required', 'string'],
            'license_expiry_date' => ['nullable', 'date'],
            'business_type'       => ['required', 'string'],
        ]);

        $sellerRole = \Spatie\Permission\Models\Role::where('name', 'seller')->firstOrFail();

        $user = User::create([
            'name'                => $request->name,
            'email'               => $request->email,
            'phone'               => $request->phone,
            'password'            => Hash::make($request->password),
            'birth_date'          => $request->birth_date,
            'address'             => $request->address,
            'license_number'      => $request->license_number,
            'license_expiry_date' => $request->license_expiry_date,
            'business_type'       => $request->business_type,
            'role_id'             => $sellerRole->id,
            'is_active'           => true,
        ]);

        $user->assignRole('seller');

        $token = $user->createToken('Sanctum', [])->plainTextToken;

        return response()->json([
            'user'  => new UserResource($user),
            'token' => $token,
        ], 200);
    }

    /**
     * @OA\Post(
     *     path="/seller/login",
     *     description="Login for Sellers only",
     *     operationId="sellerLogin",
     *     tags={"Seller - Auth"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"email","password"},
     *                 @OA\Property(property="email", format="email", type="string"),
     *                 @OA\Property(property="password", type="password"),
     *             )
     *         )
     *     ),
     *     @OA\Response(response=200, description="Successful operation"),
     *     @OA\Response(response=403, description="Forbidden - Not a seller"),
     *     @OA\Response(response=422, description="Validation error"),
     * )
     */
    public function login(Request $request)
    {
        $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required', 'min:6'],
        ]);

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = User::find(Auth::id());

            if (!$user->is_active) {
                Auth::logout();
                return response()->json([
                    'message' => 'Your account is deactivated. Please contact support.',
                ], 403);
            }

            if (!$user->hasRole('seller')) {
                Auth::logout();
                return response()->json([
                    'message' => 'Access denied. Only sellers can login here.',
                ], 403);
            }

            $token = $user->createToken('Sanctum', [])->plainTextToken;

            return response()->json([
                'user'  => new UserResource($user),
                'token' => $token,
            ], 200);
        }

        return response()->json([
            'message' => 'email or password is incorrect.',
            'errors'  => [
                'email' => ['email or password is incorrect.']
            ]
        ], 422);
    }

    /**
     * @OA\Post(
     *     path="/seller/logout",
     *     description="Logout seller",
     *     operationId="sellerLogout",
     *     tags={"Seller - Auth"},
     *     security={{"bearer_token":{}}},
     *     @OA\Response(response=200, description="Logged out successfully"),
     * )
     */
    public function logout(Request $request)
    {
        $user = to_user(Auth::user());
        to_token($user->currentAccessToken())->delete();
        return response()->json(['message' => 'Logged out successfully']);
    }

    /**
     * @OA\Get(
     *     path="/seller/profile",
     *     description="Get seller profile",
     *     operationId="sellerGetProfile",
     *     tags={"Seller - Auth"},
     *     security={{"bearer_token":{}}},
     *     @OA\Response(response=200, description="Success"),
     * )
     */
    public function get_profile()
    {
        $user = User::with('roleModel')->find(Auth::id());
        return response()->json(new UserResource($user), 200);
    }

    /**
     * @OA\Put(
     *     path="/seller/profile",
     *     description="Edit seller profile",
     *     operationId="sellerEditProfile",
     *     tags={"Seller - Auth"},
     *     security={{"bearer_token":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(property="name", type="string"),
     *                 @OA\Property(property="email", format="email", type="string"),
     *                 @OA\Property(property="phone", type="string"),
     *                 @OA\Property(property="birth_date", type="string", format="date"),
     *                 @OA\Property(property="address", type="string"),
     *                 @OA\Property(property="license_number", type="string"),
     *                 @OA\Property(property="license_expiry_date", type="string", format="date"),
     *                 @OA\Property(property="business_type", type="string"),
     *                 @OA\Property(property="_method", type="string", example="PUT"),
     *             )
     *         )
     *     ),
     *     @OA\Response(response=200, description="Success"),
     * )
     */
    public function edit_profile(Request $request)
    {
        $user = User::find(Auth::id());

        $request->validate([
            'name'                => ['required', 'string'],
            'phone'               => ['required', 'string', Rule::unique('users', 'phone')->ignore($user->id)],
            'email'               => ['required', 'string', Rule::unique('users', 'email')->ignore($user->id)],
            'birth_date'          => ['nullable', 'date'],
            'address'             => ['nullable', 'string'],
            'license_number'      => ['required', 'string'],
            'license_expiry_date' => ['required', 'date'],
            'business_type'       => ['required', 'string'],
        ]);

        $user->update($request->only([
            'name', 'phone', 'birth_date', 'address',
            'license_number', 'license_expiry_date', 'business_type'
        ]));

        return response()->json(new UserResource($user), 200);
    }

    /**
     * @OA\Post(
     * path="/seller/change-password",
     * description="change my password for seller",
     *     tags={"Seller - Auth"},
     *  security={{"bearer_token": {} }},
     *   @OA\RequestBody(
     *       required=true,
     *       @OA\MediaType(
     *           mediaType="multipart/form-data",
     *           @OA\Schema(
     *              @OA\Property(property="current_password", type="string"),
     *              @OA\Property(property="password", type="string"),
     *              @OA\Property(property="password_confirmation", type="string"),
     *           )
     *       )
     *   ),
     *     @OA\Response(
     *         response="200",
     *    description="Success"
     *     ),
     * )
     */

     public function changePassword(Request $request)
     {
         $user = to_user(Auth::user());
 
         $request->validate([
            'current_password' => 'required|string',
            'password' => 'required|string|min:6|confirmed',
        ]);

         if (!Hash::check($request->current_password, $user->password)) {
             return response()->json([
                 'message' => 'current password incorrect',
             ], 400);
         }
 
         $user->update([
             'password' => Hash::make($request->password),
         ]);
 
         return response()->json([
             'message' => 'password changed success',
         ], 200);
     }
 
    /**
     * @OA\Delete(
     *     path="/seller/account",
     *     description="Delete seller account",
     *     operationId="sellerDeleteAccount",
     *     tags={"Seller - Auth"},
     *     security={{"bearer_token":{}}},
     *     @OA\Response(response=204, description="Deleted"),
     * )
     */
    public function delete_account()
    {
        $user = to_user(Auth::user());
        $user->delete();
        return response()->json(null, 204);
    }
}
