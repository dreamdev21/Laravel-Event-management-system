
<div class="row">
    <div class="col-md-12">
        <h1 class="event-listing-heading">{{ $panel_title }}</h1>
        <ul class="event-list">

            @if(count($events))

                @foreach($events as $event)
                    <li>
                        <time datetime="{{ $event->start_date }}">
                            <span class="day">{{ $event->start_date->format('d') }}</span>
                            <span class="month">{{ $event->start_date->format('M') }}</span>
                            <span class="year">{{ $event->start_date->format('Y') }}</span>
                            <span class="time">{{ $event->start_date->format('h:i') }}</span>
                        </time>
                        @if(count($event->images))
                        <img class="hide" alt="{{ $event->title }}" src="{{ asset($event->images->first()['image_path']) }}"/>
                        @endif
                        <div class="info">
                            <h2 class="title ellipsis">
                               <a href="{{$event->event_url }}">{{ $event->title }}</a>
                            </h2>
                            <p class="desc ellipsis">{{ $event->venue_name }}</p>
                            <ul>
                                <li style="width:50%;"><a href="{{$event->event_url }}">Tickets</a></li>
                                <li style="width:50%;"><a href="{{$event->event_url }}">Information</a></li>
                            </ul>
                        </div>
                    </li>
                @endforeach
            @else
                <div class="alert alert-info">
                    There are no {{ $panel_title }} to display.
                </div>
            @endif

        </ul>
    </div>
</div>
