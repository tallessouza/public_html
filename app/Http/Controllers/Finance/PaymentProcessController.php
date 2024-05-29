<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Services\GatewaySelector;
use App\Models\PaymentPlans;
use App\Models\Gateways;
use App\Models\UserOrder;
use App\Models\CustomBilingPlans;
use App\Models\User;
// use App\Models\Subscriptions;
use Laravel\Cashier\Subscription as Subscriptions;
use App\Models\GatewayProducts;
use App\Models\Currency;
use App\Models\Setting;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class PaymentProcessController extends Controller
{
    # payment area
    function isActiveSubscription($planId){ # Checks subscription table if given plan is active on user (already subscribed)
        $activeSub = getCurrentActiveSubscription();
        if($activeSub != null){
            $activesubid = $activeSub->id;
        }else{
            $activeSub_yokassa = getCurrentActiveSubscriptionYokkasa();
            if($activeSub_yokassa != null){
                $activesubid = $activeSub_yokassa->id;
            }else{
                $activesubid = 0; //id can't be zero, so this will be easy to check instead of null
            }
        }
        return $activesubid == $planId;
    }
    public function startSubscriptionProcess($planId, $gatewayCode)
    { # when click on subscribe
        $plan = PaymentPlans::where('id', $planId)->first();
        if($plan != null){
            if(self::isActiveSubscription($planId) == true){
                return back()->with(['message' => __('You already have subscription. Please cancel it before creating a new subscription'), 'type' => 'error']);
            }
            if($gatewayCode == 'walletmaxpay'){
                return back()->with(['message' => __('WalletMaxPay available only for Token Packs'), 'type' => 'error']);
            }
            try {
                return GatewaySelector::selectGateway($gatewayCode)::subscribe($plan);
            } catch (\Exception $e) {   
                return back()->with(['message' => $e->getMessage(), 'type' => 'error']);
            }
        }
        abort(404);
    }
    public function startPrepaidPaymentProcess($planId, $gatewayCode){
        $plan = PaymentPlans::where('id', $planId)->first();
        if($plan != null){
            try {
                return GatewaySelector::selectGateway($gatewayCode)::prepaid($plan);
            } catch (\Exception $e) {   
                return back()->with(['message' => $e->getMessage(), 'type' => 'error']);
            }
        }
        abort(404);
    }
    public function startSubscriptionCheckoutProcess(Request $request, $gateway= null, $referral = null){
        if($gateway !== "freeservice" && $request->isMethod('post'))
        {
            $gateways = Gateways::where('is_active', 1)->pluck('code')->toArray();
            $request->validate([
                'planID' => 'required',
                'orderID' => 'nullable',
                'couponID' => 'nullable',
                'gateway' => ['required', 'in:' . implode(',', $gateways)],
            ]);
            if($gateway == null){
                $gateway = $request->gateway;
            }
        }
        try {
            return GatewaySelector::selectGateway($gateway)::subscribeCheckout($request, $referral);
        } catch (\Exception $e) {   
            return back()->with(['message' => $e->getMessage(), 'type' => 'error']);
        }
    }
    public function startPrepaidCheckoutProcess(Request $request, $gateway= null, $referral = null){
        if($gateway !== "freeservice" && $request->isMethod('post')){
            $gateways = Gateways::where('is_active', 1)->pluck('code')->toArray();
            $request->validate([
                'planID' => 'required',
                'orderID' => 'nullable',
                'couponID' => 'nullable',
                'gateway' => ['required', 'in:' . implode(',', $gateways)],
            ]);
            if($gateway == null){
                $gateway = $request->gateway;
            }
        }
        try {
            return GatewaySelector::selectGateway($gateway)::prepaidCheckout($request, $referral);
        } catch (\Exception $e) {   
            return back()->with(['message' => $e->getMessage(), 'type' => 'error']);
        }
    }

    # additional required functions 
    public function createPayPalOrder(Request $request){
        try {
            return GatewaySelector::selectGateway('paypal')::createPayPalOrder($request);
        } catch (\Exception $e) {   
            return back()->with(['message' => $e->getMessage(), 'type' => 'error']);
        }
    }
    public function iyzicoPrepaidCallback(Request $request){
        try {
            return GatewaySelector::selectGateway('iyzico')::iyzicoPrepaidCallback($request);
        } catch (\Exception $e) {   
            return back()->with(['message' => $e->getMessage(), 'type' => 'error']);
        }
    }
    public function iyzicoSubscribeCallback(Request $request){
        try {
            return GatewaySelector::selectGateway('iyzico')::iyzicoSubscribeCallback($request);
        } catch (\Exception $e) {   
            return back()->with(['message' => $e->getMessage(), 'type' => 'error']);
        }
    }
    public function iyzicoProductsList(Request $request){
        try {
            return GatewaySelector::selectGateway('iyzico')::iyzicoProductsList($request);
        } catch (\Exception $e) {   
            return back()->with(['message' => $e->getMessage(), 'type' => 'error']);
        }
    }
    public function succesful() {
        return view("panel.user.finance.succesful");
    }
    # webhook control area
    public function handleWebhook(Request $request, $gateway)
    {
        # [stripe,paypal,yokassa,iyzico,paystack,twocheckout]
        try {
            if($request->isMethod('post'))
            { # accept the post method for all
                if($gateway == "simulate"){
                    return GatewaySelector::selectGateway($gateway)::simulateWebhookEvent($request);
                }
                return GatewaySelector::selectGateway($gateway)::handleWebhook($request);
            }
            elseif($request->isMethod('get')) { # accept the get method only for [twocheckout, simulate]
                if($gateway == "simulate"){
                    return GatewaySelector::selectGateway('paypal')::simulateWebhookEvent($request);
                }
                elseif($gateway == "twocheckout"){
                    return GatewaySelector::selectGateway($gateway)::handleWebhook($request);
                }
                elseif($gateway == "razorpay"){
                    return GatewaySelector::selectGateway($gateway)::handleWebhook($request);
                }
                else{
                    abort(404);
                }
            }
        } catch (\Exception $e) {   
            return back()->with(['message' => $e->getMessage(), 'type' => 'error']);
        }
    }
    # admin control area
    public static function banktransactions() {
        $bankOrders = UserOrder::where('payment_type', 'banktransfer')->orderByRaw("CASE WHEN status = 'Waiting' THEN 0 ELSE 1 END")->orderBy('created_at', 'desc')->get();
        return view('panel.admin.banktransfer.index', compact('bankOrders'));
    }
    public static function bankDelete($id = null){
        $post = UserOrder::findOrFail($id);
        $post->delete();
        return back()->with(['message' => __('Deleted Successfully'), 'type' => 'success']);
    }
    public static function bankUpdateSave(Request $request){
        if($request->order_status != 0){
            $order = UserOrder::findOrFail($request->order_id);
            self::changeOrderStatus($request->order_status, $order);            
        }
        return back()->with(['message' => __('Updated Successfully'), 'type' => 'success']);
    }
    private static function changeOrderStatus($status, $order){
        switch ($status) {
            case 'Waiting':
                    # sent mail if required here later
                    createActivity($order->user->id, __('Bank transaction status updated to:')." ". __($status), $order->plan->name . ' '. __('Plan'), null);
                break;
            case 'Approved':      
                    if($order->type == "subscription"){ 
                        $subs = Subscriptions::where("stripe_id", $order->order_id)->first();
						if ($subs) {
							$subs->stripe_status = "bank_approved";
		
							switch ($order->plan->frequency) {
								case 'monthly':
									$subs->ends_at = \Carbon\Carbon::now()->addMonths(1);
									break;
								case 'yearly':
									$subs->ends_at = \Carbon\Carbon::now()->addYears(1);
									break;
								case 'lifetime_monthly':
									$subs->ends_at = \Carbon\Carbon::now()->addMonths(1); #ends each month but auto renewing without payment reqs
									$subs->auto_renewal = 1;
									break;
								case 'lifetime_yearly':
									$subs->ends_at = \Carbon\Carbon::now()->addYears(1); #ends each year but auto renewing without payment reqs
									$subs->auto_renewal = 1;
									break;
								default:
									$subs->ends_at = \Carbon\Carbon::now()->addMonths(1);
									break;
							}
							$subs->save(); 
						}
                    }
                    $order->plan->total_words == -1? ($order->user->remaining_words = -1) : ($order->user->remaining_words += $order->plan->total_words);
                    $order->plan->total_images == -1? ($order->user->remaining_images = -1) : ($order->user->remaining_images += $order->plan->total_images);
                    $order->user->save();
                    # sent mail if required here later
                    createActivity($order->user->id, __('Purchased with approved bank transaction'), $order->plan->name. ' '. __('Plan'), null);
                break;
            case 'Rejected':
                    $subs = Subscriptions::where("stripe_id", $order->order_id)->first();
					if ($subs) {
						$subs->stripe_status = "bank_rejected";
                    	$subs->save();   
					}
                    # sent mail if required here later
                    createActivity($order->user->id, __('Bank transaction status updated to:')." ". __($status), $order->plan->name . ' '. __('Plan'), null);
                break;
            default:
                break;
        }

        $order->status = $status;
        $order->save();
    }

    public static function getSubscriptionStatus()
    {
        $gateway = null;
        $activeSub = getCurrentActiveSubscription();

        if($activeSub != null){
            $gateway = $activeSub->paid_with;
        } else {
            $activeSubY = getCurrentActiveSubscriptionYokkasa();
            if($activeSubY != null) {
                $gateway = "yokassa";
            }
        }

        try {
            return GatewaySelector::selectGateway($gateway)::getSubscriptionStatus();
        } catch (\Exception $e) {
            return false;
        }
    }

    public static function getSubscriptionDaysLeft(){
        $gateway = null;
        $activeSub = getCurrentActiveSubscription();
        if($activeSub != null){
            $gateway = $activeSub->paid_with;
        } else {
            $activeSubY = getCurrentActiveSubscriptionYokkasa();
            if($activeSubY != null) {
                $gateway = "yokassa";
            }
        }
        try {
            return GatewaySelector::selectGateway($gateway)::getSubscriptionDaysLeft();
        } catch (\Exception $e) {   
            return null;
        }
    }
    public static function cancelActiveSubscription(){
        $gateway = null;
        $activeSub = getCurrentActiveSubscription();
        if($activeSub != null){
            $gateway = $activeSub->paid_with;
        } else {
            $activeSubY = getCurrentActiveSubscriptionYokkasa();
            if($activeSubY != null) {
                $gateway = "yokassa";
            }
        }
        try {
            return GatewaySelector::selectGateway($gateway, 'Could not cancel subscription. Please try again. If this error occures again, please update and migrate.')::subscribeCancel();
        } catch (\Exception $e) {   
            return back()->with(['message' => $e->getMessage(), 'type' => 'error']);
        }
    }
    public static function checkIfTrial(){
        $gateway = null;
        $activeSub = getCurrentActiveSubscription();
        if($activeSub != null){
            $gateway = $activeSub->paid_with;
        } else {
            $activeSubY = getCurrentActiveSubscriptionYokkasa();
            if($activeSubY != null) {
                $gateway = "yokassa";
            }
        }
        try {
            if($gateway == null){
                return false;
            }
            return GatewaySelector::selectGateway($gateway)::checkIfTrial();
        } catch (\Exception $e) {   
            return back()->with(['message' => $e->getMessage(), 'type' => 'error']);
        }
    }
    public static function getSubscriptionRenewDate(){
        $gateway = null;
        $activeSub = getCurrentActiveSubscription();
        if($activeSub != null){
            $gateway = $activeSub->paid_with;
        } else {
            $activeSubY = getCurrentActiveSubscriptionYokkasa();
            if($activeSubY != null) {
                $gateway = "yokassa";
            }
        }
        try {
            return GatewaySelector::selectGateway($gateway)::getSubscriptionRenewDate();
        } catch (\Exception $e) {
            return null;
        }
    }
    public static function deletePaymentPlan($id){
        $plan = PaymentPlans::where('id', $id)->first();
        if($plan != null){        
            // Get related subscriptions
            $queryAnd   = [['stripe_status', '=', 'active'  ], ['plan_id', '=', $plan->id]];
            $queryOr    = [['stripe_status', '=', 'trialing'], ['plan_id', '=', $plan->id]];
            $queryOr2   = [['stripe_status', '=', 'bank_approved'], ['plan_id', '=', $plan->id]];
            $queryOr3   = [['stripe_status', '=', 'bank_renewed'], ['plan_id', '=', $plan->id]];
            $queryOr4   = [['stripe_status', '=', 'free_approved'], ['plan_id', '=', $plan->id]];
            $subscriptions = Subscriptions::where($queryAnd)
            ->orWhere($queryOr)
            ->orWhere($queryOr2)
            ->orWhere($queryOr3)
            ->orWhere($queryOr4)
            ->get();

            // Remove subcriptions one by one
            if($subscriptions != null){
                foreach ($subscriptions as $subscription) {
                    try {
                        $tmp = GatewaySelector::selectGateway($subscription->paid_with)::cancelSubscribedPlan($subscription, $plan->id);
                    } catch (\Exception $e) {
                        return false;
                    }
                }
            }
            
            // Delete Plan
            $plan->delete();
            return back()->with(['message' => __('All subscriptions related to this plan has been cancelled. Plan is deleted.'), 'type' => 'success']);
        }else{
            return back()->with(['message' => 'Couldn\'t find plan.', 'type' => 'error']);
        }

    }
    public static function saveGatewayProducts($plan){

        // $typ = $type == "prepaid" ? "o" : "s"; # o => one-time | s => subscription
        // switch ($frequency) {
        //     case 'monthly':
        //         $freq = "m";
        //         break;
        //     case 'yearly':
        //         $freq = "y";
        //         break;
        //     case 'lifetime_monthly':
        //         $freq = "lm";
        //         break;
        //     case 'lifetime_yearly':
        //         $freq = "ly";
        //         break;
        //     default:
        //         $freq = "m";
        //         break;
        // }

        $gateways = Gateways::all();
        if($gateways != null){
            foreach($gateways as $gateway){
                if((int)$gateway->is_active == 1){
                    try {
                        $tmp = GatewaySelector::selectGateway($gateway->code)::saveProduct($plan);
                    } catch (\Exception $e) {}
                }
            }
        }else{
            Log::error(self::$GATEWAY_CODE."-> saveGatewayProducts(): Could not find any active gateways!");
            return back()->with(['message' => __('Please enable at least one gateway.'), 'type' => 'error']);
        }
    }
    public static function checkForOngoingPayments()
    {
        return null;
    }
    public static function checkUnmatchingSubscriptions(){
        $gateway = null;
        $activeSub = getCurrentActiveSubscription();
        if($activeSub != null){
            $gateway = $activeSub->paid_with;
        } else {
            $activeSubY = getCurrentActiveSubscriptionYokkasa();
            if($activeSubY != null) {
                $gateway = "yokassa";
                $activeSub = $activeSubY;
            }
        }
        if($activeSub){
			$priceArray = GatewayProducts::all()->pluck('price_id')->toArray();
			# if some plan cancelation happens when appling coupon then add the custom product price_id to custom bilings table 
			$customPriceArray = CustomBilingPlans::all()->pluck('custom_plan_price_id')->toArray();
			if(in_array($activeSub->stripe_price, $priceArray) == true || in_array($activeSub->stripe_price, $customPriceArray) == true){
				// Do nothing. This is what we want.
			}else{
				// Cancel subscription 
				try{
					if($activeSub->paid_with != "yokassa"){
						$tmp = self::cancelActiveSubscription();
					}
				}catch(\Exception $ex){
					Log::error("PaymentProcessController::checkUnmatchingSubscriptions()\n".$ex->getMessage());
				}
			}
			// Check if active subscription exists on gateway (by stripe_id / subscription id)
			// getSubscriptionStatus function is already called on subscription status file. BUT after functions which gives errors,
			// this needs priority, that's why we add here too. Also this function updates database as cancelled if can't find in gateway
			# there are webhooks for paypal and stripe and paystack
			if($gateway != "paypal" && $gateway != "stripe" && $gateway != "paystack"){
				$isValid = false;
				try {
					$isValid = GatewaySelector::selectGateway($gateway)::getSubscriptionStatus();
				} catch (\Exception $e) {}
			}
			// For some gateways we need to create orders first thats why we have so many Waiting order records. We must clean them.
			$orders = UserOrder::where([['payment_type', '!=', 'banktransfer'], ['status', '=', 'Waiting'], ['user_id', '=', auth()->user()->id]])->get();
			foreach ($orders as $order) {
				$order->delete();
			}
		}
        return null;
    }

    public static function cancelActiveSubscriptionByAdmin($userId){
        $gateway = null;
        $activeSub = getCurrentActiveSubscription($userId);
        if($activeSub != null){
            $gateway = $activeSub->paid_with;
        } else {
            $activeSubY = getCurrentActiveSubscriptionYokkasa($userId);
            if($activeSubY != null) {
                $gateway = "yokassa";
            }
        }
        try {
            $user = User::find($userId);
            return GatewaySelector::selectGateway($gateway, 'Could not cancel subscription. Please try again. If this error occures again, please update and migrate.')::subscribeCancel($user);
        } catch (\Exception $e) {   
            return back()->with(['message' => $e->getMessage(), 'type' => 'error']);
        }
    }

    public static function assignPlanByAdmin(Request $request){
        $request->validate([
            'planID' => 'required',
            'userID' => 'required',
        ]);
        $planID = $request->planID;
        $userID = $request->userID;

        $user = User::find($userID);
        $plan = PaymentPlans::where('id', $planID)->first();
        $total = $plan->price;

        $gatewayCode= "freeservice";
        $tax = 0;
        $taxRate =0;
        $status = "free_approved";
        $gateway = Gateways::where('is_active', 1)->first();
        if($gateway != null){
            $taxValue = taxToVal($plan->price, $gateway->tax);
            $total += $taxValue;
            $gatewayCode =$gateway->code;
            $tax = $taxValue;
            $taxRate = $gateway->tax;
            $status = $gateway->code."_approved";
        }

        $currency = Currency::where('id', $gateway->currency)->first()->code;
        $settings = Setting::first();
        try {
            DB::beginTransaction();
            # Create the subscription with the customer ID, price ID, and necessary options.
            $subscription = new Subscriptions();
            $subscription->user_id = $user->id;
            $subscription->name = $planID;
            $subscription->stripe_id = 'FLS-' . strtoupper(Str::random(13));
            $subscription->stripe_status = $status; 
            $subscription->stripe_price = "Not Needed";
            $subscription->quantity = 1;
            $subscription->trial_ends_at = null;
            $subscription->ends_at = $plan->frequency == 'lifetime_monthly'? \Carbon\Carbon::now()->addMonths(1): \Carbon\Carbon::now()->addYears(1);
            $subscription->auto_renewal = 1;
            $subscription->plan_id = $planID;
            $subscription->paid_with = $gatewayCode;
            $subscription->tax_rate = $taxRate;
            $subscription->tax_value = $tax;
            $subscription->total_amount = $total;
            $subscription->save();

            # save the order
            $order = new UserOrder();
            $order->order_id = $subscription->stripe_id;
            $order->plan_id = $planID;
            $order->user_id = $user->id;
            $order->payment_type = $gatewayCode;
            $order->price = $total;
            $order->affiliate_earnings = ($total * $settings->affiliate_commission_percentage) / 100;
            $order->status = 'Success';
            $order->country = $user->country ?? 'Unknown';
            $order->tax_rate = $taxRate;
            $order->tax_value = $tax;
            $order->save();
            # add plan credits
            $plan->total_words == -1? ($user->remaining_words = -1) : ($user->remaining_words += $plan->total_words);
            $plan->total_images == -1? ($user->remaining_images = -1) : ($user->remaining_images += $plan->total_images);
            $user->save();
            # inform the admin
            createActivity($user->id, __('Subscribed to'), $plan->name . ' '. __('Plan'), null);    
        
            DB::commit();
            return back()->with(['message' => __('The plan has been successfully assigned to the user.'),'type' => 'success' ]);
        }catch (\Exception $ex) {
            DB::rollBack();
            Log::error($gatewayCode."-> assignPlan(): ". $ex->getMessage());
            return back()->with(['message' => Str::before($ex->getMessage(), ':'),'type' => 'error' ]);
        } 
    }

    public static function assignTokenByAdmin(Request $request){
        $request->validate([
            'token' => 'required',
            'userID' => 'required',
        ]);
        $planID = $request->token;
        $userID = $request->userID;
        
        $user = User::find($userID);
        $plan = PaymentPlans::where('id', $planID)->first();

        $total = $plan->price;

        $gatewayCode= "freeservice";
        $tax = 0;
        $taxRate =0;
        $status = "free_approved";
        $gateway = Gateways::where('is_active', 1)->first();
        if($gateway != null){
            $taxValue = taxToVal($plan->price, $gateway->tax);
            $total += $taxValue;
            $gatewayCode =$gateway->code;
            $tax = $taxValue;
            $taxRate = $gateway->tax;
            $status = $gateway->code."_approved";
        }

        try {
            DB::beginTransaction();
            $order = new UserOrder();
            $order->order_id = 'ADM-' . strtoupper(Str::random(13));;
            $order->plan_id = $plan->id;
            $order->user_id = $user->id;
            $order->type = 'prepaid';
            $order->payment_type = $gatewayCode;
            $order->price = $plan->price;
            $order->affiliate_earnings = 0;
            $order->status = 'Approved';
            $order->country = $user->country ?? 'Unknown';
            $order->tax_rate = 0;
            $order->tax_value = 0;
            $order->save();

            $order->plan->total_words == -1? ($order->user->remaining_words = -1) : ($order->user->remaining_words += $order->plan->total_words);
            $order->plan->total_images == -1? ($order->user->remaining_images = -1) : ($order->user->remaining_images += $order->plan->total_images);
            $order->user->save();
            # sent mail if required here later
            createActivity($order->user->id, __('Purchased'), $order->plan->name. ' '. __('Plan'). ' '. __('For free'), null);
            DB::commit();
            return back()->with(['message' => __('The plan has been successfully assigned to the user.'),'type' => 'success' ]);
        } catch (\Exception $th) {
            DB::rollBack();
            Log::error($gatewayCode."-> subscribe(): ". $th->getMessage());
            return back()->with(['message' => Str::before($th->getMessage(), ':'),'type' => 'error' ]);
        } 
    }
}