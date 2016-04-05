<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Ticket;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Input;
use Log;
use Response;
use View;

/*
  Attendize.com   - Event Management & Ticketing
 */

class EventTicketsController extends MyBaseController
{
    public function showTickets(Request $request, $event_id)
    {
        $allowed_sorts = [
            'created_at' => 'Creation date',
            'title' => 'Ticket title',
            'quantity_sold' => 'Quantity sold',
            'sales_volume' => 'Sales volume',
        ];

        // Getting get parameters.
        $q = $request->get('q', '');
        $sort_by = $request->get('sort_by');
        if (isset($allowed_sorts[$sort_by]) === false)
            $sort_by = 'title';

        // Find event or return 404 error.
        $event = Event::scope()->find($event_id);
        if ($event === null)
            abort(404);

        // Get tickets for event.
        $tickets = empty($q) === false
                ? $event->tickets()->where('title', 'like', '%'.$q.'%')->orderBy($sort_by, 'desc')->paginate()
                : $event->tickets()->orderBy($sort_by, 'desc')->paginate();

        // Return view.
        return view('ManageEvent.Tickets', compact('event', 'tickets', 'sort_by', 'q', 'allowed_sorts'));
    }

    public function showEditTicket($event_id, $ticket_id)
    {
        $data = [
            'event'    => Event::scope()->find($event_id),
            'ticket'   => Ticket::scope()->find($ticket_id),
        ];

        return view('ManageEvent.Modals.EditTicket', $data);
    }

    public function showCreateTicket($event_id)
    {
        return View::make('ManageEvent.Modals.CreateTicket', [
                    'event'    => Event::find($event_id),
        ]);
    }

    public function postCreateTicket($event_id)
    {
        $ticket = Ticket::createNew();

        if (!$ticket->validate(Input::all())) {
            return Response::json([
                        'status'   => 'error',
                        'messages' => $ticket->errors(),
            ]);
        }

        $ticket->event_id = $event_id;
        $ticket->title = Input::get('title');
        $ticket->quantity_available = !Input::get('quantity_available') ? null : Input::get('quantity_available');
        $ticket->start_sale_date = Input::get('start_sale_date') ? Carbon::createFromFormat('d-m-Y H:i', Input::get('start_sale_date')) : null;
        $ticket->end_sale_date = Input::get('end_sale_date') ? Carbon::createFromFormat('d-m-Y H:i', Input::get('end_sale_date')) : null;
        $ticket->price = Input::get('price');
        $ticket->min_per_person = Input::get('min_per_person');
        $ticket->max_per_person = Input::get('max_per_person');
        $ticket->description = Input::get('description');
        $ticket->save();

        \Session::flash('message', 'Successfully Created Ticket');

        return Response::json([
                    'status'      => 'success',
                    'id'          => $ticket->id,
                    'message'     => 'Refreshing...',
                    'redirectUrl' => route('showEventTickets', [
                        'event_id' => $event_id,
                    ]),
        ]);
    }

    public function postPauseTicket()
    {
        $ticket_id = Input::get('ticket_id');

        $ticket = Ticket::scope()->find($ticket_id);

        $ticket->is_paused = ($ticket->is_paused == 1) ? 0 : 1;

        if ($ticket->save()) {
            return Response::json([
                        'status'  => 'success',
                        'message' => 'Ticket Successfully Updated',
                        'id'      => $ticket->id,
            ]);
        }

        Log::error('Ticket Failed to pause/resume', [
            'ticket' => $ticket,
        ]);

        return Response::json([
                    'status'  => 'error',
                    'id'      => $ticket->id,
                    'message' => 'Whoops!, looks like something went wrong. Please try again.',
        ]);
    }

    public function postDeleteTicket()
    {
        $ticket_id = Input::get('ticket_id');

        $ticket = Ticket::scope()->find($ticket_id);

        if ($ticket->quantity_sold > 0) {
            return Response::json([
                        'status'  => 'error',
                        'message' => 'Sorry, you can\'t delete this ticket as some have already been sold',
                        'id'      => $ticket->id,
            ]);
        }

        if ($ticket->delete()) {
            return Response::json([
                        'status'  => 'success',
                        'message' => 'Ticket Successfully Deleted',
                        'id'      => $ticket->id,
            ]);
        }

        Log::error('Ticket Failed to delete', [
            'ticket' => $ticket,
        ]);

        return Response::json([
                    'status'  => 'error',
                    'id'      => $ticket->id,
                    'message' => 'Whoops!, looks like something went wrong. Please try again.',
        ]);
    }

    public function postEditTicket($event_id, $ticket_id)
    {
        $ticket = Ticket::findOrFail($ticket_id);

        /*
         * Override some vaidation rules
         */
        $validation_rules['quantity_available'] = ['integer', 'min:'.($ticket->quantity_sold + $ticket->quantity_reserved)];
        $validation_messages['quantity_available.min'] = 'Quantity available can\'t be less the amount sold or reserved.';

        $ticket->rules = $validation_rules + $ticket->rules;
        $ticket->messages = $validation_messages + $ticket->messages;

        if (!$ticket->validate(Input::all())) {
            return Response::json([
                        'status'   => 'error',
                        'messages' => $ticket->errors(),
            ]);
        }

        $ticket->title = Input::get('title');
        $ticket->quantity_available = !Input::get('quantity_available') ? null : Input::get('quantity_available');
        $ticket->price = Input::get('price');
        $ticket->start_sale_date = Input::get('start_sale_date') ? Carbon::createFromFormat('d-m-Y H:i', Input::get('start_sale_date')) : null;
        $ticket->end_sale_date = Input::get('end_sale_date') ? Carbon::createFromFormat('d-m-Y H:i', Input::get('end_sale_date')) : null;
        $ticket->description = Input::get('description');
        $ticket->min_per_person = Input::get('min_per_person');
        $ticket->max_per_person = Input::get('max_per_person');

        $ticket->save();

        return Response::json([
                    'status'      => 'success',
                    'id'          => $ticket->id,
                    'message'     => 'Refreshing...',
                    'redirectUrl' => route('showEventTickets', [
                        'event_id' => $event_id,
                    ]),
        ]);
    }
}
