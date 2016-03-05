<?php

namespace App\Http\Controllers;

use App\Models\Organiser;
use View;

class OrganiserViewController extends Controller
{
    public function showOrganiserHome($organiser_id, $slug = '', $preview = false)
    {
        $organiser = Organiser::findOrFail($organiser_id);

        $data = [
            'organiser'   => $organiser,
            'tickets'     => $organiser->events()->orderBy('created_at', 'desc')->get(),
            'is_embedded' => 0,
        ];

        return View::make('Public.ViewOrganiser.OrganiserPage', $data);
    }

    public function showEventHomePreview($event_id)
    {
        return showEventHome($event_id, true);
    }
}
