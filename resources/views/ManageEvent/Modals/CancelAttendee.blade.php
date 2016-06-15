<div role="dialog"  class="modal fade " style="display: none;">
   {!! Form::model($attendee, array('url' => route('postCancelAttendee', array('event_id' => $event->id, 'attendee_id' => $attendee->id)), 'class' => 'ajax')) !!}
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header text-center">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h3 class="modal-title">
                    <i class="ico-cancel"></i>
                    Cancel <b>{{$attendee->full_name}} <b></h3>
            </div>
            <div class="modal-body">
                <p>
                    Cancelling Attendees will remove them from the attendee list.
                </p>

                <p>
                    If you would like to refund the order which this attendee belongs to you can do so
                    <a href="{{route('showEventOrders', ['event_id' => $attendee->event->id, 'q' => $attendee->order->order_reference])}}">here</a>.
                </p>
                <br>
                <div class="form-group">
                    <div class="checkbox custom-checkbox">
                        <input type="checkbox" name="notify_attendee" id="notify_attendee" value="1">
                        <label for="notify_attendee">&nbsp;&nbsp;Notify <b>{{$attendee->full_name}}</b> their ticket has been cancelled.</label>
                    </div>
                </div>
                @if(config('attendize.default_payment_gateway') == config('attendize.payment_gateway_stripe'))
                    <div class="form-group">
                            <div class="checkbox custom-checkbox">
                                <input type="checkbox" name="refund_attendee" id="refund_attendee" value="1">
                                <label for="refund_attendee">&nbsp;&nbsp;Refund <b>{{$attendee->full_name}}</b> for their ticket.</label>
                            </div>
                    </div>
                @endif
            </div> <!-- /end modal body-->
            <div class="modal-footer">
               {!! Form::hidden('attendee_id', $attendee->id) !!}
               {!! Form::button('Cancel', ['class'=>"btn modal-close btn-danger",'data-dismiss'=>'modal']) !!}
               {!! Form::submit('Confirm Cancel Attendee', ['class'=>"btn btn-success"]) !!}
            </div>
        </div><!-- /end modal content-->
       {!! Form::close() !!}
    </div>
</div>

