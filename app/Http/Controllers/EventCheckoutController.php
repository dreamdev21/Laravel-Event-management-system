<?php

namespace App\Http\Controllers;

use App;
use App\Commands\OrderTicketsCommand;
use App\Models\Affiliate;
use App\Models\Attendee;
use App\Models\Event;
use App\Models\EventStats;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ReservedTickets;
use App\Models\Ticket;
use Carbon\Carbon;
use Cookie;
use DB;
use Input;
use Log;
use Omnipay;
use PDF;
use Redirect;
use Request;
use Response;
use Session;
use Validator;
use View;

class EventCheckoutController extends Controller
{
    protected $is_embedded;

    public function __construct()
    {
        /*
         * See if the checkout is being called from an embedded iframe.
         */
        $this->is_embedded = Input::get('is_embedded') == '1';
    }

    public function postValidateTickets($event_id)
    {
        /*
         * Order expires after X min
         */
        $order_expires_time = Carbon::now()->addMinutes(config('attendize.checkout_timeout_after'));

        $event = Event::findOrFail($event_id);

        $ticket_ids = Input::get('tickets');

        /*
         * Remove any tickets the user has reserved
         */
        ReservedTickets::where('session_id', '=', Session::getId())->delete();

        /*
         * Go though the selected tickets and check if they're available
         * , tot up the price and reserve them to prevent over selling.
         */

        $validation_rules = [];
        $validation_messages = [];
        $tickets = [];
        $order_total = 0;
        $total_ticket_quantity = 0;
        $booking_fee = 0;
        $organiser_booking_fee = 0;
        $quantity_available_validation_rules = [];

        foreach ($ticket_ids as $ticket_id) {
            $current_ticket_quantity = (int) Input::get('ticket_'.$ticket_id);

            if ($current_ticket_quantity < 1) {
                continue;
            }

            $total_ticket_quantity = $total_ticket_quantity + $current_ticket_quantity;

            $ticket = Ticket::find($ticket_id);

            $ticket_quantity_remaining = $ticket->quantity_remaining;

            /*
             * @todo Check max/min per person
             */
            $max_per_person = min($ticket_quantity_remaining, $ticket->max_per_person);

            $quantity_available_validation_rules['ticket_'.$ticket_id] = ['numeric', 'min:'.$ticket->min_per_person, 'max:'.$max_per_person];

            $quantity_available_validation_messages = [
                'ticket_'.$ticket_id.'.max' => 'The maximum number of tickets you can register is '.$ticket_quantity_remaining,
                'ticket_'.$ticket_id.'.min' => 'You must select at least '.$ticket->min_per_person.' tickets.',
            ];

            $validator = Validator::make(['ticket_'.$ticket_id => (int) Input::get('ticket_'.$ticket_id)], $quantity_available_validation_rules, $quantity_available_validation_messages);

            if ($validator->fails()) {
                return Response::json([
                    'status'   => 'error',
                    'messages' => $validator->messages()->toArray(),
                ]);
            }

            $order_total = $order_total + ($current_ticket_quantity * $ticket->price);
            $booking_fee = $booking_fee + ($current_ticket_quantity * $ticket->booking_fee);
            $organiser_booking_fee = $organiser_booking_fee + ($current_ticket_quantity * $ticket->organiser_booking_fee);

            $tickets[] = [
                'ticket'                => $ticket,
                'qty'                   => $current_ticket_quantity,
                'price'                 => ($current_ticket_quantity * $ticket->price),
                'booking_fee'           => ($current_ticket_quantity * $ticket->booking_fee),
                'organiser_booking_fee' => ($current_ticket_quantity * $ticket->organiser_booking_fee),
                'full_price'            => $ticket->price + $ticket->total_booking_fee,
            ];

            /*
             * Reserve the tickets in the DB
             */
            $reservedTickets = new ReservedTickets();
            $reservedTickets->ticket_id = $ticket_id;
            $reservedTickets->event_id = $event_id;
            $reservedTickets->quantity_reserved = $current_ticket_quantity;
            $reservedTickets->expires = $order_expires_time;
            $reservedTickets->session_id = Session::getId();
            $reservedTickets->save();

            if ($event->ask_for_all_attendees_info) {
                for ($i = 0; $i < $current_ticket_quantity; $i++) {
                    /*
                     * Create our validation rules here
                     */
                    $validation_rules['ticket_holder_first_name.'.$i.'.'.$ticket_id] = ['required'];
                    $validation_rules['ticket_holder_last_name.'.$i.'.'.$ticket_id] = ['required'];
                    $validation_rules['ticket_holder_email.'.$i.'.'.$ticket_id] = ['required', 'email'];

                    $validation_messages['ticket_holder_first_name.'.$i.'.'.$ticket_id.'.required'] = 'Ticket holder '.($i + 1).'\'s first name is required';
                    $validation_messages['ticket_holder_last_name.'.$i.'.'.$ticket_id.'.required'] = 'Ticket holder '.($i + 1).'\'s last name is required';
                    $validation_messages['ticket_holder_email.'.$i.'.'.$ticket_id.'.required'] = 'Ticket holder '.($i + 1).'\'s email is required';
                    $validation_messages['ticket_holder_email.'.$i.'.'.$ticket_id.'.email'] = 'Ticket holder '.($i + 1).'\'s email appears to be invalid';
                }
            }
        }

        if (empty($tickets)) {
            return Response::json([
                'status'  => 'error',
                'message' => 'No tickets selected.',
            ]);
        }

        /*
         * @todo - Store this in something other than a session?
         */
        Session::set('ticket_order_'.$event->id, [
            'validation_rules'       => $validation_rules,
            'validation_messages'    => $validation_messages,
            'event_id'               => $event->id,
            'tickets'                => $tickets, /* probably shouldn't store the whole ticket obj in session */
            'total_ticket_quantity'  => $total_ticket_quantity,
            'order_started'          => time(),
            'expires'                => $order_expires_time,
            'reserved_tickets_id'    => $reservedTickets->id,
            'order_total'            => $order_total,
            'booking_fee'            => $booking_fee,
            'organiser_booking_fee'  => $organiser_booking_fee,
            'total_booking_fee'      => $booking_fee + $organiser_booking_fee,
            'order_requires_payment' => (ceil($order_total) == 0) ? false : true,
            'account_id'             => $event->account->id,
            'affiliate_referral'     => Cookie::get('affiliate_'.$event_id),
        ]);

        if (Request::ajax()) {
            return Response::json([
                'status'      => 'success',
                'redirectUrl' => route('showEventCheckout', [
                        'event_id'    => $event_id,
                        'is_embedded' => $this->is_embedded,
                    ]).'#order_form',
            ]);
        }

        /*
         * TODO: We should just show an enable JS message here instead
         */
        return Redirect::to(route('showEventCheckout', [
                'event_id' => $event_id,
            ]).'#order_form');
    }

