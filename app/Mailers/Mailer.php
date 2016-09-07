<?php

namespace App\Mailers;

use Mail;

class Mailer
{
    public function sendTo($toEmail, $fromEmail, $fromName, $subject, $view, $data = [], $attachment = false)
    {
        Mail::send($view, $data, function ($message) use ($toEmail, $fromEmail, $fromName, $subject, $attachment) {
            $replyEmail = $fromEmail;
            $fromEmail = config('attendize.outgoing_email');
            if ($attachment) {
                $message->attach($attachment);
            }
            $message
                ->to($toEmail)
                ->from($fromEmail, $fromName)
                ->replyTo($replyEmail, $fromName)
                ->subject($subject);
        });
    }
}