<!doctype html>
<html>
    <head>
        <title>
            Check In: {{$event->title}}
        </title>

       {!! HTML::style('assets/stylesheet/application.css') !!}
       {!! HTML::script('vendor/jquery/jquery.js') !!}
       {!! HTML::style('assets/stylesheet/qrcode-check-in.css') !!}

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

        <style>

            ::-webkit-input-placeholder { /* WebKit browsers */
            }
            :-moz-placeholder { /* Mozilla Firefox 4 to 18 */
                opacity: 1;
            }
            ::-moz-placeholder { /* Mozilla Firefox 19+ */
                opacity: 1;
            }
            :-ms-input-placeholder { /* Internet Explorer 10+ */
            }


            body {
                background-color: #0384a6;
            }

            .attendeeList .container {
                background: #fff;
                border-radius: 2px;
                -webkit-box-shadow: 0 2px 2px #ccc;
                box-shadow: 0 2px 2px #ccc;
                margin-bottom: 50px;
                padding-top: 10px;
            }

            .attendeeList .container .attendee_list {
                padding: 15px;
                padding-top: 0;
            }

            .list-group-item:first-child {
                border-top-right-radius: 4px;
                border-top-left-radius: 4px;
            }

            .attendeeList .container .attendee_list .attendees_title {
                margin: 10px 0 20px 0;
                color: #666;
            }

            header {
                background-color: #FFF;
                padding: 10px 0;
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                z-index: 200;
            }

            header .menuToggle {
                position: absolute;
                left: 15px;
                color: #ccc;
                text-align: center;
                font-size: 30px;
            }

            section.attendeeList {
                margin-top: 80px;
            }

            .attendee_search {
                font-size: 16px;
                margin-bottom: 0;
                border: none;
                height: 40px;
            }

            .qr_search {
                height: 40px;
                background: #FFF;
                color: #000;
                font-size: 25px;
                border: none;
                border-right: 1px solid #999;
            }

            .clearSearch {
                position: absolute;
                top: 8px;
                right: 25px;
                font-size: 24px;
                cursor: pointer;
                display: none;
            }

            .at {
                position: relative;
                padding-left: 70px;
                cursor: pointer;
            }

            .at:active {
                background-color: #f9f9f9 !important;
            }

            .at .ci {
                position: absolute;
                left: 15px;
                top: 20px;
                color: white;
                border: none;
                border-radius: 150px;
                padding: 10px;
                width: 40px;
                height: 40px;
                line-height: 20px;
            }

            .at.arrived {
                background-color: #E6FFE7;
            }
            .at.not_arrived .ci {
                background-color: #fafafa;

            }
            .at.arrived .ci {
                background-color: #36F158;
            }

            footer {
                background-color: #333;
                height: 50px;
                position: fixed;
                bottom: 0;
                right: 0;
                left: 0;
            }

            /* Small Devices, Tablets */

            @media (min-width: 100px) and (max-width: 767px) {

                header {
                    border-bottom: 1px solid #ddd;
                }

                section.attendeeList {
                    margin-top: 60px;
                }

                section.attendeeList .container {
                    margin-bottom: 0;
                }

                section.attendeeList .col-md-12 {
                    padding: 0;
                }

                section.attendeeList .attendees_title {
                    padding-left: 10px;
                }

                section.attendeeList .container .attendee_list {
                    padding: 0;
                }

                .list-group-item:first-child {
                    border-top-right-radius: 0px;
                    border-top-left-radius: 0px;
                }

                .at {
                    position: relative;
                    padding-left: 70px;
                    cursor: pointer;
                    border-left: none;
                    border-right: none;
                    border-radius: 0;
                }
            }

        </style>

        <script>

            var workingAway = false;

            function populateAttendeeList(attendees) {
                $('#attendee_list').empty();

                if (jQuery.isEmptyObject(attendees)) {
                    $('#attendee_list').html('There are no results.');
                } else {
                    for (i in attendees) {
                        $('#attendee_list').append('<li id="a_' + attendees[i].id + '" class="' + (attendees[i].has_arrived == '1' ? 'arrived' : 'not_arrived') + ' at list-group-item" data-id="' + attendees[i].id + '">'
                                + 'Name: <b>' + attendees[i].first_name + ' '
                                + attendees[i].last_name
                                + ' </b><br>Reference: <b>' + attendees[i].reference + '</b>'
                                + ' <br>Ticket: <b>' + attendees[i].ticket + '</b>'
                                + '<a href="" class="ci btn btn-success"><i class="ico-checkmark"></i></a> '
                                + '</li>');
                    }
                }
            }
            function search() {
                var query_value = $('input#search').val();

                if(workingAway) {
                    return;
                }
                workingAway = true;

                $.ajax({
                    type: "POST",
                    url: "{{route('postCheckInSearch', ['event_id' => $event->id])}}",
                    data: {q: query_value},
                    cache: false,
                    error: function() {
                        workingAway = false;
                    },
                    success: function(attendees) {
                        if (query_value !== '') {
                            $('.attendees_title').html('Results for<b>: ' + query_value + '</b>');
                        } else {
                            $('.attendees_title').html('All Attendees');
                        }

                        workingAway = false;
                        populateAttendeeList(attendees);
                    }
                }, 'json');
                return false;
            }

            $(document).ready(function() {

                search();

                $('input#search').focus();

                $(document.body).on('click', '.at', function(e) {

                    if ($(this).hasClass('working')) {
                        return false;
                    }

                    var hasArrived = $(this).hasClass('arrived'),
                            attendeeId = $(this).data('id'),
                            checking = hasArrived ? 'out' : 'in',
                            $this = $(this),
                            $icon = $('i', $this);


                    $this.addClass("working");
                    $icon.removeClass('ico-checkmark').addClass('ico-busy');


                    $.ajax({
                        type: "POST",
                        url: "{{route('postCheckInAttendee', ['event_id' => $event->id])}}",
                        data: {
                            attendee_id: attendeeId,
                            has_arrived: hasArrived ? 1 : 0,
                            checking: checking
                        },
                        cache: false,
                        success: function(data) {

                            if (data.status === 'success' || data.status === 'error') {

                                if (data.checked === 'in') {
                                    $this.addClass('arrived').removeClass('not_arrived');
                                } else if (data.checked === 'out') {
                                    $this.removeClass('arrived').addClass('not_arrived');
                                }

                                if (data.status === 'error') {
                                    alert(data.message);
                                }

                            } else {
                                alert('An unknown error has occured. Please try again.');
                            }

                            $icon.addClass('ico-checkmark').removeClass('ico-busy');
                            $this.removeClass('working');
                        }
                    }, 'json');
                    e.preventDefault();
                });

                $('.clearSearch').on('click', function() {
                    $("input#search").val('').focus();
                    $(this).fadeOut();
                    search();
                });


                $('.qr_search').on('click', function(e) {
                    load();
                    $('#QrModal').modal('show');
                });

                $("input#search").on("keyup", function(e) {
                    clearTimeout($.data(this, 'timer'));
                    var search_string = $(this).val();
                    if (search_string === '') {
                        $('.attendees_title').html('All Attendees');
                        $(this).data('timer', setTimeout(search, 100));
                        $('.clearSearch').fadeOut();
                    } else {
                        $('.attendees_title').html('Results for<b>: ' + search_string + '</b>');
                        $(this).data('timer', setTimeout(search, 500));
                        $('.clearSearch').fadeIn();
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
                    <div class="modal-header text-center">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h3 class="modal-title">
                            <i class="ico-qrcode"></i>
                            Check-in
                        </h3>
                    </div>
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


                            <div id="outdiv">
                            </div>
                            <p><a onclick="event.preventDefault(); workingAway = false; load();" href="{{ Request::url() }}"><i class="fa fa-refresh"></i> Scan another ticket</a></p>
                            <div id="result"></div>
                        <canvas id="qr-canvas" width="800" height="600"></canvas>

                    </div>
                    <div class="modal-footer">
                        {!! Form::button('Close', ['class'=>"btn modal-close btn-danger",'data-dismiss'=>'modal']) !!}
                    </div>
                </div><!-- /end modal content-->
            </div>
        </div>
        {{-- /END QR Modal--}}


        {!! HTML::script('vendor/qrcode-scan/llqrcode.js') !!}

        {{--QR JS - THIS WILL BE MOVED--}}
        <script>
            // QRCODE reader Copyright 2011 Lazar Laszlo
            // http://www.webqr.com

            var workingAway = false;
            var gCtx = null;
            var gCanvas = null;
            var c=0;
            var stype=0;
            var gUM=false;
            var webkit=false;
            var moz=false;
            var v=null;

            var beepSound = new Audio('/mp3/beep.mp3');

            var vidhtml = '<video id="v" autoplay></video>';

            function initCanvas(w,h)
            {
                gCanvas = document.getElementById("qr-canvas");
                gCanvas.style.width = w + "px";
                gCanvas.style.height = h + "px";
                gCanvas.width = w;
                gCanvas.height = h;
                gCtx = gCanvas.getContext("2d");
                gCtx.clearRect(0, 0, w, h);
            }


            function captureToCanvas() {
                if(stype!=1)
                    return;
                if(gUM)
                {
                    try{
                        gCtx.drawImage(v,0,0);
                        try{
                            qrcode.decode();
                        }
                        catch(e){
                            console.log(e);
                            setTimeout(captureToCanvas, 500);
                        };
                    }
                    catch(e){
                        console.log(e);
                        setTimeout(captureToCanvas, 500);
                    };
                }
            }

            function htmlEntities(str) {
                return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
            }

            function read(qrcode_token)
            {
                if(workingAway) {
                    return;
                }

                workingAway = true;

                $.ajax({
                    type: "POST",
                    url: '{{ route('postQRCodeCheckInAttendee', ['event_id' => $event->id]) }}',
                    data: {qrcode_token: htmlEntities(qrcode_token)},
                    cache: false,
                    complete: function(){
                        beepSound.play();
                    },
                    error: function() {
                    },
                    success: function(response) {
                        document.getElementById("result").innerHTML = "<b>" + response.message +"</b>";
                    }
                });
            }

            function isCanvasSupported(){
                var elem = document.createElement('canvas');
                return !!(elem.getContext && elem.getContext('2d'));
            }

            function success(stream) {
                if(webkit)
                    v.src = window.webkitURL.createObjectURL(stream);
                else
                if(moz)
                {
                    v.mozSrcObject = stream;
                    v.play();
                }
                else
                    v.src = stream;
                gUM=true;
                setTimeout(captureToCanvas, 500);
            }

            function error(error) {
                gUM=false;
                return;
            }

            function load()
            {
                if(isCanvasSupported() && window.File && window.FileReader)
                {
                    initCanvas(800, 600);
                    qrcode.callback = read;
                    setwebcam();
                }
                else
                {
                    document.getElementById("mainbody").style.display="inline";
                    document.getElementById("mainbody").innerHTML='<p id="mp1">Attendize Checkpoint Manager for HTML5 capable browsers</p><br>'+
                            '<br><p id="mp2">sorry your browser is not supported</p><br><br>'+
                            '<p id="mp1">try <a href="http://www.mozilla.com/firefox"><img src="/assets/images/firefox.png"/></a> or <a href="http://chrome.google.com"><img src="/assets/images/chrome_logo.gif"/></a> or <a href="http://www.opera.com"><img src="/assets/images/Opera-logo.png"/></a></p>';
                }
            }

            function setwebcam()
            {
//                document.getElementById("help-text").style.display = "block";
                document.getElementById("result").innerHTML='Scanning&nbsp;&nbsp;&nbsp;<i class="fa fa-spinner fa-spin"></i>';
                if(stype==1)
                {
                    setTimeout(captureToCanvas, 500);
                    return;
                }
                var n=navigator;
                document.getElementById("outdiv").innerHTML = vidhtml;
                v=document.getElementById("v");

                if(n.getUserMedia)
                    n.getUserMedia({video: true, audio: false}, success, error);
                else
                if(n.webkitGetUserMedia)
                {
                    webkit=true;
                    n.webkitGetUserMedia({video:true, audio: false}, success, error);
                }
                else
                if(n.mediaDevices.getUserMedia)
                {
                    moz=true;
                    n.mozGetUserMedia({video: true, audio: false}, success, error);
                }
                else
                if(n.mozGetUserMedia)
                {
                    moz=true;
                    n.mozGetUserMedia({video: true, audio: false}, success, error);
                }


                stype=1;
                setTimeout(captureToCanvas, 500);
            }
        </script>


        {!! HTML::script('assets/javascript/backend.js') !!}

    </body>
</html>