    public function showEventCheckout($event_id)
    {
        $order_session = Session::get('ticket_order_'.$event_id);

        if (!$order_session || $order_session['expires'] < Carbon::now()) {
            return Redirect::route('showEventPage', ['event_id' => $event_id]);
        }

        $secondsToExpire = Carbon::now()->diffInSeconds($order_session['expires']);

        //dd($secondsToExpire);
        $data = $order_session + [
                'event'           => Event::findorFail($order_session['event_id']),
                'secondsToExpire' => $secondsToExpire,
                'is_embedded'     => $this->is_embedded,
            ];

        if ($this->is_embedded) {
            return View::make('Public.ViewEvent.Embedded.EventPageCheckout', $data);
        }

        return View::make('Public.ViewEvent.EventPageCheckout', $data);
    }

    public function postCreateOrder($event_id)
    {
        $mirror_buyer_info = (Input::get('mirror_buyer_info') == 'on') ? true : false;

        $event = Event::findOrFail($event_id);

        $order = new Order();

        $ticket_order = Session::get('ticket_order_'.$event_id);

        $attendee_increment = 1;

        $validation_rules = $ticket_order['validation_rules'];
        $validation_messages = $ticket_order['validation_messages'];

        if (!$mirror_buyer_info && $event->ask_for_all_attendees_info) {
            $order->rules = $order->rules + $validation_rules;
            $order->messages = $order->messages + $validation_messages;
        }

        if (!$order->validate(Input::all())) {
            return Response::json([
                'status'   => 'error',
                'messages' => $order->errors(),
            ]);
        }

        /*
         * Begin payment attempt before creating the attendees etc.
         * */
        if ($ticket_order['order_requires_payment']) {
            try {
                $error = false;
                $token = Input::get('stripeToken');

                $gateway = Omnipay::gateway('stripe');

                $gateway->initialize([
                    'apiKey' => $event->account->stripe_api_key,
                ]);

                $transaction = $gateway->purchase([
                    'amount'      => ($ticket_order['order_total'] + $ticket_order['organiser_booking_fee']),
                    'currency'    => $event->currency->code,
                    'description' => Input::get('order_email'),
                    'description' => 'Order for customer: '.Input::get('order_email'),
                    'token'       => $token,
                ]);

                $response = $transaction->send();

                if ($response->isSuccessful()) {
                    $order->transaction_id = $response->getTransactionReference();
                } elseif ($response->isRedirect()) {
                    $response->redirect();
                } else {
                    // display error to customer
                    return Response::json([
                        'status'  => 'error',
                        'message' => $response->getMessage(),
                    ]);
                }
            } catch (\Exeption $e) {
                Log::error($e);
                $error = 'Sorry, there was an error processing your payment. Please try again.';
            }

            if ($error) {
                return Response::json([
                    'status'  => 'error',
                    'message' => $error,
                ]);
            }
        }

        /*
         * Create the order
         */
        $order->first_name = Input::get('order_first_name');
        $order->last_name = Input::get('order_last_name');
        $order->email = Input::get('order_email');
        $order->order_status_id = config('attendize.order_complete');
        $order->amount = $ticket_order['order_total'];
        $order->booking_fee = $ticket_order['booking_fee'];
        $order->organiser_booking_fee = $ticket_order['organiser_booking_fee'];
        $order->discount = 0.00;
        $order->account_id = $event->account->id;
        $order->event_id = $event_id;
        $order->save();

        /*
         * Update the event sales volume
         */
        $event->increment('sales_volume', $order->amount);
        $event->increment('organiser_fees_volume', $order->organiser_booking_fee);

        /*
         * Update affiliates stats stats
         */
        if ($ticket_order['affiliate_referral']) {
            $affiliate = Affiliate::where('name', '=', $ticket_order['affiliate_referral'])->first();
            $affiliate->increment('sales_volume', $order->amount + $order->organiser_booking_fee);
            $affiliate->increment('tickets_sold', $ticket_order['total_ticket_quantity']);
        }

        /*
         * Update the event stats
         */
        $event_stats = EventStats::firstOrNew([
            'event_id' => $event_id,
            'date'     => DB::raw('CURDATE()'),
        ]);
        $event_stats->increment('tickets_sold', $ticket_order['total_ticket_quantity']);

        if ($ticket_order['order_requires_payment']) {
            $event_stats->increment('sales_volume', $order->amount);
            $event_stats->increment('organiser_fees_volume', $order->organiser_booking_fee);
        }

        /*
         * Add the attendees
         */
        foreach ($ticket_order['tickets'] as $attendee_details) {

            /*
             * Update ticket's quantity sold
             */
            $ticket = Ticket::findOrFail($attendee_details['ticket']['id']);

            /*
             * Update some ticket info
             */
            $ticket->increment('quantity_sold', $attendee_details['qty']);
            $ticket->increment('sales_volume', ($attendee_details['ticket']['price'] * $attendee_details['qty']));
            $ticket->increment('organiser_fees_volume', ($attendee_details['ticket']['organiser_booking_fee'] * $attendee_details['qty']));

            /*
             * Insert order items (for use in generating invoices)
             */
            $orderItem = new OrderItem();
            $orderItem->title = $attendee_details['ticket']['title'];
            $orderItem->quantity = $attendee_details['qty'];
            $orderItem->order_id = $order->id;
            $orderItem->unit_price = $attendee_details['ticket']['price'];
            $orderItem->unit_booking_fee = $attendee_details['ticket']['booking_fee'] + $attendee_details['ticket']['organiser_booking_fee'];
            $orderItem->save();

            /*
             * Create the attendees
             */
            for ($i = 0; $i < $attendee_details['qty']; $i++) {
                $attendee = new Attendee();
                $attendee->first_name = $event->ask_for_all_attendees_info ? ($mirror_buyer_info ? $order->first_name : Input::get("ticket_holder_first_name.$i.{$attendee_details['ticket']['id']}")) : $order->first_name;
                $attendee->last_name = $event->ask_for_all_attendees_info ? ($mirror_buyer_info ? $order->last_name : Input::get("ticket_holder_last_name.$i.{$attendee_details['ticket']['id']}")) : $order->last_name;
                $attendee->email = $event->ask_for_all_attendees_info ? ($mirror_buyer_info ? $order->email : Input::get("ticket_holder_email.$i.{$attendee_details['ticket']['id']}")) : $order->email;
                $attendee->event_id = $event_id;
                $attendee->order_id = $order->id;
                $attendee->ticket_id = $attendee_details['ticket']['id'];
                $attendee->account_id = $event->account->id;
                $attendee->reference = $order->order_reference.'-'.($attendee_increment);
                $attendee->save();

                /*
                 * Queue an email to send to each attendee
                 */

                /* Keep track of total number of attendees */
                $attendee_increment++;
            }
        }

        /*
         * Queue up some tasks - Emails to be sents, PDFs etc.
         */
        $this->dispatch(new OrderTicketsCommand($order));

        /*
         * Release the reserved the tickets
         */
        ReservedTickets::where('session_id', '=', Session::getId())->delete();

        /*
         * Kill the session
         */
        Session::forget('ticket_order_'.$event->id);

        /*
         * Queue the PDF creation jobs
         */

        return Response::json([
            'status'      => 'success',
            'redirectUrl' => route('showOrderDetails', [
                'is_embedded'     => $this->is_embedded,
                'order_reference' => $order->order_reference,
            ]),
        ]);
    }

