<?php

namespace App\Http\Controllers;

use App\Models\Organiser;
use Carbon\Carbon;

class OrganiserDashboardController extends MyBaseController
{
    /**
     * Show the organiser dashboard
     *
     * @param $organiser_id
     * @return mixed
     */
    public function showDashboard($organiser_id)
    {
        $organiser = Organiser::scope()->findOrFail($organiser_id);
        $upcoming_events = $organiser->events()->where('end_date', '>=', Carbon::now())->get();

        $data = [
            'organiser'       => $organiser,
            'upcoming_events' => $upcoming_events,
        ];

        return view('ManageOrganiser.Dashboard', $data);
    }
}
