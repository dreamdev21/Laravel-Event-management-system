<?php

namespace App\Http\Controllers;

use App\Models\Organiser;
use Carbon\Carbon;
use View;

class OrganiserDashboardController extends MyBaseController
{
    public function showDashboard($organiser_id)
    {
        $organiser = Organiser::scope()->findOrFail($organiser_id);
        $upcoming_events = $organiser->events()->where('end_date', '>=', Carbon::now())->get();

        $data = [
            'organiser'       => $organiser,
            'upcoming_events' => $upcoming_events,
            'search'          => [
                'sort_by' => 's',
                'q'       => '',
            ],
            'q' => 'dd',
        ];

        return View::make('ManageOrganiser.Dashboard', $data);
    }
}
