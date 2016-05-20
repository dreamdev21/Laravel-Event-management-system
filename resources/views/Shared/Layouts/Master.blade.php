<!DOCTYPE html>
<html>
<head>
    <!--
              _   _                 _ _
         /\  | | | |               | (_)
        /  \ | |_| |_ ___ _ __   __| |_ _______   ___ ___  _ __ ___
       / /\ \| __| __/ _ \ '_ \ / _` | |_  / _ \ / __/ _ \| '_ ` _ \
      / ____ \ |_| ||  __/ | | | (_| | |/ /  __/| (_| (_) | | | | | |
     /_/    \_\__|\__\___|_| |_|\__,_|_/___\___(_)___\___/|_| |_| |_|

    -->
    <title>
        @section('title')
            Attendize ::
        @show
    </title>

    @include('Shared.Layouts.ViewJavascript')

    <!--Meta-->
    @include('Shared.Partials.GlobalMeta')
   <!--/Meta-->

    <!--JS-->
    {!! HTML::script(config('attendize.cdn_url_static_assets').'/vendor/jquery/dist/jquery.min.js') !!}
    <!--/JS-->

    <!--Style-->
    {!! HTML::style(config('attendize.cdn_url_static_assets').'/assets/stylesheet/application.css') !!}
    <!--/Style-->

    @yield('head')
</head>
<body class="attendize">
@yield('pre_header')
<header id="header" class="navbar">

    <div class="navbar-header">
        <a class="navbar-brand" href="javascript:void(0);">
            <img style="width: 150px;" class="logo" alt="Attendize" src="{{asset('assets/images/logo-light.png')}}"/>
        </a>
    </div>

    <div class="navbar-toolbar clearfix">
        @yield('top_nav')

        <ul class="nav navbar-nav navbar-right">
            <li class="dropdown profile">

                <a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown">
                    <span class="meta">
                        <span class="text ">{{isset($organiser->name) ? $organiser->name : $event->organiser->name}}</span>
                        <span class="arrow"></span>
                    </span>
                </a>


                <ul class="dropdown-menu" role="menu">
                    <li>
                        <a href="{{route('showCreateOrganiser')}}">
                            <i class="ico ico-plus"></i>
                            Create Organiser
                        </a>
                    </li>
                    @foreach($organisers as $org)
                        <li>
                            <a href="{{route('showOrganiserDashboard', ['organiser_id' => $org->id])}}">
                                <i class="ico ico-building"></i> &nbsp;
                                {{$org->name}}
                            </a>

                        </li>
                    @endforeach
                    <li class="divider"></li>

                    <li>
                        <a data-href="{{route('showEditUser')}}" data-modal-id="EditUser"
                           class="loadModal editUserModal" href="javascript:void(0);"><span class="icon ico-user"></span>My Profile</a>
                    </li>
                    <li class="divider"></li>
                    <li><a data-href="{{route('showEditAccount')}}" data-modal-id="EditAccount" class="loadModal"
                           href="javascript:void(0);"><span class="icon ico-cog"></span>Account Settings</a></li>


                    <li class="divider"></li>
                    <li><a target="_blank" href="https://www.attendize.com/feedback.php?v={{ config('attendize.version') }}"><span class="icon ico-megaphone"></span>Feedback / Bug Report</a></li>
                    <li class="divider"></li>
                    <li><a href="{{route('logout')}}"><span class="icon ico-exit"></span>Sign Out</a></li>
                </ul>
            </li>
        </ul>
    </div>
</header>

@yield('menu')

<!--Main Content-->
<section id="main" role="main">
    <div class="container-fluid">
        <div class="page-title">
            <h1 class="title">@yield('page_title')</h1>
        </div>
        @if(array_key_exists('page_header', View::getSections()))
        <!--  header -->
        <div class="page-header page-header-block row">
            <div class="row">
                @yield('page_header')
            </div>
        </div>
        <!--/  header -->
        @endif

        <!--Content-->
        @yield('content')
        <!--/Content-->
    </div>

    <!--To The Top-->
    <a href="#" style="display:none;" class="totop"><i class="ico-angle-up"></i></a>
    <!--/To The Top-->

</section>
<!--/Main Content-->

<!--JS-->
{!! HTML::script('assets/javascript/backend.js') !!}
<script>
    $(function () {
        $.ajaxSetup({
            headers: {
                'X-CSRF-Token': "<?php echo csrf_token() ?>"
            }
        });
    });

    @if(!Auth::user()->first_name)
      setTimeout(function () {
        $('.editUserModal').click();
    }, 1000);
    @endif

</script>
<!--/JS-->
@yield('foot')

@include('Shared.Partials.GlobalFooterJS')

</body>
</html>