<?php

namespace App\Services\PaymentGateways;

use App\Helpers\Classes\Helper;
use App\Models\GatewayProducts;
use App\Models\Gateways;
use App\Models\User;
use App\Models\PaymentPlans;
use App\Models\UserOrder;
use App\Services\Contracts\BaseGatewayService;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

use Illuminate\Support\Str;
use Laravel\Cashier\Subscription as Subscriptions;
use Razorpay\Api\Api;

class RazorpayService implements BaseGatewayService
{
    public static $gateway;

    protected static $GATEWAY_CODE      = "razorpay";

    protected static $GATEWAY_NAME      = "Razorpay";

 	# payment functions
    public static function saveAllProducts()
    {

        try{
            $gateway = self::geteway();

            if($gateway == null) {
                return back()->with(['message' => __('Please enable coingate'), 'type' => 'error']);
            }

            $plans = PaymentPlans::query()
                ->where('active', 1)
                ->get();

            foreach ($plans as $plan) {
                self::saveProduct($plan);
            }

        }catch (Exception $ex) {
            Log::error(self::$GATEWAY_CODE."-> saveAllProducts(): " . $ex->getMessage());
            return back()->with(['message' => $ex->getMessage(), 'type' => 'error']);
        }
    }

 	public static function saveProduct($plan): bool|\Illuminate\Http\RedirectResponse
    {

        if ($plan->type == 'prepaid' || $plan->currency != 'INR') {
            return true;
        }

        $gateway = self::geteway();

        if($gateway == null) {
            return back()->with(['message' => __('Please enable coingate'), 'type' => 'error']);
        }

        $api = self::client();


		try {
            DB::beginTransaction();

            $data = $api->plan->create([
                'period' => $plan->frequency,
                'interval' => 1,
                'item' => [
                    'name' => $plan->name,
                    'description' => $plan->name,
                    'amount' =>(int) $plan->price * 100,
                    'currency' => 'INR'
                ],
                'notes'=> []
            ]);


            $gateProduct = GatewayProducts::query()
                ->firstOrCreate([
                    'plan_id' => $plan->id,
                    'gateway_code' => self::$GATEWAY_CODE,
                    'gateway_title' => self::$GATEWAY_NAME,
                ]);

            $gateProduct->update([
                'plan_name' => $plan->name,
                'product_id' => $data['id'],
                'price_id' => $data['item']['id'],
                'payload' => $data->toArray(),
            ]);

			DB::commit();

            return true;

        } catch (\Exception $ex) {
            DB::rollBack();

            Log::error(self::$GATEWAY_CODE."-> saveProduct():\n" . $ex->getMessage());

            return back()->with(['message' => $ex->getMessage(), 'type' => 'error']);
        }
	}

    public static function client(): Api
    {
        $gateway = self::geteway();

        $apiKey = $gateway->getAttribute('mode') == 'sandbox'
            ? $gateway->getAttribute('sandbox_client_id')
            : $gateway->getAttribute('live_client_id');

        $secret = $gateway->getAttribute('mode') == 'sandbox'
            ? $gateway->getAttribute('sandbox_client_secret')
            : $gateway->getAttribute('live_client_secret');

        return new Api($apiKey, $secret);
    }

    public static function geteway(): Model|Builder|null
    {
        if (self::$gateway) {
            return self::$gateway;
        }

        self::$gateway = Gateways::where('code', self::$GATEWAY_CODE)->first();

        return self::$gateway;
    }

    public static function objectToArray($request)
    {
        if (is_object($request)) {
            if (property_exists($request, 'id')) {
                $request = json_decode(json_encode($request), true);
            }
        }

        return $request;
    }

    public static function gatewayDefinitionArray(): array
    {
        return [
            "code" => "razorpay",
            "title" => "Razorpay",
            "link" => "https://razorpay.com/",
            "active" => 0,
            "available" => 1,
            "img" => "/assets/img/payments/razorpay.svg",
            "whiteLogo" => 0,
            "mode" => 1,
            "sandbox_client_id" => 1,
            "sandbox_client_secret" => 1,
            "sandbox_app_id" => 0,
            "live_client_id" => 1,
            "live_client_secret" => 1,
            "live_app_id" => 0,
            "currency" => 0,
            "currency_locale" => 0,
            "notify_url" => 0,
            "base_url" => 0,
            "sandbox_url" => 0,
            "locale" => 0,
            "validate_ssl" => 0,
            "webhook_secret" => 0,
            "logger" => 0,
            "tax" => 1,              // Option in settings
            "bank_account_details" => 0,
            "bank_account_other" => 0,
        ];
    }

