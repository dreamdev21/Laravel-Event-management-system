<?php

namespace App\Http\Controllers;

class EventPromoteController extends MyBaseController
{
    /**
     * @param $event_id
     * @return mixed
     */
    public function showPromote($event_id)
    {
        $data = [
            'event' => Event::scope()->find($event_id),
        ];

        return view('ManageEvent.Promote', $data);
    }
}
