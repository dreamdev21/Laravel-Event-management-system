<?php

namespace App\Jobs;

use App\Jobs\Job;
use App\Mailers\AttendeeMailer;
use App\Models\Attendee;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendAttendeeTicket extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels, DispatchesJobs;

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
        $this->dispatchNow(new GenerateTicket($this->attendee->reference));
        $attendeeMailer->sendAttendeeTicket($this->attendee);
    }
}
