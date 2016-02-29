<div role="dialog" id="{{$modal_id}}" class="modal fade " style="display: none;">
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
                <button type="button" class="close" data-dismiss="modal">×</button>
                <h3 class="modal-title">
                    <i class="ico-cogs"></i>
                    Account</h3>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <!-- tab -->
                        <ul class="nav nav-tabs">
                            <li class="active"><a href="#general" data-toggle="tab">General</a></li>
                            <li><a href="#payment" data-toggle="tab">Payment</a></li>
                            <li class=""><a href="#users" data-toggle="tab">Users</a></li>
                        </ul>
                        <div class="tab-content panel ">
                            <div class="tab-pane active" id="general">
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
                                            {!!Form::submit('Save Account Details', ['class' => 'btn btn-success pull-right'])!!}

                                        </div>
                                    </div>
                                </div>


                                {!!Form::close()!!}
                            </div>
                            <div class="tab-pane " id="payment">


                                @if(Utils::isAttendize())

                                    <p>
                                        We use <a href="https://stripe.com">Stripe</a> to handle payments. Stripe allows you
                                        accept payment in 139 currencies.
                                    </p>

                                    <p>
                                        If you don't have an existing account with Stripe you will be prompted to create
                                        one. The process will only take a couple of minutes.
                                    </p>

                                    @if($account->stripe_access_token)
                                        <div class="alert alert-info">
                                            You have connected your Stripe account. If you would like to connect a new
                                            account you can do so using the button below.
                                        </div>
                                    @endif
                                    <a target="__blank"
                                       href="https://connect.stripe.com/oauth/authorize?response_type=code&client_id={{$_ENV['STRIPE_APP_CLIENT_ID']}}&scope=read_write&state={{Auth::user()->id}}">
                                        <img src="{{asset('assets/images/stripe-connect-blue.png')}}"
                                             alt="Connect with Stripe"/>
                                    </a>

                                @else

                                    {!! Form::model($account, array('url' => route('postEditAccountPayment'), 'class' => 'ajax ')) !!}
                                    <div class="alert alert-info">
                                        <p>
                                            We use <a href="https://stripe.com">Stripe</a> to handle payments. Stripe allows you
                                            accept payment in 139 currencies. Once you have created Stripe account you can find you Secret Key and Publishable key here: <a href="https://dashboard.stripe.com/account/apikeys" target="_blank">https://dashboard.stripe.com/account/apikeys</a>.
                                        </p>

                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">


                                                {!! Form::label('stripe_secret_key', 'Stripe Secret Key', array('class'=>'control-label ')) !!}
                                                {!!  Form::text('stripe_secret_key', Input::old('stripe_secret_key'),
                                            array(
                                            'class'=>'form-control'
                                            ))  !!}
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">


                                                {!! Form::label('stripe_publishable_key', 'Stripe Publishable Key', array('class'=>'control-label ')) !!}
                                                {!!  Form::text('stripe_publishable_key', Input::old('stripe_publishable_key'),
                                            array(
                                            'class'=>'form-control'
                                            ))  !!}
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="panel-footer">
                                                {!!Form::submit('Save Payment Details', ['class' => 'btn btn-success pull-right'])!!}

                                            </div>
                                        </div>
                                    </div>


                                    {!!Form::close()!!}

                                @endif


                            </div>
                            <div class="tab-pane " id="users">
                                {!! Form::open(array('url' => route('postInviteUser'), 'class' => 'ajax ')) !!}

                                <div class="table-responsive">
                                    <table class="table table-bordered">

                                        <tbody>
                                        @foreach($account->users as $user)
                                            <tr class=''>
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
                                        <tr class=''>
                                            <td colspan="3">
                                                <div class="input-group">
                                                    {!! Form::text('email', '',  ['class' => 'form-control', 'placeholder' => 'Email Address']) !!}
                                                    <span class="input-group-btn">
                                                          {!!Form::submit('Add User', ['class' => 'btn btn-primary'])!!}
                                                    </span>
                                                </div>
                                                <span class="help-block">
                                                    Added users will receive instructions complete their registration.
                                                </span>
                                            </td>

                                        </tr>


                                        </tbody>
                                    </table>
                                </div>
                                {!! Form::close() !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>