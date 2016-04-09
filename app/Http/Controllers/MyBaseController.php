<?php

namespace App\Http\Controllers;

use App\Models\Event;

use App\Models\Organiser;
use View;

class MyBaseController extends Controller
{

    public function __construct()
    {
        /*
         * Share the organizers across all views
         */
        View::share('organisers', Organiser::scope()->get());
    }

    /**
     * Setup the layout used by the controller.
     *
     * @return void
     */
    protected function setupLayout()
    {
        if (!is_null($this->layout)) {
            $this->layout = View::make($this->layout);
        }
    }

    /**
     * Returns data which is required in each view, optionally combined with additional data.
     *
     * @param int   $event_id
     * @param array $additional_data
     *
     * @return arrau
     */
    public function getEventViewData($event_id, $additional_data = [])
    {
        $event = Event::scope()->findOrFail($event_id);

        return array_merge([
            'event' => $event,
            'questions' => $event->questions()->get(),
        ], $additional_data);
    }
}
