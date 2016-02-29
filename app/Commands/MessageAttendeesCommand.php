<?php

namespace App\Commands;

use Log;
use App\Commands\Command;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldBeQueued;
use App\Attendize\mailers\AttendeeMailer;
use Illuminate\Contracts\Bus\SelfHandling;

class MessageAttendeesCommand extends Command implements ShouldBeQueued, SelfHandling {

    use InteractsWithQueue,
        SerializesModels;

    public $attendeeMessage;


    public function __construct(\App\Models\Message $attendeeMessage) {
        $this->attendeeMessage = $attendeeMessage;
    }

    function handle(AttendeeMailer $mailer) {
        Log::info(date('d m y H:i') . " - Starting Job  {$this->job->getJobId()} ".__CLASS__);
        
        $mailer->sendMessageToAttendees($this->attendeeMessage);

        Log::info( date('d m y H:i') . " - Finished Job {$this->job->getJobId()} ".__CLASS__);

        $this->delete();
    }

}
