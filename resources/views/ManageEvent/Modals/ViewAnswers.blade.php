<div role="dialog" id="{{$modal_id}}" class="modal fade" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header text-center">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h3 class="modal-title">
                    <i class="ico-question3"></i>
                    View Answers
                </h3>
            </div>
            <div class="modal-body">
                @foreach($attendees as $attendee)
                    @if(count($attendee->answers))
                        {{ $attendee->first_name }} {{ $attendee->last_name }}<br>
                        @foreach($attendee->answers as $answer)
                            <b>{{ $answer->question->title }}</b><br>
                            {{ $answer->answer_text }}<br>
                        @endforeach
                        <hr>
                    @endif
                @endforeach
            </div> <!-- /end modal body-->
            <div class="modal-footer">
                {!! Form::button('Close', ['class'=>"btn modal-close btn-danger",'data-dismiss'=>'modal']) !!}
            </div>
        </div><!-- /end modal content-->
    </div>
</div>