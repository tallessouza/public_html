<?php

namespace App\Services\PaymentGateways;

use App\Events\StripeWebhookEvent;
use App\Jobs\CancelAwaitingPaymentSubscriptions;
use App\Jobs\ProcessStripeCustomerJob;
use App\Models\Coupon;
use App\Models\Currency;
use App\Models\GatewayProducts;
use App\Models\Gateways;
use App\Models\OldGatewayProducts;
// use App\Models\Subscriptions;
use App\Models\PaymentPlans;
// use App\Models\SubscriptionItems;
use App\Models\Setting;
use App\Models\User;
use App\Models\UserOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Cashier\Subscription as Subscriptions;
use Stripe\Exception\AuthenticationException;
use Stripe\Exception\InvalidArgumentException;
use Stripe\PaymentIntent;
use Stripe\Stripe;
use Stripe\StripeClient;

/**
 * Base functions foreach payment gateway
 *
 * @param saveAllProducts
 * @param saveProduct ($plan)
 * @param subscribe ($plan)
 * @param subscribeCheckout (Request $request, $referral= null)
 * @param prepaid ($plan)
 * @param prepaidCheckout (Request $request, $referral= null)
 * @param getSubscriptionStatus ($incomingUserId = null)
 * @param getSubscriptionDaysLeft
 * @param subscribeCancel
 * @param checkIfTrial
 * @param getSubscriptionRenewDate
 * @param cancelSubscribedPlan ($subscription)
 */
class StripeService
{
    protected static $GATEWAY_CODE = 'stripe';

    protected static $GATEWAY_NAME = 'Stripe';

    // payment functions
    public static function saveAllProducts()
    {
        $key = self::getKey();
        try {
            $stripe = new StripeClient($key);
            // ------------ start creation of users stripe ids, for the first time only ------------------
            $existingCustomerIds = collect();
            $cursor = null;
            do {
                $parameters = ['limit' => 100];
                if ($cursor !== null) {
                    $parameters['starting_after'] = $cursor;
                }
                $customers = $stripe->customers->all($parameters);
                $existingCustomerIds = $existingCustomerIds->merge($customers->data);
                if ($customers->has_more) {
                    $cursor = $customers->data[count($customers->data) - 1]->id;
                } else {
                    $cursor = null;
                }
            } while ($cursor !== null);
            $allUsers = User::all();
            $userUpdates = [];
            foreach ($allUsers as $aUser) {
                if (! in_array($aUser->stripe_id, $existingCustomerIds->pluck('id')->toArray())) {
                    $userData = [
                        'email' => $aUser->email,
                        'name' => $aUser->name.' '.$aUser->surname,
                        'phone' => $aUser->phone,
                        'address' => [
                            'line1' => $aUser->address,
                            'postal_code' => $aUser->postal,
                        ],
                    ];
                    $userUpdates[] = [
                        'user' => $aUser,
                        'userData' => $userData,
                    ];
                }
            }
            foreach ($userUpdates as $update) {
                dispatch(new ProcessStripeCustomerJob($stripe, $update['user'], $update['userData']));
            }
            // ------------ end creation of users stripe ids, for the first time only ------------------
            $plans = PaymentPlans::where('active', 1)->get();
            foreach ($plans as $plan) {
                self::saveProduct($plan);
            }
            // create webhooks after saving the products
            $tmp = self::createWebhook();
        } catch (\Exception $ex) {
            Log::error(self::$GATEWAY_CODE.'-> saveAllProducts(): '.$ex->getMessage());

            return back()->with(['message' => $ex->getMessage(), 'type' => 'error']);
        }
    }

    public static function saveProduct($plan)
    {
        $gateway = Gateways::where('code', self::$GATEWAY_CODE)->first() ?? abort(404);
        try {
            DB::beginTransaction();
            $price = (int) (((float) $plan->price) * 100); // Must be in cents level for stripe
            $currency = Currency::where('id', $gateway->currency)->first()->code;
            $stripe = new StripeClient(self::getKey($gateway));
            $oldProductId = null;

            // check if product exists
            $product = GatewayProducts::where(['plan_id' => $plan->id, 'gateway_code' => self::$GATEWAY_CODE])->first();
            $newProduct = $stripe->products->create(['name' => $plan->name]); // Create product in every situation. maybe user updated stripe credentials.
            if ($product !== null) {
                if ($product->product_id !== null && $plan->name !== null) {
                    $oldProductId = $product->product_id; // Product has been created before
                } // ELSE Product has not been created before but record exists. Create new product and update record.
            } else {
                $product = new GatewayProducts();
                $product->plan_id = $plan->id;
                $product->gateway_code = self::$GATEWAY_CODE;
                $product->gateway_title = self::$GATEWAY_NAME;
            }
            $product->product_id = $newProduct->id;
            $product->plan_name = $plan->name;
            $product->save();
            $data = [
                'unit_amount' => $price,
                'currency' => $currency,
                'product' => $product->product_id,
            ];
            // if Subscription price and its not lifetime subscription
            if ($plan->price != 0 && $plan->type == 'subscription' && $plan->frequency !== 'lifetime_monthly' && $plan->frequency !== 'lifetime_yearly') {
                $data['recurring'] = ['interval' => $plan->frequency == 'monthly' ? 'month' : 'year'];
                // check if price exists
                if ($product->price_id !== null) {
                    // Since stripe api does not allow to update recurring values, we deactivate all prices added to this product before and add a new price object.
                    // Deactivate all prices
                    foreach ($stripe->prices->all(['product' => $product->product_id]) as $oldPrice) {
                        try {
                            $stripe->prices->update($oldPrice->id, ['active' => false]);
                        } catch (\Exception $ex) {
                        }
                    }
                    $updatedPrice = $stripe->prices->create($data);
                    // save history and update all plans prices and cancel the plan subs with updateUserData()
                    // --------- start create history for old priceID ---------
                    $history = new OldGatewayProducts();
                    $history->plan_id = $plan->id;
                    $history->plan_name = $plan->name;
                    $history->gateway_code = self::$GATEWAY_CODE;
                    $history->product_id = $product->product_id;
                    $history->old_product_id = $oldProductId;
                    $history->old_price_id = $product->price_id;
                    $history->new_price_id = $updatedPrice->id;
                    $history->status = 'check';
                    $history->save();
                    // --------- end create history for old priceID ---------
                    $tmp = self::updateUserData();
                } else {
                    $updatedPrice = $stripe->prices->create($data);
                }
                $product->price_id = $updatedPrice->id;

            } else {
                $product->price_id = 'Not Needed';
            }

            $product->save();
            DB::commit();
        } catch (\Exception $ex) {
            DB::rollBack();
            Log::error(self::$GATEWAY_CODE."-> saveProduct():\n".$ex->getMessage());

            return back()->with(['message' => $ex->getMessage(), 'type' => 'error']);
        }
    }

