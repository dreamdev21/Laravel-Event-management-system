<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Organiser;
use Input;
use View;

class OrganiserEventsController extends MyBaseController
{
    public function showEvents($organiser_id)
    {
        $organiser = Organiser::scope()->findOrfail($organiser_id);

        $allowed_sorts = ['created_at', 'start_date', 'end_date', 'title'];

        $searchQuery = Input::get('q');
        $sort_by = (in_array(Input::get('sort_by'), $allowed_sorts) ? Input::get('sort_by') : 'start_date');

        $events = $searchQuery
            ? Event::scope()->where('title', 'like', '%'.$searchQuery.'%')->orderBy($sort_by, 'desc')->where('organiser_id', '=', $organiser_id)->paginate(12)
            : Event::scope()->where('organiser_id', '=', $organiser_id)->orderBy($sort_by, 'desc')->paginate(12);

        $data = [
            'events'    => $events,
            'organiser' => $organiser,
            'search'    => [
                'q'        => $searchQuery ? $searchQuery : '',
                'sort_by'  => Input::get('sort_by') ? Input::get('sort_by') : '',
                'showPast' => Input::get('past'),
            ],
        ];

        return View::make('ManageOrganiser.Events', $data);
    }

    
}
