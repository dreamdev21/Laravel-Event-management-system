<?php

namespace App\Listeners;

use App\Events\OrderCompletedEvent;
use App\Jobs\GenerateTicket;
use App\Jobs\SendOrderNotification;
use App\Jobs\SendOrderTickets;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class OrderCompletedListener implements ShouldQueue
{

    use DispatchesJobs;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  OrderCompletedEvent  $event
     * @return void
     */
    public function handle(OrderCompletedEvent $event)
    {
        /**
         * Generate the PDF tickets and send notification emails etc.
         */
        Log::info('Begin Processing Order: ' . $event->order->order_reference);
        $this->dispatchNow(new GenerateTicket($event->order->order_reference));
        $this->dispatch(new SendOrderTickets($event->order));
        $this->dispatch(new SendOrderNotification($event->order));

    }
}