    public static function subscribe($plan)
    {
        $gateway = Gateways::where('code', self::$GATEWAY_CODE)->where('is_active', 1)->first() ?? abort(404);
        try {
            DB::beginTransaction();
            $exception = null;
            $key = self::getKey($gateway);
            Stripe::setApiKey($key);
            $stripe = new StripeClient($key);
            $user = auth()->user();
            try {
                if ($user->stripe_id != null) {
                    // Check if the customer already exists in Stripe
                    $existingCustomer = $stripe->customers->retrieve($user->stripe_id);
                } else {
                    // Customer doesn't exist, create a new customer
                    $userData = [
                        'email' => $user->email,
                        'name' => $user->name.' '.$user->surname,
                        'phone' => $user->phone,
                        'address' => [
                            'line1' => $user->address,
                            'postal_code' => $user->postal,
                        ],
                    ];
                    $stripeCustomer = $stripe->customers->create($userData);
                    $user->stripe_id = $stripeCustomer->id;
                    $user->save();
                }
            } catch (\Stripe\Exception\InvalidRequestException $e) {
                // Customer doesn't exist, create a new customer
                $userData = [
                    'email' => $user->email,
                    'name' => $user->name.' '.$user->surname,
                    'phone' => $user->phone,
                    'address' => [
                        'line1' => $user->address,
                        'postal_code' => $user->postal,
                    ],
                ];
                $stripeCustomer = $stripe->customers->create($userData);
                $user->stripe_id = $stripeCustomer->id;
                $user->save();
            }
            $currency = Currency::where('id', $gateway->currency)->first()->code;
            $taxRate = $gateway->tax;
            $tax_rate_id = null;
            $taxValue = taxToVal($plan->price, $taxRate);
            if ($taxRate > 0) {
                try {
                    $stripe_taxs = $stripe->taxRates->all();
                    foreach ($stripe_taxs as $s_tax) {
                        if ($s_tax->percentage == $taxRate) {
                            $tax_rate_id = $s_tax->id;
                            break;
                        }
                    }
                    if ($tax_rate_id == null) {
                        $new_tax = $stripe->taxRates->create([
                            'percentage' => $taxRate,
                            'display_name' => Str::random(13),
                            'inclusive' => false,
                        ]);
                        $tax_rate_id = $new_tax->id ?? null;
                    }
                } catch (\Stripe\Exception\InvalidRequestException $e) {
                    $new_tax = $stripe->taxRates->create([
                        'percentage' => $taxRate,
                        'display_name' => Str::random(13),
                        'inclusive' => false,
                    ]);
                    $tax_rate_id = $new_tax->id ?? null;
                }
            }
            $coupon = checkCouponInRequest(); //if there a coupon in request it will return the coupin instanse
            $product = GatewayProducts::where(['plan_id' => $plan->id, 'gateway_code' => self::$GATEWAY_CODE])->first();
            if ($product == null) {
                $exception = __('Stripe product is not defined! Please save Membership Plan again.');

                return back()->with(['message' => $exception, 'type' => 'error']);
            }
            if ($product->price_id == null) {
                $exception = __('Stripe product ID is not set! Please save Membership Plan again.');

                return back()->with(['message' => $exception, 'type' => 'error']);
            }
            $paymentIntent = null;
            $price_id_product = $product->price_id;
            $newDiscountedPrice = $plan->price;
            $newDiscountedPriceCents = $plan->price * 100;

            // if the plan lifetime plan.
            if ($plan->frequency == 'lifetime_monthly' || $plan->frequency == 'lifetime_yearly') {
                if ($coupon) {
                    $newDiscountedPrice = $plan->price - ($plan->price * ($coupon->discount / 100));
                    $newDiscountedPriceCents = (int) (((float) $newDiscountedPrice) * 100);
                    if ($newDiscountedPrice != floor($newDiscountedPrice)) {
                        $newDiscountedPrice = number_format($newDiscountedPrice, 2);
                    }
                }

                // Create the subscription with the customer ID, price ID, and necessary options.
                $subscription = new Subscriptions();
                $subscription->user_id = $user->id;
                $subscription->name = $plan->id;
                $subscription->stripe_id = 'SLS-'.strtoupper(Str::random(13));
                $subscription->stripe_status = 'AwaitingPayment'; // $plan->trial_days != 0 ? "trialing" : "AwaitingPayment";
                $subscription->stripe_price = $price_id_product;
                $subscription->quantity = 1;
                $subscription->trial_ends_at = null;
                $subscription->ends_at = $plan->frequency == 'lifetime_monthly' ? \Carbon\Carbon::now()->addMonths(1) : \Carbon\Carbon::now()->addYears(1);
                $subscription->auto_renewal = 1;
                $subscription->plan_id = $plan->id;
                $subscription->paid_with = self::$GATEWAY_CODE;
                $subscription->save();

                // $subscriptionItem = new SubscriptionItems();
                // $subscriptionItem->subscription_id = $subscription->id;
                // $subscriptionItem->stripe_id = $subscription->stripe_id;
                // $subscriptionItem->stripe_product = $product->product_id;
                // $subscriptionItem->stripe_price = $price_id_product;
                // $subscriptionItem->quantity = 1;
                // $subscriptionItem->save();

                $paymentIntent = PaymentIntent::create([
                    'amount' => $newDiscountedPriceCents,
                    'currency' => $currency,
                    'automatic_payment_methods' => [
                        'enabled' => true,
                    ],
                    'metadata' => [
                        'product_id' => $product->product_id,
                        'price_id' => $product->price_id,
                        'plan_id' => $plan->id,
                    ],
                ]);

            } else {
                $subscriptionInfo = [
                    'customer' => $user->stripe_id,
                    'items' => [
                        [
                            'price' => $price_id_product,
                            'tax_rates' => $tax_rate_id ? [$tax_rate_id] : [],
                        ],
                    ],
                    'payment_behavior' => 'default_incomplete',
                    'payment_settings' => ['save_default_payment_method' => 'on_subscription'],
                    'expand' => ['latest_invoice.payment_intent'],
                    'metadata' => [
                        'product_id' => $product->product_id,
                        'price_id' => $price_id_product,
                        'plan_id' => $plan->id,
                    ],
                ];
                if ($coupon) {
                    $newDiscountedPrice = $plan->price - ($plan->price * ($coupon->discount / 100));
                    $newDiscountedPriceCents = (int) (((float) $newDiscountedPrice) * 100);
                    if ($newDiscountedPrice != floor($newDiscountedPrice)) {
                        $newDiscountedPrice = number_format($newDiscountedPrice, 2);
                    }
                    // search for exist coupon with same percentage created before in stripe then use it, else create new one. $new_coupon
                    try {
                        $new_coupon = null;
                        $stripe_coupons = $stripe->coupons->all()?->data;
                        foreach ($stripe_coupons ?? [] as $s_coupon) {
                            if ($s_coupon->percent_off == $coupon->discount) {
                                $new_coupon = $s_coupon;
                                break;
                            }
                        }
                        if ($new_coupon == null) {
                            $new_coupon = $stripe->coupons->create([
                                'percent_off' => $coupon->discount,
                                'duration' => 'once',
                            ]);
                        }
                    } catch (\Stripe\Exception\InvalidRequestException $e) {
                        $new_coupon = $stripe->coupons->create([
                            'percent_off' => $coupon->discount,
                            'duration' => 'once',
                        ]);
                    }
                    $subscriptionInfo['coupon'] = $new_coupon->id ?? null;
                }
                if ($plan->trial_days != 0) {
                    $trialEndTimestamp = \Carbon\Carbon::now()->addDays($plan->trial_days)->timestamp;
                    $subscriptionInfo += [
                        'trial_end' => strval($trialEndTimestamp),
                        'billing_cycle_anchor' => strval($trialEndTimestamp),
                    ];
                }
                // Create the subscription with the customer ID, price ID, and necessary options.
                $newSubscription = $stripe->subscriptions->create($subscriptionInfo);
                $subscription = new Subscriptions();
                $subscription->user_id = $user->id;
                $subscription->name = $plan->id;
                $subscription->stripe_id = $newSubscription->id;
                $subscription->stripe_status = 'AwaitingPayment'; // $plan->trial_days != 0 ? "trialing" : "AwaitingPayment";
                $subscription->stripe_price = $price_id_product;
                $subscription->quantity = 1;
                $subscription->trial_ends_at = $plan->trial_days != 0 ? \Carbon\Carbon::now()->addDays($plan->trial_days) : null;
                $subscription->ends_at = $plan->trial_days != 0 ? \Carbon\Carbon::now()->addDays($plan->trial_days) : \Carbon\Carbon::now()->addDays(30);
                $subscription->plan_id = $plan->id;
                $subscription->paid_with = self::$GATEWAY_CODE;
                $subscription->save();

                // $subscriptionItem = new SubscriptionItems();
                // $subscriptionItem->subscription_id = $subscription->id;
                // $subscriptionItem->stripe_id = $newSubscription->items->data[0]->id;
                // $subscriptionItem->stripe_product = $product->product_id;
                // $subscriptionItem->stripe_price = $price_id_product;
                // $subscriptionItem->quantity = 1;
                // $subscriptionItem->save();
                $paymentIntent = [
                    'subscription_id' => $newSubscription->id,
                    'client_secret' => ($plan->trial_days != 0)
                        ? $stripe->setupIntents->retrieve($newSubscription->pending_setup_intent, [])->client_secret
                        : $newSubscription->latest_invoice->payment_intent->client_secret,
                    'trial' => ($plan->trial_days != 0),
                    'currency' => $currency,
                    'amount' => $newDiscountedPriceCents,
                ];
            }
            DB::commit();

            return view('panel.user.finance.subscription.'.self::$GATEWAY_CODE, compact('plan', 'newDiscountedPrice', 'taxValue', 'taxRate', 'gateway', 'paymentIntent', 'product'));
        } catch (\Exception $ex) {
            DB::rollBack();
            Log::error(self::$GATEWAY_CODE.'-> subscribe(): '.$ex->getMessage());

            return back()->with(['message' => Str::before($ex->getMessage(), ':'), 'type' => 'error']);
        }
    }

