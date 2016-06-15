@extends('Shared.Layouts.Master')

@section('title')
@parent

Event Widgets
@stop

@section('top_nav')
@include('ManageEvent.Partials.TopNav')
@stop

@section('menu')
@include('ManageEvent.Partials.Sidebar')
@stop

@section('page_title')
<i class='ico-code mr5'></i>
Event Surveys
@stop

@section('head')

@stop

@section('page_header')
<style>
    .page-header {display: none;}
</style>
@stop


@section('content')
<div class="row">


    <div class="col-md-12">

        <div class="panel">
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-6">
                        <h4>HTML Embed Code</h4>
                            <textarea rows="7" onfocus="this.select();"
                                      class="form-control">{{$event->embed_html_code}}</textarea>
                    </div>
                    <div class="col-md-6">
                        <h4>Instructions</h4>

                        <p>
                            Simply copy and paste the HTML provided into your website wherever you would like the widget to appear.
                        </p>

                        <h5>
                            <b>Embed Preview</b>
                        </h5>

                        <div class="preview_embed" style="border:1px solid #ddd; padding: 5px;">
                            {!! $event->embed_html_code !!}
                        </div>

                    </div>
                </div>
            </div>

        </div>

    </div>

</div>
@stop
