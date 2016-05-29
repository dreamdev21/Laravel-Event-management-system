@extends('Shared.Layouts.BlankSlate')


@section('blankslate-icon-class')
    ico-users
@stop

@section('blankslate-title')
    No Attendees Yet
@stop

@section('blankslate-text')
    Attendees will appear here once they successfully registered for your event, or, you can manually invite attendees yourself.
@stop

@section('blankslate-body')
<button data-invoke="modal" data-modal-id='InviteAttendee' data-href="{{route('showInviteAttendee', array('event_id'=>$event->id))}}" href='javascript:void(0);'  class=' btn btn-success mt5 btn-lg' type="button" >
    <i class="ico-user-plus"></i>
    Invite Attendee
</button>
@stop


