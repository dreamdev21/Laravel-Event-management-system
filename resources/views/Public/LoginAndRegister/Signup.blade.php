@extends('Shared.Layouts.MasterWithoutMenus')

@section('title')
Sign Up
@stop


@section('content')

<div class="row">
    <div class="col-lg-4 col-lg-offset-4">
       {!! Form::open(array('url' => 'signup', 'class' => 'panel')) !!}


        <div class="panel-body">
            
            <div class="logo">
               {!!HTML::image('assets/images/logo-100x100-lightBg.png')!!}
            </div>

            @if(Input::get('first_run'))
                <div class="alert alert-info">
                    You're almost there. Just create a user account and you're ready to go.
                </div>
                @endif
            
        @if($errors->has())
        <h4 class="text-danger mt0">Whoops! </h4>
        <ul class="list-group">
            @foreach ($errors->all() as $error)
                <li class='list-group-item'>{{ $error }}</li>
            @endforeach
        </ul>
        @endif
            
            <div class="form-group">
               {!! Form::label('first_name', 'First Name') !!}
               {!! Form::text('first_name', null, ['class' => 'form-control']) !!}
            </div>
            <div class="form-group">
               {!! Form::label('last_name', 'Last Name') !!}
               {!! Form::text('last_name', null, ['class' => 'form-control']) !!}
            </div>
            <div class="form-group">
               {!! Form::label('email', 'Email') !!}
               {!! Form::text('email', null, ['class' => 'form-control']) !!}
            </div>
            <div class="form-group">
               {!! Form::label('password', 'Password') !!}
               {!! Form::password('password',  ['class' => 'form-control']) !!}
            </div>
            <div class="form-group">
               {!! Form::label('password_confirmation', 'Password again') !!}
               {!! Form::password('password_confirmation',  ['class' => 'form-control']) !!}
            </div>
        
            <div class="form-group">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="checkbox custom-checkbox">  
                           {!! Form::checkbox('terms_agreed', Input::old('terms_agreed'), false, ['id' => 'terms_agreed']) !!}
                           {!! Form::rawLabel('terms_agreed', '&nbsp;&nbsp;I agree to <a target="_blank" href="'.route('termsAndConditions').'"> Terms & Conditions </a>') !!}
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group ">
               {!! Form::submit('Sign Up', array('class'=>"btn btn-block btn-success")) !!}
            </div>

            @if(Utils::isAttendize())
        <div class="signup">
            <span>Already have account? <a class="semibold" href="/login">Sign In</a></span>
        </div>
                @endif
        </div>
       {!! Form::close() !!}
    </div>
</div>

@stop
