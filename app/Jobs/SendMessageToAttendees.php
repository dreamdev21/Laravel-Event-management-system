<?php

namespace App\Jobs;

use App\Jobs\Job;
use App\Mailers\AttendeeMailer;
use App\Models\Message;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Log;

class SendMessageToAttendees extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    public $attendeeMessage;
    
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Message $attendeeMessage)
    {
        $this->attendeeMessage = $attendeeMessage;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(AttendeeMailer $attendeeMailer)
    {
        $attendeeMailer->sendMessageToAttendees($this->attendeeMessage);
    }
}
