<?php

namespace App\Jobs;

use App\Jobs\Job;
use App\Mailers\AttendeeMailer;
use App\Mailers\TicketMailer;
use App\Models\Attendee;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendAttendeeTicket extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    public $attendee;
    
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Attendee $attendee)
    {
        $this->attendee = $attendee;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(AttendeeMailer $attendeeMailer)
    {
        $attendeeMailer->sendAttendeeTicket($this->attendee);
    }
}
