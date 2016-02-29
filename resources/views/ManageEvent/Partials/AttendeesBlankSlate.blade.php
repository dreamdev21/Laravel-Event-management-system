@extends('Shared.Layouts.BlankSlate')


@section('blankslate-icon-class')
    ico-users
@stop

@section('blankslate-title')
    No Attendees Yet
@stop

@section('blankslate-text')
    Attendees will appear here once they successfully registered for your event, or, you can manually add attendees yourself.
@stop

@section('blankslate-body')
<button data-invoke="modal" data-modal-id='CreateTicket' data-href="{{route('showCreateAttendee', array('event_id'=>$event->id))}}" href='javascript:void(0);'  class=' btn btn-success mt5 btn-lg' type="button" >
    <i class="ico-ticket"></i>
    Create Attendee
</button>
@stop


