@extends('Shared.Layouts.MasterWithoutMenus')

@section('title', 'Login')

@section('content')
    {!! Form::open(array('url' => 'login')) !!}
    <div class="row">
        <div class="col-md-4 col-md-offset-4">
            <div class="panel">
                <div class="panel-body">
                    <div class="logo">
                        {!!HTML::image('assets/images/logo-dark.png')!!}
                    </div>

                    @if(Session::has('failed'))
                        <h4 class="text-danger mt0">Whoops! </h4>
                        <ul class="list-group">
                            <li class="list-group-item">Please check your details and try again.</li>
                        </ul>
                    @endif

                    <div class="form-group">
                        {!! Form::label('email', 'Email', ['class' => 'control-label']) !!}
                        {!! Form::text('email', null, ['class' => 'form-control', 'autofocus' => true]) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::label('password', 'Password', ['class' => 'control-label']) !!}
                        (<a class="forgotPassword" href="{{route('forgotPassword')}}" tabindex="-1">Forgot password?</a>)
                        {!! Form::password('password',  ['class' => 'form-control']) !!}
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-block btn-success">Login</button>
                    </div>

                    @if(Utils::isAttendize())
                    <div class="signup">
                        <span>Don't have any account? <a class="semibold" href="{{ url('signup') }}">Sign up</a></span>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    {!! Form::close() !!}
@stop
