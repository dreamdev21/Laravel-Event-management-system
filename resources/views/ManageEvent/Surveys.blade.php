@extends('Shared.Layouts.Master')

@section('title')
    @parent

    Event Surveys
@stop

@section('top_nav')
    @include('ManageEvent.Partials.TopNav')
@stop

@section('menu')
    @include('ManageEvent.Partials.Sidebar')
@stop

@section('page_title')
    <i class='ico-clipboard4 mr5'></i>
    Event Surveys
@stop

@section('head')

@stop

@section('page_header')
    <div class="col-md-9 col-sm-6">
        <!-- Toolbar -->
        <div class="btn-toolbar" role="toolbar">
            <div class="btn-group btn-group btn-group-responsive">

                <button class="loadModal btn btn-success" type="button" data-modal-id="CreateQuestion"
                        href="javascript:void(0);"
                        data-href="{{route('showCreateEventQuestion', ['event_id' => $event->id])}}">
                    <i class="ico-question"></i> Add question
                </button>
            </div>

            <div class="btn-group btn-group btn-group-responsive">
                <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown">
                    <i class="ico-users"></i> Export Answers <span class="caret"></span>
                </button>
                <ul class="dropdown-menu" role="menu">
                    <li><a href="{{route('showExportAnswers', ['event_id'=>$event->id,'export_as'=>'xlsx'])}}">Excel
                            (XLSX)</a></li>
                    <li><a href="{{route('showExportAnswers', ['event_id'=>$event->id,'export_as'=>'xls'])}}">Excel
                            (XLS)</a></li>
                    <li><a href="{{route('showExportAnswers', ['event_id'=>$event->id,'export_as'=>'csv'])}}">CSV</a>
                    </li>
                    <li><a href="{{route('showExportAnswers', ['event_id'=>$event->id,'export_as'=>'html'])}}">HTML</a>
                    </li>
                </ul>
            </div>
        </div>
        <!--/ Toolbar -->
    </div>
    <div class="col-md-3 col-sm-6">

    </div>
    @stop


    @section('content')
            <!--Start Questions table-->
    <div class="row">
        <script>
            /*
            @todo Move this into main JS file
             */
            $(function () {


                $(document.body).on('click', '.enableQuestion', function (e) {

                    var questionId = $(this).data('id'),
                            route = $(this).data('route');

                    $.post(route, 'question_id=' + questionId)
                            .done(function (data) {

                                if (typeof data.message !== 'undefined') {
                                    showMessage(data.message);
                                }

                                switch (data.status) {
                                    case 'success':
                                        setTimeout(function () {
                                            document.location.reload();
                                        }, 300);
                                        break;
                                    case 'error':
                                        showMessage(Attendize.GenericErrorMessages);
                                        break;

                                    default:
                                        break;
                                }
                            }).fail(function (data) {
                        showMessage(Attendize.GenericErrorMessages);
                    });


                    e.preventDefault();
                });


                $('.sortable').sortable({
                    handle: '.sortHanlde',
                    forcePlaceholderSize: true,
                    placeholder: '<tr><td class="bg-info" colspan="6">&nbsp;</td></tr>'
                }).bind('sortupdate', function (e, ui) {

                    var data = $('.sortable tr').map(function () {
                        return $(this).data('question-id');
                    }).get();

                    $.ajax({
                        type: 'POST',
                        url: Attendize.postUpdateQuestionsOrderRoute,
                        dataType: 'json',
                        data: {question_ids: data},
                        success: function (data) {
                            showMessage(data.message)
                        },
                        error: function (data) {
                            console.log(data);
                        }
                    });
                });
            });
        </script>
        @if($questions->count())

            <div class="col-md-12">

                <!-- START panel -->
                <div class="panel">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                            <th style="width: 25px;">

                            </th>

                            <th>
                                Question Title
                            </th>
                            <th>
                                Required
                            </th>
                            <th>
                                Status
                            </th>
                            <th>
                                # Responses
                            </th>
                            <th>

                            </th>
                            </thead>

                            <tbody class="sortable">
                            @foreach ($questions as $question)
                                <tr id="question-{{ $question->id }}" data-question-id="{{ $question->id }}">
                                    <td>
                                        <div style="cursor: move;" class="sortHanlde">
                                            <i class="ico-sort "></i>
                                        </div>
                                    </td>
                                    <td>
                                        {{ $question->title }}<br>
                                        <span style="font-size: 11px; color: #888;"
                                              class="muted">Tickets: {{implode(', ', array_column($question->tickets->toArray(), 'title'))}}</span>
                                    </td>
                                    <td>
                                        {{ $question->is_required ? 'Yes' : 'No' }}
                                    </td>
                                    <td>
                                        <span class="label label-{{ $question->is_enabled ? 'info' : 'warning' }}">{{ $question->is_enabled ? 'Enabled' : 'Disabled' }}</span>
                                    </td>
                                    <td>
                                        <a class="loadModal" data-modal-id="showEventQuestionAnswers"
                                           href="javascript:void(0);"
                                           data-href="{{route('showEventQuestionAnswers', ['event_id' => $event->id, 'question_id' => $question->id])}}">
                                            {{ $question->answers->count() }}
                                        </a>

                                    </td>
                                    <td class="text-center">


                                        <a class="btn btn-xs btn-primary loadModal" data-modal-id="EditQuestion"
                                           href="javascript:void(0);"
                                           data-href="{{route('showEditEventQuestion', ['event_id' => $event->id, 'question_id' => $question->id])}}">
                                            Edit
                                        </a>
                                        <a class="btn btn-xs btn-primary loadModal" href="javascript:void(0);"
                                           data-href="{{route('showEventQuestionAnswers', ['event_id' => $event->id, 'question_id' => $question->id])}}">
                                            Answers
                                        </a>
                                        <a class="btn btn-xs btn-primary enableQuestion" href="javascript:void(0);"
                                           data-route="{{ route('postEnableQuestion', ['event_id' => $event->id, 'question_id' => $question->id]) }}"
                                           data-id="{{ $question->id }}"
                                        >
                                            {{ $question->is_enabled ? 'Disable' : 'Enable' }}
                                        </a>
                                        <a data-id="{{ $question->id }}"
                                           title="All answers will also be deleted. If you want to keep attendee's answers you should deactivate the question instead."
                                           data-route="{{ route('postDeleteEventQuestion', ['event_id' => $event->id, 'question_id' => $question->id]) }}"
                                           data-type="question" href="javascript:void(0);"
                                           class="deleteThis btn btn-xs btn-danger">
                                            Delete
                                        </a>

                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>


        @else

            @include('ManageEvent.Partials.SurveyBlankSlate')

        @endif
    </div>    <!--/End questions table-->


@stop