    public static function subscribeCheckout(Request $request, $referral = null)
    {
        $gateway = Gateways::where('code', self::$GATEWAY_CODE)->where('is_active', 1)->first() ?? abort(404);
        $settings = Setting::first();
        $key = self::getKey($gateway);
        Stripe::setApiKey($key);
        $user = auth()->user();
        $stripe = new StripeClient($key);
        $couponID = null;
        if ($referral !== null) {
            $stripe->customers->update(
                $user->stripe_id,
                [
                    'metadata' => [
                        'referral' => $referral,
                    ],
                ]
            );
        }
        $previousRequest = app('request')->create(url()->previous());
        $intentType = $request->has('payment_intent') ? 'payment_intent' : ($request->has('setup_intent') ? 'setup_intent' : null);
        $intentId = $request->input($intentType);
        $clientSecret = $request->input($intentType.'_client_secret');
        $redirectStatus = $request->input('redirect_status');
        if ($redirectStatus != 'succeeded') {
            return back()->with(['message' => __("A problem occurred! $redirectStatus"), 'type' => 'error']);
        }
        $intentStripe = $request->has('payment_intent') ? 'paymentIntents' : ($request->has('setup_intent') ? 'setupIntents' : null);
        $intent = $stripe->{$intentStripe}->retrieve($intentId) ?? abort(404);
        try {
            DB::beginTransaction();
            // check validity of the intent
            if ($intent->client_secret == $clientSecret && $intent->status == 'succeeded') {
                self::cancelAllSubscriptions();
                $subscription = Subscriptions::where('paid_with', self::$GATEWAY_CODE)->where(['user_id' => $user->id, 'stripe_status' => 'AwaitingPayment'])->latest()->first();
                $planId = $subscription->plan_id;
                $plan = PaymentPlans::where('id', $planId)->first();
                $total = $plan->price;
                $currency = Currency::where('id', $gateway->currency)->first()->code;
                $tax_rate_id = null;
                $taxValue = taxToVal($plan->price, $gateway->tax);
                // check the coupon existince
                if ($previousRequest->has('coupon')) {
                    $coupon = Coupon::where('code', $previousRequest->input('coupon'))->first();
                    if ($coupon) {
                        $coupon->usersUsed()->attach(auth()->user()->id);
                        $couponID = $coupon->discount;
                        $total -= ($plan->price * ($coupon->discount / 100));
                        if ($total != floor($total)) {
                            $total = number_format($total, 2);
                        }
                    }
                }
                $total += $taxValue;
                // update the subscription to make it active and save the total
                if ($subscription->auto_renewal) {
                    $subscription->stripe_status = 'stripe_approved';
                } else {
                    $subscription->stripe_status = $plan->trial_days != 0 ? 'trialing' : 'active';
                }
                $subscription->tax_rate = $gateway->tax;
                $subscription->tax_value = $taxValue;
                $subscription->coupon = $couponID;
                $subscription->total_amount = $total;
                $subscription->save();
                // save the order
                $order = new UserOrder();
                $order->order_id = $subscription->stripe_id;
                $order->plan_id = $planId;
                $order->user_id = $user->id;
                $order->payment_type = self::$GATEWAY_CODE;
                $order->price = $total;
                $order->affiliate_earnings = ($total * $settings->affiliate_commission_percentage) / 100;
                $order->status = 'Success';
                $order->country = Auth::user()->country ?? 'Unknown';
                $order->tax_rate = $gateway->tax;
                $order->tax_value = $taxValue;
                $order->save();
                // add plan credits
                $plan->total_words == -1 ? ($user->remaining_words = -1) : ($user->remaining_words += $plan->total_words);
                $plan->total_images == -1 ? ($user->remaining_images = -1) : ($user->remaining_images += $plan->total_images);
                $user->save();
                // check if any other "AwaitingPayment" subscription exists if so cancel it
                // $waiting_subscriptions = Subscriptions::where('paid_with', self::$GATEWAY_CODE)->where(['user_id' => $user->id, 'stripe_status' => 'AwaitingPayment'])
                // ->get();
                // foreach($waiting_subscriptions as $waitingSubs){
                //     dispatch(new CancelAwaitingPaymentSubscriptions($stripe, $waitingSubs));
                // }
                // inform the admin
                createActivity($user->id, __('Subscribed to'), $plan->name.' '.__('Plan'), null);
				\App\Models\Usage::getSingle()->updateSalesCount($total);
            } else {
                Log::error("StripeController::subscribeCheckout() - Invalid $intentType");
                DB::rollBack();

                return back()->with(['message' => __("A problem occurred! $redirectStatus"), 'type' => 'error']);
            }
            DB::commit();

            if (class_exists('App\Events\AffiliateEvent')) {
                event(new \App\Events\AffiliateEvent($total, $gateway->currency));
            }

            return redirect()->route('dashboard.user.payment.succesful')->with([
                'message' => __('Thank you for your purchase. Enjoy your remaining words and images.'),
                'type' => 'success',
            ]);
        } catch (\Exception $ex) {
            DB::rollBack();
            Log::error(self::$GATEWAY_CODE.'-> subscribeCheckout(): '.$ex->getMessage());

            return back()->with(['message' => Str::before($ex->getMessage(), ':'), 'type' => 'error']);
        }
    }

