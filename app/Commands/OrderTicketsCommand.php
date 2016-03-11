<?php

namespace App\Commands;

use App\Attendize\mailers\OrderMailer;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Log;

class OrderTicketsCommand extends Command implements ShouldQueue, SelfHandling
{
    use InteractsWithQueue,
        SerializesModels;

    public $ticketOrder, $sendOrderConfirmation;
    private $outSeperator = "\n ---------------------- \n";

    /**
     * OrderTicketsCommand constructor.
     *
     * @param \App\Models\Order $ticketOrder
     * @param bool|true         $sendOrderConfirmation
     */
    public function __construct(\App\Models\Order $ticketOrder, $sendOrderConfirmation = true)
    {
        $this->ticketOrder = $ticketOrder;
        $this->sendOrderConfirmation = $sendOrderConfirmation;
    }

    /**
     * @param OrderMailer $mailer
     */
    public function handle(OrderMailer $mailer)
    {
        Log::info(date('d m y H:i')." - Starting Job {$this->job->getJobId()} ".__CLASS__);

        //1 - Notify event organiser
        if ($this->sendOrderConfirmation) {
            $mailer->sendOrderNotification($this->ticketOrder);
        }

        //2 - Generate PDF Tickets
        $this->ticketOrder->generatePdfTickets();

        //3 - Send Tickets / Order confirmation
        $mailer->sendOrderConfirmation($this->ticketOrder);

        Log::info(date('d m y H:i')." - Finished Job {$this->job->getJobId()} ".__CLASS__);

        $this->delete();
    }
}
