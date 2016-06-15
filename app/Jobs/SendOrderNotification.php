<?php

namespace App\Jobs;

use App\Jobs\Job;
use App\Mailers\OrderMailer;
use App\Models\Order;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendOrderNotification extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    public $order;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(OrderMailer $orderMailer)
    {
        $orderMailer->sendOrderNotification($this->order);
    }
}