    public static function prepaid($plan)
    {
        $gateway = Gateways::where('code', self::$GATEWAY_CODE)->where('is_active', 1)->first() ?? abort(404);
        try {
            $user = auth()->user();
            $key = self::getKey($gateway);
            Stripe::setApiKey($key);
            $stripe = new StripeClient($key);
            try {
                if ($user->stripe_id != null) {
                    // Check if the customer already exists in Stripe
                    $existingCustomer = $stripe->customers->retrieve($user->stripe_id);
                } else {
                    // Customer doesn't exist, create a new customer
                    $userData = [
                        'email' => $user->email,
                        'name' => $user->name.' '.$user->surname,
                        'phone' => $user->phone,
                        'address' => [
                            'line1' => $user->address,
                            'postal_code' => $user->postal,
                        ],
                    ];
                    $stripeCustomer = $stripe->customers->create($userData);
                    $user->stripe_id = $stripeCustomer->id;
                    $user->save();
                }
            } catch (\Stripe\Exception\InvalidRequestException $e) {
                // Customer doesn't exist, create a new customer
                $userData = [
                    'email' => $user->email,
                    'name' => $user->name.' '.$user->surname,
                    'phone' => $user->phone,
                    'address' => [
                        'line1' => $user->address,
                        'postal_code' => $user->postal,
                    ],
                ];
                $stripeCustomer = $stripe->customers->create($userData);
                $user->stripe_id = $stripeCustomer->id;
                $user->save();
            }
            $couponCode = request()->input('coupon', null);
            $currency = Currency::where('id', $gateway->currency)->first()->code;
            $taxRate = $gateway->tax;
            $taxValue = taxToVal($plan->price, $taxRate);
            $product = GatewayProducts::where(['plan_id' => $plan->id, 'gateway_code' => self::$GATEWAY_CODE])->first();
            if ($product == null) {
                $exception = __('Stripe product is not defined! Please save Membership Plan again.');

                return back()->with(['message' => $exception, 'type' => 'error']);
            }
            if ($product->price_id == null) {
                $exception = __('Stripe product ID is not set! Please save Membership Plan again.');

                return back()->with(['message' => $exception, 'type' => 'error']);
            }
            $newDiscountedPrice = $plan->price;
            $newDiscountedPriceCents = $plan->price * 100;
            if ($couponCode != null) {
                $coupon = Coupon::where('code', $couponCode)->first();
                if ($coupon) {
                    $newDiscountedPrice -= ($plan->price * ($coupon->discount / 100));
                    $newDiscountedPriceCents = (int) (((float) $newDiscountedPrice) * 100);
                    if ($newDiscountedPrice != floor($newDiscountedPrice)) {
                        $newDiscountedPrice = number_format($newDiscountedPrice, 2);
                    }
                }
            }
            $paymentIntent = PaymentIntent::create([
                'amount' => $newDiscountedPriceCents,
                'currency' => $currency,
                'automatic_payment_methods' => [
                    'enabled' => true,
                ],
                'metadata' => [
                    'product_id' => $product->product_id,
                    'price_id' => $product->price_id,
                    'plan_id' => $plan->id,
                ],
            ]);

            return view('panel.user.finance.prepaid.'.self::$GATEWAY_CODE, compact('plan', 'newDiscountedPrice', 'taxValue', 'taxRate', 'gateway', 'paymentIntent', 'product'));
        } catch (\Exception $th) {
            Log::error(self::$GATEWAY_CODE.'-> prepaid(): '.$th->getMessage());

            return back()->with(['message' => Str::before($th->getMessage(), ':'), 'type' => 'error']);
        }
    }

