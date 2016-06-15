<div role="dialog"  class="modal fade " style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header text-center">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h3 class="modal-title">
                    <i class="ico-question"></i>
                    Coupons</h3>
            </div>
            <div class="modal-body">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <a data-toggle="collapse" data-parent="#QuestionsAccordion" href="#collapse{{$i}}" class="collapsed">
                                <span class="arrow mr5"></span> What age are you?
                            </a>
                        </h4>
                    </div>
                    <div class="panel-body">
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-12">
                                    <label class="required">
                                        Question
                                    </label>
                                    <input placeholder="What is your name?" class="form-control" type="text" name="title" />
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-12">
                                    <label>
                                        Question Type
                                    </label>
                                    <select class="form-control" name="question_type_id">
                                        <option value="1">
                                            Text Box
                                        </option>
                                        <option value="2">
                                            Drop Down List
                                        </option>
                                        <option value="3">
                                            Checkbox (True/False)
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-12">
                                    <label>
                                        Instructions
                                    </label>
                                    <input class="form-control" type="text" name="instructions" />
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-12">
                                    <label>
                                        Question Options
                                    </label>
                                    <input placeholder="e.g option 1, option 2, option 3" class="form-control" type="text" name="options" />
                                    <div class="help-block">
                                        Please use a comma to separate options.
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-12">
                                    <span class="checkbox custom-checkbox">
                                        <input type="checkbox" id="requiredq" value="option1">
                                        <label for="requiredq">&nbsp; Make this a required question</label>
                                    </span>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="panel-footer">
                        <div class="form-group no-border">
                            <div class="col-sm-12">
                                <button  class="btn btn-danger deleteThis float-right">Delete Question</button>
                                <button type="submit" class="btn btn-success float-right">Save Question</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div> <!-- /end modal body-->
            <div class="modal-footer">
                <a href="" class="btn btn-danger" data-dismiss="modal">
                    Close
                </a>
                <a href="" class="btn btn-success">
                    Create Question
                </a>
                <button data-modal-id="CreateTicket" href="javascript:void(0);"  data-href="{{route('showCreateTicket', array('event_id'=>$event->id))}}" class="loadModal btn btn-success" type="button" ><i class="ico-ticket"></i> Create Ticket</button>
            </div>
        </div><!-- /end modal content-->
    </div>
</div>
