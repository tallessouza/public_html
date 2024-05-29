<?php

namespace App\Listeners;

use App\Events\PaystackWebhookEvent;
use App\Models\WebhookHistory;
use Carbon\Carbon;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Cashier\Subscription as Subscriptions;
use Throwable;

class PaystackWebhookListener
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
    public function handle(PaystackWebhookEvent $event): void
    {
        try {
            $payload = $event->payload;
            $method = 'handle'.Str::studly(str_replace('.', '_', $payload['event']));
            if (method_exists($this, $method)) {
                $response = $this->{$method}($payload);
            }
        } catch (\Exception $ex) {
            Log::error("PaystackWebhookListener::handle()\n".$ex->getMessage());
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(PaystackWebhookEvent $event, Throwable $exception): void
    {
        $space = '*****';
        $msg = '\n'.$space.'\n'.$space;
        $msg = $msg.json_encode($event->payload);
        $msg = $msg.'\n'.$space.'\n';
        $msg = $msg.'\n'.$exception.'\n';
        $msg = $msg.'\n'.$space.'\n'.$space;

        Log::error($msg);
    }

    protected function successMethod(array $parameters = [])
    {

    }

    protected function missingMethod(array $parameters = [])
    {

    }

    public function handleChargeSuccess($payload) //A successful charge was made
    {

    }

    public function handlesubScriptionDisable($payload) //A subscription desabled
    {
        $subscriptionData = $payload['data'];
        // Extract relevant subscription data
        $subscriptionCode = $subscriptionData['subscription_code'];
        $status = $subscriptionData['status'];
        // Extract plan details
        $plan = $subscriptionData['plan'];
        $planId = $plan['id'];
        // Extract customer details
        $customer = $subscriptionData['customer'];
        $customerFirstName = $customer['first_name'];
        $customerLastName = $customer['last_name'];
        $customerEmail = $customer['email'];
        // Extract authorization details
        $authorization = $subscriptionData['authorization'];
        $authorizationCode = $authorization['authorization_code'];
        // Save to Webhook History
        $newData = new WebhookHistory();
        $newData->gatewaycode = 'paystack';
        $newData->webhook_id = $authorizationCode;
        $newData->event_type = 'subscription.disable';
        $newData->resource_id = $subscriptionCode;  // Subscription id
        $newData->resource_type = 'subscription'; // Subscription
        $newData->status = 'check';
        $newData->summary = "Subscription Disabled: $customerFirstName $customerLastName - $customerEmail - $status - $planId";
        $newData->incoming_json = json_encode($payload);
        $newData->create_time = $subscriptionData['created_at'];
        $newData->resource_state = 'cancelled';
        $newData->save();

        $currentSubscription = Subscriptions::where('stripe_id', $subscriptionCode)->first();
        if ($currentSubscription != null && $currentSubscription->stripe_status != 'cancelled') {
            $currentSubscription->stripe_status = 'cancelled';
            $currentSubscription->ends_at = Carbon::now();
            $currentSubscription->save();
            $newData->status = 'checked';
            $newData->save();
        }
    }
}
