<aside class="sidebar sidebar-<?php
if (App::getLocale() == "ar"){
    echo 'right';
}else{
    echo 'left';
}
?> sidebar-menu">
    <section class="content">
        <h5 class="heading">{{ trans('manageorganiser.organiser-menu') }}</h5>

        <ul id="nav" class="topmenu">
            <li class="{{ Request::is('*dashboard*') ? 'active' : '' }}">
                <a href="{{route('showOrganiserDashboard', array('organiser_id' => $organiser->id))}}">
                    <span class="figure"><i class="ico-home2"></i></span>
                    <span class="text">{{  trans('manageorganiser.dashboard') }}</span>
                </a>
            </li>
            <li class="{{ Request::is('*events*') ? 'active' : '' }}">
                <a href="{{route('showOrganiserEvents', array('organiser_id' => $organiser->id))}}">
                    <span class="figure"><i class="ico-calendar"></i></span>
                    <span class="text">{{ trans('manageorganiser.events') }}</span>
                </a>
            </li>

            <li class="{{ Request::is('*customize*') ? 'active' : '' }}">
                <a href="{{route('showOrganiserCustomize', array('organiser_id' => $organiser->id))}}">
                    <span class="figure"><i class="ico-cog"></i></span>
                    <span class="text">{{ trans('manageorganiser.customize') }}</span>
                </a>
            </li>
        </ul>
    </section>
</aside>
