<div role="dialog"  class="modal fade " style="display: none;">
    {!! Form::open(array('url' => route('postMessageAttendees', array('event_id' => $event->id)), 'class' => 'reset ajax closeModalAfter')) !!}
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header text-center">
                <button type="button" class="close" data-dismiss="modal">×</button>
                <h3 class="modal-title">
                    <i class="ico-envelope"></i>
                    Message Attendees</h3>
            </div>
            <div class="modal-body">
                <ul class="nav nav-tabs">
                    <li class="active"><a href="#new_message" data-toggle="tab">New Message</a></li>
                    <li><a href="#sent_messages" data-toggle="tab">Sent Messages</a></li>
                </ul>

                <div class="tab-content panel">
                    <div class="tab-pane active" id="new_message">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    {!! Form::label('subject', 'Message Subject', array('class'=>'control-label required')) !!}
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
                                    {!! Form::label('recipients', 'Send To', array('class'=>'control-label required')) !!}
                                    {!!  Form::select('recipients', [
                                            'all' => 'All Event Attendees'
                                        ] + ['Attendees with ticket type' => $tickets] ,
                                        null, [
                                            'class'=>'form-control'
                                        ])  !!}
                                </div>

                                <div class="form-group hide">
                                    {!! Form::label('sent_time', 'Schedule Send Time', array('class'=>'control-label required')) !!}
                                    {!!  Form::text('sent_time', Input::old('sent_time'),
                                        array(
                                        'class'=>'form-control'
                                        ))  !!}
                                    <div class="help-block">
                                        Leave blank to send immediately.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane" id="sent_messages">

                        @if(count($event->messages) > 0)
                            <div class="table-layout">
                                <!-- content -->
                                <div class="col-lg-12 valign-top panel panel-default">
                                    <div class="nm">
                                        <table class="table table-hover table-email">
                                            <thead>
                                            <tr>
                                                <td style="width: 100px;">
                                                    <h5>
                                                        <b>Date</b>
                                                    </h5>
                                                </td>
                                                <td>
                                                    <h5>
                                                        <b>Message</b>
                                                    </h5>
                                                </td>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach($event->messages as $message)
                                                <tr>
                                                    <td class="meta">
                                                        <p class="date">{{$message->sent_at->format('M j, Y, g:i a')}}</p>
                                                    </td>
                                                    <td class="message">
                                                        <h5 class="sender">To: <b>{{$message->recipients_label}}</b></h5>
                                                        <h5 class="heading"><a href="javascript:void();">{{$message->subject}}</a></h5>

                                                        <p class="text">{{nl2br($message->message)}}</p>
                                                    </td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    <!--/ email list -->
                                </div>
                                <!--/ content -->
                            </div>
                        @else
                            <div class="alert alert-info">
                                You have not sent any messages for this event.
                            </div>
                        @endif
                    </div>
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
