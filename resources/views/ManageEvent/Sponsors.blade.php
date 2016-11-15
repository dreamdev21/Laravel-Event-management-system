@extends('Shared.Layouts.Master')

@section('title')
    @parent
    Sponsors
@stop

@section('top_nav')
    @include('ManageEvent.Partials.TopNav')
@stop

@section('menu')
    @include('ManageEvent.Partials.Sidebar')
@stop

@section('page_title')
    <div class="btn-toolbar" role="toolbar">
        <div class="btn-group btn-group-responsive">
            <button data-modal-id='CreateSponsor'
                    data-href="{{route('showCreateSponsor', array('event_id'=>$event->id))}}"
                    class='loadModal btn btn-success' type="button"><i class="ico-bullhorn"></i> Create Sponsor
            </button>
        </div>
    </div>
@stop

@section('page_header')
    <style>
        .page-header {
            display: none;
        }
    </style>
@stop

@section('head')

@stop


@section('content')
    <div class="row">
        <div class="col-md-12">

            {{-- New Sponsor Form --}}
            {{-- Edit Sponsor onClick --}}
            {{-- Delete Sponsor --}}

            {{--  Show Sponsors --}}
            <div class="col-md-12">
                <div class="panel">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                            <tr>
                                <th>Name</th>
                                <th colspan="2">On Ticket</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($event->sponsors as $sponsor)
                                <tr>
                                    <td>
                                        <a href='javascript:void(0);' data-modal-id='view-sponsor-{{ $sponsor->id }}' data-href="{{route('showEditSponsor', ['event_id'=>$event->id, 'sponsor_id' => $sponsor->id])}}" class="loadModal">{{ $sponsor->name }}</a>
                                    </td>
                                    <td>{{ $sponsor->on_ticket  ? 'Yes' : 'No' }}</td>
                                    <td>
                                        {!! Form::open(array('url' => route('postDeleteSponsor', ['event_id' => $event->id, 'sponsor_id' => $sponsor->id]), 'class' => 'ajax text-right')) !!}
                                            {!! Form::submit('Delete Sponsor', ['class'=>"btn btn-danger"]) !!}
                                        {!! Form::close() !!}
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop