<div role="dialog" class="modal fade" style="display: ;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header text-center">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h3 class="modal-title">
                   Q: {{ $question->title }}
                </h3>
            </div>

            @if(count($answers))
            <div class="table-responsive">
                           <table class="table">
                               <thead>
                               <tr>
                                   <th>
                                       Attendee Details
                                   </th>
                                   <th>
                                       Ticket
                                   </th>
                                   <th>
                                       Answer
                                   </th>
                               </tr>

                               </thead>
                               <tbody>
                               @foreach($answers as $answer)
                                   <tr>
                                       <td>

                                           {{ $answer->attendee->full_name }}
                                           @if($answer->attendee->is_cancelled)
                                               (<span title="This attendee has been cancelled" class="text-danger">Cancelled</span>)
                                           @endif<br>
                                           <a title="Go to attendee: {{ $answer->attendee->full_name }}" href="{{route('showEventAttendees', ['event_id' => $answer->attendee->event_id, 'q' => $answer->attendee->reference])}}">{{ $answer->attendee->email }}</a><br>

                                       </td>
                                       <td>
                                           {{ $answer->attendee->ticket->title }}
                                       </td>
                                       <td>
                                           {!! nl2br(e($answer->answer_text)) !!}
                                       </td>
                                   </tr>
                               @endforeach
                               </tbody>
                           </table>

                       </div>
            @else
                <div class="modal-body">
                    <div class="alert alert-info">
                        Sorry, there are no answers to this question yet.
                    </div>
                </div>

            @endif

            <div class="modal-footer">
                {!! Form::button('Close', ['class'=>"btn modal-close btn-danger",'data-dismiss'=>'modal']) !!}
            </div>
        </div><!-- /end modal content-->
    </div>
</div>