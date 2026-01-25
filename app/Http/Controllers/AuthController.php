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
     * description="Register by enter name,email,phone.",
     * operationId="Register",
     *   @OA\RequestBody(
     *       required=true,
     *       @OA\MediaType(
     *           mediaType="multipart/form-data",
     *           @OA\Schema(
     *              required={"name","email","password"},
     *              @OA\Property(property="name", type="string"),
     *              @OA\Property(property="email",format="email", type="string"),
     *              @OA\Property(property="password", type="string"),
     *              @OA\Property(property="password_confirmation", type="string"),
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
            'name'          => ['required', 'string'],
            'email'         => ['required', 'string', 'email', 'unique:users'],
            'password'      => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        $user = User::create([
            'name'        => $request->name,
            'email'            => $request->email,
            'phone'            => $request->phone,
            'password'         => Hash::make($request->password),
        ]);
        $user->assignRole('user');

        $token = $user->createToken('Sanctum', [])->plainTextToken;
        return response()->json([
            'user' => new UserResource($user),
            'token' => $token,
        ], 200);

        return response()->json(null, 200);
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

        //TODO: check if the user verified or not 

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password], $request->remember??false)) {
            $user = to_user(Auth::user());
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
        $user = to_user(Auth::user());
        $user->load('nationality', 'phone_country');

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
     *              @OA\Property(property="phone_country_id", type="integer"),
     *              @OA\Property(property="phone", type="string"),
     *              @OA\Property(property="country_id", type="integer"),
     *              @OA\Property(property="summary", type="string"),
     *              @OA\Property(property="image", type="file"),
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
        $user = to_user(Auth::user());
        //TODO: required if the user is a shaer

        $request->validate([
            'name'                  => ['required', 'string'],
            'phone_country_id'      => ['integer', 'exists:countries,id'],
            'phone'                 => ['required', 'size:8', Rule::unique('users', 'phone')->ignore($user->id)],
            'country_id'            => ['integer', 'exists:countries,id'],
            'summary'               => ['string'],
            'image'                 => ['image'],
            // 'email'               => ['required', 'string', 'email', Rule::unique('users', 'email')->ignore($user->id)],
        ]);
        
        $image = null;
        if($request->image)
            $image = upload_file($request->image, 'users', 'user');
        
        $user->name = $request->name;
        $user->phone_country_id = $request->phone_country_id;
        $user->phone = $request->phone;
        $user->country_id = $request->country_id;
        $user->summary = $request->summary;
        $user->image = $image;

        $user->save();

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
