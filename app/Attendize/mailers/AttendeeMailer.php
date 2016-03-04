<?php

namespace App\Attendize\mailers;

use Mail;
use App\Models\Attendee;
use App\Models\Message;
use Carbon\Carbon;

class AttendeeMailer extends Mailer {

    public function sendMessageToAttendees(Message $message_object) {

        $event = $message_object->event;

        $attendees = ($message_object->recipients == 0)
                 ? $event->attendees // all attendees
                : Attendee::where('ticket_id', '=', $message_object->recipients)->where('account_id', '=', $message_object->account_id)->get();

        $toFields = [];
        foreach ($attendees as $attendee) {
            $toFields[$attendee->email] = $attendee->full_name;
        }

        $data = [
            'event' => $event,
            'message_content' => $message_object->message,
            'subject' => $message_object->subject
        ];

        /*
         * Mandril lets us send the email to multiple people at once.
         */
        Mail::send('Emails.messageAttendees', $data, function($message) use ($toFields, $event, $message_object) {
            $message->to($toFields)
                    ->from(config('attendize.outgoing_email_noreply'), $event->organiser->name)
                    ->replyTo($event->organiser->email, $event->organiser->name)
                    ->subject($message_object->subject);
        });



        $message_object->is_sent = 1;
        $message_object->sent_at = Carbon::now();
        $message_object->save();
    }

}
