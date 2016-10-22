@extends('Public.ViewEvent.Layouts.EmbeddedEventPage')

@section('content')
    @include('Public.ViewEvent.Partials.EventShareSection')
    @include('Public.ViewEvent.Partials.EventViewOrderSection')
    @include('Public.ViewEvent.Embedded.Partials.PoweredByEmbedded')
@stop
