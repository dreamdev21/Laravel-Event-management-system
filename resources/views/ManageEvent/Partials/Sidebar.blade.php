<aside class="sidebar sidebar-left sidebar-menu">
    <section class="content">
        <h5 class="heading">Main Menu</h5>
        <ul id="nav" class="topmenu">
            <li>
                <a href="{{route('showOrganiserDashboard', ['organiser_id' => $event->organiser->id])}}">
                    <span class="figure"><i class="ico-arrow-left"></i></span>
                    <span class="text">Back To {{$event->organiser->name}}</span>
                </a>
            </li>
        </ul>
        <h5 class="heading">Event Menu</h5>
        <ul id="nav" class="topmenu">
            <li class="{{ Request::is('*dashboard*') ? 'active' : '' }}">
                <a href="{{route('showEventDashboard', array('event_id' => $event->id))}}">
                    <span class="figure"><i class="ico-home2"></i></span>
                    <span class="text">Dashboard</span>
                </a>
            </li>
            <li class="{{ Request::is('*tickets*') ? 'active' : '' }}">
                <a href="{{route('showEventTickets', array('event_id' => $event->id))}}">
                    <span class="figure"><i class="ico-ticket"></i></span>
                    <span class="text">Tickets</span>
                </a>
            </li>
            <li class="{{ Request::is('*orders*') ? 'active' : '' }}">
                <a href="{{route('showEventOrders', array('event_id' => $event->id))}}">
                    <span class="figure"><i class="ico-cart"></i></span>
                    <span class="text">Orders</span>
                </a>
            </li>
            <li class="{{ Request::is('*attendees*') ? 'active' : '' }}">
                <a href="{{route('showEventAttendees', array('event_id' => $event->id))}}">
                    <span class="figure"><i class="ico-user"></i></span>
                    <span class="text">Attendees</span>
                </a>
            </li>
            <li class="{{ Request::is('*promote*') ? 'active' : '' }} hide">
                <a href="{{route('showEventPromote', array('event_id' => $event->id))}}">
                    <span class="figure"><i class="ico-bullhorn"></i></span>
                    <span class="text">Promote</span>
                </a>
            </li>
            <li class="{{ Request::is('*customize*') ? 'active' : '' }}">
                <a href="{{route('showEventCustomize', array('event_id' => $event->id))}}">
                    <span class="figure"><i class="ico-cog"></i></span>
                    <span class="text">Customize</span>
                </a>
            </li>
        </ul>
    </section>
</aside>
