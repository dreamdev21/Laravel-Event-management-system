
<div role="dialog" id="{{ $modal_id }}" class="modal fade" style="display: none;">
    <script id="question-option-template" type="text/template">
        <tr>
            <td><input class="form-control" name="option[]" type="text"></td>
            <td width="50">
                <i class="btn btn-danger ico-remove" onclick="removeQuestionOption(this);"></i>
            </td>
        </tr>
    </script>
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header text-center">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h3 class="modal-title">
                    <i class="ico-question"></i>
                    Create Question</h3>
            </div>
            <div class="modal-body">
                <div class="panel panel-default">
                    {!!  Form::model($question, ['url' => $form_url, 'id' => 'edit-question-form', 'class' => 'ajax']) !!}
                    <div class="panel-body">
                        <div class="form-group">
                            <label for="question-title" class="required">
                                Question
                            </label>
                            {!! Form::text('title', '', [
                                'id' => 'question-title',
                                'class' => 'form-control',
                                'placeholder' => 'e.g.: What is your name?',
                            ]) !!}
                        </div>
                        <div class="form-group">
                            <label for="question-type">
                                Question Type
                            </label>

                            <select id="question-type" class="form-control" name="question_type_id" onchange="changeQuestionType(this);">
                                @foreach ($question_types as $question_type)
                                    <option data-has-options="{{$question_type->has_options}}" value="{{$question_type->id}}">
                                        {{$question_type->name}}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="question-instructions">
                                Instructions
                            </label>
                            {!! Form::text('instructions', null, [
                                'id' => 'question-instructions',
                                'class' => 'form-control',
                            ]) !!}
                        </div>
                        <fieldset id="question-options" {!! empty($question->has_options) ? ' class="hide"' : '' !!}>
                            <legend>Question Options</legend>
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th colspan="2">Option name</th>
                                    </tr>
                                </thead>
                                <tbody>
                                @foreach ($question_options as $question_option)
                                    <tr>
                                        <td><input class="form-control" name="option[]" type="text" value="{{ $question_option->name }}"></td>
                                        <td width="50">
                                            <i class="btn btn-danger ico-remove" onclick="removeQuestionOption(this);"></i>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                       <td colspan="2">
                                           <span id="add-question-option" class="btn btn-success btn-xs" onclick="addQuestionOption();">
                                               <i class="ico-plus"></i>
                                               Add another option
                                           </span>
                                       </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </fieldset>

                        <div class="form-group">
                            {!! Form::checkbox('is_required', 1, null, ['id' => 'is_required']) !!}
                            {!! Form::label('is_required', 'Make this a required question') !!}
                        </div>

                        <div class="form-group">
                            <label>
                                Require this question for ticket(s):
                            </label>

                            @foreach ($event->tickets as $ticket)
                                <br>
                                <input id="ticket_{{ $ticket->id }}" name="tickets[]" type="checkbox" value="{{ $ticket->id }}">
                                <label for="ticket_{{ $ticket->id }}">&nbsp; {{ $ticket->title }}</label>
                            @endforeach
                        </div>

                    </div>
                    {!! Form::close() !!}
                </div>
            </div> <!-- /end modal body-->
            <div class="modal-footer">
                <a href="" class="btn btn-danger" data-dismiss="modal">
                    Close
                </a>
                <a class="btn btn-success" href="javascript:void(0);" onclick="submitQuestionForm();">
                    Create Question
                </a>
            </div>
        </div><!-- /end modal content-->
    </div>
</div>