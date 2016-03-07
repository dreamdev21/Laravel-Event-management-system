<?php

namespace App\Http\Controllers;

use App\Commands\OrderTicketsCommand;
use App\Models\Attendee;
use App\Models\Event;
use App\Models\EventStats;
use App\Models\Message;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Ticket;
use Auth;
use DB;
use Excel;
use Input;
use Mail;
use Response;
use Session;
use Validator;
use View;

class EventAttendeesController extends MyBaseController
{
    public function showAttendees($event_id)
    {
        $allowed_sorts = ['first_name', 'email', 'ticket_id', 'order_reference'];

        $searchQuery = Input::get('q');
        $sort_order = Input::get('sort_order') == 'asc' ? 'asc' : 'desc';
        $sort_by = (in_array(Input::get('sort_by'), $allowed_sorts) ? Input::get('sort_by') : 'created_at');

        $event = Event::scope()->find($event_id);
        //$event = Event::scope()->join('orders', 'orders.event_id', '=', 'attendees.id')->find($event_id);

        if ($searchQuery) {
            $attendees = $event->attendees()
                    ->withoutCancelled()
                    ->join('orders', 'orders.id', '=', 'attendees.order_id')
                    ->where(function ($query) use ($searchQuery) {
                        $query->where('orders.order_reference', 'like', $searchQuery.'%')
                        ->orWhere('attendees.first_name', 'like', $searchQuery.'%')
                        ->orWhere('attendees.email', 'like', $searchQuery.'%')
                        ->orWhere('attendees.last_name', 'like', $searchQuery.'%');
                    })
                    ->orderBy(($sort_by == 'order_reference' ? 'orders.' : 'attendees.').$sort_by, $sort_order)
                    ->select('attendees.*', 'orders.order_reference')
                    ->paginate();
        } else {
            $attendees = $event->attendees()
                    ->join('orders', 'orders.id', '=', 'attendees.order_id')
                    ->withoutCancelled()
                    ->orderBy(($sort_by == 'order_reference' ? 'orders.' : 'attendees.').$sort_by, $sort_order)
                    ->select('attendees.*', 'orders.order_reference')
                    ->paginate();
        }

        $data = [
            'attendees'  => $attendees,
            'event'      => $event,
            'sort_by'    => $sort_by,
            'sort_order' => $sort_order,
            'q'          => $searchQuery ? $searchQuery : '',
        ];

        return View::make('ManageEvent.Attendees', $data);
    }

    public function showCreateAttendee($event_id)
    {
        $event = Event::scope()->find($event_id);

        /*
         * If there are no tickets then we can't create an attendee
         * @todo This is a bit hackish
         */
        if ($event->tickets->count() === 0) {
            return '<script>showMessage("You need to create a ticket before you can add an attendee.");</script>';
        }

        return View::make('ManageEvent.Modals.CreateAttendee', [
                    'modal_id' => \Input::get('modal_id'),
                    'event'    => $event,
                    'tickets'  => $event->tickets()->lists('title', 'id'),
        ]);
    }

    public function postCreateAttendee($event_id)
    {
        $rules = [
            'first_name'   => 'required',
            'ticket_id'    => 'required|exists:tickets,id,account_id,'.\Auth::user()->account_id,
            'ticket_price' => 'numeric|required',
            'email'        => 'email|required',
        ];

        $messages = [
            'ticket_id.exists'   => 'The ticket you have selected does not exist',
            'ticket_id.required' => 'The ticket field is required. ',
        ];

        $validator = Validator::make(Input::all(), $rules, $messages);

        if ($validator->fails()) {
            return Response::json([
                        'status'   => 'error',
                        'messages' => $validator->messages()->toArray(),
            ]);
        }

        $ticket_id = Input::get('ticket_id');
        $ticket_price = Input::get('ticket_price');
        $attendee_first_name = Input::get('first_name');
        $attendee_last_name = Input::get('last_name');
        $attendee_email = Input::get('email');
        $email_attendee = Input::get('email_ticket');

        /*
         * Create the order
         */
        $order = new Order();
        $order->first_name = $attendee_first_name;
        $order->last_name = $attendee_last_name;
        $order->email = $attendee_email;
        $order->order_status_id = config('attendize.order_complete');
        $order->amount = $ticket_price;
        $order->account_id = Auth::user()->account_id;
        $order->event_id = $event_id;
        $order->save();

        /*
         * Update qty sold
         */
        $ticket = Ticket::scope()->find($ticket_id);
        $ticket->increment('quantity_sold');
        $ticket->increment('sales_volume', $ticket_price);
        $ticket->event->increment('sales_volume', $ticket_price);

        /*
         * Insert order item
         */
        $orderItem = new OrderItem();
        $orderItem->title = $ticket->title;
        $orderItem->quantity = 1;
        $orderItem->order_id = $order->id;
        $orderItem->unit_price = $ticket_price;
        $orderItem->save();

        /*
         * Update the event stats
         */
        $event_stats = new EventStats();
        $event_stats->updateTicketsSoldCount($event_id, 1);
        $event_stats->updateTicketRevenue($ticket_id, $ticket_price);

        /*
         * Create the attendee
         */
        $attendee = new Attendee();
        $attendee->first_name = $attendee_first_name;
        $attendee->last_name = $attendee_last_name;
        $attendee->email = $attendee_email;
        $attendee->event_id = $event_id;
        $attendee->order_id = $order->id;
        $attendee->ticket_id = $ticket_id;
        $attendee->account_id = Auth::user()->account_id;
        $attendee->reference = $order->order_reference.'-1';
        $attendee->save();

        if ($email_attendee == '1') {
            $this->dispatch(new OrderTicketsCommand($order, false));
        }

        Session::flash('message', 'Attendee Successfully Created');

        return Response::json([
                    'status'      => 'success',
                    'id'          => $attendee->id,
                    'redirectUrl' => route('showEventAttendees', [
                        'event_id' => $event_id,
                    ]),
        ]);
    }

