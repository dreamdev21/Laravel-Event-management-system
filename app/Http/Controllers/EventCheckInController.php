<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendee;
use App\Models\Event;
use Carbon\Carbon;
use DB;

class EventCheckInController extends MyBaseController
{
    /**
     * Show the check-in page
     *
     * @param $event_id
     * @return \Illuminate\View\View
     */
    public function showCheckIn($event_id)
    {


        $data['event'] = Event::scope()->findOrFail($event_id);
        $data['attendees'] = $data['event']->attendees;

        
        return view('ManageEvent.CheckIn', $data);
    }

    public function showQRCodeModal(Request $request, $event_id)
    {
        return view('ManageEvent.Modals.QrcodeCheckIn');
    }

    /**
     * Search attendees
     *
     * @param Request $request
     * @param $event_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function postCheckInSearch(Request $request, $event_id)
    {
        $searchQuery = $request->get('q');

        $attendees = Attendee::scope()->withoutCancelled()
                ->join('tickets', 'tickets.id', '=', 'attendees.ticket_id')
                ->where(function ($query) use ($event_id) {
                    $query->where('attendees.event_id', '=', $event_id);
                })->where(function ($query) use ($searchQuery) {
                    $query->orWhere('attendees.first_name', 'like', $searchQuery.'%')
                    ->orWhere(DB::raw("CONCAT_WS(' ', first_name, last_name)"), 'like', $searchQuery.'%')
                    //->orWhere('attendees.email', 'like', $searchQuery . '%')
                    ->orWhere('attendees.reference', 'like', $searchQuery.'%')
                    ->orWhere('attendees.last_name', 'like', $searchQuery.'%');
                })
                ->select([
                    'attendees.id',
                    'attendees.first_name',
                    'attendees.last_name',
                    'attendees.email',
                    'attendees.reference',
                    'attendees.arrival_time',
                    'attendees.has_arrived',
                    'tickets.title as ticket',
                ])
                ->orderBy('attendees.first_name', 'ASC')
                ->get();

        return response()->json($attendees);
    }

    /**
     * Check in/out an attendee
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function postCheckInAttendee(Request $request)
    {
        $attendee_id = $request->get('attendee_id');
        $checking    = $request->get('checking');

        $attendee = Attendee::scope()->find($attendee_id);

        /*
         * Ugh
         */
        if ((($checking == 'in') && ($attendee->has_arrived == 1)) || (($checking == 'out') && ($attendee->has_arrived == 0))) {
            return response()->json([
                        'status'  => 'error',
                        'message' => 'Warning: This Attendee Has Already Been Checked '.(($checking == 'in') ? 'In (at '.$attendee->arrival_time->format('H:i A, F j').')' : 'Out').'!',
                        'checked' => $checking,
                        'id'      => $attendee->id,
            ]);
        }

        $attendee->has_arrived = ($checking == 'in') ? 1 : 0;
        $attendee->arrival_time = Carbon::now();
        $attendee->save();

        return response()->json([
                    'status'  => 'success',
                    'checked' => $checking,
                    'message' => 'Attendee Successfully Checked '.(($checking == 'in') ? 'In' : 'Out'),
                    'id'      => $attendee->id,
        ]);
    }
}
