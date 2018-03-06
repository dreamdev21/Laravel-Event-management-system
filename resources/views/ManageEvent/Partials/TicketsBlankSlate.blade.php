@extends('Shared.Layouts.BlankSlate')

@section('blankslate-icon-class')
    {{ trans('manageevent.ico-ticket') }}
@stop

@section('blankslate-title')
    {{ trans('manageevent.no-ticket-yet') }}
@stop

@section('blankslate-text')
    {{ trans('manageevent.create-first-ticket') }}
@stop

@section('blankslate-body')
    <button data-invoke="modal" data-modal-id='CreateTicket' data-href="{{route('showCreateTicket', array('event_id'=>$event->id))}}" href='javascript:void(0);'  class=' btn btn-success mt5 btn-lg' type="button" >
        <i class="ico-ticket"></i>
        {{ trans('manageevent.create-ticket') }}
    </button>
@stop
