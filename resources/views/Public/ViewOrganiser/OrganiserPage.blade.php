@extends('Public.ViewOrganiser.Layouts.OrganiserPage')

@section('head')
     <style>
          body { background-color: {{$organiser->page_bg_color}} !important; }
          section#intro {
               background-color: {{$organiser->page_header_bg_color}} !important;
               color: {{$organiser->page_text_color}} !important;
          }
          .event-list > li > time {
               color: {{$organiser->page_text_color}};
               background-color: {{$organiser->page_header_bg_color}};
          }

     </style>
     @if($organiser->google_analytics_code)
          @include('Public.Partials.ga', ['analyticsCode' => $organiser->google_analytics_code])
     @endif
@stop

@section('content')
     @include('Public.ViewOrganiser.Partials.OrganiserHeaderSection')
     @include('Public.ViewOrganiser.Partials.OrganiserEventsSection')
     @include('Public.ViewOrganiser.Partials.OrganiserFooterSection')
@stop

