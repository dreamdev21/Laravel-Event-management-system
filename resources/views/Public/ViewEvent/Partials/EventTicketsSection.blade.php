<section id="tickets" class="container">
    <div class="row">
        <h1 class='section_head'>
            Tickets
        </h1>
    </div>

    @if($event->start_date->isPast())
    <div class="alert alert-boring">
        This event has {{($event->end_date->isFuture() ? 'already started' : 'ended')}}.
    </div>
    @else

    @if($tickets->count() > 0)

    {!! Form::open(['url' => route('postValidateTickets', ['event_id' => $event->id]), 'class' => 'ajax']) !!}
    <div class="row">
        <div class="col-md-12">
            <div class="content">
                <div class="tickets_table_wrap">
                    <table class="table">
                        <?php
                         $is_free_event = true;
                        ?>
                        @foreach($tickets as $ticket)
                        <tr class="ticket" property="offers" typeof="Offer">
                            <td>
                                <span class="ticket-title semibold" property="name">
                                    {{$ticket->title}}
                                </span>
                                <p class="ticket-descripton mb0 text-muted" property="description">
                                    {{$ticket->description}}
                                </p>
                            </td>
                            <td style="width:180px; text-align: right;">
                                <div class="ticket-pricing" style="margin-right: 20px;">
                                    @if($ticket->is_free)
                                    FREE
                                    <meta property="price" content="0">
                                    @else
                                        <?php
                                        $is_free_event = false;
                                        ?>
                                    <span title='{{money($ticket->price, $event->currency->code)}} Ticket Price + {{money($ticket->total_booking_fee, $event->currency->code)}} Booking Fees'>{{money($ticket->total_price, $event->currency->code)}} </span>
                                    <meta property="priceCurrency" content="{{ $event->currency->code }}">
                                    <meta property="price" content="{{ number_format($ticket->price, 2, '.', '') }}">
                                    @endif
                                </div>
                            </td>
                            <td style="width:85px;">
                                @if($ticket->is_paused)

                                <span class="text-danger">
                                    Currently Not On Sale
                                </span>

                                @else

                                @if($ticket->sale_status === config('attendize.ticket_status_sold_out'))
                                <span class="text-danger" property="availability" content="http://schema.org/SoldOut">
                                    Sold Out
                                </span>
                                @elseif($ticket->sale_status === config('attendize.ticket_status_before_sale_date'))
                                <span class="text-danger">
                                    Sales Have Not Started
                                </span>
                                @elseif($ticket->sale_status === config('attendize.ticket_status_after_sale_date'))
                                <span class="text-danger">
                                    Sales Have Ended
                                </span>
                                @else
                               {!! Form::hidden('tickets[]', $ticket->id) !!}
                                <meta property="availability" content="http://schema.org/InStock">
                                <select name="ticket_{{$ticket->id}}" class="form-control" style="text-align: center">
                                    @if ($tickets->count() > 1)
                                    <option value="0">0</option>
                                    @endif
                                    @for($i=$ticket->min_per_person; $i<=$ticket->max_per_person; $i++)
                                    <option value="{{$i}}">{{$i}}</option>
                                    @endfor
                                </select>
                                @endif

                                @endif
                            </td>
                        </tr>
                        @endforeach

                        <tr class="checkout">
                            <td colspan="3">
                                @if(!$is_free_event)
                                    <div class="hidden-xs pull-left">
                                    <img class="" src="{{asset('assets/images/public/EventPage/credit-card-logos.png')}}" />
                                    @if($event->enable_offline_payments)

                                    <div class="help-block" style="font-size: 11px;">
                                        Offline Payment Methods Available
                                    </div>
                                    @endif

                                    </div>

                                @endif
                                {!!Form::submit('Register', ['class' => 'btn btn-lg btn-primary pull-right'])!!}
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
   {!! Form::hidden('is_embedded', $is_embedded) !!}
   {!! Form::close() !!}

    @else

    <div class="alert alert-boring">
        Tickets are currently unavailable.
    </div>

    @endif

    @endif

</section>