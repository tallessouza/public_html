<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
// use App\Models\Subscriptions;
use Laravel\Cashier\Subscription as Subscriptions;
use App\Models\UserOrder;
use App\Events\FreePaymentEvent;

class FreePaymentListener
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


    public $queue = 'default';

    public $delay = 0;

    /**
     * Handle the event.
     */
    public function handle(FreePaymentEvent $event): void
    {
        try{
            $status = $event->status;
            $order_ids = $event->orderIds;
            # 1. free_renewed, 2. free_expired

            if($status == 'free_expired'){
                Subscriptions::whereIn('stripe_id', $order_ids)->update(['stripe_status' => $status]);
                $orders = UserOrder::whereIn('order_id', $order_ids)->get();
                foreach ($orders as $order) {
                    $order->user->remaining_words = 0;
                    $order->user->remaining_images = 0;
                    $order->user->save();
                    # sent mail if required here later
                    createActivity($order->user->id, __('Subscription Expired') , $order->plan->name. ' '. __('Plan'), null);
                }
            }else if($status == 'free_renewed'){
                $orders = UserOrder::whereIn('order_id', $order_ids)->get();
                foreach ($orders as $order) {
                    switch ($order->plan->frequency) {
                        case 'monthly':
                            Subscriptions::where('stripe_id', $order->order_id) ->update(['stripe_status' => $status, 'ends_at' => \Carbon\Carbon::now()->addMonths(1)]);
                            $msg= __('Subscription renewed for 1 month.');
                            break;
                        case 'yearly':
                            Subscriptions::where('stripe_id', $order->order_id) ->update(['stripe_status' => $status, 'ends_at' => \Carbon\Carbon::now()->addYears(1)]);
                            $msg= __('Subscription renewed for 1 year.');
                            break;
                        case 'lifetime_monthly':
                            Subscriptions::where('stripe_id', $order->order_id) ->update(['stripe_status' => $status, 'ends_at' => \Carbon\Carbon::now()->addMonths(1)]);
                            $msg= __('Subscription renewed for 1 month.');
                            break;
                        case 'lifetime_yearly':
                            Subscriptions::where('stripe_id', $order->order_id) ->update(['stripe_status' => $status, 'ends_at' => \Carbon\Carbon::now()->addYears(1)]);
                            $msg= __('Subscription renewed for 1 year.');
                            break;
                        default:
                            Subscriptions::where('stripe_id', $order->order_id) ->update(['stripe_status' => $status, 'ends_at' => \Carbon\Carbon::now()->addMonths(1)]);
                            $msg= __('Subscription renewed for 1 month.');
                            break;
                    }
                    # all old tokens deleted
                    $order->plan->total_words == -1? ($order->user->remaining_words = -1) : ($order->user->remaining_words = $order->plan->total_words);
                    $order->plan->total_images == -1? ($order->user->remaining_images = -1) : ($order->user->remaining_images = $order->plan->total_images);
                    $order->user->save();
                    # sent mail if required here later
                    createActivity($order->user->id, $msg , $order->plan->name. ' '. __('Plan'), null);
                }
            }


        }catch(\Exception $ex){
            Log::error("FreeTransferListener::handle()\n".$ex->getMessage());
        }
    }
}