<?php namespace App\Http\Controllers;

use Carbon\Carbon;
use Input, View, Response, Log;
use App\Models\Event;
use App\Models\Ticket;

/*
  Attendize.com   - Event Management & Ticketing
 */

class EventTicketsController extends MyBaseController {

    public function showTickets($event_id) {

        $allowed_sorts = ['created_at', 'quantity_sold', 'sales_volume', 'title'];

        $searchQuery = Input::get('q');
        $sort_by = (in_array(Input::get('sort_by'), $allowed_sorts) ? Input::get('sort_by') : 'created_at');
       

        $event = Event::scope()->findOrFail($event_id);

        $tickets = $searchQuery 
                ? $event->tickets()->where('title', 'like', '%' . $searchQuery . '%')->orderBy($sort_by, 'desc')->paginate(10) 
                : $event->tickets()->orderBy($sort_by, 'desc')->paginate(10);


        $data = [
            'event' => $event,
            'tickets' => $tickets,
            'sort_by' => $sort_by,
            'q' => $searchQuery ? $searchQuery : ''
        ];

        return View::make('ManageEvent.Tickets', $data);
    }

    public function showEditTicket($event_id, $ticket_id) {

        $data = [
            'event' => Event::scope()->find($event_id),
            'ticket' => Ticket::scope()->find($ticket_id),
            'modal_id' => Input::get('modal_id'),
        ];

        return View::make('ManageEvent.Modals.EditTicket', $data);
    }

    public function showCreateTicket($event_id) {
        return View::make('ManageEvent.Modals.CreateTicket', array(
                    'modal_id' => Input::get('modal_id'),
                    'event' => Event::find($event_id)
        ));
    }

    public function postCreateTicket($event_id) {

        $ticket = Ticket::createNew();

        if (!$ticket->validate(Input::all())) {

            return Response::json(array(
                        'status' => 'error',
                        'messages' => $ticket->errors()
            ));
        }

        $ticket->event_id = $event_id;
        $ticket->title = Input::get('title');
        $ticket->quantity_available = !Input::get('quantity_available') ? NULL : Input::get('quantity_available');
        $ticket->start_sale_date = Input::get('start_sale_date') ? Carbon::createFromFormat('d-m-Y H:i', Input::get('start_sale_date')) : NULL;
        $ticket->end_sale_date = Input::get('end_sale_date') ? Carbon::createFromFormat('d-m-Y H:i', Input::get('end_sale_date')) : NULL;
        $ticket->price = Input::get('price');
        $ticket->min_per_person = Input::get('min_per_person');
        $ticket->max_per_person = Input::get('max_per_person');
        $ticket->description = Input::get('description');
        $ticket->save();

        \Session::flash('message', 'Successfully Created Ticket');

        return Response::json(array(
                    'status' => 'success',
                    'id' => $ticket->id,
                    'message' => 'Refreshing...',
                    'redirectUrl' => route('showEventTickets', array(
                        'event_id' => $event_id
                    ))
        ));
    }
    
    public function postPauseTicket() {
        $ticket_id = Input::get('ticket_id');

        $ticket = Ticket::scope()->find($ticket_id);
        
        $ticket->is_paused = ($ticket->is_paused == 1) ? 0 : 1;
        
        if ($ticket->save()) {
            return Response::json([
                        'status' => 'success',
                        'message' => 'Ticket Successfully Updated',
                        'id' => $ticket->id
            ]);
        }

        Log::error('Ticket Failed to pause/resume', [
            'ticket' => $ticket
        ]);

        return Response::json([
                    'status' => 'error',
                    'id' => $ticket->id,
                    'message' => 'Whoops!, looks like something went wrong. Please try again.'
        ]);
        
    }
    
    public function postDeleteTicket() {

        $ticket_id = Input::get('ticket_id');

        $ticket = Ticket::scope()->find($ticket_id);

        if ($ticket->quantity_sold > 0) {
            return Response::json([
                        'status' => 'error',
                        'message' => 'Sorry, you can\'t delete this ticket as some have already been sold',
                        'id' => $ticket->id
            ]);
        }

        if ($ticket->delete()) {
            return Response::json([
                        'status' => 'success',
                        'message' => 'Ticket Successfully Deleted',
                        'id' => $ticket->id
            ]);
        }

        Log::error('Ticket Failed to delete', [
            'ticket' => $ticket
        ]);

        return Response::json([
                    'status' => 'error',
                    'id' => $ticket->id,
                    'message' => 'Whoops!, looks like something went wrong. Please try again.'
        ]);
    }

    public function postEditTicket($event_id, $ticket_id) {

        $ticket = Ticket::findOrFail($ticket_id);

        /*
         * Override some vaidation rules
         */
        $validation_rules['quantity_available'] = ['integer','min:'.($ticket->quantity_sold + $ticket->quantity_reserved)];
        $validation_messages['quantity_available.min'] = 'Quantity available can\'t be less the amount sold or reserved.';
        
        $ticket->rules = $validation_rules + $ticket->rules;
        $ticket->messages = $validation_messages + $ticket->messages;
        
        
        if (!$ticket->validate(Input::all())) {

            return Response::json(array(
                        'status' => 'error',
                        'messages' => $ticket->errors()
            ));
        }

        $ticket->title = Input::get('title');
        $ticket->quantity_available = !Input::get('quantity_available') ? NULL : Input::get('quantity_available');
        $ticket->price = Input::get('price');
        $ticket->start_sale_date = Input::get('start_sale_date') ? Carbon::createFromFormat('d-m-Y H:i', Input::get('start_sale_date')) : NULL;
        $ticket->end_sale_date = Input::get('end_sale_date') ? Carbon::createFromFormat('d-m-Y H:i', Input::get('end_sale_date')) : NULL;
        $ticket->description = Input::get('description');
        $ticket->min_per_person = Input::get('min_per_person');
        $ticket->max_per_person = Input::get('max_per_person');


        $ticket->save();

        return Response::json(array(
                    'status' => 'success',
                    'id' => $ticket->id,
                    'message' => 'Refreshing...',
                    'redirectUrl' => route('showEventTickets', array(
                        'event_id' => $event_id
                    ))
        ));
    }

}