    public static function subscribe($plan)
    {
        $order_id = 'ORDER-' . strtoupper(Str::random(13));

        return view("panel.user.finance.subscription.". self::$GATEWAY_CODE, compact('plan','order_id'));
    }

    public static function subscribeCheckout(Request $request, $referral = null)
    {
        $planID = $request->input('planID', null);

        $plan = PaymentPlans::query()
            ->where('id', $planID)
            ->first();

        $gateway = self::geteway();

        if($gateway == null) {
            return back()->with(['message' => __('Please enable coingate'), 'type' => 'error']);
        }

        $gatewayProduct = GatewayProducts::query()
            ->where('plan_id', $plan->getAttribute('id'))
            ->where('gateway_code', self::$GATEWAY_CODE)
            ->first();

        if (! $gatewayProduct) {
            return back()->with(['message' => __('Gateway product not found'), 'type' => 'error']);
        }

        $planId = $gatewayProduct->getAttribute('product_id');

        try {

            $api = self::client();

            $data = $api->subscription->create([
                "plan_id" => $planId,
                "total_count" => 6,
                "quantity" => 1,
                "customer_notify" => 0
            ]);

            Subscriptions::query()
                ->create([
                    'user_id' => auth()->id(),
                    'name' => $plan->id,
                    'stripe_id' => $data['id'],
                    'stripe_status' => "WAITING",
                    'stripe_price' => null,
                    'quantity' => 1,
                    'trial_ends_at' => null,
                    'tax_rate' => 0,
                    'tax_value' => 0,
                    'coupon' => null,
                    'total_amount' => $plan->price,
                    'plan_id' => $plan->id,
                    'paid_with' => self::$GATEWAY_CODE,
                ]);

            $order = UserOrder::query()
                ->create([
                    'order_id' => $data['id'],
                    'plan_id' => $plan->id,
                    'user_id' => auth()->id(),
                    'payment_type' => self::$GATEWAY_CODE,
                    'price' => $plan->price,
                    'affiliate_earnings' => 0,
                    'status' => 'WAITING',
                    'country' => $user->country ?? 'Unknown',
                    'tax_rate' => 0,
                    'type'=> 'subscription',
                    'tax_value' => 0,
                    'payload' => $data->toArray(),
                ]);

			\App\Models\Usage::getSingle()->updateSalesCount($plan->price);
            $short_link = $data['short_url'];

            return redirect($short_link);
        } catch (Exception $exception) {
            Log::error(self::$GATEWAY_CODE."-> subscribe(): ". $exception->getMessage());
            return back()->with(['message' => Str::before($exception->getMessage(), ':'),'type' => 'error' ]);
        }
    }

    public static function prepaidCheckout(Request $request, $referral = null)
    {
        $planID = $request->input('planID', null);

        $orderID = $request->input('orderID', null);

        $plan = PaymentPlans::query()->where('id', $planID)->first();

        $user = Auth::user();

        $gateway = self::geteway();

        if($gateway == null) {
            return back()->with(['message' => __('Please enable coingate'), 'type' => 'error']);
        }

        $api = self::client();

        try {
            $order = UserOrder::query()
                ->create([
                    'order_id' => $orderID,
                    'plan_id' => $plan->id,
                    'user_id' => $user->id,
                    'payment_type' => self::$GATEWAY_CODE,
                    'price' => $plan->price,
                    'affiliate_earnings' => 0,
                    'status' => 'WAITING',
                    'country' => $user->country ?? 'Unknown',
                    'tax_rate' => 0,
                    'tax_value' => 0,
                    'type'  => 'token-pack',
                    'payload' => [],
                ]);



            $paymentLink = $api->paymentLink->create([
                'amount'=>$plan->price * 100,
                'currency'=>'INR',
                'accept_partial'=>true,
                'reference_id'=> $orderID,
                'description' => 'For XYZ purpose',
                'customer' => [
                    'name'=> auth()->user()->name,
                    'email' => auth()->user()->email,
                ],
                'notify'=> [ 'sms'=> false, 'email' => true] ,
                'reminder_enable'=>true ,
                'callback_url' => Helper::setting('site_url') . '/webhooks/coingate',
                'callback_method'=>'get'
            ]);

            $order->update([
                'order_id' => $paymentLink['id'], // 'id' => 'pl_1J2f3g4h5I6j7k8l9',
                'payload' => $paymentLink->toArray()
            ]);

			\App\Models\Usage::getSingle()->updateSalesCount($plan->price);

            return redirect()->to($paymentLink['short_url']);

        }catch (Exception $exception) {
            Log::error(self::$GATEWAY_CODE."-> prepaidCheckout(): ". $exception->getMessage());
            return back()->with(['message' => Str::before($exception->getMessage(), ':'),'type' => 'error' ]);
        }
    }

