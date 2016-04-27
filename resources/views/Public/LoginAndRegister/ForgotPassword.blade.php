@extends('Shared.Layouts.MasterWithoutMenus')

@section('title')
Forgot Password
@stop

@section('content')
    <div class="row">
        <div class="col-md-4 col-md-offset-4">

            {!! Form::open(array('url' => route('postForgotPassword'), 'class' => 'panel')) !!}

            <div class="panel-body">

                <div class="logo">
                   {!!HTML::image('assets/images/logo-dark.png')!!}
                </div>
                <h2>Forgot Password</h2>

                @if (Session::has('status'))
                <div class="alert alert-info">
                    An email with the password reset has been sent to your email.
                </div>
                @else

                @if(Session::has('error'))
                <h4 class="text-danger mt0">Whoops! </h4>
                <ul class="list-group">
                    <li class="list-group-item">{{Session::get('error')}}</li>
                </ul>
                @endif

                <div class="form-group">
                   {!! Form::label('email', 'Your Email') !!}
                   {!! Form::text('email', null, ['class' => 'form-control', 'autofocus' => true]) !!}
                </div>

                <div class="form-group nm">
                    <button type="submit" class="btn btn-block btn-success">Submit</button>
                </div>
                <div class="signup">
                    <a class="semibold" href="{{route('login')}}">
                        <i class="ico ico-arrow-left"></i> Back to login
                    </a>
                </div>
            </div>
            {!! Form::close() !!}
            @endif
        </div>
    </div>
@stop