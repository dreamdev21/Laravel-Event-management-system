<?php

namespace App\Attendize\mailers;

use App\Models\Attendee;
use App\Models\Message;
use App\Models\Order;
use Carbon\Carbon;
use Mail;

class AttendeeMailer extends Mailer
{
    /**
     * Send the attendee the ticket
     *
     * @param Attendee $attendee
     * @param Order $order
     * @param $ticket_path
     */
    public function sendAttendeeTicket(Attendee $attendee, Order $order, $ticket_path)
    {
        $this->sendTo($attendee->email, config('attendize.outgoing_email'), $order->event->organiser->name, 'Your ticket for the event '.$order->event->title, 'Emails.AttendeeTicketResend', [
            'order'      => $order,
            'email_logo' => $order->event->organiser->full_logo_path,
            'attendee'   => $attendee
        ], $ticket_path);
    }

    /**
     * Sends the attendees a message
     *
     * @param Message $message_object
     */
    public function sendMessageToAttendees(Message $message_object)
    {
        $event = $message_object->event;

        $attendees = ($message_object->recipients == 'all')
                 ? $event->attendees // all attendees
                : Attendee::where('ticket_id', '=', $message_object->recipients)->where('account_id', '=', $message_object->account_id)->get();

        foreach($attendees as $attendee) {

            $data = [
                'attendee'        => $attendee,
                'event'           => $event,
                'message_content' => $message_object->message,
                'subject'         => $message_object->subject,
                'email_logo'      => $attendee->event->organiser->full_logo_path,
            ];

            Mail::send('Emails.messageAttendees', $data, function ($message) use ($attendee, $data) {
                $message->to($attendee->email, $attendee->full_name)
                    ->from(config('attendize.outgoing_email_noreply'), $attendee->event->organiser->name)
                    ->replyTo($attendee->event->organiser->email, $attendee->event->organiser->name)
                    ->subject($data['subject']);
            });
        }

        $message_object->is_sent = 1;
        $message_object->sent_at = Carbon::now();
        $message_object->save();
    }
}
