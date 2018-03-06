@extends('Shared.Layouts.BlankSlate')


@section('blankslate-icon-class')
    {{ trans('manageevent.ico-users') }}
@stop

@section('blankslate-title')
    {{ trans('manageevent.no-attendees-yet') }}
@stop

@section('blankslate-text')
    {{ trans('manageevent.attendees-will-appear-here') }}
@stop

@section('blankslate-body')
<button data-invoke="modal" data-modal-id='InviteAttendee' data-href="{{route('showInviteAttendee', array('event_id'=>$event->id))}}" href='javascript:void(0);'  class=' btn btn-success mt5 btn-lg' type="button" >
    <i class="ico-user-plus"></i>
    {{ trans('manageevent.invite-attendees') }}
</button>
@stop