    public static function prepaid($plan)
    {
        $order_id = 'ORDER-' . strtoupper(Str::random(13));

        return view("panel.user.finance.prepaid.". self::$GATEWAY_CODE, compact('plan','order_id'));
    }

    public static function subscribeCancel(?User $internalUser = null)
    {
        $user = Auth::user() ?: $internalUser;

        $userId = $user->getAttribute('id');

        // Get current active subscription
        $activeSub = getCurrentActiveSubscription($userId);

        if (! $activeSub) {
            return;
        }

        $plan = PaymentPlans::query()
            ->where('id', $activeSub->getAttribute('plan_id'))
            ->first();

        $api = self::client();


        $data = $api->subscription->fetch($activeSub->getAttribute('stripe_id'))->cancel([]);

        if ($data) {
            $status = data_get($data, 'status');

            if ($status == 'cancelled') {
                $activeSub->stripe_status = $status;
                $activeSub->ends_at = Carbon::now();
                $activeSub->save();

                $recent_words = $user->remaining_words - $plan->total_words;
                $recent_images = $user->remaining_images - $plan->total_images;
                $user->remaining_words = $recent_words < 0 ? 0 : $recent_words;
                $user->remaining_images = $recent_images < 0 ? 0 : $recent_images;
                $user->save();
                createActivity($user->id, 'Cancelled', 'Subscription plan', null);

                return back()->with(['message' => __('Your subscription is cancelled succesfully.'), 'type' => 'success']);
            }
        }


        return back()->with(['message' => __('Could not find active subscription. Nothing changed!'), 'type' => 'error']);
    }


    public static function cancelSubscribedPlan($subscription, $planId)
    {
        $api = self::client();

        $user = Auth::user();

        $check = $subscription instanceof Subscriptions;

        if (! $check){
            $subscription = Subscriptions::where('id', $subscription)->first();
        }


        if ($subscription == null) {
            return back()->with(['message' => __('Subscription not found'), 'type' => 'error']);
        }

        try {

            $data = $api->subscription->fetch($subscription->getAttribute('stripe_id'))->cancel([]);

            $subscription->update([
                'stripe_status' => 'canceled'
            ]);

        } catch (Exception $ex) {

            Log::error(self::$GATEWAY_CODE . "-> cancelSubscribedPlan(): " . $ex->getMessage());
            return back()->with(['message' => $ex->getMessage(), 'type' => 'error']);
        }
    }

    public static function checkIfTrial()
    {
        return true;
    }

    public static function getSubscriptionRenewDate()
    {
        $user = Auth::user() ?: User::query()->first();

        $subscription = getCurrentActiveSubscription($user->getAttribute('id'));

        $next_delivery_date = null;

        if ($subscription) {

            $id = $subscription->getAttribute('stripe_id');

            $request = self::client()->subscription->fetch($id);

            $next_delivery_date =  data_get($request, 'charge_at');
        }

        if ($next_delivery_date) {
            return date('F jS, Y',$next_delivery_date);
        }

        return false;
    }

