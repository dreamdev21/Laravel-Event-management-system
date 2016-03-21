<?php

namespace App\Http\Controllers;

use App\Models\Organiser;
use View;
use Carbon\Carbon;

class OrganiserViewController extends Controller
{
    public function showOrganiserHome($organiser_id, $slug = '', $preview = false)
    {
        $organiser = Organiser::findOrFail($organiser_id);

        $upcoming_events = $organiser->events()->where('end_date', '>=', Carbon::now())->get();
        $past_events = $organiser->events()->where('end_date', '<', Carbon::now())->get();

        $data = [
            'organiser'         => $organiser,
            'tickets'           => $organiser->events()->orderBy('created_at', 'desc')->get(),
            'is_embedded'       => 0,
            'upcoming_events'   => $upcoming_events,
            'past_events'       => $past_events
        ];

        return View::make('Public.ViewOrganiser.OrganiserPage', $data);
    }

    public function showEventHomePreview($event_id)
    {
        return showEventHome($event_id, true);
    }
}
