@extends('Shared.Layouts.Master')

@section('title')
    @parent
    Dashboard
@stop

@section('top_nav')
    @include('ManageOrganiser.Partials.TopNav')
@stop
@section('page_title')
    <i class="ico-building"></i>
    <i>{{ $organiser->name }}</i> Dashboard
@stop

@section('menu')
    @include('ManageOrganiser.Partials.Sidebar')
@stop

@section('head')

    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/morris.js/0.5.1/morris.css">
    <script src="//cdnjs.cloudflare.com/ajax/libs/raphael/2.1.0/raphael-min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/morris.js/0.5.1/morris.min.js"></script>

    {!!HTML::script('https://maps.googleapis.com/maps/api/js?sensor=false&amp;libraries=places')!!}
    {!! HTML::script('vendor/geocomplete/jquery.geocomplete.min.js')!!}

    <style>
        svg {
            width: 100% !important;
        }
    </style>

@stop

@section('content')
    <div class="row">
        <div class="col-sm-4">
            <div class="stat-box">
                <h3>
                    {{$organiser->events->count()}}
                </h3>
            <span>
                Events
            </span>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="stat-box">
                <h3>
                    {{$organiser->attendees->count()}}
                </h3>
            <span>
                Tickets Sold
            </span>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="stat-box">
                <h3>
                    {{ money($organiser->events->sum('sales_volume') + $organiser->events->sum('organiser_fees_volume'), 'EUR') }}
                </h3>
            <span>
                Sales Volume
            </span>
            </div>
        </div>
    </div>

    @if($upcoming_events->count())
        <h4 style="margin-bottom: 25px;margin-top: 20px;">Upcoming Events</h4>
        <div class="row">
            @foreach($upcoming_events as $event)
                <div class="col-md-6 col-sm-6 col-xs-12">
                    @include('ManageOrganiser.Partials.EventPanel')
                </div>
            @endforeach
        </div>
    @else
            @include('ManageOrganiser.Partials.EventsBlankSlate')
    @endif
@stop
