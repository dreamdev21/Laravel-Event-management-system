@extends('Shared.Layouts.MasterWithoutMenus')

@section('title')
    {{ trans('viewevent.event-not-live') }}
@stop

@section('content')
    <div class="row">
        <div class="col-md-4 col-md-offset-4">
            <div class="panel">
                <div class="panel-body">
                    <h4 style="text-align: center;">{{ trans('viewevent.event-page-not-avaliable') }}</h4>
                </div>
            </div>
        </div>
    </div>
@stop