    public static function getSubscriptionStatus($incomingUserId = null)
    {
        $user = null;

        if ($incomingUserId) {
            $user = User::query()->where('id', $incomingUserId)->first();
        }

        if (Auth::check() && $incomingUserId == null) {
            $user = Auth::user();
        }

        if (! $user) {
            return false;
        }

        $subscription = getCurrentActiveSubscription($user->getAttribute('id'));

        if ($subscription) {
            $id = $subscription->getAttribute('stripe_id');

            try {
                $request = self::client()->subscription->fetch($id);

                $status = data_get($request, 'status');

                if ((string) $status == 'active') {
                    return true;
                } else {
                    if ($subscription->getAttribute('created_at') < Carbon::now()->subHours(2)) {
                        $subscription->update([
                            'stripe_status' => 'cancelled',
                            'ends_at' => Carbon::now()
                        ]);
                    }
                    return false;
                }
            }catch (Exception $th) {
                if ($subscription->getAttribute('created_at') < Carbon::now()->subHours(2)) {
                    $subscription->update([
                        'stripe_status' => 'cancelled',
                        'ends_at' => Carbon::now(),
                    ]);
                }
                return false;
            }
        }

        return false;
    }

    public static function getSubscriptionDaysLeft()
    {
        $user = null;

        if (Auth::check()) {
            $user = Auth::user();
        }

        if (! $user) {
            return;
        }

        $subscription = getCurrentActiveSubscription($user->getAttribute('id'));

        if ($subscription) {
            $id = $subscription->getAttribute('stripe_id');

            try {
                $request = self::client()->subscription->fetch($id);

                $next_delivery_date =  data_get($request, 'charge_at');

                if ($next_delivery_date) {
                    $date = date('Y-m-d H:i:s', $next_delivery_date);

                    $next_delivery_date = Carbon::parse($date);

                    return $next_delivery_date->diffInDays(Carbon::now());
                }
            } catch (\Exception $th) {
                return false;
            }
        }
    }

    public static function handleWebhook(Request $request)
    {
        $data = $request->all();

        $entity = data_get($data, 'payload.subscription.entity');

        if ($entity) {
            $subscription = Subscriptions::query()
                ->where('stripe_id', data_get($entity, 'id'))
                ->first();


            if ($subscription)
            {
                $subscription->update([
                    'stripe_status' => data_get($entity,'status')
                ]);

                if (data_get($entity,'status') == 'active') {

                    $user = User::query()->where('id', $subscription->getAttribute('user_id'))->first();

                    $plan = PaymentPlans::query()->where('id', $subscription->getAttribute('plan_id'))->first();

                    $user->remaining_words = $user->remaining_words + $plan->total_words;

                    $user->remaining_images = $user->remaining_images + $plan->total_images;

                    $user->save();
                }
            }
        }

        return response()->json(['success' => true]);
    }


    public static function checkPayments()
    {
        $date = now()->subHours(2);

        $orders = UserOrder::query()
            ->where('payment_type', self::$GATEWAY_CODE)
            ->where('status', 'WAITING')
            ->where('created_at', '>', $date)
            ->get();

        foreach ($orders as $order) {
            $payments = $order->payload;

            $user = User::query()->where('id', $order->getAttribute('user_id'))->first();

            $plan = PaymentPlans::query()->where('id', $order->getAttribute('plan_id'))->first();

            $last = \Arr::last($payments);

            try {

                if ($order->type == 'token-pack') {

                    $api = self::client();

                    $data = $api->paymentLink->fetch($order->order_id);


                    $status = data_get($data, 'status');

                    if ($status == 'paid') {

                        $order->update([
                            'status' => 'PAID',
                        ]);

                        $user->remaining_words = $user->remaining_words + $plan->total_words;

                        $user->remaining_images = $user->remaining_images + $plan->total_images;

                        $user->save();
                    }

                }else {
                    $request = self::client()->subscription->fetch($order->order_id);

                    $subscription = Subscriptions::query()
                        ->where('stripe_id', $order->order_id)
                        ->first();

                    $status = data_get($request, 'status');

                    if ($subscription) {
                        $subscription->stripe_status = $status;
                        $subscription->save();

                        if ($status == 'active') {
                            $user->remaining_words = $user->remaining_words + $plan->total_words;

                            $user->remaining_images = $user->remaining_images + $plan->total_images;

                            $user->save();
                        }

                    }
                }

            }  catch (\Exception $th) {

            }
        }
    }
}