<?php

namespace App\Commands;

use App\Attendize\mailers\AttendeeMailer;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Log;

class MessageAttendeesCommand extends Command implements ShouldQueue, SelfHandling
{
    use InteractsWithQueue,
        SerializesModels;

    public $attendeeMessage;

    public function __construct(\App\Models\Message $attendeeMessage)
    {
        $this->attendeeMessage = $attendeeMessage;
    }

    public function handle(AttendeeMailer $mailer)
    {
        Log::info(date('d m y H:i')." - Starting Job  {$this->job->getJobId()} ".__CLASS__);

        $mailer->sendMessageToAttendees($this->attendeeMessage);

        Log::info(date('d m y H:i')." - Finished Job {$this->job->getJobId()} ".__CLASS__);

        $this->delete();
    }
}
