@extends('Shared.Layouts.MasterWithoutMenus')

@section('title')
    Login
@stop


@section('content')


    {!! Form::open(array('url' => 'login')) !!}
    <div class="row">
        <div class="col-md-4 col-md-offset-4">

            <div class="panel">


                <div class="panel-body">

                    <div class="logo">
                        {!!HTML::image('assets/images/logo-100x100-lightBg.png')!!}
                    </div>

                    @if(Input::get('failed'))
                        <h4 class="text-danger mt0">Whoops! </h4>
                        <ul class="list-group">
                            <li class='list-group-item'>Please check your details and try again.</li>
                        </ul>
                    @endif

                    <div class="form-group">
                        {!! Form::label('email', 'Email') !!}
                        {!! Form::text('email', null, ['class' => 'form-control']) !!}
                    </div>
                    <div class="form-group">

                        {!! Form::label('password', 'Password') !!}
                        (<a class="forgotPassword" href="{{route('forgotPassword')}}">Forgot password?</a>)
                        {!! Form::password('password',  ['class' => 'form-control']) !!}
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-block btn-success">Login</button>
                    </div>

                    <div class="signup">
                        <span>Don't have any account? <a class="semibold" href="/signup">Sign up</a></span>
                    </div>
                </div>


            </div>

        </div>
    </div>
    {!! Form::close() !!}
@stop
