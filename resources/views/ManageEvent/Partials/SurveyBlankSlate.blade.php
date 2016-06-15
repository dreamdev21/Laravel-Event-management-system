@extends('Shared.Layouts.BlankSlate')

@section('blankslate-icon-class')
    ico-question2
@stop

@section('blankslate-title')
    No Questions Yet
@stop

@section('blankslate-text')
    Here you can add questions which attendees will be asked to answer during the check-out process.
@stop

@section('blankslate-body')
    <button data-invoke="modal" data-modal-id='CreateQuestion' data-href="{{route('showCreateEventQuestion', array('event_id'=>$event->id))}}" href='javascript:void(0);'  class=' btn btn-success mt5 btn-lg' type="button" >
        <i class="ico-question"></i>
        Create Question
    </button>
@stop


