<div role="dialog" id="{{$modal_id}}" class="modal fade " style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header text-center">
                <button type="button" class="close" data-dismiss="modal">×</button>
                <h3 class="modal-title">
                    <i class="ico-question"></i>
                    Questions</h3>
            </div>
            <div class="modal-body">

                <h3>
                    Existing Questions
                </h3>
                <?php $i = 1; ?>
                <div class="panel-group" id="QuestionsAccordion">

                 @foreach($event->questions as $question) 

                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h4 class="panel-title">
                                <a data-toggle="collapse" data-parent="#QuestionsAccordion" href="#collapse{{$question->id}}" class="collapsed">
                                    <span class="arrow mr5"></span> {{$question->title}}
                                </a>
                            </h4>
                        </div>
                        
                        
                        
                        <div id="collapse{{$question->id}}" class="panel-collapse collapse" style="height: 0px;">
                           {!!Form::open(['url' => ''])!!}
                   <div class="panel-body">
                        <div class="form-group">
                            <label class="required">
                                Question
                            </label>
                            <input placeholder="What is your name?" class="form-control" type='text' name='title' />
                        </div>
                        <div class="form-group">
                            <label>
                                Question Type
                            </label>
                            <select class="form-control"  name='question_type_id' >
                                @foreach($question_types as $question_type)
                                <option data-allow-multiple='{{$question_type->allow_multiple}}' value="{{$question_type->id}}">
                                    {{$question_type->name}}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>
                                Instructions
                            </label>
                            <input class="form-control" type='text' name='instructions' />
                        </div>
                        <div class="form-group">
                            <label>
                                Question Options
                            </label>
                            <input placeholder="e.g option 1, option 2, option 3" class="form-control" type='text' name='options' />
                            <div class="help-block">
                                Please use a comma to separate options.
                            </div>
                        </div>

                        <div class="form-group">
                            <input type="checkbox" name='is_required' id="is_required" value="1">  
                            <label for="is_required">&nbsp; Make this a required question</label>   
                        </div>

                        <div class="form-group">
                            <label>
                                Require this question for ticket(s):
                            </label>

                            @foreach($event->tickets as $ticket)
                            <br>
                            <input name="tickets[]" type="checkbox" id="ticket_{{$ticket->id}}" value="{{$ticket->id}}">  
                            <label for="ticket_{{$ticket->id}}">&nbsp; {{$ticket->title}}</label>
                            @endforeach

                        </div>

                    </div>
                           {!!Form::close();!!}
                            <div class="panel-footer">
                                <div class="form-group no-border">
                                    <button  class="btn btn-danger deleteThis float-right">Delete Question</button>
                                    <button type="submit" class="btn btn-success float-right">Save Question</button>
                                </div> 
                            </div>
                        </div>
                        
                        
                    </div>
@endforeach

                </div>


                <div class="panel panel-default">

                    {!! Form::open(['url' => route('postCreateQuestion', [
                                'event_id' => $event->id
                            ]), 'class' => 'ajax']) !!}
                    <div class="panel-body">
                        <div class="form-group">
                            <label class="required">
                                Question
                            </label>
                            <input placeholder="What is your name?" class="form-control" type='text' name='title' />
                        </div>
                        <div class="form-group">
                            <label>
                                Question Type
                            </label>
                            <select class="form-control"  name='question_type_id' >
                                @foreach($question_types as $question_type)
                                <option data-allow-multiple='{{$question_type->allow_multiple}}' value="{{$question_type->id}}">
                                    {{$question_type->name}}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>
                                Instructions
                            </label>
                            <input class="form-control" type='text' name='instructions' />
                        </div>
                        <div class="form-group">
                            <label>
                                Question Options
                            </label>
                            <input placeholder="e.g option 1, option 2, option 3" class="form-control" type='text' name='options' />
                            <div class="help-block">
                                Please use a comma to separate options.
                            </div>
                        </div>

                        <div class="form-group">
                            <input type="checkbox" name='is_required' id="is_required" value="1">  
                            <label for="is_required">&nbsp; Make this a required question</label>   
                        </div>

                        <div class="form-group">
                            <label>
                                Require this question for ticket(s):
                            </label>

                            @foreach($event->tickets as $ticket)
                            <br>
                            <input name="tickets[]" type="checkbox" id="ticket_{{$ticket->id}}" value="{{$ticket->id}}">  
                            <label for="ticket_{{$ticket->id}}">&nbsp; {{$ticket->title}}</label>
                            @endforeach

                        </div>

                    </div>
                    <div class="panel-footer">
                        <div class="form-group no-border">
                            <button type="submit" class="btn btn-success float-right">Create Question</button>
                        </div> 
                    </div>
                   {!!Form::close();!!}

                </div>



            </div> <!-- /end modal body-->
            <div class="modal-footer">
                <a href='' class="btn btn-danger" data-dismiss='modal'>
                    Close
                </a>
                <a href='' class="btn btn-success">
                    Create Question
                </a>
            </div>
        </div><!-- /end modal content-->
    </div>

