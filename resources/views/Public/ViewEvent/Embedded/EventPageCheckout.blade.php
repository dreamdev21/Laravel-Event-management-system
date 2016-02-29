@extends('Public.ViewEvent.Layouts.EmbeddedEventPage')

@section('head')
<script type="text/javascript" src="https://js.stripe.com/v2/"></script>
@stop

@section('content')
@include('Public.ViewEvent.Partials.EventCreateOrderSection')
<script> var OrderExpires = {{strtotime($expires)}};</script>

@stop

