@extends('Shared.Layouts.Master')

@section('title')
    @parent
    Organiser Events
@stop

@section('page_title')
    <i class="ico-calendar"></i>
    <i>{{$organiser->name}}</i> Events
@stop

@section('top_nav')
    <ul class="nav navbar-nav navbar-left">
        <li class="navbar-main">
            <a href="javascript:void(0);" class='toggleSidebar' title="Organisers">
            <span class="toggleSidebarIcon">
                <span class="icon">
                    <i class="ico-menu"></i>
                </span>
            </span>
            </a>
        </li>
    </ul>
@stop

@section('head')
    <style>
        .page-header {
            display: none;
        }
    </style>
@stop

@section('menu')
    @include('ManageOrganiser.Partials.Sidebar')
@stop

@section('page_header')

@stop

@section('content')

    <div class="row">
        <div class="col-md-12">
            <ul class="nav nav-tabs">
                <li class="active">
                    <a href="#organiserSettings" data-toggle="tab">Organiser Settings</a>
                </li>
                <li>
                    <a href="#OrganiserPageDesign" data-toggle="tab">Organiser Page Design</a>
                </li>
            </ul>
            <div class="tab-content panel">
                <div class="tab-pane active" id="organiserSettings">
                    {!! Form::model($organiser, array('url' => route('postEditOrganiser', ['organiser_id' => $organiser->id]), 'class' => 'ajax')) !!}

                        <div class="form-group">
                            {!! Form::label('name', 'Organiser Name', array('class'=>'required control-label ')) !!}
                            {!!  Form::text('name', Input::old('name'),
                                                    array(
                                                    'class'=>'form-control'
                                                    ))  !!}
                        </div>
                        <div class="form-group">
                            {!! Form::label('email', 'Organiser Email', array('class'=>'control-label required')) !!}
                            {!!  Form::text('email', Input::old('email'),
                                                    array(
                                                    'class'=>'form-control ',
                                                    'placeholder'=>''
                                                    ))  !!}
                        </div>
                        <div class="form-group">
                            {!! Form::label('about', 'Organiser Description', array('class'=>'control-label ')) !!}
                            {!!  Form::textarea('about', Input::old('about'),
                                                    array(
                                                    'class'=>'form-control ',
                                                    'placeholder'=>'',
                                                    'rows' => 4
                                                    ))  !!}
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    {!! Form::label('facebook', 'Organiser Facebook', array('class'=>'control-label ')) !!}

                                    <div class="input-group">
                                        <span style="background-color: #eee;" class="input-group-addon">facebook.com/</span>
                                        {!!  Form::text('facebook', Input::old('facebook'),
                                                        array(
                                                        'class'=>'form-control ',
                                                        'placeholder'=>'Username'
                                                        ))  !!}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    {!! Form::label('twitter', 'Organiser Twitter', array('class'=>'control-label ')) !!}

                                    <div class="input-group">
                                        <span style="background-color: #eee;" class="input-group-addon">twitter.com/</span>
                                        {!!  Form::text('twitter', Input::old('twitter'),
                                                 array(
                                                 'class'=>'form-control ',
                                                 'placeholder'=>'Username'
                                                 ))  !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                        @if(is_file($organiser->logo_path))
                            <div class="form-group">
                                {!! Form::label('current_logo', 'Current Logo', array('class'=>'control-label ')) !!}

                                <div class="thumbnail">
                                    {!!HTML::image($organiser->logo_path)!!}
                                    {!! Form::label('remove_current_image', 'Delete Logo?', array('class'=>'control-label ')) !!}
                                    {!! Form::checkbox('remove_current_image') !!}
                                </div>
                            </div>
                        @endif
                        <div class="form-group">
                            {!!  Form::labelWithHelp('organiser_logo', 'Organiser Logo', array('class'=>'control-label '),
                                'We recommend a square image, as this will look best on printed tickets and event pages.')  !!}l
                            {!!Form::styledFile('organiser_logo')!!}
                        </div>
                    <div class="modal-footer">
                        {!! Form::submit('Save Organiser', ['class'=>"btn btn-success"]) !!}
                    </div>
                    {!! Form::close() !!}
                </div>
                <div class="tab-pane" id="OrganiserPageDesign">
                    Coming soon.
                </div>
            </div>
        </div>
    </div>
@stop


