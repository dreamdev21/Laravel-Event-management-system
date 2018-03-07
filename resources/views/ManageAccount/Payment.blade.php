@extends('Shared.Layouts.Master')

@section('title')
@parent
{{ trans('manageevent.event-attendees') }}
@stop


@section('page_title')
<i class="ico-users"></i>
{{ trans('manageaccount.account-payment') }}
@stop