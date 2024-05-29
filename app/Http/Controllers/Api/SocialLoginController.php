<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\Setting;
use Illuminate\Support\Facades\Validator;
use App\Jobs\SendPasswordResetEmail;
use App\Jobs\SendConfirmationEmail;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Log;

class SocialLoginController extends Controller
{

    /// Social Login with Google
    /**
     * Social Login with Google
     *
     * @OA\Post(
     *      path="/api/auth/google-login",
     *      operationId="google",
     *      tags={"Authentication"},
     *      summary="Social Login with Google",
     *      description="Social Login with Google",
     *      security={{ "passport": {} }},
     *      @OA\RequestBody(
     *         required=true,
     *         description="Google token data",
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(
     *                     property="google_token",
     *                     description="Google Access Token",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="google_id",
     *                     description="Google User ID (OpenID)",
     *                     type="string"
     *                 ),
     *             ),
     *         ),
     *     ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              type="object",
     *          ),
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=412,
     *          description="Precondition Failed",
     *      ),
     * )
    */
    public function google(Request $request)
    {

        if($request->google_id == 'undefined' || $request->google_token == 'undefined' || $request->google_id == '' || $request->google_token == '' || $request->google_id == null || $request->google_token == null) {
            return response()->json([
                'status' => false,
                'message' => __('Invalid token'),
            ], 412);
        }

        $googleUser = Socialite::driver('google')->userFromToken($request->google_token);

        if(!$googleUser) {
            Log::info("Sign in with Google - Invalid token - User not found");
            return response()->json([
                'status' => false,
                'message' => __('Invalid token - User not found'),
            ], 412);
            
        }

        $checkUser = User::where('email', $googleUser->getEmail())->exists();
        $settings = Setting::first();

        $nameParts = explode(' ', $googleUser->getName());
        $name = $nameParts[0] ?? '';
        $surname = $nameParts[1] ?? '';

        if ($checkUser) {
            $user = User::where('email', $googleUser->getEmail())->first();
            $user->google_token = $googleUser->token;
            $user->google_refresh_token = $googleUser->refreshToken;
            $user->avatar = $googleUser->getAvatar();
            $user->affiliate_code = $user->affiliate_code ?? Str::upper(Str::random(12));
            $user->save();
        } else {
            $user = User::updateOrCreate([
                'google_id' => $googleUser->id,
            ], [
                'name' => $name,
                'surname' => $surname,
                'email' => $googleUser->getEmail(),
                'google_token' => $googleUser->token,
                'google_refresh_token' => $googleUser->refreshToken,
                'avatar' => $googleUser->getAvatar(),
                'remaining_words' => explode(',', $settings->free_plan)[0],
                'remaining_images' => explode(',', $settings->free_plan)[1] ?? 0,
                'password' => Hash::make(Str::random(12)),
                'affiliate_code' => Str::upper(Str::random(12)),
                'email_verified_at' => now(),
            ]);
        }

        $token = $user->createToken('google')->accessToken;

        if($token != null) {
            return response()->json([
                'access_token' => $token,
            ], 200);
        } else {
            Log::info("Sign in with Google - Invalid passport token");
            return response()->json([
                'status' => false,
                'message' => __('Invalid passport token'),
            ], 412);
        }

    }



    /// Social Login with Apple
    /**
     * Social Login with Apple
     *
     * @OA\Post(
     *      path="/api/auth/apple-login",
     *      operationId="apple",
     *      tags={"Authentication"},
     *      summary="Social Login with Apple",
     *      description="Social Login with Apple",
     *      security={{ "passport": {} }},
     *      @OA\RequestBody(
     *         required=true,
     *         description="Apple token data",
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(
     *                     property="apple_token",
     *                     description="Apple Access Token",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="apple_id",
     *                     description="Apple User ID (OpenID)",
     *                     type="string"
     *                 ),
     *             ),
     *         ),
     *     ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              type="object",
     *          ),
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=412,
     *          description="Precondition Failed",
     *      ),
     * )
    */
    public function apple(Request $request)
    {

        if($request->apple_id == 'undefined' || $request->apple_token == 'undefined' || $request->apple_id == '' || $request->apple_token == '' || $request->apple_id == null || $request->apple_token == null) {
            return response()->json([
                'status' => false,
                'message' => __('Invalid token'),
            ], 412);
        }

        try{
           $appleUser = Socialite::driver('apple')->userFromToken($request->apple_id); 
        } catch (\Exception $e) {
            Log::error("Sign in with Apple : ".$e->getMessage());
            return response()->json([
                'status' => false,
                'message' => __('Invalid token - User not found'),
            ], 500);
        }

        if(!$appleUser) {
            Log::warning("Sign in with Apple - Invalid token - User not found");
            return response()->json([
                'status' => false,
                'message' => __('Invalid token - User not found'),
            ], 412);
            
        }

        $checkUser = User::where('email', $appleUser->getEmail())->exists();
        $settings = Setting::first();

        $nameParts = explode(' ', $appleUser->getName());
        $name = $nameParts[0] ?? '';
        $surname = $nameParts[1] ?? '';

        if ($checkUser) {
            $user = User::where('email', $appleUser->getEmail())->first();
            $user->apple_token = $appleUser->token;
            $user->apple_refresh_token = $appleUser->refreshToken;
            $user->avatar = $appleUser->getAvatar() ?? ($user->avatar ?? 'assets/img/auth/default-avatar.png');
            $user->affiliate_code = $user->affiliate_code ?? Str::upper(Str::random(12));
            $user->save();
        } else {
            $user = User::updateOrCreate([
                'apple_id' => $appleUser->id,
            ], [
                'name' => $name,
                'surname' => $surname,
                'email' => $appleUser->getEmail(),
                'apple_token' => $appleUser->token,
                'apple_refresh_token' => $appleUser->refreshToken,
                'avatar' => $appleUser->getAvatar() ?? 'assets/img/auth/default-avatar.png',
                'remaining_words' => explode(',', $settings->free_plan)[0],
                'remaining_images' => explode(',', $settings->free_plan)[1] ?? 0,
                'password' => Hash::make(Str::random(12)),
                'affiliate_code' => Str::upper(Str::random(12)),
                'email_verified_at' => now(),
            ]);
        }

        $token = $user->createToken('apple')->accessToken;

        if($token != null) {
            return response()->json([
                'access_token' => $token,
            ], 200);
        } else {
            Log::info("Sign in with Apple - Invalid passport token");
            return response()->json([
                'status' => false,
                'message' => __('Invalid passport token'),
            ], 412);
        }

    }


}