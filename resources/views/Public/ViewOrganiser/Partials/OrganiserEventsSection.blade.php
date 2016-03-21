<section class="container">
    <div class="row">
        <div class="col-xs-12 col-md-8">
            @include('Public.ViewEvent.Partials.EventListingPanel',
                array(
                    'panel_title' => 'Upcoming Events',
                    'events'      => $upcoming_events
                )
            )
            @include('Public.ViewEvent.Partials.EventListingPanel',
                array(
                    'panel_title' => 'Past Events',
                    'events' => $past_events
                )
            )
        </div>
        <div class="col-xs-6 col-md-4">
            @if ($organiser->facebook)
                @include('Shared.Partials.FacebookTimelinePanel',
                    array(
                        'facebook_account' => $organiser->facebook
                    )
                )
            @endif
            @if ($organiser->twitter)
                @include('Shared.Partials.TwitterTimelinePanel',
                    array(
                        'twitter_account' => $organiser->facebook
                    )
                )
            @endif
        </div>
    </div>
</section>