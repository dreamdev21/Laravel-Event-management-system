@foreach($ticket->questions as $question)
    <div class="col-md-12">
        <div class="form-group">
            {!! Form::label("ticket_holder_questions[{$ticket->id}][{$i}][$question->id]", $question->title, ['class' => $question->is_required ? 'required' : '']) !!}

            @if($question->question_type_id == config('attendize.question_textbox_single'))
                {!! Form::text("ticket_holder_questions[{$ticket->id}][{$i}][$question->id]", null, ['required' => $question->is_required ? 'required' : '', 'class' => "ticket_holder_email.$i.{$ticket['ticket']['id']}  form-control"]) !!}
            @elseif($question->question_type_id == config('attendize.question_textbox_multi'))
                {!! Form::textarea("ticket_holder_questions[{$ticket->id}][{$i}][$question->id]", null, ['rows'=>5, 'required' => $question->is_required ? 'required' : '', 'class' => "ticket_holder_question.{$ticket['ticket']['id']}.$i.$question->id  form-control"]) !!}
            @elseif($question->question_type_id == config('attendize.question_dropdown_single'))
                {!! Form::select("ticket_holder_questions[{$ticket->id}][{$i}][$question->id]",$question->options->lists('name', 'id'), null, ['required' => $question->is_required ? 'required' : '', 'class' => "ticket_holder_question.{$ticket['ticket']['id']}.$i.$question->id  form-control"]) !!}
            @elseif($question->question_type_id == config('attendize.question_dropdown_multi'))
                {!! Form::select("ticket_holder_questions[{$ticket->id}][{$i}][$question->id]",$question->options->lists('name', 'id'), null, ['required' => $question->is_required ? 'required' : '', 'multiple' => 'multiple','class' => "ticket_holder_question.{$ticket['ticket']['id']}.$i.$question->id  form-control"]) !!}
            @elseif($question->question_type_id == config('attendize.question_checkbox_multi'))
                <br>
                @foreach($question->options as $option)
                    {{$option->name}}
                    {!! Form::checkbox("ticket_holder_questions[{$ticket->id}][{$i}][$question->id]",$option->name) !!}<br>
                @endforeach
            @elseif($question->question_type_id == config('attendize.question_radio_single'))
                <br>
                @foreach($question->options as $option)
                    {{$option->name}}
                    {!! Form::radio("ticket_holder_questions[{$ticket->id}][{$i}][$question->id]",$option->name) !!}<br>
                @endforeach
            @endif

        </div>
    </div>
@endforeach