    /**
     * Show the order details page.
     *
     * @param string $order_reference
     *
     * @return view
     */
    public function showOrderDetails($order_reference)
    {
        $order = Order::where('order_reference', '=', $order_reference)->first();

        if (!$order) {
            App::abort(404);
        }

        $data = [
            'order'       => $order,
            'event'       => $order->event,
            'tickets'     => $order->event->tickets,
            'is_embedded' => $this->is_embedded,
        ];

        if ($this->is_embedded) {
            return View::make('Public.ViewEvent.Embedded.EventPageViewOrder', $data);
        }

        return View::make('Public.ViewEvent.EventPageViewOrder', $data);
    }

    /**
     * Output order tickets.
     *
     * @param string $order_reference
     */
    public function showOrderTickets($order_reference)
    {
        $order = Order::where('order_reference', '=', $order_reference)->first();

        if (!$order) {
            App::abort(404);
        }

        $data = [
            'order'     => $order,
            'event'     => $order->event,
            'tickets'   => $order->event->tickets,
            'attendees' => $order->attendees,
        ];

        if (Input::get('download') == '1') {
            return PDF::html('Public.ViewEvent.Partials.PDFTicket', $data, 'Tickets');
        }

        return View::make('Public.ViewEvent.Partials.PDFTicket', $data);
    }

    public function handleStripePayment()
    {
    }

    public function handlePaypalPayment()
    {
    }
}
