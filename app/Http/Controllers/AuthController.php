<?php

namespace App\Http\Controllers;

use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

use App\Models\VerificationCode;
use App\Models\User;

use App\Http\Resources\UserResource;
use Illuminate\Validation\Rule;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum')->only(['logout','get_profile','update_my_profile','delete_user']);
    }
    
    /**
     * @OA\Post(
     * path="/register",
     * tags={"User - Auth"},
     * description="Register by enter name,email,phone,password,birth_date,address.",
     * operationId="Register",
     *   @OA\RequestBody(
     *       required=true,
     *       @OA\MediaType(
     *           mediaType="multipart/form-data",
     *           @OA\Schema(
     *              required={"name","email","password","phone"},
     *              @OA\Property(property="name", type="string"),
     *              @OA\Property(property="email",format="email", type="string"),
     *              @OA\Property(property="password", type="string"),
     *              @OA\Property(property="password_confirmation", type="string"),
     *              @OA\Property(property="phone", type="string"),
     *              @OA\Property(property="birth_date", type="string", format="date"),
     *              @OA\Property(property="address", type="string"),
     *           )
     *       )
     *   ),
     * @OA\Response(
     *     response=200,
     *     description="successful operation",
     *  ),
     *  )
    */
    public function register(Request $request)
    {
        $request->validate([
            'name'       => ['required', 'string'],
            'email'      => ['required', 'string', 'email', 'unique:users'],
            'password'   => ['required', 'string', 'min:6', 'confirmed'],
            'phone'      => ['required', 'string'],
            'birth_date' => ['nullable', 'date'],
            'address'    => ['nullable', 'string'],
        ]);

        $userRole = \Spatie\Permission\Models\Role::where('name', 'user')->firstOrFail();

        $user = User::create([
            'name'       => $request->name,
            'email'      => $request->email,
            'phone'      => $request->phone,
            'password'   => Hash::make($request->password),
            'birth_date' => $request->birth_date,
            'address'    => $request->address,
            'role_id'    => $userRole->id,
            'is_active'  => true,
        ]);

        $user->assignRole('user');

        $token = $user->createToken('Sanctum', [])->plainTextToken;

        return response()->json([
            'user'  => new UserResource($user),
            'token' => $token,
        ], 200);
    }

    /**
     * @OA\Post(
     * path="/login",
     * description="Login by email and password",
     * operationId="authLogin",
     * tags={"User - Auth"},
     *   @OA\RequestBody(
     *       required=true,
     *       @OA\MediaType(
     *           mediaType="multipart/form-data",
     *           @OA\Schema(
     *              required={"email","password"},
     *              @OA\Property(property="email", format="email" ,type="string"),
     *              @OA\Property(property="password", type="password"),
     *           )
     *       )
     *   ),
     * @OA\Response(
     *     response=200,
     *     description="successful operation",
     *  ),
     *  )
    */
    public function login(Request $request)
    {
        $request->validate( [
            'email'    => ['required'],
            'password' => ['required','min:6'],
        ]);

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password], $request->remember??false)) {
            $user = User::find(Auth::id());
            
            if (!$user->is_active) {
                Auth::logout();
                return response()->json([
                    'message' => 'Your account is deactivated. Please contact support.',
                ], 403);
            }

            $token = $user->createToken('Sanctum', [])->plainTextToken;
            
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
     * @OA\Get(
     * path="/user",
     * description="Get your profile",
     * operationId="get_profile",
     * tags={"User - Auth"},
     * security={{"bearer_token":{}}},
     *   @OA\Response(
     *     response=200,
     *     description="Success",
     *  ),
     *  )
    */
    public function get_profile(Request $request)
    {
        $user = User::with('roleModel')->find(Auth::id());

        return response()->json(new UserResource($user),200);
    }

    /**
     * @OA\Post(
     * path="/user",
     * description="Edit your profile",
     *  tags={"User - Auth"},
     *  security={{"bearer_token": {} }},
     *   @OA\RequestBody(
     *       required=true,
     *       @OA\MediaType(
     *           mediaType="multipart/form-data",
     *           @OA\Schema(
     *              @OA\Property(property="name", type="string"),
     *              @OA\Property(property="email",format="email", type="string"),
     *              @OA\Property(property="phone", type="string"),
     *              @OA\Property(property="birth_date", type="string", format="date"),
     *              @OA\Property(property="address", type="string"),
     *              @OA\Property(property="_method", type="string", format="string", example="PUT"),
     *           )
     *       )
     *   ),
     *     @OA\Response(
     *         response="200",
     *    description="Success"
     *     ),
     * )
    */
    public function edit_profile(Request $request){
        $user = User::find(Auth::id());

        $request->validate([
            'name'                  => ['required', 'string'],
            'phone'                 => ['required', 'string', Rule::unique('users', 'phone')->ignore($user->id)],
            'email'                 => ['required', 'string', Rule::unique('users', 'email')->ignore($user->id)],
            'birth_date'            => ['nullable', 'date'],
            'address'               => ['nullable', 'string'],
        ]);
        
        $user->update($request->only([
            'name', 'phone', 'birth_date', 'address','email' 
        ]));

        return response()->json(new UserResource($user),200);
    }

    /**
     * @OA\Post(
     * path="/logout",
     * description="Logout authorized user",
     * operationId="authLogout",
     * tags={"User - Auth"},
     * security={{"bearer_token":{}}},
     * @OA\Response(
     *    response=200,
     *    description="successful operation"
     *     ),
     * )
    */

    public function logout(Request $request)
    {
        $user = to_user(Auth::user());
        to_token($user->currentAccessToken())->delete();
    }

    /**
     * @OA\Post(
     * path="/user/change-password",
     * description="change my password for user",
     *  tags={"User - Auth"},
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
     * path="/users/delete_account",
     * description="Delete my account",
     * operationId="delete_account",
     * tags={"User - Auth"},
     * security={{"bearer_token":{}}},
     * @OA\Response(
     *    response=200,
     *    description="Success"
     * )
     *)
    */
    public function delete_user()
    {
        $user = to_user(Auth::user());
        $user->delete();
        return response()->json(null, 204);
    }
}
