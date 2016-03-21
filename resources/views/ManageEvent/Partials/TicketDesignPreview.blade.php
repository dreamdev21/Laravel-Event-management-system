<style>
    .ticket {
        /*page-break-after: always;*/
        padding: 10px;
        border: 1px solid {{$event->ticket_border_color}};
        width: 700px;
        margin: 0 auto;
        margin-top: 20px;
        background: {{$event->ticket_bg_color}};
        position: relative;
        height: 330px;
        font-size: 12px;
        color: {{$event->ticket_sub_text_color}};
        border-left-width: 3px;
        border-left-color: {{$event->ticket_border_color}};
        overflow: hidden;
        zoom: .6;
        -moz-transform: scale(.6);
    }

    .ticket table {
        width: 100%;
    }

    .ticket h1 {
        margin-bottom: 5px;
        margin-top: 0px;
    }

    .ticket hr {
        border: none;
        border-bottom: 1px solid #ccc;
        margin: 5px 0;
    }

    .ticket .barcode {
        width: 150px;
        height: 150px;
        position: absolute;
        left: 1px;
        bottom: 85px;
        overflow: hidden;
        padding: 10px;
        border: 1px solid #000;
        border-left: none;
        background-color: #fdfdfd;
    }

    .ticket .barcode_vertical
    {
        position: absolute;
        right: -40px;
        -webkit-transform: rotate(90deg);
        top: 171px;
    }

    .ticket .top_barcode {
        margin-bottom: 15px;
    }

    .ticket h4 {
        font-size: 17px;
        margin: 6px auto;
        text-transform: uppercase;
        color: {{$event->ticket_text_color}};
    }

    .ticket .event_details, .ticket .attendee_details {
        position: absolute;
        top: 15px;
    }

    .ticket .event_details {
        left: 175px;
        overflow: hidden;
        max-width: 210px;
        white-space: nowrap;
        text-overflow: ellipsis;
        top: 50px;
    }

    .ticket .attendee_details {
        left: 390px;
        overflow: hidden;
        max-width: 195px;
        white-space: nowrap;
        text-overflow: ellipsis;
        top: 50px;
    }

    .ticket .logo {
        position: absolute;
        right: 1px;
        top: 1px;
        border: 1px solid {{$event->ticket_border_color}};
        border-top: none;
        border-right: none;
        padding: 5px;
        background-color: #fdfdfd;
        text-align: center;
    }

    .ticket .logo img {
        max-width: 110px;
    }
</style>
<div class="ticket">
    <div class="logo">
        {!! HTML::image(asset('assets/images/logo-email.png')) !!}
    </div>

    <div class="event_details">
        <h4>Event</h4>Demo Event<h4>Organiser</h4>Demo Organiser<h4>Venue</h4>Demo Location<h4>Start Date / Time</h4>
        Mar 18th 4:08PM
        <h4>End Date / Time</h4>
        Mar 18th 5:08PM
    </div>

    <div class="attendee_details">
        <h4>Name</h4>Bill Blogs<h4>Ticket Type</h4>
        General Admission
        <h4>Order Ref.</h4>
        #YLY9U73
        <h4>Attendee Ref.</h4>
        #YLY9U73-1
        <h4>Price</h4>
        €XX.XX
    </div>

    <div class="barcode">
        {!! HTML::image(asset('assets/images/qrcode.png')) !!}
    </div>
</div>