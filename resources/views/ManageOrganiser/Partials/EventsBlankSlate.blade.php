@extends('Shared.Layouts.BlankSlate')

@section('blankslate-icon-class')
    ico-ticket
@stop

@section('blankslate-title')
    No Events Yet!
@stop

@section('blankslate-text')
    Looks like you have yet to create an event. You can create one by clicking the button below.
@stop

@section('blankslate-body')
<button data-invoke="modal" data-modal-id="CreateEvent" data-href="{{route('showCreateEvent', ['organiser_id' => $organiser->id])}}" href='javascript:void(0);'  class="btn btn-success mt5 btn-lg" type="button">
    <i class="ico-ticket"></i>
    Create Event
</button>
@stop


