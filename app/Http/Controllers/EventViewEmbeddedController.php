<?php namespace App\Http\Controllers;

use View;
use App\Http\Controllers\Controller;
use App\Models\Event;


class EventViewEmbeddedController extends Controller {

    public function showEmbeddedEvent($event_id) {

        $event = Event::findOrFail($event_id);

        $data = [
            'event' => $event,
            'tickets' => $event->tickets()->orderBy('created_at', 'desc')->get(),
            'is_embedded' => '1'
        ];

        return View::make('Public.ViewEvent.Embedded.EventPage', $data);
    }

}
