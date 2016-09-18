@extends('Public.ViewEvent.Layouts.EventPage')

@section('head')
    <script type="text/javascript" src="https://js.stripe.com/v2/"></script>
@stop

@section('content')
    @include('Public.ViewEvent.Partials.EventHeaderSection')

    @include('Public.ViewEvent.Partials.EventCreateOrderSection')
    <script>var OrderExpires = {{strtotime($expires)}};</script>
    @include('Public.ViewEvent.Partials.EventFooterSection')
@stop

