<?php

namespace App\Http\Controllers;

use App\Models\Attendee;
use App\Models\Event;
use App\Models\Order;
use DB;
use Excel;
use Input;
use Log;
use Mail;
use Omnipay;
use Response;
use Validator;
use View;

class EventOrdersController extends MyBaseController
{
    public function showOrders($event_id = '')
    {
        $allowed_sorts = ['first_name', 'email', 'order_reference', 'order_status_id', 'created_at'];

        $searchQuery = Input::get('q');
        $sort_by = (in_array(Input::get('sort_by'), $allowed_sorts) ? Input::get('sort_by') : 'created_at');
        $sort_order = Input::get('sort_order') == 'asc' ? 'asc' : 'desc';

        $event = Event::scope()->find($event_id);

        if ($searchQuery) {

            /*
             * Strip the hash from the start of the search term in case people search for
             * order references like '#EDGC67'
             */
            if ($searchQuery[0] === '#') {
                $searchQuery = str_replace('#', '', $searchQuery);
            }

            $orders = $event->orders()
                ->where(function ($query) use ($searchQuery) {
                    $query->where('order_reference', 'like', $searchQuery.'%')
                        ->orWhere('first_name', 'like', $searchQuery.'%')
                        ->orWhere('email', 'like', $searchQuery.'%')
                        ->orWhere('last_name', 'like', $searchQuery.'%');
                })
                ->orderBy($sort_by, $sort_order)
                ->paginate();
        } else {
            $orders = $event->orders()->orderBy($sort_by, $sort_order)->paginate();
        }

        $data = [
            'orders'     => $orders,
            'event'      => $event,
            'sort_by'    => $sort_by,
            'sort_order' => $sort_order,
            'q'          => $searchQuery ? $searchQuery : '',
        ];

        return View::make('ManageEvent.Orders', $data);
    }

    public function manageOrder($order_id)
    {
        $data = [
            'order'    => Order::scope()->find($order_id),
            'modal_id' => Input::get('modal_id'),
        ];

        return View::make('ManageEvent.Modals.ManageOrder', $data);
    }

    public function showCancelOrder($order_id)
    {
        $order = Order::scope()->find($order_id);

        $data = [
            'order'     => $order,
            'event'     => $order->event(),
            'attendees' => $order->attendees()->withoutCancelled()->get(),
            'modal_id'  => Input::get('modal_id'),
        ];

        return View::make('ManageEvent.Modals.CancelOrder', $data);
    }

    /**
     * @param $order_id
     *
     * @return mixed
     */
    public function postCancelOrder($order_id)
    {
        $rules = [
            'refund_amount' => ['numeric'],
        ];
        $messages = [
            'refund_amount.integer' => 'Refund amount must only contain numbers.',
        ];

        $validator = Validator::make(Input::all(), $rules, $messages);

        if ($validator->fails()) {
            return Response::json([
                'status'   => 'error',
                'messages' => $validator->messages()->toArray(),
            ]);
        }

        $order = Order::scope()->findOrFail($order_id);
        $refund_order = (Input::get('refund_order') === 'on') ? true : false;
        $refund_type = Input::get('refund_type');
        $refund_amount = Input::get('refund_amount');
        $attendees = Input::get('attendees');
        $error_message = false;

        if ($refund_order) {
            if (!$order->transaction_id) {
                $error_message = 'Sorry, this order cannot be refunded.';
            }

            if ($order->is_refunded) {
                $error_message = 'This order has already been refunded';
            } elseif ($order->organiser_amount == 0) {
                $error_message = 'Nothing to refund';
            } elseif ($refund_amount > ($order->organiser_amount - $order->amount_refunded)) {
                $error_message = 'The maximum amount you can refund is '.(money($order->organiser_amount - $order->amount_refunded, $order->event->currency->code));
            }
            if (!$error_message) {
                // @todo - remove the code repetition here
                try {
                    $gateway = Omnipay::gateway('stripe');

                    $gateway->initialize([
                        'apiKey' => $order->account->stripe_api_key,
                    ]);

                    if ($refund_type === 'full') { /* Full refund */

                        $refund_amount = $order->organiser_amount - $order->amount_refunded;

                        $request = $gateway->refund([
                            'transactionReference' => $order->transaction_id,
                            'amount'               => $refund_amount,
                            'refundApplicationFee' => floatval($order->booking_fee) > 0 ? true : false,
                        ]);

                        $response = $request->send();

                        if ($response->isSuccessful()) {
                            /* Update the event sales volume*/
                            $order->event->decrement('sales_volume', $refund_amount);

                            $order->is_refunded = 1;
                            $order->amount_refunded = $order->organiser_amount;
                            $order->order_status_id = config('attendize.order_refunded');
                        } else {
                            $error_message = $response->getMessage();
                        }
                    } else { /* Partial refund */

                        $refund = $gateway->refund([
                            'transactionReference' => $order->transaction_id,
                            'amount'               => floatval($refund_amount),
                            'refundApplicationFee' => floatval($order->booking_fee) > 0 ? true : false,
                        ]);

                        $response = $refund->send();

                        if ($response->isSuccessful()) {
                            /* Update the event sales volume*/
                            $order->event->decrement('sales_volume', $refund_amount);

                            $order->order_status_id = config('attendize.order_partially_refunded');

                            if (($order->organiser_amount - $order->amount_refunded) == 0) {
                                $order->is_refunded = 1;
                                $order->order_status_id = config('attendize.order_refunded');
                            }

                            $order->is_partially_refunded = 1;
                        } else {
                            $error_message = $response->getMessage();
                        }
                    }
                    $order->amount_refunded = round(($order->amount_refunded + $refund_amount), 2);
                    $order->save();
                } catch (\Exeption $e) {
                    Log::error($e);
                    $error_message = 'There has been a problem processing your refund. Please check your information and try again.';
                }
            }

            if ($error_message) {
                return Response::json([
                    'status'  => 'success',
                    'message' => $error_message,
                ]);
            }
        }

        /*
         * Cancel the attendees
         */
        if ($attendees) {
            foreach ($attendees as $attendee_id) {
                $attendee = Attendee::scope()->where('id', '=', $attendee_id)->first();
                $attendee->is_cancelled = 1;
                $attendee->save();
            }
        }

        \Session::flash('message',
            (!$refund_amount && !$attendees) ? 'Nothing To Do' : 'Successfully '.($refund_order ? ' Refunded Order' : ' ').($attendees && $refund_order ? ' & ' : '').($attendees ? 'Cancelled Attendee(s)' : ''));

        return Response::json([
            'status'      => 'success',
            'redirectUrl' => '',
        ]);
    }

