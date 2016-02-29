<?php namespace App\Attendize\mailers;

use Mail;

class Mailer
{

    public function sendTo($toEmail, $fromEmail, $fromName, $subject, $view, $data = [], $attachment = FALSE)
    {
        Mail::send($view, $data, function ($message) use ($toEmail, $fromEmail, $fromName, $subject, $attachment) {
            $replyEmail = $fromEmail;
            $fromEmail = OUTGOING_EMAIL;

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