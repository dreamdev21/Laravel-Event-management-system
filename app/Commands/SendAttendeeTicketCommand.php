<?php

namespace App\Commands;

use App\Attendize\mailers\AttendeeMailer;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Log;
use PDF;

class SendAttendeeTicketCommand extends Command implements ShouldQueue, SelfHandling
{
    use InteractsWithQueue,
        SerializesModels;

    public $ticketOrder, $attendee;

    /**
     * OrderTicketsCommand constructor.
     *
     * @param \App\Models\Order $ticketOrder
     * @param bool|true         $sendOrderConfirmation
     */
    public function __construct(\App\Models\Attendee $attendee)
    {
        $this->ticketOrder = $attendee->order;
        $this->attendee = $attendee;
    }

    /**
     * @param AttendeeMailer $mailer
     */
    public function handle(AttendeeMailer $mailer)
    {
        Log::info(date('d m y H:i')." - Starting Job {$this->job->getJobId()} ".__CLASS__);

        //1 - Generate PDF Tickets
        $ticket_path = $this->generateAttendeeTicket();

        //2 - Send Tickets / Order confirmation
        $mailer->sendAttendeeTicket($this->attendee, $this->ticketOrder, $ticket_path);

        Log::info(date('d m y H:i')." - Finished Job {$this->job->getJobId()} ".__CLASS__);

        $this->delete();
    }


    /**
     * Create a ticket for an attendee
     *
     * @todo should the path to the PDF be stored in the DB?
     * @todo This probably shouldn't be done here
     * @return bool|string
     */
    public function generateAttendeeTicket() {
        $data = [
            'order'     => $this->ticketOrder,
            'event'     => $this->attendee->event,
            'tickets'   => $this->attendee->event->tickets,
            'attendees' => [$this->attendee],
            'css'     => file_get_contents(public_path('assets/stylesheet/ticket.css')), 
            'image'     => base64_encode(file_get_contents(public_path($this->attendee->event->organiser->full_logo_path))),
        ];

        $pdf_file_name = $this->ticketOrder->order_reference.'-'.$this->attendee->id;
        $pdf_file_path = public_path(config('attendize.event_pdf_tickets_path')).'/'.$pdf_file_name;
        $pdf_file = $pdf_file_path.'.pdf';

        if (file_exists($pdf_file)) {
            return $pdf_file;
        }

        PDF::setOutputMode('F'); // force to file
        PDF::html('Public.ViewEvent.Partials.PDFTicket', $data, $pdf_file_path);

        return $pdf_file;
    }

}
