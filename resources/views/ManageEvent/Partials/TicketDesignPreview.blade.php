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
        <!--                    <img src="http://dev.attendize.com/assets/images/logo-100x100-lightBg.png" />-->
        <img src="http://dev.attendize.com/user_content/organiser_images/billy-ray-eventd-logo-48.jpg">
    </div>

    <div class="event_details">
        <div class="top_barcode hide">
            <img src="data:image/png; base64,iVBORw0KGgoAAAANSUhEUgAAAQAAAAAoAQMAAAArNLbKAAAABlBMVEX///8AAABVwtN+AAAAAXRSTlMAQObYZgAAAAlwSFlzAAAOxAAADsQBlSsOGwAAACRJREFUOI1j+MDw4T8QAgEDkPyPoKHiDKMKRhWMKhhVMLIVAADLdiz9/f0dNQAAAABJRU5ErkJggg==" alt="barcode">
        </div>
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
        <!--?xml version="1.0" standalone="no"?-->

        <svg width="126" height="126" version="1.1" xmlns="http://www.w3.org/2000/svg">
            <desc>236434278</desc>
            <g id="elements" fill="black" stroke="none">
                <rect x="0" y="0" width="6" height="6"></rect>
                <rect x="6" y="0" width="6" height="6"></rect>
                <rect x="12" y="0" width="6" height="6"></rect>
                <rect x="18" y="0" width="6" height="6"></rect>
                <rect x="24" y="0" width="6" height="6"></rect>
                <rect x="30" y="0" width="6" height="6"></rect>
                <rect x="36" y="0" width="6" height="6"></rect>
                <rect x="72" y="0" width="6" height="6"></rect>
                <rect x="84" y="0" width="6" height="6"></rect>
                <rect x="90" y="0" width="6" height="6"></rect>
                <rect x="96" y="0" width="6" height="6"></rect>
                <rect x="102" y="0" width="6" height="6"></rect>
                <rect x="108" y="0" width="6" height="6"></rect>
                <rect x="114" y="0" width="6" height="6"></rect>
                <rect x="120" y="0" width="6" height="6"></rect>
                <rect x="0" y="6" width="6" height="6"></rect>
                <rect x="36" y="6" width="6" height="6"></rect>
                <rect x="60" y="6" width="6" height="6"></rect>
                <rect x="72" y="6" width="6" height="6"></rect>
                <rect x="84" y="6" width="6" height="6"></rect>
                <rect x="120" y="6" width="6" height="6"></rect>
                <rect x="0" y="12" width="6" height="6"></rect>
                <rect x="12" y="12" width="6" height="6"></rect>
                <rect x="18" y="12" width="6" height="6"></rect>
                <rect x="24" y="12" width="6" height="6"></rect>
                <rect x="36" y="12" width="6" height="6"></rect>
                <rect x="60" y="12" width="6" height="6"></rect>
                <rect x="66" y="12" width="6" height="6"></rect>
                <rect x="72" y="12" width="6" height="6"></rect>
                <rect x="84" y="12" width="6" height="6"></rect>
                <rect x="96" y="12" width="6" height="6"></rect>
                <rect x="102" y="12" width="6" height="6"></rect>
                <rect x="108" y="12" width="6" height="6"></rect>
                <rect x="120" y="12" width="6" height="6"></rect>
                <rect x="0" y="18" width="6" height="6"></rect>
                <rect x="12" y="18" width="6" height="6"></rect>
                <rect x="18" y="18" width="6" height="6"></rect>
                <rect x="24" y="18" width="6" height="6"></rect>
                <rect x="36" y="18" width="6" height="6"></rect>
                <rect x="48" y="18" width="6" height="6"></rect>
                <rect x="60" y="18" width="6" height="6"></rect>
                <rect x="72" y="18" width="6" height="6"></rect>
                <rect x="84" y="18" width="6" height="6"></rect>
                <rect x="96" y="18" width="6" height="6"></rect>
                <rect x="102" y="18" width="6" height="6"></rect>
                <rect x="108" y="18" width="6" height="6"></rect>
                <rect x="120" y="18" width="6" height="6"></rect>
                <rect x="0" y="24" width="6" height="6"></rect>
                <rect x="12" y="24" width="6" height="6"></rect>
                <rect x="18" y="24" width="6" height="6"></rect>
                <rect x="24" y="24" width="6" height="6"></rect>
                <rect x="36" y="24" width="6" height="6"></rect>
                <rect x="48" y="24" width="6" height="6"></rect>
                <rect x="54" y="24" width="6" height="6"></rect>
                <rect x="72" y="24" width="6" height="6"></rect>
                <rect x="84" y="24" width="6" height="6"></rect>
                <rect x="96" y="24" width="6" height="6"></rect>
                <rect x="102" y="24" width="6" height="6"></rect>
                <rect x="108" y="24" width="6" height="6"></rect>
                <rect x="120" y="24" width="6" height="6"></rect>
                <rect x="0" y="30" width="6" height="6"></rect>
                <rect x="36" y="30" width="6" height="6"></rect>
                <rect x="54" y="30" width="6" height="6"></rect>
                <rect x="66" y="30" width="6" height="6"></rect>
                <rect x="84" y="30" width="6" height="6"></rect>
                <rect x="120" y="30" width="6" height="6"></rect>
                <rect x="0" y="36" width="6" height="6"></rect>
                <rect x="6" y="36" width="6" height="6"></rect>
                <rect x="12" y="36" width="6" height="6"></rect>
                <rect x="18" y="36" width="6" height="6"></rect>
                <rect x="24" y="36" width="6" height="6"></rect>
                <rect x="30" y="36" width="6" height="6"></rect>
                <rect x="36" y="36" width="6" height="6"></rect>
                <rect x="48" y="36" width="6" height="6"></rect>
                <rect x="60" y="36" width="6" height="6"></rect>
                <rect x="72" y="36" width="6" height="6"></rect>
                <rect x="84" y="36" width="6" height="6"></rect>
                <rect x="90" y="36" width="6" height="6"></rect>
                <rect x="96" y="36" width="6" height="6"></rect>
                <rect x="102" y="36" width="6" height="6"></rect>
                <rect x="108" y="36" width="6" height="6"></rect>
                <rect x="114" y="36" width="6" height="6"></rect>
                <rect x="120" y="36" width="6" height="6"></rect>
                <rect x="0" y="48" width="6" height="6"></rect>
                <rect x="6" y="48" width="6" height="6"></rect>
                <rect x="30" y="48" width="6" height="6"></rect>
                <rect x="36" y="48" width="6" height="6"></rect>
                <rect x="42" y="48" width="6" height="6"></rect>
                <rect x="66" y="48" width="6" height="6"></rect>
                <rect x="96" y="48" width="6" height="6"></rect>
                <rect x="102" y="48" width="6" height="6"></rect>
                <rect x="6" y="54" width="6" height="6"></rect>
                <rect x="12" y="54" width="6" height="6"></rect>
                <rect x="18" y="54" width="6" height="6"></rect>
                <rect x="24" y="54" width="6" height="6"></rect>
                <rect x="48" y="54" width="6" height="6"></rect>
                <rect x="66" y="54" width="6" height="6"></rect>
                <rect x="72" y="54" width="6" height="6"></rect>
                <rect x="78" y="54" width="6" height="6"></rect>
                <rect x="84" y="54" width="6" height="6"></rect>
                <rect x="90" y="54" width="6" height="6"></rect>
                <rect x="108" y="54" width="6" height="6"></rect>
                <rect x="114" y="54" width="6" height="6"></rect>
                <rect x="0" y="60" width="6" height="6"></rect>
                <rect x="18" y="60" width="6" height="6"></rect>
                <rect x="30" y="60" width="6" height="6"></rect>
                <rect x="36" y="60" width="6" height="6"></rect>
                <rect x="48" y="60" width="6" height="6"></rect>
                <rect x="54" y="60" width="6" height="6"></rect>
                <rect x="60" y="60" width="6" height="6"></rect>
                <rect x="72" y="60" width="6" height="6"></rect>
                <rect x="78" y="60" width="6" height="6"></rect>
                <rect x="84" y="60" width="6" height="6"></rect>
                <rect x="90" y="60" width="6" height="6"></rect>
                <rect x="102" y="60" width="6" height="6"></rect>
                <rect x="0" y="66" width="6" height="6"></rect>
                <rect x="6" y="66" width="6" height="6"></rect>
                <rect x="12" y="66" width="6" height="6"></rect>
                <rect x="24" y="66" width="6" height="6"></rect>
                <rect x="48" y="66" width="6" height="6"></rect>
                <rect x="54" y="66" width="6" height="6"></rect>
                <rect x="66" y="66" width="6" height="6"></rect>
                <rect x="72" y="66" width="6" height="6"></rect>
                <rect x="96" y="66" width="6" height="6"></rect>
                <rect x="108" y="66" width="6" height="6"></rect>
                <rect x="120" y="66" width="6" height="6"></rect>
                <rect x="6" y="72" width="6" height="6"></rect>
                <rect x="12" y="72" width="6" height="6"></rect>
                <rect x="24" y="72" width="6" height="6"></rect>
                <rect x="30" y="72" width="6" height="6"></rect>
                <rect x="36" y="72" width="6" height="6"></rect>
                <rect x="48" y="72" width="6" height="6"></rect>
                <rect x="54" y="72" width="6" height="6"></rect>
                <rect x="66" y="72" width="6" height="6"></rect>
                <rect x="72" y="72" width="6" height="6"></rect>
                <rect x="90" y="72" width="6" height="6"></rect>
                <rect x="96" y="72" width="6" height="6"></rect>
                <rect x="114" y="72" width="6" height="6"></rect>
                <rect x="120" y="72" width="6" height="6"></rect>
                <rect x="48" y="78" width="6" height="6"></rect>
                <rect x="60" y="78" width="6" height="6"></rect>
                <rect x="72" y="78" width="6" height="6"></rect>
                <rect x="84" y="78" width="6" height="6"></rect>
                <rect x="90" y="78" width="6" height="6"></rect>
                <rect x="96" y="78" width="6" height="6"></rect>
                <rect x="102" y="78" width="6" height="6"></rect>
                <rect x="108" y="78" width="6" height="6"></rect>
                <rect x="114" y="78" width="6" height="6"></rect>
                <rect x="120" y="78" width="6" height="6"></rect>
                <rect x="0" y="84" width="6" height="6"></rect>
                <rect x="6" y="84" width="6" height="6"></rect>
                <rect x="12" y="84" width="6" height="6"></rect>
                <rect x="18" y="84" width="6" height="6"></rect>
                <rect x="24" y="84" width="6" height="6"></rect>
                <rect x="30" y="84" width="6" height="6"></rect>
                <rect x="36" y="84" width="6" height="6"></rect>
                <rect x="48" y="84" width="6" height="6"></rect>
                <rect x="66" y="84" width="6" height="6"></rect>
                <rect x="90" y="84" width="6" height="6"></rect>
                <rect x="0" y="90" width="6" height="6"></rect>
                <rect x="36" y="90" width="6" height="6"></rect>
                <rect x="48" y="90" width="6" height="6"></rect>
                <rect x="54" y="90" width="6" height="6"></rect>
                <rect x="60" y="90" width="6" height="6"></rect>
                <rect x="84" y="90" width="6" height="6"></rect>
                <rect x="96" y="90" width="6" height="6"></rect>
                <rect x="108" y="90" width="6" height="6"></rect>
                <rect x="114" y="90" width="6" height="6"></rect>
                <rect x="120" y="90" width="6" height="6"></rect>
                <rect x="0" y="96" width="6" height="6"></rect>
                <rect x="12" y="96" width="6" height="6"></rect>
                <rect x="18" y="96" width="6" height="6"></rect>
                <rect x="24" y="96" width="6" height="6"></rect>
                <rect x="36" y="96" width="6" height="6"></rect>
                <rect x="54" y="96" width="6" height="6"></rect>
                <rect x="66" y="96" width="6" height="6"></rect>
                <rect x="102" y="96" width="6" height="6"></rect>
                <rect x="120" y="96" width="6" height="6"></rect>
                <rect x="0" y="102" width="6" height="6"></rect>
                <rect x="12" y="102" width="6" height="6"></rect>
                <rect x="18" y="102" width="6" height="6"></rect>
                <rect x="24" y="102" width="6" height="6"></rect>
                <rect x="36" y="102" width="6" height="6"></rect>
                <rect x="54" y="102" width="6" height="6"></rect>
                <rect x="66" y="102" width="6" height="6"></rect>
                <rect x="72" y="102" width="6" height="6"></rect>
                <rect x="78" y="102" width="6" height="6"></rect>
                <rect x="84" y="102" width="6" height="6"></rect>
                <rect x="90" y="102" width="6" height="6"></rect>
                <rect x="96" y="102" width="6" height="6"></rect>
                <rect x="102" y="102" width="6" height="6"></rect>
                <rect x="108" y="102" width="6" height="6"></rect>
                <rect x="0" y="108" width="6" height="6"></rect>
                <rect x="12" y="108" width="6" height="6"></rect>
                <rect x="18" y="108" width="6" height="6"></rect>
                <rect x="24" y="108" width="6" height="6"></rect>
                <rect x="36" y="108" width="6" height="6"></rect>
                <rect x="54" y="108" width="6" height="6"></rect>
                <rect x="66" y="108" width="6" height="6"></rect>
                <rect x="72" y="108" width="6" height="6"></rect>
                <rect x="78" y="108" width="6" height="6"></rect>
                <rect x="90" y="108" width="6" height="6"></rect>
                <rect x="96" y="108" width="6" height="6"></rect>
                <rect x="102" y="108" width="6" height="6"></rect>
                <rect x="108" y="108" width="6" height="6"></rect>
                <rect x="114" y="108" width="6" height="6"></rect>
                <rect x="120" y="108" width="6" height="6"></rect>
                <rect x="0" y="114" width="6" height="6"></rect>
                <rect x="36" y="114" width="6" height="6"></rect>
                <rect x="48" y="114" width="6" height="6"></rect>
                <rect x="54" y="114" width="6" height="6"></rect>
                <rect x="66" y="114" width="6" height="6"></rect>
                <rect x="72" y="114" width="6" height="6"></rect>
                <rect x="78" y="114" width="6" height="6"></rect>
                <rect x="84" y="114" width="6" height="6"></rect>
                <rect x="90" y="114" width="6" height="6"></rect>
                <rect x="96" y="114" width="6" height="6"></rect>
                <rect x="108" y="114" width="6" height="6"></rect>
                <rect x="0" y="120" width="6" height="6"></rect>
                <rect x="6" y="120" width="6" height="6"></rect>
                <rect x="12" y="120" width="6" height="6"></rect>
                <rect x="18" y="120" width="6" height="6"></rect>
                <rect x="24" y="120" width="6" height="6"></rect>
                <rect x="30" y="120" width="6" height="6"></rect>
                <rect x="36" y="120" width="6" height="6"></rect>
                <rect x="48" y="120" width="6" height="6"></rect>
                <rect x="54" y="120" width="6" height="6"></rect>
                <rect x="60" y="120" width="6" height="6"></rect>
                <rect x="72" y="120" width="6" height="6"></rect>
                <rect x="90" y="120" width="6" height="6"></rect>
                <rect x="102" y="120" width="6" height="6"></rect>
                <rect x="114" y="120" width="6" height="6"></rect>
            </g>
        </svg>
    </div>
    <div class="barcode_vertical">
        <!--?xml version="1.0" standalone="no"?-->
        <svg width="20" height="40" version="1.1" xmlns="http://www.w3.org/2000/svg">
            <desc>236434278</desc>
            <g id="bars" fill="black" stroke="none">
                <rect x="0" y="0" width="1" height="40"></rect>
                <rect x="2" y="0" width="2" height="40"></rect>
                <rect x="6" y="0" width="2" height="40"></rect>
                <rect x="9" y="0" width="2" height="40"></rect>
                <rect x="12" y="0" width="1" height="40"></rect>
                <rect x="14" y="0" width="4" height="40"></rect>
                <rect x="19" y="0" width="1" height="40"></rect>
            </g>
        </svg>
    </div>
</div>