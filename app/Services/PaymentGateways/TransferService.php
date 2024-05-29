<?php

namespace App\Services\PaymentGateways;

use App\Models\Currency;
use App\Models\Gateways;
use App\Models\PaymentPlans;
use App\Models\GatewayProducts;
// use App\Models\Subscriptions;
use Laravel\Cashier\Subscription as Subscriptions;
// use App\Models\SubscriptionItems;
use App\Models\UserOrder;
use App\Models\PaymentProof;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

/**
 * Base functions foreach payment gateway
 * @param saveAllProducts
 * @param saveProduct ($plan)
 * @param subscribe ($plan)
 * @param subscribeCheckout ($planID, $orderID, $couponID= null, $referral= null)
 * @param prepaid ($plan)
 * @param prepaidCheckout ($planID, $orderID, $couponID= null, $referral= null)
 * @param getSubscriptionStatus ($incomingUserId = null)
 * @param getSubscriptionDaysLeft
 * @param subscribeCancel
 * @param checkIfTrial
 * @param getSubscriptionRenewDate
 * @param cancelSubscribedPlan ($subscription, $planId)
 */
class TransferService 
{
    protected static $GATEWAY_CODE = "banktransfer";
    protected static $GATEWAY_NAME = "Bank Transfer";

    public static function saveAllProducts()
    {
        $gateway = Gateways::where("code", self::$GATEWAY_CODE)->where('is_active', 1)->first() ?? abort(404);
        try {
            $plans = PaymentPlans::where('active', 1)->get();
            foreach ($plans as $plan) {
                self::saveProduct($plan);
            }
        } catch (\Exception $ex) {
            Log::error(self::$GATEWAY_CODE."-> saveAllProducts(): " . $ex->getMessage());
            return back()->with(['message' => $ex->getMessage(), 'type' => 'error']);
        }        
    }
    public static function saveProduct($plan)
    {
        try {
            $productData = GatewayProducts::where(["plan_id" => $plan->id, "gateway_code" =>  self::$GATEWAY_CODE])->first();
            if ($productData == null) {
                $product = new GatewayProducts();
                $product->plan_id = $plan->id;
                $product->plan_name = $plan->name;
                $product->gateway_code =  self::$GATEWAY_CODE;
                $product->gateway_title = self::$GATEWAY_NAME;
                $product->product_id = 'BTP-' . strtoupper(Str::random(13));
                $product->price_id = "Not Needed";
                $product->save();
            }else{
                $productData->plan_name = $plan->name;
                $productData->save();
            }
        } catch (\Exception $ex) {
            Log::error(self::$GATEWAY_CODE."-> saveProduct(): ". $ex->getMessage());
            return back()->with(['message' => $ex->getMessage(), 'type' => 'error']);
        }
    }
    public static function subscribe($plan)
    {   
        $gateway = Gateways::where("code", self::$GATEWAY_CODE)->where('is_active', 1)->first() ?? abort(404);
        try {
            $user = auth()->user();
            $currency = Currency::where('id', $gateway->currency)->first()->code;
            $taxRate  = $gateway->tax;
            $taxValue = taxToVal($plan->price, $taxRate);
            $coupon = checkCouponInRequest(); #if there a coupon in request it will return the coupin instanse
            $product = GatewayProducts::where(["plan_id" => $plan->id, "gateway_code" => self::$GATEWAY_CODE])->first();
            if($product == null){
                self::saveProduct($plan);
                $product = GatewayProducts::where(["plan_id" => $plan->id, "gateway_code" => self::$GATEWAY_CODE])->first();
            }
            $newDiscountedPrice = $plan->price;
            if($coupon){
                $newDiscountedPrice -= ($plan->price * ($coupon->discount / 100));
                if ($newDiscountedPrice != floor($newDiscountedPrice)) {
                    $newDiscountedPrice = number_format($newDiscountedPrice, 2);
                }
            }               
            // $subscription = new Subscriptions();
            // $subscription->user_id = $user->id;
            // $subscription->name = $plan->id;
            // $subscription->stripe_id = 'TSO-' . strtoupper(Str::random(13));
            // $subscription->stripe_status = "bank_preparing"; 
            // $subscription->stripe_price = "Not Needed";
            // $subscription->quantity = 1;
            // $subscription->trial_ends_at = $plan->trial_days != 0 ? \Carbon\Carbon::now()->addDays($plan->trial_days) : null;
            // $subscription->ends_at = \Carbon\Carbon::now()->addDays(30);
            // $subscription->plan_id = $plan->id;
            // $subscription->paid_with = self::$GATEWAY_CODE;
            // $subscription->save();

            // $order_id = $subscription->stripe_id;

            // $subscriptionItem = new SubscriptionItems();
            // $subscriptionItem->subscription_id = $subscription->id;
            // $subscriptionItem->stripe_id = $order_id;
            // $subscriptionItem->stripe_product = $product->product_id;
            // $subscriptionItem->stripe_price = "Not Needed";
            // $subscriptionItem->quantity = 1;
            // $subscriptionItem->save();    
            $order_id = 'TSO-' . strtoupper(Str::random(13));
            return view("panel.user.finance.subscription.". self::$GATEWAY_CODE, compact('plan', 'newDiscountedPrice', 'taxValue', 'taxRate','gateway', 'order_id'));
        } catch (\Exception $th) {
            Log::error(self::$GATEWAY_CODE."-> subscribe(): ". $th->getMessage());
            return back()->with(['message' => Str::before($th->getMessage(), ':'),'type' => 'error' ]);
        }
    }
    public static function subscribeCheckout(Request $request, $referral= null)
    {
        $request->validate([
            'proof_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048', 
        ]);

        $planID = $request->input('planID', null);
        $orderID = $request->input('orderID', null);
        $couponID = $request->input('couponID', null);
        $proof_image = $request->input('proof_image', null);
        $user = auth()->user();

        $settings = Setting::first();
        $gateway = Gateways::where("code", self::$GATEWAY_CODE)->where('is_active', 1)->first() ?? abort(404);
        $plan   = PaymentPlans::find($planID) ?? abort(404);
        $total = $plan->price;
        try {
            DB::beginTransaction();
            if($couponID !== null){
                $coupon = checkCouponInRequest($couponID);
                $couponID = $coupon->discount;
                $total -= ($plan->price * ($coupon->discount / 100));
                if ($total != floor($total)) {
                    $total = number_format($total, 2);
                }
                $coupon->usersUsed()->attach(auth()->user()->id);
            }
            $total += taxToVal($plan->price, $gateway->tax);  

            $subscription = new Subscriptions();
            $subscription->user_id = $user->id;
            $subscription->name = $plan->id;
            $subscription->stripe_id = $orderID;
            $subscription->stripe_status = "bank_waiting"; 
            $subscription->stripe_price = "Not Needed";
            $subscription->quantity = 1;
            $subscription->trial_ends_at = null;
            switch ($plan->frequency) {
                case 'monthly':
                    $subscription->ends_at = \Carbon\Carbon::now()->addMonths(1);
                    break;
                case 'yearly':
                    $subscription->ends_at = \Carbon\Carbon::now()->addYears(1);
                    break;
                case 'lifetime_monthly':
                    $subscription->ends_at = \Carbon\Carbon::now()->addMonths(1); #ends each month but auto renewing without payment reqs
                    $subscription->auto_renewal = 1;
                    break;
                case 'lifetime_yearly':
                    $subscription->ends_at = \Carbon\Carbon::now()->addYears(1); #ends each year but auto renewing without payment reqs
                    $subscription->auto_renewal = 1;
                    break;
                default:
                    $subscription->ends_at = \Carbon\Carbon::now()->addDays(30);
                    break;
            }
            $subscription->tax_rate = $gateway->tax;
            $subscription->tax_value = taxToVal($plan->price, $gateway->tax);
            $subscription->coupon = $couponID;
            $subscription->total_amount = $total;
            $subscription->plan_id = $plan->id;
            $subscription->paid_with = self::$GATEWAY_CODE;
            $subscription->save();

            $order = new UserOrder();
            $order->order_id = $orderID;
            $order->plan_id = $plan->id;
            $order->user_id = $user->id;
            $order->payment_type = self::$GATEWAY_CODE;
            $order->price = $total;
            $order->affiliate_earnings = ($total * $settings->affiliate_commission_percentage) / 100;
            $order->status = 'Waiting';
            $order->country = $user->country ?? 'Unknown';
            $order->tax_rate = $gateway->tax;
            $order->tax_value = taxToVal($plan->price, $gateway->tax);
            $order->save();

            $filename = Str::random(20) . '_' . time() . '.' . $request->file('proof_image')->getClientOriginalExtension();
            $paymentProof = new PaymentProof();
            $paymentProof->order_id = $orderID; 
            $paymentProof->user_id = $user->id; 
            $paymentProof->plan_id = $planID; 
            $paymentProof->total_amount = $total;
            $paymentProof->proof_image = $filename;
            $paymentProof->save();
            $request->file('proof_image')->move(public_path('proofs'), $filename);
			\App\Models\Usage::getSingle()->updateSalesCount($total);
            createActivity($user->id, __('initiated a subscription approval-awaiting bank transaction.'), $plan->name . ' '. __('Plan'), null);

        } catch (\Exception $th) {
            DB::rollBack();
            Log::error(self::$GATEWAY_CODE."-> subscribe(): ". $th->getMessage());
            return back()->with(['message' => Str::before($th->getMessage(), ':'),'type' => 'error' ]);
        } 
        DB::commit();
        return redirect()->route('dashboard.user.payment.succesful')->with([
            'message' => __('Thank you for your purchase. You will be notified once the payment transaction is accepted.'), 
            'type' => 'success'
        ]);
    }
    public static function prepaid($plan)
    {
        $gateway = Gateways::where("code", self::$GATEWAY_CODE)->where('is_active', 1)->first() ?? abort(404);
        try {
            $user = auth()->user();
            $currency = Currency::where('id', $gateway->currency)->first()->code;
            $taxRate  = $gateway->tax;
            $taxValue = taxToVal($plan->price, $taxRate);
            $coupon = checkCouponInRequest(); #if there a coupon in request it will return the coupin instanse

            $product = GatewayProducts::where(["plan_id" => $plan->id, "gateway_code" => self::$GATEWAY_CODE])->first();
            if($product == null){
                self::saveProduct($plan);
                $product = GatewayProducts::where(["plan_id" => $plan->id, "gateway_code" => self::$GATEWAY_CODE])->first();
            }
            $newDiscountedPrice = $plan->price;
            if($coupon){
                $newDiscountedPrice -= ($plan->price * ($coupon->discount / 100));
                if ($newDiscountedPrice != floor($newDiscountedPrice)) {
                    $newDiscountedPrice = number_format($newDiscountedPrice, 2);
                }
            }               
            $order_id = 'TPO-' . strtoupper(Str::random(13));
            return view("panel.user.finance.prepaid.". self::$GATEWAY_CODE, compact('plan', 'newDiscountedPrice', 'taxValue', 'taxRate','gateway', 'order_id'));
        } catch (\Exception $th) {
            Log::error(self::$GATEWAY_CODE."-> prepaid(): ". $th->getMessage());
            return back()->with(['message' => Str::before($th->getMessage(), ':'),'type' => 'error' ]);
        }
    }
    public static function prepaidCheckout(Request $request, $referral= null)
    {
        $planID = $request->input('planID', null);
        $orderID = $request->input('orderID', null);
        $couponID = $request->input('couponID', null);
        
        $user = auth()->user();
        $settings = Setting::first();
        $gateway = Gateways::where("code", self::$GATEWAY_CODE)->where('is_active', 1)->first() ?? abort(404);
        $plan   = PaymentPlans::find($planID) ?? abort(404);
        $total = $plan->price;
        try {
            DB::beginTransaction();
            if($couponID !== null){
                $coupon = checkCouponInRequest($couponID);
                $couponID = $coupon->discount;
                $total -= ($plan->price * ($coupon->discount / 100));
                if ($total != floor($total)) {
                    $total = number_format($total, 2);
                }
                $coupon->usersUsed()->attach(auth()->user()->id);
            }
            $total += taxToVal($plan->price, $gateway->tax);  

            $order = new UserOrder();
            $order->order_id = $orderID;
            $order->plan_id = $plan->id;
            $order->user_id = $user->id;
            $order->type = 'prepaid';
            $order->payment_type = self::$GATEWAY_CODE;
            $order->price = $total;
            $order->affiliate_earnings = ($total * $settings->affiliate_commission_percentage) / 100;
            $order->status = 'Waiting';
            $order->country = $user->country ?? 'Unknown';
            $order->tax_rate = $gateway->tax;
            $order->tax_value = taxToVal($plan->price, $gateway->tax);
            $order->save();
			\App\Models\Usage::getSingle()->updateSalesCount($total);
            createActivity($user->id, __('initiated a prepaid pack approval-awaiting bank transaction.'), $plan->name . ' '. __('Plan'), null);

        } catch (\Exception $th) {
            DB::rollBack();
            Log::error(self::$GATEWAY_CODE."-> subscribe(): ". $th->getMessage());
            return back()->with(['message' => Str::before($th->getMessage(), ':'),'type' => 'error' ]);
        } 
        DB::commit();
        return redirect()->route('dashboard.user.payment.succesful')->with([
            'message' => __('Thank you for your purchase. You will be notified once the payment transaction is accepted.'), 
            'type' => 'success'
        ]);
    }
    public static function getSubscriptionStatus($incomingUserId = null){
        if($incomingUserId != null){
            $user = User::where('id', $incomingUserId)->first();
        }else{
            $user = Auth::user();
        }
        $gateway = Gateways::where("code", self::$GATEWAY_CODE)->where('is_active', 1)->first() ?? abort(404);
        $currency = Currency::where('id', $gateway->currency)->first()->code;
        $sub = getCurrentActiveSubscription($user->id);
        if ($sub != null) {
            return true;
        }
        return false;
    }
    public static function getSubscriptionDaysLeft()
    {
        $user = Auth::user();
        $settings = Setting::first();
        $gateway = Gateways::where("code", self::$GATEWAY_CODE)->where('is_active', 1)->first() ?? abort(404);
        $currency = Currency::where('id', $gateway->currency)->first()->code;
        $sub = getCurrentActiveSubscription($user->id);
        if ($sub) {
            return \Carbon\Carbon::now()->diffInDays($sub->ends_at);
        } else {
            Log::error("getSubscriptionDaysLeft()");
            return 0;
        }
    }
    public static function subscribeCancel($internalUser=null)
    {
        $user = $internalUser?? Auth::user();
        $activeSub = getCurrentActiveSubscription($user->id);
        if ($activeSub != null) {
            $plan = PaymentPlans::where('id', $activeSub->plan_id)->first();

            $recent_words = $user->remaining_words - $plan->total_words;
            $recent_images = $user->remaining_images - $plan->total_images;
            $user->remaining_words = $recent_words < 0 ? 0 : $recent_words;
            $user->remaining_images = $recent_images < 0 ? 0 : $recent_images;
            $user->save();

            $activeSub->stripe_status = "bank_canceled";
            $activeSub->save();

            createActivity($user->id, 'cancelled', $plan->name, null);
            if($internalUser != null){
                return back()->with(['message' => __('User subscription is cancelled succesfully.'), 'type' => 'success']);
            }
            return redirect()->route('dashboard.user.index')->with(['message' => __('Your subscription is cancelled succesfully.'), 'type' => 'success']);        
        }

        return back()->with(['message' => __('Could not find active subscription. Nothing changed!'), 'type' => 'error']);
    }
    public static function checkIfTrial()
    {
        # there is no trail in bank transfer
        return false;
    }
    public static function getSubscriptionRenewDate()
    {
        $user = Auth::user();
        $activeSub = getCurrentActiveSubscription($user->id);
        return \Carbon\Carbon::parse($activeSub->ends_at)->format('F jS, Y');
    }
    public static function cancelSubscribedPlan($subscription, $planId)
    {
        try {
            $order = UserOrder::where('order_id', $subscription->stripe_id)->first();
            $recent_words = $order->user->remaining_words - $order->plan->total_words;
            $recent_images = $order->user->remaining_images - $order->plan->total_images;
            $order->user->remaining_words = $recent_words < 0 ? 0 : $recent_words;
            $order->user->remaining_images = $recent_images < 0 ? 0 : $recent_images;
            $order->user->save();

            $subscription->stripe_status = "bank_canceled";
            $subscription->save();
            # sent mail if required here later
            createActivity($order->user->id, __('Subscription canceled due to plan deletion.') , $order->plan->name. ' '. __('Plan'), null);
            return true;
        } catch (\Exception $th) {
            Log::error(self::$GATEWAY_CODE. " cancelSubscribedPlan(): " . $th->getMessage() . "\n------------------------\n");
            return false;
        }
    }
    public static function handleWebhook(Request $request)
    {
        return response()->json(['success' => true]);
    }
}