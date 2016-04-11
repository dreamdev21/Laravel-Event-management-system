/**
 * @todo Refactor this
 */

var workingAway = false;
var canvasContext = null;
var c = 0;
var stype = 0;
var gUM = false;
var webkit = false;
var moz = false;
var theVideo = null;
var QrTimeout = null;
var beepSound = new Audio('/mp3/beep.mp3');
var vidhtml = '<video id="ScanVideo" autoplay></video>';


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
        $('#QrModal').modal('show');
        loadQrReader();
    });

    $('.startScanner').on('click', function(e) {
        e.preventDefault();
        loadQrReader();
    });

    $( window ).resize(resizeVideo);

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
                + '<a href="" class="ci btn btn-successfulQrRead"><i class="ico-checkmark"></i></a> '
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
        url: Attendize.checkInRoute,
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

// QRCODE reader Copyright 2011 Lazar Laszlo
// http://www.webqr.com

function resizeVideo() {
    var $videoWrapper = $('#ScanVideoOutter');
    var $video = $('#ScanVideo');

    $video.height($videoWrapper.height());
    $video.width($videoWrapper.width());
}

function captureToCanvas() {
    if(stype!=1)
        return;
    if(gUM)
    {
        try{
            canvasContext.drawImage(theVideo,0,0);
            try{
                qrcode.decode();
            }
            catch(e){
                console.log(e);
                QrTimeout = setTimeout(captureToCanvas, 500);
            };
        }
        catch(e){
            console.log(e);
            QrTimeout = setTimeout(captureToCanvas, 500);
        }
    }
}

function htmlEntities(str) {
    return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
}

function read(qrcode_token)
{
    $.ajax({
        type: "POST",
        url: Attendize.qrcodeCheckInRoute,
        data: {qrcode_token: htmlEntities(qrcode_token)},
        cache: false,
        complete: function(){
            beepSound.play();
        },
        error: function() {
            showMessage('Something has gone wrong. Please try again.');
        },
        success: function(response) {
            $('#ScanResult').html("<b>" + response.message +"</b>");
        }
    });
}



function successfulQrRead(stream) {
    if(webkit)
        theVideo.src = window.URL.createObjectURL(stream);
    else if(moz)
    {
        theVideo.mozSrcObject = stream;
        theVideo.play();
    }
    else {
        theVideo.src = stream;
    }

    gUM=true;
    QrTimeout = setTimeout(captureToCanvas, 500);
}

function error(error) {
    gUM=false;
    return;
}

function loadQrReader()
{

    var $canvas = $('#QrCanvas');

    $canvas.height('300px');
    $canvas.width('600px');

    canvasContext = $canvas[0].getContext('2d');
    canvasContext.clearRect(0, 0, 600, 300);
    qrcode.callback = read;

    $('#ScanResult').html('<div id="scanning-ellipsis">Scanning<span>.</span><span>.</span><span>.</span></div>');
    if(stype==1)
    {
        clearTimeout(QrTimeout);
        QrTimeout = setTimeout(captureToCanvas, 500);
        return;
    }

    $('#ScanVideoOutter').html(vidhtml);
    theVideo = $("#ScanVideo")[0];

    if(navigator.getUserMedia)
    {
        navigator.getUserMedia({video: true, audio: false}, successfulQrRead, error);
    } else if(navigator.webkitGetUserMedia)
    {
        webkit=true;
        navigator.webkitGetUserMedia({video:true, audio: false}, successfulQrRead, error);
    }
    else if(navigator.mediaDevices.getUserMedia)
    {
        moz=true;
        navigator.mozGetUserMedia({video: true, audio: false}, successfulQrRead, error);
    }
    else if(navigator.mozGetUserMedia)
    {
        moz=true;
        navigator.mozGetUserMedia({video: true, audio: false}, successfulQrRead, error);
    }

    stype=1;
    QrTimeout = setTimeout(captureToCanvas, 500);

}