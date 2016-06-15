@extends('Shared.Layouts.MasterWithoutMenus')

@section('title')
Sign Up
@stop

@section('content')
    <div class="row">
        <div class="col-md-7 col-md-offset-2">
            {!! Form::open(array('url' => 'signup', 'class' => 'panel')) !!}
            <div class="panel-body">
                <div class="logo">
                   {!! HTML::image('assets/images/logo-dark.png') !!}
                </div>
                <h2>Sign up</h2>

                @if(Input::get('first_run'))
                    <div class="alert alert-info">
                        You're almost there. Just create a user account and you're ready to go.
                    </div>
                @endif

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group {{ ($errors->has('first_name')) ? 'has-error' : '' }}">
                            {!! Form::label('first_name', 'First Name', ['class' => 'control-label required']) !!}
                            {!! Form::text('first_name', null, ['class' => 'form-control']) !!}
                            @if($errors->has('first_name'))
                                <p class="help-block">{{ $errors->first('first_name') }}</p>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group {{ ($errors->has('last_name')) ? 'has-error' : '' }}">
                            {!! Form::label('last_name', 'Last Name', ['class' => 'control-label']) !!}
                            {!! Form::text('last_name', null, ['class' => 'form-control']) !!}
                            @if($errors->has('last_name'))
                                <p class="help-block">{{ $errors->first('last_name') }}</p>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="form-group {{ ($errors->has('email')) ? 'has-error' : '' }}">
                    {!! Form::label('email', 'Email', ['class' => 'control-label required']) !!}
                    {!! Form::text('email', null, ['class' => 'form-control']) !!}
                    @if($errors->has('email'))
                        <p class="help-block">{{ $errors->first('email') }}</p>
                    @endif
                </div>
                <div class="form-group {{ ($errors->has('password')) ? 'has-error' : '' }}">
                    {!! Form::label('password', 'Password', ['class' => 'control-label required']) !!}
                    {!! Form::password('password',  ['class' => 'form-control']) !!}
                    @if($errors->has('password'))
                        <p class="help-block">{{ $errors->first('password') }}</p>
                    @endif
                </div>
                <div class="form-group {{ ($errors->has('password_confirmation')) ? 'has-error' : '' }}">
                    {!! Form::label('password_confirmation', 'Password again', ['class' => 'control-label required']) !!}
                    {!! Form::password('password_confirmation',  ['class' => 'form-control']) !!}
                    @if($errors->has('password_confirmation'))
                        <p class="help-block">{{ $errors->first('password_confirmation') }}</p>
                    @endif
                </div>

                @if($is_attendize)
                <div class="form-group {{ ($errors->has('terms_agreed')) ? 'has-error' : '' }}">
                    <div class="checkbox custom-checkbox">
                        {!! Form::checkbox('terms_agreed', Input::old('terms_agreed'), false, ['id' => 'terms_agreed']) !!}
                        {!! Form::rawLabel('terms_agreed', '&nbsp;&nbsp;I agree to <a target="_blank" href="'.route('termsAndConditions').'"> Terms & Conditions </a>') !!}
                        @if ($errors->has('terms_agreed'))
                            <p class="help-block">{{ $errors->first('terms_agreed') }}</p>
                        @endif
                    </div>
                </div>
                @endif

                <div class="form-group ">
                   {!! Form::submit('Sign Up', array('class'=>"btn btn-block btn-success")) !!}
                </div>

                @if($is_attendize)
                    <div class="signup">
                        <span>Already have account? <a class="semibold" href="/login">Sign In</a></span>
                    </div>
                @endif
            </div>
            {!! Form::close() !!}
        </div>
    </div>
@stop
