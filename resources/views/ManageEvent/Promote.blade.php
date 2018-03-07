@extends('Shared.Layouts.Master')

@section('title')
@parent
{{ trans('manageevent.promote-event') }}
@stop

@section('top_nav')
    @include('ManageEvent.Partials.TopNav')
@stop


@section('menu')
    @include('ManageEvent.Partials.Sidebar')
@stop

@section('page_title')
<i class="ico-bullhorn mr5"></i>
{{ trans('manageevent.promote-event') }}
@stop


@section('content')
<div class='row'>
    <div class="col-md-12">
        <h1>
            {{ trans('manageevent.promote') }}
            <pre>
                [PROMOTE PAGE]
            </pre>
        </h1>
    </div>
</div>
@stop