    public static function prepaidCheckout(Request $request, $referral = null)
    {
        $gateway = Gateways::where('code', self::$GATEWAY_CODE)->where('is_active', 1)->first() ?? abort(404);
        $settings = Setting::first();
        $key = self::getKey($gateway);
        Stripe::setApiKey($key);
        $user = auth()->user();
        $stripe = new StripeClient($key);
        if ($referral !== null) {
            $stripe->customers->update(
                $user->stripe_id,
                [
                    'metadata' => [
                        'referral' => $referral,
                    ],
                ]
            );
        }
        $previousRequest = app('request')->create(url()->previous());
        if ($request->has('payment_intent') && $request->has('payment_intent_client_secret') && $request->has('redirect_status')) {
            $payment_intent = $request->input('payment_intent');
            $payment_intent_client_secret = $request->input('payment_intent_client_secret');
            $redirect_status = $request->input('redirect_status');
            if ($redirect_status != 'succeeded') {
                return back()->with(['message' => __("A problem occurred! $redirect_status"), 'type' => 'error']);
            }
            $intent = $stripe->paymentIntents->retrieve($payment_intent);
            if ($intent == null || $intent->client_secret != $payment_intent_client_secret || $intent->status != 'succeeded') {
                return back()->with(['message' => __('A problem occurred!'), 'type' => 'error']);
            }
            try {
                DB::beginTransaction();

                $planId = $intent->metadata->plan_id;
                $productId = $intent->metadata->product_id;
                $priceId = $intent->metadata->price_id;
                $plan = PaymentPlans::where('id', $planId)->first();
                if ($plan == null) {
                    return back()->with(['message' => __('A problem occurred!'), 'type' => 'error']);
                }

                $total = $plan->price;
                if ($previousRequest->has('coupon')) {
                    $coupon = Coupon::where('code', $previousRequest->input('coupon'))->first();
                    if ($coupon) {
                        $couponID = $coupon->discount;
                        $total -= ($plan->price * ($coupon->discount / 100));
                        if ($total != floor($total)) {
                            $total = number_format($total, 2);
                        }
                        $coupon->usersUsed()->attach(auth()->user()->id);
                    }
                }
                $total += taxToVal($plan->price, $gateway->tax);

                $order = new UserOrder();
                $order->order_id = 'SPO-'.strtoupper(Str::random(13));
                $order->plan_id = $plan->id;
                $order->user_id = $user->id;
                $order->type = 'prepaid';
                $order->payment_type = self::$GATEWAY_CODE;
                $order->price = $total;
                $order->affiliate_earnings = ($total * $settings->affiliate_commission_percentage) / 100;
                $order->status = 'Success';
                $order->country = $user->country ?? 'Unknown';
                $order->tax_rate = $gateway->tax;
                $order->tax_value = taxToVal($plan->price, $gateway->tax);
                $order->save();

                $plan->total_words == -1 ? ($user->remaining_words = -1) : ($user->remaining_words += $plan->total_words);
                $plan->total_images == -1 ? ($user->remaining_images = -1) : ($user->remaining_images += $plan->total_images);
                $user->save();

                // check if any other "AwaitingPayment" subscription exists if so cancel it
                $waiting_subscriptions = Subscriptions::where('paid_with', self::$GATEWAY_CODE)->where(['user_id' => $user->id, 'stripe_status' => 'AwaitingPayment'])->get();
                foreach ($waiting_subscriptions as $waitingSubs) {
                    dispatch(new CancelAwaitingPaymentSubscriptions($stripe, $waitingSubs));
                }
                createActivity($user->id, __('Purchased'), $plan->name.' '.__('Plan'), null);
				\App\Models\Usage::getSingle()->updateSalesCount($total);
            } catch (\Exception $th) {
                DB::rollBack();
                Log::error(self::$GATEWAY_CODE.'-> prepaidCheckout(): '.$th->getMessage());

                return back()->with(['message' => Str::before($th->getMessage(), ':'), 'type' => 'error']);
            }
        } else {
            return back()->with(['message' => __('A problem occurred!'), 'type' => 'error']);
        }
        DB::commit();

        return redirect()->route('dashboard.user.payment.succesful')->with([
            'message' => __('Thank you for your purchase. Enjoy your remaining words and images.'),
            'type' => 'success',
        ]);
    }

