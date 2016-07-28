@extends('Shared.Layouts.MasterWithoutMenus')

@section('title')
    Attendize Web Installer
@stop

@section('head')
    <style>
        .modal-header {
            background-color: transparent !important;
            color: #666 !important;
            text-shadow: none !important;;
        }
        .alert-success {
            background-color: #dff0d8 !important;
            border-color: #d6e9c6  !important;
            color: #3c763d  !important;
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

                    <h1>Attendize Setup</h1>


                    <h3>PHP Version Check</h3>
                    @if (version_compare(phpversion(), '5.5.9', '<'))
                        <div class="alert alert-warning">
                            Warning: The application requires PHP >= <b>5.5.9.</b> Your version is <b>{{phpversion()}}</b>
                        </div>
                    @else
                        <div class="alert alert-success">
                            Success: The application requires PHP >= <b>5.5.9.</b> and yours is <b>{{phpversion()}}</b>
                        </div>
                    @endif

                    <h3>Files &amp; Folders Check</h3>
                    @foreach($paths as $path)

                        @if(!File::isWritable($path))
                            <div class="alert alert-danger">
                                Warning: <b>{{$path}}</b> is not writable
                            </div>
                        @else
                            <div class="alert alert-success">
                                Success: <b>{{$path}}</b> is writable
                            </div>
                        @endif

                    @endforeach

                    <h3>PHP Requirements Check</h3>
                    @foreach($requirements as $requirement)

                        @if(!extension_loaded($requirement))
                            <div class="alert alert-danger">
                                Error: <b>{{$requirement}}</b> extension is not loaded
                            </div>
                        @else
                            <div class="alert alert-success">
                                Success: <b>{{$requirement}}</b> extension is loaded
                            </div>
                        @endif

                    @endforeach

                    <h3>PHP Optional Requirements Check</h3>

                    @foreach($optional_requirements as $optional_requirement)

                        @if(!extension_loaded($optional_requirement))
                            <div class="alert alert-warning">
                                Warning: <b>{{$optional_requirement}}</b> extension is not loaded
                            </div>
                        @else
                            <div class="alert alert-success">
                                Success: <b>{{$optional_requirement}}</b> extension is loaded
                            </div>
                        @endif

                    @endforeach

                    {!! Form::open(array('url' => route('postInstaller'), 'class' => 'installer_form')) !!}

                    <h3>App Settings</h3>

                    <div class="form-group">
                        {!! Form::label('app_url', 'Application URL', array('class'=>'required control-label ')) !!}
                        {!!  Form::text('app_url', Input::old('app_url'),
                                    array(
                                    'class'=>'form-control',
                                    'placeholder' => 'http://www.myticketsite.com'
                                    ))  !!}
                    </div>

                    <h3>Database Settings</h3>

                    <div class="form-group">
                        {!! Form::label('database_type', 'Database Type', array('class'=>'required control-label ')) !!}
                        {!!  Form::select('database_type', array(
                                  'pgsql' => "Postgres",
                                  'mysql' => "MySQL",
                                    ), Input::old('database_type'),
                                    array(
                                    'class'=>'form-control'
                                    ))  !!}
                    </div>

                    <div class="form-group">
                        {!! Form::label('database_host', 'Database Host', array('class'=>'control-label required')) !!}
                        {!!  Form::text('database_host', Input::old('database_host'),
                                    array(
                                    'class'=>'form-control ',
                                    'placeholder'=>''
                                    ))  !!}
                    </div>

                    <div class="form-group">
                        {!! Form::label('database_name', 'Database Name', array('class'=>'required control-label ')) !!}
                        {!!  Form::text('database_name', Input::old('database_name'),
                                    array(
                                    'class'=>'form-control'
                                    ))  !!}
                    </div>

                    <div class="form-group">
                        {!! Form::label('database_username', 'Database Username', array('class'=>'control-label required')) !!}
                        {!!  Form::text('database_username', Input::old('database_username'),
                                    array(
                                    'class'=>'form-control ',
                                    'placeholder'=>'',
                                    ))  !!}
                    </div>
                    <div class="form-group">
                        {!! Form::label('database_password', 'Database Password', array('class'=>'control-label ')) !!}
                        {!!  Form::text('database_password', Input::old('database_password'),
                                    array(
                                    'class'=>'form-control ',
                                    'placeholder'=>'',
                                    ))  !!}
                    </div>

                    <div class="form-group">
                        <script>
                            $(function () {
                                $.ajaxSetup({
                                    headers: {
                                        'X-CSRF-Token': "{{csrf_token()}}"
                                    }
                                });

                                $('.test_db').on('click', function (e) {

                                    var url = $(this).attr('href');

                                    $.post(url, $(".installer_form").serialize(), function (data) {
                                        if (data.status === 'success') {
                                            alert('Success! Database settings are working!');
                                        } else {
                                            alert('Unable to connect. Please check your settings.')
                                        }
                                    }, 'json').fail(function (data) {
                                        var returned = $.parseJSON(data.responseText);
                                        console.log(returned.error);
                                        alert('Unable to connect. Please check your settings.\n\n' + 'Error Type: ' + returned.error.type + '\n' + 'Error Message: ' + returned.error.message);
                                    });

                                    e.preventDefault();
                                });
                            });
                        </script>
                        <a href="{{route('postInstaller',['test' => 'db'])}}" class="test_db">
                            Test Database Connection
                        </a>
                    </div>

                    <h3>Email Settings</h3>

                    <div class="form-group">
                        {!! Form::label('mail_from_address', 'Mail From Address', array('class'=>' control-label required')) !!}
                        {!!  Form::text('mail_from_address', Input::old('mail_from_address'),
                                    array(
                                    'class'=>'form-control'
                                    ))  !!}
                    </div>
                    <div class="form-group">
                        {!! Form::label('mail_from_name', 'Mail From Name', array('class'=>' control-label required')) !!}
                        {!!  Form::text('mail_from_name', Input::old('mail_from_name'),
                                    array(
                                    'class'=>'form-control'
                                    ))  !!}
                    </div>
                    <div class="form-group">
                        {!! Form::label('mail_driver', 'Mail Driver', array('class'=>' control-label required')) !!}
                        {!!  Form::text('mail_driver', Input::old('mail_driver'),
                                    array(
                                    'class'=>'form-control ',
                                    'placeholder' => 'mail'
                                    ))  !!}
                        <div class="help-block">
                            To use PHP's <a target="_blank" href="http://php.net/manual/en/function.mail.php">mail</a>
                            feature enter <b>mail</b> in this box and leave the below fields empty.
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('mail_port', 'Mail Port', array('class'=>' control-label ')) !!}
                        {!!  Form::text('mail_port', Input::old('mail_port'),
                                    array(
                                    'class'=>'form-control'
                                    ))  !!}
                    </div>
                    <div class="form-group">
                        {!! Form::label('mail_encryption', 'Mail Encryption', array('class'=>' control-label ')) !!}
                        {!!  Form::text('mail_encryption', Input::old('mail_encryption'),
                                    array(
                                    'class'=>'form-control'
                                    ))  !!}
                    </div>
                    <div class="form-group">
                        {!! Form::label('mail_host', 'Mail Host', array('class'=>' control-label ')) !!}
                        {!!  Form::text('mail_host', Input::old('mail_host'),
                                    array(
                                    'class'=>'form-control'
                                    ))  !!}
                    </div>
                    <div class="form-group">
                        {!! Form::label('mail_username', 'Mail Username', array('class'=>' control-label ')) !!}
                        {!!  Form::text('mail_username', Input::old('mail_username'),
                                    array(
                                    'class'=>'form-control'
                                    ))  !!}
                    </div>
                    <div class="form-group">
                        {!! Form::label('mail_password', 'Mail Password', array('class'=>' control-label ')) !!}
                        {!!  Form::text('mail_password', Input::old('mail_password'),
                                    array(
                                    'class'=>'form-control'
                                    ))  !!}
                    </div>

                    <div class="well">
                        <p>
                            Installation may make take several minutes to complete. Once you click '<b>Install Attendize</b>' the config settings will be written to this file: <b>{{base_path('.env')}}</b>. You can manually change these settings in the future by editing this file.
                        </p>
                        <p>
                            If the install fails be sure to check the log file in <b>{{storage_path('logs')}}</b>. If there are no errors in the log files also <b>check other log files on your server</b>.
                        </p>
                        <p>
                            If you are using shared hosting please ask your host if they support the Attendize requirements before requesting support.
                        </p>
                        <p>
                            If you still need help you can email us at <a href="mailto:help@attendize.com" target="_blank">help@attendize.com</a>. Please include as much detail as possible, including any errors in the log file.
                        </p>
                        <p>
                            Please also  <a style="text-decoration: underline;" target="_blank" href="https://attendize.com/licence.php?from_installer">read the licence</a> before installing Attendize.
                        </p>
                    </div>

                    {!! Form::submit('Install Attendize', ['class'=>" btn-block btn btn-success"]) !!}
                    {!! Form::close() !!}

                </div>
            </div>
        </div>
    </div>
@stop
