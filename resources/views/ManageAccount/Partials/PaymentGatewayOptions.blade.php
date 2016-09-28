<script>
    $(function() {
        $('.payment_gateway_options').hide();
        $('#gateway_{{$account->payment_gateway_id}}').show();

        $('.gateway_selector').on('change', function(e) {
            $('.payment_gateway_options').hide();
            $('#gateway_' + $(this).val()).fadeIn();
        });

    });
</script>

{!! Form::model($account, array('url' => route('postEditAccountPayment'), 'class' => 'ajax ')) !!}
<div class="form-group">
    {!! Form::label('payment_gateway_id', 'Default Payment Gateway', array('class'=>'control-label ')) !!}
    {!! Form::select('payment_gateway_id', $payment_gateways, $account->payment_gateway_id, ['class' => 'form-control gateway_selector']) !!}
</div>

{{--Stripe--}}
<section class="payment_gateway_options" id="gateway_{{config('attendize.payment_gateway_stripe')}}">
    <h4>Stripe Settings</h4>
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                {!! Form::label('stripe[apiKey]', 'Stripe Secret Key', array('class'=>'control-label ')) !!}
                {!! Form::text('stripe[apiKey]', $account->getGatewayConfigVal(config('attendize.payment_gateway_stripe'), 'apiKey'),[ 'class'=>'form-control'])  !!}
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                {!! Form::label('publishableKey', 'Stripe Publishable Key', array('class'=>'control-label ')) !!}
                {!! Form::text('stripe[publishableKey]', $account->getGatewayConfigVal(config('attendize.payment_gateway_stripe'), 'publishableKey'),[ 'class'=>'form-control'])  !!}
            </div>
        </div>
    </div>
</section>

{{--Paypal--}}
<section class="payment_gateway_options"  id="gateway_{{config('attendize.payment_gateway_paypal')}}">
    <h4>PayPal Settings</h4>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                {!! Form::label('paypal[username]', 'PayPal Username', array('class'=>'control-label ')) !!}
                {!! Form::text('paypal[username]', $account->getGatewayConfigVal(config('attendize.payment_gateway_paypal'), 'username'),[ 'class'=>'form-control'])  !!}
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                {!! Form::label('paypal[password]', 'PayPal Password', ['class'=>'control-label ']) !!}
                {!! Form::text('paypal[password]', $account->getGatewayConfigVal(config('attendize.payment_gateway_paypal'), 'password'),[ 'class'=>'form-control'])  !!}
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                {!! Form::label('paypal[signature]', 'PayPal Signature', array('class'=>'control-label ')) !!}
                {!! Form::text('paypal[signature]', $account->getGatewayConfigVal(config('attendize.payment_gateway_paypal'), 'signature'),[ 'class'=>'form-control'])  !!}
            </div>
        </div>
    </div>
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    {!! Form::label('paypal[brandName]', 'Branding Name', array('class'=>'control-label ')) !!}
                    {!! Form::text('paypal[brandName]', $account->getGatewayConfigVal(config('attendize.payment_gateway_paypal'), 'brandName'),[ 'class'=>'form-control'])  !!}
                    <div class="help-block">
                        This is the name buyers will see when checking out. Leave this blank if you want the event organiser's name to be used.
                    </div>
                </div>
            </div>
        </div>


</section>

{{--BitPay--}}
<section class="payment_gateway_options" id="gateway_{{config('attendize.payment_gateway_bitpay')}}">
    <h4>BitPay Settings</h4>
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                {!! Form::label('bitpay[apiKey]', 'BitPay Api Key', array('class'=>'control-label ')) !!}
                {!! Form::text('bitpay[apiKey]', $account->getGatewayConfigVal(config('attendize.payment_gateway_bitpay'), 'apiKey'),[ 'class'=>'form-control'])  !!}
            </div>
        </div>
    </div>
</section>


{{--Coinbase--}}
<section class="payment_gateway_options"  id="gateway_{{config('attendize.payment_gateway_coinbase')}}">
    <h4>Coinbase Settings</h4>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                {!! Form::label('coinbase[apiKey]', 'API Key', array('class'=>'control-label ')) !!}
                {!! Form::text('coinbase[apiKey]', $account->getGatewayConfigVal(config('attendize.payment_gateway_coinbase'), 'apiKey'),[ 'class'=>'form-control'])  !!}
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                {!! Form::label('coinbase[secret]', 'Secret Code', ['class'=>'control-label ']) !!}
                {!! Form::text('coinbase[secret]', $account->getGatewayConfigVal(config('attendize.payment_gateway_coinbase'), 'secret'),[ 'class'=>'form-control'])  !!}
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                {!! Form::label('coinbase[accountId]', 'Account ID', array('class'=>'control-label ')) !!}
                {!! Form::text('coinbase[accountId]', $account->getGatewayConfigVal(config('attendize.payment_gateway_coinbase'), 'accountId'),[ 'class'=>'form-control'])  !!}
            </div>
        </div>
    </div>


</section>

{{--BDO MIGS--}}
<section class="payment_gateway_options"  id="gateway_{{config('attendize.payment_gateway_migs')}}">
    <h4>Mastercard Internet Gateway Service Settings</h4>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                {!! Form::label('migs[merchantAccessCode]', 'Merchant Access Code', array('class'=>'control-label ')) !!}
                {!! Form::text('migs[merchantAccessCode]', $account->getGatewayConfigVal(config('attendize.payment_gateway_migs'), 'merchantAccessCode'),[ 'class'=>'form-control'])  !!}
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                {!! Form::label('migs[merchantId]', 'Merchant ID', ['class'=>'control-label ']) !!}
                {!! Form::text('migs[merchantId]', $account->getGatewayConfigVal(config('attendize.payment_gateway_migs'), 'merchantId'),[ 'class'=>'form-control'])  !!}
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                {!! Form::label('migs[secureHash]', 'Secure Hash Code', array('class'=>'control-label ')) !!}
                {!! Form::text('migs[secureHash]', $account->getGatewayConfigVal(config('attendize.payment_gateway_migs'), 'secureHash'),[ 'class'=>'form-control'])  !!}
            </div>
        </div>
    </div>


</section>




<div class="row">
    <div class="col-md-12">
        <div class="panel-footer">
            {!! Form::submit('Save Payment Details', ['class' => 'btn btn-success pull-right']) !!}
        </div>
    </div>
</div>


{!! Form::close() !!}