@extends('Shared.Layouts.MasterWithoutMenus')

@section('title')
Reset Password
@stop

@section('content')
    <div class="row">
        <div class="col-md-4 col-md-offset-4">

           {!! Form::open(array('url' => route('postResetPassword'), 'class' => 'panel')) !!}

            <div class="panel-body">
                <div class="logo">
                   {!!HTML::image('assets/images/logo-dark.png')!!}
                </div>
                <h2>Reset Password</h2>
                @if (Session::has('status'))
                <div class="alert alert-info">
                    An email with the password reset has been sent to your email.
                </div>
                @else

                @if (count($errors) > 0)
                <div class="alert alert-danger">
                    <strong>Whoops!</strong> There were some problems with your input.<br><br>
                    <ul>
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <div class="form-group">
                    {!! Form::label('email', 'Your Email', ['class' => 'control-label']) !!}
                    {!! Form::text('email', null, ['class' => 'form-control', 'autofocus' => true]) !!}
                </div>
                <div class="form-group">
                    {!! Form::label('password', 'New Password', ['class' => 'control-label']) !!}
                    {!! Form::password('password',  ['class' => 'form-control']) !!}
                </div>
                <div class="form-group">
                    {!! Form::label('password_confirmation', 'Confirm Password', ['class' => 'control-label']) !!}
                    {!! Form::password('password_confirmation',  ['class' => 'form-control']) !!}
                </div>
                {!! Form::hidden('token',  $token) !!}
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