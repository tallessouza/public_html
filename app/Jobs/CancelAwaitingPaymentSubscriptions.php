<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Stripe\Exception\ApiErrorException;
use Stripe\Stripe;
// use App\Models\Subscriptions;
use Laravel\Cashier\Subscription as Subscriptions;
use Illuminate\Support\Facades\Log;

class CancelAwaitingPaymentSubscriptions implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $subscription;
    protected $stripe;
    /**
     * Create a new job instance.
     */
    public function __construct($stripe, $subscription)
    {
        $this->subscription = $subscription;
        $this->stripe = $stripe;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $subscriptionObj = $this->stripe->subscriptions->retrieve($this->subscription->stripe_id);
            if ($subscriptionObj !== null) {
                $subscriptionObj->delete();
            }
            $this->subscription->update(['stripe_status' => 'cancelled']);
        } catch (ApiErrorException $ex) {
            $this->subscription->update(['stripe_status' => 'cancelled']);
            Log::error("CancelAwaitingPaymentSubscriptions Job\n" . $ex->getMessage());
        }        
    }
}
