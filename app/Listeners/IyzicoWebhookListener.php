<?php

namespace App\Listeners;

use App\Events\IyzicoWebhookEvent;
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

class IyzicoWebhookListener implements ShouldQueue
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
    public function handle(IyzicoWebhookEvent $event): void
    {

        $newData = new WebhookHistory();

        try {
            //Log::info(json_encode($event->payload));

            $settings = Setting::first();

            $incomingJson = json_decode($event->payload);

            // Incoming data is verified at IyzicoService handleWebhook function, which fires this event.

            $event_type = $incomingJson->iyziEventType;

            // save incoming data

            $newData->gatewaycode = 'iyzico';
            $newData->webhook_id = $incomingJson->iyziReferenceCode; // "aac139a9-43db-4f40-82dd-d4e5a77a3d2e",
            $newData->create_time = $incomingJson->iyziEventTime; //1579612261619
            $newData->resource_type = 'subscription';
            $newData->event_type = $incomingJson->iyziEventType; //"subscription.order.success",
            $newData->summary = $incomingJson->orderReferenceCode; //"9ed2d128-b106-464b-8170-84325e75703b",
            $newData->resource_id = $incomingJson->subscriptionReferenceCode; //"b0f6d38f-b2d1-4a72-9bf2-bc9375665f3a",
            $newData->resource_state = $incomingJson->iyziEventType == 'subscription.order.success' ? 'paid' : 'cancelled';
            $newData->incoming_json = json_encode($incomingJson);
            $newData->status = 'check';
            $newData->save();

            /*


            {

            "orderReferenceCode": "9ed2d128-b106-464b-8170-84325e75703b",

            "customerReferenceCode": "042f0b61-079a-4a38-9454-6564a3c11a5a",

            "subscriptionReferenceCode": "b0f6d38f-b2d1-4a72-9bf2-bc9375665f3a",

            "iyziReferenceCode": "aac139a9-43db-4f40-82dd-d4e5a77a3d2e",

            "iyziEventType": "subscription.order.success",

            "iyziEventTime": 1579612261619

            }




            {

            “orderReferenceCode”: “9ed2d128-b106-464b-8170-84325e75703b”,

            “customerReferenceCode”: “042f0b61-079a-4a38-9454-6564a3c11a5a”,

            “subscriptionReferenceCode”: “b0f6d38f-b2d1-4a72-9bf2-bc9375665f3a”,

            “iyziReferenceCode”: “aac139a9-43db-4f40-82dd-d4e5a77a3d2e”,

            “iyziEventType”: “subscription.order.failure”,

            “iyziEventTime”: 1579612261619

            }
            */

            if ($event_type == 'subscription.order.failure') {
                // $resource_id is subscription id in this event.
                $currentSubscription = Subscriptions::where('stripe_id', $newData->resource_id)->first();
                if ($currentSubscription->stripe_status != 'cancelled') {
                    $currentSubscription->stripe_status = 'cancelled';
                    $currentSubscription->ends_at = Carbon::now();
                    $currentSubscription->save();
                    $newData->status = 'checked';
                    $newData->save();
                }

            } elseif ($event_type == 'subscription.order.success') {
                // $resource_id is subscription id in this event.
                $currentSubscription = Subscriptions::where('stripe_id', $newData->resource_id)->first();

                $plan = PaymentPlans::where('id', $currentSubscription->plan_id)->first();

                // check for duplication against time
                $duplicate = false;
                // check for first payment in subscription
                if (Carbon::parse($currentSubscription->created_at)->diffInMinutes(Carbon::parse($newData->create_time)) < 5) {
                    $duplicate = true;
                }

                //check current subscription status from iyzico
                if (GatewaySelector::selectGateway('iyzico')::getSubscriptionStatus() == true) {

                    if ($duplicate == false) {
                        // if it is trial then convert it to active
                        // if it is active and/or converted to active add plan word/image amount to the user
                        // if($currentSubscription->stripe_status == 'trialing'){} // it may be cancelled so in any case its going to be active
                        $currentSubscription->stripe_status = 'active';
                        $currentSubscription->save();

                        $payment = new UserOrder();
                        $payment->order_id = $incomingJson->orderReferenceCode;
                        $payment->plan_id = $plan->id;
                        $payment->user_id = $currentSubscription->user_id;
                        $payment->payment_type = 'iyzico Recurring Payment';
                        $payment->price = $plan->price;
                        $payment->affiliate_earnings = ($plan->price * $settings->affiliate_commission_percentage) / 100;
                        $payment->status = 'Success';
                        $payment->country = $user->country ?? 'Unknown';
                        $payment->save();

                        $user = User::where('id', $currentSubscription->user_id)->first();
                        $plan->total_words == -1 ? ($user->remaining_words = -1) : ($user->remaining_words += $plan->total_words);
                        $plan->total_images == -1 ? ($user->remaining_images = -1) : ($user->remaining_images += $plan->total_images);

                        $user->save();

                        $newData->status = 'checked';
                        $newData->save();
                    }

                }

            }

        } catch (\Exception $ex) {
            Log::error("IyzicoWebhookListener::handle()\n".$ex->getMessage()."\n".$event->payload);
            $newData->status = 'error';
            $newData->save();
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(IyzicoWebhookEvent $event, Throwable $exception): void
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
