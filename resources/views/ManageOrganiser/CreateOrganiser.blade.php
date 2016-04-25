@extends('Shared.Layouts.MasterWithoutMenus')

@section('title')
    Create Organiser
@stop

@section('head')
    <style>
        .modal-header {
            background-color: transparent !important;
            color: #666 !important;
            text-shadow: none !important;;
        }
    </style>
@stop

@section('content')
    <div class="row">
        <div class="col-md-7 col-md-offset-2">
            <div class="panel">
                <div class="panel-body">
                    <div class="logo">
                        {!!HTML::image('assets/images/logo-dark.png')!!}
                    </div>
                    <h2>Create Organiser</h2>

                    {!! Form::open(array('url' => route('postCreateOrganiser'), 'class' => 'ajax')) !!}
                    @if(@$_GET['first_run'] == '1')
                        <div class="alert alert-info">
                            Before you create events you'll need to create an organiser. An organiser is simply whoever is organising the event. It can be anyone, from a person to an organisation.
                        </div>
                    @endif

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                {!! Form::label('name', 'Organiser Name', array('class'=>'required control-label ')) !!}
                                {!!  Form::text('name', Input::old('name'),
                                            array(
                                            'class'=>'form-control'
                                            ))  !!}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {!! Form::label('email', 'Organiser Email', array('class'=>'control-label required')) !!}
                                {!!  Form::text('email', Input::old('email'),
                                            array(
                                            'class'=>'form-control ',
                                            'placeholder'=>''
                                            ))  !!}
                            </div>
                        </div>
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

                    <div class="form-group">
                        {!! Form::label('organiser_logo', 'Organiser Logo', array('class'=>'control-label ')) !!}
                        {!! Form::styledFile('organiser_logo') !!}
                    </div>

                    {!! Form::submit('Create Organiser', ['class'=>" btn-block btn btn-success"]) !!}
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
@stop