    // other functions
    public static function subscribeCancel($internalUser = null)
    {
        $key = self::getKey();
        try {
            $user = $internalUser ?? Auth::user();
            Stripe::setApiKey($key);
            $stripe = new StripeClient($key);
            $activeSub = getCurrentActiveSubscription($user->id);
            if ($activeSub != null) {
                $plan = PaymentPlans::where('id', $activeSub->plan_id)->first();
                $recent_words = $user->remaining_words - $plan->total_words;
                $recent_images = $user->remaining_images - $plan->total_images;
                try {
                    $subscription = $stripe->subscriptions->retrieve($activeSub->stripe_id);
                    if ($subscription != null) {
                        $subscription->delete();
                    }
                } catch (\Exception $ex) {
                    Log::error(self::$GATEWAY_CODE."::subscribeCancel()\n".$ex->getMessage());
                    // return back()->with(['message' => __('Could not find active subscription. Nothing changed!'), 'type' => 'error']);
                }
                $activeSub->stripe_status = 'cancelled';
                $activeSub->save();
                $user->remaining_words = $recent_words < 0 ? 0 : $recent_words;
                $user->remaining_images = $recent_images < 0 ? 0 : $recent_images;
                $user->save();
                createActivity($user->id, __('cancelled'), $plan->name, null);
                if ($internalUser != null) {
                    return back()->with(['message' => __('User subscription is cancelled succesfully.'), 'type' => 'success']);
                }

                return redirect()->route('dashboard.user.index')->with(['message' => __('Your subscription is cancelled succesfully.'), 'type' => 'success']);
            }

            return back()->with(['message' => __('Could not find active subscription. Nothing changed!'), 'type' => 'error']);
        } catch (\Exception $th) {
            Log::error(self::$GATEWAY_CODE."-> subscribeCancel():\n".$th->getMessage());

            return back()->with(['message' => $th->getMessage(), 'type' => 'error']);
        }
    }

    public static function cancelSubscribedPlan($subscription, $planId)
    {
        $key = self::getKey();
        try {
            $user = Auth::user();
            $user->subscription($planId)->cancelNow();
            $user->save();

            return true;
        } catch (\Exception $th) {
            Log::error(self::$GATEWAY_CODE."-> cancelSubscribedPlan():\n".$th->getMessage());
            $plan = PaymentPlans::where('id', $planId)->first();
            $recent_words = $user->remaining_words - $plan->total_words;
            $recent_images = $user->remaining_images - $plan->total_images;
            $subscription->stripe_status = 'cancelled';
            $subscription->save();
            $user->remaining_words = $recent_words < 0 ? 0 : $recent_words;
            $user->remaining_images = $recent_images < 0 ? 0 : $recent_images;
            $user->save();

            return true;
        }
    }

    public static function checkIfTrial()
    {
        $user = Auth::user();
        $key = self::getKey();
        $sub = getCurrentActiveSubscription($user->id);
        if ($sub != null) {
            try {
                return $user->subscription($sub->name)->onTrial();
            } catch (\Throwable $th) {
                return false;
            }
        }

        return false;
    }

