@if(Auth::check() && !$event->is_live)
<section id="adminBar">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <style>
                    .not_live {
                        margin: 20px;
                        text-align: center;
                    }
                </style>
                <div class="alert alert-warning not_live">
                    This event is not visible to the public. <a href="{{route('MakeEventLive' , ['event_id' => $event->id])}}" target="_blank">Click
                    here to make it live</a> .
                </div>
            </div>
        </div>
    </div>
</section>
@endif
<section id="organiserHead" class="container-fluid">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div onclick="window.location='{{$event->event_url}}#organiser'" class="event_organizer">
                    <b>{{$event->organiser->name}}</b> Presents
                </div>
            </div>
        </div>
    </div>
</section>
<section id="intro" class="container">
    <div class="row">
        <div class="col-md-12">
            <h1 property="name">{{$event->title}}</h1>
            <div class="event_venue">
                <span property="startDate" content="{{ $event->start_date->toIso8601String() }}">
                    {{ $event->start_date->format('D d M H:i A') }}
                </span>
                -
                <span property="endDate" content="{{ $event->end_date->toIso8601String() }}">
                    {{ $event->end_date->format('H:i A') }}
                </span>
                @
                <span property="location" typeof="Place">
                    <b property="name">{{$event->venue_name}}</b>
                    <meta property="address" content="{{ urldecode($event->venue_name) }}">
                </span>
            </div>

            <div class="event_buttons">
                <div class="row">
                    <div class="col-md-4 col-sm-4">
                        <a class="btn btn-event-link btn-lg" href="{{{$event->event_url}}}#tickets">TICKETS</a>
                    </div>
                    <div class="col-md-4 col-sm-4">
                        <a class="btn btn-event-link btn-lg" href="{{{$event->event_url}}}#details">DETAILS</a>
                    </div>
                    <div class="col-md-4 col-sm-4">
                        <a class="btn btn-event-link btn-lg" href="{{{$event->event_url}}}#location">LOCATION</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
