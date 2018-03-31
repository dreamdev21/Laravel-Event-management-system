<!DOCTYPE html>
<html dir = "<?php
    if (App::getLocale() == "ar"){
        echo 'rtl';
    }else{
        echo 'ltr';
    }
?>">
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
    <?php if (App::getLocale() == "ar"){
        echo ' <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-rtl/3.4.0/css/bootstrap-rtl.css" rel="stylesheet"/>';
    }
    ?>
    <style>
        @import url('https://fonts.googleapis.com/css?family=Cairo');
        body{
            font-family: 'Cairo', sans-serif;
        }
        body, h1, h2, h3, h4, h5, h6, .h1, .h2, .h3, .h4, .h5, .h6 {
            font-family: 'Cairo', sans-serif !important;
        }
       <?php
            if (App::getLocale() == "ar"){
                echo '
                @media (min-width: 992px){
                    .sidebar.sidebar-menu+#main {
                        padding-right: 220px !important;
                        padding-left: 0px !important;
                    }
                    .sidebar.sidebar-right.sidebar-menu {
                        position: absolute;
                        padding-top: 65px;
                        bottom: auto;
                        min-height: 100%;
                    }
                    #header.navbar>.navbar-toolbar {
                        position: unset;
                        margin-right: 220px;
                        margin-left: 0px;
                        height: 65px;
                    }
                    #header.navbar>.navbar-header {
                        background-color: #2e3254;
                        height: 65px;
                        float: right;
                    }
                }
                @media (max-width: 992px){
                    .sidebar.sidebar-right {
                        right: -220px;
                    }
                }
                .totop {
                    position: fixed;
                    z-index: 998;
                    bottom: 10px;
                    right:0px;
                    left: 25px;
                    display: block;
                    width: 80px;
                    height: 40px;
                    line-height: 40px;
                    background-color: rgba(46,50,84,0.8);
                    color: rgba(255,255,255,0.8);
                    text-align: center;
                    text-shadow: 0 -1px 0 rgba(0,0,0,0.1);
                    font-size: 16px;
                }
                ';
            }
        ?>
    </style>
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
            <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                    {{ trans('common.'. App::getLocale()) }}
                </a>
                <ul class="dropdown-menu">
                    @foreach (Config::get('app.languages') as $language)
                        @if ($language != App::getLocale())
                            <li>
                                <a href="/{{$language }}">{{ trans('common.'. $language) }}</a>
                            </li>
                        @endif
                    @endforeach
                </ul>
            </li>
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
                            {{ trans('shared.create-organiser') }}
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
                           class="loadModal editUserModal" href="javascript:void(0);"><span class="icon ico-user"></span>{{ trans('shared.my-profile') }}</a>
                    </li>
                    <li class="divider"></li>
                    <li><a data-href="{{route('showEditAccount')}}" data-modal-id="EditAccount" class="loadModal"
                           href="javascript:void(0);"><span class="icon ico-cog"></span>{{ trans('shared.account-settings') }}</a></li>


                    <li class="divider"></li>
                    <li><a target="_blank" href="https://www.attendize.com/feedback.php?v={{ config('attendize.version') }}"><span class="icon ico-megaphone"></span>{{ trans('shared.feedback-bugreport') }}</a></li>
                    <li class="divider"></li>
                    <li><a href="{{route('logout')}}"><span class="icon ico-exit"></span>{{ trans('shared.sign-out') }}</a></li>
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



<?php

if (App::getLocale() == "ar"){
    echo "<script>
            $(document.body).on('click', '.toggleSidebar', function (e) {
                $('html').toggleClass('sidebar-open-rtl');
                e.preventDefault();
            });
        </script>
        ";
}else{
    echo "<script>
            $(document.body).on('click', '.toggleSidebar', function (e) {
                $('html').toggleClass('sidebar-open-ltr');
                e.preventDefault();
            });
        </script>
    ";
}

?>


<!--/JS-->
@yield('foot')

@include('Shared.Partials.GlobalFooterJS')

</body>
</html>