    public function showPrintAttendees($event_id)
    {
        $data['event'] = Event::scope()->find($event_id);
        $data['attendees'] = $data['event']->attendees()->withoutCancelled()->orderBy('first_name')->get();

        return View::make('ManageEvent.PrintAttendees', $data);
    }

    public function showMessageAttendee($attendee_id)
    {
        $attendee = Attendee::scope()->findOrFail($attendee_id);

        $data = [
            'attendee' => $attendee,
            'event'    => $attendee->event,
            'modal_id' => Input::get('modal_id'),
        ];

        return View::make('ManageEvent.Modals.MessageAttendee', $data);
    }

    public function postMessageAttendee($attendee_id)
    {
        $rules = [
            'subject' => 'required',
            'message' => 'required',
        ];

        $validator = Validator::make(Input::all(), $rules);

        if ($validator->fails()) {
            return Response::json([
                        'status'   => 'error',
                        'messages' => $validator->messages()->toArray(),
            ]);
        }

        $attendee = Attendee::scope()->findOrFail($attendee_id);

        $data = [
            'attendee'        => $attendee,
            'message_content' => Input::get('message'),
            'subject'         => Input::get('subject'),
            'event'           => $attendee->event,
            'email_logo'      => $attendee->event->organiser->full_logo_path,
        ];

        Mail::send('Emails.messageAttendees', $data, function ($message) use ($attendee, $data) {
            $message->to($attendee->email, $attendee->full_name)
                    ->from(config('attendize.outgoing_email_noreply'), $attendee->event->organiser->name)
                    ->replyTo($attendee->event->organiser->email, $attendee->event->organiser->name)
                    ->subject($data['subject']);
        });

        /* Could bcc in the above? */
        if (Input::get('send_copy') == '1') {
            Mail::send('Emails.messageAttendees', $data, function ($message) use ($attendee, $data) {
                $message->to($attendee->event->organiser->email, $attendee->event->organiser->name)
                        ->from(config('attendize.outgoing_email_noreply'), $attendee->event->organiser->name)
                        ->replyTo($attendee->event->organiser->email, $attendee->event->organiser->name)
                        ->subject($data['subject'].'[ORGANISER COPY]');
            });
        }

        return Response::json([
                    'status'  => 'success',
                    'message' => 'Message Successfully Sent',
        ]);
    }

    public function showMessageAttendees($event_id)
    {
        $data = [
            'event'    => Event::scope()->find($event_id),
            'modal_id' => Input::get('modal_id'),
            'tickets'  => Event::scope()->find($event_id)->tickets()->lists('title', 'id')->toArray(),
        ];

        return View::make('ManageEvent.Modals.MessageAttendees', $data);
    }

    public function postMessageAttendees($event_id)
    {
        $rules = [
            'subject'    => 'required',
            'message'    => 'required',
            'recipients' => 'required',
        ];

        $validator = Validator::make(Input::all(), $rules);

        if ($validator->fails()) {
            return Response::json([
                        'status'   => 'error',
                        'messages' => $validator->messages()->toArray(),
            ]);
        }

        $message = Message::createNew();
        $message->message = Input::get('message');
        $message->subject = Input::get('subject');
        $message->recipients = Input::get('recipients');
        $message->event_id = $event_id;
        $message->save();

        /*
         * Add to the queue
         */

        return Response::json([
                    'status'  => 'success',
                    'message' => 'Message Successfully Sent',
        ]);
    }

