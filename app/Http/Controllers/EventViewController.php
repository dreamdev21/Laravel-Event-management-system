<?php namespace App\Http\Controllers;

use Input, View, Cookie, Mail, Validator, Response, Auth;
use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Affiliate;
use App\Models\EventStats;


class EventViewController extends Controller
{

    public function showEventHome($event_id, $slug = '', $preview = FALSE)
    {

        $event = Event::findOrFail($event_id);

        if(!Auth::check() && !$event->is_live) {
            return View::make('Public.ViewEvent.EventNotLivePage');
        }

        $data = [
            'event' => $event,
            'tickets' => $event->tickets()->orderBy('created_at', 'desc')->get(),
            'is_embedded' => 0
        ];
        /*
         * Don't record stats if we're previewing the event page from the backend or if we own the event.
         */
        if (!$preview || !Auth::check()) {

            $event_stats = new EventStats;
            $event_stats->updateViewCount($event_id);
        }

        /*
         * See if there is an affiliate referral in the URL
         */
        if ($affiliate_ref = \Input::get('ref')) {

            $affiliate_ref = preg_replace("/\W|_/", '', $affiliate_ref);

            if ($affiliate_ref) {
                $affiliate = Affiliate::firstOrNew([
                    'name' => Input::get('ref'),
                    'event_id' => $event_id,
                    'account_id' => $event->account_id,
                ]);

                ++$affiliate->visits;

                $affiliate->save();

                Cookie::queue('affiliate_' . $event_id, $affiliate_ref, 60 * 24 * 60);
            }
        }

        return View::make('Public.ViewEvent.EventPage', $data);
    }

    public function showEventHomePreview($event_id)
    {
        return showEventHome($event_id, TRUE);
    }


    public function postContactOrganiser($event_id)
    {

        $rules = [
            'name' => 'required',
            'email' => ['required', 'email'],
            'message' => ['required']
        ];

        $validator = Validator::make(Input::all(), $rules);

        if ($validator->fails()) {
            return Response::json(array(
                'status' => 'error',
                'messages' => $validator->messages()->toArray()
            ));
        }

        $event = Event::findOrFail($event_id);

        $data = [
            'sender_name' => Input::get('name'),
            'sender_email' => Input::get('email'),
            'message_content' => strip_tags(Input::get('message')),
            'event' => $event
        ];

        Mail::send('Emails.messageOrganiser', $data, function ($message) use ($event, $data) {
            $message->to($event->organiser->email, $event->organiser->name)
                ->from(OUTGOING_EMAIL_NOREPLY, $data['sender_name'])
                ->replyTo($data['sender_email'], $data['sender_name'])
                ->subject('Message Regarding: ' . $event->title);
        });

        return Response::json(array(
            'status' => 'success',
            'message' => 'Message Successfully Sent'
        ));


    }
}

