<div role="dialog" class="modal fade" style="display: ;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header text-center">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h3 class="modal-title">

                   Q: {{ $question->title }}
                </h3>
            </div>

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
                                           {{ $answer->attendee->full_name }}<br>
                                           <a href="javascript:void(0);">{{ $answer->attendee->email }}</a><br>

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

            <div class="modal-footer">
                {!! Form::button('Close', ['class'=>"btn modal-close btn-danger",'data-dismiss'=>'modal']) !!}
            </div>
        </div><!-- /end modal content-->
    </div>
</div>