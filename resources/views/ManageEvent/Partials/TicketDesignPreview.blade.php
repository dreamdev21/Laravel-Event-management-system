{!! HTML::style(asset('assets/stylesheet/ticket.css')) !!}
<style>
    .ticket {
        border: 1px solid {{$event->ticket_border_color}};
        background: {{$event->ticket_bg_color}} ;
        color: {{$event->ticket_sub_text_color}};
        border-left-color: {{$event->ticket_border_color}} ;
    }
    .ticket h4 {color: {{$event->ticket_text_color}};}
    .ticket .logo {
        border-left: 1px solid {{$event->ticket_border_color}};
        border-bottom: 1px solid {{$event->ticket_border_color}};

    }
</style>
<div class="ticket">
    <div class="logo">
        {!! HTML::image(asset($image_path)) !!}
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
        {!! DNS2D::getBarcodeSVG('hello', "QRCODE", 6, 6) !!}
    </div>
    @if($event->is_1d_barcode_enabled)
        <div class="barcode_vertical">
            {!! DNS1D::getBarcodeSVG(12211221, "C39+", 1, 50) !!}
        </div>
    @endif
</div>