    /**
     * @param $event_id
     * @param string $export_as Accepted: xls, xlsx, csv, pdf, html
     */
    public function showExportOrders($event_id, $export_as = 'xls')
    {
        $event = Event::scope()->findOrFail($event_id);

        Excel::create('orders-as-of-'.date('d-m-Y-g.i.a'), function ($excel) use ($event) {

            $excel->setTitle('Orders For Event: '.$event->title);

            // Chain the setters
            $excel->setCreator(config('attendize.app_name'))
                ->setCompany(config('attendize.app_name'));

            $excel->sheet('orders_sheet_1', function ($sheet) use ($event) {

                \DB::connection()->setFetchMode(\PDO::FETCH_ASSOC);
                $data = DB::table('orders')
                    ->where('orders.event_id', '=', $event->id)
                    ->where('orders.event_id', '=', $event->id)
                    ->select([
                        'orders.first_name',
                        'orders.last_name',
                        'orders.email',
                        'orders.order_reference',
                        'orders.amount',
                        \DB::raw("(CASE WHEN orders.is_refunded = 1 THEN 'YES' ELSE 'NO' END) AS `orders.is_refunded`"),
                        \DB::raw("(CASE WHEN orders.is_partially_refunded = 1 THEN 'YES' ELSE 'NO' END) AS `orders.is_partially_refunded`"),
                        'orders.amount_refunded',
                        'orders.created_at',
                    ])->get();
                //DB::raw("(CASE WHEN UNIX_TIMESTAMP(`attendees.arrival_time`) = 0 THEN '---' ELSE 'd' END) AS `attendees.arrival_time`"))

                $sheet->fromArray($data);

                $sheet->row(1, [
                    'First Name', 'Last Name', 'Email', 'Order Reference', 'Amount', 'Fully Refunded', 'Partially Refunded', 'Amount Refunded', 'Order Date',
                ]);

                // Set gray background on first row
                $sheet->row(1, function ($row) {
                    $row->setBackground('#f5f5f5');
                });
            });
        })->export($export_as);
    }

    public function showMessageOrder($order_id)
    {
        $order = Order::scope()->findOrFail($order_id);

        $data = [
            'order'    => $order,
            'event'    => $order->event,
            'modal_id' => Input::get('modal_id'),
        ];

        return View::make('ManageEvent.Modals.MessageOrder', $data);
    }

    public function postMessageOrder($order_id)
    {
        $rules = [
            'subject' => 'required|max:250',
            'message' => 'required|max:5000',
        ];

        $validator = Validator::make(Input::all(), $rules);

        if ($validator->fails()) {
            return Response::json([
                'status'   => 'error',
                'messages' => $validator->messages()->toArray(),
            ]);
        }

        $order = Attendee::scope()->findOrFail($order_id);

        $data = [
            'order'           => $order,
            'message_content' => Input::get('message'),
            'subject'         => Input::get('subject'),
            'event'           => $order->event,
            'email_logo'      => $order->event->organiser->full_logo_path,
        ];

        Mail::send('Emails.messageOrder', $data, function ($message) use ($order, $data) {
            $message->to($order->email, $order->full_name)
                ->from(config('attendize.outgoing_email_noreply'), $order->event->organiser->name)
                ->replyTo($order->event->organiser->email, $order->event->organiser->name)
                ->subject($data['subject']);
        });

        /* Could bcc in the above? */
        if (Input::get('send_copy') == '1') {
            Mail::send('Emails.messageOrder', $data, function ($message) use ($order, $data) {
                $message->to($order->event->organiser->email)
                    ->from(config('attendize.outgoing_email_noreply'), $order->event->organiser->name)
                    ->replyTo($order->event->organiser->email, $order->event->organiser->name)
                    ->subject($data['subject'].' [Organiser copy]');
            });
        }

        return Response::json([
            'status'  => 'success',
            'message' => 'Message Successfully Sent',
        ]);
    }
}
