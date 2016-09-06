<?php

namespace App\Http\Controllers;

use App\Models\Attendee;
use App\Models\Event;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use JavaScript;

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

        $event = Event::scope()->findOrFail($event_id);

        $data = [
            'event'     => $event,
            'attendees' => $event->attendees
        ];

        JavaScript::put([
            'qrcodeCheckInRoute' => route('postQRCodeCheckInAttendee', ['event_id' => $event->id]),
            'checkInRoute'       => route('postCheckInAttendee', ['event_id' => $event->id]),
            'checkInSearchRoute' => route('postCheckInSearch', ['event_id' => $event->id]),
        ]);

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
            ->join('orders', 'orders.id', '=', 'attendees.order_id')
            ->where(function ($query) use ($event_id) {
                $query->where('attendees.event_id', '=', $event_id);
            })->where(function ($query) use ($searchQuery) {
                $query->orWhere('attendees.first_name', 'like', $searchQuery . '%')
                    ->orWhere(DB::raw("CONCAT_WS(' ', attendees.first_name, attendees.last_name)"), 'like',
                        $searchQuery . '%')
                    //->orWhere('attendees.email', 'like', $searchQuery . '%')
                    ->orWhere('orders.order_reference', 'like', $searchQuery . '%')
                    ->orWhere('attendees.last_name', 'like', $searchQuery . '%');
            })
            ->select([
                'attendees.id',
                'attendees.first_name',
                'attendees.last_name',
                'attendees.email',
                'attendees.arrival_time',
                'attendees.reference_index',
                'attendees.has_arrived',
                'tickets.title as ticket',
                'orders.order_reference',
                'orders.is_payment_received'
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
        $checking = $request->get('checking');

        $attendee = Attendee::scope()->find($attendee_id);

        /*
         * Ugh
         */
        if ((($checking == 'in') && ($attendee->has_arrived == 1)) || (($checking == 'out') && ($attendee->has_arrived == 0))) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Attendee Already Checked ' . (($checking == 'in') ? 'In (at ' . $attendee->arrival_time->format('H:i A, F j') . ')' : 'Out') . '!',
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
            'message' => 'Attendee Successfully Checked ' . (($checking == 'in') ? 'In' : 'Out'),
            'id'      => $attendee->id,
        ]);
    }


    /**
     * Check in an attendee
     *
     * @param $event_id
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function postCheckInAttendeeQr($event_id, Request $request)
    {
        $event = Event::scope()->findOrFail($event_id);

        $qrcodeToken = $request->get('attendee_reference');
        $attendee = Attendee::scope()->withoutCancelled()
            ->join('tickets', 'tickets.id', '=', 'attendees.ticket_id')
            ->where(function ($query) use ($event, $qrcodeToken) {
                $query->where('attendees.event_id', $event->id)
                    ->where('attendees.private_reference_number', $qrcodeToken);
            })->select([
                'attendees.id',
                'attendees.order_id',
                'attendees.first_name',
                'attendees.last_name',
                'attendees.email',
                'attendees.reference_index',
                'attendees.arrival_time',
                'attendees.has_arrived',
                'tickets.title as ticket',
            ])->first();

        if (is_null($attendee)) {
            return response()->json([
                'status'  => 'error',
                'message' => "Invalid Ticket! Please try again."
            ]);
        }

        $relatedAttendesCount = Attendee::where('id', '!=', $attendee->id)
            ->where([
                'order_id'    => $attendee->order_id,
                'has_arrived' => false
            ])->count();

        if ($relatedAttendesCount >= 1) {
            $confirmOrderTicketsRoute = route('confirmCheckInOrderTickets', [$event->id, $attendee->order_id]);

            /*
             * @todo Incorporate this feature into the new design
             */
            //$appendedText = '<br><br><form class="ajax" action="' . $confirmOrderTicketsRoute . '" method="POST">' . csrf_field() . '<button class="btn btn-primary btn-sm" type="submit"><i class="ico-ticket"></i> Check in all tickets associated to this order</button></form>';
        } else {
            $appendedText = '';
        }

        if ($attendee->has_arrived) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Attendee already checked in at ' . $attendee->arrival_time->format('H:i A, F j') . $appendedText
            ]);
        }

        Attendee::find($attendee->id)->update(['has_arrived' => true, 'arrival_time' => Carbon::now()]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Success !<br>Name: ' . $attendee->first_name . ' ' . $attendee->last_name . '<br>Reference: ' . $attendee->reference . '<br>Ticket: ' . $attendee->ticket . '.' . $appendedText
        ]);
    }

    /**
     * Confirm tickets of same order.
     *
     * @param $event_id
     * @param $order_id
     * @return \Illuminate\Http\Response
     */
    public function confirmOrderTicketsQr($event_id, $order_id)
    {
        $updateRowsCount = Attendee::scope()->where([
            'event_id'    => $event_id,
            'order_id'    => $order_id,
            'has_arrived' => 0,
        ])->update([
            'has_arrived'  => 1,
            'arrival_time' => Carbon::now(),
        ]);

        return response()->json([
            'message' => $updateRowsCount . ' Attendee(s) Checked in.'
        ]);
    }

}
