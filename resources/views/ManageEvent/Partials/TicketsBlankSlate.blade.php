@extends('Shared.Layouts.BlankSlate')

@section('blankslate-icon-class')
    ico-ticket
@stop

@section('blankslate-title')
    No Tickets Yet
@stop

@section('blankslate-text')
    Create your first ticket by clicking the button below.
@stop

@section('blankslate-body')
    <button data-invoke="modal" data-modal-id='CreateTicket' data-href="{{route('showCreateTicket', array('event_id'=>$event->id))}}" href='javascript:void(0);'  class=' btn btn-success mt5 btn-lg' type="button" >
        <i class="ico-ticket"></i>
        Create Ticket
    </button>
@stop
