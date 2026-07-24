<?php

namespace App\Http\Controllers;

use App\Mail\PasswordOtpMail;
use App\Models\PasswordOtp;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class PasswordResetController extends Controller
{
    /**
     * @OA\Post(
     *     path="/forgot-password",
     *     tags={"Auth - Password Reset"},
     *     summary="Send OTP to email for password reset",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"email"},
     *                 @OA\Property(property="email", type="string", format="email")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=200, description="OTP sent if email exists"),
     * )
     */
    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $user = User::where('email', $request->email)->first();

        // Same response whether user exists or not (security)
        if ($user) {
            PasswordOtp::where('email', $user->email)
                ->whereNull('used_at')
                ->update(['used_at' => now()]);

            $otp = (string) random_int(100000, 999999);

            PasswordOtp::create([
                'email' => $user->email,
                'otp' => Hash::make($otp),
                'expires_at' => now()->addMinutes(10),
            ]);

            Mail::to($user->email)->send(new PasswordOtpMail($otp, $user->name ?? 'User'));
        }

        return response()->json([
            'message' => 'If this email exists, an OTP has been sent.',
        ], 200);
    }

    /**
     * @OA\Post(
     *     path="/verify-otp",
     *     tags={"Auth - Password Reset"},
     *     summary="Verify password reset OTP",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"email","otp"},
     *                 @OA\Property(property="email", type="string", format="email"),
     *                 @OA\Property(property="otp", type="string", example="123456")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=200, description="OTP verified"),
     *     @OA\Response(response=422, description="Invalid or expired OTP"),
     * )
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'otp' => ['required', 'string', 'size:6'],
        ]);

        $record = $this->findValidOtp($request->email, $request->otp);

        if (!$record) {
            return response()->json([
                'message' => 'Invalid or expired OTP.',
                'errors' => [
                    'otp' => ['Invalid or expired OTP.'],
                ],
            ], 422);
        }

        $record->update(['verified_at' => now()]);

        return response()->json([
            'message' => 'OTP verified successfully.',
        ], 200);
    }

    /**
     * @OA\Post(
     *     path="/reset-password",
     *     tags={"Auth - Password Reset"},
     *     summary="Reset password using email OTP",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"email","otp","password","password_confirmation"},
     *                 @OA\Property(property="email", type="string", format="email"),
     *                 @OA\Property(property="otp", type="string", example="123456"),
     *                 @OA\Property(property="password", type="string"),
     *                 @OA\Property(property="password_confirmation", type="string")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=200, description="Password reset successfully"),
     *     @OA\Response(response=422, description="Invalid or expired OTP"),
     * )
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'otp' => ['required', 'string', 'size:6'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'message' => 'Invalid or expired OTP.',
                'errors' => [
                    'otp' => ['Invalid or expired OTP.'],
                ],
            ], 422);
        }

        $record = $this->findValidOtp($request->email, $request->otp);

        if (!$record) {
            return response()->json([
                'message' => 'Invalid or expired OTP.',
                'errors' => [
                    'otp' => ['Invalid or expired OTP.'],
                ],
            ], 422);
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        $record->update([
            'used_at' => now(),
            'verified_at' => $record->verified_at ?? now(),
        ]);

        // Invalidate other unused OTPs for this email
        PasswordOtp::where('email', $user->email)
            ->whereNull('used_at')
            ->where('id', '!=', $record->id)
            ->update(['used_at' => now()]);

        // Logout all tokens
        $user->tokens()->delete();

        return response()->json([
            'message' => 'Password reset successfully.',
        ], 200);
    }

    private function findValidOtp(string $email, string $otp): ?PasswordOtp
    {
        $records = PasswordOtp::where('email', $email)
            ->whereNull('used_at')
            ->where('expires_at', '>', now())
            ->latest()
            ->get();

        foreach ($records as $record) {
            if (Hash::check($otp, $record->otp)) {
                return $record;
            }
        }

        return null;
    }
}
