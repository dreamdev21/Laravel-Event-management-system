<div role="dialog"  class="modal fade" style="display: none;">
    <style>
        .account_settings .modal-body {
            border: 0;
            margin-bottom: -35px;
            border: 0;
            padding: 0;
        }

        .account_settings .panel-footer {
            margin: -15px;
            margin-top: 20px;
        }

        .account_settings .panel {
            margin-bottom: 0;
            border: 0;
        }
    </style>
    <div class="modal-dialog account_settings">
        <div class="modal-content">
            <div class="modal-header text-center">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h3 class="modal-title">
                    <i class="ico-cogs"></i>
                    Account</h3>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <!-- tab -->
                        <ul class="nav nav-tabs">
                            <li class="active"><a href="#general_account" data-toggle="tab">General</a></li>
                            <li><a href="#payment_account" data-toggle="tab">Payment</a></li>
                            <li><a href="#users_account" data-toggle="tab">Users</a></li>
                            <li><a href="#about" data-toggle="tab">About</a></li>
                        </ul>
                        <div class="tab-content panel">
                            <div class="tab-pane active" id="general_account">
                                {!! Form::model($account, array('url' => route('postEditAccount'), 'class' => 'ajax ')) !!}
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            {!! Form::label('first_name', 'First Name', array('class'=>'control-label required')) !!}
                                            {!!  Form::text('first_name', Input::old('first_name'),
                                        array(
                                        'class'=>'form-control'
                                        ))  !!}
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            {!! Form::label('last_name', 'Last Name', array('class'=>'control-label required')) !!}
                                            {!!  Form::text('last_name', Input::old('last_name'),
                                        array(
                                        'class'=>'form-control'
                                        ))  !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            {!! Form::label('email', 'Email', array('class'=>'control-label required')) !!}
                                            {!!  Form::text('email', Input::old('email'),
                                        array(
                                        'class'=>'form-control'
                                        ))  !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            {!! Form::label('timezone_id', 'Timezone', array('class'=>'control-label required')) !!}
                                            {!! Form::select('timezone_id', $timezones, $account->timezone_id, ['class' => 'form-control']) !!}
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            {!! Form::label('currency_id', 'Default Currency', array('class'=>'control-label required')) !!}
                                            {!! Form::select('currency_id', $currencies, $account->currency_id, ['class' => 'form-control']) !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="panel-footer">
                                            {!! Form::submit('Save Account Details', ['class' => 'btn btn-success pull-right']) !!}
                                        </div>
                                    </div>
                                </div>

                                {!! Form::close() !!}
                            </div>
                            <div class="tab-pane " id="payment_account">

                               @include('ManageAccount.Partials.PaymentGatewayOptions')

                            </div>
                            <div class="tab-pane" id="users_account">
                                {!! Form::open(array('url' => route('postInviteUser'), 'class' => 'ajax ')) !!}

                                <div class="table-responsive">
                                    <table class="table table-bordered">

                                        <tbody>
                                        @foreach($account->users as $user)
                                            <tr>
                                                <td>
                                                    {{$user->first_name}} {{$user->last_name}}
                                                </td>
                                                <td>
                                                    {{$user->email}}
                                                </td>
                                                <td>
                                                    {!! $user->is_parent ? '<span class="label label-info">Account owner</span>' : '' !!}
                                                </td>

                                            </tr>
                                        @endforeach
                                        <tr>
                                            <td colspan="3">
                                                <div class="input-group">
                                                    {!! Form::text('email', '',  ['class' => 'form-control', 'placeholder' => 'Email Address']) !!}
                                                    <span class="input-group-btn">
                                                          {!!Form::submit('Add User', ['class' => 'btn btn-primary'])!!}
                                                    </span>
                                                </div>
                                                <span class="help-block">
                                                    Added users will receive further instruction via email.
                                                </span>
                                            </td>

                                        </tr>

                                        </tbody>
                                    </table>
                                </div>
                                {!! Form::close() !!}
                            </div>
                            <div class="tab-pane " id="about">
                                <h4>
                                    Version Information
                                </h4>
                                <p>
                                    @if($version_info['is_outdated'])
                                        Your version (<b>{{ $version_info['installed'] }}</b>) of Attendize is out of date. The latest version (<b>{{ $version_info['latest'] }}</b>) can be <a href="https://attendize.com/documentation.php#download" target="_blank">downloaded here</a>.
                                    @else
                                        Your Attendize version (<b>{{ $version_info['installed'] }}</b>) is up to date!
                                    @endif
                                </p>

                                <h4>
                                    Licence Information
                                </h4>
                                <p>
                                    Attendize is licenced under the <b><a target="_blank"
                                                                          href="https://tldrlegal.com/license/attribution-assurance-license-(aal)#summary">Attribution Assurance Licence (AAL)</a></b>. This licence requires the <b>'Powered
                                        By Attendize'</b> notice to be kept in place on any Attendize installation. If you wish to remove references to Attendize you must purchase one of the white-label licences <b><a target="_blank" href="https://attendize.com/licence.php">listed here</a></b>.
                                </p>
                                <h4>
                                    Open-source Software
                                </h4>
                                <p>
                                    Attendize is built using many fantastic open-source libraries. You can see an overview of these on <b><a href="https://libraries.io/github/Attendize/Attendize?ref=Attendize_About_Page" target="_blank">libraries.io</a></b>.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>