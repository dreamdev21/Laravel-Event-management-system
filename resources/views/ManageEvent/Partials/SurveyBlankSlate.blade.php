@extends('Shared.Layouts.BlankSlate')

@section('blankslate-icon-class')
    {{ trans('manageevent.ico-question2') }}
@stop

@section('blankslate-title')
    {{ trans('manageevent.no-questions-yet') }}
@stop

@section('blankslate-text')
    {{ trans('manageevent.here-you-can-add') }}
@stop

@section('blankslate-body')
    <button data-invoke="modal" data-modal-id='CreateQuestion' data-href="{{route('showCreateEventQuestion', array('event_id'=>$event->id))}}" href='javascript:void(0);'  class=' btn btn-success mt5 btn-lg' type="button" >
        <i class="ico-question"></i>
        {{ trans('manageevent.create-question') }}
    </button>
@stop


