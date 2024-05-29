<?php

namespace App\Listeners;

use App\Events\StripeWebhookEvent;
use App\Models\PaymentPlans;
use App\Models\Setting;
use App\Models\User;
use App\Models\UserOrder;
use App\Models\WebhookHistory;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Laravel\Cashier\Subscription as Subscriptions;
use Throwable;

class YokassaWebhookListener implements ShouldQueue
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
    public $queue = 'stripelisteners';

    /**
     * The time (seconds) before the job should be processed.
     *
     * @var int
     */
    public $delay = 5; //60

    /**
     * Handle the event.
     */
    public function handle(StripeWebhookEvent $event): void
    {
        try {
            Log::info(json_encode($event->payload));

            $settings = Setting::first();

            $incomingJson = json_decode($event->payload);

            // Incoming data is verified at StripeController handleWebhook function, which fires this event.

            $event_type = $incomingJson->type;
            // $resource_id = $incomingJson->data->object->lines->data[0]->price->id; //Price id
            if ($event_type == 'invoice.paid') {
                $resource_id = $incomingJson->data->object->subscription; // Subscription id
                $resource_type = $incomingJson->data->object->lines->data[0]->type; // Subscription / prepaid
                $summary = $incomingJson->data->object->lines->data[0]->description;
                $resource_state = $incomingJson->data->object->status;

            } elseif ($event_type == 'customer.subscription.deleted') {
                $resource_id = $incomingJson->data->object->items->data[0]->subscription; // Subscription id
                $resource_type = $incomingJson->data->object->object; // Subscription / prepaid
                $summary = $incomingJson->data->object->cancellation_details->reason;
                $resource_state = 'cancelled'; // $incomingJson->data->object->items->status;
            }

            // save incoming data

            $newData = new WebhookHistory();
            $newData->gatewaycode = 'stripe';
            $newData->webhook_id = $incomingJson->id;
            $newData->create_time = $incomingJson->created;
            $newData->resource_type = $resource_type; // Subscription / prepaid
            $newData->event_type = $event_type;
            $newData->summary = $summary;
            $newData->resource_id = $resource_id;
            $newData->resource_state = $resource_state;
            if ($event_type == 'invoice.paid') {
                $newData->parent_payment = $incomingJson->data->object->payment_intent;
                $newData->amount_total = $incomingJson->data->object->lines->data[0]->amount;
                $newData->amount_currency = $incomingJson->data->object->lines->data[0]->currency;
            }
            $newData->incoming_json = json_encode($incomingJson);
            $newData->status = 'check';
            $newData->save();

            // // switch/check event type

            if ($event_type == 'customer.subscription.deleted') {
                // $resource_id is subscription id in this event.
                $currentSubscription = Subscriptions::where('stripe_id', $resource_id)->first();
                if ($currentSubscription->stripe_status != 'cancelled') {
                    $currentSubscription->stripe_status = 'cancelled';
                    $currentSubscription->ends_at = Carbon::now();
                    $currentSubscription->save();
                    $newData->status = 'checked';
                    $newData->save();
                }

            } elseif ($event_type == 'invoice.paid') {
                // $resource_id is subscription id in this event.
                $activeSub = Subscriptions::where('stripe_id', $resource_id)->first();
                if (isset($activeSub->plan_id) == true) { // Plan may be deleted and null at database.

                    // Get plan
                    $plan = PaymentPlans::where('id', $activeSub->plan_id)->first();

                    if ($plan != null) {
                        // Check status from gateway first
                        $currentStripeStatus = StripeController::getSubscriptionStatus($activeSub->user_id);

                        if ($currentStripeStatus == true) { // active or trial at stripe side

                            // check for duplication
                            $duplicate = false;
                            // check for first payment in subscription
                            if (Carbon::parse($activeSub->created_at)->diffInMinutes(Carbon::parse($incomingJson->created)) < 5) {
                                $duplicate = true;
                            }

                            if ($duplicate == false) {
                                // if it is trial then convert it to active
                                // if it is active and/or converted to active add plan word/image amount to the user
                                // if($activeSub->stripe_status == 'trialing'){} // it may be cancelled so in any case its going to be active
                                $activeSub->stripe_status = 'active';
                                $activeSub->save();

                                $payment = new UserOrder();
                                $payment->order_id = $incomingJson->id;
                                $payment->plan_id = $plan->id;
                                $payment->user_id = $activeSub->user_id;
                                $payment->payment_type = 'Stripe Recurring Payment';
                                $payment->price = $plan->price;
                                $payment->affiliate_earnings = ($plan->price * $settings->affiliate_commission_percentage) / 100;
                                $payment->status = 'Success';
                                $payment->country = $user->country ?? 'Unknown';
                                $payment->save();

                                $user = User::where('id', $activeSub->user_id)->first();
                                $plan->total_words == -1 ? ($user->remaining_words = -1) : ($user->remaining_words += $plan->total_words);
                                $plan->total_images == -1 ? ($user->remaining_images = -1) : ($user->remaining_images += $plan->total_images);

                                $user->save();

                                $newData->status = 'checked';
                                $newData->save();
                            }
                        }
                    }

                } else { // plan id is null at subscription database table.
                    if ($activeSub->stripe_status != 'cancelled') {
                        $activeSub->stripe_status = 'cancelled';
                        $activeSub->ends_at = Carbon::now();
                        $activeSub->save();
                        $newData->status = 'checked';
                        $newData->save();
                    }
                    Log::error('Payment on a deleted plan. Please check: '.$resource_id.' with incoming webhook : '.json_encode($incomingJson));
                }

            }

            // save new order if required
            // on cancel we do not delete anything. just check if subs cancelled

        } catch (\Exception $ex) {
            Log::error("YokassaWebhookListener::handle()\n".$ex->getMessage());
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(StripeWebhookEvent $event, Throwable $exception): void
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