    public static function getSubscriptionDaysLeft()
    {
        $user = Auth::user();
        $settings = Setting::first();
        $key = self::getKey();
        $sub = getCurrentActiveSubscription($user->id);
        try {
            $activeSub = $sub->asStripeSubscription();
            if ($activeSub->status == 'active' || $activeSub->status == 'trialing') {
                return \Carbon\Carbon::now()->diffInDays(\Carbon\Carbon::createFromTimeStamp($activeSub->current_period_end));
            } elseif ($sub->stripe_status == 'stripe_approved') {
                return \Carbon\Carbon::now()->diffInDays(\Carbon\Carbon::parse($sub->ends_at));
            } else {
                return \Carbon\Carbon::now()->diffInDays(\Carbon\Carbon::parse($sub->trial_ends_at));
            }
        } catch (\Throwable $th) {
            if ($sub->stripe_status == 'stripe_approved') {
                return \Carbon\Carbon::now()->diffInDays(\Carbon\Carbon::parse($sub->ends_at));
            } else {
                return \Carbon\Carbon::now()->diffInDays(\Carbon\Carbon::parse($sub->trial_ends_at));
            }
        }
    }

    public static function getSubscriptionStatus($incomingUserId = null)
    {
        $incomingUserId == null ? $user = Auth::user() : $user = User::where('id', $incomingUserId)->first();
        $settings = Setting::first();
        $key = self::getKey();
        $sub = getCurrentActiveSubscription($user->id);
        if ($sub != null) {
            try {
                if ($sub->asStripeSubscription()->status == 'active' || $sub->asStripeSubscription()->status == 'trialing' || $sub->stripe_status == 'stripe_approved') {
                    return true;
                } else {
                    $sub->stripe_status = 'cancelled';
                    $sub->ends_at = \Carbon\Carbon::now();
                    $sub->save();

                    return false;
                }
            } catch (\Throwable $th) {
                if ($sub->stripe_status == 'stripe_approved') {
                    return true;
                } else {
                    $sub->stripe_status = 'cancelled';
                    $sub->ends_at = \Carbon\Carbon::now();
                    $sub->save();

                    return false;
                }
            }
        }

        return false;
    }

    private static function getKey($gateway = null)
    {
        $gateway = $gateway ?? Gateways::where('code', self::$GATEWAY_CODE)->where('is_active', 1)->first() ?? abort(404);
        $currency = Currency::where('id', $gateway->currency)->first()->code;
        if ($gateway->mode == 'sandbox') {
            config(['cashier.key' => $gateway->sandbox_client_id]);
            config(['cashier.secret' => $gateway->sandbox_client_secret]);
            config(['cashier.currency' => $currency]);
            $key = $gateway->sandbox_client_secret;
        } else {
            config(['cashier.key' => $gateway->live_client_id]);
            config(['cashier.secret' => $gateway->live_client_secret]);
            config(['cashier.currency' => $currency]);
            $key = $gateway->live_client_secret;
        }

        return $key;
    }

    private static function updateUserData()
    {
        $key = self::getKey();
        try {
            $history = OldGatewayProducts::where(['gateway_code' => self::$GATEWAY_CODE, 'status' => 'check'])->get();
            if ($history != null) {
                $user = Auth::user();
                $stripe = new StripeClient($key);
                foreach ($history as $record) {
                    // check record current status from gateway
                    $lookingFor = $record->old_price_id;
                    // if active disable it
                    if ($lookingFor != 'undefined') {
                        $stripe->prices->update($lookingFor, ['active' => false]);
                    }
                    // search subscriptions for record
                    $subs = Subscriptions::where('paid_with', self::$GATEWAY_CODE)
                        ->where('stripe_status', 'active')
                        ->where('stripe_price', $lookingFor)
                        ->get();
                    if ($subs != null) {
                        foreach ($subs as $sub) {
                            // cancel subscription order from gateway
                            $user->subscription($sub->name)->cancelNow();
                            // cancel subscription from our database
                            $sub->stripe_status = 'cancelled';
                            $sub->ends_at = \Carbon\Carbon::now();
                            $sub->save();
                        }
                    }
                    $record->status = 'checked';
                    $record->save();
                }
            }
        } catch (\Exception $ex) {
            Log::error(self::$GATEWAY_CODE."-> updateUserData():\n".$ex->getMessage());

            return ['result' => Str::before($ex->getMessage(), ':')];
        }
    }

    private static function cancelAllSubscriptions()
    {
        $key = self::getKey();
        try {
            $stripe = new StripeClient($key);
            $product = null;
            $user = Auth::user();
            $allSubscriptions = getCurrentActiveSubscription($user->id);
            if ($allSubscriptions != null) {
                foreach ($allSubscriptions as $waitingSubs) {
                    dispatch(new CancelAwaitingPaymentSubscriptions($stripe, $waitingSubs));
                }
                // old code
                // foreach ($allSubscriptions as $subs) {
                //     if ($subs->stripe_id != 'undefined' && $subs->stripe_id != null && $subs->user_id == $user->id) {
                //         try{
                //             $subscription = $stripe->subscriptions->retrieve($subs->stripe_id);
                //             if($subscription) {
                //                 $subscription->delete();
                //             }
                //         }catch(\Exception $ex){
                //             Log::error("StripeController::cancelAllSubscriptions()\n" . $ex->getMessage());
                //         }
                //     }
                // }
            }
        } catch (\Exception $ex) {
            Log::error(self::$GATEWAY_CODE."-> cancelAllSubscriptions():\n".$ex->getMessage());
        }
    }

