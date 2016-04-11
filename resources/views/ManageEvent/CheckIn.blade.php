<!doctype html>
<html>
    <head>
        <title>
            Check In: {{$event->title}}
        </title>

       {!! HTML::style('assets/stylesheet/application.css') !!}
       {!! HTML::style('assets/stylesheet/check_in.css') !!}
       {!! HTML::script('vendor/jquery/jquery.js') !!}

        @include('Shared/Layouts/ViewJavascript')
        <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0">

        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
          <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
          <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
        <![endif]-->

        <script>
            $(function() {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-Token': "<?php echo csrf_token() ?>"
                    }
                });
            });
        </script>
    </head>
    <body>
        <header>
            <div class="menuToggle hide">
                <i class="ico-menu"></i>
            </div>
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <div class="attendee_input_wrap">
                            <div class="input-group">
                                  <span class="input-group-btn">
                                 <button title="Scan QR Code" class="btn btn-default qr_search" type="button"><i class="ico-qrcode"></i> </button>
                                </span>
                                {!!  Form::text('attendees_q', null, [
                            'class' => 'form-control attendee_search',
                                    'id' => 'search',
                                    'placeholder' => 'Search by Attendee Name, Order Reference, Attendee Reference... '
                        ])  !!}


                            </div>

                            <span class="clearSearch ico-cancel"></span>
                        </div>
                    </div>
                </div>
            </div>
        </header>


        <section class="attendeeList">
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <div class="attendee_list">
                            <h4 class="attendees_title">
                                All Attendees
                            </h4>
                            <ul class="list-group" id="attendee_list">
                                Loading Attendees...
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <footer class="hide">
            <div class="container">
                <div class="row">
                    <div class="col-md-12">

                    </div>
                </div>
            </div>
        </footer>

        {{--QR Modal--}}
        <div role="dialog" id="QrModal"  class="modal fade" style="display: none;">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-body">
                        @if(session()->has('success_message'))
                            <div class="container">
                                <div class="row">
                                    <div class="col-md-6 col-md-offset-3 col-xs-12">
                                        <div class="alert alert-success alert-dismissible text-center" role="alert">
                                            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                            <p><strong>Success</strong>: {{ session('success_message') }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif


                            <div id="ScanVideoOutter">
                            </div>
                            <div class="well" id="ScanResult"></div>
                        <canvas id="QrCanvas" width="800" height="600"></canvas>

                    </div>
                    <div class="modal-footer">
                        {!! Form::button('Close', ['class'=>"btn modal-close btn-danger",'data-dismiss'=>'modal']) !!}
                        <a class="btn btn-primary startScanner"  href="javascript:void(0);"><i class="fa fa-refresh"></i> Scan another ticket</a>

                    </div>
                </div><!-- /end modal content-->
            </div>
        </div>
        {{-- /END QR Modal--}}


        {!! HTML::script('vendor/qrcode-scan/llqrcode.js') !!}
        {!! HTML::script('assets/javascript/backend.js') !!}
        {!! HTML::script('assets/javascript/check_in.js') !!}

    </body>
</html>