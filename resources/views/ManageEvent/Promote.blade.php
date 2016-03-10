@extends('Shared.Layouts.Master')

@section('title')
@parent
Promote Event
@stop

@section('top_nav')
    @include('ManageEvent.Partials.TopNav')
@stop


@section('menu')
    @include('ManageEvent.Partials.Sidebar')
@stop

@section('page_title')
<i class="ico-bullhorn mr5"></i>
Promote Event
@stop


@section('content')
<div class='row'>
    <div class="col-md-12">
        <h1>
            Promote
            <pre>
                [PROMOTE PAGE]
            </pre>
        </h1>
    </div>
</div>
@stop