    public function showExportAttendees($event_id, $export_as = 'xls')
    {
        Excel::create('attendees-as-of-'.date('d-m-Y-g.i.a'), function ($excel) use ($event_id) {

            $excel->setTitle('Attendees List');

            // Chain the setters
            $excel->setCreator(config('attendize.app_name'))
                    ->setCompany(config('attendize.app_name'));

            $excel->sheet('attendees_sheet_1', function ($sheet) use ($event_id) {

                DB::connection()->setFetchMode(\PDO::FETCH_ASSOC);
                $data = DB::table('attendees')
                    ->where('attendees.event_id', '=', $event_id)
                    ->where('attendees.is_cancelled', '=', 0)
                                ->where('attendees.account_id', '=', Auth::user()->account_id)
                                ->join('events', 'events.id', '=', 'attendees.event_id')
                                ->join('orders', 'orders.id', '=', 'attendees.order_id')
                                ->join('tickets', 'tickets.id', '=', 'attendees.ticket_id')
                                ->select(
                                        'attendees.first_name', 'attendees.last_name', 'attendees.email', 'attendees.reference', 'orders.order_reference', 'tickets.title', 'orders.created_at', DB::raw("(CASE WHEN attendees.has_arrived = 1 THEN 'YES' ELSE 'NO' END) AS `attendees.has_arrived`"), 'attendees.arrival_time')->get();
                //DB::raw("(CASE WHEN UNIX_TIMESTAMP(`attendees.arrival_time`) = 0 THEN '---' ELSE 'd' END) AS `attendees.arrival_time`"))

                $sheet->fromArray($data);

                $sheet->row(1, [
                    'First Name', 'Last Name', 'Email', 'Ticket Reference', 'Order Reference', 'Ticket Type', 'Purchase Date', 'Has Arrived', 'Arrival Time',
                ]);

                // Set gray background on first row
                $sheet->row(1, function ($row) {
                    $row->setBackground('#f5f5f5');
                });
            });
        })->export($export_as);
    }

    public function showEditAttendee($event_id, $attendee_id)
    {
        $attendee = Attendee::scope()->findOrFail($attendee_id);

        $data = [
            'attendee' => $attendee,
            'event'    => $attendee->event,
            'tickets'  => $attendee->event->tickets->lists('title', 'id'),
            'modal_id' => Input::get('modal_id'),
        ];

        return View::make('ManageEvent.Modals.EditAttendee', $data);
    }

    public function postEditAttendee($event_id, $attendee_id)
    {
        $rules = [
            'first_name' => 'required',
            'ticket_id'  => 'required|exists:tickets,id,account_id,'.Auth::user()->account_id,
            'email'      => 'required|email',
        ];

        $messages = [
            'ticket_id.exists'   => 'The ticket you have selected does not exist',
            'ticket_id.required' => 'The ticket field is required. ',
        ];

        $validator = Validator::make(Input::all(), $rules, $messages);

        if ($validator->fails()) {
            return Response::json([
                        'status'   => 'error',
                        'messages' => $validator->messages()->toArray(),
            ]);
        }

        $attendee = Attendee::scope()->findOrFail($attendee_id);

        $attendee->first_name = Input::get('first_name');
        $attendee->last_name = Input::get('last_name');
        $attendee->email = Input::get('email');
        $attendee->ticket_id = Input::get('ticket_id');
        $attendee->save();

        return Response::json([
                    'status'      => 'success',
                    'id'          => $attendee->id,
                    'message'     => 'Refreshing...',
                    'redirectUrl' => '',
        ]);
    }

    public function showCancelAttendee($event_id, $attendee_id)
    {
        $attendee = Attendee::scope()->findOrFail($attendee_id);

        $data = [
            'attendee' => $attendee,
            'event'    => $attendee->event,
            'tickets'  => $attendee->event->tickets->lists('title', 'id'),
            'modal_id' => Input::get('modal_id'),
        ];

        return View::make('ManageEvent.Modals.CancelAttendee', $data);
    }

    public function postCancelAttendee($event_id, $attendee_id)
    {
        $attendee = Attendee::scope()->findOrFail($attendee_id);

        if ($attendee->is_cancelled) {
            return Response::json([
                'status'      => 'success',
                'message'     => 'Attendee Already Cancelled',
            ]);
        }

        $attendee->ticket->decrement('quantity_sold');
        $attendee->is_cancelled = 1;
        $attendee->save();

        $data = [
            'attendee'   => $attendee,
            'email_logo' => $attendee->event->organiser->full_logo_path,
        ];

        if (Input::get('notify_attendee') == '1') {
            Mail::send('Emails.notifyCancelledAttendee', $data, function ($message) use ($attendee) {
                $message->to($attendee->email, $attendee->full_name)
                        ->from(config('attendize.outgoing_email_noreply'), $attendee->event->organiser->name)
                        ->replyTo($attendee->event->organiser->email, $attendee->event->organiser->name)
                        ->subject('You\'re ticket has been cancelled');
            });
        }

        \Session::flash('message', 'Successfully Cancelled Attenddee');

        return Response::json([
                    'status'      => 'success',
                    'id'          => $attendee->id,
                    'message'     => 'Refreshing...',
                    'redirectUrl' => '',
        ]);
    }
}
