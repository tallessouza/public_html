<?php

namespace App\Listeners;

use App\Events\PaypalWebhookEvent;
use App\Models\PaymentPlans;
use App\Models\Setting;
use App\Models\User;
use App\Models\UserOrder;
use App\Models\WebhookHistory;
use App\Services\GatewaySelector;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Laravel\Cashier\Subscription as Subscriptions;
use Throwable;

class PaypalWebhookListener implements ShouldQueue
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    use InteractsWithQueue;

    public $afterCommit = true;

    // /**
    //  * The name of the connection the job should be sent to.
    //  *
    //  * @var string|null
    //  */
    // public $connection = 'sqs';

    /**
     * The name of the queue the job should be sent to.
     *
     * @var string|null
     */
    public $queue = 'default';

    /**
     * The time (seconds) before the job should be processed.
     *
     * @var int
     */
    public $delay = 0; //60

    /**
     * Handle the event.
     */
    public function handle(PaypalWebhookEvent $event): void
    {
        try {
            Log::info(json_encode($event->payload));

            $settings = Setting::first();

            $incomingJson = json_decode($event->payload);

            // Incoming data is verified at PayPalService handleWebhook function, which fires this event.

            $event_type = $incomingJson->event_type;
            $resource_id = $incomingJson->resource->id;

            // save incoming data

            $newData = new WebhookHistory();
            $newData->gatewaycode = 'paypal';
            $newData->webhook_id = $incomingJson->id;
            $newData->create_time = $incomingJson->create_time;
            $newData->resource_type = $incomingJson->resource_type;
            $newData->event_type = $event_type;
            $newData->summary = $incomingJson->summary;
            $newData->resource_id = $resource_id;
            $newData->resource_state = isset($incomingJson->resource->state) == true ? $incomingJson->resource->state : (isset($incomingJson->resource->status) ? $incomingJson->resource->status : null);
            if ($event_type == 'PAYMENT.SALE.COMPLETED') {
                $newData->parent_payment = $incomingJson->resource->parent_payment;
                $newData->amount_total = $incomingJson->resource->amount->total;
                $newData->amount_currency = $incomingJson->resource->amount->currency;
            }
            $newData->incoming_json = json_encode($incomingJson);
            $newData->status = 'check';
            $newData->save();

            // switch/check event type

            if ($event_type == 'BILLING.SUBSCRIPTION.CANCELLED') {
                // $resource_id is subscription id in this event.
                $currentSubscription = Subscriptions::where('stripe_id', $resource_id)->first();
                if ($currentSubscription->stripe_status != 'cancelled') {
                    $currentSubscription->stripe_status = 'cancelled';
                    $currentSubscription->ends_at = Carbon::now();
                    $currentSubscription->save();
                    $newData->status = 'checked';
                    $newData->save();
                }

            } elseif ($event_type == 'PAYMENT.SALE.COMPLETED') {
                // $resource_id is transaction id in this event.
                // Hence we must make new request to get subscription id.

                $provider = GatewaySelector::selectGateway('paypal')::getPaypalProvider();

                $filters = [
                    'transaction_id' => $resource_id,
                    'start_date' => Carbon::now()->subDays(7)->toIso8601String(),
                    'end_date' => Carbon::now()->addDays(2)->toIso8601String(),
                ];

                // https://developer.paypal.com/docs/api/transaction-search/v1/#transactions_get
                $transactionList = $provider->listTransactions($filters);
                $transactions = json_decode($transactionList);

                // Log::info(json_encode($transactions));

                if (array_key_exists('error', $transactions) === false) {

                    foreach ($transactions->transaction_details as $transaction) {
                        // https://developer.paypal.com/docs/transaction-search/transaction-event-codes/
                        // T0002: Subscription payment. Either payment sent or payment received.
                        // S: The transaction successfully completed without a denial and after any pending statuses.
                        if ($transaction->transaction_info->transaction_event_code == 'T0002' and $transaction->transaction_status == 'S') {

                            $amountPaidValue = $transaction->transaction_info->transaction_amount->value;
                            $amountPaidCurrency = $transaction->transaction_info->transaction_amount->currency_code;
                            $email = $transaction->payer_info->email_address;
                            $name = $transaction->payer_info->given_name;
                            $surname = $transaction->payer_info->surname;
                            $transaction_id = $transaction->transaction_info->transaction_id;

                            // We can NOT get subscription id directly, thats why we are going to make a workaround.
                            // Get user
                            $user = User::where('email', $email)->first();
                            if ($user != null) {

                                $userId = $user->id;
                                // Get users active/trial subscription
                                $activeSub = getCurrentActiveSubscription($userId);

                                if ($activeSub != null) {

                                    // Get plan
                                    $plan = PaymentPlans::where('id', $activeSub->plan_id)->first();

                                    if ($plan != null) {

                                        // Check if its price is equal to amountPaidValue.
                                        // amountPaidValue returns decimal with . (i.e. "value": "465.00" , "value": "-13.79")
                                        // we save price in plan as double (i.e. 10 , 19.9 (not 19.90))
                                        if (number_format((float) $amountPaidValue, 2, '.', '') == number_format((float) $plan->price, 2, '.', '')) {

                                            // check for duplication
                                            $duplicate = false;
                                            // check for first payment in subscription
                                            if (Carbon::parse($activeSub->created_at)->diffInMinutes(Carbon::parse($incomingJson->create_time)) < 5) {
                                                $duplicate = true;
                                            }

                                            if ($duplicate == false) {

                                                // if it is trial then convert it to active ( Check status from gateway first )
                                                // if it is active and/or converted to active add plan word/image amount to the user
                                                if ($activeSub->stripe_status == 'trialing') {

                                                    $subscription = $provider->showSubscriptionDetails($activeSub->stripe_id);

                                                    if (isset($subscription['error'])) {
                                                        Log::error("PaypalWebhookListener::handle() -> getSubscriptionStatus() :\n".json_encode($subscription));
                                                    } else {

                                                        if ($subscription['status'] == 'ACTIVE') {

                                                            $trial = false;
                                                            if (isset($subscription['billing_info']['cycle_executions'][0]['tenure_type'])) {
                                                                if ($subscription['billing_info']['cycle_executions'][0]['tenure_type'] == 'TRIAL') {
                                                                    $trial = true;
                                                                }
                                                            }

                                                            if ($trial == false) {
                                                                $activeSub->stripe_status = 'active';
                                                                $activeSub->save();
                                                            }

                                                            $payment = new UserOrder();
                                                            $payment->order_id = $transaction_id;
                                                            $payment->plan_id = $plan->id;
                                                            $payment->user_id = $user->id;
                                                            $payment->payment_type = 'PayPal Recurring Payment';
                                                            $payment->price = $plan->price;
                                                            $payment->affiliate_earnings = ($plan->price * $settings->affiliate_commission_percentage) / 100;
                                                            $payment->status = 'Success';
                                                            $payment->country = $user->country ?? 'Unknown';
                                                            $payment->save();

                                                            $plan->total_words == -1 ? ($user->remaining_words = -1) : ($user->remaining_words += $plan->total_words);
                                                            $plan->total_images == -1 ? ($user->remaining_images = -1) : ($user->remaining_images += $plan->total_images);

                                                            $user->save();

                                                            $newData->status = 'checked';
                                                            $newData->save();

                                                        } else {
                                                            $activeSub->stripe_status = 'cancelled';
                                                            $activeSub->ends_at = \Carbon\Carbon::now();
                                                            $activeSub->save();
                                                            $newData->status = 'checked';
                                                            $newData->save();
                                                        }
                                                    }
                                                }
                                            } else { // active or cancelled

                                                $subscription = $provider->showSubscriptionDetails($activeSub->stripe_id);

                                                if (isset($subscription['error'])) {
                                                    Log::error("PaypalWebhookListener::handle() -> getSubscriptionStatus() :\n".json_encode($subscription));
                                                } else {

                                                    // check for duplication
                                                    $duplicate = false;
                                                    // check for first payment in subscription
                                                    if (Carbon::parse($activeSub->created_at)->diffInMinutes(Carbon::parse($incomingJson->create_time)) < 5) {
                                                        $duplicate = true;
                                                    }

                                                    if ($duplicate == false) {

                                                        if ($subscription['status'] == 'ACTIVE') {

                                                            if ($activeSub->stripe_status == 'cancelled') {
                                                                $activeSub->stripe_status = 'active';
                                                                $activeSub->save();
                                                            }

                                                            $payment = new UserOrder();
                                                            $payment->order_id = $transaction_id;
                                                            $payment->plan_id = $plan->id;
                                                            $payment->user_id = $user->id;
                                                            $payment->payment_type = 'PayPal Recurring Payment';
                                                            $payment->price = $plan->price;
                                                            $payment->affiliate_earnings = ($plan->price * $settings->affiliate_commission_percentage) / 100;
                                                            $payment->status = 'Success';
                                                            $payment->country = $user->country ?? 'Unknown';
                                                            $payment->save();

                                                            $plan->total_words == -1 ? ($user->remaining_words = -1) : ($user->remaining_words += $plan->total_words);
                                                            $plan->total_images == -1 ? ($user->remaining_images = -1) : ($user->remaining_images += $plan->total_images);

                                                            $user->save();

                                                            $newData->status = 'checked';
                                                            $newData->save();

                                                        } else {
                                                            $activeSub->stripe_status = 'cancelled';
                                                            $activeSub->ends_at = \Carbon\Carbon::now();
                                                            $activeSub->save();
                                                            $newData->status = 'checked';
                                                            $newData->save();
                                                        }
                                                    }
                                                }

                                            }

                                        } else {
                                            Log::error('PaypalWebhookListener::handle() Error : Subscription prices do not match. || '.json_encode($transactions));
                                        }
                                    } else {
                                        Log::error('PaypalWebhookListener::handle() Error : Membership Plan Not Found || '.json_encode($transactions));
                                    }

                                } else {
                                    Log::error('PaypalWebhookListener::handle() Error : Subscription Not Found || '.json_encode($transactions));
                                }

                            } else {
                                Log::error('PaypalWebhookListener::handle() Error : User Not Found || '.json_encode($transactions));
                            }

                        }
                    }

                } else {
                    Log::error('PaypalWebhookListener::handle() Error : '.$transactions->error->message);
                }

            }

            // save new order if required
            // on cancel we do not delete anything. just check if subs cancelled

        } catch (\Exception $ex) {
            Log::error("PaypalWebhookListener::handle()\n".$ex->getMessage());
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(PaypalWebhookEvent $event, Throwable $exception): void
    {
        // $space = "*************************************************************************************************************";
        $space = '*****';
        $msg = '\n'.$space.'\n'.$space;
        $msg = $msg.json_encode($event->payload);
        $msg = $msg.'\n'.$space.'\n';
        $msg = $msg.'\n'.$exception.'\n';
        $msg = $msg.'\n'.$space.'\n'.$space;

        Log::error($msg);
    }
}
