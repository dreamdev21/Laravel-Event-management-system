@extends('Shared.Layouts.BlankSlate')

@section('blankslate-icon-class')
    {{ trans('manageorganiser.ico-ticket') }}
@stop

@section('blankslate-title')
    {{ trans('manageorganiser.no-events') }}
@stop

@section('blankslate-text')
    {{  trans('manageorganiser.message-label') }}
@stop

@section('blankslate-body')
<button data-invoke="modal" data-modal-id="CreateEvent" data-href="{{route('showCreateEvent', ['organiser_id' => $organiser->id])}}" href='javascript:void(0);'  class="btn btn-success mt5 btn-lg" type="button">
    <i class="ico-ticket"></i>
    {{ trans('manageorganiser.create-event') }}
</button>
@stop


