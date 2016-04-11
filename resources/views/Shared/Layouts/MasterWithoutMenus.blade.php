<html>
    <head>

        <title>@yield('title')</title>

        @include('Shared.Partials.GlobalMeta')

        @yield('head')

        <!--JS-->
       {!! HTML::script('vendor/jquery/jquery.js') !!}
        <!--/JS-->

        <!--Style-->
       {!!HTML::style('assets/stylesheet/application.css')!!}
        <!--/Style-->

        <style>

            body {
                background: url({{asset('assets/images/splash.jpg')}}) no-repeat center center fixed;
                background-size: cover;
            }

            h2 {
                text-align: center;
                margin-bottom: 31px;
                text-transform: uppercase;
                letter-spacing: 4px;
                font-size: 23px;
            }
            .panel {
                background-color: #ffffff;
                background-color: rgba(255,255,255,.95);
                padding: 15px 30px ;
                border: none;
                color: #333;
                box-shadow: 0 0 5px 0 rgba(0,0,0,.2);
                margin-top: 40px;
            }

            .panel a {
                color: #333;
                font-weight: 600;
            }

            .logo {
                text-align: center;
                margin-bottom: 20px;
            }

            .signup {
                margin-top: 10px;
            }

            .forgotPassword {
                font-size: 12px;
                color: #ccc;
            }
        </style>
    </head>
    <body>
        <section id="main" role="main">
            <section class="container">
                @yield('content')
            </section>
        </section>

        {!!HTML::script('assets/javascript/backend.js')!!}
    </body>
    @include('Shared.Partials.GlobalFooterJS')
</html>