<?php

namespace App\Jobs;

use App\Models\Order;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Log;
use PDF;

class GenerateTicket extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    protected $reference;
    protected $order_reference;
    protected $attendee_reference_index;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($reference)
    {
        Log::info("Generating ticket: #" . $reference);
        $this->reference = $reference;
        $this->order_reference = explode("-", $reference)[0];
        if (strpos($reference, "-")) {
            $this->attendee_reference_index = explode("-", $reference)[1];
        }
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        $file_name = $this->reference;
        $file_path = public_path(config('attendize.event_pdf_tickets_path')) . '/' . $file_name;
        $file_with_ext = $file_path . ".pdf";

        if (file_exists($file_with_ext)) {
            Log::info("Use ticket from cache: " . $file_with_ext);
            return;
        }

        $order = Order::where('order_reference', $this->order_reference)->first();
        Log::info($order);
        $event = $order->event;

        $query = $order->attendees();
        if ($this->isAttendeeTicket()) {
            $query = $query->where('reference_index', '=', $this->attendee_reference_index);
        }
        $attendees = $query->get();

        $image_path = $event->organiser->full_logo_path;
        if ($event->images->first() != null) {
            $image_path = $event->images()->first()->image_path;
        }

        $data = [
            'order'     => $order,
            'event'     => $event,
            'attendees' => $attendees,
            'css'       => file_get_contents(public_path('assets/stylesheet/ticket.css')),
            'image'     => base64_encode(file_get_contents(public_path($image_path))),
        ];

        PDF::setOutputMode('F'); // force to file
        PDF::html('Public.ViewEvent.Partials.PDFTicket', $data, $file_path);

        Log::info("Ticket generated!");
    }

    private function isAttendeeTicket()
    {
        return ($this->attendee_reference_index != null);
    }
}
