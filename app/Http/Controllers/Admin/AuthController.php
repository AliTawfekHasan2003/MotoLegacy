<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * @OA\Post(
     *     path="/admin/login",
     *     description="Login for Admin only",
     *     operationId="adminLogin",
     *     tags={"Admin - Auth"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"email","password"},
     *                 @OA\Property(property="email", format="email" ,type="string"),
     *                 @OA\Property(property="password", type="password"),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden - Not an admin",
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *     )
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

            if (!$user->hasRole('admin')) {
                Auth::logout();
                return response()->json([
                    'message' => 'Access denied. Only admins can login here.',
                ], 403);
            }

            $token = $user->createToken('Sanctum', ['admin'])->plainTextToken;

            return response()->json([
                'user' => new UserResource($user),
                'token' => $token,
            ], 200);
        }

        return response()->json([
            'message' => 'email or password is incorrect.',
            'errors' => [
                'email' => ['email or password is incorrect.']
            ]
        ], 422);
    }

    /**
     * @OA\Post(
     *     path="/admin/logout",
     *     description="Logout admin",
     *     operationId="adminLogout",
     *     tags={"Admin - Auth"},
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
     *     path="/admin/profile",
     *     description="Get admin profile",
     *     operationId="adminGetProfile",
     *     tags={"Admin - Auth"},
     *     security={{"bearer_token":{}}},
     *     @OA\Response(response=200, description="Success"),
     * )
     */

    public function get_profile()
    {
        $user = User::with('roleModel')->find(\Illuminate\Support\Facades\Auth::id());
        return response()->json(new UserResource($user), 200);
    }
}
