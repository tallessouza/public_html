<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\PaymentController;
use Illuminate\Http\Request;
use App\Models\Currency;
use App\Models\PaymentPlans;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;

class AppController extends Controller
{
    /**
     * Get the email confirmation setting.
     *
     * @OA\Get(
     *      path="/api/app/email-confirmation-setting",
     *      operationId="getEmailConfirmationSetting",
     *      tags={"App Settings"},
     *      summary="Get email confirmation setting",
     *      description="Get the email confirmation setting from the application settings.",
     *      security={{ "bearerAuth": {} }},
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              type="object",
     *              example={"emailconfirmation": true},
     *          ),
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     * )
    */
    public function getEmailConfirmationSetting(Request $request)
    {
        $settings = Setting::first();
        $data = [
            'emailconfirmation' => !((bool)$settings->login_without_confirmation),
        ];
        return response()->json($data);
    }
    /**
     * Get general application settings.
     *
     * @OA\Get(
     *      path="/api/app/get-setting",
     *      operationId="getAppSettings",
     *      tags={"App Settings"},
     *      summary="Get application settings",
     *      description="Get general application settings.",
     *      security={{ "bearerAuth": {} }},
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
     * )
    */
    public function getSetting(Request $request)
    {
        $settings = Setting::first();
        return $settings;
    }


    /**
     * Get usage data of current user
     *
     * @OA\Get(
     *      path="/api/app/usage-data",
     *      operationId="getUsageData",
     *      tags={"App Settings"},
     *      summary="Get usage data of current user",
     *      description="Get usage data and subscription plan details of current user.",
     *      security={{ "passport": {} }},
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
     * )
    */
    public function getUsageData(Request $request) {

        $user = $request->user();
        $userId=Auth::user()->id;

        $plan_name = "";
        $planId = "";
        $paid_with = "";
        $total_words = 0;
        $total_images = 0;
        $remaining_words = $user->remaining_words ?? 0;
        $remaining_images = $user->remaining_images ?? 0;

        // Get current active subscription
        $activeSub = getCurrentActiveSubscription($userId);
        if($activeSub != null){
            $paid_with = $activeSub->paid_with;
            $planId = $activeSub->plan_id;
        }else{
            $activeSub = getCurrentActiveSubscriptionYokkasa($userId);
            if($activeSub != null) {
                $planId = $activeSub->plan_id;
                $paid_with = "yokassa";
            }
        }

        $status = getSubscriptionStatus();
        $daysLeft = getSubscriptionDaysLeft() ?? 0;
        $isTrial = checkIfTrial();

        if($planId != ""){
            $plan = PaymentPlans::where([['id', '=', $planId]])->first();
            $total_words = $plan->total_words;
            $total_images = $plan->total_images;
            $plan_name = $plan->name;
        }

        $usage_percentage_words = self::calculateUsagePercent($remaining_words, $total_words);
        $usage_percentage_images = self::calculateUsagePercent($remaining_images, $total_images);

        $data = [
            "subscription_status" => $status,
            "is_trial" => $isTrial,
            "days_left" => $daysLeft,
            "paid_with" => $paid_with,
            "plan_name" => $plan_name,
            "total_words" => $total_words,
            "total_images" => $total_images,
            "remaining_words" => $remaining_words,
            "remaining_images" => $remaining_images,
            "usage_percentage_words" => (float)number_format((float)$usage_percentage_words, 2, '.', ''),
            "usage_percentage_images" => (float)number_format((float)$usage_percentage_images, 2, '.', ''),
        ];

        return response()->json($data);

    }

    
    private function calculateUsagePercent($remaining, $total) {

        if($remaining == 0) {
            return 100;
        } 
        if($total == 0 && $remaining != 0) {
            // total=0 remaining=2100
            return 0;
        }
        if($total < $remaining) {
            // total=1000 remaining=2100
            return 0;
        }

        // total=3000 remaining=2100
        // remainingPercent = 2100 / 3000 => 0.7
        $remainingPercent = $remaining / $total;

        // (1 - 0.7) * 100 = 0.3 * 100 = 30 usage percent
        return (1 - $remainingPercent) * 100;
    }


    /**
     * Get default currency
     *
     * @OA\Get(
     *      path="/api/app/currency/{id?}",
     *      operationId="getCurrency",
     *      tags={"App Settings"},
     *      summary="Get default currency",
     *      description="Returns default currency if id is not provided, else returns currency by id. Use 'all' to get all currencies.",
     *      security={{ "passport": {} }},
     *      @OA\Parameter(
     *          name="id",
     *          description="Id of currency or 'all' or null to get default currency.",
     *          in="path",
     *          required=false,
     *          @OA\Schema(type="string"),
     *      ),
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
     *          response=404,
     *          description="No currency found.",
     *      ),
     * )
    */
    public function getCurrency(Request $request, $id = null) {

        if($id == "all") {
            $currency = Currency::all();
            return response()->json($currency);
        } else if($id != null) {
            $currency = Currency::where([['id', '=', $id]])->first();
            return response()->json($currency);
        }


        $currencyId = Setting::first()->default_currency;
        if($currencyId != null) {
            $currency = Currency::where([['id', '=', $currencyId]])->first();
            return response()->json($currency);
        }

        return response()->json(["message" => "No currency found."], 404);

    }


    /**
     * Gets app logo
     *
     * @OA\Get(
     *      path="/api/auth/logo",
     *      operationId="getLogo",
     *      tags={"General (Helpers)"},
     *      summary="Get default logo",
     *      description="Returns default logo from settings.",
     *      security={{ "passport": {} }},
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
     *          response=404,
     *          description="No logo found.",
     *      ),
     * )
    */
    public function getLogo(Request $request, $id = null) {

        $logo = Setting::first()->logo;
        if($logo != null) {
            return response()->json($logo);
        }

        return response()->json(["error" => "No logo found."], 404);

    }





}