    public static function getSubscriptionRenewDate()
    {
        $user = Auth::user();
        $key = self::getKey();
        $end = null;
        $activeSub = getCurrentActiveSubscription($user->id);
        try {
            $activeSub->asStripeSubscription();
            $end = $activeSub->current_period_end;
        } catch (\Throwable $th) {
            $end = $activeSub->ends_at;
        }

        return \Carbon\Carbon::createFromTimeStamp($end)->format('F jS, Y');
    }

    // webhook functions
    public static function verifyIncomingJson(Request $request)
    {
        $gateway = Gateways::where('code', self::$GATEWAY_CODE)->where('is_active', 1)->first() ?? abort(404);
        if (isset($gateway->webhook_secret)) {
            $secret = $gateway->webhook_secret;
            if (Str::startsWith($secret, 'whsec') == true) {
                $endpoint_secret = $secret;
                if ($request->hasHeader('stripe-signature') == true) {
                    $sig_header = $request->header('stripe-signature');
                } else {
                    Log::error('(Webhooks) StripeController::verifyIncomingJson() -> Invalid header');

                    return null;
                }
                $payload = $request->getContent();
                $event = null;
                try {
                    $event = \Stripe\Webhook::constructEvent(
                        $payload, $sig_header, $endpoint_secret
                    );

                    return json_encode($event);
                } catch (\UnexpectedValueException $e) {
                    // Invalid payload
                    Log::error('(Webhooks) StripeController::verifyIncomingJson() -> Invalid payload : '.$payload);

                    return null;
                } catch (\Stripe\Exception\SignatureVerificationException $e) {
                    // Invalid signature
                    Log::error('(Webhooks) StripeController::verifyIncomingJson() -> Invalid signature : '.$payload);

                    return null;
                }
            }
        }

        return null;
    }

    public static function handleWebhook(Request $request)
    {
        $verified = self::verifyIncomingJson($request);
        if ($verified != null) {
            // Retrieve the JSON payload
            $payload = $verified;
            // Fire the event with the payload
            event(new StripeWebhookEvent($payload));

            return response()->json(['success' => true]);
        } else {
            // Incoming json is NOT verified
            abort(404);
        }
    }

    public static function createWebhook()
    {
        $gateway = Gateways::where('code', self::$GATEWAY_CODE)->where('is_active', 1)->first() ?? abort(404);
        try {
            $stripe = new StripeClient(self::getKey($gateway));
            $webhooks = $stripe->webhookEndpoints->all();
            if (count($webhooks['data']) > 0) {
                // There is/are webhook(s) defined. Remove existing.
                foreach ($webhooks['data'] as $hook) {
                    $tmp = json_decode($stripe->webhookEndpoints->delete($hook->id, []));
                    if (isset($tmp->deleted)) {
                        if ($tmp->deleted == false) {
                            Log::error('Webhook '.$hook->id.' could not deleted.');
                        }
                    } else {
                        Log::error('Webhook '.$hook->id.' could not deleted.');
                    }
                }
            }
            // Create new webhook
            $url = url('/').'/webhooks/stripe';
            $events = [
                'invoice.paid',                     // A payment is made on a subscription.
                'customer.subscription.deleted',     // A subscription is cancelled.
            ];
            $response = $stripe->webhookEndpoints->create([
                'url' => $url,
                'enabled_events' => $events,
            ]);
            $gateway->webhook_id = $response->id;
            $gateway->webhook_secret = $response->secret;
            $gateway->save();
        } catch (AuthenticationException $th) {
            Log::error('StripeController::createWebhook(): '.$th->getMessage());

            return back()->with(['message' => __('Stripe Authentication Error. Invalid API Key provided.'), 'type' => 'error']);
        } catch (InvalidArgumentException $th) {
            Log::error('StripeController::createWebhook(): '.$th->getMessage());

            return back()->with(['message' => __('You must provide Stripe API Key.'), 'type' => 'error']);
        } catch (\Exception $th) {
            Log::error('StripeController::createWebhook(): '.$th->getMessage());

            return back()->with(['message' => 'Stripe Error : '.$th->getMessage(), 'type' => 'error']);
        }
    }

    public static function gatewayDefinitionArray(): array
    {
        return [
            'code' => 'stripe',
            'title' => 'Stripe',
            'link' => 'https://stripe.com/',
            'active' => 0,                      //if user activated this gateway - dynamically filled in main page
            'available' => 1,                   //if gateway is available to use
            'img' => '/assets/img/payments/stripe.svg',
            'whiteLogo' => 0,                   //if gateway logo is white
            'mode' => 1,                        // Option in settings - Automatically set according to the "Development" mode. "Development" ? sandbox : live (PAYPAL - 1)
            'sandbox_client_id' => 1,           // Option in settings 0-Hidden 1-Visible
            'sandbox_client_secret' => 1,       // Option in settings
            'sandbox_app_id' => 0,              // Option in settings
            'live_client_id' => 1,              // Option in settings
            'live_client_secret' => 1,          // Option in settings
            'live_app_id' => 0,                 // Option in settings
            'currency' => 1,                    // Option in settings
            'currency_locale' => 0,             // Option in settings
            'base_url' => 1,                    // Option in settings
            'sandbox_url' => 0,                 // Option in settings
            'locale' => 0,                      // Option in settings
            'validate_ssl' => 0,                // Option in settings
            'logger' => 0,                      // Option in settings
            'notify_url' => 0,                  // Gateway notification url at our side
            'webhook_secret' => 0,              // Option in settings
            'tax' => 1,              // Option in settings
            'bank_account_details' => 0,
            'bank_account_other' => 0,
        ];
    }
}