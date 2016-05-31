<?php

namespace App\Mailers;

use Mail;
use Log;

class TicketMailer
{

  public static function sendOrderTickets($order) {

    Log::info("Sending ticket to: ".$order->email);

    $data = [
      'order' => $order,
    ];

    Mail::queue('Mailers.TicketMailer.SendOrderTickets', $data, function($message) use ($order) {
      $message->to($order->email);
      $message->subject('Your tickets for the event '.$order->event->title);

      $file_name = $order->order_reference;
      $file_path = public_path(config('attendize.event_pdf_tickets_path')).'/'.$file_name.'.pdf';

      $message->attach($file_path);
    });

  }

  public static function sendAttendeeTicket($attendee) {

    Log::info("Sending ticket to: ".$attendee->email);

    $data = [
      'attendee' => $attendee,
    ];

    Mail::queue('Mailers.TicketMailer.SendAttendeeTicket', $data, function($message) use ($attendee) {
      $message->to($attendee->email);
      $message->subject('Your ticket for the event '.$attendee->order->event->title);

      $file_name = $attendee->getReferenceAttribute();
      $file_path = public_path(config('attendize.event_pdf_tickets_path')).'/'.$file_name.'.pdf';

      $message->attach($file_path);
    });

  }

  public static function sendAttendeeInvite($attendee) {

    Log::info("Sending invite to: ".$attendee->email);

    $data = [
      'attendee' => $attendee,
    ];

    Mail::queue('Mailers.TicketMailer.SendAttendeeInvite', $data, function($message) use ($attendee) {
      $message->to($attendee->email);
      $message->subject('Your ticket for the event '.$attendee->order->event->title);

      $file_name = $attendee->getReferenceAttribute();
      $file_path = public_path(config('attendize.event_pdf_tickets_path')).'/'.$file_name.'.pdf';

      $message->attach($file_path);
    });

  }

}
