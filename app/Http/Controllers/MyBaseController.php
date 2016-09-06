<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Organiser;
use Auth;
use JavaScript;
use View;


class MyBaseController extends Controller
{
    public function __construct()
    {
        /*
         * Set up JS across all views
         */
        JavaScript::put([
            'User'                => [
                'full_name'    => Auth::user()->full_name,
                'email'        => Auth::user()->email,
                'is_confirmed' => Auth::user()->is_confirmed,
            ],
            /*
             * @todo These should be user selectable
             */
            'DateFormat'          => 'dd-MM-yyyy',
            'DateTimeFormat'      => 'dd-MM-yyyy hh:mm',
            'GenericErrorMessage' => 'Whoops! An unknown error has occurred. Please try again or contact support if the problem persists.'
        ]);

        /*
         * Share the organizers across all views
         */
        View::share('organisers', Organiser::scope()->get());
    }

    /**
     * Returns data which is required in each view, optionally combined with additional data.
     *
     * @param int $event_id
     * @param array $additional_data
     *
     * @return arrau
     */
    public function getEventViewData($event_id, $additional_data = [])
    {
        $event = Event::scope()->findOrFail($event_id);

        $image_path = $event->organiser->full_logo_path;
        if ($event->images->first() != null) {
            $image_path = $event->images()->first()->image_path;
        }

        return array_merge([
            'event'      => $event,
            'questions'  => $event->questions()->get(),
            'image_path' => $image_path,
        ], $additional_data);
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
}
