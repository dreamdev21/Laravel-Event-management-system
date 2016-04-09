<?php

namespace App\Http\Controllers;

use App\Models\Organiser;
use Carbon\Carbon;
use Auth;

class OrganiserViewController extends Controller
{

    /**
     * Show the public organiser page
     *
     * @param $organiser_id
     * @param string $slug
     * @param bool $preview
     * @return \Illuminate\Contracts\View\View
     */
    public function showOrganiserHome($organiser_id, $slug = '', $preview = false)
    {
        $organiser = Organiser::findOrFail($organiser_id);

        if(!$organiser->enable_organiser_page && !Auth::check()) {
            abort(404);
        }

        $upcoming_events = $organiser->events()->where('end_date', '>=', Carbon::now())->get();
        $past_events = $organiser->events()->where('end_date', '<', Carbon::now())->get();

        $data = [
            'organiser'         => $organiser,
            'tickets'           => $organiser->events()->orderBy('created_at', 'desc')->get(),
            'is_embedded'       => 0,
            'upcoming_events'   => $upcoming_events,
            'past_events'       => $past_events
        ];

        return view('Public.ViewOrganiser.OrganiserPage', $data);
    }

    /**
     * Show the backend preview of the organiser page
     *
     * @param $event_id
     * @return mixed
     */
    public function showEventHomePreview($event_id)
    {
        return showEventHome($event_id, true);
    }
}
