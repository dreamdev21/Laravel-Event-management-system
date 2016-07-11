<div role="dialog"  class="modal fade" style="display: none;">
   {!! Form::open(array('url' => route('postMessageAttendee', array('attendee_id' => $attendee->id)), 'class' => 'ajax reset closeModalAfter')) !!}
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header text-center">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h3 class="modal-title">
                    <i class="ico-envelope"></i>
                    Message {{{$attendee->full_name}}}
                </h3>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            {!! Form::label('subject', 'Mesage Subject', array('class'=>'control-label required')) !!}
                            {!!  Form::text('subject', Input::old('subject'),
                                        array(
                                        'class'=>'form-control'
                                        ))  !!}
                        </div>

                        <div class="form-group">
                            {!! Form::label('message', 'Message Content', array('class'=>'control-label required')) !!}

                            {!!  Form::textarea('message', Input::old('message'),
                                        array(
                                        'class'=>'form-control',
                                        'rows' => '5'
                                        ))  !!}
                        </div>

                        <div class="form-group">
                            <div class="custom-checkbox">
                                <input type="checkbox" name="send_copy" id="send_copy" value="1">
                                <label for="send_copy">&nbsp;&nbsp;Send a copy to <b>{{$attendee->event->organiser->email}}</b></label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="help-block">
                    The attendee will be instructed to send any reply to <b>{{$attendee->event->organiser->email}}</b>
                </div>
            </div> <!-- /end modal body-->
            <div class="modal-footer">
               {!! Form::button('Cancel', ['class'=>"btn modal-close btn-danger",'data-dismiss'=>'modal']) !!}
               {!! Form::submit('Send Message', ['class'=>"btn btn-success"]) !!}
            </div>
        </div><!-- /end modal content-->
        {!! Form::close() !!}
    </div>
</div>