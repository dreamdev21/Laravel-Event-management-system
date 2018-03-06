<div role="dialog"  class="modal fade " style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header text-center">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h3 class="modal-title">
                    <i class="ico-question"></i>
                    {{ trans('manageevent.coupons') }}</h3>
            </div>
            <div class="modal-body">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <a data-toggle="collapse" data-parent="#QuestionsAccordion" href="#collapse{{$i}}" class="collapsed">
                                <span class="arrow mr5"></span> {{ trans('manageevent.what-age-are-you') }}
                            </a>
                        </h4>
                    </div>
                    <div class="panel-body">
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-12">
                                    <label class="required">
                                        {{  trans('manageevent.question') }}
                                    </label>
                                    <input placeholder="What is your name?" class="form-control" type="text" name="title" />
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-12">
                                    <label>
                                        {{trans('manageevent.question-type')}}
                                    </label>
                                    <select class="form-control" name="question_type_id">
                                        <option value="1">
                                            {{ trans('manageevent.text-box') }}
                                        </option>
                                        <option value="2">
                                            {{ trans('manageevent.drop-down-list') }}
                                        </option>
                                        <option value="3">
                                            {{ trans('manageevent.checkbox-t-f') }}
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-12">
                                    <label>
                                        {{ trans('manageevent.instructions') }}
                                    </label>
                                    <input class="form-control" type="text" name="instructions" />
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-12">
                                    <label>
                                        {{ trans('manageevent.question-options') }}
                                    </label>
                                    <input placeholder="e.g option 1, option 2, option 3" class="form-control" type="text" name="options" />
                                    <div class="help-block">
                                        {{ trans('manageevent.use-comma') }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-12">
                                    <span class="checkbox custom-checkbox">
                                        <input type="checkbox" id="requiredq" value="option1">
                                        <label for="requiredq">&nbsp; {{ trans('manageevent.require-question') }}</label>
                                    </span>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="panel-footer">
                        <div class="form-group no-border">
                            <div class="col-sm-12">
                                <button  class="btn btn-danger deleteThis float-right">{{ trans('manageevent.delete-question') }}</button>
                                <button type="submit" class="btn btn-success float-right">{{ trans('manageevent.save-question') }}</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div> <!-- /end modal body-->
            <div class="modal-footer">
                <a href="" class="btn btn-danger" data-dismiss="modal">
                    {{ trans('common.close') }}
                </a>
                <a href="" class="btn btn-success">
                    {{ trans('manageevent.create-question') }}
                </a>
                <button data-modal-id="CreateTicket" href="javascript:void(0);"  data-href="{{route('showCreateTicket', array('event_id'=>$event->id))}}" class="loadModal btn btn-success" type="button" ><i class="ico-ticket"></i> {{ trans('manageevent.create-ticket') }}</button>
            </div>
        </div><!-- /end modal content-->
    </div>
</div>
